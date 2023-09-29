<?php

namespace App\Http\Controllers;

use App\Exports\OdStockExport;
use Illuminate\Http\Request;
use App\Imports\OdStockImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\MiddlewareStock as Stock;
use App\Models\OdLocation as Location;
use App\Models\EXLocation;
use App\Models\OdStock;
use App\Models\EXProduct;
use DB;

class StockOdooController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 600);
    }
    public function index(Request $request)
    {
        return view('product.odoo.od-stockimport');
    }
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required',
        ], [
            'file.required' => 'ยังไม่ได้เลือกไฟล์',
        ]);
        Excel::import(new OdStockImport, $request->file('file'));
        return back()->with('success', 'User Imported Successfully');
    }
    public function stocks()
    {
        $location = Location::all();
        return view('product.odoo.od-stock', compact('location'));
    }
    public function search(Request $request)
    {
        if ($request->ajax()) {
            $on = ['06', 'OL'];
            $f4 = ['06', '10'];
            $product = OdStock::leftJoin('od_flag as f', 'od_stocks.stkcod', '=', 'f.sku')
                ->leftJoin('kacee_middleware.stocks as s', 'f.sku', '=', 's.sku')
                ->where(
                    function ($query) use ($request, $on, $f4) {
                        if (($stock_select = $request->stock)) {
                            if ($stock_select != 'all') {
                                if ($stock_select == 'ON') {
                                    $query->where('od_stocks.loccod', '=', $stock_select);
                                    $query->whereIN('s.storage', $on);
                                } else if ($stock_select == 'F4') {
                                    $query->where('od_stocks.loccod', '=', $stock_select);
                                    $query->whereIN('s.storage', $f4);
                                } else if ($stock_select == 'RA') {
                                    $query->where('od_stocks.loccod', '=', $stock_select);
                                    $query->whereIN('s.storage', $f4);
                                } else if ($stock_select == 'PT') {
                                    $query->where('od_stocks.loccod', '=', $stock_select);
                                    $query->whereIN('s.storage', $f4);
                                } else {
                                    $query->where('od_stocks.loccod', '=', $stock_select);
                                    $query->where('s.storage', '=', '06');
                                }
                            }
                        }
                        if (($status = $request->status)) {
                            if ($status != 'all') {
                                if ($status != 'none') {
                                    $query->where('f.status', '=', $status);
                                } else {
                                    $query->where('f.status', '=', null);
                                }
                            }
                        }
                    }
                );
            $totalRecords = $product->select('count(*) as allcount')->count();
            $records = $product->select('od_stocks.stkcod', 'od_stocks.loccod', 's.name', 's.qty', 's.unit', 's.storage', 'od_stocks.created_at', 'od_stocks.updated_at', 'f.status')->orderBy('created_at', 'asc')
                ->selectRaw('JSON_ARRAYAGG(JSON_OBJECT("storage", s.storage, "qty", s.qty)) as list ')
                ->groupBy('od_stocks.stkcod')
                ->get()->toArray();
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

    public function none_ex(Request $request)
    {
        if ($request->ajax()) {
            $noneex = DB::select('SELECT s.stkcod,s.loccod FROM kacee_center_dev.od_stocks as s left join kacee_middleware.stocks as e on s.stkcod = e.sku where e.sku is null and s.loccod ="' . $request->stock . '"');
            $data = array(
                'noneex' => $noneex,
            );
            unset($noneex);
            return response()->json($data);
        }
    }
    public function noneStock()
    {
        $location = EXLocation::all();
        $update = Stock::groupBy('updated_at')->orderBy('updated_at', 'desc')->first('updated_at');
        return view('product.odoo.od-nonestock', compact('location', 'update'));
    }
    public function searchNoneStock(Request $request)
    {
        if ($request->ajax()) {
            $location = EXLocation::get('typcod')->toArray();
            $storage = Stock::leftJoin('kacee_center_dev.od_stocks as s', 'stocks.sku', '=', 's.stkcod')
                ->leftJoin('kacee_center_dev.od_flag as f', 'f.sku', '=', 'stocks.sku')
                ->whereNull('s.stkcod')
                ->where(
                    function ($query) use ($request, $location) {
                        if (($status = $request->status)) {
                            if ($status != 'all') {
                                if ($status != 'none') {
                                    $query->where('f.status', '=', $status);
                                } else {
                                    $query->where('f.status', '=', null);
                                }
                            }
                        }
                        if (($stock_select = $request->stock)) {
                            if ($stock_select != 'all') {
                                if ($stock_select == 'none') {
                                    $query->whereNotIn('stocks.storage', $location);
                                } else {
                                    $query->where('storage', '=', $stock_select);
                                    $query->whereIn('stocks.storage', $location);
                                }
                            }
                        }
                    }
                );
            $totalRecords = $storage->select('count(*) as allcount')->count();
            $records = $storage->select('stocks.sku', 'stocks.name', 'stocks.storage_des', 'stocks.storage', 'stocks.lmov_date', 'f.status')
                ->selectRaw('SUM(stocks.qty) as total_qty')
                ->groupBy('stocks.sku')
                ->get()->toArray();

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
}
