<?php

namespace App\Http\Controllers;

use App\Imports\ExcelImport;
use App\Models\Printer;
use App\Models\Product;
use App\Models\RequestLabelDetail;
use App\Models\RequestLabelHeader;
use Carbon\Carbon;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;

class RequestLabelController extends Controller
{
    protected $printer;
    protected $label;
    public function __construct()
    {
        $this->middleware('auth');
        // $this->printer = array(
        //     [
        //         "printer_id" => 99,
        //         "printer_name" => "TSC TTP-247",
        //         "client_ip" => "192.168.3.88",
        //         "remark" => "(Admin)",
        //         "role" => "admin",
        //         "location" => "DV",
        //     ],
        //     [
        //         "printer_id" => 1,
        //         "printer_name" => "TSC MH241(MU) ONLINE 1",
        //         "client_ip" => "",
        //         "remark" => "(Store Online)",
        //         "role" => "store",
        //         "location" => "ON",
        //     ],
        //     [
        //         "printer_id" => 2,
        //         "printer_name" => "TSC TTP-247",
        //         "client_ip" => "",
        //         "remark" => "(Store 11 ไร่)",
        //         "role" => "store",
        //         "location" => "F4",
        //     ]
        // );
        $this->label = array(
            [
                "label" => 'product_barcode',
                "label_detail" => "เล็ก (32 x 16 mm)"
            ],
            [
                "label" => 'product_barcode_md',
                "label_detail" => "กลาง (50 x 30 mm)"
            ],
        );
    }

    public function getPrinters()
    {
        if (Auth::User()->roleAdmin()) {
            $result = Printer::where('status', '=', 1)->orderBy('role', 'asc')->orderBy('id', 'asc')->get()->toArray();
        } else {
            $result = Printer::where('status', '=', 1)->where('role', '<>', 1)->where('user_permission', 'like', '%'.Auth::user()->emp_id.'%')->orderBy('id', 'asc')->get()->toArray();
        }
        return $result;
    }

    public function index()
    {
        return view('product.request-label');
    }

