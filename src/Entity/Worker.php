<?php

namespace App\Entity;

use App\Repository\WorkerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkerRepository::class)]
class Worker {

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function __construct(
        #[ORM\Column]
        private string $name,
        #[ORM\Column]
        private int    $age,
    ) {
    }

    public function getId()
    : ?int {

        return $this->id;
    }

    public function getName()
    : string {

        return $this->name;
    }

    public function getAge()
    : int {

        return $this->age;
    }
}

