<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemSourcingEditVersions extends Model
{
    protected $table = 'item_sourcing_edit_versions';

    protected $fillable = [
       'header_id',	
       'body_id',
       'old_description',
       'new_description',
       'old_brand_value',
       'new_brand_value',
       'old_model_value',
       'new_model_value',
       'old_size_value',
       'new_size_value',
       'old_ac_value',
       'new_ac_value',
       'old_material',
       'new_material',
       'old_thickness',
       'new_thickness',
       'old_lamination',
       'new_lamination',
       'old_add_ons',
       'new_add_ons',
       'old_installation',
       'new_installation',
       'old_dismantling',
       'new_dismantling',
       'old_qty_value',
       'new_qty_value',
       'version',
       'updated_by',
       'created_at',
    ];

}
