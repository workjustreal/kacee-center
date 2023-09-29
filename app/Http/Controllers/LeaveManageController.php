<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\PeriodSalary;
use App\Models\SalesArea;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use File;

class LeaveManageController extends LeaveBaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function allusers_search(Request $request)
    {
        if ($request->ajax()) {
            $users = DB::table('employee')->leftjoin('department', 'employee.dept_id', '=', 'department.dept_id')
                ->leftjoin('position', 'employee.position_id', '=', 'position.position_id')
                ->where('employee.emp_id', '<>', '')->where('employee.emp_type', '<>', '')->where('employee.emp_status', '<>', 0);

            $totalRecords = $users->select('count(employee.*) as allcount')->count();
            $records = $users->select('employee.*', 'department.dept_name', 'position.position_name')->orderBy('employee.emp_id', 'asc')->get();
            $rows = [];
            $n = 1;
            foreach ($records as $rec) {
                $nickname = '';
                if ($rec->nickname != "") {
                    $nickname = ' ('.$rec->nickname.')';
                }
                $type = self::get_emp_type($rec->emp_type);
                $emp_type = '<span class="badge ' . $type["badge"] . '">' . $type["name"] . '</span>';
                $status = self::get_emp_status($rec->emp_status);
                $emp_status = '<span class="badge ' . $status["badge"] . '">' . $status["name"] . '</span>';
                $rows[] = array(
                    "emp_id" => $rec->emp_id,
                    "emp_name" => '<div class="table-user"><img src="'.url('assets/images/users/thumbnail/'.$rec->image).'" onerror="this.onerror=null;this.src=\''.url('assets/images/users/thumbnail/user-1.jpg').'\';" alt="table-user" class="me-2 rounded-circle">' . $rec->name . ' ' . $rec->surname . $nickname . '</div>',
                    "emp_dept" => $rec->dept_name,
                    "emp_position" => $rec->position_name,
                    "emp_type" => $emp_type,
                    "emp_status" => $emp_status,
                );
                $n++;
            }

            $response = array(
                "total" => $totalRecords,
                "totalNotFiltered" => $totalRecords,
                "rows" => $rows,
            );
            return response()->json($response);
        }
    }

    public function report_hr(Request $request)
    {
        $leave_type = LeaveType::where('leave_type_status', '=', 1)->orderBy('leave_type_id', 'ASC')->get();
        $period_salary = PeriodSalary::where('year', '=', date('Y'))->get();
        $leave_status = self::get_leave_status();
        return view('leave.manage.report-hr', compact('leave_type', 'period_salary', 'leave_status'));
    }

    public function search_report_hr(Request $request)
    {
        if ($request->ajax()) {
            // $data = DB::table('leave as l')->leftJoin('employee as e', 'l.emp_id', '=', 'e.emp_id')
            //     ->leftJoin('department as d', 'e.dept_id', '=', 'd.dept_id')->leftJoin('leave_type as t', 'l.leave_type_id', '=', 't.leave_type_id')->leftJoin('period_salary as p', 'l.period_salary_id', '=', 'p.id')
            //     ->where(function ($query) use ($request) {
            //         if ($request->year != ""){
            //             $query->whereRaw('substring(l.leave_start_date, 1, 4) = '.$request->year);
            //         }
            //         if ($request->period_salary != "all"){
            //             $query->where('l.period_salary_id', '=', $request->period_salary);
            //         }
            //         if ($request->leave_type != "all"){
            //             $query->where('l.leave_type_id', '=', $request->leave_type);
            //         }
            //         if ($request->emp_type != "all"){
            //             $query->where('l.emp_type', '=', $request->emp_type);
            //         }
            //         if ($request->leave_status != "all"){
            //             $query->where('l.leave_status', '=', $request->leave_status);
            //         }
            //     });

            // $totalRecords = $data->select('count(l.*) as allcount')->count();
            // $records = $data->select('l.*', 'e.image', 'e.name', 'e.surname', 'd.dept_name', 't.leave_type_name', 'p.start as ps_start', 'p.end as ps_end')->orderBy('l.leave_start_date', 'asc')->get();
            $data = DB::table('report_leave_view')
                ->where(function ($query) use ($request) {
                    if ($request->year != ""){
                        $query->whereRaw('substring(leave_start_date, 1, 4) = '.$request->year);
                    }
                    if ($request->period_salary != "all"){
                        $query->where('period_salary_id', '=', $request->period_salary);
                    }
                    if ($request->leave_type != "all"){
                        $query->where('leave_type_id', '=', $request->leave_type);
                    }
                    if ($request->emp_type != "all"){
                        $query->where('emp_type', '=', $request->emp_type);
                    }
                    if ($request->leave_status != "all"){
                        $query->where('leave_status', '=', $request->leave_status);
                    }

                    $leave_start = "";
                    $leave_end = "";
                    if ($request->leave_start_date != '') {
                        $leave_start = Carbon::createFromFormat('d/m/Y', $request->leave_start_date)->format('Y-m-d');
                    }
                    if ($request->leave_end_date != '') {
                        $leave_end = Carbon::createFromFormat('d/m/Y', $request->leave_end_date)->format('Y-m-d');
                    }
                    if ($leave_start != '' && $leave_end != '') {
                        $query->where('leave_start_date', '>=', $leave_start);
                        $query->where('leave_end_date', '<=', $leave_end);
                    } else if ($leave_start != '' && $leave_end == '') {
                        $query->where('leave_start_date', '>=', $leave_start);
                    } else if ($leave_start == '' && $leave_end != '') {
                        $query->where('leave_end_date', '<=', $leave_end);
                    }

                    $record_start = "";
                    $record_end = "";
                    if ($request->record_start_date != '') {
                        $record_start = Carbon::createFromFormat('d/m/Y', $request->record_start_date)->format('Y-m-d');
                    }
                    if ($request->record_end_date != '') {
                        $record_end = Carbon::createFromFormat('d/m/Y', $request->record_end_date)->format('Y-m-d');
                    }
                    if ($record_start != '' && $record_end != '') {
                        $query->whereRaw('SUBSTRING(created_at, 1, 10) >= "' . $record_start . '"');
                        $query->whereRaw('SUBSTRING(created_at, 1, 10) <= "' . $record_end . '"');
                    } else if ($record_start != '' && $record_end == '') {
                        $query->whereRaw('SUBSTRING(created_at, 1, 10) >= "' . $record_start . '"');
                    } else if ($record_start == '' && $record_end != '') {
                        $query->whereRaw('SUBSTRING(created_at, 1, 10) <= "' . $record_end . '"');
                    }
                });
            $totalRecords = $data->select('count(*) as allcount')->count();
            $records = $data->select('*')->orderBy('created_at', 'desc')->get();
            $rows = [];
            $n = 1;
            foreach ($records as $rec) {
                $type = self::get_emp_type($rec->emp_type);
                $emp_type = '<span class="text-' . $type["color"] . '">' . $type["name"] . '</span>';
                if ($rec->leave_start_date == $rec->leave_end_date) {
                    $leave_date = Carbon::parse($rec->leave_start_date)->thaidate('d/m/Y');
                } else {
                    $leave_date = Carbon::parse($rec->leave_start_date)->thaidate('d/m/Y') . ' ถึง ' . Carbon::parse($rec->leave_end_date)->thaidate('d/m/Y');
                }
                $leave_time = '';
                if ($rec->leave_minute > 0) {
                    $leave_time .= Carbon::parse($rec->leave_start_time)->format('H:i') . '-' . Carbon::parse($rec->leave_end_time)->format('H:i') . '<br>';
                }
                $leave_amount = '';
                if ($rec->leave_day > 0) {
                    $leave_amount .= $rec->leave_day . ' วัน';
                }
                if ($rec->leave_minute > 0) {
                    if ($leave_amount != '') $leave_amount .= ', ';
                    $leave_amount .= self::hoursandmins($rec->leave_minute, '%02d ชม., %02d น.');
                }
                $status = self::get_leave_status($rec->leave_status);
                $leave_status = '<span class="badge ' . $status["badge"] . '">' . $status["name"] . '</span>';
                $rows[] = array(
                    "no" => $n,
                    "emp_id" => $rec->emp_id,
                    "name" => '<a href="'.url('leave/document', $rec->leave_id).'" class="text-dark"><i class="mdi mdi-file-document-outline text-primary"></i>' . $rec->name . ' ' . $rec->surname . '</a>',
                    "dept" => $rec->dept_name,
                    "emp_type" => $emp_type,
                    "leave_type" => $rec->leave_type_name,
                    "create_date" => Carbon::parse($rec->created_at)->thaidate('d/m/Y'),
                    "leave_date" => $leave_date,
                    "leave_amount" => $leave_time . $leave_amount,
                    // "period_salary" => ($rec->ps_year + 543) . "/" . str_pad($rec->ps_month, 2, "0", STR_PAD_LEFT) . ", " . substr($rec->ps_start, -2) . " - " . substr($rec->ps_end, -2),
                    "leave_status" => $leave_status,
                    "leave_reason" => $rec->leave_reason,
                    "created_at" => Carbon::parse($rec->created_at)->thaidate('d/m/Y H:i:s'),
                    "updated_at" => Carbon::parse($rec->updated_at)->thaidate('d/m/Y H:i:s'),
                );
                $n++;
            }

            $response = array(
                "total" => $totalRecords,
                "totalNotFiltered" => $totalRecords,
                "rows" => $rows,
            );
            return response()->json($response);
        }
    }

    public function emp_leave_history(Request $request)
    {
        $leave_type = LeaveType::where('leave_type_status', '=', 1)->orderBy('leave_type_id', 'ASC')->get();
        $period_salary = PeriodSalary::where('year', '=', date('Y'))->get();
        $leave_status = self::get_leave_status_all();
        return view('leave.manage.emp-leave-history', compact('leave_type', 'period_salary', 'leave_status'));
    }

    public function emp_leave_history_search(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('report_leave_view')
                ->where(function ($query) use ($request) {
                    if ($request->year != ""){
                        $query->whereRaw('substring(leave_start_date, 1, 4) = '.$request->year);
                    }
                    if ($request->period_salary != "all"){
                        $query->where('period_salary_id', '=', $request->period_salary);
                    }
                    if ($request->leave_type != "all"){
                        $query->where('leave_type_id', '=', $request->leave_type);
                    }
                    if ($request->emp_type != "all"){
                        $query->where('emp_type', '=', $request->emp_type);
                    }
                    if ($request->leave_status != "all"){
                        $query->where('leave_status', '=', $request->leave_status);
                    }

                    if ($request->leave_date != '') {
                        if (str_contains($request->leave_date, 'ถึง')) {
                            $exp = explode(' ถึง ', $request->leave_date);
                            $leave_start = Carbon::createFromFormat('d/m/Y', trim($exp[0]))->format('Y-m-d');
                            $leave_end = Carbon::createFromFormat('d/m/Y', trim($exp[1]))->format('Y-m-d');
                            $query->where(function ($_query) use ($leave_start, $leave_end) {
                                $_query->whereBetween('leave_start_date', [$leave_start, $leave_end])->orWhereBetween('leave_end_date', [$leave_start, $leave_end]);
                            });
                        } else {
                            $leave_date = Carbon::createFromFormat('d/m/Y', $request->leave_date)->format('Y-m-d');
                            $query->whereRaw('"'.$leave_date.'" between leave_start_date and leave_end_date');
                        }
                    }
                    if ($request->record_date != '') {
                        if (str_contains($request->record_date, 'ถึง')) {
                            $exp = explode(' ถึง ', $request->record_date);
                            $record_start = Carbon::createFromFormat('d/m/Y', trim($exp[0]))->format('Y-m-d');
                            $record_end = Carbon::createFromFormat('d/m/Y', trim($exp[1]))->format('Y-m-d');
                            $query->whereRaw('SUBSTRING(created_at, 1, 10) between "' . $record_start . '" and "' . $record_end . '"');
                        } else {
                            $record_date = Carbon::createFromFormat('d/m/Y', $request->record_date)->format('Y-m-d');
                            $query->whereRaw('SUBSTRING(created_at, 1, 10) = "' . $record_date . '"');
                        }
                    }
                });
            $totalRecords = $data->select('count(*) as allcount')->count();
            $records = $data->select('*')->orderBy('created_at', 'desc')->get();
            $rows = [];
            $n = 1;
            foreach ($records as $rec) {
                $nickname = '';
                if ($rec->nickname != "") {
                    $nickname = ' ('.$rec->nickname.')';
                }
                $area_code = '';
                if ($rec->area_code != "") {
                    $area_code = ' ('.$rec->area_code.')';
                }
                $type = self::get_emp_type($rec->emp_type);
                $emp_type = '<span class="text-' . $type["color"] . '">' . $type["name"] . '</span>';
                if ($rec->leave_start_date == $rec->leave_end_date) {
                    $leave_date = Carbon::parse($rec->leave_start_date)->thaidate('d/m/Y');
                } else {
                    $leave_date = Carbon::parse($rec->leave_start_date)->thaidate('d/m/Y') . ' ถึง ' . Carbon::parse($rec->leave_end_date)->thaidate('d/m/Y');
                }
                $leave_time = '';
                if ($rec->leave_minute > 0) {
                    $leave_time .= Carbon::parse($rec->leave_start_time)->format('H:i') . '-' . Carbon::parse($rec->leave_end_time)->format('H:i') . '<br>';
                }
                $leave_amount = '';
                if ($rec->leave_day > 0) {
                    $leave_amount .= $rec->leave_day . ' วัน';
                }
                if ($rec->leave_minute > 0) {
                    if ($leave_amount != '') $leave_amount .= ', ';
                    $leave_amount .= self::hoursandmins($rec->leave_minute, '%02d ชม., %02d น.');
                }
                $status = self::get_leave_status($rec->leave_status);
                $leave_status = '<span class="badge ' . $status["badge"] . '">' . $status["name"] . '</span>';
                $action = '<div class="d-inline-flex flex-row">';
                $action .= '<a class="action-icon" href="'.url('leave/document', $rec->leave_id).'" title="ดู"><i class="mdi mdi-eye"></i></a>';
                if ($rec->leave_status != "C1" && $rec->leave_status != "C2" && $rec->leave_status != "C3") {
                    $action .= '<a class="action-icon" href="'.url('leave/approve-hr/emp-leave-edit', $rec->leave_id).'" title="แก้ไข"><i class="mdi mdi-square-edit-outline"></i></a>';
                    if ($rec->leave_type_id == 6) {
                        $action .= '<a class="action-icon" href="javascript:void(0);" onclick="returnHRLeaveConfirmation(\''.$rec->leave_id.'\')" title="ถอยสถานะ"><i class="mdi mdi-keyboard-return"></i></a>';
                    }
                    $action .= '<a class="action-icon" href="javascript:void(0);" onclick="cancelHRLeaveConfirmation(\''.$rec->leave_id.'\')" title="ยกเลิก"><i class="mdi mdi-cancel"></i></a>';
                }
                $action .= '</div>';
                $rows[] = array(
                    "no" => $n,
                    "emp_id" => $rec->emp_id,
                    "name" => '<a href="'.url('leave/document', $rec->leave_id).'" class="text-dark"><i class="mdi mdi-file-document-outline text-primary"></i>' . $rec->name . ' ' . $rec->surname . $nickname . '</a>',
                    "dept_id" => $rec->dept_id,
                    "dept_name" => $rec->dept_name . $area_code,
                    "emp_type" => $emp_type,
                    "leave_type" => $rec->leave_type_name,
                    "create_date" => Carbon::parse($rec->created_at)->thaidate('d/m/Y'),
                    "leave_date" => $leave_date,
                    "leave_amount" => $leave_time . $leave_amount,
                    "leave_status" => $leave_status,
                    "leave_reason" => $rec->leave_reason,
                    "leave_manage" => $action,
                );
                $n++;
            }

            $response = array(
                "total" => $totalRecords,
                "totalNotFiltered" => $totalRecords,
                "rows" => $rows,
            );
            return response()->json($response);
        }
    }

    public function emp_record_working_history(Request $request)
    {
        $approve_status = self::get_leave_status_all();
        return view('leave.manage.emp-record-working-history', compact('approve_status'));
    }

    public function emp_record_working_history_search(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('record_working as l')->leftJoin('employee as e', 'l.emp_id', '=', 'e.emp_id')->leftJoin('department as d', 'e.dept_id', '=', 'd.dept_id')
                ->leftJoin('leave', function ($join) {
                    $join->on('leave.leave_id', '=', 'l.leave_id')->where('l.leave_id', '<>', null)->where('l.leave_id', '<>', '');
                })
                ->where(function ($query) use ($request) {
                    if ($request->year != ""){
                        $query->whereRaw('substring(l.work_date, 1, 4) = '.$request->year);
                    }
                    if ($request->emp_type != "all"){
                        $query->where('e.emp_type', '=', $request->emp_type);
                    }
                    if ($request->approve_status != "all"){
                        $query->where('l.approve_status', '=', $request->approve_status);
                    }
                    if ($request->use_status != "all"){
                        if ($request->use_status == "1"){
                            $query->where('l.use_status', '=', '2');
                        } else {
                            $query->whereIn('l.use_status', ['0','1']);
                        }
                    }

                    if ($request->work_date != '') {
                        if (str_contains($request->work_date, 'ถึง')) {
                            $exp = explode(' ถึง ', $request->work_date);
                            $work_start = Carbon::createFromFormat('d/m/Y', trim($exp[0]))->format('Y-m-d');
                            $work_end = Carbon::createFromFormat('d/m/Y', trim($exp[1]))->format('Y-m-d');
                            $query->whereBetween('l.work_date', [$work_start, $work_end]);
                        } else {
                            $work_date = Carbon::createFromFormat('d/m/Y', $request->work_date)->format('Y-m-d');
                            $query->whereDate('l.work_date', '=', $work_date);
                        }
                    }
                    if ($request->record_date != '') {
                        if (str_contains($request->record_date, 'ถึง')) {
                            $exp = explode(' ถึง ', $request->record_date);
                            $record_start = Carbon::createFromFormat('d/m/Y', trim($exp[0]))->format('Y-m-d');
                            $record_end = Carbon::createFromFormat('d/m/Y', trim($exp[1]))->format('Y-m-d');
                            $query->whereRaw('SUBSTRING(l.created_at, 1, 10) between "' . $record_start . '" and "' . $record_end . '"');
                        } else {
                            $record_date = Carbon::createFromFormat('d/m/Y', $request->record_date)->format('Y-m-d');
                            $query->whereRaw('SUBSTRING(l.created_at, 1, 10) = "' . $record_date . '"');
                        }
                    }
                });

            $totalRecords = $data->select('count(l.*) as allcount')->count();
            $records = $data->select('l.*', 'leave.leave_start_date', 'e.image', 'e.name', 'e.surname', 'e.nickname', 'e.emp_type', 'e.area_code', 'd.dept_id', 'd.dept_name')->orderBy('l.created_at', 'desc')->get();

            $rows = [];
            $n = 1;
            foreach ($records as $rec) {
                $nickname = '';
                if ($rec->nickname != "") {
                    $nickname = ' ('.$rec->nickname.')';
                }
                $area_code = '';
                if ($rec->area_code != "") {
                    $area_code = ' ('.$rec->area_code.')';
                }
                $type = self::get_emp_type($rec->emp_type);
                $emp_type = '<span class="text-' . $type["color"] . '">' . $type["name"] . '</span>';
                $status = self::get_leave_status($rec->approve_status);
                $approve_status = '<span class="badge ' . $status["badge"] . '">' . $status["name"] . '</span>';
                if ($rec->leave_id != '') {
                    $use_date = Carbon::parse($rec->leave_start_date)->thaidate('d/m/Y');
                } else {
                    $use_date = '';
                }
                $action = '<div class="d-inline-flex flex-row">';
                $action .= '<a class="action-icon" href="'.url('leave/document-record-working', $rec->id).'" title="ดู"><i class="mdi mdi-eye"></i></a>';
                if ($rec->approve_status != "C1" && $rec->approve_status != "C2" && $rec->approve_status != "C3") {
                    $action .= '<a class="action-icon" href="javascript:void(0);" onclick="cancelHRRecordWorkingConfirmation(\''.$rec->id.'\')" title="ยกเลิก"><i class="mdi mdi-cancel"></i></a>';
                }
                $action .= '</div>';
                $rows[] = array(
                    "no" => $n,
                    "emp_id" => $rec->emp_id,
                    "name" => '<a href="'.url('leave/document-record-working', $rec->id).'" class="text-dark"><i class="mdi mdi-file-document-outline text-primary"></i>' . $rec->name . ' ' . $rec->surname . $nickname . '</a>',
                    "dept_id" => $rec->dept_id,
                    "dept_name" => $rec->dept_name . $area_code,
                    "emp_type" => $emp_type,
                    "create_date" => Carbon::parse($rec->created_at)->thaidate('d/m/Y'),
                    "work_date" => Carbon::parse($rec->work_date)->thaidate('d/m/Y'),
                    "use_date" => $use_date,
                    "remark" => $rec->remark,
                    "status" => $approve_status,
                    "manage" => $action,
                );
                $n++;
            }

            $response = array(
                "total" => $totalRecords,
                "totalNotFiltered" => $totalRecords,
                "rows" => $rows,
            );
            return response()->json($response);
        }
    }

    public function emp_attendance()
    {
        $level0 = Department::where('level', '=', 0)->get();
        $level1 = Department::where('level', '=', 1)->get();
        $level2 = Department::where('level', '=', 2)->get();
        $level3 = Department::where('level', '=', 3)->get();
        $level4 = Department::where('level', '=', 4)->get();
        $sales_area = SalesArea::orderBy('area_code', 'ASC')->get();
        return view('leave.manage.emp-attendance', compact('level0', 'level1', 'level2', 'level3', 'level4', 'sales_area'));
    }

    public function emp_attendance_search(Request $request)
    {
        if ($request->ajax()) {
            $depts = self::getDepartmentToArray();
            $data = Employee::leftjoin('department', 'employee.dept_id', '=', 'department.dept_id')
                ->leftjoin('position', 'employee.position_id', '=', 'position.position_id')
                ->where('employee.emp_id', '<>', '')
                ->where(function ($query) use ($request) {
                    if ($request->level4 != "all"){
                        $query->where('employee.dept_id', '=', $request->level4);
                    } else if ($request->level3 != "all"){
                        $query->where('employee.dept_id', 'like', substr($request->level3, 0, 7).'%');
                    } else if ($request->level2 != "all"){
                        $query->where('employee.dept_id', 'like', substr($request->level2, 0, 5).'%');
                    } else if ($request->level1 != "all"){
                        $query->where('employee.dept_id', 'like', substr($request->level1, 0, 3).'%');
                    } else if ($request->level0 != "all"){
                        $query->where('employee.dept_id', 'like', substr($request->level0, 0, 1).'%');
                    }
                    if ($request->area_code != ""){
                        $query->where('employee.area_code', '=', $request->area_code);
                    }
                    if ($request->emp_type != "all"){
                        $query->where('employee.emp_type', '=', $request->emp_type);
                    }
                    if ($request->emp_status != "all"){
                        $query->where('employee.emp_status', '=', $request->emp_status);
                    }
                });

            $totalRecords = $data->select('count(employee.*) as allcount')->count();
            $records = $data->select('employee.*', 'department.level', 'department.dept_name', 'position.position_name')->orderBy('employee.emp_id', 'asc')->get();
            $rows = [];

            $isPer = false;
            if (Auth::User()->manageEmployee()) {
                $isPer = true;
            }
            foreach ($records as $rec) {
                $area_code = '';
                if ($rec->area_code != "") {
                    $area_code = ' <small class="text-pink"><i>('.$rec->area_code.')</i></small>';
                }
                if ($rec->emp_type == "D") {
                    $emp_type = '<span class="badge bg-info">รายวัน</span>';
                } else if ($rec->emp_type == "M") {
                    $emp_type = '<span class="badge bg-primary">รายเดือน</span>';
                } else {
                    $emp_type = '<span class="badge bg-secondary">อื่นๆ</span>';
                }
                if ($rec->emp_status == "1") {
                    $status = '<span class="badge bg-success">ปกติ</span>';
                } else if ($rec->emp_status == "2") {
                    $status = '<span class="badge bg-info">ทดลองงาน</span>';
                } else if ($rec->emp_status == "0") {
                    $status = '<span class="badge bg-danger">ลาออก</span>';
                } else {
                    $status = '<span class="badge bg-secondary">อื่นๆ</span>';
                }
                $action = '';
                if ($isPer) {
                    $action = '<div>
                        <a class="action-icon" href="'.url('leave/manage/emp-attendance-log/id', $rec->emp_id).'" title="ดู"><i class="mdi mdi-eye"></i></a>
                    </div>';
                }
                $dept_arr = self::callDepartment($rec->level, $rec->dept_id, $depts);
                $rows[] = array(
                    "emp_id" => '<b>'.$rec->emp_id.'</b>',
                    "name" => self::callUserName($rec->image, $rec->name, $rec->surname, $rec->nickname),
                    "level1" => $dept_arr["level1"]["name"],
                    "level2" => $dept_arr["level2"]["name"],
                    "level3" => $dept_arr["level3"]["name"],
                    "level4" => $dept_arr["level4"]["name"],
                    "position" => $rec->position_name . $area_code,
                    "emp_type" => $emp_type,
                    "emp_status" => $status,
                    "action" => $action,
                );
            }

            $response = array(
                "total" => $totalRecords,
                "totalNotFiltered" => $totalRecords,
                "rows" => $rows,
            );
            return response()->json($response);
        }
    }

    public function emp_attendance_log($id)
    {
        $result = DB::table('employee as e')->leftJoin('department as d', 'e.dept_id', '=', 'd.dept_id')
            ->where('e.emp_id', '=', $id)->select(['e.*', 'd.dept_name'])->first();
        if (!$result) {
            alert()->warning('ไม่พบรหัสพนักงาน!');
            return back();
        }
        $attendance_latest = self::getAttendanceLatest();
        return view('leave.manage.emp-attendance-log')->with('emp_id', $id)->with('result', $result)->with('attendance_latest', $attendance_latest);
    }

    public function emp_attendance_log_search(Request $request)
    {
        if ($request->ajax()) {
            $current_date = date('Y-m-d');
            $previous_date = date("Y-m-d", strtotime("-1 month", strtotime($current_date)));

            $data = [];
            $i = 0;

            if ($request->search != '') {
                $work = DB::table('attendance_log as w')->leftJoin('employee as e', 'w.emp_id', '=', 'e.emp_id')
                ->whereRaw('substring(w.datetime, 1, 10) between "'.$previous_date.'" and "'.$current_date.'"')
                ->where(function ($query) use ($request) {
                    if ($request->search != '') {
                        $query->where('w.emp_id', 'LIKE', '%' . trim(str_replace(' ', '%', $request->search)) . '%');
                        $query->orWhere('e.name', 'LIKE', '%' . trim(str_replace(' ', '%', $request->search)) . '%');
                        $query->orWhere('e.surname', 'LIKE', '%' . trim(str_replace(' ', '%', $request->search)) . '%');
                    }
                })->select('w.*', 'e.name', 'e.surname')->groupBy('w.emp_id', 'w.datetime', 'w.device_id')->orderBy('w.datetime', 'ASC')->get();
                if ($work->isNotEmpty()) {
                    foreach ($work as $rec) {
                        $exp = explode(" ", $rec->datetime);
                        $data[$i]["emp_id"] = $rec->emp_id;
                        $data[$i]["date"] = $exp[0];
                        $data[$i]["time"] = $exp[1];
                        $i++;
                    }
                }
                $date = $previous_date;
                $arr_history = [];
                $i = 0;
                while (strtotime($date) < strtotime($current_date)) {
                    $time = "";
                    $is_true = array_search($date, array_column($data, 'date'));
                    if ($is_true !== false) {
                        $keys = array_keys(array_column($data, 'date'), $date);
                        for ($j=0; $j<count($keys); $j++) {
                            if ($time != "") $time .= "<br>";
                            $time .= $data[$keys[$j]]["time"];
                        }
                    }

                    $dt1 = strtotime($date);
                    $dt2 = date("l", $dt1);
                    $dt3 = strtolower($dt2);
                    $calc = self::calcday($dt3);

                    $arr_history[$i]["day"] = $dt3;
                    $arr_history[$i]["day_th"] = $calc["th"];
                    $arr_history[$i]["day_color"] = $calc["color"];
                    $arr_history[$i]["date"] = $date;
                    $arr_history[$i]["time"] = $time;
                    $date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
                    $i++;
                }
                $hol_arr = self::getHolidayDateAndTitle();
                $arr_history = self::phparraysort($arr_history, array('date'));
                $output = '';
                $count = count($arr_history);
                if ($count > 0) {
                    for ($i=0; $i<$count; $i++) {
                        $text_sunday = '';
                        if ($arr_history[$i]['day'] == 'sunday') {
                            $text_sunday = 'text-muted';
                        }
                        $remark = '';
                        $ind = array_search($arr_history[$i]['date'], array_column($hol_arr, 'date'));
                        if ($ind !== false) {
                            $text_sunday = 'text-muted';
                            $remark = $hol_arr[$ind]["title"];
                        }
                        $output .= '
                                <tr class="'.$text_sunday.'">
                                <td class="text-center fw-bold" style="border-left: 5px solid '.$arr_history[$i]['day_color'].'">' . $arr_history[$i]['day_th'] . '</td>
                                <td class="text-center table-light '.$text_sunday.'">' . Carbon::parse($arr_history[$i]['date'])->thaidate('d/m/Y') . '</td>
                                <td class="lh35">' . $arr_history[$i]['time'] . '</td>
                                <td class="text-center table-light"></td>
                                <td class="text-center"></td>
                                <td class="text-center table-light"></td>
                                <td class="text-center"></td>
                                <td class="text-center table-light"></td>
                                <td class="text-center"></td>
                                <td class="text-center table-light"></td>
                                <td class="text-center"></td>
                                <td class="text-center table-light"></td>
                                <td class="text-center"></td>
                                <td class="table-light">' . $remark . '</td>
                                </tr>
                                ';
                    }
                } else {
                    $output = '<tr> <td align="center" colspan="14"> ไม่พบข้อมูล </td> </tr>';
                }
            } else {
                $output = '<tr> <td align="center" colspan="14"> ไม่พบข้อมูล </td> </tr>';
                $count = 0;
            }

            $result = array(
                'count_data'  => $count,
                'table_data'  => $output,
            );
            echo json_encode($result);
        }
    }

    public function period_salary()
    {
        return view('leave.manage.period-salary');
    }

    public function period_salary_search(Request $request)
    {
        if ($request->ajax()) {
            $data = PeriodSalary::where('year', '=', $request->year);

            $totalRecords = $data->select('count(*) as allcount')->count();
            $records = $data->select('*')->orderBy("month", "ASC")->orderBy("start", "ASC")->get();
            $rows = [];
            $n = 1;
            foreach ($records as $rec) {
                $action = '<div>
                        <a class="action-icon" href="'.url('leave/manage/period-salary/edit', $rec->id).'" title="แก้ไข"><i class="mdi mdi-square-edit-outline"></i></a>
                        <a class="action-icon" href="javascript:void(0);" onclick="deletePeriodSalaryConfirmation(\''.$rec->id.'\')" title="ลบ"><i class="mdi mdi-delete"></i></a>
                    </div>';
                $rows[] = array(
                    "no" => $n,
                    "year" => $rec->year + 543,
                    "month" => Carbon::createFromDate($rec->year, $rec->month, 1)->thaidate('F'),
                    "start" => Carbon::parse($rec->start)->thaidate('d/m/Y'),
                    "end" => Carbon::parse($rec->end)->thaidate('d/m/Y'),
                    "last" => ($rec->last!="") ? Carbon::parse($rec->last)->thaidate('d/m/Y') : "-",
                    "remark" => $rec->remark,
                    "action" => $action,
                );
                $n++;
            }

            $response = array(
                "total" => $totalRecords,
                "totalNotFiltered" => $totalRecords,
                "rows" => $rows,
            );
            return response()->json($response);
        }
    }

    public function period_salary_create()
    {
        $month = self::get_months();
        return view('leave.manage.period-salary-create', compact('month'));
    }

    public function period_salary_store(Request $request)
    {
        $request->validate([
            'year' => 'required',
            'month' => 'required',
            'start' => 'required',
            'end' => 'required',
            // 'last' => 'required',
        ],[
            'year.required' => 'กรุณาเลือกปี',
            'month.required' => 'กรุณาเลือกเดือน',
            'start.required' => 'กรุณาเลือกวันเริ่มต้น',
            'end.required' => 'กรุณาเลือกวันสิ้นสุด',
            // 'last.required' => 'กรุณาเลือกวันสุดท้ายของการลางาน',
        ]);

        if ($request->start != "") {
            $start = Carbon::createFromFormat('d/m/Y', $request->start)->format('Y-m-d');
        } else {
            $start = null;
        }
        if ($request->end != "") {
            $end = Carbon::createFromFormat('d/m/Y', $request->end)->format('Y-m-d');
        } else {
            $end = null;
        }
        if ($request->last != "") {
            $last = Carbon::createFromFormat('d/m/Y', $request->last)->format('Y-m-d');
        } else {
            $last = null;
        }

        $period = new PeriodSalary();
        $period->year = $request->year;
        $period->month = $request->month;
        $period->start = $start;
        $period->end = $end;
        $period->last = $last;
        $period->remark = $request->remark;
        $period->save();

        alert()->success('เพิ่มงวดค่าแรงเรียบร้อย');
        return redirect('leave/manage/period-salary');
    }

    public function period_salary_edit($id)
    {
        $month = self::get_months();
        $period = PeriodSalary::find($id);
        return view('leave.manage.period-salary-edit', compact('month', 'period'));
    }

    public function period_salary_update(Request $request)
    {
        $request->validate([
            'year' => 'required',
            'month' => 'required',
            'start' => 'required',
            'end' => 'required',
            // 'last' => 'required',
        ],[
            'year.required' => 'กรุณาเลือกปี',
            'month.required' => 'กรุณาเลือกเดือน',
            'start.required' => 'กรุณาเลือกวันเริ่มต้น',
            'end.required' => 'กรุณาเลือกวันสิ้นสุด',
            // 'last.required' => 'กรุณาเลือกวันสุดท้ายของการลางาน',
        ]);

        if ($request->start != "") {
            $start = Carbon::createFromFormat('d/m/Y', $request->start)->format('Y-m-d');
        } else {
            $start = null;
        }
        if ($request->end != "") {
            $end = Carbon::createFromFormat('d/m/Y', $request->end)->format('Y-m-d');
        } else {
            $end = null;
        }
        if ($request->last != "") {
            $last = Carbon::createFromFormat('d/m/Y', $request->last)->format('Y-m-d');
        } else {
            $last = null;
        }

        $period = PeriodSalary::find($request->id);
        $period->year = $request->year;
        $period->month = $request->month;
        $period->start = $start;
        $period->end = $end;
        $period->last = $last;
        $period->remark = $request->remark;
        $period->save();

        alert()->success('อัปเดตงวดค่าแรงเรียบร้อย');
        return redirect('leave/manage/period-salary');
    }

    public function period_salary_destroy($id)
    {
        $leave = Leave::where('period_salary_id', '=', $id)->whereNotIn('leave_status', ['C1', 'C2', 'C3'])->get();
        if ($leave->isNotEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'มีใบลาในงวดค่าแรงนี้อยู่ ไม่สามารถลบได้',
            ]);
        }
        $period = PeriodSalary::find($id);
        $period->delete();
        return response()->json([
            'success' => true,
            'message' => 'ลบข้อมูลงวดค่าแรงเรียบร้อย',
        ]);
    }

    public function upload()
    {
        return view('leave.manage.leave-upload');
    }

    public function upload_file(Request $request)
    {
        $request->validate([
            'file' => 'required',
        ],[
            'file.required' => 'ยังไม่ได้เลือกไฟล์',
        ]);
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = 'finger_records';
            $input['filename'] = $fileName . '.' . $file->extension();
            $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/assets/uploads/excel/';
            $file->move($destinationPath, $input['filename']);

            $users = [];
            $i = 0;
            if (($open = fopen($destinationPath . $input['filename'], "r")) !== FALSE) {
                while (($data = fgetcsv($open, 1000, ",")) !== FALSE) {
                    if ($data[1] != "0" && $data[1] != "") {
                        $users[$i]["emp_id"] = $data[1];
                        $users[$i]["datetime"] = $data[0];
                        $i++;
                    }
                }
                fclose($open);
            }
            if (File::exists($destinationPath.$input['filename'])) {
                File::delete($destinationPath.$input['filename']);
            }
            $users = self::phparraysort($users, array('emp_id','datetime'));
            foreach ($users as $user) {
                $work = AttendanceLog::firstOrNew(array('emp_id' => $user["emp_id"], 'datetime' => $user["datetime"]));
                $work->emp_id = $user["emp_id"];
                $work->datetime = $user["datetime"];
                $work->save();
            }
        }
        alert()->success('อัปโหลดข้อมูลเรียบร้อย');
        return back();
    }

    public function fingerprint()
    {
        return view('leave.manage.leave-fingerprint');
    }

    public function fingerprint_upload(Request $request)
    {
        $request->validate([
            'file' => 'required',
        ],[
            'file.required' => 'ยังไม่ได้เลือกไฟล์',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = 'finger_records_by_'.auth()->user()->emp_id;
            $input['filename'] = $fileName . '.' . $file->extension();
            $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/assets/uploads/excel/';
            $file->move($destinationPath, $input['filename']);

            $data = self::convertFingerprintToArray($destinationPath . $input['filename']);
            if ($data === false) {
                alert()->warning('รูปแบบไฟล์ข้อมูลไม่ถูกต้อง!')->autoClose(false);
                return back();
            }
            $result_summary = $data["summary"];

            $list = [];
            $branch =  DB::table('fingerprint_branch')->orderBy('fpbranch_id', 'ASC')->get(['fpbranch_id','fpbranch_name']);
            $device =  DB::table('fingerprint_devices')->orderBy('fpdevice_id', 'ASC')->get(['fpdevice_id','fpdevice_name','fpbranch_id']);
            if ($branch->isNotEmpty()) {
                foreach ($branch as $b) {
                    $child = [];
                    if ($device->isNotEmpty()) {
                        foreach ($device as $d) {
                            if ($b->fpbranch_id === $d->fpbranch_id) {
                                $index = array_search($d->fpdevice_id, array_column($result_summary, "device_id"));
                                $qty = ($index !== false) ? $result_summary[$index]["qty"] : 0;
                                $child[] = array(
                                    "id" => $d->fpdevice_id,
                                    "name" => $d->fpdevice_name,
                                    "qty" => $qty,
                                );
                            }
                        }
                    }
                    $sumQty = (!empty($child)) ? array_sum(array_column($child,'qty')) : 0;
                    $list[] = array(
                        "id" => $b->fpbranch_id,
                        "name" => $b->fpbranch_name,
                        "total" => $sumQty,
                        "device" => $child,
                    );
                }
                $sumTotal = array_sum(array_column($list, 'total'));
            }
            return redirect()->back()->with('data', $list)->with('total', $sumTotal)->with('file_name', $input['filename']);
        }
    }

    public function fingerprint_store(Request $request)
    {
        $device = $request->device;
        $download_name = $request->download_name;
        $input['filename'] = $request->file_name;
        $destinationPath = $_SERVER['DOCUMENT_ROOT'];
        $excelPath = $destinationPath . '/assets/uploads/excel/';
        $data = self::convertFingerprintToArray($excelPath . $input['filename']);
        if ($data === false) {
            alert()->warning('รูปแบบไฟล์ข้อมูลไม่ถูกต้อง!')->autoClose(false);
            return back();
        }
        $result = $data["result"];
        if (!empty($result)) {
            $createMultipleLogs = [];
            $attendance = self::scopeAttendanceLog($result);
            $text = "";
            $text .= "\r\n";
            foreach ($result as $res) {
                if (in_array(trim($res["device_id"]), $device)) {
                    $datetime = Carbon::createFromFormat('d/m/Y H:i:s', trim($res["datetime"]))->format('Y-m-d H:i:s');
                    $isData = false;
                    if (count($attendance)) {
                        foreach ($attendance as $att) {
                            if (trim($res["emp_id"]) == trim($att->emp_id) && strtotime(trim($datetime)) == strtotime(trim($att->datetime)) && trim($res["device_id"]) == trim($att->device_id)) {
                                $isData = true;
                                break;
                            }
                        }
                    }
                    if (!$isData) {
                        $createMultipleLogs[] = ['emp_id' => $res["emp_id"], 'datetime' => $datetime, 'device_id' => $res["device_id"], 'created_at' => now(), 'updated_at' => now()];
                    }

                    $exp = explode(" ", $res['datetime']);
                    $text .= trim($res["emp_id"]) . " " . trim($exp[0]) . " " . trim($exp[1]) . "\r\n"; // emp_id date time
                }
            }
            if (count($createMultipleLogs)) {
                AttendanceLog::insert($createMultipleLogs);
            }
            if (File::exists($excelPath.$input['filename'])) {
                File::delete($excelPath.$input['filename']);
            }
            if ($download_name != "" && $download_name != null) {
                $txtFile = $download_name . '.txt';
                $textPath = $destinationPath . '/assets/uploads/txt/';
                File::put($textPath . $txtFile, $text);
            }

            return response()->json(['success' => true,'message' => 'บันทึกข้อมูลเรียบร้อย','file_name' => $download_name]);
        }
    }

    public function fingerprint_data(Request $request)
    {
        if ($request->has('date_start') && $request->has('date_end')) {
            $date_start = Carbon::createFromFormat('d/m/Y', $request->date_start)->format('Y-m-d');
            $date_end = Carbon::createFromFormat('d/m/Y', $request->date_end)->format('Y-m-d');

            $data = self::convertAttendanceToArray($date_start, $date_end);
            if ($data === false) {
                alert()->warning('รูปแบบข้อมูลไม่ถูกต้อง!')->autoClose(false);
                return back();
            }
            $result_summary = $data["summary"];

            $list = [];
            $branch =  DB::table('fingerprint_branch')->orderBy('fpbranch_id', 'ASC')->get(['fpbranch_id','fpbranch_name']);
            $device =  DB::table('fingerprint_devices')->orderBy('fpdevice_id', 'ASC')->get(['fpdevice_id','fpdevice_name','fpbranch_id']);
            if ($branch->isNotEmpty()) {
                foreach ($branch as $b) {
                    $child = [];
                    if ($device->isNotEmpty()) {
                        foreach ($device as $d) {
                            if ($b->fpbranch_id === $d->fpbranch_id) {
                                $index = array_search($d->fpdevice_id, array_column($result_summary, "device_id"));
                                $qty = ($index !== false) ? $result_summary[$index]["qty"] : 0;
                                $child[] = array(
                                    "id" => $d->fpdevice_id,
                                    "name" => $d->fpdevice_name,
                                    "qty" => $qty,
                                );
                            }
                        }
                    }
                    $sumQty = (!empty($child)) ? array_sum(array_column($child,'qty')) : 0;
                    $list[] = array(
                        "id" => $b->fpbranch_id,
                        "name" => $b->fpbranch_name,
                        "total" => $sumQty,
                        "device" => $child,
                    );
                }
            }
            $sumTotal = array_sum(array_column($list, 'total'));
            return redirect()->back()->with('data_export', $list)->with('total', $sumTotal)->with('date_start', $date_start)->with('date_end', $date_end);
        }
    }

    public function fingerprint_data_download(Request $request)
    {
        if ($request->has('devicedl') && $request->has('date_start_download') && $request->has('date_end_download') && $request->has('downloaddl_name')) {
            $device = $request->devicedl;
            $date_start = $request->date_start_download;
            $date_end = $request->date_end_download;
            $download_name = $request->downloaddl_name;

            $attendance =  DB::table('attendance_log')->whereRaw('SUBSTRING(datetime, 1, 10) >= "'.$date_start.'"')->whereRaw('SUBSTRING(datetime, 1, 10) <= "'.$date_end.'"')->orderBy('emp_id', 'ASC')->orderBy('datetime', 'ASC')->get(['emp_id','datetime','device_id'])->toArray();
            if (count($attendance)) {
                $text = "";
                $text .= "\r\n";
                foreach ($attendance as $res) {
                    if (in_array(trim($res->device_id), $device)) {
                        $datetime = Carbon::createFromFormat('Y-m-d H:i:s', trim($res->datetime))->format('d/m/Y H:i:s');
                        $exp = explode(" ", $datetime);
                        $text .= trim($res->emp_id) . " " . trim($exp[0]) . " " . trim($exp[1]) . "\r\n"; // emp_id date time
                    }
                }
                if ($download_name != "" && $download_name != null) {
                    $txtFile = $download_name . '.txt';
                    $destinationPath = $_SERVER['DOCUMENT_ROOT'];
                    $textPath = $destinationPath . '/assets/uploads/txt/';
                    File::put($textPath . $txtFile, $text);
                    return response()->download($textPath . $download_name . '.txt')->deleteFileAfterSend(true);
                }
            } else {
                alert()->warning('ไม่พบข้อมูล!')->autoClose(false);
                return back();
            }
        } else {
            alert()->warning('ข้อมูลที่ส่งไปไม่ถูกต้อง!')->autoClose(false);
            return back();
        }
    }

    public function fingerprint_download($file_name)
    {
        $destinationPath = $_SERVER['DOCUMENT_ROOT'];
        $textPath = $destinationPath . '/assets/uploads/txt/';
        return response()->download($textPath . $file_name . '.txt')->deleteFileAfterSend(true);
    }

    public function convertFingerprintToArray($path)
    {
        if ($path != "") {
            $result = [];
            $result_summary = [];
            $i = 0;
            if (($open = fopen($path, "r")) !== FALSE) {
                while (($data = fgetcsv($open, 1000, ",")) !== FALSE) {
                    if (strlen($data[5]) === 6 && $data[5] !== "0" && $data[0] !== "" && $data[0] !== null) {
                        if (!is_numeric($data[1]) || !is_numeric($data[5]) || !self::validateDateTime($data[0])) {
                            return false;
                        }
                        $result[$i]["datetime"] = Carbon::parse($data[0])->format('d/m/Y H:i:s');
                        $result[$i]["device_id"] = $data[1];
                        $result[$i]["status"] = $data[3];
                        $result[$i]["emp_id"] = $data[5];
                        $result[$i]["name"] = $data[6];

                        $index = array_search($data[1], array_column($result_summary, "device_id"));
                        if ($index !== false) {
                            $result_summary[$index]["qty"]++;
                        } else {
                            $data_count = count($result_summary);
                            $result_summary[$data_count]["device_id"] = $data[1];
                            $result_summary[$data_count]["qty"] = 1;
                            $data_count++;
                        }

                        $i++;
                    }
                }
                fclose($open);
            }
            $result = self::phparraysort($result, array('emp_id','datetime'));
            return array("result"=>$result,"summary"=>$result_summary);
        }
    }

    public function convertAttendanceToArray($date_start, $date_end)
    {
        if ($date_start != "" && $date_end != "") {
            $result = [];
            $result_summary = [];
            $i = 0;
            $attendance =  DB::table('attendance_log')->whereRaw('SUBSTRING(datetime, 1, 10) >= "'.$date_start.'"')->whereRaw('SUBSTRING(datetime, 1, 10) <= "'.$date_end.'"')->orderBy('emp_id', 'ASC')->orderBy('datetime', 'ASC')->get(['emp_id','datetime','device_id']);
            if ($attendance->isNotEmpty()) {
                foreach ($attendance as $data) {
                    $result[$i]["datetime"] = Carbon::parse($data->datetime)->format('d/m/Y H:i:s');
                    $result[$i]["emp_id"] = $data->emp_id;
                    $result[$i]["device_id"] = $data->device_id;

                    $index = array_search($data->device_id, array_column($result_summary, "device_id"));
                    if ($index !== false) {
                        $result_summary[$index]["qty"]++;
                    } else {
                        $data_count = count($result_summary);
                        $result_summary[$data_count]["device_id"] = $data->device_id;
                        $result_summary[$data_count]["qty"] = 1;
                        $data_count++;
                    }
                    $i++;
                }
            }
            $result = self::phparraysort($result, array('emp_id','datetime'));
            return array("result"=>$result,"summary"=>$result_summary);
        }
    }

    public function scopeAttendanceLog($arr)
    {
        $date_sort = self::phparraysort($arr, array('datetime'));
        $date_sort = array_column($date_sort, "datetime");
        $firstDate = Carbon::createFromFormat('d/m/Y H:i:s', current($date_sort))->format('Y-m-d');
        $lastDate = Carbon::createFromFormat('d/m/Y H:i:s', end($date_sort))->format('Y-m-d');
        $attendance =  DB::table('attendance_log')->whereRaw('SUBSTRING(datetime, 1, 10) >= "'.$firstDate.'"')->whereRaw('SUBSTRING(datetime, 1, 10) <= "'.$lastDate.'"')->orderBy('emp_id', 'ASC')->orderBy('datetime', 'ASC')->get(['emp_id','datetime','device_id'])->toArray();
        return $attendance;
    }

    public function validateDateTime($datetime, $format = 'Y-m-d H:i:s'){
        $d = \DateTime::createFromFormat($format, $datetime);
        return $d && $d->format($format) === $datetime;
    }

    public function search_emp(Request $request)
    {
        if ($request->ajax()) {
            $result = DB::table('employee as e')->leftJoin('department as d', 'e.dept_id', '=', 'd.dept_id')
            ->where('e.emp_id', '<>', '')
            ->where(function ($query) use ($request) {
                if ($request->search != "") {
                    $query->orWhere('e.emp_id', 'like', '%'.trim(str_replace(' ', '%', $request->search)).'%');
                    $query->orWhere('e.name', 'like', '%'.trim(str_replace(' ', '%', $request->search)).'%');
                    $query->orWhere('e.surname', 'like', '%'.trim(str_replace(' ', '%', $request->search)).'%');

                    $exp = explode(' ', $request->search);
                    if (count($exp) == 2) {
                        $query->orWhere('e.name', 'like', '%'.trim(str_replace(' ', '%', $exp[0])).'%');
                        $query->orWhere('e.surname', 'like', '%'.trim(str_replace(' ', '%', $exp[1])).'%');
                    }
                }
            })->orderBy("e.emp_id", "asc")->get(['e.emp_id', 'e.title', 'e.name', 'e.surname', 'e.nickname', 'e.gender', 'e.image', 'e.position_id', 'e.dept_id', 'e.area_code', 'e.emp_type', 'e.emp_status', 'd.level', 'd.dept_name']);
            return response()->json($result);
        }
    }

    public function phparraysort($Array, $SortBy=array(), $Sort = SORT_REGULAR) {
        if (is_array($Array) && count($Array) > 0 && !empty($SortBy)) {
            $Map = array();
            foreach ($Array as $Key => $Val) {
                $Sort_key = '';
                foreach ($SortBy as $Key_key) {
                    $Sort_key .= $Val[$Key_key];
                }
                $Map[$Key] = $Sort_key;
            }
            asort($Map, $Sort);
            $Sorted = array();
            foreach ($Map as $Key => $Val) {
                $Sorted[] = $Array[$Key];
            }
            // return array_reverse($Sorted);
            return $Sorted;
        }
        return $Array;
    }

    public function selection_period_salary(Request $request)
    {
        $year = ($request->year != "") ? $request->year : date('Y');
        $data = PeriodSalary::where('year', '=', $year)->orderBy('year', 'asc')->orderBy('month', 'asc')->orderBy('start', 'asc')->get();

        return response()->json(["data"=>$data]);
    }
}