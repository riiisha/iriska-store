<?php

namespace App\Service\Address;

use App\DTO\Address\AddressDTO;
use App\Entity\Address\Address;
use App\Entity\Address\City;
use App\Entity\Address\House;
use App\Entity\Address\Street;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

readonly class AddressService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger
    ) {
    }


    /** Поиск адреса. Если не найден - создается */
    public function getAddress(AddressDTO $addressDTO, User $user): Address
    {
        return $this->getOrCrateAddress($addressDTO, $user);
    }

    private function getOrCrateAddress(AddressDTO $addressDTO, User $user): Address
    {
        $repository = $this->entityManager->getRepository(City::class);

        $city = $repository->findOneBy(['name' => $addressDTO->city]) ?? $this->createCity($addressDTO->city);
        $street = $this->getOrCreateStreet($city, $addressDTO->street);
        $house = $this->getOrCreateHouse($street, $addressDTO->house, $addressDTO->corpus);

        $address = $this->entityManager->getRepository(Address::class)->findOneBy([
            'house' => $house,
            'owner' => $user
        ]);

        if (!$address) {
            $address = (new Address())->setOwner($user)->setHouse($house);
            $this->entityManager->persist($city);
            $this->entityManager->flush();
            $this->logger->debug('Create new address');
        }

        return $address;
    }

    private function createCity(string $cityName): City
    {
        $city = (new City())->setName($cityName);
        $this->entityManager->persist($city);
        $this->logger->debug('Create new city');

        return $city;
    }

    private function getOrCreateStreet(City $city, string $streetName): Street
    {
        $street = $city->getStreets()->filter(function (Street $street) use ($streetName): bool {
            return $street->getName() === $streetName;
        })->first();

        if (!$street) {
            $street = (new Street())->setName($streetName)->setCity($city);
            $this->entityManager->persist($street);
            $this->logger->debug('Create new street');
        }

        return $street;
    }

    private function getOrCreateHouse(Street $street, string $houseNumber, ?string $houseCorpus): House
    {
        $house = $street->getHouses()->filter(function (House $house) use ($houseNumber, $houseCorpus): bool {
            return ($house->getNumber() === $houseNumber && $house->getCorpus() === $houseCorpus);
        })->first();

        if (!$house) {
            $house = (new House())->setNumber($houseNumber)->setCorpus($houseCorpus)->setStreet($street);
            $this->entityManager->persist($house);
            $this->logger->debug('Create new house');
        }
        $this->entityManager->flush();

        return $house;
    }

}
