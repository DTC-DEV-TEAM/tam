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
}
