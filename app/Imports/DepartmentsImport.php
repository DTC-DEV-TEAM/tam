<?php

namespace App\Imports;

use App\Models\Departments;
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
class DepartmentsImport implements ToCollection, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return Users|null
     */
    public function collection(Collection $rows)
    {
        foreach ($rows->toArray() as $row){
          
            Departments::updateOrcreate([
                'department_name' => $row['department_name'] 
            ],
            [
            'department_name'   => $row['department_name'],
            'coa_id'            => NULL,
            'status'            => 'ACTIVE',
            'created_by'        => CRUDBooster::myId()
            ]);
        }
    }
}