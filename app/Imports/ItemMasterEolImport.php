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
class ItemMasterEolImport implements ToCollection, SkipsEmptyRows, WithHeadingRow
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
            Assets::where(['digits_code'=>$row['digits_code']])
            ->update(
                        [
                        'category_id' => $row['category_id'],
                        'class_id'    => $row['class_id'],
                        'status'      => $row['status']         
                        ]
                    );
            DB::commit();
            } catch (\Exception $e) {
                \Log::debug($e);
                DB::rollback();
            }
   
        }
    }
}