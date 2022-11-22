<?php

namespace App\Imports;

use App\Models\Locations;
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
class LocationsImport implements ToCollection, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return Users|null
     */
    public function collection(Collection $rows)
    {
        foreach ($rows->toArray() as $row){
          
            Locations::updateOrcreate([
                'store_name' => $row['store_name'] 
            ],
            [
            'channels_id'       => $row['channels_id'],
            'store_name'        => $row['store_name'],
            'coa_id'            => NULL,
            'store_status'      => 'ACTIVE',
            'created_by'        => CRUDBooster::myId()
            ]);
        }
    }
}