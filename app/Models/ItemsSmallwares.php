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
                     ->select('items_smallwares.*',
                            'items_smallwares.id as assetID',
                            'assets_smallwares_inventory.quantity as wh_qty'
                     );
        }
}
