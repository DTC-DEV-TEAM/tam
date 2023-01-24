@extends('crudbooster::admin_template')
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

           <!-- <hr/>

            <div class="row">                           
                <label class="control-label col-md-2">{{ trans('message.form-label.condition') }}:</label>
                <div class="col-md-4">
                        <p>{{$Header->condition_description}}</p>
                </div>

        
            </div> -->

            <hr/>                
            
            <div class="box-header text-center">
                <h3 class="box-title"><b>{{ trans('message.form-label.asset_items') }}</b></h3>
            </div>

            <table  class='table table-striped table-bordered'>
                <thead>
                    <tr>
                        <th width="30%" class="text-center">{{ trans('message.table.item_description') }}</th>
                        <th width="25%" class="text-center">{{ trans('message.table.category_id_text') }}</th>                                                         
                        <th width="20%" class="text-center">{{ trans('message.table.sub_category_id_text') }}</th> 
                        <th width="7%" class="text-center">{{ trans('message.table.quantity_text') }}</th> 
                    <!-- <th width="13%" class="text-center">{{ trans('message.table.image') }}</th> -->
                    </tr>
                </thead>
                <tbody>
                    @foreach($Body as $rowresult)
                        <tr>
                            <td style="text-align:center" height="10">{{$rowresult->item_description}}</td>
                            <td style="text-align:center" height="10">{{$rowresult->category_id}}</td>
                            <td style="text-align:center" height="10">

                                {{$rowresult->sub_category_id}}

                            </td>

                            <td style="text-align:center" height="10">{{$rowresult->quantity}}</td>
                            
                            <!--
                                    {{$rowresult->app_id}}

                                    @if($rowresult->app_id_others != null || $rowresult->app_id_others != "" )
                                        <br>
                                        {{$rowresult->app_id_others}}
                                    @endif
                            
                            </td>
                            <td style="text-align:center" height="10">{{$rowresult->quantity}}</td>
                             <td style="text-align:center" height="10">

                                @if($rowresult->image != null || $rowresult->image != "")
                                    <img src="{{asset("$rowresult->image")}}" style="width:150px;height:150px;">
                                @endif  

                            </td> -->
                        
                        </tr>
                    @endforeach

                    <tr>
                        <td colspan="3" style="text-align:right">
                            <label>{{ trans('message.table.total_quantity') }}:</label>
                        </td>

                        <td style="text-align:center">
                            <label>{{$Header->quantity_total}}</label>
                        </td>

                    </tr>
                </tbody>
                
            </table> 

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
                    <label class="control-label col-md-2">{{ trans('message.table.note') }}:</label>
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
                        <textarea placeholder="{{ trans('message.table.comments') }} ..." rows="3" class="form-control" name="approver_comments">{{$Header->approver_comments}}</textarea>
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

    function preventBack() {
        window.history.forward();
    }
     window.onunload = function() {
        null;
    };
    setTimeout("preventBack()", 0);

    $('#btnApprove').click(function(event) {
        // var strconfirm = confirm("Are you sure you want to approve this request?");
        // if (strconfirm == true) {
        //     $(this).attr('disabled','disabled');
        //     $('#approval_action').val('1');
        //     $('#myform').submit(); 
        // }else{
        //     return false;
        //     window.stop();
        // }
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
        // var strconfirm = confirm("Are you sure you want to reject this request?");
        // if (strconfirm == true) {
        //     $(this).attr('disabled','disabled');
        //     $('#approval_action').val('0');
        //     $('#myform').submit(); 
        // }else{
        //     return false;
        //     window.stop();
        // }
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


</script>
@endpush