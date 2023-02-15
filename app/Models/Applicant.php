<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    protected $table = 'applicant_table';
    protected $fillable = [
        'status', 
        'erf_number',
        'first_name',
        'last_name',
        'full_name',
        'screen_date',
        'created_by',
        'created_at	',
        'updated_by',
        'updated_at	',
        'app_id'
    ];
}
