<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\AttendanceLog;
use App\Models\Leave;
use App\Models\LeaveLog;
use App\Models\RecordWorking;
use App\Models\LeaveType;
use App\Models\LeaveTypeProperty;
use App\Models\PeriodSalary;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;
use File;
use Illuminate\Support\Facades\App;

class LeaveController extends LeaveBaseController
{
    protected $leave_dash;
    protected $attathPath;
    protected $status_summary;
    public function __construct()
    {
        $this->middleware('auth');
        $this->attathPath = $_SERVER['DOCUMENT_ROOT'] . '/assets/files/leave/';
        $this->status_summary = ['P', 'E', 'A1', 'A2', 'S'];
    }

    public function dashboard()
    {
        $user = auth()->user();
        $private_leave = self::privateLeave($user->emp_id);
        $private_total = self::privateTotal();
        $sick_leave = self::sickLeave($user->emp_id);
        $sick_total = self::sickTotal();
        $vacation_leave = self::vacationLeave($user->emp_id);
        $vacation_total = self::vacationTotal();
        $unpaid_leave = self::unpaidLeave($user->emp_id);
        $urgent_leave_amount = self::urgentLeaveAmount($user->emp_id);
        $compensation_leave = self::compensationLeave($user->emp_id);
        $record_working_total = self::recordWorkingTotal($user->emp_id);
        $other_leave = self::otherLeave($user->emp_id);
        $worked_days = self::worked_days($user->emp_id);
        $attendance_latest = self::getAttendanceLatest();
        $this->leave_dash = self::get_dash_change();
        $leave_dash = $this->leave_dash;

        return view('leave.leave-dashboard', compact('leave_dash', 'private_leave', 'private_total', 'sick_leave', 'sick_total', 'other_leave', 'vacation_leave', 'vacation_total', 'unpaid_leave', 'urgent_leave_amount', 'compensation_leave', 'record_working_total', 'worked_days', 'attendance_latest'));
    }

    public function attach_file($id)
    {
        $agent = new Agent();
        $filePath = 'assets/files/leave/'.$id;
        $infoPath = pathinfo(public_path($filePath));
        $extension = $infoPath['extension'];
        if ($extension == "pdf" || $extension == "jpg" || $extension == "png" || $extension == "gif" || $extension == "bmp") {
            if ($agent->isDesktop()) {
                return response()->file($filePath);
            } else {
                return response()->download($filePath);
            }
        }
        return response()->download($filePath);
    }

    public function leave_document($id)
    {
        $leave = Leave::where('leave_id', '=', $id)->first();
        if (!$leave) {
            alert()->warning('ไม่พบใบลา!');
            return back();
        }
        $leaveType = LeaveType::where('leave_type_status', '=', 1)->get();
        $periodSalary = PeriodSalary::find($leave->period_salary_id);
        $emp = DB::table('employee as e')->leftJoin('position as p', 'e.position_id', '=', 'p.position_id')->leftJoin('department as d', 'e.dept_id', '=', 'd.dept_id')
            ->where('e.emp_id', '=', $leave->emp_id)->select(['e.*', 'p.position_name', 'd.dept_name', 'd.level'])->first();
        $worked_days = self::leave_doc_worked_days($leave->emp_id, $leave->leave_id);
        $depts = self::getDepartmentToArray();
        $dept_arr = self::callDepartment($emp->level, $emp->dept_id, $depts);
        $leaveEmp = self::getLeaveEmpDetail($leave->leave_id);
        $leaveLeader = self::getLeaveLeaderDetail($leave->leave_id);
        $approvedLeader = self::getApproveLeaderDetail($leave->leave_id);
        $approvedManager = self::getApproveManagerDetail($leave->leave_id);
        $approvedHR = self::getApproveHRDetail($leave->leave_id);
        $leaveMinutes = self::minutesToTime($leave->leave_minute);
        $status = self::get_leave_status($leave->leave_status);
        $rwRef = self::getRecordWorkingRef($leave->leave_id);
        $leaveLog = self::getLeaveLog($leave->leave_id);

        if ($leave->emp_id == auth()->user()->emp_id && ($leave->leave_status == "C1" || $leave->leave_status == "C2" || $leave->leave_status == "C3")) {
            self::leaveRemoveNotification($leave->leave_id);
        } else {
            self::leaveUpdateNotification($leave->leave_id);
        }

        $agent = new Agent();
        if ($agent->isPhone()) {
            $view = 'leave.leave-document-mobile';
        } else {
            $view = 'leave.leave-document';
        }
        return view($view, compact('leave', 'leaveType', 'periodSalary', 'emp', 'dept_arr', 'worked_days', 'leaveEmp', 'leaveLeader', 'approvedLeader', 'approvedManager', 'approvedHR', 'leaveMinutes', 'status', 'rwRef', 'leaveLog'));
    }

