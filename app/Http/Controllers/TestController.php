<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Support\Str;
use DNS2D;

class TestController extends Controller
{
    private $img;
    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function html5_qrcode()
    {
        return view('test.html5-qrcode');
    }

    public function animation()
    {
        return view('test.animation');
    }

    public function grid_elements()
    {
        // $filename = 'demo.pdf';

        // $view = \View::make('test.qrcode');
        // $html = $view->render();

        // $pdf = new TCPDF();

        // $pdf::SetTitle('Hello World');
        // $pdf::SetFont('freeserif', '', 8);
        // $pdf::SetMargins(1, 1, 1, 1);
        // $pdf::SetAutoPageBreak(TRUE, 0);
        // $pdf::AddPage('L', array(32,16), false, false);
        // // $pdf::AddPage('L', array(50,20), false, false);
        // $pdf::writeHTML($html, true, false, true, false, '');

        // // $pdf::Output(public_path($filename), 'F');
        // $pdf::Output($filename, 'I');

        // return response()->download(public_path($filename));

        header("Content-type: image/jpeg");
        // $imgPath = 'http://upload.wikimedia.org/wikipedia/en/3/37/VII-200-bakside.jpg';
        // $image = imagecreatefromjpeg($imgPath);
        //create the image
        $image = imagecreatetruecolor(400, 600);
        $color = imagecolorallocate($image, 0,0, 250);
        $string = "A&B UK LIMITED";
        $fontSize = 5;
        $x = 50;
        $y = 50;
        imagestring($image, $fontSize, $x, $y, $string, $color);
        imagejpeg($image,'test.jpeg',75);
        imagejpeg($image);
    }

    public function test_web_service_printer()
    {
        $response = Http::post('http://192.168.3.88/Integration/WebServiceIntegrationProductBarcodeLabelPrinter/Execute', [
            'PrinterName' => 'TSC TTP-247',
            'BarTenderLabel' => 'product_barcode.btw',
            'EventData' => '123456789;ABCDEFG;2\n123456789;ABCDEFGH;3',
        ]);
        $responseBody = $response->body();
        return $responseBody;
    }

