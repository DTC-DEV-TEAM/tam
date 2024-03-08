<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnTransferAssetsHeader extends Model
{
    protected $table = 'return_transfer_assets_header';

    protected $fillable = [
        'status', 
        'requestor_name', 
        'reference_no', 
        'request_type_id',
        'request_type',
        'purpose',
        'requested_by',
        'requested_date',
        'location_to_pick',
        'store_branch',
        'transfer_to',
        'schedule_by',
        'schedule_at',
        'transport_type',
        'hand_carry_name',
        'hand_carry_schedule'
    ];

    public function scopeDetail($query, $id){
        return $query->leftjoin('cms_users as employees', 'return_transfer_assets_header.requestor_name', '=', 'employees.id')
        ->leftjoin('requests', 'return_transfer_assets_header.request_type_id', '=', 'requests.id')
        ->leftjoin('departments', 'employees.department_id', '=', 'departments.id')
        ->leftjoin('cms_users as approved', 'return_transfer_assets_header.approved_by','=', 'approved.id')
        ->leftjoin('cms_users as received', 'return_transfer_assets_header.transacted_by','=', 'received.id')
        ->leftjoin('cms_users as closed', 'return_transfer_assets_header.close_by','=', 'closed.id')
        ->leftjoin('locations', 'return_transfer_assets_header.store_branch', '=', 'locations.id')
        ->select(
                'return_transfer_assets_header.*',
                'return_transfer_assets_header.id as requestid',
                'requests.request_name as request_name',
                'employees.name as employee_name',
                'employees.company_name_id as company',
                'employees.position_id as position',
                'departments.department_name as department_name',
                'approved.name as approvedby',
                'received.name as receivedby',
                'closed.name as closedby',
                'locations.store_name as store_branch'
                )
        ->where('return_transfer_assets_header.id', $id);
    }
}
