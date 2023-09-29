<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;

class CheckstockExport implements FromCollection, WithHeadings, WithColumnWidths, WithTitle
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
            'ลำดับ',
            'รหัสสินค้า',
            'ชื่อสินค้า',
            'บาร์โค้ด',
            'จำนวน',
        ];
    }
    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 30,
            'C' => 45,
            'D' => 30,
            'E' => 10,
        ];
    }
    public function title(): string
    {
        return $this->title;
    }
}