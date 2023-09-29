<?php

namespace App\Http\Controllers;

use App\Exports\DailySalesTop10Export;
use App\Models\ProductGroupReport;
use App\Models\DailySale;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DailySaleTop10Controller extends DailySaleBaseController
{
    protected $parent_cutout;
    protected $cuscod_cutout;
    public function __construct()
    {
        $this->middleware('auth');
        parent::__construct();
        $this->parent_cutout = ['2', '3', '4', '5', '6', '7'];
        $this->cuscod_cutout = ['3-03-101', '3-03-102', '3-03-200'];
        ini_set('memory_limit','512M');
    }

    public function index()
    {
        $sort = self::sortCategory();
        $daily_category = ProductGroupReport::groupBy("daily_category")->orderByRaw($sort)->get(['daily_category'])->reverse()->values();
        $available_date = self::callAvailableDate();
        return view('sales-report.daily-sales-top10', compact('daily_category', 'available_date'));
    }

    public function search(Request $request)
    {
        if ($request->ajax()) {
            if ($request->doc_date == "") {
                $totalRecords = 0;
                $response = array(
                    "total" => $totalRecords,
                    "totalNotFiltered" => $totalRecords,
                    "rows" => [],
                );
                return response()->json($response);
            }
            $daily_category = $request->daily_category;

            $_date = self::splitDateConvert($request->doc_date);
            $doc_date_start = Carbon::createFromFormat('d/m/Y', $_date[0])->format('Y-m-d');
            $doc_date_end = Carbon::createFromFormat('d/m/Y', $_date[1])->format('Y-m-d');

            $top10 = self::callDataTop10($daily_category, $doc_date_start, $doc_date_end);
            $top10_headers = self::callDataTop10Header($daily_category, $doc_date_start, $doc_date_end);
            $top10_items = self::callDataTop10Items($daily_category, $doc_date_start, $doc_date_end);
            $items = self::callDataTop10Calc($top10, $top10_headers, $top10_items);

            $i = 0;
            $rows = [];
            foreach ($items as $customer) {
                $line = 0;
                foreach ($customer as $cus) {
                    $data_headers = [];
                    $j = 0;
                    foreach ($cus["headers"] as $header) {
                        $data_headers[$j]["doc_num"] = $header["doc_num"];
                        $data_headers[$j]["qty_total"] = self::calcNumberFormat($header["qty_total"]);
                        $data_headers[$j]["price_total"] = self::calcNumberFormat($header["price_total"]);
                        $data_headers[$j]["shortnam"] = $header["shortnam"];
                        $data_items = [];
                        $k = 0;
                        foreach ($header["items"] as $item) {
                            $data_items[$k]["stkcod"] = $item["stkcod"];
                            $data_items[$k]["stkdes"] = $item["stkdes"];
                            $data_items[$k]["qty_total"] = self::calcNumberFormat($item["qty_total"]);
                            $data_items[$k]["price_total"] = self::calcNumberFormat($item["price_total"]);
                            $k++;
                        }
                        $data_headers[$j]["items"] = $data_items;
                        $j++;
                    }
                    $no = ($line == 0) ? number_format(($i + 1)) : "";
                    $rows[] = array(
                        "no" => '<b class="text-blue">อันดับ '.$no.'</b>',
                        "cusnam" => '<b class="text-blue">'.$cus["cusnam"].'</b>',
                        "cuscod" => '<b class="text-blue">'.$cus["cuscod"].'</b>',
                        "qty_total" => '<b class="text-blue">'.self::calcNumberFormat($cus["qty_total"]).'</b>',
                        "price_total" => '<b class="text-blue">'.self::calcNumberFormat($cus["price_total"]).'</b>',
                        "headers" => $data_headers,
                    );
                    $line++;
                }
                $i++;
            }

            return response()->json($rows);
        }
    }

    public function print(Request $request)
    {
        if ($request->doc_date == "") {
            alert()->warning('ยังไม่ได้เลือกวันที่!');
            return back();
        }
        $daily_category = $request->daily_category;

        $_date = self::splitDateConvert($request->doc_date);
        $doc_date_start = Carbon::createFromFormat('d/m/Y', $_date[0])->format('Y-m-d');
        $doc_date_end = Carbon::createFromFormat('d/m/Y', $_date[1])->format('Y-m-d');

        $date_short = "";
        if (count($_date)) {
            foreach (array_unique($_date) as $d) {
                $date_short .= ($date_short != "") ? " ถึง " : "";
                $date_short .= Carbon::createFromFormat('d/m/Y', $d)->thaidate('d/m/y');
            }
        }

        $top10 = self::callDataTop10($daily_category, $doc_date_start, $doc_date_end);
        $top10_headers = self::callDataTop10Header($daily_category, $doc_date_start, $doc_date_end);
        $top10_items = self::callDataTop10Items($daily_category, $doc_date_start, $doc_date_end);
        $items = self::callDataTop10Calc($top10, $top10_headers, $top10_items);
        if (count($items) <= 0) {
            alert()->warning('ไม่พบข้อมูล!');
            return back();
        } else {
            $i = 0;
            $rows = [];
            foreach ($items as $customer) {
                $line = 0;
                foreach ($customer as $cus) {
                    $data_headers = [];
                    $j = 0;
                    foreach ($cus["headers"] as $header) {
                        $data_headers[$j]["doc_num"] = $header["doc_num"];
                        $data_headers[$j]["qty_total"] = self::calcNumberFormat($header["qty_total"]);
                        $data_headers[$j]["price_total"] = self::calcNumberFormat($header["price_total"]);
                        $data_headers[$j]["shortnam"] = $header["shortnam"];
                        $data_items = [];
                        $k = 0;
                        foreach ($header["items"] as $item) {
                            $data_items[$k]["stkcod"] = $item["stkcod"];
                            $data_items[$k]["stkdes"] = $item["stkdes"];
                            $data_items[$k]["qty_total"] = self::calcNumberFormat($item["qty_total"]);
                            $data_items[$k]["price_total"] = self::calcNumberFormat($item["price_total"]);
                            $k++;
                        }
                        $data_headers[$j]["items"] = $data_items;
                        $j++;
                    }
                    $no = ($line == 0) ? number_format(($i + 1)) : "";
                    $rows[] = array(
                        "no" => '<b class="text-blue">อันดับ '.$no.'</b>',
                        "cusnam" => '<b class="text-blue">'.$cus["cusnam"].'</b>',
                        "cuscod" => '<b class="text-blue">'.$cus["cuscod"].'</b>',
                        "qty_total" => '<b class="text-blue">'.self::calcNumberFormat($cus["qty_total"]).'</b>',
                        "price_total" => '<b class="text-blue">'.self::calcNumberFormat($cus["price_total"]).'</b>',
                        "headers" => $data_headers,
                    );
                    $line++;
                }
                $i++;
            }
            $data["title"] = "TOP10 วันที่ ".str_replace("/", "-", $date_short);
            $data["header"] = "TOP10 วันที่ ".$date_short;
            $data["rows"] = $rows;
            return view('sales-report.daily-sales-top10-print', compact('data'));
        }
    }

    public function export(Request $request)
    {
        if ($request->doc_date == "") {
            alert()->warning('ยังไม่ได้เลือกวันที่!');
            return back();
        }
        $daily_category = $request->daily_category;
        $_date = self::splitDateConvert($request->doc_date);
        $doc_date_start = Carbon::createFromFormat('d/m/Y', $_date[0])->format('Y-m-d');
        $doc_date_end = Carbon::createFromFormat('d/m/Y', $_date[1])->format('Y-m-d');

        $date_short = "";
        if (count($_date)) {
            foreach (array_unique($_date) as $d) {
                $date_short .= ($date_short != "") ? " ถึง " : "";
                $date_short .= Carbon::createFromFormat('d/m/Y', $d)->thaidate('d/m/y');
            }
        }

        $top10 = self::callDataTop10($daily_category, $doc_date_start, $doc_date_end);
        $top10_headers = self::callDataTop10Header($daily_category, $doc_date_start, $doc_date_end);
        $top10_items = self::callDataTop10Items($daily_category, $doc_date_start, $doc_date_end);
        $items = self::callDataTop10Calc($top10, $top10_headers, $top10_items);
        if (count($items) <= 0) {
            alert()->warning('ไม่พบข้อมูล!');
            return back();
        } else {
            $i = 0;
            $rows = [];
            foreach ($items as $customer) {
                $line = 0;
                foreach ($customer as $cus) {
                    $no = ($line == 0) ? number_format(($i + 1)) : "";
                    $rows[] = array(
                        "a" => 'อันดับ '.$no,
                        "b" => $cus["cusnam"],
                        "c" => '',
                        "d" => '',
                        "e" => $cus["cuscod"],
                        "f" => self::calcRound($cus["qty_total"]),
                        "g" => self::calcRound($cus["price_total"]),
                    );
                    foreach ($cus["headers"] as $header) {
                        $rows[] = array(
                            "a" => '',
                            "b" => $header["doc_num"].' / ผู้รับออเดอร์ - '.$header["shortnam"],
                            "c" => '',
                            "d" => '',
                            "e" => '',
                            "f" => '',
                            "g" => '',
                        );
                        foreach ($header["items"] as $item) {
                            $rows[] = array(
                                "a" => '',
                                "b" => 'รหัสสินค้า:',
                                "c" => $item["stkcod"],
                                "d" => $item["stkdes"],
                                "e" => '',
                                "f" => self::calcRound($item["qty_total"]),
                                "g" => self::calcRound($item["price_total"]),
                            );
                        }
                    }
                    $line++;
                }
                $i++;
            }
            $data = array(
                "title" => "TOP10 วันที่ ".$date_short,
                "rows" => $rows
            );

            $data = json_decode(json_encode($data));
            return Excel::download(new DailySalesTop10Export($data, "รายละเอียดสินค้า"), 'TOP10 วันที่ '.str_replace("/", "-", $date_short).'.xlsx');
        }
    }

    public function callDataTop10($daily_category, $doc_date_start, $doc_date_end)
    {
        $daily_category = (!empty($daily_category)) ? $daily_category : "";
        $items = DB::table('daily_sales as d')->leftJoin('ex_customer as c', 'd.cuscod', '=', 'c.cuscod')->leftJoin('ex_customer_parent as p', 'c.cuscod', 'like', DB::Raw("REPLACE(p.cuscod,'*','%')"))
        ->whereNotIn('p.parent_id', $this->parent_cutout)->whereNotIn('d.cuscod', $this->cuscod_cutout)->where('d.stkcod', '<>', '');
        if ($daily_category != "all") {
            $items = $items->where('d.daily_category', '=', $daily_category);
        }
        if ($doc_date_start != "" && $doc_date_end != "") {
            $items = $items->whereRaw(' d.doc_date between "'.$doc_date_start.'" and "'.$doc_date_end.'" ');
        }
        $items = $items->select(
            'd.cuscod', 'c.prenam', 'c.cusnam',
            DB::Raw('COUNT(DISTINCT(d.doc_num)) as bill_total'),
            DB::Raw('IF(ROUND(SUM(d.qty),2)>=1, ROUND(SUM(d.qty),2), 1) as qty_total'),
            DB::Raw('ROUND(SUM(d.netval),2) as netval_total')
        )->groupBy('d.cuscod')->orderBy('netval_total', 'DESC')->take(10)->get();

        return $items;
    }

    public function callDataTop10Header($daily_category, $doc_date_start, $doc_date_end)
    {
        $daily_category = (!empty($daily_category)) ? $daily_category : "";
        $items = DB::table('daily_sales as d')->leftJoin('ex_customer as c', 'd.cuscod', '=', 'c.cuscod')->leftJoin('ex_customer_parent as p', 'c.cuscod', 'like', DB::Raw("REPLACE(p.cuscod,'*','%')"))
        ->whereNotIn('p.parent_id', $this->parent_cutout)->whereNotIn('d.cuscod', $this->cuscod_cutout)->where('d.stkcod', '<>', '');
        if ($daily_category != "all") {
            $items = $items->where('d.daily_category', '=', $daily_category);
        }
        if ($doc_date_start != "" && $doc_date_end != "") {
            $items = $items->whereRaw(' d.doc_date between "'.$doc_date_start.'" and "'.$doc_date_end.'" ');
        }
        $items = $items->select(
            'd.cuscod', 'd.doc_num', 'd.typcod', 'd.shortnam',
            DB::Raw('IF(ROUND(SUM(d.qty))>=1, ROUND(SUM(d.qty)), 1) as qty_total'),
            DB::Raw('ROUND(SUM(d.netval),2) as netval_total')
        )->groupBy('d.cuscod', 'd.doc_num')->orderBy('netval_total', 'DESC')->get();

        return $items;
    }

    public function callDataTop10Items($daily_category, $doc_date_start, $doc_date_end)
    {
        $daily_category = (!empty($daily_category)) ? $daily_category : "";
        $items = DB::table('daily_sales as d')->leftJoin('ex_customer as c', 'd.cuscod', '=', 'c.cuscod')->leftJoin('ex_customer_parent as p', 'c.cuscod', 'like', DB::Raw("REPLACE(p.cuscod,'*','%')"))
        ->whereNotIn('p.parent_id', $this->parent_cutout)->whereNotIn('d.cuscod', $this->cuscod_cutout)->where('d.stkcod', '<>', '');
        if ($daily_category != "all") {
            $items = $items->where('d.daily_category', '=', $daily_category);
        }
        if ($doc_date_start != "" && $doc_date_end != "") {
            $items = $items->whereRaw(' d.doc_date between "'.$doc_date_start.'" and "'.$doc_date_end.'" ');
        }
        $items = $items->select(
            'd.cuscod', 'd.doc_num', 'd.stkcod', 'd.barcod', 'd.stkdes',
            DB::Raw('IF(ROUND(SUM(d.qty))>=1, ROUND(SUM(d.qty)), 1) as qty_total'),
            DB::Raw('ROUND(SUM(d.netval),2) as netval_total')
        )->groupBy('d.cuscod', 'd.doc_num', 'd.stkcod')->orderByRaw('d.cuscod ASC, d.doc_num ASC, d.stkcod ASC')->get();

        return $items;
    }

    public function callDataTop10Calc($top10, $top10_headers, $top10_items)
    {
        $i = 0;
        $items = [];
        foreach ($top10 as $top) {
            $items[$i]["customer"]["cuscod"] = $top->cuscod;
            $items[$i]["customer"]["cusnam"] = $top->prenam . ' ' . $top->cusnam;
            $items[$i]["customer"]["bill_total"] = self::calcRound($top->bill_total);
            $data_headers = [];
            $j = 0;
            $qty_total = 0;
            $price_total = 0;
            foreach ($top10_headers as $header) {
                if ($top->cuscod == $header->cuscod) {
                    $data_headers[$j]["doc_num"] = $header->doc_num;
                    $data_headers[$j]["typcod"] = $header->typcod;
                    $data_headers[$j]["shortnam"] = $header->shortnam;
                    // $data_headers[$j]["qty_total"] = self::calcRound($header->qty_total);
                    // $data_headers[$j]["price_total"] = self::calcRound($header->netval_total);
                    // $qty_total += self::calcRound($header->qty_total);
                    // $price_total += self::calcRound($header->netval_total);
                    $data_headers[$j]["qty_total"] = 0;
                    $data_headers[$j]["price_total"] = 0;
                    $data_items = [];
                    $k = 0;
                    foreach ($top10_items as $item) {
                        if ($header->doc_num == $item->doc_num) {
                            $data_items[$k]["stkcod"] = $item->stkcod;
                            $data_items[$k]["stkdes"] = $item->stkdes;
                            $data_items[$k]["qty_total"] = self::calcRound($item->qty_total);
                            $data_items[$k]["price_total"] = self::calcRound($item->netval_total);

                            // ที่ต้องวนลูปรวมจำนวน,ราคา เพราะต้องทำให้ตรงกับจำนวนที่แสดง (ไม่เอาจุดทศนิยม)
                            $data_headers[$j]["qty_total"] += self::calcRound($item->qty_total);
                            $data_headers[$j]["price_total"] += self::calcRound($item->netval_total);

                            $qty_total += self::calcRound($item->qty_total);
                            $price_total += self::calcRound($item->netval_total);
                            $k++;
                        }
                    }
                    $data_headers[$j]["items"] = $data_items;
                    $j++;
                }
            }
            $items[$i]["customer"]["qty_total"] = self::calcRound($qty_total);
            $items[$i]["customer"]["price_total"] = self::calcRound($price_total);
            $items[$i]["customer"]["headers"] = $data_headers;
            $i++;
        }
        return $items;
    }
}