<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use App\CommentsGoodDefect;

class AssetListsDefectComments implements FromCollection, WithHeadings, WithTitle
{
    //use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return CommentsGoodDefect::leftjoin('cms_users', 'comments_good_defect_tbl.users', '=', 'cms_users.id')
        ->select(
            'comments_good_defect_tbl.digits_code',
            'comments_good_defect_tbl.asset_code',
            'comments_good_defect_tbl.comments',
            'cms_users.name',
            'comments_good_defect_tbl.created_at as comment_created',
        
        ) 
        ->get();
    }

    public function headings(): array
    {
        return [
                "Digits Code",
                "Asset Code",
                "Comments",
                "Created By",
                "Created Date",
               ];
    }

    public function title(): string
    {
        return 'Asset Lists Good Defective Comments';
    }
}
