<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetsSuppliesInventory extends Model
{
    protected $table = 'assets_supplies_inventory';
    protected $fillable = [
        'digits_code', 
        'description',
        'quantity',
        'status',
        'created_by',
        'created_at	',
        'updated_by',
        'updated_at	',
      
    ];
}
