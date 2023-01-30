<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnTransferAssetsHeader extends Model
{
    protected $table = 'return_transfer_assets_header';

    protected $fillable = [
        'status', 
        'requestor_name', 
        'reference_no', 
        'request_type_id',
        'request_type',
        'requested_by',
        'requested_date',
        'location_to_pick',
        'store_branch',
        'transfer_to'
    ];
}
