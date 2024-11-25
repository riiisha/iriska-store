<?php

namespace App\Service\User;

use App\DTO\User\UserEditDTO;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserUpdateService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository         $userRepository,
    )
    {
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
}
