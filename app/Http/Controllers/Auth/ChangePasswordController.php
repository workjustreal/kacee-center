<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
    public function showChangePassword(){
        return view('auth.profile');
    }

    public function showChangePasswordAccount(){
        return view('auth.change-password');
    }

    public function changePassword(Request $request){
        $user = Auth::user();

        if(!(Hash::check($request->post('current_password'), $user->password))){
            return back()->withErrors(['current_password'=>'รหัสผ่านปัจจุบันไม่ตรงกับรหัสผ่านที่มีอยู่']);
        }

        if(strcmp($request->post('current_password'), $request->post('new_password')) == 0){
            return back()->withErrors(['new_password'=>'รหัสผ่านปัจจุบันและรหัสผ่านใหม่ต้องไม่เหมือนกัน']);
        }

        $this->validate($request, [
            'current_password' => 'required',
            'new_password' => 'required|min:8|max:20|same:confirm_new_password',
            'confirm_new_password' => 'required',
        ],[
            'current_password.required' => 'ป้อนรหัสผ่านปัจจุบันด้วย',
            'new_password.min' => 'กำหนดรหัสผ่านใหม่อย่างน้อย 8 ตัวอักษร',
            'new_password.max' => 'กำหนดรหัสผ่านใหม่ไม่เกิน 20 ตัวอักษร',
            'new_password.same' => 'รหัสผ่านใหม่และรหัสผ่านยืนยันไม่ตรงกัน',
        ]);

        $user->password = bcrypt($request->post('new_password'));
        $user->save();

        return redirect()->back()->with('success', 'password_changed');
    }
}