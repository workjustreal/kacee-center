<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Auth;
use Carbon\Carbon;
use Jenssegers\Agent\Agent;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $input = $request->all();

        $this->validate($request, [
            'email' => 'required',
            'password' => 'required',
        ]);
        if (Auth::attempt(array('emp_id' => $input['email'], 'password' => $input['password']), true)) {

            $agent = new Agent();
            $user = User::find(Auth::User()->id);
            $user->last_login_at = Carbon::now()->toDateTimeString();
            $user->last_login_ip = $request->ip();
            $user->last_login_client = $agent->getUserAgent();
            $user->last_active = Carbon::now()->toDateTimeString();
            $user->timestamps = false;
            $user->update();

            if (!Auth::user()->isAccountVerified()) {
                return redirect('/verify-account');
            }

            if (Auth::user()->isAppCenter()) {
                return redirect('/home');
            } else {
                Auth::logout();
                Session::flush();
                alert()->warning('คุณไม่มีสิทธิเข้าใช้งานระบบนี้');
                return redirect('/login');
            }
        } else {
            return redirect()->route('login')->with('error', 'รหัสพนักงาน หรือรหัสผ่านไม่ถูกต้อง');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        Session::flush();
        return redirect('/');
    }
}