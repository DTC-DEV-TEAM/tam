@extends('crudbooster::admin_template')
@push('head')
        <style type="text/css">   
            table, th, td {
            border: 1px solid rgba(000, 0, 0, .5);
            padding: 8px;
            /* border-radius: 5px 0 0 5px; */
            }
            #asset-items1, td, tr {
                border: 1px solid rgba(000, 0, 0, .5);
                padding: 8px;
            }
            #asset-items, td, tr {
                border: 1px solid rgba(000, 0, 0, .5);
                padding: 8px;
            }
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
        Detail Form
    </div>

    <form method='post' id="myform" action='{{CRUDBooster::mainpath('edit-save/'.$Header->requestid)}}'>
        <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
        <input type="hidden" value="0" name="action" id="action">

        <input type="hidden" value="{{$Header->requestid}}" name="headerID" id="headerID">

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
                        <p>{{$Header->employee_name}}</p>
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

            @if($Header->store_branch != null || $Header->store_branch != "")
                <div class="row">                           
                    <label class="control-label col-md-2">{{ trans('message.form-label.store_branch') }}:</label>
                    <div class="col-md-4">
                            <p>{{$Header->store_name}}</p>
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
                        <div class="table-responsive">
                            <div class="pic-container">
                                <div class="pic-row">
                                    <table id="asset-items1">
                                        <tbody id="bodyTable">
                                            <tr class="tbl_header_color dynamicRows">
                                                <th width="10%" class="text-center">{{ trans('message.table.digits_code') }}</th>
                                                <th width="15%" class="text-center">{{ trans('message.table.item_description') }}</th>
                                                <th width="9%" class="text-center">{{ trans('message.table.category_id_text') }}</th>                                                         
                                                <th width="10%" class="text-center">{{ trans('message.table.sub_category_id_text') }}</th>
                                                <th width="5%" class="text-center">{{ trans('message.table.quantity_text') }}</th> 
                                            
                                             
                                                <th width="5%" class="text-center">For Replenish Qty</th> 
                                                <th width="5%" class="text-center">For Re Order Qty</th> 
                                                <th width="5%" class="text-center">Serve Qty</th> 
                                                <th width="5%" class="text-center">UnServe Qty</th> 
                                                <th width="7%" class="text-center">Item Cost</th> 
                                                <th width="7%" class="text-center">Total Cost</th>                                                                                                                                            
                                                <th width="10%" class="text-center">MO/SO</th>    
                                                <th width="5%" class="text-center">Cancelled Qty</th> 
                                                <th>Reason</th>                                                 
                                          
                                                @if($Header->recommendedby != null || $Header->recommendedby != "")
                                                    <th width="13%" class="text-center">{{ trans('message.table.recommendation_text') }}</th> 
                                                    <th width="14%" class="text-center">{{ trans('message.table.reco_digits_code_text') }}</th> 
                                                    <th width="24%" class="text-center">{{ trans('message.table.reco_item_description_text') }}</th>
                                                @endif 
                                               <th>Action</th>
                                                   
                                             
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
                                                                        
                                                                </td>
                                                                
                                                                <td style="text-align:center" class="rep_qty">{{$rowresult->replenish_qty ? $rowresult->replenish_qty : 0}}</td>  
                                                                <td style="text-align:center" class="ro_qty">{{$rowresult->reorder_qty ? $rowresult->reorder_qty : 0}}</td>                                                           
                                                                <td style="text-align:center" class="served_qty">{{$rowresult->serve_qty ? $rowresult->serve_qty : 0}}</td>
                                                                <td style="text-align:center" class="unserved_qty">{{$rowresult->unserved_qty ? $rowresult->unserved_qty : 0}}</td>
                                                                <td style="text-align:center" class="unit_cost" height="10">{{$rowresult->unit_cost ? $rowresult->unit_cost  : 0}}</td>
                                                                <td style="text-align:center" height="10" class="total_cost">{{$rowresult->unit_cost * $rowresult->serve_qty}}</td>
                                                                <td style="text-align:center" height="10">{{$rowresult->mo_so_num}}</td>   
                                                                <td style="text-align:center" class="po_qty">{{$rowresult->cancelled_qty ? $rowresult->cancelled_qty : 0}}</td>   
                                                                <td style="text-align:center">{{$rowresult->reason_to_cancel}}</td>
                                                             
                                                                
                                                                @if($Header->recommendedby != null || $Header->recommendedby != "")                                                                               
                                                                    <td style="text-align:center" height="10">
                                                                        {{$rowresult->recommendation}}
                                                                    </td>                                                                                  
                                                                    <td style="text-align:center" height="10">
                                                                        {{$rowresult->reco_digits_code}}
                                                                    </td>
                                                                    <td style="text-align:center" height="10">
                                                                        {{$rowresult->reco_item_description}}
                                                                    </td>
                                                                @endif
                                                                <td  style="text-align:center; color:#fff"><i class="fa fa-times-circle"></i></td>
                                                                    {{-- <td style="text-align:center" height="10">
                                                                        <button id="deleteRow{{$tableRow}}" name="removeRow" data-id="{{$tableRow}}" class="btn btn-danger removeRow" disabled><i class="glyphicon glyphicon-remove"></i></button>
                                                                    </td>                  --}}
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
                                                                       
                                                                </td>
                                                              
                                                                <td style="text-align:center" class="rep_qty">{{$rowresult->replenish_qty ? $rowresult->replenish_qty : 0}}</td>  
                                                                <td style="text-align:center" class="ro_qty">{{$rowresult->reorder_qty ? $rowresult->reorder_qty : 0}}</td>                                                           
                                                                <td style="text-align:center" class="served_qty">{{$rowresult->serve_qty ? $rowresult->serve_qty : 0}}</td>
                                                                <td style="text-align:center" class="unserved_qty">{{$rowresult->unserved_qty ? $rowresult->unserved_qty : 0}}</td>
                                                                <td style="text-align:center" class="unit_cost" height="10">{{$rowresult->unit_cost ? $rowresult->unit_cost  : 0}}</td>
                                                                <td style="text-align:center" class="total_cost " height="10" class="cost">{{$rowresult->unit_cost * $rowresult->serve_qty}}</td>
                                                                <td style="text-align:center" height="10">{{$rowresult->mo_so_num}}</td>   
                                                                <td style="text-align:center" class="po_qty">{{$rowresult->cancelled_qty ? $rowresult->cancelled_qty : 0}}</td>   
                                                                <td style="text-align:center">{{$rowresult->reason_to_cancel}}</td>
                                                              
                                                                @if($Header->recommendedby != null || $Header->recommendedby != "")                                                                               
                                                                    <td style="text-align:center" height="10">
                                                                        {{$rowresult->recommendation}}
                                                                    </td>                                                                                  
                                                                    <td style="text-align:center" height="10">
                                                                        {{$rowresult->reco_digits_code}}
                                                                    </td>
                                                                    <td style="text-align:center" height="10">
                                                                        {{$rowresult->reco_item_description}}
                                                                    </td>
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

            @if( $MoveOrder->count() != 0 )
                <hr />
                <div class="row">
                    <div class="col-md-12">
                        <div class="box-header text-center">
                            <h3 class="box-title"><b>{{ trans('message.form-label.asset_items') }}</b></h3>
                        </div>
                        <div class="box-body no-padding">
                            <div class="table-responsive">
                                <div class="pic-container">
                                    <div class="pic-row">
                                        <table id="asset-items">
                                            <tbody>
                                                <tr class="tbl_header_color dynamicRows">
                                                    <th width="10%" class="text-center">{{ trans('message.table.mo_reference_number') }}</th>
                                                    <th width="13%" class="text-center">{{ trans('message.table.status_id') }}</th>
                                                    <th width="10%" class="text-center">{{ trans('message.table.digits_code') }}</th>
                                                    <th width="10%" class="text-center">{{ trans('message.table.asset_tag') }}</th>
                                                    <th width="26%" class="text-center">{{ trans('message.table.item_description') }}</th>
                                                    <th width="13%" class="text-center">{{ trans('message.table.serial_no') }}</th>
                                                    <th width="4%" class="text-center">{{ trans('message.table.item_quantity') }}</th>
                                                    <th width="8%" class="text-center">{{ trans('message.table.item_cost') }}</th>
                                                    <th width="16%" class="text-center">{{ trans('message.table.item_total_cost') }}</th>         
                                                </tr>
                                                <?php   $tableRow1 = 0; ?>
                                                @if( !empty($MoveOrder) )
                                                    @foreach($MoveOrder as $rowresult)
                                                        <?php   $tableRow1++; ?>
                                                        <tr>
                                                            <td style="text-align:center" height="10">

                                                                            <input type="hidden" value="{{$rowresult->id}}" name="item_id[]">

                                                                            {{$rowresult->mo_reference_number}}
                                                                            
                                                            </td>
                                                            <td style="text-align:center" height="10">

                                                                            <label style="color: #3c8dbc;">
                                                                                {{$rowresult->status_description}}
                                                                            </label>
                                                                        

                                                            </td>
                                                            <td style="text-align:center" height="10">
                                                                            {{$rowresult->digits_code}}
                                                            </td>
                                                            <td style="text-align:center" height="10">
                                                                            {{$rowresult->asset_code}}
                                                            </td>
                                                            <td style="text-align:center" height="10">
                                                                            {{$rowresult->item_description}}
                                                            </td>
                                                            <td style="text-align:center" height="10">
                                                                            {{$rowresult->serial_no}}
                                                            </td>
                                                            <td style="text-align:center" height="10" class="qty">
                                                                            {{$rowresult->quantity}}
                                                            </td>
                                                            <td style="text-align:center" height="10" class="unit_cost">
                                                                            {{$rowresult->unit_cost}}
                                                            </td>
                                                            <td style="text-align:center" height="10" class="total_cost">
                                                                            {{$rowresult->total_unit_cost}}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif       
                                                {{-- <tr class="tableInfo">
                                                    <td colspan="8" align="right"><strong>{{ trans('message.table.total') }}</strong></td>
                                                    <td align="center" colspan="1">
                                                        <label>{{$Header->total}}</label>
                                                    </td>
                                                    <td colspan="1"></td>
                                                </tr> --}}
        
                                            </tbody>
                                        </table>
                                    </div>
                                </div> 
                            </div>
                        </div>
                    </div>
                </div> 
            @endif
            <hr>
            @if( $Header->processedby != null )
                <div class="row">
                    <div class="col-md-6">
                        <table style="width:100%">
                            <tbody id="footer">
                                <tr>
                                    <th class="control-label col-md-2">{{ trans('message.form-label.mo_by') }}:</th>
                                    <td class="col-md-4">{{$Header->mo_by}} / {{$Header->mo_at}}</td>     
                                </tr>
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
                            </tbody>
                        </table>
                    </div>

                    <div class="col-md-6">
                        <table style="width:100%">
                            <tbody id="footer">
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

            <a href="{{ CRUDBooster::mainpath() }}" class="btn btn-default">{{ trans('message.form.cancel') }}</a>

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
    
    var tds = document.getElementById("asset-items1").getElementsByTagName("td");
    var sumqty       = 0;
    var rep_qty      = 0;
    var ro_qty       = 0;
    var served_qty   = 0;
    var unserved_qty = 0;
    var dr_qty       = 0;
    var po_qty       = 0;
    var unit_cost    = 0;
    var total_cost   = 0;
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
        }else if(tds[i].className == "unit_cost"){
            unit_cost += isNaN(tds[i].innerHTML) ? 0 : parseFloat(tds[i].innerHTML);
        }else if(tds[i].className == "total_cost"){
            total_cost += isNaN(tds[i].innerHTML) ? 0 : parseFloat(tds[i].innerHTML);
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
                    unit_cost +
                "</strong>"+
            "</td>"+
            "<td style='text-align:center'>"+
                "<strong>" +
                    total_cost +
                "</strong>"+
            "</td>"+
            "<td style='text-align:center'>"+
            "</td>"+
    "</tr>";

    var tds = document.getElementById("asset-items").getElementsByTagName("td");
    var qty            = 0;
    var unit_cost      = 0;
    var total_cost     = 0;
    for (var i = 0; i < tds.length; i++) {
        if(tds[i].className == "qty") {
            qty += isNaN(tds[i].innerHTML) ? 0 : parseFloat(tds[i].innerHTML);
        }else if(tds[i].className == "unit_cost"){
            unit_cost += isNaN(tds[i].innerHTML) ? 0 : parseFloat(tds[i].innerHTML);
        }else if(tds[i].className == "total_cost"){
            total_cost += isNaN(tds[i].innerHTML) ? 0 : parseFloat(tds[i].innerHTML);
        }
    }
    document.getElementById("asset-items").innerHTML +=
    "<tr>"+
        "<td colspan='6' style='text-align:right'>"+
                "<strong>TOTAL</strong>"+
            "</td>"+
            
            "<td style='text-align:center'>"+
                "<strong>" +
                    qty +
                "</strong>"+
            "</td>"+
            "<td style='text-align:center'>"+
                "<strong>" +
                    unit_cost +
                "</strong>"+
            "</td>"+
            "<td style='text-align:center'>"+
                "<strong>" +
                    total_cost +
                "</strong>"+
            "</td>"+
    "</tr>";
</script>
@endpush