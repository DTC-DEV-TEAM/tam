<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemSourcingHeaderFile extends Model
{
    protected $table = 'item_sourcing_header_file';

    protected $fillable = [
       'header_id',	
       'file_name',
       'ext',
       'created_by',
       'created_at',
       'archived',
       'updated_at',
    ];

}
