<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Notification;
use App\Models\RequestLabelHeader;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $application = Application::orderBy('id', 'ASC')->get();
        return view('admin.notifications.notification-list', compact('application'));
    }

    public function search(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('notification as a')->leftJoin('applications as app', 'a.app_id', '=', 'app.id')
            ->where('a.type', '<>', '00')
            ->where(function ($query) use ($request) {
                if ($request->app_id != "") {
                    $query->where('a.app_id', '=', $request->app_id);
                }
                if ($request->search != ""){
                    $query->where('a.app_name', 'LIKE', '%'.$request->search.'%');
                    $query->orWhere('a.title', 'LIKE', '%'.$request->search.'%');
                    $query->orWhere('a.job_id', 'LIKE', '%'.$request->search.'%');
                    $query->orWhere('a.from_uid', 'LIKE', '%'.$request->search.'%');
                    $query->orWhere('a.from_uname', 'LIKE', '%'.$request->search.'%');
                    $query->orWhere('a.to_uid', 'LIKE', '%'.$request->search.'%');
                    $query->orWhere('a.to_uname', 'LIKE', '%'.$request->search.'%');
                }
            });

            $totalRecords = $data->select('count(a.*) as allcount')->count();
            $records = $data->select('a.*', 'app.icon', 'app.color')->orderBy("a.id", "DESC")
            ->offset($request->offset)->limit($request->limit)->get();
            $rows = [];
            $n = 1;
            foreach ($records as $rec) {
                if ($rec->status == 1) {
                    $status = '<span class="badge badge-soft-danger">ใหม่</span>';
                } else if ($rec->status == 2) {
                    $status = '<span class="badge badge-soft-blue">อ่านแล้ว</span>';
                } else if ($rec->status == 3) {
                    $status = '<span class="badge badge-soft-success">เสร็จสิ้น</span>';
                } else {
                    $status = '';
                }
                $action = '<div>
                        <a class="action-icon" href="'.url('admin/notifications/edit', $rec->id).'" title="แก้ไข"><i class="mdi mdi-square-edit-outline"></i></a>
                        <a class="action-icon" href="javascript:void(0);" onclick="deleteNotificationConfirmation(\''.$rec->id.'\')" title="ลบ"><i class="mdi mdi-delete"></i></a>
                    </div>';

                $rows[] = array(
                    "no" => $n,
                    "app_name" => '<i class="'.$rec->icon.'" style="color:'.$rec->color.'"></i> '.$rec->app_name,
                    "description" => '<b class="text-primary">'.$rec->title.'</b><br><small>'.$rec->description.'</small>',
                    "from" => '<b class="text-primary">'.$rec->from_uid.'</b><br><small>'.$rec->from_uname.'</small>',
                    "to" => '<b class="text-primary">'.$rec->to_uid.'</b><br><small>'.$rec->to_uname.'</small>',
                    "job_id" => '<b class="text-primary">'.$rec->job_id.'</b>',
                    "type" => '<b class="text-primary">'.$rec->type.'</b>',
                    "status" => '<b class="text-primary">'.$status.'</b>',
                    "created_at" => Carbon::createFromFormat('Y-m-d H:i:s', $rec->created_at)->format('d/m/Y').'<br><small>'.Carbon::createFromFormat('Y-m-d H:i:s', $rec->created_at)->format('H:i:s').'</small>',
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
        $application = DB::table('applications')->orderBy('id', 'ASC')->get(['*']);
        return view('admin.notifications.notification-create', compact('application'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'app_id' => 'required',
        ],[
            'app_id.required' => 'กรุณาเลือกระบบงาน',
        ]);

        $app = Application::find($request->app_id);

        $notification = new Notification();
        $notification->app_id = $app->app_id;
        $notification->app_name = $app->name;
        $notification->title = $request->title;
        $notification->description = $request->description;
        $notification->url = $request->url;
        $notification->job_id = $request->job_id;
        $notification->from_uid = $request->from;
        $notification->from_uname = $request->from_name;
        $notification->to_uid = $request->to;
        $notification->to_uname = $request->to_name;
        $notification->type = $request->type;
        $notification->status = 1;
        $notification->save();

        alert()->success('เพิ่มการแจ้งเตือนเรียบร้อย');
        return redirect('admin/notifications');
    }

    public function edit($id)
    {
        $notification = Notification::find($id);
        if ($notification->app_id == 23) {
            $application = (object) ['id'=>23, 'name'=>'ร้องขอสติ๊กเกอร์บาร์โค้ด'];
        } else {
            $application = Application::find($notification->app_id);
        }

        return view('admin.notifications.notification-edit', compact('notification', 'application'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'app_id' => 'required',
            'id' => 'required',
        ],[
            'app_id.required' => 'กรุณาเลือกระบบงาน',
            'id.required' => 'ไม่พบข้อมูลการแจ้งเตือน',
        ]);

        $notification = Notification::find($request->id);
        $notification->update([
            "title" => $request->title,
            "description" => $request->description,
            "url" => $request->url,
            "job_id" => $request->job_id,
            "from_uid" => $request->from,
            "from_uname" => $request->from_name,
            "to_uid" => $request->to,
            "to_uname" => $request->to_name,
            "type" => $request->type,
            "status" => $request->status,
        ]);

        $label = RequestLabelHeader::where('request_id', '=', $request->job_id);
        $label->update([
            "status" => $request->status,
        ]);

        alert()->success('อัปเดตการแจ้งเตือนเรียบร้อย');
        return redirect('admin/notifications');
    }

    public function destroy($id)
    {
        $notification = Notification::find($id);
        $notification->delete();
        return response()->json([
            'success' => true,
            'message' => 'ลบข้อมูลการแจ้งเตือนเรียบร้อย',
        ]);
    }
}