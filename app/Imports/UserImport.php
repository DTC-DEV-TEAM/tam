<?php

namespace App\Imports;

use App\Users;
use Illuminate\Support\Facades\Hash;
//use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use DB;
class UserImport implements ToCollection, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return Users|null
     */
    public function collection(Collection $rows)
    {
        foreach ($rows->toArray() as $row){
            $priviledgeId       = DB::table('cms_privileges')->where(DB::raw('LOWER(name)'),strtolower($row['privilege']))->value('id');
            $approver           = DB::table('cms_users')->where(DB::raw('LOWER(name)'),strtolower($row['approver']))->value('id');
            
            $departments 	    = DB::table('departments')->where(['department_name' => strtoupper($row['department'])])->first();
            $sub_departments 	= DB::table('sub_department')->where(['sub_department_name' => strtoupper($row['sub_department'])])->first();
            
            $locations 	        = DB::table('locations')->where(['store_name' => strtoupper($row['location'])])->first();
            $full_name          = $row['first_name'].", ".$row['first_name'];
            $full_name_employee = $full_name.".EEE";
            if($priviledgeId == 3){
				$approver_id = $approver;
				$approver_id_manager = $approver;
				$approver_id_executive = NULL;
			}else if($priviledgeId == 11){
				$approver_id = NULL;
				$approver_id_manager = NULL;
				$approver_id_executive = NULL;
			}else if($priviledgeId == 12){
				$approver_id = NULL;
				$approver_id_manager = NULL;
				$approver_id_executive = NULL;
			}else{
				$approver_id = $approver;
				$approver_id_manager = NULL;
				$approver_id_executive = NULL;
			}
          
            Users::updateOrcreate([
                'email' => $row['email'] 
            ],
            [
            'name'                   => $row['first_name'] . " " . $row['last_name'],
            'first_name'             => $row['first_name'],
            'last_name'              => $row['last_name'],
            'user_name'              => $row['last_name'].''.substr($row['first_name'], 0, 1),
            'photo'                  => 'uploads/1/2019-05/businessman.png',
            'email'                  => $row['email'], 
            'id_cms_privileges'      => $priviledgeId,
            'password'               => bcrypt('qwerty'),
            'contact_person'         => $full_name,
            'bill_to'                => $full_name,
            'customer_location_name' => $full_name_employee,
            'department_id'          => $departments->id,
            'company_name_id'        => 'TASTELESS',
            'location_id'            => $locations->id,
            'sub_department_id'      => $sub_departments->id,
            'position_id'            => $row['position'],
            'approver_id'            => $approver_id,
            'approver_id_manager'    => $approver_id_manager,
            'approver_id_executive'  => $approver_id_executive,
            ]);
        }
    }
}