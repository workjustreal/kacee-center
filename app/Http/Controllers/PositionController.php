<?php

namespace App\Http\Controllers;

use App\Exports\PositionExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PositionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $position = Position::all();
        return view('organization.position.index')->with('position', $position);
    }

    public function search(Request $request)
    {
        if ($request->ajax()) {
            $data = Position::where('position_id', '<>', '');
            $totalRecords = $data->select('count(*) as allcount')->count();
            $records = $data->select('*')->orderBy('position_id', 'ASC')->get();
            $rows = [];
            foreach ($records as $rec) {
                $action = '';
                if (Auth::User()->manageEmployee()) {
                    $action = '<div>
                        <a class="action-icon" href="'.url('organization/position/show', $rec->position_id).'" title="ดู"><i class="mdi mdi-eye"></i></a>
                        <a class="action-icon" href="'.url('organization/position/edit', $rec->position_id).'" title="แก้ไข"><i class="mdi mdi-square-edit-outline"></i></a>
                        <a class="action-icon" href="javascript:void(0);" onclick="deletePositionConfirmation(\''.$rec->position_id.'\')" title="ลบ"><i class="mdi mdi-delete"></i></a>
                    </div>';
                }
                $rows[] = array(
                    "position_id" => $rec->position_id,
                    "position_name" => $rec->position_name,
                    "position_name_en" => $rec->position_name_en,
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
        return view('organization.position.create');
    }

    public function store(Request $request)
    {
        $position_id = strtoupper(trim($request->position_id));
        $pos = Position::where('position_id', '=', $position_id)->first();
        if ($pos) {
            alert()->warning('รหัสตำแหน่งงานซ้ำ!');
            return back()->withInput();
        }
        if (!preg_match('/^[0-9 ]+$/', $position_id)) {
            alert()->warning('รหัสตำแหน่งงานต้องเป็นตัวเลข 3 หลัก!');
            return back()->withInput();
        }

        $request->validate([
            'position_id' => 'required',
            'position_name' => 'required',
        ],[
            'position_id.required' => 'กรุณากรอกรหัสตำแหน่งงาน',
            'position_name.required' => 'กรุณากรอกชื่อตำแหน่งงาน',
        ]);

        $position = new Position();
        $position->position_id = $position_id;
        $position->position_name = $request->position_name;
        $position->position_name_en = $request->position_name_en;
        $position->user_manage = auth()->user()->emp_id;
        $position->ip_address = $request->ip();
        $position->save();

        alert()->success('เพิ่มข้อมูลตำแหน่งงานเรียบร้อย');
        return redirect('organization/position');
    }

    public function show($id)
    {
        $position = Position::where('position_id', '=', $id)->first();
        return view('organization.position.show', compact('position'));
    }

    public function edit($id)
    {
        $position = Position::where('position_id', '=', $id)->first();
        return view('organization.position.edit', compact('position'));
    }

    public function update(Request $request)
    {
        $position_id = strtoupper(trim($request->position_id));
        if (!preg_match('/^[0-9 ]+$/', $position_id)) {
            alert()->warning('รหัสตำแหน่งงานต้องเป็นตัวเลข 3 หลัก!');
            return back()->withInput();
        }

        $request->validate([
            'position_id' => 'required',
            'position_name' => 'required',
        ],[
            'position_id.required' => 'กรุณากรอกรหัสตำแหน่งงาน',
            'position_name.required' => 'กรุณากรอกชื่อตำแหน่งงาน',
        ]);

        $data = [
            'position_name' => $request->position_name,
            'position_name_en' => $request->position_name_en,
            'user_manage' => auth()->user()->emp_id,
            'ip_address' => $request->ip(),
            'updated_at' => now(),
        ];
        Position::where('position_id', '=', $position_id)->update($data);

        alert()->success('อัปเดตข้อมูลตำแหน่งงานเรียบร้อย');
        return redirect('organization/position');
    }

    public function destroy($id)
    {
        $position = Position::where('position_id', '=', $id);
        $position->delete();
        sleep(1);
        return redirect('organization/position');
    }

    public function export()
    {
        $position = Position::orderBy('position_id', 'ASC')->get();

        return Excel::download(new PositionExport($position, "Sheet1"), 'ข้อมูลตำแหน่งงาน_'.now().'.xlsx');
    }
}