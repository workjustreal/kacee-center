<?php

namespace App\Http\Controllers;

use App\Models\Eplatform;
use App\Models\Eshop;
use App\Models\NocnocApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NocNocApiController extends Controller
{
    public $url = '';
    public $partner_id = '';
    public $secretKey = '';

    public function __construct()
    {
        $this->middleware('auth');
        $eplatform = Eplatform::find(3);
        $this->url = $eplatform->api_url;
        $this->partner_id = (int)$eplatform->app_key;
        $this->secretKey = $eplatform->app_secret;
    }

    public function index()
    {
        $list = NocnocApi::all();
        return view('token.nocnoc.nocnoc-list')->with('list', $list);
    }

    public function check_token($id)
    {
        $api = NocnocApi::find($id);
        $seller_id = $api->seller_id;

        $token_expire = self::callCheckTokenExpire($seller_id);
        if ($token_expire) {
            $requestToken = self::callRequestAccessToken($seller_id);
            if ($requestToken != true) {
                return view('token.nocnoc.nocnoc-check-token')->with('success', false)->with('seller', $requestToken);
            }
        }
        return view('token.nocnoc.nocnoc-check-token')->with('success', true);

        // $seller = self::callApiV1('/shop/get_shop_info', $seller_id, null, "GET");
        // return view('token.nocnoc.nocnoc-check-token')->with('seller', $seller);
    }

    public function access_token()
    {
        $eshop = Eshop::where('platform_id', '=', 3)->get();
        return view('token.nocnoc.nocnoc-access-token')->with('eshop', $eshop);
    }

    public function generate_access_token(Request $request)
    {
        $request->validate([
            'shop' => 'required',
        ]);
        $shop_id = trim($request->input('shop'));

        $result = self::callRequestAccessToken($shop_id);
        if ($result != true) {
            alert()->warning("Error", $result);
            return redirect()->back()->with('errors', $result);
        }

        alert()->success('Success.');
        return redirect('admin/token/nocnoc');
    }

    public function callRequestAccessToken($shop_id)
    {
        $api = NocnocApi::where('seller_id', '=', $shop_id)->first();
        $client_id = $api->client_id;
        $body = [
            "client_id" => $client_id,
            "secret_key" => $this->secretKey,
        ];
        $url = $this->url . "/api/v1/client/token";

        $headers = [
            "Content-Type" => "application/json"
        ];
        $response = Http::timeout(120)->withOptions([
            'verify' => false,
        ])->withHeaders($headers)->withBody(json_encode($body), 'application/json')->post($url);
        $responseBody = json_decode($response->getBody());
        if (isset($responseBody->access_token)) {
            $nocnoc = NocnocApi::where('seller_id', '=', $shop_id)->first();
            if ($nocnoc) {
                $dt = new \DateTime(date('Y-m-d H:i:s', strtotime($responseBody->token_expiry_timestamp)), new \DateTimeZone('GMT'));
                $loc = (new \DateTime)->getTimezone();
                $dt->setTimezone($loc);
                $token_expiry_timestamp = $dt->format('Y-m-d H:i:s T');
                $current_time = date('Y-m-d H:i:s');
                $token_expired = (strtotime(date('Y-m-d H:i:s', strtotime($token_expiry_timestamp))) - strtotime($current_time));

                $api = NocnocApi::find($nocnoc->id);
                $api->access_token = $responseBody->access_token;
                $api->expires_in = $token_expired;
                $api->updated_at = now();
                $api->update();
            }
            return true;
        } else {
            return $responseBody;
        }
    }

    public function callCheckTokenExpire($shop_id)
    {
        $api = NocnocApi::where('seller_id', '=', $shop_id)->first();
        $expires_in = $api->expires_in;

        $current_time = date('Y-m-d H:i:s');
        $updated_at = $api->updated_at;
        $diff_date = (strtotime($current_time) - strtotime($updated_at));
        $expire_time = ($expires_in - $diff_date);
        if ((int)$expire_time <= 600) {
            // 10 min
            return true;
        }
        return false;
    }

    public function callApiV1($request_url, $shop_id, $parameters, $method)
    {
        $token_expire = self::callCheckTokenExpire($shop_id);
        if ($token_expire) {
            $requestToken = self::callRequestAccessToken($shop_id);
            if ($requestToken != true) {
                return json_decode(json_encode($requestToken));
            }
        }
        $api = NocnocApi::where('seller_id', '=', $shop_id)->first();
        $access_token = $api->access_token;

        $version = "/api/v1";
        $apiUrl = $this->url.$version.$request_url;
        $token = $access_token;
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => "Bearer $token",
        ];
        if ($method == "POST") {
            $response = Http::timeout(120)->withOptions([
                'verify' => false,
            ])->withHeaders($headers)->withBody(json_encode($parameters), 'application/json')->post($apiUrl);
        } else if ($method == "GET") {
            if ($parameters == null) {
                $response = Http::timeout(120)->withOptions([
                    'verify' => false,
                ])->withHeaders($headers)->get($apiUrl);
            } else {
                $response = Http::timeout(120)->withOptions([
                    'verify' => false,
                ])->withHeaders($headers)->withBody(json_encode($parameters), 'application/json')->get($apiUrl);
            }
        }
        $responseBody = json_decode($response->getBody());

        return $responseBody;
    }
}