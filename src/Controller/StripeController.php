<?php

namespace App\Controller;

use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\StripePayment;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class StripeController extends AbstractController
{
    //region réussite paiement
    #[Route('/pay/success/{id}', name: 'app_stripe_success')]
    public function success($id, ReservationRepository $reservationRepository, EntityManagerInterface $entityManager): Response
    {
        $reservation = $reservationRepository->find($id);
        if ($reservation) {
            $reservation->setStatus('confirmée');
            $reservation->setConfirmedAt(new \DateTimeImmutable());
            $reservation->setConfirmedBy($reservation->getUser());


            $entityManager->flush();
            $this->addFlash('success', "Le paiement de l'acompte pour votre séjour a bien été effectué! Reportez vous sur l'email de confirmation pour connaître les prochaines étapes.");
        }
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('danger', 'Vous devez être connecté pour voir vos réservations.');
            return $this->redirectToRoute('app_login');
        }

        $reservations = $reservationRepository->findBy(['user' => $user], ['startDate' => 'DESC']);

        return $this->render('user/reservations.html.twig', [
            'reservations' => $reservations,
        ]);
    }
    //endregion

    //region annulation paiement
    #[Route('/pay/cancel', name: 'app_stripe_cancel')]
    public function cancel(): Response
    {
        return $this->render('cart/index.html.twig', [
            'controller_name' => 'StripeController',
        ]);
    }
    //endregion

    //region notification de Stripe
    #[Route('/stripe/notify', name: 'app_stripe_notify')]
    public function stripeNotify(Request $request, ReservationRepository $reservationRepository, EntityManagerInterface $entityManager): Response
    {
        // Utilisation de la clé secrète de Stripe à partir de la variable d'environnement
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY'] ?? '');
        //////// Pour créer un fichier de tests :
        file_put_contents("log.txt", "");
        //////// Définir la clé de webhook de Stripe, elle est à remettre à jour tous les 90 jours.
        //////// Pour se faire : invite de commandes cmd    -> stripe login
        ////////                                            -> stripe listen --forward-to http://localhost:8000/stripe/notify
        ////////                                            / ou ->  stripe listen et -> stripe --forward-to http://localhost:8000/stripe/notify
        //////// affecter à $endpoint_secret la valeur de webhooko signing secret
        $endpoint_secret = 'whsec_dd9be64d8ebcd07b44538b5fa3052fcef312fc35e25df09640949ef3d64fe9d4';
        // Récupérer le contenu de la requête
        $payload = $request->getContent();
        file_put_contents("log.txt", $payload); // fichier test
        // Récupérer l'en-tête de signature de la requête
        $sigHeader = $request->headers->get('Stripe-Signature');
        // Initialiser l'événement à null
        $event = null;

        try {
            // Construire l'événement à partir de la requête et de la signature
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sigHeader,
                $endpoint_secret
            );
            file_put_contents("log.txt", print_r($event, true), FILE_APPEND); // fichier test
        } catch (\UnexpectedValueException $e) {
            // Retourner une erreur 400 si le payload est invalide
            return new Response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Retourner une erreur 400 si la signature est invalide
            return new Response('Invalid signature', 400);
        }

        // Gérer les différents types d'événements
        switch ($event->type) {
            case 'charge.succeeded':
            case 'charge.updated':
                $charge = $event->data->object;
                //fichier de test
                $fileName = 'stripe-details-' . $charge->id . '.txt';
                file_put_contents($fileName, print_r($charge, true));

                $reservationId = $charge->metadata->reservationId ?? null;
                $address = $charge->billing_details->address ?? null;

                if ($reservationId) {
                    $reservation = $reservationRepository->find($reservationId);
                    if ($reservation) {
                        $reservation->setStatus('confirmée');
                        $reservation->setIsCompleted(false);

                        $user = $reservation->getUser();
                        if ($user) {
                            if ($address) {
                                $user->setBillingAddressLine1($address->line1 ?? null);
                                $user->setBillingAddressCity($address->city ?? null);
                                $user->setBillingAddressPostalCode($address->postal_code ?? null);
                            }
                        }

                        file_put_contents('log.txt', "User: " . print_r([
                            'id' => $user?->getId(),
                            'address' => $user?->getBillingAddressLine1(),
                            'city' => $user?->getBillingAddressCity(),
                            'cp' => $user?->getBillingAddressPostalCode(),
                        ], true), FILE_APPEND);
                        $entityManager->flush();
                    }
                }
                break;
            case 'checkout.session.completed':
                break;
            case 'payment_intent.succeeded':
                // Optionnel : pour gérer la confirmation du paiement
                break;
            case 'payment_method.attached':
                // Optionnel : pour gérer l'attachement d'une méthode de paiement
                break;
            default:
                // .. ??
                break;
        }

        return new Response('Événement reçu avec succès', 200);
    }
    //endregion

    //region checkout - Utilisation de Service/StripePayment pour createCheckoutSession
    #[Route('/pay/checkout/{id}', name: 'app_stripe_checkout')]
    public function checkout($id, ReservationRepository $reservationRepository, StripePayment $stripePayment): Response
    {
        // 1. Récupérer la réservation
        $reservation = $reservationRepository->find($id);
        if (!$reservation) {
            $this->addFlash('danger', 'Réservation introuvable.');
            return $this->redirectToRoute('app_cart');
        }

        // 2. Créer les URLs de succès et d'annulation
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $DOMAIN = $request->getSchemeAndHttpHost();
        $successUrl = $DOMAIN . $this->generateUrl('app_stripe_success', ['id' => $reservation->getId()]);
        $cancelUrl = $DOMAIN . $this->generateUrl('app_stripe_cancel');

        // 3. Créer la session Stripe Checkout
        $checkout_session = $stripePayment->createCheckoutSession($reservation, $successUrl, $cancelUrl);

        return $this->redirect($checkout_session->url, 303);
    }
    //endregion
}
