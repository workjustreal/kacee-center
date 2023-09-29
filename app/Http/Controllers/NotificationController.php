<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Repair;
use App\Models\Eshop;
use App\Models\LazadaApi;
use App\Models\Notification;
use App\Models\ShopeeApi;
use App\Models\TiktokApi;
use App\Models\User;
use App\Models\RequestDecorate as Decorate;
use App\Models\RequestSpecialDiscount as Special;
use App\Models\RequestDiscountRepair as DiscountRepair;
use App\Models\RequestDecorateNoti as DeNoti;
use App\Models\RequestDiscountNoti as DisNoti;
use App\Models\RequestSpecialNoti as SpNoti;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function notifications(Request $request)
    {
        if ($request->ajax()) {
            $notification = [];
            $i = 0;

            if (Auth::User()->isAdmin()) {
                // <!-- ========== token expired ========== -->
                $day_second = 86400;
                $current_date = date('Y-m-d');
                $lazada_list = LazadaApi::all();
                if ($lazada_list) {
                    foreach ($lazada_list as $list) {
                        // Access Token Duration 30 days
                        $access_token_duration = ($list->expires_in / $day_second);
                        $updated_at = substr($list->updated_at, 0, 10);
                        $diff_date = (strtotime($current_date) - strtotime($updated_at));
                        $past_duration = ($diff_date / $day_second);
                        $balance_date = floor($access_token_duration - $past_duration);
                        if ($balance_date <= 5) {
                            $eshop = Eshop::where('seller_id', '=', $list->seller_id)->first();
                            $notification[$i]["bg"] = "active";
                            $notification[$i]["color"] = "bg-danger";
                            $notification[$i]["icon"] = "mdi-api";
                            $notification[$i]["app_id"] = 0;
                            $notification[$i]["type"] = "00";
                            $notification[$i]["status"] = 1;
                            $notification[$i]["title"] = "Lazada Access Token : " . $eshop->name;
                            $notification[$i]["body"] = "กำลังจะหมดอายุใน " . $balance_date . " วัน";
                            $notification[$i]["link"] = $request->getSchemeAndHttpHost() . "/admin/lazada/token";
                            $notification[$i]["close"] = '';
                            $notification[$i]["clear"] = '';
                            $i++;
                        }

                        // Refresh Token Duration 180 days
                        $refresh_token_duration = ($list->refresh_expires_in / $day_second);
                        $updated_at = substr($list->updated_at, 0, 10);
                        $diff_date = (strtotime($current_date) - strtotime($updated_at));
                        $past_duration = ($diff_date / $day_second);
                        $balance_date = floor($refresh_token_duration - $past_duration);
                        if ($balance_date <= 5) {
                            $eshop = Eshop::where('seller_id', '=', $list->seller_id)->first();
                            $notification[$i]["bg"] = "active";
                            $notification[$i]["color"] = "bg-danger";
                            $notification[$i]["icon"] = "mdi-api";
                            $notification[$i]["app_id"] = 0;
                            $notification[$i]["type"] = "00";
                            $notification[$i]["status"] = 1;
                            $notification[$i]["title"] = "Lazada Refresh Token : " . $eshop->name;
                            $notification[$i]["body"] = "กำลังจะหมดอายุใน " . $balance_date . " วัน";
                            $notification[$i]["link"] = $request->getSchemeAndHttpHost() . "/admin/lazada/token";
                            $notification[$i]["close"] = '';
                            $notification[$i]["clear"] = '';
                            $i++;
                        }
                    }
                }

                $shopee_list = ShopeeApi::all();
                if ($shopee_list) {
                    foreach ($shopee_list as $list) {
                        $updated_at = substr($list->updated_at, 0, 10);
                        $diff_date = (strtotime($current_date) - strtotime($updated_at));
                        $balance_date = (2592000 - $diff_date) / $day_second;
                        if ($balance_date <= 5) {
                            $eshop = Eshop::where('seller_id', '=', $list->seller_id)->first();
                            $notification[$i]["bg"] = "active";
                            $notification[$i]["color"] = "bg-danger";
                            $notification[$i]["icon"] = "mdi-api";
                            $notification[$i]["app_id"] = 0;
                            $notification[$i]["type"] = "00";
                            $notification[$i]["status"] = 1;
                            $notification[$i]["title"] = "Shopee Refresh Token : " . $eshop->name;
                            $notification[$i]["body"] = "กำลังจะหมดอายุใน " . $balance_date . " วัน";
                            $notification[$i]["link"] = $request->getSchemeAndHttpHost() . "/admin/shopee/token";
                            $notification[$i]["close"] = '';
                            $notification[$i]["clear"] = '';
                            $i++;
                        }
                    }
                }

                $tiktok_list = TiktokApi::all();
                if ($tiktok_list) {
                    foreach ($tiktok_list as $list) {
                        $dateS = date_create($current_date);
                        $dateE = date_create(date("Y-m-d H:i:s", $list->refresh_expires_in));
                        $diff = date_diff($dateS, $dateE);
                        $balance_date = (int)$diff->format("%R%a");
                        if ($balance_date <= 5) {
                            $eshop = Eshop::where('seller_id', '=', $list->seller_id)->first();
                            $notification[$i]["bg"] = "active";
                            $notification[$i]["color"] = "bg-danger";
                            $notification[$i]["icon"] = "mdi-api";
                            $notification[$i]["app_id"] = 0;
                            $notification[$i]["type"] = "00";
                            $notification[$i]["status"] = 1;
                            $notification[$i]["title"] = "TikTok Refresh Token : " . $eshop->name;
                            $notification[$i]["body"] = "กำลังจะหมดอายุใน " . $balance_date . " วัน";
                            $notification[$i]["link"] = $request->getSchemeAndHttpHost() . "/admin/tiktok/token";
                            $notification[$i]["close"] = '';
                            $notification[$i]["clear"] = '';
                            $i++;
                        }
                    }
                }
                // <!-- ========== end ========== -->
            }

            if (Auth::User()->roleAdmin()) {
                $notify = Notification::where('status', '<>', 3)->where(function ($q) {
                    $q->where('to_uid', '=', Auth::User()->emp_id)->orWhere('to_uid', '=', '100001');
                })->orderBy('type', 'asc')->orderBy('created_at', 'desc')->get();
            } else {
                $notify = Notification::where('status', '<>', 3)->where('to_uid', '=', Auth::User()->emp_id)->orderBy('type', 'asc')->orderBy('created_at', 'desc')->get();
            }
            if ($notify->isNotEmpty()) {
                foreach ($notify as $list) {
                    if ($list->status == 1) {
                        $notification[$i]["bg"] = "active";
                    } else if ($list->status == 2) {
                        $notification[$i]["bg"] = "";
                    }
                    // type
                    // 00 = ประกาศสำคัญ
                    // 01 = ทั่วไป
                    // 02 = ทั่วไป (ยกเลิก, ลบ)
                    // 03 = ทั่วไป (แยกย่อยจากระบบหลัก)
                    // 04 = ทั่วไป (แยกย่อยจากระบบหลัก) (ยกเลิก, ลบ)

                    // status
                    // 1 = แจ้งเตือนใหม่
                    // 2 = อัปเดตแจ้งเตือน
                    // 3 = ลบแจ้งเตือน
                    if ($list->type == "00") {
                        $notification[$i]["bg"] = "alert alert-warning rounded-0 border-0 mb-0";
                        $notification[$i]["color"] = "bg-danger";
                        $notification[$i]["icon"] = "mdi-message-alert-outline"; // ประกาศสำคัญ
                    } else {
                        $notification[$i]["color"] = "bg-blue";
                        $notification[$i]["icon"] = "mdi-bell-ring-outline";
                        if ($list->app_id == 2) { // ลางาน
                            if ($list->type == "01" || $list->type == "03") {
                                $notification[$i]["icon"] = "mdi-account-clock-outline";
                            } else if ($list->type == "02" || $list->type == "04") {
                                $notification[$i]["color"] = "bg-danger";
                                $notification[$i]["icon"] = "mdi-account-cancel-outline";
                            }
                        } else if ($list->app_id == 23) { // ร้องขอสติ๊กเกอร์
                            $notification[$i]["icon"] = "mdi-printer";
                        }
                    }
                    $notification[$i]["app_id"] = $list->app_id;
                    $notification[$i]["type"] = $list->type;
                    $notification[$i]["status"] = $list->status;
                    $notification[$i]["title"] = $list->title;
                    $notification[$i]["body"] = $list->description;
                    $notification[$i]["link"] = $request->getSchemeAndHttpHost() . $list->url;
                    $notification[$i]["close"] = '';
                    $notification[$i]["clear"] = '';
                    $i++;
                }
            }

            //========================================= Notification Product Discount Repair =====================================

            // notification manager productdiscount
            $admin = substr(auth()->user()->dept_id, 0, 5);
            $_dept = self::deptFormat(auth()->user()->dept_id);
            if (Auth::User()->noti_CheckApproveMar()) {
                $data_mn = DiscountRepair::where('doc_status', '=', 2)
                    // ->where('dept_id', 'LIKE', $_dept . '%')
                    ->where(function ($query) use ($admin, $_dept) {
                        if ($admin == 'A0202' || $admin == 'A0201') {
                            $query->where('dept_id', 'LIKE', 'A0201' . '%');
                            $query->orWhere('dept_id', 'LIKE', 'A0202' . '%');
                        } else {
                            $query->where('dept_id', 'LIKE', $_dept . '%');
                        }
                    })
                    ->get();
                if ($data_mn) {
                    foreach ($data_mn as $emp_id) {
                        $emp = Auth::User()->findEmployee($emp_id->emp_id);
                        $notification[$i]["bg"] = "active";
                        $notification[$i]["color"] = "bg-blue";
                        $notification[$i]["icon"] = "mdi-sale";
                        $notification[$i]["title"] = "ส่วนลดงานผิดพลาด";
                        $notification[$i]["body"] = 'รออนุมัติ <span class="text-blue">' . $emp_id->doc_id . '</span>';
                        $notification[$i]["app_id"] = 0;
                        $notification[$i]["type"] = "00";
                        $notification[$i]["status"] = 2;
                        $notification[$i]["link"] = $request->getSchemeAndHttpHost() . "/sales-document/discount-mistake/productdiscount-preview/view/" . $emp_id->doc_id . "/" . $emp_id->doc_status;
                        $notification[$i]["close"] = '';
                        $notification[$i]["clear"] = '';
                        $i++;
                    }
                }
            }

            // notification Secretary productdiscount
            if (Auth::User()->noti_CheckSecretary()) {
                $data_sec = DiscountRepair::where('doc_status', '=', 1)->get();
                if ($data_sec) {
                    foreach ($data_sec as $data) {
                        // $emp = Auth::User()->findEmployee($emp_id->emp_id);
                        $notification[$i]["bg"] = "active";
                        $notification[$i]["color"] = "bg-blue";
                        $notification[$i]["icon"] = "mdi-sale";
                        $notification[$i]["title"] = "ส่วนลดงานผิดพลาด";
                        $notification[$i]["body"] = 'รออนุมัติ <span class="text-blue">' . $data->doc_id . '</span>';
                        $notification[$i]["app_id"] = 0;
                        $notification[$i]["type"] = "00";
                        $notification[$i]["status"] = 2;
                        $notification[$i]["link"] = $request->getSchemeAndHttpHost() . "/sales-document/discount-mistake/productdiscount-preview/view/" . $data->doc_id . "/" . $data->doc_status;
                        $notification[$i]["close"] = '';
                        $notification[$i]["clear"] = '';
                        $i++;
                    }
                }
            }

            $admin_manager = self::adminManager(auth()->user()->emp_id);
            if (Auth::User()->noti_CheckMar()) {
                $personal = DiscountRepair::from('request_discount_repair as r')
                    ->leftJoin('request_discount_noti as n', 'n.doc_id', '=', 'r.doc_id')
                    ->where('r.emp_id', '=', auth()->user()->emp_id)
                    ->get();
                if ($personal && !is_int($admin_manager)) {
                    foreach ($personal as $perso) {
                        // $emp_mn = Auth::User()->findEmployee($perso->mn_approve);
                        // $emp_sec = Auth::User()->findEmployee($perso->sec_approve);
                        if ($perso->personal_read != 'read') {
                            if ($perso->doc_status == 3) {
                                $notification[$i]["bg"] = "active";
                                $notification[$i]["icon"] = "mdi-sale";
                                $notification[$i]["title"] = "ส่วนลดงานผิดพลาด";
                                $notification[$i]["body"] = 'เลขเอกสาร : ' . $perso->doc_id . '<br>สถานะ : ผจก.<span class="text-danger">(ไม่อนุมัติ)</span>';
                                $notification[$i]["color"] = "bg-danger";
                                $notification[$i]["app_id"] = 0;
                                $notification[$i]["type"] = "00";
                                $notification[$i]["status"] = 2;
                                $notification[$i]["link"] = $request->getSchemeAndHttpHost() . "/sales-document/discount-mistake/productdiscount-preview/personal_read/" . $perso->doc_id . "/" . $perso->doc_status;
                                $notification[$i]["close"] = '<span class="float-end noti-close-btn text-muted" data-bs-dismiss="alert" aria-label="Close"><i class="mdi mdi-close"></i></span>';
                                $notification[$i]["clear"] = $request->getSchemeAndHttpHost() . "/remove-noti/personal/discount/" . $perso->doc_id;
                                $i++;
                            } else if ($perso->doc_status == 9 || $perso->doc_status == 0) {
                                if ($perso->secretary_action != 'read') {
                                    if ($perso->doc_status == 9) {
                                        $body = 'เลขเอกสาร : ' . $perso->doc_id . '<br>สถานะ : <span class="text-success">(เสร็จสิ้น)</span>';
                                        $notification[$i]["color"] = "bg-success";
                                    } else if ($perso->doc_status == 0) {
                                        $body = 'เลขเอกสาร : ' . $perso->doc_id . '<br>สถานะ : เลขา.<span class="text-danger">(ไม่อนุมัติ)</span>';
                                        $notification[$i]["color"] = "bg-danger";
                                    }
                                    $notification[$i]["bg"] = "active";
                                    $notification[$i]["icon"] = "mdi-sale";
                                    $notification[$i]["title"] = "ส่วนลดงานผิดพลาด";
                                    $notification[$i]["body"] = $body;
                                    $notification[$i]["app_id"] = 0;
                                    $notification[$i]["type"] = "00";
                                    $notification[$i]["status"] = 2;
                                    $notification[$i]["link"] = $request->getSchemeAndHttpHost() . "/sales-document/discount-mistake/productdiscount-preview/personal_read/" . $perso->doc_id . "/" . $perso->doc_status;
                                    $notification[$i]["clear"] = $request->getSchemeAndHttpHost() . "/remove-noti/personal/discount/" . $perso->doc_id;
                                    $notification[$i]["close"] = '<span class="float-end noti-close-btn text-muted" data-bs-dismiss="alert" aria-label="Close"><i class="mdi mdi-close"></i></span>';
                                    $i++;
                                }
                            }
                        }
                    }
                }
            }

            if (is_int($admin_manager)) {
                $personal = DiscountRepair::from('request_discount_repair as r')
                    ->leftJoin('request_discount_noti as n', 'n.doc_id', '=', 'r.doc_id')
                    // ->where('r.emp_id', '=', auth()->user()->emp_id)
                    ->where(function ($query) use ($admin) {
                        if ($admin == 'A0202' || $admin == 'A0201') {
                            $query->where('dept_id', 'LIKE', 'A0201' . '%');
                            $query->orWhere('dept_id', 'LIKE', 'A0202' . '%');
                        } else {
                            $query->where('dept_id', 'LIKE', $admin . '%');
                        }
                    })
                    ->where('r.doc_status', '=', 9)
                    ->orWhere('r.doc_status', '=', 0)
                    ->get();
                if ($personal) {
                    foreach ($personal as $perso) {
                        if ($perso->manager_read != 'read') {
                            // $emp_mn = Auth::User()->findEmployee($perso->mn_approve);
                            // $emp_sec = Auth::User()->findEmployee($perso->sec_approve);
                            if ($perso->doc_status == 9) {
                                $body = 'เลขเอกสาร : ' . $perso->doc_id . '<br>สถานะ : <span class="text-success">(เสร็จสิ้น)</span>';
                                $notification[$i]["color"] = "bg-success";
                            } else {
                                $body = 'เลขเอกสาร : ' . $perso->doc_id . '<br>สถานะ : เลขา.<span class="text-danger">(ไม่อนุมัติ)</span>';
                                $notification[$i]["color"] = "bg-danger";
                            }
                            $notification[$i]["bg"] = "active";
                            $notification[$i]["icon"] = "mdi-sale";
                            $notification[$i]["title"] = "ส่วนลดงานผิดพลาด";
                            $notification[$i]["body"] = $body;
                            $notification[$i]["app_id"] = 0;
                            $notification[$i]["type"] = "00";
                            $notification[$i]["status"] = 2;
                            $notification[$i]["link"] = $request->getSchemeAndHttpHost() . "/sales-document/discount-mistake/productdiscount-preview/manager_read/" . $perso->doc_id . "/" . $perso->doc_status;
                            $notification[$i]["close"] = '<span class="float-end noti-close-btn text-muted" data-bs-dismiss="alert" aria-label="Close"><i class="mdi mdi-close"></i></span>';
                            $notification[$i]["clear"] = $request->getSchemeAndHttpHost() . "/remove-noti/manager/discount/" . $perso->doc_id;
                            $i++;
                        }
                    }
                }
            }

            //========================================= END Notification Product Discount Repair =====================================


            //========================================= Notification Product Decorate ===============================================

            // notification manager productdiscount
            if (Auth::User()->noti_CheckApproveMar()) {
                $_dept = self::deptFormat(auth()->user()->dept_id);
                $data_mn = Decorate::where('doc_status', '=', 2)
                    ->where(function ($query) use ($admin, $_dept) {
                        if ($admin == 'A0202' || $admin == 'A0201') {
                            $query->where('dept_id', 'LIKE', 'A0201' . '%');
                            $query->orWhere('dept_id', 'LIKE', 'A0202' . '%');
                        } else {
                            $query->where('dept_id', 'LIKE', $_dept . '%');
                        }
                    })
                    // ->where('dept_id', 'LIKE', $_dept . '%')
                    ->get();
                if ($data_mn) {
                    foreach ($data_mn as $emp_id) {
                        $emp = Auth::User()->findEmployee($emp_id->emp_id);
                        $notification[$i]["bg"] = "active";
                        $notification[$i]["color"] = "bg-blue";
                        $notification[$i]["icon"] = "mdi-storefront-outline";
                        $notification[$i]["title"] = "ขอสินค้าตกแต่งหน้าร้าน";
                        $notification[$i]["body"] = 'รออนุมัติ <span class="text-blue">' . $emp_id->doc_id . '</span>';
                        $notification[$i]["app_id"] = 0;
                        $notification[$i]["type"] = "00";
                        $notification[$i]["status"] = 2;
                        $notification[$i]["link"] = $request->getSchemeAndHttpHost() . "/sales-document/product-decorate/preview/view/" . $emp_id->doc_id . "/2";
                        $notification[$i]["close"] = '';
                        $notification[$i]["clear"] = '';
                        $i++;
                    }
                }
            }

            // notification Secretary productdecorate
            if (Auth::User()->noti_CheckHeadSecretary()) {
                $data_sec = Decorate::where('doc_status', '=', 1)->get();
                if ($data_sec) {
                    foreach ($data_sec as $data) {
                        // $emp = Auth::User()->findEmployee($emp_id->emp_id);
                        $notification[$i]["bg"] = "active";
                        $notification[$i]["color"] = "bg-blue";
                        $notification[$i]["icon"] = "mdi-storefront-outline";
                        $notification[$i]["title"] = "ขอสินค้าตกแต่งหน้าร้าน";
                        $notification[$i]["body"] = 'รออนุมัติ <span class="text-blue">' . $data->doc_id . '</span>';
                        $notification[$i]["app_id"] = 0;
                        $notification[$i]["type"] = "00";
                        $notification[$i]["status"] = 2;
                        $notification[$i]["link"] = $request->getSchemeAndHttpHost() . "/sales-document/product-decorate/preview/view/" . $data->doc_id . "/1";
                        $notification[$i]["close"] = '';
                        $notification[$i]["clear"] = '';
                        $i++;
                    }
                }
            }

            $admin_manager = self::adminManager(auth()->user()->emp_id);
            if (Auth::User()->noti_CheckMar()) {
                $personal = Decorate::from('request_decorate as r')
                    ->leftJoin('request_decorate_noti as n', 'n.doc_id', '=', 'r.doc_id')
                    ->where('r.emp_id', '=', auth()->user()->emp_id)
                    ->get();
                if ($personal && !is_int($admin_manager)) {
                    foreach ($personal as $perso) {
                        // $emp_mn = Auth::User()->findEmployee($perso->mn_approve);
                        // $emp_sec = Auth::User()->findEmployee($perso->sec_approve);
                        if ($perso->personal_read != 'read') {
                            if ($perso->doc_status == 3) {
                                $notification[$i]["bg"] = "active";
                                $notification[$i]["icon"] = "mdi-storefront-outline";
                                $notification[$i]["title"] = "ขอสินค้าตกแต่งหน้าร้าน";
                                $notification[$i]["body"] = 'เลขเอกสาร : ' . $perso->doc_id . '<br>สถานะ : ผจก.<span class="text-danger">(ไม่อนุมัติ)</span>';
                                $notification[$i]["color"] = "bg-danger";
                                $notification[$i]["app_id"] = 0;
                                $notification[$i]["type"] = "00";
                                $notification[$i]["status"] = 2;
                                $notification[$i]["link"] = $request->getSchemeAndHttpHost() . "/sales-document/product-decorate/preview/personal_read/" . $perso->doc_id . "/" . $perso->doc_status;
                                $notification[$i]["close"] = '<span class="float-end noti-close-btn text-muted" data-bs-dismiss="alert" aria-label="Close"><i class="mdi mdi-close"></i></span>';
                                $notification[$i]["clear"] = $request->getSchemeAndHttpHost() . "/remove-noti/personal/decorate/" . $perso->doc_id;
                                $i++;
                            } else if ($perso->doc_status == 9 || $perso->doc_status == 0) {
                                if ($perso->secretary_action != 'read') {
                                    if ($perso->doc_status == 9) {
                                        $body = 'เลขเอกสาร : ' . $perso->doc_id . '<br>สถานะ : <span class="text-success">(เสร็จสิ้น)</span>';
                                        $notification[$i]["color"] = "bg-success";
                                    } else if ($perso->doc_status == 0) {
                                        $body = 'เลขเอกสาร : ' . $perso->doc_id . '<br>สถานะ : เลขา.<span class="text-danger">(ไม่อนุมัติ)</span>';
                                        $notification[$i]["color"] = "bg-danger";
                                    }
                                    $notification[$i]["bg"] = "active";
                                    $notification[$i]["icon"] = "mdi-storefront-outline";
                                    $notification[$i]["title"] = "ขอสินค้าตกแต่งหน้าร้าน";
                                    $notification[$i]["body"] = $body;
                                    $notification[$i]["app_id"] = 0;
                                    $notification[$i]["type"] = "00";
                                    $notification[$i]["status"] = 2;
                                    $notification[$i]["link"] = $request->getSchemeAndHttpHost() . "/sales-document/product-decorate/preview/personal_read/" . $perso->doc_id . "/" . $perso->doc_status;
                                    $notification[$i]["close"] = '<span class="float-end noti-close-btn text-muted" data-bs-dismiss="alert" aria-label="Close"><i class="mdi mdi-close"></i></span>';
                                    $notification[$i]["clear"] = $request->getSchemeAndHttpHost() . "/remove-noti/personal/decorate/" . $perso->doc_id;
                                    $i++;
                                }
                            }
                        }
                    }
                }
            }

            if (is_int($admin_manager)) {
                $personal = Decorate::from('request_decorate as r')
                    ->leftJoin('request_decorate_noti as n', 'n.doc_id', '=', 'r.doc_id')
                    // ->where('r.emp_id', '=', auth()->user()->emp_id)
                    ->where(function ($query) use ($admin, $_dept) {
                        if ($admin == 'A0202' || $admin == 'A0201') {
                            $query->where('dept_id', 'LIKE', 'A0201' . '%');
                            $query->orWhere('dept_id', 'LIKE', 'A0202' . '%');
                        } else {
                            $query->where('dept_id', 'LIKE', $_dept . '%');
                        }
                    })
                    ->where('r.doc_status', '=', 9)
                    ->orWhere('r.doc_status', '=', 0)
                    ->get();
                if ($personal) {
                    foreach ($personal as $perso) {
                        if ($perso->manager_read != 'read') {
                            // $emp_mn = Auth::User()->findEmployee($perso->mn_approve);
                            // $emp_sec = Auth::User()->findEmployee($perso->sec_approve);
                            if ($perso->doc_status == 9) {
                                $body = 'เลขเอกสาร : ' . $perso->doc_id . '<br>สถานะ : <span class="text-success">(เสร็จสิ้น)</span>';
                                $notification[$i]["color"] = "bg-success";
                            } else {
                                $body = 'เลขเอกสาร : ' . $perso->doc_id . '<br>สถานะ : เลขา.<span class="text-danger">(ไม่อนุมัติ)</span>';
                                $notification[$i]["color"] = "bg-danger";
                            }
                            $notification[$i]["bg"] = "active";
                            $notification[$i]["icon"] = "mdi-storefront-outline";
                            $notification[$i]["title"] = "ขอสินค้าตกแต่งหน้าร้าน";
                            $notification[$i]["body"] = $body;
                            $notification[$i]["app_id"] = 0;
                            $notification[$i]["type"] = "00";
                            $notification[$i]["status"] = 2;
                            $notification[$i]["link"] = $request->getSchemeAndHttpHost() . "/sales-document/product-decorate/preview/manager_read/" . $perso->doc_id . "/" . $perso->doc_status;
                            $notification[$i]["close"] = '<span class="float-end noti-close-btn text-muted" data-bs-dismiss="alert" aria-label="Close"><i class="mdi mdi-close"></i></span>';
                            $notification[$i]["clear"] = $request->getSchemeAndHttpHost() . "/remove-noti/manager/decorate/" . $perso->doc_id;
                            $i++;
                        }
                    }
                }
            }

            //========================================= END Notification Product Decorate ===============================================


            //========================================= Notification Special Discount ===============================================

            // notification manager productdiscount
            if (Auth::User()->noti_CheckApproveMar()) {
                $_dept = self::deptFormat(auth()->user()->dept_id);
                $data_mn = Special::where('doc_status', '=', 2)
                    ->where(function ($query) use ($admin, $_dept) {
                        if ($admin == 'A0202' || $admin == 'A0201') {
                            $query->where('dept_id', 'LIKE', 'A0201' . '%');
                            $query->orWhere('dept_id', 'LIKE', 'A0202' . '%');
                        } else {
                            $query->where('dept_id', 'LIKE', $_dept . '%');
                        }
                    })
                    // ->where('dept_id', 'LIKE', $_dept . '%')
                    ->get();
                if ($data_mn) {
                    foreach ($data_mn as $emp_id) {
                        $emp = Auth::User()->findEmployee($emp_id->emp_id);
                        $notification[$i]["bg"] = "active";
                        $notification[$i]["color"] = "bg-blue";
                        $notification[$i]["icon"] = "mdi-star-half-full";
                        $notification[$i]["title"] = "ส่วนลดงานล็อตใหญ่";
                        $notification[$i]["body"] = 'รออนุมัติ <span class="text-blue">' . $emp_id->doc_id . '</span>';
                        $notification[$i]["app_id"] = 0;
                        $notification[$i]["type"] = "00";
                        $notification[$i]["status"] = 2;
                        $notification[$i]["link"] = $request->getSchemeAndHttpHost() . "/sales-document/special-discount/preview/view/" . $emp_id->doc_id . "/2";
                        $notification[$i]["close"] = '';
                        $i++;
                    }
                }
            }

            // notification Secretary productdecorate
            if (Auth::User()->noti_CheckSecretary()) {
                $data_sec = Special::where('doc_status', '=', 1)->get();
                if ($data_sec) {
                    foreach ($data_sec as $data) {
                        // $emp = Auth::User()->findEmployee($emp_id->emp_id);
                        $notification[$i]["bg"] = "active";
                        $notification[$i]["color"] = "bg-blue";
                        $notification[$i]["icon"] = "mdi-star-half-full";
                        $notification[$i]["title"] = "ส่วนลดงานล็อตใหญ่";
                        $notification[$i]["body"] = 'รออนุมัติ <span class="text-blue">' . $data->doc_id . '</span>';
                        $notification[$i]["app_id"] = 0;
                        $notification[$i]["type"] = "00";
                        $notification[$i]["status"] = 2;
                        $notification[$i]["link"] = $request->getSchemeAndHttpHost() . "/sales-document/special-discount/preview/view/" . $data->doc_id . "/1";
                        $notification[$i]["close"] = '';
                        $notification[$i]["clear"] = '';
                        $i++;
                    }
                }
            }

            $admin_manager = self::adminManager(auth()->user()->emp_id);
            if (Auth::User()->noti_CheckMar()) {
                $personal = Special::from('request_special_discount as r')
                    ->leftJoin('request_special_noti as n', 'n.doc_id', '=', 'r.doc_id')
                    ->where('r.emp_id', '=', auth()->user()->emp_id)
                    ->get();
                if ($personal && !is_int($admin_manager)) {
                    foreach ($personal as $perso) {
                        // $emp_mn = Auth::User()->findEmployee($perso->mn_approve);
                        // $emp_sec = Auth::User()->findEmployee($perso->sec_approve);
                        if ($perso->personal_read != 'read') {
                            if ($perso->doc_status == 3) {
                                $notification[$i]["bg"] = "active";
                                $notification[$i]["icon"] = "mdi-star-half-full";
                                $notification[$i]["title"] = "ส่วนลดงานล็อตใหญ่";
                                $notification[$i]["body"] = 'เลขเอกสาร : ' . $perso->doc_id . '<br>สถานะ : ผจก.<span class="text-danger">(ไม่อนุมัติ)</span>';
                                $notification[$i]["color"] = "bg-danger";
                                $notification[$i]["app_id"] = 0;
                                $notification[$i]["type"] = "00";
                                $notification[$i]["status"] = 2;
                                $notification[$i]["link"] = $request->getSchemeAndHttpHost() . "/sales-document/special-discount/preview/personal_read/" . $perso->doc_id . "/" . $perso->doc_status;
                                $notification[$i]["clear"] = $request->getSchemeAndHttpHost() . "/remove-noti/personal/special/" . $perso->doc_id;
                                $notification[$i]["close"] = '<span class="float-end noti-close-btn text-muted" data-bs-dismiss="alert" aria-label="Close"><i class="mdi mdi-close"></i></span>';
                                $i++;
                            } else if ($perso->doc_status == 9 || $perso->doc_status == 0) {
                                if ($perso->secretary_action != 'read') {
                                    if ($perso->doc_status == 9) {
                                        $body = 'เลขเอกสาร : ' . $perso->doc_id . '<br>สถานะ : <span class="text-success">(เสร็จสิ้น)</span>';
                                        $notification[$i]["color"] = "bg-success";
                                    } else if ($perso->doc_status == 0) {
                                        $body = 'เลขเอกสาร : ' . $perso->doc_id . '<br>สถานะ : เลขา.<span class="text-danger">(ไม่อนุมัติ)</span>';
                                        $notification[$i]["color"] = "bg-danger";
                                    }
                                    $notification[$i]["bg"] = "active";
                                    $notification[$i]["icon"] = "mdi-star-half-full";
                                    $notification[$i]["title"] = "ส่วนลดงานล็อตใหญ่";
                                    $notification[$i]["body"] = $body;
                                    $notification[$i]["app_id"] = 0;
                                    $notification[$i]["type"] = "00";
                                    $notification[$i]["status"] = 2;
                                    $notification[$i]["link"] = $request->getSchemeAndHttpHost() . "/sales-document/special-discount/preview/personal_read/" . $perso->doc_id . "/" . $perso->doc_status;
                                    $notification[$i]["close"] = '<span class="float-end noti-close-btn text-muted" data-bs-dismiss="alert" aria-label="Close"><i class="mdi mdi-close"></i></span>';
                                    $notification[$i]["clear"] = $request->getSchemeAndHttpHost() . "/remove-noti/personal/special/" . $perso->doc_id;
                                    $i++;
                                }
                            }
                        }
                    }
                }
            }

            if (is_int($admin_manager)) {
                $personal = Special::from('request_special_discount as r')
                    ->leftJoin('request_special_noti as n', 'n.doc_id', '=', 'r.doc_id')
                    // ->where('r.emp_id', '=', auth()->user()->emp_id)
                    ->where(function ($query) use ($admin, $_dept) {
                        if ($admin == 'A0202' || $admin == 'A0201') {
                            $query->where('r.dept_id', 'LIKE', '%' . 'A0201' . '%');
                            $query->orWhere('r.dept_id', 'LIKE', '%' . 'A0202' . '%');
                        } else {
                            $query->where('r.dept_id', 'LIKE', '%' . $_dept . '%');
                        }
                    })
                    ->where('r.doc_status', '=', 9)
                    ->orWhere('r.doc_status', '=', 0)
                    ->get();
                if ($personal) {
                    foreach ($personal as $perso) {
                        if ($perso->manager_read != 'read') {
                            // $emp_mn = Auth::User()->findEmployee($perso->mn_approve);
                            // $emp_sec = Auth::User()->findEmployee($perso->sec_approve);
                            if ($perso->doc_status == 9) {
                                $body = 'เลขเอกสาร : ' . $perso->doc_id . '<br>สถานะ : <span class="text-success">(เสร็จสิ้น)</span>';
                                $notification[$i]["color"] = "bg-success";
                            } else {
                                $body = 'เลขเอกสาร : ' . $perso->doc_id . '<br>สถานะ : เลขา.<span class="text-danger">(ไม่อนุมัติ)</span>';
                                $notification[$i]["color"] = "bg-danger";
                            }
                            $notification[$i]["bg"] = "active";
                            $notification[$i]["icon"] = "mdi-star-half-full";
                            $notification[$i]["title"] = "ส่วนลดงานล็อตใหญ่";
                            $notification[$i]["body"] = $body;
                            $notification[$i]["app_id"] = 0;
                            $notification[$i]["type"] = "00";
                            $notification[$i]["status"] = 2;
                            $notification[$i]["link"] = $request->getSchemeAndHttpHost() . "/sales-document/special-discount/preview/manager_read/" . $perso->doc_id . "/" . $perso->doc_status;
                            // $notification[$i]["close"] = '<span xx class="float-end noti-close-btn text-muted" data-bs-dismiss="alert" aria-label="Close"><i class="mdi mdi-close"></i></span>';
                            $notification[$i]["close"] = '<span class="float-end noti-close-btn text-muted" data-bs-dismiss="alert" aria-label="Close"><i class="mdi mdi-close"></i></span>';
                            $notification[$i]["clear"] = $request->getSchemeAndHttpHost() . "/remove-noti/manager/special/" . $perso->doc_id;
                            $i++;
                        }
                    }
                }
            }

            //========================================= END Notification Special Discount ===============================================



            // *************************** Alert Repair *****************************
            $i2 = 0;
            $_status = "";
            $link_status = "";
            $_notification = [];
            if (auth()->user()->emp_id != "500383" && !Auth::User()->roleAdmin()) {
                $_repair = self::repair_topbar();
                foreach ($_repair as $repair) {
                    if (count($repair)) {
                        foreach ($repair as $row) {
                            $user = Auth::User()->findEmployee($row->user_id);
                            switch ($row->status) {
                                case 'รออนุมัติ':
                                    $link_status = "/repair/approve";
                                    $_status = $row->status;
                                    break;
                                case 'หัวหน้าอนุมัติ':
                                    $link_status = "/repair/action";
                                    $_status = "รอรับงาน";
                                    break;
                                case 'ดำเนินการ':
                                    $link_status = "/repair/action";
                                    $_status = $row->status;
                                    break;
                                case 'รอตรวจสอบ':
                                    $link_status = "/repair/action";
                                    $_status = $row->status;
                                    break;
                                case 'ผ่านการตรวจสอบ':
                                    if (Auth::User()->isManagerHelper()) {
                                        $link_status = "/repair/approve";
                                    } else {
                                        $link_status = "/repair/repair";
                                    }
                                    $_status = $row->status;
                                    break;
                                default:
                                    $link_status = "/repair/repair";
                                    $_status = $row->status;
                                    break;
                            }
                            $_notification[$i2]["color"] = "bg-blue";
                            $_notification[$i2]["icon"] = "mdi-alert-octagon-outline";
                            $_notification[$i2]["app_id"] = 0;
                            $_notification[$i2]["type"] = "00";
                            $_notification[$i2]["status"] = 2;
                            $_notification[$i2]["title"] = "ใบแจ้งซ่อม " . $row->order_id;
                            $_notification[$i2]["body"] = "มีสถานะ : " . $_status;
                            $_notification[$i2]["link"] = $request->getSchemeAndHttpHost() . $link_status;
                            $_notification[$i2]["close"] = '';
                            $_notification[$i2]["clear"] = '';
                            $i2++;
                        }
                    }
                }
                $__notification = array_unique(array_column($_notification, 'title'));
                $_notification = array_intersect_key($_notification, $__notification);
                $notification = array_merge($notification, $_notification);
                $i += count($notification);
            }

            // *************************** END Alert Repair *****************************

            return response()->json(['notification' => $notification]);
        }
    }

    public function leftmenu_notifications()
    {
        // ##################### แจ้งเตือนเมนูด้านซ้าย ####################
        $leave_noti = 0;
        $leave_dashboard_noti = 0;
        $leave_approve_noti = 0;
        $notify = Notification::where('status', '<>', 3)->where('to_uid', '=', Auth::User()->emp_id)->orderBy('created_at', 'desc')->get();
        if ($notify->isNotEmpty()) {
            foreach ($notify as $list) {
                // ######### ลางาน #########
                if ($list->app_id == 2) {
                    $leave_noti++;
                    if ($list->type == '02' || $list->type == '04') {
                        $leave_dashboard_noti++;
                    }
                    if ($list->type == '01' || $list->type == '03') {
                        $leave_approve_noti++;
                    }
                }
                // #########################
            }
        }

        // ######### ลางานกรณีบุคคล #########
        $leave_approve_hr_noti = 0;
        $leave_approve_hr_noti1 = 0;
        $leave_approve_hr_noti2 = 0;
        if (Auth::user()->manageLeave() && !Auth::user()->roleAdmin()) {
            $leave = DB::table('leave')->where('leave_status', '=', 'A2')->orderBy('leave_id', 'DESC')->take(5)->get();
            $record_working = DB::table('record_working')->where('approve_status', '=', 'A2')->orderBy('id', 'DESC')->take(5)->get();
            $leave_approve_hr_noti1 = $leave->count();
            $leave_approve_hr_noti2 = $record_working->count();
            $leave_approve_hr_noti = ($leave_approve_hr_noti1 + $leave_approve_hr_noti2);
        }
        // #########################

        // ######### แจ้งซ่อม #########

        $data_repair = self::repair_leftmenu();

        // ########## sales-document ##########

        $sd_noti = 0;
        $user = auth()->user();
        if ($user->dept_id == "A03000000" || $user->dept_id == 'A03040000' || ($user->dept_id == 'A03040200' && Auth::User()->isLeader())) {
            $sd_noti = DB::table('sales_form')->where('status', 'รออนุมัติ')->count();
        }


        // #########################

        $data = [
            'leave' => [
                'leave_noti' => $leave_noti,
                'leave_dashboard_noti' => $leave_dashboard_noti,
                'leave_approve_noti' => $leave_approve_noti,
                'leave_approve_hr_noti' => $leave_approve_hr_noti,
                'leave_approve_hr_noti1' => $leave_approve_hr_noti1,
                'leave_approve_hr_noti2' => $leave_approve_hr_noti2,
            ],
            'repair' => [
                'repair_all_noti' => $data_repair['repair_all_noti'],
                'repair_success_noti' => $data_repair['repair_success_noti'],
                'repair_wait_noti' => $data_repair['repair_wait_noti'],
                'repair_action_noti' => $data_repair['repair_action_noti'],
            ],
            'sales_document' => [
                'sd_noti' => $sd_noti
            ]
        ];

        return response()->json(['success' => true, 'data' => $data], 200);
        // ########################################################
    }

    public function repair_leftmenu()
    {
        // ######### แจ้งซ่อม #########
        $status_wait = 0;
        $status_appove = 0;
        $status_check = 0;
        $status_success = 0;
        $status_action = 0;
        $user = auth()->user();

        $deptDatas = Department::where('dept_id', '=', $user->dept_id)->first();;
        if ($deptDatas->level == 1) {
            $order_dept = substr($user->dept_id, 0, 3);
        } elseif ($deptDatas->level == 2) {
            $order_dept = substr($user->dept_id, 0, 5);
        } else {
            $order_dept = $user->dept_id;
        }

        $users = RepairController::getAuthorizeUserLevel2();
        if (Auth::User()->isManager()) {
            $status_wait = Repair::whereIn('status', ['รออนุมัติ', 'ผ่านการตรวจสอบ'])
                ->whereIn('user_id', $users)
                ->count();
        } elseif (Auth::User()->isManagerHelper()) {
            $status_wait = Repair::whereIn('status', ['รออนุมัติ', 'ผ่านการตรวจสอบ'])
                ->where('dept_id', 'LIKE', $order_dept . '%')
                ->count();
        }
        if (Auth::User()->isLeader() && Auth::User()->manageMaintenance()) {
            $status_appove = Repair::where('status', '=', 'หัวหน้าอนุมัติ')
                ->where('order_dept', 'LIKE', $order_dept . '%')
                ->count();
            $status_check = Repair::where('status', '=', 'รอตรวจสอบ')
                ->where('order_dept', 'LIKE', $order_dept . '%')
                ->count();
        }
        $status_success = Repair::where('status', '=', 'ผ่านการตรวจสอบ')
            ->where('user_id', '=', $user->emp_id)
            ->count();

        if (Auth::User()->isLeader() && Auth::User()->manageMaintenance()) {
            $status_action = Repair::where('status', '=', 'ดำเนินการ')
                ->where('order_dept', 'LIKE', $order_dept . '%')
                ->count();
        } else {
            $status_action = Repair::where('status', '=', 'ดำเนินการ')
                ->where('order_dept', 'LIKE', $order_dept . '%')
                ->where('technician_name', 'LIKE', '%' . $user->emp_id . '%')
                ->count();
        }

        if (Auth::User()->isManager()) {
            $repair_all_noti = ($status_wait + $status_appove + $status_action + $status_check + $status_success);
        } else if (Auth::User()->isLeader()) {
            $repair_all_noti = ($status_appove + $status_action + $status_check + $status_success);
        } else {
            $repair_all_noti = ($status_action + $status_success);
        }

        $repair_action_noti = ($status_appove + $status_action + $status_check);
        $res = array(
            "repair_all_noti" => $repair_all_noti,
            "repair_success_noti" => $status_success,
            "repair_wait_noti" => $status_wait,
            "repair_action_noti" => $repair_action_noti,
        );
        return $res;
    }

    public function repair_topbar()
    {
        // ######### แจ้งซ่อม #########
        $user = auth()->user();
        $deptDatas = Department::where('dept_id', '=', $user->dept_id)->first();
        if ($deptDatas->level == 1) {
            $order_dept = substr($user->dept_id, 0, 3);
        } elseif ($deptDatas->level == 2) {
            $order_dept = substr($user->dept_id, 0, 5);
        } else {
            $order_dept = $user->dept_id;
        }

        $result = [];
        $users = RepairController::getAuthorizeUserLevel2();
        if (Auth::User()->isManager()) {
            $_data = Repair::whereIn('status', ['รออนุมัติ', 'ผ่านการตรวจสอบ'])
                ->whereIn('user_id', $users)
                ->get(['order_id', 'user_id', 'status']);
            $result["wait"] = $_data;
        } elseif (Auth::User()->isManagerHelper()) {
            $_data = Repair::whereIn('status', ['รออนุมัติ', 'ผ่านการตรวจสอบ'])
                ->where('dept_id', 'LIKE', $order_dept . '%')
                ->get(['order_id', 'user_id', 'status']);
            $result["wait"] = $_data;
        }

        if (Auth::User()->isLeader() && Auth::User()->manageMaintenance()) {
            $status_appove = Repair::where('status', '=', 'หัวหน้าอนุมัติ')
                ->where('order_dept', 'LIKE', $order_dept . '%')
                ->get(['order_id', 'user_id', 'status']);
            $result["approve"] = $status_appove;

            $status_check = Repair::where('status', '=', 'รอตรวจสอบ')
                ->where('order_dept', 'LIKE', $order_dept . '%')
                ->get(['order_id', 'user_id', 'status']);
            $result["check"] = $status_check;
        }
        if (Auth::User()->isLeader() && Auth::User()->manageMaintenance()) {
            $status_action = Repair::where('status', '=', 'ดำเนินการ')
                ->where('order_dept', 'LIKE', $order_dept . '%')
                ->get(['order_id', 'user_id', 'status']);
        } else {
            $status_action = Repair::where('status', '=', 'ดำเนินการ')
                ->where('order_dept', 'LIKE', $order_dept . '%')
                ->where('technician_name', 'LIKE', '%' . $user->emp_id . '%')
                ->get(['order_id', 'user_id', 'status']);
        }
        $result["action"] = $status_action;

        $status_success = Repair::where('status', '=', 'ผ่านการตรวจสอบ')
            ->where('user_id', '=', $user->emp_id)
            ->get(['order_id', 'user_id', 'status']);
        $result["success"] = $status_success;

        //------------------------------------------------------------
        return $result;
    }

    public function check_notification($app_id, $job_id)
    {
        if ($app_id != "" && $job_id != "") {
            $notify = Notification::where('status', '<>', 3)->where("app_id", "=", $app_id)->where('job_id', '=', $job_id)->get();
            if ($notify->isNotEmpty()) {
                return true;
            }
        }
        return false;
    }

    public function check_notification_by_type($app_id, $job_id, $type)
    {
        if ($app_id != "" && $job_id != "" && $type != "") {
            $notify = Notification::where('status', '<>', 3)->where("app_id", "=", $app_id)->where('job_id', '=', $job_id)->where('type', '=', $type)->get();
            if ($notify->isNotEmpty()) {
                return true;
            }
        }
        return false;
    }

    public function push_notification($parameters)
    {
        if ($parameters != null) {
            DB::beginTransaction();
            try {
                Notification::create([
                    "app_id" => $parameters["app_id"],
                    "app_name" => self::app_name($parameters["app_id"]),
                    "title" => $parameters["title"],
                    "description" => $parameters["description"],
                    "url" => $parameters["url"],
                    "job_id" => $parameters["job_id"],
                    "from_uid" => $parameters["from_uid"],
                    "from_uname" => self::user_name($parameters["from_uid"]),
                    "to_uid" => $parameters["to_uid"],
                    "to_uname" => self::user_name($parameters["to_uid"]),
                    "type" => $parameters["type"],
                    "status" => $parameters["status"],
                ]);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json(['success' => false, 'message' => $e]);
            }
        }
    }

    public function update_notification($app_id = "", $job_id = "")
    {
        if ($app_id != "" && $job_id != "") {
            if (Auth::User()->roleAdmin()) {
                DB::beginTransaction();
                try {
                    Notification::where("app_id", "=", $app_id)->where("job_id", "=", $job_id)->where(function ($q) {
                        $q->where("to_uid", "=", Auth::User()->emp_id)->orWhere('to_uid', '=', '100001');
                    })->where("status", "=", 1)->update(["status" => 2]);
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollback();
                    return response()->json(['success' => false, 'message' => $e]);
                }
            } else {
                DB::beginTransaction();
                try {
                    Notification::where("app_id", "=", $app_id)->where("job_id", "=", $job_id)->where("to_uid", "=", Auth::User()->emp_id)->where("status", "=", 1)->update(["status" => 2]);
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollback();
                    return response()->json(['success' => false, 'message' => $e]);
                }
            }
        }
    }

    public function update_notification_by_type($app_id = "", $job_id = "", $type = "")
    {
        if ($app_id != "" && $job_id != "" && $type != "") {
            if (Auth::User()->roleAdmin()) {
                DB::beginTransaction();
                try {
                    Notification::where("app_id", "=", $app_id)->where("job_id", "=", $job_id)->where(function ($q) {
                        $q->where("to_uid", "=", Auth::User()->emp_id)->orWhere('to_uid', '=', '100001');
                    })->where('type', '=', $type)->where("status", "=", 1)->update(["status" => 2]);
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollback();
                    return response()->json(['success' => false, 'message' => $e]);
                }
            } else {
                DB::beginTransaction();
                try {
                    Notification::where("app_id", "=", $app_id)->where("job_id", "=", $job_id)->where("to_uid", "=", Auth::User()->emp_id)->where('type', '=', $type)->where("status", "=", 1)->update(["status" => 2]);
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollback();
                    return response()->json(['success' => false, 'message' => $e]);
                }
            }
        }
    }

    public function remove_notification($app_id = "", $job_id = "")
    {
        if ($app_id != "" && $job_id != "") {
            DB::beginTransaction();
            try {
                Notification::where("app_id", "=", $app_id)->where("job_id", "=", $job_id)->update(["status" => 3]);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json(['success' => false, 'message' => $e]);
            }
        }
    }

    public function remove_notification_by_type($app_id = "", $job_id = "", $type = "")
    {
        if ($app_id != "" && $job_id != "" && $type != "") {
            DB::beginTransaction();
            try {
                Notification::where("app_id", "=", $app_id)->where("job_id", "=", $job_id)->where('type', '=', $type)->update(["status" => 3]);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json(['success' => false, 'message' => $e]);
            }
        }
    }

    public function app_name($app_id)
    {
        if ($app_id == 23) {
            $name = "ร้องขอสติ๊กเกอร์";
        } else {
            $app = Application::find($app_id);
            if ($app) {
                $name = $app->name;
            } else {
                $name = "";
            }
        }
        return $name;
    }

    public function user_name($id)
    {
        if ($id == "100001") {
            $emp = User::where('emp_id', '=', $id)->first();
            if ($emp) {
                $name = $emp->name . " " . $emp->surname;
            } else {
                $name = "";
            }
        } else {
            $emp = Employee::where('emp_id', '=', $id)->first();
            if ($emp) {
                $name = $emp->name . " " . $emp->surname;
            } else {
                $name = "";
            }
        }
        return $name;
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

    public function adminManager($emp)
    {
        // $admin = ['620274', '630040', '640172', '640195', '660039', '660100'];
        $manager = ['580073', '530107', '510186', '630324', '610284', '630056', '660182'];
        $admin_manager = array_search($emp, $manager);
        return $admin_manager;
    }

    public function removeNoti(Request $request, $permission, $app, $doc_id)
    {
        if ($request->ajax()) {
            if ($app == 'discount') {
                if ($permission == 'personal') {
                    $noti = DisNoti::where('doc_id', '=', $doc_id)->first();
                    $noti->personal_read = "read";
                    $noti->secretary_action = "read";
                    $noti->update();
                } else {
                    $noti = DisNoti::where('doc_id', '=', $doc_id)->first();
                    $noti->manager_read = "read";
                    $noti->update();
                }
            } else if ($app == 'decorate') {
                if ($permission == 'personal') {
                    $noti = DeNoti::where('doc_id', '=', $doc_id)->first();
                    $noti->personal_read = "read";
                    $noti->secretary_action = "read";
                    $noti->update();
                } else {
                    $noti = DeNoti::where('doc_id', '=', $doc_id)->first();
                    $noti->manager_read = "read";
                    $noti->update();
                }
            } else if ($app == 'special') {
                if ($permission == 'personal') {
                    $noti = SpNoti::where('doc_id', '=', $doc_id)->first();
                    $noti->personal_read = "read";
                    $noti->secretary_action = "read";
                    $noti->update();
                } else {
                    $noti = SpNoti::where('doc_id', '=', $doc_id)->first();
                    $noti->manager_read = "read";
                    $noti->update();
                }
            }
            return response()->json(['success' => true,]);
        }
    }
}
