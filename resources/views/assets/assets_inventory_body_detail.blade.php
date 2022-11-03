
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
        Inventory Item Lists
    </div>
    <div class='panel-body'>    
        <div class="row">                           
            <label class="control-label col-md-2">PO No:</label>
            <div class="col-md-4">
                    <p>{{$Body->po_no}}</p>
            </div>

            <label class="control-label col-md-2">{{ trans('message.form-label.invoice_date') }}:</label>
            <div class="col-md-4">
                    <p>{{date('Y-m-d', strtotime($Body->invoice_date))}}</p>
            </div>
        </div>
        <div class="row">                           
            <label class="control-label col-md-2">Invoice No:</label>
            <div class="col-md-4">
                    <p>{{$Body->invoice_no}}</p>
            </div>

            <label class="control-label col-md-2">RR Date:</label>
            <div class="col-md-4">
                    <p>{{date('Y-m-d', strtotime($Body->rr_date))}}</p>
            </div>
        </div>
        <div class="row">                           
            <label class="control-label col-md-2">Created By:</label>
            <div class="col-md-4">
                    <p>{{$Body->name}}</p>
            </div>
            <label class="control-label col-md-2">Date Created:</label>
            <div class="col-md-4">
             <p>{{ $Body->date_created }}</p>
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
                    width: 140%; ">
                        <div class="hack2" style="  display: table-cell;
                        
                        width: ;"> 
                          
                                    <table id='table_dashboard' class="table table-hover table-striped table-bordered" style="height:150px">
                                        <thead>
                                            <tr class="active">
                                                <th>Action</th>
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
                                                <tr>
                                                <td style="text-align:center">        
                                                    <a class='btn btn-success btn-xs' href='{{CRUDBooster::mainpath("generate-barcode-single/".$Body->aib_id)."?return_url=".urlencode(Request::fullUrl())}}'><i class='fa fa-barcode'></i></a>
                                                </td>  
                                                <td>{{$Body->asset_code}}</td> 
                                                <td>{{$Body->digits_code}}</td>
                                                <td>{{$Body->serial_no}}</td>  
                                                <td>{{$Body->body_location}}</td>  
                                                <td>{{$Body->status_description}}</td>
                                                <td>{{$Body->deployed_to}}</td>
                                                <td>{{$Body->item_condition}}</td>  
                                                <td>{{$Body->item_description}}</td>   
                                                <td>{{$Body->value}}</td>        
                                                <td>{{$Body->quantity}}</td>       
                                                <td>{{$Body->warranty_coverage}}</td>                                            
                                                <td>
                                                    @if ($Body->itemImage)
                                                      <img width="60px"; height="50px"; src="{{URL::to($Body->itemImage)}}" alt="" data-action="zoom">
                                                    @else
                                                      <img width="60px"; height="50px"; src="{{URL::to('vendor/crudbooster/no_image_available/No_Image_Available.jpg')}}" alt="" data-action="zoom">
                                                    @endif 
                                                    
                                                </td>   
                                                <td>{{$Body->updated_by}}</td>    
                                                <td>{{$Body->date_updated}}</td>                                                                       
                                               </tr>
                                        </tbody>
                                        
                                    </table> 

                                
                            
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

// $("#table_dashboard").DataTable({
//     pageLength:10
// });

    </script>
@endpush