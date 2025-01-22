<?php

namespace App\Helper\Reporting;

use App\Entity\Compensation;
use DateTimeImmutable;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class CompensationReportWriter extends AbstractReportWriter {

    public function __construct(
        #[Autowire('%kernel.project_dir%/reports/compensation')]
        string $saveLocation
    ) {

        parent::__construct($saveLocation);
    }

    public function write(array $data)
    : void {

        $this->writeReportHeader();
        $this->writeReportBody($data);
        $this->save((new DateTimeImmutable)->format('F Y'));
    }

    private function writeReportHeader()
    : void {

        $this->writeHeader("A$this->rowIndex", "Compensated Worker");
        $this->writeHeader("B$this->rowIndex", "Number of incidents");
        $this->writeHeader("C$this->rowIndex", "Compensated Amount");
        $this->rowIndex++;
    }

    /**@param Compensation[] $data */
    private function writeReportBody(array $data)
    : void {

        foreach ($data as $compensation) {
            $this->writeToCell("A$this->rowIndex", $compensation->getRecipientName());
            $this->writeToCell("B$this->rowIndex", $compensation->getNumberOfCompensatedEvents());
            $this->writeToCell("C$this->rowIndex", $compensation->getAmount());
            $this->applyAccountingFormat("C$this->rowIndex");
            $this->rowIndex++;
        }
    }
}
