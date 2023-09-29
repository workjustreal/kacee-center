<?php

namespace App\Http\Controllers;

use App\Models\Authorization;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthorizationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('organization.authorization.authorization');
    }

    public function search(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('authorization as a')->leftJoin('department as d', 'a.dept_id', '=', 'd.dept_id');

            $totalRecords = $data->select('count(a.*) as allcount')->count();
            $records = $data->select('a.*', 'd.dept_name')->orderBy("a.dept_id", "ASC")->get();
            $rows = [];
            $n = 1;
            foreach ($records as $rec) {
                $action = '<div>
                        <a class="action-icon" href="'.url('organization/authorization/edit', $rec->id).'" title="แก้ไข"><i class="mdi mdi-square-edit-outline"></i></a>
                        <a class="action-icon" href="javascript:void(0);" onclick="deleteAuthorizationConfirmation(\''.$rec->id.'\')" title="ลบ"><i class="mdi mdi-delete"></i></a>
                    </div>';

                $user = self::get_emp($rec->auth);
                $auth = ($user) ? $user->name . ' ' . $user->surname . ' ('.$rec->auth.')' : $rec->auth;
                $user2 = self::get_emp($rec->auth2);
                $auth2 = ($user2) ? $user2->name . ' ' . $user2->surname . ' ('.$rec->auth2.')' : $rec->auth2;

                $rows[] = array(
                    "no" => $n,
                    "dept_id" => $rec->dept_id,
                    "dept_name" => $rec->dept_name,
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
        $emp = Employee::where('emp_id', '=', $id)->select('name', 'surname')->first();
        return $emp;
    }

    public function create()
    {
        $level1 = Department::where('level', '=', 1)->get();
        $level2 = Department::where('level', '=', 2)->get();
        $level3 = Department::where('level', '=', 3)->get();
        $level4 = Department::where('level', '=', 4)->get();
        return view('organization.authorization.authorization-create', compact('level1', 'level2', 'level3', 'level4'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'dept_id' => 'required',
        ],[
            'dept_id.required' => 'กรุณาเลือกหน่วยงาน / แผนก',
        ]);

        if ($request->auth == "" && $request->auth2 == "") {
            alert()->warning('โปรดระบุผู้อนุมัติอย่างน้อย 1 คน!');
            return back();
        }

        $authorization = new Authorization();
        $authorization->dept_id = $request->dept_id;
        $authorization->auth = $request->auth;
        $authorization->auth2 = $request->auth2;
        $authorization->save();

        alert()->success('เพิ่มสิทธิ์การอนุมัติเรียบร้อย');
        return redirect('organization/authorization');
    }

    public function edit($id)
    {
        $authorization = Authorization::find($id);
        $user = self::get_emp($authorization->auth);
        $auth_name = ($user) ? $user->name . ' ' . $user->surname : '';
        $user2 = self::get_emp($authorization->auth2);
        $auth2_name = ($user2) ? $user2->name . ' ' . $user2->surname : '';
        $level1 = Department::where('level', '=', 1)->get();
        $level2 = Department::where('level', '=', 2)->get();
        $level3 = Department::where('level', '=', 3)->get();
        $level4 = Department::where('level', '=', 4)->get();
        return view('organization.authorization.authorization-edit', compact('authorization', 'auth_name', 'auth2_name', 'level1', 'level2', 'level3', 'level4'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'dept_id' => 'required',
        ],[
            'id.required' => 'ไม่พบสิทธิ์การอนุมัติลางาน',
            'dept_id.required' => 'กรุณาเลือกหน่วยงาน / แผนก',
        ]);

        if ($request->auth == "" && $request->auth2 == "") {
            alert()->warning('โปรดระบุผู้อนุมัติอย่างน้อย 1 คน!');
            return back();
        }

        $authorization = Authorization::find($request->id);
        $authorization->dept_id = $request->dept_id;
        $authorization->auth = $request->auth;
        $authorization->auth2 = $request->auth2;
        $authorization->save();

        alert()->success('อัปเดตสิทธิ์การอนุมัติลางานเรียบร้อย');
        return redirect('organization/authorization');
    }

    public function destroy($id)
    {
        $authorization = Authorization::find($id);
        $authorization->delete();
        return response()->json([
            'success' => true,
            'message' => 'ลบข้อมูลสิทธิ์การอนุมัติลางานเรียบร้อย',
        ]);
    }
}