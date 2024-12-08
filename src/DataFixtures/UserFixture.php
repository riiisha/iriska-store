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
        $admin = new User(
            'admin',
            'admin@example.com',
            '+79111111111',
            ['ROLE_ADMIN']
        );
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin'));

        $user = new User(
            'user',
            'user@example.com',
            '+79811111111',
            ['ROLE_USER']
        );
        $user->setPassword($this->passwordHasher->hashPassword($user, 'user'));

        $manager->persist($admin);
        $manager->persist($user);
        $manager->flush();
    }
}
