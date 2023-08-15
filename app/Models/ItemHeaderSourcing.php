<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemHeaderSourcing extends Model
{
    protected $table = 'item_sourcing_header';

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
       'approved_by',
       'approved_at',
       'request_type_id',
       'processed_by',
       'processed_at',
       'application_others',
       'to_reco',
       'if_from_erf',
       'sampling',
       'mark_up',
       'dismantling',
       'artworklink'
    ];

    public static function boot(){
        parent::boot();
        static::creating(function($model){
            $model->created_by = CRUDBooster::myId();
        });
        
    }

    public function scopeHeader($query,$id){
        return $query->leftjoin('cms_users as employees', 'item_sourcing_header.employee_name', '=', 'employees.id')
        ->leftjoin('companies', 'item_sourcing_header.company_name', '=', 'companies.id')
        ->leftjoin('departments', 'item_sourcing_header.department', '=', 'departments.id')
        ->leftjoin('locations', 'employees.location_id', '=', 'locations.id')
        ->leftjoin('cms_users as requested', 'item_sourcing_header.created_by','=', 'requested.id')
        ->leftjoin('cms_users as approved', 'item_sourcing_header.approved_by','=', 'approved.id')
        ->leftjoin('cms_users as processed', 'item_sourcing_header.processed_by','=', 'processed.id')
        ->leftjoin('cms_users as closed', 'item_sourcing_header.closed_by','=', 'closed.id')
        ->leftjoin('statuses', 'item_sourcing_header.status_id', '=', 'statuses.id')
        ->select(
                'item_sourcing_header.*',
                'item_sourcing_header.id as requestid',
                'item_sourcing_header.created_at as created',
                'requested.name as requestedby',
                'employees.bill_to as employee_name',
                'item_sourcing_header.employee_name as header_emp_name',
                'item_sourcing_header.created_by as header_created_by',
                'departments.department_name as department',
                'locations.store_name as store_name',
                'approved.name as approvedby',
                'processed.name as processedby',
                'closed.name as closedby',
                'item_sourcing_header.created_at as created_at',
                'statuses.status_description as status_description'
                )
        ->where('item_sourcing_header.id', $id)->first();

    }

    public function scopeHeaderInfo($query,$id){
        return $query
        ->leftjoin('cms_users as employees', 'item_sourcing_header.employee_name', '=', 'employees.id')
        ->leftjoin('companies', 'item_sourcing_header.company_name', '=', 'companies.id')
        ->leftjoin('departments', 'item_sourcing_header.department', '=', 'departments.id')
        ->leftjoin('statuses', 'item_sourcing_header.status_id', '=', 'statuses.id')
        ->leftjoin('requests', 'item_sourcing_header.request_type_id', '=', 'requests.id')
        ->where('item_sourcing_header.id',$id)->first();
    }
}
