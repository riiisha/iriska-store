<?php

namespace App\Entity\Address;

use App\Entity\User;
use App\Repository\Address\AddressRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
class Address
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private House $house;

    #[ORM\ManyToOne(inversedBy: 'addresses')]
    #[ORM\JoinColumn(nullable: false)]
    private User $owner;

    /**
     * @param House $house
     * @param User $owner
     */
    public function __construct(House $house, User $owner)
    {
        $this->house = $house;
        $this->owner = $owner;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHouse(): ?House
    {
        return $this->house;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }
}
