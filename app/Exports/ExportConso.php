<?php

namespace App\Exports;

use App\BodyRequest;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use CRUDBooster;
use DB;
class ExportConso implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public function __construct($data){
        $this->from      = $data['from'];
        $this->to        = $data['to'];
        $this->category  = $data['category'];
    }

    public function headings():array{
        return [
            'Status',
            'Reference Number', 
            'Requested By',
            'Department',
            'Sub Department',
            'Store Branch',
            'Digits Code',
            'Item Description',
            'Category', 
            'Sub Category',
            'Po No',
            'Wh Qty',
            'Quantity',
            'Replenish Qty',
            'Re Order Qty',
            'Served Qty',
            'Unserved Qty',
            'Cancelled Qty',
            'Reason to Cancel',
            'Dr Number',
            'Requested Date'
        ];
    } 

    public function map($conso): array {
        return [
            $conso->status_description,
            $conso->reference_number,
            $conso->bill_to,
            $conso->department_name,
            $conso->sub_department_name,
            $conso->store_branch,
            $conso->digits_code,
            $conso->item_description,
            $conso->category_id,
            $conso->sub_category_id,
            $conso->po_no,
            $conso->wh_qty,
            $conso->quantity,
            $conso->replenish_qty,
            $conso->reorder_qty,
            $conso->serve_qty,
            $conso->unserved_qty,
            $conso->cancelled_qty,
            $conso->reason_to_cancel,
            $conso->mo_so_num,
            $conso->requested_at
        ];
    }

    public function query()
    {
        $data = BodyRequest::query()->leftjoin('header_request', 'body_request.header_request_id', '=', 'header_request.id')
        ->leftjoin('request_type', 'header_request.purpose', '=', 'request_type.id')
        ->leftjoin('companies', 'header_request.company_name', '=', 'companies.id')
        ->leftjoin('departments', 'header_request.department', '=', 'departments.id')
        ->leftjoin('positions', 'header_request.position', '=', 'positions.id')
        ->leftjoin('locations', 'header_request.store_branch', '=', 'locations.id')
        ->leftjoin('cms_users as requested', 'header_request.created_by','=', 'requested.id')
        ->leftjoin('cms_users as approved', 'header_request.approved_by','=', 'approved.id')
        ->leftjoin('cms_users as recommended', 'header_request.recommended_by','=', 'recommended.id')
        ->leftjoin('cms_users as tagged', 'header_request.purchased2_by','=', 'tagged.id')
        ->leftjoin('sub_department', 'requested.sub_department_id', '=', 'sub_department.id')
        ->leftjoin('statuses', 'header_request.status_id', '=', 'statuses.id')
        ->select(
          'statuses.status_description',
          'header_request.reference_number',
          'requested.bill_to',
          'departments.department_name',
          DB::raw('IF(header_request.store_branch IS NULL, departments.department_name, locations.store_name) as store_branch'),
          'sub_department.sub_department_name',
          'body_request.digits_code',
          'body_request.item_description',
          'body_request.category_id',
          'body_request.sub_category_id',
          'body_request.po_no',
          'body_request.wh_qty',
          'body_request.quantity',
          'body_request.replenish_qty',
          'body_request.reorder_qty',
          'body_request.serve_qty',
          'body_request.unserved_qty',
          'body_request.cancelled_qty',
          'body_request.reason_to_cancel',
          'body_request.mo_so_num',
          'header_request.created_at as requested_at'
        )
        //->where('header_request.request_type_id',7)
        ->whereNull('body_request.deleted_at')
        ->groupBy('body_request.id');
        //dd($this->from, $this->to);
        if($this->from && $this->to){
            $data->whereBetween('header_request.approved_at',[$this->from,$this->to]);
        }
        if($this->category){
            $data->where('header_request.request_type_id',$this->category);
        }
        return $data;
    }
}

?>
