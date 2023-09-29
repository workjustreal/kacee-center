<?php

namespace App\Http\Controllers;

use App\Models\Eplatform;
use App\Models\Eshop;
use App\Models\LazadaApi;
use Illuminate\Http\Request;
use Lazada\LazopClient;
use Lazada\LazopRequest;

class LazadaApiController extends Controller
{
    public $urlToken = "https://api.lazada.com/rest";
    public $url = '';
    public $appkey = '';
    public $appSecret = '';

    public function __construct()
    {
        $this->middleware('auth');
        $eplatform = Eplatform::find(2);
        $this->url = $eplatform->api_url;
        $this->appkey = $eplatform->app_key;
        $this->appSecret = $eplatform->app_secret;
    }

    public function index()
    {
        $list = LazadaApi::all();
        return view('token.lazada.lazada-list')->with('list', $list);
    }

    public function check_token($id)
    {
        $api = LazadaApi::find($id);
        $seller_id = $api->seller_id;

        $seller = self::callApi('/seller/get', $seller_id, null);

        return view('token.lazada.lazada-check-token')->with('seller', $seller);
    }

    public function access_token()
    {
        return view('token.lazada.lazada-access-token');
    }

    public function generate_access_token(Request $request)
    {
        $request->validate([
            'code' => 'required',
        ]);
        $code = trim($request->input('code'));

        $lazOp = new LazopClient($this->urlToken, $this->appkey, $this->appSecret);
        $lazRequest = new LazopRequest('/auth/token/create');
        $lazRequest->addApiParam('code', $code);
        $result = $lazOp->execute($lazRequest);
        $result = json_decode($result, true);

        if (isset($result["code"])) {
            if ($result["code"] == "0") {
                $user_id = "";
                $seller_id = "";
                $short_code = "";
                $country_user_info = $result["country_user_info"];
                for($i=0; $i<count($country_user_info); $i++){
                    if($country_user_info[$i]["country"] == "th"){
                        $user_id = $country_user_info[$i]["user_id"];
                        $seller_id = $country_user_info[$i]["seller_id"];
                        $short_code = $country_user_info[$i]["short_code"];
                    }
                }
                $laz = LazadaApi::where('short_code', '=', $short_code)->first();
                if ($laz) {
                    $api = LazadaApi::find($laz->id);
                    $api->code = $code;
                    $api->access_token = $result["access_token"];
                    $api->refresh_token = $result["refresh_token"];
                    $api->expires_in = (int)$result["expires_in"];
                    $api->refresh_expires_in = (int)$result["refresh_expires_in"];
                    $api->updated_at = now();
                    $api->update();
                } else {
                    $api = new LazadaApi();
                    $api->code = $code;
                    $api->access_token = $result["access_token"];
                    $api->refresh_token = $result["refresh_token"];
                    $api->expires_in = (int)$result["expires_in"];
                    $api->refresh_expires_in = (int)$result["refresh_expires_in"];
                    $api->country = $result["country"];
                    $api->account = $result["account"];
                    $api->account_platform = $result["account_platform"];
                    $api->user_id = $user_id;
                    $api->seller_id = $seller_id;
                    $api->short_code = $short_code;
                    $api->save();
                }
            } else {
                alert()->warning($result["code"]);
                return redirect()->back()->with('errors', $result);
            }
        } else {
            alert()->warning('Generate Access Token Error!');
            return redirect()->back();
        }

        alert()->success('Success.');
        return redirect('admin/token/lazada');
    }

    public function refresh_token($id)
    {
        $api = LazadaApi::find($id);
        $eshop = Eshop::where('seller_id', '=', $api->seller_id)->first();
        return view('token.lazada.lazada-refresh-token')->with('api', $api)->with('eshop', $eshop);
    }

    public function refresh_access_token(Request $request, $id)
    {
        $request->validate([
            'refresh_token' => 'required',
        ]);

        $api = LazadaApi::find($id);
        $refreshToken = $api->refresh_token;

        $lazOp = new LazopClient($this->urlToken, $this->appkey, $this->appSecret);
        $lazRequest = new LazopRequest('/auth/token/refresh');
        $lazRequest->addApiParam('refresh_token', $refreshToken);
        $result = $lazOp->execute($lazRequest);
        $result = json_decode($result, true);

        if (isset($result["code"])) {
            if ($result["code"] == "0") {
                $api = LazadaApi::find($id);
                $api->access_token = $result["access_token"];
                $api->refresh_token = $result["refresh_token"];
                $api->expires_in = (int)$result["expires_in"];
                $api->refresh_expires_in = (int)$result["refresh_expires_in"];
                $api->updated_at = now();
                $api->update();
            } else {
                alert()->warning($result["code"]);
                return redirect()->back()->with('errors', $result);
            }
        } else {
            alert()->warning('Refresh Token Error!');
            return redirect()->back();
        }

        alert()->success('Success.');
        return redirect('admin/token/lazada');
    }

    public function callApi($request_url, $seller_id, $parameters)
    {
        $api = LazadaApi::where('seller_id', '=', $seller_id)->first();
        $accessToken = $api->access_token;

        $lazOp = new LazopClient($this->url, $this->appkey, $this->appSecret);
        $lazRequest = new LazopRequest($request_url, 'GET');
        if ($parameters) {
            foreach ($parameters as $key => $value) {
                $lazRequest->addApiParam($key, $value);
            }
        }
        $result = $lazOp->execute($lazRequest, $accessToken);
        $result = json_decode($result, true);

        return $result;
    }
}