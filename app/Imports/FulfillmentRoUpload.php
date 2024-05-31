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
class FulfillmentRoUpload implements ToCollection, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return Users|null
     */
    public function collection(Collection $rows)
    {
        foreach ($rows->toArray() as $key => $row){
            $arf_number = trim($row['arf_number']);
            $item_code = trim($row['item_code']);
            $dr_qty = trim($row['dr_qty']);
            $dr_number = trim($row['dr_number']);
            $header   = DB::table('header_request')->where(['reference_number' => $arf_number])->first();
            $checkQty = DB::table('body_request')->where(['header_request_id'=>$header->id,'digits_code'=>$item_code])->value('unserved_ro_qty');
            
            $checkRowDbDigitsCode       = DB::table('items_smallwares')->select("tasteless_code AS codes")->get()->toArray();
            $checkRowDbColumnDigitsCode = array_column($checkRowDbDigitsCode, 'codes');
          
            if(!in_array($item_code, $checkRowDbColumnDigitsCode)){
                return CRUDBooster::redirect(CRUDBooster::adminpath('smallwares'),"Item Code not exist in Item Master: ".($key+2),"danger");
            }

            $checkRowDbRefNo       = DB::table('header_request')->select("reference_number AS ref_num")->get()->toArray();
            $checkRowDbColumnRefNo = array_column($checkRowDbRefNo, 'ref_num');
          
            if(!in_array($arf_number, $checkRowDbColumnRefNo)){
                return CRUDBooster::redirect(CRUDBooster::adminpath('smallwares'),"Arf Invalid! please check arf reference no: ".($key+2),"danger");
            }
            
            if($dr_qty > $checkQty){
                return CRUDBooster::redirect(CRUDBooster::adminpath('smallwares'),"ReOrder Fullfill Qty Exceed! at line: ".($key+2),"danger");
            }

            if(empty($dr_qty)){
                return CRUDBooster::redirect(CRUDBooster::adminpath('smallwares'),"Dr Qty Required! at line: ".($key+2),"danger");
            }
         
            HeaderRequest::where('id',$header->id)
			->update([
					'mo_so_num'      => $dr_number,
                    'purchased2_by'	 => CRUDBooster::myId(),
                    'purchased2_at'  => date('Y-m-d H:i:s')
			]);	
            BodyRequest::where(['header_request_id'=>$header->id,'digits_code'=>$item_code])
            ->update(
                        [
                        'mo_so_num'       => DB::raw('CONCAT_WS(",",mo_so_num, "'. $dr_number.'")'),
                        'serve_qty'       => DB::raw("IF(serve_qty IS NULL, '".(int)$dr_qty."', serve_qty + '".(int)$dr_qty."')"), 
                        'unserved_ro_qty' => DB::raw("unserved_ro_qty - '".(int)$dr_qty."'"), 
                        'unserved_qty'    => DB::raw("unserved_qty - '".(int)$dr_qty."'"),
                        'dr_qty'          => DB::raw("IF(dr_qty IS NULL, '".(int)$dr_qty."', dr_qty + '".(int)$dr_qty."')")            
                        ]
                    );
            FulfillmentHistories::Create(
                [
                    'arf_number'  => $arf_number, 
                    'digits_code' => $item_code, 
                    'dr_qty'      => $dr_qty,
                    'dr_no'       => $dr_number,
                    'dr_type'     => trim($row['dr_type']),
                    'updated_by'  => CRUDBooster::myId(),
                    'updated_at'  => date('Y-m-d H:i:s')
                ]
            );  

            //Close if all unserved quantity is fulfill
            $checkBodyQty = DB::table('body_request')->where(['header_request_id'=>$header->id])->whereNull('deleted_at')->get();
            $resData = [];
            foreach($checkBodyQty as $item){
                if($item->quantity != $item->serve_qty){                
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