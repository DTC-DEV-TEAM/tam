<?php namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExportApplicantMultipleSheet implements WithMultipleSheets
{
    use Exportable;

    protected $filter_column;
    public function __construct($fields){
        $this->filter_column  = $fields['filter_column'];
 
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];
        $sheets[] = new ApplicantExport($this->filter_column);
        // $sheets[] = new ApplicantErf($this->filter_column);
        // $sheets[] = new ApplicantStatus($this->filter_column);
        return $sheets;
    }
}