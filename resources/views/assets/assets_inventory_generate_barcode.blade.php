
@extends('crudbooster::admin_template')

@push('head')
<style type="text/css">   
.wholePrint {
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
  .alert-success  {
		display: none;
	}
  .main-footer  {
		display: none;
	}
  .wholePrint {
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
	<p class="noprint"><a title='Main Module' href='{{CRUDBooster::adminPath("assets_inventory_body")}}'><i class='fa fa-chevron-circle-left '></i> &nbsp; {{trans("crudbooster.form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}}</a></p>       
@endif

  <div class='panel panel-default'>
    <div class='panel-heading noprint'>  
        Generate Assets Inventory Barcode 
    </div>
    <div class='panel-body' id="printMe">    
      <div class="row">
        <div class="col-md-12">
          <div class="wholePrint" style="width: 17%; text-align:center; display:inline-block">
          <!-- <table style="width:100%">
          @foreach($details as $key => $result)

              @if ($key % 3 == 0)
                  <tr>
              @endif

              <td> 
              <strong><p style="padding:0; margin-bottom:0;line-height:15px;">{!! DNS1D::getBarcodeSVG("$result->digits_code", 'C93', 1,38) !!}</p></strong>
              <strong><p style="font-size:7px; padding:0; margin-bottom:0;"><span> {{$result->item_description}}</span></p></strong>
              <div class="left">
                <strong><p style="font-size:7px; margin-right:30px;margin-bottom:0;">Asset Code:<span> {{$result->asset_code}}</span></p></strong>
                <strong><p style="font-size:7px; margin-right:55px;">SN:<span> {{$result->serial_no}}</span></p></strong>
              </div>
              </td>
             
              @if (($key + 1) % 3 == 0)
                  </tr>
              @endif

          @endforeach

          @if (($key + 1) % 3 != 0)
              </tr>
          @endif
          </table> -->

          @foreach ($details as $result)
           <strong><p style="padding:0; margin-bottom:0;line-height:15px; margin-bottom:2px">{!! DNS1D::getBarcodeSVG("$result->digits_code", 'C93', 1,38) !!}</p></strong>
           <strong><p style="font-size:7px; padding:0; margin-bottom:0;"><span> {{$result->item_description}}</span></p></strong>
           <div class="left">
            <strong><p style="font-size:7px; margin-right:30px;margin-bottom:0;">Asset Code:<span> {{$result->asset_code}}</span></p></strong>
            @if ($result->serial_no)
            <strong><p style="font-size:7px; margin-right:55px;">SN:<span> {{$result->serial_no}}</span></p></strong>
            @else
            <strong><p style="font-size:7px; margin-right:55px;">SN:<span> No-Serial</span></p></strong>
            @endif 
           
           </div>
           
           <!-- <strong><p style="padding:0; margin:0;line-height:15px;">{!! DNS1D::getBarcodeSVG("$result->asset_code", 'C93', 0.9,38) !!}</p></strong>
           <strong><p style="font-size:7px;padding:0; margin-left:75px;"><span> {{$result->digits_code}}</span></p></strong> -->
           @endforeach
          
          </div>
                
        </div>
      </div>
     <br><br>
      <div class="row">
        <div class="col-md-12">  
          <button id="print" class="btn btn-primary"><i class="fa fa-print"></i></button>
          <a class='btn btn-primary noprint' href='{{CRUDBooster::mainpath("export-ap-recording/".$header_id."/".$created_at)."?return_url=".urlencode(Request::fullUrl())}}'><i class='fa fa-download'></i> Export for AP Recording</a>
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