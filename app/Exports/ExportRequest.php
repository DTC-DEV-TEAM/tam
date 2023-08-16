<?php

namespace App\Exports;
use App\BodyRequest;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Models\Applicant;
use DB;

class ExportRequest implements FromQuery, WithHeadings, WithMapping
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
        ->leftjoin('header_request', 'body_request.header_request_id', '=', 'header_request.id')
        ->leftjoin('mo_body_request', 'body_request.id', '=', 'mo_body_request.body_request_id')
			->leftjoin('request_type', 'header_request.purpose', '=', 'request_type.id')
			->leftjoin('condition_type', 'header_request.conditions', '=', 'condition_type.id')
			->leftjoin('employees', 'header_request.employee_name', '=', 'employees.id')
			->leftjoin('companies', 'header_request.company_name', '=', 'companies.id')
			->leftjoin('departments', 'header_request.department', '=', 'departments.id')
			->leftjoin('positions', 'header_request.position', '=', 'positions.id')
			->leftjoin('locations', 'header_request.store_branch', '=', 'locations.id')
			->leftjoin('cms_users as requested', 'header_request.created_by','=', 'requested.id')
			->leftjoin('cms_users as approved', 'header_request.approved_by','=', 'approved.id')
			->leftjoin('cms_users as recommended', 'header_request.recommended_by','=', 'recommended.id')
			->leftjoin('cms_users as tagged', 'header_request.purchased2_by','=', 'tagged.id')
			->leftjoin('statuses', 'header_request.status_id', '=', 'statuses.id')
			->leftjoin('statuses as body_statuses', 'body_request.line_status_id', '=', 'body_statuses.id')
			->leftjoin('statuses as mo_statuses', 'mo_body_request.status_id', '=', 'mo_statuses.id')
			->select(
                    'mo_statuses.status_description',
					'header_request.reference_number',
                    'body_request.item_description as description',
					'mo_body_request.quantity',
                    'mo_body_request.category_id',
                    'mo_body_request.category_id',
					'requested.name as requestedby',
                    'departments.department_name as department',
					'locations.store_name as store_branch',
                    'mo_body_request.mo_reference_number',
                    'mo_body_request.asset_code',
                    'mo_body_request.digits_code',
                    'mo_body_request.item_description',
                    'header_request.created_at as requested_date',
                    'tagged.name as transacted_by',
                    'header_request.purchased2_at as transacted_date',
                     );
                if($this->from && $this->to){
                    $data->whereBetween('header_request.approved_at',[$this->from,$this->to]);
                }
                if($this->category){
                    $data->where('header_request.request_type_id',$this->category);
                }

                return $data;
    }

  

    public function title(): string
    {
        return 'Request';
    }
}
