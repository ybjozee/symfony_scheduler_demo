<?php

namespace App\Helper\Reporting;

use App\Entity\Incident;
use DateTimeImmutable;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class IncidentReportWriter extends AbstractReportWriter
{

    public function __construct(
        #[Autowire('%kernel.project_dir%/reports/incident')]
        string $saveLocation
    ) {
        parent::__construct($saveLocation);
    }

    public function write(array $data): string {
        $this->writeReportHeader();
        $this->writeReportBody($data);
        return $this->save((new DateTimeImmutable)->format('d_m_Y_H'));
    }

    private function writeReportHeader(): void {
        $this->writeHeader("A$this->rowIndex", "Time");
        $this->writeHeader("B$this->rowIndex", "Incident");
        $this->writeHeader("C$this->rowIndex", "Affected Worker");
        $this->writeHeader("D$this->rowIndex", "Age");
        $this->writeHeader("E$this->rowIndex", "Cost to company");
        $this->rowIndex++;
    }

    /**@param Incident[] $data */
    private function writeReportBody(array $data): void {
        foreach ($data as $incident) {
            $this->writeToCell("A$this->rowIndex", $incident->occurredAt()->format('H:i'));
            $this->writeToCell("B$this->rowIndex", $incident->getType()->value);
            $this->writeToCell("C$this->rowIndex", $incident->getAffectedWorker()->getName());
            $this->writeToCell("D$this->rowIndex", $incident->getAffectedWorker()->getAge());
            $this->writeToCell("E$this->rowIndex", $incident->getDueCompensation());
            $this->applyAccountingFormat("E$this->rowIndex");
            $this->rowIndex++;
        }
    }
}