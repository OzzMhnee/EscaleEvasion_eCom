<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search_results', methods: ['GET'])]
    public function search(Request $request, ProductRepository $productRepository): Response
    {
        $query = $request->query->get('q', ''); // Récupérer la requête de recherche
        $products = [];

        if ($query) {
            $products = $productRepository->searchEngine($query);
        }

        return $this->render('search/searchResults.html.twig', [
            'products' => $products,
            'query' => $query,
        ]);
    }
}