    public function leave_document_pdf($id)
    {
        $leave = Leave::where('leave_id', '=', $id)->first();
        $leaveType = LeaveType::where('leave_type_status', '=', 1)->get();
        $emp = DB::table('employee as e')->leftJoin('position as p', 'e.position_id', '=', 'p.position_id')->leftJoin('department as d', 'e.dept_id', '=', 'd.dept_id')
            ->where('e.emp_id', '=', $leave->emp_id)->select(['e.*', 'p.position_name', 'd.dept_name', 'd.level'])->first();
        $worked_days = self::leave_doc_worked_days($leave->emp_id, $leave->leave_id);
        $depts = self::getDepartmentToArray();
        $dept_arr = self::callDepartment($emp->level, $emp->dept_id, $depts);
        $leaveEmp = self::getLeaveEmpDetail($leave->leave_id);
        $leaveLeader = self::getLeaveLeaderDetail($leave->leave_id);
        $approvedLeader = self::getApproveLeaderDetail($leave->leave_id);
        $approvedManager = self::getApproveManagerDetail($leave->leave_id);
        $approvedHR = self::getApproveHRDetail($leave->leave_id);
        $leaveMinutes = self::minutesToTime($leave->leave_minute);
        $rwRef = self::getRecordWorkingRef($leave->leave_id);

        $dompdf = App::make('dompdf.wrapper');

        //############ if image not loading ################################
        // $contxt = stream_context_create([
        //     'ssl' => [
        //         'verify_peer' => FALSE,
        //         'verify_peer_name' => FALSE,
        //         'allow_self_signed' => TRUE,
        //     ]
        // ]);

        // $dompdf = Pdf::setOptions(['isHTML5ParserEnabled' => true, 'isRemoteEnabled' => true]);
        // $dompdf->getDomPDF()->setHttpContext($contxt);
        //#################################################################################

        $dompdf->loadView('leave.leave-document-pdf', compact('leave', 'leaveType', 'emp', 'dept_arr', 'worked_days', 'leaveEmp', 'leaveLeader', 'approvedLeader', 'approvedManager', 'approvedHR', 'leaveMinutes', 'rwRef'))
        ->setPaper('a5', 'landscape')->setWarnings(false);
        $agent = new Agent();
        if ($agent->isPhone()) {
            return $dompdf->download($leave->emp_id.' '.date('Y-m-d').'.pdf');
        } else {
            return $dompdf->stream('ใบลางาน.pdf');
        }
    }

    public function callLinkDocumentLV($id, $value)
    {
        return '<a href="'.url('leave/document', $id).'" class="text-secondary text-fw">'.$value.'</a>';
    }

    public function callLinkDocumentRW($id, $value)
    {
        return '<a href="'.url('leave/document-record-working', $id).'" class="text-secondary text-fw">'.$value.'</a>';
    }

    public function form()
    {
        $auth = Auth::user();
        $user = DB::table('employee as e')->leftJoin('department as d', 'd.dept_id', '=', 'e.dept_id')->leftJoin('position as p', 'p.position_id', '=', 'e.position_id')
        ->where('e.emp_id', '=', $auth->emp_id)->select('e.*', 'd.dept_name', 'p.position_name')->first();

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

        return view('leave.leave-form', compact('user', 'dept_level', 'period', 'pre_period', 'leave_type', 'work_date', 'hol_arr', 'record_working_arr'));
    }

    public function store(Request $request)
    {
        $auth = auth()->user();
        $leave_mode = 1; // พนักงานบันทึกด้วยตัวเอง
        $emp_id = $auth->emp_id;
        $leader_id = null;
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
            $record_working = RecordWorking::where('emp_id', '=', $auth->emp_id)->where('use_status', '=', 1)->orderBy('work_date', 'ASC')->get();
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
                $record_working = RecordWorking::where('emp_id', '=', $auth->emp_id)->where('use_status', '=', 1)->orderBy('work_date', 'ASC')->first();
                if ($record_working) {
                    $leaveS = RecordWorking::find($record_working->id);
                    $leaveS->update([
                        "use_status" => 2,
                        "leave_id" => $leave_id,
                    ]);
                }
            }
            self::addLeaveLog($leave_id, "สร้างใบลางาน", auth()->user()->emp_id, $request->ip());
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
        return redirect('leave/dashboard');
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

