<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithPreCalculateFormulas;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class OrdersReportExport implements FromCollection, WithHeadings, WithColumnWidths, WithColumnFormatting, WithTitle, WithEvents, WithMapping, ShouldAutoSize, WithPreCalculateFormulas
{
    protected $data;
    protected $title;
    protected $type;
    protected $header;
    protected $detail;
    protected $footer;
    protected $col1;
    protected $col2;
    protected $row;
    protected $columns;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct($data, $title)
    {
        $this->title = $title;
        $this->type = $data["type"];
        $this->header = $data["header"];
        $this->detail = $data["detail"];
        $this->footer = $data["footer"];
        $this->col1 = 0;
        $this->col2 = 0;
        $this->row = 3;
        $this->columns = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
    }
    public function collection()
    {
        return collect($this->detail);
    }
    public function map($data): array
    {
        $list = [];
        $list[] = $data["category"];
        $this->col1 = 1;
        $this->col2 = $this->header[0]["chanel"][0]["colspan"];
        for ($i=0; $i<count($data["list"]); $i++) {
            if (substr($data["list"][$i]["name"], 0, 3) == "sum") {
                $list[] = '=IF(SUM('.$this->columns[$this->col1].$this->row.':'.$this->columns[$this->col2].$this->row.')=0,"-",SUM('.$this->columns[$this->col1].$this->row.':'.$this->columns[$this->col2].$this->row.'))';
            } else {
                $list[] = ($this->type=="qty") ? $data["list"][$i]["qty"] : $data["list"][$i]["price"];
            }
        }
        // $list[] = $data["summary"];
        $this->row++;
        return $list;
    }
    public function headings() :array
    {
        $header1[0] = $this->header[0]["category"];
        $n = 1;
        for ($i=0; $i<count($this->header[0]["chanel"]); $i++) {
            for ($j=0; $j<$this->header[0]["chanel"][$i]["colspan"]; $j++) {
                $header1[$n] = ($j == 0) ? $this->header[0]["chanel"][$i]["display_name"] : "";
                $n++;
            }
        }
        $header1[$n] = $this->header[0]["summary"];
        $header2 = [""];
        $n = 1;
        for ($i=0; $i<count($this->header[0]["shop"]); $i++) {
            $header2[$n] = $this->header[0]["shop"][$i]["display_name"];
            $n++;
        }
        $header2[$n] = "";
        return [$header1, $header2];
    }
    public function columnWidths(): array
    {
        return [
            'A' => 35,
            'H' => 18,
        ];
    }
    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_GENERAL,
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
                $event->sheet->getDelegate()->getStyle('A1:H2')->getFont()->setBold(true);
                $event->sheet->mergeCells('A1:A2');
                $event->sheet->mergeCells('H1:H2');
                $event->sheet->getDelegate()->getStyle('A1:H1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A2:H2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $event->sheet->getDelegate()->getStyle('H1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                // Merge cell header & subheader
                $cl1 = 1;
                $cl2 = 0;
                $cl_col = 0;
                for ($i=0; $i<count($this->header[0]["chanel"]); $i++) {
                    $colspan = $this->header[0]["chanel"][$i]["colspan"];
                    $cl2 += $colspan;
                    if ($colspan > 1) {
                        $event->sheet->mergeCells($this->columns[$cl1].'1:'.$this->columns[$cl2].'1');
                    }
                    $cl_col++;
                    $cl1 += $colspan;
                }

                $row = count($this->detail) + 3;
                $last_cl = $this->header[0]["chanel"][0]["colspan"];

                // รายละเอียด
                $cl = 1;
                for ($i=0; $i<count($this->detail[0]["list"]); $i++) {
                    $event->sheet->getDelegate()->getStyle($this->columns[$cl].$row)->getNumberFormat()->setFormatCode('#,##0');
                    $cl++;
                }

                // Set summary & set cell format
                // คำสั่งซื้อ
                $event->sheet->setCellValue('A'.$row, 'คำสั่งซื้อ');
                $event->sheet->getDelegate()->getStyle('A'.$row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A'.$row)->getFont()->setBold(true);
                $cl = 1;
                for ($i=0; $i<count($this->footer["list"]); $i++) {
                    $event->sheet->setCellValue($this->columns[$cl].$row, $this->footer["list"][$i]["order"]);
                    $event->sheet->getDelegate()->getStyle($this->columns[$cl].$row)->getNumberFormat()->setFormatCode('#,##0');
                    $cl++;
                }
                $event->sheet->setCellValue($this->columns[($last_cl+1)].$row, '=SUM(B'.$row.':'.$this->columns[$last_cl].$row.')');
                $event->sheet->getDelegate()->getStyle($this->columns[($last_cl+1)].$row)->getNumberFormat()->setFormatCode('#,##0');
                $row++;

                // ยอดรวม
                $event->sheet->setCellValue('A'.$row, 'ยอดรวม');
                $event->sheet->getDelegate()->getStyle('A'.$row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A'.$row)->getFont()->setBold(true);
                $cl = 1;
                for ($i=0; $i<count($this->footer["list"]); $i++) {
                    $event->sheet->setCellValue($this->columns[$cl].$row, $this->footer["list"][$i]["price"]);
                    $event->sheet->getDelegate()->getStyle($this->columns[$cl].$row)->getNumberFormat()->setFormatCode('#,##0');
                    $cl++;
                }
                $event->sheet->setCellValue($this->columns[($last_cl+1)].$row, '=SUM(B'.$row.':'.$this->columns[$last_cl].$row.')');
                $event->sheet->getDelegate()->getStyle($this->columns[($last_cl+1)].$row)->getNumberFormat()->setFormatCode('#,##0');

                // Set summary font bold
                $event->sheet->getDelegate()->getStyle('H3:'.$this->columns[($last_cl+1)].$row)->getFont()->setBold(true);

                // Set borders
                $event->sheet->getDelegate()->getStyle('A1:'.$this->columns[($last_cl+1)].$row)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                // Set background
                $event->sheet->getStyle('H1:H'.$row)->getFill()->applyFromArray(['fillType' => 'solid','rotation' => 0, 'color' => ['rgb' => 'F3F7F9']]);
                $event->sheet->getStyle('H3:H'.$row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                // Set focus A1
                $event->sheet->getDelegate()->getStyle('A1')->getFont()->setBold(true);
                $row++;
            },
        ];
    }
}