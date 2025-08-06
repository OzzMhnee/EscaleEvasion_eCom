<?php

namespace App\Controller;


use App\Entity\ContactMessage;
use App\Form\ContactMessageType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function contact(Request $request, ProductRepository $productRepository, UserRepository $userRepository, EntityManagerInterface $em): Response
    {
        $productId = $request->query->get('productId');
        $product = $productId ? $productRepository->find($productId) : null;
        $userId = $request->query->get('id');
        $user = $userId ? $userRepository->find($userId) : $this->getUser();

        $contactMessage = new ContactMessage();
        if ($user) {
            /** @var \App\Entity\User $user */
            $contactMessage->setFirstName($user->getFirstName());
            $contactMessage->setLastName($user->getLastName());
            $contactMessage->setEmail($user->getEmail());
        }
        if ($product) {
            $contactMessage->setProductId($product->getId());
            $contactMessage->setProductName($product->getName());
        }
        $contactMessage->setCreatedAt(new \DateTimeImmutable());

        $form = $this->createForm(ContactMessageType::class, $contactMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($contactMessage);
            $em->flush();
            $this->addFlash('success', 'Votre message a bien été envoyé !');
            return $this->redirectToRoute('app_home_page');
        }

        $userData = [
            'first_name' => $user ? $user->getFirstName() : '',
            'last_name' => $user ? $user->getLastName() : '',
            'email' => $user ? $user->getEmail() : '',
        ];

        return $this->render('contact/contact.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
            'userData' => $userData,
        ]);
    }
}