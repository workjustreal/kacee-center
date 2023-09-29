<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Manual;
use Carbon\Carbon;

class ManualController extends Controller
{
    public function index(){
        return view('manual.manual-index');
    }

    public function search(Request $request){
        if ($request->ajax()) {
            $data = Manual::where('manual_id', '<>', '');
            $totalRecords = $data->select('count(*) as allcount')->count();
            $records = $data->select('*')->orderBy('manual_id', 'ASC')->get();
            $rows = [];
            foreach ($records as $key => $rec) {
                $action = self::manageEditor($rec->manual_id);
                $date_th = self::dateFormat($rec->created_at);
                $fileLink = self::fileLink($rec->manual_file);

                $rows[] = array(
                    "manual_id" => $key + 1,
                    "manual_name" => $rec->manual_name,
                    "manual_file" => $fileLink,
                    "created_at" => $date_th,
                    "action" => $action,
                );
            }

            $response = array(
                "total" => $totalRecords,
                "totalNotFiltered" => $totalRecords,
                "rows" => $rows,
            );
            return response()->json($response);dd($request->page);
        }
    }

    // ---------------------------------------------------------------

    public function dateFormat($date){
        $day = Carbon::parse($date)->format('d');
        $month = Carbon::parse($date)->locale('th_TH')->isoFormat('MMMM');
        $year = Carbon::parse($date)->format('Y') + 543;
        return $day.' '.$month.' '.$year;
    }

    public function fileLink($file_name){
        $res = '<a href="'.url('assets/files/manual', $file_name).'" target="_blank" rel="noopener noreferrer">
            <i class="mdi mdi-file-document-outline text-primary"></i> '.$file_name.'
        </a>';
        return $res;
    }

    public function manageEditor($id){
        $result = '';
        if (Auth::User()->roleAdmin()) {
            $result .= '<a class="action-icon" href="javascript:void(0);" onclick="deleteConfirmation(\''.$id.'\')" \
                title="ลบ"><i class="mdi mdi-delete"></i></a>
            ';
        }
        return $result;
    }

    // ---------------------------------------------------------------

    public function store(Request $request){

        $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/assets/files/manual/';
        $pdfFile = $request->file('pdf_file');

        if ($pdfFile) {
            $filename = $pdfFile->getClientOriginalName();
            $pdfPath = $pdfFile->move(($destinationPath), $filename);

            $pdf = new Manual;
            $pdf->manual_name = $request->file_name;
            $pdf->manual_file = $filename;
            $pdf->save();
        }
        
        alert()->success('เพิ่มข้อมูลเรียบร้อย');
        return redirect('manual');
    }

    public function destroy($id){
        if ($id != "") {
            Manual::where('manual_id', $id)->delete();
            return response()->json(['success' => true, 'message' => 'ลบข้อมูลเรียบร้อย']);
        }
    }

}
