<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\User;
use SplFileInfo;
use File;
use Auth;
use Carbon\Carbon;

class EventController extends Controller
{
    protected $destinationPath;
    public function __construct()
    {
        $this->middleware('auth');
        $this->destinationPath = $_SERVER['DOCUMENT_ROOT'];
    }

    public function index()
    {
        return view('events.index');
    }

    public function search(Request $request)
    {
        if ($request->ajax()) {
            $data = Event::leftjoin('users', 'users.id', 'events.userid')->where('events.holiday', '=', 0)
            ->whereRaw('SUBSTRING(events.start, 1,  4) = '.$request->year);

            $totalRecords = $data->select('count(events.*) as allcount')->count();
            $records = $data->select('events.*', 'users.name', 'users.surname')->orderBy("events.start", "DESC")->get();
            $rows = [];
            $n = 1;
            foreach ($records as $rec) {
                if ($rec->calendar == "1") {
                    $calendar = '<span class="badge bg-success">แสดง</span>';
                } else {
                    $calendar = '<span class="badge bg-danger">ซ่อน</span>';
                }
                if ($rec->info == "1") {
                    $info = '<span class="badge bg-success">แสดง</span>';
                } else {
                    $info = '<span class="badge bg-danger">ซ่อน</span>';
                }
                if ($rec->status == "1") {
                    $status = '<span class="badge bg-success">แสดง</span>';
                } else {
                    $status = '<span class="badge bg-danger">ซ่อน</span>';
                }
                $action = '';
                if (Auth::User()->isAdmin() || Auth::User()->id == $rec->userid) {
                    $action = '<div>
                        <a class="action-icon" href="'.url('events/edit', $rec->id).'" title="แก้ไข"><i class="mdi mdi-square-edit-outline"></i></a>
                        <a class="action-icon" href="javascript:void(0);" onclick="deleteEventConfirmation(\''.$rec->id.'\')" title="ลบ"><i class="mdi mdi-delete"></i></a>
                    </div>';
                }
                $rows[] = array(
                    "no" => $n,
                    "title" => '<a href="'.url('events/show', $rec->id).'" type="button"><b>'.$rec->title.'</b></a>',
                    "start" => Carbon::parse($rec->start)->format('d/m/Y'),
                    "end" => Carbon::parse($rec->end)->format('d/m/Y'),
                    "calendar" => $calendar,
                    "info" => $info,
                    "status" => $status,
                    "user" => $rec->name . ' ' . $rec->surname . '<br><small class="text-muted">อัปเดตล่าสุด '.Carbon::parse($rec->updated_at)->format('d/m/Y H:i:s').'</small>',
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

    public function file_upload(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time();
            $input['filename'] = $fileName . '.' . $file->extension();
            $destinationPath = $this->destinationPath . '/assets/uploads/';
            $file->move($destinationPath, $input['filename']);
            $fileName = $input['filename'];

            return response()->json(['status' => true, 'name' => $fileName]);
        }
    }

    public function create()
    {
        return view('events.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'start' => 'required',
            'end' => 'required'
        ]);

        $ev = Event::orderBy('id', 'DESC')->first();
        $event_id = ($ev) ? $ev->id+1 : 1;

        libxml_use_internal_errors(true) AND libxml_clear_errors();
        $dom = new \DomDocument('1.0', 'UTF-8');
        $dom->loadHTML(mb_convert_encoding($request->description, 'HTML-ENTITIES', 'UTF-8'));

        // libxml_use_internal_errors(true);
        // $dom = new \DomDocument();
        // $dom->loadHtml(mb_convert_encoding($request->description, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $filePath = '/assets/files/events/';
        $imagePath = '/assets/images/events/';

        $attach_file = $dom->getElementsByTagName('a');
        foreach ($attach_file as $key => $param) {
            if ($param->getAttribute('name') == "attach_file") {
                if (!File::exists($this->destinationPath . $filePath . $event_id)) {
                    File::makeDirectory($this->destinationPath . $filePath . $event_id);
                }
                $data = $param->getAttribute('href');
                list($name, $ext) = explode('.', $data);
                $info = new SplFileInfo($data);
                $attach_name = $filePath . $event_id . "/" . $name . $key . '.' . $info->getExtension();
                $path = $this->destinationPath . $attach_name;
                $link_url = url('') . $attach_name;
                if (File::exists($this->destinationPath . '/assets/uploads/' . $data)) {
                    File::move($this->destinationPath . '/assets/uploads/' . $data, $path);
                }
                $param->removeAttribute('href');
                $param->setAttribute('href', $attach_name);
            }
        }

        $image_file = $dom->getElementsByTagName('img');
        if (count($image_file) !== 0) {
            if (!File::exists($this->destinationPath . $imagePath . $event_id)) {
                File::makeDirectory($this->destinationPath . $imagePath . $event_id);
            }
        }

        foreach($image_file as $key => $image) {
            $data = $image->getAttribute('src');

            if (strpos($data, 'data:image')!==false){
                list($type, $data) = explode(';', $data);
                list(, $data) = explode(',', $data);
                list(, $ext) = explode('/', $type);

                $img_data = base64_decode($data);
                $image_name = $imagePath . $event_id . "/" . time() . $key . '.' . $ext;
                $path = $this->destinationPath . $image_name;
                $link_url = url('') . $image_name;
                file_put_contents($path, $img_data);

                $image->removeAttribute('src');
                $image->setAttribute('src', $image_name);
            }
        }

        $request->description = $dom->saveHTML($dom->documentElement) . PHP_EOL . PHP_EOL;

        $user = auth()->user();

        // type
        // 1 = แอดมิน
        // 2 = ผู้ใช้งาน
        // 3 = บุคคล
        // 4 = ขาย
        // 5 = เลขา
        // 6 = ไอที
        if (Auth::user()->roleAdmin()) {
            $color = "#28aa6d";
        } else if (Auth::user()->roleHR()) {
            $color = "#fc466b";
        } else if (Auth::user()->roleSales()) {
            $color = "#7040f7";
        } else if (Auth::user()->roleSecretary()) {
            $color = "#1259fa";
        } else if (Auth::user()->roleIT()) {
            $color = "#2196f3";
        }
        $type = $user->is_role;
        if ($request->has('incalendar')) {
            // แสดงใน Calendar
            $calendar = 1;
        } else {
            $calendar = 0;
        }

        if ($request->has('show')) {
            $status = 1;
        } else {
            $status = 0;
        }

        if ($request->has('info')) {
            $info = 1;
        } else {
            $info = 0;
        }

        $event = new Event();
        $event->title = $request->title;
        $event->description = $request->description;
        $event->start = $request->start;
        $event->end = $request->end;
        $event->type = $type;
        $event->holiday = 0;
        $event->calendar = $calendar;
        $event->info = $info;
        $event->color = $color;
        $event->status = $status;
        $event->userid = $user->id;
        $event->userip = $request->ip();
        $event->save();

        alert()->success('บันทึกข้อมูลเรียบร้อย');
        return redirect('events');
    }

    public function show($id)
    {
        $event = Event::find($id);
        $user = User::leftjoin('department', 'department.dept_id', 'users.dept_id')->where('users.id', '=', $event->userid)
        ->select('users.*', 'department.dept_name')->first();
        return view('events.show', compact('event','user'));
    }

    public function edit($id)
    {
        $event = Event::find($id);
        return view('events.edit', compact('event'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'start' => 'required',
            'end' => 'required'
        ]);

        $event_id = $request->id;

        libxml_use_internal_errors(true) AND libxml_clear_errors();
        $dom = new \DomDocument('1.0', 'UTF-8');
        $dom->loadHTML(mb_convert_encoding($request->description, 'HTML-ENTITIES', 'UTF-8'));

        // libxml_use_internal_errors(true);
        // $dom = new \DomDocument();
        // $dom->loadHtml(mb_convert_encoding($request->description, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        // dd($dom->saveHTML());

        $filePath = '/assets/files/events/';
        $imagePath = '/assets/images/events/';

        $attach_file = $dom->getElementsByTagName('a');
        if (count($attach_file) === 0) {
            File::deleteDirectory($this->destinationPath . $filePath . $event_id);
        }

        foreach ($attach_file as $key => $param) {
            if ($param->getAttribute('name') == "attach_file") {
                if (!File::exists($this->destinationPath . $filePath . $event_id)) {
                    File::makeDirectory($this->destinationPath . $filePath . $event_id);
                }
                $data = $param->getAttribute('href');
                $data = pathinfo($data);
                $data = $data['basename'];
                list($name, $ext) = explode('.', $data);
                $info = new SplFileInfo($data);
                if (!File::exists($this->destinationPath . $filePath . $event_id . '/' . $data)) {
                    $attach_name = $filePath . $event_id . "/" . $name . $key . '.' . $info->getExtension();
                    $path = $this->destinationPath . $attach_name;
                    $link_url = url('') . $attach_name;
                    if (File::exists($this->destinationPath . '/assets/uploads/' . $data)) {
                        File::move($this->destinationPath . '/assets/uploads/' . $data, $path);
                    }
                    $param->removeAttribute('href');
                    $param->setAttribute('href', $attach_name);
                }
            }
        }

        $image_file = $dom->getElementsByTagName('img');
        if (count($image_file) !== 0) {
            if (!File::exists($this->destinationPath . $imagePath . $event_id)) {
                File::makeDirectory($this->destinationPath . $imagePath . $event_id);
            }
        } else {
            File::deleteDirectory($this->destinationPath . $imagePath . $event_id);
        }

        foreach ($image_file as $key => $image) {
            $data = $image->getAttribute('src');

            if (strpos($data, 'data:image')!==false){
                list($type, $data) = explode(';', $data);
                list(, $data) = explode(',', $data);
                list(, $ext) = explode('/', $type);

                $img_data = base64_decode($data);
                $image_name = $imagePath . $event_id . "/" . time() . $key . '.' . $ext;
                $path = $this->destinationPath . $image_name;
                $link_url = url('') . $image_name;
                file_put_contents($path, $img_data);

                $image->removeAttribute('src');
                $image->setAttribute('src', $image_name);
            }
        }

        $request->description = $dom->saveHTML($dom->documentElement) . PHP_EOL . PHP_EOL;

        $user = auth()->user();
        // type
        // 1 = แอดมิน
        // 2 = ผู้ใช้งาน
        // 3 = บุคคล
        // 4 = ขาย
        // 5 = เลขา
        // 6 = ไอที
        if (Auth::user()->roleAdmin()) {
            $color = "#28aa6d";
        } else if (Auth::user()->roleHR()) {
            $color = "#fc466b";
        } else if (Auth::user()->roleSales()) {
            $color = "#7040f7";
        } else if (Auth::user()->roleSecretary()) {
            $color = "#1259fa";
        } else if (Auth::user()->roleIT()) {
            $color = "#2196f3";
        }
        $type = $user->is_role;
        if ($request->has('incalendar')) {
            $calendar = 1;
        } else {
            $calendar = 0;
        }
        if ($request->has('show')) {
            $status = 1;
        } else {
            $status = 0;
        }
        if ($request->has('info')) {
            $info = 1;
        } else {
            $info = 0;
        }

        $event = Event::find($event_id);
        $event->title = $request->title;
        $event->description = $request->description;
        $event->start = $request->start;
        $event->end = $request->end;
        // $event->type = $type;
        $event->holiday = 0;
        $event->calendar = $calendar;
        $event->info = $info;
        // $event->color = $color;
        $event->status = $status;
        $event->updated_at = now();
        $event->save();

        alert()->success('อัปเดตข้อมูลเรียบร้อย');
        return redirect('events');
    }

    public function destroy($id)
    {
        $event = Event::find($id);
        $event->delete();
        sleep(1);
        return redirect('events');
    }
}