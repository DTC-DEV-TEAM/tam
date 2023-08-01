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
    /**
     * @param array $row
     *
     * @return Users|null
     */
    public function collection(Collection $rows){

        foreach ($rows->toArray() as $key => $row){
            $header   = DB::table('header_request')->where(['reference_number' => $row['arf_number']])->first();
            
            $checkQty = DB::table('body_request')->where(['header_request_id'=>$header->id,'digits_code'=>$row['digits_code']])->value('reorder_qty');
            
            $checkRowDbDigitsCode       = DB::table('assets')->select("digits_code AS codes")->get()->toArray();
            $checkRowDbColumnDigitsCode = array_column($checkRowDbDigitsCode, 'codes');
          
            if(!in_array($row['digits_code'], $checkRowDbColumnDigitsCode)){
                return CRUDBooster::redirect(CRUDBooster::adminpath('move_order'),"Digits Code not exist in Item Master: ".($key+2),"danger");
            }

            $checkRowDbRefNo       = DB::table('header_request')->select("reference_number AS ref_num")->get()->toArray();
            $checkRowDbColumnRefNo = array_column($checkRowDbRefNo, 'ref_num');
          
            if(!in_array($row['arf_number'], $checkRowDbColumnRefNo)){
                return CRUDBooster::redirect(CRUDBooster::adminpath('move_order'),"Arf Invalid! please check arf reference no: ".($key+2),"danger");
            }
            
            if($row['po_qty'] > $checkQty){
                return CRUDBooster::redirect(CRUDBooster::adminpath('move_order'),"PO Fullfill Qty Exceed! at line: ".($key+2),"danger");
            }

            if(empty($row['po_qty'])){
                return CRUDBooster::redirect(CRUDBooster::adminpath('move_order'),"PO Qty Required! at line: ".($key+2),"danger");
            }
            
            BodyRequest::where(['header_request_id'=>$header->id,'digits_code'=>$row['digits_code']])
            ->update(
                        [
                        'po_qty' => $row['po_qty'],
                        'po_no'  => $row['po_number']         
                        ]
                    );

            FulfillmentHistories::Create(
                [
                    'arf_number'  => $row['arf_number'], 
                    'digits_code' => $row['digits_code'], 
                    'po_qty'      => $row['po_qty'],
                    'po_no'       => $row['po_number'] ,
                    'updated_by' => CRUDBooster::myId(),
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            );  
           
        }

    }
}