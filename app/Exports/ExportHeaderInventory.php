<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\AssetsInventoryBodyForApproval;

class ExportHeaderInventory implements FromCollection, WithHeadings
{
    //use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return AssetsInventoryBodyForApproval::leftjoin('statuses', 'assets_inventory_body_for_approval.statuses_id','=','statuses.id')
        ->leftjoin('assets_inventory_header_for_approval', 'assets_inventory_body_for_approval.header_id','=','assets_inventory_header_for_approval.id')
        ->leftjoin('cms_users', 'assets_inventory_body_for_approval.created_by', '=', 'cms_users.id')
        ->select(
          'statuses.status_description',
          'assets_inventory_header_for_approval.po_no',
          'assets_inventory_header_for_approval.invoice_date',
          'assets_inventory_header_for_approval.invoice_no',
          'assets_inventory_header_for_approval.rr_date',
          'assets_inventory_header_for_approval.location',
          'assets_inventory_body_for_approval.digits_code',
          'assets_inventory_body_for_approval.serial_no',
          'assets_inventory_body_for_approval.location as body_location',
          'assets_inventory_body_for_approval.item_condition',
          'assets_inventory_body_for_approval.item_description',
          'assets_inventory_body_for_approval.value',
          'assets_inventory_body_for_approval.quantity',
          'assets_inventory_body_for_approval.warranty_coverage',
          'cms_users.name',
          'assets_inventory_header_for_approval.created_at as header_date_created',
        ) 
        ->get()
        ->each(function ($model) {
            $model->setAttribute('assets_inventory_body_for_approval.location', null);
        });
    }

    public function headings(): array
    {
        return [
                "Status", 
                "PO No", 
                "Invoice Date",
                "Invoice No",
                "RR Date",
                "Location",
                "Digits Code",
                "Serial No",
                "Location",
                "Item Condition",
                "Item Description",
                "Value",
                "Quantity",
                "Warranty Coverage",
                "Created By",
                "Created Date",
               ];
    }
}
