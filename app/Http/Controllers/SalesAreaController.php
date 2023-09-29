<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\SalesArea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesAreaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('organization.sales-area.index');
    }

    public function search(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('sales_area as s')->leftjoin('department as d', 'd.dept_id', '=', 's.dept_id');
            $totalRecords = $data->select('count(s.*) as allcount')->count();
            $records = $data->select('s.*', 'd.dept_name')->orderBy('s.area_code', 'ASC')->get();
            $rows = [];
            foreach ($records as $rec) {
                $action = '';
                if (Auth::User()->manageEmployee()) {
                    $action = '<div>
                        <a class="action-icon" href="'.url('organization/sales-area/edit', $rec->area_code).'" title="แก้ไข"><i class="mdi mdi-square-edit-outline"></i></a>
                        <a class="action-icon" href="javascript:void(0);" onclick="deleteSalesAreaConfirmation(\''.$rec->area_code.'\')" title="ลบ"><i class="mdi mdi-delete"></i></a>
                    </div>';
                }
                $rows[] = array(
                    "area_code" => '<b>'.$rec->area_code.'</b>',
                    "dept_name" => $rec->dept_name,
                    "area_description" => $rec->area_description,
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
        $dept = Department::where('level', '=', 2)->where('dept_parent', '=', 'A02000000')->get();
        return view('organization.sales-area.create')->with('dept', $dept);
    }

    public function store(Request $request)
    {
        $area_code = strtoupper(trim($request->area_code));
        $area = SalesArea::where('area_code', '=', $area_code)->first();
        if ($area) {
            alert()->warning('รหัสพื้นที่การขายซ้ำ!');
            return back()->withInput();
        }

        $request->validate([
            'area_code' => 'required|min:4|max:4',
            'dept_id' => 'required',
        ],[
            'area_code.required' => 'กรุณากรอกรหัสพื้นที่การขาย',
            'area_code.min' => 'รหัสพื้นที่การขายต้องมี 4 หลัก',
            'area_code.max' => 'รหัสพื้นที่การขายต้องมี 4 หลัก',
            'dept_id.required' => 'กรุณาเลือกฝ่าย',
        ]);

        $area = new SalesArea();
        $area->area_code = $area_code;
        $area->dept_id = $request->dept_id;
        $area->area_description = $request->area_description;
        $area->user_manage = auth()->user()->emp_id;
        $area->ip_address = $request->ip();
        $area->save();

        alert()->success('เพิ่มพื้นที่การขายเรียบร้อย');
        return redirect('organization/sales-area');
    }

    public function edit($id)
    {
        $area = SalesArea::where('area_code', '=', $id)->first();
        $dept = Department::where('level', '=', 2)->where('dept_parent', '=', 'A02000000')->get();
        return view('organization.sales-area.edit', compact('area', 'dept'));
    }

    public function update(Request $request)
    {
        $area_code = strtoupper(trim($request->area_code));
        $request->validate([
            'area_code' => 'required|min:4|max:4',
            'dept_id' => 'required',
        ],[
            'area_code.required' => 'กรุณากรอกรหัสพื้นที่การขาย',
            'area_code.min' => 'รหัสพื้นที่การขายต้องมี 4 หลัก',
            'area_code.max' => 'รหัสพื้นที่การขายต้องมี 4 หลัก',
            'dept_id.required' => 'กรุณาเลือกฝ่าย',
        ]);

        $data = [
            'dept_id' => $request->dept_id,
            'area_description' => $request->area_description,
            'user_manage' => auth()->user()->emp_id,
            'ip_address' => $request->ip(),
            'updated_at' => now(),
        ];
        SalesArea::where('area_code', '=', $area_code)->update($data);

        alert()->success('อัปเดตพื้นที่การขายเรียบร้อย');
        return redirect('organization/sales-area');
    }

    public function destroy($id)
    {
        $area = SalesArea::where('area_code', '=', $id);
        $area->delete();
        sleep(1);
        return redirect('organization/sales-area');
    }
}