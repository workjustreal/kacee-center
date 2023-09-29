<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountVerificationController extends Controller
{
    public function verifyAccount()
    {
        return view('auth.verify-account');
    }

    public function accountVerified(Request $request)
    {
        $user = Auth::user();
        $this->validate($request, [
            'personal_id' => 'required',
            'new_password' => 'required|min:8|max:20|same:confirm_new_password',
            'confirm_new_password' => 'required',
        ],[
            'personal_id.required' => 'ป้อนเลขบัตรประจำตัวประชาชน',
            'new_password.min' => 'กำหนดรหัสผ่านใหม่อย่างน้อย 8 ตัวอักษร',
            'new_password.max' => 'กำหนดรหัสผ่านใหม่ไม่เกิน 20 ตัวอักษร',
            'new_password.same' => 'รหัสผ่านใหม่และรหัสผ่านยืนยันไม่ตรงกัน',
        ]);

        $emp = Employee::where('emp_id', '=', $user->emp_id)->where('personal_id', '=', $request->personal_id)->get();
        if ($emp->isEmpty()) {
            $request->flash();
            return redirect()->back()->with('error_pid', 'ข้อมูลเลขบัตรประจำตัวประชาชนไม่ถูกต้อง');
        }

        $user->is_flag = 1;
        $user->password = bcrypt($request->post('new_password'));
        $user->save();

        alert()->success('ยืนยันตัวตนสำเร็จ');
        return redirect('/home');
    }
}