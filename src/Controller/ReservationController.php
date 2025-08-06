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
    //region Réservation d'un produit
    #[Route('/product/{id}', name: 'app_reservation_new', methods: ['GET', 'POST'])]
    public function new(
        Product $product,
        Request $request,
        EntityManagerInterface $entityManager,
        ReservationRepository $reservationRepository
    ): Response {
        $reservation = new Reservation();
        $reservation->setProduct($product);

        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        // Récupère les réservations existantes pour désactiver les dates déjà prises
        $existingReservations = $reservationRepository->findBy(['product' => $product]);
        $reservedRanges = [];
        foreach ($existingReservations as $r) {
            $reservedRanges[] = [
                'from' => $r->getStartDate()->format('Y-m-d'),
                'to' => $r->getEndDate()->format('Y-m-d'),
            ];
        }

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérification côté serveur : aucune réservation ne doit chevaucher
            $start = $reservation->getStartDate();
            $end = $reservation->getEndDate();
            $overlap = $reservationRepository->createQueryBuilder('r')
                ->andWhere('r.product = :product')
                ->andWhere('r.startDate < :end')
                ->andWhere('r.endDate > :start')
                ->setParameter('product', $product)
                ->setParameter('start', $start)
                ->setParameter('end', $end)
                ->getQuery()
                ->getResult();

            if (count($overlap) > 0) {
                $this->addFlash('danger', 'Ce logement est déjà réservé sur cette période.');
            } else {
                $reservation->setCreatedAt(new \DateTimeImmutable());
                if ($this->getUser()) {
                    $reservation->setUser($this->getUser());
                }
                if (!$reservation->getStatus()) {
                    $reservation->setStatus('en attente');
                }
                $entityManager->persist($reservation);
                $entityManager->flush();
                $this->addFlash('success', 'Réservation enregistrée !');
                return $this->redirectToRoute('app_user_reservations', ['id' => $product->getId()]);
            }
        }

        return $this->render('reservation/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
            'reservedRanges' => $reservedRanges,
        ]);
    }
    //endregion

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