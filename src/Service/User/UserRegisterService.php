<?php

namespace App\Service\User;

use App\DTO\User\UserRegisterDTO;
use App\Entity\User;
use App\Event\User\UserRegisterEvent;
use App\Exception\UserExistsException;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class UserRegisterService
{
    public function __construct(
        private EntityManagerInterface      $entityManager,
        private UserRepository              $userRepository,
        private UserPasswordHasherInterface $userPasswordHasher,
        private EventDispatcherInterface    $eventDispatcher
    ) {
    }

    /**
     * @throws UserExistsException
     */
    public function register(UserRegisterDTO $userRegisterDTO): User
    {
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

        $event = new UserRegisterEvent($newUser);
        $this->eventDispatcher->dispatch($event, UserRegisterEvent::NAME);

        return $newUser;
    }
}
