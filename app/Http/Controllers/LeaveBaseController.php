<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\Authorization;
use App\Models\AuthorizationManual;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Event;
use App\Models\Leave;
use App\Models\LeaveLog;
use App\Models\LeaveTypeProperty;
use App\Models\RecordWorking;
use App\Models\PeriodSalary;
use App\Models\RecordWorkingLog;
use App\Services\ThaiDateHelperService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LeaveBaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function leaveSecondsToTime($inputSeconds)
    {
        $secondsInAMinute = 60;
        $secondsInAnHour  = 60 * $secondsInAMinute;
        $secondsInADay    = 8 * $secondsInAnHour;

        // extract days
        $days = floor($inputSeconds / $secondsInADay);

        // extract hours
        $hourSeconds = $inputSeconds % $secondsInADay;
        $hours = floor($hourSeconds / $secondsInAnHour);

        // extract minutes
        $minuteSeconds = $hourSeconds % $secondsInAnHour;
        $minutes = floor($minuteSeconds / $secondsInAMinute);

        // extract the remaining seconds
        $remainingSeconds = $minuteSeconds % $secondsInAMinute;
        $seconds = ceil($remainingSeconds);

        // return the final array
        $obj = array(
            'd' => (int) $days,
            'h' => (int) $hours,
            'm' => (int) $minutes,
            's' => (int) $seconds,
        );
        return $obj;
    }

    public function secondsToTime($inputSeconds)
    {
        $secondsInAMinute = 60;
        $secondsInAnHour  = 60 * $secondsInAMinute;
        $secondsInADay    = 24 * $secondsInAnHour;

        // extract days
        $days = floor($inputSeconds / $secondsInADay);

        // extract hours
        $hourSeconds = $inputSeconds % $secondsInADay;
        $hours = floor($hourSeconds / $secondsInAnHour);

        // extract minutes
        $minuteSeconds = $hourSeconds % $secondsInAnHour;
        $minutes = floor($minuteSeconds / $secondsInAMinute);

        // extract the remaining seconds
        $remainingSeconds = $minuteSeconds % $secondsInAMinute;
        $seconds = ceil($remainingSeconds);

        // return the final array
        $obj = array(
            'd' => (int) $days,
            'h' => (int) $hours,
            'm' => (int) $minutes,
            's' => (int) $seconds,
        );
        return $obj;
    }

    public function minutesToTime($time)
    {
        $hours = floor($time / 60);
        $minutes = ($time % 60);

        // return the final array
        $obj = array(
            'h' => (int) $hours,
            'm' => (int) $minutes,
        );
        return $obj;
    }

    public function hoursandmins($time, $format = '%02d:%02d')
    {
        if ($time < 1) {
            return;
        }
        $hours = floor($time / 60);
        $minutes = ($time % 60);
        return sprintf($format, $hours, $minutes);
    }

    public function calcDaysDiffBetweenTwoDates($sdate, $edate)
    {
        $sdate = date_create($sdate);
        $edate = date_create($edate);
        $interval = date_diff($sdate, $edate);
        return $interval->format('%a');
    }

    public function calcYearsMonthsDaysDiffBetweenTwoDates($sdate, $edate)
    {
        $date_diff = abs(strtotime($edate) - strtotime($sdate));

        $years = floor($date_diff / (365*60*60*24));
        $months = floor(($date_diff - $years * 365*60*60*24) / (30*60*60*24));
        $days = floor(($date_diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

        $result = "";
        if ($years > 0) {
            $result .= $years . " ปี";
        }
        if ($months > 0) {
            if ($result != "") $result .= ", ";
            $result .= $months . " เดือน";
        }
        if ($days > 0) {
            if ($result != "") $result .= ", ";
            $result .= $days . "  วัน";
        }

        return $result;
        // return sprintf("%d ปี, %d เดือน, %d วัน", $years, $months, $days);
    }

    public function calc_date_diff($start, $end)
    {
        // คำนวณจำนวนวันที่ลา
        $dateS = date_create($start);
        $end = date('Y-m-d',strtotime($end . "+1 days"));
        $dateE = date_create($end);
        $diff = date_diff($dateS, $dateE);
        if ((int)$diff->format("%R%a") <= 0) {
            return false;
        }
        return (int)$diff->format("%a");
    }

    public function calc_date_diff2($start, $end)
    {
        // คำนวณจำนวนวันที่ลา
        $dateS = date_create($start);
        $end = date('Y-m-d',strtotime($end . "+1 days"));
        $dateE = date_create($end);
        $diff = date_diff($dateS, $dateE);
        if ((int)$diff->format("%R%a") < 0) {
            return false;
        }
        return (int)$diff->format("%a");
    }

    public function calc_time_diff($start, $end)
    {
        // คำนวณจำนวนนาทีที่ลา
        $timeS = date_create($start);
        $timeE = date_create($end);
        $diff = date_diff($timeS, $timeE);
        $minutes = $diff->days * 24 * 60;
        $minutes += $diff->h * 60;
        $minutes += $diff->i;
        if ((int)$diff->format("%R%h") < 0 || ((int)$diff->format("%R%h") == 0 && (int)$diff->format("%R%i") <= 0)) {
            return false;
        }
        return $minutes;
    }

    public function calcday($day)
    {
        $TH_Day = array("sunday"=>"อาทิตย์","monday"=>"จันทร์","tuesday"=>"อังคาร","wednesday"=>"พุธ","thursday"=>"พฤหัสบดี","friday"=>"ศุกร์","saturday"=>"เสาร์");
        $COLOR_Day = array("sunday"=>"#ff3333","monday"=>"#f7b84b","tuesday"=>"#f672a7","wednesday"=>"#1abc9c","thursday"=>"#fd7e14","friday"=>"#4fc6e1","saturday"=>"#6658dd");
        return array("th"=>$TH_Day[$day], "color"=>$COLOR_Day[$day]);
    }

    public function getBetweenDates($startDate, $endDate)
    {
        $rangArray = [];
        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);
        for ($currentDate = $startDate; $currentDate <= $endDate; $currentDate += (86400)) {
            $date = date('Y-m-d', $currentDate);
            $rangArray[] = $date;
        }
        return $rangArray;
    }

    public function timespan($seconds = 1, $time = '')
    {
        if (
            !is_numeric($seconds)
        ) {
            $seconds = 1;
        }

        if (
            !is_numeric($time)
        ) {
            $time = time();
        }

        if ($time <= $seconds) {
            $seconds = 1;
        } else {
            $seconds = $time - $seconds;
        }

        $str = '';
        $years = floor($seconds / 31536000);

        if ($years > 0) {
            $str .= $years . ' ปี, ';
        }

        $seconds -= $years * 31536000;
        $months = floor($seconds / 2628000);

        if ($years > 0 or $months > 0) {
            if ($months > 0) {
                $str .= $months . ' เดือน, ';
            }

            $seconds -= $months * 2628000;
        }

        $weeks = floor($seconds / 604800);

        if ($years > 0 or $months > 0 or $weeks > 0) {
            if ($weeks > 0) {
                $str .= $weeks . ' สัปดาห์, ';
            }

            $seconds -= $weeks * 604800;
        }

        $days = floor($seconds / 86400);

        if ($months > 0 or $weeks > 0 or $days > 0) {
            if ($days > 0) {
                $str .= $days . ' วัน, ';
            }

            $seconds -= $days * 86400;
        }

        // $hours = floor($seconds / 3600);

        // if ($days > 0 or $hours > 0) {
        //     if ($hours > 0) {
        //         $str .= $hours . ' ชั่วโมง, ';
        //     }

        //     $seconds -= $hours * 3600;
        // }

        // $minutes = floor($seconds / 60);

        // if ($days > 0 or $hours > 0 or $minutes > 0) {
        //     if ($minutes > 0) {
        //         $str .= $minutes . ' นาที, ';
        //     }

        //     $seconds -= $minutes * 60;
        // }

        // if ($str == '') {
        //     $str .= $seconds . ' วินาที';
        // }

        return substr(trim($str), 0, -1);
    }

    public function get_emp_status($id=null)
    {
        $status = array(
            [
                "status" => "1",
                "name" => "ปกติ",
                "bg" => "bg-success",
                "badge" => "badge-soft-success rounded-pill",
            ],
            [
                "status" => "2",
                "name" => "ทดลองงาน",
                "bg" => "bg-info",
                "badge" => "badge-soft-info rounded-pill",
            ],
            [
                "status" => "0",
                "name" => "ลาออก",
                "bg" => "bg-danger",
                "badge" => "badge-soft-danger rounded-pill",
            ],
            [
                "status" => "",
                "name" => "อื่นๆ",
                "bg" => "bg-secondary",
                "badge" => "badge-soft-secondary rounded-pill",
            ],
        );
        if ($id == null) {
            return $status;
        } else {
            $key = array_search($id, array_column($status, 'status'));
            return $status[$key];
        }
    }

    public function get_emp_type($id=null)
    {
        $type = array(
            [
                "type" => "D",
                "name" => "รายวัน",
                "color" => "info",
                "bg" => "bg-info",
                "badge" => "badge-soft-info rounded-pill",
            ],
            [
                "type" => "M",
                "name" => "รายเดือน",
                "color" => "primary",
                "bg" => "bg-primary",
                "badge" => "badge-soft-primary rounded-pill",
            ],
            [
                "type" => "",
                "name" => "อื่นๆ",
                "color" => "secondary",
                "bg" => "bg-secondary",
                "badge" => "badge-soft-secondary rounded-pill",
            ],
        );
        if ($id == null) {
            return $type;
        } else {
            $key = array_search($id, array_column($type, 'type'));
            return $type[$key];
        }
    }

    public function get_leave_status($id=null)
    {
        $status = array(
            [
                "id" => "E",
                "name" => "รอแก้ไข",
                "text" => "white",
                "color" => "warning",
                "bg" => "bg-warning",
                "badge" => "badge-soft-warning rounded-pill",
            ],
            [
                "id" => "P",
                "name" => "รออนุมัติ",
                "text" => "white",
                "color" => "info",
                "bg" => "bg-info",
                "badge" => "badge-soft-info rounded-pill",
            ],
            [
                "id" => "A1",
                "name" => "อนุมัติโดยหัวหน้า",
                "text" => "white",
                "color" => "blue",
                "bg" => "bg-blue",
                "badge" => "badge-soft-blue rounded-pill",
            ],
            [
                "id" => "A2",
                "name" => "อนุมัติโดยผู้จัดการ",
                "text" => "white",
                "color" => "primary",
                "bg" => "bg-primary",
                "badge" => "badge-soft-primary rounded-pill",
            ],
            [
                "id" => "S",
                "name" => "อนุมัติโดยบุคคล",
                "text" => "white",
                "color" => "success",
                "bg" => "bg-success",
                "badge" => "badge-soft-success rounded-pill",
            ],
            // [
            //     "id" => "C1",
            //     "name" => "ยกเลิกโดยหัวหน้า",
            //     "text" => "white",
            //     "color" => "secondary",
            //     "bg" => "bg-secondary",
            //     "badge" => "badge-soft-secondary rounded-pill",
            // ],
            // [
            //     "id" => "C2",
            //     "name" => "ยกเลิกโดยผู้จัดการ",
            //     "text" => "white",
            //     "color" => "secondary",
            //     "bg" => "bg-secondary",
            //     "badge" => "badge-soft-secondary rounded-pill",
            // ],
            // [
            //     "id" => "C3",
            //     "name" => "ยกเลิกโดยบุคคล",
            //     "text" => "white",
            //     "color" => "secondary",
            //     "bg" => "bg-secondary",
            //     "badge" => "badge-soft-secondary rounded-pill",
            // ],
        );
        if ($id == null) {
            return $status;
        } else {
            $status[] = [
                "id" => "C1",
                "name" => "ยกเลิกโดยหัวหน้า",
                "text" => "white",
                "color" => "danger",
                "bg" => "bg-danger",
                "badge" => "badge-soft-danger rounded-pill",
            ];
            $status[] = [
                "id" => "C2",
                "name" => "ยกเลิกโดยผู้จัดการ",
                "text" => "white",
                "color" => "danger",
                "bg" => "bg-danger",
                "badge" => "badge-soft-danger rounded-pill",
            ];
            $status[] = [
                "id" => "C3",
                "name" => "ยกเลิกโดยบุคคล",
                "text" => "white",
                "color" => "danger",
                "bg" => "bg-danger",
                "badge" => "badge-soft-danger rounded-pill",
            ];
            $key = array_search($id, array_column($status, 'id'));
            return $status[$key];
        }
    }

    public function get_leave_status_all()
    {
        $status = array(
            [
                "id" => "E",
                "name" => "รอแก้ไข",
                "text" => "white",
                "color" => "warning",
                "bg" => "bg-warning",
                "badge" => "badge-soft-warning rounded-pill",
            ],
            [
                "id" => "P",
                "name" => "รออนุมัติ",
                "text" => "white",
                "color" => "info",
                "bg" => "bg-info",
                "badge" => "badge-soft-info rounded-pill",
            ],
            [
                "id" => "A1",
                "name" => "อนุมัติโดยหัวหน้า",
                "text" => "white",
                "color" => "blue",
                "bg" => "bg-blue",
                "badge" => "badge-soft-blue rounded-pill",
            ],
            [
                "id" => "A2",
                "name" => "อนุมัติโดยผู้จัดการ",
                "text" => "white",
                "color" => "primary",
                "bg" => "bg-primary",
                "badge" => "badge-soft-primary rounded-pill",
            ],
            [
                "id" => "S",
                "name" => "อนุมัติโดยบุคคล",
                "text" => "white",
                "color" => "success",
                "bg" => "bg-success",
                "badge" => "badge-soft-success rounded-pill",
            ],
            [
                "id" => "C1",
                "name" => "ยกเลิกโดยหัวหน้า",
                "text" => "white",
                "color" => "secondary",
                "bg" => "bg-secondary",
                "badge" => "badge-soft-secondary rounded-pill",
            ],
            [
                "id" => "C2",
                "name" => "ยกเลิกโดยผู้จัดการ",
                "text" => "white",
                "color" => "secondary",
                "bg" => "bg-secondary",
                "badge" => "badge-soft-secondary rounded-pill",
            ],
            [
                "id" => "C3",
                "name" => "ยกเลิกโดยบุคคล",
                "text" => "white",
                "color" => "secondary",
                "bg" => "bg-secondary",
                "badge" => "badge-soft-secondary rounded-pill",
            ],
        );
        return $status;
    }

    public function get_month($m)
    {
        $month = (int)$m;
        $month_arr=array(
            "1"=>"มกราคม",
            "2"=>"กุมภาพันธ์",
            "3"=>"มีนาคม",
            "4"=>"เมษายน",
            "5"=>"พฤษภาคม",
            "6"=>"มิถุนายน",
            "7"=>"กรกฎาคม",
            "8"=>"สิงหาคม",
            "9"=>"กันยายน",
            "10"=>"ตุลาคม",
            "11"=>"พฤศจิกายน",
            "12"=>"ธันวาคม"
        );
        return $month_arr[$month];
    }

    public function get_months()
    {
        $month=array(
            [
                "id" => 1,
                "en" => "January",
                "en_sh" => "Jan.",
                "th" => "มกราคม",
                "th_sh" => "ม.ค.",
            ],
            [
                "id" => 2,
                "en" => "February",
                "en_sh" => "Feb.",
                "th" => "กุมภาพันธ์",
                "th_sh" => "ก.พ.",
            ],
            [
                "id" => 3,
                "en" => "March",
                "en_sh" => "Mar.",
                "th" => "มีนาคม",
                "th_sh" => "มี.ค.",
            ],
            [
                "id" => 4,
                "en" => "April",
                "en_sh" => "Apr.",
                "th" => "เมษายน",
                "th_sh" => "เม.ย.",
            ],
            [
                "id" => 5,
                "en" => "May",
                "en_sh" => "May.",
                "th" => "พฤษภาคม",
                "th_sh" => "พ.ค.",
            ],
            [
                "id" => 6,
                "en" => "June",
                "en_sh" => "Jun.",
                "th" => "มิถุนายน",
                "th_sh" => "มิ.ย.",
            ],
            [
                "id" => 7,
                "en" => "July",
                "en_sh" => "Jul.",
                "th" => "กรกฎาคม",
                "th_sh" => "ก.ค.",
            ],
            [
                "id" => 8,
                "en" => "August",
                "en_sh" => "Aug.",
                "th" => "สิงหาคม",
                "th_sh" => "ส.ค.",
            ],
            [
                "id" => 9,
                "en" => "September",
                "en_sh" => "Sep.",
                "th" => "กันยายน",
                "th_sh" => "ก.ย.",
            ],
            [
                "id" => 10,
                "en" => "October",
                "en_sh" => "Oct.",
                "th" => "ตุลาคม",
                "th_sh" => "ต.ค.",
            ],
            [
                "id" => 11,
                "en" => "November",
                "en_sh" => "Nov.",
                "th" => "พฤศจิกายน",
                "th_sh" => "พ.ย.",
            ],
            [
                "id" => 12,
                "en" => "December",
                "en_sh" => "Dec.",
                "th" => "ธันวาคม",
                "th_sh" => "ธ.ค.",
            ],
        );
        return $month;
    }

    public function getDeptLevel($dept_id)
    {
        $dept_level = array(
            [
                "level" => 0,
                "detail" => [
                    "dept_id" => "",
                    "dept_name" => "",
                ]
            ],
            [
                "level" => 1,
                "detail" => [
                    "dept_id" => "",
                    "dept_name" => "",
                ]
            ],
            [
                "level" => 2,
                "detail" => [
                    "dept_id" => "",
                    "dept_name" => "",
                ]
            ],
            [
                "level" => 3,
                "detail" => [
                    "dept_id" => "",
                    "dept_name" => "",
                ]
            ],
            [
                "level" => 4,
                "detail" => [
                    "dept_id" => "",
                    "dept_name" => "",
                ]
            ]
        );
        $dept = Department::where('dept_id', $dept_id)->first();
        if ($dept) {
            $key = array_search($dept->level, array_column($dept_level, 'level'));
            $dept_level[$key]["detail"]["dept_id"] = $dept->dept_id;
            $dept_level[$key]["detail"]["dept_name"] = $dept->dept_name;
            $dept_parent = $dept->dept_parent;
            for ($i = $dept->level; $i>=0; $i--) {
                $level = Department::where('dept_id', $dept_parent)->where('level', $i)->first();
                if ($level) {
                    $key = array_search($level->level, array_column($dept_level, 'level'));
                    $dept_level[$key]["detail"]["dept_id"] = $level->dept_id;
                    $dept_level[$key]["detail"]["dept_name"] = $level->dept_name;
                    $dept_parent = $level->dept_parent;
                }
            }
        }
        return json_decode(json_encode($dept_level));
    }

    public function passed_pro_worked_days($emp_id)
    {
        // ระยะเวลาทำงานหลังผ่านทดลองงาน
        $data = Employee::where('emp_id', '=', $emp_id)->first();
        $sdate = $data->start_work_date; // วันที่ผ่านทดลองงาน
        $edate = date('Y-m-d');
        $result["worked_days"] = self::calcDaysDiffBetweenTwoDates($sdate, $edate);
        $result["worked_text"] = self::calcYearsMonthsDaysDiffBetweenTwoDates($sdate, $edate);
        return $result;
    }

    public function worked_days($emp_id)
    {
        // ระยะเวลาทำงาน
        $data = Employee::where('emp_id', '=', $emp_id)->first();
        $sdate = $data->start_work_date;
        $edate = date('Y-m-d');
        $result["worked_days"] = self::calcDaysDiffBetweenTwoDates($sdate, $edate);
        $result["worked_text"] = self::calcYearsMonthsDaysDiffBetweenTwoDates($sdate, $edate);
        return $result;
    }

    public function leave_doc_worked_days($emp_id, $leave_id)
    {
        // ระยะเวลาทำงาน ของใบลา
        $data = Employee::where('emp_id', '=', $emp_id)->first();
        $sdate = $data->start_work_date;
        $leave = Leave::where('leave_id', '=', $leave_id)->first();
        $edate = substr($leave->created_at, 0, 10);
        $result["worked_days"] = self::calcDaysDiffBetweenTwoDates($sdate, $edate);
        $result["worked_text"] = self::calcYearsMonthsDaysDiffBetweenTwoDates($sdate, $edate);
        return $result;
    }

    public function rw_doc_worked_days($emp_id, $rw_id)
    {
        // ระยะเวลาทำงาน ของใบลา
        $data = Employee::where('emp_id', '=', $emp_id)->first();
        $sdate = $data->start_work_date;
        $leave = RecordWorking::find($rw_id);
        $edate = substr($leave->created_at, 0, 10);
        $result["worked_days"] = self::calcDaysDiffBetweenTwoDates($sdate, $edate);
        $result["worked_text"] = self::calcYearsMonthsDaysDiffBetweenTwoDates($sdate, $edate);
        return $result;
    }

    public function getHolidayDateAndTitle()
    {
        $holiday = Event::where('holiday', '=', '1')->where('status', '=', '1')->orderBy("start", "ASC")->take(100)->select('start', 'title')->get();
        $arr = array();
        foreach ($holiday as $hol) {
            $arr[] = array(
                "date"=>$hol->start,
                "title"=>$hol->title
            );
        }
        return $arr;
    }

    public function getHolidayDate()
    {
        $holiday = Event::where('holiday', '=', '1')->where('status', '=', '1')->orderBy("start", "ASC")->take(100)->select('start')->get();
        $arr = array();
        foreach ($holiday as $hol) {
            $arr[] = $hol->start;
        }
        return $arr;
    }

    public function getRecordWorkingDate()
    {
        $auth = auth()->user();
        $record_working = RecordWorking::where('emp_id', '=', $auth->emp_id)->where('use_status', '=', 1)->orderBy('work_date', 'ASC')->get();
        $arr = array();
        foreach ($record_working as $v) {
            $arr[] = $v->work_date;
        }
        return $arr;
    }

    public function getAuthorizeUsers()
    {
        // เรียกข้อมูลพนักงานที่อยู่ภายใต้สิทธิ์การอนุมัติของเรา
        $auth = auth()->user();
        $emp = [];
        $data = AuthorizationManual::where('auth', '=', $auth->emp_id)->orWhere('auth2', '=', $auth->emp_id)->orderBy('emp_id', 'asc')->get();
        if ($data->isNotEmpty()) {
            foreach ($data as $value) {
                $emp[] = $value->emp_id;
            }
        }
        $data = DB::table('authorization as a')->leftJoin('employee as e', 'a.dept_id', '=', 'e.dept_id')->where('a.auth', '=', $auth->emp_id)->orWhere('a.auth2', '=', $auth->emp_id)->orderBy('a.dept_id', 'asc')->get(['e.emp_id']);
        if ($data->isNotEmpty()) {
            foreach ($data as $value) {
                $emp[] = $value->emp_id;
            }
        }
        $users = DB::table('employee as e')->leftJoin('department as d', 'e.dept_id', '=', 'd.dept_id')->leftJoin('position as p', 'e.position_id', '=', 'p.position_id')
        ->where('e.emp_status', '<>', 0)->where('e.emp_id', '<>', $auth->emp_id)->whereIn('e.emp_id', $emp)
        ->select('e.emp_id', 'e.title', 'e.name', 'e.surname', 'e.nickname', 'e.gender', 'e.image', 'e.position_id', 'e.dept_id', 'e.area_code', 'e.emp_type', 'e.emp_status', 'd.level', 'd.dept_name', 'p.position_name')
        ->orderByRaw('e.dept_id asc, e.position_id=0, e.position_id asc, e.emp_id asc')->get();
        return $users;
    }

    public function getAuthorizeUsersD()
    {
        // เรียกข้อมูลพนักงานที่อยู่ภายใต้สิทธิ์การอนุมัติของเรา (เฉพาะรายวัน)
        $auth = auth()->user();
        $emp = [];
        $data = AuthorizationManual::where('auth', '=', $auth->emp_id)->orWhere('auth2', '=', $auth->emp_id)->orderBy('emp_id', 'asc')->get();
        if ($data->isNotEmpty()) {
            foreach ($data as $value) {
                $emp[] = $value->emp_id;
            }
        }
        $data = DB::table('authorization as a')->leftJoin('employee as e', 'a.dept_id', '=', 'e.dept_id')->where('a.auth', '=', $auth->emp_id)->orWhere('a.auth2', '=', $auth->emp_id)->orderBy('a.dept_id', 'asc')->get(['e.emp_id']);
        if ($data->isNotEmpty()) {
            foreach ($data as $value) {
                $emp[] = $value->emp_id;
            }
        }
        $users = DB::table('employee as e')->leftJoin('department as d', 'e.dept_id', '=', 'd.dept_id')->leftJoin('position as p', 'e.position_id', '=', 'p.position_id')
        ->where('e.emp_status', '<>', 0)->where('e.emp_type', '=', 'D')->where('e.emp_id', '<>', $auth->emp_id)->whereIn('e.emp_id', $emp)
        ->select('e.emp_id', 'e.title', 'e.name', 'e.surname', 'e.nickname', 'e.gender', 'e.image', 'e.position_id', 'e.dept_id', 'e.area_code', 'e.emp_type', 'e.emp_status', 'd.level', 'd.dept_name', 'p.position_name')
        ->orderByRaw('e.dept_id asc, e.position_id=0, e.position_id asc, e.emp_id asc')->get();
        return $users;
    }

    public function getAuthorizeLevelUsers()
    {
        // เรียกข้อมูลพนักงานที่อยู่ภายใต้สิทธิ์การอนุมัติและระดับต่ำลงมา
        $auth = auth()->user();
        // #################### เฉพาะ พี่เล็ก, พี่เหม๋ย ####################
        $isUser = false;
        if ($auth->emp_id == "400010" || $auth->emp_id == "500089") {
            $isUser = true; // (ยกเว้น รหัสพนักงานและรหัสหน่วยงานของตัวเอง)
        }
        // ########################## END ##########################
        $dept = [];
        $data = DB::table('authorization_manual as a')->leftJoin('employee as e', 'a.emp_id', '=', 'e.emp_id')->leftJoin('department as d', 'e.dept_id', '=', 'd.dept_id')
        ->where(function ($query) use ($auth) {
            $query->where('a.auth', '=', $auth->emp_id)->orWhere('a.auth2', '=', $auth->emp_id);
        })->where('a.emp_id', '<>', $auth->emp_id)->orderBy('d.dept_id', 'asc')->get(['a.*', 'd.dept_id', 'd.level']);
        if ($data->isNotEmpty()) {
            foreach ($data as $value) {
                $dept[] = [
                    "dept_id" => $value->dept_id,
                    "level" => $value->level,
                ];
            }
        }
        $data = DB::table('authorization as a')->leftJoin('department as d', 'a.dept_id', '=', 'd.dept_id')
        ->where(function ($query) use ($auth) {
            $query->where('a.auth', '=', $auth->emp_id)->orWhere('a.auth2', '=', $auth->emp_id);
        });
        if ($isUser) {
            $data = $data->where('a.dept_id', '<>', $auth->dept_id);
        }
        $data = $data->orderBy('a.dept_id', 'asc')->get(['a.*', 'd.level']);
        if ($data->isNotEmpty()) {
            foreach ($data as $value) {
                $dept[] = [
                    "dept_id" => $value->dept_id,
                    "level" => $value->level,
                ];
            }
        }
        $dept =  array_map("unserialize", array_unique(array_map("serialize", $dept)));
        $users = DB::table('employee as e')->leftJoin('department as d', 'e.dept_id', '=', 'd.dept_id')->leftJoin('position as p', 'e.position_id', '=', 'p.position_id')
        ->where('e.emp_status', '<>', 0)->where('e.emp_id', '<>', $auth->emp_id)
        ->where(function ($query) use ($dept) {
            if (count($dept) > 0) {
                foreach ($dept as $d) {
                    if ($d["level"] == 1) {
                        $sub = substr($d["dept_id"], 0, 3) . '%';
                    } else if ($d["level"] == 2) {
                        $sub = substr($d["dept_id"], 0, 5) . '%';
                    } else if ($d["level"] == 3) {
                        $sub = substr($d["dept_id"], 0, 7) . '%';
                    } else if ($d["level"] == 4) {
                        $sub = $d["dept_id"] . '%';
                    }
                    $query->orWhere('e.dept_id', 'like', $sub);
                }
            } else {
                $query->where('e.emp_id', '=', 0);
            }
        })->select('e.emp_id', 'e.title', 'e.name', 'e.surname', 'e.nickname', 'e.gender', 'e.image', 'e.position_id', 'e.dept_id', 'e.area_code', 'e.emp_type', 'e.emp_status', 'd.level', 'd.dept_name', 'p.position_name')
        ->orderByRaw('e.dept_id asc, e.position_id=0, e.position_id asc, e.emp_id asc')->get();
        return $users;
    }

    public function chkSelfApproveLeader()
    {
        // เช็คว่าเรามีสิทธิ์อนุมัติของตัวเองหรือไม่ สิทธิ์หัวหน้า
        $auth = auth()->user();
        $data = AuthorizationManual::where('emp_id', '=', $auth->emp_id)->where('auth', '=', $auth->emp_id)->get();
        if ($data->isNotEmpty()) {
            return true;
        } else {
            $data1 = AuthorizationManual::where('emp_id', '=', $auth->emp_id)->where('auth', '<>', $auth->emp_id)->get();
            $data2 = Authorization::where('dept_id', '=', $auth->dept_id)->where('auth', '=', $auth->emp_id)->get();
            if ($data1->isEmpty() && $data2->isNotEmpty()) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function chkSelfApproveManager()
    {
        // เช็คว่าเรามีสิทธิ์อนุมัติของตัวเองหรือไม่ สิทธิ์ผู้จัดการ
        $auth = auth()->user();
        $data = AuthorizationManual::where('emp_id', '=', $auth->emp_id)->where('auth2', '=', $auth->emp_id)->get();
        if ($data->isNotEmpty()) {
            return true;
        } else {
            $data1 = AuthorizationManual::where('emp_id', '=', $auth->emp_id)->where('auth2', '<>', $auth->emp_id)->get();
            $data2 = Authorization::where('dept_id', '=', $auth->dept_id)->where('auth2', '=', $auth->emp_id)->get();
            if ($data1->isEmpty() && $data2->isNotEmpty()) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function chkEmpApprove($emp_id, $dept_id, $leave_status)
    {
        // เช็คข้อมูลใบลาของพนักงานคนนี้ว่าเรามีสิทธิ์อนุมัติหรือไม่
        $auth = auth()->user();
        $data = AuthorizationManual::where('emp_id', '=', $emp_id);
        if ($leave_status == 'P') {
            $data = $data->whereRaw('((auth = ' . $auth->emp_id . ' and auth2 = ' . $auth->emp_id . ') or auth = ' . $auth->emp_id . ')')->get();
        } else if ($leave_status == 'A1') {
            $data = $data->where('auth2', '=', $auth->emp_id)->get();
        }
        if ($data->isNotEmpty()) {
            return true;
        }
        $data = Authorization::where('dept_id', '=', $dept_id);
        if ($leave_status == 'P') {
            $data = $data->whereRaw('((auth = ' . $auth->emp_id . ' and auth2 = ' . $auth->emp_id . ') or auth = ' . $auth->emp_id . ')')->get();
        } else if ($leave_status == 'A1') {
            $data = $data->where('auth2', '=', $auth->emp_id)->get();
        }
        if ($data->isNotEmpty()) {
            return true;
        }
        return false;
    }

    public function chkEmpApproveLeader($emp_id, $dept_id)
    {
        // เช็คว่าเรามีสิทธิ์อนุมัติของพนักงานคนนี้หรือไม่ สิทธิ์หัวหน้า
        $auth = auth()->user();
        $data = AuthorizationManual::where('emp_id', '=', $emp_id)->where('auth', '=', $auth->emp_id)->get();
        if ($data->isNotEmpty()) {
            return true;
        } else {
            $data1 = AuthorizationManual::where('emp_id', '=', $emp_id)->where('auth', '<>', $auth->emp_id)->get();
            $data2 = Authorization::where('dept_id', '=', $dept_id)->where('auth', '=', $auth->emp_id)->get();
            if ($data1->isEmpty() && $data2->isNotEmpty()) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function chkEmpApproveManager($emp_id, $dept_id)
    {
        // เช็คว่าเรามีสิทธิ์อนุมัติของพนักงานคนนี้หรือไม่ สิทธิ์ผู้จัดการ
        $auth = auth()->user();
        $data = AuthorizationManual::where('emp_id', '=', $emp_id)->where('auth2', '=', $auth->emp_id)->get();
        if ($data->isNotEmpty()) {
            return true;
        } else {
            $data1 = AuthorizationManual::where('emp_id', '=', $emp_id)->where('auth2', '<>', $auth->emp_id)->get();
            $data2 = Authorization::where('dept_id', '=', $dept_id)->where('auth2', '=', $auth->emp_id)->get();
            if ($data1->isEmpty() && $data2->isNotEmpty()) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function getEmpApproveLeader($emp_id, $dept_id)
    {
        // เรียกข้อมูลว่าเรามีสิทธิ์อนุมัติพนักงานคนนี้หรือไม่ (สิทธิ์หัวหน้า)
        $auth = auth()->user();
        $data = AuthorizationManual::where('emp_id', '=', $emp_id)->where('auth', '<>', null)->where('auth', '<>', '')->orderBy('updated_at', 'DESC')->get();
        if ($data->isNotEmpty()) {
            foreach ($data as $v) {
                if ($auth->emp_id == $v->auth) {
                    $emp = self::getEmployee($v->auth);
                    return self::callUserNameDetail($emp->emp_id, $emp->dept_id, $emp->name, $emp->surname, $emp->nickname);
                }
            }
            return false;
        } else {
            $data1 = AuthorizationManual::where('emp_id', '=', $emp_id)->where('auth', '<>', $auth->emp_id)->get();
            $data2 = Authorization::where('dept_id', '=', $dept_id)->where('auth', '<>', null)->where('auth', '<>', '')->orderBy('updated_at', 'DESC')->get();
            if ($data1->isEmpty() && $data2->isNotEmpty()) {
                foreach ($data2 as $v) {
                    if ($auth->emp_id == $v->auth) {
                        $emp = self::getEmployee($v->auth);
                        return self::callUserNameDetail($emp->emp_id, $emp->dept_id, $emp->name, $emp->surname, $emp->nickname);
                    }
                }
                return false;
            } else {
                return false;
            }
        }
    }

    public function getEmpApproveManager($emp_id, $dept_id)
    {
        // เรียกข้อมูลว่าเรามีสิทธิ์อนุมัติพนักงานคนนี้หรือไม่ (สิทธิ์ผู้จัดการ)
        $auth = auth()->user();
        $data = AuthorizationManual::where('emp_id', '=', $emp_id)->where('auth2', '<>', null)->where('auth2', '<>', '')->orderBy('updated_at', 'DESC')->get();
        if ($data->isNotEmpty()) {
            foreach ($data as $v) {
                if ($auth->emp_id == $v->auth2) {
                    $emp = self::getEmployee($v->auth2);
                    return self::callUserNameDetail($emp->emp_id, $emp->dept_id, $emp->name, $emp->surname, $emp->nickname);
                }
            }
            return false;
        } else {
            $data1 = AuthorizationManual::where('emp_id', '=', $emp_id)->where('auth2', '<>', $auth->emp_id)->get();
            $data2 = Authorization::where('dept_id', '=', $dept_id)->where('auth2', '<>', null)->where('auth2', '<>', '')->orderBy('updated_at', 'DESC')->get();
            if ($data1->isEmpty() && $data2->isNotEmpty()) {
                foreach ($data2 as $v) {
                    if ($auth->emp_id == $v->auth2) {
                        $emp = self::getEmployee($v->auth2);
                        return self::callUserNameDetail($emp->emp_id, $emp->dept_id, $emp->name, $emp->surname, $emp->nickname);
                    }
                }
                return false;
            } else {
                return false;
            }
        }
    }

    public function getOurApproveLeader($emp_id, $dept_id)
    {
        // เรียกข้อมูลหัวหน้าที่มีสิทธิ์อนุมัติตัวเรา (ไม่รวมตัวเอง)
        $auth = auth()->user();
        $data = AuthorizationManual::where('emp_id', '=', $emp_id)->where('auth', '<>', $auth->emp_id)->where('auth', '<>', null)->where('auth', '<>', '')->orderBy('updated_at', 'DESC')->get();
        if ($data->isEmpty()) {
            $data = Authorization::where('dept_id', '=', $dept_id)->where('auth', '<>', $auth->emp_id)->where('auth', '<>', null)->where('auth', '<>', '')->orderBy('updated_at', 'DESC')->get();
            if ($data->isEmpty()) {
                return false;
            }
        }
        $us = array();
        foreach ($data as $v) {
            $emp = self::getEmployee($v->auth);
            $us[] = self::callUserNameDetail($emp->emp_id, $emp->dept_id, $emp->name, $emp->surname, $emp->nickname);
        }
        return $us;
    }

    public function getOurApproveManager($emp_id, $dept_id)
    {
        // เรียกข้อมูลผู้จัดการที่มีสิทธิ์อนุมัติตัวเรา (ไม่รวมตัวเอง)
        $auth = auth()->user();
        $data = AuthorizationManual::where('emp_id', '=', $emp_id)->where('auth2', '<>', $auth->emp_id)->where('auth2', '<>', null)->where('auth2', '<>', '')->orderBy('updated_at', 'DESC')->get();
        if ($data->isEmpty()) {
            $data = Authorization::where('dept_id', '=', $dept_id)->where('auth2', '<>', $auth->emp_id)->where('auth2', '<>', null)->where('auth2', '<>', '')->orderBy('updated_at', 'DESC')->get();
            if ($data->isEmpty()) {
                return false;
            }
        }
        $us = array();
        foreach ($data as $v) {
            $emp = self::getEmployee($v->auth2);
            $us[] = self::callUserNameDetail($emp->emp_id, $emp->dept_id, $emp->name, $emp->surname, $emp->nickname);
        }
        return $us;
    }

    public function getOurApproveLeaderSelf($emp_id, $dept_id)
    {
        // เรียกข้อมูลหัวหน้าที่มีสิทธิ์อนุมัติตัวเรา (รวมตัวเอง)
        $data = AuthorizationManual::where('emp_id', '=', $emp_id)->where('auth', '<>', null)->where('auth', '<>', '')->orderBy('updated_at', 'DESC')->get();
        if ($data->isEmpty()) {
            $data = Authorization::where('dept_id', '=', $dept_id)->where('auth', '<>', null)->where('auth', '<>', '')->orderBy('updated_at', 'DESC')->get();
            if ($data->isEmpty()) {
                return false;
            }
        }
        $us = array();
        foreach ($data as $v) {
            $emp = self::getEmployee($v->auth);
            $us[] = self::callUserNameDetail($emp->emp_id, $emp->dept_id, $emp->name, $emp->surname, $emp->nickname);
        }
        return $us;
    }

    public function getOurApproveManagerSelf($emp_id, $dept_id)
    {
        // เรียกข้อมูลผู้จัดการที่มีสิทธิ์อนุมัติตัวเรา (รวมตัวเอง)
        $data = AuthorizationManual::where('emp_id', '=', $emp_id)->where('auth2', '<>', null)->where('auth2', '<>', '')->orderBy('updated_at', 'DESC')->get();
        if ($data->isEmpty()) {
            $data = Authorization::where('dept_id', '=', $dept_id)->where('auth2', '<>', null)->where('auth2', '<>', '')->orderBy('updated_at', 'DESC')->get();
            if ($data->isEmpty()) {
                return false;
            }
        }
        $us = array();
        foreach ($data as $v) {
            $emp = self::getEmployee($v->auth2);
            $us[] = self::callUserNameDetail($emp->emp_id, $emp->dept_id, $emp->name, $emp->surname, $emp->nickname);
        }
        return $us;
    }

    public function chkEmpApproveRecordWorking($emp_id, $dept_id, $approve_status)
    {
        // เช็คข้อมูลบันทึกวันทำงานของพนักงานคนนี้ว่าเรามีสิทธิ์อนุมัติหรือไม่
        $auth = auth()->user();
        $data = AuthorizationManual::where('emp_id', '=', $emp_id);
        if ($approve_status == 'P') {
            $data = $data->whereRaw('((auth = '.$auth->emp_id.' and auth2 = '.$auth->emp_id.') or auth = '.$auth->emp_id.')')->get();
        } else if ($approve_status == 'A1') {
            $data = $data->where('auth2', '=', $auth->emp_id)->get();
        }
        if ($data->isNotEmpty()) {
            return true;
        }
        $data = Authorization::where('dept_id', '=', $dept_id);
        if ($approve_status == 'P') {
            $data = $data->whereRaw('((auth = ' . $auth->emp_id . ' and auth2 = ' . $auth->emp_id . ') or auth = ' . $auth->emp_id . ')')->get();
        } else if ($approve_status == 'A1') {
            $data = $data->where('auth2', '=', $auth->emp_id)->get();
        }
        if ($data->isNotEmpty()) {
            return true;
        }
        return false;
    }

    public function getApproveLeaderDetail($leave_id)
    {
        // เรียกข้อมูลหัวหน้าที่อนุมัติใบลา
        $result = array();
        $data = DB::table('leave as l')->leftJoin('employee as e', 'l.approve_lid', '=', 'e.emp_id')->where('l.leave_id', '=', $leave_id)
        ->whereIn('l.leave_status', ['A1', 'A2', 'S', 'C1', 'C2', 'C3'])
        ->select('l.*', 'e.title', 'e.name', 'e.surname', 'e.nickname')->first();
        if ($data) {
            if ($data->approve_lid != "") {
                $date = ThaiDateHelperService::shortDateFormat($data->approve_ldate);
                $result = self::callUserNameDetail2($data->approve_lid, $data->title, $data->name, $data->surname, $data->nickname, $date);
            }
        }
        return $result;
    }

    public function getApproveManagerDetail($leave_id)
    {
        // เรียกข้อมูลผู้จัดการที่อนุมัติใบลา
        $result = array();
        $data = DB::table('leave as l')->leftJoin('employee as e', 'l.approve_mid', '=', 'e.emp_id')->where('l.leave_id', '=', $leave_id)
        ->whereIn('l.leave_status', ['A2', 'S', 'C1', 'C2', 'C3'])
        ->select('l.*', 'e.title', 'e.name', 'e.surname', 'e.nickname')->first();
        if ($data) {
            if ($data->approve_mid != "") {
                $date = ThaiDateHelperService::shortDateFormat($data->approve_mdate);
                $result = self::callUserNameDetail2($data->approve_mid, $data->title, $data->name, $data->surname, $data->nickname, $date);
            }
        }
        return $result;
    }

    public function getApproveHRDetail($leave_id)
    {
        // เรียกข้อมูลบุคคลที่อนุมัติใบลา
        $result = array();
        $data = DB::table('leave as l')->leftJoin('employee as e', 'l.approve_hrid', '=', 'e.emp_id')->where('l.leave_id', '=', $leave_id)
        ->whereIn('l.leave_status', ['S', 'C1', 'C2', 'C3'])
        ->select('l.*', 'e.title', 'e.name', 'e.surname', 'e.nickname')->first();
        if ($data) {
            if ($data->approve_hrid != "") {
                $date = ThaiDateHelperService::shortDateFormat($data->approve_hrdate);
                $result = self::callUserNameDetail2($data->approve_hrid, $data->title, $data->name, $data->surname, $data->nickname, $date);
            }
        }
        return $result;
    }

    public function getLeaveLeaderDetail($leave_id)
    {
        // เรียกข้อมูลผู้บันทึกใบลา
        $result = array();
        $data = DB::table('leave as l')->leftJoin('employee as e', 'l.leader_id', '=', 'e.emp_id')->where('l.leave_id', '=', $leave_id)
        ->whereIn('l.leave_status', ['E', 'P', 'A1', 'A2', 'S', 'C1', 'C2', 'C3'])
        ->select('l.*', 'e.title', 'e.name', 'e.surname', 'e.nickname')->first();
        if ($data) {
            if ($data->leader_id != "") {
                $date = ThaiDateHelperService::shortDateFormat($data->created_at);
                $result = self::callUserNameDetail2($data->leader_id, $data->title, $data->name, $data->surname, $data->nickname, $date);
            }
        }
        return $result;
    }

    public function getLeaveEmpDetail($leave_id)
    {
        // เรียกข้อมูลผู้ที่ลา
        $result = array();
        $data = DB::table('leave as l')->leftJoin('employee as e', 'l.emp_id', '=', 'e.emp_id')->where('l.leave_id', '=', $leave_id)
        ->whereIn('l.leave_status', ['E', 'P', 'A1', 'A2', 'S', 'C1', 'C2', 'C3'])
        ->select('l.*', 'e.title', 'e.name', 'e.surname', 'e.nickname')->first();
        if ($data) {
            if ($data->emp_id != "") {
                $date = ThaiDateHelperService::shortDateFormat($data->created_at);
                $result = self::callUserNameDetail2($data->emp_id, $data->title, $data->name, $data->surname, $data->nickname, $date);
            }
        }
        return $result;
    }

    public function getApproveRecordWorkingLeaderDetail($id)
    {
        // เรียกข้อมูลหัวหน้าที่อนุมัติบันทึกวันทำงาน
        $result = array();
        $data = DB::table('record_working as l')->leftJoin('employee as e', 'l.approve_lid', '=', 'e.emp_id')->where('l.id', '=', $id)
        ->whereIn('l.approve_status', ['A1', 'A2', 'S', 'C1', 'C2', 'C3'])
        ->select('l.*', 'e.title', 'e.name', 'e.surname', 'e.nickname')->first();
        if ($data) {
            if ($data->approve_lid != "") {
                $date = ThaiDateHelperService::shortDateFormat($data->approve_ldate);
                $result = self::callUserNameDetail2($data->approve_lid, $data->title, $data->name, $data->surname, $data->nickname, $date);
            }
        }
        return $result;
    }

    public function getApproveRecordWorkingManagerDetail($id)
    {
        // เรียกข้อมูลผู้จัดการที่อนุมัติบันทึกวันทำงาน
        $result = array();
        $data = DB::table('record_working as l')->leftJoin('employee as e', 'l.approve_mid', '=', 'e.emp_id')->where('l.id', '=', $id)
        ->whereIn('l.approve_status', ['A2', 'S', 'C1', 'C2', 'C3'])
        ->select('l.*', 'e.title', 'e.name', 'e.surname', 'e.nickname')->first();
        if ($data) {
            if ($data->approve_mid != "") {
                $date = ThaiDateHelperService::shortDateFormat($data->approve_mdate);
                $result = self::callUserNameDetail2($data->approve_mid, $data->title, $data->name, $data->surname, $data->nickname, $date);
            }
        }
        return $result;
    }

    public function getApproveRecordWorkingHRDetail($id)
    {
        // เรียกข้อมูลบุคคลที่อนุมัติบันทึกวันทำงาน
        $result = array();
        $data = DB::table('record_working as l')->leftJoin('employee as e', 'l.approve_hrid', '=', 'e.emp_id')->where('l.id', '=', $id)
        ->whereIn('l.approve_status', ['S', 'C1', 'C2', 'C3'])
        ->select('l.*', 'e.title', 'e.name', 'e.surname', 'e.nickname')->first();
        if ($data) {
            if ($data->approve_hrid != "") {
                $date = ThaiDateHelperService::shortDateFormat($data->approve_hrdate);
                $result = self::callUserNameDetail2($data->approve_hrid, $data->title, $data->name, $data->surname, $data->nickname, $date);
            }
        }
        return $result;
    }

    public function getRecordWorkingLeaderDetail($id)
    {
        // เรียกข้อมูลผู้บันทึกวันทำงานแทน
        $result = array();
        $data = DB::table('record_working as l')->leftJoin('employee as e', 'l.emp_id', '=', 'e.emp_id')->where('l.id', '=', $id)
        ->whereIn('l.approve_status', ['E', 'P', 'A1', 'A2', 'S', 'C1', 'C2', 'C3'])
        ->select('l.*', 'e.title', 'e.name', 'e.surname', 'e.nickname')->first();
        if ($data) {
            if ($data->leader_id != "") {
                $date = ThaiDateHelperService::shortDateFormat($data->created_at);
                $result = self::callUserNameDetail2($data->leader_id, $data->title, $data->name, $data->surname, $data->nickname, $date);
            }
        }
        return $result;
    }

    public function getRecordWorkingEmpDetail($id)
    {
        // เรียกข้อมูลผู้บันทึกวันทำงาน
        $result = array();
        $data = DB::table('record_working as l')->leftJoin('employee as e', 'l.emp_id', '=', 'e.emp_id')->where('l.id', '=', $id)
        ->whereIn('l.approve_status', ['E', 'P', 'A1', 'A2', 'S', 'C1', 'C2', 'C3'])
        ->select('l.*', 'e.title', 'e.name', 'e.surname', 'e.nickname')->first();
        if ($data) {
            if ($data->emp_id != "") {
                $date = ThaiDateHelperService::shortDateFormat($data->created_at);
                $result = self::callUserNameDetail2($data->emp_id, $data->title, $data->name, $data->surname, $data->nickname, $date);
            }
        }
        return $result;
    }

    public function getPeriodLeave($start, $end)
    {
        // งวดค่าแรงวันที่ลา
        $period = PeriodSalary::where('start', '<=', $start)->where('end', '>=', $start)->first();
        return $period;
    }

    public function getAttendanceLatest()
    {
        // อัปเดตล่าสุด ประวัติการมาทำงาน
        $attendance = AttendanceLog::orderBy('updated_at', 'desc')->take(5)->first();
        if ($attendance) {
            $result = Carbon::parse($attendance->updated_at)->thaidate('D j M Y, เวลา H:i น.');
        }
        return $result;
    }

    public function getEmployee($emp_id)
    {
        // เรียกข้อมูลพนักงาน
        $emp = DB::table('employee as e')->leftJoin('department as d', 'e.dept_id', '=', 'd.dept_id')->where('e.emp_id', '=', $emp_id)->select('e.emp_id', 'e.title', 'e.name', 'e.surname', 'e.nickname', 'e.gender', 'e.image', 'e.position_id', 'e.dept_id', 'e.area_code', 'e.emp_type', 'e.emp_status', 'd.level', 'd.dept_name')->first();
        if ($emp) {
            return $emp;
        }
        return false;
    }

    public function callDepartment($level, $dept_id, $depts)
    {
        $arr = array(
            "level0" => array("id" => "", "name" => ""),
            "level1" => array("id" => "", "name" => ""),
            "level2" => array("id" => "", "name" => ""),
            "level3" => array("id" => "", "name" => ""),
            "level4" => array("id" => "", "name" => "")
        );
        if ($level == 0) {
            $arr["level0"] = array("id" => $dept_id, "name" => self::callDeptName($dept_id, $depts));
        } else if ($level == 1) {
            $dept0 = substr($dept_id, 0, 1) . "00000000";
            $arr["level0"] = array("id" => $dept0, "name" => self::callDeptName($dept0, $depts));
            $arr["level1"] = array("id" => $dept_id, "name" => self::callDeptName($dept_id, $depts));
        } else if ($level == 2) {
            $dept0 = substr($dept_id, 0, 1) . "00000000";
            $dept1 = substr($dept_id, 0, 3) . "000000";
            $arr["level0"] = array("id" => $dept0, "name" => self::callDeptName($dept0, $depts));
            $arr["level1"] = array("id" => $dept1, "name" => self::callDeptName($dept1, $depts));
            $arr["level2"] = array("id" => $dept_id, "name" => self::callDeptName($dept_id, $depts));
        } else if ($level == 3) {
            $dept0 = substr($dept_id, 0, 1) . "00000000";
            $dept1 = substr($dept_id, 0, 3) . "000000";
            $dept2 = substr($dept_id, 0, 5) . "0000";
            $arr["level0"] = array("id" => $dept0, "name" => self::callDeptName($dept0, $depts));
            $arr["level1"] = array("id" => $dept1, "name" => self::callDeptName($dept1, $depts));
            $arr["level2"] = array("id" => $dept2, "name" => self::callDeptName($dept2, $depts));
            $arr["level3"] = array("id" => $dept_id, "name" => self::callDeptName($dept_id, $depts));
        } else if ($level == 4) {
            $dept0 = substr($dept_id, 0, 1) . "00000000";
            $dept1 = substr($dept_id, 0, 3) . "000000";
            $dept2 = substr($dept_id, 0, 5) . "0000";
            $dept3 = substr($dept_id, 0, 7) . "00";
            $arr["level0"] = array("id" => $dept0, "name" => self::callDeptName($dept0, $depts));
            $arr["level1"] = array("id" => $dept1, "name" => self::callDeptName($dept1, $depts));
            $arr["level2"] = array("id" => $dept2, "name" => self::callDeptName($dept2, $depts));
            $arr["level3"] = array("id" => $dept3, "name" => self::callDeptName($dept3, $depts));
            $arr["level4"] = array("id" => $dept_id, "name" => self::callDeptName($dept_id, $depts));
        }
        return $arr;
    }

    public function callDeptName($dept_id, $depts)
    {
        $index = array_search($dept_id, array_column($depts, 'dept_id'));
        $dept_name = ($index !== false) ? $depts[$index]["dept_name"] : "";
        return $dept_name;
    }

    public function callUserName($image, $name, $surname, $nickname)
    {
        $nname = '';
        if ($nickname != "") {
            $nname = ' ('.$nickname.')';
        }
        $result = '<div class="table-user"><img src="'.url('assets/images/users/thumbnail/'.$image).'" onerror="this.onerror=null;this.src=\''.url('assets/images/users/thumbnail/user-1.jpg').'\';" alt="table-user" class="me-2 rounded-circle">' . $name . ' ' . $surname . $nname . '</div>';
        return $result;
    }

    public function callUserNameDetail($emp_id, $dept_id, $name, $surname, $nickname)
    {
        $nname = '';
        if ($nickname != "") {
            $nname = ' ('.$nickname.')';
        }
        $sname = '';
        if ($surname != "") {
            $sname = ' '.$surname;
        }
        return array("emp_id"=>$emp_id, "dept_id"=>$dept_id, "name"=>$name.$sname.$nname);
    }

    public function callUserNameDetail2($emp_id, $title, $name, $surname, $nickname, $date)
    {
        $nname = '';
        if ($nickname != "") {
            $nname = ' ('.$nickname.')';
        }
        $sname = '';
        if ($surname != "") {
            $sname = ' '.$surname;
        }
        return array("emp_id"=>$emp_id, "title"=>$title, "name"=>$name.$sname.$nname, "date"=>$date);
    }

    public function getDepartmentToArray()
    {
        $dept = Department::orderBy('dept_id')->get()->toArray();
        return $dept;
    }

    public function newLeaveId()
    {
        $leave = Leave::orderBy('leave_id', 'DESC')->select('leave_id')->first();
        if ($leave) {
            return $leave->leave_id+1;
        } else {
            return 1;
        }
    }

    public function getLeaveIdLatest()
    {
        $leave = Leave::orderBy('leave_id', 'DESC')->select('leave_id')->first();
        if ($leave) {
            return $leave->leave_id;
        } else {
            return 1;
        }
    }

    public function newRecordWorkingId()
    {
        $leave = RecordWorking::orderBy('id', 'DESC')->select('id')->first();
        if ($leave) {
            return $leave->id+1;
        } else {
            return 1;
        }
    }

    public function getRecordWorkingIdLatest()
    {
        $leave = RecordWorking::orderBy('id', 'DESC')->select('id')->first();
        if ($leave) {
            return $leave->id;
        } else {
            return 1;
        }
    }

    public function getLeaveLog($id)
    {
        $leave = DB::table('leave_log as lg')->leftJoin('employee as e', 'lg.emp_id', '=', 'e.emp_id')->where('lg.leave_id', '=', $id)->orderBy('lg.id', 'ASC')->select('lg.*', 'e.name', 'e.surname', 'e.nickname', 'e.image')->get();
        return $leave;
    }

    public function getRecordWorkingLog($id)
    {
        $leave = DB::table('record_working_log as lg')->leftJoin('employee as e', 'lg.emp_id', '=', 'e.emp_id')->where('lg.rw_id', '=', $id)->orderBy('lg.id', 'ASC')->select('lg.*', 'e.name', 'e.surname', 'e.nickname', 'e.image')->get();
        return $leave;
    }

    public function getRecordWorkingRef($id)
    {
        $leave = DB::table('record_working')->where('leave_id', '=', $id)->select('id', 'work_date', 'remark')->first();
        return $leave;
    }

    public function addLeaveLog($id, $des, $emp_id, $ip)
    {
        $log = new LeaveLog();
        $log->leave_id = $id;
        $log->description = $des;
        $log->emp_id = $emp_id;
        $log->ip_address = $ip;
        $log->save();
    }

    public function addRecordWorkingLog($id, $des, $emp_id, $ip)
    {
        $log = new RecordWorkingLog();
        $log->rw_id = $id;
        $log->description = $des;
        $log->emp_id = $emp_id;
        $log->ip_address = $ip;
        $log->save();
    }

    public function checkUrgentLeavePerMonth($id, $emp_id, $date)
    {
        // ลาเร่งด่วน 2 ครั้งต่อเดือน
        // $ym = substr($date, 0, 7);
        // $y = substr($date, 0, 4);
        // $m = substr($date, 5, 2);
        // $check = Leave::where('leave_type_id', '=', 4)->where('emp_id', '=', $emp_id)->where('leave_id', '<>', $id)->whereRaw('substring(leave_start_date, 1, 7)="'.$ym.'"')
        // ->whereIn('leave_status', ['P','A1','A2','S'])->get();
        // $count = $check->count();
        // if ($count > 2) {
        //     $month = self::get_month($m);
        //     $title = "ลาเร่งด่วนห้ามเกิน 2 ต่อเดือน";
        //     $message = "เดือน ".$month." พ.ศ. ".($y+543)." เคยลาไปแล้ว " . $count . " ครั้ง";
        //     return array("title"=>$title, "message"=>$message);
        // }
        return true;
    }

    public function checkLeaveLimitPerYear($ppt_id, $date, $emp_id, $leave_id, $leave_type, $leave_day, $leave_minute)
    {
        // เช็ควันลาต่อปี
        // 1 วัน = 8 ชม.
        $ltppt = LeaveTypeProperty::where('leave_type_ppt_id', '=', $ppt_id)->where('leave_type_ppt_status', '=', 1)->first();
        if ($ltppt) {
            $day_limit = $ltppt->leave_type_ppt_day;
            $day_total = 0;
            $leave_year = substr($date, 0, 4);
            $check = Leave::whereRaw('substring(leave_start_date, 1, 4) = ' . $leave_year)->where('leave_id', '<>', $leave_id)->where('emp_id', '=', $emp_id)->where('leave_type_id', '=', $leave_type)->whereIn('leave_status', ['P', 'A1', 'A2', 'S'])
            ->select(DB::raw('SUM(leave_day) as sum_day'), DB::raw('SUM(leave_minute) as sum_minute'))->first();
            if ($check) {
                $day_total += ($check->sum_day * 8) * 60;
                $day_total += $check->sum_minute;
            }
            $sum_day_total = ((($leave_day * 8) * 60) + $leave_minute) + $day_total; // รวมที่เคยลาไปแล้วกับใบใหม่
            if ($sum_day_total > (($day_limit * 8) * 60)) { // เวลารวม(sec) > เวลาต่อปีที่ห้ามเกิน(sec)
                $title = "";
                $message = "";
                $sec2 = self::leaveSecondsToTime($day_total * 60); // ที่เคยลาไปแล้ว
                if ($sec2['d'] > 0 || $sec2['h'] > 0 || $sec2['m'] > 0) {
                    $leave_total = self::hoursandmins(($sec2['h'] * 60) + $sec2['m'], '%02d ชม., %02d น.');
                    $message = ($sec2['d'] > 0) ? $sec2['d'] . ' วัน, ' . $leave_total : $leave_total;
                }
                if ($leave_type == "1") {
                    $title = 'ลากิจห้ามเกิน ' . $day_limit . ' วันต่อปี!';
                    $message = 'เคยลาไปแล้ว ' . $message;
                } else if ($leave_type == "2" || $leave_type == "3") {
                    $title = 'ลาป่วยห้ามเกิน ' . $day_limit . ' วันต่อปี!';
                    $message = 'เคยลาไปแล้ว ' . $message;
                } else if ($leave_type == "7") {
                    $title = 'ลาพักร้อนห้ามเกิน ' . $day_limit . ' วันต่อปี!';
                    $message = 'เคยลาไปแล้ว ' . $message;
                }
                return array("title"=>$title, "message"=>$message);
            }
        }
        return true;
    }

    // <!-- ========== ลางาน ========== -->

    public function leavePushNotification($id, $emp_id, $dept_id, $username)
    {
        $leave = Leave::where('leave_id', '=', $id)->where('emp_id', '=', $emp_id)->whereIn('leave_status', ['P', 'A1', 'A2'])->orderBy('leave_id', 'DESC')->first();
        if ($leave->leave_status == "P" || $leave->leave_status == "A1") {
            $to_uid = [];
            if ($leave->leave_status == "P") {
                $approveL = self::getOurApproveLeader($emp_id, $dept_id);
                if ($approveL !== false) {
                    foreach ($approveL as $a) {
                        $to_uid[] = $a["emp_id"];
                    }
                }
            } else if ($leave->leave_status == "A1") {
                $approveM = self::getOurApproveManager($emp_id, $dept_id);
                if ($approveM !== false) {
                    foreach ($approveM as $a) {
                        $to_uid[] = $a["emp_id"];
                    }
                }
            }
            $to_uid = array_unique($to_uid);
            if (count($to_uid)) {
                $notiController = new NotificationController;
                for ($i=0; $i<count($to_uid); $i++) {
                    if ($emp_id != $to_uid[$i]) {
                        $parameters = [
                            "app_id" => 2,
                            "title" => "ลางานออนไลน์",
                            "description" => "มีใบลางานรออนุมัติ จาก:" . $username,
                            "url" => "/leave/approve/emp-leave-approve/" . $leave->leave_id,
                            "job_id" => $leave->leave_id,
                            "from_uid" => $emp_id,
                            "to_uid" => $to_uid[$i],
                            "type" => "01",
                            "status" => 1
                        ];
                        $notiController->push_notification($parameters);
                    }
                }
            }
        }
    }

    public function leaveCancelPushNotification($id)
    {
        $auth = auth()->user();
        $leave = Leave::where('leave_id', '=', $id)->first();
        if ($leave->leave_status == "C1") {
            $des = " (หัวหน้า)";
        } else if ($leave->leave_status == "C2") {
            $des = " (ผู้จัดการ)";
        } else if ($leave->leave_status == "C3") {
            $des = " (บุคคล)";
        }
        $notiController = new NotificationController;
        $parameters = [
            "app_id" => 2,
            "title" => "ลางานออนไลน์",
            "description" => "ใบลางานถูกยกเลิก โดย: " . $auth->name . $des,
            "url" => "/leave/document/" . $leave->leave_id,
            "job_id" => $leave->leave_id,
            "from_uid" => $auth->emp_id,
            "to_uid" => $leave->emp_id,
            "type" => "02",
            "status" => 1
        ];
        $notiController->push_notification($parameters);
    }

    public function leaveUpdateNotification($id)
    {
        $notiController = new NotificationController;
        if ($notiController->check_notification_by_type(2, $id, "01")) {
            $notiController->update_notification_by_type(2, $id, "01");
        }
    }

    public function leaveRemoveNotification($id)
    {
        $notiController = new NotificationController;
        if ($notiController->check_notification_by_type(2, $id, "01")) {
            $notiController->remove_notification_by_type(2, $id, "01");
        }
        if ($notiController->check_notification_by_type(2, $id, "02")) {
            $notiController->remove_notification_by_type(2, $id, "02");
        }
    }

    // <!-- ========== end ========== -->

    // <!-- ========== บันทึกวันทำงานพิเศษ ========== -->

    public function recordWorkingPushNotification($id, $emp_id, $dept_id, $username)
    {
        $leave = RecordWorking::where('id', '=', $id)->where('emp_id', '=', $emp_id)->whereIn('approve_status', ['P', 'A1', 'A2'])->orderBy('id', 'DESC')->first();
        if ($leave->approve_status == "P" || $leave->approve_status == "A1") {
            $to_uid = [];
            if ($leave->approve_status == "P") {
                $approveL = self::getOurApproveLeader($emp_id, $dept_id);
                if ($approveL !== false) {
                    foreach ($approveL as $a) {
                        $to_uid[] = $a["emp_id"];
                    }
                }
            } else if ($leave->approve_status == "A1") {
                $approveM = self::getOurApproveManager($emp_id, $dept_id);
                if ($approveM !== false) {
                    foreach ($approveM as $a) {
                        $to_uid[] = $a["emp_id"];
                    }
                }
            }
            $to_uid = array_unique($to_uid);
            if (count($to_uid)) {
                $notiController = new NotificationController;
                for ($i=0; $i<count($to_uid); $i++) {
                    if ($emp_id != $to_uid[$i]) {
                        $parameters = [
                            "app_id" => 2,
                            "title" => "ลางานออนไลน์",
                            "description" => "มีบันทึกวันทำงานพิเศษรออนุมัติ จาก:" . $username,
                            "url" => "/leave/approve/emp-record-working-approve/" . $leave->id,
                            "job_id" => $leave->id,
                            "from_uid" => $emp_id,
                            "to_uid" => $to_uid[$i],
                            "type" => "03",
                            "status" => 1
                        ];
                        $notiController->push_notification($parameters);
                    }
                }
            }
        }
    }

    public function recordWorkingCancelPushNotification($id)
    {
        $auth = auth()->user();
        $leave = RecordWorking::where('id', '=', $id)->first();
        if ($leave->approve_status == "C1") {
            $des = " (หัวหน้า)";
        } else if ($leave->approve_status == "C2") {
            $des = " (ผู้จัดการ)";
        } else if ($leave->approve_status == "C3") {
            $des = " (บุคคล)";
        }
        $notiController = new NotificationController;
        $parameters = [
            "app_id" => 2,
            "title" => "ลางานออนไลน์",
            "description" => "ใบบันทึกวันทำงานถูกยกเลิก โดย: " . $auth->name . $des,
            "url" => "/leave/document-record-working/" . $leave->id,
            "job_id" => $leave->id,
            "from_uid" => $auth->emp_id,
            "to_uid" => $leave->emp_id,
            "type" => "04",
            "status" => 1
        ];
        $notiController->push_notification($parameters);
    }

    public function recordWorkingUpdateNotification($id)
    {
        $notiController = new NotificationController;
        if ($notiController->check_notification_by_type(2, $id, "03")) {
            $notiController->update_notification_by_type(2, $id, "03");
        }
    }

    public function recordWorkingRemoveNotification($id)
    {
        $notiController = new NotificationController;
        if ($notiController->check_notification_by_type(2, $id, "03")) {
            $notiController->remove_notification_by_type(2, $id, "03");
        }
        if ($notiController->check_notification_by_type(2, $id, "04")) {
            $notiController->remove_notification_by_type(2, $id, "04");
        }
    }

    // <!-- ========== end ========== -->
}