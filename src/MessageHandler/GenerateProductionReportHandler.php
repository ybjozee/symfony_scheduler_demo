<?php

namespace App\MessageHandler;

use App\Helper\Reporting\ProductionReportWriter;
use App\Message\GenerateProductionReport;
use App\Repository\ProductRepository;
use DateTimeImmutable;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use DateTimeInterface;

#[AsMessageHandler]
final class GenerateProductionReportHandler {

    public function __construct(
        private ProductionReportWriter $reportWriter,
        private ProductRepository      $productRepository
    ) {
    }

    public function __invoke(GenerateProductionReport $message)
    : void {
        $cutoffDate = new DateTimeImmutable('yesterday midnight');
        $data = $this->productRepository->getProductionForDate($cutoffDate);
        $this->reportWriter->write($data);
    }
}
