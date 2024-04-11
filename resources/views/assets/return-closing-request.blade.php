@extends('crudbooster::admin_template')
@section('content')
    @push('head')
        <style type="text/css">   
            #asset-items th, td, tr {
                border: 1px solid rgba(000, 0, 0, .5);
                padding: 8px;
            }
            table, th, td {
            border: 1px solid rgba(000, 0, 0, .5);
            padding: 8px;
            }
            #asset-items th, td, tr {
                border: 1px solid rgba(000, 0, 0, .5);
                padding: 8px;
            }
        </style>
    @endpush
@if(g('return_url'))
	<p class="noprint"><a title='Return' href='{{g("return_url")}}'><i class='fa fa-chevron-circle-left '></i> &nbsp; {{trans("crudbooster.form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}}</a></p>       
@else
	<p class="noprint"><a title='Main Module' href='{{CRUDBooster::mainpath()}}'><i class='fa fa-chevron-circle-left '></i> &nbsp; {{trans("crudbooster.form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}}</a></p>       
@endif
<div class='panel panel-default'>
    <div class='panel-heading'>
        Closing Return/Transfer Form
    </div>

    <form method='post' id="myform" action='{{CRUDBooster::mainpath('edit-save/'.$Header->requestid)}}'>
        <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
        <input type="hidden" value="" name="approval_action" id="approval_action">

        <div class='panel-body'>
            <div class="row">
                <div class="col-md-6" style="margin: 0; padding:0">  
                    <div class="form-group">
                        <label class="control-label col-md-3">{{ trans('message.form-label.employee_name') }}:</label>
                        <p>{{$Header->employee_name}}</p>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-md-3">{{ trans('message.form-label.created_at') }}:</label>
                        <p>{{$Header->requested_date}}</p>
                    </div>
    
                    <div class="form-group">
                        <label class="control-label col-md-3">{{ trans('message.form-label.company_name') }}:</label>
                        <p>{{$Header->company}}</p>
                    </div>
    
                    <div class="form-group">
                        <label class="control-label col-md-3">{{ trans('message.form-label.department') }}:</label>
                        <p>{{$Header->department_name}}</p>
                    </div>
    
                    <div class="form-group">
                        <label class="control-label col-md-3">{{ trans('message.form-label.position') }}:</label>
                        <p>{{$Header->position}}</p>
                    </div>
    
                    @if($Header->store_branch != null || $Header->store_branch != "")
                    <label class="control-label col-md-2">{{ trans('message.form-label.store_branch') }}:</label>
                        
                    <p>{{$Header->store_branch}}</p>
                        
                    @endif
                </div>                          
                <div class="col-md-6">
                    <table style="width:100%">
                        <tbody id="footer">
                            @if($Header->request_type_id == 1)
                                <tr>
                                    <th class="control-label col-md-3">Location:</th>
                                    <td>
                                        @if($Header->location_pick != null || $Header->location_pick != "")
                                        {{$Header->location_pick}}
                                        @endif
                                    </td>                              
                                </tr>
                            @endif
                            <tr>
                                <th class="control-label col-md-3">Pick up by:</th>
                                <td>
                                   @if($Header->pick_up_by != null || $Header->pick_up_by != "")
                                    {{$Header->pick_up_by}}
                                   @endif
                                </td>                              
                            </tr>
                            @if($Header->hand_carry_name != null || $Header->hand_carry_name != "")
                                <tr>
                                    <th class="control-label col-md-3">Hand Carrier:</th>
                                    <td>
                                        {{$Header->hand_carry_name}}
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <th class="control-label col-md-3">Schedule date:</th>
                                <td>
                                   @if($Header->schedule_at != null || $Header->schedule_at != "")
                                    {{$Header->schedule_at}}
                                   @endif
                                </td>                              
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
         
            <hr>
            <div class="row">
                <label class="control-label col-md-2">Request type:</label>
                <div class="col-md-4">
                        <p>{{$Header->request_type}}</p>
                </div> 
                @if($Header->transfer_to != null)    
                    <label class="control-label col-md-2">Reason:</label>
                    <div class="col-md-4">
                            <p>{{$Header->purpose}}</p>
                    </div>                    
                    @else
                    <label class="control-label col-md-2">Reason:</label>
                    <div class="col-md-4">
                            <p>{{$Header->purpose}}</p>
                    </div>
                @endif
            </div> 

            <hr/>
    
            <table  class='table' id="asset-items">
                <thead>
                    <tr style="background-color:#00a65a; border: 0.5px solid #000;">
                        <th style="text-align: center" colspan="11"><h4 class="box-title" style="color: #fff;"><b>{{ trans('message.form-label.asset_items') }}</b></h4></th>
                    </tr>
                    <tr>
                        <th width="15%" class="text-center">Reference No</th>
                        <th width="15%" class="text-center">Asset Code</th>
                        <th width="15%" class="text-center">Digits Code</th>
                        <th width="10%" class="text-center">Serial No</th>
                        <th width="25%" class="text-center">{{ trans('message.table.item_description') }}</th>
                        <th width="20%" class="text-center">Asset Type</th>   
                        <th width="25%" class="text-center">Cost</th>                                                          
                    </tr>
                </thead>
                <tbody>
                    @foreach($return_body as $rowresult)
                        <tr>
                            <td style="text-align:center" height="10">{{$rowresult->reference_no}}</td>
                            <td style="text-align:center" height="10">{{$rowresult->asset_code}}</td>
                            <td style="text-align:center" height="10">{{$rowresult->digits_code}}</td>
                            <td style="text-align:center" height="10">{{$rowresult->serial_no}}</td>
                            <td style="text-align:center" height="10">{{$rowresult->description}}</td>
                            <td style="text-align:center" height="10">{{$rowresult->asset_type}}</td>
                            <td style="text-align:center" height="10" class="unit_cost">{{$rowresult->unit_cost}}</td>               
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="6" style="text-align: center;"><b>Total</b></td>
                        <td class="text-center"><span id="unitCost">0</span></td>
                    </tr>
                </tbody>
                
            </table> 
            <hr/>
            <div class="row">
                <div class="col-md-6">
                    <table style="width:100%">
                        <tbody id="footer">
                            @if($Header->approvedby != null || $Header->approvedby != "")
                                <tr>
                                    <th class="control-label col-md-2">{{ trans('message.form-label.approved_by') }}:</th>
                                    <td class="col-md-4">{{$Header->approvedby}} / {{$Header->approved_date}}</td>
                                </tr>
                            @endif
                            @if($Header->approver_comments != null || $Header->approver_comments != "")
                                <tr>
                                    <th class="control-label col-md-2">{{ trans('message.table.approver_comments') }}:</th>
                                    <td class="col-md-4">{{$Header->approver_comments}}</td>
                                </tr>
                            @endif
                            @if($Header->verifiedby != null || $Header->verifiedby != "")
                                <tr>
                                    <th class="control-label col-md-2">Verified By:</th>
                                    <td class="col-md-4">{{$Header->verifiedby}} / {{$Header->transacted_date}}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <table style="width:100%">
                        <tbody id="footer">
                            @if($Header->receivedby != null || $Header->receivedby != "")
                                <tr>
                                    <th class="control-label col-md-2">Received By:</th>
                                    <td class="col-md-4">{{$Header->receivedby}} / {{$Header->transacted_date}}</td>
                                </tr>
                            @endif
                            @if($Header->closedby != null || $Header->closedby != "")
                            <tr>
                                <th class="control-label col-md-2">Closed By:</th>
                                <td class="col-md-4">{{$Header->closedby}} / {{$Header->close_at}}</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
    
        </div>


        <div class='panel-footer'>
            <a href="{{ CRUDBooster::mainpath() }}" class="btn btn-default">{{ trans('message.form.cancel') }}</a>
           
            <button class="btn btn-success pull-right" type="submit" id="btnSubmit"> <i class="fa fa-save" ></i> {{ trans('message.form.closing') }}</button>
        </div>

    </form>



</div>

@endsection
@push('bottom')
<script type="text/javascript">

    function preventBack() {
        window.history.forward();
    }
     window.onunload = function() {
        null;
    };
    setTimeout("preventBack()", 0);

    $(document).ready(function() {
        $('#unitCost').text(calculateUnitCost().toFixed(2));
    });

    $('#btnSubmit').click(function(event) {
        event.preventDefault();
        swal({
            title: "Are you sure?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#41B314",
            cancelButtonColor: "#F9354C",
            confirmButtonText: "Yes, close it!",
            width: 450,
            height: 200
            }, function () {
                $(this).attr('disabled','disabled');
                $('#myform').submit();                                                  
        });

    });

    function calculateUnitCost() {
        let totalQuantity = 0;
        $('.unit_cost').each(function() {
            let qty = 0;
            if($(this).text().trim()) {
                qty = parseInt($(this).text().replace(/,/g, ''));
            }

            totalQuantity += qty;
        });
        return totalQuantity;
    }

</script>
@endpush