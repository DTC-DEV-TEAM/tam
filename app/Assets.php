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
        'category_id',
        'class_id',
        'created_by',
        'created_at',
        'sub_category_id',
        'sub_class_id',
    ] ;
}
