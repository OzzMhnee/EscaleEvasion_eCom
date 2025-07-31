<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
}
