<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product {

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeInterface $createdOn;

    public function __construct(
        #[ORM\Column(enumType: ProductType::class)]
        private ProductType $type,
        #[ORM\Column]
        private int         $quantity,
    ) {

        $this->createdOn = new DateTimeImmutable();
    }

    public function createdOn()
    : ?DateTimeImmutable {

        return $this->createdOn;
    }

    public function getType()
    : ProductType {

        return $this->type;
    }

    public function getQuantity()
    : int {

        return $this->quantity;
    }
}
