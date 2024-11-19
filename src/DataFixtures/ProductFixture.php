<?php

namespace App\DataFixtures;

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
                $measurement = [
                    'weight' => $faker->numberBetween(1, 50),
                    'height' => $faker->numberBetween(1, 50),
                    'length' => $faker->numberBetween(1, 50),
                    'width' => $faker->numberBetween(1, 50)
                ];
                $product = (new Product())
                    ->setId($id)
                    ->setVersion($version)
                    ->setName($faker->word())
                    ->setDescription($faker->sentence())
                    ->setCost($faker->numberBetween(1, 50))
                    ->setTax($faker->numberBetween(1, 50))
                    ->setMeasurements($measurement);

                $manager->persist($product);
            }
        }
        $manager->flush();
    }
}
