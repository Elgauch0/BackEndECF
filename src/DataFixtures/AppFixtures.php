<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $hashedPassword = $this->passwordHasher->hashPassword($admin, 'password');
        $admin->setEmail('admin@admin.com')
            ->setNom('nom admin')
            ->setPrenom('prenom admin')
            ->setRoles(['Admin_Role'])
            ->setPassword($hashedPassword);

        $veterinaire = new User();
        $hashedPassword = $this->passwordHasher->hashPassword($veterinaire, 'password');
        $veterinaire->setEmail('veterinaire@vet.com')
            ->setPrenom('prenom vet')
            ->setNom('nom vet')
            ->setRoles(['Veterinaire_Role'])
            ->setPassword($hashedPassword);

        $employe = new User();
        $hashedPassword = $this->passwordHasher->hashPassword($employe, 'password');
        $employe->setEmail('employe@emp.com')
            ->setPrenom('prenom employe')
            ->setNom('nom employe')
            ->setPassword($hashedPassword);

        $manager->persist($admin);
        $manager->persist($veterinaire);
        $manager->persist($employe);
        $manager->flush();
    }
}
