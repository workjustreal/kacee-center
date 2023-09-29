<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;

class OrdersShopeeExport implements FromCollection, WithHeadings, WithColumnWidths, WithTitle, WithEvents
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
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return collect($this->data);
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function headings() :array
    {
        return [
            'หมายเลขคำสั่งซื้อ',
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
            'B' => 20,
            'C' => 25,
            'D' => 20,
            'E' => 20,
            'F' => 20,
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
                $event->sheet->getDelegate()->getStyle('A1:F1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            }
        ];
    }
}