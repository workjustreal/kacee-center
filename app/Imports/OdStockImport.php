<?php

namespace App\Imports;

use App\Models\OdStock;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
// use Maatwebsite\Excel\Concerns\WithValidation;

class OdStockImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        if (!array_filter($row)) {
            return null;
        }
        $_stk = OdStock::where('stkcod', '=', $row['stkcod'])->where('loccod', '=', $row['loccod'])->get();
        if ($_stk->isEmpty()) {
            return new OdStock([
                "loccod" => $row['loccod'],
                "stkcod" => $row['stkcod'],
            ]);
        }
    }
}
