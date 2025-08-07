<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use App\Repository\ProductRepository;
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

    //region Liste de toutes les réservations
    #[Route('/products/calendar', name: 'app_reservation_all_calendar', methods: ['GET'])]
    public function allCalendar(ReservationRepository $reservationRepository, ProductRepository $productRepository): Response
    {
        $reservations = $reservationRepository->findAll();
        $products = $productRepository->findAll();
        $AwaitingStatus = $reservationRepository->findBy(['status' => 'en attente']);
        $ConfirmedStatus = $reservationRepository->findBy(['status' => 'confirmée']);
        $CancelledStatus = $reservationRepository->findBy(['status' => 'annulée']);
        return $this->render('reservation/all_calendar.html.twig', [
            'reservations' => $reservations,
            'products' => $products,
            'AwaitingStatus' => $AwaitingStatus,
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

        $reservation->setStatus('annulée');
        $em->flush();

        $this->addFlash('success', 'La réservation a bien été annulée.');
        return $this->redirectToRoute('app_reservation_all_calendar');
    }
    //endregion
}