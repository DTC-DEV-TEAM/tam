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
            <!-- <div class="col-md-3">
                    <h3>Select Date Start</h3>
                    <input type="text" class="form-control date" name="start_date"  id="start_date" placeholder="Please Select Start Date">
                </div>  
                <div class="col-md-3">
                    <h3>Select Date End</h3>
                    <input type="text" class="form-control date" name="end_date"  id="end_date" placeholder="Please Select End Date">
                </div>  
                <div class="col-md-3">
                    <h3>Asset Code</h3>
                    <select class="form-control select2" name="asset_code" id="asset_code">
                        <option value="">-- Select Asset Code --</option> 
                       
                    </select>
                </div>  
                <div class="col-md-3">
                    <h3>Item Description</h3>
                    <input type="text" class="form-control" name="item_description"  id="item_description" readonly>
                </div>  
                
            </div>
            <button type="submit" class="btn btn-primary" style="margin-top:15px; margin-bottom:10px"
                                    id="btn-generate"><i class="fa fa-search"></i> Search History</button>
            <br> -->
           
        <div class="row" style="margin:5px">   
            <button type="button" id="btn-export" class="btn btn-primary btn-sm btn-export" style="margin-bottom:10px"><i class="fa fa-download"></i>
                <span>Export Data</span>
            </button>
            
            <table class='table table-hover table-striped table-bordered' id="table_dashboard">
            
                <thead>
                    <tr class="active">
                        <th width="auto">Action</th>
                        <th width="auto">Status</th>
                        <th width="auto">Reference No.</th>
                        <th width="auto">Description</th>
                        <th width="auto">Request Quantity</th>
                        <th width="auto">Transaction Type</th>
                        <th width="auto">Request Type</th>
                        <th width="auto">Requested By</th>
                        <th width="auto">Department</th>
                        <th width="auto">Store Branch</th>
                        <th width="auto">MO Reference</th>
                        <th width="auto">MO Item Code</th>
                        <th width="auto">MO Item Description</th>
                        <th width="auto">MO QTY/Serve QTY</th>
                        <th width="auto">Requested Date</th>
                        <th width="auto">Transacted By</th>
                        <th width="auto">Transacted Date</th>
               
                    </tr>
                </thead>
                <tbody>
                @foreach($finalData as $val)
                    <tr>
                    <td style="text-align:center">   
                     <a class='btn btn-primary btn-xs' href='{{CRUDBooster::adminpath("request_history/detail/".$val["id"])."?return_url=".urlencode(Request::fullUrl())}}'><i class='fa fa-eye'></i></a>                                         
                    </td> 
                    @if($val['status'] == "FOR APPROVAL")
                    <td style="text-align:center">
                     <label class="label label-warning" style="align:center">{{$val['status']}}</label>
                    </td>
                    @elseif($val['status'] == "CLOSED")
                    <td style="text-align:center">
                     <label class="label label-success" style="align:center">{{$val['status']}}</label>
                    </td>
                    @elseif($val['status'] == "CANCELLED" || $val['status'] == "REJECTED")
                    <td style="text-align:center">
                     <label class="label label-danger" style="align:center">{{$val['status']}}</label>
                    </td>
                    @else
                    <td style="text-align:center">
                     <label class="label label-info" style="align:center">{{$val['status']}}</label>
                    </td>
                    @endif
                    <td>{{$val['reference_number']}}</td>
                    <td>{{$val['description']}}</td>  
                    <td>{{$val['request_quantity']}}</td>
                    <td>{{$val['transaction_type']}}</td>  
                    <td>{{$val['request_type']}}</td>
                    <td>{{$val['requested_by']}}</td>     
                    <td>{{$val['department']}}</td>                                                                
                    <td>{{$val['store_branch']}}</td>  
                    <td>{{$val['mo_reference']}}</td>  
                    <td>{{$val['mo_item_code']}}</td>  
                    <td>{{$val['mo_item_description']}}</td>  
                    <td>{{$val['mo_qty_serve_qty']}}</td>  
                    <td>{{$val['requested_date']}}</td> 
                    <td>{{$val['transacted_by']}}</td>  
                    <td>{{$val['transacted_date']}}</td>  
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
                        title: "Request Assets Report",
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