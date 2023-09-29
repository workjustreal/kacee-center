<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DailySalesTop10Export implements FromCollection, WithHeadings, WithColumnWidths, WithColumnFormatting, WithTitle, WithEvents, WithMapping, WithStyles, ShouldAutoSize
{
    protected $data;
    protected $title;
    protected $row_count;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct($data, $title)
    {
        $this->data = $data;
        $this->title = $title;
        $this->row_count = count($data->rows) + 2;
    }
    public function collection()
    {
        return collect($this->data->rows);
    }
    public function map($data): array
    {
        return [
            $data->a,
            $data->b,
            $data->c,
            $data->d,
            $data->e,
            $data->f,
            $data->g,
        ];
    }
    public function headings() :array
    {
        $headers1 = [$this->data->title, '', '', '', '', '', ''];
        $headers2 = ['อันดับ', 'ชื่อร้านค้า', '', '', 'รหัสร้านค้า', 'จำนวน', 'ยอดเงิน (บาท)'];
        return [$headers1, $headers2];
    }
    public function columnWidths(): array
    {
        return [
            'D' => 45,
            'E' => 12,
            'F' => 8,
            'G' => 15,
        ];
    }
    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_GENERAL,
        ];
    }
    public function styles(Worksheet $sheet)
    {
    }
    public function title(): string
    {
        return $this->title;
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A1:G1')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A2:G2')->getFont()->setBold(true);
                $event->sheet->mergeCells('A1:G1');
                $event->sheet->mergeCells('B2:D2');
                $event->sheet->getDelegate()->freezePane('A3');
                $event->sheet->getDelegate()->getStyle('A1:G1')->getFont()->setSize(16);
                $event->sheet->getDelegate()->getStyle('A1:G1')->getFont()->getColor()->setARGB('00A36C');

                foreach ($event->sheet->getColumnIterator('A') as $row) {
                    $i = 1;
                    foreach ($row->getCellIterator() as $cell) {
                        if (substr($cell->getCoordinate(), 0, 1) == "A") {
                            $line = strlen($cell->getValue());
                            $line_plus = strlen($event->sheet->getCell('A'.($i+1))->getValue());

                            $valueB = $event->sheet->getCell('B'.$i)->getValue();
                            $lineB = strlen($valueB);
                            $lineB_plus = strlen($event->sheet->getCell('B'.($i+1))->getValue());

                            if ($i > 2 && $line > 0 && $line_plus <= 0) {
                                $event->sheet->getStyle('A'.$i.':G'.$i)->getFont()->setBold(true)->getColor()->setARGB('0000FF');
                                $event->sheet->mergeCells('B'.$i.':D'.$i);
                                $event->sheet->getDelegate()->getStyle('F'.$i.':G'.$i)->getNumberFormat()->setFormatCode('#,##0');
                            }

                            if ($i > 3 && $lineB > 0 && !substr_count($valueB, "รหัสสินค้า:")) {
                                $event->sheet->getStyle('A'.$i.':G'.$i)->getFont()->setBold(true);
                                $event->sheet->mergeCells('B'.$i.':D'.$i);
                                $event->sheet->getDelegate()->getStyle('F'.$i.':G'.$i)->getNumberFormat()->setFormatCode('#,##0');
                            }

                            if ($i > 3 && $lineB > 0 && substr_count($valueB, "รหัสสินค้า:")) {
                                $event->sheet->getDelegate()->getStyle('B'.$i)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                                $event->sheet->getDelegate()->getStyle('F'.$i.':G'.$i)->getNumberFormat()->setFormatCode('#,##0');
                            }

                            $i++;
                        }
                    }
                }
                $event->sheet->getDelegate()->getStyle('A1:G1')->getFont()->setBold(true);
            },
        ];
    }
}