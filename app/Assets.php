<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Assets extends Model
{
    //
    protected $primaryKey = 'id';
    protected $table = 'assets';
    protected $fillable = [
        'digits_code',
        'item_description',
        'item_cost',
        'fulfillment_type' ,
        'tam_category_id',
        'tam_sub_category_id',
        'dam_category_id',
        'dam_sub_category_id',
        'dam_class_id',
        'dam_sub_class_id',
        'status',
        'from_dam',
        'created_by',
        'created_at',
        
    ] ;
}
