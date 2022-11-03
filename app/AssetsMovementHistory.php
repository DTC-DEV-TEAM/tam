<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetsMovementHistory extends Model
{
    protected $table = 'assets_movement_histories';
    protected $fillable = [
        'header_id',
        'transaction_type',
        'reference_no',
        'body_id',
        'date_update',
        'history_updated_by',
        'description',
        'deployed_to',
        'location',
        'remarks',
        'archived'
    ];
}
