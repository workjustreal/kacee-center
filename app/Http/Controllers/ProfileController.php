<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\User;
use Image;
use File;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function profile()
    {
        $user = auth()->user();
        $emp = Employee::leftjoin('department', 'employee.dept_id', 'department.dept_id')
                    ->leftjoin('position', 'employee.position_id', 'position.position_id')
                    ->leftjoin('branch', 'employee.branch_id', 'branch.branch_id')
                    ->where('employee.emp_id', '=', $user->emp_id)
                    ->select('employee.*', 'department.dept_name', 'position.position_name', 'branch.branch_name')
                    ->first();
        return view("auth.profile")->with('user', $user)->with('emp', $emp);
    }

    public function changeAvatar(Request $request)
    {
        $this->validate($request, [
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
        ]);

        $user = auth()->user();

        $image = $request->file('avatar');
        $input['imagename'] = $user->emp_id.'.'.$image->extension();

        $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/assets/images/users/thumbnail/';
        if ($image->extension() == "gif") {
            File::copy($image->path(), $destinationPath.$input['imagename']);
        } else {
            $img = Image::make($image->path());
            $img->resize(100, 100, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath.$input['imagename']);
        }

        $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/assets/images/users/';
        $image->move($destinationPath, $input['imagename']);

        User::where('emp_id', '=', $user->emp_id)->update(['image'=>$input['imagename'], 'updated_at'=>now()]);
        Employee::where('emp_id', '=', $user->emp_id)->update(['image'=>$input['imagename'], 'updated_at'=>now()]);

        alert()->success('อัปเดตรูปโปรไฟล์เรียบร้อย');
        return back();
    }

    public function removeAvatar(Request $request)
    {
        if ($request->emp_id != "") {
            $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/assets/images/users/';
            $destinationThumbnailPath = $_SERVER['DOCUMENT_ROOT'] . '/assets/images/users/thumbnail/';
            $user = User::where('emp_id', '=', $request->emp_id)->first();
            if ($user) {
                if ($user->image != "user-1.jpg") {
                    if( File::exists($destinationPath.$user->image) ) {
                        File::delete($destinationPath.$user->image);
                    }
                    if( File::exists($destinationThumbnailPath.$user->image) ) {
                        File::delete($destinationThumbnailPath.$user->image);
                    }
                    Employee::where('emp_id', '=', $request->emp_id)->update(['image'=>'user-1.jpg', 'updated_at'=>now()]);
                    User::where('emp_id', '=', $request->emp_id)->update(['image'=>'user-1.jpg', 'updated_at'=>now()]);
                }
            }
            return response()->json([
                'success' => true,
                'message' => 'ลบรูปโปรไฟล์เรียบร้อย',
            ]);
        }
    }

    public function updatePersonalData(Request $request)
    {
        if ($request->input("input-update-personal-data") == "1") {
            Employee::where('emp_id', '=', auth()->user()->emp_id)
            ->update([
                'nickname'=>$request->nickname,
                'tel'=>$request->tel,
                'tel2'=>$request->tel2,
                'phone'=>$request->phone,
                'phone2'=>$request->phone2,
                'email'=>$request->email,
                'detail'=>$request->detail,
                'current_address'=>$request->current_address,
                'current_subdistrict'=>$request->current_subdistrict,
                'current_district'=>$request->current_district,
                'current_province'=>$request->current_province,
                'current_country'=>'ไทย',
                'current_zipcode'=>$request->current_zipcode
            ]);
        }
        alert()->success('อัปเดตข้อมูลเรียบร้อย');
        return redirect('/profile');
    }
}