<?php

namespace App\Http\Controllers;

use App\Models\Printer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrinterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('admin.printers.printer-list');
    }

    public function search(Request $request)
    {
        if ($request->ajax()) {
            $totalRecords = Printer::select('count(*) as allcount')->count();
            $records = Printer::select('*')->orderBy('role', 'asc')->orderBy("id", "asc")->get();
            $rows = [];
            $n = 1;
            foreach ($records as $rec) {
                $action = '<div>
                        <a class="action-icon" href="'.url('admin/printers/edit', $rec->id).'" title="แก้ไข"><i class="mdi mdi-square-edit-outline"></i></a>
                        <a class="action-icon" href="javascript:void(0);" onclick="deletePrinterConfirmation(\''.$rec->id.'\')" title="ลบ"><i class="mdi mdi-delete"></i></a>
                    </div>';
                $rows[] = array(
                    "no" => $n,
                    "name" => $rec->name,
                    "description" => $rec->description,
                    "type" => $rec->type,
                    "client_ip" => $rec->client_ip,
                    "role" => ($rec->role==1) ? '<span class="badge bg-blue fw-normal">Admin</span>' : '<span class="badge bg-secondary fw-normal">ผู้ใช้งาน</span>',
                    "status" => ($rec->status==1) ? '<span class="badge bg-success fw-normal">ใช้งาน</span>' : '<span class="badge bg-danger fw-normal">ไม่ใช้งาน</span>',
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
        return view('admin.printers.printer-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'type' => 'required',
            'role' => 'required',
            'status' => 'required',
        ],[
            'name.required' => 'ระบุชื่อเครื่องพิมพ์',
            'description.required' => 'ระบุรายละเอียดเครื่องพิมพ์',
            'type.required' => 'ระบุประเภทเครื่องพิมพ์',
            'role.required' => 'ระบุผู้ใช้งานเครื่องพิมพ์',
            'status.required' => 'ระบุสถานะเครื่องพิมพ์',
        ]);

        try {
            $printer = new Printer();
            $printer->name = $request->name;
            $printer->description = $request->description;
            $printer->type = $request->type;
            $printer->client_ip = $request->client_ip;
            $printer->role = $request->role;
            $printer->status = $request->status;
            $printer->user_permission = $request->user_permission;
            $printer->save();
        } catch (\Exception $e) {
            alert()->error('Error!', $e);
            return back();
        }

        $request->flash();
        alert()->success('เพิ่มข้อมูลเรียบร้อย');
        return redirect('/admin/printers');
    }

    public function edit($id)
    {
        $data = Printer::find($id);
        $users = [];
        if (!empty($data->user_permission)) {
            $permission = explode(",", $data->user_permission);
            foreach ($permission as $emp_id) {
                $emp = self::getEmployee($emp_id);
                $users[] = array(
                    "emp_id" => (string)$emp->emp_id,
                    "emp_name" => (string)$emp->name . ' ' . (string)$emp->surname,
                    "emp_dept" => (string)$emp->dept_name,
                    "emp_position" => (string)$emp->position_name,
                );
            }
        }
        return view('admin.printers.printer-edit')->with('data', $data)->with('users', json_encode($users));
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'name' => 'required',
            'description' => 'required',
            'type' => 'required',
            'role' => 'required',
            'status' => 'required',
        ],[
            'name.required' => 'ระบุชื่อเครื่องพิมพ์',
            'description.required' => 'ระบุรายละเอียดเครื่องพิมพ์',
            'type.required' => 'ระบุประเภทเครื่องพิมพ์',
            'role.required' => 'ระบุผู้ใช้งานเครื่องพิมพ์',
            'status.required' => 'ระบุสถานะเครื่องพิมพ์',
        ]);

        try {
            $printer = Printer::find($request->id);
            $printer->name = $request->name;
            $printer->description = $request->description;
            $printer->type = $request->type;
            $printer->client_ip = $request->client_ip;
            $printer->role = $request->role;
            $printer->status = $request->status;
            $printer->user_permission = $request->user_permission;
            $printer->save();
        } catch (\Exception $e) {
            alert()->error('Error!', $e);
            return back();
        }

        $request->flash();
        alert()->success('อัปเดตข้อมูลเรียบร้อย');
        return redirect('/admin/printers');
    }

    public function destroy(Request $request)
    {
        try {
            $printer = Printer::find($request->id);
            $printer->delete();
            return response()->json([
                'success' => true,
                'message' => 'ลบข้อมูลเรียบร้อย',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด!',
            ]);
        }
    }

    public function add_user(Request $request)
    {
        if ($request->ajax()) {
            $emp = self::getEmployee($request->emp_id);
            if (!$emp) {
                return response()->json(["success"=>false, "message"=>"ไม่พบรหัสพนักงาน"]);
            }
            $data = array(
                "emp_id" => (string)$emp->emp_id,
                "emp_name" => (string)$emp->name . ' ' . (string)$emp->surname,
                "emp_dept" => (string)$emp->dept_name,
                "emp_position" => (string)$emp->position_name,
            );
            return response()->json(["success"=>true, "message"=>"", "data"=>$data]);
        }
    }

    public function getEmployee($id)
    {
        $result = DB::table('employee as e')->leftJoin('department as d', 'e.dept_id', '=', 'e.dept_id')->leftJoin('position as p', 'p.position_id', '=', 'e.position_id')
        ->where('e.emp_id', '=', $id)->select('e.*', 'd.dept_name', 'p.position_name')->first();
        return $result;
    }
}