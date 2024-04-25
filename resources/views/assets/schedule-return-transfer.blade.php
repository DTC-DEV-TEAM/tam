@extends('crudbooster::admin_template')
@section('content')
@push('head')
    <style type="text/css">   
        #asset-items th, td, tr {
            border: 1px solid rgba(000, 0, 0, .5);
            padding: 8px;
        }
        .finput {
            border:none;
            border-bottom: 1px solid rgba(18, 17, 17, 0.5);
        }
        #footer th, td {
            border: 1px solid rgba(000, 0, 0, .5);
            padding: 8px;
            /* border-radius: 5px 0 0 5px; */
        }
        .select2-selection__choice{
                font-size:14px !important;
                color:black !important;
        }
        .select2-selection__rendered {
            line-height: 31px !important;
        }
        .select2-container .select2-selection--single {
            height: 35px !important;
        }
        .select2-selection__arrow {
            height: 34px !important;
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
        Request Form
    </div>

    <form method='post' id="myform" action='{{CRUDBooster::mainpath('edit-save/'.$Header->requestid)}}'>
        <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
        <input type="hidden" value="" name="approval_action" id="approval_action">
        <input type="hidden" name="header_id" id="header_id" value="{{$Header->id}}">

        <div class='panel-body'>

            <div class="row"> 
                <div class="col-md-6">  
                    <div class="form-group">
                        <label class="control-label col-md-4">{{ trans('message.form-label.employee_name') }}:</label>
                        <p>{{$Header->employee_name}}</p>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-md-4">{{ trans('message.form-label.created_at') }}:</label>
                        <p>{{$Header->requested_date}}</p>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-4">{{ trans('message.form-label.company_name') }}:</label>
                        <p>{{$Header->company}}</p>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-4">{{ trans('message.form-label.department') }}:</label>
                        <p>{{$Header->department_name}}</p>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-4">{{ trans('message.form-label.position') }}:</label>
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
                            @if(in_array($Header->request_type_id, [1,5]))
                                <tr>
                                    <th class="control-label col-md-3">Location:</th>
                                    <td>
                                        <select class="users" data-placeholder="Choose location"  style="width: 100%;" name="location_to_pick" id="location_to_pick">
                                            <option value=""></option>
                                            @foreach($warehouse_location as $location)
                                                <option value="{{$location->id}}">{{$location->location}}</option>
                                            @endforeach
                                        </select>
                                    </td>                              
                                </tr>
                            @endif
                            <tr>
                                <th class="control-label col-md-3">Pick up by:</th>
                                <td>
                                    <select class="users" data-placeholder="Choose transport type"  style="width: 100%;" name="transport_type" id="transport_type">
                                        <option value=""></option>
                                        @foreach($transport_types as $type)
                                            <option value="{{$type->id}}">{{$type->transport_type}}</option>
                                        @endforeach
                                    </select>
                                    <div class="form-group" id="hand_carry_div" style="display: none; margin-top:6px">
                                        <label for="">Hand Carrier:</label>
                                        <input type="text" class="form-control"  id="hand_carry" name="hand_carry" placeholder="Fullname">   
                                    </div>
                                </td>                              
                            </tr>
                            <tr>
                                <th class="control-label col-md-3">Schedule date:</th>
                                <td>
                                   <input type="text" class="form-control date" autocomplete="off" placeholder="Select date" name="schedule_date" id="schedule_date">
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
                    <label class="control-label col-md-2">Purpose:</label>
                    <div class="col-md-4">
                            <p>{{$Header->purpose}}</p>
                    </div>                    
                    @else
                    <label class="control-label col-md-2">Purpose:</label>
                    <div class="col-md-4">
                            <p>{{$Header->purpose}}</p>
                    </div>
                @endif
            </div>

            <hr/>

            <table  class='table' id="asset-items">
                <thead>
                    <tr style="background-color:#00a65a; border: 0.5px solid #000;">
                        <th style="text-align: center" colspan="16"><h4 class="box-title" style="color: #fff;"><b>Item details</b></h4></th>
                    </tr>
                    <tr>
                        <th width="20%" class="text-center">{{ trans('message.form-label.reference') }}</th>
                        <th width="10%" class="text-center">{{ trans('message.table.asset_tag') }}</th>
                        <th width="20%" class="text-center">{{ trans('message.table.digits_code') }}</th>
                        <th width="30%" class="text-center">{{ trans('message.table.item_description') }}</th>
                        <th width="25%" class="text-center">Asset Type</th>                                                        
                    </tr>
                </thead>
                <tbody>
                    @foreach($return_body as $rowresult)
                        <tr>
                            <input type="hidden" value="{{$rowresult->mo_id}}" name="mo_id[]">
                            <td style="text-align:center" height="10">{{$rowresult->reference_no}}</td>
                            <td style="text-align:center" height="10">{{$rowresult->asset_code}}</td>
                            <td style="text-align:center" height="10">{{$rowresult->digits_code}}</td>
                            <td style="text-align:center" height="10">{{$rowresult->description}}</td>
                            <td style="text-align:center" height="10">{{$rowresult->asset_type}}</td>
                                                   
                        </tr>
                    @endforeach

                </tbody>
                
            </table> 
        </div>

        <div class='panel-footer'>
            <a href="{{ CRUDBooster::mainpath() }}" class="btn btn-default">{{ trans('message.form.cancel') }}</a>
            <button class="btn btn-success pull-right" type="button" id="btnApprove"><i class="fa fa-thumbs-up" ></i> Schedule</button>
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
        $('#location_to_pick, #transport_type').select2();

        $('.date').datetimepicker({
            minDate: moment().millisecond(0).second(0).minute(0).hour(0),
            viewMode: 'days',
            format: 'YYYY-MM-DD',
            dayViewHeaderFormat: 'MMMM YYYY'
        });
        $(".date").val('');
        $("#transport_type").change(function() {
            var value = $(this).val();
            if(value == 2){
                $("#hand_carry_div").show();
            }else{
                $("#hand_carry_div").hide();
            }
        });

        $('#btnApprove').click(function(event) {
            event.preventDefault();
            if($("#location_to_pick").val() === ''){
                swal({
                    type: 'error',
                    title: 'Location required!',
                    icon: 'error',
                    confirmButtonColor: "#00a65a",
                }); 
                event.preventDefault(); // cancel default behavior
                return false;
            }
            if($("#schedule_date").val() === ''){
                swal({
                    type: 'error',
                    title: 'Schedule date required!',
                    icon: 'error',
                    confirmButtonColor: "#00a65a",
                }); 
                event.preventDefault(); // cancel default behavior
                return false;
            }
            if($("#transport_type").val() === ''){
                swal({
                    type: 'error',
                    title: 'Transport type required!',
                    icon: 'error',
                    confirmButtonColor: "#00a65a",
                }); 
                event.preventDefault(); // cancel default behavior
                return false;
            }
            if($("#transport_type :selected").val() == 2){
                if($("#hand_carry").val() === ''){
                    swal({
                        type: 'error',
                        title: 'Hand carrier required!',
                        icon: 'error',
                        confirmButtonColor: "#00a65a",
                    }); 
                    event.preventDefault(); // cancel default behavior
                    return false;
                }
            }
            console.log($("#transport_type :selected").val());
            swal({
                title: "Are you sure?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#41B314",
                cancelButtonColor: "#F9354C",
                confirmButtonText: "Yes, schedule it!",
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
    });

</script>
@endpush