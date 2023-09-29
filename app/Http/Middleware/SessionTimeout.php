<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
use Auth;
use Session;

class SessionTimeout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    protected $session;
    protected $timeout = 7200;

    public function __construct(Store $session){
        $this->session = $session;
    }

    public function handle($request, Closure $next)
    {
        if(!$this->session->has('lastActivityTime')) {
            $this->session->put('lastActivityTime',time());
        } else if((time() - $this->session->get('lastActivityTime')) > $this->getTimeOut()){
            $this->session->forget('lastActivityTime');
            Auth::logout();
            Session::flush();
            alert()->warning('ไม่มีการเคลื่อนไหวเกินเวลาที่กำหนด', 'โปรด Login เพื่อเข้าใช้งานใหม่');
            return redirect('/login')->with('error', 'ไม่มีการเคลื่อนไหวเกินเวลาที่กำหนด โปรด Login เพื่อเข้าใช้งานใหม่');
        }
        $this->session->put('lastActivityTime',time());
        return $next($request);
    }

    protected function getTimeOut()
    {
        return (env('TIMEOUT')) ?: $this->timeout;
    }
}