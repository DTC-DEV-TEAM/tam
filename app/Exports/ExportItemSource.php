<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\ItemBodySourcing;

class ExportItemSource implements FromCollection, WithHeadings
{
    //use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection(){
        return ItemBodySourcing::leftjoin('item_sourcing_header', 'item_sourcing_body.header_request_id', '=', 'item_sourcing_header.id')
        ->leftjoin('cms_users as employees', 'item_sourcing_header.employee_name', '=', 'employees.id')
        ->leftjoin('companies', 'item_sourcing_header.company_name', '=', 'companies.id')
        ->leftjoin('departments', 'item_sourcing_header.department', '=', 'departments.id')
        ->leftjoin('locations', 'employees.location_id', '=', 'locations.id')
        ->leftjoin('cms_users as requested', 'item_sourcing_header.created_by','=', 'requested.id')
        ->leftjoin('cms_users as approved', 'item_sourcing_header.approved_by','=', 'approved.id')
        ->leftjoin('cms_users as processed', 'item_sourcing_header.processed_by','=', 'processed.id')
        ->leftjoin('cms_users as closed', 'item_sourcing_header.closed_by','=', 'closed.id')
        ->leftjoin('statuses', 'item_sourcing_header.status_id', '=', 'statuses.id')
        ->leftjoin('category', 'item_sourcing_body.category_id', '=', 'category.id')
        ->leftjoin('tam_subcategories', 'item_sourcing_body.sub_category_id', '=', 'tam_subcategories.id')
        //->leftjoin('class', 'item_sourcing_body.class_id', '=', 'class.id')
        //->leftjoin('new_sub_class', 'item_sourcing_body.sub_class_id', '=', 'new_sub_class.id')
        ->select(
          'statuses.status_description',
          'item_sourcing_header.reference_number',
          'employees.bill_to',
          'departments.department_name',
          'item_sourcing_header.position',
          'item_sourcing_header.company_name',
          'category.category_description',
          //'new_sub_category.sub_category_description',
          'tam_subcategories.subcategory_description',
          //'new_sub_class.sub_class_description',
          'item_sourcing_body.item_description',
          'item_sourcing_body.brand',
          'item_sourcing_body.model',
          'item_sourcing_body.size',
          'item_sourcing_body.actual_color',
          'item_sourcing_body.quantity',
          'item_sourcing_body.budget',
          'item_sourcing_header.date_needed',
          'item_sourcing_header.created_at as created_date',
          'approved.name',
          'item_sourcing_header.approved_at',
          'processed.name',
          'item_sourcing_header.processed_at',
          'closed.name',
          'item_sourcing_header.closed_at',
        )->get();
    }

    public function headings(): array
    {
        return [
                "Status",
                "Reference Number", 
                "Requestor",
                "Department",
                "Position", 
                "Company",
                "Category",
                "Sub Category",
                "Item Description",
                "Brand",
                "Model",
                "Size",
                "Actual Color",
                "Quantity",
                "Budget Range",
                "Date Needed",
                "Requested Date",
                "Approved By",
                "Approved At",
                "Processed By",
                "Processed At",
                "Closed By",
                "Closed At"
               ];
    }
}
