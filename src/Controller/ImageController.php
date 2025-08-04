<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImageController extends AbstractController
{
    #[Route('/protected-bg/{filename}', name: 'protected_bg')]
    public function protectedBg(string $filename): Response
    {
        // Vérifie les droits d'accès ici (exemple : utilisateur connecté)
        // if (!$this->getUser()) {
        //     throw $this->createAccessDeniedException('Vous devez être connecté pour voir cette image.');
        // }

        $imagePath = $this->getParameter('kernel.project_dir') . '/private/img/' . $filename;

        if (!file_exists($imagePath)) {
            throw $this->createNotFoundException('Image non trouvée');
        }

         // Détecte le type d'image
        $ext = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
        if ($ext === 'png') {
            $image = imagecreatefrompng($imagePath);
            $contentType = 'image/png';
            $outputFunc = 'imagepng';
        } elseif (in_array($ext, ['jpg', 'jpeg'])) {
            $image = imagecreatefromjpeg($imagePath);
            $contentType = 'image/jpeg';
            $outputFunc = 'imagejpeg';
        } else {
            throw $this->createNotFoundException('Format d\'image non supporté');
        }

        // Prépare le texte du filigrane
        $watermarkText = 'Escale Evasion';
        $fontSize = 30;
        $angle = 0;
        $fontFile = $this->getParameter('kernel.project_dir') . '/public/fonts/Italianno-Regular.ttf';
        if (!file_exists($fontFile)) {
            $fontFile = 'C:\Windows\Fonts\arial.ttf';
        }
        if (!file_exists($fontFile)) {
            throw new \RuntimeException('Aucune police TTF trouvée pour le filigrane');
        }

        // Couleur blanche semi-transparente
        $white = imagecolorallocatealpha($image, 255, 255, 255, 60);

        // Calcule la taille du texte
        $bbox = imagettfbbox($fontSize, $angle, $fontFile, $watermarkText);
        $textWidth = $bbox[2] - $bbox[0];
        $textHeight = $bbox[1] - $bbox[7];

        // Positionne le texte en bas à droite
        $x = imagesx($image) - $textWidth - 340;
        $y = imagesy($image) - 140;

        // Ajoute le texte
        imagettftext($image, $fontSize, $angle, $x, $y, $white, $fontFile, $watermarkText);

        // Capture l'image en sortie
        ob_start();
        $outputFunc($image);
        $imageData = ob_get_clean();

        imagedestroy($image);

        return new Response($imageData, 200, [
            'Content-Type' => $contentType,
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }
     #[Route('/protected-img/{filename}', name: 'protected_img')]
    public function protectedImg(string $filename): Response
    {
        $imagePath = $this->getParameter('kernel.project_dir') . '/private/img/' . $filename;

        if (!file_exists($imagePath)) {
            throw $this->createNotFoundException('Image non trouvée');
        }

        // Détecte le type d'image
        $ext = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
        if ($ext === 'png') {
            $image = imagecreatefrompng($imagePath);
            $contentType = 'image/png';
            $outputFunc = 'imagepng';
        } elseif (in_array($ext, ['jpg', 'jpeg'])) {
            $image = imagecreatefromjpeg($imagePath);
            $contentType = 'image/jpeg';
            $outputFunc = 'imagejpeg';
        } else {
            throw $this->createNotFoundException('Format d\'image non supporté');
        }

        // Pas de filigrane ici, on sert l'image brute

        ob_start();
        $outputFunc($image);
        $imageData = ob_get_clean();

        imagedestroy($image);

        return new Response($imageData, 200, [
            'Content-Type' => $contentType,
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }
}