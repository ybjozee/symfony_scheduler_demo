<?php

namespace App\Helper\Reporting;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

abstract class AbstractReportWriter {

    const ACCOUNTING_FORMAT = '_("₦ "* #,##0.00_);_("₦"* \(#,##0.00\);_("₦"* "-"??_);_(@_)';
    const NUMBER_FORMAT     = '#,###';
    protected int $rowIndex = 1;
    private Spreadsheet $spreadsheet;

    public function __construct(
        private readonly string $saveLocation
    ) {

        $this->spreadsheet = new Spreadsheet;
    }

    public abstract function write(array $data)
    : string;

    protected function writeHeader(string $cell, string $value)
    : void {

        $this->writeToCell($cell, $value);
        $this->getStyle($cell)->getFont()->setBold(true);
        $this->applyThinBorder($cell);
        $this->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    protected function writeToCell(string $cell, string|int|float $value)
    : void {

        $this->getActiveSheet()->setCellValue($cell, $value);
        $this->applyThinBorder($cell);
        $this->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    protected function getActiveSheet()
    : Worksheet {

        return $this->spreadsheet->getActiveSheet();
    }

    protected function applyThinBorder(string $range)
    : void {

        $this->getStyle($range)->applyFromArray(
            [
                'borders'   => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => [
                            'argb' => Color::COLOR_BLACK,
                        ],
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_JUSTIFY,
                ],
            ]
        );
    }

    private function getStyle(string $cell)
    : Style {

        return $this->getActiveSheet()->getStyle($cell);
    }

    protected function applyAccountingFormat(string $cell)
    : void {

        $this->getActiveSheet()
             ->getStyle($cell)
             ->getNumberFormat()
             ->setFormatCode(self::ACCOUNTING_FORMAT);
    }

    protected function applyNumberFormat(string $cell)
    : void {

        $this->getActiveSheet()
             ->getStyle($cell)
             ->getNumberFormat()
             ->setFormatCode(self::NUMBER_FORMAT);
    }

    protected function save(string $name)
    : string {

        $this->autosizeColumns();

        $writer = IOFactory::createWriter($this->spreadsheet, "Xlsx");
        $filePath = "$this->saveLocation/$name.xlsx";
        $writer->save($filePath);
        return $filePath;
    }

    private function autosizeColumns()
    : void {

        foreach ($this->spreadsheet->getWorksheetIterator() as $worksheet) {
            $this->spreadsheet->setActiveSheetIndex($this->spreadsheet->getIndex($worksheet));
            $sheet = $this->spreadsheet->getActiveSheet();
            $cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(true);
            foreach ($cellIterator as $cell) {
                $sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
            }
        }
    }
}