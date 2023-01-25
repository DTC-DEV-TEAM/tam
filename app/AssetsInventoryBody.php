<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssetsInventoryBody extends Model
{
    protected $table = 'assets_inventory_body';
    protected $fillable = [
        'header_id', 
        'statuses_id', 
        'po_no',
        'warranty_coverage',
        'digits_code',
        'item_code',
        'item_description',
        'item_condition',
        'value',
        'item_type',
        'quantity',
        'serial_no',
        'item_photo',
        'asset_code',
        'deployed_to',
        'barcode',
        'created_by',
        'deployed_to_id',
        'location',
        'request_type_id_inventory',
        'item_category',
        'item_id'
    ];

    public function assetsinventoryheader()
    {
        return $this->belongsTo('App\AssetsInventoryHeader');
    }
}
