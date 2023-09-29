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

class DailySalesCustomerExport implements FromCollection, WithHeadings, WithColumnWidths, WithColumnFormatting, WithTitle, WithEvents, WithMapping, ShouldAutoSize, WithPreCalculateFormulas
{
    protected $data;
    protected $columns;
    protected $type;
    protected $header;
    protected $detail;
    protected $col1;
    protected $col2;
    protected $row;
    protected $title;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct($data, $title)
    {
        $this->columns = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        $this->type = $data["type"];
        $this->header = $data["header"];
        $this->detail = $data["detail"];
        $this->col1 = 0;
        $this->col2 = 0;
        $this->row = 3;
        $this->title = $title;
    }
    public function collection()
    {
        return collect($this->detail);
    }
    public function map($data): array
    {
        $list = [];
        $list[] = $data["category"];
        $list[] = $data["summary"];
        $this->col1 = 2;
        $this->col2 = 1;
        for ($i=0; $i<count($data["list"]); $i++) {
            if (substr($data["list"][$i]["name"], 0, 3) == "sum") {
                $list[] = '=SUM('.$this->columns[$this->col1].$this->row.':'.$this->columns[$this->col2].$this->row.')';
                $this->col1 = $this->col2 + 2;
            } else {
                $list[] = ($this->type=="qty") ? $data["list"][$i]["qty"] : $data["list"][$i]["price"];
            }
            $this->col2++;
        }
        $this->row++;
        return $list;
    }
    public function headings() :array
    {
        $header1[0] = $this->header[0]["category"];
        $header1[1] = $this->header[0]["summary"];
        $n = 2;
        for ($i=0; $i<count($this->header[0]["chanel"]); $i++) {
            for ($j=0; $j<$this->header[0]["chanel"][$i]["colspan"]; $j++) {
                $header1[$n] = ($j == 0) ? $this->header[0]["chanel"][$i]["display_name"] : "";
                $n++;
            }
        }
        $header2 = ["", ""];
        $n = 2;
        for ($i=0; $i<count($this->header[0]["shop"]); $i++) {
            $header2[$n] = $this->header[0]["shop"][$i]["display_name"];
            $n++;
        }
        return [$header1, $header2];
    }
    public function columnWidths(): array
    {
        return [
            'A' => 28,
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
                $event->sheet->getDelegate()->getStyle('A1:P2')->getFont()->setBold(true);
                $event->sheet->mergeCells('A1:A2');
                $event->sheet->mergeCells('B1:B2');
                $event->sheet->getDelegate()->getStyle('A1:P1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A2:P2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $event->sheet->getDelegate()->getStyle('B1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                $col_color = array('FFDFD3', 'EDFFD4', 'FFEDF1', 'E3E9FF');

                // Merge cell header & subheader
                $cl1 = 2;
                $cl2 = 1;
                $cl_col = 0;
                for ($i=0; $i<count($this->header[0]["chanel"]); $i++) {
                    $colspan = $this->header[0]["chanel"][$i]["colspan"];
                    $cl2 += $colspan;
                    if ($colspan > 1) {
                        $event->sheet->mergeCells($this->columns[$cl1].'1:'.$this->columns[$cl2].'1');
                    }
                    $event->sheet->getStyle($this->columns[$cl1].'1:'.$this->columns[$cl2].'1')->getFill()->applyFromArray(['fillType' => 'solid','rotation' => 0, 'color' => ['rgb' => $col_color[$cl_col]]]);
                    $cl_col++;
                    $cl1 += $colspan;
                }

                // Calculate bill & set cell format
                $bill_summary = 0;
                $rowlist = [];
                $n = 0;
                $r = 3;
                for ($i=0; $i<count($this->detail); $i++) {
                    if ($n == 0) {
                        foreach ($this->detail[$n]["list"] as $list) {
                            $rowlist[$list["name"]] = array("name"=>$list["name"], "bill"=>0, "qty"=>0, "price"=>0);
                        }
                    }
                    $cl = 2;
                    $event->sheet->getDelegate()->getStyle('B'.$r)->getNumberFormat()->setFormatCode('#,##0');
                    foreach ($this->detail[$n]["list"] as $list) {
                        $rowlist[$list["name"]]["bill"] += $list["bill"];
                        $event->sheet->getDelegate()->getStyle($this->columns[$cl].$r)->getNumberFormat()->setFormatCode('#,##0');
                        $cl++;
                    }
                    $bill_summary += $this->detail[$n]["bill_summary"];
                    $n++;
                    $r++;
                }

                $row = count($this->detail) + 3;

                // Set summary price & set cell format
                if ($this->type=="qty") {
                    $event->sheet->setCellValue('A'.$row, 'ผลรวมยอดขายจำนวน');
                } else {
                    $event->sheet->setCellValue('A'.$row, 'ผลรวมยอดขายบาท');
                }
                $event->sheet->setCellValue('B'.$row, '=SUM(B3:B'.($row-1).')');
                $event->sheet->getDelegate()->getStyle('B'.$row)->getNumberFormat()->setFormatCode('#,##0');
                $cl = 2;
                for ($i=0; $i<count($this->detail[0]["list"]); $i++) {
                    $event->sheet->setCellValue($this->columns[$cl].$row, '=SUM('.$this->columns[$cl].'3:'.$this->columns[$cl].($row-1).')');
                    $event->sheet->getDelegate()->getStyle($this->columns[$cl].$row)->getNumberFormat()->setFormatCode('#,##0');
                    $cl++;
                }
                $event->sheet->getDelegate()->getStyle('A'.$row.':P'.$row)->getFont()->setBold(true);
                $row++;

                // Set summary bill & set cell format
                $event->sheet->setCellValue('A'.$row, 'ผลรวมจำนวนบิล');
                $event->sheet->setCellValue('B'.$row, $bill_summary);
                $event->sheet->getDelegate()->getStyle('B'.$row)->getNumberFormat()->setFormatCode('#,##0');

                $cl = 2;
                foreach ($rowlist as $list) {
                    // $event->sheet->getStyle('A'.$i)->getFill()->applyFromArray(['fillType' => 'solid','rotation' => 0, 'color' => ['rgb' => 'FFF2E9']]);
                    $event->sheet->setCellValue($this->columns[$cl].$row, $list["bill"]);
                    $event->sheet->getDelegate()->getStyle($this->columns[$cl].$row)->getNumberFormat()->setFormatCode('#,##0');
                    $cl++;
                }

                // Set summary font bold
                $event->sheet->getDelegate()->getStyle('A'.$row.':P'.$row)->getFont()->setBold(true);

                // Set borders
                $event->sheet->getDelegate()->getStyle('A1:'.$this->columns[$cl2].$row)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
                $event->sheet->getDelegate()->getStyle('A'.($row-1).':'.$this->columns[$cl2].($row-1))->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
                $event->sheet->getDelegate()->getStyle('A'.$row.':'.$this->columns[$cl2].$row)->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                // Set color
                $col_color = array('FFF7F2', 'F0FFF2', 'FFE8F9', 'F3F2FF');
                $cl1 = 2;
                $cl2 = 1;
                $cl_col = 0;
                for ($i=0; $i<count($this->header[0]["chanel"]); $i++) {
                    $colspan = $this->header[0]["chanel"][$i]["colspan"];
                    $cl2 += $colspan;
                    $event->sheet->getStyle($this->columns[$cl1].'1:'.$this->columns[$cl2].$row)->getFill()->applyFromArray(['fillType' => 'solid','rotation' => 0, 'color' => ['rgb' => $col_color[$cl_col]]]);
                    $cl_col++;
                    $cl1 += $colspan;
                }

                // Set focus A1
                $event->sheet->getDelegate()->getStyle('A1')->getFont()->setBold(true);
                $row++;
            },
        ];
    }
}