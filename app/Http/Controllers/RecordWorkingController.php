<?php

namespace App\Http\Controllers;

use App\Models\RecordWorking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\App;

class RecordWorkingController extends LeaveBaseController
{
    protected $leave_dash;
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function form()
    {
        $auth = Auth::user();
        $user = DB::table('employee as e')->leftJoin('department as d', 'd.dept_id', '=', 'e.dept_id')->leftJoin('position as p', 'p.position_id', '=', 'e.position_id')
        ->where('e.emp_id', '=', $auth->emp_id)->select('e.*', 'd.dept_name', 'p.position_name')->first();

        if ($user->emp_type != "M") {
            alert()->warning('เฉพาะ "รายเดือน" เท่านั้น!');
            return back();
        }

        $dept_level = self::getDeptLevel($user->dept_id);
        $work_date = self::calcYearsMonthsDaysDiffBetweenTwoDates($user->start_work_date, date('Y-m-d'));

        return view('leave.record-working-form', compact('user', 'dept_level', 'work_date'));
    }

    public function store(Request $request)
    {
        // use_status
        // 0 ยังใช้ไม่ได้
        // 1 ใช้ได้
        // 2 ใช้งานแล้ว
        $auth = auth()->user();
        $emp_id = $auth->emp_id;
        $date_start = $request->date_start;
        $date_end = $request->date_end;
        $start_date = null;
        $end_date = null;

        $start_date = Carbon::createFromFormat('d/m/Y', $date_start)->format('Y-m-d');
        $end_date = Carbon::createFromFormat('d/m/Y', $date_end)->format('Y-m-d');
        $calc_date = self::calc_date_diff2($start_date, $end_date);
        if ($calc_date === false) {
            alert()->warning('กรุณาเลือกวันที่ทำงานให้ถูกต้อง!');
            return back();
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

        $dates = self::getBetweenDates($start_date, $end_date);
        foreach ($dates as $date) {
            $leave = RecordWorking::where('emp_id', '=', $emp_id)->where('work_date', '=', $date)->whereIn('approve_status', ['P','A1','A2','S'])->first();
            if ($leave) {
                $d = Carbon::parse($leave->work_date)->locale('th_TH')->isoFormat('dddd , LL');
                alert()->warning('มีวันซ้ำกับที่มีในระบบ!', $d)->autoClose(false);
                return back();
            }
        }

        $approve_status = "P";
        $approveM = self::getEmpApproveManager($emp_id, $emp->dept_id);
        if ($approveM !== false) {
            $approve_lid = $approveM["emp_id"];
            $approve_lip = $request->ip();
            $approve_ldate = now();
            $approve_mid = $approveM["emp_id"];
            $approve_mip = $request->ip();
            $approve_mdate = now();
            $approve_status = "A2";
        } else {
            $approve_mid = null;
            $approve_mip = null;
            $approve_mdate = null;
            $approveL = self::getEmpApproveLeader($emp_id, $emp->dept_id);
            if ($approveL !== false) {
                $approve_lid = $approveL["emp_id"];
                $approve_lip = $request->ip();
                $approve_ldate = now();
                $approve_status = "A1";
            } else {
                $approve_lid = null;
                $approve_lip = null;
                $approve_ldate = null;
            }
        }

        try {
            $dates = self::getBetweenDates($start_date, $end_date);
            foreach ($dates as $date) {
                $leave = new RecordWorking();
                $leave->emp_id = $emp_id;
                $leave->work_date = $date;
                $leave->use_status = 0;
                $leave->remark = $request->remark;
                $leave->leave_mode = 1;
                $leave->approve_status = $approve_status;
                $leave->approve_lid = $approve_lid;
                $leave->approve_lip = $approve_lip;
                $leave->approve_ldate = $approve_ldate;
                $leave->approve_mid = $approve_mid;
                $leave->approve_mip = $approve_mip;
                $leave->approve_mdate = $approve_mdate;
                $leave->save();

                $id = self::getRecordWorkingIdLatest();
                self::addRecordWorkingLog($id, "สร้างใบบันทึกวันทำงาน", auth()->user()->emp_id, $request->ip());
                if ($approve_status == "A1" || $approve_status == "A2") {
                    self::addRecordWorkingLog($id, "อนุมัติใบบันทึกวันทำงาน (โดยหัวหน้า)", auth()->user()->emp_id, $request->ip());
                    if ($approve_status == "A2") {
                        self::addRecordWorkingLog($id, "อนุมัติใบบันทึกวันทำงาน (โดยผู้จัดการ)", auth()->user()->emp_id, $request->ip());
                    }
                }
                self::recordWorkingPushNotification($id, $emp_id, $emp->dept_id, $emp->name.' '.$emp->surname);
            }
        } catch (\Exception $e) {
            return view('errors.500')->withErrors(['เกิดข้อผิดพลาด!']);
        }

        alert()->success('บันทึกวันทำงานพิเศษเรียบร้อย');
        return redirect('leave/dashboard');
    }

    public function edit($id)
    {
        $auth = Auth::user();
        $user = DB::table('employee as e')->leftJoin('department as d', 'd.dept_id', '=', 'e.dept_id')->leftJoin('position as p', 'p.position_id', '=', 'e.position_id')
        ->where('e.emp_id', '=', $auth->emp_id)->select('e.*', 'd.dept_name', 'p.position_name')->first();

        if ($user->emp_type != "M") {
            alert()->warning('เฉพาะ "รายเดือน" เท่านั้น!');
            return back();
        }

        $dept_level = self::getDeptLevel($user->dept_id);
        $work_date = self::calcYearsMonthsDaysDiffBetweenTwoDates($user->start_work_date, date('Y-m-d'));

        $leave = RecordWorking::where('id', '=', $id)->first();

        return view('leave.record-working-edit', compact('user', 'dept_level', 'work_date', 'leave'));
    }

    public function update(Request $request)
    {
        $auth = auth()->user();
        $emp_id = $auth->emp_id;

        $work_date = Carbon::createFromFormat('d/m/Y', $request->date_work)->format('Y-m-d');
        if ($work_date == "" || $work_date == null) {
            alert()->warning('กรุณาเลือกวันที่ทำงานให้ถูกต้อง!');
            return back();
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

        $check = RecordWorking::where('emp_id', '=', $emp_id)->where('work_date', '=', $work_date)->where('id', '<>', $request->id)->whereIn('approve_status', ['P','A1','A2','S'])->first();
        if ($check) {
            $d = Carbon::parse($check->work_date)->locale('th_TH')->isoFormat('dddd , LL');
            alert()->warning('มีวันซ้ำกับที่มีในระบบ!', $d)->autoClose(false);
            return back();
        }
        $leave = RecordWorking::where('id', '=', $request->id)->where('emp_id', '=', $emp_id)->first();
        if ($leave) {
            if ($leave->approve_status != "P") {
                alert()->warning('ไม่สามารถแก้ไขได้!', 'ข้อมูลอาจถูกอนุมัติไปแล้ว');
                return back();
            }
            $approve_status = "P";
            $approveM = self::getEmpApproveManager($emp_id, $emp->dept_id);
            if ($approveM !== false) {
                $approve_lid = $approveM["emp_id"];
                $approve_lip = $request->ip();
                $approve_ldate = now();
                $approve_mid = $approveM["emp_id"];
                $approve_mip = $request->ip();
                $approve_mdate = now();
                $approve_status = "A2";
            } else {
                $approve_mid = null;
                $approve_mip = null;
                $approve_mdate = null;
                $approveL = self::getEmpApproveLeader($emp_id, $emp->dept_id);
                if ($approveL !== false) {
                    $approve_lid = $approveL["emp_id"];
                    $approve_lip = $request->ip();
                    $approve_ldate = now();
                    $approve_status = "A1";
                } else {
                    $approve_lid = null;
                    $approve_lip = null;
                    $approve_ldate = null;
                }
            }
            try {
                $leave->update([
                    "work_date" => $work_date,
                    "remark" => $request->remark,
                    "approve_status" => $approve_status,
                    "approve_lid" => $approve_lid,
                    "approve_lip" => $approve_lip,
                    "approve_ldate" => $approve_ldate,
                    "approve_mid" => $approve_mid,
                    "approve_mip" => $approve_mip,
                    "approve_mdate" => $approve_mdate,
                ]);
                self::addRecordWorkingLog($request->id, "แก้ไขใบบันทึกวันทำงาน", auth()->user()->emp_id, $request->ip());
            } catch (\Exception $e) {
                return view('errors.500')->withErrors(['เกิดข้อผิดพลาด!']);
            }
        } else {
            alert()->warning('ไม่สามารถแก้ไขได้!');
            return back();
        }

        alert()->success('แก้ไขวันทำงานพิเศษเรียบร้อย');
        return redirect('leave/dashboard');
    }

    public function destroy($id)
    {
        try {
            $leave = RecordWorking::where('id', '=', $id);
            $leave->delete();
            self::recordWorkingRemoveNotification($id);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'เกิดข้อผิดพลาด!']);
        }
        return response()->json(['success' => true, 'message' => 'ลบข้อมูลเรียบร้อย']);
    }

