<?php

namespace App\Entity;

use App\DTO\Product\CreateProductDTO;
use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\SerializedName;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\Column]
    private int $id;

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    private int $version;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'json', )]
    #[SerializedName('measurements')]
    private array $measurements;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $description;

    #[ORM\Column(type: 'integer')]
    private int $cost;

    #[ORM\Column(type: 'integer')]
    private int $tax;

    /**
     * @param CreateProductDTO $createProductDTO
     */
    public function __construct(CreateProductDTO $createProductDTO) {
        $this->id = $createProductDTO->id;
        $this->version = $createProductDTO->version;
        $this->name = $createProductDTO->name;
        $this->measurements = (array)$createProductDTO->measurements;
        $this->description = $createProductDTO->description;
        $this->cost = $createProductDTO->cost;
        $this->tax = $createProductDTO->tax;
    }

    /**
     * @param CreateProductDTO $createProductDTO
     */
    public function update(CreateProductDTO $createProductDTO): void
    {
        $this->name = $createProductDTO->name;
        $this->measurements = (array)$createProductDTO->measurements;
        $this->description = $createProductDTO->description;
        $this->cost = $createProductDTO->cost;
        $this->tax = $createProductDTO->tax;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getMeasurements(): array
    {
        return $this->measurements;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getCost(): int
    {
        return $this->cost;
    }

    public function getTax(): int
    {
        return $this->tax;
    }
}
