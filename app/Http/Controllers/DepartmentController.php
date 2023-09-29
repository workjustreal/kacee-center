<?php

namespace App\Http\Controllers;

use App\Exports\DepartmentExport;
use App\Exports\OrganizationalChartExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $department = Department::orderBy('dept_id', 'ASC')->get();
        return view('organization.department.index')->with('department', $department);
    }

    public function search(Request $request)
    {
        if ($request->ajax()) {
            $data = Department::where('dept_id', '<>', '');
            $totalRecords = $data->select('count(*) as allcount')->count();
            $records = $data->select('*')->orderBy('dept_id', 'ASC')->get();
            $rows = [];
            foreach ($records as $rec) {
                if (substr($rec->dept_id, 0, 1) == "A") {
                    $dept_link = '<a href="'.url('organization/department/site-map', $rec->dept_id).'">'.$rec->dept_id.'</a>';
                } else {
                    $dept_link = '<a href="javascript:void(0);">'.$rec->dept_id.'</a>';
                }
                $action = '';
                if (Auth::User()->manageEmployee()) {
                    $action = '<div>
                        <a class="action-icon" href="'.url('organization/department/show', $rec->dept_id).'" title="ดู"><i class="mdi mdi-eye"></i></a>
                        <a class="action-icon" href="'.url('organization/department/edit', $rec->dept_id).'" title="แก้ไข"><i class="mdi mdi-square-edit-outline"></i></a>
                        <a class="action-icon" href="javascript:void(0);" onclick="deleteDepartmentConfirmation(\''.$rec->dept_id.'\')" title="ลบ"><i class="mdi mdi-delete"></i></a>
                    </div>';
                }
                $rows[] = array(
                    "dept_id" => $dept_link,
                    "dept_name" => $rec->dept_name,
                    "dept_name_en" => $rec->dept_name_en,
                    "level" => $rec->level,
                    "dept_parent" => $rec->dept_parent,
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

    public function create()
    {
        $dept_parent = Department::where('level', '<>', 4)->orderBy('dept_id', 'ASC')->get();
        return view('organization.department.create', compact('dept_parent'));
    }

    public function store(Request $request)
    {
        $dept_id = strtoupper(trim($request->dept_id));
        $dept = Department::where('dept_id', '=', $dept_id)->first();
        if ($dept) {
            alert()->warning('รหัสหน่วยงานซ้ำ!');
            return back()->withInput();
        }
        if (!preg_match('/^[A-Z ]+$/', $dept_id[0])) {
            alert()->warning('รหัสหน่วยงานต้องขึ้นต้นด้วย A-Z!');
            return back()->withInput();
        }
        if ($request->level == 0) {
            if (substr($dept_id, 1, 8) != '00000000') {
                alert()->warning('ตรวจสอบรหัสหน่วยงานให้ถูกต้อง!', 'หลักรหัสต้องตรงกับระดับบริษัท');
                return back()->withInput();
            }
        } else if ($request->level == 1) {
            if (intval(substr($dept_id, 1, 2)) < 1 || substr($dept_id, 3, 6) != '000000') {
                alert()->warning('ตรวจสอบรหัสหน่วยงานให้ถูกต้อง!', 'หลักรหัสต้องตรงกับระดับส่วน');
                return back()->withInput();
            }
        } else if ($request->level == 2) {
            if (intval(substr($dept_id, 1, 2)) < 1 || intval(substr($dept_id, 3, 2)) < 1 || substr($dept_id, 5, 4) != '0000') {
                alert()->warning('ตรวจสอบรหัสหน่วยงานให้ถูกต้อง!', 'หลักรหัสต้องตรงกับระดับฝ่าย');
                return back()->withInput();
            }
        } else if ($request->level == 3) {
            if (intval(substr($dept_id, 1, 2)) < 1 || intval(substr($dept_id, 3, 2)) < 1 || intval(substr($dept_id, 5, 2)) < 1 || substr($dept_id, 7, 2) != '00') {
                alert()->warning('ตรวจสอบรหัสหน่วยงานให้ถูกต้อง!', 'หลักรหัสต้องตรงกับระดับแผนก');
                return back()->withInput();
            }
        } else if ($request->level == 4) {
            if (intval(substr($dept_id, 1, 2)) < 1 || intval(substr($dept_id, 3, 2)) < 1 || intval(substr($dept_id, 5, 2)) < 1 || intval(substr($dept_id, 7, 2)) < 1) {
                alert()->warning('ตรวจสอบรหัสหน่วยงานให้ถูกต้อง!', 'หลักรหัสต้องตรงกับระดับหน่วย');
                return back()->withInput();
            }
        }

        $request->validate([
            'dept_id' => 'required',
            'dept_name' => 'required',
            'level' => 'required',
        ],[
            'dept_id.required' => 'กรุณากรอกรหัสหน่วยงาน',
            'dept_name.required' => 'กรุณากรอกชื่อหน่วยงาน',
            'level.required' => 'กรุณาเลือกระดับ',
        ]);

        $department = new Department();
        $department->dept_id = $dept_id;
        $department->dept_name = $request->dept_name;
        $department->dept_name_en = $request->dept_name_en;
        $department->level = $request->level;
        $department->dept_parent = $request->dept_parent;
        $department->user_manage = auth()->user()->emp_id;
        $department->ip_address = $request->ip();
        $department->save();

        alert()->success('เพิ่มข้อมูลหน่วยงานเรียบร้อย');
        return redirect('organization/department');
    }

    public function site_map($id)
    {
        $color = ["text-primary", "text-success", "text-info", "text-warning"];
        $site = [];
        // $level0 = Department::where('level', '=', 0)->where('dept_id', '=', $id[0].'00000000')->first();
        $level0 = DB::table("department")->where('department.level', '=', 0)->where('department.dept_id', '=', $id[0].'00000000')
        ->select('*', DB::raw("(SELECT COUNT(employee.emp_id) FROM employee WHERE employee.emp_status != '0' and employee.dept_id = department.dept_id) as emp_count"))
        ->orderBy('department.dept_id', 'ASC')->first();
        $site["level"] = 0;
        $site["id"] = $level0->dept_id;
        $site["name"] = $level0->dept_name;
        $site["emp_count"] = $level0->emp_count;
        $site["color"] = "text-danger";

        // $level1 = Department::where('level', '=', 1)->where('dept_id', 'like', $id[0].'%')->orderBy('dept_id', 'ASC')->get();
        $level1 = DB::table("department")->where('department.level', '=', 1)->where('department.dept_id', 'like', $id[0].'%')
        ->select('*', DB::raw("(SELECT COUNT(employee.emp_id) FROM employee WHERE employee.emp_status != '0' and employee.dept_id = department.dept_id) as emp_count"))
        ->orderBy('department.dept_id', 'ASC')->get();
        for ($l1=0; $l1<$level1->count(); $l1++) {
            $site["list"][$l1] = [
                "level" => 1,
                "id" => $level1[$l1]->dept_id,
                "name" => $level1[$l1]->dept_name,
                "emp_count" => $level1[$l1]->emp_count,
                "color" => $color[$l1],
                "list" => [],
            ];
            // $level2 = Department::where('level', '=', 2)->where('dept_id', 'like', substr($level1[$l1]->dept_id, 0, 3).'%')->orderBy('dept_id', 'ASC')->get();
            $level2 = DB::table("department")->where('department.level', '=', 2)->where('department.dept_id', 'like', substr($level1[$l1]->dept_id, 0, 3).'%')
            ->select('*', DB::raw("(SELECT COUNT(employee.emp_id) FROM employee WHERE employee.emp_status != '0' and employee.dept_id = department.dept_id) as emp_count"))
            ->orderBy('department.dept_id', 'ASC')->get();
            for ($l2=0; $l2<$level2->count(); $l2++) {
                $site["list"][$l1]["list"][$l2] = [
                    "level" => 2,
                    "id" => $level2[$l2]->dept_id,
                    "name" => $level2[$l2]->dept_name,
                    "emp_count" => $level2[$l2]->emp_count,
                    "color" => $color[$l1],
                    "list" => [],
                ];
                // $level3 = Department::where('level', '=', 3)->where('dept_id', 'like', substr($level2[$l2]->dept_id, 0, 5).'%')->orderBy('dept_id', 'ASC')->get();
                $level3 = DB::table("department")->where('department.level', '=', 3)->where('department.dept_id', 'like', substr($level2[$l2]->dept_id, 0, 5).'%')
                ->select('*', DB::raw("(SELECT COUNT(employee.emp_id) FROM employee WHERE employee.emp_status != '0' and employee.dept_id = department.dept_id) as emp_count"))
                ->orderBy('department.dept_id', 'ASC')->get();
                for ($l3=0; $l3<$level3->count(); $l3++) {
                    $site["list"][$l1]["list"][$l2]["list"][$l3] = [
                        "level" => 3,
                        "id" => $level3[$l3]->dept_id,
                        "name" => $level3[$l3]->dept_name,
                        "emp_count" => $level3[$l3]->emp_count,
                        "color" => $color[$l1],
                        "list" => [],
                    ];
                    // $level4 = Department::where('level', '=', 4)->where('dept_id', 'like', substr($level3[$l3]->dept_id, 0, 7).'%')->orderBy('dept_id', 'ASC')->get();
                    $level4 = DB::table("department")->where('department.level', '=', 4)->where('department.dept_id', 'like', substr($level3[$l3]->dept_id, 0, 7).'%')
                    ->select('*', DB::raw("(SELECT COUNT(employee.emp_id) FROM employee WHERE employee.emp_status != '0' and employee.dept_id = department.dept_id) as emp_count"))
                    ->orderBy('department.dept_id', 'ASC')->get();
                    for ($l4=0; $l4<$level4->count(); $l4++) {
                        $site["list"][$l1]["list"][$l2]["list"][$l3]["list"][$l4] = [
                            "level" => 4,
                            "id" => $level4[$l4]->dept_id,
                            "name" => $level4[$l4]->dept_name,
                            "emp_count" => $level4[$l4]->emp_count,
                        ];
                    }
                }
            }
        }

        return view('organization.department.site-map', compact('site', 'id'));
    }

    public function show($id)
    {
        $department = Department::where('dept_id', '=', $id)->first();
        $dept_parent = Department::where('level', '=', $department->level-1)->orderBy('dept_id', 'ASC')->get();
        return view('organization.department.show', compact('department', 'dept_parent'));
    }

    public function edit($id)
    {
        $department = Department::where('dept_id', '=', $id)->first();
        $dept_parent = Department::where('level', '=', $department->level-1)->orderBy('dept_id', 'ASC')->get();
        return view('organization.department.edit', compact('department', 'dept_parent'));
    }

    public function update(Request $request)
    {
        $dept_id = strtoupper(trim($request->dept_id));
        if (!preg_match('/^[A-Z ]+$/', $dept_id[0])) {
            alert()->warning('รหัสหน่วยงานต้องขึ้นต้นด้วย A-Z!');
            return back()->withInput();
        }
        if ($request->level == 0) {
            if (substr($dept_id, 1, 8) != '00000000') {
                alert()->warning('ตรวจสอบรหัสหน่วยงานให้ถูกต้อง!', 'หลักรหัสต้องตรงกับระดับบริษัท');
                return back()->withInput();
            }
        } else if ($request->level == 1) {
            if (intval(substr($dept_id, 1, 2)) < 1 || substr($dept_id, 3, 6) != '000000') {
                alert()->warning('ตรวจสอบรหัสหน่วยงานให้ถูกต้อง!', 'หลักรหัสต้องตรงกับระดับส่วน');
                return back()->withInput();
            }
        } else if ($request->level == 2) {
            if (intval(substr($dept_id, 1, 2)) < 1 || intval(substr($dept_id, 3, 2)) < 1 || substr($dept_id, 5, 4) != '0000') {
                alert()->warning('ตรวจสอบรหัสหน่วยงานให้ถูกต้อง!', 'หลักรหัสต้องตรงกับระดับฝ่าย');
                return back()->withInput();
            }
        } else if ($request->level == 3) {
            if (intval(substr($dept_id, 1, 2)) < 1 || intval(substr($dept_id, 3, 2)) < 1 || intval(substr($dept_id, 5, 2)) < 1 || substr($dept_id, 7, 2) != '00') {
                alert()->warning('ตรวจสอบรหัสหน่วยงานให้ถูกต้อง!', 'หลักรหัสต้องตรงกับระดับแผนก');
                return back()->withInput();
            }
        } else if ($request->level == 4) {
            if (intval(substr($dept_id, 1, 2)) < 1 || intval(substr($dept_id, 3, 2)) < 1 || intval(substr($dept_id, 5, 2)) < 1 || intval(substr($dept_id, 7, 2)) < 1) {
                alert()->warning('ตรวจสอบรหัสหน่วยงานให้ถูกต้อง!', 'หลักรหัสต้องตรงกับระดับหน่วย');
                return back()->withInput();
            }
        }

        $request->validate([
            'dept_id' => 'required',
            'dept_name' => 'required',
            'level' => 'required',
        ],[
            'dept_id.required' => 'กรุณากรอกรหัสหน่วยงาน',
            'dept_name.required' => 'กรุณากรอกชื่อหน่วยงาน',
            'level.required' => 'กรุณาเลือกระดับ',
        ]);

        $data = [
            'dept_name' => $request->dept_name,
            'dept_name_en' => $request->dept_name_en,
            'level' => $request->level,
            'dept_parent' => $request->dept_parent,
            'user_manage' => auth()->user()->emp_id,
            'ip_address' => $request->ip(),
            'updated_at' => now(),
        ];
        Department::where('dept_id', '=', $dept_id)->update($data);

        alert()->success('อัปเดตข้อมูลหน่วยงานเรียบร้อย');
        return redirect('organization/department');
    }

    public function destroy($id)
    {
        $department = Department::where('dept_id', '=', $id);
        $department->delete();
        sleep(1);
        return redirect('organization/department');
    }

    public function export()
    {
        $department = Department::orderBy('dept_id', 'ASC')->get();

        return Excel::download(new DepartmentExport($department, "Sheet1"), 'ข้อมูลหน่วยงาน_'.now().'.xlsx');
    }

    public function get_dept_parent($level)
    {
        $department = Department::where('level', '=', $level-1)->orderBy('dept_id', 'ASC')->get();
        return response()->json(["data" => $department]);
    }

    public function organizational_data()
    {
        $color = ["text-primary", "text-success", "text-info", "text-warning"];
        $site = [];
        // $level0 = Department::where('level', '=', 0)->where('dept_id', '=', 'A00000000')->first();
        $level0 = DB::table("department")->where('department.level', '=', 0)->where('department.dept_id', '=', 'A00000000')
        ->select('*', DB::raw("(SELECT COUNT(employee.emp_id) FROM employee WHERE employee.emp_status != '0' and employee.dept_id = department.dept_id) as emp_count"))
        ->orderBy('department.dept_id', 'ASC')->first();
        $site["level"] = 0;
        $site["id"] = $level0->dept_id;
        $site["name"] = $level0->dept_name;
        $site["emp_count"] = $level0->emp_count;
        $site["color"] = "text-danger";

        // $level1 = Department::where('level', '=', 1)->where('dept_id', 'like', substr($level0->dept_id, 0, 1).'%')->orderBy('dept_id', 'ASC')->get();
        $level1 = DB::table("department")->where('department.level', '=', 1)->where('department.dept_id', 'like', substr($level0->dept_id, 0, 1).'%')
        ->select('*', DB::raw("(SELECT COUNT(employee.emp_id) FROM employee WHERE employee.emp_status != '0' and employee.dept_id = department.dept_id) as emp_count"))
        ->orderBy('department.dept_id', 'ASC')->get();
        for ($l1=0; $l1<$level1->count(); $l1++) {
            $site["list"][$l1] = [
                "level" => 1,
                "id" => $level1[$l1]->dept_id,
                "name" => $level1[$l1]->dept_name,
                "emp_count" => $level1[$l1]->emp_count,
                "color" => $color[$l1],
                "list" => [],
            ];
            // $level2 = Department::where('level', '=', 2)->where('dept_id', 'like', substr($level1[$l1]->dept_id, 0, 3).'%')->orderBy('dept_id', 'ASC')->get();
            $level2 = DB::table("department")->where('department.level', '=', 2)->where('department.dept_id', 'like', substr($level1[$l1]->dept_id, 0, 3).'%')
            ->select('*', DB::raw("(SELECT COUNT(employee.emp_id) FROM employee WHERE employee.emp_status != '0' and employee.dept_id = department.dept_id) as emp_count"))
            ->orderBy('department.dept_id', 'ASC')->get();
            for ($l2=0; $l2<$level2->count(); $l2++) {
                $site["list"][$l1]["list"][$l2] = [
                    "level" => 2,
                    "id" => $level2[$l2]->dept_id,
                    "name" => $level2[$l2]->dept_name,
                    "emp_count" => $level2[$l2]->emp_count,
                    "color" => $color[$l1],
                    "list" => [],
                ];
                // $level3 = Department::where('level', '=', 3)->where('dept_id', 'like', substr($level2[$l2]->dept_id, 0, 5).'%')->orderBy('dept_id', 'ASC')->get();
                $level3 = DB::table("department")->where('department.level', '=', 3)->where('department.dept_id', 'like', substr($level2[$l2]->dept_id, 0, 5).'%')
                ->select('*', DB::raw("(SELECT COUNT(employee.emp_id) FROM employee WHERE employee.emp_status != '0' and employee.dept_id = department.dept_id) as emp_count"))
                ->orderBy('department.dept_id', 'ASC')->get();
                for ($l3=0; $l3<$level3->count(); $l3++) {
                    $site["list"][$l1]["list"][$l2]["list"][$l3] = [
                        "level" => 3,
                        "id" => $level3[$l3]->dept_id,
                        "name" => $level3[$l3]->dept_name,
                        "emp_count" => $level3[$l3]->emp_count,
                        "color" => $color[$l1],
                        "list" => [],
                    ];
                    // $level4 = Department::where('level', '=', 4)->where('dept_id', 'like', substr($level3[$l3]->dept_id, 0, 7).'%')->orderBy('dept_id', 'ASC')->get();
                    $level4 = DB::table("department")->where('department.level', '=', 4)->where('department.dept_id', 'like', substr($level3[$l3]->dept_id, 0, 7).'%')
                    ->select('*', DB::raw("(SELECT COUNT(employee.emp_id) FROM employee WHERE employee.emp_status != '0' and employee.dept_id = department.dept_id) as emp_count"))
                    ->orderBy('department.dept_id', 'ASC')->get();
                    for ($l4=0; $l4<$level4->count(); $l4++) {
                        $site["list"][$l1]["list"][$l2]["list"][$l3]["list"][$l4] = [
                            "level" => 4,
                            "id" => $level4[$l4]->dept_id,
                            "name" => $level4[$l4]->dept_name,
                            "emp_count" => $level4[$l4]->emp_count,
                        ];
                    }
                }
            }
        }
        return $site;
    }

    public function organizational_chart()
    {
        $site = self::organizational_data();
        return view('organization.department.organizational-chart', compact('site'));
    }

    public function organizational_chart_export()
    {
        $site = self::organizational_data();
        $data = [];
        for ($l1=0; $l1<count($site["list"]); $l1++) {
            if (substr($site["list"][$l1]["id"], 0, 3) == "A01") {
                $data[] = [
                    "column" => "A",
                    "level" => 1,
                    "name" => $site["list"][$l1]["name"]." (".$site["list"][$l1]["id"].")",
                ];
            } else if (substr($site["list"][$l1]["id"], 0, 3) == "A02") {
                $data[] = [
                    "column" => "B",
                    "level" => 1,
                    "name" => $site["list"][$l1]["name"]." (".$site["list"][$l1]["id"].")",
                ];
            } else if (substr($site["list"][$l1]["id"], 0, 3) == "A03") {
                $data[] = [
                    "column" => "C",
                    "level" => 1,
                    "name" => $site["list"][$l1]["name"]." (".$site["list"][$l1]["id"].")",
                ];
            }
            for ($l2 = 0; $l2 < count($site["list"][$l1]["list"]); $l2++) {
                if (substr($site["list"][$l1]["id"], 0, 3) == "A01") {
                    $data[] = [
                        "column" => "A",
                        "level" => 2,
                        "name" => "-".$site["list"][$l1]["list"][$l2]["name"]." (".$site["list"][$l1]["list"][$l2]["id"].")",
                    ];
                } else if (substr($site["list"][$l1]["id"], 0, 3) == "A02") {
                    $data[] = [
                        "column" => "B",
                        "level" => 2,
                        "name" => "-".$site["list"][$l1]["list"][$l2]["name"]." (".$site["list"][$l1]["list"][$l2]["id"].")",
                    ];
                } else if (substr($site["list"][$l1]["id"], 0, 3) == "A03") {
                    $data[] = [
                        "column" => "C",
                        "level" => 2,
                        "name" => "-".$site["list"][$l1]["list"][$l2]["name"]." (".$site["list"][$l1]["list"][$l2]["id"].")",
                    ];
                }
                for ($l3 = 0; $l3 < count($site["list"][$l1]["list"][$l2]["list"]); $l3++) {
                    if (substr($site["list"][$l1]["id"], 0, 3) == "A01") {
                        $data[] = [
                            "column" => "A",
                            "level" => 3,
                            "name" => "--".$site["list"][$l1]["list"][$l2]["list"][$l3]["name"]." (".$site["list"][$l1]["list"][$l2]["list"][$l3]["id"].")",
                        ];
                    } else if (substr($site["list"][$l1]["id"], 0, 3) == "A02") {
                        $data[] = [
                            "column" => "B",
                            "level" => 3,
                            "name" => "--".$site["list"][$l1]["list"][$l2]["list"][$l3]["name"]." (".$site["list"][$l1]["list"][$l2]["list"][$l3]["id"].")",
                        ];
                    } else if (substr($site["list"][$l1]["id"], 0, 3) == "A03") {
                        $data[] = [
                            "column" => "C",
                            "level" => 3,
                            "name" => "--".$site["list"][$l1]["list"][$l2]["list"][$l3]["name"]." (".$site["list"][$l1]["list"][$l2]["list"][$l3]["id"].")",
                        ];
                    }
                    for ($l4 = 0; $l4 < count($site["list"][$l1]["list"][$l2]["list"][$l3]["list"]); $l4++) {
                        if (substr($site["list"][$l1]["id"], 0, 3) == "A01") {
                            $data[] = [
                                "column" => "A",
                                "level" => 4,
                                "name" => "---".$site["list"][$l1]["list"][$l2]["list"][$l3]["list"][$l4]["name"]." (".$site["list"][$l1]["list"][$l2]["list"][$l3]["list"][$l4]["id"].")",
                            ];
                        } else if (substr($site["list"][$l1]["id"], 0, 3) == "A02") {
                            $data[] = [
                                "column" => "B",
                                "level" => 4,
                                "name" => "---".$site["list"][$l1]["list"][$l2]["list"][$l3]["list"][$l4]["name"]." (".$site["list"][$l1]["list"][$l2]["list"][$l3]["list"][$l4]["id"].")",
                            ];
                        } else if (substr($site["list"][$l1]["id"], 0, 3) == "A03") {
                            $data[] = [
                                "column" => "C",
                                "level" => 4,
                                "name" => "---".$site["list"][$l1]["list"][$l2]["list"][$l3]["list"][$l4]["name"]." (".$site["list"][$l1]["list"][$l2]["list"][$l3]["list"][$l4]["id"].")",
                            ];
                        }
                    }
                }
            }
        }
        $name = "แผนผังองค์กร";
        return Excel::download(new OrganizationalChartExport($data, $name), $name.'.xlsx');
    }
}