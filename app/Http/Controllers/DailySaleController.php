<?php

namespace App\Http\Controllers;

use App\Exports\DailySalesExport;
use App\Models\ProductGroupReport;
use App\Models\DailySale;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DailySaleController extends DailySaleBaseController
{
    public function __construct()
    {
        $this->middleware('auth');
        parent::__construct();
        ini_set('memory_limit','512M');
    }

    public function index()
    {
        $sort = self::sortCategory();
        $daily_category = ProductGroupReport::groupBy("daily_category")->orderByRaw($sort)->get(['daily_category'])->reverse()->values();
        $available_date = self::callAvailableDate();
        return view('sales-report.daily-sales', compact('daily_category', 'available_date'));
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

            $items = self::callData($daily_category, $doc_date_start, $doc_date_end);
            $items_count = $items->count();

            $i = 0;
            $rows = [];
            foreach ($items as $cat) {
                $_category = (!empty($cat->daily_category)) ? $cat->daily_category : "";
                $cat_daily = ($cat->daily_category != "") ? $cat->daily_category : $this->other_category;

                if (in_array($_category, $this->fix_category)) {
                    // ################################# FIX CATEGORY ####################################
                    $fix_category = self::callDataFixCategory($_category, $doc_date_start, $doc_date_end);
                    foreach ($fix_category as $fix_cat) {
                        $unit_cat = self::unit_category($cat_daily.'('.$fix_cat->unit.')');
                        // $text_total = self::calcNumberFormat($fix_cat->qty_total) . ' '.$unit_cat.' ' . self::calcNumberFormat($fix_cat->netval_total) . ' บาท';
                        $daily_sale = self::callDailySaleFixCategory($_category, $doc_date_start, $doc_date_end, $fix_cat->unit);
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
                    // $text_total = self::calcNumberFormat($cat->qty_total) . ' '.$unit_cat.' ' . self::calcNumberFormat($cat->netval_total) . ' บาท';
                    $daily_sale = self::callDailySale($_category, $doc_date_start, $doc_date_end);
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
                $summary = self::callSummary($daily_category, $doc_date_start, $doc_date_end);
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

            $totalRecords = count($rows);
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

        $items = self::callData($daily_category, $doc_date_start, $doc_date_end);
        if ($items->isEmpty()) {
            alert()->warning('ไม่พบข้อมูล!');
            return back();
        } else {
            $items = self::callData($daily_category, $doc_date_start, $doc_date_end);
            $items_count = $items->count();

            $i = 0;
            $title = "";
            $rows = [];
            foreach ($items as $cat) {
                $_category = (!empty($cat->daily_category)) ? $cat->daily_category : "";
                $cat_daily = ($cat->daily_category != "") ? $cat->daily_category : $this->other_category;

                if (in_array($_category, $this->fix_category)) {
                    // ################################# FIX CATEGORY ####################################
                    $fix_category = self::callDataFixCategory($_category, $doc_date_start, $doc_date_end);
                    foreach ($fix_category as $fix_cat) {
                        $unit_cat = self::unit_category($cat_daily.'('.$fix_cat->unit.')');
                        // $text_total = self::calcNumberFormat($fix_cat->qty_total) . ' '.$unit_cat.' ' . self::calcNumberFormat($fix_cat->netval_total) . ' บาท';
                        $daily_sale = self::callDailySaleFixCategory($_category, $doc_date_start, $doc_date_end, $fix_cat->unit);
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
                    // $text_total = self::calcNumberFormat($cat->qty_total) . ' '.$unit_cat.' ' . self::calcNumberFormat($cat->netval_total) . ' บาท';
                    $daily_sale = self::callDailySale($_category, $doc_date_start, $doc_date_end);
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
                $summary = self::callSummary($daily_category, $doc_date_start, $doc_date_end);
                $header = array("title"=>"วันที่ ".$date_short." รายงานยอดขาย", "qty_total"=>self::calcNumberFormat($summary["qty"]), "price_total"=>self::calcNumberFormat($summary["price"]));
                $rows[] = array(
                    "daily_category" => '<b class="text-success">รวมทั้งหมด</b>',
                    "stkcod" => "",
                    "stkdes" => "",
                    "unit" => "",
                    "qty" => '<b class="text-success">'.self::calcNumberFormat($summary["qty"]).'</b>',
                    "price" => '<b class="text-success">'.self::calcNumberFormat($summary["price"]).'</b>',
                );
            }

            $data["title"] = "รายละเอียด วันที่ ".str_replace("/", "-", $date_short);
            $data["header"] = $header;
            $data["rows"] = $rows;

            return view('sales-report.daily-sales-print', compact('data'));
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

        $items = self::callData($daily_category, $doc_date_start, $doc_date_end);
        if ($items->isEmpty()) {
            alert()->warning('ไม่พบข้อมูล!');
            return back();
        } else {
            $rows = [];
            foreach ($items as $cat) {
                $_category = (!empty($cat->daily_category)) ? $cat->daily_category : "";
                $cat_daily = ($cat->daily_category != "") ? $cat->daily_category : $this->other_category;

                if (in_array($_category, $this->fix_category)) {
                    // ################################# FIX CATEGORY ####################################
                    $fix_category = self::callDataFixCategory($_category, $doc_date_start, $doc_date_end);
                    foreach ($fix_category as $fix_cat) {
                        $unit_cat = self::unit_category($cat_daily.'('.$fix_cat->unit.')');
                        // $text_total = self::calcNumberFormat($fix_cat->qty_total) . ' '.$unit_cat.' ' . self::calcNumberFormat($fix_cat->netval_total) . ' บาท';
                        $daily_sale = self::callDailySaleFixCategory($_category, $doc_date_start, $doc_date_end, $fix_cat->unit);
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
                    // $text_total = self::calcNumberFormat($cat->qty_total) . ' '.$unit_cat.' ' . self::calcNumberFormat($cat->netval_total) . ' บาท';
                    $daily_sale = self::callDailySale($_category, $doc_date_start, $doc_date_end);
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

            $summary = self::callSummary($daily_category, $doc_date_start, $doc_date_end);

            $data = array(
                "title" => array(
                    "title" => "วันที่ ".$date_short." รายงานยอดขาย",
                    "sum_qty" => self::calcRound($summary["qty"]),
                    "sum_price" => self::calcRound($summary["price"]),
                ),
                "rows" => $rows
            );

            $data = json_decode(json_encode($data));
            unset($rows);
            return Excel::download(new DailySalesExport($data, "รายละเอียดสินค้า"), 'รายละเอียด วันที่ '.str_replace("/", "-", $date_short).'.xlsx');
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

    public function callData($daily_category, $doc_date_start, $doc_date_end)
    {
        $daily_category = (!empty($daily_category)) ? $daily_category : "";
        $sort = self::sortCategory();
        $items = DailySale::where('stkcod', '<>', '');
        if ($daily_category != "all") {
            $items = $items->where('daily_category', '=', $daily_category);
        }
        if ($doc_date_start != "" && $doc_date_end != "") {
            $items = $items->whereRaw(' doc_date between "'.$doc_date_start.'" and "'.$doc_date_end.'" ');
        }
        $items = $items->select(
            'daily_category',
            DB::Raw('IF(ROUND(SUM(qty),2)>=1, ROUND(SUM(qty),2), 1) as qty_total'),
            DB::Raw('ROUND(SUM(unitpr),2) as unitpr_total'),
            DB::Raw('ROUND(SUM(netval),2) as netval_total')
        )->groupBy('daily_category')->orderByRaw($sort)->get()->reverse()->values();

        return $items;
    }

    public function callDataFixCategory($daily_category, $doc_date_start, $doc_date_end)
    {
        $daily_category = (!empty($daily_category)) ? $daily_category : "";
        $sort = self::sortCategory();
        $items = DailySale::where('stkcod', '<>', '');
        if ($daily_category != "all") {
            $items = $items->where('daily_category', '=', $daily_category);
        }
        if ($doc_date_start != "" && $doc_date_end != "") {
            $items = $items->whereRaw(' doc_date between "'.$doc_date_start.'" and "'.$doc_date_end.'" ');
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

    public function callDailySale($category, $doc_date_start, $doc_date_end)
    {
        $category = (!empty($category)) ? $category : "";
        $daily_sale = DailySale::where('stkcod', '<>', '')->where('daily_category', '=', $category);
        if ($doc_date_start != "" && $doc_date_end != "") {
            $daily_sale = $daily_sale->whereRaw(' doc_date between "'.$doc_date_start.'" and "'.$doc_date_end.'" ');
        }
        $daily_sale = $daily_sale->select('doc_date', 'doc_num', 'cuscod', 'daily_category', 'stkcod', 'stkdes', 'unit', DB::Raw('IF(ROUND(SUM(qty),2)>=1, ROUND(SUM(qty),2), 1) as qty_total'), DB::Raw('ROUND(SUM(unitpr),2) as unitpr_total'), DB::Raw('ROUND(SUM(netval),2) as netval_total'))
        ->groupBy('daily_category', 'stkcod', 'unit')->orderBy('daily_category', 'ASC')->orderBy('qty_total', 'DESC')->orderBy('stkcod', 'ASC')->get();
        return $daily_sale;
    }

    public function callDailySaleFixCategory($category, $doc_date_start, $doc_date_end, $unit)
    {
        $category = (!empty($category)) ? $category : "";
        $daily_sale = DailySale::where('stkcod', '<>', '')->where('daily_category', '=', $category)->where('unit', '=', $unit);
        if ($doc_date_start != "" && $doc_date_end != "") {
            $daily_sale = $daily_sale->whereRaw(' doc_date between "'.$doc_date_start.'" and "'.$doc_date_end.'" ');
        }
        $daily_sale = $daily_sale->select('doc_date', 'doc_num', 'cuscod', 'daily_category', 'stkcod', 'stkdes', 'unit', DB::Raw('IF(ROUND(SUM(qty),2)>=1, ROUND(SUM(qty),2), 1) as qty_total'), DB::Raw('ROUND(SUM(unitpr),2) as unitpr_total'), DB::Raw('ROUND(SUM(netval),2) as netval_total'))
        ->groupBy('daily_category', 'stkcod', 'unit')->orderBy('daily_category', 'ASC')->orderBy('qty_total', 'DESC')->orderBy('stkcod', 'ASC')->get();
        return $daily_sale;
    }
}