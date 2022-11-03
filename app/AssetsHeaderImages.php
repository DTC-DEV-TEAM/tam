<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssetsHeaderImages extends Model
{
    protected $table = 'assets_header_images';
    protected $fillable = [
        'header_id', 
        'file_name', 
        'ext',
        'created_by'
    ];
}
