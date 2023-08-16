<?php namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExportMultipleByApprover implements WithMultipleSheets
{
    use Exportable;

    protected $filter_column;
    public function __construct($fields){
        $this->fields  = $fields;
 
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];
        $sheets[] = new ExportRequest($this->fields);
        $sheets[] = new ExportReturnTransfer($this->fields);
        return $sheets;
    }
}