<?php

namespace App\Http\Controllers;

use App\Models\Eplatform;
use App\Models\Eshop;
use App\Models\ShopeeApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ShopeeApiController extends Controller
{
    public $url = '';
    public $partner_id = '';
    public $secretKey = '';

    public function __construct()
    {
        $this->middleware('auth');
        $eplatform = Eplatform::find(1);
        $this->url = $eplatform->api_url;
        $this->partner_id = (int)$eplatform->app_key;
        $this->secretKey = $eplatform->app_secret;
    }

    public function index()
    {
        $list = ShopeeApi::all();
        return view('token.shopee.shopee-list')->with('list', $list);
    }

    public function getRefreshTokenByUpgradeCode()
    {
        $timest = time();
        $body = [
            "upgrade_code" => "70665059476f424c4452507741445552586d4f574555715448754d484a51786e",
            "shop_id_list" => [82190001],
        ];
        $host = $this->url;
        $path = "/api/v2/public/get_refresh_token_by_upgrade_code";
        $base_string = $this->partner_id . $path . $timest;
        $sign = hash_hmac('sha256', $base_string, $this->secretKey, false);
        $url = $host . $path . "?partner_id=" . $this->partner_id . "&timestamp=" . $timest . "&sign=" . $sign;

        $headers = [
            "Content-Type" => "application/json"
        ];
        $response = Http::withOptions([
            'verify' => false,
        ])->timeout(120)->withHeaders($headers)->post($url, $body);
        $responseBody = json_decode($response->getBody());

        return $responseBody;
    }

    public function getAccessToken($code, $shop_id)
    {
        // shop level
        $timest = time();
        $body = [
            "code" => $code,
            "partner_id" => $this->partner_id,
            "shop_id" => (int)$shop_id,
        ];
        $host = $this->url;
        $path = "/api/v2/auth/token/get";
        $base_string = $this->partner_id . $path . $timest;
        $sign = hash_hmac('sha256', $base_string, $this->secretKey, false);
        $url = $host . $path . "?partner_id=" . $this->partner_id . "&timestamp=" . $timest . "&sign=" . $sign;

        $headers = [
            "Content-Type" => "application/json"
        ];
        $response = Http::withOptions([
            'verify' => false,
        ])->timeout(120)->withHeaders($headers)->post($url, $body);
        $responseBody = json_decode($response->getBody());

        return $responseBody;
    }

    public function getRefreshAccessToken($refresh_token, $shop_id)
    {
        // shop level
        $timest = time();
        $body = [
            "refresh_token" => $refresh_token,
            "partner_id" => $this->partner_id,
            "shop_id" => (int)$shop_id,
        ];
        $host = $this->url;
        $path = "/api/v2/auth/access_token/get";
        $base_string = $this->partner_id . $path . $timest;
        $sign = hash_hmac('sha256', $base_string, $this->secretKey, false);
        $url = $host . $path . "?partner_id=" . $this->partner_id . "&timestamp=" . $timest . "&sign=" . $sign;

        $headers = [
            "Content-Type" => "application/json"
        ];
        $response = Http::withOptions([
            'verify' => false,
        ])->timeout(120)->withHeaders($headers)->post($url, $body);
        $responseBody = json_decode($response->getBody());

        return $responseBody;
    }

    public function getAuthorizationUrl()
    {
        $timest = time();
        $host = $this->url;
        $path = "/api/v2/shop/auth_partner";
        $redirect_url = "https://shop.kaceebest.com/";
        $base_string = $this->partner_id . $path . $timest;
        $sign = hash_hmac('sha256', $base_string, $this->secretKey, false);
        $url = $host . $path . "?partner_id=" . $this->partner_id . "&timestamp=" . $timest . "&sign=" . $sign . "&redirect=" . $redirect_url;
        return $url;
    }

    public function getHttpResponseCode($code)
    {
        switch ($code) {
            case 200:
                $result = "OK";
                break;
            case 201:
                $result = "Created";
                break;
            case 202:
                $result = "Accepted";
                break;
            case 203:
                $result = "Non-Authoritative Information";
                break;
            case 400:
                $result = "Bad Request";
                break;
            case 401:
                $result = "Unauthorized";
                break;
            case 403:
                $result = "Forbidden";
                break;
            case 404:
                $result = "Not Found";
                break;
            case 405:
                $result = "Method Not Allowed";
                break;
            case 408:
                $result = "Request Timeout";
                break;
            case 500:
                $result = "Internal Server Error";
                break;
            case 501:
                $result = "Not Implemented";
                break;
            case 502:
                $result = "Bad Gateway";
                break;
            case 503:
                $result = "Service Unavailable";
                break;
            default:
                $result = "";
                break;
        }
        return $result;
    }

    public function check_token($id)
    {
        $api = ShopeeApi::find($id);
        $seller_id = $api->seller_id;

        $seller = self::callApiV2('/api/v2/shop/get_shop_info', $seller_id, null, 'GET');

        return view('token.shopee.shopee-check-token')->with('seller', $seller);
    }

    public function access_token()
    {
        $eshop = Eshop::where('platform_id', '=', 1)->get();
        $link = self::getAuthorizationUrl();
        return view('token.shopee.shopee-access-token')->with('eshop', $eshop)->with('link', $link);
    }

    public function generate_access_token(Request $request)
    {
        $request->validate([
            'shop' => 'required',
            'code' => 'required',
        ]);
        $shop_id = trim($request->input('shop'));
        $code = trim($request->input('code'));

        $result = self::getAccessToken($code, $shop_id);
        if (isset($result->error)) {
            if ($result->error == "") {
                $shopee = ShopeeApi::where('seller_id', '=', $shop_id)->first();
                if ($shopee) {
                    $api = ShopeeApi::find($shopee->id);
                    $api->code = $code;
                    $api->access_token = $result->access_token;
                    $api->refresh_token = $result->refresh_token;
                    $api->expires_in = $result->expire_in;
                    $api->updated_at = now();
                    $api->update();
                } else {
                    $api = new ShopeeApi();
                    $api->code = $code;
                    $api->access_token = $result->access_token;
                    $api->refresh_token = $result->refresh_token;
                    $api->expires_in = $result->expire_in;
                    $api->refresh_expires_in = 0;
                    $api->country = "th";
                    $api->account = "-";
                    $api->account_platform = "seller_center";
                    $api->user_id = 0;
                    $api->seller_id = $shop_id;
                    $api->short_code = "-";
                    $api->save();
                }
            } else {
                alert()->warning($result->error);
                return redirect()->back()->with('errors', $result);
            }
        } else {
            alert()->warning('Generate Access Token Error!');
            return redirect()->back();
        }

        alert()->success('Success.');
        return redirect('admin/token/shopee');
    }

    public function refresh_token($id)
    {
        $api = ShopeeApi::find($id);
        $eshop = Eshop::where('seller_id', '=', $api->seller_id)->first();
        return view('token.shopee.shopee-refresh-token')->with('api', $api)->with('eshop', $eshop);
    }

    public function refresh_access_token(Request $request, $id)
    {
        $request->validate([
            'refresh_token' => 'required',
        ]);

        $api = ShopeeApi::find($id);
        $shop_id = $api->seller_id;
        $refresh_token = $api->refresh_token;

        $result = self::getRefreshAccessToken($refresh_token, $shop_id);
        if (isset($result->error)) {
            if ($result->error == "") {
                $api = ShopeeApi::find($id);
                $api->access_token = $result->access_token;
                $api->refresh_token = $result->refresh_token;
                $api->expires_in = $result->expire_in;
                $api->refresh_expires_in = 0;
                $api->updated_at = now();
                $api->update();
            } else {
                alert()->warning($result->error);
                return redirect()->back()->with('errors', $result);
            }
        } else {
            alert()->warning('Refresh Token Error!');
            return redirect()->back();
        }

        alert()->success('Success.');
        return redirect('admin/token/shopee');
    }

    public function callRefreshAccessToken($refresh_token, $shop_id, $id)
    {
        $result = self::getRefreshAccessToken($refresh_token, $shop_id);
        if (isset($result->error)) {
            if ($result->error == "") {
                $api = ShopeeApi::find($id);
                $api->access_token = $result->access_token;
                $api->refresh_token = $result->refresh_token;
                $api->expires_in = $result->expire_in;
                $api->refresh_expires_in = 0;
                $api->updated_at = now();
                $api->update();
            } else {
                return $result;
            }
        }
        return $result;
    }

    public function callCheckTokenExpire($shop_id)
    {
        $api = ShopeeApi::where('seller_id', '=', $shop_id)->first();
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

    public function callApi($request_url, $parameters)
    {
        $version = "/api/v1";
        $apiUrl = $this->url.$version.$request_url;
        $authorisation = $apiUrl . "|" . json_encode($parameters);
        $authorisation = rawurlencode(hash_hmac('sha256', $authorisation, $this->secretKey, false));
        $headers = array(
            'Content-Type: application/json',
            'Authorization: ' . $authorisation,
        );
        $connection = curl_init();
        curl_setopt($connection, CURLOPT_URL, $apiUrl);
        curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($connection, CURLOPT_POST, 1);
        curl_setopt($connection, CURLOPT_POSTFIELDS, json_encode($parameters));
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($connection);
        curl_close($connection);
        $data = json_decode($response);

        return $data;
    }

    public function callApiV1($request_url, $parameters)
    {
        $version = "/api/v1";
        $apiUrl = $this->url.$version.$request_url;
        $authorisation = $apiUrl . "|" . json_encode($parameters);
        $authorisation = rawurlencode(hash_hmac('sha256', $authorisation, $this->secretKey, false));
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => $authorisation,
        ];
        $response = Http::withOptions([
            'verify' => false,
        ])->timeout(120)->withHeaders($headers)->post($apiUrl, $parameters);
        // $statusCode = $response->status();
        // $statusMsg = self::getHttpResponseCode($statusCode);
        $responseBody = json_decode($response->getBody());

        return $responseBody;
    }

    public function callApiV2($request_url, $shop_id, $parameters, $method)
    {
        $token_expire = self::callCheckTokenExpire($shop_id);
        if ($token_expire) {
            $api = ShopeeApi::where('seller_id', '=', $shop_id)->first();
            $api_id = $api->id;
            $refresh_token = $api->refresh_token;
            $refresh = self::callRefreshAccessToken($refresh_token, $shop_id, $api_id);
            if ($refresh->error != "") {
                return json_decode(json_encode($refresh));
            }
        }

        $api = ShopeeApi::where('seller_id', '=', $shop_id)->first();
        $api_id = $api->id;
        $access_token = $api->access_token;
        $refresh_token = $api->refresh_token;

        $timest = time();
        $host = $this->url;
        $path = $request_url;
        $base_string = $this->partner_id . $path . $timest . $access_token . $shop_id;
        $sign = hash_hmac('sha256', $base_string, $this->secretKey, false);
        $url = $host . $path . "?partner_id=" . $this->partner_id . "&timestamp=" . $timest . "&access_token=" . $access_token . "&shop_id=" . $shop_id . "&sign=" . $sign;
        $headers = [
            "Content-Type" => "application/json"
        ];
        if ($method == "POST") {
            $response = Http::withOptions([
                'verify' => false,
            ])->timeout(120)->withHeaders($headers)->post($url, $parameters);
        } else if ($method == "GET") {
            if ($parameters != null) {
                foreach ($parameters as $key => $value) {
                    $url .= "&".$key."=".$value;
                }
            }
            $response = Http::withOptions([
                'verify' => false,
            ])->timeout(120)->withHeaders($headers)->get($url);
        }
        // $statusCode = $response->status();
        $responseBody = json_decode($response->getBody());

        return $responseBody;
    }
}