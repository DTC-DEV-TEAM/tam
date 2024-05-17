<?php

namespace App\Exports;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use CRUDBooster;
use DB;
class ExportServices implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;
    protected $services;
    public function __construct($services)
    {
        $this->services = $services;
    }
    public function headings():array{
        return [
            'Asset Code',
            'Description', 
            'Vendor',
            'Location',
            'Amount',
            'Created By',
            'Created At',
            'Update By',
            'Update At'
        ];
    } 

    public function map($map): array {
        return [
            $map->asset_code,
            $map->description,
            $map->vendor,
            $map->location,
            $map->amount,
            $map->createdBy,
            $map->created_at,
            $map->updatedBy,
            $map->updated_at
        ];
    }

    public function collection()
    {
        return $this->services;
    }
}

?>
