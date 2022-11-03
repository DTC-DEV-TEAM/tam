
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
           
        </div>

        <hr>

        <!-- Body Area -->
      
        <div class="box-header text-center">
        <!-- <a style="float:left" class='btn btn-success btn-xs' href='{{CRUDBooster::mainpath("generate-barcode/".$Header->header_id)."?return_url=".urlencode(Request::fullUrl())}}'><i class='fa fa-barcode'></i> Print all Barcode</a> -->
            <h3 class="box-title"><b>{{ trans('message.form-label.asset_items') }}</b></h3>
        </div>

        <div class="box-body">
            <div class="table-responsive">
               
                    <div class="hack1" style="display: table;
                    table-layout: ;
                    width: 140%;">
                        <div class="hack2" style="  display: table-cell;
                        
                        width: ;"> 
                          
                                    <table id='table_dashboard' class="table table-hover table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <!-- <th>Action</th> -->
                                                <th>Asset Code</th>   
                                                <th>Digits Code</th>   
                                                <th>Serial No</th> 
                                                <th>Location</th> 
                                                <th>Status</th>     
                                                <th>Deployed To</th>       
                                                <th>Item Condition</th>                                               
                                                <th>Item Description</th> 
                                                <th>Value</th>                                         
                                                <th>Quantity</th>    
                                                <th>Warranty Coverage Year</th>                                           
                                                <th>Item Photo</th>     
                                                <th>Updated By</th>                                            
                                                <th>Date Updated</th>  
                                              
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($Body as $res)
                                                <tr>
                                                <!-- <td style="text-align:center">        
                                                <a id="getHistory" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#demoModal" data-id="{{ $res->aib_id }}"><i class='fa fa-history'></i> </a>
                                             <a class='btn btn-success btn-xs' href='{{CRUDBooster::mainpath("generate-barcode-single/".$res->aib_id)."?return_url=".urlencode(Request::fullUrl())}}'><i class='fa fa-barcode'></i></a>                                      
                                                </td>   -->
                                                <td>{{$res->asset_code}}</td> 
                                                <td>{{$res->digits_code}}</td>
                                                <td>{{$res->serial_no}}</td>  
                                                <td>{{$res->body_location}}</td>  
                                                <td>{{$res->status_description}}</td>
                                                <td>{{$res->deployed_to}}</td>
                                                <td>{{$res->item_condition}}</td> 
                                                <td>{{$res->item_description}}</td>   
                                                <td>{{$res->value}}</td>
                                                <td>{{$res->quantity}}</td>     
                                                <td>{{$res->warranty_coverage}}</td>                                                      
                                                <td>
                                                   @if ($res->itemImage)
                                                      <img width="80px"; height="50px"; src="{{URL::to($res->itemImage)}}" alt="" data-action="zoom">
                                                    @else
                                                      <img width="60px"; height="50px"; src="{{URL::to('vendor/crudbooster/no_image_available/No_Image_Available.jpg')}}" alt="" data-action="zoom">
                                                    @endif    
                                                </td>                                                                      
                                                <td>{{$res->updated_by}}</td>    
                                                <td>{{$res->date_updated}}</td>    
                                               </tr>
                                            @endforeach
                                        </tbody>
                                        
                                    </table> 
                 
                                 <!-- Modal Example Start-->
                                <div class="modal fade" id="demoModal" tabindex="-1" role="dialog" aria- 
                                    labelledby="demoModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                   <button type="button" class="close" data-dismiss="modal" aria- 
                                                    label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                    <h3 class="modal-title text-center" id="demoModalLabel">Assets Movement History</h3>
                                            </div>
                                            <div class="modal-body">
                                                    <table id='table_dashboard' class="table table-hover table-striped table-bordered historyTbl">
                                                        <thead>
                                                            <tr>
                                                                <th>Asset Code</th>   
                                                                <th>Digits Code</th>   
                                                                <th>Serial No</th> 
                                                                <th>Time Update</th> 
                                                                <th>Update By</th>
                                                                <!-- <th>Deployed To</th>    -->
                                                                <th>Location</th>   
                                                                <th style="width:30%">Remarks</th>  
                                                               
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            
                                                        </tbody>
                                                        
                                                    </table> 
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Modal Example End-->
                            
                        </div>
                    </div>
                
            </div>
        </div>

    </div>

    <div class='panel-footer'>
        
    </div>
  </div>
@endsection
@push('bottom')
    <script type="text/javascript">

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
    $(document).ready(function () {
        $.ajaxSetup({
        headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
        });
    $(document).on('click', '#getHistory', function (event) {
    event.preventDefault();
    var id = $(this).data('id');
    
        $.ajax({
            url: "{{ route('assets.get.history') }}",
            dataType: "json",
            type: "POST",
            data: {
                //"_token": token,
                "id": id
            },
            success: function (data) {
                console.log(data.items);
                var json = JSON.parse(JSON.stringify(data.items));
                if(data.items != null){
                    $.each(json, function (index, item) { 
                            var row = '<tr><td>'   + item.assets_code + 
                                      '</td><td> ' + item.digits_code + 
                                      '</td><td>'  + item.serial_no + 
                                      '</td><td>'  + item.date_update + 
                                      '</td><td>'  + item.updated_by +  
                                    //   '</td><td>'  + item.deployed_to + 
                                      '</td><td>'  + item.location + 
                                      '</td><td>'  + item.remarks +  
                                       
                                      '</td></tr>'
                    $(".historyTbl tbody").append(row);
                   }); 
                }else{
                    var row = '<tr><td colspan="8" style="text-align:center">' + 'No Asset Movement history yet' + '</td> </tr>'
                    $(".historyTbl tbody").append(row);
                }
                 
                
            }
        })
    });

     //remove data in table after modal close
     $(".modal").on("hidden.bs.modal", function(){
        $(".historyTbl tbody").html("");
    });
    
    }); 

    </script>
@endpush