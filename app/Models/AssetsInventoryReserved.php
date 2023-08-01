<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetsInventoryReserved extends Model
{
    protected $table = 'assets_inventory_reserved';
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
