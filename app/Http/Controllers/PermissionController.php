<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('admin.permissions.permission-list');
    }

    public function search(Request $request)
    {
        if ($request->ajax()) {
            $totalRecords = Permission::select('count(*) as allcount')->count();
            $records = Permission::select('*')->orderBy("permission", "asc")->get();
            $rows = [];
            $n = 1;
            foreach ($records as $rec) {
                $action = '<div>
                        <a class="action-icon" href="'.url('admin/permissions/edit', $rec->id).'" title="แก้ไข"><i class="mdi mdi-square-edit-outline"></i></a>
                        <a class="action-icon" href="javascript:void(0);" onclick="deletePermissionConfirmation(\''.$rec->id.'\')" title="ลบ"><i class="mdi mdi-delete"></i></a>
                    </div>';
                $rows[] = array(
                    "no" => $n,
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
        return view('admin.permissions.permission-create');
    }

    public function edit($id)
    {
        $permission = Permission::find($id);
        return view('admin.permissions.permission-edit')->with('permission', $permission);
    }

    public function store(Request $request)
    {
        $request->validate([
            'permission' => 'required|unique:permissions,permission',
            'description' => 'required',
        ],[
            'permission.required' => 'ระบุชื่อสิทธิ์',
            'permission.unique' => 'ชื่อสิทธิ์ซ้ำกับที่มีอยู่',
            'description.required' => 'ระบุรายละเอียดสิทธิ์',
        ]);

        $permission = new Permission();
        $permission->permission = $request->input('permission');
        $permission->description = $request->input('description');
        $permission->save();

        $request->flash();
        alert()->success('เพิ่มข้อมูลเรียบร้อย');
        return redirect('/admin/permissions');
    }

    public function update(Request $request)
    {
        $request->validate([
            'permission' => 'required|unique:permissions,permission,'.$request->input('id'),
            'description' => 'required',
        ],[
            'permission.required' => 'ระบุชื่อสิทธิ์',
            'permission.unique' => 'ชื่อสิทธิ์ซ้ำกับที่มีอยู่',
            'description.required' => 'ระบุรายละเอียดสิทธิ์',
        ]);

        $permission = Permission::find($request->input('id'));
        $permission->permission = $request->input('permission');
        $permission->description = $request->input('description');
        $permission->save();

        $request->flash();
        alert()->success('อัปเดตข้อมูลเรียบร้อย');
        return redirect('/admin/permissions');
    }

    public function destroy($id)
    {
        $permission = Permission::find($id);
        $permission->delete();
        return response()->json([
            'success' => true,
            'message' => 'ลบข้อมูลเรียบร้อย',
        ]);
    }
}