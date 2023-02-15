<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use App\Models\ErfHeaderRequest;

class ApplicantErf implements FromCollection, WithHeadings, WithTitle
{
    //use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */

    public function collection(){

        return ErfHeaderRequest::select(
                'erf_header_request.reference_number',
                )->where('erf_header_request.status_id', 30)->get();
    }

    public function headings(): array
    {
        return [
                "ERF List",
               ];
    }

    public function title(): string
    {
        return 'Verified Erf';
    }
}
