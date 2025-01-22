<?php

namespace App\Entity;

use App\Repository\CompensationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompensationRepository::class)]
class Compensation {

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, Incident>
     */
    #[ORM\OneToMany(targetEntity: Incident::class, mappedBy: 'compensation')]
    private Collection $incidents;

    #[ORM\Column]
    private float $amount = 0;

    #[ORM\ManyToOne]
    private ?Worker $recipient = null;

    public function __construct() {

        $this->incidents = new ArrayCollection();
    }

    public function addIncident(Incident $incident)
    : void {

        if (!$this->incidents->contains($incident)) {
            $this->incidents->add($incident);
            $incident->setCompensation($this);
            $this->amount += $incident->getDueCompensation();
            $this->recipient ??= $incident->getAffectedWorker();
        }
    }

    public function getAmount()
    : float {

        return $this->amount;
    }

    public function getNumberOfCompensatedEvents()
    : int {

        return $this->incidents->count();
    }

    public function getRecipientName()
    : string {

        return $this->recipient?->getName() ?? '';
    }

}

