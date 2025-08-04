<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/product')]
final class ProductController extends AbstractController
{
    #[Route(name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Image principale
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('private_directory') . '/img',
                    $newFilename
                );
                $product->setImageUrl($newFilename);
            }
            // Image 2
            $imageFile2 = $form->get('imageFile2')->getData();
            if ($imageFile2) {
                $newFilename2 = uniqid().'.'.$imageFile2->guessExtension();
                $imageFile2->move(
                    $this->getParameter('private_directory') . '/img',
                    $newFilename2
                );
                $product->setImage2($newFilename2);
            }
            // Image 3
            $imageFile3 = $form->get('imageFile3')->getData();
            if ($imageFile3) {
                $newFilename3 = uniqid().'.'.$imageFile3->guessExtension();
                $imageFile3->move(
                    $this->getParameter('private_directory') . '/img',
                    $newFilename3
                );
                $product->setImage3($newFilename3);
            }
            // Image 4
            $imageFile4 = $form->get('imageFile4')->getData();
            if ($imageFile4) {
                $newFilename4 = uniqid().'.'.$imageFile4->guessExtension();
                $imageFile4->move(
                    $this->getParameter('private_directory') . '/img',
                    $newFilename4
                );
                $product->setImage4($newFilename4);
            }
            // Image 5
            $imageFile5 = $form->get('imageFile5')->getData();
            if ($imageFile5) {
                $newFilename5 = uniqid().'.'.$imageFile5->guessExtension();
                $imageFile5->move(
                    $this->getParameter('private_directory') . '/img',
                    $newFilename5
                );
                $product->setImage5($newFilename5);
            }

            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('success', 'Produit ajouté avec succès !');

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {



            // Image principale
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('private_directory') . '/img',
                    $newFilename
                );
                $product->setImageUrl($newFilename);
            }
            // Image 2
            $imageFile2 = $form->get('imageFile2')->getData();
            if ($imageFile2) {
                $newFilename2 = uniqid().'.'.$imageFile2->guessExtension();
                $imageFile2->move(
                    $this->getParameter('private_directory') . '/img',
                    $newFilename2
                );
                $product->setImage2($newFilename2);
            }
            // Image 3
            $imageFile3 = $form->get('imageFile3')->getData();
            if ($imageFile3) {
                $newFilename3 = uniqid().'.'.$imageFile3->guessExtension();
                $imageFile3->move(
                    $this->getParameter('private_directory') . '/img',
                    $newFilename3
                );
                $product->setImage3($newFilename3);
            }
            // Image 4
            $imageFile4 = $form->get('imageFile4')->getData();
            if ($imageFile4) {
                $newFilename4 = uniqid().'.'.$imageFile4->guessExtension();
                $imageFile4->move(
                    $this->getParameter('private_directory') . '/img',
                    $newFilename4
                );
                $product->setImage4($newFilename4);
            }
            // Image 5
            $imageFile5 = $form->get('imageFile5')->getData();
            if ($imageFile5) {
                $newFilename5 = uniqid().'.'.$imageFile5->guessExtension();
                $imageFile5->move(
                    $this->getParameter('private_directory') . '/img',
                    $newFilename5
                );
                $product->setImage5($newFilename5);
            }


            $entityManager->persist($product);
            $entityManager->flush();
            
            $this->addFlash('success', 'Produit modifié avec succès !');
            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }
        $this->addFlash('success', 'Produit supprimé avec succès !');
        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }


}