<?php

namespace App\Http\Controllers;

use App\Models\Eplatform;
use App\Models\Eshop;
use App\Models\TiktokApi;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TikTokApiController extends Controller
{
    protected $domain = '';
    public $url = '';
    public $appKey = '';
    public $appSecret = '';

    public function __construct()
    {
        $this->middleware('auth');
        $eplatform = Eplatform::find(4);
        $this->url = $eplatform->api_url;
        $this->appKey = $eplatform->app_key;
        $this->appSecret = $eplatform->app_secret;
        $this->domain = "https://auth.tiktok-shops.com";
    }

    public function index()
    {
        $list = TiktokApi::all();
        return view('token.tiktok.tiktok-list')->with('list', $list);
    }

    public function getAccessToken($code, $shop_id)
    {
        $headers = [
            "Content-Type" => "application/json"
        ];
        $path = "/api/v2/token/get";
        $url = $this->domain . $path . "?app_key=" . $this->appKey . "&app_secret=" . $this->appSecret . "&auth_code=" . $code . "&grant_type=authorized_code";

        $response = Http::withOptions([
            'verify' => false,
        ])->timeout(120)->withHeaders($headers)->get($url);

        $responseBody = json_decode($response->getBody());
        return $responseBody;
    }

    public function getRefreshAccessToken($refresh_token, $shop_id)
    {
        $headers = [
            "Content-Type" => "application/json"
        ];
        $path = "/api/v2/token/refresh";
        $url = $this->domain . $path . "?app_key=" . $this->appKey . "&app_secret=" . $this->appSecret . "&refresh_token=" . $refresh_token . "&grant_type=refresh_token";

        $response = Http::withOptions([
            'verify' => false,
        ])->timeout(120)->withHeaders($headers)->get($url);

        $responseBody = json_decode($response->getBody());
        return $responseBody;
    }

    public function getAuthorizationUrl()
    {
        $state = Str::random(12); //hctV3mC0iYNe
        $url = "https://auth.tiktok-shops.com/oauth/authorize?app_key=".$this->appKey."&state=".$state;
        return $url;
    }

    public function check_token($id)
    {
        $api = TiktokApi::find($id);
        $seller_id = $api->seller_id;

        $seller = self::callApiV2('/api/seller/global/active_shops', $seller_id, null, 'GET');

        return view('token.tiktok.tiktok-check-token')->with('seller', $seller);
    }

    public function access_token()
    {
        $eshop = Eshop::where('platform_id', '=', 4)->get();
        $link = self::getAuthorizationUrl();
        return view('token.tiktok.tiktok-access-token')->with('eshop', $eshop)->with('link', $link);
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
        if (isset($result->code)) {
            if ($result->code == 0) {
                $tiktok = TiktokApi::where('seller_id', '=', $shop_id)->first();
                if ($tiktok) {
                    $api = TiktokApi::find($tiktok->id);
                    $api->code = $code;
                    $api->access_token = $result->data->access_token;
                    $api->expires_in = $result->data->access_token_expire_in;
                    $api->refresh_token = $result->data->refresh_token;
                    $api->refresh_expires_in = $result->data->refresh_token_expire_in;
                    $api->updated_at = now();
                    $api->update();
                } else {
                    $api = new TiktokApi();
                    $api->code = $code;
                    $api->access_token = $result->data->access_token;
                    $api->expires_in = $result->data->access_token_expire_in;
                    $api->refresh_token = $result->data->refresh_token;
                    $api->refresh_expires_in = $result->data->refresh_token_expire_in;
                    $api->country = "th";
                    $api->account = "-";
                    $api->account_platform = "seller_center";
                    $api->user_id = 0;
                    $api->seller_id = "";
                    $api->short_code = "-";
                    $api->save();
                }
            } else {
                alert()->warning($result->message);
                return redirect()->back()->with('errors', $result);
            }
        } else {
            alert()->warning('Generate Access Token Error!');
            return redirect()->back();
        }

        alert()->success('Success.');
        return redirect('admin/token/tiktok');
    }

    public function refresh_token($id)
    {
        $api = TiktokApi::find($id);
        $eshop = Eshop::where('seller_id', '=', $api->seller_id)->first();
        return view('token.tiktok.tiktok-refresh-token')->with('api', $api)->with('eshop', $eshop);
    }

    public function refresh_access_token(Request $request, $id)
    {
        $request->validate([
            'refresh_token' => 'required',
        ]);

        $api = TiktokApi::find($id);
        $shop_id = $api->seller_id;
        $refresh_token = $api->refresh_token;

        $result = self::getRefreshAccessToken($refresh_token, $shop_id);
        if (isset($result->code)) {
            if ($result->code == 0) {
                $api = TiktokApi::find($id);
                $api->access_token = $result->data->access_token;
                $api->expires_in = $result->data->access_token_expire_in;
                $api->refresh_token = $result->data->refresh_token;
                $api->refresh_expires_in = $result->data->refresh_token_expire_in;
                $api->updated_at = now();
                $api->update();
            } else {
                alert()->warning($result->message);
                return redirect()->back()->with('errors', $result);
            }
        } else {
            alert()->warning('Refresh Token Error!');
            return redirect()->back();
        }

        alert()->success('Success.');
        return redirect('admin/token/tiktok');
    }

    public function callRefreshAccessToken($refresh_token, $shop_id, $id)
    {
        $result = self::getRefreshAccessToken($refresh_token, $shop_id);
        if (isset($result->code)) {
            if ($result->code == 0) {
                $api = TiktokApi::find($id);
                $api->access_token = $result->data->access_token;
                $api->expires_in = $result->data->access_token_expire_in;
                $api->refresh_token = $result->data->refresh_token;
                $api->refresh_expires_in = $result->data->refresh_token_expire_in;
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
        $api = TiktokApi::where('seller_id', '=', $shop_id)->first();
        $expires_in = $api->expires_in;

        $dateS = date_create(date('Y-m-d'));
        $dateE = date_create(date("Y-m-d H:i:s", $expires_in));
        $diff = date_diff($dateS, $dateE);
        $balance_date = (int)$diff->format("%R%a");
        if ($balance_date <= 1) {
            // 1 day
            return true;
        }
        return false;
    }

    public function callApiV2($request_url, $shop_id, $parameters, $method)
    {
        $token_expire = self::callCheckTokenExpire($shop_id);
        if ($token_expire) {
            $api = TiktokApi::where('seller_id', '=', $shop_id)->first();
            $api_id = $api->id;
            $refresh_token = $api->refresh_token;
            $refresh = self::callRefreshAccessToken($refresh_token, $shop_id, $api_id);
            if (isset($refresh->code)) {
                if ($refresh->code != 0) {
                    return json_decode(json_encode($refresh));
                }
            }
        }

        $api = TiktokApi::where('seller_id', '=', $shop_id)->first();
        $api_id = $api->id;
        $access_token = $api->access_token;

        $timest = time();
        $host = $this->url;
        $path = $request_url;

        $common_params = 'app_key='.$this->appKey.'&timestamp='.$timest.'&sign='.'&access_token='.$access_token.'&shop_id='.$shop_id;
        $params = '';
        if ($method == "GET") {
            if ($parameters != null) {
                $params .= '&'.http_build_query($parameters);
            }
        }
        $query = $host . $path . '?' . $common_params . $params;
        $url_components = parse_url($query); // Split scheme, host, path, query
        parse_str($url_components['query'], $queries); // query to array
        ksort($queries); // Sort array by key
        $concat_params = ''; // 1. Extract all query param EXCEPT ' sign ', ' access_token ', reorder the params based on alphabetical order.
        foreach ($queries as $key => $value) {
            if ($key != "sign" && $key != "access_token") {
                $concat_params .= $key.$value; // 2. Concat all the param in the format of {key}{value}
            }
        }
        $concat_params = $path . $concat_params; // 3. Append the request path to the beginning
        $base_string = $this->appSecret.$concat_params.$this->appSecret; // 4. Wrap string generated in step 3 with app_secret.
        // 5. Initiate the algorithm based on app_secret and produce the digest.
        // 6. Encode the digest byte stream in hexadecimal.
        // 7. Use sha256 to generate sign with salt(secret).
        // 8. Timestamp valid within 5 minutes.
        $sign = hash_hmac('sha256', $base_string, $this->appSecret, false);

        $headers = [
            "Content-Type" => "application/json"
        ];
        if ($method == "POST") {
            $url = $host . $path . '?app_key=' . $this->appKey . '&access_token=' . $access_token . '&sign=' . $sign . '&timestamp=' . $timest . '&shop_id=' . $shop_id;
            $response = Http::timeout(120)->withOptions([
                'verify' => false,
            ])->withHeaders($headers)->withBody(json_encode($parameters), 'application/json')->post($url);
        } else if ($method == "GET") {
            $url = $host . $path . '?app_key=' . $this->appKey . '&access_token=' . $access_token . '&sign=' . $sign . '&timestamp=' . $timest . '&shop_id=' . $shop_id . $params;
            $response = Http::withOptions([
                'verify' => false,
            ])->timeout(120)->withHeaders($headers)->get($url);
        }

        $responseBody = json_decode($response->getBody());
        return $responseBody;
    }
}