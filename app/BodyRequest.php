<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BodyRequest extends Model
{
    //
    protected $table = 'body_request';

    //customers query
    public function scopeArrayone($query){
        return $query->leftjoin('header_request', 'body_request.header_request_id', '=', 'header_request.id')
        ->leftjoin('mo_body_request', 'body_request.id', '=', 'mo_body_request.body_request_id')
        ->leftjoin('request_type', 'header_request.purpose', '=', 'request_type.id')
        ->leftjoin('condition_type', 'header_request.conditions', '=', 'condition_type.id')
        ->leftjoin('employees', 'header_request.employee_name', '=', 'employees.id')
        ->leftjoin('companies', 'header_request.company_name', '=', 'companies.id')
        ->leftjoin('departments', 'header_request.department', '=', 'departments.id')
        ->leftjoin('positions', 'header_request.position', '=', 'positions.id')
        ->leftjoin('locations', 'header_request.store_branch', '=', 'locations.id')
        ->leftjoin('cms_users as requested', 'header_request.created_by','=', 'requested.id')
        ->leftjoin('cms_users as approved', 'header_request.approved_by','=', 'approved.id')
        ->leftjoin('cms_users as recommended', 'header_request.recommended_by','=', 'recommended.id')
        ->leftjoin('cms_users as tagged', 'header_request.purchased2_by','=', 'tagged.id')
        ->leftjoin('statuses', 'header_request.status_id', '=', 'statuses.id')
        ->leftjoin('statuses as body_statuses', 'body_request.line_status_id', '=', 'body_statuses.id')
        ->leftjoin('statuses as mo_statuses', 'mo_body_request.status_id', '=', 'mo_statuses.id')
        ->select('body_request.*',
                 'header_request.*',
                 'mo_body_request.*',
                 'header_request.id as requestid',
                 'header_request.created_at as created',
                 'request_type.*',
                 'condition_type.*',
                 'requested.name as requestedby',
                 'employees.bill_to as employee_name',
                 'companies.company_name as company_name',
                 'departments.department_name as department',
                 'locations.store_name as store_branch',
                 'approved.name as approvedby',
                 'recommended.name as recommendedby',
                 'tagged.name as taggedby',
                 'header_request.purchased2_at as transacted_date',
                 'header_request.created_at as created_at',
                 'statuses.status_description as status_description',
                 'body_request.item_description as body_description',
                 'body_request.digits_code as body_digits_code',
                 'mo_body_request.digits_code as mo_digits_code',
                 'mo_body_request.item_description as mo_item_description',
                 'body_request.quantity as body_quantity',
                 'mo_statuses.status_description as mo_statuses_description',
                 'body_statuses.status_description as body_statuses_description',
                 'body_request.category_id as body_category_id',
                 'mo_body_request.body_request_id as mo_body_request_id',
                 'body_request.mo_so_num as body_mo_so_num',
                 )
                 ->whereNull('body_request.deleted_at')
                 ->orderBy('header_request.created_at','DESC')
                 ->groupBy('body_request.id')
        ->get();
    }

    //Filtering Report
    public function scopeRequestfilter($query,$fields){
        $from = $fields['from'];
        $to = $fields['to'];
        $category = $fields['category'];
    
       $query->orderby('body_request.id','asc')
            ->leftjoin('header_request', 'body_request.header_request_id', '=', 'header_request.id')
            ->leftjoin('mo_body_request', 'body_request.id', '=', 'mo_body_request.body_request_id')
			->leftjoin('request_type', 'header_request.purpose', '=', 'request_type.id')
			->leftjoin('condition_type', 'header_request.conditions', '=', 'condition_type.id')
			->leftjoin('employees', 'header_request.employee_name', '=', 'employees.id')
			->leftjoin('companies', 'header_request.company_name', '=', 'companies.id')
			->leftjoin('departments', 'header_request.department', '=', 'departments.id')
			->leftjoin('positions', 'header_request.position', '=', 'positions.id')
			->leftjoin('locations', 'header_request.store_branch', '=', 'locations.id')
			->leftjoin('cms_users as requested', 'header_request.created_by','=', 'requested.id')
			->leftjoin('cms_users as approved', 'header_request.approved_by','=', 'approved.id')
			->leftjoin('cms_users as recommended', 'header_request.recommended_by','=', 'recommended.id')
			->leftjoin('cms_users as tagged', 'header_request.purchased2_by','=', 'tagged.id')
			->leftjoin('statuses', 'header_request.status_id', '=', 'statuses.id')
			->leftjoin('statuses as body_statuses', 'body_request.line_status_id', '=', 'body_statuses.id')
			->leftjoin('statuses as mo_statuses', 'mo_body_request.status_id', '=', 'mo_statuses.id')
			->select('body_request.*',
					'header_request.*',
					'mo_body_request.*',
					'header_request.id as requestid',
					'header_request.created_at as created',
					'request_type.*',
					'condition_type.*',
					'requested.name as requestedby',
					'employees.bill_to as employee_name',
					'companies.company_name as company_name',
					'departments.department_name as department',
					'locations.store_name as store_branch',
					'approved.name as approvedby',
					'recommended.name as recommendedby',
					'tagged.name as taggedby',
					'header_request.purchased2_at as transacted_date',
					'header_request.created_at as created_at',
					'statuses.status_description as status_description',
					'body_request.item_description as body_description',
					'body_request.digits_code as body_digits_code',
					'body_request.quantity as body_quantity',
					'mo_statuses.status_description as mo_statuses_description',
					'body_statuses.status_description as body_statuses_description',
					'body_request.category_id as body_category_id',
					'mo_body_request.body_request_id as mo_body_request_id',
                    'mo_body_request.digits_code as mo_digits_code',
                    'mo_body_request.item_description as mo_item_description',
					'body_request.mo_so_num as body_mo_so_num'
				    ) 
                    ->whereNull('body_request.deleted_at')
                    ->groupBy('body_request.id');
                if($from != '' && !is_null($from)){
                    $query->whereBetween('header_request.created_at',[$from,$to]);
                }
                if($category != '' && !is_null($category)){
                    $query->where('header_request.request_type_id', $category);
                }
			return $query->get();
    }
}
