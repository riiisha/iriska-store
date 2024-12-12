<?php

namespace App\DataFixtures;

use App\DTO\Product\CreateProductDTO;
use App\DTO\Product\MeasurementDTO;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProductFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($id = 1; $id <= 10; $id++) {
            for ($version = 1; $version <= 3; $version++) {
                $measurement = new MeasurementDTO(
                    $faker->numberBetween(1, 50),
                    $faker->numberBetween(1, 50),
                    $faker->numberBetween(1, 50),
                    $faker->numberBetween(1, 50)
                );
                $dto = new CreateProductDTO(
                    $id,
                    $version,
                    $faker->word(),
                    $measurement,
                    $faker->sentence(),
                    $faker->numberBetween(1, 50),
                    $faker->numberBetween(1, 50)
                );
                $product = new Product($dto);
                $manager->persist($product);
            }
        }
        $manager->flush();
    }
}
