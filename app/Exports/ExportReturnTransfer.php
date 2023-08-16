<?php

namespace App\Exports;
use App\BodyRequest;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Models\ReturnTransferAssets;
use DB;

class ExportReturnTransfer implements FromQuery, WithHeadings, WithMapping
{
    //use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $filter_column;

    public function __construct($fields){
        $this->from      = $fields['from'];
        $this->to        = $fields['to'];
        $this->category  = $fields['category'];
     
    }
    public function headings(): array
    {
        return [
                "Status",
                "Reference No.",
                "Description",
                "Request Quantity", 
                "Transaction Type",
                "Request Type",
                "Requested By",
                "Department",
                "Store Branch",
                "MO Reference",
                "MO Asset Code",
                "MO Item Code",
                "MO Item Description",
                "Requested Date",
                "Transacted By",
                "Transacted Date",
               ];
    }
    public function map($data): array {
        return [
            $data->status_description,
            $data->reference_number,
            $data->description,
            $data->quantity,
            $data->category_id,
            $data->category_id,
            $data->requestedby,
            $data->department,
            $data->store_branch,
            $data->mo_reference_number,
            $data->asset_code,
            $data->digits_code,
            $data->item_description,
            $data->requested_date,
            $data->transacted_by,
            $data->transacted_date,
        ];
    }

    public function query(){
        $data = BodyRequest::query()
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
					'approved.name as approvedby',
					'received.name as receivedby',
					'closed.name as closedby',
					'locations.store_name as store_branch',
                    'statuses.status_description as status_description',
                    'mo_body_request.quantity as quantity',
					);
                if($this->from && $this->to){
                    $data->whereBetween('return_transfer_assets_header.approved_at',[$this->from,$this->to]);
                }
                if($this->category){
                    $data->where('return_transfer_assets_header.request_type_id',$this->category);
                }

                return $data;
    }

  

    public function title(): string
    {
        return 'Return/Transfer';
    }
}
