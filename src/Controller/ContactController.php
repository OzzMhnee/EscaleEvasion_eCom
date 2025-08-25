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
    //region Formulaire de contact
    #[Route('/contact', name: 'app_contact')]
    public function contact(Request $request, ProductRepository $productRepository, UserRepository $userRepository, EntityManagerInterface $em): Response
    {
        $productId = $request->query->get('productId');
        $product = $productId ? $productRepository->find($productId) : null;
        $userId = $request->query->get('id');
        $user = $userId ? $userRepository->find($userId) : $this->getUser();

        // Force l'autocomplétion pour l'IDE
        /** @var \App\Entity\User|null $user */
        $contactMessage = new ContactMessage();
        if ($user instanceof \App\Entity\User) {
            $contactMessage->setFirstName($user->getFirstName());
            $contactMessage->setLastName($user->getLastName());
            $contactMessage->setEmail($user->getEmail());
        }
        if ($product) {
            $contactMessage->setProductId($product->getId());
            $contactMessage->setProductName($product->getName());
        }

        $form = $this->createForm(ContactMessageType::class, $contactMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contactMessage->setCreatedAt(new \DateTimeImmutable());
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
    //endregion
    #[Route('/admin/messages', name: 'admin_messages')]
    public function adminMessages(EntityManagerInterface $em): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_EDITOR')) {
            throw $this->createAccessDeniedException();
        }
        $messages = $em->getRepository(\App\Entity\ContactMessage::class)->findBy([], ['createdAt' => 'DESC']);
        // Récupère les textes pré-formulés (à adapter selon ton entité)
        $predefinedAnswers = $em->getRepository(\App\Entity\PredefinedAnswer::class)->findAll();
        return $this->render('admin/messages.html.twig', [
            'messages' => $messages,
            'predefinedAnswers' => $predefinedAnswers,
        ]);
    }

        #[Route('/admin/messages/reply/{id}', name: 'admin_message_reply', methods: ['POST'])]
    public function reply(
        ContactMessage $contactMessage,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $answer = $request->request->get('answer_content');
        // On ignore totalement le champ "predefined_id" à la soumission : seul le contenu du textarea compte
        // Remplacement dynamique des variables %PRENOM% et %NOM% côté serveur AVANT enregistrement
        $firstName = $contactMessage->getFirstName();
        $lastName = $contactMessage->getLastName();
        if ($firstName && $lastName && $answer) {
            $answer = str_replace(['%PRENOM%', '%NOM%'], [$firstName, $lastName], $answer);
        }
        // Remplacement des \n par un vrai retour à la ligne (pour affichage HTML ou mail)
        $answer = str_replace('\n', "\n", $answer);

        $contactMessage->setAnswerContent($answer);
        $contactMessage->setAnsweredAt(new \DateTimeImmutable());
        $contactMessage->setAnsweredBy($this->getUser());
        $em->flush();

        $this->addFlash('success', 'Réponse envoyée !');
        return $this->redirectToRoute('admin_messages');
    }
    //endregion

    #[Route('/mes-messages', name: 'user_messages')]
    public function userMessages(EntityManagerInterface $em): Response
    {
        /** @var \App\Entity\User|null $user */
        $user = $this->getUser();
        if (!$user instanceof \App\Entity\User) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir vos messages.');
        }
        $messages = $em->getRepository(ContactMessage::class)->findBy(
            ['email' => $user->getEmail()],
            ['createdAt' => 'DESC']
        );
        return $this->render('user/messages.html.twig', [
            'messages' => $messages,
        ]);
    }
    #[Route('/mes-messages/read/{id}', name: 'user_message_read', methods: ['POST'])]
    public function markAsRead(ContactMessage $message, EntityManagerInterface $em): Response
    {
        /** @var \App\Entity\User|null $user */
        $user = $this->getUser();
        if (!$user instanceof \App\Entity\User) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour cette action.');
        }
        if ($message->getEmail() !== $user->getEmail()) {
            throw $this->createAccessDeniedException();
        }
        $message->setIsRead(true);
        $message->setReadAt(new \DateTimeImmutable());
        $em->flush();
        $this->addFlash('success', 'Message marqué comme lu.');
        return $this->redirectToRoute('user_messages');
    }
}
