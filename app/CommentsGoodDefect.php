<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentsGoodDefect extends Model
{
    protected $table = 'comments_good_defect_tbl';
    protected $fillable = [
        'arf_number',
        'digits_code',
        'asset_code',
        'comments',
        'users',
        'created_at',
    ];
}
