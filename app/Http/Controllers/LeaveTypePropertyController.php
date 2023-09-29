<?php

namespace App\Http\Controllers;

use App\Models\LeaveTypeProperty;
use Illuminate\Http\Request;

class LeaveTypePropertyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('leave.manage.leave-type-property');
    }

    public function search(Request $request)
    {
        if ($request->ajax()) {
            $data = LeaveTypeProperty::where('leave_type_ppt_id', '<>', '');

            $totalRecords = $data->select('count(*) as allcount')->count();
            $records = $data->select('*')->orderBy("leave_type_ppt_id", "ASC")->get();
            $rows = [];
            $n = 1;
            foreach ($records as $rec) {
                if ($rec->leave_type_ppt_monthly == "1") {
                    $monthly = '<i class="fas fa-check text-success"></i>';
                } else {
                    $monthly = '<i class="fas fa-times text-danger"></i>';
                }
                if ($rec->leave_type_ppt_daily == "1") {
                    $daily = '<i class="fas fa-check text-success"></i>';
                } else {
                    $daily = '<i class="fas fa-times text-danger"></i>';
                }
                if ($rec->leave_type_ppt_status == "1") {
                    $status = '<span class="badge bg-success">ใช้งาน</span>';
                } else {
                    $status = '<span class="badge bg-secondary">ไม่ใช้งาน</span>';
                }
                // $action = '<div>
                //         <a class="action-icon" href="'.url('leave/manage/leave-type-property/edit', $rec->leave_type_ppt_id).'" title="แก้ไข"><i class="mdi mdi-square-edit-outline"></i></a>
                //         <a class="action-icon" href="javascript:void(0);" onclick="deleteLeaveTypePropertyConfirmation(\''.$rec->leave_type_ppt_id.'\')" title="ลบ"><i class="mdi mdi-delete"></i></a>
                //     </div>';
                $action = '<div>
                        <a class="action-icon" href="'.url('leave/manage/leave-type-property/edit', $rec->leave_type_ppt_id).'" title="แก้ไข"><i class="mdi mdi-square-edit-outline"></i></a>
                    </div>';
                $rows[] = array(
                    "no" => $n,
                    "name" => $rec->leave_type_ppt_name,
                    "day" => $rec->leave_type_ppt_day.' วัน / ปี',
                    "monthly" => $monthly,
                    "daily" => $daily,
                    "status" => $status,
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
        return view('leave.manage.leave-type-property-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'day' => 'required',
            'status' => 'required',
        ],[
            'name.required' => 'กรุณาระบุชื่อวันหยุด',
            'day.required' => 'กรุณาระบุจำนวนวันหยุด',
            'status.required' => 'กรุณาเลือกสถานะ',
        ]);

        $monthly = ($request->has('monthly')) ? 1 : 0;
        $daily = ($request->has('daily')) ? 1 : 0;

        $leave_type_property = new LeaveTypeProperty();
        $leave_type_property->leave_type_ppt_name = $request->name;
        $leave_type_property->leave_type_ppt_day = $request->day;
        $leave_type_property->leave_type_ppt_monthly = $monthly;
        $leave_type_property->leave_type_ppt_daily = $daily;
        $leave_type_property->leave_type_ppt_status = $request->status;
        $leave_type_property->save();

        alert()->success('เพิ่มจำนวนวันหยุดเรียบร้อย');
        return redirect('leave/manage/leave-type-property');
    }

    public function edit($id)
    {
        $leave_type_property = LeaveTypeProperty::where('leave_type_ppt_id', '=', $id)->first();
        return view('leave.manage.leave-type-property-edit', compact('leave_type_property'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'name' => 'required',
            'day' => 'required',
            'status' => 'required',
        ],[
            'id.required' => 'ไม่พบรหัสวันหยุด',
            'name.required' => 'กรุณาระบุชื่อวันหยุด',
            'day.required' => 'กรุณาระบุจำนวนวันหยุด',
            'status.required' => 'กรุณาเลือกสถานะ',
        ]);

        $monthly = ($request->has('monthly')) ? 1 : 0;
        $daily = ($request->has('daily')) ? 1 : 0;

        $leave_type_property = LeaveTypeProperty::where('leave_type_ppt_id', '=', $request->id);
        $leave_type_property->update([
            "leave_type_ppt_name" => $request->name,
            "leave_type_ppt_day" => $request->day,
            "leave_type_ppt_monthly" => $monthly,
            "leave_type_ppt_daily" => $daily,
            "leave_type_ppt_status" => $request->status,
        ]);

        alert()->success('อัปเดตจำนวนวันหยุดเรียบร้อย');
        return redirect('leave/manage/leave-type-property');
    }

    public function destroy($id)
    {
        $leave_type_property = LeaveTypeProperty::where('leave_type_ppt_id', '=', $id);
        $leave_type_property->delete();
        return response()->json([
            'success' => true,
            'message' => 'ลบข้อมูลจำนวนวันหยุดเรียบร้อย',
        ]);
    }
}