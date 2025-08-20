<?php

namespace App\Controller;

use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class StripeController extends AbstractController
{
    //region réusite paiement
    #[Route('/pay/success/{id}', name: 'app_stripe_success')]
    public function success($id, ReservationRepository $reservationRepository, EntityManagerInterface $entityManager): Response
    {
        $reservation = $reservationRepository->find($id);
        if ($reservation) {
            $reservation->setStatus('confirmée');
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
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;

                //fichier de test
                $fileName = 'stripe-details-' . $paymentIntent->id . '.txt';
                file_put_contents($fileName, print_r($paymentIntent, true));
                
                $reservationId = $paymentIntent->metadata->reservationId ?? null;
                if ($reservationId) {
                    $reservation = $reservationRepository->find($reservationId);
                    if ($reservation) {
                        // Vérifie le montant payé
                        $expectedAmount = $reservation->getProduct()->getPrice() * 0.10; // acompte 10%
                        $stripeAmount = $paymentIntent->amount_received / 100;
                        if (abs($expectedAmount - $stripeAmount) < 0.01) {
                            $reservation->setStatus('confirmée');
                            $reservation->setIsCompleted(false);
                            $entityManager->flush();
                        }
                    }
                }
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

    #[Route('/pay/checkout/{id}', name: 'app_stripe_checkout')]
    public function checkout($id, ReservationRepository $reservationRepository): Response
    {
        // 1. Récupérer la réservation
        $reservation = $reservationRepository->find($id);
        if (!$reservation) {
            $this->addFlash('danger', 'Réservation introuvable.');
            return $this->redirectToRoute('app_cart');
        }

        // 2. Calculer le montant de l'acompte (10% du prix total du séjour)
        $price = floatval($reservation->getProduct()->getPrice());
        $nights = ($reservation->getEndDate()->getTimestamp() - $reservation->getStartDate()->getTimestamp()) / 86400;
        $totalPrice = $price * $nights;
        $acompte = round($totalPrice * 0.10, 2);


        // 3. Créer la session Stripe Checkout
        \Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY'] ?? '');

        $request = $this->container->get('request_stack')->getCurrentRequest();
        $DOMAIN = $request->getSchemeAndHttpHost();

        $checkout_session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Acompte réservation : ' . $reservation->getProduct()->getName(),
                    ],
                    'unit_amount' => intval($acompte * 100), // en centimes
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $DOMAIN . $this->generateUrl('app_stripe_success', ['id' => $reservation->getId()]),
            'cancel_url' => $DOMAIN . $this->generateUrl('app_stripe_cancel'),
            'payment_intent_data' => [
                'metadata' => [
                    'reservationId' => $reservation->getId(),
                    'clientName' => $reservation->getUser() ? $reservation->getUser()->getFirstName() . ' ' . $reservation->getUser()->getLastName() : '',
                    'clientEmail' => $reservation->getUser() ? $reservation->getUser()->getEmail() : '',
                    'sejourDates' => $reservation->getStartDate()->format('d/m/Y') . ' - ' . $reservation->getEndDate()->format('d/m/Y'),
                    'logement' => $reservation->getProduct()->getName(),
                ],
            ],
        ]);

        // 4. Rediriger l'utilisateur vers Stripe Checkout
        return $this->redirect($checkout_session->url, 303);
    }
    //endregion
}
