<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProvinceController extends Controller
{
    protected $path;
    protected $json;
    protected $data;

    public function __construct()
    {
        $this->path = $_SERVER['DOCUMENT_ROOT'] . '/assets/thai.json';
        $this->json = json_decode(file_get_contents($this->path), true);
        $this->data = collect($this->json);
    }

    public function getChangwats()
    {
        $changwats = $this->data->groupBy('province')->map->only(['province'])->keys()->all();

        return $changwats;
    }

    public function getAmphoes(Request $request)
    {
        $changwat = $request->get('changwat');

        $amphoes = $this->data->where('province', $changwat)->groupBy('amphoe')->keys()->all();

        return $amphoes;
    }

    public function getTambons(Request $request)
    {
        $changwat = $request->get('changwat');
        $amphoe = $request->get('amphoe');

        $tambons = $this->data->where('province', $changwat)->where('amphoe', $amphoe)->groupBy('district')->keys()->all();

        return $tambons;
    }

    public function getZipcodes(Request $request)
    {
        $changwat = $request->get('changwat');
        $amphoe = $request->get('amphoe');
        $tambon = $request->get('tambon');

        $zipcodes = $this->data->where('province', $changwat)->where('amphoe', $amphoe)->where('district', $tambon)->groupBy('zipcode')->keys()->all();

        return $zipcodes;
    }
}