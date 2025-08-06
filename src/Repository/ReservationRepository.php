<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 */

class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    /**
     * Annule toutes les réservations "en attente" créées il y a plus de 24h.
     * Retourne le nombre de réservations annulées.
     */
    public function cancelExpiredPendingReservations(): int
    {
        $qb = $this->createQueryBuilder('r');
        $qb->update()
            ->set('r.status', ':cancelled')
            ->where('r.status = :pending')
            ->andWhere('r.createdAt <= :expiredAt')
            ->setParameter('cancelled', 'annulée')
            ->setParameter('pending', 'en attente')
            ->setParameter('expiredAt', new \DateTimeImmutable('-24 hours'));
        return $qb->getQuery()->execute();
    }
}
