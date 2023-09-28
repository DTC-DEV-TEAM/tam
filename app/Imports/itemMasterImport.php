<?php

namespace App\Imports;

use App\Assets;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithConditionalSheets;
use DB;
use CRUDBooster;
class ItemMasterImport implements ToCollection, SkipsEmptyRows, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return Users|null
     */
    public function collection(Collection $rows)
    {
        foreach ($rows->toArray() as $row){
            DB::beginTransaction();
			try {
                Assets::updateOrcreate([
                    'digits_code' => $row['digits_code'] 
                ],
                [
                    'digits_code'          => $row['digits_code'],
                    'item_description'     => $row['item_description'],
                    'tam_category_id'      => $row['tam_category_id'],
                    'tam_sub_category_id'  => $row['tam_sub_category_id'],
                    'item_cost'            => $row['item_cost'],
                    'status'               => $row['status'],
                    'created_by'           => CRUDBooster::myId()
                ]);
            DB::commit();
            } catch (\Exception $e) {
                \Log::debug($e);
                DB::rollback();
            }
        }
    }
}