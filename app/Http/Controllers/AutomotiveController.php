<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Automotive;
use App\Models\Department;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AutomotiveController extends Controller
{
    public function selectAll(){
        $brands = DB::select('select brand_id, brand_name from car_brand');
        $types = DB::select('select type_id, type_name from car_types');
        $colors = DB::select('select color_id, color_name from car_color');
        $department = Department::all();
        return compact('brands', 'types', 'colors', 'department');
    }

    public function index(){
        $data = self::selectAll();
        return view('automotive.index', $data);
    }

    public function main(){
        $data = self::selectAll();
        return view('automotive.types.index', $data);
    }

    public function add($request){
        $brands = DB::select('select brand_id, brand_name from car_brand');
        return view('automotive.types.create', compact('request', 'brands'));
    }

    public function show($id){
        $car = Automotive::where('id', $id)->first();
        $manageCar = self::manageCar($car->brand, $car->type, $car->model);
        return view('automotive.show', compact('car', 'manageCar'));
    }

    public function create(){
        $data = self::selectAll();
        return view('automotive.create', $data);
    }

    public function edit($id){
        $car = Automotive::where('id', $id)->first();
        $brands = DB::select('select brand_id, brand_name from car_brand');
        $types = DB::select('select type_id, type_name from car_types');
        $colors = DB::select('select color_id, color_name from car_color');
        $department = Department::all();
        return view('automotive.edit', compact('car', 'brands', 'types', 'colors', 'department'));
    }

    public function editType($id, $name){
        $_brand = DB::select('select brand_id, brand_name from car_brand');
        $brands = DB::select('SELECT brand_id, brand_name, comment FROM car_brand WHERE brand_id = ?', [$id])[0];
        $types = DB::select('SELECT type_id, type_name, comment FROM car_types WHERE type_id = ?', [$id])[0];
        $models = DB::select('SELECT model_id, model_name, brand_id, comment FROM car_model WHERE model_id = ?', [$id])[0];
        return view('automotive.types.edit', compact('name', 'brands', 'types', 'models', '_brand'));
    }
    

    public function get_model_parent($id){
        // dd($id);
        $models = DB::select('select model_id, model_name from car_model where brand_id = ?', [$id]);
        return response()->json(["data" => $models]);
    }

    public function searchAuto(Request $request){
        $search = $request->get('search');
        $result = DB::table('car_main')->where('dept_id', 'LIKE', '%'.$search.'%')
            ->orderBy('dept_id', 'ASC')
            ->groupBy('dept_id')
            ->take(10)
            ->get(['dept_id']);
        return response()->json($result);
    }

    // ********************************** Manage ***************************************

    public function manageEditor($id, $page){
        switch ($page) {
            case 'INDEX':
                $result = '<a class="action-icon" href="'.url('automotive/show', $id).'" title="ดูรายละเอียด"><i class="mdi mdi-eye"></i></a>
                    <a class="action-icon" href="'.url('automotive/edit', $id).'" title="แก้ไข"><i class="mdi mdi-square-edit-outline"></i></a>
                    <a class="action-icon" href="javascript:void(0);" onclick="deleteCarConfirmation(\''.$id.'\')" title="ลบ"><i class="mdi mdi-delete"></i></a>
                ';
                break;
            case 'BRAND':
                $result = '<a class="action-icon" href="'.url('automotive/edit-type', $id.'/'.$page).'" title="แก้ไข"><i class="mdi mdi-square-edit-outline"></i></a>
                    <a class="action-icon" href="javascript:void(0);" onclick="deleteConfirmation(\''.$id.'\', \''."BRAND".'\')" title="ลบ"><i class="mdi mdi-delete"></i></a>
                ';
                break;
            case 'MODEL':
                $result = '<a class="action-icon" href="'.url('automotive/edit-type', $id.'/'.$page).'" title="แก้ไข"><i class="mdi mdi-square-edit-outline"></i></a>
                    <a class="action-icon" href="javascript:void(0);" onclick="deleteConfirmation(\''.$id.'\', \''."MODEL".'\')" title="ลบ"><i class="mdi mdi-delete"></i></a>
                ';
                break;
            case 'TYPES':
                $result = '<a class="action-icon" href="'.url('automotive/edit-type', $id.'/'.$page).'" title="แก้ไข"><i class="mdi mdi-square-edit-outline"></i></a>
                    <a class="action-icon" href="javascript:void(0);" onclick="deleteConfirmation(\''.$id.'\', \''."TYPES".'\')" title="ลบ"><i class="mdi mdi-delete"></i></a>
                ';
                break;
        }
        return $result;
    }

    public function manageStatus($status){
        switch ($status) {
            case 0:
                $res = '<span class="badge bg-danger">เลิกใช้งาน</span>';
                break;
            case 1:
                $res = '<span class="badge bg-success">ปกติ</span>';
                break;
            default:
                $res = '';
                break;
        }
        return $res;
    }

    public function manageCar($brand, $types, $model){
        $_brand = DB::table('car_brand')->where('brand_id', $brand)->first();
        $_types = DB::table('car_types')->where('type_id', $types)->first();
        $_model = DB::table('car_model')->where('brand_id', $brand)->where('model_id', $model)->first();
        return compact('_brand', '_types', '_model');
    }

    // ********************************** Search ***************************************

    public function search(Request $request){
        if ($request->ajax()) {      
            $data = Automotive::where('id', '<>', '');
            $totalRecords = $data->select('count(*) as allcount')->count();
            $records = $data->select('*')
                ->where(function ($query) use ($request) {
                    if ($request->brand_category != "") { $query->where('brand', '=', $request->brand_category); }
                    if ($request->model_category != "") { $query->where('model', '=', $request->model_category); }
                    if ($request->type_category != "") { $query->where('type', '=', $request->type_category); }
                    if ($request->dept_category != "") { $query->where('dept_id', '=', $request->dept_category); }
                })
                ->orderBy('id', 'ASC')
                ->get();
            
            $rows = [];
            foreach ($records as $rec) {
                $status = self::manageStatus($rec->status);
                $action = self::manageEditor($rec->id, 'INDEX');
                $_manageCar = self::manageCar($rec->brand, $rec->type, $rec->model);
                $_models = $_manageCar['_model'] ? $_manageCar['_model']->model_name : '-';

                $rows[] = array(
                    "id" => $rec->id,
                    "car_id" => $rec->car_id,
                    "brand" => $_manageCar['_brand']->brand_name,
                    "model" => $_models,
                    "type" => $_manageCar['_types']->type_name,
                    "color" => $rec->color,
                    "dept_id" => $rec->dept_id,
                    "status" => $status,
                    "action" => $action,
                );
            }

            $response = array(
                "total" => $totalRecords,
                "totalNotFiltered" => $totalRecords,
                "rows" => $rows,
            );
            return response()->json($response);dd($request->page);
        }
    }

    public function searchBrand(Request $request){
        if ($request->ajax()) {      
            $data = DB::select('select brand_id, brand_name from car_brand order by brand_id ASC');
            $rows = [];
            foreach ($data as $rec) {
                $action = self::manageEditor($rec->brand_id, 'BRAND');
                $rows[] = array(
                    "id" => $rec->brand_id,
                    "brand" => $rec->brand_name,
                    "action" => $action
                );
            }

            $response = array(
                "total" => count($data),
                "totalNotFiltered" => count($data),
                "rows" => $rows,
            );
            return response()->json($response);dd($request->page);
        }
    }

    public function searchModel(Request $request){
        if ($request->ajax()) {      
            $data = DB::select('select model_id, model_name, brand_id from car_model order by model_id ASC');
            $rows = [];
            foreach ($data as $rec) {
                $action = self::manageEditor($rec->model_id, 'MODEL');
                $_manageCar = self::manageCar($rec->brand_id, '', '');
                $_brand = $_manageCar['_brand'] ? $_manageCar['_brand']->brand_name : '-';
                $rows[] = array(
                    "id" => $rec->model_id,
                    "brand" => $_brand,
                    "model" => $rec->model_name,
                    "action" => $action
                );
            }

            $response = array(
                "total" => count($data),
                "totalNotFiltered" => count($data),
                "rows" => $rows,
            );
            return response()->json($response);dd($request->page);
        }
    }

    public function searchTypes(Request $request){
        if ($request->ajax()) {      
            $data = DB::select('select type_id, type_name from car_types order by type_id ASC');
            $rows = [];
            foreach ($data as $rec) {
                $action = self::manageEditor($rec->type_id, 'TYPES');

                $rows[] = array(
                    "id" => $rec->type_id,
                    "type" => $rec->type_name,
                    "action" => $action
                );
            }

            $response = array(
                "total" => count($data),
                "totalNotFiltered" => count($data),
                "rows" => $rows,
            );
            return response()->json($response);dd($request->page);
        }
    }

    // ********************************** ACTION ***************************************

    public function store(Request $request){

        if ($request->SQL == 'INS') {
            $request->validate([
                'car_id' => 'required|unique:car_main,car_id',
                'car_brand' => 'required',
                'car_model' => 'required',
                'car_type' => 'required',
                'car_color' => 'required',
                'dept_id' => 'required',
                'car_status' => 'required',
            ],[
                'car_id.required' => 'กรุณากรอกทะเบียนรถ',
                'car_id.unique' => 'ทะเบียนรถต้องเป็นไม่ซ้ำ',
                'car_brand.required' => 'กรุณาเลือกยี่ห้อของรถ',
                'car_model.required' => 'กรุณาเลือกโมเดลของรถ',
                'car_type.required' => 'กรุณาเลือกประเภทรถ',
                'car_color.required' => 'กรุณาเลือกสีรถ',
                'car_status.required' => 'กรุณาเลือกสถานะการใช้งาน',
                'dept_id.required' => 'กรุณาเลือกแผนก/หน่วยงาน',
            ]);
            $car = new Automotive();
            $car->car_id = $request->car_id;
            $car->brand = $request->car_brand;
            $car->model = $request->car_model;
            $car->type = $request->car_type;
            $car->color = $request->car_color;
            $car->dept_id = $request->dept_id;
            $car->status = $request->car_status;
            $car->save();

            alert()->success('เพิ่มข้อมูลรถเรียบร้อย');
            return redirect('automotive/automotive');
        }
        
        elseif ($request->SQL == 'EDIT') {
            Automotive::where('id', '=', $request->ID)->update([
                "car_id" => $request->car_id,
                "brand" => $request->car_brand,
                "model" => $request->car_model,
                "type" => $request->car_type,
                "color" => $request->car_color,
                "dept_id" => $request->dept_id,
                "status" => $request->car_status,
                "comment" => $request->car_detail,
            ]);
            
            alert()->success('แก้ไขข้อมูลเรียบร้อย');
            return redirect('automotive/automotive');
        }
    }

    public function destroy($id){
        if ($id != "") {
            Automotive::where('id', '=', $id)->delete();
            return response()->json([
                'success' => true,
                'message' => 'ลบข้อมูลเรียบร้อย',
            ]);
        }
    } 
    
    public function storeType(Request $request){
        // dd($request);
        if ($request->SQL == 'INS') {
            switch ($request->page) {
                case 'brand':
                    $request->validate(['brand_name' => 'required',],['brand_name.required' => 'กรุณาป้อนยี่ห้อรถ']);

                    DB::table('car_brand')->insert([
                        'brand_name' => $request->brand_name, 
                        'comment' => $request->comment
                    ]);

                    break;
                case 'model':
                    $request->validate(['brand_id' => 'required', 'model_name' => 'required'
                    ],[
                        'brand_id.required' => 'กรุณาเลือกยี่ห้อรถ',
                        'model_name.required' => 'กรุณาป้อนรุ่นรถ',
                    ]);

                    DB::table('car_model')->insert([
                        'brand_id' => $request->brand_id, 
                        'model_name' => $request->model_name, 
                        'comment' => $request->comment]
                    );

                    break;
                case 'types':
                    $request->validate(['type_name' => 'required'],['type_name.required' => 'กรุณาป้อนประเภทรถ']);

                    DB::table('car_types')->insert([
                        'type_name' => $request->type_name, 
                        'comment' => $request->comment]
                    );
                    break;
            }

            alert()->success('เพิ่มข้อมูลเรียบร้อย');
            return redirect('automotive/main');
        }
        
        elseif ($request->SQL == 'EDIT') {
            switch ($request->page) {
                case 'brand':
                    DB::table('car_brand')->where('brand_id', $request->ID)
                        ->update([  
                            'brand_name' => $request->brand_name,
                            'comment' => $request->comment,
                        ]);

                    break;
                case 'model':
                    DB::table('car_model')->where('model_id', $request->ID)
                        ->update([  
                            'brand_id' => $request->brand_id,
                            'model_name' => $request->model_name,
                            'comment' => $request->comment,
                        ]);

                    break;
                case 'types':
                    DB::table('car_types')->where('type_id', $request->ID)
                        ->update([  
                            'type_name' => $request->type_name,
                            'comment' => $request->comment,
                        ]);
                    break;
            }
            
            alert()->success('แก้ไขข้อมูลเรียบร้อย');
            return redirect('automotive/main');
        }
    }

    public function delete($id, $name){
        if ($id != "") {
            switch ($name) {
            case 'BRAND':
                DB::table('car_brand')->where('brand_id' , $id)->delete();
                return response()->json(['success' => true,'message' => 'ลบข้อมูลเรียบร้อย',]);
                break;
            
            case 'MODEL':
                DB::table('car_model')->where('model_id' , $id)->delete();
                return response()->json(['success' => true,'message' => 'ลบข้อมูลเรียบร้อย',]);
                break;

            case 'TYPES':
                DB::table('car_types')->where('type_id' , $id)->delete();
                return response()->json(['success' => true,'message' => 'ลบข้อมูลเรียบร้อย',]);
                break;
            }
        }
    }

    
}