    public function edit($id)
    {
        $auth = Auth::user();
        $user = DB::table('employee as e')->leftJoin('department as d', 'd.dept_id', '=', 'e.dept_id')->leftJoin('position as p', 'p.position_id', '=', 'e.position_id')
        ->where('e.emp_id', '=', $auth->emp_id)->select('e.*', 'd.dept_name', 'p.position_name')->first();

        $dept_level = self::getDeptLevel($user->dept_id);
        $current_date = date('Y-m-d');
        $period = PeriodSalary::where('start', '<=', $current_date)->where('end', '>=', $current_date)->first();
        $previous_date = date("Y-m-d", strtotime("-7 day",strtotime($period->start)));
        $pre_period = PeriodSalary::where('start', '<=', $previous_date)->where('end', '>=', $previous_date)->first();

        $leave = Leave::where('leave_id', '=', $id)->first();

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

        $record_working = RecordWorking::where('emp_id', '=', $auth->emp_id)->where('leave_id', '=', $leave->leave_id)->first();

        if ($auth->emp_id != $leave->emp_id) {
            // ห้ามแก้ไขของคนอื่น
            return view('errors.500');
        }

        return view('leave.leave-edit', compact('user', 'dept_level', 'period', 'pre_period', 'leave', 'leave_type', 'leave_type_detail', 'sum_time', 'work_date', 'hol_arr', 'record_working_arr', 'record_working'));
    }

