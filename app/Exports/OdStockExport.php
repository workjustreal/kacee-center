<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class OdStockExport implements FromCollection, WithHeadings
{
    protected $data;
    public function __construct($data)
    {
        $this->data = $data;
    }
    public function collection()
    {
        return collect($this->data);
    }
    public function map($data): array
    {
        return [
            $data->loccod,
            $data->stkcod,
        ];
    }
    public function headings(): array
    {
        return [
            [
                'Location',
                'SKU',
            ],
        ];
    }
}
