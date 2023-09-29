<?php

namespace App\Http\Controllers;

use App\Models\ProductGroupReport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonthlySaleBaseController extends Controller
{
    protected $sort_category;
    protected $other_category;
    protected $fix_category;
    protected $unit_category;

    public function __construct()
    {
        $this->middleware('auth');
        $this->sort_category = ['HOME KACEE', 'KACEE MALLของกิน', 'KACEE MALLของใช้', 'KACEE FASHION', 'เครื่องเขียน-อุปกรณ์สำนักงาน', 'ผ้าม่าน', 'ผ้าม่านสำเร็จรูป', 'ม่านม้วน', 'ม่านม้วน สำเร็จรูป', 'ม่านม้วน ค่าสกรีน', 'มู่ลี่ไม้', 'มู่ลี่อลูมิเนียม', 'ฉากกั้นห้อง', 'ม่านปรับแสง', 'รางประดับ', 'รางประดับ สำเร็จรูป', 'รางม่าน', 'รางม่านพับ'];
        $this->other_category = "อื่นๆ";
        $this->fix_category = ['รางประดับ', 'รางม่าน', 'รางม่านพับ'];
        $this->unit_category = array(
            [
                "category" => "HOME KACEE",
                "unit" => "ชิ้น"
            ],
            [
                "category" => "KACEE MALLของกิน",
                "unit" => "ชิ้น"
            ],
            [
                "category" => "KACEE MALLของใช้",
                "unit" => "ชิ้น"
            ],
            [
                "category" => "KACEE FASHION",
                "unit" => "ตัว"
            ],
            [
                "category" => "เครื่องเขียน-อุปกรณ์สำนักงาน",
                "unit" => "ชิ้น"
            ],
            [
                "category" => "ผ้าม่าน",
                "unit" => "หลา"
            ],
            [
                "category" => "ผ้าม่านสำเร็จรูป",
                "unit" => "ชุด"
            ],
            [
                "category" => "ม่านม้วน",
                "unit" => "ชุด"
            ],
            [
                "category" => "ม่านม้วน สำเร็จรูป",
                "unit" => "ชุด"
            ],
            [
                "category" => "ม่านม้วน ค่าสกรีน",
                "unit" => "ตร.ม."
            ],
            [
                "category" => "มู่ลี่ไม้",
                "unit" => "ชุด"
            ],
            [
                "category" => "มู่ลี่อลูมิเนียม",
                "unit" => "ชุด"
            ],
            [
                "category" => "ฉากกั้นห้อง",
                "unit" => "ชุด"
            ],
            [
                "category" => "ม่านปรับแสง",
                "unit" => "ชุด"
            ],
            [
                "category" => "รางประดับ(ชด)",
                "unit" => "ชุด"
            ],
            [
                "category" => "รางประดับ(มร)",
                "unit" => "เมตร"
            ],
            [
                "category" => "รางประดับ สำเร็จรูป",
                "unit" => "ชุด"
            ],
            [
                "category" => "รางม่าน(ชด)",
                "unit" => "ชุด"
            ],
            [
                "category" => "รางม่าน(มร)",
                "unit" => "เมตร"
            ],
            [
                "category" => "รางม่านพับ(ชด)",
                "unit" => "ชุด"
            ],
            [
                "category" => "รางม่านพับ(มร)",
                "unit" => "เมตร"
            ],
            [
                "category" => "OUTDOOR BLINDS",
                "unit" => "ชุด"
            ],
            [
                "category" => "SKY LIGHT ชุดโซ่",
                "unit" => "ชุด"
            ],
            [
                "category" => "SKY LIGHT ชุดมอเตอร์",
                "unit" => "ชุด"
            ],
            [
                "category" => "WALLPAPER 3D แผ่น",
                "unit" => "แผ่น"
            ],
            [
                "category" => "WALLPAPER ม้วน",
                "unit" => "ม้วน"
            ],
            [
                "category" => "กระดุมแม่เหล็ก",
                "unit" => "ชิ้น"
            ],
            [
                "category" => "กันสาด ชุด",
                "unit" => "ชุด"
            ],
            [
                "category" => "กันสาดอลูมิเนียม",
                "unit" => "ชุด"
            ],
            [
                "category" => "เข็มกลัดผ้า",
                "unit" => "ชิ้น"
            ],
            [
                "category" => "เครื่องล๊อคประตูอัตโนมัติ",
                "unit" => "ชุด"
            ],
            [
                "category" => "ชายครุย บานพับ",
                "unit" => "ชุด"
            ],
            [
                "category" => "ชายครุย พับ",
                "unit" => "พับ"
            ],
            [
                "category" => "แชงกรีล่า",
                "unit" => "ชุด"
            ],
            [
                "category" => "ด้ามจูง",
                "unit" => "เส้น"
            ],
            [
                "category" => "ตะขอเกี่ยวสายรวบม่าน",
                "unit" => "ตัว"
            ],
            [
                "category" => "ตุ้มประดับ",
                "unit" => "ชิ้น"
            ],
            [
                "category" => "พื้น กระเบื้องยาง",
                "unit" => "ตร.ม."
            ],
            [
                "category" => "ฟิล์มกรองแสง",
                "unit" => "เมตร"
            ],
            [
                "category" => "เฟอร์นิเจอร์",
                "unit" => "ตัว"
            ],
            [
                "category" => "มอเตอร์ ชุด",
                "unit" => "ชุด"
            ],
            [
                "category" => "ม่านดรีมบาย",
                "unit" => "ชุด"
            ],
            [
                "category" => "ม่านบาหลี",
                "unit" => "ชุด"
            ],
            [
                "category" => "ม่านพลาสติก",
                "unit" => "ชุด"
            ],
            [
                "category" => "ม่านพาแนลแทรค เมตร",
                "unit" => "เมตร"
            ],
            [
                "category" => "ม่านไม้ไผ่",
                "unit" => "ชุด"
            ],
            [
                "category" => "ม่านไม้ไผ่ ฉากบังตา",
                "unit" => "ชุด"
            ],
            [
                "category" => "ม่านรังผึ้ง",
                "unit" => "ชุด"
            ],
            [
                "category" => "ม่านริ้วญี่ปุ่น",
                "unit" => "ชุด"
            ],
            [
                "category" => "ม่านลูกปัด",
                "unit" => "ชุด"
            ],
            [
                "category" => "ม่านห้องน้ำ",
                "unit" => "ผืน"
            ],
            [
                "category" => "ม่านห้องน้ำ ราง",
                "unit" => "เส้น"
            ],
            [
                "category" => "มุ้งลวด",
                "unit" => "ชุด"
            ],
            [
                "category" => "มู่ลี่PVCสำเร็จรูป",
                "unit" => "ชุด"
            ],
            [
                "category" => "เมจิกสกรีน",
                "unit" => "ชุด"
            ],
            [
                "category" => "เยื่อไม้ธรรมชาติ",
                "unit" => "ชุด"
            ],
            [
                "category" => "รางเลื่อน",
                "unit" => "ชุด"
            ],
            [
                "category" => "รางสำเร็จรูป",
                "unit" => "เส้น"
            ],
            [
                "category" => "โรลเลอร์ชัตเตอร์",
                "unit" => "ชุด"
            ],
            [
                "category" => "สายรวบม่าน",
                "unit" => "เส้น"
            ],
            [
                "category" => "สินค้าอื่นๆ",
                "unit" => "ชิ้น"
            ],
            [
                "category" => "สุขภัณฑ์",
                "unit" => "ชุด"
            ],
            [
                "category" => "ห่วงตาไก่",
                "unit" => "ห่วง"
            ],
            [
                "category" => "อุปกรณ์ม่าน",
                "unit" => "ชิ้น"
            ],
            [
                "category" => $this->other_category, // อื่นๆ
                "unit" => "ชิ้น"
            ]
        );
    }

    public function sortCategory($table="")
    {
        $result = "";
        $tb = ($table!="") ? $table."." : "";
        foreach ($this->sort_category as $sort) {
            if ($result != "") $result .= ',';
            $result .= $tb."daily_category='$sort'";
        }
        $daily_category = ProductGroupReport::groupBy("daily_category")->orderByRaw('daily_category="", daily_category ASC')->get(['daily_category']);
        foreach ($daily_category as $cat) {
            if (!in_array($cat->daily_category, $this->sort_category)) {
                if ($result != "") $result .= ',';
                $result .= $tb."daily_category='$cat->daily_category'";
            }
        }
        return $result;
    }

    public function unit_category($category)
    {
        $key = array_search($category, array_column($this->unit_category, 'category'));
        $result = ($key !== false) ? $this->unit_category[$key]["unit"] : '';
        return $result;
    }

    public function callGroupSummary($daily_category, $doc_date)
    {
        $daily_category = (!empty($daily_category)) ? $daily_category : "";
        $data = DB::table('ex_customer_group as g')->leftJoin('ex_customer_parent as p', 'g.id', '=', 'p.parent_id')
        ->leftJoin('monthly_sales as d', 'd.cuscod', 'like', DB::Raw("REPLACE(p.cuscod,'*','%')"))->where('g.status', '=', 1)->where('g.level', '=', 1);
        if ($daily_category != "all") {
            $data = $data->where('d.daily_category', '=', $daily_category);
        }
        if ($doc_date != "") {
            $data = $data->whereRaw('SUBSTRING(d.doc_date, 1, 7) = "'.$doc_date.'"');
        }
        $data = $data->select('g.name', 'p.parent_id', 'd.daily_category', DB::Raw('COUNT(DISTINCT(d.doc_num)) as bill_total'), DB::Raw('IF(ROUND(SUM(d.qty),2)>=1, ROUND(SUM(d.qty),2), 1) as qty_total'), DB::Raw('ROUND(SUM(d.unitpr),2) as unitpr_total'), DB::Raw('ROUND(SUM(d.netval),2) as netval_total'))
        ->groupBy('g.id', 'd.daily_category', 'p.parent_id')->orderBy('d.daily_category')->orderBy('p.parent_id')->get()->toArray();
        return $data;
    }

    public function callGroupUnitSummary($daily_category, $doc_date, $unit)
    {
        $daily_category = (!empty($daily_category)) ? $daily_category : "";
        $data = DB::table('ex_customer_group as g')->leftJoin('ex_customer_parent as p', 'g.id', '=', 'p.parent_id')
        ->leftJoin('monthly_sales as d', 'd.cuscod', 'like', DB::Raw("REPLACE(p.cuscod,'*','%')"))->where('g.status', '=', 1)->where('g.level', '=', 1);
        if ($daily_category != "all") {
            $data = $data->where('d.daily_category', '=', $daily_category);
        }
        if ($unit != "") {
            $data = $data->where('d.unit', '=', $unit);
        }
        if ($doc_date != "") {
            $data = $data->whereRaw('SUBSTRING(d.doc_date, 1, 7) = "'.$doc_date.'"');
        }
        $data = $data->select('g.name', 'p.parent_id', 'd.daily_category', DB::Raw('COUNT(DISTINCT(d.doc_num)) as bill_total'), DB::Raw('IF(ROUND(SUM(d.qty),2)>=1, ROUND(SUM(d.qty),2), 1) as qty_total'), DB::Raw('ROUND(SUM(d.unitpr),2) as unitpr_total'), DB::Raw('ROUND(SUM(d.netval),2) as netval_total'))
        ->groupBy('g.id', 'd.daily_category', 'p.parent_id')->orderBy('d.daily_category')->orderBy('p.parent_id')->get()->toArray();
        return $data;
    }

    public function callSummary($daily_category, $doc_date)
    {
        $daily_category = (!empty($daily_category)) ? $daily_category : "";
        $data = DB::table('monthly_sales')->where('stkcod', '<>', '');
        if ($daily_category != "all") {
            $data = $data->where('daily_category', '=', $daily_category);
        }
        if ($doc_date != "") {
            $data = $data->whereRaw('SUBSTRING(doc_date, 1, 7) = "'.$doc_date.'"');
        }
        $data = $data->select(
            'daily_category',
            DB::Raw('COUNT(DISTINCT(doc_num)) as bill_total'),
            DB::Raw('IF(ROUND(SUM(qty),2)>=1, ROUND(SUM(qty),2), 1) as qty_total'),
            DB::Raw('ROUND(SUM(unitpr),2) as unitpr_total'),
            DB::Raw('ROUND(SUM(netval),2) as netval_total')
        )->groupBy('daily_category', 'unit')->orderBy('daily_category', 'ASC')->get();
        if ($data->isNotEmpty()) {
            // ที่ต้องวนลูปรวมจำนวน,ราคา เพราะต้องทำให้ตรงกับจำนวนที่แสดง (ไม่เอาจุดทศนิยม)
            $bill = 0;
            $qty = 0;
            $price = 0;
            foreach ($data as $v) {
                $bill += self::calcRound($v->bill_total);
                $qty += self::calcRound($v->qty_total);
                $price += self::calcRound($v->netval_total);
            }
            return array("bill"=>self::calcRound($bill), "qty"=>self::calcRound($qty), "price"=>self::calcRound($price));
        }
        return array("bill"=>0, "qty"=>self::calcRound(0), "price"=>self::calcRound(0));
    }

    public function summary(Request $request)
    {
        if ($request->ajax()) {
            if ($request->doc_date == "") {
                return response()->json(["success"=>false]);
            }
            $daily_category = (!empty($request->daily_category)) ? $request->daily_category : "";
            $doc_date = "";
            $date_short = "";
            if ($request->doc_date != '') {
                $d = explode("/", $request->doc_date);
                $doc_date = $d[1]."-".$d[0];
                $date_short = $d[0]."/".substr(($d[1]+543), 2, 2);
            }

            $summary = self::callSummary($daily_category, $doc_date);

            if (count($summary)) {
                return response()->json(["success"=>true, "summary_text"=>"เดือน ".$date_short." รายงานยอดขาย", "summary_qty"=>self::calcNumberFormat($summary["qty"]), "summary_price"=>self::calcNumberFormat($summary["price"])]);
            } else {
                return response()->json(["success"=>false]);
            }
        }
    }

    public function calcRound($value)
    {
        return round($value);
    }

    public function calcNumberFormat($value)
    {
        return number_format(round($value));
    }

    public function calcRound2Decimal($value)
    {
        return round($value, 2);
    }

    public function calcNumberFormat2Decimal($value)
    {
        return number_format(round($value, 2), 2);
    }
}