<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use App\AssetsInventoryBody;
use DB;

class Assetlists implements FromCollection, WithHeadings, WithTitle
{
    //use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return AssetsInventoryBody::leftjoin('statuses', 'assets_inventory_body.statuses_id','=','statuses.id')
        ->leftjoin('assets_inventory_header', 'assets_inventory_body.header_id','=','assets_inventory_header.id')
        ->leftjoin('cms_users', 'assets_inventory_body.created_by', '=', 'cms_users.id')
        ->leftjoin('warehouse_location_model', 'assets_inventory_body.location', '=', 'warehouse_location_model.id')
        ->leftjoin('assets', 'assets_inventory_body.item_id', '=', 'assets.id')
        ->leftjoin('tam_categories', 'assets.tam_category_id', '=', 'tam_categories.id')
        ->leftjoin('tam_subcategories', 'assets.tam_sub_category_id', '=', 'tam_subcategories.id')
        ->leftjoin('category', 'assets.dam_category_id','=', 'category.id')
		->leftjoin('sub_category', 'assets.dam_class_id','=', 'sub_category.id')
        ->select(
          'assets_inventory_body.asset_code',
          'assets_inventory_body.digits_code',
          'assets_inventory_body.serial_no',
          'statuses.status_description',
          'assets_inventory_body.deployed_to',
          'assets_inventory_header.rr_date',
          'warehouse_location_model.loc_description',
          'assets_inventory_body.item_condition',
          'assets_inventory_body.item_description',
          'assets_inventory_body.value',
          'assets_inventory_body.quantity',
          DB::raw('IF(tam_categories.category_description IS NOT NULL, tam_categories.category_description, category.category_description) AS category_description'),
          DB::raw('IF(tam_subcategories.subcategory_description IS NOT NULL, tam_subcategories.subcategory_description, sub_category.class_description) AS subcategory_description'),
          'assets_inventory_body.warranty_coverage',
          'cms_users.name',
          'assets_inventory_body.created_at as body_created',
        ) 
        ->whereNotNull('assets_inventory_body.received')
        ->get()
        ->each(function ($model) {
            $model->setAttribute('assets_inventory_body.location', null);
        });
    }

    public function headings(): array
    {
        return [
                "Asset Code",
                "Digits Code",
                "Serial No",
                "Status", 
                "Deployed To",
                "RR Date",
                "Location",
                "Item Condition",
                "Item Description",
                "Value",
                "Quantity",
                "Category",
                "Sub Category",
                "Warranty Coverage",
                "Created By",
                "Created Date",
               ];
    }

    public function title(): string
    {
        return 'Asset Lists';
    }
}
