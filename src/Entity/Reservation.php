<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    //region Properties
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\Column]
    private ?\DateTime $startDate = null;

    #[ORM\Column]
    private ?\DateTime $endDate = null;

    #[ORM\ManyToOne(targetEntity: \App\Entity\User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?\App\Entity\User $user = null;

    #[ORM\Column(length: 255)]
    private ?string $status = 'en attente';

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $cancelled_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $confirmed_at = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    private ?User $cancelled_by = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    private ?User $confirmed_by = null;

    #[ORM\Column]
    private ?bool $isCompleted = null;
    //endregion

    //region Getters and Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTime $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTime $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
    //endregion

    public function getCancelledAt(): ?\DateTimeImmutable
    {
        return $this->cancelled_at;
    }

    public function setCancelledAt(?\DateTimeImmutable $cancelled_at): static
    {
        $this->cancelled_at = $cancelled_at;

        return $this;
    }

    public function getConfirmedAt(): ?\DateTimeImmutable
    {
        return $this->confirmed_at;
    }

    public function setConfirmedAt(?\DateTimeImmutable $confirmed_at): static
    {
        $this->confirmed_at = $confirmed_at;

        return $this;
    }

    public function getCancelledBy(): ?User
    {
        return $this->cancelled_by;
    }

    public function setCancelledBy(?User $cancelled_by): static
    {
        $this->cancelled_by = $cancelled_by;

        return $this;
    }

    public function getConfirmedBy(): ?User
    {
        return $this->confirmed_by;
    }

    public function setConfirmedBy(?User $confirmed_by): static
    {
        $this->confirmed_by = $confirmed_by;

        return $this;
    }

    public function isCompleted(): ?bool
    {
        return $this->isCompleted;
    }

    public function setIsCompleted(bool $isCompleted): static
    {
        $this->isCompleted = $isCompleted;

        return $this;
    }
}
