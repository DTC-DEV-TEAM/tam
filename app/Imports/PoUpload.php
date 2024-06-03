<?php

namespace App\Imports;

use App\HeaderRequest;
use App\BodyRequest;
use App\Models\FulfillmentHistories;
use Illuminate\Support\Facades\Hash;
//use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use DB;
use CRUDBooster;
class PoUpload implements ToCollection, WithHeadingRow
{
    private $uploadType;

    public function __construct($type){
        $this->uploadType = $type;
    }

    public function collection(Collection $rows){

        foreach ($rows->toArray() as $key => $row){
            $arf_number = trim($row['arf_number']);
            $item_code = trim($row['item_code']);
            $po_qty   = trim($row['po_qty']);
            $po_number   = trim($row['po_number']);
            $header   = DB::table('header_request')->where(['reference_number' => $arf_number])->first();
            
            $checkQty = DB::table('body_request')->where(['header_request_id'=>$header->id,'digits_code'=>$item_code])->value('reorder_qty');
            
            if($this->uploadType === 'it_fa'){
                $checkRowDbDigitsCode       = DB::table('assets')->select("digits_code AS codes")->get()->toArray();
            }else{
                $checkRowDbDigitsCode       = DB::table('items_smallwares')->select("tasteless_code AS codes")->get()->toArray();
            }
          
            $checkRowDbColumnDigitsCode = array_column($checkRowDbDigitsCode, 'codes');
          
            if(!in_array($item_code, $checkRowDbColumnDigitsCode)){
                return CRUDBooster::redirect(CRUDBooster::adminpath('move_order'),"Item Code not exist in Item Master: ".($key+2),"danger");
            }

            $checkRowDbRefNo       = DB::table('header_request')->select("reference_number AS ref_num")->get()->toArray();
            $checkRowDbColumnRefNo = array_column($checkRowDbRefNo, 'ref_num');
          
            if(!in_array($arf_number, $checkRowDbColumnRefNo)){
                return CRUDBooster::redirect(CRUDBooster::adminpath('move_order'),"Arf Invalid! please check arf reference no: ".($key+2),"danger");
            }
            
            if($po_qty > $checkQty){
                return CRUDBooster::redirect(CRUDBooster::adminpath('move_order'),"PO Fullfill Qty Exceed! at line: ".($key+2),"danger");
            }

            if(empty($po_qty)){
                return CRUDBooster::redirect(CRUDBooster::adminpath('move_order'),"PO Qty Required! at line: ".($key+2),"danger");
            }
            
            BodyRequest::where(['header_request_id'=>$header->id,'digits_code'=>$item_code])
            ->update(
                        [
                        'po_qty' => $po_qty,
                        'po_no'  => $po_number         
                        ]
                    );

            FulfillmentHistories::Create(
                [
                    'arf_number'  => $arf_number, 
                    'digits_code' => $item_code, 
                    'po_qty'      => $po_qty,
                    'po_no'       => $po_number ,
                    'updated_by' => CRUDBooster::myId(),
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            );  
           
        }

    }
}