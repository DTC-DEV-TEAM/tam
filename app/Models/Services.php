<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Services extends Model
{
    //
    protected $table = 'assets_services';

    protected $fillable = [
        'asset_code',	
        'service_description',
        'vendor_name',
        'vendor',
        'amount',
        'is_export',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at'
     ];
 

    public function scopeGetServicesToExport($query){
        return $query->leftjoin('warehouse_location_model', 'assets_services.location', '=', 'warehouse_location_model.id')
        ->leftjoin('cms_users as creator', 'assets_services.created_by','=', 'creator.id')
        ->leftjoin('cms_users as updater', 'assets_services.updated_by','=', 'updater.id')
        ->whereNull('is_export')
        ->select(
          'assets_services.id as s_id',
          'assets_services.asset_code',
          'assets_services.service_description',  
          'assets_services.vendor',  
          'warehouse_location_model.location',  
          'assets_services.amount',  
          'creator.name as createdBy',
          'assets_services.created_at',
          'updater.name as updatedBy',
          'assets_services.updated_at'
        );
    }

    public function scopeGetServicesToExportAll($query){
        return $query->leftjoin('warehouse_location_model', 'assets_services.location', '=', 'warehouse_location_model.id')
        ->leftjoin('cms_users as creator', 'assets_services.created_by','=', 'creator.id')
        ->leftjoin('cms_users as updater', 'assets_services.updated_by','=', 'updater.id')
        ->select(
          'assets_services.id as s_id',
          'assets_services.asset_code',
          'assets_services.service_description',  
          'assets_services.vendor',  
          'warehouse_location_model.location',  
          'assets_services.amount',  
          'creator.name as createdBy',
          'assets_services.created_at',
          'updater.name as updatedBy',
          'assets_services.updated_at'
        );
    }
}
