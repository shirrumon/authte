<?php

namespace App\DataFixtures;

use App\Entity\Admin2;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    // ...
    public function load(ObjectManager $manager)
    {
        $user = new Admin2();
        $user->setEmail('admin@admin.com');

        $password =  $this->passwordEncoder->hashPassword($user, 'coolrx666');
        $user->setPassword($password);

        $manager->persist($user);
        $manager->flush();
    }
}
