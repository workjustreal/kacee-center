<?php

namespace App\Http\Controllers\Middleware;

use App\Exports\OrdersShopeeExport;
use App\Exports\OrdersLazadaExport;
use App\Exports\OrdersTiktokExport;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ShopeeApiController;
use App\Http\Controllers\LazadaApiController;
use App\Http\Controllers\NocNocApiController;
use App\Http\Controllers\TikTokApiController;
use App\Models\Middleware\Order;
use App\Models\Middleware\OrderItem;
use App\Models\Middleware\OrderLog;
use App\Models\Eplatform;
use App\Models\Eshop;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class OrdersController extends Controller
{
    protected $shopeeController;
    protected $lazadaController;
    protected $tiktokController;
    protected $shopeeId;
    protected $lazadaId;
    protected $tiktokId;

    public function __construct()
    {
        $this->middleware('auth');
        ini_set('memory_limit','512M');
        ini_set('max_execution_time', 300);

        $this->shopeeController = new ShopeeApiController;
        $this->lazadaController = new LazadaApiController;
        $this->tiktokController = new TiktokApiController;
        $eshop = Eshop::find(1);
        $this->shopeeId = (int)$eshop->seller_id;
        $eshop = Eshop::find(2);
        $this->lazadaId = (int)$eshop->seller_id;
        $eshop = Eshop::find(4);
        $this->tiktokId = (int)$eshop->seller_id;
    }

    public function index()
    {
        $eshop = Eshop::where('status', '<>', 0)->whereIn('id', [1,2,4])->get();
        return view('middleware.orders.order-list')->with('eshop', $eshop);
    }

    public function search(Request $request)
    {
        if ($request->ajax()) {
            $data = Order::from('orders as o')->leftJoin('kacee_center.eshop as s', 'o.shop_id', '=', 's.id')->where('o.order_number', '<>', '')
            ->where(function ($query) use ($request) {
                if ($request->shop_id != "all"){
                    $query->where('o.shop_id', '=', $request->shop_id);
                }
                if ($request->order_status != "all"){
                    $query->where('o.status', '=', $request->order_status);
                }
                if ($request->search != ""){
                    $query->where(function ($_query) use ($request) {
                        $_query->where('s.platform_name', 'LIKE', '%'.$request->search.'%');
                        $_query->orWhere('o.order_number', 'LIKE', '%'.$request->search.'%');
                    });
                }
                $start = "";
                $end = "";
                if ($request->date_start != '') {
                    $start = Carbon::createFromFormat('d/m/Y H:i', $request->date_start)->format('Y-m-d H:i').':00';
                }
                if ($request->date_end != '') {
                    $end = Carbon::createFromFormat('d/m/Y H:i', $request->date_end)->format('Y-m-d H:i').':59';
                }
                if ($start != '' && $end != '') {
                    $query->where('o.create_time', '>=', $start);
                    $query->where('o.create_time', '<=', $end);
                } else if ($start != '' && $end == '') {
                    $query->where('o.create_time', '>=', $start);
                } else if ($start == '' && $end != '') {
                    $query->where('o.create_time', '<=', $end);
                }
            });
            $totalRecords = $data->get('o.order_number')->count();
            $records = $data->select('o.id', 'o.order_number', 'o.create_time', 's.platform_name', 'o.package_count', 'o.total_amount', 'o.total_quantity', 'o.total_shipping_fee', 'o.total_discount', 'o.status')
            ->orderBy('o.create_time', 'DESC')->offset($request->offset)->limit($request->limit)->get();

            $rows = [];
            $n = ($request->offset > 0) ? $request->offset + 1 : 1;
            if ($records->isNotEmpty()) {
                foreach ($records as $rec) {
                    $action = '<div>';
                    if (auth()->user()->roleAdmin()) {
                        $action .= '<a class="action-icon" href="javascript:void(0);" onclick="deleteOrderConfirmation(\''.$rec->id.'\', \''.$rec->order_number.'\')" data-bs-toggle="tooltip" title="ลบคำสั่งซื้อ" tabindex="0" data-plugin="tippy" data-tippy-animation="shift-away" data-tippy-arrow="true"><i class="mdi mdi-delete"></i></a>';
                    } else {
                        if ($rec->status == 1) {
                            $action .= '<a class="action-icon" href="javascript:void(0);" onclick="deleteOrderConfirmation(\''.$rec->id.'\', \''.$rec->order_number.'\')" data-bs-toggle="tooltip" title="ลบคำสั่งซื้อ" tabindex="0" data-plugin="tippy" data-tippy-animation="shift-away" data-tippy-arrow="true"><i class="mdi mdi-delete"></i></a>';
                        }
                    }
                    $action .= '</div>';
                    // $action = '<div>
                    //     <a class="action-icon" href="'.url('middleware/orders/show', $rec->id).'" title="ดู"><i class="mdi mdi-eye"></i></a>
                    //     <a class="action-icon" href="'.url('middleware/orders/create', $rec->id).'" title="สร้าง"><i class="mdi mdi-file-document-edit-outline"></i></a>
                    //     <a class="action-icon" href="javascript:void(0);" onclick="cancelOrderConfirmation(\''.$rec->id.'\')" title="ยกเลิก"><i class="mdi mdi-cancel"></i></a>
                    // </div>';
                    $rows[] = array(
                        "no" => $n,
                        "order_id" => $rec->id,
                        "shop" => $rec->platform_name,
                        "order_number" => '<a class="text-primary" href="'.url('middleware/orders/show', $rec->id).'">'.$rec->order_number.' <span class="text-muted fst-italic">( <span class="text-primary">'.$rec->package_count.'</span> )</span></a>',
                        "create_time" => $rec->create_time,
                        "total_quantity" => self::calcNumberFormat($rec->total_quantity),
                        "total_amount" => self::calcNumberFormat2Decimal($rec->total_amount + $rec->total_shipping_fee),
                        "status" => $rec->status,
                        "status_text" => self::orderStatus($rec->status),
                        "action" => $action,
                    );
                    $n++;
                }
            } else {
                $totalRecords = 0;
                $rows = [];
            }
            $response = array(
                "total" => $totalRecords,
                "totalNotFiltered" => $totalRecords,
                "rows" => $rows,
            );
            return response()->json($response);
        }
    }

    public function updateAndExport(Request $request)
    {
        $order_ids = $request->order_ids;
        if (count($order_ids) <= 0) {
            return response()->json(['success' => false, 'message' => 'ไม่พบข้อมูล!']);
        }
        $chkShop = [];
        $dataExport = [];
        $orders = Order::from('orders as o')->leftJoin('order_items as i', 'o.id', '=', 'i.order_id')
        ->where('o.status', '=', 1)->where('i.status', '=', 1)->whereIn('o.id', $order_ids)
        ->orderBy('o.order_number')->orderBy('i.package_number')
        ->get(['o.id', 'o.shop_id', 'o.order_id', 'o.order_number', 'o.customer_name', 'o.shipping_address', 'i.order_item_id', 'i.sku', 'i.shop_sku', 'i.quantity', 'i.original_price', 'i.sale_price', 'i.tracking_number']);
        foreach ($orders as $order) {
            $shipping_address = ($order->shipping_address != null) ? $order->shipping_address : [];
            if ($order->shop_id == 1) {
                // Shopee
                $shipping_name = trim($shipping_address['name']);
                $shipping_name = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $shipping_name);
                $shipping_name = str_replace("\r\n", '', $shipping_name);
                $dataExport[] = [
                    "order_number" => $order->order_number,
                    "shipping_name" => $shipping_name,
                    "tracking_number" => $order->tracking_number,
                    "sku" => $order->sku,
                    "quantity" => $order->quantity,
                    "sale_price" => $order->sale_price,
                ];
            } else if ($order->shop_id == 2) {
                // Lazada
                $customer_name = trim($order->customer_name);
                $customer_name = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $customer_name);
                $customer_name = str_replace("\r\n", '', $customer_name);

                $shipping_name = trim($shipping_address['first_name']);
                if (trim($shipping_address['last_name']) != "") {
                    $shipping_name .= " ".trim($shipping_address['last_name']);
                }
                $shipping_name = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $shipping_name);
                $shipping_name = str_replace("\r\n", '', $shipping_name);
                for ($i=0; $i<$order->quantity; $i++) {
                    $dataExport[] = [
                        "order_id" => $order->order_id,
                        "order_number" => $order->order_number,
                        "customer_name" => $shipping_name,
                        "shipping_name" => $shipping_name,
                        "tracking_number" => $order->tracking_number,
                        "order_item_id" => $order->order_item_id,
                        "sku" => $order->sku,
                        "shop_sku" => $order->shop_sku,
                        "sale_price" => $order->sale_price,
                        "original_price" => $order->original_price,
                    ];
                }
            } else if ($order->shop_id == 4) {
                // Tiktok
                $shipping_name = trim($shipping_address['name']);
                $shipping_name = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $shipping_name);
                $shipping_name = str_replace("\r\n", '', $shipping_name);
                $dataExport[] = [
                    "order_number" => $order->order_number,
                    "shipping_name" => $shipping_name,
                    "tracking_number" => $order->tracking_number,
                    "sku" => $order->sku,
                    "quantity" => $order->quantity,
                    "sale_price" => $order->sale_price,
                ];
            }
            array_push($chkShop, $order->shop_id);
        }
        if (count(array_unique($chkShop)) > 1) {
            return response()->json(['success' => false, 'message' => 'ห้ามเลือกหลายร้านค้าพร้อมกัน!']);
        }

        $shop = array_unique($chkShop);
        $shop_id = $shop[0];
        $updateStatus = self::callUpdateStatus($shop_id, $order_ids, $dataExport);
        if ($updateStatus["status"] == false) {
            return response()->json(["success"=>false, "message"=>$updateStatus["message"]]);
        }
        $orderCanceled = $updateStatus["orders_canceled"];
        $dataExport = $updateStatus["export"];
        if ($shop_id == 1) {
            $file_name = "SHOPEE-".date('yMd_His').'.xls';
            Excel::store(new OrdersShopeeExport($dataExport, "Sheet1"), $file_name, 'orders');
        } else if ($shop_id == 2) {
            $file_name = "LAZADA-".date('yMd_His').'.csv';
            Excel::store(new OrdersLazadaExport($dataExport, "Sheet1"), $file_name, 'orders', \Maatwebsite\Excel\Excel::CSV);
        } else if ($shop_id == 4) {
            $file_name = "TIKTOK-".date('yMd_His').'.xls';
            Excel::store(new OrdersTiktokExport($dataExport, "Sheet1"), $file_name, 'orders');
        }
        return response()->json(['success' => true, 'message' => 'เรียบร้อย', 'orders_canceled' => $orderCanceled, 'file_name' => $file_name]);
    }

    public function downloadFile($file_name)
    {
        self::deleteFileHistory();
        return Storage::disk('orders')->download($file_name);
    }

    public function fileHistory()
    {
        $disk = Storage::disk('orders');
        $files = $disk->files();
        $fileDataShopee = collect();
        $fileDataLazada = collect();
        $fileDataTiktok = collect();
        foreach($files as $file) {
            if (strpos($file, "SHOPEE") !== false) {
                $fileDataShopee->push([
                    'file' => $file,
                    'date' => $disk->lastModified($file)
                ]);
            } else if (strpos($file, "LAZADA") !== false) {
                $fileDataLazada->push([
                    'file' => $file,
                    'date' => $disk->lastModified($file)
                ]);
            } else if (strpos($file, "TIKTOK") !== false) {
                $fileDataTiktok->push([
                    'file' => $file,
                    'date' => $disk->lastModified($file)
                ]);
            }
        }
        $newest = $fileDataShopee->sortByDesc('date')->take(15);
        $list_shopee = [];
        foreach ($newest as $file) {
            $list_shopee[] = ["file_name"=>$file['file'], "file_date"=>date('Y-m-d H:i:s', $file['date'])];
        }
        $newest = $fileDataLazada->sortByDesc('date')->take(15);
        $list_lazada = [];
        foreach ($newest as $file) {
            $list_lazada[] = ["file_name"=>$file['file'], "file_date"=>date('Y-m-d H:i:s', $file['date'])];
        }
        $newest = $fileDataTiktok->sortByDesc('date')->take(15);
        $list_tiktok = [];
        foreach ($newest as $file) {
            $list_tiktok[] = ["file_name"=>$file['file'], "file_date"=>date('Y-m-d H:i:s', $file['date'])];
        }
        return response()->json(['success' => true, 'message' => 'เรียบร้อย', 'shopee' => $list_shopee, 'lazada' => $list_lazada, 'tiktok' => $list_tiktok]);
    }

    public function deleteFileHistory()
    {
        $disk = Storage::disk('orders');
        $files = $disk->files();
        if (count($files) > 100) {
            $fileData = collect();
            foreach ($files as $file) {
                $fileData->push([
                    'file' => $file,
                    'date' => $disk->lastModified($file)
                ]);
            }
            $i = 0;
            $oldest = $fileData->sortByDesc('date');
            foreach ($oldest as $file) {
                if (Storage::exists('public/orders/'.$file['file']) && $i >= 100){
                    Storage::delete('public/orders/'.$file['file']);
                }
                $i++;
            }
        }
    }

    public function destroy(Request $request)
    {
        if ($request->id > 0) {
            $chk = Order::find($request->id);
            if (!auth()->user()->roleAdmin()) {
                if ($chk->status != 1) {
                    return response()->json(['success' => false, 'message' => 'ไม่สามารถลบข้อมูลได้!']);
                }
            }
            Order::find($request->id)->delete();
            return response()->json(['success' => true, 'message' => 'ลบข้อมูลเรียบร้อย']);
        }
    }

    public function show($id)
    {
        $order = Order::find($id);
        $order_package_items = OrderItem::where('order_id', '=', $order->id)->groupBy('tracking_number')->orderBy('package_number', 'asc')
        ->get(['*', DB::raw('SUM(quantity) as package_total_quantity')]);
        $order_items = OrderItem::where('order_id', '=', $order->id)->orderBy('item', 'asc')->get();
        $order_logs = self::getOrderLog($order->id);
        $eshop = Eshop::find($order->shop_id);
        $shipping_address = self::convertShippingAddress($order->shop_id, $order->shipping_address);
        $status = self::orderStatus($order->status);
        return view('middleware.orders.order-show', compact('order', 'order_package_items', 'order_items', 'order_logs', 'eshop', 'shipping_address', 'status'));
    }

    public function getOrderLog($id)
    {
        $data = OrderLog::from('order_logs as l')->leftJoin('kacee_center.employee as e', 'l.user_by', '=', 'e.emp_id')
        ->where('l.order_id', '=', $id)->orderBy('l.id', 'asc')
        ->select('l.*', 'e.name', 'e.surname', 'e.image')->get();
        return $data;
    }

    public function loadOrders()
    {
        $eshop = Eshop::where('status', '<>', 0)->whereIn('id', [1,2])->get();
        return view('middleware.orders.order-get')->with('eshop', $eshop);
    }

    public function getOrders(Request $request)
    {
        if ($request->ajax()) {
            $shop_list = $request->shop;
            $date_start = ($request->date_start) ? Carbon::createFromFormat('d/m/Y H:i', $request->date_start)->format('Y-m-d H:i').':00' : "";
            $date_end = ($request->date_end) ? Carbon::createFromFormat('d/m/Y H:i', $request->date_end)->format('Y-m-d H:i').':59' : "";

            $orders_total = 0;
            $data = array();

            for ($shop=0; $shop < count($shop_list); $shop++) {
                $eshop = Eshop::find($shop_list[$shop]);
                $eplatform = Eplatform::find($eshop->platform_id);
                if ($eplatform->id == 1) {
                    // Shopee
                    $orders_list = [];
                    $orders = self::callShopeeOrdersProcessed($orders_list, $date_start, $date_end, 100, ""); //100
                    if ($orders["code"] != "") {
                        return response()->json(["success"=>false, "message"=>$orders["message"]]);
                    }
                    $orders = self::callShopeeOrdersReadyToShip($orders_list, $date_start, $date_end, 100, ""); //100
                    if ($orders["code"] != "") {
                        return response()->json(["success"=>false, "message"=>$orders["message"]]);
                    }

                    // Check orders duplicate
                    $check_orders = Order::where('shop_id', '=', $eshop->id)->whereBetween('create_time', [$date_start, $date_end])->pluck('order_number')->toArray();
                    $orders_list_duplicate = [];
                    if (count($orders_list) && count($check_orders)) {
                        foreach ($orders_list as $list) {
                            if (!in_array($list, $check_orders)) {
                                array_push($orders_list_duplicate, $list);
                            }
                        }
                        $orders_list = $orders_list_duplicate;
                    }

                    if (count($orders_list)) {
                        $orders_total += count($orders_list);
                        $chunk_array = array_chunk($orders_list, 50, true);
                        foreach ($chunk_array as $row) {
                            $order_sn_list = implode(",", $row);
                            $parameters = [
                                "order_sn_list" => $order_sn_list,
                                "response_optional_fields" => "item_list,recipient_address,payment_method,total_amount,estimated_shipping_fee,actual_shipping_fee,buyer_username,invoice_data,package_list",
                            ];
                            $details = $this->shopeeController->callApiV2('/api/v2/order/get_order_detail', $this->shopeeId, $parameters, 'GET'); //50
                            if ($details->error == "") {
                                $data_items = array();
                                $details_count = count($details->response->order_list);
                                for ($j = 0; $j < $details_count; $j++) {
                                    $detail_list = $details->response->order_list[$j];
                                    $items_count = count($detail_list->item_list);
                                    $package_count = count($detail_list->package_list);
                                    $d = 0;
                                    for ($k = 0; $k < $items_count; $k++) {
                                        $item_list = $detail_list->item_list[$k];
                                        $on = trim($detail_list->order_sn);
                                        $item_sku = ($item_list->model_sku != "") ? trim($item_list->model_sku) : trim($item_list->item_sku);
                                        $shipping_fee = ($d == 0) ? $detail_list->actual_shipping_fee : 0;

                                        $package_number = "";
                                        $tracking_number = "";
                                        for ($l = 0; $l < $package_count; $l++) {
                                            $package_item_list = $detail_list->package_list[$l]->item_list;
                                            $package_item_count = count($package_item_list);
                                            for ($m = 0; $m < $package_item_count; $m++) {
                                                if ($item_list->item_id == $package_item_list[$m]->item_id && $item_list->model_id == $package_item_list[$m]->model_id) {
                                                    $package_number = $detail_list->package_list[$l]->package_number;
                                                    break;
                                                }
                                            }
                                        }

                                        $data_items[$on][$d]["order_item_id"] = "";
                                        $data_items[$on][$d]["sku_id"] = trim($item_list->item_id);
                                        $data_items[$on][$d]["shop_sku"] = "";
                                        $data_items[$on][$d]["sku"] = trim($item_sku);
                                        $data_items[$on][$d]["name"] = trim($item_list->item_name);
                                        $data_items[$on][$d]["variation"] = trim($item_list->model_name);
                                        $data_items[$on][$d]["original_price"] = self::calcRound2Decimal($item_list->model_original_price);
                                        $data_items[$on][$d]["sale_price"] = self::calcRound2Decimal($item_list->model_discounted_price);
                                        $data_items[$on][$d]["shipping_fee"] = self::calcRound2Decimal(0);
                                        $data_items[$on][$d]["discount"] = self::calcRound2Decimal(0);
                                        $data_items[$on][$d]["addon"] = 0;
                                        $data_items[$on][$d]["addon_desc"] = "";
                                        $data_items[$on][$d]["quantity"] = (int)$item_list->model_quantity_purchased;
                                        $data_items[$on][$d]["package_number"] = trim($package_number);
                                        $data_items[$on][$d]["tracking_number"] = trim($tracking_number);
                                        $data_items[$on][$d]["status"] = 1;
                                        $d++;
                                    }
                                    self::callShopeeToList($data, $detail_list, $data_items);
                                }
                            }
                        }
                    }
                } else if ($eplatform->id == 2) {
                    // Lazada
                    $start_date = Carbon::createFromFormat('Y-m-d H:i:s', $date_start)->format('c');
                    $end_date = Carbon::createFromFormat('Y-m-d H:i:s', $date_end)->format('c');

                    $orders_list = array();
                    $orders = self::callLazadaOrders($orders_list, $start_date, $end_date, 0, 100); //100
                    if ($orders["code"] != "0") {
                        return response()->json(["success"=>false, "message"=>$orders["message"]]);
                    }

                    // Check orders duplicate
                    $check_orders = Order::where('shop_id', '=', $eshop->id)->whereBetween('create_time', [$date_start, $date_end])->pluck('order_id')->toArray();
                    $orders_list_duplicate = [];
                    if (count($orders_list) && count($check_orders)) {
                        foreach ($orders_list as $list) {
                            if (!in_array($list["order_id"], $check_orders)) {
                                array_push($orders_list_duplicate, $list);
                            }
                        }
                        $orders_list = $orders_list_duplicate;
                    }

                    if (count($orders_list)) {
                        $orders_total += count($orders_list);
                        $chunk_array = array_chunk($orders_list, 50, true); //50
                        foreach ($chunk_array as $row) {
                            $order_ids = "";
                            foreach ($row as $sub_row) {
                                $order_ids .= ($order_ids != "") ? ",".$sub_row["order_id"] : $sub_row["order_id"];
                            }
                            $parameters = array(
                                "order_ids" => "[" . $order_ids . "]",
                            );
                            $items = $this->lazadaController->callApi('/orders/items/get', $this->lazadaId, $parameters); // limit 50
                            if ($items["code"] != "0") {
                                return response()->json(["success"=>false, "message"=>$items["message"]]);
                            }
                            $data_items = array();
                            $orders_count = count($items["data"]);
                            for ($i = 0; $i < $orders_count; $i++) {
                                $items_count = count($items["data"][$i]["order_items"]);
                                $d = 0;
                                for ($j = 0; $j < $items_count; $j++) {
                                    $on = trim($items["data"][$i]["order_number"]);
                                    $data_items[$on][$d]["order_item_id"] = trim($items["data"][$i]["order_items"][$j]["order_item_id"]);
                                    $data_items[$on][$d]["sku_id"] = trim($items["data"][$i]["order_items"][$j]["sku_id"]);
                                    $data_items[$on][$d]["shop_sku"] = trim($items["data"][$i]["order_items"][$j]["shop_sku"]);
                                    $data_items[$on][$d]["sku"] = trim($items["data"][$i]["order_items"][$j]["sku"]);
                                    $data_items[$on][$d]["name"] = trim($items["data"][$i]["order_items"][$j]["name"]);
                                    $data_items[$on][$d]["variation"] = trim($items["data"][$i]["order_items"][$j]["variation"]);
                                    $data_items[$on][$d]["original_price"] = self::calcRound2Decimal(trim($items["data"][$i]["order_items"][$j]["item_price"]));
                                    $data_items[$on][$d]["sale_price"] = self::calcRound2Decimal((trim($items["data"][$i]["order_items"][$j]["paid_price"]) + trim($items["data"][$i]["order_items"][$j]["voucher_platform"])));
                                    $data_items[$on][$d]["shipping_fee"] = self::calcRound2Decimal(trim($items["data"][$i]["order_items"][$j]["shipping_amount"]));
                                    $data_items[$on][$d]["discount"] = self::calcRound2Decimal(trim($items["data"][$i]["order_items"][$j]["voucher_amount"]));
                                    $data_items[$on][$d]["addon"] = 0;
                                    $data_items[$on][$d]["addon_desc"] = "";
                                    $data_items[$on][$d]["quantity"] = 1;
                                    $data_items[$on][$d]["package_number"] = trim($items["data"][$i]["order_items"][$j]["package_id"]);
                                    $data_items[$on][$d]["tracking_number"] = trim($items["data"][$i]["order_items"][$j]["tracking_code"]);
                                    $data_items[$on][$d]["status"] = (trim($items["data"][$i]["order_items"][$j]["status"]) == "canceled") ? 0 : 1;
                                    $d++;
                                }
                            }
                            self::callLazadaToList($data, $row, $data_items);
                        }
                    }
                } else if ($eplatform->id == 4) {
                    // Tiktok
                    $start_date = Carbon::createFromFormat('Y-m-d H:i:s', $date_start)->timestamp;
                    $end_date = Carbon::createFromFormat('Y-m-d H:i:s', $date_end)->timestamp;

                    $orders_list = array();
                    $orders = self::callTiktokOrders($orders_list, $start_date, $end_date, "", 100); //100
                    if ($orders["code"] != "0") {
                        return response()->json(["success"=>false, "message"=>$orders["message"]]);
                    }

                    // Check orders duplicate
                    $check_orders = Order::where('shop_id', '=', $eshop->id)->whereBetween('create_time', [$date_start, $date_end])->pluck('order_number')->toArray();
                    $orders_list_duplicate = [];
                    if (count($orders_list) && count($check_orders)) {
                        foreach ($orders_list as $_order_id) {
                            if (!in_array($_order_id, $check_orders)) {
                                array_push($orders_list_duplicate, $_order_id);
                            }
                        }
                        $orders_list = $orders_list_duplicate;
                    }

                    if (count($orders_list)) {
                        $orders_total += count($orders_list);
                        $chunk_array = array_chunk($orders_list, 50, true); //50
                        foreach ($chunk_array as $row) {
                            $parameters = [
                                "order_id_list" => $row,
                            ];
                            $items = $this->tiktokController->callApiV2('/api/orders/detail/query', $this->tiktokId, $parameters, 'POST');
                            if ($items->code != 0) {
                                return response()->json(["success"=>false, "message"=>$items->message]);
                            }
                            $data_items = array();
                            $orders_count = count($items->data->order_list);
                            for ($i = 0; $i < $orders_count; $i++) {
                                $items_count = count($items->data->order_list[$i]->item_list);
                                $d = 0;
                                for ($j = 0; $j < $items_count; $j++) {
                                    $on = trim($items->data->order_list[$i]->order_id);
                                    $shipping_fee = ($d == 0) ? $items->data->order_list[$i]->payment_info->shipping_fee : 0;

                                    $data_items[$on][$d]["order_item_id"] = "";
                                    $data_items[$on][$d]["sku_id"] = trim($items->data->order_list[$i]->item_list[$j]->sku_id);
                                    $data_items[$on][$d]["shop_sku"] = "";
                                    $data_items[$on][$d]["sku"] = trim($items->data->order_list[$i]->item_list[$j]->seller_sku);
                                    $data_items[$on][$d]["name"] = trim($items->data->order_list[$i]->item_list[$j]->product_name);
                                    $data_items[$on][$d]["variation"] = trim($items->data->order_list[$i]->item_list[$j]->sku_name);
                                    $data_items[$on][$d]["original_price"] = self::calcRound2Decimal($items->data->order_list[$i]->item_list[$j]->sku_original_price);
                                    // $data_items[$on][$d]["sale_price"] = self::calcRound2Decimal($items->data->order_list[$i]->item_list[$j]->sku_sale_price);
                                    $data_items[$on][$d]["sale_price"] = self::calcRound2Decimal($items->data->order_list[$i]->item_list[$j]->sku_original_price - $items->data->order_list[$i]->item_list[$j]->sku_seller_discount);
                                    $data_items[$on][$d]["shipping_fee"] = self::calcRound2Decimal($shipping_fee);
                                    // $data_items[$on][$d]["discount"] = self::calcRound2Decimal($items->data->order_list[$i]->item_list[$j]->sku_platform_discount + $items->data->order_list[$i]->item_list[$j]->sku_seller_discount);
                                    $data_items[$on][$d]["discount"] = self::calcRound2Decimal($items->data->order_list[$i]->item_list[$j]->sku_seller_discount);
                                    $data_items[$on][$d]["addon"] = 0;
                                    $data_items[$on][$d]["addon_desc"] = "";
                                    // $data_items[$on][$d]["addon"] = isset($items->data->order_list[$i]->item_list[$j]->sku_small_order_fee) ? self::calcRound2Decimal($items->data->order_list[$i]->item_list[$j]->sku_small_order_fee) : 0;
                                    // $data_items[$on][$d]["addon_desc"] = isset($items->data->order_list[$i]->item_list[$j]->sku_small_order_fee) ? "ค่าธรรมเนียมคำสั่งซื้อขนาดเล็ก" : "";
                                    $data_items[$on][$d]["quantity"] = $items->data->order_list[$i]->item_list[$j]->quantity;
                                    $data_items[$on][$d]["package_number"] = "";
                                    $data_items[$on][$d]["tracking_number"] = "";
                                    $data_items[$on][$d]["status"] = (trim($items->data->order_list[$i]->item_list[$j]->sku_display_status) == 140) ? 0 : 1;
                                    $d++;
                                }
                                self::callTiktokToList($data, $items->data->order_list[$i], $data_items);
                            }
                        }
                    }
                }
            }
            if ($orders_total <= 0) {
                return response()->json(["success"=>false, "message"=>"ไม่พบคำสั่งซื้อ"]);
            }
            $sum_orders_total = 0;
            $sum_packages_total = 0;
            if (isset($data["orders"])) {
                if (count($data["orders"])) {
                    DB::beginTransaction();
                    try {
                        foreach ($data["orders"] as $order) {
                            $mwOrders = Order::firstOrNew([
                                "order_number" => $order['order_number'],
                                "shop_id" => $order['shop_id']
                            ]);
                            $order_exists = $mwOrders->exists;
                            if (!$order_exists) {
                                $mwOrders->order_number = $order['order_number'];
                                $mwOrders->order_id = $order['order_id'];
                                $mwOrders->create_time = $order['create_time'];
                                $mwOrders->shop_id = $order['shop_id'];
                                $mwOrders->total_amount = $order['total_amount'];
                                $mwOrders->total_quantity = $order['total_quantity'];
                                $mwOrders->total_shipping_fee = $order['total_shipping_fee'];
                                $mwOrders->total_discount = $order['total_discount'];
                                $mwOrders->total_addon = $order['total_addon'];
                                $mwOrders->items_count = $order['items_count'];
                                $mwOrders->package_count = $order['package_count'];
                                $mwOrders->payment_method = $order['payment_method'];
                                $mwOrders->customer_name = $order['customer_name'];
                                $mwOrders->billing_address = $order['billing_address'];
                                $mwOrders->shipping_address = $order['shipping_address'];
                                $mwOrders->tax_invoice_requested = $order['tax_invoice_requested'];
                                $mwOrders->tax_invoice_info = $order['tax_invoice_info'];
                                $mwOrders->order_status = $order['order_status'];
                                $mwOrders->status = 1;
                                $mwOrders->created_by = auth()->user()->emp_id;
                                $mwOrders->save();

                                $itemNo = 1;
                                foreach ($order["items"] as $item) {
                                    $mwOrderItem = new OrderItem();
                                    $mwOrderItem->order_id = $mwOrders->id;
                                    $mwOrderItem->item = $itemNo;
                                    $mwOrderItem->order_item_id = $item['order_item_id'];
                                    $mwOrderItem->sku_id = $item['sku_id'];
                                    $mwOrderItem->shop_sku = $item['shop_sku'];
                                    $mwOrderItem->sku = $item['sku'];
                                    $mwOrderItem->name = $item['name'];
                                    $mwOrderItem->variation = $item['variation'];
                                    $mwOrderItem->original_price = $item['original_price'];
                                    $mwOrderItem->sale_price = $item['sale_price'];
                                    $mwOrderItem->shipping_fee = $item['shipping_fee'];
                                    $mwOrderItem->discount = $item['discount'];
                                    $mwOrderItem->quantity = $item['quantity'];
                                    $mwOrderItem->addon = $item['addon'];
                                    $mwOrderItem->addon_desc = $item['addon_desc'];
                                    $mwOrderItem->package_number = $item['package_number'];
                                    $mwOrderItem->tracking_number = $item['tracking_number'];
                                    $mwOrderItem->status = $item['status'];
                                    $mwOrderItem->save();
                                    $itemNo++;
                                }

                                $mwOrderLog = new OrderLog();
                                $mwOrderLog->order_id = $mwOrders->id;
                                $mwOrderLog->title = "โหลดข้อมูลคำสั่งซื้อ";
                                $mwOrderLog->description = "โหลดข้อมูลคำสั่งซื้อจากร้านค้าออนไลน์";
                                $mwOrderLog->user_by = auth()->user()->emp_id;
                                $mwOrderLog->ip_address = $request->ip();
                                $mwOrderLog->save();

                                $sum_orders_total++;
                                $sum_packages_total += (int)$order['package_count'];
                            }
                        }

                        // all good
                        DB::commit();
                    } catch (\Exception $e) {
                        // something went wrong
                        DB::rollback();
                        return response()->json(["success"=>false, "message"=>'เกิดข้อผิดพลาด!'.$e]);
                    }
                }
            }
            unset($data);
            return response()->json(["success"=>true, "message"=>"เรียบร้อย", "orders_total"=>$orders_total, "sum_orders_total"=>$sum_orders_total, "sum_packages_total"=>$sum_packages_total]);
        }
    }

    // ---------------------------------------- SHOPEE -----------------------------------------

    public function callShopeeOrdersProcessed(& $orders_list, $date_start, $date_end, $page_size, $cursor)
    {
        $parameters = [
            "time_range_field" => "create_time",
            "time_from" => strtotime("$date_start"),
            "time_to" => strtotime("$date_end"),
            "page_size" => $page_size,
            "cursor" => $cursor,
            "order_status" => "PROCESSED",
            "response_optional_fields" => "order_status",
        ];
        $orders = $this->shopeeController->callApiV2('/api/v2/order/get_order_list', $this->shopeeId, $parameters, 'GET');
        if ($orders->error == "") {
            $cursor = ($orders->response->more==true) ? $orders->response->next_cursor : "";
            $orders_count = count($orders->response->order_list);
            for ($i = 0; $i < $orders_count; $i++) {
                $order_list = $orders->response->order_list[$i];
                array_push($orders_list, $order_list->order_sn);
            }
            if ($cursor != "") {
                self::callShopeeOrdersProcessed($orders_list, $date_start, $date_end, $page_size, $cursor);
            }
        } else {
            return ["code"=>$orders->error, "message"=>$orders->message];
        }
        return ["code"=>"", "message"=>"", "orders"=>$orders_list];
    }

    public function callShopeeOrdersReadyToShip(& $orders_list, $date_start, $date_end, $page_size, $cursor)
    {
        $parameters = [
            "time_range_field" => "create_time",
            "time_from" => strtotime("$date_start"),
            "time_to" => strtotime("$date_end"),
            "page_size" => $page_size,
            "cursor" => $cursor,
            "order_status" => "READY_TO_SHIP",
            "response_optional_fields" => "order_status",
        ];
        $orders = $this->shopeeController->callApiV2('/api/v2/order/get_order_list', $this->shopeeId, $parameters, 'GET');
        if ($orders->error == "") {
            $cursor = ($orders->response->more==true) ? $orders->response->next_cursor : "";
            $orders_count = count($orders->response->order_list);
            for ($i = 0; $i < $orders_count; $i++) {
                $order_list = $orders->response->order_list[$i];
                array_push($orders_list, $order_list->order_sn);
            }
            if ($cursor != "") {
                self::callShopeeOrdersReadyToShip($orders_list, $date_start, $date_end, $page_size, $cursor);
            }
        } else {
            return ["code"=>$orders->error, "message"=>$orders->message];
        }
        return ["code"=>"", "message"=>"", "orders"=>$orders_list];
    }

    public function callShopeeToList(& $data, $order, $items)
    {
        $taxInvoiceRequested = 0;
        $tax_invoice_info = [];
        // $ivparameters = [
        //     "queries" => [['order_sn' => trim($order->order_sn)]],
        // ];
        // $invoice = $this->shopeeController->callApiV2('/api/v2/order/get_buyer_invoice_info', $this->shopeeId, $ivparameters, 'POST');
        // if ($invoice->error == "") {
        //     for ($k = 0; $k < count($invoice->invoice_info_list); $k++) {
        //         if ($invoice->invoice_info_list[$k]->is_requested == true) {
        //             $taxInvoiceRequested = 1;
        //             $tax_invoice_info = [
        //                 "invoice_type" => $invoice->invoice_info_list[$k]->invoice_type,
        //                 "invoice_detail" => $invoice->invoice_info_list[$k]->invoice_detail,
        //             ];
        //         }
        //     }
        // }
        $total_shipping_fee = 0;
        $total_discount = 0;
        $parameters = [
            "order_sn" => trim($order->order_sn)
        ];
        $detail = $this->shopeeController->callApiV2('/api/v2/payment/get_escrow_detail', $this->shopeeId, $parameters, 'GET');
        if ($detail->error == "") {
            $total_shipping_fee = $detail->response->order_income->buyer_paid_shipping_fee;
            $total_discount = $detail->response->order_income->voucher_from_seller + $detail->response->order_income->voucher_from_shopee;
            for ($i=0; $i<count($detail->response->order_income->items); $i++) {
                for ($j=0; $j<count($items[trim($order->order_sn)]); $j++) {
                    if ($items[trim($order->order_sn)][$j]["sku_id"] == $detail->response->order_income->items[$j]->item_id) {
                        $items[trim($order->order_sn)][$j]["shipping_fee"] = ($i == 0) ? self::calcRound2Decimal($detail->response->order_income->buyer_paid_shipping_fee) : 0;
                        $items[trim($order->order_sn)][$j]["discount"] = self::calcRound2Decimal($detail->response->order_income->items[$j]->discount_from_voucher_seller + $detail->response->order_income->items[$j]->discount_from_voucher_shopee);
                    }
                }
            }
        }
        $total_quantity = 0;
        for ($i=0; $i<count($items[trim($order->order_sn)]); $i++) {
            $total_quantity += (int)$items[trim($order->order_sn)][$i]["quantity"];
        }

        $_istracking = 0;
        $package_list = self::unique_array($items[trim($order->order_sn)], "package_number");
        $package_list = array_values($package_list);
        $package_count = count($package_list);
        for ($i=0; $i<$package_count; $i++) {
            if ($package_list[$i]['package_number'] != "") {
                $parameters = [
                    "order_sn" => trim($order->order_sn),
                    "package_number" => $package_list[$i]['package_number'],
                ];
            } else {
                $parameters = [
                    "order_sn" => trim($order->order_sn),
                ];
            }
            $tracking = $this->shopeeController->callApiV2('/api/v2/logistics/get_tracking_number', $this->shopeeId, $parameters, 'GET');
            if ($tracking->error == "") {
                for ($j=0; $j<count($items[trim($order->order_sn)]); $j++) {
                    if ($items[trim($order->order_sn)][$j]["package_number"] == $package_list[$i]['package_number']) {
                        $items[trim($order->order_sn)][$j]["tracking_number"] = $tracking->response->tracking_number;
                        if (strlen($tracking->response->tracking_number) > 5) {
                            $_istracking++;
                        }
                    }
                }
            }
        }
        if ($_istracking > 0) {
            // ออเดอร์ที่มี tracking number เท่านั้น
            $data["orders"][] = [
                "order_number" => trim($order->order_sn),
                "order_id" => "",
                "create_time" => Carbon::createFromTimestamp(trim($order->create_time))->format('Y-m-d H:i:s'),
                "shop_id" => 1,
                "total_amount" => self::calcRound2Decimal($order->total_amount),
                "total_quantity" => self::calcRound($total_quantity),
                "items_count" => self::calcRound(count($items[trim($order->order_sn)])),
                "package_count" => self::calcRound($package_count),
                "total_shipping_fee" => self::calcRound2Decimal($total_shipping_fee),
                "total_discount" => self::calcRound2Decimal($total_discount),
                "total_addon" => 0,
                "payment_method" => trim($order->payment_method),
                "customer_name" => $order->buyer_username,
                "billing_address" => null,
                "shipping_address" => $order->recipient_address,
                "tax_invoice_requested" => $taxInvoiceRequested,
                "tax_invoice_info" => count($tax_invoice_info) ? $tax_invoice_info : null,
                "order_status" => trim($order->order_status),
                "items" => $items[trim($order->order_sn)]
            ];
        }
        return $data;
    }

    // public function callShopeeSetStatusToProcessed($orders)
    // {
    //     foreach ($orders as $order) {
    //         $parameters = [
    //             "order_sn" => $order->order_number,
    //         ];
    //         $shipping = $this->shopeeController->callApiV2('/api/v2/logistics/get_shipping_parameter', $this->shopeeId, $parameters, 'GET');
    //         if ($shipping->error == "") {
    //             $parameters = [
    //                 "order_sn" => $order->order_number,
    //             ];
    //             $ship = $this->shopeeController->callApiV2('/api/v2/logistics/ship_order', $this->shopeeId, $parameters, 'GET');
    //             if ($ship->error == "") {
    //                 $parameters = [
    //                     "order_sn" => $order->order_number,
    //                 ];
    //                 $tracking = $this->shopeeController->callApiV2('/api/v2/logistics/get_tracking_number', $this->shopeeId, $parameters, 'GET');
    //                 if ($tracking->error == "") {
    //                     $tracking_number = $tracking->response->tracking_number;

    //                     $uporder = Order::where("order_number", '=', $order->order_number)->where("shop_id", '=', 1)->first();
    //                     $uporder->update(['tracking_number' => $tracking_number, 'status' => 2, 'updated_by' => auth()->user()->emp_id]);

    //                     $orderLog = new OrderLog();
    //                     $orderLog->order_id = $uporder->id;
    //                     $orderLog->title = "อัปเดตและส่งออก";
    //                     $orderLog->description = "อัปเดตสถานะคำสั่งซื้อและส่งออกไฟล์";
    //                     $orderLog->user_by = auth()->user()->emp_id;
    //                     $orderLog->ip_address = \Request::ip();
    //                     $orderLog->save();
    //                 }
    //             }
    //         }
    //     }
    // }

    public function callShopeeCheckCanceledStatus($dataExport)
    {
        $order_canceled = [];
        $chunk_array = array_chunk($dataExport, 50, true); //50
        foreach ($chunk_array as $row) {
            $order_ids = [];
            foreach ($row as $sub_row) {
                $order_ids[] = $sub_row["order_number"];
            }
            $order_sn_list = implode(",", $order_ids);
            $parameters = [
                "order_sn_list" => $order_sn_list,
            ];
            $details = $this->shopeeController->callApiV2('/api/v2/order/get_order_detail', $this->shopeeId, $parameters, 'GET'); //50
            if ($details->error != "") {
                return ["code"=>$details->error, "message"=>$details->message];
            }
            $details_count = count($details->response->order_list);
            for ($i = 0; $i < $details_count; $i++) {
                $detail_list = $details->response->order_list[$i];
                if ($detail_list->order_status == "CANCELLED") {
                    array_push($order_canceled, trim($detail_list->order_sn));
                }
            }
        }
        $order_canceled = array_unique($order_canceled);
        return ["code"=>"", "message"=>"", "orders"=>$order_canceled];
    }

    // ---------------------------------------- LAZADA -----------------------------------------

    public function callLazadaOrders(& $orders_list, $start_date, $end_date, $offset, $limit)
    {
        $parameters = array(
            "sort_direction" => 'DESC',
            "offset" => $offset,
            "limit" => $limit,
            "sort_by" => 'updated_at',
            "created_before" => $end_date, // 2020-03-29T23:59:59+07:00
            "created_after" => $start_date, // 2020-03-28T00:00:00+07:00
            "status" => "packed",
        );
        $orders = $this->lazadaController->callApi('/orders/get', $this->lazadaId, $parameters);
        if ($orders["code"] == "0") {
            $orders_count = $orders["data"]["count"];
            $orders_data = $orders["data"]["orders"];
            for ($i = 0; $i < $orders_count; $i++) {
                array_push($orders_list, $orders_data[$i]);
            }
            if ($orders_count > 0) {
                $_offset = ($offset + $limit);
                self::callLazadaOrders($orders_list, $start_date, $end_date, $_offset, $limit);
            }
        } else {
            return ["code"=>$orders["code"], "message"=>$orders["message"]];
        }
        return ["code"=>"0", "message"=>"", "orders"=>$orders_list];
    }

    public function callLazadaToList(& $data, $orders, $items)
    {
        foreach ($orders as $order) {
            $tax_invoice_info = [];
            $extra_attributes = json_decode($order["extra_attributes"]);
            if (isset($extra_attributes->TaxInvoiceRequested)) {
                $taxInvoiceRequested = ($extra_attributes->TaxInvoiceRequested) ? 1 : 0;
                if ($taxInvoiceRequested == 1) {
                    $tax_invoice_info = [
                        "invoice_type" => (trim($order["branch_number"]) != "") ? 2 : 1, //1: personal, 2: company
                        "invoice_detail" => [
                            "branch_number" => trim($order["branch_number"]),
                            "tax_code" => trim($order["tax_code"]),
                            "address" => $order["address_billing"],
                        ],
                    ];
                }
            } else {
                $taxInvoiceRequested = 0;
            }
            $customer_name = trim($order["customer_first_name"]);
            if (strlen(trim($order["customer_last_name"])) > 1) {
                $customer_name .= " ".trim($order["customer_last_name"]);
            }
            $package_count = count(self::unique_array($items[trim($order["order_number"])], "package_number"));
            $data["orders"][] = [
                "order_number" => trim($order["order_number"]),
                "order_id" => trim($order["order_id"]),
                "create_time" => Carbon::parse(trim($order["created_at"]))->format('Y-m-d H:i:s'),
                "shop_id" => 2,
                "total_amount" => self::calcRound2Decimal(((float)$order["price"] - $order["voucher"]) + $order["shipping_fee"]),
                "total_quantity" => self::calcRound($order["items_count"]),
                "items_count" => self::calcRound(count($items[trim($order["order_number"])])),
                "package_count" => self::calcRound($package_count),
                "total_shipping_fee" => self::calcRound2Decimal($order["shipping_fee"]),
                "total_discount" => self::calcRound2Decimal($order["voucher"]),
                "total_addon" => 0,
                "payment_method" => trim($order["payment_method"]),
                "customer_name" => $customer_name,
                "billing_address" => $order["address_billing"],
                "shipping_address" => $order["address_shipping"],
                "tax_invoice_requested" => $taxInvoiceRequested,
                "tax_invoice_info" => count($tax_invoice_info) ? $tax_invoice_info : null,
                "order_status" => trim($order["statuses"][0]),
                "items" => $items[trim($order["order_number"])]
            ];
        }
        return $data;
    }

    // public function callLazadaSetStatusToPackedByMarketplace($orders)
    // {
    //     $order_ids = "";
    //     foreach ($orders as $order) {
    //         $order_ids .= ($order_ids != "") ? ",".$order->order_id : $order->order_id;
    //     }
    //     $parameters = array(
    //         // "shipping_provider" => 'Aramax',
    //         // "delivery_type" => 'dropship',
    //         // "order_item_ids" => '[1530553,1830236]',
    //         "order_item_ids" => "[" . $order_ids . "]",
    //     );
    //     $orders = $this->lazadaController->callApi('/order/pack', $this->lazadaId, $parameters);
    //     if ($orders["code"] == "0") {
    //         $orders_data = $orders["data"]["order_items"];
    //         $orders_count = count($orders_data);
    //         DB::beginTransaction();
    //         try {
    //             $dataLogs = [];
    //             for ($i = 0; $i < $orders_count; $i++) {
    //                 $uporder = Order::where("order_item_id", '=', $orders_data[$i]['order_item_id'])->where("shop_id", '=', 2)->first();
    //                 $uporder->update(['tracking_number' => $orders_data[$i]['tracking_number'], 'status' => 2, 'updated_by' => auth()->user()->emp_id]);
    //                 $dataLogs[] = [
    //                     "order_id" => $uporder->id,
    //                     "title" => "อัปเดตและส่งออก",
    //                     "description" => "อัปเดตสถานะคำสั่งซื้อและส่งออกไฟล์",
    //                     "user_by" => auth()->user()->emp_id,
    //                     "ip_address" => \Request::ip(),
    //                     "created_at" => now(),
    //                     "updated_at" => now(),
    //                 ];
    //             }
    //             OrderLog::insert($dataLogs);
    //             // all good
    //             DB::commit();
    //         } catch (\Exception $e) {
    //             // something went wrong
    //             DB::rollback();
    //             return ["code"=>"", "message"=>"เกิดข้อผิดพลาดขณะบันทึกข้อมูล!"];
    //         }
    //     } else {
    //         return ["code"=>$orders["code"], "message"=>$orders["message"]];
    //     }
    //     return ["code"=>"0", "message"=>""];
    // }

    public function callLazadaCheckCanceledStatus($dataExport)
    {
        $order_canceled = [];
        $chunk_array = array_chunk($dataExport, 50, true); //50
        foreach ($chunk_array as $row) {
            $order_ids = "";
            foreach ($row as $sub_row) {
                $order_ids .= ($order_ids != "") ? ",".$sub_row["order_id"] : $sub_row["order_id"];
            }
            $parameters = array(
                "order_ids" => "[" . $order_ids . "]",
            );
            $items = $this->lazadaController->callApi('/orders/items/get', $this->lazadaId, $parameters); // limit 50
            if ($items["code"] != "0") {
                return ["code"=>"", "message"=>$items["message"]];
            }
            $orders_count = count($items["data"]);
            for ($i = 0; $i < $orders_count; $i++) {
                $items_count = count($items["data"][$i]["order_items"]);
                $chk_order_canceled = 0;
                for ($j = 0; $j < $items_count; $j++) {
                    if ($items["data"][$i]["order_items"][$j]["status"] == "canceled") {
                        $chk_order_canceled++; // กรณียกเลิกแค่บางแพ็คเกจในออเดอร์นั้นๆ
                    }
                }
                if ($items_count == $chk_order_canceled) {
                    array_push($order_canceled, trim($items["data"][$i]["order_number"]));
                }
            }
        }
        $order_canceled = array_unique($order_canceled);
        return ["code"=>"0", "message"=>"", "orders"=>$order_canceled];
    }

    // ---------------------------------------- TIKTOK -----------------------------------------

    public function callTiktokOrders(& $orders_list, $start_date, $end_date, $cursor, $page_size)
    {
        $parameters = [
            "order_status" => 112,
            "page_size" => $page_size,
            "sort_by" => "CREATE_TIME",
            "create_time_from" => $start_date,
            "create_time_to" => $end_date,
        ];
        if ($cursor != "") {
            $parameters["cursor"] = $cursor;
        }
        $orders = $this->tiktokController->callApiV2('/api/orders/search', $this->tiktokId, $parameters, 'POST');
        if ($orders->code == 0) {
            $orders_count = count($orders->data->order_list);
            for ($i = 0; $i < $orders_count; $i++) {
                array_push($orders_list, $orders->data->order_list[$i]->order_id);
            }
            if ($orders->data->more) {
                $cursor = $orders->data->next_cursor;
                self::callTiktokOrders($orders_list, $start_date, $end_date, $cursor, $page_size);
            }
        } else {
            return ["code"=>"$orders->code", "message"=>$orders->message];
        }
        return ["code"=>"0", "message"=>"", "orders"=>$orders_list];
    }

    public function callTiktokToList(& $data, $order, $items)
    {
        $taxInvoiceRequested = 0;
        $tax_invoice_info = [];
        for ($i = 0; $i < count($order->order_line_list); $i++) {
            for ($j=0; $j<count($items[trim($order->order_id)]); $j++) {
                if ($items[trim($order->order_id)][$j]["sku_id"] == $order->order_line_list[$i]->sku_id) {
                    $items[trim($order->order_id)][$j]["tracking_number"] = trim($order->order_line_list[$i]->tracking_number);
                }
            }
        }
        for ($i=0; $i<count($order->package_list); $i++) {
            $parameters = [
                "package_id" => $order->package_list[$i]->package_id
            ];
            $package = $this->tiktokController->callApiV2('/api/fulfillment/detail', $this->tiktokId, $parameters, 'GET');
            if ($package->code == 0) {
                for ($j=0; $j<count($items[trim($order->order_id)]); $j++) {
                    if ($items[trim($order->order_id)][$j]["tracking_number"] == $package->data->tracking_number) {
                        $items[trim($order->order_id)][$j]["package_number"] = trim($package->data->package_id);
                    }
                }
            }
        }
        $total_quantity = 0;
        for ($i=0; $i<count($items[trim($order->order_id)]); $i++) {
            $total_quantity += (int)$items[trim($order->order_id)][$i]["quantity"];
        }
        $package_list = self::unique_array($items[trim($order->order_id)], "package_number");
        $package_list = array_values($package_list);
        $package_count = count($package_list);
        $data["orders"][] = [
            "order_number" => trim($order->order_id),
            "order_id" => "",
            "create_time" => Carbon::createFromTimestampMs(trim($order->create_time))->format('Y-m-d H:i:s'),
            "shop_id" => 4,
            "total_amount" => self::calcRound2Decimal(($order->payment_info->original_total_product_price + $order->payment_info->shipping_fee) - $order->payment_info->seller_discount),
            // "total_amount" => self::calcRound2Decimal($order->payment_info->total_amount),
            "total_quantity" => self::calcRound($total_quantity),
            "items_count" => self::calcRound(count($items[trim($order->order_id)])),
            "package_count" => self::calcRound($package_count),
            "total_shipping_fee" => self::calcRound2Decimal($order->payment_info->shipping_fee),
            "total_discount" => self::calcRound2Decimal($order->payment_info->seller_discount),
            // "total_discount" => self::calcRound2Decimal($order->payment_info->platform_discount + $order->payment_info->seller_discount),
            "total_addon" => 0,
            // "total_addon" => isset($order->payment_info->small_order_fee) ? self::calcRound2Decimal($order->payment_info->small_order_fee) : 0,
            "payment_method" => trim($order->payment_method_name),
            "customer_name" => $order->buyer_uid,
            "billing_address" => null,
            "shipping_address" => $order->recipient_address,
            "tax_invoice_requested" => $taxInvoiceRequested,
            "tax_invoice_info" => count($tax_invoice_info) ? $tax_invoice_info : null,
            "order_status" => trim($order->order_status),
            "items" => $items[trim($order->order_id)]
        ];
        return $data;
    }

    public function callTiktokCheckCanceledStatus($dataExport)
    {
        $order_canceled = [];
        $chunk_array = array_chunk($dataExport, 50, true); //50
        foreach ($chunk_array as $row) {
            $order_id_list = [];
            foreach ($row as $sub_row) {
                $order_id_list[] = $sub_row["order_number"];
            }
            $order_id_list = array_values(array_unique($order_id_list));
            $parameters = [
                "order_id_list" => $order_id_list,
            ];
            $items = $this->tiktokController->callApiV2('/api/orders/detail/query', $this->tiktokId, $parameters, 'POST'); // limit 50
            if ($items->code != 0) {
                return ["code"=>"", "message"=>$items->message];
            }
            $orders_count = count($items->data->order_list);
            for ($i = 0; $i < $orders_count; $i++) {
                $items_count = count($items->data->order_list[$i]->item_list);
                $chk_order_canceled = 0;
                for ($j = 0; $j < $items_count; $j++) {
                    if ($items->data->order_list[$i]->item_list[$j]->sku_display_status == 140) {
                        $chk_order_canceled++; // กรณียกเลิกแค่บางแพ็คเกจในออเดอร์นั้นๆ
                    }
                }
                if ($items_count == $chk_order_canceled) {
                    array_push($order_canceled, trim($items->data->order_list[$i]->order_id));
                }
            }
        }
        $order_canceled = array_unique($order_canceled);
        return ["code"=>"0", "message"=>"", "orders"=>$order_canceled];
    }

    // -----------------------------------------------------------------------------------------

    public function callUpdateStatus($shop_id, $order_ids, $dataExport)
    {
        if ($shop_id == 1) {
            $checkOrderCanceled = self::callShopeeCheckCanceledStatus($dataExport);
            if ($checkOrderCanceled["code"] != "") {
                return ["status"=>false, "message"=>$checkOrderCanceled["message"]];
            }
        } else if ($shop_id == 2) {
            $checkOrderCanceled = self::callLazadaCheckCanceledStatus($dataExport);
            if ($checkOrderCanceled["code"] != "0") {
                return ["status"=>false, "message"=>$checkOrderCanceled["message"]];
            }
        } else if ($shop_id == 4) {
            $checkOrderCanceled = self::callTiktokCheckCanceledStatus($dataExport);
            if ($checkOrderCanceled["code"] != "0") {
                return ["status"=>false, "message"=>$checkOrderCanceled["message"]];
            }
        }
        $orderCanceled = $checkOrderCanceled["orders"];
        DB::beginTransaction();
        try {
            $orders = Order::where('status', '=', 1)->whereIn('id', $order_ids)
            ->groupBy('order_number')->orderBy('order_number')->get(['id', 'shop_id', 'order_id', 'order_number']);
            foreach ($orders as $order) {
                $status = 2;
                if (count($orderCanceled)) {
                    $item = 0;
                    foreach ($dataExport as $data) {
                        foreach ($orderCanceled as $cancel) {
                            if ($data['order_number'] == $cancel) {
                                unset($dataExport[$item]);
                            }
                        }
                        $item++;
                    }
                    $dataExport = array_values($dataExport);
                    foreach ($orderCanceled as $cancel) {
                        if ($order->order_number == $cancel) {
                            $status = 0;
                            break;
                        }
                    }
                }
                $uporder = Order::where("id", '=', $order->id)->first();
                $uporder->update(['status' => $status, 'updated_by' => auth()->user()->emp_id]);

                $logTitle = ($status == 2) ? "อัปเดตและส่งออก" : "ยกเลิกคำสั่งซื้อ";
                $logDescription = ($status == 2) ? "อัปเดตสถานะคำสั่งซื้อและส่งออกไฟล์" : "สถานะคำสั่งซื้อถูกยกเลิก";
                $orderLog = new OrderLog();
                $orderLog->order_id = $uporder->id;
                $orderLog->title = $logTitle;
                $orderLog->description = $logDescription;
                $orderLog->user_by = auth()->user()->emp_id;
                $orderLog->ip_address = \Request::ip();
                $orderLog->save();
            }
            // all good
            DB::commit();
        } catch (\Exception $e) {
            // something went wrong
            DB::rollback();
            return ["status"=>false, "message"=>"เกิดข้อผิดพลาดขณะบันทึกข้อมูล!"];
        }
        return ["status"=>true, "message"=>"", "orders_canceled"=>implode(",",$orderCanceled), "export"=>$dataExport];
    }

    public function convertShippingAddress($shop_id, $addr)
    {
        $result = [];
        if ($shop_id == 1) {
            if (count($addr)) {
                $result = [
                    "first_name" => $addr['name'],
                    "last_name" => "",
                    "phone" => $addr['phone'],
                    "address1" => "",
                    "address2" => "",
                    "subdistrict" => "",
                    "district" => $addr['city'],
                    "province" => $addr['state'],
                    "post_code" => $addr['zipcode'],
                    "country" => $addr['region'],
                    "full_address" => $addr['full_address'],
                ];
            }
        } else if ($shop_id == 2) {
            if (count($addr)) {
                $result = [
                    "first_name" => $addr['first_name'],
                    "last_name" => $addr['last_name'],
                    "phone" => $addr['phone'],
                    "address1" => $addr['address1'],
                    "address2" => $addr['address2'],
                    "subdistrict" => "",
                    "district" => $addr['address4'],
                    "province" => $addr['address3'],
                    "post_code" => $addr['post_code'],
                    "country" => $addr['country'],
                ];
            }
        } else if ($shop_id == 4) {
            if (count($addr)) {
                $result = [
                    "first_name" => $addr['name'],
                    "last_name" => "",
                    "phone" => $addr['phone'],
                    "address1" => "",
                    "address2" => "",
                    "subdistrict" => "",
                    "district" => $addr['city'],
                    "province" => $addr['state'],
                    "post_code" => $addr['zipcode'],
                    "country" => $addr['region'],
                    "address_detail" => $addr['address_detail'],
                    "full_address" => $addr['full_address'],
                ];
            }
        }
        return $result;
    }

    public function convertInvoiceAddress($shop_id, $addr)
    {
        $result = [];
        if ($shop_id == 1) {
            if (count($addr)) {
                $result = [
                    "first_name" => $addr['name'],
                    "last_name" => "",
                    "phone" => $addr['phone'],
                    "address1" => "",
                    "address2" => "",
                    "subdistrict" => "",
                    "district" => $addr['city'],
                    "province" => $addr['state'],
                    "post_code" => $addr['zipcode'],
                    "country" => $addr['region'],
                    "full_address" => $addr['full_address'],
                ];
            }
        } else if ($shop_id == 2) {
            if (count($addr)) {
                $result = [
                    "first_name" => $addr['first_name'],
                    "last_name" => $addr['last_name'],
                    "phone" => $addr['phone'],
                    "address1" => $addr['address1'],
                    "address2" => $addr['address2'],
                    "subdistrict" => "",
                    "district" => $addr['address4'],
                    "province" => $addr['address3'],
                    "post_code" => $addr['post_code'],
                    "country" => $addr['country'],
                ];
            }
        } else if ($shop_id == 4) {
            if (count($addr)) {
                $result = [
                    "first_name" => $addr['name'],
                    "last_name" => "",
                    "phone" => $addr['phone'],
                    "address1" => "",
                    "address2" => "",
                    "subdistrict" => "",
                    "district" => $addr['city'],
                    "province" => $addr['state'],
                    "post_code" => $addr['zipcode'],
                    "country" => $addr['region'],
                    "address_detail" => $addr['address_detail'],
                    "full_address" => $addr['full_address'],
                ];
            }
        }
        return $result;
    }

    public function unique_array($my_array, $key) {
        $result = array();
        $i = 0;
        $key_array = array();
        foreach($my_array as $val) {
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i] = $val[$key];
                $result[$i] = $val;
            }
            $i++;
        }
        return $result;
    }

    public function calcRound($value)
    {
        return (int)$value;
    }

    public function calcNumberFormat($value)
    {
        return number_format((int)$value);
    }

    public function calcRound2Decimal($value)
    {
        return round((float)$value, 2);
    }

    public function calcNumberFormat2Decimal($value)
    {
        return number_format(round((float)$value, 2), 2);
    }

    public function orderStatus($status)
    {
        if ($status == "1") {
            $result = '<span class="badge bg-secondary fw-normal">รอดำเนินการ</span>';
        } else if ($status == "2") {
            $result = '<span class="badge bg-success fw-normal">อัปเดตและส่งออก</span>';
        } else if ($status == "0") {
            $result = '<span class="badge bg-danger fw-normal">ยกเลิก</span>';
        } else {
            $result = '';
        }
        return $result;
    }

    public function platformOrderStatus($platform, $status)
    {
        $result = '';
        if (strtoupper($platform) == "SHOPEE") {
            if ($status == "UNPAID") {
                $result = '<span class="badge bg-blue fw-normal">รอชำระ</span>';
            } else if ($status == "READY_TO_SHIP") {
                $result = '<span class="badge bg-blue fw-normal">พร้อมจัดส่ง</span>';
            } else if ($status == "RETRY_SHIP") {
                $result = '<span class="badge bg-blue fw-normal">จัดส่งอีกครั้ง</span>';
            } else if ($status == "SHIPPED") {
                $result = '<span class="badge bg-blue fw-normal">จัดส่งแล้ว</span>';
            } else if ($status == "PROCESSED") {
                $result = '<span class="badge bg-blue fw-normal">รอดำเนินการ</span>';
            } else if ($status == "TO_CONFIRM_RECEIVE") {
                $result = '<span class="badge bg-blue fw-normal">ผู้ซื้อยืนยันการรับ</span>';
            } else if ($status == "COMPLETED") {
                $result = '<span class="badge bg-blue fw-normal">เสร็จสมบูรณ์</span>';
            } else if ($status == "IN_CANCEL") {
                $result = '<span class="badge bg-danger fw-normal">รอยกเลิก</span>';
            } else if ($status == "CANCELLED") {
                $result = '<span class="badge bg-danger fw-normal">คำสั่งซื้อถูกยกเลิก</span>';
            } else if ($status == "INVOICE_PENDING") {
                $result = '<span class="badge bg-danger fw-normal">ใบแจ้งหนี้รอดำเนินการ</span>';
            } else if ($status == "TO_RETURN") {
                $result = '<span class="badge bg-danger fw-normal">คืนสินค้า</span>';
            }
        } else if (strtoupper($platform) == "LAZADA") {
            if ($status == "unpaid") {
                $result = '<span class="badge bg-blue fw-normal">รอชำระ</span>';
            } else if ($status == "ready_to_ship") {
                $result = '<span class="badge bg-blue fw-normal">พร้อมจัดส่ง</span>';
            } else if ($status == "packed") {
                $result = '<span class="badge bg-blue fw-normal">บรรจุ</span>';
            } else if ($status == "shipped") {
                $result = '<span class="badge bg-blue fw-normal">จัดส่งแล้ว</span>';
            } else if ($status == "pending") {
                $result = '<span class="badge bg-blue fw-normal">รอดำเนินการ</span>';
            } else if ($status == "delivered") {
                $result = '<span class="badge bg-blue fw-normal">จัดส่ง</span>';
            } else if ($status == "canceled") {
                $result = '<span class="badge bg-danger fw-normal">คำสั่งซื้อถูกยกเลิก</span>';
            } else if ($status == "returned") {
                $result = '<span class="badge bg-danger fw-normal">คืนสินค้า</span>';
            } else if ($status == "failed") {
                $result = '<span class="badge bg-danger fw-normal">ล้มเหลว</span>';
            } else if ($status == "lost") {
                $result = '<span class="badge bg-danger fw-normal">สูญหาย</span>';
            }
        } else if (strtoupper($platform) == "NOCNOC") {
            if ($status == "PLACED") {
                $result = '<span class="badge bg-blue fw-normal">รอชำระ</span>';
            } else if ($status == "READYTOSHIP") {
                $result = '<span class="badge bg-blue fw-normal">พร้อมจัดส่ง</span>';
            } else if ($status == "IN_TRANSIT") {
                $result = '<span class="badge bg-blue fw-normal">อยู่ระหว่างจัดส่ง</span>';
            } else if ($status == "DELIVERED") {
                $result = '<span class="badge bg-blue fw-normal">จัดส่ง</span>';
            } else if ($status == "PACKING") {
                $result = '<span class="badge bg-blue fw-normal">บรรจุ</span>';
            } else if ($status == "AWAITING_CONFIRMATION") {
                $result = '<span class="badge bg-blue fw-normal">รอการยืนยัน</span>';
            } else if ($status == "COMPLETED") {
                $result = '<span class="badge bg-blue fw-normal">เสร็จสมบูรณ์</span>';
            } else if ($status == "CANCELLED") {
                $result = '<span class="badge bg-danger fw-normal">คำสั่งซื้อถูกยกเลิก</span>';
            }
        } else if (strtoupper($platform) == "TIKTOK") {
            if ($status == "100") {
                $result = '<span class="badge bg-blue fw-normal">รอชำระ</span>';
            } else if ($status == "111") {
                $result = '<span class="badge bg-blue fw-normal">รอจัดส่ง</span>';
            } else if ($status == "112") {
                $result = '<span class="badge bg-blue fw-normal">รอรวบรวม</span>';
            } else if ($status == "114") {
                $result = '<span class="badge bg-blue fw-normal">จัดส่งบางส่วน</span>';
            } else if ($status == "121") {
                $result = '<span class="badge bg-blue fw-normal">อยู่ระหว่างจัดส่ง</span>';
            } else if ($status == "122") {
                $result = '<span class="badge bg-blue fw-normal">จัดส่ง</span>';
            } else if ($status == "130") {
                $result = '<span class="badge bg-blue fw-normal">เสร็จสมบูรณ์</span>';
            } else if ($status == "140") {
                $result = '<span class="badge bg-danger fw-normal">คำสั่งซื้อถูกยกเลิก</span>';
            }
        }
        return $result;
    }
}