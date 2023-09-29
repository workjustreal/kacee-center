<?php

namespace App\Http\Controllers\Middleware;

use App\Exports\OrdersReportMultipleSheetExport;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ShopeeApiController;
use App\Http\Controllers\LazadaApiController;
use App\Http\Controllers\NocNocApiController;
use App\Http\Controllers\TikTokApiController;
use App\Models\Middleware\LoadOrders;
use App\Models\Middleware\ProductOnline;
use App\Models\Eplatform;
use App\Models\Eshop;
use App\Models\EXCustomerGroup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class OrderReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        ini_set('memory_limit','512M');
        ini_set('max_execution_time', 300);
    }

    public function callCategory()
    {
        $category = ProductOnline::groupBy('category')->orderBy('category', 'asc')->get(['category']);
        $prefix = [];
        foreach ($category as $cat) {
            $prefix[] = ["name" => $cat->category, "display" => $cat->category];
        }
        $prefix[] = ["name" => "", "display" => "--ไม่มีหมวดหมู่--"];
        return $prefix;
    }

    public function callCategorySkuList()
    {
        $category = ProductOnline::groupBy('category', 'sku')->orderBy('category', 'asc')->orderBy('sku', 'asc')->get(['category', 'sku']);
        $product_prefix = [];
        foreach ($category as $cat) {
            $key = array_search($cat->category, array_column($product_prefix, "category"));
            if ($key !== false) {
                array_push($product_prefix[$key]["sku_list"], $cat->sku);
            } else {
                $product_prefix[] = [
                    "category" => $cat->category,
                    "sku_list" => ["$cat->sku"],
                ];
            }
        }
        return $product_prefix;
    }

    public function index()
    {
        $eshop = Eshop::where('status', '<>', 0)->get();
        return view('middleware.orders.order-report')->with('eshop', $eshop);
    }

    public function get_orders(Request $request)
    {
        // if ($request->ajax()) {
            $shop_id = $request->shop_id;
            $date_start = "";
            $date_end = "";
            if ($request->order_date_start) {
                $date_start = Carbon::createFromFormat('d/m/Y H:i', $request->order_date_start)->format('Y-m-d H:i').':00';
            }
            if ($request->order_date_end) {
                $date_end = Carbon::createFromFormat('d/m/Y H:i', $request->order_date_end)->format('Y-m-d H:i').':59';
            }

            $product_prefix = self::callCategorySkuList();

            $orders_total = 0;
            $data = array();
            $n = 0;

            $shop_list = array();
            if ($shop_id == "all") {
                $eshop = Eshop::where('status', '=', '1')->orderBy('id')->get(['id', 'name']);
                if ($eshop->isNotEmpty()) {
                    foreach ($eshop as $sp) {
                        array_push($shop_list, $sp->id);
                    }
                }
            } else {
                $eshop = Eshop::find($shop_id);
                array_push($shop_list, $eshop->id);
            }

            for ($shop=0; $shop < count($shop_list); $shop++) {
                $eshop = Eshop::find($shop_list[$shop]);
                $eplatform = Eplatform::find($eshop->platform_id);
                if ($eplatform->id == 1) {
                    // Shopee ------------------ V2 ------------------------
                    $shopeeController = new ShopeeApiController;
                    $shopid = (int)$eshop->seller_id;

                    $order_sn = [];
                    $orders = self::callShopeeOrders($order_sn, $shopid, $date_start, $date_end, 100, "");
                    if ($orders["code"] != "") {
                        return response()->json(["success"=>false, "message"=>$orders["message"]]);
                    }
                    if (count($order_sn)) {
                        $orders_total += count($order_sn);
                        $chunk_array = array_chunk($order_sn, 50, true);
                        foreach ($chunk_array as $row) {
                            $order_sn_list = implode(",", $row);
                            $parameters = [
                                "order_sn_list" => $order_sn_list,
                                "response_optional_fields" => "item_list",
                            ];
                            $details = $shopeeController->callApiV2('/api/v2/order/get_order_detail', $shopid, $parameters, 'GET');
                            if ($details->error == "") {
                                $details_count = count($details->response->order_list);
                                for ($j = 0; $j < $details_count; $j++) {
                                    $detail_list = $details->response->order_list[$j];
                                    $items_count = count($detail_list->item_list);
                                    for ($k = 0; $k < $items_count; $k++) {
                                        $item_list = $detail_list->item_list[$k];
                                        $order_number = $detail_list->order_sn;
                                        $order_create = $detail_list->create_time;
                                        $item_sku = ($item_list->model_sku != "") ? trim($item_list->model_sku) : trim($item_list->item_sku);

                                        $data[$n]["ordernumber"] = trim($order_number);
                                        $data[$n]["order_create"] = Carbon::createFromTimestamp(trim($order_create))->format('Y-m-d H:i:s');
                                        $data[$n]["sku"] = trim($item_sku);
                                        $data[$n]["name"] = trim($item_list->item_name);
                                        // $data[$n]["price"] = self::calcRound2Decimal((float)$item_list->model_original_price);
                                        $data[$n]["price"] = self::calcRound2Decimal((float)$item_list->model_discounted_price);
                                        $data[$n]["qty"] = (int)$item_list->model_quantity_purchased;
                                        $data[$n]["shop"] = "shopee";
                                        $data[$n]["category"] = "";
                                        foreach ($product_prefix as $category) {
                                            if (in_array($data[$n]["sku"], $category["sku_list"])) {
                                                $data[$n]["category"] = $category["category"];
                                                break;
                                            }
                                        }
                                        $n++;
                                    }
                                }
                            }
                        }
                    }
                    //------------------ END -------------------------
                } else if ($eplatform->id == 2) {
                    // Lazada
                    $lazadaController = new LazadaApiController;
                    $seller_id = $eshop->seller_id;

                    $start_date = Carbon::createFromFormat('Y-m-d H:i:s', $date_start)->format('c');
                    $end_date = Carbon::createFromFormat('Y-m-d H:i:s', $date_end)->format('c');

                    $orderid = array();
                    $orders = self::callLazadaOrders($orderid, $seller_id, $start_date, $end_date, 0, 100);
                    if ($orders["code"] != "0") {
                        return response()->json(["success"=>false, "message"=>$orders["message"]]);
                    }
                    if (count($orderid)) {
                        $orders_total += count($orderid);
                        $item_uniq = array();
                        $chunk_array = array_chunk($orderid, 50, true);
                        foreach ($chunk_array as $row) {
                            $order_ids = "";
                            foreach ($row as $sub_row) {
                                $order_ids .= ($order_ids != "") ? ",".$sub_row : $sub_row;
                            }
                            $parameters = array(
                                "order_ids" => "[" . $order_ids . "]",
                            );
                            $items = $lazadaController->callApi('/orders/items/get', $seller_id, $parameters); // limit 50
                            if ($items["code"] != "0") {
                                return response()->json(["success"=>false, "message"=>$items["message"]]);
                            }
                            $orders_count = count($items["data"]);
                            for ($i = 0; $i < $orders_count; $i++) {
                                $items_count = count($items["data"][$i]["order_items"]);
                                for ($j = 0; $j < $items_count; $j++) {
                                    $item_chk = $items["data"][$i]["order_number"]."|".$items["data"][$i]["order_items"][$j]["sku"];
                                    if (in_array($item_chk, $item_uniq)) {
                                        $data[$n-1]["qty"] += 1;
                                    } else {
                                        $data[$n]["ordernumber"] = trim($items["data"][$i]["order_number"]);
                                        $data[$n]["order_create"] = Carbon::parse(trim($items["data"][$i]["order_items"][$j]["created_at"]))->format('Y-m-d H:i:s');
                                        $data[$n]["sku"] = trim($items["data"][$i]["order_items"][$j]["sku"]);
                                        $data[$n]["name"] = trim($items["data"][$i]["order_items"][$j]["name"]);
                                        $data[$n]["price"] = self::calcRound2Decimal((float)trim($items["data"][$i]["order_items"][$j]["paid_price"]) + (float)trim($items["data"][$i]["order_items"][$j]["voucher_platform"]));
                                        $data[$n]["qty"] = 1;
                                        $data[$n]["shop"] = "lazada";
                                        $data[$n]["category"] = "";
                                        foreach ($product_prefix as $category) {
                                            if (in_array($data[$n]["sku"], $category["sku_list"])) {
                                                $data[$n]["category"] = $category["category"];
                                                break;
                                            }
                                        }
                                        $n++;
                                    }
                                    array_push($item_uniq, $item_chk);
                                }
                            }
                        }
                    }
                } else if ($eplatform->id == 3) {
                    // NocNoc
                    $seller_id = $eshop->seller_id;

                    // $start_date = Carbon::createFromFormat('Y-m-d H:i:s', $date_start)->format('Y-m-d');
                    // $end_date = Carbon::createFromFormat('Y-m-d H:i:s', $date_end)->format('Y-m-d');
                    $start_date = $date_start;
                    $end_date = $date_end;

                    $order_sn = [];
                    $nocnoc_data = [];
                    $orders = self::callNocNocOrders($order_sn, $nocnoc_data, $seller_id, $start_date, $end_date);
                    if ($orders["code"] != "") {
                        return response()->json(["success"=>false, "message"=>$orders["message"]]);
                    }
                    if (count($order_sn)) {
                        $orders_total += count($order_sn);
                        $items_count = count($orders["orders"]);
                        for ($k = 0; $k < $items_count; $k++) {
                            $item_list = $orders["orders"][$k];
                            $data[$n]["ordernumber"] = $item_list["ordernumber"];
                            $data[$n]["order_create"] = $item_list["order_create"];
                            $data[$n]["sku"] = $item_list["sku"];
                            $data[$n]["name"] = $item_list["name"];
                            $data[$n]["price"] = self::calcRound2Decimal((float)$item_list["price"]);
                            $data[$n]["qty"] = (int)$item_list["qty"];
                            $data[$n]["shop"] = $item_list["shop"];
                            $data[$n]["category"] = $item_list["category"];
                            foreach ($product_prefix as $category) {
                                if (in_array($data[$n]["sku"], $category["sku_list"])) {
                                    $data[$n]["category"] = $category["category"];
                                    break;
                                }
                            }
                            $n++;
                        }
                    }
                } else if ($eplatform->id == 4) {
                    // TikTok
                    $tiktokController = new TikTokApiController;
                    $shop_id = (int)$eshop->seller_id;

                    $orderid = [];
                    $orders = self::callTikTokOrders($orderid, $shop_id, $date_start, $date_end, 50, "");
                    if ($orders["code"] != "0") {
                        return response()->json(["success"=>false, "message"=>$orders["message"]]);
                    }
                    if (count($orderid)) {
                        $orders_total += count($orderid);
                        $chunk_array = array_chunk($orderid, 50, true);
                        foreach ($chunk_array as $row) {
                            $order_id_list = implode('","', $row);
                            $parameters = [
                                "order_id_list" => '["' . $order_id_list . '"]',
                            ];
                            $details = $tiktokController->callApiV2('/api/orders/detail/query', $shop_id, $parameters, 'POST');
                            if (isset($details->code)) {
                                if ($details->code == 0) {
                                    $details_count = count($details->data->order_list);
                                    for ($j = 0; $j < $details_count; $j++) {
                                        $detail_list = $details->data->order_list[$j];
                                        if (!isset($detail_list->cancel_user)) { // Not return/refund
                                            $items_count = count($detail_list->item_list);
                                            for ($k = 0; $k < $items_count; $k++) {
                                                $item_list = $detail_list->item_list[$k];

                                                $data[$n]["ordernumber"] = trim($detail_list->order_id);
                                                $data[$n]["order_create"] = Carbon::createFromTimestampMs(trim($detail_list->create_time))->format('Y-m-d H:i:s');
                                                $data[$n]["sku"] = trim($item_list->seller_sku);
                                                $data[$n]["name"] = trim($item_list->product_name);
                                                // $data[$n]["price"] = (float)$item_list->sku_original_price;
                                                $data[$n]["price"] = self::calcRound2Decimal((float)$item_list->sku_sale_price + (float)$item_list->sku_platform_discount);
                                                $data[$n]["qty"] = (int)$item_list->quantity;
                                                $data[$n]["shop"] = "tiktok";
                                                $data[$n]["category"] = "";
                                                foreach ($product_prefix as $category) {
                                                    if (in_array($data[$n]["sku"], $category["sku_list"])) {
                                                        $data[$n]["category"] = $category["category"];
                                                        break;
                                                    }
                                                }
                                                $n++;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if ($orders_total <= 0) {
                return response()->json(["success"=>false, "message"=>"ไม่พบคำสั่งซื้อ"]);
            }
            if (count($data)) {
                LoadOrders::where('id', '>', 0)->delete();
                DB::connection('mysql_mw')->statement("ALTER TABLE load_orders AUTO_INCREMENT = 1");
                foreach ($data as $d) {
                    $loadData = new LoadOrders();
                    $loadData->ordernumber = $d['ordernumber'];
                    $loadData->order_create = $d['order_create'];
                    $loadData->sku = $d['sku'];
                    $loadData->name = $d['name'];
                    $loadData->price = $d['price'];
                    $loadData->qty = $d['qty'];
                    $loadData->shop = $d['shop'];
                    $loadData->category = $d['category'];
                    $loadData->start = $date_start;
                    $loadData->end = $date_end;
                    $loadData->save();
                }
            }
            $response = array(
                "orders_count" => $orders_total,
                "order_list" => $data
            );
            unset($product_prefix);
            unset($data);
            return response()->json(["success"=>true, "response"=>$response]);
        // }
    }

    public function callShopeeOrders(& $order_sn, $shopid, $date_start, $date_end, $page_size, $cursor)
    {
        // $statuses = ["READY_TO_SHIP","PROCESSED","RETRY_SHIP","SHIPPED","TO_CONFIRM_RECEIVE","COMPLETED","INVOICE_PENDING"];
        $statuses = ["UNPAID","IN_CANCEL","CANCELLED"];
        $parameters = [
            "time_range_field" => "create_time",
            "time_from" => strtotime("$date_start"),
            "time_to" => strtotime("$date_end"),
            "page_size" => $page_size,
            "cursor" => $cursor,
            "response_optional_fields" => "order_status",
        ];
        $shopeeController = new ShopeeApiController;
        $orders = $shopeeController->callApiV2('/api/v2/order/get_order_list', $shopid, $parameters, 'GET');
        if ($orders->error == "") {
            $cursor = ($orders->response->more==true) ? $orders->response->next_cursor : "";
            $orders_count = count($orders->response->order_list);
            for ($i = 0; $i < $orders_count; $i++) {
                $order_list = $orders->response->order_list[$i];
                if (!in_array($order_list->order_status, $statuses)) {
                    array_push($order_sn, $order_list->order_sn);
                }
            }
            if ($cursor != "") {
                self::callShopeeOrders($order_sn, $shopid, $date_start, $date_end, $page_size, $cursor);
            }
        } else {
            return ["code"=>$orders->error, "message"=>$orders->message];
        }
        return ["code"=>"", "message"=>"", "orders"=>$order_sn];
    }

    public function callLazadaOrders(& $orderid, $seller_id, $start_date, $end_date, $offset, $limit)
    {
        // $statuses = ["ready_to_ship","packed","shipped","pending","delivered"];
        $statuses = ["unpaid","canceled","returned","failed","lost"];
        $parameters = array(
            "sort_direction" => 'DESC',
            "offset" => $offset,
            "limit" => $limit,
            "sort_by" => 'updated_at',
            "created_before" => $end_date, // 2020-03-29T23:59:59+07:00
            "created_after" => $start_date, // 2020-03-28T00:00:00+07:00
        );
        $lazadaController = new LazadaApiController;
        $orders = $lazadaController->callApi('/orders/get', $seller_id, $parameters);
        if ($orders["code"] == "0") {
            $orders_count = $orders["data"]["count"];
            $orders_data = $orders["data"]["orders"];
            for ($i = 0; $i < $orders_count; $i++) {
                // if (!in_array($orders_data[$i]["statuses"][0], $statuses)) {
                //     array_push($orderid, $orders_data[$i]["order_id"]);
                // }
                $chk_status = false;
                for ($j = 0; $j < count($orders_data[$i]["statuses"]); $j++) {
                    if (in_array($orders_data[$i]["statuses"][$j], $statuses)) {
                        $chk_status = true;
                    }
                }
                if ($chk_status == false) {
                    array_push($orderid, $orders_data[$i]["order_id"]);
                }
            }
            if ($orders_count > 0) {
                $_offset = ($offset + $limit);
                self::callLazadaOrders($orderid, $seller_id, $start_date, $end_date, $_offset, $limit);
            }
        } else {
            return ["code"=>$orders["code"], "message"=>$orders["message"]];
        }
        return ["code"=>"0", "message"=>"", "orders"=>$orderid];
    }

    public function callNocNocOrders(& $order_sn, & $nocnoc_data, $seller_id, $start_date, $end_date, $page=1)
    {
        // $statuses = ['ACCEPTED','READYTOSHIP','IN_TRANSIT','AWAITING_CONFIRMATION','PACKING','DELIVERED','COMPLETED'];
        $statuses = ['PLACED','CANCELLED'];
        $parameters = [
            // "placed_date" => [
            //     "from" => $start_date,
            //     "to" => $end_date,
            // ],
            "payment_processed_date" => [
                "from" => Carbon::createFromFormat('Y-m-d H:i:s', $start_date)->format('Y-m-d'),
                "to" => Carbon::createFromFormat('Y-m-d H:i:s', $end_date)->format('Y-m-d'),
            ],
        ];
        $nocnocController = new NocNocApiController;
        $orders = $nocnocController->callApiV1('/orders/shipments/_search?page='.$page, $seller_id, $parameters, "POST");
        if (isset($orders->status)) {
            if ($orders->status == "SUCCESS") {
                $orders_count = count($orders->data);
                for ($i = 0; $i < $orders_count; $i++) {
                    $payment_time = strtotime($orders->data[$i]->payment_processed_time_stamp);
                    if ($payment_time >= strtotime($start_date) && $payment_time <= strtotime($end_date)) {
                        if (!in_array($orders->data[$i]->order_status, $statuses)) {
                            array_push($order_sn, $orders->data[$i]->order_number);
                            $data_count = count($orders->data[$i]->products);
                            for ($j = 0; $j < $data_count; $j++) {
                                $products = $orders->data[$i]->products[$j];
                                $nocnoc_data[] = [
                                    "ordernumber" => $orders->data[$i]->order_number,
                                    // "order_create" => $orders->data[$i]->order_placed_date . " 00:00:00", // วันที่สั่งซื้อ ไม่มีเวลา
                                    "order_create" => $orders->data[$i]->payment_processed_time_stamp, // วันที่ชำระเงิน มีเวลา
                                    "sku" => $products->mpn,
                                    "name" => $products->sku_name,
                                    "price" => $products->original_price_per_unit,
                                    "qty" => $products->quantity,
                                    "shop" => "nocnoc",
                                    "category" => "",
                                ];
                            }
                        }
                    }
                }
                if ($orders_count > 0) {
                    $_page = ($page + 1);
                    self::callNocNocOrders($order_sn, $nocnoc_data, $seller_id, $start_date, $end_date, $_page);
                }
            } else {
                return ["code"=>$orders->errorCode, "message"=>$orders->error];
            }
        }
        return ["code"=>"", "message"=>"", "orders"=>$nocnoc_data];
    }

    public function callTikTokOrders(& $orderid, $shop_id, $date_start, $date_end, $page_size, $cursor)
    {
        // $statuses = [111, 112, 114, 121, 122, 130];
        $statuses = [100, 140];
        // - UNPAID = 100;
        // - AWAITING_SHIPMENT = 111;
        // - AWAITING_COLLECTION = 112;
        // - PARTIALLY_SHIPPING = 114;
        // - IN_TRANSIT = 121;
        // - DELIVERED = 122;
        // - COMPLETED = 130;
        // - CANCELLED = 140;
        $parameters = [
            "create_time_from" => strtotime("$date_start"),
            "create_time_to" => strtotime("$date_end"),
            "page_size" => $page_size,
            "cursor" => $cursor,
        ];
        $tiktokController = new TikTokApiController;
        $orders = $tiktokController->callApiV2('/api/orders/search', $shop_id, $parameters, 'POST');
        if (isset($orders->code)) {
            if ($orders->code == 0) {
                $cursor = ($orders->data->more==true) ? $orders->data->next_cursor : "";
                $orders_count = count($orders->data->order_list);
                for ($i = 0; $i < $orders_count; $i++) {
                    $order_list = $orders->data->order_list[$i];
                    if (!in_array($order_list->order_status, $statuses)) {
                        array_push($orderid, $order_list->order_id);
                    }
                }
                if ($cursor != "") {
                    self::callTikTokOrders($orderid, $shop_id, $date_start, $date_end, $page_size, $cursor);
                }
            } else {
                return ["code"=>$orders->code, "message"=>$orders->message];
            }
        }
        return ["code"=>"0", "message"=>"", "orders"=>$orderid];
    }

    public function get_data(Request $request)
    {
        if ($request->ajax()) {
            $records = LoadOrders::groupBy('ordernumber')->orderBy('shop', 'asc')->orderBy('ordernumber', 'asc')
            ->select('shop', 'ordernumber', 'order_create', DB::raw('SUM(qty) as total_qty'), DB::raw('ROUND(SUM(qty * price), 2) as total_price'))->get();
            if ($records->isNotEmpty()) {
                $totalRecords = $records->count();
                $rows = [];
                $n = 1;
                foreach ($records as $rec) {
                    $rows[] = array(
                        "no" => $n,
                        "shop" => $rec->shop,
                        "ordernumber" => $rec->ordernumber,
                        "total_qty" => self::calcNumberFormat($rec->total_qty),
                        "total_price" => self::calcNumberFormat2Decimal($rec->total_price),
                        "order_create" => $rec->order_create,
                    );
                    $n++;
                }
            } else {
                $totalRecords = 0;
                $rows = [];
            }
            $summary = LoadOrders::select(DB::raw('SUM(qty) as total_qty'), DB::raw('ROUND(SUM(qty * price), 2) as total_price'), 'start', 'end', 'updated_at')->first();
            if ($summary) {
                $total_qty = self::calcNumberFormat($summary->total_qty);
                $total_price = self::calcNumberFormat2Decimal($summary->total_price);
                $load_date = Carbon::parse($summary->start)->format('d/m/Y H:i') . ' ถึง ' . Carbon::parse($summary->end)->format('d/m/Y H:i');
                $time_data = Carbon::parse($summary->updated_at)->format('d/m/Y H:i');
            } else {
                $total_qty = 0;
                $total_price = 0;
                $load_date = "";
                $time_data = "";
            }
            $response = array(
                "total_qty" => $total_qty,
                "total_price" => $total_price,
                "load_date" => $load_date,
                "time_data" => $time_data,
                "total" => $totalRecords,
                "totalNotFiltered" => $totalRecords,
                "rows" => $rows,
            );
            return response()->json($response);
        }
    }

    public function export(Request $request)
    {
        $summary = LoadOrders::first(['start', 'end']);
        if ($summary) {
            $date = Carbon::parse($summary->start)->format('d/m/Y H:i') . ' ถึง ' . Carbon::parse($summary->end)->format('d/m/Y H:i');
        } else {
            $date = "";
        }
        $type = $request->type;
        $result = self::callDataCalc();
        // dd($result);
        if (count($result) <= 0) {
            alert()->warning('ไม่พบข้อมูล!');
            return back();
        } else {
            $items = $result["items"];

            $header = [];
            for ($i = 0; $i < count($items); $i++) {
                $header[0]["category"] = $date;
                for ($j = 0; $j < count($items[$i]["data"]); $j++) {
                    $colspan = count($items[$i]["data"][$j]["shop"]);
                    $header[0]["chanel"][] = array("name" => $items[$i]["data"][$j]["chanel"], "display_name" => "จำนวน ชิ้น, ชุด", "colspan" => $colspan);
                    for ($k = 0; $k < count($items[$i]["data"][$j]["shop"]); $k++) {
                        $header[0]["shop"][] = array("name" => $items[$i]["data"][$j]["shop"][$k]["name"], "display_name" => $items[$i]["data"][$j]["shop"][$k]["display_name"]);
                    }
                }
                $header[0]["summary"] = "รวมทั้งหมด";
                break;
            }
            $detail = [];
            $n = 0;
            for ($i = 0; $i < count($items); $i++) {
                $detail[$n]["category"] = $items[$i]["category"];
                if ($type == "qty") {
                    $detail[$n]["summary"] = self::calcRound($items[$i]["qty_total"]);
                } else {
                    $detail[$n]["summary"] = self::calcRound($items[$i]["price_total"]);
                }
                $detail[$n]["order_summary"] = self::calcRound($items[$i]["order_total"]);
                for ($j = 0; $j < count($items[$i]["data"]); $j++) {
                    $colspan = count($items[$i]["data"][$j]["shop"]);
                    for ($k = 0; $k < count($items[$i]["data"][$j]["shop"]); $k++) {
                        $detail[$n]["list"][] = array("name" => $items[$i]["data"][$j]["shop"][$k]["name"], "display_name" => $items[$i]["data"][$j]["shop"][$k]["display_name"], "qty" => self::calcRound($items[$i]["data"][$j]["shop"][$k]["qty"]), "price" => self::calcRound($items[$i]["data"][$j]["shop"][$k]["price"]));
                    }
                    if ($colspan > 1) {
                        $detail[$n]["list"][] = array("name" => "sum_" . $items[$i]["data"][$j]["chanel"], "display_name" => "รวมยอด" . str_replace('ชาแนล', '', $items[$i]["data"][$j]["display_name"]), "qty" => self::calcRound($items[$i]["data"][$j]["qty_total"]), "price" => self::calcRound($items[$i]["data"][$j]["price_total"]));
                    }
                }
                $n++;
            }

            $summary = $result["summary"];
            $footer = [];
            $n = 0;
            $footer["qty_summary"] = self::calcRound($summary["qty_total"]);
            $footer["price_summary"] = self::calcRound($summary["price_total"]);
            $footer["order_summary"] = self::calcRound($summary["order_total"]);
            for ($j = 0; $j < count($summary["data"]); $j++) {
                $colspan = count($summary["data"][$j]["shop"]);
                for ($k = 0; $k < count($summary["data"][$j]["shop"]); $k++) {
                    $footer["list"][] = array("name" => $summary["data"][$j]["shop"][$k]["name"], "display_name" => $summary["data"][$j]["shop"][$k]["display_name"], "qty" => self::calcRound($summary["data"][$j]["shop"][$k]["qty"]), "price" => self::calcRound($summary["data"][$j]["shop"][$k]["price"]), "order" => self::calcRound($summary["data"][$j]["shop"][$k]["order"]));
                }
                $n++;
            }
        }

        $data["type"] = $type;
        $data["header"] = $header;
        $data["detail"] = $detail;
        $data["footer"] = $footer;
        // dd($data);
        // return Excel::download(new OrdersReportExport($data, "ตารางสรุปยอดคำสั่งซื้อ"), 'ตารางสรุปยอดคำสั่งซื้อ วันที่ '.date("d-m-Y").'.xlsx');
        $data_none = LoadOrders::where('category', '=', '')->orderBy('category', 'asc')->get(['sku', 'name', 'price', 'qty', 'shop', 'category']);
        return Excel::download(new OrdersReportMultipleSheetExport($data, $data_none), 'ตารางสรุปยอดคำสั่งซื้อ วันที่ '.date("d-m-Y").'.xlsx');
    }

    public function callDataCalc()
    {
        $summary = LoadOrders::select(DB::raw('SUM(qty) as total_qty'), DB::raw('ROUND(SUM(qty * price), 2) as total_price'))->first();
        if ($summary) {
            $total_qty = self::calcRound($summary->total_qty);
            $total_price = self::calcRound2Decimal($summary->total_price);
        } else {
            $total_qty = 0;
            $total_price = 0;
        }
        $records1 = LoadOrders::groupBy('ordernumber')->orderBy('shop', 'asc')->orderBy('ordernumber', 'asc')
            ->select('shop', 'category', DB::raw('SUM(qty) as total_qty'), DB::raw('ROUND(SUM(qty * price), 2) as total_price'))->get();
        $records2 = LoadOrders::groupBy('shop', 'ordernumber')->orderBy('shop', 'asc')->orderBy('ordernumber', 'asc')
            ->select('shop', 'category', DB::raw('SUM(qty) as total_qty'), DB::raw('ROUND(SUM(qty * price), 2) as total_price'))->get();
        $records3 = LoadOrders::orderBy('shop', 'asc')->orderBy('category', 'asc')->orderBy('ordernumber', 'asc')
            ->select('shop', 'category', DB::raw('qty as total_qty'), DB::raw('ROUND((qty * price), 2) as total_price'))->get();

        $category = self::callCategory();
        $online = self::callCustomerOnline();
        $chanel = self::callCustomerChanel();

        $summary = [];
        $summary["order_total"] = self::calcRound($records1->count());
        $summary["qty_total"] = self::calcRound($total_qty);
        $summary["price_total"] = self::calcRound2Decimal($total_price);

        $i = 0;
        $items = [];
        foreach ($category as $cat) {

            $cat_total_order = 0;
            $cat_total_qty = 0;
            $cat_total_price = 0;
            $chanel_total_order = 0;
            $chanel_total_qty = 0;
            $chanel_total_price = 0;
            $summary_chanel_total_order = 0;
            $summary_chanel_total_qty = 0;
            $summary_chanel_total_price = 0;

            $data = [];
            $data_summary = [];
            $j = 0;
            $k = 0;
            foreach ($chanel as $ch) {
                if ($online->id == $ch->parent_id) {
                    $cat_qty = 0;
                    $cat_price = 0;
                    if ($records3->isNotEmpty()) {
                        foreach ($records3 as $rec) {
                            if ($rec->shop == $ch->name) {
                                if ($cat['name'] == $rec->category) {
                                    $cat_qty += self::calcRound($rec->total_qty);
                                    $cat_price += self::calcRound2Decimal($rec->total_price);
                                    $cat_total_qty += self::calcRound($rec->total_qty);
                                    $cat_total_price += self::calcRound2Decimal($rec->total_price);
                                }
                            }
                        }
                    }
                    $data[$j]["shop"][$k]["name"] = $ch->name;
                    $data[$j]["shop"][$k]["display_name"] = $ch->display_name;
                    $data[$j]["shop"][$k]["qty"] = self::calcRound($cat_qty);
                    $data[$j]["shop"][$k]["price"] = self::calcRound2Decimal($cat_price);

                    $summary_total_qty = 0;
                    $summary_total_price = 0;
                    if ($records2->isNotEmpty()) {
                        foreach ($records2 as $rec) {
                            if ($rec->shop == $ch->name) {
                                if ($cat['name'] == $rec->category) {
                                    $chanel_total_qty += self::calcRound($rec->total_qty);
                                    $chanel_total_price += self::calcRound2Decimal($rec->total_price);
                                }
                                $summary_total_qty += self::calcRound($rec->total_qty);
                                $summary_total_price += self::calcRound2Decimal($rec->total_price);
                                $summary_chanel_total_qty += self::calcRound($rec->total_qty);
                                $summary_chanel_total_price += self::calcRound2Decimal($rec->total_price);
                            }
                        }
                    }
                    $summary_total_order = 0;
                    if ($records1->isNotEmpty()) {
                        foreach ($records1 as $rec) {
                            if ($rec->shop == $ch->name) {
                                if ($cat['name'] == $rec->category) {
                                    $cat_total_order++;
                                    $chanel_total_order++;
                                }
                                $summary_total_order++;
                                $summary_chanel_total_order++;
                            }
                        }
                    }

                    $data_summary[$j]["shop"][$k]["name"] = $ch->name;
                    $data_summary[$j]["shop"][$k]["display_name"] = $ch->display_name;
                    $data_summary[$j]["shop"][$k]["qty"] = self::calcRound($summary_total_qty);
                    $data_summary[$j]["shop"][$k]["price"] = self::calcRound2Decimal($summary_total_price);
                    $data_summary[$j]["shop"][$k]["order"] = self::calcRound($summary_total_order);
                    $k++;
                }
            }
            $data[$j]["chanel"] = $online->name;
            $data[$j]["display_name"] = $online->display_name;
            $data[$j]["order_total"] = self::calcRound($chanel_total_order);
            $data[$j]["qty_total"] = self::calcRound($chanel_total_qty);
            $data[$j]["price_total"] = self::calcRound($chanel_total_price);

            $data_summary[$j]["chanel"] = $online->name;
            $data_summary[$j]["display_name"] = $online->display_name;
            $data_summary[$j]["order_total"] = self::calcRound($summary_chanel_total_order);
            $data_summary[$j]["qty_total"] = self::calcRound($summary_chanel_total_qty);
            $data_summary[$j]["price_total"] = self::calcRound2Decimal($summary_chanel_total_price);

            $items[$i]["category"] = $cat["display"];
            $items[$i]["order_total"] = self::calcRound($cat_total_order);
            $items[$i]["qty_total"] = self::calcRound($cat_total_qty);
            $items[$i]["price_total"] = self::calcRound($cat_total_price);
            $items[$i]["data"] = $data;
            $summary["data"] = $data_summary;
            $i++;
            $j++;
        }

        $result["items"] = $items;
        $result["summary"] = $summary;

        return $result;
    }

    public function callCustomerOnline()
    {
        $data = EXCustomerGroup::find(1);
        return $data;
    }

    public function callCustomerChanel()
    {
        $data = EXCustomerGroup::where('level', '=', 1)->where('parent_id', '=', 1)->where('status', '=', 1)->orderBy('id')->get(['id', 'name', 'display_name', 'level', 'parent_id']);
        return $data;
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
        return round($value, 2);
    }

    public function calcNumberFormat2Decimal($value)
    {
        return number_format(round($value, 2), 2);
    }
}