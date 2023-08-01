<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FulfillmentHistories extends Model
{
    protected $table = 'fulfillment_histories';

    protected $fillable = [
        'arf_number', 
        'digits_code', 
        'dr_no', 
        'po_no', 
        'dr_qty',
        'dr_type',
        'po_qty',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at'
    ];
}
