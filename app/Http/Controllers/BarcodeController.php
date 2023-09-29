<?php

namespace App\Http\Controllers;

use App\Exports\GenerateBarcodeExport;
use App\Imports\ExcelImport;
use App\Models\EXProduct;
use App\Models\GenerateBarcodeDetail;
use App\Models\GenerateBarcodeHeader;
use Carbon\Carbon;
use Exception;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class BarcodeController extends Controller
{
    public function upload()
    {
        return view('product.barcode-upload');
    }

    public function upload_print(Request $request)
    {
        $request->validate([
            'file' => 'required',
        ],[
            'file.required' => 'ยังไม่ได้เลือกไฟล์',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = 'barcode';
            $input['filename'] = $fileName . '.' . $file->extension();
            $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/assets/uploads/txt/';
            $file->move($destinationPath, $input['filename']);
            $fileName = $input['filename'];
        }

        $request->flash();
        return redirect('/product/barcode-view');
    }

    public function view()
    {
        $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/assets/uploads/txt/';
        $filecont = file_get_contents($destinationPath . 'barcode.txt');

        $barcode = array();

        $i = 0;
        foreach (explode("\r\n", $filecont) as $line) {
            if (!empty($line)) {
                $exp = explode("\t", $line);
                for ($j=0; $j<$exp[2]; $j++){
                    $barcode[$i]["barcode"] = $exp[0];
                    $barcode[$i]["sku"] = $exp[1];
                    $i++;
                }
            }
        }

        array_filter($barcode);

        return view('product.barcode-view')->with('barcode', $barcode);
    }

    public function lastDigit($runningcode)
    {
        $runningcode = (string)$runningcode;
        $sum = 0;
        $j = 12;
        for($i = 0; $i < 12; $i++) {
            $sum += ($runningcode[$i] * $j);
            $j--;
        }
        $last_digit = ($sum % 11) % 10;
        return $last_digit;
    }

    public function upload_generate(Request $request)
    {
        $request->validate([
            'file' => 'required',
        ],[
            'file.required' => 'ยังไม่ได้เลือกไฟล์',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = 'barcode_generate';
            $input['filename'] = $fileName . '.' . $file->extension();
            $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/assets/uploads/excel/';
            $file->move($destinationPath, $input['filename']);
            $fileName = $input['filename'];

            $data = Excel::toCollection(new ExcelImport, $destinationPath.$fileName)->toArray();
            $result= [];
            $i = 0;
            foreach ($data[0] as $value) {
                if ($i > 0) {
                    $result[] = [
                        "sku" => $value[0],
                        "description" => $value[1],
                    ];
                }
                $i++;
            }
            if (File::exists($destinationPath.$fileName)) {
                File::delete($destinationPath.$fileName);
            }
            return response()->json([
                "success" => true,
                "result" => $result
            ]);
        }
    }

    public function generate(Request $request)
    {
        $skus = $request->sku;
        $descriptions = $request->description;
        $remark = $request->remark;

        $errors = [];
        $chk_error = false;
        for ($i = 0; $i < count($skus); $i++) {
            $errors[$i] = (object) array(
                "index" => $i,
                "status" => "success",
                "message" => "",
            );
            if ($skus[$i] != "" && $skus[$i] != null) {
                $barcodeDetail = GenerateBarcodeDetail::where('sku', '=', Str::of($skus[$i])->trim()->upper())->first();
                if ($barcodeDetail) {
                    $errors[$i] = (object) array(
                        "index" => $i,
                        "status" => "error",
                        "message" => "* รหัสสินค้านี้มีบาร์โค้ดแล้ว",
                    );
                    $chk_error = true;
                } else {
                    $barcodeDetail = EXProduct::where('stkcod', '=', Str::of($skus[$i])->trim()->upper())->first();
                    if ($barcodeDetail) {
                        $errors[$i] = (object) array(
                            "index" => $i,
                            "status" => "error",
                            "message" => "* รหัสสินค้านี้มีบาร์โค้ดแล้ว",
                        );
                        $chk_error = true;
                    }
                }
            }
        }
        if ($chk_error == true) {
            $request->flash();
            alert()->warning("โปรดตรวจสอบข้อมูลให้ถูกต้อง");
            return redirect()->back()->with('errors', $errors);
        }

        $user = auth()->user();

        $success = false;
        DB::beginTransaction();

        try {
            // เลขเอกสารใหม่
            $gen = "GE" . date("ym");
            $rundoc = GenerateBarcodeHeader::whereRaw('SUBSTRING(generate_id, 1, 6) = "' . $gen . '"')->orderBy('generate_id', 'desc')->first();
            if ($rundoc) {
                $running_id = str_pad(intval(substr($rundoc->generate_id, 6, 4) + 1), 4, "0", STR_PAD_LEFT);
            } else {
                $running_id = "0001";
            }
            $generate_id = $gen . $running_id;

            // insert ข้อมูลใหม่
            $genheader = new GenerateBarcodeHeader();
            $genheader->generate_id = $generate_id;
            $genheader->remark = $remark;
            $genheader->userid = $user->id;
            $genheader->userip = $request->ip();
            $genheader->save();

            $organize = "318";
            $group = "00";
            $running = "0037410"; // Running start

            $lastGenId = GenerateBarcodeDetail::where('runningcode', '<>', '')->orderBy('runningcode', 'desc')->first();
            if ($lastGenId) {
                $runningcode = ($lastGenId->runningcode + 1);
            } else {
                $runningcode = $organize . $group . $running;
            }

            $data = [];
            $n = 0;
            for ($i = 0; $i < count($skus); $i++) {
                if ($skus[$i] != "" && $skus[$i] != null) {
                    $barcode = $runningcode . self::lastDigit($runningcode);
                    if (strlen($barcode) !== 13) {
                        throw new Exception("Barcode Length Error!");
                    }
                    // insert ข้อมูลใหม่
                    $gendetail = new GenerateBarcodeDetail();
                    $gendetail->generate_id = $generate_id;
                    $gendetail->sku = Str::of($skus[$i])->trim()->upper();
                    $gendetail->barcode = $barcode;
                    $gendetail->description = Str::of($descriptions[$i])->trim();
                    $gendetail->runningcode = $runningcode;
                    $gendetail->status = 1;
                    $gendetail->userid = $user->id;
                    $gendetail->userip = $request->ip();
                    $gendetail->save();

                    $data[$n] = (object) array(
                        "sku" => Str::of($skus[$i])->trim()->upper(),
                        "description" => Str::of($descriptions[$i])->trim(),
                        "barcode" => $barcode,
                    );

                    $runningcode++;
                    $n++;
                }
            }

            $success = true;
            if ($success) {
                DB::commit();
            }
            // all good
        } catch (\Exception $e) {
            DB::rollback();
            // something went wrong
            alert()->error('เกิดข้อผิดพลาด ไม่สามารถบันทึกข้อมูลได้ ' . $e->getMessage());
            return back();
        }

        alert()->success('สร้างบาร์โค้ดสินค้าเรียบร้อย');
        return redirect()->back()->with('data', $data)->with('generate_id', $generate_id);
    }

    public function barcode_search(Request $request)
    {
        if ($request->ajax()) {
            $data = GenerateBarcodeHeader::leftjoin('users', 'generate_barcode_header.userid', '=', 'users.id')
            ->where('generate_barcode_header.generate_id', '<>', '')
            ->where(function ($query) use ($request) {
                if (($username = $request->username)) {
                    $query->orWhere('users.name', 'LIKE', '%' . trim(str_replace(' ', '%', $username)) . '%');
                    $query->orWhere('users.surname', 'LIKE', '%' . trim(str_replace(' ', '%', $username)) . '%');
                }
                $start = "";
                $end = "";
                if ($request->date_start) {
                    $start = Carbon::createFromFormat('d/m/Y', $request->date_start)->format('Y-m-d');
                }
                if ($request->date_end) {
                    $end = Carbon::createFromFormat('d/m/Y', $request->date_end)->format('Y-m-d');
                }
                if ($start != '' && $end != '') {
                    $query->whereRaw('SUBSTRING(generate_barcode_header.created_at, 1, 10) >= "' . $start . '"');
                    $query->whereRaw('SUBSTRING(generate_barcode_header.created_at, 1, 10) <= "' . $end . '"');
                } else if ($start != '' && $end == '') {
                    $query->whereRaw('SUBSTRING(generate_barcode_header.created_at, 1, 10) = "' . $start . '"');
                } else if ($start == '' && $end != '') {
                    $query->whereRaw('SUBSTRING(generate_barcode_header.created_at, 1, 10) = "' . $end . '"');
                }
            });

            $totalRecords = $data->select('count(generate_barcode_header.*) as allcount')->count();
            $records = $data->select('generate_barcode_header.*', 'users.name as fname', 'users.surname as lname')->orderBy('generate_barcode_header.created_at', 'desc')->get();

            $response = array(
                "draw" => 25,
                "data" => $records,
                "recordsTotal" => $totalRecords,
                "recordsFiltered" => $totalRecords,
            );
            return response()->json($response);
        }
    }

    public function barcode_action(Request $request)
    {
        if ($request->ajax()) {
            $input = filter_input_array(INPUT_POST);
            if ($input['action'] == 'edit') {
                $data = GenerateBarcodeDetail::find($input['id']);
                $data->update(["description" => $input['description'], "updated_at" => now()]);
            } else if ($input['action'] == 'delete') {
                $data = GenerateBarcodeDetail::find($input['id']);
                $data->update(["status" => 0, "updated_at" => now()]);
            } else if ($input['action'] == 'restore') {
                $data = GenerateBarcodeDetail::find($input['id']);
                $data->update(["status" => 1, "updated_at" => now()]);
            }
            return response()->json($input);
        }
    }

    public function barcode_edit(Request $request)
    {
        $header = GenerateBarcodeHeader::leftjoin('users', 'generate_barcode_header.userid', '=', 'users.id')
                ->where('generate_barcode_header.generate_id', '=', $request->id)
                ->select('generate_barcode_header.*', 'users.name as fname', 'users.surname as lname')->first();
        $detail = GenerateBarcodeDetail::where('generate_id', '=', $request->id)->orderBy('id', 'asc')->get();
        return view('product.barcode-edit')->with('header', $header)->with('detail', $detail);
    }

    public function barcode_cancel(Request $request)
    {
        $detail = GenerateBarcodeDetail::find($request->id);
        $detail->status = 0;
        $detail->update();
        return redirect()->back();
    }

    public function barcode_export(Request $request)
    {
        $data = GenerateBarcodeDetail::where('generate_id', '=', $request->id)->where('status', '=', 1)
        ->select('sku', 'barcode', 'description')->orderBy('id', 'asc')->get();
        return Excel::download(new GenerateBarcodeExport($data, "Sheet1"), now().'.xls');
    }
}