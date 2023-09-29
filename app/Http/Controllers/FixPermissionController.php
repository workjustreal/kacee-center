<?php

namespace App\Http\Controllers;

use App\Models\FixPermission;
use App\Models\FixPermissionUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FixPermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('admin.fix_permissions.fix-permission-list');
    }

    public function search(Request $request)
    {
        if ($request->ajax()) {
            $totalRecords = FixPermission::select('count(*) as allcount')->count();
            $records = FixPermission::select('*')->orderBy("permission", "asc")->get();
            $rows = [];
            $n = 1;
            foreach ($records as $rec) {
                // $action = '<div>
                //         <a class="action-icon" href="'.url('admin/fix-permissions/edit', $rec->id).'" title="แก้ไข"><i class="mdi mdi-square-edit-outline"></i></a>
                //         <a class="action-icon" href="javascript:void(0);" onclick="deleteFixPermissionConfirmation(\''.$rec->id.'\')" title="ลบ"><i class="mdi mdi-delete"></i></a>
                //     </div>';
                $action = '<div>
                        <a class="action-icon" href="'.url('admin/fix-permissions/edit', $rec->id).'" title="แก้ไข"><i class="mdi mdi-square-edit-outline"></i></a>
                    </div>';
                $rows[] = array(
                    "no" => $n,
                    "id" => '<b>#'.$rec->id.'</b>',
                    "permission" => $rec->permission,
                    "description" => $rec->description,
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

    public function create()
    {
        return view('admin.fix_permissions.fix-permission-create');
    }

    public function edit($id)
    {
        $permission = FixPermission::find($id);
        $empper = FixPermissionUser::where('permission_id', '=', $id)->orderBy("emp_id", "asc")->get();

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
        session()->put('fixper_user_cart_'.$session_act, $emp);
        return view('admin.fix_permissions.fix-permission-edit')->with('permission', $permission)->with('emp', $emp);
    }

    public function store(Request $request)
    {
        $request->validate([
            'permission' => 'required|unique:fix_permissions,permission',
            'description' => 'required',
        ],[
            'permission.required' => 'ระบุชื่อสิทธิ์',
            'permission.unique' => 'ชื่อสิทธิ์ซ้ำกับที่มีอยู่',
            'description.required' => 'ระบุรายละเอียดสิทธิ์',
        ]);

        $session_act = $request->session_act;
        $data = [];
        if (session()->has('fixper_user_cart_'.$session_act)) {
            $data = session()->get('fixper_user_cart_'.$session_act);
        }
        $emp_count = count($data);

        if ($emp_count <= 0) {
            alert()->warning('โปรดกำหนดสิทธิ์รายบุคคล!');
            return back();
        }

        try {
            DB::beginTransaction();

            $permission = FixPermission::orderBy('id', 'desc')->first();
            $permission_id = ($permission) ? $permission->id + 1 : 1;

            $permission = new FixPermission();
            $permission->permission = $request->permission;
            $permission->description = $request->description;
            $permission->save();

            $emp_in = array();
            if ($emp_count > 0) {
                foreach ($data as $data) {
                    $permission = FixPermissionUser::firstOrNew(array('permission_id' => $permission_id, 'emp_id' => $data["emp_id"]));
                    $permission->permission_id = $permission_id;
                    $permission->emp_id = $data["emp_id"];
                    $permission->save();
                    $emp_in[] = $data["emp_id"];
                }
            }
            if (count($emp_in) > 0) {
                FixPermissionUser::where('permission_id', '=', $permission_id)->whereNotIn('emp_id', $emp_in)->delete();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            alert()->error('Error!', $e);
            return back();
        }

        if ($emp_count > 0) {
            session()->forget('fixper_user_cart_'.$session_act);
        }

        $request->flash();
        alert()->success('เพิ่มข้อมูลเรียบร้อย');
        return redirect('/admin/fix-permissions');
    }

    public function update(Request $request)
    {
        $request->validate([
            'permission' => 'required|unique:fix_permissions,permission,'.$request->permission_id,
            'description' => 'required',
        ],[
            'permission.required' => 'ระบุชื่อสิทธิ์',
            'permission.unique' => 'ชื่อสิทธิ์ซ้ำกับที่มีอยู่',
            'description.required' => 'ระบุรายละเอียดสิทธิ์',
        ]);

        $session_act = $request->session_act;
        $permission_id = $request->permission_id;
        $data = [];
        if (session()->has('fixper_user_cart_'.$session_act)) {
            $data = session()->get('fixper_user_cart_'.$session_act);
        }
        $emp_count = count($data);

        if ($emp_count <= 0) {
            alert()->warning('โปรดกำหนดสิทธิ์รายบุคคล!');
            return back();
        }

        try {
            DB::beginTransaction();

            $permission = FixPermission::find($permission_id);
            $permission->permission = $request->permission;
            $permission->description = $request->description;
            $permission->save();

            $emp_in = array();
            if ($emp_count > 0) {
                foreach ($data as $data) {
                    $permission = FixPermissionUser::firstOrNew(array('permission_id' => $permission_id, 'emp_id' => $data["emp_id"]));
                    $permission->permission_id = $permission_id;
                    $permission->emp_id = $data["emp_id"];
                    $permission->save();
                    $emp_in[] = $data["emp_id"];
                }
            }
            if (count($emp_in) > 0) {
                FixPermissionUser::where('permission_id', '=', $permission_id)->whereNotIn('emp_id', $emp_in)->delete();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            alert()->error('Error!', $e);
            return back();
        }

        if ($emp_count > 0) {
            session()->forget('fixper_user_cart_'.$session_act);
        }

        $request->flash();
        alert()->success('อัปเดตข้อมูลเรียบร้อย');
        return redirect('/admin/fix-permissions');
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $permission = FixPermission::find($id);
            $permission->delete();
            $permission_user = FixPermissionUser::where('permission_id', '=', $id);
            $permission_user->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            alert()->error('Error!', $e);
            return back();
        }
        return response()->json([
            'success' => true,
            'message' => 'ลบข้อมูลเรียบร้อย',
        ]);
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
                if (session()->has('fixper_user_cart_'.$session_act)) {
                    session()->forget('fixper_user_cart_'.$session_act);
                }
                if ($request->has('permission_id')) {
                    $detail = DB::table('fix_permission_user as u')->leftJoin('fix_permissions as p', 'u.permission_id', '=', 'p.id')
                    ->where('u.permission_id', '=', $request->permission_id)->select('u.*', 'p.permission')->orderBy("u.permission_id", "asc")->orderBy("u.emp_id", "asc")->get();
                    if ($detail->isNotEmpty()) {
                        session()->put('fixper_user_cart_'.$session_act, []);
                        $data = session()->get('fixper_user_cart_'.$session_act);
                        foreach ($detail as $list) {
                            $emp = self::getEmployee($list->emp_id);
                            $data_count = count($data);
                            $data[$data_count]["emp_id"] = $emp->emp_id;
                            $data[$data_count]["emp_name"] = $emp->name . ' ' . $emp->surname;
                            $data[$data_count]["emp_dept"] = $emp->dept_name;
                            $data[$data_count]["emp_position"] = $emp->position_name;
                        }
                        session()->put('fixper_user_cart_'.$session_act, $data);
                    }
                }
            }
            if (session()->has('fixper_user_cart_'.$session_act)) {
                $data = session()->get('fixper_user_cart_'.$session_act);
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
            if (session()->has('fixper_user_cart_'.$session_act)) {
                $data = session()->get('fixper_user_cart_'.$session_act);
                $index = array_search($request->emp_id, array_column($data, "emp_id"));
                if ($index !== false) {
                    $isData = true;
                }
                $data = session()->get('fixper_user_cart_'.$session_act);
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
            session()->put('fixper_user_cart_'.$session_act, $data);
            return response()->json(["success"=>true, "message"=>""]);
        }
    }

    public function remove_user(Request $request)
    {
        $session_act = $request->session_act;
        if (session()->has('fixper_user_cart_'.$session_act)) {
            $data = session()->get('fixper_user_cart_'.$session_act);
            $index = array_search($request->emp_id, array_column($data, "emp_id"));
            if ($index !== false) {
                unset($data[$index]);
            }
            $data = array_values($data);
            session()->put('fixper_user_cart_'.$session_act, $data);
        }
        return response()->json(["success"=>true, "message"=>""]);
    }
}