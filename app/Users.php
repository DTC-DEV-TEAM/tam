<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    //
    protected $table = 'cms_users';
    protected $fillable = [
        'name',
        'first_name',
        'last_name' ,
        'username',
        'photo',
        'email', 
        'id_cms_privileges',
        'password',
        'department_id',
        'company_name_id',
        'location_id',
        'approver_id_manager',
        'approver_id_executive',
        'contact_person',
        'bill_to',
        'customer_location_name',
        'sub_department_id',
        'position_id',
        'approver_id',
        'store_id'
    ] ;
}
