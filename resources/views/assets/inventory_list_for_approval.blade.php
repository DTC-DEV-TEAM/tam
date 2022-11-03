@extends('crudbooster::admin_template')
@push('head')
<style type="text/css">   
    img[data-action="zoom"] {
        z-index: 1000;
    cursor: pointer;
    cursor: -webkit-zoom-in;
    cursor: -moz-zoom-in;
    }
    .header_images,
    .header_images-wrap {
        z-index: 1000;
    position: relative;
    z-index: 666;
    -webkit-transition: all 300ms;
        -o-transition: all 300ms;
            transition: all 300ms;
    }
    img.header_images {
        z-index: 1000;
    cursor: pointer;
    cursor: -webkit-zoom-out;
    cursor: -moz-zoom-out;
    }
    .zoom-overlay {
    z-index: 1000;
    background: #fff;
    top: 0;
    left: ;
    right: 0;
    bottom: 0;
    pointer-events: none;
    filter: "alpha(opacity=0)";
    opacity: 0;
    -webkit-transition:      opacity 300ms;
        -o-transition:      opacity 300ms;
            transition:      opacity 300ms;
    }
    .zoom-overlay-open .zoom-overlay {
        z-index: 1000;
    filter: "alpha(opacity=100)";
    opacity: 1;
    }
    .zoom-overlay-open,
    .zoom-overlay-transitioning {
    cursor: default;
    z-index: 1000;
    position: relative;
    }
    #friendsoptionstable {
    table-layout: fixed;
    word-wrap: break-word;
    }