    public function record_working_document($id)
    {
        $leave = RecordWorking::where('id', '=', $id)->first();
        if (!$leave) {
            alert()->warning('ไม่พบข้อมูล!');
            return back();
        }
        $emp = DB::table('employee as e')->leftJoin('position as p', 'e.position_id', '=', 'p.position_id')->leftJoin('department as d', 'e.dept_id', '=', 'd.dept_id')
            ->where('e.emp_id', '=', $leave->emp_id)->select(['e.*', 'p.position_name', 'd.dept_name', 'd.level'])->first();
        $worked_days = self::rw_doc_worked_days($leave->emp_id, $leave->id);
        $depts = self::getDepartmentToArray();
        $dept_arr = self::callDepartment($emp->level, $emp->dept_id, $depts);
        $leaveLeader = self::getRecordWorkingLeaderDetail($leave->id);
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

        $agent = new Agent();
        if ($agent->isPhone()) {
            $view = 'leave.record-working-document-mobile';
        } else {
            $view = 'leave.record-working-document';
        }
        return view($view, compact('leave', 'emp', 'dept_arr', 'worked_days', 'leaveLeader', 'leaveEmp', 'approvedLeader', 'approvedManager', 'approvedHR', 'status', 'recordWorkingLog'));
    }

    public function record_working_document_pdf($id)
    {
        $leave = RecordWorking::where('id', '=', $id)->first();
        $emp = DB::table('employee as e')->leftJoin('position as p', 'e.position_id', '=', 'p.position_id')->leftJoin('department as d', 'e.dept_id', '=', 'd.dept_id')
            ->where('e.emp_id', '=', $leave->emp_id)->select(['e.*', 'p.position_name', 'd.dept_name', 'd.level'])->first();
            $worked_days = self::rw_doc_worked_days($leave->emp_id, $leave->id);
        $depts = self::getDepartmentToArray();
        $dept_arr = self::callDepartment($emp->level, $emp->dept_id, $depts);
        $leaveLeader = self::getRecordWorkingLeaderDetail($leave->id);
        $leaveEmp = self::getRecordWorkingEmpDetail($leave->id);
        $approvedLeader = self::getApproveRecordWorkingLeaderDetail($leave->id);
        $approvedManager = self::getApproveRecordWorkingManagerDetail($leave->id);
        $approvedHR = self::getApproveRecordWorkingHRDetail($leave->id);
        $status = self::get_leave_status($leave->approve_status);

        $dompdf = App::make('dompdf.wrapper');

        $dompdf->loadView('leave.record-working-document-pdf', compact('leave', 'emp', 'dept_arr', 'worked_days', 'leaveLeader', 'leaveEmp', 'approvedLeader', 'approvedManager', 'approvedHR', 'status'))
        ->setPaper('a5', 'landscape')->setWarnings(false);
        $agent = new Agent();
        if ($agent->isPhone()) {
            return $dompdf->download($leave->emp_id.' '.date('Y-m-d').'.pdf');
        } else {
            return $dompdf->stream('ใบบันทึกวันทำงาน.pdf');
        }
    }

