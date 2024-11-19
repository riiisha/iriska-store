<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{
    public function __construct(protected UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@example.com')
            ->setPassword($this->passwordHasher->hashPassword($admin, 'admin'))
            ->setPhone('+79111111111')
            ->setName('admin')
            ->setRoles(['ROLE_ADMIN']);

        $user = new User();
        $user->setEmail('user@example.com')
            ->setPassword($this->passwordHasher->hashPassword($user, 'user'))
            ->setPhone('+79811111111')
            ->setName('user')
            ->setRoles(['ROLE_USER']);

        $manager->persist($admin);
        $manager->persist($user);
        $manager->flush();
    }
}
