<?php

namespace App\MessageHandler;

use App\Entity\Compensation;
use App\Entity\Incident;
use App\Helper\Reporting\CompensationReportWriter;
use App\Message\GenerateCompensationReport;
use App\Repository\IncidentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GenerateCompensationReportHandler {

    public function __construct(
        private CompensationReportWriter $reportWriter,
        private EntityManagerInterface   $entityManager,
        private IncidentRepository       $incidentRepository
    ) {
    }

    public function __invoke(GenerateCompensationReport $message)
    : void {

        $compensations = [];
        $uncompensatedIncidents = $this->incidentRepository->getUncompensatedIncidents();
        /**@var Incident $incident */
        foreach ($uncompensatedIncidents as $incident) {
            $affectedWorkerId = $incident->getAffectedWorker()->getId();
            $compensation = $compensations[$affectedWorkerId] ?? new Compensation();
            $compensation->addIncident($incident);
            $this->entityManager->persist($compensation);
            $this->entityManager->persist($incident);
            $compensations[$affectedWorkerId] = $compensation;
        }
        $this->entityManager->flush();
        $this->reportWriter->write(array_values($compensations));
    }
}
