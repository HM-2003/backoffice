<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Event\AddedPackageEvent;



class AddedPackageSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager ;
    }

     public static function getSubscribedEvents(): array
    {
        return [
            AddedPackageEvent::NAME => 'onTotalPrice',
        ];
    }

    public function onTotalPrice(AddedPackageEvent $event): void
    {
        $packageSubscription = $event->getPackageSubscription();
        $subscription = $packageSubscription->getSubscription();
        $TotalPrice = 0.0;
        
        foreach($subscription->getPackageSubscriptions() as $packageSubscription)
        {
            $TotalPrice += $packageSubscription->getPrice(); 
        }

        $subscription->setTotalPrice($TotalPrice);
        $this->entityManager->persist($subscription);
        $this->entityManager->flush();
    }
}
