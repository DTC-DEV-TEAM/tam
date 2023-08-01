@extends('crudbooster::admin_template')
    @push('head')
        <style type="text/css">   
 
            .firstRow {
                border: 1px solid rgba(39, 38, 38, 0.5);
                padding: 10px;
                margin-left: 10px;
                border-radius: 3px;
                opacity: 2;
            }

            .firstRow {
                padding: 10px;
                margin-left: 10px;
            }

            .finput {
                border:none;
                border-bottom: 1px solid rgba(18, 17, 17, 0.5);
            }

            input.finput:read-only {
                background-color: #fff;
            }

            input.sinput:read-only {
                background-color: #fff;
            }

            input.addinput:read-only {
                background-color: #f5f5f5;
            }

            .input-group-addon {
                background-color: #f5f5f5 !important;
            }

            .card, .card2, .card3, .card4, .card5, .card6, .card7, .card8{
                background-color: #fff ;
                padding: 15px;
                border-radius: 3px;
                box-shadow: rgba(0, 0, 0, 0.16) 0px 1px 4px;
                margin-bottom: 15px;
            }
            .panel-heading{
                background-color: #f5f5f5 ;
            }

            table, th, td {
            border: 1px solid rgba(000, 0, 0, .5);
            padding: 8px;
            border-radius: 5px 0 0 5px;
            }
           
        </style>
    @endpush
