
@extends('crudbooster::admin_template')

@push('head')
<style type="text/css">   
.singlePrint {
    width: 17%;
    padding:0;
    margin:0;
    font-size: 16px;
    font-size: 2.5vw;
    text-align:center;
 }
 .left{
  text-align:left;
  padding:0;
  margin-left:50px;
 }
@media print {
  #print {
    display: none;
  }
  .noprint  {
		display: none;
	}
	#printme  {
		display: block;
	}
  .main-footer  {
		display: none;
	}

  .singlePrint {
    width: 17%;
    font-size: 16px;
    font-size: 2.5vw;
 }
 .left{
  text-align:left;
  padding:0;
    margin-left:2.9px;
 }
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
    <div class='panel-heading noprint'>  
        Generate Assets Inventory Barcode 
    </div>
    <div class='panel-body' id="printme">    
      <div class="row">
        <div class="col-md-12">
         <div class="singlePrint" style="width: 17%; text-align:center">
          <strong><p style="padding:0; margin-bottom:5px;line-height:10px; margin-bottom:2px">{!! DNS1D::getBarcodeSVG("$item_code", 'C93', 1,38) !!}</p></strong>
           <strong><p style="font-size:7px; padding:0; margin-bottom:0; text-align:center"><span> {{$details->item_description}}</span></p></strong>
           <div class="left">
            <strong><p style="font-size:7px; margin-right:30px;margin-bottom:0;">Asset Code:<span> {{$details->asset_code}}</span></p></strong>
            @if ($details->serial_no)
            <strong><p style="font-size:7px; margin-right:55px;">SN:<span> {{$details->serial_no}}</span></p></strong>
            @else
            <strong><p style="font-size:7px; margin-right:55px;">SN:<span> No-Serial</span></p></strong>
            @endif 
           
           
          </div>
          <!-- <strong><p style="padding:0; margin:4px;line-height:10px;">{!! DNS1D::getBarcodeSVG("$asset_tag", 'C93', 0.9,38) !!}</p></strong>
          <strong><p style="font-size:7px;padding:0; margin-left:80px;"><span> {{$item_code}}</span></p></strong> -->
        </div>
           
        </div>
      </div>
     <br><br>
      <div class="row">
        <div class="col-md-12">  
          <button id="print" class="btn btn-primary"><i class="fa fa-print"></i></button>
      </div>
      </div>
    

    </div>

  
  </div>
@endsection
@push('bottom')
    <script type="text/javascript">
      window.onload = function() { window.print(); }
      $("#print").click(function(event) {
        window.print()
      });
      
    </script>
@endpush