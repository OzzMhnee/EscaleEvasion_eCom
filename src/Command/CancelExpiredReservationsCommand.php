<?php

namespace App\Command;

use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:cancel-expired-reservations',
    description: 'Annule automatiquement les réservations en attente de plus de 24h.',
)]
class CancelExpiredReservationsCommand extends Command
{
    private ReservationRepository $reservationRepository;
    private EntityManagerInterface $em;

    public function __construct(ReservationRepository $reservationRepository, EntityManagerInterface $em)
    {
        parent::__construct();
        $this->reservationRepository = $reservationRepository;
        $this->em = $em;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $nbCancelled = $this->reservationRepository->cancelExpiredPendingReservations();
        $output->writeln("<info>$nbCancelled réservation(s) en attente ont été automatiquement annulées.</info>");
        return Command::SUCCESS;
    }
}