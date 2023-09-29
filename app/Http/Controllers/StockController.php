<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MiddlewareStock as Stock;
use App\Models\EXLocation as Location;

class StockController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        ini_set('memory_limit', '512M');
    }

    public function index(Request $request)
    {
        $location = Location::all();
        $update = Stock::groupBy('updated_at')->orderBy('updated_at', 'desc')->first('updated_at');
        $category = Stock::where('sale_category', '<>', '')->groupBy('sale_category')->get('sale_category');
        return view('product.stock', compact('location', 'update', 'category'));
    }

    public function search(Request $request)
    {
        if ($request->ajax()) {
            $location = Location::get('typcod')->toArray();
            $stock = Stock::leftJoin('kacee_center_dev.od_flag as f', 'stocks.sku', '=', 'f.sku')
                ->where(
                    function ($query) use ($request, $location) {
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
                        if (($category = $request->category)) {
                            if ($category != 'all') {
                                if ($category == 'none') {
                                    $query->where('stocks.sale_category', '=', "");
                                } else {
                                    $query->where('stocks.sale_category', '=', $category);
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
            $totalRecords = $stock->select('count(*) as allcount')->count();
            $records = $stock->select('stocks.sku', 'stocks.name', 'stocks.storage_des', 'stocks.storage', 'stocks.qty', 'stocks.unit_des', 'stocks.created_at', 'stocks.lmov_date', 'stocks.sale_category', 'f.status')->orderBy('stocks.created_at', 'asc')->get()->toArray();

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
