<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BillController extends AbstractController
{
    //region Devis
    #[Route('/devis/{id}', name: 'app_devis')]
    public function devis(): Response
    {
        return $this->render('bill/devis.html.twig', [
            'controller_name' => 'BillController',
        ]);
    }
    //endregion

    //region Facture d'acompte
    #[Route('/depositBill/{id}', name: 'app_deposit_bill')]
    public function depositBill(): Response
    {
        return $this->render('bill/depositBill.html.twig', [
            'controller_name' => 'BillController',
        ]);
    }
    //endregion

    //region Facture    
    #[Route('/bill/{id}', name: 'app_bill')]
    public function index(): Response
    {
        return $this->render('bill/index.html.twig', [
            'controller_name' => 'BillController',
        ]);
    }
    //endregion
}
