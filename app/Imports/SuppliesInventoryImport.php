<?php

namespace App\Imports;

use App\Models\AssetsSmallwaresInventory;
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
        $updateToZero = DB::table('assets_smallwares_inventory')->update(['quantity' => 0]);
        foreach ($rows->toArray() as $key => $row){
            $item 	                    = DB::table('items_smallwares')->where(['tasteless_code' => $row['item_code']])->first();
            $checkRowDbDigitsCode       = DB::table('items_smallwares')->select("tasteless_code AS codes")->get()->toArray();
     
            $checkRowDbColumnDigitsCode = array_column($checkRowDbDigitsCode, 'codes');
          
            if(!in_array($row['item_code'], $checkRowDbColumnDigitsCode)){
                return CRUDBooster::redirect(CRUDBooster::mainpath(),"Item Code not exist in Item Master: ".($key+2),"danger");
            }

            $save = AssetsSmallwaresInventory::updateOrcreate([
                'digits_code'      => $row['item_code'] 
            ],
            [
                'digits_code'      => $row['item_code'],
                'description'      => $item->full_item_description,
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