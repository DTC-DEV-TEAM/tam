<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Assets;

class ExportItemMaster implements FromCollection, WithHeadings
{
    //use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Assets::leftjoin('category', 'assets.category_id','=','category.id')
        ->leftjoin('class', 'assets.class_id','=','class.id')
        ->select(
          'assets.digits_code',
          'assets.item_description',
          'category.category_description',
          'class.class_description',
          'assets.item_cost',
          'assets.status'
        ) 
        ->get()
        // ->each(function ($model) {
        //     $model->setAttribute('assets_inventory_body_for_approval.location', null);
        // })
        ;
    }

    public function headings(): array
    {
        return [
                "Digits Code", 
                "Item Description",
                "Category", 
                "Sub Category",
                "Item Cost",
                "Status"
               ];
    }
}
