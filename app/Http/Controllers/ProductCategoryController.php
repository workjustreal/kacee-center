<?php

namespace App\Http\Controllers;

use App\Models\EXProduct;
use App\Models\ProductGroupReport;
use App\Imports\ExcelImport;
use Maatwebsite\Excel\Facades\Excel;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        ini_set('memory_limit','512M');
    }

    public function index()
    {
        $sale_category = ProductGroupReport::where('sale_category', '<>', '')->groupBy("sale_category")->orderBy("sale_category", "asc")->select('sale_category')->get();
        $main_category = ProductGroupReport::where('main_category', '<>', '')->groupBy("main_category")->orderBy("main_category", "asc")->select('main_category')->get();
        $sec_category = ProductGroupReport::where('sec_category', '<>', '')->groupBy("sec_category")->orderBy("sec_category", "asc")->select('sec_category')->get();
        $online_category = ProductGroupReport::where('online_category', '<>', '')->groupBy("online_category")->orderBy("online_category", "asc")->select('online_category')->get();
        $daily_category = ProductGroupReport::where('daily_category', '<>', '')->groupBy("daily_category")->orderBy("daily_category", "asc")->select('daily_category')->get();
        return view('product.category-search', compact('sale_category', 'main_category', 'sec_category', 'online_category', 'daily_category'));
    }

    public function index2()
    {
        $sale_category = ProductGroupReport::where('sale_category', '<>', '')->groupBy("sale_category")->orderBy("sale_category", "asc")->select('sale_category')->get();
        $main_category = ProductGroupReport::where('main_category', '<>', '')->groupBy("main_category")->orderBy("main_category", "asc")->select('main_category')->get();
        $sec_category = ProductGroupReport::where('sec_category', '<>', '')->groupBy("sec_category")->orderBy("sec_category", "asc")->select('sec_category')->get();
        $online_category = ProductGroupReport::where('online_category', '<>', '')->groupBy("online_category")->orderBy("online_category", "asc")->select('online_category')->get();
        $daily_category = ProductGroupReport::where('daily_category', '<>', '')->groupBy("daily_category")->orderBy("daily_category", "asc")->select('daily_category')->get();
        return view('product.category-search2', compact('sale_category', 'main_category', 'sec_category', 'online_category', 'daily_category'));
    }

    public function product_search(Request $request)
    {
        if ($request->ajax()) {
            $data = EXProduct::from('ex_product as p')->leftjoin('product_group_report as r', 'p.stkcod', '=', 'r.stkcod')
                ->where('p.status', '=', 1)
                ->where(function ($query) use ($request) {
                    if (($sale_category = $request->sale_category)) {
                        if ($sale_category != "all") {
                            if ($sale_category == "none") {
                                $query->whereRaw('IFNULL(r.sale_category, "")=""');
                            } else {
                                $query->where('r.sale_category', '=', $sale_category);
                            }
                        }
                    }
                    if (($main_category = $request->main_category)) {
                        if ($main_category != "all") {
                            if ($main_category == "none") {
                                $query->whereRaw('IFNULL(r.main_category, "")=""');
                            } else {
                                $query->where('r.main_category', '=', $main_category);
                            }
                        }
                    }
                    if (($sec_category = $request->sec_category)) {
                        if ($sec_category != "all") {
                            if ($sec_category == "none") {
                                $query->whereRaw('IFNULL(r.sec_category, "")=""');
                            } else {
                                $query->where('r.sec_category', '=', $sec_category);
                            }
                        }
                    }
                    if (($online_category = $request->online_category)) {
                        if ($online_category != "all") {
                            if ($online_category == "none") {
                                $query->whereRaw('IFNULL(r.online_category, "")=""');
                            } else {
                                $query->where('r.online_category', '=', $online_category);
                            }
                        }
                    }
                    if (($daily_category = $request->daily_category)) {
                        if ($daily_category != "all") {
                            if ($daily_category == "none") {
                                $query->whereRaw('IFNULL(r.daily_category, "")=""');
                            } else {
                                $query->where('r.daily_category', '=', $daily_category);
                            }
                        }
                    }
                });

            $totalRecords = $data->select('count(p.*) as allcount')->count();
            $records = $data->select('p.stkcod', 'p.barcod', 'p.stkdes', 'r.sale_category', 'r.main_category', 'r.sec_category', 'r.online_category', 'r.daily_category', 'r.model', 'r.color_code', 'r.size', 'r.remark')->orderBy('p.stkcod', 'asc')->get()->toArray();

            $response = array(
                "draw" => 25,
                "data" => $records,
                "recordsTotal" => $totalRecords,
                "recordsFiltered" => $totalRecords,
            );
            unset($records);
            return response()->json($response);
        }
    }

    public function product_search2(Request $request)
    {
        if ($request->ajax()) {
            $data = EXProduct::from('ex_product as p')->leftjoin('product_group_report as r', 'p.stkcod', '=', 'r.stkcod')
                ->where('p.status', '=', 1)
                ->where(function ($query) use ($request) {
                    if (($sale_category = $request->sale_category)) {
                        if ($sale_category != "all") {
                            if ($sale_category == "none") {
                                $query->whereRaw('IFNULL(r.sale_category, "")=""');
                            } else {
                                $query->where('r.sale_category', '=', $sale_category);
                            }
                        }
                    }
                    if (($main_category = $request->main_category)) {
                        if ($main_category != "all") {
                            if ($main_category == "none") {
                                $query->whereRaw('IFNULL(r.main_category, "")=""');
                            } else {
                                $query->where('r.main_category', '=', $main_category);
                            }
                        }
                    }
                    if (($sec_category = $request->sec_category)) {
                        if ($sec_category != "all") {
                            if ($sec_category == "none") {
                                $query->whereRaw('IFNULL(r.sec_category, "")=""');
                            } else {
                                $query->where('r.sec_category', '=', $sec_category);
                            }
                        }
                    }
                    if (($online_category = $request->online_category)) {
                        if ($online_category != "all") {
                            if ($online_category == "none") {
                                $query->whereRaw('IFNULL(r.online_category, "")=""');
                            } else {
                                $query->where('r.online_category', '=', $online_category);
                            }
                        }
                    }
                    if (($daily_category = $request->daily_category)) {
                        if ($daily_category != "all") {
                            if ($daily_category == "none") {
                                $query->whereRaw('IFNULL(r.daily_category, "")=""');
                            } else {
                                $query->where('r.daily_category', '=', $daily_category);
                            }
                        }
                    }
                });

            $totalRecords = $data->select('count(p.*) as allcount')->count();
            $records = $data->select('p.stkcod', 'p.barcod', 'p.stkdes', 'r.sale_category', 'r.main_category', 'r.sec_category', 'r.online_category', 'r.daily_category', 'r.model', 'r.color_code', 'r.size', 'r.remark')->orderBy('p.stkcod', 'asc')->get();

            $isPer = false;
            if (auth()->user()->manageProductCat()) {
                $isPer = true;
            }

            $n = 1;
            $i = 0;
            $rows = [];
            foreach ($records as $rec) {
                $rows[$i] = array(
                    "no" => $n,
                    "stkcod" => $rec->stkcod,
                    "barcod" => $rec->barcod,
                    "stkdes" => $rec->stkdes,
                    "sale_category" => $rec->sale_category,
                    "main_category" => $rec->main_category,
                    "sec_category" => $rec->sec_category,
                    "model" => $rec->model,
                    "color_code" => $rec->color_code,
                    "size" => $rec->size,
                    "online_category" => $rec->online_category,
                    "daily_category" => $rec->daily_category,
                );
                if ($isPer) {
                    $manage = '<i class="fas fa-pen" role="button" onclick="edit(\''.$rec->stkcod.'\')" title="Edit"></i>';
                    $rows[$i]["manage"] = $manage;
                }
                $n++;
                $i++;
            }

            $response = array(
                "total" => $totalRecords,
                "totalNotFiltered" => $totalRecords,
                "rows" => $rows,
            );
            unset($records);
            unset($rows);
            return response()->json($response);
        }
    }

    public function stkcod_edit_search(Request $request)
    {
        $search = $request->get('search');
        $result = EXProduct::from('ex_product as p')->leftjoin('product_group_report as r', 'p.stkcod', '=', 'r.stkcod')
                ->where('p.status', '=', 1)->where('p.stkcod', 'LIKE', '%' . $search . '%')
                ->select('p.stkcod', 'p.stkdes', 'r.sale_category', 'r.main_category', 'r.sec_category', 'r.online_category', 'r.daily_category', 'r.model', 'r.color_code', 'r.size', 'r.remark')->first();
        return response()->json($result);
    }
    public function sale_category_search(Request $request)
    {
        $search = $request->get('search');
        $result = ProductGroupReport::where('sale_category', '<>', '')->where('sale_category', 'LIKE', '%' . $search . '%')->groupBy("sale_category")->orderBy("sale_category", "asc")->select('sale_category')->get();
        return response()->json($result);
    }
    public function main_category_search(Request $request)
    {
        $search = $request->get('search');
        $result = ProductGroupReport::where('main_category', '<>', '')->where('main_category', 'LIKE', '%' . $search . '%')->groupBy("main_category")->orderBy("main_category", "asc")->select('main_category')->get();
        return response()->json($result);
    }
    public function sec_category_search(Request $request)
    {
        $search = $request->get('search');
        $result = ProductGroupReport::where('sec_category', '<>', '')->where('sec_category', 'LIKE', '%' . $search . '%')->groupBy("sec_category")->orderBy("sec_category", "asc")->select('sec_category')->get();
        return response()->json($result);
    }
    public function online_category_search(Request $request)
    {
        $search = $request->get('search');
        $result = ProductGroupReport::where('online_category', '<>', '')->where('online_category', 'LIKE', '%' . $search . '%')->groupBy("online_category")->orderBy("online_category", "asc")->select('online_category')->get();
        return response()->json($result);
    }
    public function daily_category_search(Request $request)
    {
        $search = $request->get('search');
        $result = ProductGroupReport::where('daily_category', '<>', '')->where('daily_category', 'LIKE', '%' . $search . '%')->groupBy("daily_category")->orderBy("daily_category", "asc")->select('daily_category')->get();
        return response()->json($result);
    }
    public function model_search(Request $request)
    {
        $search = $request->get('search');
        $result = ProductGroupReport::where('model', '<>', '')->where('model', 'LIKE', '%' . $search . '%')->groupBy("model")->orderBy("model", "asc")->select('model')->get();
        return response()->json($result);
    }
    public function color_code_search(Request $request)
    {
        $search = $request->get('search');
        $result = ProductGroupReport::where('color_code', '<>', '')->where('color_code', 'LIKE', '%' . $search . '%')->groupBy("color_code")->orderBy("color_code", "asc")->select('color_code')->get();
        return response()->json($result);
    }
    public function size_search(Request $request)
    {
        $search = $request->get('search');
        $result = ProductGroupReport::where('size', '<>', '')->where('size', 'LIKE', '%' . $search . '%')->groupBy("size")->orderBy("size", "asc")->select('size')->get();
        return response()->json($result);
    }
    public function category_edit_update(Request $request)
    {
        $request->validate([
            'stkcod' => 'required',
        ],[
            'stkcod.required' => 'ไม่พบรหัสสินค้า',
        ]);

        $user = auth()->user();

        $category = ProductGroupReport::where('stkcod', '=', $request->stkcod);
        $category->update([
            "sale_category" => $request->sale_category,
            "main_category" => $request->main_category,
            "sec_category" => $request->sec_category,
            "online_category" => $request->online_category,
            "daily_category" => $request->daily_category,
            "model" => $request->model,
            "color_code" => $request->color_code,
            "size" => $request->size,
            "userid" => $user->id,
            "userip" => $request->ip(),
            "updated_at" => now(),
        ]);
        return response()->json(["success"=>true, "message"=>"บันทึกข้อมูลเรียบร้อย"]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required',
        ],[
            'file.required' => 'ยังไม่ได้เลือกไฟล์',
        ]);
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = 'product_category';
            $input['filename'] = $fileName . '.' . $file->extension();
            $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/assets/uploads/excel/';
            $file->move($destinationPath, $input['filename']);
            $fileName = $input['filename'];

            $product = EXProduct::select('stkcod')->get()->toArray();
            $productCat = ProductGroupReport::select('stkcod')->get()->toArray();

            $data = Excel::toCollection(new ExcelImport, $destinationPath.$fileName)->toArray();
            $result= [];
            $i = 0;
            foreach ($data[0] as $value) {
                if ($i > 1) {
                    if (strlen($value[1]) >= 3) {
                        $pkey = array_search(trim($value[1]), array_column($product, 'stkcod'));
                        if ($pkey !== false) {
                            $gkey = array_search(trim($value[1]), array_column($productCat, 'stkcod'));
                            if ($gkey !== false) {
                                $action = "update";
                            } else {
                                $action = "insert";
                            }
                        } else {
                            $action = "null";
                        }
                        $result[] = [
                            "stkcod" => trim($value[1]),
                            "barcod" => trim($value[2]),
                            "stkdes" => trim($value[3]),
                            "sale_category" => trim($value[4]),
                            "main_category" => trim($value[5]),
                            "sec_category" => trim($value[6]),
                            "model" => trim($value[7]),
                            "color_code" => trim($value[8]),
                            "size" => trim($value[9]),
                            "online_category" => trim($value[10]),
                            "daily_category" => trim($value[11]),
                            "action" => trim($action),
                        ];
                    }
                }
                $i++;
            }
            unset($product);
            unset($productCat);
            unset($data);
            if (File::exists($destinationPath.$fileName)) {
                File::delete($destinationPath.$fileName);
            }
            if (count($result) > 200) {
                alert()->warning('อัปเดตข้อมูลได้ครั้งละไม่เกิน 200 รายการ');
                return view('product.category-upload')->with('data', []);
            }
            session()->put('product_category', []);
            session()->put('product_category', $result);
            return view('product.category-upload')->with('data', $result);
        }
    }

    public function update(Request $request)
    {
        $result= [];
        if (session()->get('product_category')) {
            $sess = session()->get('product_category');
            $count = count($sess);
            for ($i = 0; $i < $count; $i++) {
                $result[] = [
                    "stkcod" => $sess[$i]["stkcod"],
                    "sale_category" => (is_null($sess[$i]["sale_category"])) ? '' : $sess[$i]["sale_category"],
                    "main_category" => (is_null($sess[$i]["main_category"])) ? '' : $sess[$i]["main_category"],
                    "sec_category" => (is_null($sess[$i]["sec_category"])) ? '' : $sess[$i]["sec_category"],
                    "online_category" => (is_null($sess[$i]["online_category"])) ? '' : $sess[$i]["online_category"],
                    "daily_category" => (is_null($sess[$i]["daily_category"])) ? '' : $sess[$i]["daily_category"],
                    "model" => (is_null($sess[$i]["model"])) ? '' : $sess[$i]["model"],
                    "color_code" => (is_null($sess[$i]["color_code"])) ? '' : $sess[$i]["color_code"],
                    "size" => (is_null($sess[$i]["size"])) ? '' : $sess[$i]["size"],
                    "action" => (is_null($sess[$i]["action"])) ? '' : $sess[$i]["action"],
                ];
                if ($sess[$i]["action"] == "null") {
                    alert()->warning('พบข้อมูลบางรายการที่ไม่มีในสินค้า');
                    return back();
                }
            }
        }
        if ($result) {
            if (count($result) > 200) {
                alert()->warning('อัปเดตข้อมูลได้ครั้งละไม่เกิน 200 รายการ');
                return back();
            }
            $user = auth()->user();

            $success = false;
            DB::beginTransaction();
            try {
                $count = count($result);
                for ($i = 0; $i < $count; $i++) {
                    if ($result[$i]["action"] == "update") {
                        // update ข้อมูล
                        $category = ProductGroupReport::where('stkcod', '=', $result[$i]["stkcod"]);
                        $category->update([
                            "sale_category" => $result[$i]["sale_category"],
                            "main_category" => $result[$i]["main_category"],
                            "sec_category" => $result[$i]["sec_category"],
                            "online_category" => $result[$i]["online_category"],
                            "daily_category" => $result[$i]["daily_category"],
                            "model" => $result[$i]["model"],
                            "color_code" => $result[$i]["color_code"],
                            "size" => $result[$i]["size"],
                            "userid" => $user->id,
                            "userip" => $request->ip(),
                            "updated_at" => now(),
                        ]);
                    } else if ($result[$i]["action"] == "insert") {
                        // insert ข้อมูลใหม่
                        $category = new ProductGroupReport();
                        $category->stkcod = $result[$i]["stkcod"];
                        $category->sale_category = $result[$i]["sale_category"];
                        $category->main_category = $result[$i]["main_category"];
                        $category->sec_category = $result[$i]["sec_category"];
                        $category->online_category = $result[$i]["online_category"];
                        $category->daily_category = $result[$i]["daily_category"];
                        $category->model = $result[$i]["model"];
                        $category->color_code = $result[$i]["color_code"];
                        $category->size = $result[$i]["size"];
                        $category->userid = $user->id;
                        $category->userip = $request->ip();
                        $category->save();
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
                alert()->error('เกิดข้อผิดพลาด ไม่สามารถบันทึกข้อมูลได้ ' . $e->getMessage());
                return back();
            }
            session()->forget('product_category');
            alert()->success('บันทึกข้อมูลเรียบร้อย');
            return redirect('/product/category-search');
        }
    }
}