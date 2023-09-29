<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrganizationalChartExport implements WithHeadings, WithColumnFormatting, WithTitle, WithEvents, ShouldAutoSize
{
    protected $data;
    protected $title;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct($data, $title)
    {
        $this->data = $data;
        $this->title = $title;
    }
    public function headings() :array
    {
        return [$this->title, '', ''];
    }
    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_GENERAL,
            'B' => NumberFormat::FORMAT_GENERAL,
            'C' => NumberFormat::FORMAT_GENERAL,
        ];
    }
    public function title(): string
    {
        return $this->title;
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $col = "A";
                $row = 2;
                for ($i=0; $i<count($this->data); $i++) {
                    if ($i > 0 && $this->data[$i]["column"] != $col) {
                        $col = $this->data[$i]["column"];
                        $row = 2;
                    }
                    if ($this->data[$i]["level"] == 1) {
                        $event->sheet->getStyle($col.$row)->getFont()->getColor()->setARGB('C94728');
                    } else if ($this->data[$i]["level"] == 2) {
                        $event->sheet->getStyle($col.$row)->getFont()->getColor()->setARGB('0B0EE4');
                    } else if ($this->data[$i]["level"] == 3) {
                        $event->sheet->getStyle($col.$row)->getFont()->getColor()->setARGB('2A8F03');
                    } else if ($this->data[$i]["level"] == 4) {
                        $event->sheet->getStyle($col.$row)->getFont()->getColor()->setARGB('4D4D4D');
                    }
                    $event->sheet->setCellValue($col.$row, $this->data[$i]["name"]);
                    $row++;
                }
                $event->sheet->mergeCells('A1:C1');
                $event->sheet->getDelegate()->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A1:C1')->getFont()->setBold(true);
            },
        ];
    }
}