<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnTransferAssets extends Model
{
    protected $table = 'return_transfer_assets';

    public function scopeArraytwo($query){
       return $query->leftjoin('return_transfer_assets_header', 'return_transfer_assets.return_header_id', '=', 'return_transfer_assets_header.id')
			->leftjoin('cms_users as employees', 'return_transfer_assets_header.requestor_name', '=', 'employees.id')
            ->leftjoin('requests', 'return_transfer_assets_header.request_type_id', '=', 'requests.id')
			->leftjoin('departments', 'employees.department_id', '=', 'departments.id')
			->leftjoin('cms_users as approved', 'return_transfer_assets_header.approved_by','=', 'approved.id')
			->leftjoin('cms_users as received', 'return_transfer_assets_header.transacted_by','=', 'received.id')
			->leftjoin('cms_users as closed', 'return_transfer_assets_header.close_by','=', 'closed.id')
			->leftjoin('locations', 'return_transfer_assets_header.store_branch', '=', 'locations.id')
            ->leftjoin('statuses', 'return_transfer_assets.status', '=', 'statuses.id')
            ->leftjoin('mo_body_request', 'return_transfer_assets.mo_id', '=', 'mo_body_request.id')
			->select(
                    'return_transfer_assets.*',
					'return_transfer_assets_header.*',
					'return_transfer_assets_header.id as requestid',
					'return_transfer_assets.digits_code as r_digits_code',
					'requests.request_name as request_name',
					'employees.name as employee_name',
					'employees.company_name_id as company',
					'employees.position_id as position',
					'departments.department_name as department_name',
					'approved.name as approvedby',
					'received.name as receivedby',
					'closed.name as closedby',
					'locations.store_name as store_branch',
                    'statuses.status_description as status_description',
                    'mo_body_request.quantity as quantity',
					'return_transfer_assets_header.requested_date as requested_date'
					)->orderBy('return_transfer_assets_header.created_at','DESC')
                    ->get();

    }

	public function scopeReturnfilter($query, $fields){
		$from = $fields['from'];
        $to = $fields['to'];
        $category = $fields['category'];
        $query->orderBy('return_transfer_assets_header.created_at','DESC')
		    ->leftjoin('return_transfer_assets_header', 'return_transfer_assets.return_header_id', '=', 'return_transfer_assets_header.id')
	        ->leftjoin('cms_users as employees', 'return_transfer_assets_header.requestor_name', '=', 'employees.id')
            ->leftjoin('requests', 'return_transfer_assets_header.request_type_id', '=', 'requests.id')
			->leftjoin('departments', 'employees.department_id', '=', 'departments.id')
			->leftjoin('cms_users as approved', 'return_transfer_assets_header.approved_by','=', 'approved.id')
			->leftjoin('cms_users as received', 'return_transfer_assets_header.transacted_by','=', 'received.id')
			->leftjoin('cms_users as closed', 'return_transfer_assets_header.close_by','=', 'closed.id')
			->leftjoin('locations', 'return_transfer_assets_header.store_branch', '=', 'locations.id')
            ->leftjoin('statuses', 'return_transfer_assets.status', '=', 'statuses.id')
            ->leftjoin('mo_body_request', 'return_transfer_assets.mo_id', '=', 'mo_body_request.id')
			->select(
                    'return_transfer_assets.*',
					'return_transfer_assets_header.*',
					'return_transfer_assets_header.id as requestid',
					'requests.request_name as request_name',
					'employees.name as employee_name',
					'employees.company_name_id as company',
					'employees.position_id as position',
					'departments.department_name as department_name',
					'approved.name as approved_by_return',
					'received.name as receivedby',
					'closed.name as closedby',
					'locations.store_name as store_branch',
                    'statuses.status_description as status_description',
					'return_transfer_assets.digits_code as r_digits_code',
                    'mo_body_request.quantity as quantity',
			);
			if($from != '' && !is_null($from)){
				$query->whereBetween('return_transfer_assets_header.requested_date',[$from,$to]);
			}
			if($category != '' && !is_null($category)){
				$query->where('return_transfer_assets_header.request_type_id', $category);
			}
			return  $query->get();

    }
}
