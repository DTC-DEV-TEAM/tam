<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HeaderRequest extends Model
{
    //
    protected $table = 'header_request';
    protected $fillable = [
        'status_id',	
        'reference_number',
        'employee_name',
        'company_name',
        'position',
        'department',
        'store_branch',
        'purpose',
        'conditions',
        'quantity_total',
        'cost_total',
        'total',
        'requestor_comments',
        'created_by',
        'created_at',
        'request_type_id',
        'privilege_id',
        'application',
        'application_others',
        'to_reco',
        'if_from_erf'
     ];

     public function scopeHeader($query,$id){
        return $query->leftjoin('request_type', 'header_request.purpose', '=', 'request_type.id')
				->leftjoin('condition_type', 'header_request.conditions', '=', 'condition_type.id')
				->leftjoin('cms_users as employees', 'header_request.employee_name', '=', 'employees.id')
				->leftjoin('companies', 'header_request.company_name', '=', 'companies.id')
				->leftjoin('departments', 'header_request.department', '=', 'departments.id')
				->leftjoin('locations', 'employees.location_id', '=', 'locations.id')
				->leftjoin('cms_users as requested', 'header_request.created_by','=', 'requested.id')
				->leftjoin('cms_users as approved', 'header_request.approved_by','=', 'approved.id')
				->leftjoin('cms_users as recommended', 'header_request.recommended_by','=', 'recommended.id')
				->leftjoin('cms_users as processed', 'header_request.purchased2_by','=', 'processed.id')
				->leftjoin('cms_users as picked', 'header_request.picked_by','=', 'picked.id')
				->leftjoin('cms_users as received', 'header_request.received_by','=', 'received.id')
				->leftjoin('cms_users as closed', 'header_request.closed_by','=', 'closed.id')
				->select(
						'header_request.*',
						'header_request.id as requestid',
						'header_request.created_at as created',
						'request_type.*',
						'condition_type.*',
						'requested.name as requestedby',
						'employees.bill_to as employee_name',
						'header_request.employee_name as header_emp_name',
						'header_request.created_by as header_created_by',
						'departments.department_name as department',
						'locations.store_name as store_branch',
						'approved.name as approvedby',
						'recommended.name as recommendedby',
						'picked.name as pickedby',
						'received.name as receivedby',
						'processed.name as processedby',
						'closed.name as closedby',
						'header_request.created_at as created_at'
						)
				->where('header_request.id', $id)->first();
     }
}
