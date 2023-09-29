<?php

namespace App\Http\Controllers;

use App\Exports\CheckoutShipmentHistoryExport;
use App\Models\CheckoutShipmentDetail;
use App\Models\CheckoutShipmentHeader;
use App\Models\Eplatform;
use App\Models\Eshop;
use App\Models\EXCustomer;
use App\Models\ShippingCompany;
use App\Models\ShippingHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class CheckoutShipmentController extends Controller
{
    protected $destinationPath;
    public function __construct()
    {
        $this->middleware('auth');
        $this->destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/assets/shipping/';
    }

    public function history()
    {
        $eplatform = Eplatform::where('status', '<>', 0)->get();
        $ship_com = ShippingCompany::where('status', '<>', 0)->orderBy('name', 'asc')->get();
        $checkout = DB::table('checkout_shipment_header as csh')
        ->leftjoin('checkout_shipment_detail as csd', 'csh.running', '=', 'csd.running')
        ->leftjoin('eplatform as e', 'e.id', '=', 'csd.platform_id')
        ->leftjoin('users', 'users.id', '=', 'csh.userid')
        ->leftjoin('shipping_company as sc', 'sc.id', '=', 'csh.ship_com')
        ->whereRaw('SUBSTRING(csh.checkout_date, 1, 10) = "' . date('Y-m-d') . '"')
        ->select('csh.*', 'users.name as username', 'sc.name as ship_com_name', DB::raw('GROUP_CONCAT(DISTINCT(e.name)) as eplatform_list'), DB::raw('COUNT(DISTINCT(csd.trackingnumber)) as tracking_count, COUNT(DISTINCT(csd.ordernumber)) as order_count, COUNT(csd.so) as so_count, SUM(csd.packaging_qty) as packaging_total'))
        ->groupBy('csh.running')->orderBy('csh.running', 'DESC')->get();
        return view('checkout.checkout-shipment-list')->with('eplatform', $eplatform)->with('ship_com', $ship_com)->with('checkout', $checkout)->with('current_date', date('d/m/Y'));
    }

    public function search_history(Request $request)
    {
        $eplatform = Eplatform::where('status', '<>', 0)->get();
        $ship_com = ShippingCompany::where('status', '<>', 0)->orderBy('name', 'asc')->get();
        $data = DB::table('checkout_shipment_header as csh')
        ->leftjoin('checkout_shipment_detail as csd', 'csh.running', '=', 'csd.running')
        ->leftjoin('eplatform as e', 'e.id', '=', 'csd.platform_id')
        ->leftjoin('users', 'users.id', '=', 'csh.userid')
        ->leftjoin('shipping_company as sc', 'sc.id', '=', 'csh.ship_com')
        ->where(function ($query) use ($request) {
            if ($request->eplatform != '') {
                $query->where('csd.platform_id', '=', $request->eplatform);
            }
            if ($request->ship_com != '') {
                $query->where('csh.ship_com', '=', $request->ship_com);
            }
            if ($request->vehicle_registration != '') {
                $query->where('csh.vehicle_registration', 'LIKE', '%' . trim(str_replace(' ', '%', $request->vehicle_registration)) . '%');
            }
            if ($request->trackingnumber != '') {
                $query->where('csd.trackingnumber', 'LIKE', '%' . trim(str_replace(' ', '%', $request->trackingnumber)) . '%');
            }
            if ($request->ordernumber != '') {
                $query->where('csd.ordernumber', 'LIKE', '%' . trim(str_replace(' ', '%', $request->ordernumber)) . '%');
            }
            if ($request->so != '') {
                $query->where('csd.so', 'LIKE', '%' . trim(str_replace(' ', '%', $request->so)) . '%');
            }
            $start = "";
            $end = "";
            if ($request->checkout_date_start != '') {
                $start = Carbon::createFromFormat('d/m/Y', $request->checkout_date_start)->format('Y-m-d');
            }
            if ($request->checkout_date_end != '') {
                $end = Carbon::createFromFormat('d/m/Y', $request->checkout_date_end)->format('Y-m-d');
            }
            if ($start != '' && $end != '') {
                $query->whereRaw('SUBSTRING(csh.checkout_date, 1, 10) >= "' . $start . '"');
                $query->whereRaw('SUBSTRING(csh.checkout_date, 1, 10) <= "' . $end . '"');
            } else if ($start != '' && $end == '') {
                $query->whereRaw('SUBSTRING(csh.checkout_date, 1, 10) = "' . $start . '"');
            } else if ($start == '' && $end != '') {
                $query->whereRaw('SUBSTRING(csh.checkout_date, 1, 10) = "' . $end . '"');
            }
        });

        if ($request->action == "export") {
            $checkout = $data->select('csh.*', 'users.name as username', 'sc.name as ship_com_name', DB::raw('GROUP_CONCAT(DISTINCT(e.name)) as eplatform_list'), DB::raw('COUNT(DISTINCT(csd.trackingnumber)) as tracking_count, COUNT(DISTINCT(csd.ordernumber)) as order_count, COUNT(csd.so) as so_count, SUM(csd.packaging_qty) as packaging_total'))
            ->groupBy('csh.running')->orderBy('csh.running', 'ASC')->get();
            return Excel::download(new CheckoutShipmentHistoryExport($checkout, "Sheet1"), 'ประวัติเช็คเอาท์การจัดส่ง_'.now().'.xlsx');
        } else {
            $checkout = $data->select('csh.*', 'users.name as username', 'sc.name as ship_com_name', DB::raw('GROUP_CONCAT(DISTINCT(e.name)) as eplatform_list'), DB::raw('COUNT(DISTINCT(csd.trackingnumber)) as tracking_count, COUNT(DISTINCT(csd.ordernumber)) as order_count, COUNT(csd.so) as so_count, SUM(csd.packaging_qty) as packaging_total'))
            ->groupBy('csh.running')->orderBy('csh.running', 'DESC')->get();
            $request->flash();
            return view('checkout.checkout-shipment-list')->with('eplatform', $eplatform)->with('ship_com', $ship_com)->with('checkout', $checkout);
        }
    }

    public function shipmentForm()
    {
        $ship_com = ShippingCompany::where('status', '<>', 0)->orderBy('name', 'asc')->get();
        return view('checkout.checkout-shipment-form')->with('ship_com', $ship_com);
    }

    public function shipmentDetail($running)
    {
        $header = CheckoutShipmentHeader::leftjoin('users', 'users.id', '=', 'checkout_shipment_header.userid')
        ->leftjoin('shipping_company as sc', 'sc.id', '=', 'checkout_shipment_header.ship_com')
        ->where('checkout_shipment_header.running', '=', $running)
        ->select('checkout_shipment_header.*', 'users.name as fname', 'users.surname as lname', 'sc.name as ship_com_name')->first();
        $detail = DB::table('checkout_shipment_detail as d')->leftjoin('eplatform as e', 'e.id', '=', 'd.platform_id')
        ->where('d.running', '=', $running)->select('d.*', 'e.name as eplatform_name', DB::raw('SUM(d.packaging_qty) as packaging_total'))
        ->groupBy('d.trackingnumber', 'd.so')->orderBy('d.updated_at', 'desc')->orderBy('d.trackingnumber', 'asc')->orderBy('d.so', 'asc')->get();
        // $detail = DB::table('checkout_shipment_detail as d')->leftjoin('eplatform as e', 'e.id', '=', 'd.platform_id')->leftjoin('shipping_history as s', 's.ordernumber', '=', 'd.ordernumber')
        // ->where('d.running', '=', $running)->select('d.*', 'e.name as eplatform_name', DB::raw('d.packaging_qty as packaging_total'), DB::raw('SUM(s.packages) as packages_count'), DB::raw('(select sum(packaging_qty) from checkout_shipment_detail where ordernumber=d.ordernumber) as packaging_count'))
        // ->groupBy('d.trackingnumber', 'd.so')->orderBy('d.ordernumber', 'asc')->orderBy('d.trackingnumber', 'asc')->orderBy('d.so', 'asc')->orderBy('d.updated_at', 'desc')->get();
        $dataSumShop = DB::table('checkout_shipment_detail as d')->leftjoin('eplatform as e', 'e.id', '=', 'd.platform_id')
        ->where('d.running', '=', $running)->select('e.name as eplatform_name', DB::raw('COUNT(DISTINCT(d.trackingnumber)) as tracking_total'), DB::raw('COUNT(DISTINCT(d.ordernumber)) as order_total'), DB::raw('COUNT(so) as so_total'), DB::raw('COALESCE(SUM(d.packaging_qty), 0) as packaging_total'))
        ->groupBy('d.platform_id')->orderBy('d.platform_id', 'asc')->get();
        $dataSumTotal = CheckoutShipmentDetail::where('running', '=', $running)->select(DB::raw('COUNT(DISTINCT(trackingnumber)) as tracking_total'), DB::raw('COUNT(DISTINCT(ordernumber)) as order_total'), DB::raw('COUNT(so) as so_total'), DB::raw('COALESCE(SUM(packaging_qty), 0) as packaging_total'))->first();
        return view('checkout.checkout-shipment-detail')->with('header', $header)->with('detail', $detail)->with('dataSumShop', $dataSumShop)->with('dataSumTotal', $dataSumTotal);
    }

    public function shipmentPrint($running)
    {
        $header = CheckoutShipmentHeader::leftjoin('users', 'users.id', '=', 'checkout_shipment_header.userid')
        ->leftjoin('shipping_company as sc', 'sc.id', '=', 'checkout_shipment_header.ship_com')
        ->where('checkout_shipment_header.running', '=', $running)
        ->select('checkout_shipment_header.*', 'users.name as fname', 'users.surname as lname', 'sc.name as ship_com_name')->first();
        $detail = DB::table('checkout_shipment_detail as d')->leftjoin('eplatform as e', 'e.id', '=', 'd.platform_id')
        ->where('d.running', '=', $running)->select('d.*', 'e.name as eplatform_name', DB::raw('SUM(d.packaging_qty) as packaging_total'))->groupBy('d.trackingnumber', 'd.so')->orderBy('d.updated_at', 'desc')->orderBy('d.trackingnumber', 'asc')->orderBy('d.so', 'asc')->get();

        $dataSumShop = DB::table('checkout_shipment_detail as d')->leftjoin('eplatform as e', 'e.id', '=', 'd.platform_id')
        ->where('d.running', '=', $running)->select('e.name as eplatform_name', DB::raw('COUNT(DISTINCT(d.trackingnumber)) as tracking_total'), DB::raw('COUNT(DISTINCT(d.ordernumber)) as order_total'), DB::raw('COUNT(so) as so_total'), DB::raw('COALESCE(SUM(d.packaging_qty), 0) as packaging_total'))
        ->groupBy('d.platform_id')->orderBy('d.platform_id', 'asc')->get();
        $dataSumTotal = CheckoutShipmentDetail::where('running', '=', $running)->select(DB::raw('COUNT(DISTINCT(trackingnumber)) as tracking_total'), DB::raw('COUNT(DISTINCT(ordernumber)) as order_total'), DB::raw('COUNT(so) as so_total'), DB::raw('COALESCE(SUM(packaging_qty), 0) as packaging_total'))->first();
        return view('checkout.checkout-shipment-print')->with('header', $header)->with('detail', $detail)->with('dataSumShop', $dataSumShop)->with('dataSumTotal', $dataSumTotal);
    }

    public function shipment2Print($running)
    {
        $header = CheckoutShipmentHeader::leftjoin('users', 'users.id', '=', 'checkout_shipment_header.userid')
        ->leftjoin('shipping_company as sc', 'sc.id', '=', 'checkout_shipment_header.ship_com')
        ->where('checkout_shipment_header.running', '=', $running)
        ->select('checkout_shipment_header.*', 'users.name as fname', 'users.surname as lname', 'sc.name as ship_com_name')->first();

        $dataTotal = DB::table('checkout_shipment_detail as d')->leftjoin('eplatform as e', 'e.id', '=', 'd.platform_id')
        ->where('d.running', '=', $running)->select('d.*', 'e.name as eplatform_name', DB::raw('SUM(d.packaging_qty) as packaging_total'))->groupBy('d.trackingnumber')->orderBy('d.updated_at', 'desc')->orderBy('d.trackingnumber', 'asc')->get();
        $countTotal = $dataTotal->count();
        $rowPerPage = 36;
        $count = ceil($countTotal / $rowPerPage);
        $offset = 0;
        $limit = 36;
        $n = 0;
        $n1 = 0;
        $n2 = 0;
        $detail = [];
        for ($i=0; $i<$count; $i++) {
            $data = DB::table('checkout_shipment_detail as d')->leftjoin('eplatform as e', 'e.id', '=', 'd.platform_id')
            ->where('d.running', '=', $running)->select('d.*', 'e.name as eplatform_name', DB::raw('SUM(d.packaging_qty) as packaging_total'))->groupBy('d.trackingnumber')->orderBy('d.updated_at', 'desc')->orderBy('d.trackingnumber', 'asc')->offset($offset)->limit($limit)->get();
            $countData = $data->count();
            if ($i%2 == 0) {
                $column = 1;
                for ($j=0; $j<$countData; $j++) {
                    $detail[$n1][$column]["line"] = $n + 1;
                    $detail[$n1][$column]["trackingnumber"] = $data[$j]->trackingnumber;
                    $detail[$n1][$column]["packaging_total"] = $data[$j]->packaging_total;
                    $detail[$n1][$column]["eplatform_name"] = $data[$j]->eplatform_name;
                    $detail[$n1][$column]["updated_at"] = $data[$j]->updated_at;
                    $n1++;
                    $n++;
                }
            } else {
                $column = 2;
                for ($j=0; $j<$countData; $j++) {
                    $detail[$n2][$column]["line"] = $n + 1;
                    $detail[$n2][$column]["trackingnumber"] = $data[$j]->trackingnumber;
                    $detail[$n2][$column]["packaging_total"] = $data[$j]->packaging_total;
                    $detail[$n2][$column]["eplatform_name"] = $data[$j]->eplatform_name;
                    $detail[$n2][$column]["updated_at"] = $data[$j]->updated_at;
                    $n2++;
                    $n++;
                }
            }
            $offset += $limit;
        }

        $dataSumShop = DB::table('checkout_shipment_detail as d')->leftjoin('eplatform as e', 'e.id', '=', 'd.platform_id')
        ->where('d.running', '=', $running)->select('e.name as eplatform_name', DB::raw('COUNT(DISTINCT(d.trackingnumber)) as tracking_total'), DB::raw('COUNT(DISTINCT(d.ordernumber)) as order_total'), DB::raw('COALESCE(SUM(d.packaging_qty), 0) as packaging_total'))
        ->groupBy('d.platform_id')->orderBy('d.platform_id', 'asc')->get();
        $dataSumTotal = CheckoutShipmentDetail::where('running', '=', $running)->select(DB::raw('COUNT(DISTINCT(trackingnumber)) as tracking_total'), DB::raw('COUNT(DISTINCT(ordernumber)) as order_total'), DB::raw('COALESCE(SUM(packaging_qty), 0) as packaging_total'))->first();

        return view('checkout.checkout-shipment2-print')->with('header', $header)->with('detail', $detail)->with('dataSumShop', $dataSumShop)->with('dataSumTotal', $dataSumTotal);
    }

    public function shipmentSubmit(Request $request)
    {
        $vehicle_registration = $request->vehicle_registration;
        $ship_com = $request->ship_com;
        $remark = $request->remark;
        $user = auth()->user();

        // เลขเอกสารใหม่
        $gen = "CS" . date("ym");
        $rundoc = CheckoutShipmentHeader::whereRaw('SUBSTRING(running, 1, 6) = "' . $gen . '"')->orderBy('running', 'desc')->first();
        if ($rundoc) {
            $running_id = str_pad(intval(substr($rundoc->running, 6, 4) + 1), 4, "0", STR_PAD_LEFT);
        } else {
            $running_id = "0001";
        }
        $running = $gen . $running_id;
        // insert header ข้อมูลใหม่
        $shipmentheader = new CheckoutShipmentHeader();
        $shipmentheader->running = $running;
        $shipmentheader->vehicle_registration = $vehicle_registration;
        $shipmentheader->ship_com = $ship_com;
        $shipmentheader->checkout_date = now();
        $shipmentheader->remark = $remark;
        $shipmentheader->userid = $user->id;
        $shipmentheader->userip = $request->ip();
        $shipmentheader->save();

        return redirect('checkout/shipment-tracking/'.$running);
    }

    public function shipmentUpdate(Request $request)
    {
        if ($request->ajax()) {
            $running = $request->running;
            $vehicle_registration = $request->vehicle_registration;
            $remark = $request->remark;

            // update header ข้อมูลอัปเดต
            $shipmentheader = CheckoutShipmentHeader::where('running', '=', $running);
            $shipmentheader->update(["vehicle_registration" => $vehicle_registration, "remark" => $remark, "updated_at" => now()]);

            return response()->json(['success' => true,'message' => 'แก้ไขข้อมูลเรียบร้อย']);
        }
    }

    public function shipmentSignature(Request $request)
    {
        if ($request->ajax()) {
            $running = $request->running;
            $data_url = $request->data_url;

            $signaturePath = $_SERVER['DOCUMENT_ROOT'] . '/assets/images/signature/';
            if (strpos($data_url, 'data:image')!==false){
                list($type, $data) = explode(';', $data_url);
                list(, $data) = explode(',', $data);
                list(, $ext) = explode('/', $type);

                $img_data = base64_decode($data);
                $image_name = $running . '.' . $ext;
                $path = $signaturePath . $image_name;
                file_put_contents($path, $img_data);

                // update header ลายเซ็น
                $shipmentheader = CheckoutShipmentHeader::where('running', '=', $running);
                $shipmentheader->update(["signature" => $image_name, "updated_at" => now()]);
            }

            return response()->json(['success' => true,'message' => 'เซ็นรับของเรียบร้อย']);
        }
    }

    public function shipmentDelete($id)
    {
        $header = CheckoutShipmentHeader::where('running', '=', $id);
        $header->delete();
        $detail = CheckoutShipmentDetail::where('running', '=', $id);
        $detail->delete();
        return response()->json(['success' => true,'message' => 'ลบข้อมูลเรียบร้อย']);
    }

    public function shipmentItemDelete($id)
    {
        $detail = CheckoutShipmentDetail::find($id);
        if ($detail) {
            if ($detail->packaging_qty > 1) {
                $detailUpdate = CheckoutShipmentDetail::find($id);
                $detailUpdate->update(["packaging_qty" => ($detail->packaging_qty - 1)]);
            } else {
                $detailDel = CheckoutShipmentDetail::find($id);
                $detailDel->delete();
            }
        }
        return response()->json(['success' => true,'message' => 'ลบข้อมูลเรียบร้อย']);
    }

    public function shipmentTracking($running)
    {
        $checkoutheader = DB::table('checkout_shipment_header as csh')
        ->leftjoin('shipping_company as sc', 'sc.id', '=', 'csh.ship_com')
        ->where('csh.running', '=', $running)->select('csh.*', 'sc.name as ship_com_name')->first();
        return view('checkout.checkout-shipment-form')->with('checkoutheader', $checkoutheader);
    }

    public function getShipment(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('checkout_shipment_detail as csd')->leftjoin('eplatform as e', 'e.id', '=', 'csd.platform_id')
            ->where('csd.running', '=', $request->running);

            $totalRecords = $data->select('count(csd.*) as allcount')->count();
            // $records = $data->select('csd.*', 'e.name as eplatform_name', DB::raw('(select count(*) from checkout_shipment_detail where trackingnumber = csd.trackingnumber) as checkout_count'), DB::raw('(select count(*) from shipping_history where trackingnumber = csd.trackingnumber) as so_count'))
            // ->orderBy('csd.updated_at', 'desc')->orderBy('csd.trackingnumber', 'asc')->orderBy('csd.so', 'asc')->take(50)->get();
            $records = $data->select('csd.*', 'e.name as eplatform_name', DB::raw('0 as checkout_count'), DB::raw('0 as so_count'))
            ->orderBy('csd.updated_at', 'desc')->orderBy('csd.trackingnumber', 'asc')->orderBy('csd.so', 'asc')->take(50)->get()->toArray();

            if ($records) {
                $checkout = DB::table('checkout_shipment_detail as csd')
                ->where('csd.running', '=', $request->running)->select('csd.trackingnumber', DB::raw('COUNT(csd.trackingnumber) as checkout_count'))
                ->groupBy('csd.trackingnumber')->orderBy('csd.trackingnumber', 'asc')->orderBy('csd.so', 'asc')->get()->toArray();

                $shipping = DB::table('shipping_history as sh')
                ->leftjoin('checkout_shipment_detail as csd', 'sh.trackingnumber', '=', 'csd.trackingnumber')
                ->where('csd.running', '=', $request->running)->select('sh.trackingnumber', 'sh.so', DB::raw('COUNT(sh.trackingnumber) as so_count'))
                ->groupBy('sh.trackingnumber', 'sh.so')->orderBy('sh.trackingnumber', 'asc')->orderBy('sh.so', 'asc')->get()->toArray();
                $l = 0;
                foreach ($records as $val) {
                    foreach ($checkout as $value) {
                        if ($value->trackingnumber == $val->trackingnumber) {
                            $records[$l]->checkout_count = $value->checkout_count;
                        }
                    }
                    foreach ($shipping as $value) {
                        if ($value->trackingnumber == $val->trackingnumber) {
                            $records[$l]->so_count++;
                        }
                    }
                    $l++;
                }
            }

            $dataSumTracking = DB::table('checkout_shipment_detail')->where('running', '=', $request->running)->select(DB::raw('COUNT(DISTINCT(trackingnumber)) as total'))->first();
            $dataSumSO = DB::table('checkout_shipment_detail')->where('running', '=', $request->running)->select(DB::raw('COUNT(so) as total'))->first();
            $dataSumPackaging = DB::table('checkout_shipment_detail')->where('running', '=', $request->running)->select(DB::raw('COALESCE(SUM(packaging_qty), 0) as total'))->first();

            $response = array(
                "draw" => 25,
                "data" => $records,
                "recordsTotal" => $totalRecords,
                "recordsFiltered" => $totalRecords,
                "dataSumTracking" => $dataSumTracking->total,
                "dataSumSO" => $dataSumSO->total,
                "dataSumPackaging" => $dataSumPackaging->total,
            );
            return response()->json($response);
        }
    }

    public function callSaleOrder($so)
    {
        $response = Http::get('http://192.168.2.20:2980/api/SaleOrder/' . $so);

        return json_decode($response->body());
    }

    public function callShipmentSet($running, $trackingnumber)
    {
        $data = [];
        $shipping = ShippingHistory::where('trackingnumber', '=', $trackingnumber)->get();
        $items_count = $shipping->count();
        if ($shipping->isNotEmpty()) {
            foreach ($shipping as $value) {
                $checkout = false;
                $checkoutdetail = CheckoutShipmentDetail::where('running', '=', $running)->where('trackingnumber', '=', $trackingnumber)->where('so', '=', $value->so)->first();
                if ($checkoutdetail) {
                    $checkout = true;
                }
                $so_status = "";
                $so = self::callSaleOrder($value->so);
                if ($so->soid === "NotFound") {
                    $so_status = "notfound"; // ไม่พบหมายเลข SO
                } else {
                    if ($so->so_status === 0) {
                        $so_status = "cancel"; // ไม่พบหมายเลข SO ถูกยกเลิก
                    } else if ($so->so_status === 1) {
                        if ($so->packingflag == 0) {
                            $so_status = "notcheckin"; // SO ยังไม่ได้เช็คอิน
                        } else if ($so->packingflag == 6) {
                            $so_status = "checkout"; // SO ถูกเช็คเอาท์ไปแล้ว
                        } else if ($so->packingflag >= 1 && $so->packingflag <= 5) {
                            $so_status = "checkin"; // SO ถูกเช็คอินแล้ว
                        }
                    }
                }
                $data[] = [
                    "trackingnumber" => $value->trackingnumber,
                    "ordernumber" => $value->ordernumber,
                    "so" => $value->so,
                    "platform_id" => $value->platform_id,
                    "checkout" => $checkout,
                    "so_status" => $so_status,
                ];
            }
        }
        return array("items_count"=>$items_count, "data"=>$data);
    }

    public function getShop($platform_id)
    {
        $eshop = Eshop::where('platform_id', '=', $platform_id)->where('status', '=', 1)->first();
        return $eshop;
    }

    public function checkShipCom($running, $trackingnumber)
    {
        $result = false;
        $check_ship_com = DB::table('checkout_shipment_header as csh')->leftjoin('shipping_company as sc', 'sc.id', '=', 'csh.ship_com')
        ->where('csh.running', '=', $running)->select('csh.*', 'sc.check as ship_com_check')->first();
        if ($check_ship_com) {
            $ship_com_check = $check_ship_com->ship_com_check;
            if ($ship_com_check != "") {
                $exp = explode(",", $ship_com_check);
                foreach ($exp as $ship) {
                    if (trim($ship) == substr($trackingnumber, 0, strlen(trim($ship)))) {
                        $result = true;
                        break;
                    }
                }
            } else {
                $result = true;
            }
        }
        return $result;
    }

    public function search(Request $request)
    {
        if ($request->ajax()) {
            $running = $request->running;
            $trackingnumber = trim($request->trackingnumber);
            $sonumber = trim($request->sonumber);
            $addpackaging = trim($request->addpackaging);

            $check_ship_com = self::checkShipCom($running, $trackingnumber);
            if ($check_ship_com == false) {
                return response()->json(['success' => false,'message' => 'ใบปะหน้าพัสดุไม่ตรงกับขนส่งที่เลือก']);
            }

            $checkout_another = CheckoutShipmentDetail::where('running', '<>', $running)->where('trackingnumber', '=', $trackingnumber)->first();
            if ($checkout_another) { // เช็คเอาท์ในรถคันอื่นไปแล้ว
                $checkoutheader = CheckoutShipmentHeader::where('running', '=', $running)->first();
                return response()->json(['success' => false,'message' => 'เช็คเอาท์ไปแล้ว<br>ทะเบียนรถ: '.$checkoutheader->vehicle_registration.'<br>('.$checkoutheader->checkout_date.')<br>'.$checkoutheader->remark]);
            }

            if ($sonumber == "") {
                // กรณีไม่ได้เลือก SO งานชุดและปกติ
                // เช็คงานชุด
                $shipment_set = self::callShipmentSet($running, $trackingnumber);
                if ((int)$shipment_set["items_count"] > 1) {
                    return response()->json(['success' => true,'message' => 'หมายเลข SO เป็นงานชุด','result' => $shipment_set]);
                } else {
                    for ($i=0; $i<(int)$shipment_set["items_count"]; $i++) {
                        if ($shipment_set["data"][$i]["so_status"] == "notfound") {
                            return response()->json(['success' => false,'message' => 'ไม่พบหมายเลข SO']);
                        } else if ($shipment_set["data"][$i]["so_status"] == "cancel") {
                            return response()->json(['success' => false,'message' => 'หมายเลข '.$shipment_set["data"][$i]["so"].' ถูกยกเลิก']);
                        } else if ($shipment_set["data"][$i]["so_status"] == "notcheckin") {
                            return response()->json(['success' => false,'message' => 'SO ยังไม่ได้เช็คอิน']);
                        } else if ($shipment_set["data"][$i]["so_status"] == "checkout") {
                            return response()->json(['success' => false,'message' => 'SO ถูกเช็คเอาท์ไปแล้ว']);
                        }
                    }
                }
                // เช็คงานปกติ
                $checkoutdetail = CheckoutShipmentDetail::where('running', '=', $running)->where('trackingnumber', '=', $trackingnumber)->first();
                if ($checkoutdetail) {
                    return response()->json(['success' => true,'message' => 'เช็คเอาท์ไปแล้ว','confirm' => true,'so' => $checkoutdetail->so]);
                }
                $shipping = ShippingHistory::where('trackingnumber', '=', $trackingnumber)->get();
            } else {
                // กรณีเลือก SO หรือเพิ่มแพ็คเกจ
                $exp_sonumber = explode(",", $sonumber);
                if ($addpackaging == "") {
                    $checkoutdetail = CheckoutShipmentDetail::where('running', '=', $running)->where('trackingnumber', '=', $trackingnumber)
                    ->where(function ($query) use ($exp_sonumber) {
                        for ($i=0; $i<count($exp_sonumber); $i++) {
                            $query->orWhere('so', '=', $exp_sonumber[$i]);
                        }
                    })->get();
                    if ($checkoutdetail->isNotEmpty()) {
                        return response()->json(['success' => true,'message' => 'เช็คเอาท์ไปแล้ว','confirm' => true,'so' => $sonumber]);
                    }
                }

                $shipping = ShippingHistory::where('trackingnumber', '=', $trackingnumber)
                ->where(function ($query) use ($exp_sonumber) {
                    for ($i=0; $i<count($exp_sonumber); $i++) {
                        $query->orWhere('so', '=', $exp_sonumber[$i]);
                    }
                })->get();
            }

            if ($shipping->isNotEmpty()) {
                $so_arr = [];
                $order_arr = [];
                $platform_arr = [];
                foreach ($shipping as $value) {
                    $so_arr[] = $value->so;
                    $order_arr[] = $value->ordernumber;
                    $platform_arr[] = $value->platform_id;
                }
                for ($i=0; $i<count($so_arr); $i++) {
                    $ordernumber = $order_arr[$i];
                    $platform_id = $platform_arr[$i];
                    $eshop = self::getShop($platform_id);
                    if ($platform_id == 1) { // Shopee
                        $shopeeController = new ShopeeApiController;
                        $shopid = (int)$eshop->seller_id;
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
                            //------------------ END -------------------------
                        } else if ($eshop->api_version == 2) {
                            //------------------ V2 ------------------------
                            $parameters = [
                                "order_sn_list" => $ordernumber,
                            ];
                            $data = $shopeeController->callApiV2('/api/v2/order/get_order_detail', $shopid, $parameters, 'GET');
                            if (isset($data->error)) {
                                if ($data->error == "") {
                                    if ($data->response->order_list[0]->order_status == "CANCELLED") {
                                        return response()->json(['success' => false,'message' => 'ออเดอร์ถูกยกเลิก']);
                                    }
                                }
                            }
                            //------------------ END -------------------------
                        }
                    } else if ($platform_id == 2) { // Lazada
                        $lazadaController = new LazadaApiController;
                        $seller_id = $eshop->seller_id;
                        $parameters = array(
                            "order_id" => $ordernumber,
                        );
                        $order_item = $lazadaController->callApi('/order/items/get', $seller_id, $parameters);
                        if (isset($order_item["code"])) {
                            if ($order_item["code"] == "0") {
                                $count_data = 0;
                                $is_canceled = 0;
                                foreach ($order_item["data"] as $value) {
                                    if ($trackingnumber == $value["tracking_code"]) {
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
                    } else if ($platform_id == 3) { // NocNoc
                        $nocnocController = new NocNocApiController;
                        $seller_id = $eshop->seller_id;

                        $data = $nocnocController->callApiV1('/orders/shipments/'.$ordernumber, $seller_id, null, "GET");
                        if (isset($data->status)) {
                            if ($data->status == "SUCCESS") {
                                if ($data->data->order_status == "CANCELLED") {
                                    return response()->json(['success' => false,'message' => 'ออเดอร์ถูกยกเลิก']);
                                }
                            }
                        }
                    } else if ($platform_id == 4) { // TikTok
                        $tiktokController = new TikTokApiController;
                        $shop_id = (int)$eshop->seller_id;

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
                            }
                        }
                    }
                }
                $success = false;
                DB::beginTransaction();
                try {
                    for ($i=0; $i<count($so_arr); $i++) {
                        if ($addpackaging == "add") {
                            // update detail เพิ่มข้อมูลแพ็คเกจ
                            $packaging = self::callPackaging($running, $trackingnumber, $so_arr[$i]);
                            if ($packaging) {
                                // update detail เพิ่มข้อมูลแพ็คเกจ
                                $shipmentdetail = CheckoutShipmentDetail::where('running', '=', $running)->where('trackingnumber', '=', $trackingnumber)->where('so', '=', $so_arr[$i]);
                                $shipmentdetail->update(["packaging_qty" => ($packaging->packaging_qty + 1), "updated_at" => now()]);
                            } else {
                                // insert detail ข้อมูลใหม่
                                $shipmentdetail = new CheckoutShipmentDetail();
                                $shipmentdetail->running = $running;
                                $shipmentdetail->trackingnumber = $trackingnumber;
                                $shipmentdetail->ordernumber = $order_arr[$i];
                                $shipmentdetail->so = $so_arr[$i];
                                $shipmentdetail->packaging_qty = 1;
                                $shipmentdetail->platform_id = $platform_id;
                                $shipmentdetail->save();
                            }
                        } else {
                            // insert detail ข้อมูลใหม่
                            $shipmentdetail = new CheckoutShipmentDetail();
                            $shipmentdetail->running = $running;
                            $shipmentdetail->trackingnumber = $trackingnumber;
                            $shipmentdetail->ordernumber = $order_arr[$i];
                            $shipmentdetail->so = $so_arr[$i];
                            $shipmentdetail->packaging_qty = 1;
                            $shipmentdetail->platform_id = $platform_id;
                            $shipmentdetail->save();
                        }

                    }
                    $success = true;
                    if ($success) {
                        DB::commit();
                    }
                    // all good
                } catch (\Exception $e) {
                    DB::rollback();
                    // something went wrong
                    return response()->json(['success' => false,'message' => 'เกิดข้อผิดพลาด ไม่สามารถบันทึกข้อมูลได้ '.$e->getMessage()]);
                }
                return response()->json(['success' => true,'message' => 'เช็คเอาท์เรียบร้อย']);
            } else {
                return response()->json(['success' => false,'message' => 'ไม่พบข้อมูลใบปะหน้าพัสดุ']);
            }
        }
    }

    public function callPackaging($running, $trackingnumber, $sonumber)
    {
        $detail = CheckoutShipmentDetail::where('running', '=', $running)->where('trackingnumber', '=', $trackingnumber)->where('so', '=', $sonumber)->select('packaging_qty')->first();
        return $detail;
    }
}