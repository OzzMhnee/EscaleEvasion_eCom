<?php

namespace App\Controller;

use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomePageController extends AbstractController
{
    #[Route('/', name: 'app_home_page')]
    public function index(ReservationRepository $reservationRepository, \App\Repository\ProductRepository $productRepository): Response
    {
        // Récupérer tous les produits disponibles
        $products = $productRepository->findBy(['isAvailable' => true]);
        $productCount = count($products);

        // Récupérer toutes les réservations en attente ou confirmées
        $reservations = $reservationRepository->createQueryBuilder('r')
            ->where('r.status IN (:statuses)')
            ->setParameter('statuses', ['en attente', 'confirmée'])
            ->getQuery()
            ->getResult();

        // On construit un tableau de tous les jours réservés par produit
        $dateProductMap = [];
        foreach ($reservations as $r) {
            $period = new \DatePeriod(
                $r->getStartDate(),
                new \DateInterval('P1D'),
                (clone $r->getEndDate())->modify('+1 day')
            );
            foreach ($period as $date) {
                $key = $date->format('Y-m-d');
                if (!isset($dateProductMap[$key])) {
                    $dateProductMap[$key] = [];
                }
                $dateProductMap[$key][$r->getProduct()->getId()] = true;
            }
        }

        // On désactive uniquement les jours où TOUS les produits sont réservés
        $fullyBookedDates = [];
        foreach ($dateProductMap as $date => $productsReserved) {
            if (count($productsReserved) >= $productCount && $productCount > 0) {
                $fullyBookedDates[] = $date;
            }
        }

        return $this->render('home_page/index.html.twig', [
            'controller_name' => 'HomePageController',
            'reservedRanges' => $fullyBookedDates,
        ]);
    }

    //region Affichage des conditions générales de vente (CGV)
    #[Route('/cgv', name: 'app_cgv')]
    public function cgv(): Response
    {
        return $this->render('legal/cgv.html.twig');
    }
}
