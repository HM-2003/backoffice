<?php

namespace App\Entity;


use App\Repository\ClientRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\ManyToOne(targetEntity: City::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?City $city = null;

    #[ORM\OneToMany(targetEntity: Subscription::class, mappedBy: 'client')]
    private Collection $subscriptions;

    public function __construct()
    {
        $this->subscriptions = new ArrayCollection();
    }

     // Add getters and setters for subscriptions

     public function getSubscriptions(): Collection
     {
         return $this->subscriptions;
     }
 
     /*public function addSubscription(Subscription $subscription): self
     {
         if (!$this->subscriptions->contains($subscription)) {
             $this->subscriptions->add($subscription);
             $subscription->setClient($this);
         }
 
         return $this;
     }*/
 
     /*public function removeSubscription(Subscription $subscription): self
     {
         if ($this->subscriptions->removeElement($subscription)) {
             if ($subscription->getClient() === $this) {
                 $subscription->setClient(null);
             }
         }
 
         return $this;
     }*/
 


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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(City $city): self
    {
        $this->city = $city;

        return $this;
    }
}
