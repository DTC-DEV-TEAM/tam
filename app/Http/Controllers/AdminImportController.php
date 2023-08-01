<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Maatwebsite\Excel\HeadingRowImport;
use App\Models\PositionsModel;
use Illuminate\Http\Request;
use App\Imports\FulfillmentUpload;
use App\Imports\FulfillmentRoUpload;
use App\Imports\PoUpload;
use App\Imports\CancellationUpload;
use App\Imports\StatusUpdateUpload;
use App\Imports\StatusUpdateMoveOrderUpload;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use DB;
use CRUDBooster;
use Excel;


class AdminImportController extends \crocodicstudio\crudbooster\controllers\CBController
{
    public function fulfillmentUpload(Request $request) {
        $path_excel = $request->file('import_file')->store('temp');
        $path = storage_path('app').'/'.$path_excel;
        $headings = array_filter((new HeadingRowImport)->toArray($path)[0][0]);

        if (count($headings) !== 5) {
			CRUDBooster::redirect(CRUDBooster::adminpath('for_purchasing'), 'Template column not match, please refer to downloaded template.', 'danger');
		} else {
            try {

                if($request->upload_type == "dr_rep"){
                    Excel::import(new FulfillmentUpload, $path);
                }else{
                    Excel::import(new FulfillmentRoUpload, $path);
                }
                CRUDBooster::redirect(CRUDBooster::adminpath('for_purchasing'), trans("Upload Successfully!"), 'success');
                
            } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                $failures = $e->failures();
                
                $error = [];
                foreach ($failures as $failure) {
                    $line = $failure->row();
                    foreach ($failure->errors() as $err) {
                        $error[] = $err . " on line: " . $line; 
                    }
                }
                
                $errors = collect($error)->unique()->toArray();
        
            }
            CRUDBooster::redirect(CRUDBooster::adminpath('for_purchasing'), $errors[0], 'danger');

		}
        
    }

    function downloadFulfillQtyTemplate() {
        $arrHeader = [
            "arf_number"         => "ARF NUMBER",
            "digits_code"        => "DIGITS CODE",
            "dr_qty"             => "DR QTY",
            "dr_number"          => "DR NUMBER",
            "dr_type"            => "DR TYPE",
        ];

        $arrData = [
            "erf_number"         => "ARF-0000001",
            "digits_code"        => "40000054",
            "dr_qty"             => "1",
            "dr_number"          => "DR#12345",
            "dr_type"            => "REP/RO", 
        ];

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->fromArray(array_values($arrHeader), null, 'A1');
        $spreadsheet->getActiveSheet()->fromArray($arrData, null, 'A2');
        $filename = "fulfill-orders".date('Y-m-d');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }

    //PO UPLOAD
    public function poUpload(Request $request) {
        $path_excel = $request->file('import_file')->store('temp');
        $path = storage_path('app').'/'.$path_excel;
        Excel::import(new PoUpload, $path);	
        CRUDBooster::redirect(CRUDBooster::adminpath('move_order'), trans("Upload Successfully!"), 'success');
    }
     //UPLOAD PO TEMPLATE
    function downloadPOTemplate() {
        $arrHeader = [
            "arf_number"         => "ARF NUMBER",
            "digits_code"        => "DIGITS CODE",
            "po_qty"             => "PO QTY",
            "po_no"              => "PO NUMBER",
        ];
        $arrData = [
            "erf_number"         => "ARF-0000001",
            "digits_code"        => "40000054",
            "po_qty"             => "1",
            "po_no"              => "PO#1234",
        ];
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->fromArray(array_values($arrHeader), null, 'A1');
        $spreadsheet->getActiveSheet()->fromArray($arrData, null, 'A2');
        $filename = "PO-UPLOAD-".date('Y-m-d');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }

    //CANCELLATION UPLOAD
    public function cancellationUpload(Request $request) {
        $path_excel = $request->file('import_file')->store('temp');
        $path = storage_path('app').'/'.$path_excel;
        Excel::import(new CancellationUpload, $path);	
        CRUDBooster::redirect(CRUDBooster::adminpath('for_purchasing'), trans("Upload Successfully!"), 'success');
    }

    //CANCELLATION PO TEMPLATE
    function downloadCancellationTemplate() {
        $arrHeader = [
            "arf_number"         => "ARF NUMBER",
            "digits_code"        => "DIGITS CODE",
            "remarks"            => "Remarks",
        ];
        $arrData = [
            "erf_number"         => "ARF-0000001",
            "digits_code"        => "40000054",
            "po_qty"             => "Reset"
        ];
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->fromArray(array_values($arrHeader), null, 'A1');
        $spreadsheet->getActiveSheet()->fromArray($arrData, null, 'A2');
        $filename = "Cancellation-".date('Y-m-d');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }


     //TEMPORARY STATUS UPDATE UPLOAD
     public function updateStatusUpload(Request $request) {
        $path_excel = $request->file('import_file')->store('temp');
        $path = storage_path('app').'/'.$path_excel;
        if($request->upload_type == "cancel"){
            Excel::import(new StatusUpdateUpload, $path);	
        }else{
            Excel::import(new StatusUpdateMoveOrderUpload, $path);
        }
        CRUDBooster::redirect(CRUDBooster::adminpath('header_request'), trans("Update Successfully!"), 'success');
    }

}

?>
