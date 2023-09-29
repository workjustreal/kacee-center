<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class OrdersReportMultipleSheetExport implements WithMultipleSheets
{
    use Exportable;

    protected $data;
    protected $data_none;

    public function __construct($data, $data_none)
    {
        $this->data = $data;
        $this->data_none = $data_none;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];
        $sheets[] = new OrdersReportExport($this->data, "ตารางสรุปยอดคำสั่งซื้อ");
        if ($this->data_none->isNotEmpty()) {
            $sheets[] = new OrdersReportNoneExport($this->data_none, "สินค้าไม่มีหมวดหมู่");
        }

        return $sheets;
    }
}