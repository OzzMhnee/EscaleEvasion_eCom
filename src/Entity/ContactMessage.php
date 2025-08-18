<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Dom\Text;

#[ORM\Entity]
class ContactMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(type: 'text')]
    private ?string $message = null;

    #[ORM\Column(nullable: true)]
    private ?string $productName = null;

    #[ORM\Column(nullable: true)]
    private ?int $productId = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $read_At = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $answered_at = null;

    #[ORM\ManyToOne(inversedBy: 'contactMessages')]
    private ?User $answered_by = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $answer_content = null;

    // Getters and setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function getProductName(): ?string
    {
        return $this->productName;
    }

    public function setProductName(?string $productName): self
    {
        $this->productName = $productName;
        return $this;
    }

    public function getProductId(): ?int
    {
        return $this->productId;
    }

    public function setProductId(?int $productId): self
    {
        $this->productId = $productId;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getReadAt(): ?\DateTimeImmutable
    {
        return $this->read_At;
    }

    public function setReadAt(?\DateTimeImmutable $read_At): static
    {
        $this->read_At = $read_At;

        return $this;
    }

    public function getAnsweredAt(): ?\DateTimeImmutable
    {
        return $this->answered_at;
    }

    public function setAnsweredAt(?\DateTimeImmutable $answered_at): static
    {
        $this->answered_at = $answered_at;

        return $this;
    }

    public function getAnsweredBy(): ?User
    {
        return $this->answered_by;
    }

    public function setAnsweredBy(?User $answered_by): static
    {
        $this->answered_by = $answered_by;

        return $this;
    }

    public function getAnswerContent(): ?string
    {
        return $this->answer_content;
    }

    public function setAnswerContent(?string $answer_content): self
    {
        $this->answer_content = $answer_content;

        return $this;
    }
}