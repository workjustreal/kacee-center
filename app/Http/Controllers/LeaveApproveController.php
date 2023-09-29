<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Leave;
use App\Models\RecordWorking;
use App\Models\LeaveType;
use App\Models\PeriodSalary;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;
use File;

class LeaveApproveController extends LeaveBaseController
{
    protected $attathPath;
    public function __construct()
    {
        $this->middleware('auth');
        $this->attathPath = $_SERVER['DOCUMENT_ROOT'] . '/assets/files/leave/';
    }

    public function dashboard()
    {
        $pending = self::pendingLeave();
        $approved = self::approvedLeave();
        $completed = self::completedLeave();
        $canceled = self::canceledLeave();

        $pendingRecordWorking = self::pendingRecordWorking();
        $approvedRecordWorking = self::approvedRecordWorking();
        $completedRecordWorking = self::completedRecordWorking();
        $canceledRecordWorking = self::canceledRecordWorking();
        return view('leave.approve.approve-dashboard', compact('pending', 'approved', 'completed', 'canceled', 'pendingRecordWorking', 'approvedRecordWorking', 'completedRecordWorking', 'canceledRecordWorking'));
    }

    public function calendar()
    {
        $result = array();
        $approved = self::approvedLeave();
        foreach ($approved as $list) {
            $nickname = ($list->nickname!="") ? "(".$list->nickname.")" : "";
            $name = $list->name . " " . $list->surname . " " . $nickname;
            $short_name = $list->name . " " . $nickname;
            $popoverHeader = "";
            if ($list->leave_range=="etc") {
                $popoverHeader = Carbon::parse($list->leave_start_date)->thaidate('วันที่ j M y') . " เวลา " . Carbon::parse($list->leave_start_time)->format('H:i') . "-" . Carbon::parse($list->leave_end_time)->format('H:i');
            } else if ($list->leave_range=="many") {
                $popoverHeader = Carbon::parse($list->leave_start_date)->thaidate('ตั้งแต่วันที่ j M y') . " - " . Carbon::parse($list->leave_end_date)->thaidate('j M y');
            } else {
                $popoverHeader = Carbon::parse($list->leave_start_date)->thaidate('วันที่ j M y');
            }
            $popoverDescription = "ผู้ลา: ".$name."<br>";
            if ($list->leave_reason != "") {
                $popoverDescription .= "เหตุผล: ".$list->leave_reason."<br>";
            }
            $popoverDescription .= "สถานะ: ".self::get_leave_status($list->leave_status)["name"];
            $result[] = array(
                "title" => $list->leave_type_name." ".$short_name,
                "popoverHeader" => $popoverHeader,
                "popoverDescription" => $popoverDescription,
                "name" => $name,
                "start" => Carbon::parse($list->leave_start_date)->format('Y-m-d'),
                "end" => Carbon::parse($list->leave_end_date)->addDays()->format('Y-m-d'),
                "url" => url('/leave/document/'.$list->leave_id),
                "backgroundColor" => "#fe8929",
                "allDay" => ($list->leave_range=="etc") ? false : true,
            );
        }
        $approvedRecordWorking = self::approvedRecordWorking();
        foreach ($approvedRecordWorking as $list) {
            $nickname = ($list->nickname!="") ? "(".$list->nickname.")" : "";
            $name = $list->name . " " . $list->surname . " " . $nickname;
            $short_name = $list->name . " " . $nickname;
            $popoverHeader = Carbon::parse($list->work_date)->thaidate('วันที่ j M y');
            $popoverDescription = "ผู้ลา: ".$name."<br>";
            if ($list->remark != "") {
                $popoverDescription .= "เหตุผล: ".$list->remark."<br>";
            }
            $popoverDescription .= "สถานะ: ".self::get_leave_status($list->approve_status)["name"];
            $result[] = array(
                "title" => "ใบบันทึกวันทำงาน ".$short_name,
                "popoverHeader" => $popoverHeader,
                "popoverDescription" => $popoverDescription,
                "name" => $name,
                "start" => Carbon::parse($list->work_date)->format('Y-m-d'),
                "end" => Carbon::parse($list->work_date)->format('Y-m-d'),
                "url" => url('/leave/document-record-working/'.$list->id),
                "backgroundColor" => "#1259fa",
                "allDay" => true,
            );
        }
        $dt = date('Y-m-d');
        $holiday = Event::whereDate('events.start', '>=', date("Y-01-01", strtotime ('-1 year', strtotime($dt))))
                ->whereDate('events.end', '<=', date("Y-12-31", strtotime ('+1 year', strtotime($dt))))
                ->where('events.calendar', '=', 1)
                ->where('events.status', '=', 1)
                ->where('events.holiday', '=', 1)->get();
        foreach ($holiday as $list) {
            $result[] = array(
                "title" => $list->title,
                "popoverHeader" => $list->title,
                "popoverDescription" => "วันหยุดประจำปี",
                "name" => "",
                "start" => Carbon::parse($list->start)->format('Y-m-d'),
                "end" => Carbon::parse($list->end)->addDays()->format('Y-m-d'),
                "url" => url('/holidays/show/'.$list->id),
                "backgroundColor" => "#c31b1d",
                "allDay" => true,
            );
        }
        return response()->json($result);
    }

    public function pendingLeave()
    {
        // รออนุมัติ
        $emp_id = [];
        $users = self::getAuthorizeUsers();
        if ($users->isNotEmpty()) {
            foreach ($users as $users) {
                $leave = Leave::whereIn('leave_status', ['P', 'A1'])->where('emp_id', '=', $users->emp_id)->orderBy('leave_id', 'asc')->get(['leave_id', 'emp_id', 'leave_status']);
                if ($leave->isNotEmpty()) {
                    foreach ($leave as $leave) {
                        $cau = self::chkEmpApprove($leave->emp_id, $users->dept_id, $leave->leave_status);
                        if ($cau) {
                            $emp_id[] = $users->emp_id;
                            break;
                        }
                    }
                }
            }
        }
        $emp_id = array_unique($emp_id);

        $data = DB::table('leave as l')->leftJoin('employee as e', 'l.emp_id', '=', 'e.emp_id')->leftJoin('department as d', 'e.dept_id', '=', 'd.dept_id')
        ->whereIn('l.leave_status', ['P', 'A1'])->whereIn('l.emp_id', $emp_id)
        ->select('l.*', 'e.name', 'e.surname', 'e.nickname', 'e.image', 'd.dept_name')->orderBy('l.leave_id', 'asc')->get();
        return $data;
    }

    public function approvedLeave()
    {
        // อนุมัติแล้ว
        $auth = auth()->user();
        $emp_id = [];
        $users = self::getAuthorizeUsers();
        if ($users->isNotEmpty()) {
            foreach ($users as $users) {
                $emp_id[] = $users->emp_id;
            }
        }

        $data = DB::table('leave as l')->leftJoin('leave_type as t', 'l.leave_type_id', '=', 't.leave_type_id')->leftJoin('employee as e', 'l.emp_id', '=', 'e.emp_id')->leftJoin('department as d', 'e.dept_id', '=', 'd.dept_id')
        ->whereIn('l.leave_status', ['A1', 'A2', 'S'])->whereIn('l.emp_id', $emp_id)
        ->where(function($query) use ($auth){
            $query->where('l.approve_lid', '=', $auth->emp_id)->orWhere('l.approve_mid', '=', $auth->emp_id);
        })->select('l.*', 't.leave_type_name', 'e.name', 'e.surname', 'e.nickname', 'e.image', 'd.dept_name')->orderBy('l.leave_id', 'asc')->get();
        return $data;
    }

    public function completedLeave()
    {
        // เสร็จสมบูรณ์ (บุคคลอนุมัติ)
        $auth = auth()->user();
        $emp_id = [];
        $users = self::getAuthorizeUsers();
        if ($users->isNotEmpty()) {
            foreach ($users as $users) {
                $emp_id[] = $users->emp_id;
            }
        }

        $data = DB::table('leave as l')->leftJoin('employee as e', 'l.emp_id', '=', 'e.emp_id')->leftJoin('department as d', 'e.dept_id', '=', 'd.dept_id')
        ->whereIn('l.leave_status', ['S'])->whereIn('l.emp_id', $emp_id)
        ->where(function($query) use ($auth){
            $query->where('l.approve_lid', '=', $auth->emp_id)->orWhere('l.approve_mid', '=', $auth->emp_id);
        })->select('l.*', 'e.name', 'e.surname', 'e.nickname', 'e.image', 'd.dept_name')->orderBy('l.leave_id', 'asc')->get();
        return $data;
    }

    public function canceledLeave()
    {
        // ยกเลิก (ไม่อนุมัติ)
        $auth = auth()->user();
        $emp_id = [];
        $users = self::getAuthorizeUsers();
        if ($users->isNotEmpty()) {
            foreach ($users as $users) {
                $emp_id[] = $users->emp_id;
            }
        }

        $data = DB::table('leave as l')->leftJoin('employee as e', 'l.emp_id', '=', 'e.emp_id')->leftJoin('department as d', 'e.dept_id', '=', 'd.dept_id')
        ->whereIn('l.leave_status', ['C1', 'C2', 'C3'])->whereIn('l.emp_id', $emp_id)
        ->where(function($query) use ($auth){
            $query->where('l.approve_lid', '=', $auth->emp_id)->orWhere('l.approve_mid', '=', $auth->emp_id);
        })->select('l.*', 'e.name', 'e.surname', 'e.nickname', 'e.image', 'd.dept_name')->orderBy('l.leave_id', 'asc')->get();
        return $data;
    }

