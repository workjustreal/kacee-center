<?php

namespace App\Http\Controllers\Middleware;

use App\Imports\ExcelImport;
use App\Http\Controllers\Controller;
use App\Models\Middleware\ProductOnline;
use Maatwebsite\Excel\Facades\Excel;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductOnlineController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $category = ProductOnline::where('category', '<>', '')->groupBy("category")->orderBy("category", "asc")->get(['category']);
        return view('middleware.product-online', compact('category'));
    }

    public function search(Request $request)
    {
        if ($request->ajax()) {
            $data = ProductOnline::where('sku', '<>', '')
                ->where(function ($query) use ($request) {
                    if ($request->category != "all") {
                        if ($request->category == "none") {
                            $query->whereRaw('IFNULL(category, "")=""');
                        } else {
                            $query->where('category', '=', $request->category);
                        }
                    }
                });

            $totalRecords = $data->select('count(*) as allcount')->count();
            $records = $data->select('id', 'sku', 'name', 'description', 'category', 'remark', 'updated_by')->orderBy('sku', 'asc')->get();

            $isPer = false;
            if (auth()->user()->manageProductCatOnline() || auth()->user()->isDeptSaleOnline()) {
                $isPer = true;
            }

            $n = 1;
            $i = 0;
            $rows = [];
            foreach ($records as $rec) {
                $rows[$i] = array(
                    "no" => $n,
                    "sku" => $rec->sku,
                    "name" => $rec->name,
                    "category" => $rec->category,
                );
                if ($isPer) {
                    $manage = '<i class="fas fa-pen" role="button" onclick="edit(\''.$rec->id.'\', \''.$rec->sku.'\')" title="Edit"></i>';
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

    public function sku_edit_search(Request $request)
    {
        $id = $request->get('id');
        $search = $request->get('search');
        $result = ProductOnline::where('id', '=', $id)->where('sku', '<>', '')->where('sku', 'LIKE', '%' . $search . '%')->first(['id', 'sku', 'name', 'description', 'category', 'remark', 'updated_by']);
        return response()->json($result);
    }

    public function category_search(Request $request)
    {
        $search = $request->get('search');
        $result = ProductOnline::where('category', '<>', '')->where('category', 'LIKE', '%' . $search . '%')->groupBy("category")->orderBy("category", "asc")->select('category')->get();
        return response()->json($result);
    }

    public function category_edit_update(Request $request)
    {
        $request->validate([
            'sku' => 'required',
        ],[
            'sku.required' => 'ไม่พบรหัสสินค้า',
        ]);

        $user = auth()->user();

        $category = ProductOnline::where('id', '=', $request->id)->where('sku', '=', $request->sku);
        $category->update([
            "name" => $request->name,
            "category" => $request->category,
            "updated_by" => $user->emp_id,
            "updated_at" => now(),
        ]);
        return response()->json(["success"=>true, "message"=>"บันทึกข้อมูลเรียบร้อย"]);
    }

    public function category_edit_delete(Request $request)
    {
        $request->validate([
            'sku' => 'required',
        ],[
            'sku.required' => 'ไม่พบรหัสสินค้า',
        ]);

        ProductOnline::where('id', '=', $request->id)->where('sku', '=', $request->sku)->delete();

        return response()->json(["success"=>true, "message"=>"ลบข้อมูลเรียบร้อย"]);
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
            $fileName = 'product_online';
            $input['filename'] = $fileName . '.' . $file->extension();
            $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/assets/uploads/excel/';
            $file->move($destinationPath, $input['filename']);
            $fileName = $input['filename'];

            $productOnline = ProductOnline::select('sku')->get()->toArray();

            $data = Excel::toCollection(new ExcelImport, $destinationPath.$fileName)->toArray();
            $result= [];
            $i = 0;
            foreach ($data[0] as $value) {
                if ($i > 0) {
                    if (strlen($value[1]) >= 3) {
                        $sku = preg_replace('/\s/u', ' ', trim($value[1]));
                        $name = preg_replace('/\s/u', ' ', trim($value[2]));
                        $category = preg_replace('/\s/u', ' ', trim($value[3]));
                        $add = false;
                        $key = array_search(trim($sku), array_column($productOnline, 'sku'));
                        if ($key !== false) {
                            $action = "update";
                        } else {
                            $action = "insert";
                        }
                        $chk_duplicate = trim($sku) . "-" . trim($category);
                        $key_duplicate = array_search($chk_duplicate, array_column($result, 'chk_duplicate'));
                        if ($key_duplicate === false) {
                            $key_duplicate = array_search(trim($sku), array_column($result, 'sku'));
                            if ($key_duplicate !== false && $result[$key_duplicate]["category"] != trim($category)) {
                                $action = "duplicate";
                                $add = true;
                            } else {
                                $add = true;
                            }
                        }
                        if ($add == true) {
                            $result[] = [
                                "sku" => trim($sku),
                                "name" => trim($name),
                                "category" => trim($category),
                                "chk_duplicate" => $chk_duplicate,
                                "action" => trim($action),
                            ];
                        }
                    }
                }
                $i++;
            }
            unset($productOnline);
            unset($data);
            if (File::exists($destinationPath.$fileName)) {
                File::delete($destinationPath.$fileName);
            }
            if (count($result) > 200) {
                alert()->warning('อัปเดตข้อมูลได้ครั้งละไม่เกิน 200 รายการ');
                return view('middleware.product-online-upload')->with('data', []);
            }
            session()->put('product_online', []);
            session()->put('product_online', $result);
            return view('middleware.product-online-upload')->with('data', $result);
        }
    }

    public function update(Request $request)
    {
        $route_back = "/middleware/product-online/category-file";
        $result= [];
        if (session()->get('product_online')) {
            $sess = session()->get('product_online');
            $count = count($sess);
            for ($i = 0; $i < $count; $i++) {
                $result[] = [
                    "sku" => $sess[$i]["sku"],
                    "name" => (is_null($sess[$i]["name"])) ? '' : $sess[$i]["name"],
                    "category" => (is_null($sess[$i]["category"])) ? '' : $sess[$i]["category"],
                    "action" => (is_null($sess[$i]["action"])) ? '' : $sess[$i]["action"],
                ];
                if ($sess[$i]["action"] == "null") {
                    alert()->warning('พบข้อมูลบางรายการที่ไม่มีในสินค้า');
                    return redirect($route_back);
                }
            }
        }
        if ($result) {
            if (count($result) > 200) {
                alert()->warning('อัปเดตข้อมูลได้ครั้งละไม่เกิน 200 รายการ');
                return redirect($route_back);
            }
            $user = auth()->user();

            $success = false;
            DB::beginTransaction();
            try {
                $count = count($result);
                // เช็คข้อมูลซ้ำ
                for ($i = 0; $i < $count; $i++) {
                    if ($result[$i]["action"] == "insert") {
                        $check_duplicate = ProductOnline::where('sku', '=', $result[$i]["sku"])->count();
                        if ($check_duplicate > 0) {
                            alert()->warning('รหัสสินค้า: ' . $result[$i]["sku"] . ' ซ้ำ!');
                            return redirect($route_back);
                        }
                    }
                }
                for ($i = 0; $i < $count; $i++) {
                    if ($result[$i]["action"] == "update") {
                        // update ข้อมูล
                        $product = ProductOnline::where('sku', '=', $result[$i]["sku"]);
                        $product->update([
                            "name" => $result[$i]["name"],
                            "category" => $result[$i]["category"],
                            "updated_by" => $user->emp_id,
                            "updated_at" => now(),
                        ]);
                    } else if ($result[$i]["action"] == "insert") {
                        // insert ข้อมูลใหม่
                        $product = new ProductOnline();
                        $product->sku = $result[$i]["sku"];
                        $product->name = $result[$i]["name"];
                        $product->category = $result[$i]["category"];
                        $product->created_by = $user->emp_id;
                        $product->save();
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
                return redirect($route_back);
            }
            session()->forget('product_online');
            alert()->success('บันทึกข้อมูลเรียบร้อย');
            return redirect('/middleware/product-online');
        }
    }
}