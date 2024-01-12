<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetsNonTradeInventoryBody extends Model
{
    protected $table = 'assets_non_trade_inventory_body';
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
        'sub_category_id',
        'received',
        'item_id'
    ];
}
