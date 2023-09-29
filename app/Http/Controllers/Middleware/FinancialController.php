<?php

namespace App\Http\Controllers\Middleware;

use App\Http\Controllers\Controller;
use App\Models\Eshop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FinancialController extends Controller
{
    protected $baseUrl;

    public function __construct()
    {
        $this->middleware('auth');
        ini_set('memory_limit','512M');
        ini_set('max_execution_time', 300);
        $this->baseUrl = "http://192.168.2.12:4444/api/v1/financial";
    }

    public function transaction()
    {
        $eshop = Eshop::where('status', '<>', 0)->whereIn('id', [1,2])->get();
        return view('middleware.financial.transaction')->with('eshop', $eshop);
    }

    public function get_transaction(Request $request)
    {
        $body = [
            "platform" => $request->platform,
            "seller_id" => $request->seller_id,
            "date_start" => $request->date_start,
            "date_end" => $request->date_end,
        ];
        $url = $this->baseUrl . "/transaction/get";
        $headers = [
            "Content-Type" => "application/json",
            "api-key" => env('FASTAPI_KEY')
        ];
        $response = Http::withOptions([
            'verify' => false,
        ])->timeout(300)->withHeaders($headers)->post($url, $body);
        $responseBody = json_decode($response->getBody());
        return $responseBody;
    }

    public function download_transaction(Request $request)
    {
        return response()->streamDownload(function () use ($request) {
            $response = Http::withOptions(['verify' => false])->withHeaders([
                'accept' => 'application/octet-stream'
            ])->get($request->url);
            echo $response->body();
        }, $request->filename);
    }

    public function calcRound($value)
    {
        return (int)$value;
    }

    public function calcNumberFormat($value)
    {
        return number_format((int)$value);
    }

    public function calcRound2Decimal($value)
    {
        return round((float)$value, 2);
    }

    public function calcNumberFormat2Decimal($value)
    {
        return (strpos($value, ".")) ? number_format(round($value, 2), 2) : number_format($value);
    }
}