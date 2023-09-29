<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Repair;
use App\Models\Department;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Jenssegers\Agent\Agent;


class RepairPrintController extends RepairController{

    public function detail_print_pdf($id)
    {
        $car = DB::table('car_main')->get();
        $repair_type = DB::table('repair_type')->get();
        $repairs = Repair::where('id', '=', $id)->first();
        $dept_parent = Department::where('dept_id', '=', $repairs->dept_id)->first();
        $depts = Department::where('dept_id', '=', $repairs->order_dept)->first();
        $tech_name =  json_decode($repairs->technician_name, true);
        $tech_detail =  json_decode($repairs->technician_detail, true);
        $ap_detail =  json_decode($repairs->approve_detail, true);
        $user_detail =  json_decode($repairs->user_comment, true);
        $status = self::get_leave_status($repairs->status);
        

        $dompdf = App::make('dompdf.wrapper');
        $dompdf->loadView('repair.detail_pdf', compact('repairs', 'repair_type', 'depts', 'dept_parent','tech_name', 'tech_detail', 'ap_detail', 'user_detail', 'car', 'status'))
            ->setPaper('a4', 'portrait')->setWarnings(false);
        $agent = new Agent();
        if ($agent->isPhone()) {
            return $dompdf->download($repairs->order_id.' '.date('Y-m-d').'.pdf');
        } else {
            return $dompdf->stream('ใบแจ้งซ่อม.pdf');
        }

    }

}
