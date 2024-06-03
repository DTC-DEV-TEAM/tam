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
        
    ];

    public function scopeSearchItems($query, $search){
       return $query->where('assets.digits_code','LIKE','%'.$search.'%')->whereNotIn('assets.status',['EOL-DIGITS','INACTIVE'])->whereNotNull('assets.from_dam')
			->orWhere('assets.item_description','LIKE','%'.$search.'%')->whereNotIn('assets.status',['EOL-DIGITS','INACTIVE'])->whereNotNull('assets.from_dam')
			->leftjoin('tam_categories', 'assets.tam_category_id','=', 'tam_categories.id')
			->leftjoin('tam_subcategories','assets.tam_sub_category_id','tam_subcategories.id')
			->leftjoin('category', 'assets.dam_category_id','=', 'category.id')
			->leftjoin('sub_category', 'assets.dam_class_id','=', 'sub_category.id')
			->select(	'assets.*',
						'assets.id as assetID',
						'tam_categories.category_description as tam_category_description',
						'tam_subcategories.subcategory_description as tam_sub_category_description',
						'category.category_description as dam_category_description',
						'sub_category.class_description as dam_sub_category_description'
                    );
    }

    public function scopeGetItems($query){
        return $query->whereNotIn('assets.status',['EOL-DIGITS','INACTIVE'])->whereNotNull('assets.from_dam')
             ->leftjoin('tam_categories', 'assets.tam_category_id','=', 'tam_categories.id')
             ->leftjoin('tam_subcategories','assets.tam_sub_category_id','tam_subcategories.id')
             ->leftjoin('category', 'assets.dam_category_id','=', 'category.id')
             ->leftjoin('sub_category', 'assets.dam_class_id','=', 'sub_category.id')
             ->select(	'assets.*',
                         'assets.id as assetID',
                         'tam_categories.category_description as tam_category_description',
                         'tam_subcategories.subcategory_description as tam_sub_category_description',
                         'category.category_description as dam_category_description',
                         'sub_category.class_description as dam_sub_category_description'
                     );
     }

     public function scopeGetItemsFa($query){
        return $query->whereNotIn('assets.status',['EOL-DIGITS','INACTIVE'])->whereNull('assets.from_dam')
             ->leftjoin('tam_categories', 'assets.tam_category_id','=', 'tam_categories.id')
             ->leftjoin('tam_subcategories','assets.tam_sub_category_id','tam_subcategories.id')
             ->leftjoin('category', 'assets.dam_category_id','=', 'category.id')
             ->leftjoin('sub_category', 'assets.dam_class_id','=', 'sub_category.id')
             ->select(	'assets.*',
                         'assets.id as assetID',
                         'tam_categories.category_description as tam_category_description',
                         'tam_subcategories.subcategory_description as tam_sub_category_description',
                         'category.category_description as dam_category_description',
                         'sub_category.class_description as dam_sub_category_description'
                     );
     }
}
