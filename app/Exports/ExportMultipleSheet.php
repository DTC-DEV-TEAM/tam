<?php namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExportMultipleSheet implements WithMultipleSheets
{
    use Exportable;

    
    public function __construct()
    {
      
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];
        $sheets[] = new Assetlists();
        $sheets[] = new AssetListsDefectComments();
        return $sheets;
    }
}