<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class AssetsInventoryBody extends Model
{
    private const forPo = 47;
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
        'sub_category_id',
        'received',
        'item_id'
    ];

    public function assetsinventoryheader()
    {
        return $this->belongsTo('App\AssetsInventoryHeader');
    }

    public function scopeDetailBody($query, $id){
        return $query->leftjoin('statuses', 'assets_inventory_body.statuses_id','=','statuses.id')
        ->leftjoin('assets_inventory_header_for_approval', 'assets_inventory_body.header_id', '=', 'assets_inventory_header_for_approval.id')
        ->leftjoin('assets', 'assets_inventory_body.item_id', '=', 'assets.id')
        ->leftjoin('cms_users as cms_users_updated_by', 'assets_inventory_body.updated_by', '=', 'cms_users_updated_by.id')
        ->leftjoin('warehouse_location_model', 'assets_inventory_body.location', '=', 'warehouse_location_model.id')
        ->select(
          'assets_inventory_body.*',
          'assets_inventory_body.id as for_approval_body_id',
          'statuses.*',
          'warehouse_location_model.location as warehouse_location',
          'assets_inventory_header_for_approval.location as location',
          'assets_inventory_body.location as body_location',
          'assets_inventory_body.updated_at as date_updated',
          'cms_users_updated_by.name as updated_by'
        )
        ->where('assets_inventory_body.header_id', $id)
        ->get();
    }

    public function scopeDetailForPo($query, $id){
        return $query->leftjoin('statuses', 'assets_inventory_body.statuses_id','=','statuses.id')
                    ->leftjoin('assets_inventory_header', 'assets_inventory_body.header_id','=','assets_inventory_header.id')
                    ->leftjoin('cms_users', 'assets_inventory_body.created_by', '=', 'cms_users.id')
                    ->leftjoin('warehouse_location_model', 'assets_inventory_body.location', '=', 'warehouse_location_model.id')
                    ->leftjoin('assets', 'assets_inventory_body.item_id', '=', 'assets.id')
                    ->leftjoin('tam_categories', 'assets.tam_category_id', '=', 'tam_categories.id')
                    ->leftjoin('tam_subcategories', 'assets.tam_sub_category_id', '=', 'tam_subcategories.id')
                    ->leftjoin('category', 'assets.dam_category_id','=', 'category.id')
                    ->leftjoin('sub_category', 'assets.dam_class_id','=', 'sub_category.id')
                    ->select(
                    'assets_inventory_body.asset_code',
                    'assets_inventory_body.digits_code',
                    'assets_inventory_body.serial_no',
                    'statuses.status_description',
                    'assets_inventory_body.deployed_to',
                    'assets_inventory_header.rr_date',
                    'warehouse_location_model.loc_description',
                    'assets_inventory_body.item_condition',
                    'assets_inventory_body.item_description',
                    'assets_inventory_body.value',
                    'assets_inventory_body.quantity',
                    'tam_categories.category_description AS tam_cat',
                    'tam_subcategories.subcategory_description AS tam_subcat',
                    'category.category_description AS dam_cat',
                    'sub_category.class_description AS dam_subcat',
                    'assets_inventory_body.warranty_coverage',
                    'cms_users.name',
                    'assets_inventory_body.created_at as body_created',
                    ) 
                    ->where('assets_inventory_body.header_id', $id)
                    ->where('assets_inventory_body.statuses_id', self::forPo)
                    ->get();
    }
}
