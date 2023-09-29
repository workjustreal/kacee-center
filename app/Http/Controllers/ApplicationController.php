<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $application = Application::orderBy('status', 'desc')->orderBy('id', 'asc')->get();
        return view('admin.application-list')->with('application', $application);
    }

    public function add()
    {
        return view('admin.application-add');
    }

    public function edit($id)
    {
        $application = Application::find($id);
        return view('admin.application-edit')->with('application', $application);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'status' => 'required',
            'detail' => 'required',
        ],[
            'name.required' => 'ระบุชื่อระบบงาน',
            'status.required' => 'ระบุสถานะ',
            'detail.required' => 'ระบุรายละเอียดเพิ่มเติม',
        ]);

        // if ($request->hasFile('image')) {
        //     $image = $request->file('image');
        //     $fileName = time();
        //     $input['imagename'] = $fileName . '.' . $image->extension();
        //     $destinationPath = public_path('assets/images/application/');
        //     $image->move($destinationPath, $input['imagename']);
        //     $imageName = $input['imagename'];
        // } else {
        //     $imageName = 'noimage.jpg';
        // }

        $application = new Application();
        $application->name = $request->input('name');
        $application->detail = $request->input('detail');
        $application->icon = $request->input('icon');
        $application->color = $request->input('color');
        // $application->image = $imageName;
        $application->categoryid = 1;
        $application->status = $request->input('status');
        $application->url = $request->input('url');
        $application->save();

        $request->flash();
        alert()->success('เพิ่มข้อมูลเรียบร้อย');
        return redirect('/admin/application');
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'icon' => 'required',
            'status' => 'required',
            'detail' => 'required',
        ],[
            'name.required' => 'ระบุชื่อระบบงาน',
            'icon.required' => 'เลือกไอคอนระบบงาน',
            'status.required' => 'ระบุสถานะ',
            'detail.required' => 'ระบุรายละเอียดเพิ่มเติม',
        ]);

        // if ($request->input('image_update') == 1) {
        //     if ($request->hasFile('image')) {
        //         $image = $request->file('image');
        //         $fileName = time();
        //         $input['imagename'] = $fileName . '.' . $image->extension();
        //         $destinationPath = public_path('assets/images/application/');
        //         $image->move($destinationPath, $input['imagename']);
        //         $imageName = $input['imagename'];
        //     } else {
        //         $imageName = 'noimage.jpg';
        //     }
        // } else {
        //     $imageName = $request->input('image_old');
        // }

        $application = Application::find($request->input('id'));
        $application->name = $request->input('name');
        $application->detail = $request->input('detail');
        $application->icon = $request->input('icon');
        $application->color = $request->input('color');
        // $application->image = $imageName;
        $application->categoryid = 1;
        $application->status = $request->input('status');
        $application->url = $request->input('url');
        $application->updated_at = now();
        $application->save();

        $request->flash();
        alert()->success('อัปเดตข้อมูลเรียบร้อย');
        return redirect('/admin/application');
    }

    public function destroy($id)
    {
        $application = Application::find($id);
        $application->delete();
        sleep(1);
        return redirect('/admin/application');
    }
}