    public function receipt()
    {
        $data = array(
            [
                "qty" => "2",
                "item" => "เสื้อแขนยาว",
                "price" => "1,000.00",
            ],
            [
                "qty" => "1",
                "item" => "กางเกงขาสั้น สีน้ำเงินกางเกงขาสั้น สีน้ำเงิน",
                "price" => "230.00",
            ],
            [
                "qty" => "2",
                "item" => "ABCDEFG fgdffdsddgfgfgdf",
                "price" => "1,000.00",
            ],
            [
                "qty" => "1",
                "item" => "กางเกงขาสั้น สีน้ำเงิน",
                "price" => "230.00",
            ],
        );
        $height = 350;
        for ($i=0; $i<count($data); $i++) {
            $height += 18;
        }
        $width = 380;
        $fontPath = $_SERVER['DOCUMENT_ROOT'] . '\assets\fonts';
        $font = $fontPath.'\THSarabunNew.ttf';
        $fontB = $fontPath.'\THSarabunNew Bold.ttf';

        $im = imagecreate($width, $height);
        // $bg = imagecolorallocate($im, 255,255,180); // yellowv
        $bg = imagecolorallocate($im, 255,255,255); // white
        $grey = imagecolorallocate($im, 128, 128, 128);
        $black = imagecolorallocate($im, 0, 0, 0);

        // ===================== LOGO IMAGE ======================
        $logo_path = $_SERVER['DOCUMENT_ROOT'] . '/assets/images/logo-kacee.png';
        list($logo_width, $logo_height) = getimagesize($logo_path); // หาขนาดของไฟล์สำหรับวางทับด้านบน
        $newwidth = 100;
        $newheight = 100;
        // โดยสร้างจากไฟล์ ต้นฉบับ
        $logo = @imagecreatefrompng($logo_path);
        imagefilter($logo, IMG_FILTER_GRAYSCALE);
        // สร้างรูปภาพตามค่าใหม่ สำหรับรูปภาพวางทับด้านบน
        $logo_thumb = imagecreatetruecolor($newwidth, $newheight);
        // เริ่มสร้างรูปภาพตามค่าใหม่ สำหรับรูปภาพวางทับด้านบน ตามขนาดที่กำหนด
        imagecopyresized($logo_thumb, $logo, 0, 0, 0, 0, $newwidth, $newheight, $logo_width, $logo_height);
        // Copy and merge
        imagecopymerge($im, $logo, 140, 10, 0, 0, 100, 100, 100);
        // ===================== LOGO IMAGE ======================

        // ===================== LOGO TEXT ======================
        // $text = 'KACEE BEST';
        // $fw = imagefontwidth(5);     // width of a character
        // $l = strlen($text);          // number of characters
        // $tw = $l * $fw;              // text width
        // $iw = imagesx($im);          // image width

        // $xpos = ($iw - $tw)/3;
        // $ypos = 60;
        // imagettftext($im, 40, 0, $xpos, $ypos, $black, $fontB, $text);
        // ===================== LOGO TEXT ======================

        // ===================== SUB HEADER ======================
        $ypos = 110;
        $text = '19/01/2566 10:12';
        $fw = imagefontwidth(2);     // width of a character
        $l = strlen($text);          // number of characters
        $tw = $l * $fw;              // text width
        $iw = imagesx($im);          // image width

        $xpos = ($iw - $tw)/2.3;
        $ypos += 20;
        imagettftext($im, 18, 0, $xpos, $ypos, $black, $fontB, $text);

        $text = 'Tel: 02-4293333';
        $fw = imagefontwidth(3);     // width of a character
        $l = strlen($text);          // number of characters
        $tw = $l * $fw;              // text width
        $iw = imagesx($im);          // image width

        $xpos = ($iw - $tw)/2.1;
        $ypos += 20;
        imagettftext($im, 18, 0, $xpos, $ypos, $black, $fontB, $text);

        $text = 'Website: kaceebest.com';
        $fw = imagefontwidth(3);     // width of a character
        $l = strlen($text);          // number of characters
        $tw = $l * $fw;              // text width
        $iw = imagesx($im);          // image width

        $xpos = ($iw - $tw)/2.2;
        $ypos += 20;
        imagettftext($im, 18, 0, $xpos, $ypos, $black, $fontB, $text);

        $xpos = 10;
        $ypos += 5;
        imagestring($im, 5, $xpos, $ypos, "----------------------------------------", $black);
        // ===================== SUB HEADER ======================

        // ===================== DETAIL ======================
        $ypos += 15;
        for ($i=0; $i<count($data); $i++) {
            $ypos += 20;
            $xpos = 10;
            imagettftext($im, 16, 0, $xpos, $ypos, $black, $fontB, $data[$i]["qty"]);

            $xpos = 50;
            imagettftext($im, 16, 0, $xpos, $ypos, $black, $fontB, Str::limit($data[$i]["item"], 40)); //ใส่ 30 จะไม่แสดงรูปแต่ใช้ได้

            $dimensions = imagettfbbox(16, 0, $fontB, $data[$i]["price"]);
            $textWidth = abs($dimensions[4] - $dimensions[0]);
            $xpos = imagesx($im) - $textWidth - 10;
            imagettftext($im, 16, 0, $xpos, $ypos, $black, $fontB, $data[$i]["price"]);
        }
        // ===================== DETAIL ======================

        // ===================== FOOTER ======================
        $xpos = 10;
        $ypos += 40;
        imagettftext($im, 28, 0, $xpos, $ypos, $black, $fontB, "TOTAL");
        $dimensions = imagettfbbox(28, 0, $fontB, "2,460.00");
        $textWidth = abs($dimensions[4] - $dimensions[0]);
        $xpos = imagesx($im) - $textWidth - 10;
        imagettftext($im, 28, 0, $xpos, $ypos, $black, $fontB, "2,460.00");

        $xpos = 10;
        $ypos += 2;
        imagestring($im, 5, $xpos, $ypos, "========================================", $black);

        $text = '------ Thank you ------';
        $fw = imagefontwidth(2);     // width of a character
        $l = strlen($text);          // number of characters
        $tw = $l * $fw;              // text width
        $iw = imagesx($im);          // image width

        $xpos = ($iw - $tw)/2.6;
        $ypos += 60;
        imagettftext($im, 22, 0, $xpos, $ypos, $black, $fontB, $text);
        // ===================== FOOTER ======================

        header("content-type: image/png");
        ob_start();
        imagepng($im);
        imagedestroy($im); // Clear Memory
        // printf('<img id="output" src="data:image/png;base64,%s" />', base64_encode(ob_get_clean()));
        // $base64 = 'data:image/png;base64,' . base64_encode(ob_get_clean());
        $base64 = base64_encode(ob_get_clean());
        return response()->json(["success"=>true, "data"=>$base64]);
    }
}