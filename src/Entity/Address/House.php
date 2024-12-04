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
    private string $number;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $corpus;

    #[ORM\ManyToOne(inversedBy: 'houses')]
    #[ORM\JoinColumn(nullable: false)]
    private Street $street;

    /**
     * @param Street $street
     * @param string $houseNumber
     * @param string|null $corpus
     */
    public function __construct(Street $street, string $houseNumber, ?string $corpus = null)
    {
        $this->number = $houseNumber;
        $this->corpus = $corpus;
        $this->street = $street;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function getCorpus(): ?string
    {
        return $this->corpus;
    }

    public function getStreet(): ?Street
    {
        return $this->street;
    }
}
