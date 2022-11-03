@extends('crudbooster::admin_template')
    @push('head')
    
        <style type="text/css">   
            table.dataTable td.dataTables_empty {
                text-align: center;    
            }
        </style>
    @endpush
@section('content')

<div class='panel panel-default'>
    <div class='panel-heading'>
    Asset Movement History
    </div>

        <div class='panel-body'>
            <div class="row">
            <div class="col-md-3">
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
                        <!-- <option value="">-- Select Asset Code --</option> -->
                        @foreach ($result as $asset_code)
                            <option value="{{ $asset_code->asset_code }}">{{ $asset_code->asset_code }}</option>
                        @endforeach 
                    </select>
                </div>  
                <div class="col-md-3">
                    <h3>Item Description</h3>
                    <input type="text" class="form-control" name="item_description"  id="item_description" readonly>
                </div>  
                
            </div>
            <button type="submit" class="btn btn-primary" style="margin-top:15px; margin-bottom:10px"
                                    id="btn-generate"><i class="fa fa-search"></i> Search History</button>
            <br>
        <div class="row" style="margin:5px">   
           
            <table class='table table-hover table-striped table-bordered' id="table_dashboard">
                <thead>
                    <tr class="active">
                        <th width="auto">Action</th>
                        <th width="auto">Transaction Type</th>
                        <th width="auto">Reference No.</th>
                        <th width="auto">Po No.</th>
                        <th width="auto">Invoice No</th>
                        <th width="auto">Invoice Date</th>
                        <th width="auto">RR Date</th>
                        <!-- <th width="auto">Created By</th>
                        <th width="auto">Date Created</th> -->
                    </tr>
                </thead>
                <tbody>
                    @foreach($history as $res)
                        <tr>
                        <td style="text-align:center">   
                        @if($res->transaction_type === "Inventory")     
                        <a class='btn btn-primary btn-xs' href='{{CRUDBooster::mainpath("detail/".$res->header_id)."?return_url=".urlencode(Request::fullUrl())}}'><i class='fa fa-eye'></i></a>                                         
                        @else
                        <a class='btn btn-primary btn-xs' href='{{CRUDBooster::mainpath("detail-deployed/".$res->header_id)."?return_url=".urlencode(Request::fullUrl())}}'><i class='fa fa-eye'></i></a>   
                        @endif
                        </td>  
                        <td>{{$res->transaction_type}}</td> 
                        <td>{{$res->reference_no}}</td>
                        <td>{{$res->po_no}}</td>  
                        <td>{{$res->invoice_no}}</td>  
                        <td>{{$res->invoice_date}}</td>
                        <td>{{$res->rr_date}}</td>                                                                   
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
           
                   
        </div>


</div>

@endsection

@push('bottom')
    <script type="text/javascript">
       var table;
       $(document).ready(function() {
            $("#table_dashboard").DataTable({
                pageLength:10000,
                pagingType: "simple",
                bPaginate: false,
                paging: false,
                info: false,
                dom : '<"pull-left"f><"pull-right"l>tip',
                language: {
                    searchPlaceholder: "Search"
                }
            });
            $('.select2').select2({
                placeholder: " Select Asset Code",
                allowClear: true
            })
            $(".date").datetimepicker({
                    viewMode: "days",
                    format: "YYYY-MM-DD",
                    dayViewHeaderFormat: "MMMM YYYY",
            });
            // fill value of item description input
            $('#asset_code').on('change',  function(){
                ////When cost center is clicked, update the fields with corresponding data
                loadField($('#asset_code').val(), function(data){
                    console.log(data);
                    $('#item_description').val(data[0].description)
                })
            }) 
            //Get dropdown value from local storage
            if ($('#start_date').length) {
            $('#start_date').val(sessionStorage.getItem("start_date"));
            }
            if ($('#end_date').length) {
            $('#end_date').val(sessionStorage.getItem("end_date"));
            }
            if ($('#asset_code').length) {
            $('#asset_code').val(JSON.parse(sessionStorage.getItem("asset_code")));
            }
            if ($('#item_description').length) {
            $('#item_description').val(sessionStorage.getItem("item_description"));
            }
        });

       
        if ($('#asset_code').length) {
        $('#asset_code').val(JSON.parse(sessionStorage.getItem("asset_code")));
        }
        //save dropdown value in local storage
        $('#start_date').on('dp.change', function() {
        sessionStorage.setItem("start_date", $(this).val());
        });
        $('#end_date').on('dp.change', function() {
        sessionStorage.setItem("end_date", $(this).val());
        });
        $('#asset_code').on('change', function() {
        sessionStorage.setItem("asset_code", JSON.stringify($(this).val()));
        sessionStorage.setItem("item_description",  $('#item_description').val());
        });
        //sessionStorage.setItem("item_description",  $('#item_description').val());
       
        //console.log(window.performance.getEntriesByType("navigation"));
        if (String(window.performance.getEntriesByType("navigation")[0].type) !== "reload") {
        // clear sessionStorage
        sessionStorage.removeItem("start_date"); 
        sessionStorage.removeItem("end_date"); 
        sessionStorage.removeItem("asset_code");
        sessionStorage.removeItem("item_description");
        console.info( "This page is not reloaded");
        }

        $("#btn-generate").click(function (e) {
            e.preventDefault();
            $.ajax({
            url: "{{ route('assets.get.checkData') }}",
            type: "POST",
            dataType: "json",
            data: {},
            success: function (response) {
                if(response.count >= 1){ 
                generate.form();
                }
                else{
                generate.submit(false);
                }
            },
            });
        });

        var generate = {
        form: function () {
            swal({
            allowEscapeKey: false,
            allowOutsideClick: false,
            showConfirmButton: false,
            title: "Please wait...",
            onOpen: () => {swal.showLoading()}
            });
            generate.submit(true);
            // swal({
            // title: "Overwrite Existing Asset Movement History?",
            // type: "warning",
            // showCancelButton: true,
            // confirmButtonColor: "#41B314",
            // cancelButtonColor: "#F9354C",
            // confirmButtonText: "Yes!",
            // }, function () {
            //     generate.submit(true);
            // });
        },

        submit: function (Overwrite) {
            swal({
            allowEscapeKey: false,
            allowOutsideClick: false,
            showConfirmButton: false,
            title: "Please wait...",
            onOpen: () => {swal.showLoading()}
            });
            $.ajax({
            url: "{{ route('assets.get.histories') }}",
            type: "post",
            dataType: "json",
            data: {
                Overwrite: Overwrite,
                date_from: $("#start_date").val(),
                date_to: $("#end_date").val(),
                asset_code: $('#asset_code').val()
            },
            success: function (response) {
                if (response.status == "success") {
                swal({
                    type: response.status,
                    title: response.message,
                    onClose: () => { RefreshPage() }
                });
                RefreshPage()
                } else if (response.status == "error") {
                swal({
                    type: response.status,
                    title: response.message,
                });
                }
            },
            error: function (response) {
                console.log(response);
            },
            });
        },
        };

        function RefreshPage(){
            setTimeout(function(){
                location.reload();  
            }, 2000); 
        }

        //fill the item description based on selected asset code
        function loadField(asset_code, callback){
            $.ajax({
                url: "{{ route('assets.get.assetDescription') }}",
                type : "POST",
                dataType : 'JSON',
                data : {
                    asset_code : asset_code
                },
                success: callback
            })
        }
    </script>
@endpush