</style>
@endpush
@section('content')
<!-- link -->
@if(g('return_url'))
	<p class="noprint"><a title='Return' href='{{g("return_url")}}'><i class='fa fa-chevron-circle-left '></i> &nbsp; {{trans("crudbooster.form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}}</a></p>       
@else
	<p class="noprint"><a title='Main Module' href='{{CRUDBooster::mainpath()}}'><i class='fa fa-chevron-circle-left '></i> &nbsp; {{trans("crudbooster.form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}}</a></p>       
@endif
  <div class='panel panel-default'>
    <div class='panel-heading'>  
        Assets Movement History Details
    </div>
    <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
    <input type="hidden" value="{{$Header->header_id}}" name="header_id" id="header_id">
    <div class='panel-body'>    
        <div class="row">                           
            <label class="control-label col-md-2">PO No:</label>
            <div class="col-md-4">
                    <p>{{$Header->po_no}}</p>
            </div>

            <label class="control-label col-md-2">{{ trans('message.form-label.invoice_date') }}:</label>
            <div class="col-md-4">
                    <p>{{date('Y-m-d', strtotime($Header->invoice_date))}}</p>
            </div>
        </div>
        <div class="row">                           
            <label class="control-label col-md-2">Invoice No:</label>
            <div class="col-md-4">
                    <p>{{$Header->invoice_no}}</p>
            </div>

            <label class="control-label col-md-2">RR Date:</label>
            <div class="col-md-4">
                    <p>{{date('Y-m-d', strtotime($Header->rr_date))}}</p>
            </div>
        </div>
        <div class="row">                           
            <label class="control-label col-md-2">Created By:</label>
            <div class="col-md-4">
                    <p>{{$Header->name}}</p>
            </div>
            <label class="control-label col-md-2">Date Created:</label>
            <div class="col-md-4">
            <p>{{ $Header->date_created }}</p>
            </div>
        </div>
        <div class="row">                           
            <label class="control-label col-md-2">Received/Cancelled By:</label>
            <div class="col-md-4">
             <p>{{ $Header->approver }}</p>
            </div>
            <label class="control-label col-md-2">Date of Received/Cancelled:</label>
           <div class="col-md-4">
             <p>{{ $Header->date_updated }}</p>
           </div>
        </div>
        <div class="row">                           
            <label class="control-label col-md-2">SI/DR</label>
            <div class="col-md-4">
             @foreach($header_images as $res_header_images)                                    
                @if ($res_header_images->file_name)
                <img style="margin-right:5px" width="120px"; height="90px"; src="{{URL::to('vendor/crudbooster/inventory_header').'/'.$res_header_images->file_name}}" alt="" data-action="zoom"> 
                @else
                <img width="60px"; height="50px"; src="{{URL::to('vendor/crudbooster/no_image_available/No_Image_Available.jpg')}}" alt="" data-action="zoom">
                @endif                                         
             @endforeach
            </div>
            <label class="control-label col-md-2">Remarks:</label>
           <div class="col-md-4">
             <p>{{ $Header->remarks }}</p>
           </div>
        </div>

        <hr>

        <!-- Body Area -->
      
        <div class="box-header text-center">
        <!-- <a style="float:left" class='btn btn-success btn-xs' href='{{CRUDBooster::mainpath("generate-barcode/".$Header->header_id)."?return_url=".urlencode(Request::fullUrl())}}'><i class='fa fa-barcode'></i> Print all Barcode</a> -->
            <h3 class="box-title"><b>{{ trans('message.form-label.asset_items') }}</b></h3>
        </div>

        <div class="box-body">
            <div class="table-responsive">   
                <table id='table_dashboard' class="table table-hover table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Digits Code</th>   
                            <th>Item Condition</th>    
                            <th>Value</th>                                            
                            <th>Quantity</th> 
                            <th>Serial No</th>   
                            <th>Warranty Coverage Year</th>                                            
                            
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($Body as $res)
                            <tr>
                            <td>{{$res->digits_code}}</td>
                            <td>{{$res->item_description}}</td>   
                            <td>{{$res->value}}</td>
                            <td>{{$res->quantity}}</td> 
                            <td>{{$res->serial_no}}</td>    
                            <td>{{$res->warranty_coverage}}</td>                                                                                                                  
                            </tr>
                        @endforeach
                    </tbody>
                </table>     
            </div>
        </div>
        @if(CRUDBooster::myPrivilegeName() == "Asset Custodian" OR CRUDBooster::myPrivilegeName() == "Super Administrator")
            @if($Header->header_approval_status == 20)
            <button class="btn btn-danger pull-right" value="assetcustodiancancel" type="button" id="btnAssetCustodianCancel" style="margin-left: 5px; margin-right:30px"><i class="fa fa-thumbs-down" ></i> Cancel</button>
            @endif 
         @endif 
    </div>
  </div>
@endsection
@push('bottom')
    <script type="text/javascript">
        $(function(){
            $('body').addClass("sidebar-collapse");
        });
        function preventBack() {    
            window.history.forward();
        }
         window.onunload = function() {
            null;
        };
        setTimeout("preventBack()", 0);
    $("#table_dashboard").DataTable({
        pageLength:10000,
        pagingType: "simple",
        bPaginate: false,
        paging: false,
        info: false,
        dom : '<"pull-left"><"pull-right"l>tip',
        language: {
            searchPlaceholder: "Search"
        }
    });

    /**Rejected Request*/
    $('#btnAssetCustodianCancel').on('click', function (event) {
    event.preventDefault();
    var fired_button = $(this).val();
    var id = $('#header_id').val();
            swal({
                title: "Are you sure?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#41B314",
                cancelButtonColor: "#F9354C",
                confirmButtonText: "Yes, cancel it!",
                width: 450,
                height: 200
                }, function () {
                    //$("#cancelledForm").submit();  
                $.ajaxSetup({
                    headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                });
                var formData = new FormData();
                formData.append('approvalMethod', fired_button);
                formData.append('id', id);
                $.ajax({
                    url: "{{ route('assets.get.rejectedProcess') }}",
                    dataType: "json",
                    type: "POST",
                    processData : false,
                    contentType : false,
                    data: formData,
                    // data: {
                    //     "approvalMethod": fired_button,
                    //     "id": id,
                    // },
                    success: function (data) {
                        if (data.status == "success") {
                            swal({
                                type: data.status,
                                title: data.message,
                            });
                            setTimeout(function(){
                                window.location.replace(document.referrer);
                            }, 1000); 
                            } else if (data.status == "error") {
                            swal({
                                type: data.status,
                                title: data.message,
                            });
                        }
                    }
                })
            }); 
    });

    </script>
@endpush