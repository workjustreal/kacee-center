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
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class DailySalesExport extends DefaultValueBinder implements FromCollection, WithHeadings, WithColumnWidths, WithColumnFormatting, WithTitle, WithEvents, WithMapping, WithStyles, ShouldAutoSize, WithCustomValueBinder
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
            $data->daily_category,
            $data->stkcod,
            $data->stkdes,
            $data->unit,
            $data->qty,
            $data->price
        ];
    }
    public function headings() :array
    {
        $headers1 = [$this->data->title->title, '', '', '', $this->data->title->sum_qty, $this->data->title->sum_price];
        $headers2 = ['หมวดหมูสินค้า', 'รหัสสินค้า', 'ชื่อสินค้า', 'หน่วย', 'จำนวน', 'ยอดขาย(บาท)'];
        return [$headers1, $headers2];
    }
    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 25,
            'C' => 50,
        ];
    }
    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_GENERAL,
            // 'B' => NumberFormat::FORMAT_GENERAL,
            // 'C' => NumberFormat::FORMAT_GENERAL,
            // 'D' => NumberFormat::FORMAT_GENERAL,
            // 'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
            // 'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
        ];
    }
    public function bindValue(Cell $cell, $value)
    {
        if ($cell->getColumn() == 'B') {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);

            return true;
        }

        // else return default behavior
        return parent::bindValue($cell, $value);
    }
    public function styles(Worksheet $sheet)
    {
        // $sheet->getStyle('A1:F1')->getFont()->setBold(true)->applyFromArray(['fillType' => 'solid','rotation' => 0, 'color' => ['rgb' => '#00A36C'],]);
        // $sheet->getStyle('A2:F2')->getFont()->setBold(true)->applyFromArray(['fillType' => 'solid','rotation' => 0, 'color' => ['rgb' => '#0000FF'],]);
        // $sheet->getStyle('A')->getFill()->applyFromArray(['fillType' => 'solid','rotation' => 0, 'color' => ['rgb' => 'CCCCCC'],]);
    }
    public function title(): string
    {
        return $this->title;
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A1:F1')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A2:F2')->getFont()->setBold(true);
                $event->sheet->getDelegate()->freezePane('A3');
                $event->sheet->getDelegate()->getStyle('A1:F1')->getFont()->setSize(16);
                $event->sheet->getDelegate()->getStyle('A1:F1')->getFont()->getColor()->setARGB('00A36C');
                $event->sheet->getDelegate()->getStyle('A2:F2')->getFont()->getColor()->setARGB('0000FF');

                $event->sheet->getDelegate()->getStyle('E1:F1')->getNumberFormat()->setFormatCode('#,##0');
                $r = 3;
                for ($i=0; $i<count($this->data->rows); $i++) {
                    $event->sheet->getDelegate()->getStyle('E'.$r.':F'.$r)->getNumberFormat()->setFormatCode('#,##0');
                    $r++;
                }

                foreach ($event->sheet->getColumnIterator('A') as $row) {
                    $i = 1;
                    foreach ($row->getCellIterator() as $cell) {
                        if (substr($cell->getCoordinate(), 0, 1) == "A") {
                            $line = strlen($cell->getValue());
                            $line_plus = strlen($event->sheet->getCell('A'.($i+1))->getValue());

                            if ($i > 2 && $line > 0 && !is_numeric(mb_substr($cell->getValue(), 0, 1))) {
                                $event->sheet->getStyle('A'.$i)->getFont()->getColor()->setARGB('FF0000');
                                $event->sheet->getStyle('A'.$i)->getFill()->applyFromArray(['fillType' => 'solid','rotation' => 0, 'color' => ['rgb' => 'FFF2E9']]);
                            } else {
                                if ($i > 3 && $line > 0 && is_numeric(mb_substr($cell->getValue(), 0, 1))) {
                                    $event->sheet->getStyle('A'.$i)->getFont()->getColor()->setARGB('0000FF');
                                }
                            }
                            if ($i == $this->row_count) {
                                $event->sheet->getStyle('A'.$i.':F'.$i)->applyFromArray([
                                    'borders' => [
                                        'bottom' => [
                                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE,
                                            'color' => ['argb' => '000000'],
                                        ],
                                    ],
                                ]);
                            } else {
                                if ($i > 3 && $line > 0 && $line_plus > 0 && !is_numeric(mb_substr($cell->getValue(), 0, 1))) {
                                    $event->sheet->getStyle('A'.($i-1).':F'.($i-1))->applyFromArray([
                                        'borders' => [
                                            'bottom' => [
                                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE,
                                                'color' => ['argb' => '000000'],
                                            ],
                                        ],
                                    ]);
                                }
                            }
                            $i++;
                        }
                    }
                }
            },
        ];
    }
}