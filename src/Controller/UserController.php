<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ReservationRepository;
use App\Form\ProfileType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserController extends AbstractController
{
    #[Route('/admin/user', name: 'app_user')]
    public function displayAllUsers(UserRepository $repo): Response
    {
        
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'users' => $repo->findAll(),
        ]);
    }

    #[Route('/mon-profil', name: 'app_user_profile')]
    public function profile(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('danger', 'Vous devez être connecté pour accéder à votre profil.');
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }
            $entityManager->flush();
            $this->addFlash('success', 'Profil mis à jour avec succès !');
            return $this->redirectToRoute('app_user_profile');
        }

        // Gestion des erreurs du formulaire
        if ($form->isSubmitted() && !$form->isValid()) {
            // Ajouter toutes les erreurs du champ plainPassword comme messages flash
            foreach ($form->get('plainPassword')->getErrors(true) as $error) {
                $this->addFlash('danger', $error->getMessage());
            }
            // Optionnel : Ajouter un message flash générique pour les autres erreurs
            foreach ($form->getErrors(true) as $error) {
                $this->addFlash('danger', $error->getMessage());
            }
        }

        return $this->render('user/profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/user/uptoeditor/{id}', name: 'app_user_upToEditor')]
    public function updateUser(EntityManagerInterface $entityManager, User $user): Response
    {
       $user->setRoles(['ROLE_EDITOR']);
       $entityManager->flush();
       $this->addFlash('messages', 'L\'utilisateur a été promu au rôle d\'éditeur avec succès!');
       return $this->redirectToRoute('app_user');
    }
    #[Route('/admin/user/removeeditor/{id}', name: 'app_user_removeEditor')]
    public function removeEditor(EntityManagerInterface $entityManager, User $user): Response
    {
        $user->setRoles(['ROLE_USER']);
        $entityManager->flush();
        $this->addFlash('messages', 'Le rôle d\'éditeur a été retiré de l\'utilisateur avec succès!');
        return $this->redirectToRoute('app_user');
    }

    #[Route('/admin/user/delete/{id}', name: 'app_user_delete')]
    public function deleteUser(EntityManagerInterface $entityManager, User $user): Response
    {
        $entityManager->remove($user);
        $entityManager->flush();
        $this->addFlash('messages', 'L\'utilisateur a été supprimé avec succès!');
        return $this->redirectToRoute('app_user');
    }
    #[Route('/mes-reservations', name: 'app_user_reservations')]
    public function myReservations(ReservationRepository $reservationRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('danger', 'Vous devez être connecté pour voir vos réservations.');
            return $this->redirectToRoute('app_login');
        }

        $reservations = $reservationRepository->findBy(['user' => $user], ['startDate' => 'DESC']);

        return $this->render('user/reservations.html.twig', [
            'reservations' => $reservations,
        ]);
    }
}
