<?php

namespace App\Services;

use Carbon\Carbon;

class ThaiDateHelperService
{
    private static $thai_months_short = [
        1 => 'ม.ค.',
        2 => 'ก.พ.',
        3 => 'มี.ค.',
        4 => 'เม.ย.',
        5 => 'พ.ค.',
        6 => 'มิ.ย.',
        7 => 'ก.ค.',
        8 => 'ส.ค.',
        9 => 'ก.ย.',
        10 => 'ต.ค.',
        11 => 'พ.ย.',
        12 => 'ธ.ค.',
    ];

    private static $thai_months_long = [
        1 => 'มกราคม',
        2 => 'กุมภาพันธ์',
        3 => 'มีนาคม',
        4 => 'เมษายน',
        5 => 'พฤษภาคม',
        6 => 'มิถุนายน',
        7 => 'กรกฎาคม',
        8 => 'สิงหาคม',
        9 => 'กันยายน',
        10 => 'ตุลาคม',
        11 => 'พฤศจิกายน',
        12 => 'ธันวาคม',
    ];

    public static function shortDateTimeFormat($arg)
    {
        $date = Carbon::parse($arg);
        $month = self::$thai_months_short[$date->month];
        $year = $date->year + 543;
        return $date->format("j $month $year H:i:s"); // 1 มิ.ย. 2565 18:30:00
    }

    public static function longDateTimeFormat($arg)
    {
        $date = Carbon::parse($arg);
        $month = self::$thai_months_long[$date->month];
        $year = $date->year + 543;
        return $date->format("j $month $year H:i:s"); // 1 มิถุนายน 2565 18:30:00
    }

    public static function shortDateFormat($arg)
    {
        $date = Carbon::parse($arg);
        $month = self::$thai_months_short[$date->month];
        $year = $date->year + 543;
        return $date->format("j $month $year"); // 1 มิ.ย. 2565
    }

    public static function longDateFormat($arg)
    {
        $date = Carbon::parse($arg);
        $month = self::$thai_months_long[$date->month];
        $year = $date->year + 543;
        return $date->format("j $month $year"); // 1 มิถุนายน 2565
    }
}