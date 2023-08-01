<?php

namespace App\Imports;

use App\Models\AssetsSuppliesInventory;
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
class SuppliesInventoryImport implements ToCollection, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return Users|null
     */
    public function collection(Collection $rows)
    {
        $updateToZero = DB::table('assets_supplies_inventory')->update(['quantity' => 0]);
        foreach ($rows->toArray() as $key => $row){
            $item 	                    = DB::table('assets')->where(['digits_code' => $row['digits_code']])->first();
            $checkRowDbDigitsCode       = DB::table('assets')->select("digits_code AS codes")->get()->toArray();
     
            $checkRowDbColumnDigitsCode = array_column($checkRowDbDigitsCode, 'codes');
          
            if(!in_array($row['digits_code'], $checkRowDbColumnDigitsCode)){
                return CRUDBooster::redirect(CRUDBooster::mainpath(),"Digits Code not exist in Item Master: ".($key+2),"danger");
            }

            $save = AssetsSuppliesInventory::updateOrcreate([
                'digits_code'      => $row['digits_code'] 
            ],
            [
                'digits_code'      => $row['digits_code'],
                'description'      => $item->item_description,
                'quantity'         => $row['quantity']
            ]);

            if ($save->wasRecentlyCreated) {
                $save->created_by = CRUDBooster::myId();
                $save->created_at = date('Y-m-d H:i:s');
                $save->updated_at = NULL;
            }else{
                $save->updated_by = CRUDBooster::myId();
                $save->updated_at = date('Y-m-d H:i:s');
            }
            $save->save();
        }
    }
}