    public function record_working_history(Request $request)
    {
        $approve_status = self::get_leave_status_all();
        return view('leave.record-working-history', compact('approve_status'));
    }

    public function record_working_history_search(Request $request)
    {
        if ($request->ajax()) {
            $auth = auth()->user();
            $data = DB::table('record_working as l')->leftJoin('employee as e', 'l.emp_id', '=', 'e.emp_id')->leftJoin('department as d', 'e.dept_id', '=', 'd.dept_id')
                ->leftJoin('leave', function ($join) {
                    $join->on('leave.leave_id', '=', 'l.leave_id')->where('l.leave_id', '<>', null)->where('l.leave_id', '<>', '');
                })
                ->where('l.emp_id', '=', $auth->emp_id)
                ->where(function ($query) use ($request) {
                    if ($request->year != ""){
                        $query->whereRaw('substring(l.work_date, 1, 4) = '.$request->year);
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
            $records = $data->select('l.*', 'leave.leave_start_date', 'e.image', 'e.name', 'e.surname', 'e.nickname', 'e.emp_type', 'd.dept_name')->orderBy('l.created_at', 'desc')->get();

            $rows = [];
            $n = 1;
            foreach ($records as $rec) {
                $nickname = '';
                if ($rec->nickname != "") {
                    $nickname = ' ('.$rec->nickname.')';
                }
                $status = self::get_leave_status($rec->approve_status);
                $approve_status = '<span class="badge ' . $status["badge"] . '">' . $status["name"] . '</span>';
                if ($rec->leave_id != '') {
                    $use_date = Carbon::parse($rec->leave_start_date)->thaidate('d/m/Y');
                } else {
                    $use_date = '';
                }
                $rows[] = array(
                    "no" => $n,
                    "emp_id" => $rec->emp_id,
                    "name" => '<a href="'.url('leave/document-record-working', $rec->id).'" class="text-dark"><i class="mdi mdi-file-document-outline text-primary"></i>' . $rec->name . ' ' . $rec->surname . $nickname . '</a>',
                    "dept" => $rec->dept_name,
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
}