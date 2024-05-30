<?php

namespace App\Exports;

use App\Models\AssetsSmallwaresInventory;
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
        $data = AssetsSmallwaresInventory::query()->select(
          'assets_smallwares_inventory.digits_code',
          'assets_smallwares_inventory.description',
          'assets_smallwares_inventory.quantity'
        );
    
        return $data;
    }
}

?>
