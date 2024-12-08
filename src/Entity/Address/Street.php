<?php

namespace App\Entity\Address;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Street
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\ManyToOne(inversedBy: 'streets')]
    #[ORM\JoinColumn(nullable: false)]
    private City $city;

    /**
     * @var Collection<int, House>
     */
    #[ORM\OneToMany(targetEntity: House::class, mappedBy: 'street', orphanRemoval: true)]
    private Collection $houses;

    public function __construct(string $name, City $city)
    {
        $this->name = $name;
        $this->city = $city;
        $this->houses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    /**
     * @return Collection<int, House>
     */
    public function getHouses(): Collection
    {
        return $this->houses;
    }
}
