<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetsNonTradeInventoryHeader extends Model
{
    protected $table = 'assets_non_trade_inventory_header';
    protected $fillable = [
        'inv_reference_number',
        'po_no',
        'invoice_date',
        'rr_date',
        'invoice_no',
        'location',
        'header_approval_status',
        'created_by',
        'updated_by',
        'remarks',
    ];

    public function assetsinventorybody()
    {
        return $this->hasMany('App\AssetsInventoryBodyForApproval');
    }
    
}
