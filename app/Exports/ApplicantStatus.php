<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use App\Statuses;

class ApplicantStatus implements FromCollection, WithHeadings, WithTitle
{
    //use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */

    public function collection(){

        return Statuses::select(
            'statuses.status_description'
          )
          ->whereIn('id', [42,34,35,36,8,5])
          ->get();
    }

    public function headings(): array
    {
        return [
                "Status List",
               ];
    }

    public function title(): string
    {
        return 'Valid Status List';
    }
}
