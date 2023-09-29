<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class OrdersExport implements FromCollection, WithHeadings, WithColumnWidths, WithTitle, WithEvents
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
    public function collection()
    {
        return collect($this->data);
    }
    public function headings() :array
    {
        return [
            'หมายเลขตำสั่งซื้อ',
            'ชื่อผู้รับ',
            '*หมายเลขติดตามพัสดุ',
            'เลขอ้างอิง SKU (SKU Reference No.)',
            'จำนวน',
            'ราคาขาย',
        ];
    }
    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 25,
            'C' => 20,
            'D' => 25,
            'E' => 10,
            'F' => 10,
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
                $event->sheet->getDelegate()->getStyle('A1:F1')->getFont()->setBold(true);
            },
        ];
    }
}