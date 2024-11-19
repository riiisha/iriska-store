<?php

namespace App\Entity\Address;

use App\Entity\User;
use App\Repository\Address\AddressRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
class Address
{
    /* TODO - можно добавить название и комментарии к адресу */
    /* TODO - можно переделать механизм:
        добавить форму для изменения адресов в ЛК (метод user/edit),
        позволить сделать "выбранным" один из них.
        При оформлении доставки поругаться, если нет "выбранного" адреса, но выбрана доставка на дом.
        Если все гуд, то отправлять заказ на данный адрес
    */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?House $house = null;

    #[ORM\ManyToOne(inversedBy: 'addresses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHouse(): ?House
    {
        return $this->house;
    }

    public function setHouse(?House $house): static
    {
        $this->house = $house;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }
}
