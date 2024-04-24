<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssetsInventoryHeaderForApproval extends Model
{
    protected $table = 'assets_inventory_header_for_approval';
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

    public function scopeHeaderDetail($query, $id){
        return $query->leftjoin('assets_header_images', 'assets_inventory_header_for_approval.id', '=', 'assets_header_images.header_id')
                    ->leftjoin('cms_users', 'assets_inventory_header_for_approval.created_by', '=', 'cms_users.id')
                    ->leftjoin('cms_users as approver', 'assets_inventory_header_for_approval.updated_by', '=', 'approver.id')
                    ->leftjoin('warehouse_location_model', 'assets_inventory_header_for_approval.location', '=', 'warehouse_location_model.id')
                    ->select(
                        'assets_inventory_header_for_approval.*',
                        'assets_inventory_header_for_approval.id as header_id',
                        'cms_users.*',
                        'warehouse_location_model.location as warehouse_location',
                        'approver.name as approver',
                        'assets_inventory_header_for_approval.created_at as date_created'
                        )
                    ->where('assets_inventory_header_for_approval.id', $id)
                    ->first();
    }

    public function assetsinventorybody()
    {
        return $this->hasMany('App\AssetsInventoryBodyForApproval');
    }
    
}
