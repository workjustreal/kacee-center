<?php

namespace App\Imports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class EmployeeImport implements ToArray
{
    private $data;

    public function __construct()
    {
        $this->data = [];
    }

    public function array(array $rows)
    {
        $i = 0;
        foreach ($rows as $row) {
            $this->data[] = [
                'emp_id' => $row[0],
                'name_prefix' => $row[1],
                'first_name' => $row[2],
                'last_name' => $row[3],
                "dept_id" => $row[4],
                "nickname" => $row[5],
                "position_id" => $row[6],
                "emp_type" => $row[7],
                "full_address" => $row[8],
                "gender" => $row[9],
                "race" => $row[10],
                "nationality" => $row[11],
                "religion" => $row[12],
                "birth_date" => ($i == 0) ? $row[13] : Date::excelToDateTimeObject(intval($row[13]))->format('d/m/Y'),
                "idcard_number" => $row[14],
                "start_work_date" => ($i == 0) ? $row[15] : Date::excelToDateTimeObject(intval($row[15]))->format('d/m/Y'),
            ];
            $i++;
        }
    }

    public function getArray(): array
    {
        return $this->data;
    }
}