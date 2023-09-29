<?php

namespace App\Http\Controllers;

use App\Models\Eplatform;
use Illuminate\Http\Request;

class EplatformController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $list = Eplatform::all();
        return view('eplatform.eplatform-list')->with('list', $list);
    }

    public function add()
    {
        return view('eplatform.eplatform-add');
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'app_key' => 'required',
            'app_secret' => 'required',
            'api_url' => 'required',
            'status' => 'required',
        ]);

        $eplatform = new Eplatform();
        $eplatform->name = $request->input('name');
        $eplatform->app_key = $request->input('app_key');
        $eplatform->app_secret = $request->input('app_secret');
        $eplatform->api_url = $request->input('api_url');
        $eplatform->status = (int)$request->input('status');
        $eplatform->save();

        alert()->success('Created.');
        return redirect('admin/eplatform/list');
    }

    public function edit($id)
    {
        $eplatform = Eplatform::find($id);
        return view('eplatform.eplatform-edit')->with('eplatform', $eplatform);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'app_key' => 'required',
            'app_secret' => 'required',
            'api_url' => 'required',
            'status' => 'required',
        ]);

        $eplatform = Eplatform::find($id);
        $eplatform->name = $request->input('name');
        $eplatform->app_key = $request->input('app_key');
        $eplatform->app_secret = $request->input('app_secret');
        $eplatform->api_url = $request->input('api_url');
        $eplatform->status = (int)$request->input('status');
        $eplatform->updated_at = now();
        $eplatform->update();

        alert()->success('Updated.');
        return redirect('admin/eplatform/list');
    }
}