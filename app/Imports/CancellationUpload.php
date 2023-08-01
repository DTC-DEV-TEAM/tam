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
class CancellationUpload implements ToCollection, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return Users|null
     */
    public function collection(Collection $rows)
    {
        foreach ($rows->toArray() as $key => $row){
            
            $header   = DB::table('header_request')->where(['reference_number' => $row['arf_number']])->first();
            $unservedQty = DB::table('body_request')->where(['header_request_id'=>$header->id,'digits_code'=>$row['digits_code']])->value('unserved_qty');
 
            $checkRowDbDigitsCode       = DB::table('assets')->select("digits_code AS codes")->get()->toArray();
            $checkRowDbColumnDigitsCode = array_column($checkRowDbDigitsCode, 'codes');
          
            if(!in_array($row['digits_code'], $checkRowDbColumnDigitsCode)){
                return CRUDBooster::redirect(CRUDBooster::adminpath('for_purchasing'),"Digits Code not exist in Item Master: ".($key+2),"danger");
            }

            $checkRowDbRefNo       = DB::table('header_request')->select("reference_number AS ref_num")->get()->toArray();
            $checkRowDbColumnRefNo = array_column($checkRowDbRefNo, 'ref_num');
          
            if(!in_array($row['arf_number'], $checkRowDbColumnRefNo)){
                return CRUDBooster::redirect(CRUDBooster::adminpath('for_purchasing'),"Arf Invalid! please check arf reference no: ".($key+2),"danger");
            }
            
            BodyRequest::where(['header_request_id'=>$header->id,'digits_code'=>$row['digits_code']])
            ->update(
                        [
                        'unserved_qty'      => DB::raw("unserved_qty - '".(int)$unservedQty."'"),
                        'cancelled_qty'     => (int)$unservedQty,
                        'reason_to_cancel'  => $row['remarks']          
                        ]
                    );

    
            //Close if all unserved quantity is fulfill
            $checkBodyQty = DB::table('body_request')->where(['header_request_id'=>$header->id])->whereNull('deleted_at')->get();

            $resData = [];
            foreach($checkBodyQty as $item){
                if($item->quantity != $item->serve_qty + $item->cancelled_qty){                
                    $t = $item;
                    $resData[] = $t;                
                }
            }

            if(empty($resData)){
                HeaderRequest::where('id',$header->id)
				->update([
						'status_id'      => 19,
                        'purchased2_by'	 => CRUDBooster::myId(),
				        'purchased2_at'  => date('Y-m-d H:i:s')
				]);	
            }
       
        }
    }
    public function rules(): array
    {
        return [ 
            '*.dr_qty' => 'required|numeric',
        ];
    }
}