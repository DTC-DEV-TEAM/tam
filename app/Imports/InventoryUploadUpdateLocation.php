<?php

namespace App\Imports;

use App\Assets;
use App\AssetsInventoryBody;
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
use App\MoveOrder;
class InventoryUploadUpdateLocation implements ToCollection, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return Users|null
     */
    public function collection(Collection $rows){
        foreach ($rows->toArray() as $row){
            $users = DB::table('cms_users')->where(['email' => trim($row['email'])])->first();
            $invInfo = DB::table('assets_inventory_body')->where(['asset_code' => trim($row['asset_code'])])->first();
            if(!$users){
                return CRUDBooster::redirect(CRUDBooster::adminpath('assets_inventory_body'),"Users not exist in TAM!: ".($key+2),"danger");
            }
            if(!$invInfo){
                return CRUDBooster::redirect(CRUDBooster::adminpath('assets_inventory_body'),"Asset code not exist in Asset Lists!: ".($key+2),"danger");
            }
          
            AssetsInventoryBody::where('asset_code', $row['asset_code'])
                ->update([ 
                    'deployed_to'    => $users->name,
                    'deployed_to_id' => $users->id,
                    'updated_at'     => date('Y-m-d H:i:s')
                ]);
            MoveOrder::where(['inventory_id' => $invInfo->id, 'asset_code' => $invInfo->asset_code])
                ->update([ 
                    'request_created_by' => $users->id,
                    'updated_at'         => date('Y-m-d H:i:s')
                ]);
        }
    }
}