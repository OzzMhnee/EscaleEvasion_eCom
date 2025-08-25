<?php

namespace App\Controller;

use App\Entity\Product;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('admin/reservation')]
class ReservationController extends AbstractController
{
    //region Liste des réservations d'un produit
    #[Route('/product/{id}/calendar', name: 'app_reservation_calendar', methods: ['GET'])]
    public function calendar(Product $product, ReservationRepository $reservationRepository): Response
    {
        $reservations = $reservationRepository->findBy(['product' => $product]);
        return $this->render('reservation/calendar.html.twig', [
            'product' => $product,
            'reservations' => $reservations,
        ]);
    }
    //endregion

    //region Confirmation du paiement sur place (éditeur/admin)
    #[Route('/confirm-on-site/{id}', name: 'reservation_confirm_on_site', methods: ['POST'])]
    public function confirmOnSite(Reservation $reservation, EntityManagerInterface $em, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EDITOR');
        if ($this->isCsrfTokenValid('confirmonsite' . $reservation->getId(), $request->request->get('_token'))) {
            $reservation->setStatus('confirmée');
            $reservation->setIsCompleted(false);
            $reservation->setConfirmedAt(new \DateTimeImmutable());
            $reservation->setConfirmedBy($this->getUser());
            $em->flush();
            $this->addFlash('success', 'Le paiement sur place a bien été confirmé et la réservation est repassée au statut "confirmée".');
        } else {
            $this->addFlash('danger', 'Token CSRF invalide.');
        }
        return $this->redirectToRoute('app_reservation_all_calendar');
    }
    //endregion

    //region Liste de toutes les réservations
    #[Route('/products/calendar', name: 'app_reservation_all_calendar', methods: ['GET'])]
    public function allCalendar(PaginatorInterface $paginator, Request $request, ReservationRepository $reservationRepository, ProductRepository $productRepository, EntityManagerInterface $entityManager): Response
    {

        // Partie Paginator
        $query = $productRepository->createQueryBuilder('p')->getQuery(); // Crée une requête Doctrine pour récupérer tous les produits (p est l’alias de la table)

        $pagination = $paginator->paginate(  // Utilise le service KnpPaginator pour paginer la requête.
            $query, // Passe la requête Doctrine (pas le résultat, mais bien la requête elle-même)
            $request->query->getInt('page', 1), // Récupère le numéro de page dans l’URL (?page=2), ou 1 par défaut.
            10 // Définit le nombre de produits à afficher par page (ici 10).
        );

        //Next
        $reservations = $reservationRepository->findAll();
        $products = $productRepository->findAll();
        $AwaitingStatus = $reservationRepository->findBy(['status' => 'en attente']);
        $ConfirmedStatus = $reservationRepository->findBy(['status' => 'confirmée']);
        $CancelledStatus = $reservationRepository->findBy(['status' => 'annulée']);

        // En profiter pour passer le bool is_completed sur True pour les réservations complétées dont les dates de fin de réservations sont dépassées
        $now = new \DateTime();
        foreach ($ConfirmedStatus as $Element) {
            if ($Element->getEndDate() < $now) {
                $Element->setIsCompleted(1);
            }
        }
        $entityManager->flush();
        $Finalised = $reservationRepository->findBy(['isCompleted' => '1']);
        return $this->render('reservation/all_calendar.html.twig', [
            'pagination' => $pagination,
            'reservations' => $reservations,
            'products' => $products,
            'AwaitingStatus' => $AwaitingStatus,
            'FinalisedStatus' => $Finalised,
            'ConfirmedStatus' => $ConfirmedStatus,
            'CancelledStatus' => $CancelledStatus,
        ]);
    }
    //endregion

    //region Annulation d'une réservation (admin ou super admin)
    #[Route('/cancel/{id}', name: 'reservation_cancel', methods: ['GET', 'POST'])]
    public function cancelReservation(int $id, ReservationRepository $reservationRepository, EntityManagerInterface $em, Request $request): Response
    {
        $reservation = $reservationRepository->find($id);
        if (!$reservation) {
            $this->addFlash('danger', 'Réservation introuvable.');
            return $this->redirectToRoute('app_reservation_all_calendar');
        }

        // On ne peut annuler que les réservations "en attente" ou "confirmée"
        if (!in_array($reservation->getStatus(), ['en attente', 'confirmée'])) {
            $this->addFlash('danger', 'Seules les réservations en attente ou confirmées peuvent être annulées.');
            return $this->redirectToRoute('app_reservation_all_calendar');
        }

        $reservation->setCancelledBy($this->getUser());
        $reservation->setStatus('annulée');

        $em->flush();

        $this->addFlash('success', 'La réservation a bien été annulée.');
        return $this->redirectToRoute('app_reservation_all_calendar');
    }
    //endregion
}
