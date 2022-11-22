<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Locations extends Model
{
    protected $table = 'locations';
    protected $fillable = [
        'channels_id',
        'store_name',
        'coa_id' ,
        'store_status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'deleted_at'
    ] ;
}
