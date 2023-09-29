<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // ############ for server ############
        // $this->app->bind('path.public', function() {
        //     return realpath(base_path('../public'));
        // });
        // ####################################
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('*', function($view)
        {
            if (Auth::check()) {
                $app_list = [];
                // ########################### STOCK #############################
                $app = DB::table('applications')->where('status', '=', 1)->where('id', '=', 1)->orderBy('id', 'ASC')->get();
                if ($app->isNotEmpty()) {
                    foreach ($app as $v) {
                        $link_url = (strpos($v->url, "http://") !== false || strpos($v->url, "https://") !== false) ? $v->url : url('') . $v->url;
                        $app_list[] = [
                            "name" => $v->name,
                            "url" => $link_url,
                            "icon" => $v->icon,
                            "color" => $v->color,
                        ];
                    }
                }
                // ########################################################
                $app = DB::table('applications')->where('status', '=', 1)->where('id', '>', 1)->orderBy('id', 'ASC')->get();
                if ($app->isNotEmpty()) {
                    foreach ($app as $v) {
                        if (Auth::user()->showAppTopbar($v->id)) {
                            $link_url = (strpos($v->url, "http://") !== false || strpos($v->url, "https://") !== false) ? $v->url : url('') . $v->url;
                            if (strpos($link_url, "{id}") !== false) {
                                $link_url = str_replace("{id}", Auth::user()->id, $link_url);
                            }
                            if (strpos($link_url, "{emp_id}") !== false) {
                                $link_url = str_replace("{emp_id}", Auth::user()->emp_id, $link_url);
                            }
                            $app_list[] = [
                                "name" => $v->name,
                                "url" => $link_url,
                                "icon" => $v->icon,
                                "color" => $v->color,
                            ];
                        }
                    }
                }
                // ########################### การลางาน #############################
                $leave_hr_noti = 0;
                $record_working_hr_noti = 0;
                if (Auth::user()->manageLeave() && !Auth::user()->roleAdmin()) {
                    // กรณีบุคคล ให้แจ้งเตือนที่เมนูด้านซ้าย
                    $leave_hr_noti = DB::table('leave')->where('leave_status', '=', 'A2')->orderBy('leave_id', 'DESC')->get(['leave_id'])->count();
                    $record_working_hr_noti = DB::table('record_working')->where('approve_status', '=', 'A2')->orderBy('id', 'DESC')->get(['leave_id'])->count();
                }
                // ########################################################
                $view->with('app_list', $app_list)->with('leave_hr_noti', $leave_hr_noti)->with('record_working_hr_noti', $record_working_hr_noti);
            }
        });
        Paginator::useBootstrap();
    }
}