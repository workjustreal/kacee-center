<?php

namespace App\Http\Controllers;

use App\Exports\ShippingHistoryExport;
use App\Models\Eplatform;
use App\Models\Eshop;
use App\Models\EXCustomer;
use App\Models\NocnocApi;
use App\Models\ShippingHistory;
use App\Models\ShippingHistoryLog;
use App\Models\ShopeeApi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use File;

class ShippingController extends Controller
{
    protected $destinationPath;
    public function __construct()
    {
        $this->middleware('auth');
        ini_set('max_execution_time', 300);
        $this->destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/assets/shipping/';
    }

    public function index()
    {
        $eplatform = Eplatform::where('status', '<>', 0)->get();
        $shippingHistory = ShippingHistory::leftjoin('eplatform', 'eplatform.id', '=', 'shipping_history.platform_id')
        ->leftjoin('users', 'users.id', '=', 'shipping_history.userid')
        ->leftjoin('shipping_history_log', 'shipping_history_log.trackingnumber', '=', 'shipping_history.trackingnumber')
        ->where(function ($query) {
            $start = date('Y-m-d');
            $end = date('Y-m-d');
            if ($start != '' && $end != '') {
                $query->where('shipping_history.order_date', '>=', $start);
                $query->where('shipping_history.order_date', '<=', $end);
            } else if ($start != '' && $end == '') {
                $query->where('shipping_history.order_date', '=', $start);
            } else if ($start == '' && $end != '') {
                $query->where('shipping_history.order_date', '=', $end);
            }
        })
        ->select('shipping_history.*', 'eplatform.name as eplatform_name', 'users.name as username', DB::raw('GROUP_CONCAT(DISTINCT(shipping_history.so)) as so_list'), DB::raw('COUNT(DISTINCT(shipping_history.so)) as so_count'), DB::raw('COUNT(DISTINCT(shipping_history_log.id)) as print_count'))
        ->groupBy('shipping_history.trackingnumber')->orderBy('shipping_history.updated_at', 'DESC')->paginate(10);
        return view('shipping.shipping-list')->with('eplatform', $eplatform)->with('shippingHistory', $shippingHistory)->with('current_date', date('d/m/Y'))->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function search_history(Request $request)
    {
        $eplatform = Eplatform::where('status', '<>', 0)->get();
        $data = ShippingHistory::leftjoin('eplatform', 'eplatform.id', '=', 'shipping_history.platform_id')
        ->leftjoin('users', 'users.id', '=', 'shipping_history.userid')
        ->leftjoin('shipping_history_log', 'shipping_history_log.trackingnumber', '=', 'shipping_history.trackingnumber')
        ->where(function ($query) use ($request) {
            if ($request->eplatform != '') {
                $query->where('shipping_history.platform_id', '=', $request->eplatform);
            }
            if ($request->trackingnumber != '') {
                $query->where('shipping_history.trackingnumber', 'LIKE', '%' . trim(str_replace(' ', '%', $request->trackingnumber)) . '%');
            }
            if ($request->ordernumber != '') {
                $query->where('shipping_history.ordernumber', 'LIKE', '%' . trim(str_replace(' ', '%', $request->ordernumber)) . '%');
            }
            if ($request->so != '') {
                $query->where('shipping_history.so', 'LIKE', '%' . trim(str_replace(' ', '%', $request->so)) . '%');
            }
            $start = "";
            $end = "";
            if ($request->order_date_start != '') {
                $start = Carbon::createFromFormat('d/m/Y', $request->order_date_start)->format('Y-m-d');
            }
            if ($request->order_date_end != '') {
                $end = Carbon::createFromFormat('d/m/Y', $request->order_date_end)->format('Y-m-d');
            }
            if ($start != '' && $end != '') {
                $query->where('shipping_history.order_date', '>=', $start);
                $query->where('shipping_history.order_date', '<=', $end);
            } else if ($start != '' && $end == '') {
                $query->where('shipping_history.order_date', '=', $start);
            } else if ($start == '' && $end != '') {
                $query->where('shipping_history.order_date', '=', $end);
            }
        });

        if ($request->action == "export") {
            $shippingHistory = $data->select('shipping_history.*', 'eplatform.name as eplatform_name', 'users.name as username')
                ->orderBy('shipping_history.delivery_date', 'ASC')->orderBy('shipping_history.trackingnumber', 'ASC')->get();
            return Excel::download(new ShippingHistoryExport($shippingHistory, "Sheet1"), 'ประวัติใบปะหน้าพัสดุ_'.now().'.xlsx');
        } else {
            $shippingHistory = $data->select('shipping_history.*', 'eplatform.name as eplatform_name', 'users.name as username', DB::raw('GROUP_CONCAT(DISTINCT(shipping_history.so)) as so_list'), DB::raw('COUNT(DISTINCT(shipping_history.so)) as so_count'), DB::raw('COUNT(DISTINCT(shipping_history_log.id)) as print_count'))
                ->groupBy('shipping_history.trackingnumber')->orderBy('shipping_history.updated_at', 'DESC')->paginate(10);
            $request->flash();
            return view('shipping.shipping-list')->with('eplatform', $eplatform)->with('shippingHistory', $shippingHistory)->with('i', (request()->input('page', 1) - 1) * 10);
        }
    }

    public function history_print_log(Request $request)
    {
        $log = ShippingHistoryLog::leftjoin('users', 'users.id', '=', 'shipping_history_log.userid')
            ->where('shipping_history_log.trackingnumber', '=', $request->trackingnumber)
            ->select('shipping_history_log.*', 'users.name as username')
            ->orderBy('shipping_history_log.so', 'asc')->orderBy('shipping_history_log.updated_at', 'asc')->get();
        if ($log->isEmpty()) {
            $log = ShippingHistory::leftjoin('users', 'users.id', '=', 'shipping_history.userid')
            ->where('shipping_history.trackingnumber', '=', $request->trackingnumber)
            ->select('shipping_history.*', 'users.name as username')
            ->orderBy('shipping_history.so', 'asc')->orderBy('shipping_history.updated_at', 'asc')->get();
        }
        return response()->json(["success" => true, "data" => $log]);
    }

    public function print_history_clear($trackingnumber)
    {
        if ($trackingnumber != "") {
            ShippingHistoryLog::where('trackingnumber', '=', $trackingnumber)->delete();
            ShippingHistory::where('trackingnumber', '=', $trackingnumber)->delete();
            return response()->json(['success' => true,'message' => 'ล้างประวัติเรียบร้อย']);
        }
    }

    public function index_other()
    {
        $eplatform = Eplatform::where('status', '<>', 0)->get();
        return view('shipping.shipping-list2')->with('eplatform', $eplatform)->with('current_date', date('d/m/Y'));
    }

    public function search_history_other(Request $request)
    {
        ## Read value
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value

        // Data
        $data = ShippingHistory::leftjoin('eplatform', 'eplatform.id', '=', 'shipping_history.platform_id')
            ->leftjoin('users', 'users.id', '=', 'shipping_history.userid')
            ->where(function ($query) use ($request) {
                if ($request->eplatform != '') {
                    $query->where('shipping_history.platform_id', '=', $request->eplatform);
                }
                $date_start = "";
                $date_end = "";
                if ($request->order_date_start != '') {
                    $date_start = Carbon::createFromFormat('d/m/Y', $request->order_date_start)->format('Y-m-d');
                }
                if ($request->order_date_end != '') {
                    $date_end = Carbon::createFromFormat('d/m/Y', $request->order_date_end)->format('Y-m-d');
                }
                if ($date_start != '' && $date_end != '') {
                    $query->where('shipping_history.order_date', '>=', $date_start);
                    $query->where('shipping_history.order_date', '<=', $date_end);
                } else if ($date_start != '' && $date_end == '') {
                    $query->where('shipping_history.order_date', '=', $date_start);
                } else if ($date_start == '' && $date_end != '') {
                    $query->where('shipping_history.order_date', '=', $date_end);
                }
            });

        $dataFilter = $data->where(function ($query) use ($request) {
            if ($request->search['value'] != '') {
                $query->where('shipping_history.trackingnumber', 'LIKE', '%' . trim(str_replace(' ', '%', $request->search['value'])) . '%');
                $query->orWhere('shipping_history.ordernumber', 'LIKE', '%' . trim(str_replace(' ', '%', $request->search['value'])) . '%');
                $query->orWhere('shipping_history.so', 'LIKE', '%' . trim(str_replace(' ', '%', $request->search['value'])) . '%');
            }
        });

        // Total records
        $totalRecords = $data->select(DB::raw('count(distinct(shipping_history.trackingnumber)) as allcount'))->first();
        $totalRecordswithFilter = $dataFilter->select(DB::raw('count(distinct(shipping_history.trackingnumber)) as allcount'))->first();

        // Fetch records
        $records = $data->select('shipping_history.*', 'eplatform.name as eplatform_name', 'users.name as username', DB::raw('GROUP_CONCAT(DISTINCT(shipping_history.so)) as so_list'), DB::raw('COUNT(DISTINCT(shipping_history.so)) as so_count'))
            ->groupBy('shipping_history.trackingnumber')->orderBy($columnName,$columnSortOrder)
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();

        $loop = $start + 1;
        foreach ($records as $record) {
            $id = $record->id;
            $trackingnumber = $record->trackingnumber;
            $delivery_date = $record->delivery_date;
            $ordernumber = $record->ordernumber;
            $order_date = $record->order_date;
            $so_list = $record->so_list;
            $so_count = $record->so_count;
            $eplatform_name = $record->eplatform_name;
            $username = $record->username;
            $updated_at = $record->updated_at;

            $data_arr[] = array(
                "loop" => $loop,
                "trackingnumber" => $trackingnumber.' <a href="/shipping/trackingnumber/'.$trackingnumber.'" target="_blink"><i class="fas fa-file-pdf text-danger"></i></a>',
                "delivery_date" => Carbon::parse($delivery_date)->format('d/m/Y'),
                "ordernumber" => $ordernumber,
                "order_date" => Carbon::parse($order_date)->format('d/m/Y'),
                "so_list" => str_replace(",", "<br>", $so_list),
                "so_count" => $so_count,
                "eplatform_name" => $eplatform_name,
                "username" => $username,
                "updated_at" => Carbon::parse($updated_at)->format('d/m/Y H:i:s'),
            );
            $loop++;
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords->allcount,
            "iTotalDisplayRecords" => $totalRecordswithFilter->allcount,
            "aaData" => $data_arr
        );

        return response()->json($response);
    }

    public function shippingForm()
    {
        return view('shipping.shipping-form');
    }

    public function callSaleOrder($so)
    {
        $response = Http::get('http://192.168.2.20:2980/api/SaleOrder/' . $so);

        return json_decode($response->body());
    }

    public function decriptSO($sonumber) {
        $so_number = "";
        if (substr($sonumber, 0, 2) == "SO") {
            $so_number = $sonumber;
        } else {
            $yy = substr($sonumber, 0, 1);
            if ($yy == "C") {
                $yy = "22";
            } else if ($yy == "D") {
                $yy = "23";
            } else if ($yy == "E") {
                $yy = "24";
            } else if ($yy == "F") {
                $yy = "25";
            }
            $m = substr($sonumber, 1, 1);
            if ($m == "A") {
                $m = "10";
            } else if ($m == "B") {
                $m = "11";
            } else if ($m == "C") {
                $m = "12";
            }
            $m = str_pad(intval($m), 2, "0", STR_PAD_LEFT);
            $running = substr($sonumber, -5);
            $running = str_pad(intval($running), 6, "0", STR_PAD_LEFT);
            $so_number = "SO" . $yy . $m . $running;
        }
        return $so_number;
    }

    public function getShop($platform_id)
    {
        $eshop = Eshop::where('platform_id', '=', $platform_id)->where('status', '=', 1)->first();
        return $eshop;
    }

    public function search(Request $request)
    {
        if ($request->ajax()) {
            $sonumber = trim(strtoupper($request->search));
            $so_number = self::decriptSO($sonumber);

            $so = self::callSaleOrder($so_number);
            if ($so->soid === "NotFound") {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'ไม่พบหมายเลข SO นี้'
                    ]
                );
            } else {
                if ($so->so_status === 0) {
                    return response()->json(
                        [
                            'success' => false,
                            'message' => 'ไม่พบหมายเลข SO นี้ถูกยกเลิก'
                        ]
                    );
                } else if ($so->so_status === 1) {
                    $ordernumber = trim($so->ordernumber);
                }
            }

            if ($request->print != "print_again") {
                $logCount = ShippingHistoryLog::where('trackingnumber', '=', $so->trackingnumber)->where('so', '=', $so->soid)
                    ->select('count(*) as allcount')->count();
                if ($logCount > 0) {
                    return response()->json(
                        [
                            'success' => false,
                            'message' => 'หมายเลข SO นี้ ถูกพิมพ์ไปแล้ว',
                            'print_count' => $logCount
                        ]
                    );
                }
            }

            $host = url('/assets/shipping/');
            $filename = $so->trackingnumber;
            $packages = 1;

            $platform_id = 0;
            $customer = EXCustomer::where('cuscod', '=', $so->cuscod)->first();
            if ($customer) {
                if (stripos($customer->cusnam,"shopee") !== false) {
                    $platform_id = 1;
                } else if (stripos($customer->cusnam,"lazada") !== false) {
                    $platform_id = 2;
                } else if (stripos($customer->cusnam,"nocnoc") !== false) {
                    $platform_id = 3;
                } else if (stripos($customer->cusnam,"tiktok") !== false || stripos($customer->cusnam,"tik tok") !== false) {
                    $platform_id = 4;
                }
                $eshop = self::getShop($platform_id);
            }

            if ($request->print == "print_again") {
                $eplatform = Eplatform::find($platform_id);
                $platform = strtolower($eplatform->name);
                $path = $host . '/' . $platform . '/' . $filename . '.pdf';
                $path_drive = $this->destinationPath . '/' . $platform . '/' . $filename . '.pdf';
                if (File::exists($path_drive) && File::size($path_drive) > 0) {
                    self::saveShippingHistory($platform_id, $so, $packages, $request->ip());
                    $stream_opts = [
                        "ssl" => [
                            "verify_peer"=>false,
                            "verify_peer_name"=>false,
                        ]
                    ];
                    $chkfile = file($path_drive, false, stream_context_create($stream_opts));
                    $endchkfile= trim($chkfile[count($chkfile) - 1]);
                    if ($endchkfile === "%%EOF") {
                        return response()->json(
                            [
                                'success' => true,
                                'platform_id' => $platform_id,
                                'file' => $path
                            ]
                        );
                    }
                }
            }

            if ($platform_id == 1) { // Shopee
                $shopeeController = new ShopeeApiController;
                $shopid = (int)$eshop->seller_id;

                // if (is_numeric($ordernumber)) {
                //     return response()->json(['success' => false,'message' => 'หมายเลขออเดอร์ใน SO ไม่ถูกต้อง']);
                // }

                if ($eshop->api_version == 1) {
                    //------------------ V1 ------------------------
                    $ordersn_list = array($ordernumber);
                    $parameters = [
                        "ordersn_list" => $ordersn_list,
                        "partner_id" => $shopeeController->partner_id,
                        "shopid" => $shopid,
                        "timestamp" => time()
                    ];
                    $data = $shopeeController->callApiV1('/orders/detail', $parameters);
                    if ($data != NULL) {
                        if ($data->orders[0]->order_status == "IN_CANCEL" || $data->orders[0]->order_status == "CANCELLED") {
                            return response()->json(['success' => false,'message' => 'ออเดอร์ถูกยกเลิก']);
                        }
                    }
                    $parameters = [
                        "ordersn_list" => $ordersn_list,
                        "is_batch" => true,
                        "partner_id" => $shopeeController->partner_id,
                        "shopid" => $shopid,
                        "timestamp" => time()
                    ];
                    $data = $shopeeController->callApiV1('/logistics/airway_bill/get_mass', $parameters);
                    if ($data == NULL) {
                        return response()->json(
                            [
                                'success' => false,
                                'message' => NULL
                            ]
                        );
                    } else {
                        if(count($data->batch_result->errors)){
                            $message = '';
                            for($i=0; $i<count($data->batch_result->errors); $i++){
                                $message .= '<span class="text-danger">'.$data->batch_result->errors[$i]->ordersn.'</span>';
                                $message .= '<br><span class="text-danger">'.$data->batch_result->errors[$i]->error_description.'</span>';
                            }
                            return response()->json(
                                [
                                    'success' => false,
                                    'message' => $message
                                ]
                            );
                        } else {
                            $url = $data->batch_result->airway_bills[0];
                            self::savePDF($platform_id, $filename, $url);
                            self::saveShippingHistory($platform_id, $so, $packages, $request->ip());
                            // $base64 = self::convertFiletoBase64($platform_id, $filename);
                            // return response()->json(
                            //     [
                            //         'success' => true,
                            //         'file' => $base64
                            //     ]
                            // );
                            $eplatform = Eplatform::find($platform_id);
                            $platform = strtolower($eplatform->name);
                            return response()->json(
                                [
                                    'success' => true,
                                    'platform_id' => $platform_id,
                                    'file' => $host . '/' . $platform . '/' . $filename . '.pdf'
                                ]
                            );
                        }
                    }
                    //------------------ END -------------------------
                } else if ($eshop->api_version == 2) {
                    //------------------ V2 ------------------------
                    $package_number_arr = array();
                    $parameters = [
                        "order_sn_list" => $ordernumber,
                        "response_optional_fields" => "package_list",
                    ];
                    $data = $shopeeController->callApiV2('/api/v2/order/get_order_detail', $shopid, $parameters, 'GET');
                    if (isset($data->error)) {
                        if ($data->error == "") {
                            if ($data->response->order_list[0]->order_status == "CANCELLED") {
                                return response()->json(['success' => false,'message' => 'ออเดอร์ถูกยกเลิก']);
                            }
                            if (isset($data->response->order_list[0]->package_list)) {
                                for ($p1=0; $p1<count($data->response->order_list[0]->package_list); $p1++) {
                                    $package_number_arr[] = $data->response->order_list[0]->package_list[$p1]->package_number;
                                }
                            }
                        }
                    }

                    // get tracking_number by package_number
                    $package_number = "";
                    for ($p2=0; $p2<count($package_number_arr); $p2++) {
                        $parameters = [
                            "order_sn" => $ordernumber,
                            "package_number" => $package_number_arr[$p2],
                        ];
                        $data = $shopeeController->callApiV2('/api/v2/logistics/get_tracking_number', $shopid, $parameters, 'GET');
                        if (isset($data->error)) {
                            if ($data->error == "") {
                                if ($so->trackingnumber == $data->response->tracking_number) {
                                    $package_number = $package_number_arr[$p2];
                                }
                            }
                        }
                        if ($package_number != "") {
                            break;
                        }
                    }

                    // Create Shipping
                    if ($package_number == "") {
                        $parameters = [
                            "order_list" => array([
                                "order_sn" => $ordernumber,
                                "tracking_number" => $so->trackingnumber,
                                "shipping_document_type" => "NORMAL_AIR_WAYBILL",
                            ]),
                        ];
                    } else {
                        $parameters = [
                            "order_list" => array([
                                "order_sn" => $ordernumber,
                                "package_number" => $package_number,
                                "tracking_number" => $so->trackingnumber,
                                "shipping_document_type" => "NORMAL_AIR_WAYBILL",
                            ]),
                        ];
                    }
                    $data = $shopeeController->callApiV2('/api/v2/logistics/create_shipping_document', $shopid, $parameters, "POST");
                    if (isset($data->error)) {
                        if ($data->error == "") {
                            // Read Shipping status Ready
                            if ($package_number == "") {
                                $parameters = [
                                    "order_list" => array([
                                        "order_sn" => $ordernumber,
                                        "shipping_document_type" => "NORMAL_AIR_WAYBILL",
                                    ]),
                                ];
                            } else {
                                $parameters = [
                                    "order_list" => array([
                                        "order_sn" => $ordernumber,
                                        "package_number" => $package_number,
                                        "shipping_document_type" => "NORMAL_AIR_WAYBILL",
                                    ]),
                                ];
                            }
                            for ($i=0; $i<5; $i++) {
                                $data = $shopeeController->callApiV2('/api/v2/logistics/get_shipping_document_result', $shopid, $parameters, 'POST');
                                if (isset($data->error)) {
                                    if ($data->error == "") {
                                        if ($data->response->result_list[0]->status == "READY") {
                                            // Download Shipping
                                            $response = self::savePDFShopeeV2($platform_id, $shopid, $filename, $ordernumber, $package_number);
                                            if ($response["success"] == true) {
                                                self::saveShippingHistory($platform_id, $so, $packages, $request->ip());
                                                $eplatform = Eplatform::find($platform_id);
                                                $platform = strtolower($eplatform->name);
                                                return response()->json(
                                                    [
                                                        'success' => true,
                                                        'platform_id' => $platform_id,
                                                        'file' => $host . '/' . $platform . '/' . $filename . '.pdf'
                                                    ]
                                                );
                                            } else {
                                                $message = $response["message"];
                                                return response()->json(['success' => false,'message' => $message]);
                                            }
                                        } else {
                                            sleep(1);
                                        }
                                    } else {
                                        $message = '<p class="text-danger">error: ' . $data->error . ' message: ' . $data->message . '</p>';
                                        return response()->json(['success' => false,'message' => $message]);
                                    }
                                } else {
                                    return response()->json(['success' => false,'message' => 'Shipping Api Error!']);
                                }
                            }
                        } else {
                            $message = '<p class="text-danger">error: ' . $data->error . ' message: ' . $data->message . '</p>';
                            return response()->json(['success' => false,'message' => $message]);
                        }
                    } else {
                        return response()->json(['success' => false, 'message' => 'Create Shipping Api Error!']);
                    }
                    //------------------ END -------------------------
                }
            } else if ($platform_id == 2) { // Lazada
                $lazadaController = new LazadaApiController;
                $seller_id = $eshop->seller_id;

                if (!is_numeric($ordernumber)) {
                    return response()->json(['success' => false,'message' => 'หมายเลขออเดอร์ใน SO ไม่ถูกต้อง']);
                }

                $parameters = array(
                    "order_id" => $ordernumber,
                );

                $order_item = $lazadaController->callApi('/order/items/get', $seller_id, $parameters);
                $order_item_ids = "";
                if (isset($order_item["code"])) {
                    if ($order_item["code"] == "0") {
                        $count_data = 0;
                        $is_canceled = 0;
                        foreach ($order_item["data"] as $value) {
                            if ($so->trackingnumber == $value["tracking_code"]) {
                                if($order_item_ids != ""){
                                    $order_item_ids .= ",";
                                }
                                $order_item_ids .= $value["order_item_id"];

                                if ($value["status"] == "canceled") {
                                    $is_canceled++;
                                }
                                $count_data++;
                            }
                        }
                        if ($count_data == $is_canceled) {
                            return response()->json(['success' => false,'message' => 'ออเดอร์ถูกยกเลิก']);
                        }
                    }
                }
                $parameters = array(
                    "order_item_ids" => "[".$order_item_ids."]",
                );
                $data = $lazadaController->callApi('/order/document/awb/pdf/get', $seller_id, $parameters);
                if (isset($data["code"])) {
                    if ($data["code"] == "0") {
                        $iframe_string = base64_decode($data["data"]["document"]["file"]);
                        preg_match('/src="([^"]+)"/', $iframe_string, $match);
                        $url = $match[1];
                        self::savePDF($platform_id, $filename, $url);
                        self::saveShippingHistory($platform_id, $so, $packages, $request->ip());
                        // $base64 = self::convertFiletoBase64($platform_id, $filename);
                        // return response()->json(
                        //     [
                        //         'success' => true,
                        //         'file' => $base64
                        //     ]
                        // );
                        $eplatform = Eplatform::find($platform_id);
                        $platform = strtolower($eplatform->name);
                        return response()->json(
                            [
                                'success' => true,
                                'platform_id' => $platform_id,
                                'file' => $host . '/' . $platform . '/' . $filename . '.pdf'
                            ]
                        );
                    } else {
                        $message = '';
                        foreach ($data as $key => $value) {
                            $message .= '<p class="text-danger">' . $key . ' : ' . $value . '</p>';
                        }
                        return response()->json(
                            [
                                'success' => false,
                                'message' => $message
                            ]
                        );
                    }
                } else {
                    return response()->json(['success' => false, 'message' => 'Shipping Api Error!']);
                }
                // <!-- ========== HTML ========== -->
                // $data = $lazadaController->callApi('/order/document/awb/html/get', $seller_id, $parameters);
                // if ($data["code"] == "0") {
                //     $file = base64_decode($data["data"]["document"]["file"]);
                //     self::saveShippingHistory($platform_id, $so, $packages, $request->ip());
                //     return response()->json(
                //         [
                //             'success' => true,
                //             'platform_id' => $platform_id,
                //             'file' => $file
                //         ]
                //     );
                // } else {
                //     $message = '';
                //     foreach ($data as $key => $value) {
                //         $message .= '<p class="text-danger">' . $key . ' : ' . $value . '</p>';
                //     }
                //     return response()->json(
                //         [
                //             'success' => false,
                //             'message' => $message
                //         ]
                //     );
                // }
                // <!-- ========== end ========== -->
            } else if ($platform_id == 3) { // NocNoc
                $nocnocController = new NocNocApiController;
                $seller_id = $eshop->seller_id;

                if (!is_numeric($ordernumber)) {
                    // ชั่วคราว
                    if ($ordernumber == "NN-7182C472E33D") {
                        $ordernumber = 3362835;
                    } else if ($ordernumber == "NN-6182C441EFC8") {
                        $ordernumber = 3362804;
                    }
                    // END
                }

                if (!is_numeric($ordernumber)) {
                    return response()->json(['success' => false,'message' => 'หมายเลขออเดอร์ใน SO ไม่ถูกต้อง']);
                }

                $data = $nocnocController->callApiV1('/orders/shipments/'.$ordernumber, $seller_id, null, "GET");
                if (isset($data->status)) {
                    if ($data->status == "SUCCESS") {
                        if ($data->data->order_status == "CANCELLED") {
                            return response()->json(['success' => false,'message' => 'ออเดอร์ถูกยกเลิก']);
                        }
                        $packages = count($data->data->shipping_packages);
                    }
                }

                $parameters = [
                    "shipment_id" => (int) $ordernumber,
                    "label_count" => 1,
                ];
                $data = $nocnocController->callApiV1('/orders/shipments/labels', $seller_id, $parameters, "POST");
                if (isset($data->status)) {
                    if ($data->status == "SUCCESS") {
                        $url = $data->label_location;
                        self::savePDF($platform_id, $filename, $url);
                        self::saveShippingHistory($platform_id, $so, $packages, $request->ip());
                        $eplatform = Eplatform::find($platform_id);
                        $platform = strtolower($eplatform->name);
                        return response()->json(
                            [
                                'success' => true,
                                'platform_id' => $platform_id,
                                'file' => $host . '/' . $platform . '/' . $filename . '.pdf'
                            ]
                        );
                    } else {
                        $message = '<p class="text-danger">status : ' . $data->status . '</p>';
                        $message .= '<p class="text-danger">errorCode : ' . $data->errorCode . '</p>';
                        $message .= '<p class="text-danger">error : ' . $data->error . '</p>';
                        return response()->json(['success' => false, 'message' => $message]);
                    }
                } else {
                    return response()->json(['success' => false, 'message' => 'Shipping Api Error!']);
                }
            } else if ($platform_id == 4) { // TikTok
                $tiktokController = new TikTokApiController;
                $shop_id = (int)$eshop->seller_id;

                $package_id_list = [];
                $order_id_list = array($ordernumber);
                $parameters = [
                    "order_id_list" => $order_id_list,
                ];
                $data = $tiktokController->callApiV2('/api/orders/detail/query', $shop_id, $parameters, 'POST');
                if (isset($data->code)) {
                    if ($data->code == 0) {
                        if ($data->data->order_list[0]->order_status == 140) {
                            return response()->json(['success' => false, 'message' => 'ออเดอร์ถูกยกเลิก']);
                        }
                        for ($i=0; $i<count($data->data->order_list[0]->package_list); $i++) {
                            $package_id_list[] = $data->data->order_list[0]->package_list[$i]->package_id;
                        }
                    }
                }

                if (count($package_id_list) > 1) {
                    // Split
                    for ($i=0; $i<count($package_id_list); $i++) {
                        $package_id = "";
                        $parameters = [
                            "package_id" => $package_id_list[$i]
                        ];
                        $package = $tiktokController->callApiV2('/api/fulfillment/detail', $shop_id, $parameters, 'GET');
                        if ($package->code == 0) {
                            for ($j=0; $j<count($data->data->order_list[0]->order_line_list); $j++) {
                                if ($data->data->order_list[0]->order_line_list[$j]->tracking_number == $package->data->tracking_number) {
                                    $package_id = trim($package->data->package_id);
                                    break;
                                }
                            }
                        }
                        if ($package_id == "") {
                            return response()->json(['success' => false, 'message' => "Package ID not found."]);
                        }
                        $parameters = [
                            "package_id" => $package_id,
                            "document_type" => 1,
                            "document_size" => 0,
                        ];
                        $data = $tiktokController->callApiV2('/api/fulfillment/shipping_document', $shop_id, $parameters, 'GET');
                        if (isset($data->code)) {
                            if ($data->code == 0) {
                                $url = $data->data->doc_url;
                                self::savePDF($platform_id, $filename, $url);
                                self::saveShippingHistory($platform_id, $so, $packages, $request->ip());
                                $eplatform = Eplatform::find($platform_id);
                                $platform = strtolower($eplatform->name);
                                return response()->json(
                                    [
                                        'success' => true,
                                        'platform_id' => $platform_id,
                                        'file' => $host . '/' . $platform . '/' . $filename . '.pdf'
                                    ]
                                );
                            } else {
                                $message = '<p class="text-danger">code : ' . $data->code . '</p>';
                                $message .= '<p class="text-danger">message : ' . $data->message . '</p>';
                                return response()->json(['success' => false, 'message' => $message]);
                            }
                        } else {
                            return response()->json(['success' => false, 'message' => 'Shipping Api Error!']);
                        }
                    }
                } else {
                    // Single
                    $parameters = [
                        "order_id" => $ordernumber,
                        "document_type" => "SHIPPING_LABEL",
                        "document_size" => "A6",
                    ];
                    $data = $tiktokController->callApiV2('/api/logistics/shipping_document', $shop_id, $parameters, 'GET');
                    if (isset($data->code)) {
                        if ($data->code == 0) {
                            $url = $data->data->doc_url;
                            self::savePDF($platform_id, $filename, $url);
                            self::saveShippingHistory($platform_id, $so, $packages, $request->ip());
                            $eplatform = Eplatform::find($platform_id);
                            $platform = strtolower($eplatform->name);
                            return response()->json(
                                [
                                    'success' => true,
                                    'platform_id' => $platform_id,
                                    'file' => $host . '/' . $platform . '/' . $filename . '.pdf'
                                ]
                            );
                        } else {
                            $message = '<p class="text-danger">code : ' . $data->code . '</p>';
                            $message .= '<p class="text-danger">message : ' . $data->message . '</p>';
                            return response()->json(['success' => false, 'message' => $message]);
                        }
                    } else {
                        return response()->json(['success' => false, 'message' => 'Shipping Api Error!']);
                    }
                }
            }
        }
    }

    public function convertFiletoBase64($platform_id, $filename)
    {
        $eplatform = Eplatform::find($platform_id);
        $platform = strtolower($eplatform->name);
        $fileContent = file_get_contents($this->destinationPath . $platform . '/' . $filename . '.pdf');
        $base64 = base64_encode($fileContent);
        return $base64;
    }

    public function viewPDF($trackingnumber)
    {
        $shippingHistory = ShippingHistory::where('trackingnumber', '=', $trackingnumber)->first();
        if ($shippingHistory) {
            $eplatform = Eplatform::find($shippingHistory->platform_id);
            $platform = strtolower($eplatform->name) . '/';
            $file = $this->destinationPath . $platform . $trackingnumber . '.pdf';
            // Header content type
            header('Content-type: application/pdf');
            header('Content-Disposition: inline; filename="' . $trackingnumber . '.pdf' . '"');
            header('Content-Transfer-Encoding: binary');
            header('Accept-Ranges: bytes');
            // Read the file
            @readfile($file);
        } else {
            echo '<h1>ไม่พบข้อมูล!</h1>';
        }
    }

    public function savePDF($platform_id, $filename, $url)
    {
        $eplatform = Eplatform::find($platform_id);
        $shop = strtolower($eplatform->name);
        $path = $this->destinationPath . $shop . '/' . $filename . '.pdf';
        if ($platform_id == 3) { // NocNoc
            $eshop = self::getShop($platform_id);
            $api = NocnocApi::where('seller_id', '=', $eshop->seller_id)->first();
            $token = $api->access_token;
            $stream_opts = [
                'http' => [
                    'method'  => 'GET',
                    'Content-type' => 'application/pdf',
                    'header' => 'Authorization: Bearer '.$token
                ],
                "ssl" => [
                    "verify_peer"=>false,
                    "verify_peer_name"=>false,
                ]
            ];
        } else {
            $stream_opts = [
                "ssl" => [
                    "verify_peer"=>false,
                    "verify_peer_name"=>false,
                ]
            ];
        }
        $url = file_get_contents($url, false, stream_context_create($stream_opts));
        file_put_contents($path, $url);
    }

    public function saveShippingHistory($platform_id, $so, $packages, $userip)
    {
        $user = auth()->user();
        $shipping = ShippingHistory::where('trackingnumber', '=', $so->trackingnumber)->where('so', '=', $so->soid)->first();
        if ($shipping) {
            $shipping_history = ShippingHistory::find($shipping->id);
            if ($shipping->packages != $packages) {
                $shipping_history->packages = $packages;
            }
            $shipping_history->userid = $user->id;
            $shipping_history->userip = $userip;
            $shipping_history->updated_at = now();
            $shipping_history->update();
        } else {
            $shipping_history = new ShippingHistory();
            $shipping_history->trackingnumber = $so->trackingnumber;
            $shipping_history->delivery_date = (strlen($so->delivery_date) >= 10) ? substr($so->delivery_date, 0, 10) : "";
            $shipping_history->ordernumber = $so->ordernumber;
            $shipping_history->order_date = (strlen($so->order_date) >= 10) ? substr($so->order_date, 0, 10) : "";
            $shipping_history->so = $so->soid;
            $shipping_history->packages = $packages;
            $shipping_history->platform_id = $platform_id;
            $shipping_history->userid = $user->id;
            $shipping_history->userip = $userip;
            $shipping_history->save();
        }
        $shipping_history_log = new ShippingHistoryLog();
        $shipping_history_log->trackingnumber = $so->trackingnumber;
        $shipping_history_log->ordernumber = $so->ordernumber;
        $shipping_history_log->so = $so->soid;
        $shipping_history_log->userid = $user->id;
        $shipping_history_log->userip = $userip;
        $shipping_history_log->save();
    }

    public function savePDFShopeeV2($platform_id, $shop_id, $filename, $ordernumber, $package_number)
    {
        if ($package_number == "") {
            $parameters = [
                "shipping_document_type" => "NORMAL_AIR_WAYBILL",
                "order_list" => array([
                    "order_sn" => $ordernumber,
                ]),
            ];
        } else {
            $parameters = [
                "shipping_document_type" => "NORMAL_AIR_WAYBILL",
                "order_list" => array([
                    "order_sn" => $ordernumber,
                    "package_number" => $package_number,
                ]),
            ];
        }
        $eplatform = Eplatform::find($platform_id);
        $shop = strtolower($eplatform->name);
        $api_url = $eplatform->api_url;
        $partner_id = (int)$eplatform->app_key;
        $secretKey = $eplatform->app_secret;

        $timest = time();
        $api = ShopeeApi::where('seller_id', '=', $shop_id)->first();
        $access_token = $api->access_token;
        $path = '/api/v2/logistics/download_shipping_document';
        $base_string = $partner_id . $path . $timest . $access_token . $shop_id;
        $sign = hash_hmac('sha256', $base_string, $secretKey, false);
        $url = $api_url . $path . "?partner_id=" . $partner_id . "&timestamp=" . $timest . "&access_token=" . $access_token . "&shop_id=" . $shop_id . "&sign=" . $sign;
        $headers = [
            "Content-Type" => "application/json"
        ];
        $response = Http::withOptions([
            'verify' => false,
        ])->timeout(300)->withHeaders($headers)->post($url, $parameters);

        if (isset($response->error)) {
            $message = '<p class="text-danger">error: ' . $response->error . ' message: ' . $response->message . '</p>';
            return array("success"=>false,"message"=>$message);
        } else {
            $stringWithFile = $response;
            header('Content-Type:application/pdf');
            header('Content-Disposition:attachment;filename='.$filename . '.pdf');
            $path = $this->destinationPath . $shop . '/' . $filename . '.pdf';
            file_put_contents($path, $stringWithFile);
            return array("success"=>true,"message"=>'');
        }
    }
}