@section('content')
@if(g('return_url'))
	<p class="noprint"><a title='Return' href='{{g("return_url")}}'><i class='fa fa-chevron-circle-left '></i> &nbsp; {{trans("crudbooster.form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}}</a></p>       
@else
	<p class="noprint"><a title='Main Module' href='{{CRUDBooster::mainpath()}}'><i class='fa fa-chevron-circle-left '></i> &nbsp; {{trans("crudbooster.form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}}</a></p>       
@endif

    <div class='panel-heading'>
        Set On Boarding Date Form @if($Header->locking_onboarding_date !== CRUDBooster::myId()) <span style="color: red">(This form request currently used by {{$Header->current_user}}!)</span> @endif
    </div>

    <form method='post' id="myform" action='{{CRUDBooster::mainpath('edit-save/'.$Header->requestid)}}'>
        <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
        <input type="hidden" value="" name="approval_action" id="approval_action">
        <input type="hidden" value="0" name="action" id="action">
        <input type="hidden" name="id" id="id" value="{{$Header->requestid}}">
        <input type="hidden" value="{{$Header->locking_onboarding_date}}" name="locking" id="locking">
        <input type="hidden" value="{{CRUDBooster::myId()}}" name="current_user" id="current_user">

            <div class="card">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                        <label class="control-label">Company</i></label>
                                <input type="text" class="form-control finput" value="{{$Header->company}}" aria-describedby="basic-addon1" readonly>             
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"> Requested Date</label>
                            <input type="text" class="form-control finput" value="{{$Header->date_requested}}" aria-describedby="basic-addon1" readonly>             
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                        <label class="control-label"> Department</label>
                        <input type="text" class="form-control finput" value="{{$Header->department}}" aria-describedby="basic-addon1" readonly>             
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"> Date Needed</label>
                            <input type="text" class="form-control finput" value="{{$Header->date_needed}}" aria-describedby="basic-addon1" readonly>             
                        </div>
                    </div>
                
                </div>
                <div class="row"> 
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"> Position</label>
                            <input type="text" class="form-control finput" value="{{$Header->position}}" aria-describedby="basic-addon1" readonly>                                                      
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"> Work Location</label>
                            <input type="text" class="form-control finput" value="{{$Header->work_location}}" aria-describedby="basic-addon1" readonly>                                                                                    
                        </div>
                    </div>
                    
                </div>
                <div class="row"> 
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"> Salary Range</label>
                            <input type="text" class="form-control finput" value="{{number_format(Crypt::decryptString($Header->salary_range_from)) .' - '. number_format(Crypt::decryptString($Header->salary_range_to))}}" aria-describedby="basic-addon1" readonly>                                                                                         
   
                        </div>
                    </div> 
                </div>
            </div>
            <!-- 2nd Row -->
        <div class="card">
            <div class="row"> 
                <div class="col-md-6">
                    <label class="require control-label"> Schedule</label><br>
                    @foreach($schedule as $res)
                        <div class="col-md-6">
                            <label class="checkbox-inline control-label col-md-12" ><br>
                                <input type="checkbox" class="schedule" name="schedule" value="{{$res->schedule_description}}" {{ isset($Header->schedule) && $Header->schedule == $res->schedule_description ? 'checked' : '' }} aria-describedby="basic-addon1">{{$res->schedule_description}} 
                            </label>
                        </div>
                    @endforeach
                    @if($Header->other_schedule != NULL || $Header->other_schedule != "")
                        <div class="form-group">
                            <input type="text" class="form-control finput" name="other_schedule" value="{{$Header->other_schedule}}" aria-describedby="basic-addon1">                                                                                    
                        </div>
                    @else
                        <div class="form-group" id="other_schedule_div" style="display:none;">
                            <input type="text" class="form-control finput"  id="other_schedule" name="other_schedule" placeholder="Other Schedule">   
                        </div>
                    @endif
                </div>
                
                <div class="col-md-6">
                    <label class="require control-label"> Allow Wfh</label><br>
                    @foreach($allow_wfh as $res)
                        <div class="col-md-6">
                            <label class="checkbox-inline control-label col-md-12" ><br>
                                <input type="checkbox" name="allow_wfh" value="{{$res->description}}" {{ isset($Header->allow_wfh) && $Header->allow_wfh == $res->description ? 'checked' : '' }} aria-describedby="basic-addon1">{{$res->description}} 
                            </label>
                        </div>
                    @endforeach                                           
                </div>
            </div>
           
            <br>
            <div class="row"> 
                <div class="col-md-6">
                    <label class="require control-label"> Manpower</label><br>
                    @foreach($manpower as $res)
                        <div class="col-md-6">
                            <label class="checkbox-inline control-label col-md-6" ><br>
                                <input type="checkbox" class="manpower" name="manpower" value="{{$res->description}}" {{ isset($Header->manpower) && $Header->manpower == $res->description ? 'checked' : '' }} aria-describedby="basic-addon1">{{$res->description}} 
                            </label>
                        </div>
                    @endforeach   
                    @if($Header->replacement_of != NULL || $Header->replacement_of != "")
                        <div class="form-group">
                            <input type="text" class="form-control finput" value="{{$Header->replacement_of}}" aria-describedby="basic-addon1">                                                                                    
                        </div>
                    @else
                        <div class="form-group" id="show_replacement_of" style="display:none;">
                            <input type="text" class="form-control finput"  id="replacement_of" name="replacement_of" placeholder="Replacement Of">   
                        </div>
                    @endif

                    @if($Header->absorption != NULL || $Header->absorption != "")
                        <div class="form-group">
                            <input type="text" class="form-control finput" name="absorption" value="{{$Header->absorption}}" aria-describedby="basic-addon1">                                                                                    
                        </div>
                    @else
                        <div class="form-group" id="absorption_div" style="display:none;">
                            <input type="text" class="form-control finput"  id="absorption" name="absorption" placeholder="Absorption">   
                        </div>
                    @endif
                   
                   
                </div>
                <div class="col-md-6">
                    <label class="require control-label"> Manpower Type</label><br>
                    @foreach($manpower_type as $res)
                        <div class="col-md-6">
                            <label class="checkbox-inline control-label col-md-12" ><br>
                                <input type="checkbox" name="manpower_type" value="{{$res->description}}" {{ isset($Header->manpower_type) && $Header->manpower_type == $res->description ? 'checked' : '' }} aria-describedby="basic-addon1">{{$res->description}} 
                            </label>
                        </div>
                    @endforeach                                                                                    
                </div>
            </div>
            <br>
            
            
        </div>
        <!-- 3rd row -->
        <div class="card">
            <div class="row"> 
                <div class="col-md-6">
                    <label class="require control-label"> Required Exams</label><br>
                    @foreach($required_exams as $res)
                        <div class="col-md-6">
                            <label class="checkbox-inline control-label col-md-12" ><br>
                                <input type="checkbox" name="required_exams[]" value="{{$res->description}}" {{ in_array($res->description, $res_req) ? 'checked' : '' }} aria-describedby="basic-addon1">{{$res->description}} 
                            </label>
                        </div>                                                                                    
                    @endforeach
                </div>
                <div class="col-md-6">
                    <label class="require control-label"> Does the Employee need to shared files?</label><br>
                    @foreach($shared_files as $res)
                        <div class="col-md-6">
                            <label class="checkbox-inline control-label col-md-6" ><br>
                                <input type="checkbox" name="shared_files" value="{{$res->description}}" {{ isset($Header->shared_files) && $Header->shared_files == $res->description ? 'checked' : '' }} aria-describedby="basic-addon1">{{$res->description}} 
                            </label>
                        </div>
                    @endforeach    
                </div>
            </div>
            <br>
            @if($Header->other_required_exams != NULL || $Header->other_required_exams != "")
            <div class="row"> 
                <div class="col-md-6">
                    <label class="require control-label"> Other Required Exams</label><br>
                    <input type="text" class="form-control finput" value="{{$Header->other_required_exams}}" id="other_required_exams" name="other_required_exams" aria-describedby="basic-addon1">                                                                                    
                </div>
            </div>
            @endif
        </div>
        <div class="card">
            <div class="row">
                <div class="col-md-6">
                    <label class="require control-label"> Who will the employee interact with?</label><br>
                    @foreach($interact_with as $res)   
                        <div class="col-md-6">
                            <label class="checkbox-inline control-label col-md-12" ><br>
                                <input type="checkbox" name="employee_interaction[]" value="{{$res->description}}" {{ in_array($res->description, $interaction) ? 'checked' : '' }} aria-describedby="basic-addon1">{{$res->description}} 
                            </label>
                        </div>                                                                                                                                                                 
                    @endforeach
                </div>
                <div class="col-md-6">
                    <label class="require control-label"> What will you be using the PC for?</label><br>
                    @foreach($asset_usage as $res)   
                        <div class="col-md-6">
                            <label class="checkbox-inline control-label col-md-12" ><br>
                                <input type="checkbox" name="asset_usage[]" value="{{$res->description}}" {{ in_array($res->description, $asset_usage_array) ? 'checked' : '' }} aria-describedby="basic-addon1">{{$res->description}} 
                            </label>
                        </div>                                                                                                                                                                 
                    @endforeach
                </div>
            </div>
        </div>
        <div class="card">
            <div class="row">
                <div class="col-md-6">
                    <label class="require control-label"> Email Domain</label><br>
                    @foreach($email_domain as $res)
                        <div class="col-md-6">
                            <label class="checkbox-inline control-label col-md-6" ><br>
                                <input type="checkbox" class="email_domain" name="email_domain" value="{{$res->description}}" {{ isset($Header->email_domain) && $Header->email_domain == $res->description ? 'checked' : '' }} aria-describedby="basic-addon1">{{$res->description}} 
                            </label>
                        </div>
                    @endforeach   
                    @if($Header->other_email_domain != NULL || $Header->other_email_domain != "")                 
                        <div class="form-group">
                            <input type="text" class="form-control finput" id="other_email" name="other_email" value="{{$Header->other_email_domain}}" aria-describedby="basic-addon1" >                                                                                    
                        </div>                  
                    @else
                        <div class="form-group" id="other_email_domain" style="display:none;">
                            <input type="text" class="form-control finput"  id="other_email" name="other_email" placeholder="Other Email">   
                        </div>   
                    @endif    
                                                                                           
                </div>
                @if($Header->required_system != "" || $Header->required_system != NULL)
                    <div class="col-md-6">
                        <label class="require control-label"> Required System</label><br>
                        @foreach($required_system as $res)   
                            <div class="col-md-6">
                                <label class="checkbox-inline control-label col-md-12" ><br>
                                    <input type="checkbox" name="required_system[]" value="{{$res->description}}" {{ in_array($res->description, $required_system_array) ? 'checked' : '' }} aria-describedby="basic-addon1">{{$res->description}} 
                                </label>
                            </div>                                                                                                                                                                 
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
            <div class="card">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label> Qualifications</label>
                            <input type="text" class="form-control finput" value="{{$Header->qualifications}}" aria-describedby="basic-addon1" readonly>                                                                                     
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label> Job Descriptions</label>
                            <input type="text" class="form-control finput" value="{{$Header->job_description}}" aria-describedby="basic-addon1" readonly>                                                                                      
                        </div>
                    </div>
                </div>
                <div class="row">   
                    <div class="col-md-6">                        
                    <label class="control-label">Attached Documents</label>
                    @foreach($erf_header_documents as $erf_header_document)                                    
                        <a href='{{CRUDBooster::mainpath("download/".$erf_header_document->id)."?return_url=".urlencode(Request::fullUrl())}}' class="form-control finput">{{$erf_header_document->file_name}} <i style="color:#007bff" class="fa fa-download"></i></a>                                       
                    @endforeach
                    </div>
                </div>
            </div>
            <div class='panel-heading text-center'>
                <h4>Required Assets</h4>
            </div>
            <div class="card">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box-body no-padding">
                            <div class="table-responsive">
                                <div class="pic-container">
                                    <div class="pic-row">
                                        <table id='table_dashboard' class="table table-hover table-striped table-bordered">
                                            <tbody>
                                                <tr class="tbl_header_color dynamicRows">
                                                    <th width="10%" class="text-center">Item Description</th>
                                                    <th width="10%" class="text-center">Category</th> 
                                                    <th width="10%" class="text-center">Sub Category</th>  
                                                    <th width="10%" class="text-center">Quantity</th>   
                                                </tr>
                                                @foreach($Body as $rowresult)
                                                <tr>
                                                    <td style="text-align:center" height="10">
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
                                                    

                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <br>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="row">
                <div class="col-md-6" >
                            <label class="require control-label">{{ trans('message.form-label.application') }}</label>
                            @foreach($application as $val)   
                            <input type="text" class="form-control finput" value="{{trim($val)}}" aria-describedby="basic-addon1" readonly>                                                                                       
                            @endforeach  
                    </div>
                    @if($Header->application_others != "")
                    <div class="col-md-6">
                        <div class="row">
                            <label class="require control-label">*{{ trans('message.form-label.application_others') }}</label>
                            <div class="col-md-6">
                            <p>{{$Header->application_others}}</p>   
                            </div>
                        </div>
                        <hr/>
                    </div>
                    @endif
                </div>
            </div>
            
            @if($Header->approved_immediate_head_by != NULL)
        <div class="card">
            <div class="row">
                <div class="col-md-6">
                    <table style="width:100%">
                        <tbody>
                            <tr>
                                <th class="control-label col-md-2">{{ trans('message.form-label.approved_by') }}:</th>
                                <td class="col-md-4">{{$Header->approved_immediate_head_by}} / {{$Header->approved_immediate_head_at}}</td>     
                            </tr>
                            @if($Header->approver_comments != NULL)
                            <tr>
                                <th class="control-label col-md-2">{{ trans('message.table.approver_comments') }}:</th>
                                <td class="col-md-4">{{$Header->approver_comments}}</td>
                            </tr>
                            @endif

                        </tbody>
                    </table>
                </div>
                
                @if($Header->approved_hr_by != NULL)
                <div class="col-md-6">
                    <table style="width:100%">
                        <tbody>
                            <tr>
                                <th class="control-label col-md-2">Verified By:</th>
                                <td class="col-md-4">{{$Header->approved_hr_by}} / {{$Header->approved_hr_at}}</td>     
                            </tr>
                            @if($Header->hr_comments != NULL)
                            <tr>
                                <th class="control-label col-md-2">Verifier Comments:</th>
                                <td class="col-md-4">{{$Header->hr_comments}}</td>
                            </tr>
                            @endif

                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
        @endif
            
            <div class="card">  
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                        <label class="control-label">Erf#</i></label>
                                <input type="text" class="form-control finput" value="{{$Header->reference_number}}" aria-describedby="basic-addon1" readonly>             
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                        <label class="control-label">Employee Name</i></label>
                                <input type="text" class="form-control finput" value="{{$Header->first_name . ' ' . $Header->last_name}}" aria-describedby="basic-addon1" readonly>             
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                        <label class="control-label">On Boarding Date</i></label>
                            <input type="hidden" class="form-control finput" id="requesid" value="{{$Header->requestid}}" aria-describedby="basic-addon1" >             
                            <input type="text" class="form-control finput date" name="onboarding_date" id="onboarding_date" value="{{$Header->onboarding_date}}" aria-describedby="basic-addon1">             
                        </div>
                    </div>
                  
                </div>
                <hr>
                <a href="{{ CRUDBooster::mainpath() }}" id="btn-cancel" class="btn btn-default">{{ trans('message.form.cancel') }}</a>
                @if($Header->locking_onboarding_date === CRUDBooster::myId())
                <button class="btn btn-success pull-right" type="button" id="btnSet"> <i class="fa fa-send" ></i> Submit</button>
                <button class="btn btn-warning pull-right" type="submit" id="btnUpdate" style="margin-right: 10px;"> <i class="fa fa-refresh" ></i> {{ trans('message.form.update') }}</button> 
                @endif
            </div>
            

    </form>

@endsection
@push('bottom')
<script type="text/javascript">
    $(function(){
        $('body').addClass("sidebar-collapse");
    });
    window.onbeforeunload = function() {
        return "";
    };
    function preventBack() {
        window.history.forward();
    }
    setTimeout("preventBack()", 0);
    
    $("input:checkbox").click(function() { return false; });

    if($('#locking').val() === $('#current_user').val()){
        const pageHideListener = (event) => {
            var id = $('#id').val();
            $.ajaxSetup({
                headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
            });
            $.ajax({
                type: 'POST',
                url: "{{ route('delete-locking-onboarding-date') }}",
                dataType: 'json',
                data: {
                    'header_request_id': id
                },
                success: function ()
                {
                    
                }
            });  
        };
        window.addEventListener("pagehide", pageHideListener);

        var online = navigator.onLine;
        if(online == false){
            window.addEventListener("pagehide", pageHideListener);
        }
    }

    $(".date").datetimepicker({
            minDate: moment().millisecond(0).second(0).minute(0).hour(0),
            viewMode: "days",
            format: "YYYY-MM-DD",
            dayViewHeaderFormat: "MMMM YYYY",
        });
    $('.status').select2({placeholder_text_single : "- Select Status -"});

    $('#btnSet').click(function(event) {
        var id = $('#requesid').val();
        var date = $('#onboarding_date').val();
        event.preventDefault();
        if($('#onboarding_date').val() === "" ){
            swal({  
                type: 'error',
                title: 'Onboarding Date Required!',
                icon: 'error',
                confirmButtonColor: "#367fa9",
            });
            event.preventDefault();
            return false;
        }else{
            swal({
                title: "Are you sure?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#41B314",
                cancelButtonColor: "#F9354C",
                confirmButtonText: "Yes, set it!",
                width: 450,
                height: 200
                }, function () {
                    $.ajax({
                        url: "{{ route('set-onboarding-date') }}",
                        type: "POST",
                        data: {
                            id: id,
                            date: date,
                        },
                        dataType: 'json',
                        success: function (data) {
                            if (data.status == "success") {
                                swal({
                                    type: data.status,
                                    title: data.message,
                                });
                                setTimeout(function(){
                                    window.location.replace(document.referrer);
                                }, 2000); 
                                } else if (data.status == "error") {
                                swal({
                                    type: data.status,
                                    title: data.message,
                                });
                            }             
                        },
                        error: function (data) {
                            console.log('Error:', data);
                        }
                    });                 
            });
        }
            
    });

    $('#btnUpdate').click(function(event) {
        var id = $('#requesid').val();
        var date = $('#onboarding_date').val();
        event.preventDefault();
        if($('#onboarding_date').val() === "" ){
            swal({  
                type: 'error',
                title: 'Onboarding Date Required!',
                icon: 'error',
                confirmButtonColor: "#367fa9",
            });
            event.preventDefault();
            return false;
        }else{
            swal({
                title: "Are you sure?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#41B314",
                cancelButtonColor: "#F9354C",
                confirmButtonText: "Yes, update it!",
                width: 450,
                height: 200
                }, function () {
                    $.ajax({
                        url: "{{ route('set-update-onboarding-date') }}",
                        type: "POST",
                        data: {
                            id: id,
                            date: date,
                        },
                        dataType: 'json',
                        success: function (data) {
                            if (data.status == "success") {
                                swal({
                                    type: data.status,
                                    title: data.message,
                                });
                                location.reload();               
                                } else if (data.status == "error") {
                                swal({
                                    type: data.status,
                                    title: data.message,
                                });
                            }             
                        },
                        error: function (data) {
                            console.log('Error:', data);
                        }
                    });                 
            });
        }
            
    });

    var tds = document
    .getElementById("table_dashboard")
    .getElementsByTagName("td");
    var sumqty = 0;
    var sumcost = 0;
    for (var i = 0; i < tds.length; i++) {
    if (tds[i].className == "qty") {
        sumcost += isNaN(tds[i].innerHTML) ? 0 : parseFloat(tds[i].innerHTML);
    }
    }
    document.getElementById("table_dashboard").innerHTML +=
    "<tr><td colspan='3' style='text-align:right'><strong>TOTAL</strong></td><td style='text-align:center'><strong>" +
    sumcost +
    "</strong></td></td></tr>";

    $("#btn-cancel").click(function(event) {
       event.preventDefault();
       swal({
            title: "Are you sure?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#41B314",
            cancelButtonColor: "#F9354C",
            confirmButtonText: "Yes, Go back!",
            width: 450,
            height: 200
            }, function () {
                window.history.back();                                                  
        });
    });
</script>
@endpush