<?php

namespace App\Repository;

use App\Entity\Incident;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Incident>
 */
class IncidentRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {

        parent::__construct($registry, Incident::class);
    }

    public function getUncompensatedIncidents()
    : array {

        return $this->findBy(['compensation' => null]);
    }

    public function getIncidentsBetween(DateTimeInterface $start, DateTimeInterface $end)
    : array {

        return $this->createQueryBuilder('incident')
                    ->where('incident.occurredAt BETWEEN :start AND :end')
                    ->setParameter('start', $start)
                    ->setParameter('end', $end)
                    ->getQuery()
                    ->getResult();
    }
}

