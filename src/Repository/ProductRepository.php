<?php

namespace App\Repository;

use App\Entity\Product;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {

        parent::__construct($registry, Product::class);
    }

    public function getProductionForDate(DateTimeInterface $date)
    : array {

        return $this->createQueryBuilder('product')
                    ->where('product.createdOn > :date')
                    ->setParameter('date', $date)
                    ->getQuery()
                    ->getResult();
    }
}

