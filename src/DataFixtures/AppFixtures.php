<?php

namespace App\DataFixtures;

use App\Entity\Conseil;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    public function __construct(
        private UserPasswordHasherInterface $hasher
    ) {
    }
    public function load(ObjectManager $manager): void
    {

        //USER
        $user0 = new User();
        $user0->setAddress("51 avenue de la république")
            ->setCity('Tours')
            ->setCountry('France')
            ->setPostcode(37100)
            ->setEmail("lucas.detling@gmail.com")
            ->setFirstname('Lucas')
            ->setLastname('Detling')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($this->hasher->hashPassword($user0, "admin"));
        $manager->persist($user0);

        $user1 = new User();
        $user1->setAddress("51 avenue de la république")
            ->setCity('Tours')
            ->setCountry('France')
            ->setPostcode(37100)
            ->setEmail("marie.heuman@gmail.com")
            ->setFirstname('Marie')
            ->setLastname('Heuman')
            ->setRoles()
            ->setPassword($this->hasher->hashPassword($user1, "admin"));
        $manager->persist($user1);

        // CONSEIL
        $conseil0 = new Conseil();
        $conseil0->setCity('Tours')
            ->setDescription("C'est l'été, profitez pour sortir en tshirt + short")
            ->setMonth(07)
            ->setUser($user0);
        $manager->persist($conseil0);

        $manager->flush();
    }
}
