
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
                        @if($Header->request_type == "RETURN")
                            <td colspan="4">
                                <h4 align="center" ><strong>Asset Return Form</strong></h4> 
                            </td>
                        @else
                            <td colspan="4">
                                <h4 align="center" ><strong>Asset Transfer Form</strong></h4> 
                            </td>
                        @endif
                        </tr>

                        <tr>
                            <td width="20%"><label><strong>Reference_no:<strong></label></td>
                            <td width="40%"><p>{{$Header->reference_no}}</p></td>
                            <!--<td width="20%"><label><strong>Company:<strong></label></td>
                            <td><p>{{$Header->company_name}}</p></td> -->
                        </tr>

                        <tr>
                            <td width="20%"><label><strong>Employee Name:<strong></label></td>
                            <td width="40%"><p>{{$Header->employee_name}}</p></td>
                            <td width="10%"><label><strong>Company:<strong></label></td>
                            <td><p>{{$Header->company}}</p></td>
                        </tr>
                        <tr>
                            <td width="20%"><label><strong>Department:<strong></label></td>
                            <td width="40%"><p>{{$Header->department_name}}</p></td>
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
                                <td width="40%"><p>{{$Header->request_type}}</p></td>
                                @if($Header->request_type == "TRANSFER")
                                <td width="10%">
                                    <label><strong>Transferred To:<strong></label>
                                </td>
                                <td><p>{{$Header->transferTo}}</p></td>
                                @endif
                        </tr>

                        <tr>
                            <td colspan="4"><hr/></td>
                        </tr>

                        <tr>
                            <td colspan="4">
                                <table border="1" width="100%" style="text-align:center;border-collapse: collapse; table-layout: fixed; font-size: 13px;" id="total">
                                    
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

                                        @foreach($return_body as $rowresult)

                                            <tr>
                                                @if($rowresult->digits_code != null)
                                                    <td height="10">

                                                        <input type="hidden" value="{{$rowresult->id}}" name="mo_id[]">
                                                       
                                                        <input type="hidden" value="{{$rowresult->inventory_id}}" name="inventory_id[]">

                                                        <input type="hidden" value="{{$rowresult->item_id}}" name="item_id[]">

                                                        {{$rowresult->digits_code}}

                                                    </td>
                                                    <td height="10">{{$rowresult->description}}</td>
                                                    <td height="10">{{$rowresult->serial_no}}</td>
                                                    <td height="10">{{$rowresult->quantity}}</td>
                                                    <td height="10" class="cost">{{$rowresult->unit_cost}}</td>
                                                @endif
                                            </tr>

                                            <?Php $cost_total = $rowresult->total_cost; ?>
                                            
                                        @endforeach
                                
                                    </tbody>

                                    <!-- <tr>
                                        <td colspan="4" style="text-align:right">
                                            <label>Total:</label>
                                        </td>

                                        <td style="text-align:center">
                                                <label>{{$cost_total}}</label>      
                                        </td>

                                    </tr> -->

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
                            <td><p>{{$Header->approved_date}}</p></td>
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
                                <p style="font-style:italic">
                                    I, <strong>{{$Header->requestedby}}</strong> will ensure that this form is signed by the receiver and received in system before turnover of company assets
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
                            
                                <p>{{$Header->requested_date}}</p>
                            </td>

                        </tr>
                        <tr>
                        
                            <td width="20%">
                            
                                <label><strong>Received By:<strong></label>
                            </td>
                            <td width="40%">
                            
                                
                            </td>
                            <td width="20%">
                            
                                <label><strong>Received Date:<strong></label>
                            </td>
                            <td>
                            
                          
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

        // $("#printARF").on('click',function(){
        // //var strconfirm = confirm("Are you sure you want to approve this pull-out request?");
        //     var data = $('#myform').serialize();
        //         $.ajax({
        //                 type: 'GET',
        //                 url: '{{ url('admin/move_order/ADFUpdate') }}',
        //                 data: data,
        //                 success: function( response ){
        //                     console.log( response );              
                        
        //                 },
        //                 error: function( e ) {
        //                     console.log(e);
        //                 }
        //           });
        //           return false;
        // });
        function thousands_separators(num) {
        var num_parts = num.toString().split(".");
        num_parts[0] = num_parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return num_parts.join(".");
        }
        var tds = document
        .getElementById("total")
        .getElementsByTagName("td");
        var sumqty = 0;
        var sumcost = 0;
        for (var i = 0; i < tds.length; i++) {
        if (tds[i].className == "cost") {
            sumcost += isNaN(tds[i].innerHTML) ? 0 : parseFloat(tds[i].innerHTML);
        }
        }
        document.getElementById("total").innerHTML +=
        "<tr><td colspan='4' style='text-align:right'><strong>TOTAL</strong></td><td><strong>" +
        sumcost.toFixed(2) +
        "</strong></td></tr>";

    </script>
@endpush