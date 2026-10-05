<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use App\Enum\ProductStatusEnum;
use App\Entity\Product;

class ProductFixture extends Fixture
{
    public function __construct()
    {
        $this->faker = FakerFactory::create('ru_RU');
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 1000; $i++) {
            $product = new Product();

            $sku = 'SKU-' . strtoupper($this->faker->bothify('??##'));
            $name = $this->faker->word . ' ' . $this->faker->word . ' ' . $this->faker->word;
            $category = $this->faker->randomElement(['Электроника', 'Одежда', 'Книги']);
            $price = $this->faker->numberBetween(500, 10000) / 100;
            $stock = $this->faker->numberBetween(0, 100);
            $unit = 'шт';

            $status = ProductStatusEnum::tryFrom(
                $this->faker->randomElement([
                    'active',
                    'archive',
                    'out_of_stock'
                ])
            );

            $product->setSku($sku)
                    ->setName($name)
                    ->setCategory($category)
                    ->setPrice($price)
                    ->setStock($stock)
                    ->setUnit($unit)
                    ->setStatus($status);

            $manager->persist($product);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [];
    }
}
