<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemSourcingComments extends Model
{
    protected $table = 'item_sourcing_comments';

    protected $fillable = [
       'item_header_id',	
       'user_id',
       'comments',
       'created_at',
       'updated_at',
    ];

    public function scopeComments($query,$id){
        return $query->leftjoin('cms_users', 'item_sourcing_comments.user_id', '=', 'cms_users.id')
        ->select(
            'item_sourcing_comments.*',
            'cms_users.name'
          )
          ->where('item_sourcing_comments.item_header_id', $id)
          ->get();
    }

}
