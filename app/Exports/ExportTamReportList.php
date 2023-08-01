<?php

namespace App\Exports;

use App\BodyRequest;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use CRUDBooster;
use DB;
class ExportTamReportList implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public function headings():array{
        return [
            'Status',
            'Reference Number', 
            'Requested By',
            'Department',
            'Store Branch',
            'Digits Code',
            'Item Description',
            'Category', 
            'Sub Category',
            'Approved By',
            'Approved At',
            'Recommended By',
            'Recommended At',
            'It Comments',
            'Requested Date'
        ];
    } 

    public function map($conso): array {
        return [
            $conso->status_description,
            $conso->reference_number,
            $conso->bill_to,
            $conso->department_name,
            $conso->store_branch,
            $conso->digits_code,
            $conso->item_description,
            $conso->category_id,
            $conso->sub_category_id,
            $conso->approved_by,
            $conso->approved_at,
            $conso->recommeded_by,
            $conso->recommeded_at,
            $conso->it_comments,
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
        ->leftjoin('statuses', 'header_request.status_id', '=', 'statuses.id')
        ->select(
          'statuses.status_description',
          'header_request.reference_number',
          'requested.bill_to',
          'departments.department_name',
          DB::raw('IF(header_request.store_branch IS NULL, departments.department_name, locations.store_name) as store_branch'),
          'body_request.digits_code',
          'body_request.item_description',
          'body_request.category_id',
          'body_request.sub_category_id',
          'approved.name as approved_by',
          'header_request.approved_at as approved_at',
          'recommended.name as recommended_by',
          'header_request.recommended_at as recommended_at',
          'header_request.it_comments',
          'header_request.created_at as requested_at'
        );
 
        return $data;
    }
}

?>
