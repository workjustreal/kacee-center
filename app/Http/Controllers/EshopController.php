<?php

namespace App\Http\Controllers;

use App\Models\Eplatform;
use App\Models\Eshop;
use Illuminate\Http\Request;

class EshopController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $list = Eshop::all();
        return view('eplatform.eshop-list')->with('list', $list);
    }

    public function add()
    {
        $eplatform = Eplatform::all();
        return view('eplatform.eshop-add')->with('eplatform', $eplatform);
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'seller_id' => 'required',
            'platform' => 'required',
            'api_version' => 'required',
            'status' => 'required',
        ]);

        $eplatform = Eplatform::find($request->input('platform'));

        $eshop = new Eshop();
        $eshop->name = $request->input('name');
        $eshop->seller_id = $request->input('seller_id');
        $eshop->platform_id = $eplatform->id;
        $eshop->platform_name = $eplatform->name;
        $eshop->api_version = (int)$request->input('api_version');
        $eshop->status = (int)$request->input('status');
        $eshop->save();

        alert()->success('Created.');
        return redirect('admin/eplatform/shop');
    }

    public function edit($id)
    {
        $eplatform = Eplatform::all();
        $eshop = Eshop::find($id);
        return view('eplatform.eshop-edit')->with('eshop', $eshop)->with('eplatform', $eplatform);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'seller_id' => 'required',
            'platform' => 'required',
            'api_version' => 'required',
            'status' => 'required',
        ]);

        $eplatform = Eplatform::find($request->input('platform'));

        $eshop = Eshop::find($id);
        $eshop->name = $request->input('name');
        $eshop->seller_id = $request->input('seller_id');
        $eshop->platform_id = $eplatform->id;
        $eshop->platform_name = $eplatform->name;
        $eshop->api_version = (int)$request->input('api_version');
        $eshop->status = (int)$request->input('status');
        $eshop->updated_at = now();
        $eshop->update();

        alert()->success('Updated.');
        return redirect('admin/eplatform/shop');
    }
}