<?php

namespace App\Http\Controllers;

use App\Models\AuthorizationManual;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthorizationManualController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('organization.authorization.authorization-manual');
    }

    public function search(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('authorization_manual as a');

            $totalRecords = $data->select('count(a.*) as allcount')->count();
            $records = $data->select('a.*')->orderBy("a.emp_id", "ASC")->get();
            $rows = [];
            $n = 1;
            foreach ($records as $rec) {
                $action = '<div>
                        <a class="action-icon" href="'.url('organization/authorization-manual/edit', $rec->id).'" title="แก้ไข"><i class="mdi mdi-square-edit-outline"></i></a>
                        <a class="action-icon" href="javascript:void(0);" onclick="deleteAuthorizationManualConfirmation(\''.$rec->id.'\')" title="ลบ"><i class="mdi mdi-delete"></i></a>
                    </div>';

                $emp = self::get_emp($rec->emp_id);
                $empid = ($emp) ? $emp->name . ' ' . $emp->surname . ' ('.$rec->emp_id.')' : $rec->emp_id;
                $dept = ($emp) ? $emp->dept_name : '';
                $user = self::get_emp($rec->auth);
                $auth = ($user) ? $user->name . ' ' . $user->surname . ' ('.$rec->auth.')' : $rec->auth;
                $user2 = self::get_emp($rec->auth2);
                $auth2 = ($user2) ? $user2->name . ' ' . $user2->surname . ' ('.$rec->auth2.')' : $rec->auth2;

                $rows[] = array(
                    "no" => $n,
                    "empid" => $empid,
                    "dept" => $dept,
                    "auth" => $auth,
                    "auth2" => $auth2,
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

    public function get_emp($id)
    {
        $emp = DB::table('employee as e')->leftjoin('department as d', 'd.dept_id', '=', 'e.dept_id')->where('e.emp_id', '=', $id)->select('e.name', 'e.surname', 'd.dept_id', 'd.dept_name')->first();
        return $emp;
    }

    public function create()
    {
        return view('organization.authorization.authorization-manual-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'emp_id' => 'required',
        ],[
            'emp_id.required' => 'กรุณาระบุพนักงาน',
        ]);

        $authorization = new AuthorizationManual();
        $authorization->emp_id = $request->emp_id;
        $authorization->auth = $request->auth;
        $authorization->auth2 = $request->auth2;
        $authorization->save();

        alert()->success('เพิ่มสิทธิ์การอนุมัติ (รายบุคคล) เรียบร้อย');
        return redirect('organization/authorization-manual');
    }

    public function edit($id)
    {
        $authorization = AuthorizationManual::find($id);
        $emp = self::get_emp($authorization->emp_id);
        $emp_name = ($emp) ? $emp->name . ' ' . $emp->surname : '';
        $user = self::get_emp($authorization->auth);
        $auth_name = ($user) ? $user->name . ' ' . $user->surname : '';
        $user2 = self::get_emp($authorization->auth2);
        $auth2_name = ($user2) ? $user2->name . ' ' . $user2->surname : '';
        return view('organization.authorization.authorization-manual-edit', compact('authorization', 'emp_name', 'auth_name', 'auth2_name'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'emp_id' => 'required',
        ],[
            'id.required' => 'ไม่พบสิทธิ์การอนุมัติ',
            'emp_id.required' => 'กรุณาระบุพนักงาน',
        ]);

        if ($request->emp_id != "") {
            $emp = self::get_emp($request->emp_id);
            if (!$emp) {
                alert()->warning('ไม่พบข้อมูลพนักงาน!');
                return back();
            }
        }
        if ($request->auth != "") {
            $emp = self::get_emp($request->auth);
            if (!$emp) {
                alert()->warning('ไม่พบข้อมูลผู้อนุมัติ 1!');
                return back();
            }
        }
        if ($request->auth2 != "") {
            $emp = self::get_emp($request->auth2);
            if (!$emp) {
                alert()->warning('ไม่พบข้อมูลผู้อนุมัติ 2!');
                return back();
            }
        }

        $authorization = AuthorizationManual::find($request->id);
        $authorization->emp_id = $request->emp_id;
        $authorization->auth = $request->auth;
        $authorization->auth2 = $request->auth2;
        $authorization->save();

        alert()->success('อัปเดตสิทธิ์การอนุมัติ (รายบุคคล) เรียบร้อย');
        return redirect('organization/authorization-manual');
    }

    public function destroy($id)
    {
        $authorization = AuthorizationManual::find($id);
        $authorization->delete();
        return response()->json([
            'success' => true,
            'message' => 'ลบข้อมูลสิทธิ์การอนุมัติ (รายบุคคล) เรียบร้อย',
        ]);
    }
}