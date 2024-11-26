<?php

namespace App\Service\User;

use App\DTO\User\UserRegisterDTO;
use App\Entity\User;
use App\Exception\UserExistsException;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class UserRegisterService
{
    public function __construct(
        private EntityManagerInterface      $entityManager,
        private UserRepository              $userRepository,
        private UserPasswordHasherInterface $userPasswordHasher
    ) {
    }

    /**
     * @throws UserExistsException
     */
    public function register(UserRegisterDTO $userRegisterDTO): User
    {
        /*TODO - создать событие и пинговать сторонний сервис, чтобы отправили смс-ку */
        $user = $this->userRepository->findByEmail($userRegisterDTO->email);
        if ($user) {
            throw new UserExistsException();
        }

        $newUser = new User(
            $userRegisterDTO->name,
            $userRegisterDTO->email,
            $userRegisterDTO->phone,
        );
        $newUser->setPassword($this->userPasswordHasher->hashPassword($newUser, $userRegisterDTO->password));

        $this->entityManager->persist($newUser);
        $this->entityManager->flush();

        return $newUser;
    }
}
