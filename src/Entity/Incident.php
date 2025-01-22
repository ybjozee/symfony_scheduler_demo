<?php

namespace App\Entity;

use App\Repository\IncidentRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IncidentRepository::class)]
class Incident {

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeInterface $occurredAt;

    #[ORM\ManyToOne(inversedBy: 'incidents')]
    private ?Compensation $compensation = null;

    #[ORM\Column]
    private float $costToCompany;

    public function __construct(
        #[ORM\Column(enumType: IncidentType::class)]
        private IncidentType $type,

        #[ORM\ManyToOne]
        #[ORM\JoinColumn(nullable: false)]
        private Worker       $affectedWorker,
    ) {

        $this->occurredAt = new DateTimeImmutable();
        $this->costToCompany = $this->type->getCompensationDue();
    }

    public function occurredAt()
    : DateTimeImmutable {

        return $this->occurredAt;
    }

    public function hasBeenCompensated()
    : bool {

        return is_null($this->compensation);
    }

    public function getType()
    : IncidentType {

        return $this->type;
    }

    public function getDueCompensation()
    : float {

        return $this->costToCompany;
    }

    public function getAffectedWorker()
    : Worker {

        return $this->affectedWorker;
    }

    public function setCompensation(Compensation $compensation)
    : void {

        $this->compensation = $compensation;
    }
}

