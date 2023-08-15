<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemSourcingOptionsFile extends Model
{
    protected $table = 'item_sourcing_option_file';

    protected $fillable = [
       'header_id',	
       'opt_body_id',	
       'file_name',
       'ext',
       'created_by',
       'created_at',
       'archived',
       'updated_at',
    ];

}
