<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetsNonTradeHeaderImages extends Model
{
    protected $table = 'assets_non_trade_header_images';
    protected $fillable = [
        'header_id', 
        'file_name', 
        'ext',
        'created_by'
    ];
}
