<?php

namespace App\MessageHandler;

use App\Helper\Mailing\MailService;
use App\Helper\Reporting\ProductionReportWriter;
use App\Message\GenerateProductionReport;
use App\Repository\ProductRepository;
use DateTimeImmutable;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GenerateProductionReportHandler
{

    public function __construct(
        private ProductionReportWriter $reportWriter,
        private ProductRepository $productRepository,
        private MailService $mailService

    ) {}

    public function __invoke(GenerateProductionReport $message): void {
        $cutoffDate = new DateTimeImmutable('yesterday midnight');
        $data = $this->productRepository->getProductionForDate($cutoffDate);
        $filePath = $this->reportWriter->write($data);
        $this->mailService->sendMail(
            'Production Report',
            'The latest production report is available for your review',
            $filePath,
            "Production Report.xlsx"
        );
    }
}
