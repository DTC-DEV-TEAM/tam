@extends('crudbooster::admin_template')
    @push('head')
        <style type="text/css">   
            #approval-table th, td, tr {
                border: 1px solid rgba(000, 0, 0, .5);
                padding: 8px;
                border-radius: 5px 0 0 5px;
            }
            .finput {
                border:none;
                border-bottom: 1px solid rgba(18, 17, 17, 0.5);
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
        Request Form
    </div>

    <form method='post' id="myform" action='{{CRUDBooster::mainpath('edit-save/'.$Header->requestid)}}'>
        <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
        <input type="hidden" value="" name="approval_action" id="approval_action">
        <input type="hidden" value="{{$Header->request_type_id}}"  id="request_type_id">
        
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
                            <p>{{$Header->store_branch}}</p>
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

            <hr/>                
            
            <div class="box-header text-center">
                <h3 class="box-title"><b>{{ trans('message.form-label.asset_items') }}</b></h3>
            </div>

            <table id="approval-table">
                <thead>
                    <tr>
                        <th width="10%" class="text-center">{{ trans('message.table.digits_code') }}</th>
                        <th width="20%" class="text-center">{{ trans('message.table.item_description') }}</th>
                        <th width="10%" class="text-center">{{ trans('message.table.category_id_text') }}</th>                                                         
                        <th width="10%" class="text-center">{{ trans('message.table.sub_category_id_text') }}</th> 
                        
                        <th width="5%" class="text-center">WH Qty</th>  
                      
                        <th width="5%" class="text-center">{{ trans('message.table.quantity_text') }}</th> 
                           
                        <th width="5%" class="text-center">For Replenish Qty</th> 
                        <th width="5%" class="text-center">For Re Order Qty</th> 
                        <th width="5%" class="text-center">Serve Qty</th> 
                        <th width="5%" class="text-center">UnServe Qty</th> 
                        <th width="7%" class="text-center">Item Cost</th> 
                        <th width="7%" class="text-center">Total Cost</th>                                                                                                                                            
                        <th width="10%" class="text-center">MO/SO</th>                                                  
                       
                    </tr>
                </thead>
                <tbody>
                    @foreach($Body as $rowresult)
                        <tr>
                            <input type="hidden" value="{{$rowresult->id}}" name="body_ids[]">
                            <input type="hidden" value="{{$rowresult->wh_qty}}" name="wh_qty[]">
                            <input type="hidden" value="{{$rowresult->available_qty}}" name="it_wh_qty[]">
                            <td style="text-align:center">{{$rowresult->digits_code}}</td>
                            <td style="text-align:center">{{$rowresult->item_description}}</td>
                            <td style="text-align:center">{{$rowresult->category_id}}</td>
                            <td style="text-align:center">{{$rowresult->sub_category_id}}</td>

                            @if(in_array($Header->request_type_id, [6,7]))  
                                <td style="text-align:center" class="wh_qty">{{$rowresult->wh_qty ? $rowresult->wh_qty : 0}}</td>
                            @else
                                <td style="text-align:center" class="wh_qty">{{$rowresult->available_qty ? $rowresult->available_qty : 0}}</td>
                            @endif 
                            <td style="text-align:center" class="qty">{{$rowresult->quantity}}</td>
                            
                            <td style="text-align:center" class="rep_qty">{{$rowresult->replenish_qty ? $rowresult->replenish_qty : 0}}</td>  
                            <td style="text-align:center" class="ro_qty">{{$rowresult->reorder_qty ? $rowresult->reorder_qty : 0}}</td>                                                           
                            <td style="text-align:center" class="served_qty">{{$rowresult->serve_qty ? $rowresult->serve_qty : 0}}</td>
                            <td style="text-align:center" class="unserved_qty">{{$rowresult->unserved_qty ? $rowresult->unserved_qty : 0}}</td>
                            <td style="text-align:center" class="unit_cost">{{$rowresult->unit_cost ? $rowresult->unit_cost : 0}}</td>
                            <td style="text-align:center" class="total_cost">{{$rowresult->unit_cost * $rowresult->serve_qty}}</td>
                            <td style="text-align:center" height="10">{{$rowresult->mo_so_num}}</td>   
                                
                           
                        </tr>
                    @endforeach

                    {{-- <tr>
                        <td colspan="5" style="text-align:right">
                            <label>{{ trans('message.table.total_quantity') }}:</label>
                        </td>

                        <td style="text-align:center">
                            <label>{{$Header->quantity_total}}</label>
                        </td>
                    </tr> --}}
                </tbody>
                
            </table> 
            <br><hr>

            @if($Header->application != null || $Header->application != "")
                <div class="row">                           
                    <label class="control-label col-md-2">{{ trans('message.form-label.application') }}:</label>
                    <div class="col-md-4">
                            <p>{{$Header->application}}</p>
                    </div>

                    @if($Header->application_others != null || $Header->application_others != "")
                        <label class="control-label col-md-2">{{ trans('message.form-label.application_others') }}:</label>
                        <div class="col-md-4">
                                <p>{{$Header->application_others}}</p>
                        </div>
                    @endif  
                </div>
            @endif  


            @if($Header->requestor_comments != null || $Header->requestor_comments != "")
                <div class="row">                           
                    <label class="control-label col-md-2">Requestor Comment:</label>
                    <div class="col-md-10">
                            <p>{{$Header->requestor_comments}}</p>
                    </div>

            
                </div>
            @endif  

            <hr/>
            <div class="row">  
                <div class="col-md-12">
                    <div class="form-group">
                        <label>{{ trans('message.table.comments') }}:</label>
                        <textarea placeholder="{{ trans('message.table.comments') }} ..." rows="3" class="form-control finput" name="approver_comments">{{$Header->approver_comments}}</textarea>
                    </div>
                </div>
            </div>

        </div>


        <div class='panel-footer'>
            <a href="{{ CRUDBooster::mainpath() }}" class="btn btn-default">{{ trans('message.form.cancel') }}</a>
           
            <button class="btn btn-danger pull-right" type="button" id="btnReject" style="margin-left: 5px;"><i class="fa fa-thumbs-down" ></i> Reject</button>
            <button class="btn btn-success pull-right" type="button" id="btnApprove"><i class="fa fa-thumbs-up" ></i> Approve</button>
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

    $('#btnApprove').click(function(event) {
        event.preventDefault();
        swal({
            title: "Are you sure?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#41B314",
            cancelButtonColor: "#F9354C",
            confirmButtonText: "Yes, approve it!",
            width: 450,
            height: 200
            }, function () {
                $(this).attr('disabled','disabled');
                $('#approval_action').val('1');
                $("#myform").submit();                   
        });
    });

    $('#btnReject').click(function(event) {
        event.preventDefault();
        swal({
            title: "Are you sure?",
            type: "warning",
            text: "You won't be able to revert this!",
            showCancelButton: true,
            confirmButtonColor: "#41B314",
            cancelButtonColor: "#F9354C",
            confirmButtonText: "Yes, reject it!",
            width: 450,
            height: 200
            }, function () {
                $(this).attr('disabled','disabled');
                $('#approval_action').val('0');
                $("#myform").submit();                   
        });
        
    });

   
    var tds = document.getElementById("approval-table").getElementsByTagName("td");
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
    document.getElementById("approval-table").innerHTML +=
    "<tr>"+
        "<td colspan='5' style='text-align:right'>"+
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
        


</script>
@endpush