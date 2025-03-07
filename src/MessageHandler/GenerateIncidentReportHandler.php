<?php

namespace App\MessageHandler;

use App\Helper\Mailing\MailService;
use App\Helper\Reporting\IncidentReportWriter;
use App\Message\GenerateIncidentReport;
use App\Repository\IncidentRepository;
use DateTimeImmutable;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GenerateIncidentReportHandler
{

    public function __construct(
        private IncidentReportWriter $reportWriter,
        private IncidentRepository $incidentRepository,
        private MailService $mailService
    ) {}

    public function __invoke(GenerateIncidentReport $message): void {
        $end = new DateTimeImmutable();
        $start = new DateTimeImmutable('-4 hours');
        $incidents = $this->incidentRepository->getIncidentsBetween($start, $end);
        $filePath = $this->reportWriter->write($incidents);
        $this->mailService->sendMail(
            'Incident Report',
            'The latest incident report is available for your review',
            $filePath,
            "Incident Report.xlsx"
        );
    }
}
