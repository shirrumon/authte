<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Service\UserSetUp;

class AppFixtures extends Fixture
{
    private $set;
    public function __construct(UserSetUp $setUp)
    {
        $this->set = $setUp;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $this->set->AdminSet($user);
        $manager->persist($user);
        $manager->flush();
    }
}
