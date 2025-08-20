<?php

namespace App\Controller;


use App\Entity\User;
use App\Entity\Product;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mime\Email;
use App\Repository\ReservationRepository;
use App\Repository\ProductRepository;
use App\Repository\SubCategoryRepository;
use App\Form\ProfileType;
use App\Repository\CategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserController extends AbstractController
{
    public function __construct(private MailerInterface $mailer) {}

    //region Liste des utilisateurs ADMIN
    #[Route('/admin/user', name: 'app_user')]
    public function displayAllUsers(UserRepository $repo): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'users' => $repo->findAll(),
        ]); 
    }
    //endregion

    //region Fiche d'un utilisateur par cet utilisateur
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
    //endregion

    //region Promotion d'un utilisateur à éditeur ADMIN
    #[Route('/admin/user/uptoeditor/{id}', name: 'app_user_upToEditor')]
    public function updateUser(EntityManagerInterface $entityManager, User $user): Response
    {
       $user->setRoles(['ROLE_EDITOR']);
       $entityManager->flush();
       $this->addFlash('messages', 'L\'utilisateur a été promu au rôle d\'éditeur avec succès!');
       return $this->redirectToRoute('app_user');
    }
    //endregion

    //region Retrait du rôle d'éditeur ADMIN
    #[Route('/admin/user/removeeditor/{id}', name: 'app_user_removeEditor')]
    public function removeEditor(EntityManagerInterface $entityManager, User $user): Response
    {
        $user->setRoles(['ROLE_USER']);
        $entityManager->flush();
        $this->addFlash('messages', 'Le rôle d\'éditeur a été retiré de l\'utilisateur avec succès!');
        return $this->redirectToRoute('app_user');
    }
    //endregion

    //region Suppression d'un utilisateur ADMIN
    #[Route('/admin/user/delete/{id}', name: 'app_user_delete')]
    public function deleteUser(EntityManagerInterface $entityManager, User $user): Response
    {
        $entityManager->remove($user);
        $entityManager->flush();
        $this->addFlash('messages', 'L\'utilisateur a été supprimé avec succès!');
        return $this->redirectToRoute('app_user');
    }
    //endregion

    //region Mes réservations 
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
    //endregion

    //region Affichage de tous les produits (All Categories)
    #[Route('/all-products', name: 'app_user_all_products')]
    public function allProducts(ProductRepository $productRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $query = $productRepository->createQueryBuilder('p')->getQuery(); // Crée une requête Doctrine pour récupérer tous les produits (p est l’alias de la table)

        $pagination = $paginator->paginate(  // Utilise le service KnpPaginator pour paginer la requête.
            $query, // Passe la requête Doctrine (pas le résultat, mais bien la requête elle-même)
            $request->query->getInt('page', 1), // Récupère le numéro de page dans l’URL (?page=2), ou 1 par défaut.
            4 // Définit le nombre de produits à afficher par page (ici 4).
        );

        return $this->render('user/all-products.html.twig', [
            'pagination' => $pagination, // Passe la variable paginée au template (à utiliser dans le Twig)
        ]);
    }
    //endregion

    //region affichage de tous les produits par catégorie
    #[Route('/category/{id}/products', name: 'app_user_category_products')]
    public function categoryProducts(ProductRepository $productRepository, $id, CategoryRepository $categoryRepository, Request $request, PaginatorInterface $paginator): Response
    { 
        // récupération de la catégorie par son ID
        $category = $categoryRepository->find($id);

        // création d'une requête Doctrine pour récupérer tous lles produits liés à cette catégorie
        $query = $productRepository->createQueryBuilder('p')
            ->join('p.subCategory', 's')
            ->where('s.Category = :catId')
            ->setParameter('catId', $id)
            ->getQuery();

        // Utilisation du service KnpPaginator pour paginer la requête
        $pagination = $paginator->paginate(
            $query, // Passe la requête Doctrine (pas le résultat, mais bien la requête elle-même)
            $request->query->getInt('page', 1), // Récupère le numéro de page dans l’URL (?page=2), ou 1 par défaut.
            4 // Définit le nombre de produits à afficher par page (ici 6).
        );

        //Si aucun produit n'est trouvé, afficher un message flash
        if (count($pagination) === 0) {
            $this->addFlash('danger', 'Aucun produit trouvé pour cette catégorie.');
            return $this->redirectToRoute('app_user_all_products');
        }

        // On passe la pagination et la catégorie au template
        return $this->render('user/category-products.html.twig', [
            'pagination' => $pagination, // Passe la variable paginée au template (à utiliser dans le Twig)
            'category' => $category, // Passe la catégorie pour l'affichage
        ]);
    }
    //endregion

    //region Affichage de tous les produits par sous-catégorie
    #[Route('/sub-category/{id}/products', name: 'app_user_sub_category_products')]
    public function subCategoryProducts(ProductRepository $productRepository, $id, SubCategoryRepository $subCategoryRepository): Response
    {
        $subCategory = $subCategoryRepository->find($id);
        $products = $productRepository->createQueryBuilder('p')
            ->join('p.subCategory', 's')
            ->where('s.id = :subId')
            ->setParameter('subId', $id)
            ->getQuery()
            ->getResult();

        if (!$products) {
            $this->addFlash('danger', 'Aucun produit trouvé pour cette sous-catégorie.');
            return $this->redirectToRoute('app_user_all_products');
        }
        return $this->render('user/sub-category-products.html.twig', [
            'products' => $products,
            'subCategory' => $subCategory,
        ]);
    }
    //endregion

    //region Affichage de la fiche d'un produit pour le user
    #[Route('/user/product/{id}', name: 'app_user_product')]
    public function product(ProductRepository $productRepository, $id): Response
    {
        $product = $productRepository->find($id);
        if (!$product) {
            $this->addFlash('danger', 'Produit non trouvé.');
            return $this->redirectToRoute('app_user_all_products');
        }
        return $this->render('product/userShow.html.twig', [
            'product' => $product,
        ]);
    }
    //endregion

    //region Réservation d'un produit
    #[Route('/product/{id}/reservation', name: 'app_reservation_new_user', methods: ['GET', 'POST'])]
    public function new(Product $product, Request $request, EntityManagerInterface $entityManager, ReservationRepository $reservationRepository): Response
    {
        $reservation = new \App\Entity\Reservation();
        $reservation->setProduct($product);

        $form = $this->createForm(\App\Form\ReservationType::class, $reservation);
        $form->handleRequest($request);

        // Récupère les réservations existantes pour désactiver les dates déjà prises
        $existingReservations = $reservationRepository->findBy([
            'product' => $product,
            'status' => ['en attente', 'confirmée']]);
        $reservedRanges = [];
        foreach ($existingReservations as $r) {
            $reservedRanges[] = [
                'from' => $r->getStartDate()->format('Y-m-d'),
                'to' => $r->getEndDate()->format('Y-m-d'),
            ];
        }
        // // Debugging: Check reserved ranges
        // dd($reservedRanges);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérification côté serveur : aucune réservation ne doit chevaucher
            $start = $reservation->getStartDate();
            $end = $reservation->getEndDate();
            $overlap = $reservationRepository->createQueryBuilder('r')
                ->andWhere('r.product = :product')
                ->andWhere('r.startDate < :end')
                ->andWhere('r.endDate > :start')
                ->andWhere('r.status IN (:statuses)')
                ->setParameter('product', $product)
                ->setParameter('start', $start)
                ->setParameter('end', $end)
                ->setParameter('statuses', ['en attente', 'confirmée'])
                ->getQuery()
                ->getResult();

            if (count($overlap) > 0) {
                $this->addFlash('danger', 'Ce logement est déjà réservé sur cette période.');
            } else {
                $reservation->setCreatedAt(new \DateTimeImmutable());
                if ($this->getUser()) {
                    $reservation->setUser($this->getUser());
                }
                if (!$reservation->getStatus()) {
                    $reservation->setStatus('en attente');
                }
                $reservation->setIsCompleted(false);
                $entityManager->persist($reservation);
                $entityManager->flush();
                $this->addFlash('success', 'Réservation enregistrée !');

                //partie pour générer un mail de réservation avec devis
                $html = $this->renderView('mail/enAttente.html.twig', [
                    'reservation' => $reservation
                ]);
                $email = (new Email())
                    ->from('EscaleEvasion@EE.fr')
                    ->to($reservation->getUser()->getEmail())
                    ->subject('Réservation en attente, valable 24H')
                    ->html($html);
                $this->mailer->send($email);

                return $this->redirectToRoute('app_cart');
            }
        }

        return $this->render('reservation/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
            'reservedRanges' => $reservedRanges,
        ]);
    }
    //endregion
}
