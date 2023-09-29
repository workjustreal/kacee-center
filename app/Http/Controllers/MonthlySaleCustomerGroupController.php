<?php

namespace App\Http\Controllers;

use App\Exports\DailySalesCustomerExport;
use App\Models\ProductGroupReport;
use App\Models\EXCustomerGroup;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonthlySaleCustomerGroupController extends MonthlySaleBaseController
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

        $header = [];
        $subheader = [];
        $level0 = EXCustomerGroup::where('level', '=', 0)->where('status', '=', 1)->orderBy('id')->get(['id', 'name', 'display_name', 'level', 'parent_id']);
        $level1 = EXCustomerGroup::where('level', '=', 1)->where('status', '=', 1)->orderBy('id')->get(['id', 'name', 'display_name', 'level', 'parent_id']);
        foreach ($level0 as $l0) {
            $colspan = 0;
            foreach ($level1 as $l1) {
                if ($l0->id == $l1->parent_id) {
                    $subheader[] = array("name"=>$l1->name, "display_name"=>$l1->display_name);
                    $colspan++;
                }
            }
            if ($colspan > 1) {
                $subheader[] = array("name"=>"sum_".$l0->name, "display_name"=>"รวมยอด");
            }
            $header[] = array("name"=>$l0->name, "display_name"=>$l0->display_name, "colspan"=>($colspan > 1) ? $colspan+1 : $colspan);
        }
        $thead["header"] = $header;
        $thead["subheader"] = $subheader;

        return view('sales-report.monthly-sales-customer', compact('daily_category', 'thead'));
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
            $date_short = "";
            if ($request->doc_date != '') {
                $d = explode("/", $request->doc_date);
                $doc_date = $d[1]."-".$d[0];
                $date_short = $d[0]."/".substr(($d[1]+543), 2, 2);
            }

            $items = self::callDataGroup($daily_category, $doc_date);

            $detail = [];
            $n = 0;
            for ($i=0; $i<count($items); $i++) {
                $detail[$n]["category"] = $items[$i]["category"];
                if ($request->type == "qty") {
                    $detail[$n]["summary"] = self::calcRound($items[$i]["qty_total"]);
                } else {
                    $detail[$n]["summary"] = self::calcRound($items[$i]["price_total"]);
                }
                $detail[$n]["bill_summary"] = self::calcRound($items[$i]["bill_total"]);
                for ($j=0; $j<count($items[$i]["data"]); $j++) {
                    $colspan = count($items[$i]["data"][$j]["shop"]);
                    for ($k=0; $k<count($items[$i]["data"][$j]["shop"]); $k++) {
                        $detail[$n]["list"][] = array("name"=>$items[$i]["data"][$j]["shop"][$k]["name"], "display_name"=>$items[$i]["data"][$j]["shop"][$k]["display_name"], "bill"=>self::calcRound($items[$i]["data"][$j]["shop"][$k]["bill"]), "qty"=>self::calcRound($items[$i]["data"][$j]["shop"][$k]["qty"]), "price"=>self::calcRound($items[$i]["data"][$j]["shop"][$k]["price"]));
                    }
                    if ($colspan > 1) {
                        $detail[$n]["list"][] = array("name"=>"sum_".$items[$i]["data"][$j]["chanel"], "display_name"=>"รวมยอด".str_replace('ชาแนล', '', $items[$i]["data"][$j]["display_name"]), "bill"=>self::calcRound($items[$i]["data"][$j]["bill_total"]), "qty"=>self::calcRound($items[$i]["data"][$j]["qty_total"]), "price"=>self::calcRound($items[$i]["data"][$j]["price_total"]));
                    }
                }
                $n++;
            }

            $rowlist = [];
            $rows = [];
            $summary = 0;
            $bill_summary = 0;
            $i = 0;
            foreach ($detail as $rec) {
                if ($i == 0) {
                    foreach ($rec["list"] as $list) {
                        $rowlist[$list["name"]] = array("name"=>$list["name"], "bill"=>0, "qty"=>0, "price"=>0);
                    }
                }
                $rows[$i]["category"] = '<b>'.$rec["category"].'</b>';
                $rows[$i]["summary"] = '<b>'.self::calcNumberFormat($rec["summary"]).'</b>';
                foreach ($rec["list"] as $list) {
                    if ($request->type == "qty") {
                        $rows[$i][$list["name"]] = ($list["qty"] > 0) ? self::calcNumberFormat($list["qty"]) : '';
                    } else {
                        $rows[$i][$list["name"]] = ($list["price"] > 0) ? self::calcNumberFormat($list["price"]) : '';
                    }
                    $rowlist[$list["name"]]["bill"] += self::calcRound($list["bill"]);
                    $rowlist[$list["name"]]["qty"] += self::calcRound($list["qty"]);
                    $rowlist[$list["name"]]["price"] += self::calcRound($list["price"]);
                }
                $summary += self::calcRound($rec["summary"]);
                $bill_summary += self::calcRound($rec["bill_summary"]);
                $i++;
            }
            if ($request->type == "qty") {
                $rows[$i]["category"] = '<b class="text-primary">ผลรวมยอดขายจำนวน</b>';
            } else {
                $rows[$i]["category"] = '<b class="text-primary">ผลรวมยอดขายบาท</b>';
            }
            $rows[$i]["summary"] = '<b class="text-primary">'.self::calcNumberFormat($summary).'</b>';
            foreach ($rowlist as $list) {
                if ($request->type == "qty") {
                    $rows[$i][$list["name"]] = '<b class="text-primary">'.self::calcNumberFormat($list["qty"]).'</b>';
                } else {
                    $rows[$i][$list["name"]] = '<b class="text-primary">'.self::calcNumberFormat($list["price"]).'</b>';
                }
            }
            $i++;
            $rows[$i]["category"] = '<b class="text-primary">ผลรวมจำนวนบิล</b>';
            $rows[$i]["summary"] = '<b class="text-primary">'.self::calcNumberFormat($bill_summary).'</b>';
            foreach ($rowlist as $list) {
                $rows[$i][$list["name"]] = '<b class="text-primary">'.self::calcNumberFormat($list["bill"]).'</b>';
            }
            $i++;

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
        $doc_date = "";
        $date_short = "";
        if ($request->doc_date != '') {
            $d = explode("/", $request->doc_date);
            $doc_date = $d[1]."-".$d[0];
            $date_short = $d[0]."/".substr(($d[1]+543), 2, 2);
        }

        $items = self::callDataGroup($daily_category, $doc_date);
        if (count($items) <= 0) {
            alert()->warning('ไม่พบข้อมูล!');
            return back();
        } else {
            $header = [];
            $subheader = [];
            $level0 = EXCustomerGroup::where('level', '=', 0)->where('status', '=', 1)->orderBy('id')->get(['id', 'name', 'display_name', 'level', 'parent_id']);
            $level1 = EXCustomerGroup::where('level', '=', 1)->where('status', '=', 1)->orderBy('id')->get(['id', 'name', 'display_name', 'level', 'parent_id']);
            foreach ($level0 as $l0) {
                $colspan = 0;
                foreach ($level1 as $l1) {
                    if ($l0->id == $l1->parent_id) {
                        $subheader[] = array("name"=>$l1->name, "display_name"=>$l1->display_name);
                        $colspan++;
                    }
                }
                if ($colspan > 1) {
                    $subheader[] = array("name"=>"sum_".$l0->name, "display_name"=>"รวมยอด");
                }
                $header[] = array("name"=>$l0->name, "display_name"=>$l0->display_name, "colspan"=>($colspan > 1) ? $colspan+1 : $colspan);
            }
            $thead["header"] = $header;
            $thead["subheader"] = $subheader;

            $detail = [];
            $n = 0;
            for ($i=0; $i<count($items); $i++) {
                $detail[$n]["category"] = $items[$i]["category"];
                if ($request->type == "qty") {
                    $detail[$n]["summary"] = self::calcRound($items[$i]["qty_total"]);
                } else {
                    $detail[$n]["summary"] = self::calcRound($items[$i]["price_total"]);
                }
                $detail[$n]["bill_summary"] = self::calcRound($items[$i]["bill_total"]);
                for ($j=0; $j<count($items[$i]["data"]); $j++) {
                    $colspan = count($items[$i]["data"][$j]["shop"]);
                    for ($k=0; $k<count($items[$i]["data"][$j]["shop"]); $k++) {
                        $detail[$n]["list"][] = array("name"=>$items[$i]["data"][$j]["shop"][$k]["name"], "display_name"=>$items[$i]["data"][$j]["shop"][$k]["display_name"], "bill"=>self::calcRound($items[$i]["data"][$j]["shop"][$k]["bill"]), "qty"=>self::calcRound($items[$i]["data"][$j]["shop"][$k]["qty"]), "price"=>self::calcRound($items[$i]["data"][$j]["shop"][$k]["price"]));
                    }
                    if ($colspan > 1) {
                        $detail[$n]["list"][] = array("name"=>"sum_".$items[$i]["data"][$j]["chanel"], "display_name"=>"รวมยอด".str_replace('ชาแนล', '', $items[$i]["data"][$j]["display_name"]), "bill"=>self::calcRound($items[$i]["data"][$j]["bill_total"]), "qty"=>self::calcRound($items[$i]["data"][$j]["qty_total"]), "price"=>self::calcRound($items[$i]["data"][$j]["price_total"]));
                    }
                }
                $n++;
            }

            $rowlist = [];
            $rows = [];
            $summary = 0;
            $bill_summary = 0;
            $i = 0;
            foreach ($detail as $rec) {
                if ($i == 0) {
                    foreach ($rec["list"] as $list) {
                        $rowlist[$list["name"]] = array("name"=>$list["name"], "bill"=>0, "qty"=>0, "price"=>0);
                    }
                }
                $rows[$i]["category"] = $rec["category"];
                $rows[$i]["summary"] = self::calcNumberFormat($rec["summary"]);
                foreach ($rec["list"] as $list) {
                    if ($request->type == "qty") {
                        $rows[$i][$list["name"]] = ($list["qty"] > 0) ? self::calcNumberFormat($list["qty"]) : '';
                    } else {
                        $rows[$i][$list["name"]] = ($list["price"] > 0) ? self::calcNumberFormat($list["price"]) : '';
                    }
                    $rowlist[$list["name"]]["bill"] += self::calcRound($list["bill"]);
                    $rowlist[$list["name"]]["qty"] += self::calcRound($list["qty"]);
                    $rowlist[$list["name"]]["price"] += self::calcRound($list["price"]);
                }
                $summary += self::calcRound($rec["summary"]);
                $bill_summary += self::calcRound($rec["bill_summary"]);
                $i++;
            }
            if ($request->type == "qty") {
                $rows[$i]["category"] = 'ผลรวมยอดขายจำนวน';
            } else {
                $rows[$i]["category"] = 'ผลรวมยอดขายบาท';
            }
            $rows[$i]["summary"] = self::calcNumberFormat($summary);
            foreach ($rowlist as $list) {
                if ($request->type == "qty") {
                    $rows[$i][$list["name"]] = self::calcNumberFormat($list["qty"]);
                } else {
                    $rows[$i][$list["name"]] = self::calcNumberFormat($list["price"]);
                }
            }
            $i++;
            $rows[$i]["category"] = 'ผลรวมจำนวนบิล';
            $rows[$i]["summary"] = self::calcNumberFormat($bill_summary);
            foreach ($rowlist as $list) {
                $rows[$i][$list["name"]] = self::calcNumberFormat($list["bill"]);
            }
            $i++;

            $data["title"] = "ตารางสรุป เดือน ".str_replace("/", "-", $date_short);
            $data["header"]["title"] = "ตารางสรุป เดือน ".$date_short;
            $data["thead"] = $thead;
            $data["rowlist"] = $rowlist;
            $data["rows"] = $rows;

            return view('sales-report.monthly-sales-customer-print', compact('data'));
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

        $items = self::callDataGroup($daily_category, $doc_date);
        if (count($items) <= 0) {
            alert()->warning('ไม่พบข้อมูล!');
            return back();
        } else {
            $header = [];
            for ($i = 0; $i < count($items); $i++) {
                $header[0]["category"] = "หมวดหมู่";
                $header[0]["summary"] = "ยอดรวมทั้งหมด";
                for ($j = 0; $j < count($items[$i]["data"]); $j++) {
                    $colspan = count($items[$i]["data"][$j]["shop"]);
                    $header[0]["chanel"][] = array("name" => $items[$i]["data"][$j]["chanel"], "display_name" => $items[$i]["data"][$j]["display_name"], "colspan" => ($colspan > 1) ? $colspan + 1 : $colspan);
                    for ($k = 0; $k < count($items[$i]["data"][$j]["shop"]); $k++) {
                        $header[0]["shop"][] = array("name" => $items[$i]["data"][$j]["shop"][$k]["name"], "display_name" => $items[$i]["data"][$j]["shop"][$k]["display_name"]);
                    }
                    if ($colspan > 1) {
                        $header[0]["shop"][] = array("name" => "summary", "display_name" => "รวมยอด");
                    }
                }
                break;
            }
            $detail = [];
            $n = 0;
            for ($i = 0; $i < count($items); $i++) {
                $detail[$n]["category"] = $items[$i]["category"];
                if ($request->type == "qty") {
                    $detail[$n]["summary"] = self::calcRound($items[$i]["qty_total"]);
                } else {
                    $detail[$n]["summary"] = self::calcRound($items[$i]["price_total"]);
                }
                $detail[$n]["bill_summary"] = self::calcRound($items[$i]["bill_total"]);
                for ($j = 0; $j < count($items[$i]["data"]); $j++) {
                    $colspan = count($items[$i]["data"][$j]["shop"]);
                    for ($k = 0; $k < count($items[$i]["data"][$j]["shop"]); $k++) {
                        $detail[$n]["list"][] = array("name" => $items[$i]["data"][$j]["shop"][$k]["name"], "display_name" => $items[$i]["data"][$j]["shop"][$k]["display_name"], "bill" => self::calcRound($items[$i]["data"][$j]["shop"][$k]["bill"]), "qty" => self::calcRound($items[$i]["data"][$j]["shop"][$k]["qty"]), "price" => self::calcRound($items[$i]["data"][$j]["shop"][$k]["price"]));
                    }
                    if ($colspan > 1) {
                        $detail[$n]["list"][] = array("name" => "sum_" . $items[$i]["data"][$j]["chanel"], "display_name" => "รวมยอด" . str_replace('ชาแนล', '', $items[$i]["data"][$j]["display_name"]), "bill" => self::calcRound($items[$i]["data"][$j]["bill_total"]), "qty" => self::calcRound($items[$i]["data"][$j]["qty_total"]), "price" => self::calcRound($items[$i]["data"][$j]["price_total"]));
                    }
                }
                $n++;
            }
            $result["type"] = $request->type;
            $result["header"] = $header;
            $result["detail"] = $detail;
            return Excel::download(new DailySalesCustomerExport($result, "ตารางสรุปยอด"), 'ตารางสรุป เดือน '.str_replace("/", "-", $date_short).'.xlsx');
        }
    }

    public function callDataGroup($daily_category, $doc_date)
    {
        $daily_category = (!empty($daily_category)) ? $daily_category : "";
        $category = self::callDataGroupCat($daily_category, $doc_date);
        $level0 = self::callCustomerLevel(0);
        $level1 = self::callCustomerLevel(1);

        $summary = self::callGroupSummary($daily_category, $doc_date);
        $summary_count = count($summary);

        $i = 0;
        $items = [];
        foreach ($category as $cat) {
            $_category = (!empty($cat->daily_category)) ? $cat->daily_category : "";
            $cat_daily = ($cat->daily_category != "") ? $cat->daily_category : $this->other_category;

            if (in_array($_category, $this->fix_category)) {
                // ################################# FIX CATEGORY ####################################
                $fix_category = self::callDataFixGroupCat($_category, $doc_date);
                foreach ($fix_category as $fix_cat) {
                    $summary_unit = self::callGroupUnitSummary($_category, $doc_date, $fix_cat->unit);
                    $summary_unit_count = count($summary_unit);
                    $cat_daily = (!empty($fix_cat->daily_category)) ? $fix_cat->daily_category : $this->other_category;
                    $items[$i]["category"] = $cat_daily.'('.$fix_cat->unit.')';
                    $items[$i]["bill_total"] = self::calcRound($fix_cat->bill_total);
                    // $items[$i]["qty_total"] = self::calcRound($fix_cat->qty_total);
                    // $items[$i]["price_total"] = self::calcRound($fix_cat->netval_total);
                    $items[$i]["qty_total"] = 0;
                    $items[$i]["price_total"] = 0;
                    $data = [];
                    $j = 0;
                    foreach ($level0 as $l0) {
                        $data[$j]["chanel"] = $l0->name;
                        $data[$j]["display_name"] = $l0->display_name;
                        $data[$j]["bill_total"] = 0;
                        $data[$j]["qty_total"] = 0;
                        $data[$j]["price_total"] = 0;
                        $bill_total = 0;
                        $qty_total = 0;
                        $netval_total = 0;
                        $k = 0;
                        foreach ($level1 as $l1) {
                            if ($l0->id == $l1->parent_id) {
                                $data[$j]["shop"][$k]["name"] = $l1->name;
                                $data[$j]["shop"][$k]["display_name"] = $l1->display_name;
                                $data[$j]["shop"][$k]["bill"] = 0;
                                $data[$j]["shop"][$k]["qty"] = 0;
                                $data[$j]["shop"][$k]["price"] = 0;
                                for ($l = 0; $l < $summary_unit_count; $l++) {
                                    if ($_category == $summary_unit[$l]->daily_category && $l1->name == $summary_unit[$l]->name) {
                                        $data[$j]["shop"][$k]["name"] = $l1->name;
                                        $data[$j]["shop"][$k]["display_name"] = $l1->display_name;
                                        $data[$j]["shop"][$k]["bill"] = self::calcRound($summary_unit[$l]->bill_total);
                                        $data[$j]["shop"][$k]["qty"] = self::calcRound($summary_unit[$l]->qty_total);
                                        $data[$j]["shop"][$k]["price"] = self::calcRound($summary_unit[$l]->netval_total);
                                        $bill_total += self::calcRound($summary_unit[$l]->bill_total);
                                        $qty_total += self::calcRound($summary_unit[$l]->qty_total);
                                        $netval_total += self::calcRound($summary_unit[$l]->netval_total);
                                        break;
                                    }
                                }
                                $k++;
                            }
                        }
                        $data[$j]["bill_total"] = self::calcRound($bill_total);
                        $data[$j]["qty_total"] = self::calcRound($qty_total);
                        $data[$j]["price_total"] = self::calcRound($netval_total);
                        $items[$i]["qty_total"] += self::calcRound($qty_total);
                        $items[$i]["price_total"] += self::calcRound($netval_total);
                        $j++;
                    }
                    $items[$i]["data"] = $data;
                    $i++;
                }
                // ################################# END ####################################
            } else {
                $items[$i]["category"] = $cat_daily;
                $items[$i]["bill_total"] = self::calcRound($cat->bill_total);
                // $items[$i]["qty_total"] = self::calcRound($cat->qty_total);
                // $items[$i]["price_total"] = self::calcRound($cat->netval_total);
                $items[$i]["qty_total"] = 0;
                $items[$i]["price_total"] = 0;
                $data = [];
                $j = 0;
                foreach ($level0 as $l0) {
                    $data[$j]["chanel"] = $l0->name;
                    $data[$j]["display_name"] = $l0->display_name;
                    $data[$j]["bill_total"] = 0;
                    $data[$j]["qty_total"] = 0;
                    $data[$j]["price_total"] = 0;
                    $bill_total = 0;
                    $qty_total = 0;
                    $netval_total = 0;
                    $k = 0;
                    foreach ($level1 as $l1) {
                        if ($l0->id == $l1->parent_id) {
                            $data[$j]["shop"][$k]["name"] = $l1->name;
                            $data[$j]["shop"][$k]["display_name"] = $l1->display_name;
                            $data[$j]["shop"][$k]["bill"] = 0;
                            $data[$j]["shop"][$k]["qty"] = 0;
                            $data[$j]["shop"][$k]["price"] = 0;
                            for ($l = 0; $l < $summary_count; $l++) {
                                if ($_category == $summary[$l]->daily_category && $l1->name == $summary[$l]->name) {
                                    $data[$j]["shop"][$k]["name"] = $l1->name;
                                    $data[$j]["shop"][$k]["display_name"] = $l1->display_name;
                                    $data[$j]["shop"][$k]["bill"] = self::calcRound($summary[$l]->bill_total);
                                    $data[$j]["shop"][$k]["qty"] = self::calcRound($summary[$l]->qty_total);
                                    $data[$j]["shop"][$k]["price"] = self::calcRound($summary[$l]->netval_total);
                                    $bill_total += self::calcRound($summary[$l]->bill_total);
                                    $qty_total += self::calcRound($summary[$l]->qty_total);
                                    $netval_total += self::calcRound($summary[$l]->netval_total);
                                    break;
                                }
                            }
                            $k++;
                        }
                    }
                    $data[$j]["bill_total"] = self::calcRound($bill_total);
                    $data[$j]["qty_total"] = self::calcRound($qty_total);
                    $data[$j]["price_total"] = self::calcRound($netval_total);
                    $items[$i]["qty_total"] += self::calcRound($qty_total);
                    $items[$i]["price_total"] += self::calcRound($netval_total);
                    $j++;
                }
                $items[$i]["data"] = $data;
                $i++;
            }
        }
        return $items;
    }

    public function callCustomerLevel($level)
    {
        $level = EXCustomerGroup::where('level', '=', $level)->where('status', '=', 1)->orderBy('id')->get(['id', 'name', 'display_name', 'level', 'parent_id']);
        return $level;
    }

    public function callDataGroupCat($daily_category, $doc_date)
    {
        $daily_category = (!empty($daily_category)) ? $daily_category : "";
        $sort = self::sortCategory("d");
        $items = DB::table('monthly_sales as d')->leftJoin('ex_customer_parent as p', 'd.cuscod', 'like', DB::Raw("REPLACE(p.cuscod,'*','%')"))->where('d.stkcod', '<>', '');
        if ($daily_category != "all") {
            $items = $items->where('d.daily_category', '=', $daily_category);
        }
        if ($doc_date != "") {
            $items = $items->whereRaw('SUBSTRING(d.doc_date, 1, 7) = "'.$doc_date.'"');
        }
        $items = $items->select(
            'd.daily_category',
            'p.parent_id',
            DB::Raw('COUNT(DISTINCT(d.doc_num)) as bill_total'),
            DB::Raw('IF(ROUND(SUM(d.qty),2)>=1, ROUND(SUM(d.qty),2), 1) as qty_total'),
            DB::Raw('ROUND(SUM(d.unitpr),2) as unitpr_total'),
            DB::Raw('ROUND(SUM(d.netval),2) as netval_total')
        )->groupBy('d.daily_category')->orderByRaw($sort)->get()->reverse()->values();

        return $items;
    }

    public function callDataFixGroupCat($daily_category, $doc_date)
    {
        $daily_category = (!empty($daily_category)) ? $daily_category : "";
        $sort = self::sortCategory("d");
        $items = DB::table('monthly_sales as d')->leftJoin('ex_customer_parent as p', 'd.cuscod', 'like', DB::Raw("REPLACE(p.cuscod,'*','%')"))->where('d.stkcod', '<>', '');
        if ($daily_category != "all") {
            $items = $items->where('d.daily_category', '=', $daily_category);
        }
        if ($doc_date != "") {
            $items = $items->whereRaw('SUBSTRING(d.doc_date, 1, 7) = "'.$doc_date.'"');
        }
        $items = $items->select(
            'd.daily_category',
            'p.parent_id',
            'd.unit',
            DB::Raw('COUNT(DISTINCT(d.doc_num)) as bill_total'),
            DB::Raw('IF(ROUND(SUM(d.qty),2)>=1, ROUND(SUM(d.qty),2), 1) as qty_total'),
            DB::Raw('ROUND(SUM(d.unitpr),2) as unitpr_total'),
            DB::Raw('ROUND(SUM(d.netval),2) as netval_total')
        )->groupBy('d.daily_category', 'd.unit')->orderByRaw($sort)->get()->reverse()->values();

        return $items;
    }
}