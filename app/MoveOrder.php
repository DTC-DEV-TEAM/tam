<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class MoveOrder extends Model
{

    private const It         = 1;
    private const Fa         = 5;
    private const Nt         = 9;
    private const Closed     = 13;
    private const ForClosing = 19;

    protected $table = 'mo_body_request';
    protected $fillable = [
        'status_id', 
        'mo_reference_number', 
        'inventory_id', 
        'request_created_by', 
        'request_type_id_mo', 
        'digits_code', 
        'asset_code', 
        'item_description', 
        'category_id', 
        'serial_no',   
        'quantity',  
        'unit_cost',    
        'item_id'
    ];
    public function scopeArrayMO($query, $closed, $for_closing){
       return $query->leftjoin('header_request', 'mo_body_request.header_request_id', '=', 'header_request.id')
                    ->leftjoin('body_request', 'mo_body_request.body_request_id', '=', 'body_request.id')
                    ->leftjoin('request_type', 'header_request.purpose', '=', 'request_type.id')
                    ->leftjoin('condition_type', 'header_request.conditions', '=', 'condition_type.id')
                    ->leftjoin('cms_users as employees', 'header_request.employee_name', '=', 'employees.id')
                    ->leftjoin('companies', 'header_request.company_name', '=', 'companies.id')
                    ->leftjoin('departments', 'header_request.department', '=', 'departments.id')
                    ->leftjoin('locations', 'header_request.store_branch', '=', 'locations.id')
                    ->leftjoin('cms_users as requested', 'mo_body_request.request_created_by','=', 'requested.id')
                    ->leftjoin('cms_users as approved', 'header_request.approved_by','=', 'approved.id')
                    ->leftjoin('cms_users as recommended', 'header_request.recommended_by','=', 'recommended.id')
                    ->leftjoin('cms_users as processed', 'header_request.purchased2_by','=', 'processed.id')
                    ->leftjoin('cms_users as picked', 'header_request.picked_by','=', 'picked.id')
                    ->leftjoin('cms_users as received', 'header_request.received_by','=', 'received.id')
                    ->leftjoin('cms_users as closed', 'header_request.closed_by','=', 'closed.id')
                    ->leftjoin('statuses', 'mo_body_request.status_id', '=', 'statuses.id')
                    ->select(
                            'header_request.*',
                            'body_request.*',
                            'mo_body_request.*',
                            'header_request.id as requestid',
                            'header_request.created_at as created',
                            'request_type.*',
                            'condition_type.*',
                            'requested.name as requestedby',
                            'employees.bill_to as employee_name',
                            'employees.company_name_id as company_name',
                            'departments.department_name as department',
                            'locations.store_name as store_branch',
                            'approved.name as approvedby',
                            'recommended.name as recommendedby',
                            'picked.name as pickedby',
                            'received.name as receivedby',
                            'processed.name as processedby',
                            'closed.name as closedby',
                            'header_request.created_at as created_at',
                            'statuses.status_description as status_description',
                            'body_request.item_description as body_description',
                            DB::raw('IF(header_request.created_at IS NULL, mo_body_request.created_at, header_request.created_at) as received_at')
                            )
                    ->whereIn('mo_body_request.status_id', [$closed, $for_closing])
                    ->whereNull('mo_body_request.return_flag')
                    ->get();
    }

    public function scopeMoReturn($query, $closed, $for_closing, $user){
       return $query->leftjoin('header_request', 'mo_body_request.header_request_id', '=', 'header_request.id')
                    ->leftjoin('request_type', 'header_request.purpose', '=', 'request_type.id')
                    ->leftjoin('requests', 'header_request.request_type_id', '=', 'requests.id')
                    ->leftjoin('condition_type', 'header_request.conditions', '=', 'condition_type.id')
                    ->leftjoin('cms_users as employees', 'header_request.employee_name', '=', 'employees.id')
                    ->leftjoin('companies', 'header_request.company_name', '=', 'companies.id')
                    ->leftjoin('departments', 'header_request.department', '=', 'departments.id')
                    ->leftjoin('positions', 'header_request.position', '=', 'positions.id')
                    ->leftjoin('locations', 'header_request.store_branch', '=', 'locations.id')
                    ->leftjoin('cms_users as requested', 'header_request.created_by','=', 'requested.id')
                    ->leftjoin('cms_users as approved', 'header_request.approved_by','=', 'approved.id')
                    ->leftjoin('cms_users as recommended', 'header_request.recommended_by','=', 'recommended.id')
                    ->leftjoin('cms_users as tagged', 'header_request.purchased2_by','=', 'tagged.id')
                
                    ->select(
                            'header_request.*',
                            'mo_body_request.*',
                            'mo_body_request.id as mo_id',
                            'mo_body_request.location_id as warehouse_location_id',
                            'header_request.id as requestid',
                            'header_request.created_at as created',
                            'request_type.*',
                            'condition_type.*',
                            'requested.name as requestedby',
                            'employees.bill_to as employee_name',
                            'employees.company_name_id as company_name',
                            'departments.department_name as department',
                            'mo_body_request.category_id as asset_type',
                            'locations.store_name as store_branch',
                            'locations.id as location_id',
                            'approved.name as approvedby',
                            'recommended.name as recommendedby',
                            'tagged.name as taggedby',
                            'header_request.created_at as created_at',
                            DB::raw('IF(header_request.request_type_id IS NULL, mo_body_request.request_type_id_mo, header_request.request_type_id) as request_type_id')
                            )
                    ->where('mo_body_request.request_created_by', $user)
                    ->whereIn('mo_body_request.status_id', [$closed, $for_closing])
                    ->whereIn('header_request.request_type_id', [self::It, self::Fa])
                    ->whereNull('mo_body_request.return_flag')
                    ->get();
    }

    public function scopeMoSearchItem($query, $search, $user){
        return $query->leftjoin('header_request', 'mo_body_request.header_request_id', '=', 'header_request.id')
                     ->leftjoin('request_type', 'header_request.purpose', '=', 'request_type.id')
                     ->leftjoin('requests', 'header_request.request_type_id', '=', 'requests.id')
                     ->leftjoin('condition_type', 'header_request.conditions', '=', 'condition_type.id')
                     ->leftjoin('cms_users as employees', 'header_request.employee_name', '=', 'employees.id')
                     ->leftjoin('companies', 'header_request.company_name', '=', 'companies.id')
                     ->leftjoin('departments', 'header_request.department', '=', 'departments.id')
                     ->leftjoin('positions', 'header_request.position', '=', 'positions.id')
                     ->leftjoin('locations', 'header_request.store_branch', '=', 'locations.id')
                     ->leftjoin('cms_users as requested', 'header_request.created_by','=', 'requested.id')
                     ->leftjoin('cms_users as approved', 'header_request.approved_by','=', 'approved.id')
                     ->leftjoin('cms_users as recommended', 'header_request.recommended_by','=', 'recommended.id')
                     ->leftjoin('cms_users as tagged', 'header_request.purchased2_by','=', 'tagged.id')
                 
                     ->select(
                             'header_request.*',
                             'mo_body_request.*',
                             'mo_body_request.id as mo_id',
                             'mo_body_request.location_id as warehouse_location_id',
                             'header_request.id as requestid',
                             'header_request.created_at as created',
                             'request_type.*',
                             'condition_type.*',
                             'requested.name as requestedby',
                             'employees.bill_to as employee_name',
                             'employees.company_name_id as company_name',
                             'departments.department_name as department',
                             'mo_body_request.category_id as asset_type',
                             'locations.store_name as store_branch',
                             'locations.id as location_id',
                             'approved.name as approvedby',
                             'recommended.name as recommendedby',
                             'tagged.name as taggedby',
                             'header_request.created_at as created_at',
                             DB::raw('IF(header_request.request_type_id IS NULL, mo_body_request.request_type_id_mo, header_request.request_type_id) as request_type_id')
                             )
                     ->where('mo_body_request.asset_code','LIKE','%'.$search.'%')->orWhere('mo_body_request.item_description','LIKE','%'.$search.'%')->where('mo_body_request.request_created_by', $user)
                     ->whereIn('header_request.request_type_id', [self::It, self::Fa])
                     ->whereIn('mo_body_request.status_id', [self::Closed, self::ForClosing])
                     ->whereNull('mo_body_request.return_flag')
                     ->get();
     }

    public function scopeMoReturnNonTrade($query, $closed, $for_closing, $user){
        return $query->leftjoin('header_request', 'mo_body_request.header_request_id', '=', 'header_request.id')
                     ->leftjoin('request_type', 'header_request.purpose', '=', 'request_type.id')
                     ->leftjoin('requests', 'header_request.request_type_id', '=', 'requests.id')
                     ->leftjoin('condition_type', 'header_request.conditions', '=', 'condition_type.id')
                     ->leftjoin('cms_users as employees', 'header_request.employee_name', '=', 'employees.id')
                     ->leftjoin('companies', 'header_request.company_name', '=', 'companies.id')
                     ->leftjoin('departments', 'header_request.department', '=', 'departments.id')
                     ->leftjoin('positions', 'header_request.position', '=', 'positions.id')
                     ->leftjoin('locations', 'header_request.store_branch', '=', 'locations.id')
                     ->leftjoin('cms_users as requested', 'header_request.created_by','=', 'requested.id')
                     ->leftjoin('cms_users as approved', 'header_request.approved_by','=', 'approved.id')
                     ->leftjoin('cms_users as recommended', 'header_request.recommended_by','=', 'recommended.id')
                     ->leftjoin('cms_users as tagged', 'header_request.purchased2_by','=', 'tagged.id')
                 
                     ->select(
                             'header_request.*',
                             'mo_body_request.*',
                             'mo_body_request.id as mo_id',
                             'mo_body_request.location_id as warehouse_location_id',
                             'header_request.id as requestid',
                             'header_request.created_at as created',
                             'request_type.*',
                             'condition_type.*',
                             'requested.name as requestedby',
                             'employees.bill_to as employee_name',
                             'employees.company_name_id as company_name',
                             'departments.department_name as department',
                             'mo_body_request.category_id as asset_type',
                             'locations.store_name as store_branch',
                             'locations.id as location_id',
                             'approved.name as approvedby',
                             'recommended.name as recommendedby',
                             'tagged.name as taggedby',
                             'header_request.created_at as created_at',
                             DB::raw('IF(header_request.request_type_id IS NULL, mo_body_request.request_type_id_mo, header_request.request_type_id) as request_type_id')
                             )
                     ->where('mo_body_request.request_created_by', $user)
                     ->whereIn('mo_body_request.status_id', [$closed, $for_closing])
                     ->whereIn('header_request.request_type_id', [self::Nt])
                     ->whereNull('mo_body_request.return_flag')
                     ->get();
     }
}
