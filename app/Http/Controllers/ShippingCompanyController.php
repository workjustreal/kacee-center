<?php

namespace App\Http\Controllers;

use App\Models\ShippingCompany;
use Illuminate\Http\Request;

class ShippingCompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $ship_com = ShippingCompany::orderBy('name', 'asc')->get();
        return view('checkout.shipping-company-list')->with('ship_com', $ship_com);
    }

    public function add()
    {
        return view('checkout.shipping-company-add');
    }

    public function edit($id)
    {
        $ship_com = ShippingCompany::find($id);
        return view('checkout.shipping-company-edit')->with('ship_com', $ship_com);
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'status' => 'required',
        ],[
            'name.required' => 'ระบุชื่อขนส่ง',
            'status.required' => 'ระบุสถานะ',
        ]);

        $ship_com = new ShippingCompany();
        $ship_com->name = $request->input('name');
        $ship_com->check = strtoupper($request->input('check'));
        $ship_com->status = $request->input('status');
        $ship_com->save();

        $request->flash();
        alert()->success('เพิ่มข้อมูลเรียบร้อย');
        return redirect('/checkout/ship-com-manage');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'status' => 'required',
        ],[
            'name.required' => 'ระบุชื่อขนส่ง',
            'status.required' => 'ระบุสถานะ',
        ]);

        $ship_com = ShippingCompany::find($id);
        $ship_com->name = $request->input('name');
        $ship_com->check = strtoupper($request->input('check'));
        $ship_com->status = $request->input('status');
        $ship_com->updated_at = now();
        $ship_com->save();

        $request->flash();
        alert()->success('อัปเดตข้อมูลเรียบร้อย');
        return redirect('/checkout/ship-com-manage');
    }
}