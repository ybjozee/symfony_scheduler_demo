<?php

namespace App\MessageHandler;

use App\Entity\Compensation;
use App\Entity\Incident;
use App\Helper\Mailing\MailService;
use App\Helper\Reporting\CompensationReportWriter;
use App\Message\GenerateCompensationReport;
use App\Repository\IncidentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GenerateCompensationReportHandler
{

    public function __construct(
        private CompensationReportWriter $reportWriter,
        private EntityManagerInterface $entityManager,
        private IncidentRepository $incidentRepository,
        private MailService $mailService
    ) {}

    public function __invoke(GenerateCompensationReport $message): void {
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
        $filePath = $this->reportWriter->write(array_values($compensations));
        $this->mailService->sendMail(
            'Compensation Report',
            'The latest compensation report is available for your review',
            $filePath,
            "Compensation Report.xlsx"
        );
    }
}
