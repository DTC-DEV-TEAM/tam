@extends('crudbooster::admin_template')
    @push('head')
        <style type="text/css">   
            #footer th, td {
                border: 1px solid rgba(000, 0, 0, .5);
                padding: 8px;
                /* border-radius: 5px 0 0 5px; */
            }
            #asset-items1 th, td {
                border: 1px solid rgba(000, 0, 0, .5);
                padding: 8px;
            }
            #asset-items th, td {
                border: 1px solid rgba(000, 0, 0, .5);
                //padding: 8px;
            }
            table { border-collapse: collapse; empty-cells: show; }

            td { position: relative; }

            tr.strikeout td:before {
            content: " ";
            position: absolute;
            top: 50%;
            left: 0;
            border-bottom: 1px solid #111;
            width: 100%;
            }

            tr.strikeout td:after {
            content: "\00B7";
            font-size: 1px;
            }

            /* Extra styling */
            td { width: 100px; }
            th { text-align: left; }
        </style>
    @endpush
@section('content')
@if(g('return_url'))
	<p class="noprint"><a title='Return' href='{{g("return_url")}}'><i class='fa fa-chevron-circle-left '></i> &nbsp; {{trans("crudbooster.form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}}</a></p>       
@else
	<p class="noprint"><a title='Main Module' href='{{CRUDBooster::mainpath()}}'><i class='fa fa-chevron-circle-left '></i> &nbsp; {{trans("crudbooster.form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}}</a></p>       
@endif
<div class='panel panel-default'>
    <div class='panel-heading'>
        Detail Form (<span style="color:red">Kindly close the transaction within 15 days after you received the request</span>)
    </div>

    <form method='post' id="myform" action='{{CRUDBooster::mainpath('edit-save/'.$Header->requestid)}}'>
        <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
        <input type="hidden" value="0" name="action" id="action">

        <input type="hidden" value="{{$Header->requestid}}" name="headerID" id="headerID">
        <input type="hidden" value="{{$Header->request_type_id}}"  id="request_type_id">

        <input type="hidden" value="" name="bodyID" id="bodyID">

        <div class='panel-body'>

            <div class="row">                           
                <label class="control-label col-md-2">{{ trans('message.form-label.reference_number') }}:</label>
                <div class="col-md-4">
                        <p>{{$Header->reference_number}}</p>
                </div>

                <label class="control-label col-md-2">{{ trans('message.form-label.created_at') }}:</label>
                <div class="col-md-4">
                        <p>{{$Header->created}}</p>
                </div>


            </div>


            <div class="row">                           
                <label class="control-label col-md-2">{{ trans('message.form-label.employee_name') }}:</label>
                <div class="col-md-4">
                   @if($Header->header_created_by != null || $Header->header_created_by != "")
                        <p>{{$Header->employee_name}}</p>
                    @else
                    <p>{{$Header->header_emp_name}}</p>
                    @endif
                </div>

                <label class="control-label col-md-2">{{ trans('message.form-label.company_name') }}:</label>
                <div class="col-md-4">
                        <p>{{$Header->company_name}}</p>
                </div>
            </div>

            <div class="row">                           


                <label class="control-label col-md-2">{{ trans('message.form-label.department') }}:</label>
                <div class="col-md-4">
                        <p>{{$Header->department}}</p>
                </div>

                <label class="control-label col-md-2">{{ trans('message.form-label.position') }}:</label>
                <div class="col-md-4">
                        <p>{{$Header->position}}</p>
                </div>

            </div>

            @if(CRUDBooster::myPrivilegeId() == 8 || CRUDBooster::isSuperadmin())
                <div class="row">                           
                    <label class="control-label col-md-2">{{ trans('message.form-label.store_branch') }}:</label>
                    <div class="col-md-4">
                            <p>{{$Header->store_branch}}</p>
                    </div>
                </div>
            @endif

            @if($Header->if_from_erf != null || $Header->if_from_erf != "")
                <div class="row">                           
                    <label class="control-label col-md-2">Erf Number:</label>
                    <div class="col-md-4">
                            <p>{{$Header->if_from_erf}}</p>
                    </div>
                </div>
            @endif

            @if($Header->if_from_item_source != null || $Header->if_from_item_source != "")
                <div class="row">                           
                    <label class="control-label col-md-2">Item Sourcing Number:</label>
                    <div class="col-md-4">
                            <p>{{$Header->if_from_item_source}}</p>
                    </div>
                </div>
            @endif

            <hr/>

            <div class="row">                           
                <label class="control-label col-md-2">{{ trans('message.form-label.purpose') }}:</label>
                <div class="col-md-4">
                        <p>{{$Header->request_description}}</p>
                </div>

        
            </div>
            <!--
            <hr/>

            <div class="row">                           
                <label class="control-label col-md-2">{{ trans('message.form-label.condition') }}:</label>
                <div class="col-md-4">
                        <p>{{$Header->condition_description}}</p>
                </div>

        
            </div> -->

            @if($Header->requestor_comments != null || $Header->requestor_comments != "")
                <hr/>
                <div class="row">                           
                    <label class="control-label col-md-2">{{ trans('message.table.requestor_comments') }}:</label>
                    <div class="col-md-10">
                            <p>{{$Header->requestor_comments}}</p>
                    </div>

            
                </div>
            @endif  

            @if($Header->approvedby != null || $Header->approvedby != "")
            <hr/>

            <div class="row">                           
                <label class="control-label col-md-2">{{ trans('message.form-label.approved_by') }}:</label>
                <div class="col-md-4">
                        <p>{{$Header->approvedby}} / <strong>{{$Header->approved_at}}</strong></p>
                </div>
                @if($Header->approver_comments != null || $Header->approver_comments != "")          
                    <label class="control-label col-md-2">{{ trans('message.table.approver_comments') }}:</label>
                    <div class="col-md-4">
                            <p>{{$Header->approver_comments}}</p>
                    </div>
                @endif 
            </div>
            @endif   


            <hr/>                
            <div class="row">
                <div class="col-md-12">
                    <div class="box-header text-center">
                        <h3 class="box-title"><b>Item Request</b></h3>
                    </div>
                    <div class="box-body no-padding">
                        <div class="pic-container">
                            <div class="pic-row">
                                <table id="asset-items1">
                                    <tbody id="bodyTable">
                                        <tr class="tbl_header_color dynamicRows">
                                            <th width="10%" class="text-center">{{ trans('message.table.digits_code') }}</th>
                                            <th width="20%" class="text-center">{{ trans('message.table.item_description') }}</th>
                                            <th width="9%" class="text-center">{{ trans('message.table.category_id_text') }}</th>                                                         
                                            <th width="10%" class="text-center">{{ trans('message.table.sub_category_id_text') }}</th> 
                                            <th width="5%" class="text-center">{{ trans('message.table.quantity_text') }}</th> 
                                            
                                            @if(in_array($Header->request_type_id, [6,7]))       
                                                <th width="5%" class="text-center">For Replenish Qty</th> 
                                                <th width="5%" class="text-center">For ReOrder Qty</th> 
                                                <th width="5%" class="text-center">Fulfilled Qty</th> 
                                                <th width="5%" class="text-center">UnServed Qty</th>                                                                                                                                                                                                          
                                                <th width="5%" class="text-center">DR Qty</th>
                                                <th width="5%" class="text-center">PO Qty</th>     
                                                <th width="10%" class="text-center">DR#</th>         
                                                <th width="10%" class="text-center">PO#</th>                                   
                                            @endif 

                                            <th width="5%" class="text-center">Cancelled Qty</th> 
                                            <th width="10%" class="text-center">Reason</th>    

                                        </tr>
                                        <tr id="tr-table">
                                            <?php   $tableRow = 1; ?>
                                            <tr>
                                                @foreach($Body as $rowresult)
                                                    <?php   $tableRow++; ?>
                                                
                                                        @if($rowresult->deleted_at != null || $rowresult->deleted_at != "")
                                                            <tr style="background-color: #dd4b39; color:#fff">
                                                                <td style="text-align:center" height="10">
                                                                        <input type="hidden"  class="form-control"  name="ids[]" id="ids{{$tableRow}}"  required  value="{{$rowresult->id}}">                               
                                                                        {{$rowresult->digits_code}}
                                                                </td>
                                                                <td style="text-align:center" height="10">
                                                                        
                                                                        <input type="hidden"  class="form-control"  name="ids[]" id="ids{{$tableRow}}"  required  value="{{$rowresult->id}}">                               
                                                                        {{$rowresult->item_description}}
                                                                </td>
                                                                <td style="text-align:center" height="10">
                                                                        {{$rowresult->category_id}}
                                                                </td>
                                                                <td style="text-align:center" height="10">
                                                                        {{$rowresult->sub_category_id}}
                                                                </td>
                                                                <td style="text-align:center" height="10" class="qty">
                                                                    {{$rowresult->quantity}}
                                                                    {{-- <input type='hidden' name="quantity" class="form-control text-center quantity_item" id="quantity" readonly value="{{$rowresult->quantity}}">
                                                                    <input type='hidden' name="quantity_body" id="quantity{{$tableRow}}" readonly value="{{$rowresult->quantity}}"> --}}
                                                                </td>
                                                                @if(in_array($Header->request_type_id, [6,7]))
                                                                    <td style="text-align:center" class="rep_qty">{{$rowresult->replenish_qty ? $rowresult->replenish_qty : 0}}</td>  
                                                                    <td style="text-align:center" class="ro_qty">{{$rowresult->reorder_qty ? $rowresult->reorder_qty : 0}}</td>                                                           
                                                                    <td style="text-align:center" class="served_qty">{{$rowresult->serve_qty ? $rowresult->serve_qty : 0}}</td>
                                                                    <td style="text-align:center" class="unserved_qty">{{$rowresult->unserved_qty ? $rowresult->unserved_qty : 0}}</td>
                                                                    <td style="text-align:center" class="dr_qty">{{$rowresult->dr_qty ? $rowresult->dr_qty : 0}}</td> 
                                                                    <td style="text-align:center" class="po_qty">{{$rowresult->po_qty ? $rowresult->po_qty : 0}}</td>   
                                                                    <td style="text-align:center">{{$rowresult->mo_so_num}}</td>   
                                                                    <td style="text-align:center">{{$rowresult->po_no}}</td>  
                                                                    <td style="text-align:center" class="cancel_qty">{{$rowresult->cancelled_qty ? $rowresult->cancelled_qty : 0}}</td>   
                                                                    <td style="text-align:center">{{$rowresult->reason_to_cancel}}</td>
                                                                @endif
                                                          
                                                            </tr>
                                                        @else
                                                            <tr>
                                                                <td style="text-align:center" height="10">
                                                                        <input type="hidden"  class="form-control"  name="ids[]" id="ids{{$tableRow}}"  required  value="{{$rowresult->id}}">                               
                                                                        {{$rowresult->digits_code}}
                                                                </td>
                                                                <td style="text-align:center" height="10">
                                                                        <input type="hidden"  class="form-control"  name="ids[]" id="ids{{$tableRow}}"  required  value="{{$rowresult->id}}">                               
                                                                        {{$rowresult->item_description}}
                                                                </td>
                                                                <td style="text-align:center" height="10">
                                                                        {{$rowresult->category_id}}
                                                                </td>
                                                                <td style="text-align:center" height="10">
                                                                        {{$rowresult->sub_category_id}}
                                                                </td>
                                                                <td style="text-align:center" height="10" class="qty">
                                                                    {{$rowresult->quantity}}
                                                                        {{-- <input type='hidden' name="quantity" class="form-control text-center quantity_item" id="quantity" readonly value="{{$rowresult->quantity}}">
                                                                        <input type='hidden' name="quantity_body" id="quantity{{$tableRow}}" readonly value="{{$rowresult->quantity}}"> --}}
                                                                </td>
                                                                @if(in_array($Header->request_type_id, [6,7]))
                                                                    <td style="text-align:center" class="rep_qty">{{$rowresult->replenish_qty ? $rowresult->replenish_qty : 0}}</td>  
                                                                    <td style="text-align:center" class="ro_qty">{{$rowresult->reorder_qty ? $rowresult->reorder_qty : 0}}</td>                                                           
                                                                    <td style="text-align:center" class="served_qty">{{$rowresult->serve_qty ? $rowresult->serve_qty : 0}}</td>
                                                                    <td style="text-align:center" class="unserved_qty">{{$rowresult->unserved_qty ? $rowresult->unserved_qty : 0}}</td>
                                                                    <td style="text-align:center" class="dr_qty">{{$rowresult->dr_qty ? $rowresult->dr_qty : 0}}</td> 
                                                                    <td style="text-align:center" class="po_qty">{{$rowresult->po_qty ? $rowresult->po_qty : 0}}</td>   
                                                                    <td style="text-align:center">{{$rowresult->mo_so_num}}</td>   
                                                                    <td style="text-align:center">{{$rowresult->po_no}}</td>     
                                                                    <td style="text-align:center" class="cancel_qty">{{$rowresult->cancelled_qty ? $rowresult->cancelled_qty : 0}}</td>   
                                                                    <td style="text-align:center">{{$rowresult->reason_to_cancel}}</td>
                                                                @endif
                                                                                                                                          
                                                            </tr>
                                                        @endif
                                                    
                                                @endforeach     
                                                
                                                <input type='hidden' name="quantity_total" class="form-control text-center" id="quantity_total" readonly value="{{$Header->quantity_total}}">
                                            </tr>
                                        </tr>          
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($Header->recommendedby != null || $Header->recommendedby != "")
                <hr/>
                <div class="row">                           
                    <label class="control-label col-md-2">{{ trans('message.form-label.recommended_by') }}:</label>
                    <div class="col-md-4">
                            <p>{{$Header->recommendedby}} / <strong>{{$Header->recommended_at}}</strong> </p>
                    </div>
                    @if($Header->it_comments != null || $Header->it_comments != "")                        
                        <label class="control-label col-md-2">{{ trans('message.table.it_comments') }}:</label>
                        <div class="col-md-4">
                                <p>{{$Header->it_comments}}</p>
                        </div>
                    @endif 
                </div>
            @endif 
           
            <br>
            @if( $Header->processedby != null )
                <div class="row">
                    <div class="col-md-6">
                        <table style="width:100%">
                            <tbody id="footer">
                                @if($Header->request_type_id == 1 || $Header->request_type_id == 5)
                                    <tr>
                                        <th class="control-label col-md-2">{{ trans('message.form-label.po_number') }}:</th>
                                        <td class="col-md-4">{{$Header->po_number}}</td>     
                                    </tr>

                                    <tr>
                                        <th class="control-label col-md-2">{{ trans('message.form-label.po_date') }}:</th>
                                        <td class="col-md-4">{{$Header->po_date}}</td>
                                    </tr>

                                    <tr>
                                        <th class="control-label col-md-2">{{ trans('message.form-label.quote_date') }}:</th>
                                        <td class="col-md-4">{{$Header->quote_date}}</td>
                                    </tr>
                                @endif
                                @if( $Header->processedby != null )
                                    <tr>
                                        <th class="control-label col-md-2">{{ trans('message.form-label.processed_by') }}:</th>
                                        <td class="col-md-4">{{$Header->processedby}} / {{$Header->purchased2_at}}</td>
                                    </tr>
                                @endif
                               
                            </tbody>
                        </table>
                    </div>

                    <div class="col-md-6">
                        <table style="width:100%">
                            <tbody id="footer">
                                @if($Header->ac_comments != null)
                                    <tr>
                                        <th class="control-label col-md-2">{{ trans('message.table.ac_comments') }}:</th>
                                        <td class="col-md-4">{{$Header->ac_comments}}</td>
                                    </tr>
                                @endif
                                @if( $Header->pickedby != null )
                                    <tr>
                                        <th class="control-label col-md-2">{{ trans('message.form-label.picked_by') }}:</th>
                                        <td class="col-md-4">{{$Header->pickedby}} / {{$Header->picked_at}}</td>
                                    </tr>
                                @endif
                                @if( $Header->receivedby != null )
                                    <tr>
                                        <th class="control-label col-md-2">{{ trans('message.form-label.received_by') }}:</th>
                                        <td class="col-md-4">{{$Header->receivedby}} / {{$Header->received_at}}</td>
                                    </tr>
                                @endif
                                @if( $Header->closedby != null )
                                    <tr>
                                        <th class="control-label col-md-2">{{ trans('message.form-label.closed_by') }}:</th>
                                        <td class="col-md-4">{{$Header->closedby}} / {{$Header->closed_at}}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <div class='panel-footer'>

            <a href="{{ CRUDBooster::mainpath() }}" class="btn btn-default">{{ trans('message.form.back') }}</a>
            <button class="btn btn-success pull-right" type="submit" id="btnEditSubmit"> Receive</button>
        </div>

    </form>



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

    $('#btnEditSubmit').click(function() {
        event.preventDefault();
        swal({
            title: "Are you sure?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#41B314",
            cancelButtonColor: "#F9354C",
            confirmButtonText: "Yes, receive it!",
            width: 450,
            height: 200
            }, function () {
                $(this).attr('disabled','disabled');
                $('#myform').submit();                                                  
        });
    });

    var tableRow = <?php echo json_encode($tableRow); ?>;
    
        if($('#request_type_id').val() == 1){
            var tds = document
            .getElementById("asset-items1")
            .getElementsByTagName("td");
            var sumqty       = 0;
            var rep_qty      = 0;
            var ro_qty       = 0;
            var served_qty   = 0;
            var unserved_qty = 0;
            var dr_qty       = 0;
            var po_qty       = 0;
            for (var i = 0; i < tds.length; i++) {
                if(tds[i].className == "qty") {
                    sumqty += isNaN(tds[i].innerHTML) ? 0 : parseFloat(tds[i].innerHTML);
                }else if(tds[i].className == "rep_qty"){
                    rep_qty += isNaN(tds[i].innerHTML) ? 0 : parseFloat(tds[i].innerHTML);
                }else if(tds[i].className == "ro_qty"){
                    ro_qty += isNaN(tds[i].innerHTML) ? 0 : parseFloat(tds[i].innerHTML);
                }else if(tds[i].className == "served_qty"){
                    served_qty += isNaN(tds[i].innerHTML) ? 0 : parseFloat(tds[i].innerHTML);
                }else if(tds[i].className == "unserved_qty"){
                    unserved_qty += isNaN(tds[i].innerHTML) ? 0 : parseFloat(tds[i].innerHTML);
                }else if(tds[i].className == "dr_qty"){
                    dr_qty += isNaN(tds[i].innerHTML) ? 0 : parseFloat(tds[i].innerHTML);
                }else if(tds[i].className == "po_qty"){
                    po_qty += isNaN(tds[i].innerHTML) ? 0 : parseFloat(tds[i].innerHTML);
                }
            }
            document.getElementById("asset-items1").innerHTML +=
            "<tr>"+
                "<td colspan='4' style='text-align:right'>"+
                        "<strong>TOTAL</strong>"+
                    "</td>"+
                    
                    "<td style='text-align:center'>"+
                        "<strong>" +
                        sumqty +
                        "</strong>"+
                    "</td>"+

                    "<td style='text-align:center'>"+
                    
                    "</td>"+
                    
            "</tr>";
        }else{
            var tds = document.getElementById("asset-items1").getElementsByTagName("td");
            var sumqty       = 0;
            var rep_qty      = 0;
            var ro_qty       = 0;
            var served_qty   = 0;
            var unserved_qty = 0;
            var dr_qty       = 0;
            var po_qty       = 0;
            var cancel_qty   = 0;
            for (var i = 0; i < tds.length; i++) {
                if(tds[i].className == "qty") {
                    sumqty += isNaN(tds[i].innerHTML) ? 0 : parseFloat(tds[i].innerHTML);
                }else if(tds[i].className == "rep_qty"){
                    rep_qty += isNaN(tds[i].innerHTML) ? 0 : parseFloat(tds[i].innerHTML);
                }else if(tds[i].className == "ro_qty"){
                    ro_qty += isNaN(tds[i].innerHTML) ? 0 : parseFloat(tds[i].innerHTML);
                }else if(tds[i].className == "served_qty"){
                    served_qty += isNaN(tds[i].innerHTML) ? 0 : parseFloat(tds[i].innerHTML);
                }else if(tds[i].className == "unserved_qty"){
                    unserved_qty += isNaN(tds[i].innerHTML) ? 0 : parseFloat(tds[i].innerHTML);
                }else if(tds[i].className == "dr_qty"){
                    dr_qty += isNaN(tds[i].innerHTML) ? 0 : parseFloat(tds[i].innerHTML);
                }else if(tds[i].className == "po_qty"){
                    po_qty += isNaN(tds[i].innerHTML) ? 0 : parseFloat(tds[i].innerHTML);
                }else if(tds[i].className == "cancel_qty"){
                    cancel_qty += isNaN(tds[i].innerHTML) ? 0 : parseFloat(tds[i].innerHTML);
                }
            }
            document.getElementById("asset-items1").innerHTML +=
            "<tr>"+
                "<td colspan='4' style='text-align:right'>"+
                        "<strong>TOTAL</strong>"+
                    "</td>"+
                    
                    "<td style='text-align:center'>"+
                        "<strong>" +
                        sumqty +
                        "</strong>"+
                    "</td>"+
                    "<td style='text-align:center'>"+
                        "<strong>" +
                        rep_qty +
                        "</strong>"+
                    "</td>"+
                    "<td style='text-align:center'>"+
                        "<strong>" +
                        ro_qty +
                        "</strong>"+
                    "</td>"+
                    "<td style='text-align:center'>"+
                        "<strong>" +
                        served_qty +
                        "</strong>"+
                    "</td>"+
                    "<td style='text-align:center'>"+
                        "<strong>" +
                        unserved_qty +
                        "</strong>"+
                    "</td>"+
                    "<td style='text-align:center'>"+
                        "<strong>" +
                            dr_qty +
                        "</strong>"+
                    "</td>"+
                    "<td style='text-align:center'>"+
                        "<strong>" +
                            po_qty +
                        "</strong>"+
                    "</td>"+
                    "<td style='text-align:center'>"+
                    "</td>"+
                    "<td style='text-align:center'>"+
                    "</td>"+
                    "<td style='text-align:center'>"+
                    "<strong>" +
                        cancel_qty +
                    "</strong>"+
                    "</td>"+  
                    "<td style='text-align:center'>"+
                    "</td>"+
            "</tr>";
        }
       
        
</script>
@endpush