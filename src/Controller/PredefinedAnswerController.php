<?php

namespace App\Controller;

use App\Entity\PredefinedAnswer;
use App\Form\PredefinedAnswerType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/predefined/answer')]
final class PredefinedAnswerController extends AbstractController
{
    #[Route(name: 'app_predefined_answer_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $predefinedAnswers = $entityManager
            ->getRepository(PredefinedAnswer::class)
            ->findAll();

        return $this->render('predefined_answer/index.html.twig', [
            'predefined_answers' => $predefinedAnswers,
        ]);
    }

    #[Route('/new', name: 'app_predefined_answer_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $predefinedAnswer = new PredefinedAnswer();
        $form = $this->createForm(PredefinedAnswerType::class, $predefinedAnswer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($predefinedAnswer);
            $entityManager->flush();

            return $this->redirectToRoute('app_predefined_answer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('predefined_answer/new.html.twig', [
            'predefined_answer' => $predefinedAnswer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_predefined_answer_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PredefinedAnswer $predefinedAnswer, EntityManagerInterface $entityManager): Response
    {
        // Si la requête est POST et vient de l'édition inline
        if ($request->isMethod('POST') && $request->request->has('content')) {
            $token = $request->request->get('_token');
            if ($this->isCsrfTokenValid('edit' . $predefinedAnswer->getId(), $token)) {
                $predefinedAnswer->setContent($request->request->get('content'));
                $entityManager->flush();
                $this->addFlash('success', 'Message prédéfini modifié avec succès.');
            }
            return $this->redirectToRoute('app_predefined_answer_index');
        }

        $form = $this->createForm(PredefinedAnswerType::class, $predefinedAnswer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_predefined_answer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('predefined_answer/edit.html.twig', [
            'predefined_answer' => $predefinedAnswer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_predefined_answer_delete', methods: ['POST'])]
    public function delete(Request $request, PredefinedAnswer $predefinedAnswer, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$predefinedAnswer->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($predefinedAnswer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_predefined_answer_index', [], Response::HTTP_SEE_OTHER);
    }
}
