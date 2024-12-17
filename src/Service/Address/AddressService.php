<?php

namespace App\Service\Address;

use App\DTO\Address\AddressDTO;
use App\Entity\Address\Address;
use App\Entity\Address\City;
use App\Entity\Address\House;
use App\Entity\Address\Street;
use App\Entity\User;
use App\Repository\Address\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class AddressService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private AddressRepository $addressRepository,
    ) {
    }


    /** Поиск адреса. Если не найден - создается */
    public function getAddress(AddressDTO $addressDTO, User $user): Address
    {
        return $this->getOrCrateAddress($addressDTO, $user);
    }

    private function getOrCrateAddress(AddressDTO $addressDTO, User $user): Address
    {
        $cityRepository = $this->entityManager->getRepository(City::class);

        $city = $cityRepository->findOneBy(['name' => $addressDTO->city]) ?? $this->createCity($addressDTO->city);
        $street = $this->getOrCreateStreet($city, $addressDTO->street);
        $house = $this->getOrCreateHouse($street, $addressDTO->house, $addressDTO->corpus);

        $address = $this->addressRepository->findOneBy([
            'house' => $house,
            'owner' => $user
        ]);

        if (!$address) {
            $address = new Address($house, $user);
            $this->addressRepository->save($address);
        }

        return $address;
    }

    private function createCity(string $cityName): City
    {
        $city = new City($cityName);
        $this->entityManager->persist($city);

        return $city;
    }

    private function getOrCreateStreet(City $city, string $streetName): Street
    {
        $street = $city->getStreets()->filter(function (Street $street) use ($streetName): bool {
            return $street->getName() === $streetName;
        })->first();

        if (!$street) {
            $street = new Street($streetName, $city);
            $this->entityManager->persist($street);
        }

        return $street;
    }

    private function getOrCreateHouse(Street $street, string $houseNumber, ?string $houseCorpus): House
    {
        $house = $street->getHouses()->filter(function (House $house) use ($houseNumber, $houseCorpus): bool {
            return ($house->getNumber() === $houseNumber && $house->getCorpus() === $houseCorpus);
        })->first();

        if (!$house) {
            $house = new House($street, $houseNumber, $houseCorpus);
            $this->entityManager->persist($house);
        }

        return $house;
    }

}
