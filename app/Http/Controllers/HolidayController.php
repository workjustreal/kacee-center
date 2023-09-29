<?php

namespace App\Http\Controllers;

use File;
use App\Models\Event;
use App\Models\User;
use SplFileInfo;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    protected $destinationPath;
    public function __construct()
    {
        $this->middleware('auth');
        $this->destinationPath = $_SERVER['DOCUMENT_ROOT'];
    }

    public function print()
    {
        $holiday = Event::leftjoin('users', 'users.id', 'events.userid')->where('events.holiday', '=', 1)->whereRaw('SUBSTRING(events.start, 1,  4) = '.date('Y'))
        ->select('events.*', 'users.name', 'users.surname')->orderBy("events.start", "ASC")->get();
        return view("holidays.print")->with('holiday', $holiday);
    }

    public function index()
    {
        return view('holidays.index');
    }

    public function search(Request $request)
    {
        if ($request->ajax()) {
            $data = Event::leftjoin('users', 'users.id', 'events.userid')->where('events.holiday', '=', 1)
            ->whereRaw('SUBSTRING(events.start, 1,  4) = '.$request->year);

            $totalRecords = $data->select('count(events.*) as allcount')->count();
            $records = $data->select('events.*', 'users.name', 'users.surname')->orderBy("events.start", "ASC")->get();
            $rows = [];
            $n = 1;
            foreach ($records as $rec) {
                if ($rec->status == "1") {
                    $status = '<span class="badge bg-success">แสดง</span>';
                } else {
                    $status = '<span class="badge bg-danger">ซ่อน</span>';
                }
                $action = '';
                if (Auth::User()->isAdmin() || Auth::User()->id == $rec->userid) {
                    $action = '<div>
                        <a class="action-icon" href="'.url('holidays/edit', $rec->id).'" title="แก้ไข"><i class="mdi mdi-square-edit-outline"></i></a>
                        <a class="action-icon" href="javascript:void(0);" onclick="deleteHolidayConfirmation(\''.$rec->id.'\')" title="ลบ"><i class="mdi mdi-delete"></i></a>
                    </div>';
                }
                $rows[] = array(
                    "no" => $n,
                    "title" => '<a href="'.url('holidays/show', $rec->id).'" type="button"><b>'.$rec->title.'</b></a>',
                    "start" => Carbon::parse($rec->start)->format('d/m/Y'),
                    "end" => Carbon::parse($rec->end)->format('d/m/Y'),
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

    public function create()
    {
        return view('holidays.create');
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

        if ($request->has('show')) {
            $status = 1;
        } else {
            $status = 0;
        }

        $user = auth()->user();

        $event = new Event();
        $event->title = $request->title;
        $event->description = $request->description;
        $event->start = $request->start;
        $event->end = $request->end;
        $event->type = 3; // HR
        $event->holiday = 1;
        $event->calendar = 1;
        $event->info = 0;
        $event->color = "#ff8929";
        $event->status = $status;
        $event->userid = $user->id;
        $event->userip = $request->ip();
        $event->save();

        alert()->success('บันทึกข้อมูลเรียบร้อย');
        return redirect('holidays');
    }

    public function show($id)
    {
        $event = Event::find($id);
        $user = User::leftjoin('department', 'department.dept_id', 'users.dept_id')->where('users.id', '=', $event->userid)
        ->select('users.*', 'department.dept_name')->first();
        return view('holidays.show', compact('event','user'));
    }

    public function edit($id)
    {
        $event = Event::find($id);
        return view('holidays.edit', compact('event'));
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

        if ($request->has('show')) {
            $status = 1;
        } else {
            $status = 0;
        }

        $event = Event::find($event_id);
        $event->title = $request->title;
        $event->description = $request->description;
        $event->start = $request->start;
        $event->end = $request->end;
        // $event->type = 3; // HR
        $event->holiday = 1;
        $event->calendar = 1;
        $event->info = 0;
        // $event->color = "#ff8929";
        $event->status = $status;
        $event->updated_at = now();
        $event->save();

        alert()->success('อัปเดตข้อมูลเรียบร้อย');
        return redirect('holidays');
    }

    public function destroy($id)
    {
        $event = Event::find($id);
        $event->delete();
        sleep(1);
        return redirect('holidays');
    }
}