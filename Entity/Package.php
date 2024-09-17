<?php

namespace App\Entity;

use App\Repository\PackageRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PackageRepository::class)]
class Package
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private float $price;

    #[ORM\OneToMany(mappedBy: 'package', targetEntity: PackageSubscription::class, cascade: ['persist', 'remove'])]
    private Collection $packageSubscriptions;

    public function __construct()
    {
        $this->packageSubscriptions = new ArrayCollection();
    }
    

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

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getPackageSubscriptions(): Collection
    {
        return $this->packageSubscriptions;
    }

    public function addPackageSubscription(PackageSubscription $packageSubscription): self
    {
        if (!$this->packageSubscriptions->contains($packageSubscription)) {
            $this->packageSubscriptions->add($packageSubscription);
            $packageSubscription->setPackage($this);
        }

        return $this;
    }
}
