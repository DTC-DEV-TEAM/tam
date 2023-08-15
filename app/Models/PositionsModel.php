<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PositionsModel extends Model
{
    protected $table = 'positions';
    protected $fillable = [
        'position_description',	
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
     ];
}
