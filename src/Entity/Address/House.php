<?php

namespace App\Entity\Address;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class House
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $number = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $corpus = null;

    #[ORM\ManyToOne(inversedBy: 'houses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Street $street = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getCorpus(): ?string
    {
        return $this->corpus;
    }

    public function setCorpus(?string $corpus): static
    {
        $this->corpus = $corpus;

        return $this;
    }

    public function getStreet(): ?Street
    {
        return $this->street;
    }

    public function setStreet(?Street $street): static
    {
        $this->street = $street;

        return $this;
    }
}
