<?php

namespace App\Service\User;

use App\DTO\User\UserEditDTO;
use App\DTO\User\UserUpdatePasswordDTO;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class UserUpdateService
{
    public function __construct(
        private EntityManagerInterface      $entityManager,
        private UserRepository              $userRepository,
        private UserPasswordHasherInterface $userPasswordHasher,
    ) {
    }

    public function update(UserEditDTO $userEditDTO): void
    {
        $user = $this->userRepository->getByEmail($userEditDTO->email);

        $user->update(
            $userEditDTO->name,
            $userEditDTO->phone,
            [$userEditDTO->role],
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function updatePassword(UserUpdatePasswordDTO $userUpdatePasswordDTO): void
    {
        $user = $this->userRepository->getById($userUpdatePasswordDTO->userId);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $userUpdatePasswordDTO->newPassword));
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
