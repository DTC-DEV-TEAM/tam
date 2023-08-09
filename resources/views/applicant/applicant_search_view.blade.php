@extends('crudbooster::admin_template')
    @push('head')
    
        <style type="text/css">   
            table.dataTable td.dataTables_empty {
                text-align: center;    
            }
            .active{
                font-weight: bold;
                font-size: 13px;
                color:#3c8dbc
            }
            .modal-content  {
                -webkit-border-radius: 3px !important;
                -moz-border-radius: 3px !important;
                border-radius: 3px !important; 
            }
        </style>
    @endpush
@section('content')

<div class='panel panel-default'>
    <div class='panel-heading'>
    Asset Movement History
    </div>

        <div class='panel-body'>
        <div class="row" style="margin:5px">   

        <a href="javascript:showApplicantItemExport()" id="export-sales-item-report" class="btn btn-primary btn-sm">
            <i class="fa fa-download"></i> Export Applicant
        </a>
            <table class='table table-hover table-striped table-bordered' id="table_dashboard">
                <thead>
                    <tr class="active">
                        <th width="auto">Action</th>
                        <th width="auto">Status</th>
                        <th width="auto">Erf Number</th>
                        <th width="auto">First Name</th>
                        <th width="auto">Last Name</th>
                        <th width="auto">Screen Date</th>
                        <th width="auto">Created By</th>
                        <th width="auto">Created At</th>
                        <th width="auto">Updated By</th>
                        <th width="auto">Updated At</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($result as $val)
                <tr>
                    <td style="text-align:center">   
                    @if(!in_array($val->status, [5,8,36]))     
                    <a class='btn btn-xs' href='{{CRUDBooster::mainpath("edit-applicant/".$val->apid)."?return_url=".urlencode(Request::fullUrl())}}'><i class='fa fa-pencil'></i></a>                                         
                    @else
                    <a class='btn  btn-xs' href='{{CRUDBooster::mainpath("detail-applicant/".$val->apid)."?return_url=".urlencode(Request::fullUrl())}}'><i class='fa fa-eye'></i></a>   
                    @endif
                    </td>  
                    @if($val['status'] == 8)
                    <td style="text-align:center">
                     <label class="label label-danger" style="align:center; font-size:10px">{{$val['status_description']}}</label>
                    </td>
                    @elseif($val['status'] == 5)
                    <td style="text-align:center">
                     <label class="label label-danger" style="align:center; font-size:10px">{{$val['status_description']}}</label>
                    </td>
                    @elseif($val['status'] == 34)
                    <td style="text-align:center">
                     <label class="label label-info" style="align:center; font-size:10px">{{$val['status_description']}}</label>
                    </td>
                    @elseif($val['status'] == 35)
                    <td style="text-align:center">
                     <label class="label label-info" style="align:center; font-size:10px">{{$val['status_description']}}</label>
                    </td>
                    @elseif($val['status'] == 42)
                    <td style="text-align:center">
                     <label class="label label-info" style="align:center; font-size:10px">{{$val['status_description']}}</label>
                    </td>
                    @else
                    <td style="text-align:center">
                     <label class="label label-success" style="align:center; font-size:10px">{{$val['status_description']}}</label>
                    </td>
                    @endif 
                    <td>{{$val->erf_number}}</td>
                    <td>{{$val->first_name}}</td>  
                    <td>{{$val->last_name}}</td>
                    <td>{{$val->screen_date}}</td>     
                    <td>{{$val->created_name}}</td>   
                    <td>{{$val->created_at}}</td>                                                                
                    <td>{{$val->updated_by}}</td>  
                    <td>{{$val->updated_at}}</td>  
                </tr>
                    
                @endforeach
                </tbody>
            </table>
        </div>   
        </div>

<div class='modal fade' tabindex='-1' role='dialog' id='modal-applicant-export'>
    <div class='modal-dialog'>
        <div class='modal-content'>
            <div class='modal-header'>
                <button class='close' aria-label='Close' type='button' data-dismiss='modal'>
                    <span aria-hidden='true'>×</span></button>
                <h4 class='modal-title'><i class='fa fa-download'></i> Export Applicant</h4>
            </div>

            <form method='post' target='_blank' action="{{ CRUDBooster::mainpath('export-applicant') }}">
            <input type='hidden' name='_token' value="{{ csrf_token() }}">
            {{ CRUDBooster::getUrlParameters() }}
            @if(!empty($filters))
                @foreach ($filters as $keyfilter => $valuefilter )
                    <input type="hidden" name="{{ $keyfilter }}" value="{{ $valuefilter }}">
                @endforeach

            @endif
            <div class='modal-body'>
                <div class='form-group'>
                    <label>File Name</label>
                    <input type='text' name='filename' class='form-control' required value='Export Applicant {{ CRUDBooster::getCurrentModule()->name }} - {{ date('Y-m-d H:i:s') }}'/>
                </div>
            </div>
            <div class='modal-footer' align='right'>
                <button class='btn btn-default' type='button' data-dismiss='modal'>Close</button>
                <button class='btn btn-primary btn-submit' type='submit'>Submit</button>
            </div>
        </form>
        </div>
    </div>
</div>


</div>

@endsection

@push('bottom')
    <script type="text/javascript">
       var table;
       $(document).ready(function() {
            $("#table_dashboard").DataTable({
                ordering:false,
                pageLength:25,
                language: {
                    searchPlaceholder: "Search"
                },
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"],
                    ],
            });
            $('#erf_number,#status').select2({})
            $(".date").datetimepicker({
                    viewMode: "days",
                    format: "YYYY-MM-DD",
                    dayViewHeaderFormat: "MMMM YYYY",
            });
        
    });

    function showApplicantItemExport() {
        $('#modal-applicant-export').modal('show');
    }
    </script>
@endpush