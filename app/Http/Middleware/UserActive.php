<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\DB;
use Session;

class UserActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */

    protected $userActive = true;
    protected $timeactive = 0;

    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $this->timeactive = (time() - strtotime(Auth::user()->last_active));
            if ($this->timeactive > $this->getTimeOut()) {
                $this->userActive = false;
            }
            DB::table('users')->where('id', Auth::user()->id)->update(['last_active' => now()]);
        }
        if ($this->userActive === false) {
            Auth::logout();
            Session::flush();
            if ($this->timeactive < $this->getTimeAlert()) {
                alert()->warning('ไม่มีการเคลื่อนไหวเกินเวลาที่กำหนด', 'โปรด Login เพื่อเข้าใช้งานใหม่');
                return redirect('/login')->with('error', 'ไม่มีการเคลื่อนไหวเกินเวลาที่กำหนด โปรด Login เพื่อเข้าใช้งานใหม่');
            } else {
                return redirect('/login');
            }
        }
        return $next($request);
    }

    protected function getTimeOut()
    {
        return env('SESSION_LIFETIME', 120) * 60; // Seconds
    }

    protected function getTimeAlert()
    {
        return env('SESSION_LIFETIME', 120) * 60; // Seconds
    }
}