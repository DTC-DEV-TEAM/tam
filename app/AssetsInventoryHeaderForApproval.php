<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssetsInventoryHeaderForApproval extends Model
{
    protected $table = 'assets_inventory_header_for_approval';
    protected $fillable = [
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
