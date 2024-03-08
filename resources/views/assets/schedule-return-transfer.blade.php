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
                            <tr>
                                <th class="control-label col-md-3">Schedule date:</th>
                                <td>
                                   <input type="text" class="form-control date" placeholder="Select date" name="schedule_date" id="schedule_date" autocomplete="off">
                                </td>                              
                            </tr>
                            <tr>
                                <th class="control-label col-md-3">Pick up by:</th>
                                <td>
                                   <input type="text" class="form-control" name="pickup_by" id="pickup_by">
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
                        @if(in_array($Header->request_type_id, [1,5]))
                            <th width="10%" class="text-center">{{ trans('message.table.asset_tag') }}</th>
                        @endif
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
                            @if(in_array($Header->request_type_id, [1,5]))
                                <td style="text-align:center" height="10">{{$rowresult->asset_code}}</td>
                            @endif
                            <td style="text-align:center" height="10">{{$rowresult->digits_code}}</td>
                            <td style="text-align:center" height="10">{{$rowresult->description}}</td>
                            <td style="text-align:center" height="10">{{$rowresult->asset_type}}</td>
                                                   
                        </tr>
                    @endforeach

                </tbody>
                
            </table> 
            
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
        $('#location_to_pick').select2();

        $('.date').datetimepicker({
            viewMode: 'days',
            format: 'YYYY-MM-DD',
            dayViewHeaderFormat: 'MMMM YYYY'
        });

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
    });

</script>
@endpush