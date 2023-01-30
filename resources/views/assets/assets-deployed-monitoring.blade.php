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
       
        </style>
    @endpush
@section('content')

<div class='panel panel-default'>
    <div class='panel-heading'>
    Request Assets Reports
    </div>

        <div class='panel-body' style="overflow-x: scroll">
            <div class="row">
           
        <div class="row" style="margin:5px">   
            <button type="button" id="btn-export" class="btn btn-primary btn-sm btn-export" style="margin-bottom:10px"><i class="fa fa-download"></i>
                <span>Export Data</span>
            </button>
            
            <table class='table table-hover table-striped table-bordered' id="table_dashboard">
            
                <thead>
                    <tr class="active">
                        <!-- <th width="auto">Action</th> -->
                        <th width="auto">Arf NO.</th>
                        <th width="auto">MO Reference</th>
                        <th width="auto">Request Type</th>
                        <th width="auto">Requested By</th>
                        <th width="auto">Digits Code</th>
                        <th width="auto">Asset Code</th>
                        <th width="auto">Item Description</th>
                        <th width="auto">Serial No</th>
                        <th width="auto">Quantity</th>
                        <th width="auto">Item Cost</th>
                        <th width="auto">Requested Date</th>
                        <th width="auto">Received Date</th>
               
                    </tr>
                </thead>
                <tbody>
                @foreach($result as $val)
                    <tr>
                    <!-- <td style="text-align:center">   
                     <a class='btn btn-primary btn-xs' href='{{CRUDBooster::adminpath("request_history/detail/".$val["id"])."?return_url=".urlencode(Request::fullUrl())}}'><i class='fa fa-eye'></i></a>                                         
                    </td>  -->
                    <td>{{$val['reference_number']}}</td>
                    <td>{{$val['mo_reference_number']}}</td>  
                    <td>{{$val['category_id']}}</td>
                    <td>{{$val['requestedby']}}</td>
                    <td>{{$val['digits_code']}}</td>  
                    <td>{{$val['asset_code']}}</td>
                    <td>{{$val['item_description']}}</td>                                                
                    <td>{{$val['serial_no']}}</td>  
                    <td>{{$val['quantity']}}</td>  
                    <td>{{$val['unit_cost']}}</td>  
                    <td>{{$val['created_at']}}</td> 
                    <td>{{$val['received_at']}}</td>  
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
           
                   
        </div>


</div>

@endsection

@push('bottom')
<script src=
"https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js" >
    </script>
    <script src=
"https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js" >
    </script>
        <script src=
"https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js" >
    </script>
    <script type="text/javascript">
        $(function(){
            $('body').addClass("sidebar-collapse");
        });
       var table;
       $(document).ready(function() {
           table = $("#table_dashboard").DataTable({
                ordering:false,
                pageLength:100,
                language: {
                    searchPlaceholder: "Search"
                },
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"],
                    ],
                buttons: [
                    {
                        extend: "excel",
                        title: "Assets Deployed Monitoring",
                        exportOptions: {
                        columns: ":not(.not-export-column)",
                        columns: ":gt(0)",
                            modifier: {
                            page: "current",
                        }
                        },
                    },
                    ],
            });
            $("#btn-export").on("click", function () {
                table.button(".buttons-excel").trigger();
            });
        });
 
    </script>
@endpush