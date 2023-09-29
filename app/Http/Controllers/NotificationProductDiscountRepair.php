<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\RequestDiscountRepair as DiscountRepair;
use App\Models\Department;
use App\Models\RequestDiscountNoti as Noti;
use DB;

class NotificationProductDiscountRepair extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $totalRecords_mn = 0;
            $totalRecords_sec = 0;
            $totalRecords_personal = 0;
            $totalRecords_adminmanager = 0;
            $totalRecords_secaction = 0;
            $_dept = self::deptFormat(auth()->user()->dept_id);
            $admin = substr(auth()->user()->dept_id, 0, 5);
            if (Auth::user()->noti_CheckApproveMar()) {
                $data_mn = DiscountRepair::where('doc_status', '=', 2)
                    // ->where('dept_id', 'LIKE', $_dept . '%');
                    ->where(function ($query) use ($admin, $_dept) {
                        if ($admin == 'A0202' || $admin == 'A0201') {
                            $query->where('dept_id', 'LIKE', 'A0201' . '%');
                            $query->orWhere('dept_id', 'LIKE', 'A0202' . '%');
                        } else {
                            $query->where('dept_id', 'LIKE', $_dept . '%');
                        }
                    });
                $totalRecords_mn = $data_mn->select('count(*) as allcount')->count();
            }
            if (Auth::user()->noti_CheckSecretary()) {
                $data_sec = DiscountRepair::where('doc_status', '=', 1);
                $totalRecords_sec = $data_sec->select('count(*) as allcount')->count();
            }

            // manager and secretary action noti marketing
            $admin_manager = self::adminManager(auth()->user()->emp_id);
            if (Auth::User()->noti_CheckMar() && !is_int($admin_manager)) {
                $personal = DiscountRepair::from('request_discount_repair as r')
                    ->leftJoin('request_discount_noti as n', 'n.doc_id', '=', 'r.doc_id')
                    ->where('r.emp_id', '=', auth()->user()->emp_id)
                    ->where('r.doc_status', '<>', 2)
                    ->where('r.doc_status', '<>', 1)
                    ->where('n.personal_read', '=', null);
                // $sec_action = DiscountRepair::from('request_discount_repair as r')
                //     ->leftJoin('request_discount_noti as n', 'n.doc_id', '=', 'r.doc_id')
                //     ->where('r.emp_id', '=', auth()->user()->emp_id)
                //     ->where('n.secretary_action', '=', null)
                //     ->where(function ($query) {
                //         $query
                //             ->where('r.doc_status', '=', 9)
                //             ->orWhere('r.doc_status', '=', 0);
                //     });
                $totalRecords_personal = $personal->select('count(r.*) as allcount')->count();
                // $totalRecords_secaction = $sec_action->select('count(r.*) as allcount')->count();
            }

            // secretary action noti admin manager
            if (is_int($admin_manager)) {
                $action = DiscountRepair::from('request_discount_repair as r')
                    ->leftJoin('request_discount_noti as n', 'n.doc_id', '=', 'r.doc_id')
                    ->where('n.manager_read', '=', null)
                    ->where(function ($query) use ($_dept) {
                        if (strlen($_dept) == 4) {
                            $query
                                ->where('r.dept_id', '=', 'A0201')
                                ->orWhere('r.dept_id', '=', 'A0202');
                        }
                    })
                    ->where(function ($query) {
                        $query
                            ->where('r.doc_status', '=', '9')
                            ->orWhere('r.doc_status', '=', '0');
                    });
                $totalRecords_adminmanager = $action->select('count(r.*) as allcount')->count();
            }

            $response = array(
                "mn_noti" => $totalRecords_mn,
                "sec_noti" => $totalRecords_sec,
                "perso_noti" => $totalRecords_personal,
                "action_adminmanger" => $totalRecords_adminmanager,
                // "action_personal" => $totalRecords_secaction,
            );
            return response()->json($response);
        }
    }

    public function adminManager($emp)
    {
        // $admin = ['620274', '630040', '640172', '640195', '660039', '660100'];
        $manager = ['580073', '530107', '510186', '630324', '610284', '630056', '660182'];
        $admin_manager = array_search($emp, $manager);
        return $admin_manager;
    }

    public function deptFormat($dept)
    {
        $res = '';
        $_data = Department::where('dept_id', $dept)->first();
        if ($_data->level == 1) {
            $res = '';
        } elseif ($_data->level == 2) {
            $_res = substr($dept, 0, 5);
            if ($_res == 'A0204') {
                $res = substr($dept, 0, 5);
            } elseif ($_res == 'A0203') {
                $res = substr($dept, 0, 5);
            } elseif ($_res == 'A0205') {
                $res = substr($dept, 0, 5);
            } else {
                $res = substr($dept, 0, 4);
            }
        } else {
            $res = $dept;
        }
        return $res;
    }
}
