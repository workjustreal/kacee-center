<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ChatbotController extends Controller
{
    protected $baseUrl;
    protected $lineApi;

    public function __construct()
    {
        $this->middleware('auth');
        ini_set('max_execution_time', 300);
        $this->baseUrl = "http://192.168.2.12:4410";
        $this->lineApi = "/api/v1/line_chatbot";
    }

    // public function index()
    // {
    //     return view('chatbot.chatbot');
    // }

    // public function history()
    // {
    //     $user = Auth::user();
    //     $emp_id = trim((string)$user->emp_id);

    //     $url = $this->baseUrl . $this->lineApi . "/history/$emp_id";
    //     $headers = [
    //         "Content-Type" => "application/json",
    //         "api-key" => env('FASTAPI_KEY')
    //     ];
    //     $response = Http::withOptions([
    //         'verify' => false,
    //     ])->timeout(300)->withHeaders($headers)->get($url);
    //     $responseBody = json_decode($response->getBody());
    //     return $responseBody;
    // }

    // public function query(Request $request)
    // {
    //     $user = Auth::user();

    //     $body = [
    //         "emp_id" => trim((string)$user->emp_id),
    //         "query" => trim($request['query']),
    //     ];
    //     $url = $this->baseUrl . $this->lineApi . "/query";
    //     $headers = [
    //         "Content-Type" => "application/json",
    //         "api-key" => env('FASTAPI_KEY')
    //     ];
    //     $response = Http::withOptions([
    //         'verify' => false,
    //     ])->timeout(300)->withHeaders($headers)->post($url, $body);
    //     $responseBody = json_decode($response->getBody());
    //     return $responseBody;
    // }

    public function line_users()
    {
        return view('chatbot.line_users');
    }

    public function line_search(Request $request)
    {
        if ($request->ajax()) {
            $url = $this->baseUrl . $this->lineApi . "/users";
            $headers = [
                "Content-Type" => "application/json",
                "x-api-key" => env('CHATBOT_KEY')
            ];
            $parameters = [
                'status_chatbot' => $request->status_chatbot
            ];
            $response = Http::withOptions([
                'verify' => false,
            ])->timeout(300)->withHeaders($headers)->get($url, $parameters);
            $responseBody = json_decode($response->getBody());

            $rows = [];
            $n = 1;
            if ($responseBody->status == 'Ok') {
                $records = $responseBody->data;
                $totalRecords = count($records);
                foreach ($records as $rec) {
                    $status_value = ($rec->status_chatbot == "unlock") ? 'lock' : 'unlock';
                    $checked = ($rec->status_chatbot == "unlock") ? 'checked' : '';
                    $action = '<div class="form-check form-switch form-check-success">
                        <input class="form-check-input" type="checkbox" id="lock_\''.$n.'\'" onchange="updateStatusChatbot(\''.$rec->user_id.'\', \''.$status_value.'\')" '.$checked.'>
                        <label class="form-check-label" for="lock_\''.$n.'\'"></label>
                    </div>';
                    $rows[] = array(
                        "no" => $n,
                        "user_id" => $rec->user_id,
                        "status_chatbot" => $rec->status_chatbot,
                        "username" => '<div class="table-user"><img loading="lazy" src="'.$rec->picture_url.'" onerror="this.onerror=null;this.src=\''.url('assets/images/users/thumbnail/user-1.jpg').'\';" alt="table-user" class="me-2 rounded-circle">' . $rec->display_name . '</div>',
                        "created_at" => Carbon::parse($rec->created_at)->format('Y-m-d H:i:s'),
                        "updated_at" => Carbon::parse($rec->updated_at)->format('Y-m-d H:i:s'),
                        "action" => $action,
                    );
                    $n++;
                }
            } else {
                $totalRecords = 0;
                $rows = [];
            }
            $response = array(
                "total" => $totalRecords,
                "totalNotFiltered" => $totalRecords,
                "rows" => $rows,
            );
            return response()->json($response);
        }
    }

    public function line_update_status_chatbot(Request $request)
    {
        if ($request->ajax()) {
            $url = $this->baseUrl . $this->lineApi . "/lock_unlock";
            $headers = [
                "Content-Type" => "application/json",
                "x-api-key" => env('CHATBOT_KEY')
            ];
            $parameters = [
                'user_id' => $request->user_id,
                'value' => $request->value,
            ];
            $response = Http::withOptions([
                'verify' => false,
            ])->timeout(300)->withHeaders($headers)->put($url, $parameters);
            $responseBody = json_decode($response->getBody());
            if ($responseBody->status == 'Ok') {
                return response()->json(['success'=>true, 'message'=>'อัปเดตเรียบร้อย']);
            } else {
                return response()->json(['success'=>false, 'message'=>'เกิดข้อผิดพลาด!']);
            }
        }
    }

    public function line_update_status_chatbot_select(Request $request)
    {
        if (count($request->user_id) <= 0) {
            return response()->json(['success' => false, 'message' => 'ยังไม่ได้เลือกผู้ใช้!']);
        }
        $url = $this->baseUrl . $this->lineApi . "/lock_unlock_select";
        $headers = [
            "Content-Type" => "application/json",
            "x-api-key" => env('CHATBOT_KEY')
        ];
        $parameters = [
            'status_chatbot' => $request->status_chatbot,
            'user_id' => $request->user_id,
        ];
        $response = Http::withOptions([
            'verify' => false,
        ])->timeout(300)->withHeaders($headers)->put($url, $parameters);
        $responseBody = json_decode($response->getBody());
        if ($responseBody->status == 'Ok') {
            return response()->json(['success'=>true, 'message'=>'อัปเดตเรียบร้อย']);
        } else {
            return response()->json(['success'=>false, 'message'=>'เกิดข้อผิดพลาด!']);
        }
    }
}