<?php

namespace App\Controller;

use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Dompdf\Dompdf;
use Dompdf\Options;

////////////// Les lignes de code sont pratiquement identiques.
////////////// (mis à part le twig vers lequel ils renvoient)
///////// A FAIRE POUR AMELIORER :
///////// Créer un constructeur avec pour rassemble le code et l'appeler dans les trois routes avec chemin twig en paramètre.

final class BillController extends AbstractController
{
    //region Devis
    #[Route(path: '/devis/{id}', name: 'app_devis')]
    public function devis($id, ReservationRepository $reservations): Response
    {
        $reservation = $reservations->find($id);

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont','Arial');
        $domPdf = new Dompdf($pdfOptions);
        $html = $this->renderView('bill/devis.html.twig', [
            'controller_name' => 'BillController',
            'reservation' => $reservation,
        ]);
        $domPdf->loadHtml($html);
        $domPdf->setPaper('A4', 'portrait');
        $domPdf->render();
        $domPdf->stream('EsacaleEvasion-Facture'.$reservation->getId().'.pdf',[
            'Attachment' => true
        ]);

        exit;
    }
    //endregion


    //region Facture d'acompte
    #[Route('/depositBill/{id}', name: 'app_deposit_bill')]
    public function depositBill($id, ReservationRepository $reservations): Response
    {
        $reservation = $reservations->find($id);

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont','Arial');
        $domPdf = new Dompdf($pdfOptions);
        $html = $this->renderView('bill/depositBill.html.twig', [
            'controller_name' => 'BillController',
            'reservation' => $reservation,
        ]);
        $domPdf->loadHtml($html);
        $domPdf->setPaper('A4', 'portrait');
        $domPdf->render();
        $domPdf->stream('EsacaleEvasion-Facture'.$reservation->getId().'.pdf',[
            'Attachment' => true
        ]);

        exit;
    }
    //endregion

    //region Facture
    #[Route('/bill/{id}', name: 'app_bill')]
    public function bill($id, ReservationRepository $reservations): Response
    {
        $reservation = $reservations->find($id);

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont','Arial');
        $domPdf = new Dompdf($pdfOptions);
        $html = $this->renderView('bill/bill.html.twig', [
            'controller_name' => 'BillController',
            'reservation' => $reservation,
        ]);
        $domPdf->loadHtml($html);
        $domPdf->setPaper('A4', 'portrait');
        $domPdf->render();
        $domPdf->stream('EsacaleEvasion-Facture'.$reservation->getId().'.pdf',[
            'Attachment' => true
        ]);

        exit;
    }
    //endregion

}
