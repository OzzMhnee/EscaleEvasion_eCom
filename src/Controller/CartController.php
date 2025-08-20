<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/cart')]
#[IsGranted('ROLE_USER')]
class CartController extends AbstractController
{
    private ProductRepository $productRepository;
    private ReservationRepository $reservationRepository;

    public function __construct(ProductRepository $productRepository, ReservationRepository $reservationRepository)
    {
        $this->productRepository = $productRepository;
        $this->reservationRepository = $reservationRepository;
    }

    //region Affichage du panier
    #[Route(name: 'app_cart', methods: ['GET'])]
    public function index(
        SessionInterface $session,
        Request $request,
        ReservationRepository $reservationRepository,
        EntityManagerInterface $em
    ): Response
    {
        // 1. Annule automatiquement les réservations "en attente" de plus de 24h
        $nbCancelled = $reservationRepository->cancelExpiredPendingReservations();
        if ($nbCancelled > 0) {
            $this->addFlash('info', "$nbCancelled réservation(s) en attente ont été automatiquement annulées (délai dépassé).");
        }

        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('danger', 'Vous devez être connecté pour accéder à votre panier.');
            return $this->redirectToRoute('app_login');
        }

        // Récupérer les réservations "en attente" de l'utilisateur connecté
        $pendingReservations = $this->reservationRepository->findBy([
            'user' => $user,
            'status' => 'en attente'
        ]);

        // On stocke les ID des réservations dans la session (clé 'cart')
        $cart = $session->get('cart', []);
        foreach ($pendingReservations as $reservation) {
            if (!in_array(needle: $reservation->getId(), haystack: $cart)) { //chercher une aiguille dans une meule de foin
                $cart[] = $reservation->getId();
            }
        }
        $session->set('cart', $cart);

        // Calcul du nombre de réservations "en attente" dans le panier
        $cartCount = 0;
        if (!empty($cart)) {
            $qb = $reservationRepository->createQueryBuilder('r');
            $qb->select('COUNT(r.id)')
                ->where('r.user = :user')
                ->andWhere('r.status = :status')
                ->andWhere($qb->expr()->in('r.id', ':cartIds'))
                ->setParameter('user', $user)
                ->setParameter('status', 'en attente')
                ->setParameter('cartIds', $cart);
            $cartCount = (int) $qb->getQuery()->getSingleScalarResult();
        }
        $session->set('cart_count', $cartCount);

        return $this->render('cart/index.html.twig', [
            'pendingReservations' => $pendingReservations,
            'cart' => $cart,
            'cartCount' => $cartCount,
        ]);
    }
    //endregion

    //region Suppression d'une réservation du panier
    #[Route('/remove/{id}', name: 'app_cart_remove', methods: ['POST'])]
    public function removeReservation(int $id, SessionInterface $session, ReservationRepository $reservationRepository, EntityManagerInterface $em): Response {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('danger', 'Vous devez être connecté pour modifier votre panier.');
            return $this->redirectToRoute('app_login');
        }

        $reservation = $reservationRepository->find($id);
        if (!$reservation || $reservation->getUser() !== $user || $reservation->getStatus() !== 'en attente') {
            $this->addFlash('danger', 'Réservation introuvable ou non autorisée.');
            return $this->redirectToRoute('app_cart');
        }

        // Mise à jour le statut à "annulée"
        $reservation->setStatus('annulée');
        $em->flush();

        // Retirer l'ID de la session "cart"
        $cart = $session->get('cart', []);
        $cart = array_filter($cart, fn($rid) => $rid !== $id);
        $session->set('cart', $cart);

        $this->addFlash('success', 'La réservation a été annulée et retirée du panier.');
        return $this->redirectToRoute('app_cart');
    }
    //endregion

    //region suppression de toutes les réservations du panier
    #[Route('/clear', name: 'app_cart_clear', methods: ['POST'])]
    public function clearCart(SessionInterface $session, ReservationRepository $reservationRepository, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        // Récupérer les réservations "en attente" de l'utilisateur connecté
        $pendingReservations = $reservationRepository->findBy([
            'user' => $user,
            'status' => 'en attente'
        ]);

        // // Mettre à jour le statut de chaque réservation à "annulée"
        // foreach ($pendingReservations as $reservation) {
        //     $reservation->setStatus('annulée');
        //     $em->persist($reservation);
        // }
        // $em->flush();
        //////////////////// Partie commentée car nous avons déjà une commande pour annuler les réservations expirées
        //////////////////// faire :  php bin/console app:cancel-expired-reservations
        //////////////////// Il va falloir lancer cette commande régulièrement (cron job ou scheduler) pour annuler les réservations expirées
        //////////////////// Actuellement le statut se modifie lors de chaque visite du panier.
        ///// cf fichier src/Command/CancelExpiredReservationsCommand.php

        // Vider le panier dans la session
        $session->remove('cart');
        $session->set('cart_count', 0);

        $this->addFlash('success', 'Toutes les réservations en attente ont été annulées et le panier a été vidé.');
        return $this->redirectToRoute('app_cart');
    }
    //endregion
}
