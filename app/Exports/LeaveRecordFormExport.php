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

class LeaveRecordFormExport implements FromCollection, WithHeadings, WithColumnFormatting, WithColumnWidths, WithTitle, WithEvents, WithMapping, WithStyles, ShouldAutoSize
{
    protected $columns;
    protected $data;
    protected $title;
    protected $header_title;
    protected $headers;
    protected $rows;
    protected $row_count;
    protected $row;
    protected $maxcol;
    protected $more;

    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct($data, $title)
    {
        $this->columns = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        $this->data = $data;
        $this->title = $title;
        $this->header_title = $data["title"];
        $this->headers = $data["headers"];
        $this->rows = $data["rows"];
        $this->row_count = count($data["rows"]) + 5;
        $this->row = 5;
        $this->more = $data["more"];
    }
    public function collection()
    {
        return collect($this->rows);
    }
    public function map($data): array
    {
        $list = [];
        $c = 0;
        for ($i=0; $i<count($data); $i++) {
            $list[] = $data[$this->columns[$c]];
            $c++;
        }
        return $list;
    }
    public function headings() :array
    {
        $headers1 = [];
        $headers2 = [];
        $headers3 = [];
        $headers4 = [];
        $headers5 = [];
        $c = 0;
        for ($i=0; $i<count($this->headers["header1"]); $i++) {
            $headers1[] = ($i==0) ? $this->header_title["title1"] : "";
            $headers2[] = ($i==0) ? $this->header_title["title2"] : "";
            $headers3[] = ($i==0) ? $this->header_title["title3"] : "";
            $headers4[] = $this->headers["header1"][$this->columns[$c]];
            $headers5[] = $this->headers["header2"][$this->columns[$c]];
            $c++;
        }
        $this->maxcol = $c;
        return [$headers1, $headers2, $headers3, $headers4, $headers5];
    }
    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_GENERAL,
        ];
    }
    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 15,
            'C' => 20,
            'D' => 20,
            'E' => 15,
            'F' => 40,
            'G' => 15,
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
                $c = count($this->headers["header1"]);
                $event->sheet->mergeCells('A1:'.$this->columns[$c-1].'1');
                $event->sheet->getDelegate()->getStyle('A1:'.$this->columns[$c-1].'1')->getFont()->setBold(true);
                $event->sheet->mergeCells('A2:'.$this->columns[$c-1].'2');
                $event->sheet->getDelegate()->getStyle('A2:'.$this->columns[$c-1].'2')->getFont()->setBold(true);
                $event->sheet->mergeCells('A3:'.$this->columns[$c-1].'3');
                $event->sheet->getDelegate()->getStyle('A3:'.$this->columns[$c-1].'3')->getFont()->setBold(true);
                $event->sheet->getStyle('A3:'.$this->columns[$c-1].'3')->getFill()->applyFromArray(['fillType' => 'solid','rotation' => 0, 'color' => ['rgb' => 'FFFF00']]);

                $event->sheet->mergeCells('A4:A5');
                $event->sheet->mergeCells('B4:B5');
                $event->sheet->mergeCells('C4:C5');
                $event->sheet->mergeCells('D4:D5');
                $event->sheet->mergeCells('E4:E5');
                $event->sheet->mergeCells('F4:F5');
                $event->sheet->mergeCells('G4:G5');

                $cs = 7;
                $ce = 9;
                for ($i=0; $i<$this->more; $i++) {
                    $event->sheet->mergeCells($this->columns[$cs].'4:'.$this->columns[$ce].'4');
                    if (($i+1) == $this->more) break;
                    $cs += 3;
                    $ce += 3;
                }
                $event->sheet->getDelegate()->getStyle('A4:'.$this->columns[$c-1].'4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A5:'.$this->columns[$c-1].'5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A4:'.$this->columns[$c-1].'4')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A5:'.$this->columns[$c-1].'5')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                // Set borders
                $event->sheet->getDelegate()->getStyle('A4:'.$this->columns[$ce].$this->row_count)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                $event->sheet->getDelegate()->getStyle('A1')->getFont()->setBold(true);
            },
        ];
    }
}