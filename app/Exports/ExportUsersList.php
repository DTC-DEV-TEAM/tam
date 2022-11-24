<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Users;

class ExportUsersList implements FromCollection, WithHeadings
{
    //use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Users::leftjoin('cms_privileges', 'cms_users.id_cms_privileges','=','cms_privileges.id')
        ->leftjoin('departments', 'cms_users.department_id','=','departments.id')
        ->leftjoin('sub_department', 'cms_users.sub_department_id','=','sub_department.id')
        ->leftjoin('locations', 'cms_users.location_id', '=', 'locations.id')
        ->leftjoin('cms_users as approver', 'cms_users.approver_id', '=', 'approver.id')
        ->select(
          'cms_users.email',
          'cms_privileges.name',
          'cms_users.first_name',
          'cms_users.last_name',
          'departments.department_name',
          'sub_department.sub_department_name',
          'cms_users.position_id',
          'approver.bill_to',
          'locations.store_name',
        ) 
        ->get()
        // ->each(function ($model) {
        //     $model->setAttribute('assets_inventory_body_for_approval.location', null);
        // })
        ;
    }

    public function headings(): array
    {
        return [
                "Email", 
                "Privilege",
                "First Name", 
                "Last Name",
                "Department",
                "Sub Department",
                "Position",
                "Approver",
                "Location"
               ];
    }
}
