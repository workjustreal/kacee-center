<?php

namespace App\Http\Controllers;

use App\Exports\CheckstockExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Jenssegers\Agent\Agent;
use File;
use Illuminate\Support\Facades\Auth;

class CheckstockController extends Controller
{
    protected $user;
    protected $destinationPath;
    protected $fileName;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            $this->fileName = 'checkstock_'.$this->user->emp_id.'.json';
            return $next($request);
        });
        $this->destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/assets/uploads/txt/';
    }

    public function index()
    {
        $agent = new Agent();
        if ($agent->isMobile()) {
            $view = 'store.checkstock-mobile';
        } else {
            $view = 'store.checkstock';
        }
        return view($view);
    }

    public function readFile()
    {
        // Read File
        if (!File::exists($this->destinationPath . $this->fileName)) {
            File::put($this->destinationPath . $this->fileName, []);
        }
        $jsonString = file_get_contents($this->destinationPath . $this->fileName);
        $data = json_decode($jsonString, true);
        return $data;
    }

    public function writeFile($data)
    {
        // Write File
        $data = json_encode($data, JSON_PRETTY_PRINT);
        File::put($this->destinationPath . $this->fileName, $data);
    }

    public function data(Request $request)
    {
        $search = $request->search;
        $qty = $request->qty;

        $output = array();

        if ($search == "") {
            $output['status'] = 0;
            $output['msg'] = "โปรดใส่ข้อมูลสินค้า!";
            return response()->json($output);
        }
        $result = Product::where('status', '=', 1)->where('stktyp', '=', '0')->where(function ($query) use ($request) {
            if (($search = $request->search)) {
                if (strpos($search, "|")) {
                    $exp = explode("|", $search);
                    $barcode = $exp[0];
                    $query->where('barcod', 'LIKE', '%' . trim(str_replace(' ', '%', $barcode)) . '%');
                } else {
                    $query->orWhere('stkcod', 'LIKE', '%' . trim(str_replace(' ', '%', $search)) . '%');
                    $query->orWhere('barcod', 'LIKE', '%' . trim(str_replace(' ', '%', $search)) . '%');
                }
            }
        })->limit(1)->get();

        if ($result->isEmpty()) {
            $output['status'] = 0;
            $output['msg'] = "ไม่พบข้อมูลสินค้า!";
            return response()->json($output);
        } else {
            // Read File
            $data = self::readFile();

            // $data = session()->get('checkstock_cart');
            $data_detail = ($data != null) ? $data : [];
            $index_search = array_search($result[0]->stkcod, array_column($data_detail, 'sku'));
            if ($index_search !== false) {
                $data[$index_search]['qty'] += $qty;
            } else {
                $i = (isset($data)) ? count($data) : 0;
                $data[$i]['sku'] = $result[0]->stkcod;
                $data[$i]['name'] = $result[0]->stkdes;
                $data[$i]['barcode'] = $result[0]->barcod;
                $data[$i]['qty'] = $qty;
            }
            // Write File
            self::writeFile($data);

            // Read File
            $data = self::readFile();

            // $data = session()->get('checkstock_cart');
            if (isset($data)) {
                $output['status'] = 1;
                $output['count'] = count($data);
                $output['index'] = ($index_search === false) ? count($data)-1 : $index_search;
                $output['new'] = ($index_search === false) ? 1 : 0;
                $output['detail'] = '';
                $i = 0;
                $total = 0;
                $agent = new Agent();
                if ($agent->isMobile()) {
                    foreach ($data as $data) {
                        $highlight = '';
                        if ($output['index'] == $i) {
                            $highlight = 'class="table-primary"';
                        }
                        $output['detail'] .= '
                        <tr ' . $highlight . '>
                            <td><span id="index[' . $i . ']"></span>' . ($i + 1) . '</td>
                            <td><b>' . $data['sku'] . '</b><br><small class="text-blue fst-italic">' . $data['barcode'] . '</small><br><small>' . Str::limit($data['name'], 30) . '</small></td>
                            <td>' . $data['qty'] . '</td>
                            <td>
                                <button type="button" class="btn btn-xs btn-danger mb-1" onclick="negative(' . $i . ');">-1</button>
                                <button type="button" class="btn btn-xs btn-danger" onclick="remove(' . $i . ');">ลบ</button>
                            </td>
                        </tr>';
                        $total += $data['qty'];
                        $i++;
                    }
                } else {
                    foreach ($data as $data) {
                        $highlight = '';
                        if ($output['index'] == $i) {
                            $highlight = 'class="table-primary"';
                        }
                        $output['detail'] .= '
                        <tr ' . $highlight . '>
                            <td><span id="index[' . $i . ']"></span>' . ($i + 1) . '</td>
                            <td>' . $data['sku'] . '</td>
                            <td>' . $data['name'] . '</td>
                            <td>' . $data['barcode'] . '</td>
                            <td>' . $data['qty'] . '</td>
                            <td>
                                <button type="button" class="btn btn-xs btn-danger" onclick="negative(' . $i . ');">-1</button>
                                <button type="button" class="btn btn-xs btn-danger" onclick="remove(' . $i . ');">ลบ</button>
                            </td>
                        </tr>';
                        $total += $data['qty'];
                        $i++;
                    }
                }
                $output['total'] = $total;
            }
        }

        return response()->json($output);
    }

    public function loaddata()
    {
        $output = array();

        // Read File
        $data = self::readFile();

        // $data = session()->get('checkstock_cart');
        if (isset($data)) {
            $output['status'] = 1;
            $output['count'] = count($data);
            $output['detail'] = '';
            $i = 0;
            $total = 0;
            $agent = new Agent();
            if ($agent->isMobile()) {
                foreach ($data as $data) {
                    $output['detail'] .= '
                    <tr>
                        <td><span id="index[' . $i . ']"></span>' . ($i + 1) . '</td>
                        <td><b>' . $data['sku'] . '</b><br><small class="text-blue fst-italic">' . $data['barcode'] . '</small><br><small>' . Str::limit($data['name'], 30) . '</small></td>
                        <td>' . $data['qty'] . '</td>
                        <td>
                        <button type="button" class="btn btn-xs btn-danger mb-1" onclick="negative(' . $i . ');">-1</button>
                        <button type="button" class="btn btn-xs btn-danger" onclick="remove(' . $i . ');">ลบ</button>
                        </td>
                    </tr>';
                    $total += $data['qty'];
                    $i++;
                }
            } else {
                foreach ($data as $data) {
                    $output['detail'] .= '
                    <tr>
                        <td><span id="index[' . $i . ']"></span>' . ($i + 1) . '</td>
                        <td>' . $data['sku'] . '</td>
                        <td>' . $data['name'] . '</td>
                        <td>' . $data['barcode'] . '</td>
                        <td>' . $data['qty'] . '</td>
                        <td>
                        <button type="button" class="btn btn-xs btn-danger" onclick="negative(' . $i . ');">-1</button>
                        <button type="button" class="btn btn-xs btn-danger" onclick="remove(' . $i . ');">ลบ</button>
                        </td>
                    </tr>';
                    $total += $data['qty'];
                    $i++;
                }
            }
            $output['total'] = $total;
        }

        return response()->json($output);
    }

    public function negative(Request $request)
    {
        $id = $request->id;
        // Read File
        $data = self::readFile();
        // $data = session()->get('checkstock_cart');
        if (($data[$id]['qty'] - 1) <= 0) {
            unset($data[$id]);
            $data = array_values($data);
        } else {
            $data[$id]['qty'] -= 1;
        }
        self::writeFile($data);
        // session()->put('checkstock_cart', $data);
        $data = $this->loaddata();
        return response()->json($data);
    }

    public function remove(Request $request)
    {
        $id = $request->id;
        // Read File
        $data = self::readFile();
        // $data = session()->get('checkstock_cart');
        unset($data[$id]);
        $data = array_values($data);
        self::writeFile($data);
        // session()->put('checkstock_cart', $data);
        $data = $this->loaddata();
        return response()->json($data);
    }

    public function reset()
    {
        self::writeFile([]);
        // session()->put('checkstock_cart', []);
        return redirect()->route('checkstock');
    }

    public function save(Request $request)
    {
        if ($request->remark == "") {
            alert()->warning('โปรดระบุหมายเหตุ!');
            return back();
        }
        $data = self::readFile();
        // $data = session()->get('checkstock_cart');
        if (!isset($data)) {
            alert()->warning('ไม่มีข้อมูล!');
            return back();
        }

        $count = (isset($data)) ? count($data) + 1 : 1;
        $final_data = array();
        $i = 0;
        foreach ($data as $data) {
            $final_data[$i]['no'] = $i + 1;
            $final_data[$i]['sku'] = $data['sku'];
            $final_data[$i]['name'] = $data['name'];
            $final_data[$i]['barcode'] = $data['barcode'];
            $final_data[$i]['qty'] = $data['qty'];
            $i++;
        }
        array_push($final_data,['', 'รวมทั้งหมด', '', '' ,'=SUM(E2:E'.$count.')']);

        $file_name = date('Y-m-d H_i_s').'.xlsx';
        Excel::store(new CheckstockExport($final_data, $request->remark), $file_name, 'exports');
        self::writeFile([]);

        $request->flash();
        alert()->success('บันทึกเรียบร้อย');
        return redirect()->route('checkstock')->with('message', 'success')->with('file_name', $file_name);
    }

    public function download($file_name)
    {
        return Storage::disk('exports')->download($file_name);
    }
}