<?php

namespace App\DataFixtures;

use App\Entity\User;
use DateTime;
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
        $user = new User();

        $user->setName('admin');
        $user->setSurname('admin');
        $user->setPesel(1212121212);
        $user->setLang('php');
        $user->setDate(new \DateTime('1999-01-01'));
        $user->setCreateDate(new DateTime('now'));
        $user->setIsVerified(true);
        $user->setMethod('CLI');

        $user->setEmail('admin@gmail.com');

        $user->setRoles((array)'ROLE_ADMIN');

        $password =  $this->passwordEncoder->hashPassword($user, 'coolrx666');
        $user->setPassword($password);

        $manager->persist($user);
        $manager->flush();
    }
}
