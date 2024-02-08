<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetsNonTradeInventoryReserved extends Model
{
    protected $table = 'assets_non_trade_inventory_reserved';
    protected $fillable = [
        'reference_number', 
        'body_id', 
        'digits_code', 
        'approved_qty', 
        'reserved', 
        'for_po',
        'requested_by',
        'updated_by',
        'updated_at	',
      
    ];
}
