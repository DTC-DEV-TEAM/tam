<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssetsInventoryBodyForApproval extends Model
{
    protected $table = 'assets_inventory_body_for_approval';
    protected $fillable = [
        'header_id', 
        'statuses_id', 
        'po_no',
        'digits_code',
        'item_code',
        'item_description',
        'value',
        'item_type',
        'quantity',
        'serial_no',
        'item_photo',
        'assets_code',
        'barcode',
        'created_by'
    ];

    public function assetsinventoryheader()
    {
        return $this->belongsTo('App\AssetsInventoryHeaderForApproval');
    }
}
