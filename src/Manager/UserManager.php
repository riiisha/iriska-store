<?php

namespace App\Manager;

use App\DTO\Product\CreateProductDTO;
use App\DTO\User\UserEditDTO;
use App\DTO\User\UserRegisterDTO;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserManager
{
    public function __construct(
        private readonly EntityManagerInterface      $entityManager,
        private readonly UserRepository              $userRepository,
        private readonly UserPasswordHasherInterface $userPasswordHasher
    )
    {
    }

    public function register(UserRegisterDTO $userRegisterDTO): ?User
    {
        /*TODO - оповещать пользователя, что он уже есть - некорректно, но какую ошибку выдавать? */
        /*TODO - создать событие и пинговать сторонний сервис, чтобы отправили смс-ку */
        $user = $this->userRepository->findOneBy(['email' => $userRegisterDTO->email]);
        if ($user) {
            return null;
        }

        $newUser = (new User())
            ->setName($userRegisterDTO->name)
            ->setEmail($userRegisterDTO->email)
            ->setPhone($userRegisterDTO->phone)
            ->setRoles(['ROLE_USER']);
        $newUser->setPassword($this->userPasswordHasher->hashPassword($newUser, $userRegisterDTO->password));

        $this->entityManager->persist($newUser);
        $this->entityManager->flush();

        return $newUser;
    }

    public function edit(UserEditDTO $userEditDTO)
    {
        $user = $this->userRepository->findOneBy(['email' => $userEditDTO->email]);
        if (!$user) {
            throw new NotFoundHttpException();
        }
        if ($phone = $userEditDTO->phone) {
            $user->setPhone($phone);
        }
        if ($role = $userEditDTO->role) {
            $user->setRoles([$role]);
        }
        if ($name = $userEditDTO->name) {
            $user->setName($name);
        }
        $this->entityManager->flush();
    }
}
