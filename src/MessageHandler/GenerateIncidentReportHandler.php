<?php

namespace App\MessageHandler;

use App\Helper\Reporting\IncidentReportWriter;
use App\Message\GenerateIncidentReport;
use App\Repository\IncidentRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use DateTimeImmutable;

#[AsMessageHandler]
final class GenerateIncidentReportHandler {

    public function __construct(
        private IncidentReportWriter $reportWriter,
        private IncidentRepository   $incidentRepository
    ) {
    }

    public function __invoke(GenerateIncidentReport $message)
    : void {

        $end = new DateTimeImmutable();
        $start = new DateTimeImmutable('-4 hours');
        $incidents = $this->incidentRepository->getIncidentsBetween($start, $end);
        $this->reportWriter->write($incidents);
    }
}
