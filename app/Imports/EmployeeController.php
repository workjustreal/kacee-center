<?php

namespace App\Http\Controllers;

use App\Exports\EmployeeExport;
use App\Exports\EmployeeExportEdit;
use App\Http\Controllers\API\ProvinceController;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\SalesArea;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\EmployeeImport;
use File;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $level0 = Department::where('level', '=', 0)->get();
        $level1 = Department::where('level', '=', 1)->get();
        $level2 = Department::where('level', '=', 2)->get();
        $level3 = Department::where('level', '=', 3)->get();
        $level4 = Department::where('level', '=', 4)->get();
        $sales_area = SalesArea::orderBy('area_code', 'ASC')->get();

        return view('organization.employees.index', compact('level0', 'level1', 'level2', 'level3', 'level4', 'sales_area'));
    }

    public function search(Request $request)
    {
        if ($request->ajax()) {
            $depts = self::getDepartmentToArray();
            $users = self::getEmployee($request);
            $totalRecords = $users->select('count(e.*) as allcount')->count();
            $records = $users->select('e.emp_id', 'e.title', 'e.name', 'e.surname', 'e.nickname', 'e.gender', 'e.image', 'e.position_id', 'e.dept_id', 'e.area_code', 'e.emp_type', 'e.emp_status', 'd.level', 'd.dept_name', 'p.position_name')
            ->orderByRaw('e.dept_id="0", e.dept_id asc, e.position_id=0, e.position_id asc, e.emp_id asc')->get();
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
                        <a class="action-icon" href="'.url('organization/employees/show', $rec->emp_id).'" title="ดู"><i class="mdi mdi-eye"></i></a>
                        <a class="action-icon" href="'.url('organization/employees/edit', $rec->emp_id).'" title="แก้ไข"><i class="mdi mdi-square-edit-outline"></i></a>
                        <a class="action-icon" href="javascript:void(0);" onclick="deleteEmployeeConfirmation(\''.$rec->emp_id.'\')" title="ลบ"><i class="mdi mdi-delete"></i></a>
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

    public function getDepartmentToArray()
    {
        $dept = Department::orderBy('dept_id')->get()->toArray();
        return $dept;
    }

    public function getEmployee($request)
    {
        if (Auth::User()->manageEmployee() || Auth::User()->hrReadonly()) {
            $users = DB::table('employee as e')->leftJoin('department as d', 'e.dept_id', '=', 'd.dept_id')->leftJoin('position as p', 'e.position_id', '=', 'p.position_id')
            ->where('e.emp_id', '<>', '')
            ->where(function ($query) use ($request) {
                if ($request->level4 != "all") {
                    $query->where('e.dept_id', '=', $request->level4);
                } else if ($request->level3 != "all") {
                    $query->where('e.dept_id', 'like', substr($request->level3, 0, 7) . '%');
                } else if ($request->level2 != "all") {
                    $query->where('e.dept_id', 'like', substr($request->level2, 0, 5) . '%');
                } else if ($request->level1 != "all") {
                    $query->where('e.dept_id', 'like', substr($request->level1, 0, 3) . '%');
                } else if ($request->level0 != "all") {
                    $query->where('e.dept_id', 'like', substr($request->level0, 0, 1) . '%');
                }
                if ($request->area_code != "") {
                    $query->where('e.area_code', '=', $request->area_code);
                }
                if ($request->emp_type != "all") {
                    $query->where('e.emp_type', '=', $request->emp_type);
                }
                if ($request->emp_status != "all") {
                    $query->where('e.emp_status', '=', $request->emp_status);
                }
            });
        } else {
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
            ->where('e.emp_status', '<>', 0)->where('e.emp_id', '<>', '')
            ->where(function ($query) use ($dept, $auth) {
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
                $query->orWhere('e.emp_id', '=', $auth->emp_id); // รวมข้อมูลของตัวเองเข้าไปด้วย
            })->where(function ($query) use ($request) {
                if ($request->level4 != "all") {
                    $query->where('e.dept_id', '=', $request->level4);
                } else if ($request->level3 != "all") {
                    $query->where('e.dept_id', 'like', substr($request->level3, 0, 7) . '%');
                } else if ($request->level2 != "all") {
                    $query->where('e.dept_id', 'like', substr($request->level2, 0, 5) . '%');
                } else if ($request->level1 != "all") {
                    $query->where('e.dept_id', 'like', substr($request->level1, 0, 3) . '%');
                } else if ($request->level0 != "all") {
                    $query->where('e.dept_id', 'like', substr($request->level0, 0, 1) . '%');
                }
                if ($request->area_code != "") {
                    $query->where('e.area_code', '=', $request->area_code);
                }
                if ($request->emp_type != "all") {
                    $query->where('e.emp_type', '=', $request->emp_type);
                }
                if ($request->emp_status != "all") {
                    $query->where('e.emp_status', '=', $request->emp_status);
                }
            });
        }
        return $users;
    }

    public function create()
    {
        $branch = Branch::all();
        $department = Department::all();
        $position = Position::all();
        $sales_area = SalesArea::orderBy('area_code', 'ASC')->get();
        $province_controller = new ProvinceController;

        $changwats = $province_controller->getChangwats();

        return view('organization.employees.create', compact('branch', 'department', 'position', 'sales_area', 'changwats'));
    }

    public function store(Request $request)
    {
        $emp = Employee::where('emp_id', '=', $request->emp_id)->first();
        if ($emp) {
            alert()->warning('มีข้อมูลของพนักงานคนนี้แล้ว');
            return back();
        }

        $request->validate([
            'emp_id' => 'required|integer',
            'emp_id' => 'min:6|max:6',
            'branch_id' => 'required',
            'dept_id' => 'required',
            'position_id' => 'required',
            'emp_status' => 'required',
            'emp_type' => 'required',
            'title' => 'required|string',
            'name' => 'required|string|max:100',
            // 'surname' => 'string|max:100',
            'gender' => 'required',
            'birth_date' => 'required',
            'tel' => 'max:15',
            'tel2' => 'max:15',
            'phone' => 'max:15',
            'phone2' => 'max:15',
            // 'email' => 'email|max:100',
            // 'address' => 'required|string|max:200',
            // 'subdistrict' => 'required|string|max:100',
            // 'district' => 'required|string|max:100',
            // 'province' => 'required|string|max:100',
            // 'country' => 'required|string|max:100',
            // 'zipcode' => 'required|string|max:10',
            'start_work_date' => 'required',
            // 'ethnicity' => 'required|string|max:100',
            // 'nationality' => 'required|string|max:100',
            // 'religion' => 'required|string|max:100',
            'vehicle_registration' => 'max:100',
        ],[
            'emp_id.required' => 'กรุณากรอกรหัสพนักงาน',
            'emp_id.bigInteger' => 'รหัสพนักงานต้องเป็นตัวเลข',
            'emp_id.min' => 'รหัสพนักงานต้องมี 6 ตัว',
            'emp_id.max' => 'รหัสพนักงานต้องมี 6 ตัว',
            'branch_id.required' => 'กรุณาเลือกสาขา',
            'dept_id.required' => 'กรุณาเลือกแผนก/หน่วยงาน',
            'position_id.required' => 'กรุณาเลือกตำแหน่งงาน',
            'emp_type.required' => 'กรุณาเลือกประเภทพนักงาน',
            'emp_status.required' => 'กรุณาเลือกสถานะพนักงาน',
            'title.required' => 'กรุณาเลือกคำนำหน้า',
            'name.required' => 'กรุณากรอกชื่อ',
            'name.max' => 'ชื่อห้ามเกิน 100 ตัวอักษร',
            // 'surname.required' => 'กรุณากรอกนามสกุล',
            // 'surname.max' => 'นามสกุลห้ามเกิน 100 ตัวอักษร',
            'personal_id.required' => 'กรุณากรอกเลขบัตรปชช.',
            'personal_id.bigInteger' => 'เลขบัตรปชช. ต้องเป็นตัวเลข',
            'gender.required' => 'กรุณาเลือกเพศ',
            'birth_date.required' => 'กรุณาใส่วันเกิด',
            'tel.max' => 'ห้ามเกิน 15 ตัวอักษร',
            'tel2.max' => 'ห้ามเกิน 15 ตัวอักษร',
            'phone.max' => 'เบอร์มือถือห้ามเกิน 15 ตัวอักษร',
            'phone2.max' => 'เบอร์มือถือห้ามเกิน 15 ตัวอักษร',
            // 'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            // 'email.max' => 'ห้ามเกิน 100 ตัวอักษร',
            // 'address.required' => 'กรุณากรอกที่อยู่',
            // 'address.max' => 'ห้ามเกิน 200 ตัวอักษร',
            // 'subdistrict.required' => 'กรุณากรอกตำบล/แขวง',
            // 'subdistrict.max' => 'ห้ามเกิน 100 ตัวอักษร',
            // 'district.required' => 'กรุณากรอกอำเภอ/เขต',
            // 'district.max' => 'ห้ามเกิน 100 ตัวอักษร',
            // 'province.required' => 'กรุณากรอกจังหวัด',
            // 'province.max' => 'ห้ามเกิน 100 ตัวอักษร',
            // 'country.required' => 'กรุณากรอกประเทศ',
            // 'country.max' => 'ห้ามเกิน 100 ตัวอักษร',
            // 'zipcode.required' => 'กรุณากรอกรหัสไปรษณีย์',
            // 'zipcode.max' => 'ห้ามเกิน 10 ตัวอักษร',
            'start_work_date.required' => 'กรุณากรอกวันที่เข้างาน',
            // 'ethnicity.required' => 'กรุณาเลือกเชื้อชาติ',
            // 'nationality.required' => 'กรุณาเลือกสัญชาติ',
            // 'religion.required' => 'กรุณาเลือกศาสนา',
            'vehicle_registration.max' => 'ห้ามเกิน 100 ตัวอักษร',
        ]);

        $start_work_date = "";
        $end_work_date = "";
        $birth_date = "";
        if ($request->start_work_date != "") {
            $start_work_date = Carbon::createFromFormat('d/m/Y', $request->start_work_date)->format('Y-m-d');
        }
        if ($request->end_work_date != "") {
            $end_work_date = Carbon::createFromFormat('d/m/Y', $request->end_work_date)->format('Y-m-d');
        }
        if ($request->birth_date != "") {
            $birth_date = Carbon::createFromFormat('d/m/Y', $request->birth_date)->format('Y-m-d');
        }

        $title_en = "";
        if ($request->title == "นาย") {
            $title_en = "MR";
        } else if ($request->title == "นางสาว") {
            $title_en = "MS";
        }else if ($request->title == "นาง") {
            $title_en = "MRS";
        }

        DB::beginTransaction();
        try {
            $employee = new Employee();
            $employee->emp_id = $request->emp_id;
            $employee->branch_id = $request->branch_id;
            $employee->position_id = $request->position_id;
            $employee->dept_id = $request->dept_id;
            $employee->area_code = $request->area_code;
            $employee->emp_status = $request->emp_status;
            $employee->title = $request->title;
            $employee->name = $request->name;
            $employee->surname = $request->surname;
            $employee->nickname = $request->nickname;
            $employee->title_en = $title_en;
            $employee->name_en = $request->name_en;
            $employee->surname_en = $request->surname_en;
            $employee->gender = $request->gender;
            $employee->birth_date = $birth_date;
            $employee->tel = $request->tel;
            $employee->tel2 = $request->tel2;
            $employee->phone = $request->phone;
            $employee->phone2 = $request->phone2;
            $employee->email = $request->email;
            $employee->detail = $request->detail;
            $employee->image = "user-1.jpg";
            $employee->photo = "user-1.jpg";
            $employee->personal_id = $request->personal_id;
            $employee->address = $request->address;
            $employee->subdistrict = $request->subdistrict;
            $employee->district = $request->district;
            $employee->province = $request->province;
            $employee->country = $request->country;
            $employee->zipcode = $request->zipcode;
            $employee->current_address = $request->current_address;
            $employee->current_subdistrict = $request->current_subdistrict;
            $employee->current_district = $request->current_district;
            $employee->current_province = $request->current_province;
            $employee->current_country = $request->current_country;
            $employee->current_zipcode = $request->current_zipcode;
            $employee->start_work_date = $start_work_date;
            $employee->end_work_date = $end_work_date;
            $employee->emp_type = $request->emp_type;
            $employee->ethnicity = $request->ethnicity;
            $employee->nationality = $request->nationality;
            $employee->religion = $request->religion;
            $employee->vehicle_registration = $request->vehicle_registration;
            $employee->user_manage = auth()->user()->emp_id;
            $employee->ip_address = $request->ip();
            $employee->save();

            User::create([
                'emp_id' => $request->emp_id,
                'dept_id' => $request->dept_id,
                'name' => $request->name,
                'surname' => $request->surname,
                'image' => 'user-1.jpg',
                'email' => $request->emp_id . '@kaceebest.com',
                'password' => Hash::make('kacee'),
                'is_admin' => 0,
                'is_role' => 2,
                'is_login' => 1,
                'is_flag' => 0,
            ]);
            // all good
            DB::commit();
        } catch (\Exception $e) {
            // something went wrong
            DB::rollback();
            alert()->error('เกิดข้อผิดพลาด!'.$e);
        }

        alert()->success('เพิ่มข้อมูลพนักงานเรียบร้อย');
        return redirect('organization/employees');
    }

    public function show($id)
    {
        $employee = Employee::where('emp_id', '=', $id)->first();
        $branch = Branch::where('branch_id', '=', $employee->branch_id)->first();
        $deptL0 = Department::where('level', '=', 0)->where('dept_id', '=', substr($employee->dept_id, 0, 1).'00000000')->first();
        $deptL1 = Department::where('level', '=', 1)->where('dept_id', '=', substr($employee->dept_id, 0, 3).'000000')->first();
        $deptL2 = Department::where('level', '=', 2)->where('dept_id', '=', substr($employee->dept_id, 0, 5).'0000')->first();
        $deptL3 = Department::where('level', '=', 3)->where('dept_id', '=', substr($employee->dept_id, 0, 7).'00')->first();
        $deptL4 = Department::where('level', '=', 4)->where('dept_id', '=', $employee->dept_id)->first();
        $position = Position::where('position_id', '=', $employee->position_id)->first();
        return view('organization.employees.show', compact('employee','branch','deptL0','deptL1','deptL2','deptL3','deptL4','position'));
    }

    public function edit($id)
    {
        $employee = Employee::where('emp_id', '=', $id)->first();
        $branch = Branch::all();
        $department = Department::all();
        $position = Position::all();
        $sales_area = SalesArea::where('dept_id', '<>', '')
        ->where(function ($query) use ($employee) {
            if ($employee->area_code != ""){
                $query->where('dept_id', 'like', substr($employee->area_code, 0, 5) . '%');
            }
        })->orderBy('area_code', 'ASC')->get();

        $request = new Request();
        $request->setMethod('GET');
        $request->replace([
            'changwat' => $employee->province,
            'amphoe' => $employee->district,
            'tambon' => $employee->subdistrict
        ]);

        $province_controller = new ProvinceController;

        $changwats = $province_controller->getChangwats();
        $amphoes = $province_controller->getAmphoes($request);
        $tambons = $province_controller->getTambons($request);
        $zipcodes = $province_controller->getZipcodes($request);

        return view('organization.employees.edit', compact('branch','department','position','sales_area','employee','changwats','amphoes','tambons','zipcodes'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'emp_id' => 'required|integer',
            'emp_id' => 'min:6|max:6',
            'branch_id' => 'required',
            'dept_id' => 'required',
            'position_id' => 'required',
            'emp_status' => 'required',
            'emp_type' => 'required',
            'title' => 'required|string',
            'name' => 'required|string|max:100',
            // 'surname' => 'string|max:100',
            'gender' => 'required',
            'birth_date' => 'required',
            'tel' => 'max:15',
            'tel2' => 'max:15',
            'phone' => 'max:15',
            'phone2' => 'max:15',
            // 'email' => 'email|max:100',
            // 'address' => 'required|string|max:200',
            // 'subdistrict' => 'required|string|max:100',
            // 'district' => 'required|string|max:100',
            // 'province' => 'required|string|max:100',
            // 'country' => 'required|string|max:100',
            // 'zipcode' => 'required|string|max:10',
            'start_work_date' => 'required',
            // 'ethnicity' => 'required|string|max:100',
            // 'nationality' => 'required|string|max:100',
            // 'religion' => 'required|string|max:100',
            'vehicle_registration' => 'max:100',
        ],[
            'emp_id.required' => 'กรุณากรอกรหัสพนักงาน',
            'emp_id.bigInteger' => 'รหัสพนักงานต้องเป็นตัวเลข',
            'emp_id.min' => 'รหัสพนักงานต้องมี 6 ตัว',
            'emp_id.max' => 'รหัสพนักงานต้องมี 6 ตัว',
            'branch_id.required' => 'กรุณาเลือกสาขา',
            'dept_id.required' => 'กรุณาเลือกแผนก/หน่วยงาน',
            'position_id.required' => 'กรุณาเลือกตำแหน่งงาน',
            'emp_type.required' => 'กรุณาเลือกประเภทพนักงาน',
            'emp_status.required' => 'กรุณาเลือกสถานะพนักงาน',
            'title.required' => 'กรุณาเลือกคำนำหน้า',
            'name.required' => 'กรุณากรอกชื่อ',
            'name.max' => 'ชื่อห้ามเกิน 100 ตัวอักษร',
            // 'surname.required' => 'กรุณากรอกนามสกุล',
            // 'surname.max' => 'นามสกุลห้ามเกิน 100 ตัวอักษร',
            'personal_id.required' => 'กรุณากรอกเลขบัตรปชช.',
            'personal_id.bigInteger' => 'เลขบัตรปชช. ต้องเป็นตัวเลข',
            'gender.required' => 'กรุณาเลือกเพศ',
            'birth_date.required' => 'กรุณาใส่วันเกิด',
            'tel.max' => 'ห้ามเกิน 15 ตัวอักษร',
            'tel2.max' => 'ห้ามเกิน 15 ตัวอักษร',
            'phone.max' => 'เบอร์มือถือห้ามเกิน 15 ตัวอักษร',
            'phone2.max' => 'เบอร์มือถือห้ามเกิน 15 ตัวอักษร',
            // 'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            // 'email.max' => 'ห้ามเกิน 100 ตัวอักษร',
            // 'address.required' => 'กรุณากรอกที่อยู่',
            // 'address.max' => 'ห้ามเกิน 200 ตัวอักษร',
            // 'subdistrict.required' => 'กรุณากรอกตำบล/แขวง',
            // 'subdistrict.max' => 'ห้ามเกิน 100 ตัวอักษร',
            // 'district.required' => 'กรุณากรอกอำเภอ/เขต',
            // 'district.max' => 'ห้ามเกิน 100 ตัวอักษร',
            // 'province.required' => 'กรุณากรอกจังหวัด',
            // 'province.max' => 'ห้ามเกิน 100 ตัวอักษร',
            // 'country.required' => 'กรุณากรอกประเทศ',
            // 'country.max' => 'ห้ามเกิน 100 ตัวอักษร',
            // 'zipcode.required' => 'กรุณากรอกรหัสไปรษณีย์',
            // 'zipcode.max' => 'ห้ามเกิน 10 ตัวอักษร',
            'start_work_date.required' => 'กรุณากรอกวันที่เข้างาน',
            // 'ethnicity.required' => 'กรุณาเลือกเชื้อชาติ',
            // 'nationality.required' => 'กรุณาเลือกสัญชาติ',
            // 'religion.required' => 'กรุณาเลือกศาสนา',
            'vehicle_registration.max' => 'ห้ามเกิน 100 ตัวอักษร',
        ]);

        $start_work_date = "";
        $end_work_date = "";
        $birth_date = "";
        if ($request->start_work_date != "") {
            $start_work_date = Carbon::createFromFormat('d/m/Y', $request->start_work_date)->format('Y-m-d');
        }
        if ($request->end_work_date != "") {
            $end_work_date = Carbon::createFromFormat('d/m/Y', $request->end_work_date)->format('Y-m-d');
        }
        if ($request->birth_date != "") {
            $birth_date = Carbon::createFromFormat('d/m/Y', $request->birth_date)->format('Y-m-d');
        }

        $title_en = "";
        if ($request->title == "นาย") {
            $title_en = "MR";
        } else if ($request->title == "นางสาว") {
            $title_en = "MS";
        }else if ($request->title == "นาง") {
            $title_en = "MRS";
        }

        DB::beginTransaction();
        try {
            $data = [
                'branch_id' => $request->branch_id,
                'position_id' => $request->position_id,
                'dept_id' => $request->dept_id,
                'area_code' => $request->area_code,
                'emp_status' => $request->emp_status,
                'title' => $request->title,
                'name' => $request->name,
                'surname' => $request->surname,
                'nickname' => $request->nickname,
                'title_en' => $title_en,
                'name_en' => $request->name_en,
                'surname_en' => $request->surname_en,
                'gender' => $request->gender,
                'birth_date' => $birth_date,
                'tel' => $request->tel,
                'tel2' => $request->tel2,
                'phone' => $request->phone,
                'phone2' => $request->phone2,
                'email' => $request->email,
                'detail' => $request->detail,
                'personal_id' => $request->personal_id,
                'address' => $request->address,
                'subdistrict' => $request->subdistrict,
                'district' => $request->district,
                'province' => $request->province,
                'country' => $request->country,
                'zipcode' => $request->zipcode,
                'current_address' => $request->current_address,
                'current_subdistrict' => $request->current_subdistrict,
                'current_district' => $request->current_district,
                'current_province' => $request->current_province,
                'current_country' => $request->current_country,
                'current_zipcode' => $request->current_zipcode,
                'start_work_date' => $start_work_date,
                'end_work_date' => $end_work_date,
                'emp_type' => $request->emp_type,
                'ethnicity' => $request->ethnicity,
                'nationality' => $request->nationality,
                'religion' => $request->religion,
                'vehicle_registration' => $request->vehicle_registration,
                'user_manage' => auth()->user()->emp_id,
                'ip_address' => $request->ip(),
                'updated_at' => now(),
            ];
            Employee::where('emp_id', '=', $request->emp_id)->update($data);

            if ($request->emp_status == "0") {
                $is_login = 0;
            } else {
                $is_login = 1;
            }

            User::where('emp_id', '=', $request->emp_id)
            ->update([
                'dept_id' => $request->dept_id,
                'name' => $request->name,
                'surname' => $request->surname,
                "is_login"=>$is_login,
            ]);
            // all good
            DB::commit();
        } catch (\Exception $e) {
            // something went wrong
            DB::rollback();
            alert()->error('เกิดข้อผิดพลาด!'.$e);
        }

        alert()->success('อัปเดตข้อมูลพนักงานเรียบร้อย');
        return redirect('organization/employees');
    }

    public function destroy($emp_id)
    {
        if ($emp_id != "") {
            Employee::where('emp_id', '=', $emp_id)->delete();
            User::where('emp_id', '=', $emp_id)->delete();
            return response()->json([
                'success' => true,
                'message' => 'ลบข้อมูลพนักงานเรียบร้อย',
            ]);
        }
    }

    public function export(Request $request)
    {
        $users = self::getEmployee($request);
        $employee = $users->select('e.*', 'd.dept_name', 'p.position_name')->orderByRaw('e.dept_id="0", e.dept_id asc, e.position_id=0, e.position_id asc, e.emp_id asc')->get();
        return Excel::download(new EmployeeExport($employee, "Sheet1"), 'ข้อมูลพนักงาน_'.now().'.xlsx');
    }

    public function exportEdit(Request $request)
    {
        $users = self::getEmployee($request);
        $employee = $users->where(function ($query) use ($request) {
                        if ($request->emp_status != "all"){
                            $query->where('e.emp_status', '=', $request->emp_status);
                        } else {
                            $query->where('e.emp_status', '<>', 0);
                        }
                        $query->whereRaw('substring(e.emp_id, 1, 2) <> "98"');
                    })->select('e.*', 'd.dept_name', 'p.position_name')->orderByRaw('e.dept_id="0", e.dept_id asc, e.position_id=0, e.position_id asc, e.emp_id asc')->get();
        return Excel::download(new EmployeeExportEdit($employee, "Sheet1"), 'แก้ไขข้อมูลพนักงาน_'.now().'.xlsx');
    }

    public function upload()
    {
        session()->forget('employee_data');
        return view('organization.employees.upload')->with('data', []);
    }

    public function uploadData(Request $request)
    {
        $request->validate([
            'file' => 'required',
        ],[
            'file.required' => 'ยังไม่ได้เลือกไฟล์',
        ]);
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = 'employee_data';
            $input['filename'] = $fileName . '.' . $file->extension();
            $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/assets/uploads/excel/';
            $file->move($destinationPath, $input['filename']);
            $fileName = $input['filename'];

            $employee = Employee::select('emp_id')->get()->toArray();
            $department = Department::select('dept_id')->get()->toArray();
            $position = Position::select('position_id')->get()->toArray();

            $import = new EmployeeImport;
            Excel::import($import, $destinationPath.$fileName);
            $data = $import->getArray();

            $result= [];
            $i = 0;
            foreach ($data as $value) {
                if ($i > 0) {
                    $action = "";
                    $msg = "";
                    if (strlen($value["emp_id"]) == 6) {
                        $pkey = array_search(trim($value["emp_id"]), array_column($employee, 'emp_id'));
                        if ($pkey === false) {
                            $gkey = array_search(trim($value["dept_id"]), array_column($department, 'dept_id'));
                            if ($gkey === false) {
                                $action = "no_dept";
                                $msg = "ไม่พบหน่วยงาน";
                            } else {
                                if (strlen($value["position_id"]) > 0) {
                                    $gkey = array_search(trim($value["position_id"]), array_column($position, 'position_id'));
                                    if ($gkey === false) {
                                        $action = "no_position";
                                        $msg = "ไม่พบตำแหน่ง";
                                    }
                                }
                            }
                        } else {
                            $action = "emp_distinct";
                            $msg = "มีข้อมูลอยู่แล้ว";
                        }
                        $address = preg_replace('/\s+/', ' ', trim($value["full_address"]));
                        $exp_add = self::convert_address($address);
                        $result[] = [
                            "emp_id" => trim($value["emp_id"]),
                            "name_prefix" => trim($value["name_prefix"]),
                            "first_name" => trim($value["first_name"]),
                            "last_name" => trim($value["last_name"]),
                            "dept_id" => trim($value["dept_id"]),
                            "nickname" => trim($value["nickname"]),
                            "position_id" => trim($value["position_id"]),
                            "emp_type" => trim($value["emp_type"]),
                            "full_address" => trim($value["full_address"]),
                            "address" => $exp_add["address"],
                            "subdistrict" => $exp_add["subdistrict"],
                            "district" => $exp_add["district"],
                            "province" => $exp_add["province"],
                            "gender" => trim($value["gender"]),
                            "race" => trim($value["race"]),
                            "nationality" => trim($value["nationality"]),
                            "religion" => trim($value["religion"]),
                            "birth_date" => trim($value["birth_date"]),
                            "idcard_number" => trim($value["idcard_number"]),
                            "start_work_date" => trim($value["start_work_date"]),
                            "action" => trim($action),
                            "msg" => trim($msg),
                        ];
                    }
                }
                $i++;
            }
            unset($employee);
            unset($department);
            unset($position);
            unset($data);
            if (File::exists($destinationPath.$fileName)) {
                File::delete($destinationPath.$fileName);
            }
            if (count($result) > 200) {
                alert()->warning('อัปเดตข้อมูลได้ครั้งละไม่เกิน 200 รายการ');
                return view('organization.employees.upload')->with('data', []);
            }
            session()->put('employee_data', []);
            session()->put('employee_data', $result);
            return view('organization.employees.upload')->with('data', $result);
        }
    }

    public function updateData(Request $request)
    {
        $result= [];
        if (session()->get('employee_data')) {
            $sess = session()->get('employee_data');
            $count = count($sess);
            for ($i = 0; $i < $count; $i++) {
                $result[] = [
                    "emp_id" => $sess[$i]["emp_id"],
                    "name_prefix" => (empty($sess[$i]["name_prefix"])) ? null : $sess[$i]["name_prefix"],
                    "first_name" => (empty($sess[$i]["first_name"])) ? null : $sess[$i]["first_name"],
                    "last_name" => (empty($sess[$i]["last_name"])) ? null : $sess[$i]["last_name"],
                    "dept_id" => (empty($sess[$i]["dept_id"])) ? 0 : $sess[$i]["dept_id"],
                    "nickname" => (empty($sess[$i]["nickname"])) ? null : $sess[$i]["nickname"],
                    "position_id" => (empty($sess[$i]["position_id"])) ? 0 : $sess[$i]["position_id"],
                    "emp_type" => (empty($sess[$i]["emp_type"])) ? null : $sess[$i]["emp_type"],
                    "address" => (empty($sess[$i]["address"])) ? null : $sess[$i]["address"],
                    "subdistrict" => (empty($sess[$i]["subdistrict"])) ? null : $sess[$i]["subdistrict"],
                    "district" => (empty($sess[$i]["district"])) ? null : $sess[$i]["district"],
                    "province" => (empty($sess[$i]["province"])) ? null : $sess[$i]["province"],
                    "gender" => (empty($sess[$i]["gender"])) ? null : $sess[$i]["gender"],
                    "race" => (empty($sess[$i]["race"])) ? null : $sess[$i]["race"],
                    "nationality" => (empty($sess[$i]["nationality"])) ? null : $sess[$i]["nationality"],
                    "religion" => (empty($sess[$i]["religion"])) ? null : $sess[$i]["religion"],
                    "birth_date" => (empty($sess[$i]["birth_date"])) ? null : Carbon::createFromFormat('d/m/Y', $sess[$i]["birth_date"])->format('Y-m-d'),
                    "idcard_number" => (empty($sess[$i]["idcard_number"])) ? null : str_replace("-","",$sess[$i]["idcard_number"]),
                    "start_work_date" => (empty($sess[$i]["start_work_date"])) ? null : Carbon::createFromFormat('d/m/Y', $sess[$i]["start_work_date"])->format('Y-m-d'),
                ];
            }
        }
        if ($result) {
            if (count($result) <= 0) {
                alert()->warning('ไม่พบข้อมูล');
                return back();
            }
            if (count($result) > 200) {
                alert()->warning('อัปเดตข้อมูลได้ครั้งละไม่เกิน 200 รายการ');
                return back();
            }

            DB::beginTransaction();
            try {
                $count = count($result);
                for ($i = 0; $i < $count; $i++) {
                    $title_en = "";
                    if ($result[$i]["name_prefix"] == "นาย") {
                        $title_en = "MR";
                    } else if ($result[$i]["name_prefix"] == "นางสาว") {
                        $title_en = "MS";
                    }else if ($result[$i]["name_prefix"] == "นาง") {
                        $title_en = "MRS";
                    }
                    $employee = new Employee();
                    $employee->emp_id = $result[$i]["emp_id"];
                    $employee->branch_id = "";
                    $employee->position_id = $result[$i]["position_id"];
                    $employee->dept_id = $result[$i]["dept_id"];
                    $employee->emp_status = 1;
                    $employee->title = $result[$i]["name_prefix"];
                    $employee->name = $result[$i]["first_name"];
                    $employee->surname = $result[$i]["last_name"];
                    $employee->nickname = $result[$i]["nickname"];
                    $employee->title_en = $title_en;
                    $employee->gender = $result[$i]["gender"];
                    $employee->birth_date = $result[$i]["birth_date"];
                    $employee->image = "user-1.jpg";
                    $employee->photo = "user-1.jpg";
                    $employee->personal_id = $result[$i]["idcard_number"];
                    $employee->address = $result[$i]["address"];
                    $employee->subdistrict = $result[$i]["subdistrict"];
                    $employee->district = $result[$i]["district"];
                    $employee->province = $result[$i]["province"];
                    $employee->start_work_date = $result[$i]["start_work_date"];
                    $employee->emp_type = $result[$i]["emp_type"];
                    $employee->ethnicity = $result[$i]["race"];
                    $employee->nationality = $result[$i]["nationality"];
                    $employee->religion = $result[$i]["religion"];
                    $employee->user_manage = auth()->user()->emp_id;
                    $employee->ip_address = $request->ip();
                    $employee->save();

                    $user = User::where('emp_id', '=', $result[$i]["emp_id"])->first();
                    if (!$user) {
                        User::create([
                            'emp_id' => $result[$i]["emp_id"],
                            'dept_id' => $result[$i]["dept_id"],
                            'name' => $result[$i]["first_name"],
                            'surname' => $result[$i]["last_name"],
                            'image' => 'user-1.jpg',
                            'email' => $result[$i]["emp_id"] . '@kaceebest.com',
                            'password' => Hash::make('kacee'),
                            'is_admin' => 0,
                            'is_role' => 2,
                            'is_login' => 1,
                            'is_flag' => 0,
                        ]);
                    }
                }
                DB::commit();
                // all good
            } catch (\Exception $e) {
                DB::rollback();
                // something went wrong
                alert()->error('เกิดข้อผิดพลาด ไม่สามารถบันทึกข้อมูลได้ ' . $e->getMessage())->autoClose(false);
                return redirect('/organization/employees/upload');
            }
            session()->forget('employee_data');
            alert()->success('บันทึกข้อมูลเรียบร้อย');
            return redirect('/organization/employees');
        }
    }

    function get_client_ip() {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    public function search_level1(Request $request)
    {
        $level1 = Department::where('level', '=', 1)
            ->where(function ($query) use ($request) {
                if ($request->level0 != "all"){
                    $query->where('dept_parent', '=', $request->level0);
                }
            })->orderBy('dept_id', 'asc')->orderBy('dept_parent', 'asc')->get();

        $level2 = Department::where('level', '=', 2)
            ->where(function ($query) use ($request) {
                if ($request->level0 != "all"){
                    $query->where('dept_parent', 'like', substr($request->level0, 0, 1).'%');
                }
            })->orderBy('dept_id', 'asc')->orderBy('dept_parent', 'asc')->get();

        $level3 = Department::where('level', '=', 3)
            ->where(function ($query) use ($request) {
                if ($request->level0 != "all"){
                    $query->where('dept_parent', 'like', substr($request->level0, 0, 1).'%');
                }
            })->orderBy('dept_id', 'asc')->orderBy('dept_parent', 'asc')->get();

        $level4 = Department::where('level', '=', 4)
            ->where(function ($query) use ($request) {
                if ($request->level0 != "all"){
                    $query->where('dept_parent', 'like', substr($request->level0, 0, 1).'%');
                }
            })->orderBy('dept_id', 'asc')->orderBy('dept_parent', 'asc')->get();

        return response()->json(["level1"=>$level1, "level2"=>$level2, "level3"=>$level3, "level4"=>$level4]);
    }

    public function search_level2(Request $request)
    {
        $level2 = Department::where('level', '=', 2)
            ->where(function ($query) use ($request) {
                if ($request->level1 != "all"){
                    $query->where('dept_parent', '=', $request->level1);
                }
            })->orderBy('dept_id', 'asc')->orderBy('dept_parent', 'asc')->get();

        $level3 = Department::where('level', '=', 3)
            ->where(function ($query) use ($request) {
                if ($request->level1 != "all"){
                    $query->where('dept_parent', 'like', substr($request->level1, 0, 3).'%');
                }
            })->orderBy('dept_id', 'asc')->orderBy('dept_parent', 'asc')->get();

        $level4 = Department::where('level', '=', 4)
            ->where(function ($query) use ($request) {
                if ($request->level1 != "all"){
                    $query->where('dept_parent', 'like', substr($request->level1, 0, 3).'%');
                }
            })->orderBy('dept_id', 'asc')->orderBy('dept_parent', 'asc')->get();

        return response()->json(["level2"=>$level2, "level3"=>$level3, "level4"=>$level4]);
    }

    public function search_level3(Request $request)
    {
        $level3 = Department::where('level', '=', 3)
            ->where(function ($query) use ($request) {
                if ($request->level2 != "all"){
                    $query->where('dept_parent', 'like', substr($request->level2, 0, 5).'%');
                }
            })->orderBy('dept_id', 'asc')->orderBy('dept_parent', 'asc')->get();

        $level4 = Department::where('level', '=', 4)
            ->where(function ($query) use ($request) {
                if ($request->level2 != "all"){
                    $query->where('dept_parent', 'like', substr($request->level2, 0, 5).'%');
                }
            })->orderBy('dept_id', 'asc')->orderBy('dept_parent', 'asc')->get();

        $sales_area = SalesArea::where('dept_id', '<>', '')
        ->where(function ($query) use ($request) {
            if ($request->level2 != "all"){
                $query->where('dept_id', '=', $request->level2);
            }
        })->orderBy('area_code', 'ASC')->get();

        return response()->json(["level3"=>$level3, "level4"=>$level4, "sales_area"=>$sales_area]);
    }

    public function search_level4(Request $request)
    {
        $level4 = Department::where('level', '=', 4)
            ->where(function ($query) use ($request) {
                $query->where('dept_parent', '=', $request->level3);
            })->orderBy('dept_id', 'asc')->orderBy('dept_parent', 'asc')->get();

        return response()->json(["level4"=>$level4]);
    }

    public function search_sales_area(Request $request)
    {
        $sales_area = SalesArea::where('dept_id', '<>', '')
        ->where(function ($query) use ($request) {
            if ($request->dept_id != ""){
                $query->where('dept_id', '=', $request->dept_id);
            }
        })->orderBy('area_code', 'ASC')->get();

        return response()->json(["sales_area"=>$sales_area]);
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

    //------------------------------- ตัดคำที่อยู่ -----------------------------------------------
    public function convert_address($text_address){
        $result = ["address"=>"","subdistrict"=>"","district"=>"","province"=>"","zipcode"=>""];
        $exp = explode(" ", $text_address);
        if (count($exp)) {
            foreach ($exp as $value) {
                if (strpos($value, "จ.") !== false || strpos($value, "จังหวัด") !== false || strpos($value, "กทม.") !== false || strpos($value, "กรุงเทพ") !== false) {
                    $result["province"] = trim($value);
                } else if (strpos($value, "อ.") !== false || strpos($value, "อำเภอ") !== false || strpos($value, "เขต") !== false) {
                    $result["district"] = trim($value);
                } else if (strpos($value, "ต.") !== false || strpos($value, "ตำบล") !== false || strpos($value, "แขวง") !== false) {
                    $result["subdistrict"] = trim($value);
                } else {
                    $space = ($result["address"] != "") ? " " : "";
                    $result["address"] .= $space.trim($value);
                }
            }
        }
        return $result;
    }
}