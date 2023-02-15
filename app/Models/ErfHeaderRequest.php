<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ErfHeaderRequest extends Model
{
    protected $table = 'erf_header_request';

    protected $fillable = [
        'status_id'    
    ];
}
