@extends('crudbooster::admin_template')
@section('content')
    @push('head')
        <style type="text/css">   
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
        Request Form
    </div>

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
                        <th width="10%" class="text-center">Line status</th>
                        <th width="20%" class="text-center">Reference No</th>
                        @if(in_array($Header->request_type_id, [1,5]))
                            <th width="10%" class="text-center">Asset Code</th>
                        @endif
                        <th width="10%" class="text-center">Digits Code</th>
                        <th width="30%" class="text-center">{{ trans('message.table.item_description') }}</th>
                        <th width="25%" class="text-center">Asset Type</th>                                                         
                    </tr>
                </thead>
                <tbody>
                    @foreach($return_body as $rowresult)
                        <tr>
                            @if($rowresult['body_status'] == 1)
                                <td style="text-align:center">
                                <label class="label label-warning" style="align:center; font-size:10px">{{$rowresult->status_description}}</label>
                                </td>
                            @elseif($rowresult['body_status'] == 5)
                                <td style="text-align:center">
                                <label class="label label-danger" style="align:center; font-size:10px">{{$rowresult->status_description}}</label>
                                </td>
                            @elseif($rowresult['body_status'] == 8)
                                <td style="text-align:center">
                                <label class="label label-danger" style="align:center; font-size:10px">{{$rowresult->status_description}}</label>
                                </td>
                            @elseif($rowresult['body_status'] == 24)
                                <td style="text-align:center">
                                <label class="label label-info" style="align:center; font-size:10px">{{$rowresult->status_description}}</label>
                                </td>
                            @elseif($rowresult['body_status'] == 26)
                                <td style="text-align:center">
                                <label class="label label-info" style="align:center; font-size:10px">{{$rowresult->status_description}}</label>
                                </td>
                            @elseif($rowresult['body_status'] == 27)
                                <td style="text-align:center">
                                <label class="label label-info" style="align:center; font-size:10px">{{$rowresult->status_description}}</label>
                                </td>
                            @elseif($rowresult['body_status'] == 48)
                                <td style="text-align:center">
                                <label class="label label-info" style="align:center; font-size:10px">{{$rowresult->status_description}}</label>
                                </td>
                            @elseif($rowresult['body_status'] == 49)
                                <td style="text-align:center">
                                <label class="label label-warning" style="align:center; font-size:10px">{{$rowresult->status_description}}</label>
                                </td>
                            @else
                               <td style="text-align:center">
                                <label class="label label-success" style="align:center; font-size:10px">{{$rowresult->status_description}}</label>
                               </td>
                            @endif
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
                <hr>
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
                                @if($Header->scheduleby != null || $Header->scheduleby != "")
                                    <tr>
                                        <th class="control-label col-md-2">Scheduled By:</th>
                                        <td class="col-md-4">{{$Header->scheduleby}} / {{$Header->schedule_at}}</td>
                                    </tr>
                                @endif
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
        </div>

</div>

@endsection
@push('bottom')
<script type="text/javascript">


</script>
@endpush