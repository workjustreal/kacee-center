<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $role = Role::orderBy('id', 'asc')->get();
        return view('admin.roles.role-list')->with('role', $role);
    }

    public function search(Request $request)
    {
        if ($request->ajax()) {
            $totalRecords = Role::select('count(*) as allcount')->count();
            $records = Role::select('*')->orderBy("id", "asc")->get();
            $rows = [];
            $n = 1;
            foreach ($records as $rec) {
                // $action = '<div>
                //         <a class="action-icon" href="'.url('admin/roles/edit', $rec->id).'" title="แก้ไข"><i class="mdi mdi-square-edit-outline"></i></a>
                //         <a class="action-icon" href="javascript:void(0);" onclick="deleteRoleConfirmation(\''.$rec->id.'\')" title="ลบ"><i class="mdi mdi-delete"></i></a>
                //     </div>';
                $action = '<div>
                        <a class="action-icon" href="'.url('admin/roles/edit', $rec->id).'" title="แก้ไข"><i class="mdi mdi-square-edit-outline"></i></a>
                    </div>';
                $rows[] = array(
                    "no" => $n,
                    "role" => $rec->role,
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
        $permission = Permission::orderBy('permission', 'asc')->get();
        return view('admin.roles.role-create')->with('permission', $permission);
    }

    public function edit($id)
    {
        $role = Role::find($id);
        $permission = Permission::orderBy('permission', 'asc')->get();
        $role_permission = RolePermission::where('role_id', '=', $id)->orderBy('role_id', 'asc')->orderBy('permission_id', 'asc')->get();
        return view('admin.roles.role-edit')->with('role', $role)->with('permission', $permission)->with('role_permission', $role_permission);
    }

    public function store(Request $request)
    {
        $request->validate([
            'role' => 'required|unique:roles,role',
        ],[
            'role.required' => 'ระบุชื่อบทบาท',
            'role.unique' => 'ชื่อบทบาทซ้ำกับที่มีอยู่',
        ]);

        try {
            DB::beginTransaction();

            $role = Role::orderBy('id', 'desc')->first();
            $role_id = ($role) ? $role->id + 1 : 1;

            $role = new Role();
            $role->role = $request->role;
            $role->save();

            $permission_in = array();
            if ($request->permission != null) {
                foreach ($request->permission as $permission) {
                    $role_permission = RolePermission::firstOrNew(array('role_id' => $role_id, 'permission_id' => $permission));
                    $role_permission->role_id = $role_id;
                    $role_permission->permission_id = $permission;
                    $role_permission->save();
                    $permission_in[] = $permission;
                }
            }
            if (count($permission_in) > 0) {
                RolePermission::where('role_id', '=', $role_id)->whereNotIn('permission_id', $permission_in)->delete();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            alert()->error('Error!', $e);
            return back();
        }

        $request->flash();
        alert()->success('เพิ่มข้อมูลเรียบร้อย');
        return redirect('/admin/roles');
    }

    public function update(Request $request)
    {
        $request->validate([
            'role' => 'required|unique:roles,role,'.$request->role_id,
        ],[
            'role.required' => 'ระบุชื่อบทบาท',
            'role.unique' => 'ชื่อบทบาทซ้ำกับที่มีอยู่',
        ]);

        try {
            DB::beginTransaction();

            $role_id = $request->role_id;

            $role = Role::find($role_id);
            $role->role = $request->role;
            $role->save();

            $permission_in = array();
            if ($request->permission != null) {
                foreach ($request->permission as $permission) {
                    $role_permission = RolePermission::firstOrNew(array('role_id' => $role_id, 'permission_id' => $permission));
                    $role_permission->role_id = $role_id;
                    $role_permission->permission_id = $permission;
                    $role_permission->save();
                    $permission_in[] = $permission;
                }
            }
            if (count($permission_in) > 0) {
                RolePermission::where('role_id', '=', $role_id)->whereNotIn('permission_id', $permission_in)->delete();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            alert()->error('Error!', $e);
            return back();
        }

        $request->flash();
        alert()->success('อัปเดตข้อมูลเรียบร้อย');
        return redirect('/admin/roles');
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $role = Role::find($id);
            $role->delete();
            $role_permission = RolePermission::where('role_id', '=', $id);
            $role_permission->delete();
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
}