<?php

namespace App\EventSubscriber;

use App\Repository\ContactMessageRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\SecurityBundle\Security;


class UnreadMessagesSubscriber implements EventSubscriberInterface
{
    private $contactMessageRepository;
    private $security;
    private $requestStack;

    public function __construct(ContactMessageRepository $repo, Security $security, RequestStack $requestStack)
    {
        $this->contactMessageRepository = $repo;
        $this->security = $security;
        $this->requestStack = $requestStack;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        // N'exécute que sur la requête principale
        if (!$event->isMainRequest()) {
            return;
        }
        $request = $event->getRequest();
        if (!$request->hasSession()) {
            return;
        }
        $session = $request->getSession();
        if (!$session) {
            return;
        }
        // Ne fait rien si l'utilisateur n'est pas connecté ou si le token n'est pas authentifié
        $user = null;
        try {
            $user = $this->security->getUser();
        } catch (\Throwable $e) {
            // Ignore toute erreur d'accès au token
            return;
        }
        if (!$user) {
            return;
        }
        // Compteur pour admin/editor
        if (in_array('ROLE_ADMIN', $user->getRoles()) || in_array('ROLE_EDITOR', $user->getRoles())) {
            $count = $this->contactMessageRepository->count(['isRead' => false, 'answerContent' => null]);
            $session->set('unread_messages_count', $count);
        } else {
            // Compteur pour user simple : messages où il a eu une réponse (answerContent IS NOT NULL) et isRead = false
            $qb = $this->contactMessageRepository->createQueryBuilder('m');
            $qb->select('COUNT(m.id)')
                ->where('m.email = :email')
                ->andWhere('m.answerContent IS NOT NULL')
                ->andWhere('m.isRead = false')
                ->setParameter('email', $user->getEmail());
            $count = (int) $qb->getQuery()->getSingleScalarResult();
            $session->set('unread_messages_count', $count);
        }
    }

    public static function getSubscribedEvents()
    {
        return [KernelEvents::REQUEST => 'onKernelRequest'];
    }
}
