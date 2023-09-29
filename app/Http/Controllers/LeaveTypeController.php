<?php

namespace App\Http\Controllers;

use App\Models\LeaveType;
use Illuminate\Http\Request;

class LeaveTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('leave.manage.leave-type');
    }

    public function search(Request $request)
    {
        if ($request->ajax()) {
            $data = LeaveType::where('leave_type_id', '<>', '');

            $totalRecords = $data->select('count(*) as allcount')->count();
            $records = $data->select('*')->orderBy("leave_type_id", "ASC")->get();
            $rows = [];
            foreach ($records as $rec) {
                if ($rec->leave_type_monthly == "1") {
                    $monthly = '<i class="fas fa-check text-success"></i>';
                } else {
                    $monthly = '<i class="fas fa-times text-danger"></i>';
                }
                if ($rec->leave_type_daily == "1") {
                    $daily = '<i class="fas fa-check text-success"></i>';
                } else {
                    $daily = '<i class="fas fa-times text-danger"></i>';
                }
                if ($rec->leave_type_status == "1") {
                    $status = '<span class="badge bg-success">ใช้งาน</span>';
                } else {
                    $status = '<span class="badge bg-secondary">ไม่ใช้งาน</span>';
                }
                // $action = '<div>
                //         <a class="action-icon" href="'.url('leave/manage/leave-type/edit', $rec->leave_type_id).'" title="แก้ไข"><i class="mdi mdi-square-edit-outline"></i></a>
                //         <a class="action-icon" href="javascript:void(0);" onclick="deleteLeaveTypeConfirmation(\''.$rec->leave_type_id.'\')" title="ลบ"><i class="mdi mdi-delete"></i></a>
                //     </div>';
                $action = '<div>
                        <a class="action-icon" href="'.url('leave/manage/leave-type/edit', $rec->leave_type_id).'" title="แก้ไข"><i class="mdi mdi-square-edit-outline"></i></a>
                    </div>';
                $rows[] = array(
                    "id" => '<b>#'.$rec->leave_type_id.'</b>',
                    "name" => $rec->leave_type_name,
                    "detail" => '<p class="text-truncate m-0" style="max-width: 250px;">'.$rec->leave_type_detail.'</p>',
                    "monthly" => $monthly,
                    "daily" => $daily,
                    "status" => $status,
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
        return view('leave.manage.leave-type-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'status' => 'required',
        ],[
            'name.required' => 'กรุณาระบุชื่อประเภทการลางาน',
            'status.required' => 'กรุณาเลือกสถานะการลางาน',
        ]);

        $monthly = ($request->has('monthly')) ? 1 : 0;
        $daily = ($request->has('daily')) ? 1 : 0;

        $leave_type = new LeaveType();
        $leave_type->leave_type_name = $request->name;
        $leave_type->leave_type_detail = $request->detail;
        $leave_type->leave_type_note = $request->note;
        $leave_type->leave_type_monthly = $monthly;
        $leave_type->leave_type_daily = $daily;
        $leave_type->leave_type_status = $request->status;
        $leave_type->save();

        alert()->success('เพิ่มประเภทการลางานเรียบร้อย');
        return redirect('leave/manage/leave-type');
    }

    public function edit($id)
    {
        $leave_type = LeaveType::where('leave_type_id', '=', $id)->first();
        return view('leave.manage.leave-type-edit', compact('leave_type'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'name' => 'required',
            'status' => 'required',
        ],[
            'id.required' => 'ไม่พบรหัสประเภทการลางาน',
            'name.required' => 'กรุณาระบุชื่อประเภทการลางาน',
            'status.required' => 'กรุณาเลือกสถานะการลางาน',
        ]);

        $monthly = ($request->has('monthly')) ? 1 : 0;
        $daily = ($request->has('daily')) ? 1 : 0;

        $leave_type = LeaveType::where('leave_type_id', '=', $request->id);
        $leave_type->update([
            "leave_type_name" => $request->name,
            "leave_type_detail" => $request->detail,
            "leave_type_note" => $request->note,
            "leave_type_monthly" => $monthly,
            "leave_type_daily" => $daily,
            "leave_type_status" => $request->status,
        ]);

        alert()->success('อัปเดตประเภทการลางานเรียบร้อย');
        return redirect('leave/manage/leave-type');
    }

    public function destroy($id)
    {
        $leave_type = LeaveType::where('leave_type_id', '=', $id);
        $leave_type->delete();
        return response()->json([
            'success' => true,
            'message' => 'ลบข้อมูลประเภทการลางานเรียบร้อย',
        ]);
    }
}