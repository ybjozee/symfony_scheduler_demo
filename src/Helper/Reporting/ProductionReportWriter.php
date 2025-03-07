<?php

namespace App\Helper\Reporting;

use App\Entity\Product;
use DateTimeImmutable;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class ProductionReportWriter extends AbstractReportWriter
{

    public function __construct(
        #[Autowire('%kernel.project_dir%/reports/production')]
        string $saveLocation
    ) {
        parent::__construct($saveLocation);
    }

    public function write(array $data): string {
        $this->writeReportHeader();
        $this->writeReportBody($data);
        return $this->save((new DateTimeImmutable)->format('d_m_Y'));
    }

    private function writeReportHeader(): void {
        $this->writeHeader("A$this->rowIndex", "Time");
        $this->writeHeader("B$this->rowIndex", "Product");
        $this->writeHeader("C$this->rowIndex", "Quantity");
        $this->rowIndex++;
    }

    /**@param Product[] $data */
    private function writeReportBody(array $data): void {
        foreach ($data as $product) {
            $this->writeToCell("A$this->rowIndex", $product->createdOn()->format('H:i'));
            $this->writeToCell("B$this->rowIndex", $product->getType()->value);
            $this->writeToCell("C$this->rowIndex", $product->getQuantity());
            $this->applyNumberFormat("C$this->rowIndex");
            $this->rowIndex++;
        }
    }
}
