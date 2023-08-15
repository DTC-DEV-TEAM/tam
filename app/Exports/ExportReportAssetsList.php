<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use App\Models\GeneratedAssetsReports;

class ExportReportAssetsList implements FromCollection, WithHeadings, WithTitle
{
    //use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return GeneratedAssetsReports::select(
            'generated_assets_report.status',
            'generated_assets_report.reference_number',
            'generated_assets_report.digits_code',
            'generated_assets_report.description',
            'generated_assets_report.request_quantity',
            'generated_assets_report.transaction_type',
            'generated_assets_report.request_type',
            'generated_assets_report.requested_by',
            'generated_assets_report.department',
            'generated_assets_report.store_branch',
            'generated_assets_report.mo_reference',
            'generated_assets_report.mo_item_code',
            'generated_assets_report.mo_item_description',
            'generated_assets_report.mo_qty_serve_qty',
            'generated_assets_report.requested_date',
            'generated_assets_report.approved_by',
            'generated_assets_report.approved_at',
            // 'generated_assets_report.recommended_by',
            // 'generated_assets_report.recommended_at',
            // 'generated_assets_report.it_comments',
            'generated_assets_report.transacted_by',
            'generated_assets_report.transacted_date'
            )->get();
    }

    public function headings(): array
    {
        return [
            "Status",
            "Reference No",
            "Digits Code",
            "Description",
            "Request Quantity",
            "Request Type",
            "transaction Type",
            "Requested By",
            "Department",
            "Store Branch",
            "MO Reference",
            "MO Item Code",
            "MO Item Description",
            "MO Serve/Qty",
            "Requested Date",
            "Approved By",
            "Approved Date",
            // "Recommended By",
            // "Recommended At",
            // "IT Comments",
            "Transacted By",
            "Transacted Date",
               ];
    }

    public function title(): string
    {
        return 'Request and Return/Transfer Assets Report';
    }
}
