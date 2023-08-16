<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\AssetsInventoryBody;

class ExportHeaderInventory implements FromCollection, WithHeadings
{
    //use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return AssetsInventoryBody::leftjoin('statuses', 'assets_inventory_body.statuses_id','=','statuses.id')
        ->leftjoin('assets_inventory_header_for_approval', 'assets_inventory_body.header_id','=','assets_inventory_header_for_approval.id')
        ->leftjoin('cms_users', 'assets_inventory_body.created_by', '=', 'cms_users.id')
        ->select(
          'statuses.status_description',
          'assets_inventory_header_for_approval.po_no',
          'assets_inventory_header_for_approval.invoice_date',
          'assets_inventory_header_for_approval.invoice_no',
          'assets_inventory_header_for_approval.rr_date',
          'assets_inventory_header_for_approval.location',
          'assets_inventory_body.digits_code',
          'assets_inventory_body.asset_code',
          'assets_inventory_body.serial_no',
          'assets_inventory_body.location as body_location',
          'assets_inventory_body.item_condition',
          'assets_inventory_body.item_description',
          'assets_inventory_body.value',
          'assets_inventory_body.quantity',
          'assets_inventory_body.warranty_coverage',
          'cms_users.name',
          'assets_inventory_header_for_approval.created_at as header_date_created',
        ) 
        ->whereNotNull('assets_inventory_body.header_id')
        ->get();
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
                "Asset Code",
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
