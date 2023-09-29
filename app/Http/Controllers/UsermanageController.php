<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Models\User;

class UsermanageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = User::leftjoin('department', 'users.dept_id', 'department.dept_id')
                    ->select('users.*', 'department.dept_name')
                    ->orderBy('users.name', 'asc')->get();
        return view("Auth.usermanage")->with('user', $user);
    }

    public function search(Request $request)
    {
        if ($request->ajax()) {
            $data = User::leftjoin('department', 'users.dept_id', '=', 'department.dept_id')->leftjoin('employee', 'users.emp_id', '=', 'employee.emp_id')->leftjoin('roles', 'users.is_role', '=', 'roles.id');

            $totalRecords = $data->select('count(users.*) as allcount')->count();
            $records = $data->select('users.*', 'employee.nickname', 'department.dept_name', 'roles.role')->orderBy('users.name', 'asc')->get();
            $rows = [];
            foreach ($records as $rec) {
                if ($rec->is_admin == "1") {
                    $level = '<span class="badge bg-soft-success text-success">Super Admin</span>';
                } else {
                    $level = '<span class="badge bg-soft-secondary text-secondary">User</span>';
                }
                if ($rec->is_role == "1") {
                    $role = '<span class="badge bg-soft-success text-success">'.$rec->role.'</span>';
                } else if ($rec->is_role == "2") {
                    $role = '<span class="badge bg-soft-secondary text-secondary">'.$rec->role.'</span>';
                } else if ($rec->is_role == "3") {
                    $role = '<span class="badge bg-soft-pink text-pink">'.$rec->role.'</span>';
                } else if ($rec->is_role == "4") {
                    $role = '<span class="badge bg-soft-primary text-primary">'.$rec->role.'</span>';
                } else if ($rec->is_role == "5") {
                    $role = '<span class="badge bg-soft-blue text-blue">'.$rec->role.'</span>';
                } else if ($rec->is_role == "6") {
                    $role = '<span class="badge bg-soft-info text-info">'.$rec->role.'</span>';
                } else {
                    $role = '<span class="badge bg-soft-secondary text-secondary">'.$rec->role.'</span>';
                }
                if ($rec->is_login == "1") {
                    $login = '<span class="badge bg-soft-success text-success">เปิด</span>';
                } else {
                    $login = '<span class="badge bg-soft-secondary text-secondary">ปิด</span>';
                }
                if ($rec->is_flag == "1") {
                    $verified = '<span class="badge bg-soft-success text-success">ยืนยันแล้ว</span>';
                } else {
                    $verified = '<span class="badge bg-soft-secondary text-secondary">ยังไม่ยืนยัน</span>';
                }
                $action = '<div>
                        <a class="action-icon" href="user-edit/'.$rec->id.'" title="แก้ไข"><i class="mdi mdi-square-edit-outline"></i></a>
                        <a class="action-icon" href="javascript:void(0);" onclick="deleteUser(\''.$rec->id.'\')" title="ลบ"><i class="mdi mdi-delete"></i></a>
                    </div>';
                $rows[] = array(
                    // "name" => '<div class="table-user"><img src="'.url('assets/images/users/thumbnail/'.$rec->image).'" onerror="this.onerror=null;this.src=\''.url('assets/images/users/thumbnail/user-1.jpg').'\';" alt="table-user" class="me-2 rounded-circle">' . $rec->name . ' ' . $rec->surname . '</div>',
                    "name" => self::callUserName($rec->image, $rec->name, $rec->surname, $rec->nickname),
                    "emp_id" => $rec->emp_id,
                    "dept" => $rec->dept_name,
                    "email" => $rec->email,
                    "level" => $level,
                    "role" => $role,
                    "login" => $login,
                    "verified" => $verified,
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

    public function callUserName($image, $name, $surname, $nickname)
    {
        $nname = '';
        if ($nickname != "") {
            $nname = ' ('.$nickname.')';
        }
        $result = '<div class="table-user"><img src="'.url('assets/images/users/thumbnail/'.$image).'" onerror="this.onerror=null;this.src=\''.url('assets/images/users/thumbnail/user-1.jpg').'\';" alt="table-user" class="me-2 rounded-circle">' . $name . ' ' . $surname . $nname . '</div>';
        return $result;
    }

    public function register()
    {
        $role = Role::orderBy('id', 'asc')->get();
        return view('auth.register')->with('role', $role);
    }

    public function store(Request $request)
    {
        $request->validate([
            'new_password' => ['required'],
            'new_confirm_password' => ['same:new_password'],
        ]);

        User::find(auth()->user()->id)->update(['password' => Hash::make($request->new_password)]);
        return redirect('admin/user-manage');
    }

    public function edit($id)
    {
        $user = User::find($id);
        $role = Role::orderBy('id', 'asc')->get();
        return view("auth.useredit")->with('id', $id)->with('user', $user)->with('role', $role);
    }

    public function changepassword($id)
    {
        $user = User::find($id);
        return view("Auth.changepassword")->with('id', $id)->with('user', $user);
    }

    public function resetpassword($id)
    {
        $user = User::find($id);
        $user->is_flag = 0;
        $user->password = bcrypt('kacee');
        $user->update();
        sleep(1);
        return redirect('admin/user-manage')->with('user', $user);
    }

    public function delete($id)
    {
        $user = User::find($id);
        $user->delete();
        sleep(1);
        return redirect('admin/user-manage')->with('user', $user);
    }

    public function updateuser(Request $request, $id)
    {
        if ($request->has('is_admin')) {
            $is_admin = $request->input('is_admin');
        } else {
            $is_admin = 0;
        }

        $user = User::find($id);
        $user->name = $request->input("name");
        $user->surname = $request->input("surname");
        $user->emp_id = $request->input("emp_id");
        $user->email = $request->input("email");
        $user->is_admin = $is_admin;
        $user->is_role = $request->input("is_role");
        $user->is_login = $request->input("is_login");
        $user->save();
        alert()->success('อัปเดตข้อมูลเรียบร้อย');
        return redirect('admin/user-manage');
    }

    public function createuser(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'emp_id' => 'required',
            'email' => 'required',
            'password' => 'required|same:password_confirmation',
            'password_confirmation' => 'required',
        ]);

        if ($request->has('is_admin')) {
            $is_admin = $request->input('is_admin');
        } else {
            $is_admin = 0;
        }

        $user = User::create([
            'name' => trim($request->input('name')),
            'surname' => trim($request->input('surname')),
            'image' => 'user-1.jpg',
            'emp_id' => trim($request->input('emp_id')),
            'email' => strtolower($request->input('email')),
            'password' => bcrypt($request->input('password')),
            'is_admin' => $is_admin,
            'is_role' => $request->input('is_role'),
            'is_login' => $request->input('is_login'),
            'is_flag' => 0,
        ]);

        alert()->success('เพิ่มข้อมูลเรียบร้อย');
        return redirect('admin/user-manage');
    }

    public function userdetail(Request $request)
    {
        $emp_id = $request->get('emp_id');

        $result = User::where('emp_id', '=', $emp_id)->get();

        return response()->json($result);
    }
}