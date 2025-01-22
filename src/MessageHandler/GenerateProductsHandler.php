<?php

namespace App\MessageHandler;

use App\Entity\Product;
use App\Entity\ProductType;
use App\Message\GenerateProducts;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GenerateProductsHandler {

    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(GenerateProducts $message)
    : void {

        $faker = Factory::create();
        foreach (ProductType::cases() as $productType) {
            $this->entityManager->persist(
                new Product($productType, $faker->biasedNumberBetween(100, 50000))
            );
        }
        $this->entityManager->flush();
    }
}
