<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Event;
use Illuminate\Support\Facades\Session;
use Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected $userActive = true;
    protected $timeactive = 0;

    public function home(Request $request)
    {
        $depts = self::getDepartmentToArray();
        $emp = Employee::leftjoin('department', 'department.dept_id', 'employee.dept_id')->leftjoin('position', 'position.position_id', 'employee.position_id')->where('employee.emp_status', '<>', 0)
        ->where(function ($query) {
            $query->orWhere('employee.tel', '<>', '');
            $query->orWhere('employee.tel2', '<>', '');
            $query->orWhere('employee.phone', '<>', '');
            $query->orWhere('employee.phone2', '<>', '');
            $query->orWhere('employee.email', '<>', '');
        })->select('employee.emp_id', 'employee.name', 'employee.surname', 'employee.nickname', 'employee.email', 'employee.tel', 'employee.tel2', 'employee.phone', 'employee.phone2', 'employee.email', 'employee.detail', 'employee.area_code', 'employee.position_id', 'position.position_name', 'employee.dept_id', 'department.level', 'department.dept_name', 'department.dept_name_en')->orderBy('employee.name', 'ASC')->get();
        $info = Event::where('holiday', '=', '0')->where('status', '=', '1')->where('info', '=', '1')->where('start', '<=', date('Y-m-d'))->where('end', '>=', date('Y-m-d'))->orderBy("start", "DESC")->take(3)->get();
        $view = '';
        if (Auth::check() || Auth::user()) {
            if (Auth::user()->roleAdmin()) {
                $view = 'admin.dashboard';
            } else {
                $view = 'dashboard';
            }
            if ($request->noaccess==1) {
                alert()->warning('คุณไม่มีสิทธิเข้าใช้งานระบบนี้');
                return redirect('/home');
            }
            self::userActive();
        } else {
            $view = 'dashboard-public';
        }
        return view($view, compact('emp', 'depts', 'info'));
    }

    protected function userActive()
    {
        $this->timeactive = (time() - strtotime(Auth::user()->last_active));
        if ($this->timeactive > $this->getTimeOut()) {
            $this->userActive = false;
        }
        if ($this->userActive === false) {
            Auth::logout();
            Session::flush();
            return redirect('/');
        }
    }

    protected function getTimeOut()
    {
        return env('SESSION_LIFETIME', 120) * 60; // Seconds
    }

    public static function callDepartment($rec, $depts)
    {
        if (substr($rec->emp_id, 0, 2) == "98") {
            $result = $rec->detail;
        } else {
            $result = "";
        }
        if ($rec->level == 0) {
            $result .= self::callDeptName($rec->dept_id, $depts);
        } else if ($rec->level == 1) {
            $result .= self::callDeptName($rec->dept_id, $depts);
        } else if ($rec->level == 2) {
            $dept1 = substr($rec->dept_id, 0, 3) . "000000";
            $result .= self::callDeptName($dept1, $depts);
            $result .= self::callDeptName($rec->dept_id, $depts);
        } else if ($rec->level == 3) {
            $dept2 = substr($rec->dept_id, 0, 5) . "0000";
            $result .= self::callDeptName($dept2, $depts);
            $result .= "/".self::callDeptName($rec->dept_id, $depts);
        } else if ($rec->level == 4) {
            $dept2 = substr($rec->dept_id, 0, 5) . "0000";
            $dept3 = substr($rec->dept_id, 0, 7) . "00";
            $result .= self::callDeptName($dept2, $depts);
            $result .= "/".self::callDeptName($dept3, $depts);
            $result .= "/".self::callDeptName($rec->dept_id, $depts);
        }
        if ($rec->area_code != "") {
            $result .= '<small class="text-pink"><i>('.$rec->area_code.')</i></small>';
        }
        if ($rec->position_name != "") {
            $result .= " (".$rec->position_name.")";
        }
        return $result;
    }

    public static function callDeptName($dept_id, $depts)
    {
        $index = array_search($dept_id, array_column($depts, 'dept_id'));
        $dept_name = ($index !== false) ? $depts[$index]["dept_name"] : "";
        return $dept_name;
    }

    public function getDepartmentToArray()
    {
        $dept = Department::orderBy('dept_id')->get()->toArray();
        return $dept;
    }

    public function getHolidays(Request $request)
    {
        if ($request->ajax()) {
            $holiday = Event::where('holiday', '=', '1')->where('status', '=', '1')->whereRaw('SUBSTRING(start, 1,  4) = '.$request->holiday_year)->orderBy("start", "ASC")->take(100)->get();
            $rows = [];
            foreach ($holiday as $value) {
                $rows[] = array(
                    "sort" => $value->start,
                    "title" => '<a href="'.url('holidays/show', $value->id).'" type="button"><b>'.$value->title.'</b></a>',
                    "start" => Carbon::parse($value->start)->thaidate('l j F Y'),
                    "end" => Carbon::parse($value->end)->thaidate('l j F Y'),
                );
            }
            $response["data"] = $rows;
            return response()->json($response);
        }
    }

    public function getEvents(Request $request)
    {
        if ($request->ajax()) {
            $event = Event::leftjoin('roles', 'events.type', '=', 'roles.id')->where('events.holiday', '=', '0')->where('events.status', '=', '1')->orderBy("events.start", "DESC")->select('events.*', 'roles.role')->take(100)->get();
            $rows = [];
            foreach ($event as $value) {
                $rows[] = array(
                    "sort" => $value->start,
                    "role" => '<span style="color: '.$value->color.'">'.$value->role.'</span>',
                    "date" => Carbon::parse($value->start)->thaidate('l j F Y'),
                    "title" => '<a href="'.url('events/show', $value->id).'" type="button"><b style="white-space: pre-line;">'.$value->title.'</b></a>',
                );
            }
            $response["data"] = $rows;
            return response()->json($response);
        }
    }
}