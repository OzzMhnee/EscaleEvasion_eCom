<?php

namespace App\Entity;


use App\Repository\CategoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    //region Properties
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, SubCategory>
     */
    #[ORM\OneToMany(mappedBy: 'Category', targetEntity: \App\Entity\SubCategory::class, orphanRemoval: true)]
    private Collection $subCategories;
    //endregion

    //region Constructor
    public function __construct()
    {
        $this->subCategories = new ArrayCollection();
    }
    //endregion

    //region Getters and Setters
    public function getId(): ?int
    {
        return $this->id;
    }
    /**
     * @return Collection<int, SubCategory>
     */
    public function getSubCategories(): Collection
    {
        return $this->subCategories;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getName();
    }
    //endregion
}