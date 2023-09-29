<?php

namespace App\Http\Controllers;

use App\Exports\LeaveRecordFormExport;
use App\Models\Department;
use App\Models\Leave;
use App\Models\LeaveLog;
use App\Models\LeaveRecordDetail;
use App\Models\LeaveRecordHeader;
use App\Models\RecordWorking;
use App\Models\RecordWorkingLog;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Maatwebsite\Excel\Facades\Excel;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LeaveRecordController extends LeaveBaseController
{
    protected $columns;
    protected $fixEmp;

    public function __construct()
    {
        $this->middleware('auth');
        $this->columns = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        $this->fixEmp = array('580073');
    }

    public function index()
    {
        $months = self::get_months();
        $department = Department::where('level', '=', 2)->where('dept_id', 'like', 'A02%')->whereNotIn('dept_id', ['A02050000'])->get();
        return view('leave.leave-record-history')->with('months', $months)->with('department', $department);
    }

    public function search(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('leave_record_header as l')->leftJoin('department as d', 'd.dept_id', '=', 'l.dept_id')->leftJoin('employee as e', 'e.emp_id', '=', 'l.create_id')
                ->where(function ($query) use ($request) {
                    if ($request->year != ""){
                        $query->where('l.year', '=', $request->year);
                    }
                    if ($request->month != "all"){
                        $query->where('l.month', '=', $request->month);
                    }
                    if ($request->dept != "all"){
                        $query->where('l.dept_id', '=', $request->dept);
                    }
                });
            $totalRecords = $data->select('count(l.*) as allcount')->count();
            $records = $data->select('l.*', 'd.dept_name', 'e.name', 'e.surname', 'e.nickname')->orderBy('l.lr_id', 'desc')->get();
            $rows = [];
            $n = 1;
            foreach ($records as $rec) {
                $nickname = ($rec->nickname != "") ? ' ('.$rec->nickname.')' : '';
                $manage = '<div class="d-inline-flex flex-row">';
                $manage .= '<a class="action-icon" href="'.url('leave/leave-record-view/id', $rec->lr_id).'" title="ดู"><i class="mdi mdi-eye"></i></a>';
                if (auth()->user()->isAdmin()) {
                    $manage .= '<a class="action-icon" href="javascript:void(0);" onclick="deleteLeaveRecordConfirmation(\''.$rec->lr_id.'\')" title="ลบ"><i class="mdi mdi-delete"></i></a>';
                }
                $manage .= '</div>';
                $rows[] = array(
                    "no" => $n,
                    "year" => $rec->year + 543,
                    "month" => self::get_month($rec->month),
                    "dept_name" => $rec->dept_name.' ('.$rec->dept_id.')',
                    "create_id" => $rec->name . ' ' . $rec->surname . $nickname,
                    "create_date" => Carbon::parse($rec->created_at)->thaidate('d/m/Y'),
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

    public function view($id)
    {
        $months = self::get_months();
        $header = DB::table('leave_record_header as l')->leftJoin('department as d', 'd.dept_id', '=', 'l.dept_id')->where('l.lr_id', '=', $id)->first(['l.*', 'd.dept_name']);
        if (!$header) {
            alert()->warning('ไม่พบข้อมูล!');
            return back();
        }
        return view('leave.leave-record-view')->with('months', $months)->with('header', $header);
    }

    public function view_search(Request $request)
    {
        if ($request->ajax()) {
            $dept = $request->get('dept');
            $month = $request->get('month');
            $id = $request->get('id');
            $header = DB::table('leave_record_header as l')->leftJoin('department as d', 'd.dept_id', '=', 'l.dept_id')->where('l.lr_id', '=', $id)->first(['l.*', 'd.dept_name']);
            $data = self::callDataView($dept, $month, $id);

            $output = '';
            $total_row = $data["rows_count"];
            if ($total_row > 0) {
                $output .= '<div class="alert alert-secondary" role="alert">';
                for ($i=0; $i<count($data["title"]); $i++) {
                    $output .= '<span>'.$data["title"]["title".($i+1)].'</span><br>';
                }
                $output .= '</div>';
                $date = Carbon::createFromFormat('d/m/Y', "01/".$month)->format('Y-m-d');
                $output .= '<h4 class="text-center"><span class="text-primary">'.$header->dept_name.'</span>  '.Carbon::parse($date)->thaidate('เดือน F ปี พ.ศ. Y').'</h4>';
                $output .= '<input type="hidden" id="rows_count" name="rows_count" value="'.$data["rows_count"].'">';
                $output .= '<input type="hidden" id="sat_count" name="sat_count" value="'.$data["sat_count"].'">';
                $output .= '<input type="hidden" id="sat_list" name="sat_list" value="'.$data["sat_list"].'">';
                $output .= '<table class="table table-bordered table-striped nowrap w-100">';
                $output .= '<thead class="table-info text-center align-top">';
                $output .= '<tr>';
                $c = 0;
                for ($i=0; $i<count($data["headers"]["header1"]); $i++) {
                    if ($i >= 7) {
                        if ($data["headers"]["header1"][$this->columns[$c]] != "") {
                            $output .= '<th colspan="2" class="p-1">'.$data["headers"]["header1"][$this->columns[$c]].'</th>';
                        }
                    } else {
                        $output .= '<th rowspan="2" class="p-1">'.$data["headers"]["header1"][$this->columns[$c]].'</th>';
                    }
                    $c++;
                }
                $output .= '</tr>';
                $output .= '<tr>';
                $c = 0;
                for ($i=0; $i<count($data["headers"]["header2"]); $i++) {
                    if ($data["headers"]["header2"][$this->columns[$c]] != "") {
                        if (substr_count($data["headers"]["header2"][$this->columns[$c]], "เลือกวันหยุด")) {
                            $output .= '<th class="p-1 text-nowrap">วันหยุด</th>';
                        } else if (substr_count($data["headers"]["header2"][$this->columns[$c]], "หยุด")) {
                            $output .= '<th class="p-1 text-nowrap">หยุด/ทำงาน</th>';
                        }
                    }
                    $c++;
                }
                $output .= '</tr>';
                $output .= '</thead>';
                $output .= '<tbody>';
                for ($i=0; $i<count($data["rows"]); $i++) {
                    $output .= '<tr>';
                    $c = 0;
                    $col = 0;
                    $sat = 0;
                    for ($j=0; $j<count($data["rows"][$i]); $j++) {
                        if ($j >= 7) {
                            if ($col == 0) {
                                $swich_chk = ($data["rows"][$i][$this->columns[$c]]=="on") ? "checked" : "";
                                $output .= '<td class="p-1 text-center"><div class="form-check form-switch d-flex justify-content-center"><input class="form-check-input" type="checkbox" id="status['.$i.']['.$sat.']" name="status['.$i.']['.$sat.']" '.$swich_chk.' style="pointer-events: none;"></div></td>';
                                $col++;
                            }  else if ($col == 1) {
                                $col++;
                            } else {
                                $output .= '<td class="p-1 text-center">
                                    <a href="'.$data["rows"][$i][$this->columns[$c]]["href"].'"><u>'.$data["rows"][$i][$this->columns[$c]]["date"].'</u></a>
                                </td>';
                                $col = 0;
                                $sat++;
                            }
                        } else {
                            $input = ($j == 0) ? '<input type="hidden" id="emp_id['.$i.']" name="emp_id['.$i.']" value="'.$data["rows"][$i][$this->columns[1]].'">' : '';
                            $output .= '<td class="p-1">'.$input.$data["rows"][$i][$this->columns[$c]].'</td>';
                        }
                        $c++;
                    }
                    $output .= '</tr>';
                }
                $output .= '</tbody>';
                $output .= '</table>';
            }
            $data = array(
                'table_data'  => $output,
                'total_data'  => $total_row,
            );
            echo json_encode($data);
        }
    }

    public function callDataView($dept_id, $month, $id)
    {
        $dept = substr($dept_id, 0, 5);
        $exp = explode("/", $month);
        $year = $exp[1];
        $month = $exp[0];
        $date_select = "01/".$month."/".$year;
        $department = Department::where('dept_id', '=', $dept_id)->first();
        $dept_name = $department->dept_name;

        $headers["header1"] = array(
            "A" => "ลำดับ",
            "B" => "รหัสพนักงาน",
            "C" => "ชื่อ",
            "D" => "นามสกุล",
            "E" => "ชื่อเล่น",
            "F" => "หน่วยงาน",
            "G" => "เขตการขาย",
        );
        $headers["header2"] = array(
            "A" => "",
            "B" => "",
            "C" => "",
            "D" => "",
            "E" => "",
            "F" => "",
            "G" => "",
        );
        $more = 0;
        $c = 7;
        $sat_count = 0;
        foreach (self::getSaturdays($year, $month) as $saturdays) {
            $exp = explode("-", $saturdays->format("Y-m-d"));
            $headers["header1"][$this->columns[$c]] = "วันเสาร์ที่ " . $exp[2] . '/' . $exp[1] . '/' . substr(($exp[0] + 543), 2, 2);
            $headers["header2"][$this->columns[$c]] = "หยุด";
            $c++;
            $headers["header1"][$this->columns[$c]] = "";
            $headers["header2"][$this->columns[$c]] = "ทำงาน";
            $c++;
            $headers["header1"][$this->columns[$c]] = "";
            $headers["header2"][$this->columns[$c]] = "เลือกวันหยุด";
            $c++;

            $more++;
            $sat_count++;
        }
        $sat_arr = [];
        foreach (self::getSaturdays($year, $month) as $saturdays) {
            $sat_arr[] = $saturdays->format("Y-m-d");
        }

        $i = 0;
        $rows = [];
        $users = self::getUsers($dept);
        foreach ($users as $user) {
            $position = ($user->position_id > 0) ? "<br>(".$user->position_name.")" : "";
            $area_description = ($user->area_description != "") ? ' (' . $user->area_description . ')' : "";
            $rows[$i] = array(
                "A" => $i + 1,
                "B" => $user->emp_id,
                "C" => $user->name,
                "D" => $user->surname,
                "E" => $user->nickname,
                "F" => $user->dept_name . $position,
                "G" => $user->area_code . $area_description,
            );

            $listLR = DB::table('leave_record_header as h')->leftJoin('leave_record_detail as d', 'd.lr_id', '=', 'h.lr_id')->where('h.lr_id', '=', $id)->where('d.emp_id', '=', $user->emp_id)->orderBy('d.sat')->get(['d.lr_id', 'd.emp_id', 'd.sat', 'd.rw_id', 'd.leave_id']);

            $c = 7;
            $j = 1;
            foreach (self::getSaturdays($year, $month) as $saturdays) {
                $work_date = "";
                $leave_date = "";
                $leave_href = "javascript:void(0);";
                foreach ($listLR as $list) {
                    if ($list->sat == $j) {
                        $work_date = Carbon::parse($saturdays->format("Y-m-d"))->format("d/m/Y");
                        $leave = Leave::where('leave_id', '=', $list->leave_id)->first('leave_start_date');
                        $leave_date = ($leave) ? Carbon::parse($leave->leave_start_date)->format("d/m/Y") : "";
                        $leave_href = ($leave) ? url('leave/document', $list->leave_id) : "javascript:void(0);";
                        break;
                    }
                }
                $rows[$i][$this->columns[$c]] = ($leave_date!="") ? "on" : "off";
                $c++;
                $rows[$i][$this->columns[$c]] = $work_date;
                $c++;
                $rows[$i][$this->columns[$c]]["date"] = $leave_date;
                $rows[$i][$this->columns[$c]]["href"] = $leave_href;
                $c++;
                $j++;
            }
            $i++;
        }
        $data = array(
            "title" => [
                "title1" => "แจ้งตารางวันหยุด ประจำเดือน " . Carbon::createFromFormat('d/m/Y', $date_select)->thaidate('F') . " ปี พ.ศ. " . Carbon::createFromFormat('d/m/Y', $date_select)->thaidate('Y') . " (" . $dept_name . ")",
                "title2" => "แจ้งชื่อผู้ที่จะมาทำงานวันเสาร์ และเลือกวันหยุดทดแทนที่ต้องการได้เลยครับ (เลือกวันหยุดได้หลังจากเสาร์ที่เรามาทำงานแล้วเท่านั้น)",
                "title3" => "วันเสาร์ที่ตนเองต้องมาทำงาน ขอให้รับผิดชอบมาทำงานอย่างเคร่งครัดด้วยนะครับ หากมาไม่ได้จริงๆให้สลับเพื่อนมาทำงาน",
            ],
            "headers" => $headers,
            "rows_count" => $users->count(),
            "rows" => $rows,
            "more" => $more,
            "sat_count" => $sat_count,
            "sat_list" => implode(',', $sat_arr),
        );
        return $data;
    }

    public function form()
    {
        if (!Auth::User()->isAccessLeaveRacord()) {
            alert()->warning('คุณไม่มีสิทธิ์ใช้งานในส่วนนี้!');
            return redirect()->back();
        }
        $department = Department::where('level', '=', 2)->where('dept_id', 'like', 'A02%')->whereNotIn('dept_id', ['A02050000'])->get();
        return view('leave.leave-record-form')->with('department', $department);
    }

    public function callData($dept_id, $month, $export=false)
    {
        $dept = substr($dept_id, 0, 5);
        $exp = explode("/", $month);
        $year = $exp[1];
        $month = $exp[0];
        $date_select = "01/".$month."/".$year;
        $diff_month = self::diffTotalMonths(date('Y-m')."-01", $year."-".$month."-01");
        $department = Department::where('dept_id', '=', $dept_id)->first();
        $dept_name = $department->dept_name;

        $headers["header1"] = array(
            "A" => "ลำดับ",
            "B" => "รหัสพนักงาน",
            "C" => "ชื่อ",
            "D" => "นามสกุล",
            "E" => "ชื่อเล่น",
            "F" => "หน่วยงาน",
            "G" => "เขตการขาย",
        );
        $headers["header2"] = array(
            "A" => "",
            "B" => "",
            "C" => "",
            "D" => "",
            "E" => "",
            "F" => "",
            "G" => "",
        );
        $more = 0;
        $c = 7;
        $sat_count = 0;
        foreach (self::getSaturdays($year, $month) as $saturdays) {
            $exp = explode("-", $saturdays->format("Y-m-d"));
            $headers["header1"][$this->columns[$c]] = "วันเสาร์ที่ " . $exp[2] . '/' . $exp[1] . '/' . substr(($exp[0] + 543), 2, 2);
            $headers["header2"][$this->columns[$c]] = "หยุด";
            $c++;
            $headers["header1"][$this->columns[$c]] = "";
            $headers["header2"][$this->columns[$c]] = "ทำงาน";
            $c++;
            $headers["header1"][$this->columns[$c]] = "";
            $headers["header2"][$this->columns[$c]] = "เลือกวันหยุด";
            $c++;

            $more++;
            $sat_count++;
        }
        $sat_arr = [];
        foreach (self::getSaturdays($year, $month) as $saturdays) {
            $sat_arr[] = $saturdays->format("Y-m-d");
        }

        $i = 0;
        $rows = [];
        $users = self::getUsers($dept);
        foreach ($users as $user) {
            if ($export) {
                $position = ($user->position_id > 0) ? " (".$user->position_name.")" : "";
            } else {
                $position = ($user->position_id > 0) ? "<br>(".$user->position_name.")" : "";
            }
            $area_description = ($user->area_description != "") ? ' (' . $user->area_description . ')' : "";
            $rows[$i] = array(
                "A" => $i + 1,
                "B" => $user->emp_id,
                "C" => $user->name,
                "D" => $user->surname,
                "E" => $user->nickname,
                "F" => $user->dept_name . $position,
                "G" => $user->area_code . $area_description,
            );
            $c = 7;
            foreach (self::getSaturdays($year, $month) as $saturdays) {
                $rows[$i][$this->columns[$c]] = "";
                $c++;
                $rows[$i][$this->columns[$c]] = "";
                $c++;
                $rows[$i][$this->columns[$c]] = "";
                $c++;
            }
            $i++;
        }
        $data = array(
            "title" => [
                "title1" => "แจ้งตารางวันหยุด ประจำเดือน " . Carbon::createFromFormat('d/m/Y', $date_select)->thaidate('F') . " ปี พ.ศ. " . Carbon::createFromFormat('d/m/Y', $date_select)->thaidate('Y') . " (" . $dept_name . ")",
                "title2" => "แจ้งชื่อผู้ที่จะมาทำงานวันเสาร์ และเลือกวันหยุดทดแทนที่ต้องการได้เลยครับ (เลือกวันหยุดได้หลังจากเสาร์ที่เรามาทำงานแล้วเท่านั้น)",
                "title3" => "วันเสาร์ที่ตนเองต้องมาทำงาน ขอให้รับผิดชอบมาทำงานอย่างเคร่งครัดด้วยนะครับ หากมาไม่ได้จริงๆให้สลับเพื่อนมาทำงาน",
            ],
            "headers" => $headers,
            "rows_count" => $users->count(),
            "rows" => $rows,
            "more" => $more,
            "diff_month" => $diff_month,
            "sat_count" => $sat_count,
            "sat_list" => implode(',', $sat_arr),
        );
        return $data;
    }

    public function form_search(Request $request)
    {
        if ($request->ajax()) {
            $dept = $request->get('dept');
            $month = $request->get('month');
            $_year = substr($month, 3, 4);
            $_month = substr($month, 0, 2);
            $header = LeaveRecordHeader::where('year', '=', (int)$_year)->where('month', '=', (int)$_month)->where('dept_id', '=', $dept)->get();
            if ($header->isNotEmpty()) {
                $output = '<div class="alert alert-danger text-center" role="alert">';
                $output .= '<span>ข้อมูลของเดือน '.self::get_month($_month).' ปี '.($_year+543).' ถูกสร้างไปแล้ว!</span>';
                $output .= '</div>';
                $data = array(
                    'table_data'  => $output,
                    'total_data'  => 0,
                    'diff_month'  => 0,
                );
                echo json_encode($data);
                return false;
            }
            $data = self::callData($dept, $month);

            $output = '';
            $total_row = $data["rows_count"];
            if ($total_row > 0) {
                $output .= '<div class="alert alert-secondary" role="alert">';
                for ($i=0; $i<count($data["title"]); $i++) {
                    $output .= '<span>'.$data["title"]["title".($i+1)].'</span><br>';
                }
                $output .= '</div>';
                $date = Carbon::createFromFormat('d/m/Y', "01/".$month)->format('Y-m-d');
                $output .= '<h4 class="text-center">'.Carbon::parse($date)->thaidate('เดือน F ปี พ.ศ. Y').'</h4>';
                $output .= '<input type="hidden" id="rows_count" name="rows_count" value="'.$data["rows_count"].'">';
                $output .= '<input type="hidden" id="sat_count" name="sat_count" value="'.$data["sat_count"].'">';
                $output .= '<input type="hidden" id="sat_list" name="sat_list" value="'.$data["sat_list"].'">';
                $output .= '<table class="table table-bordered table-striped nowrap w-100">';
                $output .= '<thead class="table-success text-center align-top">';
                $output .= '<tr>';
                $c = 0;
                for ($i=0; $i<count($data["headers"]["header1"]); $i++) {
                    if ($i >= 7) {
                        if ($data["headers"]["header1"][$this->columns[$c]] != "") {
                            $output .= '<th colspan="2" class="p-1">'.$data["headers"]["header1"][$this->columns[$c]].'</th>';
                        }
                    } else {
                        $output .= '<th rowspan="2" class="p-1">'.$data["headers"]["header1"][$this->columns[$c]].'</th>';
                    }
                    $c++;
                }
                $output .= '</tr>';
                $output .= '<tr>';
                $c = 0;
                for ($i=0; $i<count($data["headers"]["header2"]); $i++) {
                    if ($data["headers"]["header2"][$this->columns[$c]] != "") {
                        if (substr_count($data["headers"]["header2"][$this->columns[$c]], "เลือกวันหยุด")) {
                            $output .= '<th class="p-1 text-nowrap">เลือกวันหยุด</th>';
                        } else if (substr_count($data["headers"]["header2"][$this->columns[$c]], "หยุด")) {
                            $output .= '<th class="p-1 text-nowrap">หยุด/ทำงาน</th>';
                        }
                    }
                    $c++;
                }
                $output .= '</tr>';
                $output .= '</thead>';
                $output .= '<tbody>';
                for ($i=0; $i<count($data["rows"]); $i++) {
                    $output .= '<tr>';
                    $c = 0;
                    $col = 0;
                    $sat = 0;
                    for ($j=0; $j<count($data["rows"][$i]); $j++) {
                        if ($j >= 7) {
                            if ($col == 0) {
                                $output .= '<td class="p-1 text-center"><div class="form-check form-switch d-flex justify-content-center"><input class="form-check-input" type="checkbox" role="switch" id="status['.$i.']['.$sat.']" name="status['.$i.']['.$sat.']" onclick="toggleLeaveDate(\''.$i.'\', \''.$sat.'\');" /></div></td>';
                                $col++;
                            }  else if ($col == 1) {
                                $col++;
                            } else {
                                $output .= '<td class="p-1 text-center"><input type="text" class="form-control form-control-sm leave-datepicker px-1" alt="'.$sat.'" id="leave_date['.$i.']['.$sat.']" name="leave_date['.$i.']['.$sat.']" placeholder="เลือกวันหยุด" style="width:100px;background-color:lightgrey;" disabled></td>';
                                $col = 0;
                                $sat++;
                            }
                        } else {
                            $input = ($j == 0) ? '<input type="hidden" id="emp_id['.$i.']" name="emp_id['.$i.']" value="'.$data["rows"][$i][$this->columns[1]].'">' : '';
                            $output .= '<td class="p-1">'.$input.$data["rows"][$i][$this->columns[$c]].'</td>';
                        }
                        $c++;
                    }
                    $output .= '</tr>';
                }
                $output .= '</tbody>';
                $output .= '</table>';
            }
            $data = array(
                'table_data'  => $output,
                'total_data'  => $total_row,
                'diff_month'  => $data["diff_month"],
            );
            echo json_encode($data);
        }
    }

    public function store(Request $request)
    {
        $dept = $request->post('dept');
        $month = $request->post('month');
        $year = substr($month, 3, 4);
        $month = substr($month, 0, 2);
        $content = json_decode($request->getContent(), true);
        $content = $content['data'];

        $header = LeaveRecordHeader::where('year', '=', (int)$year)->where('month', '=', (int)$month)->where('dept_id', '=', $dept)->get();
        if ($header->isNotEmpty()) {
            return response()->json(["success"=>false, "message"=>'ข้อมูลของเดือน '.self::get_month($month).' ปี '.($year+543).' ถูกสร้างไปแล้ว!']);
        }

        $emp_id = auth()->user()->emp_id;

        DB::beginTransaction();
        try {
            // ############################### ประวัติการบันทึก ###############################
            $lr_id = self::newLeaveRecordId();
            $lrh = new LeaveRecordHeader();
            $lrh->lr_id = $lr_id;
            $lrh->dept_id = $dept;
            $lrh->year = (int) $year;
            $lrh->month = (int) $month;
            $lrh->create_id = $emp_id;
            $lrh->create_ip = $request->ip();
            $lrh->save();
            // ############################### END ###############################

            $result = self::callCalcResult($content, $request->ip());
            foreach ($result as $data) {
                if ($data["type"] == "RW") {
                    ############################### บันทึกเวลาทำงาน ###############################
                    $rw = new RecordWorking();
                    $rw->id = $data["rw_id"];
                    $rw->emp_id = $data["emp_id"];
                    $rw->work_date = $data["work_date"];
                    $rw->use_status = $data["use_status"];
                    $rw->leave_id = $data["leave_id"];
                    $rw->remark = $data["remark"];
                    $rw->leave_mode = $data["leave_mode"];
                    $rw->leader_id = $data["leader_id"];
                    $rw->approve_status = $data["approve_status"];
                    $rw->approve_lid = $data["approve_lid"];
                    $rw->approve_lip = $data["approve_lip"];
                    $rw->approve_ldate = $data["approve_ldate"];
                    $rw->approve_mid = $data["approve_mid"];
                    $rw->approve_mip = $data["approve_mip"];
                    $rw->approve_mdate = $data["approve_mdate"];
                    $rw->save();

                    self::addRecordWorkingLog($data["rw_id"], "สร้างใบบันทึกวันทำงาน (โดยฟอร์มบันทึก)", $emp_id, $request->ip());
                    self::addRecordWorkingLog($data["rw_id"], "อนุมัติใบบันทึกวันทำงาน (โดยหัวหน้า)", $data["approve_lid"], $request->ip());
                    self::addRecordWorkingLog($data["rw_id"], "อนุมัติใบบันทึกวันทำงาน (โดยผู้จัดการ)", $data["approve_mid"], $request->ip());
                    // ############################### END ###############################
                }
                if ($data["type"] == "LV") {
                    // ############################### บันทึกใบลา ###############################
                    if ($data["period_salary_id"] == "") {
                        return response()->json(["success"=>false, "message"=>'ไม่พบงวดค่าแรงของเดือน '.self::get_month(substr($data["leave_start_date"],5,2)).' ปี '.(substr($data["leave_start_date"],0,4)+543)]);
                    }
                    if ($data["leave_duplicate"] == true) {
                        return response()->json(["success"=>false, "message"=>'รหัสพนักงาน '.$data["emp_id"].' มีใบลาวันที่ซ้ำกัน!']);
                    }
                    $lv = new Leave();
                    $lv->leave_id = $data["leave_id"];
                    $lv->leave_start_date = $data["leave_start_date"];
                    $lv->leave_start_time = $data["leave_start_time"];
                    $lv->leave_end_date = $data["leave_end_date"];
                    $lv->leave_end_time = $data["leave_end_time"];
                    $lv->leave_reason = $data["leave_reason"];
                    $lv->leave_day = $data["leave_day"];
                    $lv->leave_minute = $data["leave_minute"];
                    $lv->leave_type_id = $data["leave_type_id"];
                    $lv->period_salary_id = $data["period_salary_id"];
                    $lv->leave_range = $data["leave_range"];
                    $lv->leave_mode = $data["leave_mode"];
                    $lv->leader_id = $data["leader_id"];
                    $lv->emp_id = $data["emp_id"];
                    $lv->emp_type = $data["emp_type"];
                    $lv->approve_lid = $data["approve_lid"];
                    $lv->approve_lip = $data["approve_lip"];
                    $lv->approve_ldate = $data["approve_ldate"];
                    $lv->approve_mid = $data["approve_mid"];
                    $lv->approve_mip = $data["approve_mip"];
                    $lv->approve_mdate = $data["approve_mdate"];
                    $lv->leave_status = $data["leave_status"];
                    $lv->save();

                    self::addLeaveLog($data["leave_id"], "สร้างใบลางาน (โดยฟอร์มบันทึก)", $emp_id, $request->ip());
                    self::addLeaveLog($data["leave_id"], "อนุมัติใบลางาน (โดยหัวหน้า)", $data["approve_lid"], $request->ip());
                    self::addLeaveLog($data["leave_id"], "อนุมัติใบลางาน (โดยผู้จัดการ)", $data["approve_mid"], $request->ip());
                    // ############################### END ###############################
                }

                if ($data["type"] == "RW") {
                    // ############################### ประวัติการบันทึก ###############################
                    $lrd = new LeaveRecordDetail();
                    $lrd->lr_id = $lr_id;
                    $lrd->emp_id = $data["emp_id"];
                    $lrd->sat = $data["sat"];
                    $lrd->rw_id = $data["rw_id"];
                    $lrd->leave_id = $data["leave_id"];
                    $lrd->save();
                    // ############################### END ###############################
                }
            }
            // all good
            DB::commit();
        } catch (\Exception $e) {
            // something went wrong
            DB::rollback();
            return response()->json(["success"=>false, "message"=>'เกิดข้อผิดพลาด!'.$e]);
        }

        return response()->json(["success"=>true, "message"=>"บันทึกข้อมูลเรียบร้อย"]);
    }

    public function callCalcResult($content, $ip)
    {
        $result = [];
        $rw_id = self::newRecordWorkingId();
        $leave_id = self::newLeaveId();
        foreach ($content as $datalist) {
            $emp_id = $datalist["emp_id"];
            $emp = self::getEmployee($emp_id);
            foreach ($datalist["data"] as $data) {
                if ($data["status"] === "on") {
                    $approveL = self::getOurApproveLeaderSelf($emp_id, $emp->dept_id);
                    if ($approveL !== false) {
                        $approve_lid = $approveL[0]["emp_id"];
                        $approve_lip = $ip;
                        $approve_ldate = now();
                    } else {
                        $approve_lid = $emp_id; // ถ้าไม่มีข้อมูลให้ใช้รหัสของเจ้าตัว
                        $approve_lip = $ip;
                        $approve_ldate = now();
                    }
                    $approveM = self::getOurApproveManagerSelf($emp_id, $emp->dept_id);
                    if ($approveM !== false) {
                        $approve_mid = $approveM[0]["emp_id"];
                        $approve_mip = $ip;
                        $approve_mdate = now();
                    } else {
                        $approve_mid = $emp_id; // ถ้าไม่มีข้อมูลให้ใช้รหัสของเจ้าตัว
                        $approve_mip = $ip;
                        $approve_mdate = now();
                    }

                    // ############################### บันทึกเวลาทำงาน ###############################
                    $work_date = Carbon::parse($data["work_date"])->format('Y-m-d');
                    $result[] = array(
                        "type" => "RW",
                        "sat" => $data["sat"],
                        "rw_id" => $rw_id,
                        "emp_id" => (int)$emp_id,
                        "work_date" => $work_date,
                        "use_status" => 2,
                        "leave_id" => $leave_id,
                        "remark" => "ทำงานวันที่ ".Carbon::createFromFormat('Y-m-d', $work_date)->format('d/m/Y'),
                        "leave_mode" => 2,
                        "leader_id" => auth()->user()->emp_id,
                        "approve_status" => "A2",
                        "approve_lid" => $approve_lid,
                        "approve_lip" => $approve_lip,
                        "approve_ldate" => $approve_ldate,
                        "approve_mid" => $approve_mid,
                        "approve_mip" => $approve_mip,
                        "approve_mdate" => $approve_mdate
                    );
                    // ############################### END ###############################

                    // ############################### บันทึกใบลา ###############################
                    $leave_start_date = Carbon::parse($data["leave_date"])->format('Y-m-d');
                    $leave_end_date = Carbon::parse($data["leave_date"])->format('Y-m-d');
                    $leave_start_time = Carbon::parse('00:00:00')->format('H:i:s');
                    $leave_end_time = Carbon::parse('00:00:00')->format('H:i:s');
                    $period_salary = self::getPeriodLeave($leave_start_date, $leave_end_date);
                    $period_salary_id = ($period_salary) ? $period_salary->id : "";
                    $leave_duplicate = self::chkLeaveDuplicate($leave_start_date);
                    $result[] = array(
                        "type" => "LV",
                        "leave_id" => $leave_id,
                        "leave_start_date" => $leave_start_date,
                        "leave_start_time" => $leave_start_time,
                        "leave_end_date" => $leave_end_date,
                        "leave_end_time" => $leave_end_time,
                        "leave_reason" => "หยุดชดเชยวันที่ ".Carbon::createFromFormat('Y-m-d', $work_date)->format('d/m/Y'),
                        "leave_day" => 1,
                        "leave_minute" => 0,
                        "leave_type_id" => 6,
                        "period_salary_id" => $period_salary_id,
                        "leave_range" => "full",
                        "leave_mode" => 2,
                        "leader_id" => auth()->user()->emp_id,
                        "emp_id" => (int)$emp_id,
                        "emp_type" => $emp->emp_type,
                        "approve_lid" => $approve_lid,
                        "approve_lip" => $approve_lip,
                        "approve_ldate" => $approve_ldate,
                        "approve_mid" => $approve_mid,
                        "approve_mip" => $approve_mip,
                        "approve_mdate" => $approve_mdate,
                        "leave_status" => "A2",
                        "leave_duplicate" => $leave_duplicate
                    );
                    // ############################### END ###############################
                    $rw_id++;
                    $leave_id++;
                }
            }
        }
        return $result;
    }

    public function getUsers($dept)
    {
        $users = DB::table('employee as e')->leftJoin('department as d', 'e.dept_id', '=', 'd.dept_id')
            ->leftJoin('position as p', 'e.position_id', '=', 'p.position_id')->leftJoin('sales_area as a', 'e.area_code', '=', 'a.area_code')
            ->where('e.emp_status', '<>', 0)->where('e.emp_type', '=', 'M')->where('e.dept_id', 'like', $dept . '%')
            ->orWhere(function ($query) use ($dept) {
                if ($dept == 'A0201') {
                    $query->whereIn('e.emp_id', $this->fixEmp);
                }
            })
            ->select('e.emp_id', 'e.title', 'e.name', 'e.surname', 'e.nickname', 'e.gender', 'e.image', 'e.position_id', 'e.dept_id', 'e.area_code', 'a.area_description', 'e.emp_type', 'e.emp_status', 'd.level', 'd.dept_name', 'p.position_name')
            ->orderByRaw('FIELD(e.position_id, 201,200,120) desc, -e.area_code desc, e.area_code asc, e.emp_id asc')->get();
        return $users;
    }

    public function newLeaveRecordId()
    {
        $data = LeaveRecordHeader::orderBy('lr_id', 'DESC')->select('lr_id')->first();
        if ($data) {
            return $data->lr_id+1;
        } else {
            return 1;
        }
    }

    public function getLeaveRecordIdLatest()
    {
        $data = LeaveRecordHeader::orderBy('lr_id', 'DESC')->select('lr_id')->first();
        if ($data) {
            return $data->lr_id;
        } else {
            return 1;
        }
    }

    public function chkLeaveDuplicate($date)
    {
        $data = Leave::where('leave_start_date', '=', $date)->whereNotIn('leave_status', ['C1','C2','C3'])->first();
        if ($data) {
            return true;
        } else {
            return false;
        }
    }

    public function download(Request $request)
    {
        $dept = $request->get('download-dept');
        $month = $request->get('download-month');
        $data = self::callData($dept, $month, true);
        $exp = explode("/", $month);
        $name = "แบบฟอร์มบันทึกวันทำงาน ".$exp[0].'-'.substr(($exp[1]+543), 2, 2);

        return Excel::download(new LeaveRecordFormExport($data, $name), $name.'.xlsx');
    }

    public function destroy(Request $request)
    {
        $id = $request->id;
        $header = LeaveRecordHeader::where('lr_id', '=', $id)->get();
        if ($header->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'ไม่พบข้อมูล!']);
        }
        DB::beginTransaction();
        try {
            $detail = LeaveRecordDetail::where('lr_id', '=', $id)->orderBy('emp_id', 'ASC')->get();
            foreach ($detail as $d) {
                Leave::where('leave_id', '=', $d->leave_id)->delete();
                LeaveLog::where('leave_id', '=', $d->leave_id)->delete();
                RecordWorking::where('id', '=', $d->rw_id)->delete();
                RecordWorkingLog::where('rw_id', '=', $d->rw_id)->delete();
                self::leaveRemoveNotification($d->leave_id);
                self::recordWorkingRemoveNotification($d->rw_id);
            }
            LeaveRecordHeader::where('lr_id', '=', $id)->delete();
            LeaveRecordDetail::where('lr_id', '=', $id)->delete();
            // all good
            DB::commit();
        } catch (\Exception $e) {
            // something went wrong
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'เกิดข้อผิดพลาด!']);
        }
        return response()->json(['success' => true, 'message' => 'ลบข้อมูลเรียบร้อย']);
    }

    public function totalSaturdays($y, $m)
    {
        $saturday=0;
        $total_days=cal_days_in_month(CAL_GREGORIAN, $m, $y);
        for ($i=1;$i<=$total_days;$i++) {
            if (date('N',strtotime($y.'-'.$m.'-'.$i))==6) {
                $saturday++;
            }
        }
        return $saturday;
    }

    public function getSaturdays($y, $m) {
        return new DatePeriod(
            new DateTime("first saturday of $y-$m"),
            DateInterval::createFromDateString('next saturday'),
            new DateTime("last day of $y-$m")
        );
    }

    public function diffTotalMonths($s, $e)
    {
        $sdate = new DateTime($s);
        $edate = new DateTime($e);
        $start = date_create($sdate->format('Y-m-01'));
        $end = date_create($edate->format('Y-m-t'));
        $interval = date_diff($start, $end);
        $total = $interval->format('%r%m');
        return $total;
    }
}