<?php

namespace App\Http\Controllers;

use App\Exports\DailySalesExport;
use App\Models\ProductGroupReport;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonthlySaleController extends MonthlySaleBaseController
{
    public function __construct()
    {
        $this->middleware('auth');
        parent::__construct();
        ini_set('memory_limit','512M');
        ini_set('max_execution_time', 300);
    }

    public function index()
    {
        $sort = self::sortCategory();
        $daily_category = ProductGroupReport::groupBy("daily_category")->orderByRaw($sort)->get(['daily_category'])->reverse()->values();
        return view('sales-report.monthly-sales', compact('daily_category'));
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
            $doc_date = "";
            if ($request->doc_date != '') {
                $d = explode("/", $request->doc_date);
                $doc_date = $d[1]."-".$d[0];
                $date_short = $d[0]."/".substr(($d[1]+543), 2, 2);
            }

            $rows = [];

            $items = self::callData($daily_category, $doc_date);
            $items_count = $items->count();

            if ($items_count > 0) {
                // เรียกข้อมูลมาครั้งเดียวแล้วค่อยแบ่งตามกลุ่ม (เพื่อให้เร็วขึ้นหากข้อมูลมีจำนวนมาก)
                $_fix_category = self::callDataFixCategory($this->fix_category, $doc_date);
                $_daily_sale_fix = self::callMonthlySaleFixCategory($daily_category, $doc_date);
                $_daily_sale = self::callMonthlySale($daily_category, $doc_date);

                $i = 0;
                foreach ($items as $cat) {
                    $_category = (!empty($cat->daily_category)) ? $cat->daily_category : "";
                    $cat_daily = ($cat->daily_category != "") ? $cat->daily_category : $this->other_category;

                    if (in_array($_category, $this->fix_category)) {
                        // ################################# FIX CATEGORY ####################################
                        $fix_category = self::callFilterFixCategory($_fix_category, $_category);
                        foreach ($fix_category as $fix_cat) {
                            $unit_cat = self::unit_category($cat_daily.'('.$fix_cat->unit.')');
                            $daily_sale = self::callFilterFixCategoryUnit($_daily_sale_fix, $_category, $fix_cat->unit);
                            $loopSum = self::callLoopSumQtyAndPrice($daily_sale);
                            $text_total = self::calcNumberFormat($loopSum["qty"]) . ' '.$unit_cat.' ' . self::calcNumberFormat($loopSum["price"]) . ' บาท';
                            $j = 0;
                            foreach ($daily_sale as $sale) {
                                if ($j == 0) {
                                    $daily_cat = '<span class="text-danger">'.$cat_daily.'('.$fix_cat->unit.')</span>';
                                } else if ($j == 1) {
                                    $daily_cat = '<span class="text-blue">'.$text_total.'</span>';
                                } else {
                                    $daily_cat = "";
                                }

                                $no = ($j == 0) ? number_format(($i + 1)) : "";
                                $rows[] = array(
                                    "no" => $no,
                                    "daily_category" => $daily_cat,
                                    "stkcod" => $sale->stkcod,
                                    "stkdes" => $sale->stkdes,
                                    "unit" => $sale->unit,
                                    "qty" => self::calcNumberFormat($sale->qty_total),
                                    "price" => self::calcNumberFormat($sale->netval_total),
                                );
                                $j++;
                            }
                            if ($j == 1) {
                                $rows[] = array(
                                    "no" => "",
                                    "daily_category" => '<span class="text-blue">'.$text_total.'</span>',
                                    "stkcod" => "",
                                    "stkdes" => "",
                                    "unit" => "",
                                    "qty" => "",
                                    "price" => "",
                                );
                            }
                            $rows[] = array(
                                "no" => '<b class="text-primary">รวม</b>',
                                "daily_category" => '<b class="text-primary">'.$cat_daily.'('.$fix_cat->unit.')</b>',
                                "stkcod" => "",
                                "stkdes" => "",
                                "unit" => "",
                                "qty" => '<b class="text-primary">'.self::calcNumberFormat($fix_cat->qty_total).'</b>',
                                "price" => '<b class="text-primary">'.self::calcNumberFormat($fix_cat->netval_total).'</b>',
                            );
                            $rows[] = array("no"=>"", "daily_category"=>"", "stkcod"=>"", "stkdes"=>"", "unit"=>"", "qty"=>"", "price"=>"");
                            $i++;
                        }
                        // ################################# END ####################################
                    } else {
                        $unit_cat = self::unit_category($cat_daily);
                        $daily_sale = self::callFilterCategory($_daily_sale, $_category);
                        $loopSum = self::callLoopSumQtyAndPrice($daily_sale);
                        $text_total = self::calcNumberFormat($loopSum["qty"]) . ' '.$unit_cat.' ' . self::calcNumberFormat($loopSum["price"]) . ' บาท';
                        $j = 0;
                        foreach ($daily_sale as $sale) {
                            if ($j == 0) {
                                $daily_cat = '<span class="text-danger">'.$cat_daily.'</span>';
                            } else if ($j == 1) {
                                $daily_cat = '<span class="text-blue">'.$text_total.'</span>';
                            } else {
                                $daily_cat = "";
                            }

                            $no = ($j == 0) ? number_format(($i + 1)) : "";
                            $rows[] = array(
                                "no" => $no,
                                "daily_category" => $daily_cat,
                                "stkcod" => $sale->stkcod,
                                "stkdes" => $sale->stkdes,
                                "unit" => $sale->unit,
                                "qty" => self::calcNumberFormat($sale->qty_total),
                                "price" => self::calcNumberFormat($sale->netval_total),
                            );
                            $j++;
                        }
                        if ($j == 1) {
                            $rows[] = array(
                                "no" => "",
                                "daily_category" => '<span class="text-blue">'.$text_total.'</span>',
                                "stkcod" => "",
                                "stkdes" => "",
                                "unit" => "",
                                "qty" => "",
                                "price" => "",
                            );
                        }
                        $rows[] = array(
                            "no" => '<b class="text-primary">รวม</b>',
                            "daily_category" => '<b class="text-primary">'.$cat_daily.'</b>',
                            "stkcod" => "",
                            "stkdes" => "",
                            "unit" => "",
                            "qty" => '<b class="text-primary">'.self::calcNumberFormat($cat->qty_total).'</b>',
                            "price" => '<b class="text-primary">'.self::calcNumberFormat($cat->netval_total).'</b>',
                        );
                        $rows[] = array("no"=>"", "daily_category"=>"", "stkcod"=>"", "stkdes"=>"", "unit"=>"", "qty"=>"", "price"=>"");
                        $i++;
                    }
                }
                if ($items_count > 0) {
                    $summary = self::callSummary($daily_category, $doc_date);
                    $rows[] = array(
                        "no" => '<b class="text-success">รวมทั้งหมด</b>',
                        "daily_category" => "",
                        "stkcod" => "",
                        "stkdes" => "",
                        "unit" => "",
                        "qty" => '<b class="text-success">'.self::calcNumberFormat($summary["qty"]).'</b>',
                        "price" => '<b class="text-success">'.self::calcNumberFormat($summary["price"]).'</b>',
                    );
                }
            }

            $totalRecords = count($rows);
            // $rows = array_slice($rows, $request->offset, $request->limit);
            $response = array(
                "total" => $totalRecords,
                "totalNotFiltered" => $totalRecords,
                "rows" => $rows,
            );
            return response()->json($response);
        }
    }

    public function print(Request $request)
    {
        if ($request->doc_date == "") {
            alert()->warning('ยังไม่ได้เลือกเดือน!');
            return back();
        }
        $daily_category = $request->daily_category;
        $doc_date = "";
        $date_short = "";
        if ($request->doc_date != '') {
            $d = explode("/", $request->doc_date);
            $doc_date = $d[1]."-".$d[0];
            $date_short = $d[0]."/".substr(($d[1]+543), 2, 2);
        }

        $items = self::callData($daily_category, $doc_date);
        if ($items->isEmpty()) {
            alert()->warning('ไม่พบข้อมูล!');
            return back();
        } else {
            $items = self::callData($daily_category, $doc_date);
            $items_count = $items->count();

            // เรียกข้อมูลมาครั้งเดียวแล้วค่อยแบ่งตามกลุ่ม (เพื่อให้เร็วขึ้นหากข้อมูลมีจำนวนมาก)
            $_fix_category = self::callDataFixCategory($this->fix_category, $doc_date);
            $_daily_sale_fix = self::callMonthlySaleFixCategory($daily_category, $doc_date);
            $_daily_sale = self::callMonthlySale($daily_category, $doc_date);

            $i = 0;
            $title = "";
            $rows = [];
            foreach ($items as $cat) {
                $_category = (!empty($cat->daily_category)) ? $cat->daily_category : "";
                $cat_daily = ($cat->daily_category != "") ? $cat->daily_category : $this->other_category;

                if (in_array($_category, $this->fix_category)) {
                    // ################################# FIX CATEGORY ####################################
                    $fix_category = self::callFilterFixCategory($_fix_category, $_category);
                    foreach ($fix_category as $fix_cat) {
                        $unit_cat = self::unit_category($cat_daily.'('.$fix_cat->unit.')');
                        $daily_sale = self::callFilterFixCategoryUnit($_daily_sale_fix, $_category, $fix_cat->unit);
                        $loopSum = self::callLoopSumQtyAndPrice($daily_sale);
                        $text_total = self::calcNumberFormat($loopSum["qty"]) . ' '.$unit_cat.' ' . self::calcNumberFormat($loopSum["price"]) . ' บาท';
                        $j = 0;
                        foreach ($daily_sale as $sale) {
                            if ($j == 0) {
                                $daily_cat = '<span class="text-danger">'.$cat_daily.'('.$fix_cat->unit.')</span>';
                            } else if ($j == 1) {
                                $daily_cat = '<span class="text-blue">'.$text_total.'</span>';
                            } else {
                                $daily_cat = "";
                            }

                            $no = ($j == 0) ? number_format(($i + 1)) : "";
                            $rows[] = array(
                                "daily_category" => $daily_cat,
                                "stkcod" => $sale->stkcod,
                                "stkdes" => $sale->stkdes,
                                "unit" => $sale->unit,
                                "qty" => self::calcNumberFormat($sale->qty_total),
                                "price" => self::calcNumberFormat($sale->netval_total),
                            );
                            $j++;
                        }
                        if ($j == 1) {
                            $rows[] = array(
                                "daily_category" => '<span class="text-blue">'.$text_total.'</span>',
                                "stkcod" => "",
                                "stkdes" => "",
                                "unit" => "",
                                "qty" => "",
                                "price" => "",
                            );
                        }
                        $rows[] = array(
                            "daily_category" => '<b class="text-primary">'.$cat_daily.'('.$fix_cat->unit.')</b>',
                            "stkcod" => "",
                            "stkdes" => "",
                            "unit" => "",
                            "qty" => '<b class="text-primary">'.self::calcNumberFormat($fix_cat->qty_total).'</b>',
                            "price" => '<b class="text-primary">'.self::calcNumberFormat($fix_cat->netval_total).'</b>',
                        );
                        $rows[] = array("daily_category"=>"", "stkcod"=>"", "stkdes"=>"", "unit"=>"", "qty"=>"", "price"=>"");
                        $i++;
                    }
                    // ################################# END ####################################
                } else {
                    $unit_cat = self::unit_category($cat_daily);
                    $daily_sale = self::callFilterCategory($_daily_sale, $_category);
                    $loopSum = self::callLoopSumQtyAndPrice($daily_sale);
                    $text_total = self::calcNumberFormat($loopSum["qty"]) . ' '.$unit_cat.' ' . self::calcNumberFormat($loopSum["price"]) . ' บาท';
                    $j = 0;
                    foreach ($daily_sale as $sale) {
                        if ($j == 0) {
                            $daily_cat = '<span class="text-danger">'.$cat_daily.'</span>';
                        } else if ($j == 1) {
                            $daily_cat = '<span class="text-blue">'.$text_total.'</span>';
                        } else {
                            $daily_cat = "";
                        }

                        $no = ($j == 0) ? number_format(($i + 1)) : "";
                        $rows[] = array(
                            "daily_category" => $daily_cat,
                            "stkcod" => $sale->stkcod,
                            "stkdes" => $sale->stkdes,
                            "unit" => $sale->unit,
                            "qty" => self::calcNumberFormat($sale->qty_total),
                            "price" => self::calcNumberFormat($sale->netval_total),
                        );
                        $j++;
                    }
                    if ($j == 1) {
                        $rows[] = array(
                            "daily_category" => '<span class="text-blue">'.$text_total.'</span>',
                            "stkcod" => "",
                            "stkdes" => "",
                            "unit" => "",
                            "qty" => "",
                            "price" => "",
                        );
                    }
                    $rows[] = array(
                        "daily_category" => '<b class="text-primary">รวม '.$cat_daily.'</b>',
                        "stkcod" => "",
                        "stkdes" => "",
                        "unit" => "",
                        "qty" => '<b class="text-primary">'.self::calcNumberFormat($cat->qty_total).'</b>',
                        "price" => '<b class="text-primary">'.self::calcNumberFormat($cat->netval_total).'</b>',
                    );
                    $rows[] = array("daily_category"=>"", "stkcod"=>"", "stkdes"=>"", "unit"=>"", "qty"=>"", "price"=>"");
                    $i++;
                }
            }
            if ($items_count > 0) {
                $summary = self::callSummary($daily_category, $doc_date);
                $header = array("title"=>"เดือน ".$date_short." รายงานยอดขาย", "qty_total"=>self::calcNumberFormat($summary["qty"]), "price_total"=>self::calcNumberFormat($summary["price"]));
                $rows[] = array(
                    "daily_category" => '<b class="text-success">รวมทั้งหมด</b>',
                    "stkcod" => "",
                    "stkdes" => "",
                    "unit" => "",
                    "qty" => '<b class="text-success">'.self::calcNumberFormat($summary["qty"]).'</b>',
                    "price" => '<b class="text-success">'.self::calcNumberFormat($summary["price"]).'</b>',
                );
            }

            $data["title"] = "รายละเอียด เดือน ".str_replace("/", "-", $date_short);
            $data["header"] = $header;
            $data["rows"] = $rows;

            return view('sales-report.monthly-sales-print', compact('data'));
        }
    }

    public function export(Request $request)
    {
        if ($request->doc_date == "") {
            alert()->warning('ยังไม่ได้เลือกเดือน!');
            return back();
        }
        $daily_category = $request->daily_category;
        $doc_date = "";
        $date_short = "";
        if ($request->doc_date != '') {
            $d = explode("/", $request->doc_date);
            $doc_date = $d[1]."-".$d[0];
            $date_short = $d[0]."/".substr(($d[1]+543), 2, 2);
        }

        $items = self::callData($daily_category, $doc_date);
        if ($items->isEmpty()) {
            alert()->warning('ไม่พบข้อมูล!');
            return back();
        } else {
            $rows = [];
            // เรียกข้อมูลมาครั้งเดียวแล้วค่อยแบ่งตามกลุ่ม (เพื่อให้เร็วขึ้นหากข้อมูลมีจำนวนมาก)
            $_fix_category = self::callDataFixCategory($this->fix_category, $doc_date);
            $_daily_sale_fix = self::callMonthlySaleFixCategory($daily_category, $doc_date);
            $_daily_sale = self::callMonthlySale($daily_category, $doc_date);
            foreach ($items as $cat) {
                $_category = (!empty($cat->daily_category)) ? $cat->daily_category : "";
                $cat_daily = ($cat->daily_category != "") ? $cat->daily_category : $this->other_category;

                if (in_array($_category, $this->fix_category)) {
                    // ################################# FIX CATEGORY ####################################
                    $fix_category = self::callFilterFixCategory($_fix_category, $_category);
                    foreach ($fix_category as $fix_cat) {
                        $unit_cat = self::unit_category($cat_daily.'('.$fix_cat->unit.')');
                        $daily_sale = self::callFilterFixCategoryUnit($_daily_sale_fix, $_category, $fix_cat->unit);
                        $loopSum = self::callLoopSumQtyAndPrice($daily_sale);
                        $text_total = self::calcNumberFormat($loopSum["qty"]) . ' '.$unit_cat.' ' . self::calcNumberFormat($loopSum["price"]) . ' บาท';
                        $j = 0;
                        foreach ($daily_sale as $sale) {
                            if ($j == 0) {
                                $daily_cat = $cat_daily.'('.$fix_cat->unit.')';
                            } else if ($j == 1) {
                                $daily_cat = $text_total;
                            } else {
                                $daily_cat = "";
                            }

                            $rows[] = array(
                                "daily_category" => $daily_cat,
                                "stkcod" => $sale->stkcod,
                                "stkdes" => $sale->stkdes,
                                "unit" => $sale->unit,
                                "qty" => self::calcRound($sale->qty_total),
                                "price" => self::calcRound($sale->netval_total),
                            );
                            $j++;
                        }
                        if ($j == 1) {
                            $rows[] = array(
                                "daily_category" => $text_total,
                                "stkcod" => "",
                                "stkdes" => "",
                                "unit" => "",
                                "qty" => "",
                                "price" => "",
                            );
                        }
                    }
                    // ################################# END ####################################
                } else {
                    $unit_cat = self::unit_category($cat_daily);
                    $daily_sale = self::callFilterCategory($_daily_sale, $_category);
                    $loopSum = self::callLoopSumQtyAndPrice($daily_sale);
                    $text_total = self::calcNumberFormat($loopSum["qty"]) . ' '.$unit_cat.' ' . self::calcNumberFormat($loopSum["price"]) . ' บาท';
                    $j = 0;
                    foreach ($daily_sale as $sale) {
                        if ($j == 0) {
                            $daily_cat = $cat_daily;
                        } else if ($j == 1) {
                            $daily_cat = $text_total;
                        } else {
                            $daily_cat = "";
                        }

                        $rows[] = array(
                            "daily_category" => $daily_cat,
                            "stkcod" => $sale->stkcod,
                            "stkdes" => $sale->stkdes,
                            "unit" => $sale->unit,
                            "qty" => self::calcRound($sale->qty_total),
                            "price" => self::calcRound($sale->netval_total),
                        );
                        $j++;
                    }
                    if ($j == 1) {
                        $rows[] = array(
                            "daily_category" => $text_total,
                            "stkcod" => "",
                            "stkdes" => "",
                            "unit" => "",
                            "qty" => "",
                            "price" => "",
                        );
                    }
                }
            }

            $summary = self::callSummary($daily_category, $doc_date);

            $data = array(
                "title" => array(
                    "title" => "เดือน ".$date_short." รายงานยอดขาย",
                    "sum_qty" => self::calcRound($summary["qty"]),
                    "sum_price" => self::calcRound($summary["price"]),
                ),
                "rows" => $rows
            );

            $data = json_decode(json_encode($data));
            unset($rows);
            return Excel::download(new DailySalesExport($data, "รายละเอียดสินค้า"), 'รายละเอียด เดือน '.str_replace("/", "-", $date_short).'.xlsx');
        }
    }

    public function callLoopSumQtyAndPrice($daily_sale)
    {
        // ที่ต้องวนลูปรวมจำนวน,ราคา เพราะต้องทำให้ตรงกับจำนวนที่แสดง (ไม่เอาจุดทศนิยม)
        $sumQty = 0;
        $sumPrice = 0;
        foreach ($daily_sale as $sale) {
            $sumQty += self::calcRound($sale->qty_total);
            $sumPrice += self::calcRound($sale->netval_total);
        }
        return array('qty'=>$sumQty, "price"=>$sumPrice);
    }

    public function callData($daily_category, $doc_date)
    {
        $daily_category = (!empty($daily_category)) ? $daily_category : "";
        $sort = self::sortCategory();
        $items = DB::table('monthly_sales')->where('stkcod', '<>', '');
        if ($daily_category != "all") {
            $items = $items->where('daily_category', '=', $daily_category);
        }
        if ($doc_date != "") {
            $items = $items->whereRaw('SUBSTRING(doc_date, 1, 7) = "'.$doc_date.'"');
        }
        $items = $items->select(
            'daily_category',
            DB::Raw('IF(ROUND(SUM(qty),2)>=1, ROUND(SUM(qty),2), 1) as qty_total'),
            DB::Raw('ROUND(SUM(unitpr),2) as unitpr_total'),
            DB::Raw('ROUND(SUM(netval),2) as netval_total')
        )->groupBy('daily_category')->orderByRaw($sort)->get()->reverse()->values();

        return $items;
    }

    public function callDataFixCategory($fix_category, $doc_date)
    {
        $sort = self::sortCategory();
        $items = DB::table('monthly_sales')->where('stkcod', '<>', '')->whereIn('daily_category', $fix_category);
        if ($doc_date != "") {
            $items = $items->whereRaw('SUBSTRING(doc_date, 1, 7) = "'.$doc_date.'"');
        }
        $items = $items->select(
            'daily_category',
            'unit',
            DB::Raw('IF(ROUND(SUM(qty),2)>=1, ROUND(SUM(qty),2), 1) as qty_total'),
            DB::Raw('ROUND(SUM(unitpr),2) as unitpr_total'),
            DB::Raw('ROUND(SUM(netval),2) as netval_total')
        )->groupBy('daily_category', 'unit')->orderByRaw($sort)->get()->reverse()->values();

        return $items;
    }

    public function callMonthlySale($category, $date)
    {
        $category = (!empty($category)) ? $category : "";
        $daily_sale = DB::table('monthly_sales')->where('stkcod', '<>', '');
        if ($category != "all") {
            $daily_sale = $daily_sale->where('daily_category', '=', $category);
        }
        if ($date != "") {
            $daily_sale = $daily_sale->whereRaw('SUBSTRING(doc_date, 1, 7) = "'.$date.'"');
        }
        $daily_sale = $daily_sale->select('doc_date', 'doc_num', 'cuscod', 'daily_category', 'stkcod', 'stkdes', 'unit', DB::Raw('IF(ROUND(SUM(qty),2)>=1, ROUND(SUM(qty),2), 1) as qty_total'), DB::Raw('ROUND(SUM(unitpr),2) as unitpr_total'), DB::Raw('ROUND(SUM(netval),2) as netval_total'))
        ->groupBy('daily_category', 'stkcod', 'unit')->orderBy('daily_category', 'ASC')->orderBy('qty_total', 'DESC')->orderBy('stkcod', 'ASC')->get();
        return $daily_sale;
    }

    public function callMonthlySaleFixCategory($category, $date)
    {
        $category = (!empty($category)) ? $category : "";
        $daily_sale = DB::table('monthly_sales')->where('stkcod', '<>', '');
        if ($category != "all") {
            $daily_sale = $daily_sale->where('daily_category', '=', $category);
        }
        if ($date != "") {
            $daily_sale = $daily_sale->whereRaw('SUBSTRING(doc_date, 1, 7) = "'.$date.'"');
        }
        $daily_sale = $daily_sale->select('doc_date', 'doc_num', 'cuscod', 'daily_category', 'stkcod', 'stkdes', 'unit', DB::Raw('IF(ROUND(SUM(qty),2)>=1, ROUND(SUM(qty),2), 1) as qty_total'), DB::Raw('ROUND(SUM(unitpr),2) as unitpr_total'), DB::Raw('ROUND(SUM(netval),2) as netval_total'))
        ->groupBy('daily_category', 'stkcod', 'unit')->orderBy('daily_category', 'ASC')->orderBy('qty_total', 'DESC')->orderBy('stkcod', 'ASC')->get();
        return $daily_sale;
    }

    public function callFilterCategory($data, $category)
    {
        $category = (!empty($category)) ? $category : "";
        $records = collect($data)->filter(function($value) use ($category) {
            return $value->daily_category === $category;
        })->all();
        return $records;
    }

    public function callFilterFixCategory($data, $category)
    {
        $category = (!empty($category)) ? $category : "";
        $records = collect($data)->filter(function($value) use ($category) {
            return $value->daily_category === $category;
        })->all();
        return $records;
    }

    public function callFilterFixCategoryUnit($data, $category, $unit)
    {
        $category = (!empty($category)) ? $category : "";
        $records = collect($data)->filter(function($value) use ($category, $unit) {
            return $value->daily_category === $category && $value->unit === $unit;
        })->all();
        return $records;
    }
}