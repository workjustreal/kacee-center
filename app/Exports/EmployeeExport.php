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

class EmployeeExport implements FromCollection, WithHeadings, WithColumnFormatting, WithTitle, WithEvents, WithMapping, ShouldAutoSize
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
            $data->emp_id,
            $data->name,
            $data->surname,
            $data->dept_id,
            $data->dept_name,
            $data->area_code,
            $data->position_id,
            $data->position_name,
            $data->email,
            $data->emp_type,
            $data->emp_status,
        ];
    }
    public function headings() :array
    {
        return [
            'รหัสพนักงาน',
            'ชื่อ',
            'นามสกุล',
            'รหัสหน่วยงาน',
            'ชื่อหน่วยงาน',
            'รหัสพื้นที่การขาย',
            'รหัสตำแหน่ง',
            'ชื่อตำแหน่ง',
            'อีเมล',
            'ประเภทพนักงาน',
            'สถานะพนักงาน',
        ];
    }
    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_GENERAL,
            'B' => NumberFormat::FORMAT_GENERAL,
            'C' => NumberFormat::FORMAT_GENERAL,
            'D' => NumberFormat::FORMAT_GENERAL,
            'E' => NumberFormat::FORMAT_GENERAL,
            'F' => NumberFormat::FORMAT_GENERAL,
            'G' => NumberFormat::FORMAT_GENERAL,
            'H' => NumberFormat::FORMAT_GENERAL,
            'I' => NumberFormat::FORMAT_GENERAL,
            'J' => NumberFormat::FORMAT_GENERAL,
            'K' => NumberFormat::FORMAT_GENERAL,
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
                $event->sheet->getDelegate()->getStyle('A1:K1')->getFont()->setBold(true);
                // $event->sheet->setCellValue('K'. ($event->sheet->getHighestRow()+1), '=SUM(K2:K'.$event->sheet->getHighestRow().')');
                $event->sheet->setCellValue('M2', 'ประเภทพนักงาน')->getStyle('M2')->getFont()->setBold(true);
                $event->sheet->setCellValue('M3', 'D = รายวัน');
                $event->sheet->setCellValue('M4', 'M = รายเดือน');

                $event->sheet->setCellValue('M6', 'สถานะพนักงาน')->getStyle('M6')->getFont()->setBold(true);
                $event->sheet->setCellValue('M7', '1 = ปกติ');
                $event->sheet->setCellValue('M8', '2 = ทดลองงาน');
                $event->sheet->setCellValue('M9', '0 = ลาออก');

                $event->sheet->getDelegate()->freezePane('D2');
            },
        ];
    }
}