    public function pending_search(Request $request)
    {
        if ($request->ajax()) {
            $emp_id = [];
            $users = self::getAuthorizeUsers();
            if ($users->isNotEmpty()) {
                foreach ($users as $users) {
                    $leave = Leave::whereIn('leave_status', ['P', 'A1'])->where('emp_id', '=', $users->emp_id)->orderBy('leave_id', 'asc')->get(['leave_id', 'emp_id', 'leave_status']);
                    if ($leave->isNotEmpty()) {
                        foreach ($leave as $leave) {
                            $cau = self::chkEmpApprove($leave->emp_id, $users->dept_id, $leave->leave_status);
                            if ($cau) {
                                $emp_id[] = $users->emp_id;
                                break;
                            }
                        }
                    }
                }
            }
            $emp_id = array_unique($emp_id);

            $data = DB::table('leave as l')->leftJoin('leave_type as t', 'l.leave_type_id', '=', 't.leave_type_id')
            ->leftJoin('employee as e', 'l.emp_id', '=', 'e.emp_id')->leftJoin('department as d', 'e.dept_id', '=', 'd.dept_id')
            ->whereIn('l.leave_status', ['P', 'A1'])->whereIn('l.emp_id', $emp_id);

            $totalRecords = $data->select('count(l.*) as allcount')->count();
            $records = $data->select('l.*', 't.leave_type_name', 'e.name', 'e.surname', 'e.nickname', 'e.image', 'd.dept_id', 'd.dept_name')->orderBy('l.leave_id', 'asc')->get();
            $rows = [];
            $n = 1;
            foreach ($records as $rec) {
                $leave_date = '';
                if ($rec->leave_start_date == $rec->leave_end_date) {
                    $leave_date = Carbon::parse($rec->leave_start_date)->thaidate('d/m/Y');
                } else {
                    $leave_date = Carbon::parse($rec->leave_start_date)->thaidate('d/m/Y') . ' ถึง ' . Carbon::parse($rec->leave_end_date)->thaidate('d/m/Y');
                }
                $leave_amount = '';
                if ($rec->leave_day > 0) {
                    $leave_amount .= $rec->leave_day . ' ว.';
                }
                if ($rec->leave_minute > 0) {
                    if ($leave_amount != '') $leave_amount .= ', ';
                    $leave_amount .= self::hoursandmins($rec->leave_minute, '%02d ชม., %02d น.');
                }
                $status = self::get_leave_status($rec->leave_status);
                $leave_status = '<span class="badge ' . $status["badge"] . '">' . $status["name"] . '</span>';
                $leave_manage = '<div>
                        <a class="action-icon d-none" href="'.url('leave/approve/emp-leave-edit', $rec->leave_id).'" title="แก้ไข"><i class="mdi mdi-square-edit-outline"></i></a>
                        <a class="action-icon" href="'.url('leave/approve/emp-leave-approve', $rec->leave_id).'" title="อนุมัติ"><i class="mdi mdi-check-circle-outline"></i></a>
                        <a class="action-icon" href="javascript:void(0);" onclick="cancelLeaveConfirmation(\''.$rec->leave_id.'\')" title="ไม่อนุมัติ"><i class="mdi mdi-close-circle-outline"></i></a>
                    </div>';
                $rows[] = array(
                    "leave_id" => $rec->leave_id,
                    "leave_user" => '<a href="'.url('leave/approve/emp-leave-approve', $rec->leave_id).'" class="text-secondary text-fw">'.self::callUserName($rec->image, $rec->name, $rec->surname, $rec->nickname).'</a>',
                    "create_date" => '<a href="'.url('leave/approve/emp-leave-approve', $rec->leave_id).'" class="text-secondary text-fw">'.Carbon::parse($rec->created_at)->thaidate('d/m/Y').'</a>',
                    "leave_date" => '<a href="'.url('leave/approve/emp-leave-approve', $rec->leave_id).'" class="text-secondary text-fw">'.$leave_date.'</a>',
                    "leave_amount" => '<a href="'.url('leave/approve/emp-leave-approve', $rec->leave_id).'" class="text-secondary text-fw">'.$leave_amount.'</a>',
                    "leave_type" => '<a href="'.url('leave/approve/emp-leave-approve', $rec->leave_id).'" class="text-secondary text-fw">'.$rec->leave_type_name.'</a>',
                    "leave_reason" => '<a href="'.url('leave/approve/emp-leave-approve', $rec->leave_id).'" class="text-secondary text-fw">'.$rec->leave_reason.'</a>',
                    "leave_status" => '<a href="'.url('leave/approve/emp-leave-approve', $rec->leave_id).'" class="text-secondary text-fw">'.$leave_status.'</a>',
                    "leave_manage" => $leave_manage,
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

    public function approved_search(Request $request)
    {
        if ($request->ajax()) {

            $auth = auth()->user();
            $emp_id = [];
            $users = self::getAuthorizeUsers();
            if ($users->isNotEmpty()) {
                foreach ($users as $users) {
                    $emp_id[] = $users->emp_id;
                }
            }

            $data = DB::table('leave as l')->leftJoin('leave_type as t', 'l.leave_type_id', '=', 't.leave_type_id')
            ->leftJoin('employee as e', 'l.emp_id', '=', 'e.emp_id')->leftJoin('department as d', 'e.dept_id', '=', 'd.dept_id')
            ->whereIn('l.leave_status', ['A1', 'A2', 'S'])->whereIn('l.emp_id', $emp_id)
            ->where(function($query) use ($auth){
                $query->where('l.approve_lid', '=', $auth->emp_id)->orWhere('l.approve_mid', '=', $auth->emp_id);
            });

            $totalRecords = $data->select('count(l.*) as allcount')->count();
            $records = $data->select('l.*', 't.leave_type_name', 'e.name', 'e.surname', 'e.nickname', 'e.image', 'd.dept_id', 'd.dept_name')->orderBy('l.leave_id', 'desc')->get();
            $rows = [];
            $n = 1;
            foreach ($records as $rec) {
                $leave_date = '';
                if ($rec->leave_start_date == $rec->leave_end_date) {
                    $leave_date = Carbon::parse($rec->leave_start_date)->thaidate('d/m/Y');
                } else {
                    $leave_date = Carbon::parse($rec->leave_start_date)->thaidate('d/m/Y') . ' ถึง ' . Carbon::parse($rec->leave_end_date)->thaidate('d/m/Y');
                }
                $leave_amount = '';
                if ($rec->leave_day > 0) {
                    $leave_amount .= $rec->leave_day . ' ว.';
                }
                if ($rec->leave_minute > 0) {
                    if ($leave_amount != '') $leave_amount .= ', ';
                    $leave_amount .= self::hoursandmins($rec->leave_minute, '%02d ชม., %02d น.');
                }
                $status = self::get_leave_status($rec->leave_status);
                $leave_status = '<span class="badge ' . $status["badge"] . '">' . $status["name"] . '</span>';
                $leave_manage = '';
                if ($rec->leave_status == "A1") {
                    $approveL = self::chkEmpApproveLeader($rec->emp_id, $rec->dept_id);
                    if ($approveL !== false) {
                        $leave_manage = '<div>
                            <a class="action-icon d-none" href="'.url('leave/approve/emp-leave-edit', $rec->leave_id).'" title="แก้ไข"><i class="mdi mdi-square-edit-outline"></i></a>
                            <a class="action-icon" href="javascript:void(0);" onclick="cancelLeaveConfirmation(\''.$rec->leave_id.'\')" title="ยกเลิก"><i class="mdi mdi-cancel"></i></a>
                        </div>';
                    }
                } else if ($rec->leave_status == "A2") {
                    $approveM = self::chkEmpApproveManager($rec->emp_id, $rec->dept_id);
                    if ($approveM !== false) {
                        $leave_manage = '<div>
                            <a class="action-icon d-none" href="'.url('leave/approve/emp-leave-edit', $rec->leave_id).'" title="แก้ไข"><i class="mdi mdi-square-edit-outline"></i></a>
                            <a class="action-icon" href="javascript:void(0);" onclick="cancelLeaveConfirmation(\''.$rec->leave_id.'\')" title="ยกเลิก"><i class="mdi mdi-cancel"></i></a>
                        </div>';
                    }
                }
                $rows[] = array(
                    "leave_user" => '<a href="'.url('leave/document', $rec->leave_id).'" class="text-secondary text-fw">'.self::callUserName($rec->image, $rec->name, $rec->surname, $rec->nickname).'</div></a>',
                    "create_date" => '<a href="'.url('leave/document', $rec->leave_id).'" class="text-secondary text-fw">'.Carbon::parse($rec->created_at)->thaidate('d/m/Y').'</a>',
                    "leave_date" => '<a href="'.url('leave/document', $rec->leave_id).'" class="text-secondary text-fw">'.$leave_date.'</a>',
                    "leave_amount" => '<a href="'.url('leave/document', $rec->leave_id).'" class="text-secondary text-fw">'.$leave_amount.'</a>',
                    "leave_type" => '<a href="'.url('leave/document', $rec->leave_id).'" class="text-secondary text-fw">'.$rec->leave_type_name.'</a>',
                    "leave_reason" => '<a href="'.url('leave/document', $rec->leave_id).'" class="text-secondary text-fw">'.$rec->leave_reason.'</a>',
                    "leave_status" => '<a href="'.url('leave/document', $rec->leave_id).'" class="text-secondary text-fw">'.$leave_status.'</a>',
                    "leave_manage" => $leave_manage,
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

    public function users_search(Request $request)
    {
        if ($request->ajax()) {
            $users = self::getAuthorizeUsers();

            $totalRecords = $users->count();
            $records = $users;
            $rows = [];
            $n = 1;
            foreach ($records as $rec) {
                $type = self::get_emp_type($rec->emp_type);
                $emp_type = '<span class="badge ' . $type["badge"] . '">' . $type["name"] . '</span>';
                $status = self::get_emp_status($rec->emp_status);
                $emp_status = '<span class="badge ' . $status["badge"] . '">' . $status["name"] . '</span>';
                $rows[] = array(
                    "emp_id" => $rec->emp_id,
                    "emp_name" => self::callUserName($rec->image, $rec->name, $rec->surname, $rec->nickname),
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

    public function users_d_search(Request $request)
    {
        if ($request->ajax()) {
            $users = self::getAuthorizeUsersD();

            $totalRecords = $users->count();
            $records = $users;
            $rows = [];
            $n = 1;
            foreach ($records as $rec) {
                $type = self::get_emp_type($rec->emp_type);
                $emp_type = '<span class="badge ' . $type["badge"] . '">' . $type["name"] . '</span>';
                $status = self::get_emp_status($rec->emp_status);
                $emp_status = '<span class="badge ' . $status["badge"] . '">' . $status["name"] . '</span>';
                $rows[] = array(
                    "emp_id" => $rec->emp_id,
                    "emp_name" => self::callUserName($rec->image, $rec->name, $rec->surname, $rec->nickname),
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

    public function cancel_search(Request $request)
    {
        if ($request->ajax()) {
            $emp_id = [];
            $auth = auth()->user();
            $users = self::getAuthorizeUsers();
            if ($users->isNotEmpty()) {
                foreach ($users as $users) {
                    $emp_id[] = $users->emp_id;
                }
            }

            $data = DB::table('leave as l')->leftJoin('leave_type as t', 'l.leave_type_id', '=', 't.leave_type_id')->leftJoin('employee as e', 'l.emp_id', '=', 'e.emp_id')
            ->leftJoin('department as d', 'e.dept_id', '=', 'd.dept_id')->whereIn('l.leave_status', ['C1', 'C2', 'C3'])->whereIn('l.emp_id', $emp_id)
            ->where(function($query) use ($auth){
                $query->where('l.approve_lid', '=', $auth->emp_id)->orWhere('l.approve_mid', '=', $auth->emp_id);
            });

            $totalRecords = $data->select('count(l.*) as allcount')->count();
            $records = $data->select('l.*', 't.leave_type_name', 'e.name', 'e.surname', 'e.nickname', 'e.image', 'd.dept_id', 'd.dept_name')->orderBy('l.leave_id', 'asc')->get();
            $rows = [];
            $n = 1;
            foreach ($records as $rec) {
                $leave_date = '';
                if ($rec->leave_start_date == $rec->leave_end_date) {
                    $leave_date = Carbon::parse($rec->leave_start_date)->thaidate('d/m/y');
                } else {
                    $leave_date = Carbon::parse($rec->leave_start_date)->thaidate('d/m/y') . ' ถึง ' . Carbon::parse($rec->leave_end_date)->thaidate('d/m/y');
                }
                $leave_amount = '';
                if ($rec->leave_day > 0) {
                    $leave_amount .= $rec->leave_day . ' ว.';
                }
                if ($rec->leave_minute > 0) {
                    if ($leave_amount != '') $leave_amount .= ', ';
                    $leave_amount .= self::hoursandmins($rec->leave_minute, '%02d ชม., %02d น.');
                }
                $status = self::get_leave_status($rec->leave_status);
                $leave_status = '<span class="badge ' . $status["badge"] . '">' . $status["name"] . '</span>';
                $rows[] = array(
                    "leave_user" => '<a href="'.url('leave/document', $rec->leave_id).'" class="text-secondary text-fw">'.self::callUserName($rec->image, $rec->name, $rec->surname, $rec->nickname).'</a>',
                    "leave_date" => '<a href="'.url('leave/document', $rec->leave_id).'" class="text-secondary text-fw">'.$leave_date.'</a>',
                    "leave_amount" => '<a href="'.url('leave/document', $rec->leave_id).'" class="text-secondary text-fw">'.$leave_amount.'</a>',
                    "leave_type" => '<a href="'.url('leave/document', $rec->leave_id).'" class="text-secondary text-fw">'.$rec->leave_type_name.'</a>',
                    "leave_status" => '<a href="'.url('leave/document', $rec->leave_id).'" class="text-secondary text-fw">'.$leave_status.'</a>',
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
        return view('leave.approve.emp-attendance');
    }

    public function emp_attendance_search(Request $request)
    {
        if ($request->ajax()) {
            $depts = self::getDepartmentToArray();
            $users = self::getAuthorizeLevelUsers();
            if ($users->isNotEmpty()) {
                $totalRecords = $users->count();
                $records = $users;
                $rows = [];
                $n = 1;
                foreach ($records as $rec) {
                    $area_code = '';
                    if ($rec->area_code != "") {
                        $area_code = ' <small class="text-pink"><i>('.$rec->area_code.')</i></small>';
                    }
                    $type = self::get_emp_type($rec->emp_type);
                    $emp_type = '<span class="badge ' . $type["badge"] . '">' . $type["name"] . '</span>';
                    $status = self::get_emp_status($rec->emp_status);
                    $emp_status = '<span class="badge ' . $status["badge"] . '">' . $status["name"] . '</span>';
                    $dept_arr = self::callDepartment($rec->level, $rec->dept_id, $depts);
                    $rows[] = array(
                        "no" => $n,
                        "emp_id" => '<b>'.$rec->emp_id.'</b>',
                        "emp_name" => self::callUserName($rec->image, $rec->name, $rec->surname, $rec->nickname),
                        "emp_level1" => $dept_arr["level1"]["name"],
                        "emp_level2" => $dept_arr["level2"]["name"],
                        "emp_level3" => $dept_arr["level3"]["name"],
                        "emp_level4" => $dept_arr["level4"]["name"],
                        "emp_position" => $rec->position_name . $area_code,
                        "emp_type" => $emp_type,
                        "emp_status" => $emp_status,
                        "action" => '<a class="action-icon" href="'.url('leave/approve/emp-attendance-log/id', $rec->emp_id).'" title="ดู"><i class="mdi mdi-eye"></i></a>',
                    );
                    $n++;
                }
            } else {
                $totalRecords = 0;
                $rows = [];
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
        return view('leave.approve.emp-attendance-log')->with('emp_id', $id)->with('result', $result)->with('attendance_latest', $attendance_latest);
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
                })->select('w.*', 'e.name', 'e.surname')->orderBy('w.datetime', 'ASC')->get();
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
            return array_reverse($Sorted);
            // return $Sorted;
        }
        return $Array;
    }

    public function emp_leave_history(Request $request)
    {
        $leave_type = LeaveType::where('leave_type_status', '=', 1)->orderBy('leave_type_id', 'ASC')->get();
        $period_salary = PeriodSalary::where('year', '=', date('Y'))->get();
        $leave_status = self::get_leave_status();
        return view('leave.approve.emp-leave-history', compact('leave_type', 'period_salary', 'leave_status'));
    }

    public function emp_leave_history_search(Request $request)
    {
        if ($request->ajax()) {
            $leave_id = [];
            $auth = auth()->user();
            $users = self::getAuthorizeLevelUsers();
            if ($users->isNotEmpty()) {
                foreach ($users as $users) {
                    $leave = Leave::whereIn('leave_status', ['P','A1','A2','S'])->where('emp_id', '=', $users->emp_id)->orderBy('leave_id', 'asc')->get();
                    if ($leave->isNotEmpty()) {
                        foreach ($leave as $leave) {
                            $leave_id[] = $leave->leave_id;
                        }
                    }
                }
                $leave = Leave::whereIn('leave_status', ['P','A1','A2','S'])->where('emp_id', '=', $auth->emp_id)->orderBy('leave_id', 'asc')->get();
                if ($leave->isNotEmpty()) {
                    foreach ($leave as $leave) {
                        $leave_id[] = $leave->leave_id;
                    }
                }
            }
            $data = DB::table('report_leave_view')->whereIn('leave_status', ['P','A1','A2','S'])->whereIn('leave_id', $leave_id)
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
                $nickname = '';
                if ($rec->nickname != "") {
                    $nickname = ' ('.$rec->nickname.')';
                }
                $area_code = '';
                if ($rec->area_code != "") {
                    $area_code = ' ('.$rec->area_code.')';
                }
                $type = self::get_emp_type($rec->emp_type);
                $emp_id = '<span class="text-' . $type["color"] . '">' . $rec->emp_id . '</span>';
                if ($rec->leave_start_date == $rec->leave_end_date) {
                    $leave_date = Carbon::parse($rec->leave_start_date)->thaidate('d/m/Y');
                } else {
                    $leave_date = Carbon::parse($rec->leave_start_date)->thaidate('d/m/Y') . ' ถึง ' . Carbon::parse($rec->leave_end_date)->thaidate('d/m/Y');
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
                    "emp_id" => $emp_id,
                    "name" => '<a href="'.url('leave/document', $rec->leave_id).'" class="text-dark"><i class="mdi mdi-file-document-outline text-primary"></i>' . $rec->name . ' ' . $rec->surname . $nickname . '</a>',
                    "dept_id" => $rec->dept_id,
                    "dept_name" => $rec->dept_name . $area_code,
                    "leave_type" => $rec->leave_type_name,
                    "create_date" => Carbon::parse($rec->created_at)->thaidate('d/m/Y'),
                    "leave_date" => $leave_date,
                    "leave_amount" => $leave_amount,
                    "leave_status" => $leave_status,
                    "leave_reason" => $rec->leave_reason,
                    "created_at" => $rec->created_at,
                    "updated_at" => $rec->updated_at,
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

    public function emp_leave_form($emp_id)
    {
        $auth = Auth::user();
        $auth_emp = [];
        $users = self::getAuthorizeUsers();
        if ($users->isNotEmpty()) {
            foreach ($users as $users) {
                $auth_emp[] = $users->emp_id;
            }
        }
        $user = DB::table('employee as e')->leftJoin('department as d', 'd.dept_id', '=', 'e.dept_id')->leftJoin('position as p', 'p.position_id', '=', 'e.position_id')
        ->where('e.emp_id', '=', $emp_id)->select('e.*', 'd.dept_name', 'p.position_name')->first();

        if ($user->emp_type != "D") {
            alert()->warning('สามารถลางานให้พนักงานได้เฉพาะ "รายวัน" เท่านั้น!');
            return back();
        }
        if (!in_array($emp_id, $auth_emp)) {
            alert()->warning('ไม่มีสิทธิ์แก้ไขใบลานี้!');
            return back();
        }

        $dept_level = self::getDeptLevel($user->dept_id);
        $current_date = date('Y-m-d');
        $period = PeriodSalary::where('start', '<=', $current_date)->where('end', '>=', $current_date)->first();
        $previous_date = date("Y-m-d", strtotime("-7 day",strtotime($period->start)));
        $pre_period = PeriodSalary::where('start', '<=', $previous_date)->where('end', '>=', $previous_date)->first();

        $leave_type = LeaveType::where('leave_type_status', '=', 1)
        ->where(function ($query) use ($user) {
            if ($user->emp_type == 'D') {
                $query->where('leave_type_daily', '=', 1);
            } else if ($user->emp_type == 'M') {
                $query->where('leave_type_monthly', '=', 1);
            }
        })->orderBy('leave_type_id', 'ASC')->get();

        $work_date = self::calcYearsMonthsDaysDiffBetweenTwoDates($user->start_work_date, date('Y-m-d'));
        $hol_arr = implode(",", self::getHolidayDate());
        $record_working_arr = implode(",", self::getRecordWorkingDate());

        return view('leave.approve.emp-leave-form', compact('user', 'dept_level', 'period', 'pre_period', 'leave_type', 'work_date', 'hol_arr', 'record_working_arr'));
    }

    public function emp_leave_store(Request $request)
    {
        $auth = auth()->user();
        $leave_mode = 2; // หัวหน้าบันทึกให้พนักงาน
        $emp_id = $request->emp_id;
        $leader_id = $auth->emp_id;
        $leave_type = $request->leave_type;
        $leave_reason = $request->leave_reason;
        $leave_range = $request->leave_range;
        $date_start = $request->date_start;
        $date_end = $request->date_end;
        $time_start = $request->time_start;
        $time_end = $request->time_end;
        $leave_start_date = null;
        $leave_end_date = null;
        $leave_start_time = Carbon::parse('00:00:00')->format('H:i:s');
        $leave_end_time = Carbon::parse('00:00:00')->format('H:i:s');

        if ($leave_range == "full") {
            // ลาเต็มวัน
            $leave_start_date = Carbon::createFromFormat('d/m/Y', $date_start)->format('Y-m-d');
            $leave_end_date = $leave_start_date;
            $leave_day = 1;
            $leave_minute = 0;
        } else if ($leave_range == "many") {
            // ลาหลายวัน
            $leave_start_date = Carbon::createFromFormat('d/m/Y', $date_start)->format('Y-m-d');
            $leave_end_date = Carbon::createFromFormat('d/m/Y', $date_end)->format('Y-m-d');
            $calc_date = self::calc_date_diff($leave_start_date, $leave_end_date);
            if ($calc_date === false) {
                $request->flash();
                alert()->warning('กรุณาเลือกวันที่ลาให้ถูกต้อง!', 'วันที่ลาเริ่มต้นต้องน้อยกว่าวันที่ลาสิ้นสุด');
                return back();
            }
            $leave_day = (int)$request->sum_day;
            $leave_minute = 0;
        } else if ($leave_range == "etc") {
            // ลาเป็นชั่วโมง, นาที
            $leave_start_date = Carbon::createFromFormat('d/m/Y', $date_start)->format('Y-m-d');
            $leave_end_date = $leave_start_date;
            $leave_start_time = Carbon::parse($time_start)->format('H:i:s');
            $leave_end_time = Carbon::parse($time_end)->format('H:i:s');
            $calc_time = self::calc_time_diff($leave_start_date." ".$leave_start_time, $leave_end_date." ".$leave_end_time);
            if ($calc_time === false) {
                $request->flash();
                alert()->warning('กรุณาเลือกเวลาที่ลาให้ถูกต้อง!', 'เวลาที่ลาเริ่มต้นต้องน้อยกว่าเวลาที่ลาสิ้นสุด');
                return back();
            }
            $leave_day = 0;
            $leave_minute = ($request->sum_hour * 60) + $request->sum_minute;
        }
        $emp = self::getEmployee($emp_id);
        if ($emp === false) {
            $request->flash();
            alert()->warning('ไม่พบข้อมูลพนักงาน!');
            return back();
        }
        if ($emp->dept_id == "0") {
            $request->flash();
            alert()->warning('ไม่พบแผนก/หน่วยงานของผู้ลางาน!');
            return back();
        }
        $leave_attach = null;
        if ($leave_type == "1") {
            // ลากิจ
            $chkLeaveLimit = self::checkLeaveLimitPerYear(2, $leave_start_date, $emp_id, 0, $leave_type, $leave_day, $leave_minute);
            if ($chkLeaveLimit !== true) {
                $request->flash();
                alert()->warning($chkLeaveLimit['title'], $chkLeaveLimit['message'])->autoClose(false);
                return back();
            }
        } else if ($leave_type == "2" || $leave_type == "3") {
            // ลาป่วย
            if ($leave_type == "2") {
                // ลาป่วยแนบใบรับรองแพทย์
                if ($request->hasFile('attach_file')) {
                    $file = $request->file('attach_file');
                    $fileName = time();
                    $leave_attach = $fileName . '.' . $file->extension();
                    $file->move($this->attathPath, $leave_attach);
                } else {
                    $request->flash();
                    alert()->warning('โปรดแนบใบรับรองแพทย์!');
                    return back();
                }
            }
            $chkLeaveLimit = self::checkLeaveLimitPerYear(3, $leave_start_date, $emp_id, 0, $leave_type, $leave_day, $leave_minute);
            if ($chkLeaveLimit !== true) {
                $request->flash();
                alert()->warning($chkLeaveLimit['title'], $chkLeaveLimit['message'])->autoClose(false);
                return back();
            }
        } else if ($leave_type == "4") {
            // ลาเร่งด่วน
            $check = self::checkUrgentLeavePerMonth(0, $emp_id, $leave_start_date);
            if ($check !== true) {
                $request->flash();
                alert()->warning($check['title'], $check['message'])->autoClose(false);
                return back();
            }
        } else if ($leave_type == "6") {
            // ลาหยุดชดเชย
            $record_working = RecordWorking::where('emp_id', '=', $emp_id)->where('use_status', '=', 1)->orderBy('work_date', 'ASC')->get();
            if ($record_working->isEmpty()) {
                $request->flash();
                alert()->warning('ไม่มีวันหยุดที่สามารถใช้งานได้!');
                return back();
            } else {
                if (strtotime(date('Y-m-d', strtotime($leave_start_date))) < strtotime(date('Y-m-d', strtotime($record_working[0]->work_date)))) {
                    alert()->warning('โปรดตรวจสอบเงื่อนไขลาหยุดชดเชย!', 'สามารถลาได้หลังจากวันที่ '.Carbon::createFromFormat('Y-m-d', $record_working[0]->work_date)->format('d/m/Y').' เป็นต้นไป')->autoclose(false);
                    return back();
                }
            }
        } else if ($leave_type == "7") {
            // ลาพักร้อน
            $chkLeaveLimit = self::checkLeaveLimitPerYear(1, $leave_start_date, $emp_id, 0, $leave_type, $leave_day, $leave_minute);
            if ($chkLeaveLimit !== true) {
                $request->flash();
                alert()->warning($chkLeaveLimit['title'], $chkLeaveLimit['message'])->autoClose(false);
                return back();
            }
        } else if ($leave_type == "9") {
            // ลาคลอด
            // แนบใบรับรองแพทย์
            if ($request->hasFile('attach_file')) {
                $file = $request->file('attach_file');
                $fileName = time();
                $leave_attach = $fileName . '.' . $file->extension();
                $file->move($this->attathPath, $leave_attach);
            }
        }
        $period = self::getPeriodLeave($leave_start_date, $leave_end_date);
        if (!$period) {
            $request->flash();
            alert()->warning('ไม่พบงวดค่าแรงในวันที่ลา!');
            return back();
        }
        $period_salary_id = $period->id;
        $leave_status = "P";
        $approveM = self::getEmpApproveManager($emp_id, $emp->dept_id);
        if ($approveM !== false) {
            $approve_lid = $approveM["emp_id"];
            $approve_lip = $request->ip();
            $approve_ldate = now();
            $approve_mid = $approveM["emp_id"];
            $approve_mip = $request->ip();
            $approve_mdate = now();
            $leave_status = "A2";
        } else {
            $approve_mid = null;
            $approve_mip = null;
            $approve_mdate = null;
            $approveL = self::getEmpApproveLeader($emp_id, $emp->dept_id);
            if ($approveL !== false) {
                $approve_lid = $approveL["emp_id"];
                $approve_lip = $request->ip();
                $approve_ldate = now();
                $leave_status = "A1";
            } else {
                $approve_lid = null;
                $approve_lip = null;
                $approve_ldate = null;
            }
        }

        try {
            self::_addLeave($leave_start_date, $leave_start_time, $leave_end_date, $leave_end_time, $leave_reason, $leave_attach, $leave_day, $leave_minute, $leave_type, $period_salary_id, $leave_range, $leave_mode, $leader_id, $emp_id, $emp->emp_type, $approve_lid, $approve_lip, $approve_ldate, $approve_mid, $approve_mip, $approve_mdate, $leave_status);
            $leave_id = self::getLeaveIdLatest();
            if ($leave_type == "6") {
                // ลาหยุดชดเชย
                $record_working = RecordWorking::where('emp_id', '=', $emp_id)->where('use_status', '=', 1)->orderBy('work_date', 'ASC')->first();
                if ($record_working) {
                    $leaveS = RecordWorking::find($record_working->id);
                    $leaveS->update([
                        "use_status" => 2,
                        "leave_id" => $leave_id,
                    ]);
                }
            }
            self::addLeaveLog($leave_id, "สร้างใบลางาน (โดยหัวหน้า)", auth()->user()->emp_id, $request->ip());
            if ($leave_status == "A1" || $leave_status == "A2") {
                self::addLeaveLog($leave_id, "อนุมัติใบลางาน (โดยหัวหน้า)", auth()->user()->emp_id, $request->ip());
                if ($leave_status == "A2") {
                    self::addLeaveLog($leave_id, "อนุมัติใบลางาน (โดยผู้จัดการ)", auth()->user()->emp_id, $request->ip());
                }
            }
            self::leavePushNotification($leave_id, $emp_id, $emp->dept_id, $emp->name.' '.$emp->surname);
        } catch (\Exception $e) {
            return view('errors.500')->withErrors(['เกิดข้อผิดพลาด!']);
        }

        alert()->success('บันทึกลางานเรียบร้อย');
        return redirect('leave/approve/dashboard');
    }

    public function _addLeave($leave_start_date, $leave_start_time, $leave_end_date, $leave_end_time, $leave_reason, $leave_attach, $leave_day, $leave_minute, $leave_type, $period_salary_id, $leave_range, $leave_mode, $leader_id, $emp_id, $emp_type, $approve_lid, $approve_lip, $approve_ldate, $approve_mid, $approve_mip, $approve_mdate, $leave_status)
    {
        Leave::create([
            "leave_start_date" => $leave_start_date,
            "leave_start_time" => $leave_start_time,
            "leave_end_date" => $leave_end_date,
            "leave_end_time" => $leave_end_time,
            "leave_reason" => $leave_reason,
            "leave_attach" => $leave_attach,
            "leave_day" => $leave_day,
            "leave_minute" => $leave_minute,
            "leave_type_id" => $leave_type,
            "period_salary_id" => $period_salary_id,
            "leave_range" => $leave_range,
            "leave_mode" => $leave_mode,
            "leader_id" => $leader_id,
            "emp_id" => $emp_id,
            "emp_type" => $emp_type,
            "approve_lid" => $approve_lid,
            "approve_lip" => $approve_lip,
            "approve_ldate" => $approve_ldate,
            "approve_mid" => $approve_mid,
            "approve_mip" => $approve_mip,
            "approve_mdate" => $approve_mdate,
            "leave_status" => $leave_status
        ]);
    }

    public function emp_leave_edit($id)
    {
        $auth = Auth::user();
        $auth_emp = [];
        $users = self::getAuthorizeUsers();
        if ($users->isNotEmpty()) {
            foreach ($users as $users) {
                $auth_emp[] = $users->emp_id;
            }
        }
        $leave = Leave::where('leave_id', '=', $id)->first();
        $user = DB::table('employee as e')->leftJoin('department as d', 'd.dept_id', '=', 'e.dept_id')->leftJoin('position as p', 'p.position_id', '=', 'e.position_id')
        ->where('e.emp_id', '=', $leave->emp_id)->select('e.*', 'd.dept_name', 'p.position_name')->first();

        if ($user->emp_type != "D") {
            alert()->warning('สามารถลางานให้พนักงานได้เฉพาะ "รายวัน" เท่านั้น!');
            return back();
        }

        $dept_level = self::getDeptLevel($user->dept_id);
        $current_date = date('Y-m-d');
        $period = PeriodSalary::where('start', '<=', $current_date)->where('end', '>=', $current_date)->first();
        $previous_date = date("Y-m-d", strtotime("-7 day",strtotime($period->start)));
        $pre_period = PeriodSalary::where('start', '<=', $previous_date)->where('end', '>=', $previous_date)->first();

        $leave_type = LeaveType::where('leave_type_status', '=', 1)
        ->where(function ($query) use ($user) {
            if ($user->emp_type == 'D') {
                $query->where('leave_type_daily', '=', 1);
            } else if ($user->emp_type == 'M') {
                $query->where('leave_type_monthly', '=', 1);
            }
        })->orderBy('leave_type_id', 'ASC')->get();

        $leave_type2 = LeaveType::where('leave_type_id', '=', $leave->leave_type_id)->first();
        $leave_type_detail = $leave_type2->leave_type_detail;

        $sum_time = self::minutesToTime($leave->leave_minute);
        $sum_time["d"] = $leave->leave_day;

        $work_date = self::calcYearsMonthsDaysDiffBetweenTwoDates($user->start_work_date, date('Y-m-d'));
        $hol_arr = implode(",", self::getHolidayDate());
        $record_working_arr = implode(",", self::getRecordWorkingDate());

        $record_working = RecordWorking::where('emp_id', '=', $leave->emp_id)->where('use_status', '=', 2)->where('leave_id', '=', $leave->leave_id)->first();

        if (!in_array($leave->emp_id, $auth_emp)) {
            alert()->warning('ไม่มีสิทธิ์แก้ไขใบลานี้!');
            return back();
        }
        //================= Fix ห้ามแก้ไขให้คนอื่นๆ ====================
        alert()->warning('ไม่มีสิทธิ์แก้ไขใบลานี้!');
        return back();

        return view('leave.approve.emp-leave-edit', compact('user', 'dept_level', 'period', 'pre_period', 'leave', 'leave_type', 'leave_type_detail', 'sum_time', 'work_date', 'hol_arr', 'record_working_arr', 'record_working'));
    }

    public function emp_leave_update(Request $request)
    {
        $auth = auth()->user();
        $leave_mode = $request->leave_mode;
        $emp_id = $request->emp_id;
        $dept_id = $request->dept_id;
        $leader_id = $request->leader_id;
        $leave_id = $request->leave_id;
        $leave_type = $request->leave_type;
        $leave_reason = $request->leave_reason;
        $leave_range = $request->leave_range;
        $date_start = $request->date_start;
        $date_end = $request->date_end;
        $time_start = $request->time_start;
        $time_end = $request->time_end;
        $leave_start_date = null;
        $leave_end_date = null;
        $leave_start_time = Carbon::parse('00:00:00')->format('H:i:s');
        $leave_end_time = Carbon::parse('00:00:00')->format('H:i:s');

        $leave = Leave::where('leave_id', '=', $leave_id)->first();

        $approveM = self::chkEmpApproveManager($emp_id, $dept_id);
        if ($approveM !== false) {
            if ($leave->leave_status == "S") {
                alert()->warning('ใบลาถูกอนุมัติโดยบุคคลแล้ว!', 'ไม่สามารถแก้ไขได้');
                return back();
            }
        } else {
            $approveL = self::chkEmpApproveLeader($emp_id, $dept_id);
            if ($approveL !== false) {
                if ($leave->leave_status == "A2") {
                    alert()->warning('ใบลาถูกอนุมัติโดยผู้จัดการแล้ว!', 'ไม่สามารถแก้ไขได้');
                    return back();
                } else if ($leave->leave_status == "S") {
                    alert()->warning('ใบลาถูกอนุมัติโดยบุคคลแล้ว!', 'ไม่สามารถแก้ไขได้');
                    return back();
                }
            }
        }
        if ($leave->leave_status == "C1" || $leave->leave_status == "C2" || $leave->leave_status == "C3") {
            alert()->warning('สถานะใบลาถูกยกเลิกแล้ว!', 'ไม่สามารถแก้ไขได้');
            return back();
        }

        if ($leave_range == "full") {
            // ลาเต็มวัน
            $leave_start_date = Carbon::createFromFormat('d/m/Y', $date_start)->format('Y-m-d');
            $leave_end_date = $leave_start_date;
            $leave_day = 1;
            $leave_minute = 0;
        } else if ($leave_range == "many") {
            // ลาหลายวัน
            $leave_start_date = Carbon::createFromFormat('d/m/Y', $date_start)->format('Y-m-d');
            $leave_end_date = Carbon::createFromFormat('d/m/Y', $date_end)->format('Y-m-d');
            $calc_date = self::calc_date_diff($leave_start_date, $leave_end_date);
            if ($calc_date === false) {
                alert()->warning('กรุณาเลือกวันที่ลาให้ถูกต้อง!', 'วันที่ลาเริ่มต้นต้องน้อยกว่าวันที่ลาสิ้นสุด');
                return back();
            }
            $leave_day = (int)$request->sum_day;
            $leave_minute = 0;
        } else if ($leave_range == "etc") {
            // ลาเป็นชั่วโมง, นาที
            $leave_start_date = Carbon::createFromFormat('d/m/Y', $date_start)->format('Y-m-d');
            $leave_end_date = $leave_start_date;
            $leave_start_time = Carbon::parse($time_start)->format('H:i:s');
            $leave_end_time = Carbon::parse($time_end)->format('H:i:s');
            $calc_time = self::calc_time_diff($leave_start_date." ".$leave_start_time, $leave_end_date." ".$leave_end_time);
            if ($calc_time === false) {
                alert()->warning('กรุณาเลือกเวลาที่ลาให้ถูกต้อง!', 'เวลาที่ลาเริ่มต้นต้องน้อยกว่าเวลาที่ลาสิ้นสุด');
                return back();
            }
            $leave_day = 0;
            $leave_minute = ($request->sum_hour * 60) + $request->sum_minute;
        }
        $emp = self::getEmployee($emp_id);
        if ($emp === false) {
            alert()->warning('ไม่พบข้อมูลพนักงาน!');
            return back();
        }
        if ($emp->dept_id == "0") {
            alert()->warning('ไม่พบแผนก/หน่วยงานของผู้ลางาน!');
            return back();
        }
        $leave_attach = null;
        if ($leave_type == "1") {
            // ลากิจ
            $chkLeaveLimit = self::checkLeaveLimitPerYear(2, $leave_start_date, $emp_id, $leave_id, $leave_type, $leave_day, $leave_minute);
            if ($chkLeaveLimit !== true) {
                $request->flash();
                alert()->warning($chkLeaveLimit['title'], $chkLeaveLimit['message'])->autoClose(false);
                return back();
            }
        } else if ($leave_type == "2" || $leave_type == "3") {
            // ลาป่วย
            if ($leave_type == "2") {
                $attach_file_old = $request->attach_file_old;
                if ($attach_file_old != "") {
                    if (File::exists($this->attathPath . $attach_file_old)) {
                        $leave_attach = $attach_file_old;
                    }
                }
                if ($request->hasFile('attach_file')) {
                    $file = $request->file('attach_file');
                    $fileName = time();
                    $leave_attach = $fileName . '.' . $file->extension();
                    $file->move($this->attathPath, $leave_attach);
                } else {
                    alert()->warning('โปรดแนบใบรับรองแพทย์!');
                    return back();
                }
            }
            $chkLeaveLimit = self::checkLeaveLimitPerYear(3, $leave_start_date, $emp_id, $leave_id, $leave_type, $leave_day, $leave_minute);
            if ($chkLeaveLimit !== true) {
                $request->flash();
                alert()->warning($chkLeaveLimit['title'], $chkLeaveLimit['message'])->autoClose(false);
                return back();
            }
        } else if ($leave_type == "4") {
            // ลาเร่งด่วน
            $check = self::checkUrgentLeavePerMonth($leave_id, $emp_id, $leave_start_date);
            if ($check !== true) {
                $request->flash();
                alert()->warning($check['title'], $check['message'])->autoClose(false);
                return back();
            }
        } else if ($leave_type == "6") {
            // ลาหยุดชดเชย
            if ($leave->leave_type_id != "6") {
                // กรณีเปลี่ยนประเภทลางาน (อย่างอื่น => หยุดชดเชย)
                $record_working = RecordWorking::where('emp_id', '=', $emp_id)->where('use_status', '=', 1)->orderBy('work_date', 'ASC')->get();
                if ($record_working->isEmpty()) {
                    alert()->warning('ไม่มีวันหยุดที่สามารถใช้งานได้!');
                    return back();
                } else {
                    if (strtotime(date('Y-m-d', strtotime($leave_start_date))) < strtotime(date('Y-m-d', strtotime($record_working[0]->work_date)))) {
                        alert()->warning('โปรดตรวจสอบเงื่อนไขลาหยุดชดเชย!', 'สามารถลาได้หลังจากวันที่ '.Carbon::createFromFormat('Y-m-d', $record_working[0]->work_date)->format('d/m/Y').' เป็นต้นไป')->autoclose(false);
                        return back();
                    }
                }
            } else {
                // เรียกข้อมูลที่เคยบันทึก
                $record_working = RecordWorking::where('emp_id', '=', $emp_id)->where('leave_id', '=', $leave_id)->orderBy('work_date', 'ASC')->get();
                if ($record_working->isNotEmpty()) {
                    if (strtotime(date('Y-m-d', strtotime($leave_start_date))) < strtotime(date('Y-m-d', strtotime($record_working[0]->work_date)))) {
                        alert()->warning('โปรดตรวจสอบเงื่อนไขลาหยุดชดเชย!', 'สามารถลาได้หลังจากวันที่ '.Carbon::createFromFormat('Y-m-d', $record_working[0]->work_date)->format('d/m/Y').' เป็นต้นไป')->autoclose(false);
                        return back();
                    }
                }
            }
        } else if ($leave_type == "7") {
            // ลาพักร้อน
            $chkLeaveLimit = self::checkLeaveLimitPerYear(1, $leave_start_date, $emp_id, $leave_id, $leave_type, $leave_day, $leave_minute);
            if ($chkLeaveLimit !== true) {
                $request->flash();
                alert()->warning($chkLeaveLimit['title'], $chkLeaveLimit['message'])->autoClose(false);
                return back();
            }
        } else if ($leave_type == "9") {
            // ลาคลอด
            // แนบใบรับรองแพทย์
            if ($request->hasFile('attach_file')) {
                $attach_file_old = $request->attach_file_old;
                if ($attach_file_old != "") {
                    if (File::exists($this->attathPath . $attach_file_old)) {
                        $leave_attach = $attach_file_old;
                    }
                }
                if ($request->hasFile('attach_file')) {
                    $file = $request->file('attach_file');
                    $fileName = time();
                    $leave_attach = $fileName . '.' . $file->extension();
                    $file->move($this->attathPath, $leave_attach);
                }
            }
        }
        $period = self::getPeriodLeave($leave_start_date, $leave_end_date);
        if (!$period) {
            alert()->warning('ไม่พบงวดค่าแรงในวันที่ลา!');
            return back();
        }
        $period_salary_id = $period->id;

        try {
            self::_updateLeave($leave_id, $leave_start_date, $leave_start_time, $leave_end_date, $leave_end_time, $leave_reason, $leave_attach, $leave_day, $leave_minute, $leave_type, $period_salary_id, $leave_range, $leave_mode, $leader_id, $emp_id, $emp->emp_type);
            if ($leave_type == "6") {
                // ลาหยุดชดเชย
                if ($leave->leave_type_id != "6") {
                    // กรณีเปลี่ยนประเภทลางาน (อย่างอื่น => หยุดชดเชย)
                    $record_working = RecordWorking::where('emp_id', '=', $emp_id)->where('use_status', '=', 1)->orderBy('work_date', 'ASC')->first();
                    if ($record_working) {
                        $leaveS = RecordWorking::find($record_working->id);
                        $leaveS->update([
                            "use_status" => 2,
                            "leave_id" => $leave_id,
                        ]);
                    }
                }
            } else {
                if ($leave->leave_type_id == "6") {
                    // กรณีเปลี่ยนประเภทลางาน (หยุดชดเชย => อย่างอื่น)
                    $record_working = RecordWorking::where('emp_id', '=', $emp_id)->where('use_status', '=', 2)->where('leave_id', '=', $leave_id)->first();
                    if ($record_working) {
                        // กรณีที่หักวันหยุดไปแล้ว ต้องถอยสถานะวันหยุดกลับคืน
                        $leaveS = RecordWorking::find($record_working->id);
                        $leaveS->update([
                            "use_status" => 1,
                            "leave_id" => null,
                        ]);
                    }
                }
            }
            self::addLeaveLog($leave_id, "แก้ไขใบลางาน (โดยหัวหน้า)", auth()->user()->emp_id, $request->ip());
            if ($request->attach_file_old != "") {
                if ($request->hasFile('attach_file')) {
                    if( File::exists($this->attathPath.$request->attach_file_old) ) {
                        File::delete($this->attathPath.$request->attach_file_old);
                    }
                }
            } else {
                if ($leave->leave_attach != "") {
                    if( File::exists($this->attathPath.$leave->leave_attach) ) {
                        File::delete($this->attathPath.$leave->leave_attach);
                    }
                }
            }
        } catch (\Exception $e) {
            return view('errors.500')->withErrors(['เกิดข้อผิดพลาด!']);
        }

        alert()->success('อัปเดตใบลางานเรียบร้อย');
        return redirect('leave/approve/dashboard');
    }

    public function _updateLeave($leave_id, $leave_start_date, $leave_start_time, $leave_end_date, $leave_end_time, $leave_reason, $leave_attach, $leave_day, $leave_minute, $leave_type, $period_salary_id, $leave_range, $leave_mode, $leader_id, $emp_id, $emp_type)
    {
        $leave = Leave::where('leave_id', '=', $leave_id);
        $leave->update([
            "leave_start_date" => $leave_start_date,
            "leave_start_time" => $leave_start_time,
            "leave_end_date" => $leave_end_date,
            "leave_end_time" => $leave_end_time,
            "leave_reason" => $leave_reason,
            "leave_attach" => $leave_attach,
            "leave_day" => $leave_day,
            "leave_minute" => $leave_minute,
            "leave_type_id" => $leave_type,
            "period_salary_id" => $period_salary_id,
            "leave_range" => $leave_range,
            "leave_mode" => $leave_mode,
            "leader_id" => $leader_id,
            "emp_id" => $emp_id,
            "emp_type" => $emp_type
        ]);
    }

    public function emp_leave_approve($id)
    {
        $leave = Leave::where('leave_id', '=', $id)->first();
        if (!$leave) {
            alert()->warning('ไม่พบใบลานี้!');
            return back();
        }
        $leaveType = LeaveType::where('leave_type_status', '=', 1)->get();
        $periodSalary = PeriodSalary::find($leave->period_salary_id);
        $emp = DB::table('employee as e')->leftJoin('position as p', 'e.position_id', '=', 'p.position_id')->leftJoin('department as d', 'e.dept_id', '=', 'd.dept_id')
        ->where('e.emp_id', '=', $leave->emp_id)->select(['e.*', 'p.position_name', 'd.dept_name', 'd.level'])->first();
        $worked_days = self::worked_days($leave->emp_id);
        $depts = self::getDepartmentToArray();
        $dept_arr = self::callDepartment($emp->level, $emp->dept_id, $depts);
        $leaveEmp = self::getLeaveEmpDetail($leave->leave_id);
        $leaveLeader = self::getLeaveLeaderDetail($leave->leave_id);
        $approvedLeader = self::getApproveLeaderDetail($leave->leave_id);
        $approvedManager = self::getApproveManagerDetail($leave->leave_id);
        $approvedHR = self::getApproveHRDetail($leave->leave_id);
        $leaveMinutes = self::minutesToTime($leave->leave_minute);
        $rwRef = self::getRecordWorkingRef($leave->leave_id);
        $leaveLog = self::getLeaveLog($leave->leave_id);

        $auth_emp = [];
        $users = self::getAuthorizeUsers();
        if ($users->isNotEmpty()) {
            foreach ($users as $users) {
                $auth_emp[] = $users->emp_id;
            }
        }
        if (!in_array($leave->emp_id, $auth_emp)) {
            alert()->warning('ไม่มีสิทธิ์อนุมัติใบลานี้!');
            return back();
        }

        self::leaveUpdateNotification($leave->leave_id);

        $agent = new Agent();
        if ($agent->isMobile()) {
            $view = 'leave.approve.emp-leave-approve-mobile';
        } else {
            $view = 'leave.approve.emp-leave-approve';
        }
        return view($view, compact('leave', 'leaveType', 'periodSalary', 'emp', 'dept_arr', 'worked_days', 'leaveEmp', 'leaveLeader', 'approvedLeader', 'approvedManager', 'approvedHR', 'leaveMinutes', 'rwRef', 'leaveLog'));
    }

    public function emp_leave_approved(Request $request)
    {
        $leave = Leave::where('leave_id', '=', $request->leave_id)->first();
        if (!$leave) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบใบลางานนี้',
            ]);
        }
        $leave_id = $leave->leave_id;
        $emp_id = $leave->emp_id;
        $approve_lid = $leave->approve_lid;
        $approve_lip = $leave->approve_lip;
        $approve_ldate = $leave->approve_ldate;
        $approve_mid = $leave->approve_mid;
        $approve_mip = $leave->approve_mip;
        $approve_mdate = $leave->approve_mdate;
        $leave_status = $leave->leave_status;
        $desLog = "";

        $auth_emp = [];
        $users = self::getAuthorizeUsers();
        if ($users->isNotEmpty()) {
            foreach ($users as $users) {
                $auth_emp[] = $users->emp_id;
            }
        }
        if (!in_array($emp_id, $auth_emp)) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่มีสิทธิ์อนุมัติใบลานี้',
            ]);
        }
        if ($leave_status == "S") {
            return response()->json([
                'success' => false,
                'message' => 'ใบลาถูกอนุมัติโดยบุคคลแล้ว',
            ]);
        }
        if ($leave_status == "C1" || $leave_status == "C2" || $leave_status == "C3") {
            return response()->json([
                'success' => false,
                'message' => 'ใบลานี้ถูกยกเลิกไปแล้ว',
            ]);
        }

        $emp = self::getEmployee($emp_id);
        $approveM = self::getEmpApproveManager($emp_id, $emp->dept_id);
        $approveL = self::getEmpApproveLeader($emp_id, $emp->dept_id);
        if ($approveM !== false) {
            $approveL = self::getEmpApproveLeader($emp_id, $emp->dept_id);
            if ($approveL !== false) {
                $approve_lid = $approveM["emp_id"];
                $approve_lip = $request->ip();
                $approve_ldate = now();
            }
            $approve_mid = $approveM["emp_id"];
            $approve_mip = $request->ip();
            $approve_mdate = now();
            $leave_status = "A2";
            $desLog = "อนุมัติใบลางาน (โดยผู้จัดการ)";
        } else {
            $approveL = self::getEmpApproveLeader($emp_id, $emp->dept_id);
            if ($approveL !== false) {
                if ($leave_status == "A2") {
                    return response()->json([
                        'success' => false,
                        'message' => 'ใบลาถูกอนุมัติโดยผู้จัดการแล้ว',
                    ]);
                }
                $approve_lid = $approveL["emp_id"];
                $approve_lip = $request->ip();
                $approve_ldate = now();
                $leave_status = "A1";
                $desLog = "อนุมัติใบลางาน (โดยหัวหน้า)";
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่มีสิทธิ์อนุมัติใบลานี้',
                ]);
            }
        }
        try {
            $leave = Leave::where('leave_id', '=', $leave_id);
            $leave->update([
                "approve_lid" => $approve_lid,
                "approve_lip" => $approve_lip,
                "approve_ldate" => $approve_ldate,
                "approve_mid" => $approve_mid,
                "approve_mip" => $approve_mip,
                "approve_mdate" => $approve_mdate,
                "leave_status" => $leave_status,
            ]);

            self::addLeaveLog($leave_id, $desLog, auth()->user()->emp_id, $request->ip());
            self::leaveRemoveNotification($leave_id);
            self::leavePushNotification($leave_id, $emp_id, $emp->dept_id, $emp->name.' '.$emp->surname);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'เกิดข้อผิดพลาด!']);
        }
        return response()->json(['success' => true, 'message' => 'อนุมัติใบลางานเรียบร้อย']);
    }

    public function leave_approve_submit(Request $request)
    {
        $data = $request->leave_id;
        if (count($data) <= 0) {
            return response()->json(['success' => false, 'message' => 'ไม่พบข้อมูล!']);
        }
        for ($i=0; $i<count($data); $i++) {
            $chk = Leave::where('leave_id', '=', $data[$i])->first();
            if ($chk) {
                $approve_lid = $chk->approve_lid;
                $approve_lip = $chk->approve_lip;
                $approve_ldate = $chk->approve_ldate;
                $approve_mid = $chk->approve_mid;
                $approve_mip = $chk->approve_mip;
                $approve_mdate = $chk->approve_mdate;
                $leave_status = $chk->leave_status;
                $desLog = "";
                $emp = self::getEmployee($chk->emp_id);
                $approveM = self::getEmpApproveManager($chk->emp_id, $emp->dept_id);
                if ($approveM !== false) {
                    $approveL = self::getEmpApproveLeader($chk->emp_id, $emp->dept_id);
                    if ($approveL !== false) {
                        $approve_lid = $approveM["emp_id"];
                        $approve_lip = $request->ip();
                        $approve_ldate = now();
                    }
                    $approve_mid = $approveM["emp_id"];
                    $approve_mip = $request->ip();
                    $approve_mdate = now();
                    $leave_status = "A2";
                    $desLog = "อนุมัติใบลางาน (โดยผู้จัดการ)";
                } else {
                    $approveL = self::getEmpApproveLeader($chk->emp_id, $emp->dept_id);
                    if ($approveL !== false) {
                        $approve_lid = $approveL["emp_id"];
                        $approve_lip = $request->ip();
                        $approve_ldate = now();
                        $leave_status = "A1";
                        $desLog = "อนุมัติใบลางาน (โดยหัวหน้า)";
                    }
                }
                $leave = Leave::where('leave_id', '=', $data[$i]);
                $leave->update([
                    "approve_lid" => $approve_lid,
                    "approve_lip" => $approve_lip,
                    "approve_ldate" => $approve_ldate,
                    "approve_mid" => $approve_mid,
                    "approve_mip" => $approve_mip,
                    "approve_mdate" => $approve_mdate,
                    "leave_status" => $leave_status,
                ]);

                self::addLeaveLog($data[$i], $desLog, auth()->user()->emp_id, $request->ip());
                self::leaveRemoveNotification($data[$i]);
                self::leavePushNotification($data[$i], $chk->emp_id, $emp->dept_id, $emp->name.' '.$emp->surname);
            }
        }
        return response()->json(['success' => true, 'message' => 'อนุมัติการลางานเรียบร้อย']);
    }

    public function emp_leave_cancel(Request $request)
    {
        $leave = Leave::where('leave_id', '=', $request->leave_id)->first();
        if ($leave) {
            $emp = self::getEmployee($leave->emp_id);
            $approve_lid = $leave->approve_lid;
            $approve_lip = $leave->approve_lip;
            $approve_ldate = $leave->approve_ldate;
            $approve_mid = $leave->approve_mid;
            $approve_mip = $leave->approve_mip;
            $approve_mdate = $leave->approve_mdate;
            $leave_cancel_remark = $leave->leave_cancel_remark;
            $leave_status = $leave->leave_status;
            $desLog = "";
            $approveM = self::getEmpApproveManager($leave->emp_id, $emp->dept_id);
            if ($approveM !== false) {
                if ($leave->leave_status == "S") {
                    return response()->json([
                        'success' => false,
                        'message' => 'ใบลาถูกอนุมัติโดยบุคคลแล้ว',
                    ]);
                }
                $approve_mid = $approveM["emp_id"];
                $approve_mip = $request->ip();
                $approve_mdate = now();
                $leave_cancel_remark = 'ยกเลิกโดยผู้จัดการ';
                $leave_status = "C2";
                $desLog = "ยกเลิกใบลางาน (โดยผู้จัดการ)";
            } else {
                $approveL = self::getEmpApproveLeader($leave->emp_id, $emp->dept_id);
                if ($approveL !== false) {
                    if ($leave->leave_status == "A2") {
                        return response()->json([
                            'success' => false,
                            'message' => 'ใบลาถูกอนุมัติโดยผู้จัดการแล้ว',
                        ]);
                    }
                    $approve_lid = $approveL["emp_id"];
                    $approve_lip = $request->ip();
                    $approve_ldate = now();
                    $leave_cancel_remark = 'ยกเลิกโดยหัวหน้า';
                    $leave_status = "C1";
                    $desLog = "ยกเลิกใบลางาน (โดยหัวหน้า)";
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'ไม่มีสิทธิ์ยกเลิกใบลานี้',
                    ]);
                }
            }
            try {
                $leaveUpdate = Leave::where('leave_id', '=', $request->leave_id);
                $leaveUpdate->update([
                    "approve_lid" => $approve_lid,
                    "approve_lip" => $approve_lip,
                    "approve_ldate" => $approve_ldate,
                    "approve_mid" => $approve_mid,
                    "approve_mip" => $approve_mip,
                    "approve_mdate" => $approve_mdate,
                    "leave_status" => $leave_status,
                    "leave_cancel_remark" => $request->cancel_remark,
                ]);
                $record_working = RecordWorking::where('emp_id', '=', $leave->emp_id)->where('use_status', '=', 2)->where('leave_id', '=', $request->leave_id)->first();
                if ($record_working) {
                    // ถอยสถานะวันหยุดกลับคืน
                    $leaveS = RecordWorking::find($record_working->id);
                    $leaveS->update([
                        "use_status" => 1,
                        "leave_id" => null,
                    ]);
                }
                self::addLeaveLog($request->leave_id, $desLog, auth()->user()->emp_id, $request->ip());
                self::leaveRemoveNotification($request->leave_id);
                self::leaveCancelPushNotification($request->leave_id);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'เกิดข้อผิดพลาด!']);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'ไม่พบใบลา!']);
        }
        return response()->json(['success' => true, 'message' => 'ยกเลิกเรียบร้อย']);
    }

    public function search_emp(Request $request)
    {
        if ($request->ajax()) {
            $auth_emp = [];
            $users = self::getAuthorizeUsers();
            if ($users->isNotEmpty()) {
                foreach ($users as $users) {
                    $auth_emp[] = $users->emp_id;
                }
            }
            $result = DB::table('employee as e')->leftJoin('department as d', 'e.dept_id', '=', 'd.dept_id')
            ->where('e.emp_id', '<>', '')->whereIn('e.emp_id', $auth_emp)
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

    // ======================================= บันทึกวันทำงาน ========================================
    public function pendingRecordWorking()
    {
        // รออนุมัติ
        $emp_id = [];
        $users = self::getAuthorizeUsers();
        if ($users->isNotEmpty()) {
            foreach ($users as $users) {
                $leave = RecordWorking::whereIn('approve_status', ['P', 'A1'])->where('emp_id', '=', $users->emp_id)->orderBy('id', 'asc')->get();
                if ($leave->isNotEmpty()) {
                    foreach ($leave as $leave) {
                        $cau = self::chkEmpApproveRecordWorking($leave->emp_id, $users->dept_id, $leave->approve_status);
                        if ($cau) {
                            $emp_id[] = $users->emp_id;
                            break;
                        }
                    }
                }
            }
        }
        $emp_id = array_unique($emp_id);

        $data = DB::table('record_working as l')->leftJoin('employee as e', 'l.emp_id', '=', 'e.emp_id')->leftJoin('department as d', 'e.dept_id', '=', 'd.dept_id')
        ->whereIn('l.approve_status', ['P', 'A1'])->whereIn('l.emp_id', $emp_id)->select('l.*', 'e.name', 'e.surname', 'e.image', 'd.dept_name')->orderBy('l.id', 'asc')->get();
        return $data;
    }

    public function approvedRecordWorking()
    {
        // อนุมัติแล้ว
        $auth = auth()->user();
        $emp_id = [];
        $users = self::getAuthorizeUsers();
        if ($users->isNotEmpty()) {
            foreach ($users as $users) {
                $emp_id[] = $users->emp_id;
            }
        }

        $data = DB::table('record_working as l')->leftJoin('employee as e', 'l.emp_id', '=', 'e.emp_id')->leftJoin('department as d', 'e.dept_id', '=', 'd.dept_id')
        ->whereIn('l.approve_status', ['A1', 'A2', 'S'])->whereIn('l.emp_id', $emp_id)
        ->where(function($query) use ($auth){
            $query->where('l.approve_lid', '=', $auth->emp_id)->orWhere('l.approve_mid', '=', $auth->emp_id);
        })->select('l.*', 'e.name', 'e.surname', 'e.nickname', 'e.image', 'd.dept_name')->orderBy('l.id', 'asc')->get();
        return $data;
    }

    public function completedRecordWorking()
    {
        // เสร็จสมบูรณ์ (บุคคลอนุมัติ)
        $auth = auth()->user();
        $emp_id = [];
        $users = self::getAuthorizeUsers();
        if ($users->isNotEmpty()) {
            foreach ($users as $users) {
                $emp_id[] = $users->emp_id;
            }
        }

        $data = DB::table('record_working as l')->leftJoin('employee as e', 'l.emp_id', '=', 'e.emp_id')->leftJoin('department as d', 'e.dept_id', '=', 'd.dept_id')
        ->whereIn('l.approve_status', ['S'])->whereIn('l.emp_id', $emp_id)
        ->where(function($query) use ($auth){
            $query->where('l.approve_lid', '=', $auth->emp_id)->orWhere('l.approve_mid', '=', $auth->emp_id);
        })->select('l.*', 'e.name', 'e.surname', 'e.nickname', 'e.image', 'd.dept_name')->orderBy('l.id', 'asc')->get();
        return $data;
    }

    public function canceledRecordWorking()
    {
        // ยกเลิก (ไม่อนุมัติ)
        $auth = auth()->user();
        $emp_id = [];
        $users = self::getAuthorizeUsers();
        if ($users->isNotEmpty()) {
            foreach ($users as $users) {
                $emp_id[] = $users->emp_id;
            }
        }

        $data = DB::table('record_working as l')->leftJoin('employee as e', 'l.emp_id', '=', 'e.emp_id')->leftJoin('department as d', 'e.dept_id', '=', 'd.dept_id')
        ->whereIn('l.approve_status', ['C1', 'C2', 'C3'])->whereIn('l.emp_id', $emp_id)
        ->where(function($query) use ($auth){
            $query->where('l.approve_lid', '=', $auth->emp_id)->orWhere('l.approve_mid', '=', $auth->emp_id);
        })->select('l.*', 'e.name', 'e.surname', 'e.nickname', 'e.image', 'd.dept_name')->orderBy('l.id', 'asc')->get();
        return $data;
    }

    public function record_working_pending_search(Request $request)
    {
        if ($request->ajax()) {
            $emp_id = [];
            $users = self::getAuthorizeUsers();
            if ($users->isNotEmpty()) {
                foreach ($users as $users) {
                    $leave = RecordWorking::whereIn('approve_status', ['P', 'A1'])->where('emp_id', '=', $users->emp_id)->orderBy('id', 'asc')->get();
                    if ($leave->isNotEmpty()) {
                        foreach ($leave as $leave) {
                            $cau = self::chkEmpApproveRecordWorking($leave->emp_id, $users->dept_id, $leave->approve_status);
                            if ($cau) {
                                $emp_id[] = $users->emp_id;
                                break;
                            }
                        }
                    }
                }
            }
            $emp_id = array_unique($emp_id);

            $data = DB::table('record_working as l')->leftJoin('employee as e', 'l.emp_id', '=', 'e.emp_id')->leftJoin('department as d', 'e.dept_id', '=', 'd.dept_id')
            ->whereIn('l.approve_status', ['P', 'A1'])->whereIn('l.emp_id', $emp_id);

            $totalRecords = $data->select('count(l.*) as allcount')->count();
            $records = $data->select('l.*', 'e.name', 'e.surname', 'e.nickname', 'e.image', 'd.dept_id', 'd.dept_name')->orderBy('l.id', 'asc')->get();
            $rows = [];
            $n = 1;
            foreach ($records as $rec) {
                $nickname = '';
                if ($rec->nickname != "") {
                    $nickname = ' ('.$rec->nickname.')';
                }
                $status = self::get_leave_status($rec->approve_status);
                $approve_status = '<span class="badge ' . $status["badge"] . '">' . $status["name"] . '</span>';
                $manage = '<div>
                        <a class="action-icon" href="'.url('leave/approve/emp-record-working-approve', $rec->id).'" title="อนุมัติ"><i class="mdi mdi-check-circle-outline"></i></a>
                        <a class="action-icon" href="javascript:void(0);" onclick="cancelRecordWorkingConfirmation(\''.$rec->id.'\')" title="ไม่อนุมัติ"><i class="mdi mdi-close-circle-outline"></i></a>
                    </div>';
                $rows[] = array(
                    "id" => $rec->id,
                    "user" => '<a href="'.url('leave/approve/emp-record-working-approve', $rec->id).'" class="text-secondary text-fw"><div class="table-user"><img src="'.url('assets/images/users/thumbnail/'.$rec->image).'" onerror="this.onerror=null;this.src=\''.url('assets/images/users/thumbnail/user-1.jpg').'\';" alt="table-user" class="me-1 rounded-circle">' . $rec->name . ' ' . $rec->surname . $nickname . '</div></div>',
                    "create_date" => '<a href="'.url('leave/approve/emp-record-working-approve', $rec->id).'" class="text-secondary text-fw">'.Carbon::parse($rec->created_at)->thaidate('d/m/Y').'</div>',
                    "work_date" => '<a href="'.url('leave/approve/emp-record-working-approve', $rec->id).'" class="text-secondary text-fw">'.Carbon::parse($rec->work_date)->thaidate('d/m/Y').'</div>',
                    "remark" => '<a href="'.url('leave/approve/emp-record-working-approve', $rec->id).'" class="text-secondary text-fw">'.$rec->remark.'</div>',
                    "status" => '<a href="'.url('leave/approve/emp-record-working-approve', $rec->id).'" class="text-secondary text-fw">'.$approve_status.'</div>',
                    "manage" => $manage,
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

    public function record_working_approved_search(Request $request)
    {
        if ($request->ajax()) {
            $auth = auth()->user();
            $emp_id = [];
            $users = self::getAuthorizeUsers();
            if ($users->isNotEmpty()) {
                foreach ($users as $users) {
                    $emp_id[] = $users->emp_id;
                }
            }

            $data = DB::table('record_working as l')->leftJoin('employee as e', 'l.emp_id', '=', 'e.emp_id')->leftJoin('department as d', 'e.dept_id', '=', 'd.dept_id')
            ->whereIn('l.approve_status', ['A1', 'A2', 'S'])->whereIn('l.emp_id', $emp_id)
            ->where(function($query) use ($auth){
                $query->where('l.approve_lid', '=', $auth->emp_id)->orWhere('l.approve_mid', '=', $auth->emp_id);
            });

            $totalRecords = $data->select('count(l.*) as allcount')->count();
            $records = $data->select('l.*', 'e.name', 'e.surname', 'e.nickname', 'e.image', 'd.dept_id', 'd.dept_name')->orderBy('l.id', 'desc')->get();
            $rows = [];
            $n = 1;
            foreach ($records as $rec) {
                $nickname = '';
                if ($rec->nickname != "") {
                    $nickname = ' ('.$rec->nickname.')';
                }
                $status = self::get_leave_status($rec->approve_status);
                $approve_status = '<span class="badge ' . $status["badge"] . '">' . $status["name"] . '</span>';
                $manage = '';
                if ($rec->approve_status == "A1") {
                    $approveL = self::chkEmpApproveLeader($rec->emp_id, $rec->dept_id);
                    if ($approveL !== false) {
                        $manage = '<div>
                            <a class="action-icon" href="javascript:void(0);" onclick="cancelRecordWorkingConfirmation(\''.$rec->id.'\')" title="ยกเลิก"><i class="mdi mdi-cancel"></i></a>
                        </div>';
                    }
                } else if ($rec->approve_status == "A2") {
                    $approveM = self::chkEmpApproveManager($rec->emp_id, $rec->dept_id);
                    if ($approveM !== false) {
                        $manage = '<div>
                            <a class="action-icon" href="javascript:void(0);" onclick="cancelRecordWorkingConfirmation(\''.$rec->id.'\')" title="ยกเลิก"><i class="mdi mdi-cancel"></i></a>
                        </div>';
                    }
                }
                $rows[] = array(
                    "id" => $rec->id,
                    "user" => '<a href="'.url('leave/document-record-working', $rec->id).'" class="text-secondary text-fw"><div class="table-user"><img src="'.url('assets/images/users/thumbnail/'.$rec->image).'" onerror="this.onerror=null;this.src=\''.url('assets/images/users/thumbnail/user-1.jpg').'\';" alt="table-user" class="me-1 rounded-circle">' . $rec->name . ' ' . $rec->surname . $nickname . '</div></div></a>',
                    "create_date" => '<a href="'.url('leave/document-record-working', $rec->id).'" class="text-secondary text-fw">'.Carbon::parse($rec->created_at)->thaidate('d/m/Y').'</div>',
                    "work_date" => '<a href="'.url('leave/document-record-working', $rec->id).'" class="text-secondary text-fw">'.Carbon::parse($rec->work_date)->thaidate('d/m/Y').'</div>',
                    "remark" => '<a href="'.url('leave/document-record-working', $rec->id).'" class="text-secondary text-fw">'.$rec->remark.'</div>',
                    "status" => '<a href="'.url('leave/document-record-working', $rec->id).'" class="text-secondary text-fw">'.$approve_status.'</div>',
                    "manage" => '<a href="'.url('leave/document-record-working', $rec->id).'" class="text-secondary text-fw">'.$manage.'</div>',
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

    public function record_working_approve_submit(Request $request)
    {
        $data = $request->id;
        if (count($data) <= 0) {
            return response()->json(['success' => false, 'message' => 'ไม่พบข้อมูล!']);
        }
        for ($i=0; $i<count($data); $i++) {
            $chk = RecordWorking::where('id', '=', $data[$i])->first();
            if ($chk) {
                $approve_lid = $chk->approve_lid;
                $approve_lip = $chk->approve_lip;
                $approve_ldate = $chk->approve_ldate;
                $approve_mid = $chk->approve_mid;
                $approve_mip = $chk->approve_mip;
                $approve_mdate = $chk->approve_mdate;
                $approve_status = $chk->approve_status;
                $desLog = "";

                $emp = self::getEmployee($chk->emp_id);
                $approveM = self::getEmpApproveManager($chk->emp_id, $emp->dept_id);
                $approveL = self::getEmpApproveLeader($chk->emp_id, $emp->dept_id);
                if ($approveM !== false) {
                    $approveL = self::getEmpApproveLeader($chk->emp_id, $emp->dept_id);
                    if ($approveL !== false) {
                        $approve_lid = $approveM["emp_id"];
                        $approve_lip = $request->ip();
                        $approve_ldate = now();
                    }
                    $approve_mid = $approveM["emp_id"];
                    $approve_mip = $request->ip();
                    $approve_mdate = now();
                    $approve_status = "A2";
                    $desLog = "อนุมัติใบบันทึกวันทำงาน (โดยผู้จัดการ)";
                } else {
                    $approveL = self::getEmpApproveLeader($chk->emp_id, $emp->dept_id);
                    if ($approveL !== false) {
                        $approve_lid = $approveL["emp_id"];
                        $approve_lip = $request->ip();
                        $approve_ldate = now();
                        $approve_status = "A1";
                        $desLog = "อนุมัติใบบันทึกวันทำงาน (โดยหัวหน้า)";
                    }
                }
                $leave = RecordWorking::where('id', '=', $data[$i]);
                $leave->update([
                    "approve_lid" => $approve_lid,
                    "approve_lip" => $approve_lip,
                    "approve_ldate" => $approve_ldate,
                    "approve_mid" => $approve_mid,
                    "approve_mip" => $approve_mip,
                    "approve_mdate" => $approve_mdate,
                    "approve_status" => $approve_status,
                ]);

                self::addRecordWorkingLog($data[$i], $desLog, auth()->user()->emp_id, $request->ip());
                self::recordWorkingRemoveNotification($data[$i]);
                self::recordWorkingPushNotification($data[$i], $chk->emp_id, $emp->dept_id, $emp->name.' '.$emp->surname);
            }
        }
        return response()->json([
            'success' => true,
            'message' => 'อนุมัติบันทึกวันทำงานเรียบร้อย',
        ]);
    }

    public function emp_record_working_approve($id)
    {
        $leave = RecordWorking::where('id', '=', $id)->first();
        if (!$leave) {
            alert()->warning('ไม่พบใบบันทึกวันทำงานนี้!');
            return back();
        }
        $emp = DB::table('employee as e')->leftJoin('position as p', 'e.position_id', '=', 'p.position_id')->leftJoin('department as d', 'e.dept_id', '=', 'd.dept_id')
            ->where('e.emp_id', '=', $leave->emp_id)->select(['e.*', 'p.position_name', 'd.dept_name', 'd.level'])->first();
        $worked_days = self::worked_days($leave->emp_id);
        $depts = self::getDepartmentToArray();
        $dept_arr = self::callDepartment($emp->level, $emp->dept_id, $depts);
        $leaveEmp = self::getRecordWorkingEmpDetail($leave->id);
        $approvedLeader = self::getApproveRecordWorkingLeaderDetail($leave->id);
        $approvedManager = self::getApproveRecordWorkingManagerDetail($leave->id);
        $approvedHR = self::getApproveRecordWorkingHRDetail($leave->id);
        $status = self::get_leave_status($leave->approve_status);
        $recordWorkingLog = self::getRecordWorkingLog($leave->id);

        if ($leave->emp_id == auth()->user()->emp_id && ($leave->approve_status == "C1" || $leave->approve_status == "C2" || $leave->approve_status == "C3")) {
            self::recordWorkingRemoveNotification($leave->id);
        } else {
            self::recordWorkingUpdateNotification($leave->id);
        }

        $auth_emp = [];
        $users = self::getAuthorizeUsers();
        if ($users->isNotEmpty()) {
            foreach ($users as $users) {
                $auth_emp[] = $users->emp_id;
            }
        }
        if (!in_array($leave->emp_id, $auth_emp)) {
            alert()->warning('ไม่มีสิทธิ์อนุมัติใบลานี้!');
            return back();
        }

        $agent = new Agent();
        if ($agent->isMobile()) {
            $view = 'leave.approve.emp-record-working-approve-mobile';
        } else {
            $view = 'leave.approve.emp-record-working-approve';
        }
        return view($view, compact('leave', 'emp', 'dept_arr', 'worked_days', 'leaveEmp', 'approvedLeader', 'approvedManager', 'approvedHR', 'status', 'recordWorkingLog'));
    }

    public function emp_record_working_approved(Request $request)
    {
        $chk = RecordWorking::where('id', '=', $request->id)->first();
        if (!$chk) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบใบบันทึกวันทำงานนี้',
            ]);
        }

        $approve_lid = $chk->approve_lid;
        $approve_lip = $chk->approve_lip;
        $approve_ldate = $chk->approve_ldate;
        $approve_mid = $chk->approve_mid;
        $approve_mip = $chk->approve_mip;
        $approve_mdate = $chk->approve_mdate;
        $approve_status = $chk->approve_status;
        $desLog = "";

        $auth_emp = [];
        $users = self::getAuthorizeUsers();
        if ($users->isNotEmpty()) {
            foreach ($users as $users) {
                $auth_emp[] = $users->emp_id;
            }
        }
        if (!in_array($chk->emp_id, $auth_emp)) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่มีสิทธิ์อนุมัติใบบันทึกวันทำงานนี้',
            ]);
        }
        if ($approve_status == "S") {
            return response()->json([
                'success' => false,
                'message' => 'ใบบันทึกวันทำงานนี้ถูกอนุมัติโดยบุคคลแล้ว',
            ]);
        }
        if ($approve_status == "C1" || $approve_status == "C2" || $approve_status == "C3") {
            return response()->json([
                'success' => false,
                'message' => 'ใบบันทึกวันทำงานนี้ถูกยกเลิกไปแล้ว',
            ]);
        }

        $emp = self::getEmployee($chk->emp_id);
        $approveM = self::getEmpApproveManager($chk->emp_id, $emp->dept_id);
        $approveL = self::getEmpApproveLeader($chk->emp_id, $emp->dept_id);
        if ($approveM !== false) {
            $approveL = self::getEmpApproveLeader($chk->emp_id, $emp->dept_id);
            if ($approveL !== false) {
                $approve_lid = $approveM["emp_id"];
                $approve_lip = $request->ip();
                $approve_ldate = now();
            }
            $approve_mid = $approveM["emp_id"];
            $approve_mip = $request->ip();
            $approve_mdate = now();
            $approve_status = "A2";
            $desLog = "อนุมัติใบบันทึกวันทำงาน (โดยผู้จัดการ)";
        } else {
            $approveL = self::getEmpApproveLeader($chk->emp_id, $emp->dept_id);
            if ($approveL !== false) {
                $approve_lid = $approveL["emp_id"];
                $approve_lip = $request->ip();
                $approve_ldate = now();
                $approve_status = "A1";
                $desLog = "อนุมัติใบบันทึกวันทำงาน (โดยหัวหน้า)";
            }
        }
        $leave = RecordWorking::where('id', '=', $chk->id);
        $leave->update([
            "approve_lid" => $approve_lid,
            "approve_lip" => $approve_lip,
            "approve_ldate" => $approve_ldate,
            "approve_mid" => $approve_mid,
            "approve_mip" => $approve_mip,
            "approve_mdate" => $approve_mdate,
            "approve_status" => $approve_status,
        ]);

        self::addRecordWorkingLog($chk->id, $desLog, auth()->user()->emp_id, $request->ip());
        self::recordWorkingRemoveNotification($chk->id);
        self::recordWorkingPushNotification($chk->id, $chk->emp_id, $emp->dept_id, $emp->name . ' ' . $emp->surname);

        return response()->json([
            'success' => true,
            'message' => 'อนุมัติใบบันทึกวันทำงานเรียบร้อย',
        ]);
    }

    public function emp_record_working_cancel(Request $request)
    {
        $leave = RecordWorking::where('id', '=', $request->id)->first();
        if ($leave) {
            $emp = self::getEmployee($leave->emp_id);
            $approve_lid = $leave->approve_lid;
            $approve_lip = $leave->approve_lip;
            $approve_ldate = $leave->approve_ldate;
            $approve_mid = $leave->approve_mid;
            $approve_mip = $leave->approve_mip;
            $approve_mdate = $leave->approve_mdate;
            $cancel_remark = $leave->cancel_remark;
            $approve_status = $leave->approve_status;
            $desLog = "";
            $approveM = self::getEmpApproveManager($leave->emp_id, $emp->dept_id);
            if ($approveM !== false) {
                if ($leave->approve_status == "S") {
                    return response()->json([
                        'success' => false,
                        'message' => 'ใบบันทึกวันทำงานถูกอนุมัติโดยบุคคลแล้ว',
                    ]);
                }
                $approve_mid = $approveM["emp_id"];
                $approve_mip = $request->ip();
                $approve_mdate = now();
                $cancel_remark = 'ยกเลิกโดยผู้จัดการ';
                $approve_status = "C2";
                $desLog = "ยกเลิกใบบันทึกวันทำงาน (โดยผู้จัดการ)";
            } else {
                $approveL = self::getEmpApproveLeader($leave->emp_id, $emp->dept_id);
                if ($approveL !== false) {
                    if ($leave->approve_status == "A2") {
                        return response()->json([
                            'success' => false,
                            'message' => 'ใบบันทึกวันทำงานถูกอนุมัติโดยผู้จัดการแล้ว',
                        ]);
                    }
                    $approve_lid = $approveL["emp_id"];
                    $approve_lip = $request->ip();
                    $approve_ldate = now();
                    $cancel_remark = 'ยกเลิกโดยหัวหน้า';
                    $approve_status = "C1";
                    $desLog = "ยกเลิกใบบันทึกวันทำงาน (โดยหัวหน้า)";
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'ไม่มีสิทธิ์ยกเลิกใบบันทึกวันทำงานนี้',
                    ]);
                }
            }
            $leaveUpdate = RecordWorking::where('id', '=', $request->id);
            $leaveUpdate->update([
                "approve_lid" => $approve_lid,
                "approve_lip" => $approve_lip,
                "approve_ldate" => $approve_ldate,
                "approve_mid" => $approve_mid,
                "approve_mip" => $approve_mip,
                "approve_mdate" => $approve_mdate,
                "approve_status" => $approve_status,
                "use_status" => 0,
                "cancel_remark" => $request->cancel_remark,
            ]);
            self::addRecordWorkingLog($request->id, $desLog, auth()->user()->emp_id, $request->ip());
            self::recordWorkingRemoveNotification($request->id);
            self::recordWorkingCancelPushNotification($request->id);
        } else {
            return response()->json(['success' => false, 'message' => 'ไม่พบใบบันทึกวันทำงาน!']);
        }
        return response()->json([
            'success' => true,
            'message' => 'ยกเลิกเรียบร้อย',
        ]);
    }

    public function emp_record_working_history(Request $request)
    {
        $approve_status = self::get_leave_status();
        return view('leave.approve.emp-record-working-history', compact('approve_status'));
    }

    public function emp_record_working_history_search(Request $request)
    {
        if ($request->ajax()) {
            $leave_id = [];
            $auth = auth()->user();
            $users = self::getAuthorizeLevelUsers();
            if ($users->isNotEmpty()) {
                foreach ($users as $users) {
                    $leave = RecordWorking::whereIn('approve_status', ['P','A1','A2','S'])->where('emp_id', '=', $users->emp_id)->orderBy('id', 'asc')->get();
                    if ($leave->isNotEmpty()) {
                        foreach ($leave as $leave) {
                            $leave_id[] = $leave->id;
                        }
                    }
                }
                $leave = RecordWorking::whereIn('approve_status', ['P','A1','A2','S'])->where('emp_id', '=', $auth->emp_id)->orderBy('id', 'asc')->get();
                if ($leave->isNotEmpty()) {
                    foreach ($leave as $leave) {
                        $leave_id[] = $leave->id;
                    }
                }
            }
            $data = DB::table('record_working as l')->leftJoin('employee as e', 'l.emp_id', '=', 'e.emp_id')->leftJoin('department as d', 'e.dept_id', '=', 'd.dept_id')
                ->leftJoin('leave', function ($join) {
                    $join->on('leave.leave_id', '=', 'l.leave_id')->where('l.leave_id', '<>', null)->where('l.leave_id', '<>', '');
                })
                ->whereIn('approve_status', ['P','A1','A2','S'])->whereIn('l.id', $leave_id)
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

                    $work_start = "";
                    $work_end = "";
                    if ($request->work_start_date != '') {
                        $work_start = Carbon::createFromFormat('d/m/Y', $request->work_start_date)->format('Y-m-d');
                    }
                    if ($request->work_end_date != '') {
                        $work_end = Carbon::createFromFormat('d/m/Y', $request->work_end_date)->format('Y-m-d');
                    }
                    if ($work_start != '' && $work_end != '') {
                        $query->where('l.work_date', '>=', $work_start);
                        $query->where('l.work_date', '<=', $work_end);
                    } else if ($work_start != '' && $work_end == '') {
                        $query->where('l.work_date', '>=', $work_start);
                    } else if ($work_start == '' && $work_end != '') {
                        $query->where('l.work_date', '<=', $work_end);
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
                        $query->whereRaw('SUBSTRING(l.created_at, 1, 10) >= "' . $record_start . '"');
                        $query->whereRaw('SUBSTRING(l.created_at, 1, 10) <= "' . $record_end . '"');
                    } else if ($record_start != '' && $record_end == '') {
                        $query->whereRaw('SUBSTRING(l.created_at, 1, 10) >= "' . $record_start . '"');
                    } else if ($record_start == '' && $record_end != '') {
                        $query->whereRaw('SUBSTRING(l.created_at, 1, 10) <= "' . $record_end . '"');
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
                $emp_id = '<span class="text-' . $type["color"] . '">' . $rec->emp_id . '</span>';
                $status = self::get_leave_status($rec->approve_status);
                $approve_status = '<span class="badge ' . $status["badge"] . '">' . $status["name"] . '</span>';
                if ($rec->leave_id != '') {
                    $use_date = Carbon::parse($rec->leave_start_date)->thaidate('d/m/Y');
                } else {
                    $use_date = '';
                }
                $rows[] = array(
                    "no" => $n,
                    "emp_id" => $emp_id,
                    "name" => '<a href="'.url('leave/document-record-working', $rec->id).'" class="text-dark"><i class="mdi mdi-file-document-outline text-primary"></i>' . $rec->name . ' ' . $rec->surname . $nickname . '</a>',
                    "dept_id" => $rec->dept_id,
                    "dept_name" => $rec->dept_name . $area_code,
                    "create_date" => Carbon::parse($rec->created_at)->thaidate('d/m/Y'),
                    "work_date" => Carbon::parse($rec->work_date)->thaidate('d/m/Y'),
                    "use_date" => $use_date,
                    "remark" => $rec->remark,
                    "status" => $approve_status,
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
    // ======================================= END ========================================
}