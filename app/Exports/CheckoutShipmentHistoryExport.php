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

class CheckoutShipmentHistoryExport implements FromCollection, WithHeadings, WithColumnFormatting, WithTitle, WithEvents, WithMapping, ShouldAutoSize
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
            $data->running,
            $data->checkout_date,
            $data->vehicle_registration,
            ($data->tracking_count > 0) ? $data->tracking_count : '0',
            ($data->order_count > 0) ? $data->order_count : '0',
            ($data->so_count > 0) ? $data->so_count : '0',
            ($data->packaging_total > 0) ? $data->packaging_total : '0',
            $data->eplatform_list,
            $data->ship_com_name,
            $data->remark,
        ];
    }
    public function headings() :array
    {
        return [
            'เลขเอกสาร',
            'วันที่เช็คเอาท์',
            'ทะเบียนรถ',
            'Tracking',
            'Order',
            'SO',
            'แพ็คเกจ',
            'ร้านค้า',
            'ขนส่ง',
            'หมายเหตุ',
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
                $event->sheet->getDelegate()->getStyle('A1:J1')->getFont()->setBold(true);
            },
        ];
    }
}