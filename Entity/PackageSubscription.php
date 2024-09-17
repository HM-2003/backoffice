<?php

namespace App\Entity;

use App\Repository\PackageSubscriptionRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PackageSubscriptionRepository::class)]
#[UniqueEntity(fields: ['package', 'subscription'], message: 'This combination of package and subscription already exists.')]
class PackageSubscription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?float $price = null;

    #[ORM\ManyToOne(targetEntity: Package::class)]   
     #[ORM\JoinColumn(nullable: false )]
    private Package $package ;

    #[ORM\ManyToOne(targetEntity: Subscription::class)]
    #[ORM\JoinColumn(nullable: false )]
   
    private Subscription $subscription ;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getPackage(): Package
    {
        return $this->package;
    }
    
     public function setPackage(Package $package): static
    {
        $this->package = $package;

        return $this;
    }

      public function removePackage(): static
    {
        $this->package = null;

        return $this;
    }

    public function getSubscription(): Subscription
    {
        return $this->subscription;
    }

    public function setSubscription(Subscription $subscription): static
    {
        $this->subscription = $subscription;

        return $this;
    }
}
