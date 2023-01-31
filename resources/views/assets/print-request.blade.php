
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
                                <h4 align="center" ><strong>Asset Deployment Form</strong></h4> 
                            </td>
                        </tr>
                        <tr>
                            <td width="20%"><label><strong>Arf#:<strong></label></td>
                            <td width="40%"><p>{{$Header->reference_number}}</p></td>
                            <td width="20%"><label><strong>MO#:<strong></label></td>
                            <td width="40%"><p>{{$HeaderID->mo_reference_number}}</p></td>
                            <!--<td width="20%"><label><strong>Company:<strong></label></td>
                            <td><p>{{$Header->company_name}}</p></td> -->
                        </tr>

                        <tr>
                            <td width="20%"><label><strong>Employee Name:<strong></label></td>
                            <td width="40%"><p>{{$Header->employee_name}}</p></td>
                            <td width="10%"><label><strong>Company:<strong></label></td>
                            <td><p>{{$Header->company_name}}</p></td>
                        </tr>
                        <tr>
                            <td width="20%"><label><strong>Department:<strong></label></td>
                            <td width="40%"><p>{{$Header->department}}</p></td>
                            <td width="10%"><label><strong>Position:<strong></label></td>
                            <td><p>{{$Header->position}}</p></td>
                        </tr>
                        <tr>
                            @if($Header->store_branch != null || $Header->store_branch != "")
                                <td width="20%"><label><strong>Store/Branch:<strong></label></td>
                                <td width="40%"><p>{{$Header->store_branch}}</p></td>
                            @endif
                            <td width="20%"><label><strong>Date:<strong></label></td>
                            <td><p>{{ date('Y-m-d') }}</p></td>
                        </tr>

                        <tr>
                            <td colspan="4"><hr/></td>
                        </tr>

                        <tr>
                                <td width="20%"><label><strong>Purpose:<strong></label></td>
                                <td width="40%"><p>{{$Header->request_description}}</p></td>
                        </tr>

                        <tr>
                            <td colspan="4"><hr/></td>
                        </tr>

                        <tr>
                            <td colspan="4">
                                <table border="1" width="100%" style="text-align:center;border-collapse: collapse; table-layout: fixed; font-size: 13px;">
                                    
                                    <thead>
                                        <tr><th colspan="5"><h4 align="center" ><strong>Item Details</strong></h4></th></tr>
                                        <tr>
                                            <th style="text-align:center" height="10">Digits Code</th>
                                            <th style="text-align:center" height="10">Item Description</th>          
                                            <th style="text-align:center" height="10">Serial#</th>
                                            <th style="text-align:center" height="10">Qty</th>
                                            <th style="text-align:center" height="10">Unit Cost</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?Php   $item_count = 0; ?>

                                        @foreach($MoveOrder as $rowresult)

                                            <?Php $item_count++; ?>

                                            <tr>
                                                @if($rowresult->digits_code != null)
                                                    <td height="10">

                                                        <input type="hidden" value="{{$rowresult->id}}" name="mo_id[]">
                                                       
                                                        <input type="hidden" value="{{$rowresult->inventory_id}}" name="inventory_id[]">

                                                        <input type="hidden" value="{{$rowresult->item_id}}" name="item_id[]">

                                                        {{$rowresult->digits_code}}

                                                    </td>
                                                    <td height="10">{{$rowresult->item_description}}</td>
                                                    <td height="10">{{$rowresult->serial_no}}</td>
                                                    <td height="10">{{$rowresult->quantity}}</td>
                                                    <td height="10">{{$rowresult->unit_cost}}</td>
                                                @endif
                                            </tr>

                                            <?Php $cost_total = $rowresult->total_unit_cost; ?>
                                            
                                        @endforeach
                                
                                    </tbody>

                                    <tr>
                                        <td colspan="4" style="text-align:right">
                                            <label>Total:</label>
                                        </td>

                                        <td style="text-align:center">
                                            @if($item_count == 1)
                                                <label>{{$cost_total}}</label>
                                            @else
                                                <label>{{$Header->total}}</label>
                                            @endif
                                        </td>

                                    </tr>

                                </table> 
                            </td>
                        </tr>

                        <tr>
                            <td colspan="4"><hr/></td>
                        </tr>

                        <tr>
                            <td width="20%">
                                <label><strong>Approved By:<strong></label>
                            </td>
                            <td width="40%"><p>{{$Header->approvedby}}</p></td>
                            <td width="20%"><label><strong>Approved Date:<strong></label></td>
                            <td><p>{{$Header->approved_at}}</p></td>
                        </tr>

                        @if($Header->recommendedby != null || $Header->recommendedby != "")
                            <tr>
                                <td width="20%">
                                    <label><strong>Recommended By:<strong></label>
                                </td>
                                <td width="40%"><p>{{$Header->recommendedby}}</p></td>
                                <td width="20%"><label><strong>Recommended Date:<strong></label></td>
                                <td><p>{{$Header->recommended_at}}</p></td>
                            </tr>
                        @endif

                        <tr>
                            <td width="20%">
                                <label><strong>Processed By:<strong></label>
                            </td>
                            <td width="40%"><p>{{$Header->processedby}}</p></td>
                            <td width="20%"><label><strong>Processed Date:<strong></label></td>
                            <td><p>{{$Header->purchased2_at}}</p></td>
                        </tr>

                        <tr>
                            <td width="20%">
                                <label><strong>Picked By:<strong></label>
                            </td>
                            <td width="40%"><p>{{$Header->pickedby}}</p></td>
                            <td width="20%"><label><strong>Picked Date:<strong></label></td>
                            <td><p>{{$Header->picked_at}}</p></td>
                        </tr>

            
                        <tr>
                            <td colspan="4"><hr/></td>
                        </tr>

                        <tr>
                            <td colspan="4">
                                <h5 align="center" ><strong>UNDERTAKING</strong></h5> 
                            </td>
                        </tr>

                        <tr>
                            <td colspan="4">
                                <p>
                                    I, <strong>{{$Header->requestedby}}</strong> hereby confirm the acceptance of the asset as discussed above. I agree to abide all the governing policies and procedures regarding 
                                    the Company’s assets. I further agree to assume all the accountabilities attached to the possession of such asset
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="4"><hr/></td>
                        </tr>

                        <tr>
                        
                            <td width="20%">
                            
                                <label><strong>Requested By:<strong></label>
                            </td>
                            <td width="40%">
                            
                                <p>{{$Header->requestedby}}</p>
                            </td>
                            <td width="20%">
                            
                                <label><strong>Requested Date:<strong></label>
                            </td>
                            <td>
                            
                                <p>{{$Header->created_at}}</p>
                            </td>

                        </tr>

                    </table> 
                </div>

            </div>

            <div class='panel-footer'> 
                    <input type="hidden" value="{{$Header->requestid}}" name="requestid">

                    <a href="{{ CRUDBooster::mainpath() }}" class="btn btn-default">{{ trans('message.form.cancel') }}</a>
                    
                    <button class="btn btn-primary pull-right" type="submit" id="printARF" onclick="printDivision('printableArea')"> <i class="fa fa-print" ></i> Print as PDF</button>
                    
                    <!-- @if( $Header->status_id == 15 )
                    
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
                        url: '{{ url('admin/move_order/ADFUpdate') }}',
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