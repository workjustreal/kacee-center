<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Protection;

class EmployeeExportEdit implements FromCollection, WithHeadings, WithColumnFormatting, WithTitle, WithEvents, WithMapping, WithStyles, ShouldAutoSize
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
            $data->title,
            $data->name,
            $data->surname,
            $data->dept_id,
            $data->dept_name,
            $data->area_code,
            $data->nickname,
            $data->gender,
            $data->birth_date,
            $data->start_work_date,
            $data->tel,
            $data->tel2,
            $data->phone,
            $data->phone2,
            $data->email,
            $data->personal_id,
            $data->address,
            $data->subdistrict,
            $data->district,
            $data->province,
            $data->zipcode,
            $data->country,
            $data->current_address,
            $data->current_subdistrict,
            $data->current_district,
            $data->current_province,
            $data->current_zipcode,
            $data->current_country,
            $data->ethnicity,
            $data->nationality,
            $data->religion,
            $data->vehicle_registration,
        ];
    }
    public function headings() :array
    {
        return [
            'รหัสพนักงาน',
            'คำนำหน้า',
            'ชื่อ',
            'นามสกุล',
            'รหัสหน่วยงาน',
            'ชื่อหน่วยงาน',
            'รหัสพื้นที่การขาย',
            'ชื่อเล่น',
            'เพศ',
            'วันเกิด (ปี ค.ศ.-เดือน-วัน)',
            'วันที่เข้างาน (ปี ค.ศ.-เดือน-วัน)',
            'เบอร์สำนักงาน 1',
            'เบอร์สำนักงาน 2',
            'เบอร์มือถือ 1',
            'เบอร์มือถือ 2',
            'อีเมล',
            'เลขบัตร ปชช.',
            'ที่อยู่ (ตามทะเบียนบ้าน)',
            'ตำบล (ตามทะเบียนบ้าน)',
            'อำเภอ (ตามทะเบียนบ้าน)',
            'จังหวัด (ตามทะเบียนบ้าน)',
            'รหัสไปรษณีย์ (ตามทะเบียนบ้าน)',
            'ประเทศ (ตามทะเบียนบ้าน)',
            'ที่อยู่ (ปัจจุบัน)',
            'ตำบล (ปัจจุบัน)',
            'อำเภอ (ปัจจุบัน)',
            'จังหวัด (ปัจจุบัน)',
            'รหัสไปรษณีย์ (ปัจจุบัน)',
            'ประเทศ (ปัจจุบัน)',
            'สัญชาติ',
            'เชื้อชาติ',
            'ศาสนา',
            'เลขทะเบียนรถ',
        ];
    }
    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_GENERAL,
            'B' => NumberFormat::FORMAT_GENERAL,
            'C' => NumberFormat::FORMAT_GENERAL,
            'D' => NumberFormat::FORMAT_GENERAL,
        ];
    }
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A')->getFont()->setBold(true);
        $sheet->getStyle('A')->getFill()->applyFromArray(['fillType' => 'solid','rotation' => 0, 'color' => ['rgb' => 'CCCCCC'],]);
    }
    public function title(): string
    {
        return $this->title;
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A1:AG1')->getFont()->setBold(true);
                $event->sheet->getDelegate()->freezePane('E2');
                $event->sheet->getProtection()->setSheet(true);
                $event->sheet->getStyle('B:AG')->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
            },
        ];
    }
}