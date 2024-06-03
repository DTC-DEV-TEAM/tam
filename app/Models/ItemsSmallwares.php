<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemsSmallwares extends Model
{
    protected $table = 'items_smallwares';

    public function scopeSearchItems($query, $search){
        return $query->where('items_smallwares.tasteless_code','LIKE','%'.$search.'%')->where('items_smallwares.skustatus_id','!=',2)
                     ->orWhere('items_smallwares.full_item_description','LIKE','%'.$search.'%')->where('items_smallwares.skustatus_id','!=',2)
                     ->leftJoin('assets_smallwares_inventory', 'items_smallwares.tasteless_code','=', 'assets_smallwares_inventory.digits_code')
                     ->leftJoin('timfs_categories', 'items_smallwares.category_id','=', 'timfs_categories.id')
                     ->leftJoin('timfs_subcategories', 'items_smallwares.subcategory_id','=', 'timfs_subcategories.id')
                     ->select('items_smallwares.*',
                            'items_smallwares.id as assetID',
                            'assets_smallwares_inventory.quantity as wh_qty',
                            'timfs_categories.category_description',
                            'timfs_subcategories.subcategory_description'
                     );
    }

    public function scopeGetItems($query){
        return $query->where('items_smallwares.skustatus_id','!=',2)
                     ->leftJoin('assets_smallwares_inventory', 'items_smallwares.tasteless_code','=', 'assets_smallwares_inventory.digits_code')
                     ->leftJoin('timfs_categories', 'items_smallwares.category_id','=', 'timfs_categories.id')
                     ->leftJoin('timfs_subcategories', 'items_smallwares.subcategory_id','=', 'timfs_subcategories.id')
                     ->select('items_smallwares.*',
                            'items_smallwares.id as assetID',
                            'assets_smallwares_inventory.quantity as wh_qty',
                            'timfs_categories.category_description',
                            'timfs_subcategories.subcategory_description'
                     );
    }
}