    public function update(Request $request)
    {
        $auth = auth()->user();
        $leave_mode = $request->leave_mode;
        $emp_id = $auth->emp_id;
        $leader_id = null;
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
        if ($leave->leave_status == "A1" || $leave->leave_status == "A2" || $leave->leave_status == "S") {
            $emp = self::getEmployee($emp_id);
            $approveM = self::getEmpApproveManager($emp_id, $emp->dept_id);
            if ($approveM !== false) {
                if ($leave->leave_status == "S") {
                    alert()->warning('สถานะใบลาถูกอนุมัติแล้ว!', 'ไม่สามารถแก้ไขได้');
                    return back();
                }
            } else {
                $approveL = self::getEmpApproveLeader($emp_id, $emp->dept_id);
                if ($approveL !== false) {
                    if ($leave->leave_status == "A2") {
                        alert()->warning('สถานะใบลาถูกอนุมัติแล้ว!', 'ไม่สามารถแก้ไขได้');
                        return back();
                    }
                } else {
                    alert()->warning('สถานะใบลาถูกอนุมัติแล้ว!', 'ไม่สามารถแก้ไขได้');
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
                $record_working = RecordWorking::where('emp_id', '=', $auth->emp_id)->where('use_status', '=', 1)->orderBy('work_date', 'ASC')->get();
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
                $record_working = RecordWorking::where('emp_id', '=', $auth->emp_id)->where('leave_id', '=', $leave_id)->orderBy('work_date', 'ASC')->get();
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
            self::_updateLeave($leave_id, $leave_start_date, $leave_start_time, $leave_end_date, $leave_end_time, $leave_reason, $leave_attach, $leave_day, $leave_minute, $leave_type, $period_salary_id, $leave_range, $leave_mode, $leader_id, $emp_id, $emp->emp_type, $approve_lid, $approve_lip, $approve_ldate, $approve_mid, $approve_mip, $approve_mdate, $leave_status);
            if ($leave_type == "6") {
                // ลาหยุดชดเชย
                if ($leave->leave_type_id != "6") {
                    // กรณีเปลี่ยนประเภทลางาน (อย่างอื่น => หยุดชดเชย)
                    $record_working = RecordWorking::where('emp_id', '=', $auth->emp_id)->where('use_status', '=', 1)->orderBy('work_date', 'ASC')->first();
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
                    $record_working = RecordWorking::where('emp_id', '=', $auth->emp_id)->where('use_status', '=', 2)->where('leave_id', '=', $leave_id)->first();
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
            self::addLeaveLog($leave_id, "แก้ไขใบลางาน", auth()->user()->emp_id, $request->ip());
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
        return redirect('leave/dashboard');
    }

    public function _updateLeave($leave_id, $leave_start_date, $leave_start_time, $leave_end_date, $leave_end_time, $leave_reason, $leave_attach, $leave_day, $leave_minute, $leave_type, $period_salary_id, $leave_range, $leave_mode, $leader_id, $emp_id, $emp_type, $approve_lid, $approve_lip, $approve_ldate, $approve_mid, $approve_mip, $approve_mdate, $leave_status)
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
            "emp_type" => $emp_type,
            "approve_lid" => $approve_lid,
            "approve_lip" => $approve_lip,
            "approve_ldate" => $approve_ldate,
            "approve_mid" => $approve_mid,
            "approve_mip" => $approve_mip,
            "approve_mdate" => $approve_mdate,
            "leave_status" => $leave_status,
        ]);
    }

    public function destroy($id)
    {
        try {
            $check = Leave::where('leave_id', '=', $id)->first();
            if ($check) {
                $leave = Leave::where('leave_id', '=', $id);
                $leave->delete();
                $record_working = RecordWorking::where('emp_id', '=', auth()->user()->emp_id)->where('use_status', '=', 2)->where('leave_id', '=', $id)->first();
                if ($record_working) {
                    // ถอยสถานะวันหยุดกลับคืน
                    $leaveS = RecordWorking::find($record_working->id);
                    $leaveS->update([
                        "use_status" => 1,
                        "leave_id" => null,
                    ]);
                }
                self::leaveRemoveNotification($id);
                if (File::exists($this->attathPath . $check->leave_attach)) {
                    File::delete($this->attathPath . $check->leave_attach);
                }
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'เกิดข้อผิดพลาด!']);
        }
        return response()->json(['success' => true, 'message' => 'ลบข้อมูลการลางานเรียบร้อย']);
    }

    public function leave_search(Request $request)
    {
        if ($request->ajax()) {
            $this->leave_dash = self::get_dash_change();
            $auth = auth()->user();
            $data = DB::table('leave as l')->leftJoin('leave_type as t', 'l.leave_type_id', '=', 't.leave_type_id')
                ->whereRaw('substring(l.leave_start_date, 1, 4) = '.$this->leave_dash['dash_year'])->where('l.emp_id', '=', $auth->emp_id);

            $totalRecords = $data->select('count(l.*) as allcount')->count();
            $records = $data->select('l.*', 't.leave_type_name')->orderBy('l.created_at', 'desc')->get();
            $rows = [];
            $n = 1;
            foreach ($records as $rec) {
                $leave_date = '';
                if ($rec->leave_start_date == $rec->leave_end_date) {
                    $leave_date = Carbon::parse($rec->leave_start_date)->thaidate('j M y');
                } else {
                    $leave_date = Carbon::parse($rec->leave_start_date)->thaidate('j M y') . ' ถึง ' . Carbon::parse($rec->leave_end_date)->thaidate('j M y');
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
                $accessEdit = self::checkAccessEdit($rec->leave_status);
                if ($accessEdit) {
                    $leave_manage = '<div>
                        <a class="action-icon" href="'.url('leave/edit', $rec->leave_id).'" title="แก้ไข"><i class="mdi mdi-square-edit-outline"></i></a>
                        <a class="action-icon" href="javascript:void(0);" onclick="deleteLeaveConfirmation(\''.$rec->leave_id.'\')" title="ลบ"><i class="mdi mdi-delete"></i></a>
                    </div>';
                }
                $rows[] = array(
                    "leave_date" => self::callLinkDocumentLV($rec->leave_id, $leave_date),
                    "leave_amount" => self::callLinkDocumentLV($rec->leave_id, $leave_amount),
                    "leave_type" => self::callLinkDocumentLV($rec->leave_id, $rec->leave_type_name),
                    "leave_status" => self::callLinkDocumentLV($rec->leave_id, $leave_status),
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

    public function record_working_search(Request $request)
    {
        if ($request->ajax()) {
            $this->leave_dash = self::get_dash_change();
            $auth = auth()->user();
            $data = DB::table('record_working as l')->whereRaw('substring(l.work_date, 1, 4) = '.$this->leave_dash['dash_year'])->where('l.emp_id', '=', $auth->emp_id);

            $totalRecords = $data->select('count(l.*) as allcount')->count();
            $records = $data->select('l.*')->orderBy('l.created_at', 'desc')->get();
            $rows = [];
            $n = 1;
            foreach ($records as $rec) {
                $status = self::get_leave_status($rec->approve_status);
                $approve_status = '<span class="badge ' . $status["badge"] . '">' . $status["name"] . '</span>';
                if ($rec->use_status == 2) {
                    $use_status = '<span class="badge badge-soft-success">ใช้แล้ว</span>';
                } else {
                    $use_status = '<span class="badge badge-soft-secondary">ยังไม่ใช้</span>';
                }
                if ($rec->use_status == 0) {
                    $close_status = '<span class="badge badge-soft-secondary">ใช้ไม่ได้</span>';
                } else {
                    $close_status = '<span class="badge badge-soft-success">ใช้ได้</span>';
                }
                $manage = '';
                $accessEdit = self::checkAccessEdit($rec->approve_status);
                if ($accessEdit == true) {
                    $manage = '<div>
                        <a class="action-icon" href="'.url('leave/record-working-edit', $rec->id).'" title="แก้ไข"><i class="mdi mdi-square-edit-outline"></i></a>
                        <a class="action-icon" href="javascript:void(0);" onclick="deleteRecordWorkingConfirmation(\''.$rec->id.'\')" title="ลบ"><i class="mdi mdi-delete"></i></a>
                    </div>';
                }
                $rows[] = array(
                    "date" => self::callLinkDocumentRW($rec->id, Carbon::parse($rec->work_date)->thaidate('j M y')),
                    "use_status" => self::callLinkDocumentRW($rec->id, $use_status),
                    "close_status" => self::callLinkDocumentRW($rec->id, $close_status),
                    "approve_status" => self::callLinkDocumentRW($rec->id, $approve_status),
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

    public function checkAccessEdit($leave_status)
    {
        if ($leave_status == "A2") {
            return self::chkSelfApproveManager();
        } else if ($leave_status == "A1") {
            return self::chkSelfApproveLeader();
        } else if ($leave_status == "P" || $leave_status == "E") {
            return true;
        } else {
            return false;
        }
    }

    public function attendance_log_search(Request $request)
    {
        if ($request->ajax()) {
            $current_date = date('Y-m-d');
            $previous_date = date("Y-m-d", strtotime("-1 month", strtotime($current_date)));

            $emp_id = auth()->user()->emp_id;
            $data = [];
            $i = 0;

            $hol_arr = self::getHolidayDateAndTitle();
            $work = AttendanceLog::where('emp_id', '=', $emp_id)->whereRaw('substring(datetime, 1, 10) between "'.$previous_date.'" and "'.$current_date.'"')->groupBy('datetime', 'device_id')->orderBy('datetime', 'ASC')->get();
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
                $time = [];
                $inout = false;
                $absance = false;
                $is_true = array_search($date, array_column($data, 'date'));
                if ($is_true !== false) {
                    $keys = array_keys(array_column($data, 'date'), $date);
                    for ($j=0; $j<count($keys); $j++) {
                        $time[] = $data[$keys[$j]]["time"];
                    }
                }
                // if ( ( count($time) % 2 ) == 0 ) {
                //     $absance = true;
                // }
                $count_time = count($time);
                if ($count_time != 0) {
                    $inout = true;
                }
                if ($count_time > 1) {
                    $absance = true;
                }
                if ($count_time < 5) {
                    for ($j=0; $j<(5-$count_time); $j++) {
                        $time[] = "";
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
                $arr_history[$i]["absance"] = $absance;
                $arr_history[$i]["inout"] = $inout;
                $date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
                $i++;
            }
            $totalRecords = $i;
            $rows = [];
            foreach ($arr_history as $rec) {
                $is_holiday = false;
                if ($rec['day'] == 'sunday') {
                    $status = '<small class="text-warning">วันหยุด, วันอาทิตย์</small>';
                    $is_holiday = true;
                } else {
                    $ind = array_search($rec['date'], array_column($hol_arr, 'date'));
                    if ($ind !== false) {
                        $status = '<small class="text-warning">วันหยุด ('.$hol_arr[$ind]["title"].')</small>';
                        $is_holiday = true;
                    } else {
                        if ($rec["inout"] == false) {
                            $status = '<small class="text-dark">ไม่มีการเข้า-ออก</small>';
                        } else {
                            if ($rec["absance"] == false) {
                                $status = '<small class="text-danger">เวลาไม่สมบูรณ์</small>';
                            } else {
                                $status = '';
                            }
                        }
                    }
                }
                $rows[] = array(
                    "day" => $rec['day'],
                    "raw_date" => $rec['date'],
                    "date" => Carbon::parse($rec['date'])->thaidate('j M y, D'),
                    "time1" => $rec['time'][0],
                    "time2" => $rec['time'][1],
                    "time3" => $rec['time'][2],
                    "time4" => $rec['time'][3],
                    "time5" => $rec['time'][4],
                    "status" => $status,
                    "holiday" => $is_holiday,
                );
            }
            $rows = self::phparraysort($rows, array('raw_date'));
            $response = array(
                "total" => $totalRecords,
                "totalNotFiltered" => $totalRecords,
                "rows" => $rows,
            );
            return response()->json($response);
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

    public function statistic_period_search(Request $request)
    {
        if ($request->ajax()) {
            $lsController = new LeaveStatisticsController;
            $emp_id = auth()->user()->emp_id;
            $totalRecords = 1;
            $rows = [];
            $rows[] = array(
                "late_qty" => "-",
                "late" => "-",
                "sick" => $lsController->sickThisPeriod($emp_id),
                "private" => $lsController->privateThisPeriod($emp_id),
                "absence" => "-",
                "vacation" => $lsController->vacationThisPeriod($emp_id),
                "compensation" => $lsController->compensationThisPeriod($emp_id),
                "urgent" => $lsController->urgentThisPeriod($emp_id),
                "unpaid" => $lsController->unpaidThisPeriod($emp_id),
                "maternity" => $lsController->maternityThisPeriod($emp_id),
                "ordination" => $lsController->ordinationThisPeriod($emp_id),
                "onsite" => $lsController->onsiteThisPeriod($emp_id),
                "other" => $lsController->otherThisPeriod($emp_id),
            );

            $response = array(
                "total" => $totalRecords,
                "totalNotFiltered" => $totalRecords,
                "rows" => $rows,
            );
            return response()->json($response);
        }
    }

    public function statistics_search(Request $request)
    {
        if ($request->ajax()) {
            $lsController = new LeaveStatisticsController;
            $emp_id = auth()->user()->emp_id;
            $totalRecords = 1;
            $rows = [];
            $rows[] = array(
                "late_qty" => "-",
                "late" => "-",
                "sick" => $lsController->sickThisYear($emp_id),
                "private" => $lsController->privateThisYear($emp_id),
                "absence" => "-",
                "vacation" => $lsController->vacationThisYear($emp_id),
                "compensation" => $lsController->compensationThisYear($emp_id),
                "urgent" => $lsController->urgentThisYear($emp_id),
                "unpaid" => $lsController->unpaidThisYear($emp_id),
                "maternity" => $lsController->maternityThisYear($emp_id),
                "ordination" => $lsController->ordinationThisYear($emp_id),
                "onsite" => $lsController->onsiteThisYear($emp_id),
                "other" => $lsController->otherThisYear($emp_id),
            );

            $response = array(
                "total" => $totalRecords,
                "totalNotFiltered" => $totalRecords,
                "rows" => $rows,
            );
            return response()->json($response);
        }
    }

    public function statistic_byperiod_search(Request $request)
    {
        if ($request->ajax()) {
            $this->leave_dash = self::get_dash_change();
            $lsController = new LeaveStatisticsController;
            $emp_id = auth()->user()->emp_id;
            $data = PeriodSalary::whereRaw('substring(start, 1, 4) = '.$this->leave_dash['dash_year']);
            $totalRecords = $data->select('count(*) as allcount')->count();
            $records = $data->select('*')->orderBy("month", "ASC")->orderBy("start", "ASC")->get();
            $rows = [];
            foreach ($records as $rec) {
                $rows[] = array(
                    "period" => ($rec->year + 543) . "/" . str_pad($rec->month, 2, "0", STR_PAD_LEFT) . ", " . substr($rec->start, -2) . " - " . substr($rec->end, -2),
                    "late" => "-",
                    "sick" => $lsController->sickByPeriod($emp_id, $rec->id),
                    "private" => $lsController->privateByPeriod($emp_id, $rec->id),
                    "absence" => "-",
                    "vacation" => $lsController->vacationByPeriod($emp_id, $rec->id),
                    "compensation" => $lsController->compensationByPeriod($emp_id, $rec->id),
                    "urgent" => $lsController->urgentByPeriod($emp_id, $rec->id),
                    "unpaid" => $lsController->unpaidByPeriod($emp_id, $rec->id),
                    "maternity" => $lsController->maternityByPeriod($emp_id, $rec->id),
                    "ordination" => $lsController->ordinationByPeriod($emp_id, $rec->id),
                    "onsite" => $lsController->onsiteByPeriod($emp_id, $rec->id),
                    "other" => $lsController->otherByPeriod($emp_id, $rec->id),
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

    public function history(Request $request)
    {
        $leave_type = LeaveType::where('leave_type_status', '=', 1)->orderBy('leave_type_id', 'ASC')->get();
        $period_salary = PeriodSalary::where('year', '=', date('Y'))->get();
        $leave_status = self::get_leave_status_all();
        return view('leave.leave-history', compact('leave_type', 'period_salary', 'leave_status'));
    }

    public function history_search(Request $request)
    {
        if ($request->ajax()) {
            $auth = auth()->user();
            $data = DB::table('report_leave_view')->where('emp_id', '=', $auth->emp_id)
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
                    "emp_id" => $rec->emp_id,
                    "name" => '<a href="'.url('leave/document', $rec->leave_id).'" class="text-dark"><i class="mdi mdi-file-document-outline text-primary"></i>' . $rec->name . ' ' . $rec->surname . '</a>',
                    "dept" => $rec->dept_name,
                    "leave_type" => $rec->leave_type_name,
                    "create_date" => Carbon::parse($rec->created_at)->thaidate('d/m/Y'),
                    "leave_date" => $leave_date,
                    "leave_amount" => $leave_amount,
                    "leave_status" => $leave_status,
                    "leave_reason" => $rec->leave_reason,
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

    // ############################### DASHBOARD SUMMARY ####################################

    public function privateLeave($emp_id)
    {
        $this->leave_dash = self::get_dash_change();
        // ลากิจ
        $days = 0;
        $minutes = 0;
        $data = Leave::where('emp_id', '=', $emp_id)->where('leave_type_id', '=', 1)->whereIn('leave_status', $this->status_summary)->whereRaw('substring(leave_start_date, 1, 4) = '.$this->leave_dash['dash_year'])->get();
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

    public function sickLeave($emp_id)
    {
        $this->leave_dash = self::get_dash_change();
        // ลาป่วย ทั้งหมด
        $days = 0;
        $minutes = 0;
        $data = Leave::where('emp_id', '=', $emp_id)->whereIn('leave_type_id', [2,3])->whereIn('leave_status', $this->status_summary)->whereRaw('substring(leave_start_date, 1, 4) = '.$this->leave_dash['dash_year'])->get();
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

    public function sick1Leave($emp_id)
    {
        $this->leave_dash = self::get_dash_change();
        // ลาป่วย (แนบใบรับรองแพทย์)
        $days = 0;
        $minutes = 0;
        $data = Leave::where('emp_id', '=', $emp_id)->where('leave_type_id', '=', 2)->whereIn('leave_status', $this->status_summary)->whereRaw('substring(leave_start_date, 1, 4) = '.$this->leave_dash['dash_year'])->get();
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

    public function sick2Leave($emp_id)
    {
        $this->leave_dash = self::get_dash_change();
        // ลาป่วย (ไม่มีใบรับรองแพทย์)
        $days = 0;
        $minutes = 0;
        $data = Leave::where('emp_id', '=', $emp_id)->where('leave_type_id', '=', 3)->whereIn('leave_status', $this->status_summary)->whereRaw('substring(leave_start_date, 1, 4) = '.$this->leave_dash['dash_year'])->get();
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

    public function urgentLeave($emp_id)
    {
        $this->leave_dash = self::get_dash_change();
        // ลาเร่งด่วน
        $days = 0;
        $minutes = 0;
        $data = Leave::where('emp_id', '=', $emp_id)->where('leave_type_id', '=', 4)->whereIn('leave_status', $this->status_summary)->whereRaw('substring(leave_start_date, 1, 4) = '.$this->leave_dash['dash_year'])->get();
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

    public function urgentLeaveAmount($emp_id)
    {
        $this->leave_dash = self::get_dash_change();
        // ลาเร่งด่วน (ครั้ง)
        $data = Leave::where('emp_id', '=', $emp_id)->where('leave_type_id', '=', 4)->whereIn('leave_status', $this->status_summary)->whereRaw('substring(leave_start_date, 1, 4) = '.$this->leave_dash['dash_year'])->count();
        return $data;
    }

    public function unpaidLeave($emp_id)
    {
        $this->leave_dash = self::get_dash_change();
        // ลาไม่ขอรับค่าจ้าง
        $days = 0;
        $minutes = 0;
        $data = Leave::where('emp_id', '=', $emp_id)->where('leave_type_id', '=', 5)->whereIn('leave_status', $this->status_summary)->whereRaw('substring(leave_start_date, 1, 4) = '.$this->leave_dash['dash_year'])->get();
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

    public function compensationLeave($emp_id)
    {
        $this->leave_dash = self::get_dash_change();
        // ลาหยุดชดเชย
        $days = 0;
        $minutes = 0;
        $data = Leave::where('emp_id', '=', $emp_id)->where('leave_type_id', '=', 6)->whereIn('leave_status', $this->status_summary)->whereRaw('substring(leave_start_date, 1, 4) = '.$this->leave_dash['dash_year'])->get();
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

    public function vacationLeave($emp_id)
    {
        $this->leave_dash = self::get_dash_change();
        // ลาพักร้อน
        $days = 0;
        $minutes = 0;
        $data = Leave::where('emp_id', '=', $emp_id)->where('leave_type_id', '=', 7)->whereIn('leave_status', $this->status_summary)->whereRaw('substring(leave_start_date, 1, 4) = '.$this->leave_dash['dash_year'])->get();
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

    public function ordinationLeave($emp_id)
    {
        $this->leave_dash = self::get_dash_change();
        // ลาบวช
        $days = 0;
        $minutes = 0;
        $data = Leave::where('emp_id', '=', $emp_id)->where('leave_type_id', '=', 8)->whereIn('leave_status', $this->status_summary)->whereRaw('substring(leave_start_date, 1, 4) = '.$this->leave_dash['dash_year'])->get();
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

    public function maternityLeave($emp_id)
    {
        $this->leave_dash = self::get_dash_change();
        // ลาคลอด
        $days = 0;
        $minutes = 0;
        $data = Leave::where('emp_id', '=', $emp_id)->where('leave_type_id', '=', 9)->whereIn('leave_status', $this->status_summary)->whereRaw('substring(leave_start_date, 1, 4) = '.$this->leave_dash['dash_year'])->get();
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

    public function militaryServiceLeave($emp_id)
    {
        $this->leave_dash = self::get_dash_change();
        // ลาไปรับราชการทหาร
        $days = 0;
        $minutes = 0;
        $data = Leave::where('emp_id', '=', $emp_id)->where('leave_type_id', '=', 10)->whereIn('leave_status', $this->status_summary)->whereRaw('substring(leave_start_date, 1, 4) = '.$this->leave_dash['dash_year'])->get();
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

    public function marriageLeave($emp_id)
    {
        $this->leave_dash = self::get_dash_change();
        // ลาแต่งงาน
        $days = 0;
        $minutes = 0;
        $data = Leave::where('emp_id', '=', $emp_id)->where('leave_type_id', '=', 11)->whereIn('leave_status', $this->status_summary)->whereRaw('substring(leave_start_date, 1, 4) = '.$this->leave_dash['dash_year'])->get();
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

    public function onSiteLeave($emp_id)
    {
        $this->leave_dash = self::get_dash_change();
        // ลาอบรมนอกสถานที่
        $days = 0;
        $minutes = 0;
        $data = Leave::where('emp_id', '=', $emp_id)->where('leave_type_id', '=', 12)->whereIn('leave_status', $this->status_summary)->whereRaw('substring(leave_start_date, 1, 4) = '.$this->leave_dash['dash_year'])->get();
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

    public function otherLeave($emp_id)
    {
        $this->leave_dash = self::get_dash_change();
        // ลาอื่นๆ (บวช,คลอด,ทหาร,แต่งงาน,อบรม)
        $leave = self::ordinationLeave($emp_id);
        $result["d"] = (int) $leave["d"];
        $result["h"] = (int) $leave["h"];
        $result["m"] = (int) $leave["m"];
        $leave = self::maternityLeave($emp_id);
        $result["d"] += (int) $leave["d"];
        $result["h"] += (int) $leave["h"];
        $result["m"] += (int) $leave["m"];
        $leave = self::militaryServiceLeave($emp_id);
        $result["d"] += (int) $leave["d"];
        $result["h"] += (int) $leave["h"];
        $result["m"] += (int) $leave["m"];
        $leave = self::marriageLeave($emp_id);
        $result["d"] += (int) $leave["d"];
        $result["h"] += (int) $leave["h"];
        $result["m"] += (int) $leave["m"];
        $leave = self::onSiteLeave($emp_id);
        $result["d"] += (int) $leave["d"];
        $result["h"] += (int) $leave["h"];
        $result["m"] += (int) $leave["m"];
        return $result;
    }

    public function privateTotal()
    {
        // ลากิจต่อปี
        $data = LeaveTypeProperty::where('leave_type_ppt_id', '=', 2)->where('leave_type_ppt_status', '=', 1)->first();
        if ($data) {
            return (int) $data->leave_type_ppt_day;
        }
        return 0;
    }

    public function sickTotal()
    {
        // ลาป่วยต่อปี
        $data = LeaveTypeProperty::where('leave_type_ppt_id', '=', 3)->where('leave_type_ppt_status', '=', 1)->first();
        if ($data) {
            return (int) $data->leave_type_ppt_day;
        }
        return 0;
    }

    public function vacationTotal()
    {
        // ลาพักร้อนต่อปี อายุงานครบ 1 ปี จากวันที่ผ่านทดลองงาน
        $emp_id = auth()->user()->emp_id;
        $worked = self::passed_pro_worked_days($emp_id);
        if ($worked["worked_days"] > 365) {
            $data = LeaveTypeProperty::where('leave_type_ppt_id', '=', 1)->where('leave_type_ppt_status', '=', 1)->first();
            if ($data) {
                return (int) $data->leave_type_ppt_day;
            }
        }
        return 0;
    }

    public function recordWorkingTotal($emp_id)
    {
        $this->leave_dash = self::get_dash_change();
        // ลาหยุดชดเชย ทีสามารถใช้ได้และใช้ไปแล้ว
        // $data = RecordWorking::where('emp_id', '=', $emp_id)->where('use_status', '<>', 0)->whereRaw('substring(work_date, 1, 4) = '.$this->leave_dash['dash_year'])->get();
        $data = RecordWorking::where('emp_id', '=', $emp_id)->where('use_status', '<>', 0)->get();
        return (int)$data->count();
    }
    // ############################### END ####################################

    public function dash_change(Request $request)
    {
        if ($request->ajax()) {
            if ($request->dash_year != "") {
                session()->put('leave_dash', []);
                session()->put('leave_dash', ["dash_year"=>$request->dash_year]);
            } else {
                session()->put('leave_dash', []);
                session()->put('leave_dash', ["dash_year"=>date('Y')]);
            }
            $leave_dash = session()->get('leave_dash');
            return $leave_dash;
        }
    }

    public function get_dash_change()
    {
        if (session()->has('leave_dash')) {
            $leave_dash = session()->get('leave_dash');
        } else {
            session()->put('leave_dash', []);
            session()->put('leave_dash', ["dash_year"=>date('Y')]);
            $leave_dash = session()->get('leave_dash');
        }
        return $leave_dash;
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

    public function get_emp(Request $request)
    {
        if ($request->ajax()) {
            $result = DB::table('employee as e')->leftJoin('department as d', 'e.dept_id', '=', 'd.dept_id')
            ->where('e.emp_id', '=', $request->search)
            ->orderBy("e.emp_id", "asc")->select(['e.emp_id', 'e.title', 'e.name', 'e.surname', 'e.nickname', 'e.gender', 'e.image', 'e.position_id', 'e.dept_id', 'e.area_code', 'e.emp_type', 'e.emp_status', 'd.level', 'd.dept_name'])->first();
            return response()->json($result);
        }
    }
}