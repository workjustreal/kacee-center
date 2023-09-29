<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\PeriodSalary;
use Illuminate\Support\Facades\DB;

class LeaveStatisticsController extends LeaveBaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getThisPeriod()
    {
        // งวดค่าแรงปัจจุบัน
        $period = PeriodSalary::where('start', '<=', date('Y-m-d'))->where('end', '>=', date('Y-m-d'))->orderBy("month", "ASC")->orderBy("start", "ASC")->first();
        return $period;
    }

    public function getThisYearPeriod()
    {
        // งวดค่าแรงในปีปัจจุบัน
        $period = PeriodSalary::whereRaw('substring(start, 1, 4) = '.date('Y'))->orderBy("month", "ASC")->orderBy("start", "ASC")->get();
        return $period;
    }

    // -------------------------- สถิติสะสมประจำงวด -----------------------------
    public function sickThisPeriod($emp_id)
    {
        // ลาป่วย
        $days = 0;
        $minutes = 0;
        $period = self::getThisPeriod();
        if ($period) {
            $data = Leave::where('emp_id', '=', $emp_id)->where('leave_status', '=', 'S')->whereIn('leave_type_id', [2,3])->where('period_salary_id', '=', $period->id)->get();
            // ->where('leave_start_date', '<=', $period->start)->where('leave_end_date', '>=', $period->start)
            // ->orWhere('leave_start_date', '<=', $period->end)->where('leave_end_date', '>=', $period->end)->get();
            if ($data->isNotEmpty()) {
                foreach ($data as $rec) {
                    if ($rec->leave_day > 0) {
                        $days += $rec->leave_day;
                    }
                    if ($rec->leave_minute > 0) {
                        $minutes += $rec->leave_minute;
                    }
                }
            }
        }
        $result = self::minutesToTime($minutes);
        $result["d"] = (int) $days;
        if ($result["d"] > 0 || $result["h"] > 0 || $result["m"] > 0) {
            $resp = $result["d"] . "-" . $result["h"] . "-" . str_pad($result["m"], 2, "0", STR_PAD_LEFT);
        } else {
            $resp = "";
        }
        return $resp;
    }
    public function privateThisPeriod($emp_id)
    {
        // ลากิจ
        $days = 0;
        $minutes = 0;
        $period = self::getThisPeriod();
        if ($period) {
            $data = Leave::where('emp_id', '=', $emp_id)->where('leave_status', '=', 'S')->where('leave_type_id', '=', 1)->where('period_salary_id', '=', $period->id)->get();
            if ($data->isNotEmpty()) {
                foreach ($data as $rec) {
                    if ($rec->leave_day > 0) {
                        $days += $rec->leave_day;
                    }
                    if ($rec->leave_minute > 0) {
                        $minutes += $rec->leave_minute;
                    }
                }
            }
        }
        $result = self::minutesToTime($minutes);
        $result["d"] = (int) $days;
        if ($result["d"] > 0 || $result["h"] > 0 || $result["m"] > 0) {
            $resp = $result["d"] . "-" . $result["h"] . "-" . str_pad($result["m"], 2, "0", STR_PAD_LEFT);
        } else {
            $resp = "";
        }
        return $resp;
    }
    public function vacationThisPeriod($emp_id)
    {
        // ลาพักร้อน
        $days = 0;
        $minutes = 0;
        $period = self::getThisPeriod();
        if ($period) {
            $data = Leave::where('emp_id', '=', $emp_id)->where('leave_status', '=', 'S')->where('leave_type_id', '=', 7)->where('period_salary_id', '=', $period->id)->get();
            if ($data->isNotEmpty()) {
                foreach ($data as $rec) {
                    if ($rec->leave_day > 0) {
                        $days += $rec->leave_day;
                    }
                    if ($rec->leave_minute > 0) {
                        $minutes += $rec->leave_minute;
                    }
                }
            }
        }
        $result = self::minutesToTime($minutes);
        $result["d"] = (int) $days;
        if ($result["d"] > 0 || $result["h"] > 0 || $result["m"] > 0) {
            $resp = $result["d"] . "-" . $result["h"] . "-" . str_pad($result["m"], 2, "0", STR_PAD_LEFT);
        } else {
            $resp = "";
        }
        return $resp;
    }
    public function compensationThisPeriod($emp_id)
    {
        // ลาหยุดชดเชย
        $days = 0;
        $minutes = 0;
        $period = self::getThisPeriod();
        if ($period) {
            $data = Leave::where('emp_id', '=', $emp_id)->where('leave_status', '=', 'S')->where('leave_type_id', '=', 6)->where('period_salary_id', '=', $period->id)->get();
            if ($data->isNotEmpty()) {
                foreach ($data as $rec) {
                    if ($rec->leave_day > 0) {
                        $days += $rec->leave_day;
                    }
                    if ($rec->leave_minute > 0) {
                        $minutes += $rec->leave_minute;
                    }
                }
            }
        }
        $result = self::minutesToTime($minutes);
        $result["d"] = (int) $days;
        if ($result["d"] > 0 || $result["h"] > 0 || $result["m"] > 0) {
            $resp = $result["d"] . "-" . $result["h"] . "-" . str_pad($result["m"], 2, "0", STR_PAD_LEFT);
        } else {
            $resp = "";
        }
        return $resp;
    }
    public function urgentThisPeriod($emp_id)
    {
        // ลาเร่งด่วน
        $days = 0;
        $minutes = 0;
        $period = self::getThisPeriod();
        if ($period) {
            $data = Leave::where('emp_id', '=', $emp_id)->where('leave_status', '=', 'S')->where('leave_type_id', '=', 4)->where('period_salary_id', '=', $period->id)->get();
            if ($data->isNotEmpty()) {
                foreach ($data as $rec) {
                    if ($rec->leave_day > 0) {
                        $days += $rec->leave_day;
                    }
                    if ($rec->leave_minute > 0) {
                        $minutes += $rec->leave_minute;
                    }
                }
            }
        }
        $result = self::minutesToTime($minutes);
        $result["d"] = (int) $days;
        if ($result["d"] > 0 || $result["h"] > 0 || $result["m"] > 0) {
            $resp = $result["d"] . "-" . $result["h"] . "-" . str_pad($result["m"], 2, "0", STR_PAD_LEFT);
        } else {
            $resp = "";
        }
        return $resp;
    }
    public function unpaidThisPeriod($emp_id)
    {
        // ลาไม่ขอรับค่าจ้าง
        $days = 0;
        $minutes = 0;
        $period = self::getThisPeriod();
        if ($period) {
            $leave_type_id = 0;
            $emp = DB::table('employee')->where('emp_id', '=', $emp_id)->first(['emp_type']);
            if ($emp) {
                if ($emp->emp_type == "M") {
                    $leave_type_id = 5; // รายเดือน
                } else if ($emp->emp_type == "D") {
                    $leave_type_id = 13; // รายวัน
                }
            }
            $data = Leave::where('emp_id', '=', $emp_id)->where('leave_status', '=', 'S')->where('leave_type_id', '=', $leave_type_id)->where('period_salary_id', '=', $period->id)->get();
            if ($data->isNotEmpty()) {
                foreach ($data as $rec) {
                    if ($rec->leave_day > 0) {
                        $days += $rec->leave_day;
                    }
                    if ($rec->leave_minute > 0) {
                        $minutes += $rec->leave_minute;
                    }
                }
            }
        }
        $result = self::minutesToTime($minutes);
        $result["d"] = (int) $days;
        if ($result["d"] > 0 || $result["h"] > 0 || $result["m"] > 0) {
            $resp = $result["d"] . "-" . $result["h"] . "-" . str_pad($result["m"], 2, "0", STR_PAD_LEFT);
        } else {
            $resp = "";
        }
        return $resp;
    }
    public function maternityThisPeriod($emp_id)
    {
        // ลาคลอด
        $days = 0;
        $minutes = 0;
        $period = self::getThisPeriod();
        if ($period) {
            $data = Leave::where('emp_id', '=', $emp_id)->where('leave_status', '=', 'S')->where('leave_type_id', '=', 9)->where('period_salary_id', '=', $period->id)->get();
            if ($data->isNotEmpty()) {
                foreach ($data as $rec) {
                    if ($rec->leave_day > 0) {
                        $days += $rec->leave_day;
                    }
                    if ($rec->leave_minute > 0) {
                        $minutes += $rec->leave_minute;
                    }
                }
            }
        }
        $result = self::minutesToTime($minutes);
        $result["d"] = (int) $days;
        if ($result["d"] > 0 || $result["h"] > 0 || $result["m"] > 0) {
            $resp = $result["d"] . "-" . $result["h"] . "-" . str_pad($result["m"], 2, "0", STR_PAD_LEFT);
        } else {
            $resp = "";
        }
        return $resp;
    }
    public function ordinationThisPeriod($emp_id)
    {
        // ลาบวช
        $days = 0;
        $minutes = 0;
        $period = self::getThisPeriod();
        if ($period) {
            $data = Leave::where('emp_id', '=', $emp_id)->where('leave_status', '=', 'S')->where('leave_type_id', '=', 8)->where('period_salary_id', '=', $period->id)->get();
            if ($data->isNotEmpty()) {
                foreach ($data as $rec) {
                    if ($rec->leave_day > 0) {
                        $days += $rec->leave_day;
                    }
                    if ($rec->leave_minute > 0) {
                        $minutes += $rec->leave_minute;
                    }
                }
            }
        }
        $result = self::minutesToTime($minutes);
        $result["d"] = (int) $days;
        if ($result["d"] > 0 || $result["h"] > 0 || $result["m"] > 0) {
            $resp = $result["d"] . "-" . $result["h"] . "-" . str_pad($result["m"], 2, "0", STR_PAD_LEFT);
        } else {
            $resp = "";
        }
        return $resp;
    }
    public function militaryServiceThisPeriod($emp_id)
    {
        // ลาไปรับราชการทหาร
        $days = 0;
        $minutes = 0;
        $period = self::getThisPeriod();
        if ($period) {
            $data = Leave::where('emp_id', '=', $emp_id)->where('leave_status', '=', 'S')->where('leave_type_id', '=', 10)->where('period_salary_id', '=', $period->id)->get();
            if ($data->isNotEmpty()) {
                foreach ($data as $rec) {
                    if ($rec->leave_day > 0) {
                        $days += $rec->leave_day;
                    }
                    if ($rec->leave_minute > 0) {
                        $minutes += $rec->leave_minute;
                    }
                }
            }
        }
        $result = self::minutesToTime($minutes);
        $result["d"] = (int) $days;
        return $result;
    }
    public function marriageThisPeriod($emp_id)
    {
        // ลาแต่งงาน
        $days = 0;
        $minutes = 0;
        $period = self::getThisPeriod();
        if ($period) {
            $data = Leave::where('emp_id', '=', $emp_id)->where('leave_status', '=', 'S')->where('leave_type_id', '=', 11)->where('period_salary_id', '=', $period->id)->get();
            if ($data->isNotEmpty()) {
                foreach ($data as $rec) {
                    if ($rec->leave_day > 0) {
                        $days += $rec->leave_day;
                    }
                    if ($rec->leave_minute > 0) {
                        $minutes += $rec->leave_minute;
                    }
                }
            }
        }
        $result = self::minutesToTime($minutes);
        $result["d"] = (int) $days;
        return $result;
    }
    public function onSiteThisPeriod($emp_id)
    {
        // ลาอบรมนอกสถานที่
        $days = 0;
        $minutes = 0;
        $period = self::getThisPeriod();
        if ($period) {
            $data = Leave::where('emp_id', '=', $emp_id)->where('leave_status', '=', 'S')->where('leave_type_id', '=', 12)->where('period_salary_id', '=', $period->id)->get();
            if ($data->isNotEmpty()) {
                foreach ($data as $rec) {
                    if ($rec->leave_day > 0) {
                        $days += $rec->leave_day;
                    }
                    if ($rec->leave_minute > 0) {
                        $minutes += $rec->leave_minute;
                    }
                }
            }
        }
        $result = self::minutesToTime($minutes);
        $result["d"] = (int) $days;
        if ($result["d"] > 0 || $result["h"] > 0 || $result["m"] > 0) {
            $resp = $result["d"] . "-" . $result["h"] . "-" . str_pad($result["m"], 2, "0", STR_PAD_LEFT);
        } else {
            $resp = "";
        }
        return $resp;
    }
    public function otherThisPeriod($emp_id)
    {
        // ลาอื่นๆ (ทหาร,แต่งงาน)
        $days = 0;
        $hours = 0;
        $minutes = 0;
        $leave = self::militaryServiceThisPeriod($emp_id);
        $days += (int) $leave["d"];
        $hours += (int) $leave["h"];
        $minutes += (int) $leave["m"];
        $leave = self::marriageThisPeriod($emp_id);
        $days += (int) $leave["d"];
        $hours += (int) $leave["h"];
        $minutes += (int) $leave["m"];

        $seconds = ($days * 86400) + ($hours * 3600) + ($minutes * 60);
        $result = self::secondsToTime($seconds);
        if ($result["d"] > 0 || $result["h"] > 0 || $result["m"] > 0) {
            $resp = $result["d"] . "-" . $result["h"] . "-" . str_pad($result["m"], 2, "0", STR_PAD_LEFT);
        } else {
            $resp = "";
        }
        return $resp;
    }
    // -------------------------- END สถิติสะสมประจำงวด -----------------------------

    // -------------------------- สถิติสะสมประจำปี -----------------------------
    public function sickThisYear($emp_id)
    {
        // ลาป่วย
        $days = 0;
        $minutes = 0;
        $period = self::getThisYearPeriod();
        if ($period->isNotEmpty()) {
            foreach ($period as $period) {
                $data = Leave::where('emp_id', '=', $emp_id)->where('leave_status', '=', 'S')->whereIn('leave_type_id', [2,3])->where('period_salary_id', '=', $period->id)->get();
                if ($data->isNotEmpty()) {
                    foreach ($data as $rec) {
                        if ($rec->leave_day > 0) {
                            $days += $rec->leave_day;
                        }
                        if ($rec->leave_minute > 0) {
                            $minutes += $rec->leave_minute;
                        }
                    }
                }
            }
        }
        $result = self::minutesToTime($minutes);
        $result["d"] = (int) $days;
        if ($result["d"] > 0 || $result["h"] > 0 || $result["m"] > 0) {
            $resp = $result["d"] . "-" . $result["h"] . "-" . str_pad($result["m"], 2, "0", STR_PAD_LEFT);
        } else {
            $resp = "";
        }
        return $resp;
    }
    public function privateThisYear($emp_id)
    {
        // ลากิจ
        $days = 0;
        $minutes = 0;
        $period = self::getThisYearPeriod();
        if ($period->isNotEmpty()) {
            foreach ($period as $period) {
                $data = Leave::where('emp_id', '=', $emp_id)->where('leave_status', '=', 'S')->where('leave_type_id', '=', 1)->where('period_salary_id', '=', $period->id)->get();
                if ($data->isNotEmpty()) {
                    foreach ($data as $rec) {
                        if ($rec->leave_day > 0) {
                            $days += $rec->leave_day;
                        }
                        if ($rec->leave_minute > 0) {
                            $minutes += $rec->leave_minute;
                        }
                    }
                }
            }
        }
        $result = self::minutesToTime($minutes);
        $result["d"] = (int) $days;
        if ($result["d"] > 0 || $result["h"] > 0 || $result["m"] > 0) {
            $resp = $result["d"] . "-" . $result["h"] . "-" . str_pad($result["m"], 2, "0", STR_PAD_LEFT);
        } else {
            $resp = "";
        }
        return $resp;
    }
    public function vacationThisYear($emp_id)
    {
        // ลาพักร้อน
        $days = 0;
        $minutes = 0;
        $period = self::getThisYearPeriod();
        if ($period->isNotEmpty()) {
            foreach ($period as $period) {
                $data = Leave::where('emp_id', '=', $emp_id)->where('leave_status', '=', 'S')->where('leave_type_id', '=', 7)->where('period_salary_id', '=', $period->id)->get();
                if ($data->isNotEmpty()) {
                    foreach ($data as $rec) {
                        if ($rec->leave_day > 0) {
                            $days += $rec->leave_day;
                        }
                        if ($rec->leave_minute > 0) {
                            $minutes += $rec->leave_minute;
                        }
                    }
                }
            }
        }
        $result = self::minutesToTime($minutes);
        $result["d"] = (int) $days;
        if ($result["d"] > 0 || $result["h"] > 0 || $result["m"] > 0) {
            $resp = $result["d"] . "-" . $result["h"] . "-" . str_pad($result["m"], 2, "0", STR_PAD_LEFT);
        } else {
            $resp = "";
        }
        return $resp;
    }
    public function compensationThisYear($emp_id)
    {
        // ลาหยุดชดเชย
        $days = 0;
        $minutes = 0;
        $period = self::getThisYearPeriod();
        if ($period->isNotEmpty()) {
            foreach ($period as $period) {
                $data = Leave::where('emp_id', '=', $emp_id)->where('leave_status', '=', 'S')->where('leave_type_id', '=', 6)->where('period_salary_id', '=', $period->id)->get();
                if ($data->isNotEmpty()) {
                    foreach ($data as $rec) {
                        if ($rec->leave_day > 0) {
                            $days += $rec->leave_day;
                        }
                        if ($rec->leave_minute > 0) {
                            $minutes += $rec->leave_minute;
                        }
                    }
                }
            }
        }
        $result = self::minutesToTime($minutes);
        $result["d"] = (int) $days;
        if ($result["d"] > 0 || $result["h"] > 0 || $result["m"] > 0) {
            $resp = $result["d"] . "-" . $result["h"] . "-" . str_pad($result["m"], 2, "0", STR_PAD_LEFT);
        } else {
            $resp = "";
        }
        return $resp;
    }
    public function urgentThisYear($emp_id)
    {
        // ลาเร่งด่วน
        $days = 0;
        $minutes = 0;
        $period = self::getThisYearPeriod();
        if ($period->isNotEmpty()) {
            foreach ($period as $period) {
                $data = Leave::where('emp_id', '=', $emp_id)->where('leave_status', '=', 'S')->where('leave_type_id', '=', 4)->where('period_salary_id', '=', $period->id)->get();
                if ($data->isNotEmpty()) {
                    foreach ($data as $rec) {
                        if ($rec->leave_day > 0) {
                            $days += $rec->leave_day;
                        }
                        if ($rec->leave_minute > 0) {
                            $minutes += $rec->leave_minute;
                        }
                    }
                }
            }
        }
        $result = self::minutesToTime($minutes);
        $result["d"] = (int) $days;
        if ($result["d"] > 0 || $result["h"] > 0 || $result["m"] > 0) {
            $resp = $result["d"] . "-" . $result["h"] . "-" . str_pad($result["m"], 2, "0", STR_PAD_LEFT);
        } else {
            $resp = "";
        }
        return $resp;
    }
    public function unpaidThisYear($emp_id)
    {
        // ลาไม่ขอรับค่าจ้าง
        $days = 0;
        $minutes = 0;
        $period = self::getThisYearPeriod();
        if ($period->isNotEmpty()) {
            foreach ($period as $period) {
                $data = Leave::where('emp_id', '=', $emp_id)->where('leave_status', '=', 'S')->where('leave_type_id', '=', 5)->where('period_salary_id', '=', $period->id)->get();
                if ($data->isNotEmpty()) {
                    foreach ($data as $rec) {
                        if ($rec->leave_day > 0) {
                            $days += $rec->leave_day;
                        }
                        if ($rec->leave_minute > 0) {
                            $minutes += $rec->leave_minute;
                        }
                    }
                }
            }
        }
        $result = self::minutesToTime($minutes);
        $result["d"] = (int) $days;
        if ($result["d"] > 0 || $result["h"] > 0 || $result["m"] > 0) {
            $resp = $result["d"] . "-" . $result["h"] . "-" . str_pad($result["m"], 2, "0", STR_PAD_LEFT);
        } else {
            $resp = "";
        }
        return $resp;
    }
    public function maternityThisYear($emp_id)
    {
        // ลาคลอด
        $days = 0;
        $minutes = 0;
        $period = self::getThisYearPeriod();
        if ($period->isNotEmpty()) {
            foreach ($period as $period) {
                $data = Leave::where('emp_id', '=', $emp_id)->where('leave_status', '=', 'S')->where('leave_type_id', '=', 9)->where('period_salary_id', '=', $period->id)->get();
                if ($data->isNotEmpty()) {
                    foreach ($data as $rec) {
                        if ($rec->leave_day > 0) {
                            $days += $rec->leave_day;
                        }
                        if ($rec->leave_minute > 0) {
                            $minutes += $rec->leave_minute;
                        }
                    }
                }
            }
        }
        $result = self::minutesToTime($minutes);
        $result["d"] = (int) $days;
        if ($result["d"] > 0 || $result["h"] > 0 || $result["m"] > 0) {
            $resp = $result["d"] . "-" . $result["h"] . "-" . str_pad($result["m"], 2, "0", STR_PAD_LEFT);
        } else {
            $resp = "";
        }
        return $resp;
    }
    public function ordinationThisYear($emp_id)
    {
        // ลาบวช
        $days = 0;
        $minutes = 0;
        $period = self::getThisYearPeriod();
        if ($period->isNotEmpty()) {
            foreach ($period as $period) {
                $data = Leave::where('emp_id', '=', $emp_id)->where('leave_status', '=', 'S')->where('leave_type_id', '=', 8)->where('period_salary_id', '=', $period->id)->get();
                if ($data->isNotEmpty()) {
                    foreach ($data as $rec) {
                        if ($rec->leave_day > 0) {
                            $days += $rec->leave_day;
                        }
                        if ($rec->leave_minute > 0) {
                            $minutes += $rec->leave_minute;
                        }
                    }
                }
            }
        }
        $result = self::minutesToTime($minutes);
        $result["d"] = (int) $days;
        if ($result["d"] > 0 || $result["h"] > 0 || $result["m"] > 0) {
            $resp = $result["d"] . "-" . $result["h"] . "-" . str_pad($result["m"], 2, "0", STR_PAD_LEFT);
        } else {
            $resp = "";
        }
        return $resp;
    }
    public function militaryServiceThisYear($emp_id)
    {
        // ลาไปรับราชการทหาร
        $days = 0;
        $minutes = 0;
        $period = self::getThisYearPeriod();
        if ($period->isNotEmpty()) {
            foreach ($period as $period) {
                $data = Leave::where('emp_id', '=', $emp_id)->where('leave_status', '=', 'S')->where('leave_type_id', '=', 10)->where('period_salary_id', '=', $period->id)->get();
                if ($data->isNotEmpty()) {
                    foreach ($data as $rec) {
                        if ($rec->leave_day > 0) {
                            $days += $rec->leave_day;
                        }
                        if ($rec->leave_minute > 0) {
                            $minutes += $rec->leave_minute;
                        }
                    }
                }
            }
        }
        $result = self::minutesToTime($minutes);
        $result["d"] = (int) $days;
        return $result;
    }
    public function marriageThisYear($emp_id)
    {
        // ลาแต่งงาน
        $days = 0;
        $minutes = 0;
        $period = self::getThisYearPeriod();
        if ($period->isNotEmpty()) {
            foreach ($period as $period) {
                $data = Leave::where('emp_id', '=', $emp_id)->where('leave_status', '=', 'S')->where('leave_type_id', '=', 11)->where('period_salary_id', '=', $period->id)->get();
                if ($data->isNotEmpty()) {
                    foreach ($data as $rec) {
                        if ($rec->leave_day > 0) {
                            $days += $rec->leave_day;
                        }
                        if ($rec->leave_minute > 0) {
                            $minutes += $rec->leave_minute;
                        }
                    }
                }
            }
        }
        $result = self::minutesToTime($minutes);
        $result["d"] = (int) $days;
        return $result;
    }
    public function onSiteThisYear($emp_id)
    {
        // ลาอบรมนอกสถานที่
        $days = 0;
        $minutes = 0;
        $period = self::getThisYearPeriod();
        if ($period->isNotEmpty()) {
            foreach ($period as $period) {
                $data = Leave::where('emp_id', '=', $emp_id)->where('leave_status', '=', 'S')->where('leave_type_id', '=', 12)->where('period_salary_id', '=', $period->id)->get();
                if ($data->isNotEmpty()) {
                    foreach ($data as $rec) {
                        if ($rec->leave_day > 0) {
                            $days += $rec->leave_day;
                        }
                        if ($rec->leave_minute > 0) {
                            $minutes += $rec->leave_minute;
                        }
                    }
                }
            }
        }
        $result = self::minutesToTime($minutes);
        $result["d"] = (int) $days;
        if ($result["d"] > 0 || $result["h"] > 0 || $result["m"] > 0) {
            $resp = $result["d"] . "-" . $result["h"] . "-" . str_pad($result["m"], 2, "0", STR_PAD_LEFT);
        } else {
            $resp = "";
        }
        return $resp;
    }
    public function otherThisYear($emp_id)
    {
        // ลาอื่นๆ (ทหาร,แต่งงาน)
        $days = 0;
        $hours = 0;
        $minutes = 0;
        $leave = self::militaryServiceThisYear($emp_id);
        $days += (int) $leave["d"];
        $hours += (int) $leave["h"];
        $minutes += (int) $leave["m"];
        $leave = self::marriageThisYear($emp_id);
        $days += (int) $leave["d"];
        $hours += (int) $leave["h"];
        $minutes += (int) $leave["m"];

        $seconds = ($days * 86400) + ($hours * 3600) + ($minutes * 60);
        $result = self::secondsToTime($seconds);
        if ($result["d"] > 0 || $result["h"] > 0 || $result["m"] > 0) {
            $resp = $result["d"] . "-" . $result["h"] . "-" . str_pad($result["m"], 2, "0", STR_PAD_LEFT);
        } else {
            $resp = "";
        }
        return $resp;
    }
    // -------------------------- END สถิติสะสมประจำปี -----------------------------

    // -------------------------- รายละเอียดสถิติการลางานสะสม -----------------------------
    public function sickByPeriod($emp_id, $id)
    {
        // ลาป่วย
        $days = 0;
        $minutes = 0;
        $data = Leave::where('emp_id', '=', $emp_id)->where('leave_status', '=', 'S')->whereIn('leave_type_id', [2, 3])->where('period_salary_id', '=', $id)->get();
        if ($data->isNotEmpty()) {
            foreach ($data as $rec) {
                if ($rec->leave_day > 0) {
                    $days += $rec->leave_day;
                }
                if ($rec->leave_minute > 0) {
                    $minutes += $rec->leave_minute;
                }
            }
        }
        $result = self::minutesToTime($minutes);
        $result["d"] = (int) $days;
        if ($result["d"] > 0 || $result["h"] > 0 || $result["m"] > 0) {
            $resp = $result["d"] . "-" . $result["h"] . "-" . str_pad($result["m"], 2, "0", STR_PAD_LEFT);
        } else {
            $resp = "";
        }
        return $resp;
    }
    public function privateByPeriod($emp_id, $id)
    {
        // ลากิจ
        $days = 0;
        $minutes = 0;
        $data = Leave::where('emp_id', '=', $emp_id)->where('leave_status', '=', 'S')->where('leave_type_id', '=', 1)->where('period_salary_id', '=', $id)->get();
        if ($data->isNotEmpty()) {
            foreach ($data as $rec) {
                if ($rec->leave_day > 0) {
                    $days += $rec->leave_day;
                }
                if ($rec->leave_minute > 0) {
                    $minutes += $rec->leave_minute;
                }
            }
        }
        $result = self::minutesToTime($minutes);
        $result["d"] = (int) $days;
        if ($result["d"] > 0 || $result["h"] > 0 || $result["m"] > 0) {
            $resp = $result["d"] . "-" . $result["h"] . "-" . str_pad($result["m"], 2, "0", STR_PAD_LEFT);
        } else {
            $resp = "";
        }
        return $resp;
    }
    public function vacationByPeriod($emp_id, $id)
    {
        // ลาพักร้อน
        $days = 0;
        $minutes = 0;
        $data = Leave::where('emp_id', '=', $emp_id)->where('leave_status', '=', 'S')->where('leave_type_id', '=', 7)->where('period_salary_id', '=', $id)->get();
        if ($data->isNotEmpty()) {
            foreach ($data as $rec) {
                if ($rec->leave_day > 0) {
                    $days += $rec->leave_day;
                }
                if ($rec->leave_minute > 0) {
                    $minutes += $rec->leave_minute;
                }
            }
        }
        $result = self::minutesToTime($minutes);
        $result["d"] = (int) $days;
        if ($result["d"] > 0 || $result["h"] > 0 || $result["m"] > 0) {
            $resp = $result["d"] . "-" . $result["h"] . "-" . str_pad($result["m"], 2, "0", STR_PAD_LEFT);
        } else {
            $resp = "";
        }
        return $resp;
    }
    public function compensationByPeriod($emp_id, $id)
    {
        // ลาหยุดชดเชย
        $days = 0;
        $minutes = 0;
        $data = Leave::where('emp_id', '=', $emp_id)->where('leave_status', '=', 'S')->where('leave_type_id', '=', 6)->where('period_salary_id', '=', $id)->get();
        if ($data->isNotEmpty()) {
            foreach ($data as $rec) {
                if ($rec->leave_day > 0) {
                    $days += $rec->leave_day;
                }
                if ($rec->leave_minute > 0) {
                    $minutes += $rec->leave_minute;
                }
            }
        }
        $result = self::minutesToTime($minutes);
        $result["d"] = (int) $days;
        if ($result["d"] > 0 || $result["h"] > 0 || $result["m"] > 0) {
            $resp = $result["d"] . "-" . $result["h"] . "-" . str_pad($result["m"], 2, "0", STR_PAD_LEFT);
        } else {
            $resp = "";
        }
        return $resp;
    }
    public function urgentByPeriod($emp_id, $id)
    {
        // ลาเร่งด่วน
        $days = 0;
        $minutes = 0;
        $data = Leave::where('emp_id', '=', $emp_id)->where('leave_status', '=', 'S')->where('leave_type_id', '=', 4)->where('period_salary_id', '=', $id)->get();
        if ($data->isNotEmpty()) {
            foreach ($data as $rec) {
                if ($rec->leave_day > 0) {
                    $days += $rec->leave_day;
                }
                if ($rec->leave_minute > 0) {
                    $minutes += $rec->leave_minute;
                }
            }
        }
        $result = self::minutesToTime($minutes);
        $result["d"] = (int) $days;
        if ($result["d"] > 0 || $result["h"] > 0 || $result["m"] > 0) {
            $resp = $result["d"] . "-" . $result["h"] . "-" . str_pad($result["m"], 2, "0", STR_PAD_LEFT);
        } else {
            $resp = "";
        }
        return $resp;
    }
    public function unpaidByPeriod($emp_id, $id)
    {
        // ลาไม่ขอรับค่าจ้าง
        $days = 0;
        $minutes = 0;
        $data = Leave::where('emp_id', '=', $emp_id)->where('leave_status', '=', 'S')->where('leave_type_id', '=', 5)->where('period_salary_id', '=', $id)->get();
        if ($data->isNotEmpty()) {
            foreach ($data as $rec) {
                if ($rec->leave_day > 0) {
                    $days += $rec->leave_day;
                }
                if ($rec->leave_minute > 0) {
                    $minutes += $rec->leave_minute;
                }
            }
        }
        $result = self::minutesToTime($minutes);
        $result["d"] = (int) $days;
        if ($result["d"] > 0 || $result["h"] > 0 || $result["m"] > 0) {
            $resp = $result["d"] . "-" . $result["h"] . "-" . str_pad($result["m"], 2, "0", STR_PAD_LEFT);
        } else {
            $resp = "";
        }
        return $resp;
    }
    public function maternityByPeriod($emp_id, $id)
    {
        // ลาคลอด
        $days = 0;
        $minutes = 0;
        $data = Leave::where('emp_id', '=', $emp_id)->where('leave_status', '=', 'S')->where('leave_type_id', '=', 9)->where('period_salary_id', '=', $id)->get();
        if ($data->isNotEmpty()) {
            foreach ($data as $rec) {
                if ($rec->leave_day > 0) {
                    $days += $rec->leave_day;
                }
                if ($rec->leave_minute > 0) {
                    $minutes += $rec->leave_minute;
                }
            }
        }
        $result = self::minutesToTime($minutes);
        $result["d"] = (int) $days;
        if ($result["d"] > 0 || $result["h"] > 0 || $result["m"] > 0) {
            $resp = $result["d"] . "-" . $result["h"] . "-" . str_pad($result["m"], 2, "0", STR_PAD_LEFT);
        } else {
            $resp = "";
        }
        return $resp;
    }
    public function ordinationByPeriod($emp_id, $id)
    {
        // ลาบวช
        $days = 0;
        $minutes = 0;
        $data = Leave::where('emp_id', '=', $emp_id)->where('leave_status', '=', 'S')->where('leave_type_id', '=', 8)->where('period_salary_id', '=', $id)->get();
        if ($data->isNotEmpty()) {
            foreach ($data as $rec) {
                if ($rec->leave_day > 0) {
                    $days += $rec->leave_day;
                }
                if ($rec->leave_minute > 0) {
                    $minutes += $rec->leave_minute;
                }
            }
        }
        $result = self::minutesToTime($minutes);
        $result["d"] = (int) $days;
        if ($result["d"] > 0 || $result["h"] > 0 || $result["m"] > 0) {
            $resp = $result["d"] . "-" . $result["h"] . "-" . str_pad($result["m"], 2, "0", STR_PAD_LEFT);
        } else {
            $resp = "";
        }
        return $resp;
    }
    public function militaryServiceByPeriod($emp_id, $id)
    {
        // ลาไปรับราชการทหาร
        $days = 0;
        $minutes = 0;
        $data = Leave::where('emp_id', '=', $emp_id)->where('leave_status', '=', 'S')->where('leave_type_id', '=', 10)->where('period_salary_id', '=', $id)->get();
        if ($data->isNotEmpty()) {
            foreach ($data as $rec) {
                if ($rec->leave_day > 0) {
                    $days += $rec->leave_day;
                }
                if ($rec->leave_minute > 0) {
                    $minutes += $rec->leave_minute;
                }
            }
        }
        $result = self::minutesToTime($minutes);
        $result["d"] = (int) $days;
        return $result;
    }
    public function marriageByPeriod($emp_id, $id)
    {
        // ลาแต่งงาน
        $days = 0;
        $minutes = 0;
        $data = Leave::where('emp_id', '=', $emp_id)->where('leave_status', '=', 'S')->where('leave_type_id', '=', 11)->where('period_salary_id', '=', $id)->get();
        if ($data->isNotEmpty()) {
            foreach ($data as $rec) {
                if ($rec->leave_day > 0) {
                    $days += $rec->leave_day;
                }
                if ($rec->leave_minute > 0) {
                    $minutes += $rec->leave_minute;
                }
            }
        }
        $result = self::minutesToTime($minutes);
        $result["d"] = (int) $days;
        return $result;
    }
    public function onSiteByPeriod($emp_id, $id)
    {
        // ลาอบรมนอกสถานที่
        $days = 0;
        $minutes = 0;
        $data = Leave::where('emp_id', '=', $emp_id)->where('leave_status', '=', 'S')->where('leave_type_id', '=', 12)->where('period_salary_id', '=', $id)->get();
        if ($data->isNotEmpty()) {
            foreach ($data as $rec) {
                if ($rec->leave_day > 0) {
                    $days += $rec->leave_day;
                }
                if ($rec->leave_minute > 0) {
                    $minutes += $rec->leave_minute;
                }
            }
        }
        $result = self::minutesToTime($minutes);
        $result["d"] = (int) $days;
        if ($result["d"] > 0 || $result["h"] > 0 || $result["m"] > 0) {
            $resp = $result["d"] . "-" . $result["h"] . "-" . str_pad($result["m"], 2, "0", STR_PAD_LEFT);
        } else {
            $resp = "";
        }
        return $resp;
    }
    public function otherByPeriod($emp_id, $id)
    {
        // ลาอื่นๆ (ทหาร,แต่งงาน)
        $days = 0;
        $hours = 0;
        $minutes = 0;
        $leave = self::militaryServiceByPeriod($emp_id, $id);
        $days += (int) $leave["d"];
        $hours += (int) $leave["h"];
        $minutes += (int) $leave["m"];
        $leave = self::marriageByPeriod($emp_id, $id);
        $days += (int) $leave["d"];
        $hours += (int) $leave["h"];
        $minutes += (int) $leave["m"];

        $seconds = ($days * 86400) + ($hours * 3600) + ($minutes * 60);
        $result = self::secondsToTime($seconds);
        if ($result["d"] > 0 || $result["h"] > 0 || $result["m"] > 0) {
            $resp = $result["d"] . "-" . $result["h"] . "-" . str_pad($result["m"], 2, "0", STR_PAD_LEFT);
        } else {
            $resp = "";
        }
        return $resp;
    }
    // -------------------------- END รายละเอียดสถิติการลางานสะสม -----------------------------
}