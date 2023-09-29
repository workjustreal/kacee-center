<?php

namespace App\Http\Controllers;

use App\Models\EXGroup;
use App\Models\EXProduct;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        ini_set('memory_limit','512M');
    }

    public function index()
    {
        $groups = EXGroup::orderBy("typdes", "asc")->get();
        return view('product.search', compact('groups'));
    }

    public function product_search(Request $request)
    {
        if ($request->ajax()) {
            $data = EXProduct::from('ex_product as p')->leftjoin('ex_group as g', 'p.stkgrp', '=', 'g.typcod')
                ->where('p.status', '=', 1)->where('p.stktyp', '=', '0')
                ->where(function ($query) use ($request) {
                    if (($group = $request->group)) {
                        if ($group != "all") {
                            $query->where('p.stkgrp', '=', $group);
                        }
                    }
                });

            $totalRecords = $data->select('count(p.*) as allcount')->count();
            $records = $data->select('p.stkcod', 'p.barcod', 'p.stkdes', 'p.sellpr1', 'p.series', 'p.product_type', 'p.detail', 'g.typcod', 'g.typdes')->orderBy('p.stkcod', 'asc')->get();

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

    public function index2()
    {
        $groups = EXGroup::orderBy("typdes", "asc")->get();
        return view('product.search2', compact('groups'));
    }

    public function product_search2(Request $request)
    {
        if ($request->ajax()) {
            $data = EXProduct::from('ex_product as p')->leftjoin('ex_group as g', 'p.stkgrp', '=', 'g.typcod')
                ->where('p.status', '=', 1)->where('p.stktyp', '=', '0')
                ->where(function ($query) use ($request) {
                    if (($group = $request->group)) {
                        if ($group != "all") {
                            $query->where('p.stkgrp', '=', $group);
                        }
                    }
                });

            $totalRecords = $data->select('count(p.*) as allcount')->count();
            $records = $data->select('p.stkcod', 'p.barcod', 'p.stkdes', 'p.series', 'p.product_type', 'p.detail', 'g.typcod', 'g.typdes')->orderBy('p.stkcod', 'asc')->get();

            $n = 1;
            $rows = [];
            foreach ($records as $rec) {
                $rows[] = array(
                    "no" => $n,
                    "stkcod" => $rec->stkcod,
                    "barcod" => $rec->barcod,
                    "stkdes" => $rec->stkdes,
                    "typdes" => $rec->typdes,
                    "series" => $rec->series,
                    "product_type" => $rec->product_type,
                    "detail" => $rec->detail,
                );
                $n++;
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
}