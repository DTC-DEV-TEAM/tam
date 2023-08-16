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
                    'digits_code'      => $row['digits_code'],
                    'fulfillment_type' => $row['fulfillment_type'],
                ]);
            DB::commit();
            } catch (\Exception $e) {
                \Log::debug($e);
                DB::rollback();
            }
        }
    }
}