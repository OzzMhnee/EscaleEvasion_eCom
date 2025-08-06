<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    //region Properties
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $price = null;

    /**
     * @var Collection<int, SubCategory>
     */
    #[ORM\ManyToMany(targetEntity: SubCategory::class, inversedBy: 'products')]
    private Collection $subCategory;

    #[ORM\Column]
    private ?int $couchages = null;

    #[ORM\Column(length: 255)]
    private ?string $departement = null;

    #[ORM\Column]
    private ?\DateTime $startDate = null;

    #[ORM\Column]
    private ?\DateTime $endDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageUrl = null;

    #[ORM\Column(length: 255)]
    private ?string $city = null;

    #[ORM\Column]
    private ?int $surface = null;

    #[ORM\Column]
    private ?bool $isAvailable = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Image2 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Image3 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Image4 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Image5 = null;

    #[ORM\Column]
    private ?bool $isSwimmingPool = null;

    #[ORM\Column]
    private ?bool $isBath = null;

    #[ORM\Column]
    private ?bool $isClim = null;

    #[ORM\Column]
    private ?bool $isLaveLinge = null;

    #[ORM\Column]
    private ?bool $isSecheLinge = null;

    #[ORM\Column]
    private ?bool $isLaveVaisselle = null;

    #[ORM\Column]
    private ?bool $isChauffage = null;

    /**
     * @var Collection<int, Reservation>
     */
    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'product')]
    private Collection $reservations;
    //endregion

    //region Constructor
    public function __construct()
    {
        $this->subCategory = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        
    }
    //endregion

    //region Getters and Setters
    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection<int, SubCategory>
     */
    public function getSubCategory(): Collection
    {
        return $this->subCategory;
    }

    public function addSubCategory(SubCategory $subCategory): static
    {
        if (!$this->subCategory->contains($subCategory)) {
            $this->subCategory->add($subCategory);
        }

        return $this;
    }

    public function removeSubCategory(SubCategory $subCategory): static
    {
        $this->subCategory->removeElement($subCategory);

        return $this;
    }

    public function getCouchages(): ?int
    {
        return $this->couchages;
    }

    public function setCouchages(int $couchages): static
    {
        $this->couchages = $couchages;

        return $this;
    }

    public function getDepartement(): ?string
    {
        return $this->departement;
    }

    public function setDepartement(string $departement): static
    {
        $this->departement = $departement;

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

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): static
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getSurface(): ?int
    {
        return $this->surface;
    }

    public function setSurface(int $surface): static
    {
        $this->surface = $surface;

        return $this;
    }

    public function isAvailable(): ?bool
    {
        return $this->isAvailable;
    }

    public function setIsAvailable(bool $isAvailable): static
    {
        $this->isAvailable = $isAvailable;

        return $this;
    }

    public function getImage2(): ?string
    {
        return $this->Image2;
    }

    public function setImage2(?string $Image2): static
    {
        $this->Image2 = $Image2;

        return $this;
    }

    public function getImage3(): ?string
    {
        return $this->Image3;
    }

    public function setImage3(?string $Image3): static
    {
        $this->Image3 = $Image3;

        return $this;
    }

    public function getImage4(): ?string
    {
        return $this->Image4;
    }

    public function setImage4(?string $Image4): static
    {
        $this->Image4 = $Image4;

        return $this;
    }

    public function getImage5(): ?string
    {
        return $this->Image5;
    }

    public function setImage5(?string $Image5): static
    {
        $this->Image5 = $Image5;

        return $this;
    }

    public function isSwimmingPool(): ?bool
    {
        return $this->isSwimmingPool;
    }

    public function setIsSwimmingPool(bool $isSwimmingPool): static
    {
        $this->isSwimmingPool = $isSwimmingPool;

        return $this;
    }

    public function isBath(): ?bool
    {
        return $this->isBath;
    }

    public function setIsBath(bool $isBath): static
    {
        $this->isBath = $isBath;

        return $this;
    }

    public function isClim(): ?bool
    {
        return $this->isClim;
    }

    public function setIsClim(bool $isClim): static
    {
        $this->isClim = $isClim;

        return $this;
    }

    public function isLaveLinge(): ?bool
    {
        return $this->isLaveLinge;
    }

    public function setIsLaveLinge(bool $isLaveLinge): static
    {
        $this->isLaveLinge = $isLaveLinge;

        return $this;
    }

    public function isSecheLinge(): ?bool
    {
        return $this->isSecheLinge;
    }

    public function setIsSecheLinge(bool $isSecheLinge): static
    {
        $this->isSecheLinge = $isSecheLinge;

        return $this;
    }

    public function isLaveVaisselle(): ?bool
    {
        return $this->isLaveVaisselle;
    }

    public function setIsLaveVaisselle(bool $isLaveVaisselle): static
    {
        $this->isLaveVaisselle = $isLaveVaisselle;

        return $this;
    }

    public function isChauffage(): ?bool
    {
        return $this->isChauffage;
    }

    public function setIsChauffage(bool $isChauffage): static
    {
        $this->isChauffage = $isChauffage;

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setProduct($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getProduct() === $this) {
                $reservation->setProduct(null);
            }
        }

        return $this;
    }
    //endregion
}