    public function search(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('request_label_header as h')
            ->leftjoin('request_label_detail as d', 'd.request_id', '=', 'h.request_id')->leftjoin('users', 'h.userid', '=', 'users.id')
            ->leftjoin('employee as e', 'users.emp_id', '=', 'e.emp_id')->leftjoin('department as dept', 'users.dept_id', '=', 'dept.dept_id');
            $totalRecords = $data->select('count(h.*) as allcount')->count();
            $records = $data->select('h.*', 'users.name', 'users.surname', 'e.nickname', 'dept.dept_name', DB::raw('COUNT(d.sku) as sku_total'), DB::raw('SUM(d.qty) as qty_total'))->groupBy('h.request_id')->orderBy('h.request_id', 'DESC')->get();
            $rows = [];
            $n = 1;
            foreach ($records as $rec) {
                if ($rec->label_color == "pink") {
                    $label_color = '<div style="width: 20px; height: 20px; background-color: #fdc0cb;" class="border me-1"></div>';
                } else if ($rec->label_color == "yellow") {
                    $label_color = '<div style="width: 20px; height: 20px; background-color: #fffb04;" class="border me-1"></div>';
                } else {
                    $label_color = '<div style="width: 20px; height: 20px; background-color: #ffffff;" class="border me-1"></div>';
                }
                $request_link = '<div class="d-flex align-items-center">'.$label_color.'<a href="'.url('product/request-label/show', $rec->request_id).'">'.$rec->request_id.'</a></div>';
                $name = $rec->name . ' ' . $rec->surname;
                if ($rec->nickname != "") {
                    $name .= ' (' . $rec->nickname . ')';
                }
                $status = '';
                if ($rec->status == 1) {
                    $status = '<span class="badge bg-secondary fw-normal">ร้องขอ</span>';
                } else if ($rec->status == 2) {
                    $status = '<span class="badge bg-warning fw-normal">รอคิวปริ้น</span>';
                } else if ($rec->status == 3) {
                    $status = '<span class="badge bg-success fw-normal">เสร็จสิ้น</span>';
                }
                $action = '<div>';
                $action .= '<a class="action-icon" href="'.url('product/request-label/download', $rec->request_id).'" title="ดาวน์โหลด TEXT ไฟล์"><i class="mdi mdi-download"></i></a>';
                if ($rec->status == 1) {
                    $action .= '<a class="action-icon" href="'.url('product/request-label/edit', $rec->request_id).'" title="แก้ไข"><i class="mdi mdi-square-edit-outline"></i></a>';
                    $action .= '<a class="action-icon" href="javascript:void(0);" onclick="deleteRequestLabelConfirmation(\''.$rec->request_id.'\')" title="ลบ"><i class="mdi mdi-delete"></i></a>';
                }
                $action .= '</div>';

                $rows[] = array(
                    "no" => $n,
                    "request_id" => $request_link,
                    "label" => $rec->label_detail,
                    "sku_total" => $rec->sku_total,
                    "qty_total" => $rec->qty_total,
                    "remark" => $rec->remark,
                    "user" => $name,
                    "dept" => $rec->dept_name,
                    "date" => Carbon::parse($rec->created_at)->format('d/m/Y H:i:s'),
                    "status" => $status,
                    "action" => $action,
                );
                $n++;
            }

            $response = array(
                "total" => $totalRecords,
                "totalNotFiltered" => $totalRecords,
                "rows" => $rows,
            );
            return response()->json($response);
        }
    }

    public function search_sku(Request $request)
    {
        if ($request->ajax()) {
            $result = Product::where('stkcod', '<>', '')->where('barcod', '<>', '')
            ->where(function ($query) use ($request) {
                if ($request->search != "") {
                    $query->orWhere('stkcod', 'like', '%'.trim(str_replace(' ', '%', $request->search)).'%');
                    $query->orWhere('barcod', 'like', '%'.trim(str_replace(' ', '%', $request->search)).'%');
                    $query->orWhere('names', 'like', '%'.trim(str_replace(' ', '%', $request->search)).'%');
                }
            })->orderBy("stkcod", "asc")->take(10)->get();
            return response()->json($result);
        }
    }

    public function get_data(Request $request)
    {
        if ($request->ajax()) {
            $session_act = $request->session_act;
            $count = 0;
            $output = '';
            $total = 0;
            if ($request->has('request_id')) {
                $detail = DB::table('request_label_detail')->where('request_id', '=', $request->request_id)->get();
                if ($detail->isNotEmpty()) {
                    session()->put('request_label_cart_'.$session_act, []);
                    $data = session()->get('request_label_cart_'.$session_act);
                    foreach ($detail as $list) {
                        $data_count = count($data);
                        $data[$data_count]["sku"] = $list->sku;
                        $data[$data_count]["barcode"] = $list->barcode;
                        $data[$data_count]["name"] = $list->description;
                        $data[$data_count]["qty"] = $list->qty;
                    }
                    session()->put('request_label_cart_'.$session_act, $data);
                }
            }
            if (session()->has('request_label_cart_'.$session_act)) {
                $data = session()->get('request_label_cart_'.$session_act);
                $count = count($data);
                if ($count > 0) {
                    for ($i=0; $i<$count; $i++) {
                        $output .= '
                        <tr>
                        <td>' . ($i + 1) . '</td>
                        <td>' . $data[$i]["sku"] . '<input type="hidden" class="form-control form-control-sm" id="sku_edit['.$i.']" name="sku_edit[]" value="'.$data[$i]["sku"].'"></td>
                        <td>' . $data[$i]["barcode"] . '</td>
                        <td>' . $data[$i]["name"] . '</td>
                        <td>
                            <input type="hidden" class="form-control form-control-sm" id="qty_edit_old['.$i.']" name="qty_edit_old[]" value="'.$data[$i]["qty"].'">
                            <input type="number" class="form-control form-control-sm hideArrows" id="qty_edit['.$i.']" name="qty_edit[]" value="'.$data[$i]["qty"].'" autocomplete="off" placeholder="จำนวน" min="1" style="width: 100px;" onclick="$(this).select();" onkeyup="edit_qty_press('.$i.');" onblur="edit_qty('.$i.');">
                        </td>
                        <td><a class="action-icon" href="javascript:void(0);" onclick="remove_data(\''.$data[$i]["sku"].'\')" title="ลบ"><i class="mdi mdi-delete"></i></a></td>
                        </tr>
                        ';
                        $total += (int)$data[$i]["qty"];
                    }
                } else {
                    $output = ' <tr> <td align="center" colspan="6"> ไม่พบข้อมูล </td> </tr> ';
                }
            } else {
                $output = ' <tr> <td align="center" colspan="6"> ไม่พบข้อมูล </td> </tr> ';
            }
            $output .= '
            <tr> <th scope="row" colspan="4" class="text-end">รวมจำนวน :</th>
            <td colspan="2">
                <div class="fw-bold">'.$total.'</div>
            </td>
            </tr>';
            $result = array(
                'count_data'  => $count,
                'table_data'  => $output,
            );
            echo json_encode($result);
        }
    }

    public function add_data(Request $request)
    {
        if ($request->ajax()) {
            $session_act = $request->session_act;
            if (session()->has('request_label_cart_'.$session_act)) {
                $data = session()->get('request_label_cart_'.$session_act);
                $data_count = count($data);
            } else {
                $data = [];
                $data_count = 0;
            }
            $result = Product::where('stkcod', '=', trim($request->sku))->first(['stkcod', 'barcod', 'names']);
            if ($result) {
                $index = array_search($result->stkcod, array_column($data, "sku"));
                if ($index !== false) {
                    $data[$index]["qty"] += $request->qty;
                } else {
                    $data[$data_count]["sku"] = $result->stkcod;
                    $data[$data_count]["barcode"] = $result->barcod;
                    $data[$data_count]["name"] = $result->names;
                    $data[$data_count]["qty"] = $request->qty;
                }
                session()->put('request_label_cart_'.$session_act, $data);
                return response()->json(["success"=>true, "message"=>""]);
            } else {
                return response()->json(["success"=>false, "message"=>"ไม่พบรหัสสินค้านี้"]);
            }
        }
    }

    public function remove_data(Request $request)
    {
        $session_act = $request->session_act;
        if (session()->has('request_label_cart_'.$session_act)) {
            $data = session()->get('request_label_cart_'.$session_act);
            $index = array_search($request->sku, array_column($data, "sku"));
            if ($index !== false) {
                unset($data[$index]);
            }
            $data = array_values($data);
            session()->put('request_label_cart_'.$session_act, $data);
        }
    }

    public function reset_data(Request $request)
    {
        $session_act = $request->session_act;
        if (session()->get('request_label_cart_'.$session_act)) {
            session()->forget('request_label_cart_'.$session_act);
        }
    }

    public function edit_qty(Request $request)
    {
        $session_act = $request->session_act;
        if (session()->has('request_label_cart_'.$session_act)) {
            $data = session()->get('request_label_cart_'.$session_act);
            $index = array_search($request->sku, array_column($data, "sku"));
            if ($index !== false) {
                $data[$index]["qty"] = $request->qty;
            }
            $data = array_values($data);
            session()->put('request_label_cart_'.$session_act, $data);
        }
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required',
        ],[
            'file.required' => 'ยังไม่ได้เลือกไฟล์',
        ]);

        $err_data = [];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = 'request_label';
            $input['filename'] = $fileName . '.' . $file->extension();
            $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/assets/uploads/excel/';
            $file->move($destinationPath, $input['filename']);

            $data = Excel::toCollection(new ExcelImport, $destinationPath . $input['filename'])->toArray();
            $result= [];
            $i = 0;
            foreach ($data[0] as $value) {
                if ($i > 0) {
                    $result[] = [
                        "sku" => $value[0],
                        "qty" => $value[1],
                    ];
                }
                $i++;
            }

            if (File::exists($destinationPath.$input['filename'])) {
                File::delete($destinationPath.$input['filename']);
            }

            if (!empty($result)) {
                $session_act = $request->session_act_up;
                if (session()->has('request_label_cart_'.$session_act)) {
                    $data = session()->get('request_label_cart_'.$session_act);
                    $data_count = count($data);
                } else {
                    $data = [];
                    $data_count = 0;
                }
                foreach ($result as $value) {
                    $result = Product::where('stkcod', '=', trim($value['sku']))->first(['stkcod', 'barcod', 'names']);
                    if ($result) {
                        $index = array_search($result->stkcod, array_column($data, "sku"));
                        if ($index !== false) {
                            $data[$index]["qty"] += trim($value['qty']);
                        } else {
                            $data[$data_count]["sku"] = $result->stkcod;
                            $data[$data_count]["barcode"] = $result->barcod;
                            $data[$data_count]["name"] = $result->names;
                            $data[$data_count]["qty"] = trim($value['qty']);
                            $data_count++;
                        }
                        session()->put('request_label_cart_'.$session_act, $data);
                    } else {
                        $err_data[] = array(
                            "sku" => $value['sku'],
                            "msg" => "ไม่พบรหัสสินค้านี้",
                        );
                    }
                }
            }
        }
        return redirect()->back()->with('err_data', $err_data);
    }

    public function download_template()
    {
        $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/assets/files/templates/';
        $file = $destinationPath . "แบบฟอร์มร้องขอสติ๊กเกอร์บาร์โค้ด.xlsx";
        return response()->download($file);
    }

    public function create()
    {
        $label = $this->label;
        $err_data = [];
        return view('product.request-label-create', compact('label', 'err_data'));
    }

    public function store(Request $request)
    {
        $session_act = $request->session_act;
        if (session()->has('request_label_cart_'.$session_act)) {
            $data = session()->get('request_label_cart_'.$session_act);
            $count = count($data);
            if ($count > 0) {
                $user = auth()->user();

                $success = false;
                DB::beginTransaction();

                try {
                    // เลขเอกสารใหม่
                    $gen = "RQ" . date("ym");
                    $rundoc = RequestLabelHeader::whereRaw('SUBSTRING(request_id, 1, 6) = "' . $gen . '"')->orderBy('request_id', 'desc')->first();
                    if ($rundoc) {
                        $running_id = str_pad(intval(substr($rundoc->request_id, 6, 4) + 1), 4, "0", STR_PAD_LEFT);
                    } else {
                        $running_id = "0001";
                    }
                    $request_id = $gen . $running_id;

                    // insert ข้อมูลใหม่
                    $labelheader = new RequestLabelHeader();
                    $labelheader->request_id = $request_id;
                    $labelheader->label = $request->label;
                    $labelheader->label_color = $request->barcode_color;
                    $labelheader->label_detail = self::get_label($request->label);
                    $labelheader->remark = $request->remark;
                    $labelheader->userid = $user->id;
                    $labelheader->userip = $request->ip();
                    $labelheader->status = 1;
                    $labelheader->save();

                    for ($i=0; $i<$count; $i++) {
                        // insert ข้อมูลใหม่
                        $labeldetail = new RequestLabelDetail();
                        $labeldetail->request_id = $request_id;
                        $labeldetail->sku = $data[$i]["sku"];
                        $labeldetail->barcode = $data[$i]["barcode"];
                        $labeldetail->description = $data[$i]["name"];
                        $labeldetail->qty = $data[$i]["qty"];
                        $labeldetail->save();
                    }

                    $success = true;
                    if ($success) {
                        DB::commit();
                        session()->forget('request_label_cart_'.$session_act);

                        if (Auth::user()->isAdmin() || Auth::user()->printerLabelDeptPer()) {
                            $to_uid = 100001;
                        } else {
                            $to_uid = $user->emp_id;
                        }
                        $notiController = new NotificationController;
                        $parameters = [
                            "app_id" => 23,
                            "title" => "ร้องขอสติ๊กเกอร์",
                            "description" => "มีคำร้องขอปริ้นสติ๊กเกอร์ จาก:" . $user->name . ' ' . $user->surname,
                            "url" => "/product/request-label/show/" . $request_id,
                            "job_id" => $request_id,
                            "from_uid" => $user->emp_id,
                            "to_uid" => $to_uid,
                            "type" => "01",
                            "status" => 1
                        ];
                        $notiController->push_notification($parameters);
                    }
                    // all good
                } catch (\Exception $e) {
                    DB::rollback();
                    // something went wrong
                    alert()->error('เกิดข้อผิดพลาด ไม่สามารถบันทึกข้อมูลได้ ' . $e->getMessage())->autoClose(false);
                    return back();
                }
                alert()->success('ร้องขอสติ๊กเกอร์บาร์โค้ดเรียบร้อย');
                return redirect('product/request-label');
            } else {
                alert()->warning('ไม่มีรายการ');
                return redirect()->back();
            }
        }
    }

    public function show($id)
    {
        $notiController = new NotificationController;
        if ($notiController->check_notification(23, $id)) {
            $notiController->update_notification(23, $id);
        }
        $header = DB::table('request_label_header as h')->leftjoin('users', 'h.userid', '=', 'users.id')->leftjoin('employee as e', 'users.emp_id', '=', 'e.emp_id')
        ->leftjoin('department as dept', 'users.dept_id', '=', 'dept.dept_id')->where('request_id', '=', $id)
        ->select('h.*', DB::raw('(select count(sku) from request_label_detail where request_id=h.request_id) as sku_total'), DB::raw('(select sum(qty) from request_label_detail where request_id=h.request_id) as qty_total'), 'users.emp_id', 'users.name', 'users.surname', 'e.nickname', 'dept.dept_name')->first();
        $detail = DB::table('request_label_detail')->where('request_id', '=', $id)->get();
        $label = $this->label;
        // $printer = $this->printer;
        $this->printer = self::getPrinters();
        $printer = $this->printer;
        return view('product.request-label-show', compact('id', 'header', 'detail', 'label', 'printer'));
    }

    public function show_search(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('request_label_detail as d')->where('d.request_id', '=', $request->request_id);
            $totalRecords = $data->select(DB::raw('count(*) as allcount'))->count();
            if ($request->sortSKU == 1) {
                $records = $data->select('d.*')->orderBy('d.sku', 'asc')->get();
            } else {
                $records = $data->select('d.*')->get();
            }
            $rows = [];
            $n = 1;
            foreach ($records as $rec) {
                $rows[] = array(
                    "no" => $n,
                    "sku" => $rec->sku,
                    "barcode" => $rec->barcode,
                    "name" => $rec->description,
                    "qty" => $rec->qty,
                );
                $n++;
            }

            $response = array(
                "total" => $totalRecords,
                "totalNotFiltered" => $totalRecords,
                "rows" => $rows,
            );
            return response()->json($response);
        }
    }

    public function edit($id)
    {
        $header = DB::table('request_label_header as h')->leftjoin('users', 'h.userid', '=', 'users.id')->where('request_id', '=', $id)
        ->select('h.*', DB::raw('(select sum(qty) from request_label_detail where request_id=h.request_id) as total'), 'users.emp_id', 'users.name', 'users.surname')->first();
        $detail = DB::table('request_label_detail')->where('request_id', '=', $id)->get();
        if ($detail->isNotEmpty()) {
            $session_act = "edit";
            session()->put('request_label_cart_' . $session_act, []);
            $data = session()->get('request_label_cart_' . $session_act);
            foreach ($detail as $list) {
                $data_count = count($data);
                $data[$data_count]["sku"] = $list->sku;
                $data[$data_count]["barcode"] = $list->barcode;
                $data[$data_count]["name"] = $list->description;
                $data[$data_count]["qty"] = $list->qty;
            }
            session()->put('request_label_cart_' . $session_act, $data);
        }
        $label = $this->label;
        return view('product.request-label-edit', compact('id', 'header', 'detail', 'label'));
    }

    public function update(Request $request)
    {
        $session_act = $request->session_act;
        if (session()->has('request_label_cart_'.$session_act)) {
            $data = session()->get('request_label_cart_'.$session_act);
            $count = count($data);
            if ($count > 0) {
                $user = auth()->user();

                $success = false;
                DB::beginTransaction();

                $request_id = $request->request_id;

                try {
                    // update ข้อมูลใหม่
                    $labelheader = RequestLabelHeader::where('request_id', '=', $request_id);
                    $labelheader->update(["label"=>$request->label,"label_detail"=>self::get_label($request->label),"remark"=>$request->remark,"label_color"=>$request->barcode_color]);

                    // delete ข้อมูลใหม่
                    RequestLabelDetail::where('request_id', '=', $request_id)->delete();
                    for ($i=0; $i<$count; $i++) {
                        // update ข้อมูลใหม่
                        $labeldetail = new RequestLabelDetail();
                        $labeldetail->request_id = $request_id;
                        $labeldetail->sku = $data[$i]["sku"];
                        $labeldetail->barcode = $data[$i]["barcode"];
                        $labeldetail->description = $data[$i]["name"];
                        $labeldetail->qty = $data[$i]["qty"];
                        $labeldetail->save();
                    }

                    $success = true;
                    if ($success) {
                        DB::commit();
                        session()->forget('request_label_cart_'.$session_act);
                    }
                    // all good
                } catch (\Exception $e) {
                    DB::rollback();
                    // something went wrong
                    alert()->error('เกิดข้อผิดพลาด ไม่สามารถบันทึกข้อมูลได้ ' . $e->getMessage());
                    return back();
                }
                alert()->success('แก้ไขร้องขอสติ๊กเกอร์บาร์โค้ดเรียบร้อย');
                return redirect('product/request-label');
            } else {
                alert()->warning('ไม่มีรายการ');
                return redirect()->back();
            }
        }
    }

    public function destroy($id)
    {
        if ($id != "") {
            RequestLabelHeader::where('request_id', '=', $id)->delete();
            RequestLabelDetail::where('request_id', '=', $id)->delete();

            $notiController = new NotificationController;
            if ($notiController->check_notification(23, $id)) {
                $notiController->remove_notification(23, $id);
            }

            return response()->json(['success' => true,'message' => 'ลบข้อมูลเรียบร้อย']);
        }
    }

    public function download($id)
    {
        // $header = DB::table('request_label_header as h')->leftjoin('users', 'h.userid', '=', 'users.id')->where('request_id', '=', $id)
        // ->select('h.*', DB::raw('(select sum(qty) from request_label_detail where request_id=h.request_id) as total'), 'users.emp_id', 'users.name', 'users.surname')->first();
        $detail = DB::table('request_label_detail')->where('request_id', '=', $id)->get();
        if ($detail->isNotEmpty()) {
            $data = [];
            $i = 0;
            foreach ($detail as $list) {
                $data[$i]["barcode"] = $list->barcode;
                $data[$i]["sku"] = $list->sku;
                $data[$i]["qty"] = $list->qty;
                $i++;
            }
            $text = '';
            foreach ($data as $data) {
                $text .= iconv("UTF-8", "TIS620", $data["barcode"] . "\t" . $data['sku'] . "\t" . $data['qty'] . "\r\n");
            }
            $txtFile = 'ร้องขอสติ๊กเกอร์บาร์โค้ด' . time() . '.txt';
            $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/assets/uploads/txt/';
            File::put($destinationPath . $txtFile, $text);
            return response()->download($destinationPath . $txtFile)->deleteFileAfterSend(true);
        }
    }

    public function get_label($label) {
        $index = array_search($label, array_column($this->label, "label"));
        if ($index !== false) {
            $label_detail = $this->label[$index]["label_detail"];
        } else {
            $label_detail = "";
        }
        return $label_detail;
    }

    public function print_label(Request $request)
    {
        $this->printer = self::getPrinters();
        if ($request->request_id != "" && $request->printer_id > 0) {
            $client_ip = "";
            $printer_name = "";
            $index = array_search($request->printer_id, array_column($this->printer, "id"));
            if ($index !== false) {
                $client_ip = $this->printer[$index]["client_ip"];
                $printer_name = $this->printer[$index]["name"];
            } else {
                return response()->json(['success' => false,'message' => 'ไม่พบเครื่องพิมพ์ที่เลือก']);
            }
            if ($request->sortSku == 1) {
                $sort_sku = 1;
            } else {
                $sort_sku = 0;
            }

            RequestLabelHeader::where('request_id', '=', $request->request_id)
            ->update([
                "status"=>2,
                "label"=>$request->label,
                "label_detail"=>self::get_label($request->label),
                "printer_id"=>$request->printer_id,
                "printer_name"=>$printer_name,
                "sort_sku"=>$sort_sku,
            ]);

            $notiController = new NotificationController;
            if ($notiController->check_notification(23, $request->request_id)) {
                $notiController->remove_notification(23, $request->request_id);
            }
            // return response()->json(['success' => true,'message' => 'เพิ่มในคิวปริ้นเรียบร้อย']);

            // ########################## Use web service ##############################
            if ($request->printer_id != 3) {
                return response()->json(['success' => true,'message' => 'เพิ่มในคิวปริ้นเรียบร้อย']);
            } else {
                $EventData = "";
                $header = RequestLabelHeader::where('printer_id', '=', $request->printer_id)->where('status', '=', 2)->where('label', '<>', '')->orderBy('updated_at', 'desc')->first();
                if ($header) {
                    if ($header->sort_sku == 1) {
                        $detail = RequestLabelDetail::where('request_id', '=', $header->request_id)->orderBy('sku', 'asc')->get();
                    } else {
                        $detail = RequestLabelDetail::where('request_id', '=', $header->request_id)->get();
                    }
                    if ($detail->isNotEmpty()) {
                        foreach ($detail as $detail) {
                            if ($EventData != "") {
                                $EventData .= "\r\n";
                            }
                            $EventData .= $detail->barcode."\t".$detail->sku."\t".$detail->qty;
                        }
                    }
                }
                $response = Http::post('http://'.$client_ip.'/Integration/WebServiceIntegrationProductBarcodeLabelPrinter/Execute', [
                    'PrinterName' => $printer_name,
                    'BarTenderLabel' => $request->label.'.btw',
                    "FileNameDB" => $request->label.'.txt',
                    'EventData' => $EventData
                ]);
                if ($response->status() == "200") {
                    RequestLabelHeader::where('request_id', '=', $request->request_id)->update(["status"=>3]);
                } else {
                    $responseBody = $response->body();
                    return response()->json(['success' => false,'message' => $responseBody]);
                }
                return response()->json(['success' => true,'message' => 'ปริ้นเรียบร้อย']);
            }
            // ########################## end ##############################
        }
    }

    public function print_status($id)
    {
        if ($id != "") {
            $header = RequestLabelHeader::where('request_id', '=', $id)->first();
            if ($header) {
                if ($header->status == 1) {
                    $status = '<span class="badge bg-secondary fw-normal">ร้องขอ</span>';
                } else if ($header->status == 2) {
                    $status = '<span class="badge bg-warning fw-normal">รอคิวปริ้น</span>';
                } else if ($header->status == 3) {
                    $status = '<span class="badge bg-success fw-normal">เสร็จสิ้น</span>';
                } else {
                    $status = '';
                }
            } else {
                $status = '';
            }
            return response()->json(['success' => true,'message' => $status]);
        }
    }
}