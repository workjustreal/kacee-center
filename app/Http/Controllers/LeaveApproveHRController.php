<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Event;
use App\Models\FingerRecord;
use App\Models\Leave;
use App\Models\RecordWorking;
use App\Models\LeaveType;
use App\Models\PeriodSalary;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use File;

class LeaveApproveHRController extends LeaveBaseController
{
    protected $attathPath;
    public function __construct()
    {
        $this->middleware('auth');
        $this->attathPath = $_SERVER['DOCUMENT_ROOT'] . '/assets/files/leave/';
    }

    public function dashboard()
    {
        $level1 = Department::where('level', '=', 1)->get();
        $level2 = Department::where('level', '=', 2)->get();
        return view('leave.approve-hr.approve-dashboard', compact('level1', 'level2'));
    }

    public function calendar(Request $request)
    {
        $dept = $request->dept;
        $start = $request->start;
        $end = $request->end;
        $result = array();
        $approved = self::leaveAll($dept, $start, $end);
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
        $approvedRecordWorking = self::recordWorkingAll($dept, $start, $end);
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

    public function leaveAll($dept, $start, $end)
    {
        // ข้อมูลใบลาทั้งหมด
        $data = DB::table('leave as l')->leftJoin('leave_type as t', 'l.leave_type_id', '=', 't.leave_type_id')->leftJoin('employee as e', 'l.emp_id', '=', 'e.emp_id')->leftJoin('department as d', 'e.dept_id', '=', 'd.dept_id')
        ->whereIn('l.leave_status', ['P', 'E', 'A1', 'A2', 'S'])
        ->where(function ($query) use ($dept, $start, $end) {
            if ($dept != "all"){
                if (str_contains($dept, "0000")) {
                    $query->where('e.dept_id', 'like', substr($dept, 0, 5).'%');
                }
                if (str_contains($dept, "000000")) {
                    $query->where('e.dept_id', 'like', substr($dept, 0, 3).'%');
                }
            } else {
                $query->where('e.dept_id', 'like', '%%');
            }
            $query->where(function ($_query) use ($start, $end) {
                $_query->whereBetween('l.leave_start_date', [$start, $end])->orWhereBetween('l.leave_end_date', [$start, $end]);
            });
        })
        ->select('l.*', 't.leave_type_name', 'e.name', 'e.surname', 'e.nickname', 'e.image', 'd.dept_name')->orderBy('l.leave_id', 'asc')->get();
        return $data;
    }

    public function recordWorkingAll($dept, $start, $end)
    {
        // ข้อมูลใบบันทึกวันทำงานทั้งหมด
        $data = DB::table('record_working as l')->leftJoin('employee as e', 'l.emp_id', '=', 'e.emp_id')->leftJoin('department as d', 'e.dept_id', '=', 'd.dept_id')
        ->whereIn('l.approve_status', ['P', 'E', 'A1', 'A2', 'S'])
        ->where(function ($query) use ($dept, $start, $end) {
            if ($dept != "all"){
                if (str_contains($dept, "0000")) {
                    $query->where('e.dept_id', 'like', substr($dept, 0, 5).'%');
                }
                if (str_contains($dept, "000000")) {
                    $query->where('e.dept_id', 'like', substr($dept, 0, 3).'%');
                }
            } else {
                $query->where('e.dept_id', 'like', '%%');
            }
            $query->whereBetween('l.work_date', [$start, $end]);
        })
        ->select('l.*', 'e.name', 'e.surname', 'e.nickname', 'e.image', 'd.dept_name')->orderBy('l.id', 'asc')->get();
        return $data;
    }

    public function emp_leave_edit($id)
    {
        $auth = Auth::user();
        $leave = Leave::where('leave_id', '=', $id)->first();
        if (!$leave) {
            alert()->warning('ไม่พบใบลา!');
            return back();
        }
        $user = DB::table('employee as e')->leftJoin('department as d', 'd.dept_id', '=', 'e.dept_id')->leftJoin('position as p', 'p.position_id', '=', 'e.position_id')
        ->where('e.emp_id', '=', $leave->emp_id)->select('e.*', 'd.dept_name', 'p.position_name')->first();

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

        if (!Auth::User()->isAdmin() && !Auth::User()->manageLeave()) {
            alert()->warning('ไม่มีสิทธิ์แก้ไขใบลานี้!');
            return back();
        }

        return view('leave.approve-hr.emp-leave-edit', compact('user', 'dept_level', 'period', 'pre_period', 'leave', 'leave_type', 'leave_type_detail', 'sum_time', 'work_date', 'hol_arr', 'record_working_arr', 'record_working'));
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
        $leave_reason_hr = $request->leave_reason_hr;
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
            self::_updateLeave($leave_id, $leave_start_date, $leave_start_time, $leave_end_date, $leave_end_time, $leave_reason, $leave_reason_hr, $leave_attach, $leave_day, $leave_minute, $leave_type, $period_salary_id, $leave_range, $leave_mode, $leader_id, $emp_id, $emp->emp_type);
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
            self::addLeaveLog($leave_id, "แก้ไขใบลางาน (โดยบุคคล)", auth()->user()->emp_id, $request->ip());
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
        return redirect('leave/approve-hr/leave-approve');
    }

    public function _updateLeave($leave_id, $leave_start_date, $leave_start_time, $leave_end_date, $leave_end_time, $leave_reason, $leave_reason_hr, $leave_attach, $leave_day, $leave_minute, $leave_type, $period_salary_id, $leave_range, $leave_mode, $leader_id, $emp_id, $emp_type)
    {
        $leave = Leave::where('leave_id', '=', $leave_id);
        $leave->update([
            "leave_start_date" => $leave_start_date,
            "leave_start_time" => $leave_start_time,
            "leave_end_date" => $leave_end_date,
            "leave_end_time" => $leave_end_time,
            "leave_reason" => $leave_reason,
            "leave_reason_hr" => $leave_reason_hr,
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

    public function leave_approve(Request $request)
    {
        $leave_type = LeaveType::where('leave_type_status', '=', 1)->orderBy('leave_type_id', 'ASC')->get();
        $period_salary = PeriodSalary::orderBy('year', 'asc')->orderBy('month', 'asc')->get();
        $leave_status = self::get_leave_status();
        $level2 = Department::where('level', '=', 2)->get();
        $level3 = Department::where('level', '=', 3)->get();
        $leave_count = Leave::whereIn('leave_status', ['A2'])->count();
        return view('leave.approve-hr.leave-approve', compact('leave_type', 'period_salary', 'leave_status', 'level2', 'level3', 'leave_count'));
    }

    public function search_leave_approve(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('report_leave_view')->whereIn('leave_status', ['A2'])
                ->where(function ($query) use ($request) {
                    if ($request->year != "all"){
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
                    if ($request->level3 != "all"){
                        $query->where('dept_id', 'like', substr($request->level3, 0, 7).'%');
                    } else if ($request->level2 != "all"){
                        $query->where('dept_id', 'like', substr($request->level2, 0, 5).'%');
                    }
                });
            $totalRecords = $data->select('count(*) as allcount')->count();
            $records = $data->select('*')->orderBy('created_at', 'asc')->get();
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
                    $leave_date = Carbon::parse($rec->leave_start_date)->thaidate('d/m/Y') . '-' . Carbon::parse($rec->leave_end_date)->thaidate('d/m/Y');
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
                    $action = '<div>
                            <a class="action-icon" href="'.url('leave/document', $rec->leave_id).'" title="ดู"><i class="mdi mdi-eye"></i></a>
                            <a class="action-icon" href="'.url('leave/approve-hr/emp-leave-edit', $rec->leave_id).'" title="แก้ไข"><i class="mdi mdi-square-edit-outline"></i></a>
                            <a class="action-icon" href="javascript:void(0);" onclick="cancelHRLeaveConfirmation(\''.$rec->leave_id.'\')" title="ยกเลิก"><i class="mdi mdi-cancel"></i></a>
                        </div>';
                $rows[] = array(
                    "leave_id" => $rec->leave_id,
                    "leave_status" => $rec->leave_status,
                    "no" => $n,
                    "emp_id" => $emp_id,
                    "name" => $rec->name . ' ' . $rec->surname . $nickname,
                    "dept" => $rec->dept_name . $area_code,
                    "leave_type" => $rec->leave_type_name,
                    "create_date" => Carbon::parse($rec->created_at)->thaidate('d/m/Y'),
                    "leave_date" => $leave_date,
                    "leave_amount" => $leave_time . $leave_amount,
                    "leave_status_text" => $leave_status,
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

    public function leave_approve_submit(Request $request)
    {
        $auth = auth()->user();
        $data = $request->leave_id;
        if (count($data) <= 0) {
            return response()->json(['success' => false, 'message' => 'ไม่พบข้อมูล!']);
        }
        for ($i=0; $i<count($data); $i++) {
            $leave = Leave::where('leave_id', '=', $data[$i])->where('leave_status', '=', 'A2');
            $leave->update([
                "approve_hrid" => $auth->emp_id,
                "approve_hrip" => $request->ip(),
                "approve_hrdate" => now(),
                "leave_status" => 'S',
            ]);
            self::addLeaveLog($data[$i], "อนุมัติใบลางาน (โดยบุคคล)", auth()->user()->emp_id, $request->ip());
        }
        return response()->json(['success' => true, 'message' => 'อนุมัติการลางานเรียบร้อย']);
    }

    public function emp_leave_return(Request $request)
    {
        $id = $request->leave_id;
        $leave = Leave::where('leave_id', '=', $id)->first();
        if ($leave) {
            DB::beginTransaction();
            try {
                Leave::where('leave_id', '=', $id)->update(["leave_status" => "E"]);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json(['success' => false, 'message' => 'ไม่สามารถถอยสถานะได้']);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'ไม่พบข้อมูล!']);
        }
        self::addLeaveLog($id, "ถอยสถานะใบลางาน (โดยบุคคล) #".$request->return_remark, auth()->user()->emp_id, $request->ip());
        self::leaveRemoveNotification($id);
        return response()->json(['success' => true, 'message' => 'ถอยสถานะเรียบร้อย']);
    }

    public function emp_leave_cancel(Request $request)
    {
        $id = $request->leave_id;
        $leave = Leave::where('leave_id', '=', $id)->first();
        if ($leave) {
            $approve_hrid = auth()->user()->emp_id;
            $approve_hrip = $request->ip();
            $approve_hrdate = now();
            $leave_status = "C3";

            DB::beginTransaction();
            try {
                $leaveUpdate = Leave::where('leave_id', '=', $id);
                $leaveUpdate->update([
                    "approve_hrid" => $approve_hrid,
                    "approve_hrip" => $approve_hrip,
                    "approve_hrdate" => $approve_hrdate,
                    "leave_status" => $leave_status,
                    "leave_cancel_remark" => $request->cancel_remark,
                ]);
                $record_working = RecordWorking::where('emp_id', '=', $leave->emp_id)->where('use_status', '=', 2)->where('leave_id', '=', $id)->first();
                if ($record_working) {
                    // ถอยสถานะวันหยุดกลับคืน
                    $leaveS = RecordWorking::find($record_working->id);
                    $leaveS->update([
                        "use_status" => 1,
                        "leave_id" => null,
                    ]);
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json(['success' => false, 'message' => 'ไม่สามารถยกเลิกได้']);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'ไม่พบข้อมูล!']);
        }
        self::addLeaveLog($id, "ยกเลิกใบลางาน (โดยบุคคล)", auth()->user()->emp_id, $request->ip());
        self::leaveCancelPushNotification($id);
        return response()->json(['success' => true, 'message' => 'ยกเลิกเรียบร้อย']);
    }

    // ======================================= บันทึกวันทำงาน ========================================

    public function record_working_approve(Request $request)
    {
        $approve_status = self::get_leave_status();
        $level2 = Department::where('level', '=', 2)->get();
        $level3 = Department::where('level', '=', 3)->get();
        $leave_count = RecordWorking::whereIn('approve_status', ['A2'])->count();
        return view('leave.approve-hr.record-working-approve', compact('approve_status', 'level2', 'level3', 'leave_count'));
    }

    public function search_record_working_approve(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('record_working as l')->leftJoin('employee as e', 'l.emp_id', '=', 'e.emp_id')->leftJoin('department as d', 'e.dept_id', '=', 'd.dept_id')
                ->whereIn('approve_status', ['A2'])
                ->where(function ($query) use ($request) {
                    if ($request->year != "all"){
                        $query->whereRaw('substring(l.work_date, 1, 4) = '.$request->year);
                    }
                    if ($request->emp_type != "all"){
                        $query->where('e.emp_type', '=', $request->emp_type);
                    }
                    if ($request->approve_status != "all"){
                        $query->where('l.approve_status', '=', $request->approve_status);
                    }
                    if ($request->level3 != "all"){
                        $query->where('e.dept_id', 'like', substr($request->level3, 0, 7).'%');
                    } else if ($request->level2 != "all"){
                        $query->where('e.dept_id', 'like', substr($request->level2, 0, 5).'%');
                    }
                });
            $totalRecords = $data->select('count(l.*) as allcount')->count();
            $records = $data->select('l.*', 'e.image', 'e.name', 'e.surname', 'e.nickname', 'e.emp_type', 'e.area_code', 'd.dept_name')->orderBy('l.created_at', 'asc')->get();

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
                $manage = '<div>
                            <a class="action-icon" href="'.url('leave/document-record-working', $rec->id).'" title="ดู"><i class="mdi mdi-eye"></i></a>
                            <a class="action-icon" href="javascript:void(0);" onclick="cancelHRRecordWorkingConfirmation(\''.$rec->id.'\')" title="ยกเลิก"><i class="mdi mdi-cancel"></i></a>
                        </div>';
                $rows[] = array(
                    "id" => $rec->id,
                    "approve_status" => $rec->approve_status,
                    "no" => $n,
                    "emp_id" => $emp_id,
                    "name" => $rec->name . ' ' . $rec->surname . $nickname,
                    "dept" => $rec->dept_name . $area_code,
                    "create_date" => Carbon::parse($rec->created_at)->thaidate('d/m/Y'),
                    "work_date" => Carbon::parse($rec->work_date)->thaidate('d/m/Y'),
                    "remark" => $rec->remark,
                    "status" => $approve_status,
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

    public function record_working_approve_submit(Request $request)
    {
        $auth = auth()->user();
        $data = $request->id;
        if (count($data) <= 0) {
            return response()->json(['success' => false, 'message' => 'ไม่พบข้อมูล!']);
        }
        for ($i=0; $i<count($data); $i++) {
            $leave = RecordWorking::where('id', '=', $data[$i])->where('approve_status', '=', 'A2');
            $leave->update([
                "use_status" => 1,
                "approve_hrid" => $auth->emp_id,
                "approve_hrip" => $request->ip(),
                "approve_hrdate" => now(),
                "approve_status" => 'S',
            ]);
            self::addRecordWorkingLog($data[$i], "อนุมัติใบบันทึกวันทำงาน (โดยบุคคล)", auth()->user()->emp_id, $request->ip());
        }
        return response()->json(['success' => true, 'message' => 'อนุมัติบันทึกวันทำงานเรียบร้อย']);
    }

    public function emp_record_working_return(Request $request)
    {
        $id = $request->id;
        $leave = RecordWorking::where('id', '=', $id)->first();
        if ($leave) {
            DB::beginTransaction();
            try {
                RecordWorking::where('id', '=', $id)->update(["approve_status" => "E"]);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json(['success' => false, 'message' => 'ไม่สามารถถอยสถานะได้']);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'ไม่พบข้อมูล!']);
        }
        self::addLeaveLog($id, "ถอยสถานะใบบันทึกวันทำงาน (โดยบุคคล) #".$request->return_remark, auth()->user()->emp_id, $request->ip());
        self::recordWorkingRemoveNotification($id);
        return response()->json(['success' => true, 'message' => 'ถอยสถานะเรียบร้อย']);
    }

    public function emp_record_working_cancel(Request $request)
    {
        $id = $request->id;
        $leave = RecordWorking::where('id', '=', $id)->first();
        if ($leave) {
            $approve_hrid = auth()->user()->emp_id;
            $approve_hrip = $request->ip();
            $approve_hrdate = now();
            $approve_status = "C3";

            $leaveUpdate = RecordWorking::where('id', '=', $id);
            $leaveUpdate->update([
                "approve_hrid" => $approve_hrid,
                "approve_hrip" => $approve_hrip,
                "approve_hrdate" => $approve_hrdate,
                "approve_status" => $approve_status,
                "use_status" => 0,
                "cancel_remark" => $request->cancel_remark,
            ]);
            self::addRecordWorkingLog($id, "ยกเลิกใบบันทึกวันทำงาน (โดยบุคคล)", auth()->user()->emp_id, $request->ip());
            self::recordWorkingCancelPushNotification($id);
        } else {
            return response()->json(['success' => false, 'message' => 'ไม่พบข้อมูล!']);
        }
        return response()->json(['success' => true, 'message' => 'ยกเลิกเรียบร้อย']);
    }

    // ======================================= END ========================================

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
}