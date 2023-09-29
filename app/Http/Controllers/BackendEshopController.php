<?php

namespace App\Http\Controllers;

use App\Exports\BackendProductExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class BackendEshopController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function image_products(Request $request)
    {
        $shopeeController = new ShopeeApiController;
        $shopid = 82190001;

        $page = ($request->page > 1) ? $request->page : 1;
        $page_size = 50;
        $offset = (($page - 1) * $page_size);

        $result = [];
        $n = 0;

        $parameters = [
            "offset" => $offset,
            "page_size" => $page_size,
            "item_status" => "NORMAL",
        ];
        $item_list = $shopeeController->callApiV2('/api/v2/product/get_item_list', $shopid, $parameters, 'GET');
        if ($item_list->error == "") {
            $item_resp = $item_list->response;
            $item_count = count($item_resp->item);
            for ($i = 0; $i < $item_count; $i++) {
                $parameters = [
                    "item_id" => $item_resp->item[$i]->item_id,
                ];
                $model_list = $shopeeController->callApiV2('/api/v2/product/get_model_list', $shopid, $parameters, 'GET');
                if ($model_list->error == "") {
                    if (isset($model_list->response->tier_variation)) {
                        if (isset($model_list->response->tier_variation[0]->option_list)) {
                            $option_list = $model_list->response->tier_variation[0]->option_list;
                            $option_count = count($option_list);
                            for ($l=0; $l<$option_count; $l++) {
                                if (isset($option_list[$l]->image->image_id)) {
                                    $result[$n] = [
                                        "image_id" => $option_list[$l]->image->image_id,
                                        "image_url" => $option_list[$l]->image->image_url,
                                    ];
                                    $n++;
                                }
                            }
                        }
                    }
                }
            }
        }
        $data = $this->paginate($result);
        return view('backend-eshop.image-products')->with('data', $data);
    }

    public function paginate($items, $perPage = 25, $page = null, $options = [])
    {
        $options["path"] = url('/backend-eshop/image-products');
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    public function get_image_products(Request $request)
    {
        $shopeeController = new ShopeeApiController;
        $shopid = 82190001;

        $page_size = 50;
        $offset = ($request->load_count > 0) ? ($request->load_count * $page_size) : 0;

        $result = [];
        $n = 0;

        $parameters = [
            "offset" => $offset,
            "page_size" => $page_size,
            "item_status" => "NORMAL",
        ];
        $item_list = $shopeeController->callApiV2('/api/v2/product/get_item_list', $shopid, $parameters, 'GET');
        if ($item_list->error == "") {
            $item_resp = $item_list->response;
            $item_count = count($item_resp->item);
            $item_id_list = [];
            for ($i = 0; $i < $item_count; $i++) {
                $item_id_list[] = $item_resp->item[$i]->item_id;
            }
            $parameters = [
                "item_id_list" => implode(",", $item_id_list),
            ];
            $item_base = $shopeeController->callApiV2('/api/v2/product/get_item_base_info', $shopid, $parameters, 'GET');
            if ($item_base->error == "") {
                $item_list = $item_base->response->item_list;
                foreach ($item_list as $item) {
                    $parameters = [
                        "item_id" => $item->item_id,
                    ];
                    $model_list = $shopeeController->callApiV2('/api/v2/product/get_model_list', $shopid, $parameters, 'GET');
                    if ($model_list->error == "") {
                        $tier_count = count($model_list->response->tier_variation);
                        for ($r=0; $r<$tier_count; $r++) {
                            if (isset($model_list->response->tier_variation[$r]->option_list)) {
                                $option_count = count($model_list->response->tier_variation[$r]->option_list);
                                for ($i=0; $i<$option_count; $i++) {
                                    if (isset($model_list->response->tier_variation[$r]->option_list[$i]->image->image_id)) {
                                        $result[$n] = [
                                            "image_id" => $model_list->response->tier_variation[$r]->option_list[$i]->image->image_id,
                                            "image_url" => $model_list->response->tier_variation[$r]->option_list[$i]->image->image_url,
                                        ];
                                        $n++;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return response()->json(["success" => true, "result" => $result]);
    }

    public function download_products()
    {
        return view('backend-eshop.download-products');
    }

    public function get_products(Request $request)
    {
        // if($request->optionCheck) {
        //     foreach ($request->optionCheck as $req) {
        //         echo $req."<br>";
        //     }
        // }
        // dd("SUCCESS");
        $shopeeController = new ShopeeApiController;
        $shopid = 82190001;

        $result = [];
        $n = 0;

        $parameters = [
            "language" => "TH",
        ];
        $category = $shopeeController->callApiV2('/api/v2/product/get_category', $shopid, $parameters, 'GET');
        if ($category->error == "") {
            $category_resp = $category->response->category_list;
            // dd($category_resp);
        }

        $parameters = [
            "offset" => 0,
            "page_size" => 10,
            "item_status" => "NORMAL",
        ];
        $item_list = $shopeeController->callApiV2('/api/v2/product/get_item_list', $shopid, $parameters, 'GET');
        if ($item_list->error == "") {
            $item_resp = $item_list->response;
            $item_count = count($item_resp->item);
            $item_id_list = [];
            for ($i = 0; $i < $item_count; $i++) {
                $item_id_list[] = $item_resp->item[$i]->item_id;
            }
            $parameters = [
                "item_id_list" => implode(",", $item_id_list),
            ];
            $item_base = $shopeeController->callApiV2('/api/v2/product/get_item_base_info', $shopid, $parameters, 'GET');
            if ($item_base->error == "") {
                $item_list = $item_base->response->item_list;
                foreach ($item_list as $item) {
                    $parameters = [
                        "item_id" => $item->item_id,
                    ];
                    $model_list = $shopeeController->callApiV2('/api/v2/product/get_model_list', $shopid, $parameters, 'GET');
                    if ($model_list->error == "") {
                        // dd($model_list->response);
                        $model_count = count($model_list->response->model);
                        for ($r=0; $r<$model_count; $r++) {
                            $option = "";
                            if (isset($model_list->response->model[$r]->tier_index)) {
                                for ($i=0; $i<count($model_list->response->model[$r]->tier_index); $i++) {
                                    $l = $model_list->response->model[$r]->tier_index[$i];
                                    if ($option!="") $option .= ",";
                                    $option .= $model_list->response->tier_variation[$i]->option_list[$l]->option;
                                }
                            }
                            $model_sku = (isset($model_list->response->model[$r]->model_sku)) ? $model_list->response->model[$r]->model_sku : "";
                            $original_price = (isset($model_list->response->model[$r]->price_info[0]->original_price)) ? $model_list->response->model[$r]->price_info[0]->original_price : "";
                            $current_price = (isset($model_list->response->model[$r]->price_info[0]->current_price)) ? $model_list->response->model[$r]->price_info[0]->current_price: "";

                            $cat_index = array_search($item->category_id, array_column($category_resp, 'category_id'));
                            $result[$n] = [
                                "category" => ($cat_index !== false) ? $category_resp[$cat_index]->display_category_name : "",
                                "parent_sku" => $item->item_sku,
                                "name_th" => $item->item_name,
                                "sku" => $model_sku,
                                "option" => $option,
                                "description_th" => $item->description,
                                "original_price" => $original_price,
                                "current_price" => $current_price,
                                // "original_price2" => (isset($item->price_info)) ? $item->price_info->original_price : "",
                                // "current_price2" => (isset($item->price_info)) ? $item->price_info->current_price : "",
                                "width" => (isset($item->dimension)) ? $item->dimension->package_width : "",
                                "length" => (isset($item->dimension)) ? $item->dimension->package_length : "",
                                "height" => (isset($item->dimension)) ? $item->dimension->package_height : "",
                                "weight" => $item->weight,
                            ];
                            $n++;
                        }
                    }
                }
            }
        }
        // dd($result);
        return Excel::download(new BackendProductExport($result, "Sheet1"), 'product_'.now().'.xlsx');
    }
    // $detail = DB::table('checkout_shipment_detail as d')->leftjoin('shipping_history as sh', 'sh.trackingnumber', '=', 'd.trackingnumber')
    // ->where('d.running', '=', 'CS22070012')->select('d.trackingnumber', 'd.so', 'sh.ordernumber')->orderBy('d.trackingnumber', 'asc')->orderBy('d.so', 'asc')->get();
    // foreach ($detail as $value) {
    //     $shipment = DB::table('checkout_shipment_detail')->where('running', '=', 'CS22070012')->where('trackingnumber', '=', $value->trackingnumber)->where('so', '=', $value->so);
    //     $shipment->update(["ordernumber" => $value->ordernumber]);
    // }
    // dd("SUCCESS");
}