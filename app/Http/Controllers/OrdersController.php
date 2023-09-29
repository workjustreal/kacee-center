<?php

namespace App\Http\Controllers;

use App\Exports\OrdersExport;
use App\Models\Eplatform;
use App\Models\Eshop;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;

class OrdersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function downloadForm()
    {
        $eshop = Eshop::where('status', '<>', 0)->get();
        return view('orders.download-form')->with('eshop', $eshop);
    }

    public function search(Request $request)
    {
        $shop = $request->shop;
        $date_start = "";
        $date_end = "";
        if ($request->order_date_start) {
            $date_start = Carbon::createFromFormat('d/m/Y', $request->order_date_start)->format('Y-m-d');
        }
        if ($request->order_date_end) {
            $date_end = Carbon::createFromFormat('d/m/Y', $request->order_date_end)->format('Y-m-d');
        }

        $eshop = Eshop::find($shop);
        $eplatform = Eplatform::find($eshop->platform_id);
        if ($eplatform->id == 1) {
            // Shopee
            $order_status = "";
            if ($request->order_status == 1) {
                $order_status = "READY_TO_SHIP";
                // $order_status = "SHIPPED";
            }

            $data = array();
            $n = 0;

            $shopeeController = new ShopeeApiController;
            $shopid = (int)$eshop->seller_id;

            $orders_total = 0;
            $ordersn = array();

            // สูงสุด 500 คำสั่งซื้อ
            $per_page = 50;
            $offset = 0;
            for ($r = 0; $r < 10; $r++) {
                $parameters = [
                    "order_status" => $order_status,
                    "create_time_from" => strtotime("$date_start 00:00:00"),
                    "create_time_to" => strtotime("$date_end 23:59:59"),
                    "pagination_entries_per_page" => $per_page,
                    "pagination_offset" => $offset,
                    "partner_id" => $shopeeController->partner_id,
                    "shopid" => $shopid,
                    "timestamp" => time()
                ];
                $orders = $shopeeController->callApiV1('/orders/get', $parameters); // limit 50
                if (isset($orders->error)) {
                    $request->flash();
                    return back()->with("message", $orders->msg);
                }
                $orders_total += count($orders->orders);
                $orders_count = count($orders->orders);
                $ordersn = array();
                for ($i = 0; $i < $orders_count; $i++) {
                    array_push($ordersn, $orders->orders[$i]->ordersn);
                }

                if (count($ordersn)) {
                    $parameters = [
                        "ordersn_list" => $ordersn,
                        "partner_id" => $shopeeController->partner_id,
                        "shopid" => $shopid,
                        "timestamp" => time()
                    ];
                    $detail = $shopeeController->callApiV1('/orders/detail', $parameters); // limit 50
                    if (isset($detail->error)) {
                        $request->flash();
                        return back()->with("message", $detail->msg);
                    }
                    $detail_count = count($detail->orders);
                    for ($i = 0; $i < $detail_count; $i++) {
                        $items_count = count($detail->orders[$i]->items);
                        for ($j = 0; $j < $items_count; $j++) {
                            $data[$n]["ordersn"] = trim($detail->orders[$i]->ordersn);
                            $data[$n]["name"] = trim($detail->orders[$i]->recipient_address->name);
                            $data[$n]["tracking_no"] = trim($detail->orders[$i]->tracking_no);
                            // $data[$n]["item_sku"] = trim($detail->orders[$i]->items[$j]->item_sku);
                            $data[$n]["sku"] = ($detail->orders[$i]->items[$j]->variation_sku != "") ? trim($detail->orders[$i]->items[$j]->variation_sku) : trim($detail->orders[$i]->items[$j]->item_sku);
                            $data[$n]["qty"] = trim($detail->orders[$i]->items[$j]->variation_quantity_purchased);
                            $data[$n]["price"] = trim($detail->orders[$i]->items[$j]->variation_original_price);
                            // $data[$n]["discounted"] = trim($detail->orders[$i]->items[$j]->variation_discounted_price);
                            $n++;
                        }
                    }
                } else {
                    break;
                }
                $offset += $per_page;
            }
            if ($orders_total <= 0) {
                $request->flash();
                return back()->with("message", "ไม่พบคำสั่งซื้อ");
            }
            session()->put('orders_cart', []);
            session()->put('orders_cart', $data);
            session()->put('success', "จำนวนคำสั่งซื้อที่พบ " . $orders_total . " รายการ");
            $request->flash();
            return back();
        } else if ($eplatform->id == 2) {
            // Lazada
            $order_status = "";
            if ($request->order_status == 1) {
                $order_status = "ready_to_ship";
                // $order_status = "shipped";
            }

            $data = array();
            $n = 0;

            $lazadaController = new LazadaApiController;
            $seller_id = '100108607';

            $orders_total = 0;
            $ordersn = array();

            $start_date = Carbon::createFromFormat('Y-m-d H:i:s', $date_start.' 00:00:00')->format('c');
            $end_date = Carbon::createFromFormat('Y-m-d H:i:s', $date_end.' 23:59:59')->format('c');

            // สูงสุด 500 คำสั่งซื้อ
            $limit = 100;
            $offset = 0;
            for ($r = 0; $r < 5; $r++) {
                $parameters = array(
                    "update_before" => $end_date,
                    "sort_direction" => 'DESC',
                    "offset" => $offset,
                    "limit" => $limit,
                    "update_after" => $start_date,
                    "sort_by" => 'updated_at',
                    "created_before" => $end_date, // 2020-03-29T23:59:59+07:00
                    "created_after" => $start_date, // 2020-03-28T00:00:00+07:00
                    "status" => $order_status,
                );
                $orders = $lazadaController->callApi('/orders/get', $seller_id, $parameters); // limit 100
                if ($orders["code"] != "0") {
                    $request->flash();
                    return back()->with("message", $orders["message"]);
                }
                $orders_total += (int)$orders["data"]["count"];
                $orders_count = $orders["data"]["count"];
                $orders_data = $orders["data"]["orders"];
                $ordersn = array();
                for ($i = 0; $i < $orders_count; $i++) {
                    array_push($ordersn, $orders_data[$i]["order_number"]);
                }
                if (count($ordersn)) {
                    $order_ids = "";
                    foreach ($orders_data as $value) {
                        if ($order_ids != "") {
                            $order_ids .= ",";
                        }
                        $order_ids .= $value["order_id"];
                    }
                    $parameters = array(
                        "order_ids" => "[" . $order_ids . "]",
                    );
                    $items = $lazadaController->callApi('/orders/items/get', $seller_id, $parameters); // limit 100
                    if ($items["code"] != "0") {
                        $request->flash();
                        return back()->with("message", $items["message"]);
                    }
                    for ($i = 0; $i < $orders_count; $i++) {
                        $item_uniq = array();
                        $items_count = count($items["data"][$i]["order_items"]);
                        for ($j = 0; $j < $items_count; $j++) {
                            $item_chk = $items["data"][$i]["order_number"]."|".$items["data"][$i]["order_items"][$j]["sku"];
                            if (in_array($item_chk, $item_uniq)) {
                                $data[$n-1]["qty"] += 1;
                            } else {
                                $data[$n]["ordersn"] = trim($items["data"][$i]["order_number"]);
                                $data[$n]["name"] = trim($orders_data[$i]["address_shipping"]["first_name"]) . " " . trim($orders_data[$i]["address_shipping"]["last_name"]);
                                $data[$n]["tracking_no"] = trim($items["data"][$i]["order_items"][$j]["tracking_code"]);
                                $data[$n]["sku"] = trim($items["data"][$i]["order_items"][$j]["sku"]);
                                $data[$n]["qty"] = 1;
                                $data[$n]["price"] = trim($items["data"][$i]["order_items"][$j]["item_price"]);
                                $n++;
                            }
                            array_push($item_uniq, $item_chk);
                        }
                    }
                } else {
                    break;
                }
                $offset += $limit;
            }
            if ($orders_total <= 0) {
                $request->flash();
                return back()->with("message", "ไม่พบคำสั่งซื้อ");
            }
            session()->put('orders_cart', []);
            session()->put('orders_cart', $data);
            session()->put('success', "จำนวนคำสั่งซื้อที่พบ " . $orders_total . " รายการ");
            $request->flash();
            return back();
        }
    }

    public function export(Request $request)
    {
        $data = session()->get('orders_cart');
        session()->forget("orders_cart");
        session()->forget("success");
        return Excel::download(new OrdersExport($data, "Sheet1"), now().'.xls');
    }
}