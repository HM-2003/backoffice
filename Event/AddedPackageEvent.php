<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;
use App\Entity\PackageSubscription;


class AddedPackageEvent extends Event 
{
    public const NAME = 'package.added';

    private $packageSubscription;

    public function __construct(PackageSubscription $packageSubscription)
    {
        $this->packageSubscription = $packageSubscription;
    }
    public function  getPackageSubscription(): packageSubscription
    {
        return $this->packageSubscription;
    }

}