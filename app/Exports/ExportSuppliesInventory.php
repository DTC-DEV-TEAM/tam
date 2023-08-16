<?php

namespace App\Exports;

use App\Models\AssetsSuppliesInventory;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use CRUDBooster;
use DB;
class ExportSuppliesInventory implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public function headings():array{
        return [
   
            'Digits Code',
            'Item Description',
            'Quantity'
        ];
    } 

    public function map($conso): array {
        return [
            $conso->digits_code,
            $conso->description,
            number_format($conso->quantity, 0, '.', ','),
            // $conso->quantity
        ];
    }

    public function query()
    {
        $data = AssetsSuppliesInventory::query()->select(
          'assets_supplies_inventory.digits_code',
          'assets_supplies_inventory.description',
          'assets_supplies_inventory.quantity'
        );
    
        return $data;
    }
}

?>
