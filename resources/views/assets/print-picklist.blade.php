
@extends('crudbooster::admin_template')
@push('head')
<style type="text/css">   
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
        Print Form
    </div>
    <form method='' id="myform" action=""> 
        <div class='panel-body'>    
            <div id="printableArea"> 
                    <table width="100%" style="font-size: 13px;">

                        <tr>
                            <td colspan="4">
                                <h4 align="center" ><strong>Pick List Report (DAM)</strong></h4> 
                            </td>
                        </tr>

                        <tr>
                            <td colspan="4"><hr/></td>
                        </tr>
                        
                        <tr>
                            <td width="20%"><label><strong>Requested By:<strong></label></td>
                            <td width="40%"><p>{{$Header->requestedby}}</p></td>

                            <td width="10%"><label><strong>Requested Date:<strong></label></td>
                            <td><p>{{ date('Y-m-d', strtotime($Header->created)) }}</p></td>

                        </tr>

                        <tr>
                            <!--<td width="20%"><label><strong>ARF#:<strong></label></td>
                            <td width="40%"><p>{{$Header->reference_number}}</p></td> -->
              
                            <td width="20%"><label><strong>Picklist Date:<strong></label></td>
                            <td width="40%"><p>{{ date('Y-m-d') }}</p></td>

                            <td width="10%"><label><strong>Location:<strong></label></td>
                            <td><p style="margin-top: 6px;">{{$Location->location}}</p></td>
                        </tr>


            
                        <tr>
                            <td colspan="4"><hr/></td>
                        </tr>

                        <tr>
                            <td colspan="4">
                                <table border="1" width="100%" style="text-align:center;border-collapse: collapse; font-size: 13px;">
                                    
                                    <thead>

                                        <!--<tr><th colspan="7"><h4 align="center" ><strong>Item Details</strong></h4></th></tr>
                                        <tr> -->
                                            <th style="text-align:center" height="10" width="10%">MO#</th>
                                            <th style="text-align:center" height="10" width="8%">Digits Code</th>
                                            <th style="text-align:center" height="10" width="8%">Asset Tag</th>
                                            <th style="text-align:center" height="10" width="11%">Serial#</th>
                                            <th style="text-align:center" height="10" width="30%">DAM Description</th>          
                                            
                                            <th style="text-align:center" height="10" width="10%">Asset Category</th>
                                            <th style="text-align:center" height="10" width="10%">Asset Sub Category</th>
                                            <th style="text-align:center" height="10" width="6%">Qty</th>
                                            <th style="text-align:center" height="10" width="6%">Actual Qty</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?Php $item_count = 0; ?>

                                        @foreach($MoveOrder as $rowresult)

                                            <?Php $item_count++; ?>
                                            
                                            <tr>
                                                @if($rowresult->printed_at == null)
                                                    <td height="10">

                                                        <input type="hidden" value="{{$rowresult->id}}" name="mo_id[]">
                                                        
                                                        {{$rowresult->mo_reference_number}}
                                                    </td>
                                                    <td height="10">{{$rowresult->digits_code}}</td>
                                                    <td height="10">{{$rowresult->asset_code}}</td>
                                                    <td height="10">{{$rowresult->serial_no}}</td>
                                                    <td height="10">{{$rowresult->item_description}}</td>
                                                    <td height="10">{{$rowresult->category_id}}</td>
                                                    <td height="10">{{$rowresult->sub_category_id}}</td>
                                                    <td height="10">{{$rowresult->quantity}}</td>
                                                    <td height="10"></td>
                                                @endif
                                            </tr>

                                            <?Php $qty_val = $rowresult->quantity; ?>
                                        @endforeach
                                
                                    </tbody>

                                    <tr>
                                        <td colspan="7" style="text-align:right">
                                            <label>Total Qty:</label>
                                        </td>

                                        <td colspan="1" style="text-align:center">

                                            @if($item_count == 1)
                                                    <label>{{$qty_val}}</label>
                                                @else
                                                    <label>{{$item_count}}</label>
                                            @endif
                                                

                                        </td>

                                        <td style="text-align:center">
                                            <label></label>
                                        </td>

                                    </tr>

                                </table> 
                            </td>
                        </tr>

                    </table> 
            </div>
        </div>
        <div class='panel-footer'>
            <input type="hidden" value="{{$Header->requestid}}" name="requestid">
            
            <a href="{{ CRUDBooster::mainpath() }}" class="btn btn-default">{{ trans('message.form.cancel') }}</a>

            <button class="btn btn-primary pull-right" type="submit" id="printARF" onclick="printDivision('printableArea')"> <i class="fa fa-print" ></i> Print as PDF</button>

            <!-- @if( $Header->status_id == 17 )
              <button class="btn btn-primary pull-right" type="submit" id="printARF" onclick="printDivision('printableArea')"> <i class="fa fa-print" ></i> Print as PDF</button>

              @else
             <button class="btn btn-primary pull-right" type="submit" id="print"    onclick="printDivision('printableArea')"> <i class="fa fa-print" ></i> Print as PDF</button>
             @endif -->
             
        </div>
    </form> 
  </div>
@endsection
@push('bottom')
    <script type="text/javascript">

        /*$("#printPulloutForm").on('click',function(){
        //var strconfirm = confirm("Are you sure you want to approve this pull-out request?");
            var data = $('#myform').serialize();
                $.ajax({
                        type: 'GET',
                        url: '{{ url('admin/returns_retail_scheduling/ReturnPulloutUpdate') }}',
                        data: data,
                        success: function( response ){
                            console.log( response );              
                        
                        },
                        error: function( e ) {
                            console.log(e);
                        }
                  });
                  return true;
        });*/




        function printDivision(divName) {
         //alert('Please print 2 copies!');
            var generator = window.open(",'printableArea,");
            var layertext = document.getElementById(divName);
            generator.document.write(layertext.innerHTML.replace("Print Me"));
            generator.document.close();
            generator.print();
            generator.close();
        }        


        $("#printARF").on('click',function(){
        //var strconfirm = confirm("Are you sure you want to approve this pull-out request?");
            var data = $('#myform').serialize();
                $.ajax({
                        type: 'GET',
                        url: '{{ url('admin/move_order/PickListUpdate') }}',
                        data: data,
                        success: function( response ){
                            console.log( response );              
                        
                        },
                        error: function( e ) {
                            console.log(e);
                        }
                  });
                  return false;
        });

    </script>
@endpush