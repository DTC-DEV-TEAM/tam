<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemSourcingOptions extends Model
{
    protected $table = 'item_sourcing_options';

    protected $fillable = [
       'header_id',	
       'options',
       'vendor_name',
       'price',
       'created_by',
       'created_at',
       'updated_by',
       'updated_at',
    ];

    public function scopeOptions($query,$id){
        return $query->leftjoin('item_sourcing_option_file', 'item_sourcing_options.id', '=', 'item_sourcing_option_file.opt_body_id')
        ->select(
            'item_sourcing_options.*',
            'item_sourcing_options.id as optId',
            'item_sourcing_option_file.file_name',
            'item_sourcing_option_file.id as file_id',
          )
          ->where('item_sourcing_options.header_id', $id)
          ->get();
    }

}
