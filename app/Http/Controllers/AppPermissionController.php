<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\AppPermission;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppPermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $application = Application::orderBy('id', 'ASC')->get();
        return view('admin.app-permission', compact('application'));
    }

    public function search(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('app_permissions as a')->leftJoin('applications as app', 'a.app_id', '=', 'app.id')
            ->where(function ($query) use ($request) {
                if ($request->app_id != "") {
                    $query->where('a.app_id', '=', $request->app_id);
                }
            });

            $totalRecords = $data->select('count(a.*) as allcount')->groupBy('a.app_id')->count();
            $records = $data->select('a.*', 'app.icon', 'app.color')->groupBy('a.app_id')->orderBy("a.app_id", "ASC")->get();
            $rows = [];
            $n = 1;
            foreach ($records as $rec) {
                $action = '<div>
                        <a class="action-icon" href="'.url('admin/application/permission/edit', $rec->app_id).'" title="แก้ไข"><i class="mdi mdi-square-edit-outline"></i></a>
                        <a class="action-icon" href="javascript:void(0);" onclick="deleteAppPermissionConfirmation(\''.$rec->app_id.'\')" title="ลบ"><i class="mdi mdi-delete"></i></a>
                    </div>';

                $deptCount = AppPermission::where('app_id', '=', $rec->app_id)->whereRaw('(emp_id = "" OR emp_id IS NULL)')->count();
                $empCount = AppPermission::where('app_id', '=', $rec->app_id)->whereRaw('(dept_id = "" OR dept_id IS NULL)')->count();

                $rows[] = array(
                    "no" => $n,
                    "app_name" => '<i class="'.$rec->icon.'" style="color:'.$rec->color.'"></i> '.$rec->app_name,
                    "dept_count" => '<b class="text-decoration-underline">'.$deptCount.'</b> หน่วย/แผนก',
                    "emp_count" => '<b class="text-decoration-underline">'.$empCount.'</b> คน',
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

    public function get_dept($id)
    {
        $dept = Department::where('dept_id', '=', $id)->select('dept_name')->first();
        return $dept;
    }

    public function get_emp($id)
    {
        $emp = Employee::where('emp_id', '=', $id)->select('name', 'surname')->first();
        return $emp;
    }

    public function getEmployee($id)
    {
        $result = DB::table('employee as e')->leftJoin('department as d', 'e.dept_id', '=', 'e.dept_id')->leftJoin('position as p', 'p.position_id', '=', 'e.position_id')
        ->where('e.emp_id', '=', $id)->select('e.*', 'd.dept_name', 'p.position_name')->first();
        return $result;
    }

    public function get_users(Request $request)
    {
        if ($request->ajax()) {
            $session_act = $request->session_act;
            $count = 0;
            $output = '';
            if ($request->first === "first") {
                if (session()->has('appper_user_cart_'.$session_act)) {
                    session()->forget('appper_user_cart_'.$session_act);
                }
                if ($request->has('app_id')) {
                    $detail = DB::table('app_permissions as a')->leftJoin('applications as app', 'a.app_id', '=', 'app.id')
                    ->where('a.app_id', '=', $request->app_id)->whereRaw('(a.dept_id = "" OR a.dept_id IS NULL)')->select('a.*', 'app.icon', 'app.color')->orderBy("a.emp_id", "ASC")->get();
                    if ($detail->isNotEmpty()) {
                        session()->put('appper_user_cart_'.$session_act, []);
                        $data = session()->get('appper_user_cart_'.$session_act);
                        foreach ($detail as $list) {
                            $emp = self::getEmployee($list->emp_id);
                            $data_count = count($data);
                            $data[$data_count]["emp_id"] = $emp->emp_id;
                            $data[$data_count]["emp_name"] = $emp->name . ' ' . $emp->surname;
                            $data[$data_count]["emp_dept"] = $emp->dept_name;
                            $data[$data_count]["emp_position"] = $emp->position_name;
                        }
                        session()->put('appper_user_cart_'.$session_act, $data);
                    }
                }
            }
            if (session()->has('appper_user_cart_'.$session_act)) {
                $data = session()->get('appper_user_cart_'.$session_act);
                $count = count($data);
                if ($count > 0) {
                    for ($i=0; $i<$count; $i++) {
                        $output .= '
                        <tr>
                        <td class="lh35">' . ($i + 1) . '</td>
                        <td class="lh35">' . $data[$i]["emp_id"] . '</td>
                        <td class="lh35">' . $data[$i]["emp_name"] . '</td>
                        <td class="lh35">' . $data[$i]["emp_dept"] . '</td>
                        <td class="lh35">' . $data[$i]["emp_position"] . '</td>
                        <td class="lh35"><a class="action-icon" href="javascript:void(0);" onclick="remove_data(\''.$data[$i]["emp_id"].'\')" title="ลบ"><i class="mdi mdi-delete"></i></a></td>
                        </tr>
                        ';
                    }
                } else {
                    $output = ' <tr> <td align="center" colspan="6"> ไม่พบข้อมูล </td> </tr> ';
                }
            } else {
                $output = ' <tr> <td align="center" colspan="6"> ไม่พบข้อมูล </td> </tr> ';
            }
            $result = array(
                'count_data'  => $count,
                'table_data'  => $output,
            );
            echo json_encode($result);
        }
    }

    public function add_user(Request $request)
    {
        if ($request->ajax()) {
            $session_act = $request->session_act;
            $isData = false;
            if (session()->has('appper_user_cart_'.$session_act)) {
                $data = session()->get('appper_user_cart_'.$session_act);
                $index = array_search($request->emp_id, array_column($data, "emp_id"));
                if ($index !== false) {
                    $isData = true;
                }
                $data = session()->get('appper_user_cart_'.$session_act);
                $data_count = count($data);
            } else {
                $data = [];
                $data_count = 0;
            }
            if ($isData === false) {
                $emp = self::getEmployee($request->emp_id);
                if (!$emp) {
                    return response()->json(["success"=>false, "message"=>"ไม่พบรหัสพนักงาน"]);
                }
                $data_count = count($data);
                $data[$data_count]["emp_id"] = $emp->emp_id;
                $data[$data_count]["emp_name"] = $emp->name . ' ' . $emp->surname;
                $data[$data_count]["emp_dept"] = $emp->dept_name;
                $data[$data_count]["emp_position"] = $emp->position_name;
                $data = array_values($data);
            }
            session()->put('appper_user_cart_'.$session_act, $data);
            return response()->json(["success"=>true, "message"=>""]);
        }
    }

    public function remove_user(Request $request)
    {
        $session_act = $request->session_act;
        if (session()->has('appper_user_cart_'.$session_act)) {
            $data = session()->get('appper_user_cart_'.$session_act);
            $index = array_search($request->emp_id, array_column($data, "emp_id"));
            if ($index !== false) {
                unset($data[$index]);
            }
            $data = array_values($data);
            session()->put('appper_user_cart_'.$session_act, $data);
        }
        return response()->json(["success"=>true, "message"=>""]);
    }

    public function create()
    {
        $appper = AppPermission::groupBy('app_id')->orderBy('app_id', 'ASC')->get();
        $app_arr = [];
        if ($appper->isNotEmpty()) {
            foreach ($appper as $value) {
                $app_arr[] = $value->app_id;
            }
        }
        $application = DB::table('applications as a')->leftjoin('app_permissions as ap', 'a.id', '=', 'ap.app_id')->whereNotIn('a.id', $app_arr)->groupBy('a.id')->orderBy('a.id', 'ASC')->get(['a.*']);
        $dept1 = Department::where('level', '<>', 0)->whereRaw('substring(dept_id, 1, 3) = "A01"')->orderBy('dept_id', 'asc')->get();
        $dept2 = Department::where('level', '<>', 0)->whereRaw('substring(dept_id, 1, 3) = "A02"')->orderBy('dept_id', 'asc')->get();
        $dept3 = Department::where('level', '<>', 0)->whereRaw('substring(dept_id, 1, 3) = "A03"')->orderBy('dept_id', 'asc')->get();
        return view('admin.app-permission-create', compact('application', 'dept1', 'dept2', 'dept3'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'app_id' => 'required',
        ],[
            'app_id.required' => 'กรุณาเลือกระบบงาน',
        ]);

        $session_act = $request->session_act;
        $data = [];
        if (session()->has('appper_user_cart_'.$session_act)) {
            $data = session()->get('appper_user_cart_'.$session_act);
        }
        $emp_count = count($data);

        if ($request->dept == null && $emp_count <= 0) {
            alert()->warning('โปรดเลือกสิทธิ์หน่วยงาน / แผนก หรือ สิทธิ์รายบุคคล!');
            return back();
        }

        try {
            DB::beginTransaction();

            $dept_in = array();
            $app = Application::find($request->app_id);
            if ($request->dept != null) {
                foreach ($request->dept as $dept) {
                    $app_permission = AppPermission::firstOrNew(array('app_id' => $app->id, 'dept_id' => $dept));
                    $app_permission->app_id = $app->id;
                    $app_permission->app_name = $app->name;
                    $app_permission->dept_id = $dept;
                    $app_permission->save();
                    $dept_in[] = $dept;
                }
            }
            if (count($dept_in) > 0) {
                AppPermission::where('app_id', '=', $app->id)->whereRaw('(emp_id = "" OR emp_id IS NULL)')->whereNotIn('dept_id', $dept_in)->delete();
            }

            $emp_in = array();
            if ($emp_count > 0) {
                foreach ($data as $data) {
                    $app_permission = AppPermission::firstOrNew(array('app_id' => $app->id, 'emp_id' => $data["emp_id"]));
                    $app_permission->app_id = $app->id;
                    $app_permission->app_name = $app->name;
                    $app_permission->emp_id = $data["emp_id"];
                    $app_permission->save();
                    $emp_in[] = $data["emp_id"];
                }
            }
            if (count($emp_in) > 0) {
                AppPermission::where('app_id', '=', $app->id)->whereRaw('(dept_id = "" OR dept_id IS NULL)')->whereNotIn('emp_id', $emp_in)->delete();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            alert()->error('Error!', $e);
            return back();
        }

        if ($emp_count > 0) {
            session()->forget('appper_user_cart_'.$session_act);
        }

        alert()->success('เพิ่มสิทธิ์เรียบร้อย');
        return redirect('admin/application/permission');
    }

    public function edit($id)
    {
        $application = Application::find($id);
        $dept1 = Department::where('level', '<>', 0)->whereRaw('substring(dept_id, 1, 3) = "A01"')->orderBy('dept_id', 'asc')->get();
        $dept2 = Department::where('level', '<>', 0)->whereRaw('substring(dept_id, 1, 3) = "A02"')->orderBy('dept_id', 'asc')->get();
        $dept3 = Department::where('level', '<>', 0)->whereRaw('substring(dept_id, 1, 3) = "A03"')->orderBy('dept_id', 'asc')->get();
        $dept = AppPermission::where('app_id', '=', $id)->whereRaw('(emp_id = "" OR emp_id IS NULL)')->get();
        $empper = AppPermission::where('app_id', '=', $id)->whereRaw('(dept_id = "" OR dept_id IS NULL)')->get();

        $session_act = "edit";

        $emp = [];
        foreach ($empper as $list) {
            $employee = self::getEmployee($list->emp_id);
            $emp_count = count($emp);
            $emp[$emp_count]["emp_id"] = $employee->emp_id;
            $emp[$emp_count]["emp_name"] = $employee->name . ' ' . $employee->surname;
            $emp[$emp_count]["emp_dept"] = $employee->dept_name;
            $emp[$emp_count]["emp_position"] = $employee->position_name;
        }
        session()->put('appper_user_cart_'.$session_act, $emp);

        return view('admin.app-permission-edit', compact('application', 'dept1', 'dept2', 'dept3', 'dept', 'emp'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'app_id' => 'required',
        ],[
            'app_id.required' => 'กรุณาเลือกระบบงาน',
        ]);

        $session_act = $request->session_act;
        $data = [];
        if (session()->has('appper_user_cart_'.$session_act)) {
            $data = session()->get('appper_user_cart_'.$session_act);
        }
        $emp_count = count($data);

        if ($request->dept == null && $emp_count <= 0) {
            alert()->warning('โปรดเลือกสิทธิ์หน่วยงาน / แผนก หรือ สิทธิ์รายบุคคล!');
            return back();
        }

        try {
            DB::beginTransaction();

            $dept_in = array();
            $app = Application::find($request->app_id);
            if ($request->dept != null) {
                foreach ($request->dept as $dept) {
                    $app_permission = AppPermission::firstOrNew(array('app_id' => $app->id, 'dept_id' => $dept));
                    $app_permission->app_id = $app->id;
                    $app_permission->app_name = $app->name;
                    $app_permission->dept_id = $dept;
                    $app_permission->save();
                    $dept_in[] = $dept;
                }
            }
            if (count($dept_in) > 0) {
                AppPermission::where('app_id', '=', $app->id)->whereRaw('(emp_id = "" OR emp_id IS NULL)')->whereNotIn('dept_id', $dept_in)->delete();
            } else {
                AppPermission::where('app_id', '=', $app->id)->whereRaw('(emp_id = "" OR emp_id IS NULL)')->delete();
            }

            $emp_in = array();
            if ($emp_count > 0) {
                foreach ($data as $data) {
                    $app_permission = AppPermission::firstOrNew(array('app_id' => $app->id, 'emp_id' => $data["emp_id"]));
                    $app_permission->app_id = $app->id;
                    $app_permission->app_name = $app->name;
                    $app_permission->emp_id = $data["emp_id"];
                    $app_permission->save();
                    $emp_in[] = $data["emp_id"];
                }
            }
            if (count($emp_in) > 0) {
                AppPermission::where('app_id', '=', $app->id)->whereRaw('(dept_id = "" OR dept_id IS NULL)')->whereNotIn('emp_id', $emp_in)->delete();
            } else {
                AppPermission::where('app_id', '=', $app->id)->whereRaw('(dept_id = "" OR dept_id IS NULL)')->delete();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            alert()->error('Error!', $e);
            return back();
        }

        if ($emp_count > 0) {
            session()->forget('appper_user_cart_'.$session_act);
        }

        alert()->success('อัปเดตสิทธิ์เรียบร้อย');
        return redirect('admin/application/permission');
    }

    public function destroy($id)
    {
        $app_permission = AppPermission::where('app_id', '=', $id);
        $app_permission->delete();
        return response()->json([
            'success' => true,
            'message' => 'ลบข้อมูลสิทธิ์เรียบร้อย',
        ]);
    }
}