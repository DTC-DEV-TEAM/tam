@extends('crudbooster::admin_template')
    @push('head')
        <style type="text/css">   
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

            input.readonly:read-only {
                background-color: #f5f5f5;
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
        ERF for Approval Form
    </div>

    <form method='post' id="myform" action='{{CRUDBooster::mainpath('edit-save/'.$Header->requestid)}}'>
        <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
        <input type="hidden" value="" name="approval_action" id="approval_action">
        <input type="hidden" value="{{$Header->requestid}}" name="header_id" id="header_id">

            <div class="card">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                        <label class="control-label">Company</i></label>
                                <input type="text" class="form-control readonly" value="{{$Header->company}}" aria-describedby="basic-addon1" readonly>             
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"> Requested Date</label>
                            <input type="text" class="form-control readonly" value="{{$Header->date_requested}}" aria-describedby="basic-addon1" readonly>             
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                        <label class="control-label"> Department</label>
                        <input type="text" class="form-control readonly" value="{{$Header->department}}" aria-describedby="basic-addon1" readonly>             
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"> Date Needed</label>
                            <input type="text" class="form-control finput date" value="{{$Header->date_needed}}" name="date_needed" id="date_needed" aria-describedby="basic-addon1">             
                        </div>
                    </div>
                
                </div>
                <div class="row"> 
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"> Position</label>
                            <select required selected data-placeholder="-- Please Select Location --" id="position" name="position" class="form-select select2 white" style="width:100%;">
                                @foreach($positions as $res)
                                <option value="{{ $res->position_description }}"
                                    {{ isset($Header->position) && $Header->position == $res->position_description ? 'selected' : '' }}>
                                    {{ $res->position_description }} 
                                </option>>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"> Work Location</label>
                            <input type="text" class="form-control finput" value="{{$Header->work_location}}" id="work_location" name="work_location" aria-describedby="basic-addon1">                                                                                    
                        </div>
                    </div>
                    
                </div>
                <div class="row"> 
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"> Salary Range</label>
                            <input type="text" class="form-control finput" value="{{number_format(Crypt::decryptString($Header->salary_range_from)) .' - '. number_format(Crypt::decryptString($Header->salary_range_to))}}" aria-describedby="basic-addon1" id="salary_range" name="salary_range">                                                                                         
  
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
                                <label class="checkbox-inline control-label col-md-6" ><br>
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
                                <label class="checkbox-inline control-label col-md-6" ><br>
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
                                <label class="checkbox-inline control-label col-md-6" ><br>
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
                                    <input type="checkbox" class="required_exams" name="required_exams[]" id="{{$res->description}}" value="{{$res->description}}" {{ in_array($res->description, $res_req) ? 'checked' : '' }} aria-describedby="basic-addon1">{{$res->description}} 
                                </label>
                            </div>                                                                                    
                        @endforeach
                        @if($Header->other_required_exams != NULL || $Header->other_required_exams != "")
                            <div class="form-group">
                                <input type="text" class="form-control finput" name="other_required_exams" value="{{$Header->other_required_exams}}" aria-describedby="basic-addon1">                                                                                    
                            </div>
                        @else
                            <div class="form-group" id="other_required_exams_div" style="display:none;">
                                <input type="text" class="form-control finput"  id="other_required_exams" name="other_required_exams" placeholder="Other Schedule">   
                            </div>
                        @endif
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
                                <label class="checkbox-inline control-label col-md-6" ><br>
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
                 
                </div>
               
            </div>
          
            <div class="card">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label> Qualifications</label>
                            <input type="text" class="form-control finput" value="{{$Header->qualifications}}" name="qualifications" id="qualifications" aria-describedby="basic-addon1">                                                                                     
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label> Job Descriptions</label>
                            <input type="text" class="form-control finput" value="{{$Header->job_description}}" name="job_descriptions" id="job_descriptions" aria-describedby="basic-addon1">                                                                                      
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
                                        <table style="width:100%">
                                            <tbody>
                                                <tr class="tbl_header_color dynamicRows">
                                                    <th width="10%" class="text-center">Digits Code</th>
                                                    <th width="10%" class="text-center">Item Description</th>
                                                    <th width="10%" class="text-center">Category</th> 
                                                    <th width="10%" class="text-center">Sub Category</th>  
                                                    <th width="10%" class="text-center">Quantity</th>   
                                                </tr>
                                                @foreach($Body as $rowresult)
                                                <tr>
                                                    <td style="text-align:center" height="10">
                                                        {{$rowresult->digits_code}}
                                                    </td>
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

            @if($Header->application != "")
            <div class="card">
                <div class="row">
                <div class="col-md-6" >
                            <label class="require control-label">{{ trans('message.form-label.application') }}</label>
                            @foreach($application as $val)   
                            <input type="text" class="form-control readonly" value="{{trim($val)}}" aria-describedby="basic-addon1" readonly>                                                                                       
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
            @endif

            <div class="card">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label> Additional Notes</label>
                            <textarea placeholder="Additional Notes ..." rows="3" class="form-control finput" name="additional_notess"></textarea>
                        </div>
                    </div>
                </div>
                <div class='panel-footer'>
                    <a href="{{ CRUDBooster::mainpath() }}" id="btn-cancel" class="btn btn-default">{{ trans('message.form.cancel') }}</a>
                    <button class="btn btn-danger pull-right" type="button" id="btnReject" style="margin-left: 5px;"><i class="fa fa-thumbs-down" ></i> Reject</button>
                    <button class="btn btn-success pull-right" type="button" id="btnApprove"><i class="fa fa-thumbs-up" ></i> Approve</button>
                </div>
            </div>
            

    </form>

@endsection
@push('bottom')
<script type="text/javascript">
$(function(){
    $('body').addClass("sidebar-collapse");
});


    $('#position').select2({})
    $(".date").datetimepicker({
        minDate:moment().millisecond(0).second(0).minute(0).hour(0),
        viewMode: "days",
        format: "YYYY-MM-DD",
        dayViewHeaderFormat: "MMMM YYYY",
    });

     //checkbox validations
     $(".manpower").change(function() {
        var rep = $(this);
        if(rep.val() === "REPLACEMENT" && rep.is(':checked')){
            $("#show_replacement_of").show();
        }else{
            $("#show_replacement_of").hide();
            $('#replacement_of').val("");
        }

    });

      //other schedule
      //checkbox validations
      $(".schedule").change(function() {
        var rep = $(this);
        if(rep.val() === "OTHERS" && rep.is(':checked')){
            $("#other_schedule_div").show();
        }else{
            $("#other_schedule_div").hide();
            $('#other_schedule').val("");
        }

    });

    //checkbox validations manpower
    $(".manpower").change(function() {
        var rep = $(this);
        if(rep.val() === "ABSORPTION" && rep.is(':checked')){
            $("#absorption_div").show();
        }else{
            $("#absorption_div").hide();
            $('#absorption').val("");
        }

    });

    //other email domain
      //checkbox validations
      $(".email_domain").change(function() {
        var rep = $(this);
        if(rep.val() === "OTHERS" && rep.is(':checked')){
            $("#other_email_domain").show();
        }else{
            $("#other_email_domain").hide();
            $('#other_email').val("");
        }
    });

     //other Required Exam
      //checkbox validations
      $("#OTHERS").change(function() {
        var ischecked= $(this).is(':checked');
        if(ischecked == false){
            $("#other_required_exams_div").hide();
            $("#other_required_exams").removeAttr('required');
        }else{
            $("#other_required_exams_div").show();
            $("#other_required_exams").attr('required', 'required');
        }
    });

    //checkbox validations
    $("input[name^='schedule'], input[name^='allow_wfh'], input[name^='manpower'], input[name^='manpower_type'], input[name^='shared_files'], input[name^='email_domain']").on('click', function() {
        var $box = $(this);
        if ($box.is(':checked')) {
            var group = "input:checkbox[name='" + $box.attr("name") + "']";
            $(group).prop("checked", false);
            $box.prop('checked', true);
        } else {
            $box.prop('checked', false);
        }
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
    "<tr><td colspan='4' style='text-align:center'><strong>TOTAL</strong></td><td style='text-align:center'><strong>" +
    sumcost +
    "</strong></td></td></tr>";
    
</script>
@endpush