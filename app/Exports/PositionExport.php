<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PositionExport implements FromCollection, WithHeadings, WithColumnFormatting, WithTitle, WithEvents, WithMapping, ShouldAutoSize
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
    public function map($data): array
    {
        return [
            $data->position_id,
            $data->position_name,
            $data->position_name_en,
        ];
    }
    public function headings() :array
    {
        return [
            'รหัสตำแหน่ง',
            'ชื่อตำแหน่ง',
            'ชื่อตำแหน่ง (EN)',
        ];
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
                $event->sheet->getDelegate()->getStyle('A1:C1')->getFont()->setBold(true);
            },
        ];
    }
}