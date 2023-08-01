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

            input.finput:read-only {
                background-color: #fff;
            }

            input.sinput:read-only {
                background-color: #f5f5f5;
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

            .plus{
                font-size:20px;
            }
            #add-Row{
                border:none;
                background-color: #fff;
            }
          
            .iconPlus{
                background-color: #3c8dbc: 
            }
            
            .iconPlus:before {
                content: '';
                display: flex;
                justify-content: center;
                align-items: center;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                /* border: 1px solid rgb(194, 193, 193); */
                font-size: 35px;
                color: white;
                background-color: #3c8dbc;
       
            }
            #bigplus{
                transition: transform 0.5s ease 0s;
            }
            #bigplus:before {
                content: '\FF0B';
                background-color: #3c8dbc: 
                font-size: 50px;
            }
            #bigplus:hover{
                /* cursor: default;
                transform: rotate(180deg); */
                -webkit-animation: infinite-spinning 1s ease-out 0s infinite normal;
                 animation: infinite-spinning 1s ease-out 0s infinite normal;
               
            }

            @keyframes infinite-spinning {
                from {
                    transform: rotate(0deg);
                }
                to {
                    transform: rotate(360deg);
                }
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
            Employee Requisition Form
        </div>

       <form action="{{ CRUDBooster::mainpath('add-save') }}" method="POST" id="ERFRequest" enctype="multipart/form-data">
        <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
        <input type="hidden" value="1" name="request_type_id" id="request_type_id">
            <div class="card">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label require"> Company</label>
                            <input type="text" class="form-control finput"  id="company" name="company" value="Digits Trading Corp."  required readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                    <div class="form-group">
                            <label class="control-label"><span style="color:red">*</span> Department</label>
                            <select required selected data-placeholder="-- Please choose department --" id="department" name="department" class="form-select select2" style="width:100%;">
                            @foreach($departments as $res)
                                <option value=""></option>
                                <option value="{{ $res->id }}">{{ $res->department_name }}</option>
                            @endforeach
                            </select>
                            {{-- <input type="text" class="form-control finput"  id="department" name="department" value="{{$employeeinfos->department_name}}"  required readonly> --}}
                        </div>
                    </div>  
                </div>


                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"><span style="color:red">*</span> Date Needed</label>
                            <input class="form-control finput date" type="text" placeholder="Select Needed Date" name="date_needed" id="date_needed">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"><span style="color:red">*</span> Position</label>
                            <select required selected data-placeholder="-- Please Select Positions/Title --" id="position" name="position" class="form-select select2" style="width:100%;">
                            {{-- @foreach($positions as $res)
                                <option value=""></option>
                                <option value="{{ $res->id }}">{{ $res->position_description }}</option>
                            @endforeach --}}
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row"> 
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"><span style="color:red">*</span> Work Location</label>
                            <input type="text" class="form-control finput"  id="work_location" name="work_location"  required>                                   
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"><span style="color:red">*</span> Salary Range</label>
                            <input type="text" class="form-control finput" placeholder="(xx,xxx-xx,xxx)"  id="salary_range" name="salary_range"  required>                                   
                            <div id="display-error"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 2nd Row -->
            <div class="card2">
                    <div class="row"> 
                        <div class="col-md-6">
                            <label class="require control-label"><span style="color:red">*</span> Schedule</label><br>
                                @foreach($schedule as $data)
                                <div class="col-md-6">
                                    <label class="checkbox-inline control-label col-md-6" ><br>
                                    <input type="checkbox" required  class="schedule" name="schedule" id="schedule" value="{{$data->schedule_description}}" >{{$data->schedule_description}}
                                    </label>
                                </div>
                                @endforeach
                                <div class="form-group" id="other_schedule_div" style="display:none;">
                                    <input type="text" class="form-control finput"  id="other_schedule" name="other_schedule" placeholder="Other Schedule">   
                                </div>
                        </div>
                        <div class="col-md-6">
                            <label class="require control-label"><span style="color:red">*</span> Allow Wfh</label><br>
                                @foreach($allow_wfh as $data)
                                <div class="col-md-6">
                                    <label class="checkbox-inline control-label col-md-8" ><br>
                                    <input type="checkbox" required   class="allow_wfh" name="allow_wfh" value="{{$data->description}}" >{{$data->description}}
                                    </label>
                                </div>
                                @endforeach
                        </div>
                    </div>
            </div>

            
            <div class="card3">
                    <div class="row"> 
                        <div class="col-md-6">
                            <label class="require control-label"><span style="color:red">*</span> Manpower</label><br>
                                @foreach($manpower as $data)
                                <div class="col-md-6">
                                    <label class="checkbox-inline control-label col-md-6" ><br>
                                    <input type="checkbox" required   class="manpower" name="manpower" value="{{$data->description}}" >{{$data->description}}
                                    </label>
                                </div>
                                @endforeach

                                <div class="form-group" id="show_replacement_of" style="display:none;">
                                    <input type="text" class="form-control finput"  id="replacement_of" name="replacement_of" placeholder="Replacement Of">   
                                </div>
                                <div class="form-group" id="absorption_div" style="display:none;">
                                    <input type="text" class="form-control finput"  id="absorption" name="absorption" placeholder="Absorption">   
                                </div>
                        </div>
                        <div class="col-md-6">
                            <label class="require control-label"><span style="color:red">*</span> Manpower Type</label><br>
                                @foreach($manpower_type as $data)
                                <div class="col-md-6">
                                    <label class="checkbox-inline control-label col-md-6" ><br>
                                    <input type="checkbox" required   class="manpower_type" name="manpower_type" value="{{$data->description}}" >{{$data->description}}
                                </div>
                                @endforeach
                        </div>
                    </div>
            </div>
       
            <div class="card4">
                <div class="row"> 
                    <div class="col-md-6">
                        <label class="require control-label"> Required Exams</label><br>
                            @foreach($required_exams as $data)
                            <div class="col-md-6">
                                <label class="checkbox-inline control-label col-md-12" ><br>
                                <input type="checkbox" required   class="required_exams" name="required_exams[]" value="{{$data->description}}" >{{$data->description}}
                                </label>
                            </div>
                            @endforeach
                            <div class="form-group" id="other_required_exams_div" style="display:none;">
                                <input type="text" class="form-control finput"  id="other_required_exams" name="other_required_exams" placeholder="Other Required Exams">   
                            </div>
                    </div>
                    <div class="col-md-6">
                        <label class="require control-label"><span style="color:red">*</span> Does the Employee need to shared files?</label><br>
                        @foreach($shared_files as $data)
                        <div class="col-md-6">
                            <label class="checkbox-inline control-label col-md-6" ><br>
                                <input type="checkbox" required   class="shared_files" name="shared_files" value="{{$data->description}}" >{{$data->description}}
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>
             </div>
         
            <div class="card5">
                <div class="row">
                    <div class="col-md-6">
                        <label class="require control-label"><span style="color:red">*</span> Who will the employee interact with?</label><br>
                        @foreach($interact_with as $data)
                        <div class="col-md-6">
                            <label class="checkbox-inline control-label col-md-12" ><br>
                                <input type="checkbox" required   class="employee_interaction" name="employee_interaction[]" value="{{$data->description}}" >{{$data->description}}
                            </label>
                        </div>
                        @endforeach
                    </div>
                    <div class="col-md-6">
                        <label class="require control-label"><span style="color:red">*</span> What will you be using the PC for?</label><br>
                        @foreach($asset_usage as $data)
                        <div class="col-md-6">
                            <label class="checkbox-inline control-label col-md-12" ><br>
                                <input type="checkbox" required   class="asset_usage" name="asset_usage[]" value="{{$data->description}}" >{{$data->description}}
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>
             </div>
             <div class="card6">
                <div class="row">
                    <div class="col-md-6">
                        <label class="require control-label"><span style="color:red">*</span> Email Domain</label><br>
                        @foreach($email_domain as $data)
                        <div class="col-md-6">
                            <label class="checkbox-inline control-label col-md-6" ><br>
                                <input type="checkbox" required multiple class="email_domain" name="email_domain" value="{{$data->description}}" >{{$data->description}}
                            </label>
                        </div>
                        @endforeach
                       
                        <div class="form-group" id="other_email_domain" style="display:none;">
                            <input type="text" class="form-control finput"  id="other_email" name="other_email" placeholder="Other Email">   
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="require control-label"> Required System</label><br>
                        @foreach($required_system as $data)
                        <div class="col-md-3">
                            <label class="checkbox-inline control-label col-md-3" ><br>
                                <input type="checkbox" required multiple class="required_system" name="required_system[]" value="{{$data->description}}" >{{$data->description}}
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="card7">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><span style="color:red">*</span> QUALIFICATIONS</label>
                            <textarea placeholder=" Qualifications ..." rows="3" class="form-control finput" name="qualifications" id="qualifications"></textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><span style="color:red">*</span> JOB DESCRIPTIONS</label>
                            <textarea placeholder=" Job Descriptions ..." rows="3" class="form-control finput" name="job_descriptions" id="job_descriptions"></textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label"><span style="color:red">*</span> Please attach the following documents:</label>
                            <div class='callout callout-success'>
                            <h4>*Job Description <br>
                            *Specialty Test / Additional Tests with Answer Key (Optional)
                            </h4> 
                                Before uploading a file, please read below instructions : <br/>
                                * File format should be : PDF, XLSX file format<br/>
                            </div>
                            <input type="file" class="form-control finput" style="" name="documents[]" id="documents" multiple accept=".xlsx, .pdf">
                            <div class="gallery" style="margin-bottom:5px; margin-top:15px"></div>
                            <a class="btn btn-xs btn-danger" style="display:none; margin-left:10px" id="removeImageHeader" href="#"><i class="fa fa-remove"></i></a>
                        </div>
                    </div> 
                </div>
            </div>

            <div class='panel-heading text-center'>
                <h4>Required Assets</h4>
            </div>
            <div class="card8">
                <div class="row">
                    <div class="col-md-12">              
                     <table class="table table-bordered" id="asset-items">
                            <tbody id="bodyTable">
                                <tr class="tbl_header_color dynamicRows">
                                    <th width="30%" class="text-center">{{ trans('message.table.item_description') }}</th> 
                                    <th width="15%" class="text-center">Digits Code</th>
                                    <th width="25%" class="text-center">{{ trans('message.table.category_id_text') }}</th>                                                         
                                    <th width="20%" class="text-center">Sub Category</th> 
                                    <th width="7%" class="text-center">Qty</th> 
                                    <th width="5%" class="text-center">{{ trans('message.table.action') }}</th>
                                </tr>
                                <tr id="tr-table">
                                    <tr>
                    
                                    </tr>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr id="tr-table1" class="bottom">
                                    <td colspan="4">
                                        {{-- <input type="button" id="add-Row" name="add-Row" class="btn btn-primary add" value='Add Item' /> --}}
                                        <button class="red-tooltip" data-toggle="tooltip" data-placement="right" id="add-Row" name="add-Row" title="Add Row"><div class="iconPlus" id="bigplus"></div></button>
                                    </td>
                                    <td align="left" colspan="1">
                                        <input type='number' name="quantity_total" class="form-control text-center" id="quantity_total" readonly>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                                              
                        <div class="col-md-12">
                            <hr/>
                            <div class="row" id="application_div"> 
                                <label class="require control-label col-md-2" required><span style="color:red">*</span> {{ trans('message.form-label.application') }}</label>
                                    @foreach($applications as $data)
                                        <div class="col-md-2">
                                            <label class="checkbox-inline control-label col-md-12"><input type="checkbox"  class="application" id="{{$data->app_name}}" name="application[]" value="{{$data->app_name}}" >{{$data->app_name}}</label>
                                            <br>
                                        </div>
                                    @endforeach
                            </div>
                            <hr/>
                        </div>

                        <div class="col-md-12" id="application_others_div">
                            <div class="row">
                                <label class="require control-label col-md-2"><span style="color:red">*</span> {{ trans('message.form-label.application_others') }}</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control"  id="application_others" name="application_others"  required placeholder="e.g. VIBER, WHATSAPP, TELEGRAM" onkeyup="this.value = this.value.toUpperCase();">
                                </div>
                            </div>
                        </div>
                        <hr/>
                        <div class="col-md-12">
                            <div class="form-group text-center">
                                <label>CAN'T FIND WHAT YOU ARE LOOKING FOR?</label>
                                <a href='{{CRUDBooster::adminpath("header_request/download")."?return_url=".urlencode(Request::fullUrl())}}'>CHECK HERE</a>
                            </div>
                        </div>
                    </div> 
               </div>
            </div>
            <div class='panel-footer'>
                <a href="{{ CRUDBooster::mainpath() }}" id="btn-cancel" class="btn btn-default">{{ trans('message.form.cancel') }}</a>
                <button class="btn btn-primary pull-right" type="submit" id="btnSubmit"> <i class="fa fa-save" ></i> {{ trans('message.form.save') }}</button>
            </div>  
    </form>
   
                       


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

    var tableRow = 1;
    $('#department').select2({})
    $('#position').select2({})
    $("#application_div").hide();
    $("#application_others_div").hide();

    $("#application_others").removeAttr('required');
    $(".application").removeAttr('required');

    $(".date").datetimepicker({
        minDate:moment().millisecond(0).second(0).minute(0).hour(0),
        viewMode: "days",
        format: "YYYY-MM-DD",
        dayViewHeaderFormat: "MMMM YYYY",
    });

    $('#OTHERS').change(function() {
        var ischecked= $(this).is(':checked');
        if(ischecked == false){
            $("#application_others_div").hide();
            $("#application_others").removeAttr('required');
        }else{
            $("#application_others_div").show();
            $("#application_others").attr('required', 'required');
        }	
    });

    var app_count = 0;

    $('.application').change(function() {
        var ischecked= $(this).is(':checked');
        if(ischecked == false){
            app_count--;
        }else{
            app_count++;
        }

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
        var rep = $(this);;
        if(rep.val() === "OTHERS" && rep.is(':checked')){
            $("#other_email_domain").show();
        }else{
            $("#other_email_domain").hide();
            $('#other_email').val("");
        }
    });

     //other Required Exam
      //checkbox validations
      $(".required_exams").change(function() {
        var rep = $(this);
        if(rep.val() === "OTHERS" && rep.is(':checked')){
            $("#other_required_exams_div").show();
        }else{
            $("#other_required_exams_div").hide();
            $('#other_required_exams').val("");
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

    //DEPARTMENT CHANGE
    $('#department').change(function(){
        var department_id =  this.value;
        $.ajax
        ({ 
            type: 'POST',
            url: "{{ route('get-positions') }}",
            data: {
                "id": department_id
            },
            success: function(result) {
                var i;
                var showData = [];
                showData[0] = "<option value=''>-- Please choose position --</option>";
                for (i = 0; i < result.length; ++i) {
                    var j = i + 1;
                    showData[j] = "<option value='"+result[i].position_description+"'>"+result[i].position_description+"</option>";
                }
                $('#position').attr('disabled', false);
                jQuery('#position').html(showData);        
            }
        });
    });
    


    $(document).ready(function() {
        const fruits = [];
        $("#add-Row").click(function() {
            event.preventDefault(); 
            //$("#application_div").show();
            var description = "";
            var count_fail = 0;
            $('.itemDesc').each(function() {
                description = $(this).val();
                if (description == null) {

                    alert("Please fill Item Description !");
                    count_fail++;

                } else if (description == "") {

                    alert("Please fill Item Description !");
                    count_fail++;

                }else{
                    count_fail = 0;
                }
            });
            
            tableRow++;

            if(count_fail == 0){

                var newrow =
                '<tr>' +

                    '<td >' +
                        '<input type="text" placeholder="Search Item ..." class="form-control text-center finput itemDesc" id="itemDesc'+ tableRow +'" data-id="'+ tableRow +'"   name="item_description[]"  required maxlength="100">' +
                          '<ul class="ui-autocomplete ui-front ui-menu ui-widget ui-widget-content" data-id="'+ tableRow +'" id="ui-id-2'+ tableRow +'" style="display: none; top: 60px; left: 15px; width: 100%;">' +
                           '<li>Loading...</li>' +
                          '</ul>' +
                         '<div id="display-error'+ tableRow +'"></div>'+
                    '</td>' + 

                    '<td>' + 
                        '<input type="text" onkeyup="this.value = this.value.toUpperCase();" class="form-control text-center digits_code sinput" data-id="'+ tableRow +'" id="digits_code'+ tableRow +'"  name="digits_code[]"   maxlength="100" readonly>' +
                        '<input type="hidden" onkeyup="this.value = this.value.toUpperCase();" class="form-control fixed_description sinput" data-id="'+ tableRow +'" id="fixed_description'+ tableRow +'"  name="fixed_description[]"   maxlength="100" readonly>' +
                        '<input type="hidden" onkeyup="this.value = this.value.toUpperCase();" class="form-control class_type sinput" data-id="'+ tableRow +'" id="class_type'+ tableRow +'"  name="class_type[]"   maxlength="100" readonly>' +
                    '</td>' +

                    // '<td>'+
                    //     '<select selected data-placeholder="- Select Category -" class="form-control drop'+ tableRow + '" name="category_id[]" data-id="' + tableRow + '" id="category_id' + tableRow + '" required style="width:100%">' +
                    //     '  <option value=""></option>' +
                    //     '        @foreach($categories as $data)'+
                    //     '        <option value="{{$data->category_description}}">{{$data->category_description}}</option>'+
                    //     '         @endforeach'+
                    //     '</select>'+
                    // '</td>' +

                    '<td>' + 
                        '<input type="text" onkeyup="this.value = this.value.toUpperCase();" class="form-control text-center category_id sinput" data-id="'+ tableRow +'" id="category_id'+ tableRow +'"  name="category_id[]"   maxlength="100" readonly>' +
                    '</td>' +
                    '<td>' + 
                        '<input type="text" onkeyup="this.value = this.value.toUpperCase();" class="form-control text-center sub_category_id sinput" data-id="'+ tableRow +'" id="sub_category_id'+ tableRow +'"  name="sub_category_id[]"   maxlength="100" readonly>' +
                    '</td>' +  
                    // '<td>'+
                    //     '<select selected data-placeholder="- Select Sub Category -" class="form-control sub_category_id" name="sub_category_id[]" data-id="' + tableRow + '" id="sub_category_id' + tableRow + '" required style="width:100%">' +
                    //     '  <option value=""></option>' +
                    //     '        @foreach($sub_categories as $data)'+
                    //     '        <option value="{{$data->class_description}}">{{$data->class_description}}</option>'+
                    //     '         @endforeach'+
                    //     '</select>'+
                    // '</td>' +

                    '<td><input class="form-control text-center quantity_item" type="number" required name="quantity[]" id="quantity' + tableRow + '" data-id="' + tableRow  + '"  value="1" min="0" max="9999999999" step="any" onKeyPress="if(this.value.length==4) return false;" oninput="validity.valid;" readonly></td>' +
  
                    '<td>' +
                        '<button id="deleteRow" name="removeRow" data-id="' + tableRow + '" class="btn btn-danger removeRow"><i class="glyphicon glyphicon-trash"></i></button>' +
                    '</td>' +

                '</tr>';
                $(newrow).insertBefore($('table tr#tr-table1:last'));
                // $('#sub_category_id'+tableRow).attr('disabled', true);
                // $('#category_id'+tableRow).select2({});
                // $('.js-example-basic-multiple').select2();
                // $('.sub_category_id').select2({
                // placeholder_text_single : "- Select Sub Category -"});

                $('#app_id'+tableRow).change(function(){
                    if($('#app_id'+$(this).attr("data-id")).val() != null){
                        var arrx = $(this).val();
                        execute = 0;
                    }else{
                        var arrx = "";
                        execute++;
                    }
                    var s = arrx;

                    if(s.includes("OTHERS")){
                        $('#AppOthers'+$(this).attr("data-id")).show();
                        $('#AppOthers'+$(this).attr("data-id")).attr('required', 'required');
                    }else{                   
                        $('#AppOthers'+$(this).attr("data-id")).hide();
                        $('#AppOthers'+$(this).attr("data-id")).removeAttr('required');
                    }

                });

                $(document).on('keyup', '#itemDesc'+tableRow, function(ev) {
                    var category =  $('#category_id'+$(this).attr("data-id")).val();
                    var description = this.value;
                    if(description.includes("LAPTOP") && category == "IT ASSETS"){
                        $('#app_id'+$(this).attr("data-id")).attr('disabled', false);
                    }else{
                        $('#app_id'+$(this).attr("data-id")).attr('disabled', true);
                    }
                });

                //SEARCH DIGITS CODE
                var stack = [];
                var token = $("#token").val();
                var searchcount = <?php echo json_encode($tableRow); ?>;
                let countrow = 1;
                $(function(){
                    countrow++;
                    $('#itemDesc'+tableRow).autocomplete({
                        source: function (request, response) {
                        $.ajax({
                            url: "{{ route('item.erf.it.search') }}",
                            dataType: "json",
                            type: "POST",
                            data: {
                                "_token": token,
                                "search": request.term
                            },
                            success: function (data) {
                                if(data.items === null){
                                    swal({  
                                        type: 'error',
                                        title: 'No Found Item',
                                        icon: 'error',
                                        confirmButtonColor: "#367fa9",
                                    });
                                }else{ 
                                //var rowCount = $('#asset-items tr').length;
                                //myStr = data.sample;   
                                if (data.status_no == 1) {

                                    $("#val_item").html();
                                    var data = data.items;
                                    $('#ui-id-2'+tableRow).css('display', 'none');

                                    response($.map(data, function (item) {
                                        return {
                                            id:                         item.id,
                                            asset_code:                 item.asset_code,
                                            digits_code:                item.digits_code,
                                            asset_tag:                  item.asset_tag,
                                            serial_no:                  item.serial_no,
                                            value:                      item.item_description,
                                            category_description:       item.category_description,
                                            class_description:          item.class_description,
                                            item_cost:                  item.item_cost,
                                            class_type:                 item.class_type
                                        }

                                    }));

                                } else {

                                    $('.ui-menu-item').remove();
                                    $('.addedLi').remove();
                                    $("#ui-id-2"+tableRow).append($("<li class='addedLi'>").text(data.message));
                                    var searchVal = $('#itemDesc'+tableRow).val();
                                    if (searchVal.length > 0) {
                                        $("#ui-id-2"+tableRow).css('display', 'block');
                                    } else {
                                        $("#ui-id-2"+tableRow).css('display', 'none');
                                    }
                                }
                            }
                        }
                        })
                        },
                        select: function (event, ui) {
                            var e = ui.item;
                            console.log(e);
                            if (e.id) {
                                $("#digits_code"+$(this).attr("data-id")).val(e.digits_code);
                                $("#supplies_cost"+$(this).attr("data-id")).val(e.item_cost);
                                $('#itemDesc'+$(this).attr("data-id")).val(e.value);
                                $('#itemDesc'+$(this).attr("data-id")).attr('readonly','readonly');
                                $('#fixed_description'+$(this).attr("data-id")).val(e.value);
                                $('#category_id'+$(this).attr("data-id")).val(e.category_description);
                                $('#sub_category_id'+$(this).attr("data-id")).val(e.class_description);
                                $('#class_type'+$(this).attr("data-id")).val(e.class_type);
                                $('#val_item').html('');

                                if($.inArray(e.class_type,['LAPTOP','DESKTOP','MACBOOK']) > -1){
                                    $("#application_div").show();
                                }
                                return false;
                            }

                        },

                        minLength: 1,
                        autoFocus: true
                    });

                });

                $('#AppOthers'+tableRow).hide();
                $('#AppOthers'+tableRow).removeAttr('required');

                //SUB CATEGORY CHANGE
                $('.sub_category_id').change(function(){
                    var sub_category_id =  this.value;
                    var fruits = [];
                    $(".sub_category_id :selected").each(function() {
                        fruits.push(this.value.toLowerCase().replace(/\s/g, ''));
                    });
                    console.log(fruits);
                    if( fruits.includes("COMPUTER EQUIPMENT") || fruits.includes("desktop")){

                        $("#application_div").show();
                    }else{

                        $("#application_div").hide();
                        $("#application_others_div").hide();
                        $(".application").prop('checked', false);
                        $(".application_others").prop('checked', false);
                        $("#application_others").removeAttr('required');
                        //$(".application").removeAttr('required');

                    }

                });
                

                $("#quantity_total").val(calculateTotalQuantity());
                
            }

        });
        
        //deleteRow
        $(document).on('click', '.removeRow', function() {
            // $("#application_div").hide();
            // $("#application_others_div").hide();
            // $(".application").prop('checked', false);
            // $(".application_others").prop('checked', false);
            // $("#application_others").removeAttr('required');
            var id_data = $(this).attr("data-id");
            if($.inArray($("#class_type"+id_data).val().toLowerCase().replace(/\s/g, ''), ['laptop','desktop','macbook']) > -1){
                $("#application_div").hide();
                $("#application_div").val("");
                $(".application").prop('checked', false);
                $(".application_others").prop('checked', false);
                $("#application_others_div").hide();
                $("#application_others").removeAttr('required');

            }

            if ($('#asset-items tbody tr').length != 1) { //check if not the first row then delete the other rows
                tableRow--;

                $(this).closest('tr').remove();

                $("#quantity_total").val(calculateTotalQuantity());

                return false;
            }
        });

    });


    $(document).on('keyup', '.quantity_item', function(ev) {

        $("#quantity_total").val(calculateTotalQuantity());

    });

    $(document).on('keyup', '.cost_item', function(ev) {
            var id = $(this).attr("data-id");
            var rate = parseFloat($(this).val());

            var qty = $("#quantity" + id).val();
            var price = calculatePrice(qty, rate).toFixed(2); // this is for total Value in row

            $("#total_unit_cost" + id).val(price);
            $("#quantity_total").val(calculateTotalQuantity());
            $("#cost_total").val(calculateTotalValue());
            $("#total").val(calculateTotalValue2());

            var total_checker = $("#total").val();

    });

    function calculatePrice(qty, rate) {
        if (qty != 0) {
        var price = (qty * rate);
        return price;
        } else {
        return '0';
        }
    }

    function calculateTotalQuantity() {
        var totalQuantity = 0;
        $('.quantity_item').each(function() {

        totalQuantity += parseInt($(this).val());
        });
        return totalQuantity;
    }

    function calculateTotalValue() {
        var totalQuantity = 0;
        var newTotal = 0;
        $('.cost_item').each(function() {
        totalQuantity += parseFloat($(this).val());

        });
        newTotal = totalQuantity.toFixed(2);
        return newTotal;
    }

    function calculateTotalValue2() {
        var totalQuantity = 0;
        var newTotal = 0;
        $('.total_cost_item').each(function() {
        totalQuantity += parseFloat($(this).val());

        });
        newTotal = totalQuantity.toFixed(2);
        return newTotal;
    }

    $(document).ready(function() {
        $("#ERFRequest").submit(function() {
            $("#btnSubmit").attr("disabled", true);
            return true;
        });
    });

    //SUBMIT REQUEST
    $("#btnSubmit").click(function(event) {
        event.preventDefault();
        var countRow = $('#asset-items tfoot tr').length - 1;
        var reg = /^0/gi;
        // var value = $('.vvalue').val();
        if($("#requested_date").val() === ""){
            swal({
                type: 'error',
                title: 'Please select Requested Date!',
                icon: 'error',
                confirmButtonColor: "#367fa9",
            }); 
            event.preventDefault(); // cancel default behavior
            return false;
        }else if($("#department").val() === ""){
            swal({
                type: 'error',
                title: 'Please select Department!',
                icon: 'error',
                confirmButtonColor: "#367fa9",
            }); 
            event.preventDefault(); // cancel default behavior
            return false;
        }else if($("#date_needed").val() === ""){
            swal({
                type: 'error',
                title: 'Please select Needed Date!',
                icon: 'error',
                confirmButtonColor: "#367fa9",
            }); 
            event.preventDefault(); // cancel default behavior
            return false;
        }else if($("#position").val() === ""){
            swal({
                type: 'error',
                title: 'Required Position!',
                icon: 'error',
                confirmButtonColor: "#367fa9",
            }); 
            event.preventDefault(); // cancel default behavior
            return false;
        }else if($("#work_location").val() === ""){
            swal({
                type: 'error',
                title: 'Required Work Location!',
                icon: 'error',
                confirmButtonColor: "#367fa9",
            }); 
            event.preventDefault(); // cancel default behavior
            return false;
        }else if($("#salary_range").val() === ""){
            swal({
                type: 'error',
                title: 'Required Salary Range!',
                icon: 'error',
                confirmButtonColor: "#367fa9",
            }); 
            event.preventDefault(); // cancel default behavior
            return false;
        }else if(!$("#salary_range").val().includes("-")){
            swal({
                type: 'error',
                title: 'Invalid Salary Range! please refer to the ff(xx,xxx-xx,xxx).',
                icon: 'error',
                confirmButtonColor: "#367fa9",
            }); 
            event.preventDefault(); // cancel default behavior
            return false;
        }else if(!$(".schedule").is(':checked')){
                swal({
                    type: 'error',
                    title: 'Please choose Schedule!',
                    icon: 'error',
                    confirmButtonColor: "#367fa9",
                }); 
                event.preventDefault(); // cancel default behavior
                return false;
        }else if(!$(".allow_wfh").is(':checked')){
                swal({
                    type: 'error',
                    title: 'Please choose Allow Wfh!',
                    icon: 'error',
                    confirmButtonColor: "#367fa9",
                }); 
                event.preventDefault(); // cancel default behavior
                return false;
        }else if(!$(".manpower").is(':checked')){
                swal({
                    type: 'error',
                    title: 'Please choose Manpower!',
                    icon: 'error',
                    confirmButtonColor: "#367fa9",
                }); 
                event.preventDefault(); // cancel default behavior
                return false;
        }else if(!$(".manpower_type").is(':checked')){
                swal({
                    type: 'error',
                    title: 'Please choose Manpower Type!',
                    icon: 'error',
                    confirmButtonColor: "#367fa9",
                }); 
                event.preventDefault(); // cancel default behavior
                return false;
        }else if(!$(".shared_files").is(':checked')){
                swal({
                    type: 'error',
                    title: 'Please Shared Files!',
                    icon: 'error',
                    confirmButtonColor: "#367fa9",
                }); 
                event.preventDefault(); // cancel default behavior
                return false;
        }else if(!$(".employee_interaction").is(':checked')){
                swal({
                    type: 'error',
                    title: 'Please interact with!',
                    icon: 'error',
                    confirmButtonColor: "#367fa9",
                }); 
                event.preventDefault(); // cancel default behavior
                return false;
        }else if(!$(".asset_usage").is(':checked')){
                swal({
                    type: 'error',
                    title: 'Please choose what will you be using the PC for!',
                    icon: 'error',
                    confirmButtonColor: "#367fa9",
                }); 
                event.preventDefault(); // cancel default behavior
                return false;
        }else if(!$(".email_domain").is(':checked')){
                swal({
                    type: 'error',
                    title: 'Please choose Email Domain!',
                    icon: 'error',
                    confirmButtonColor: "#367fa9",
                }); 
                event.preventDefault(); // cancel default behavior
                return false;
        }else if($('#qualifications').val() === ""){
            swal({
                type: 'error',
                title: 'Qualifications required!',
                icon: 'error',
                confirmButtonColor: "#367fa9",
            });
            event.preventDefault();
        }else if($('#job_descriptions').val() === ""){
            swal({
                type: 'error',
                title: 'Job Description required!',
                icon: 'error',
                confirmButtonColor: "#367fa9",
            });
            event.preventDefault();
        }else if($('#documents').val() === ""){
            swal({
                type: 'error',
                title: 'Upload file documents required!',
                icon: 'error',
                confirmButtonColor: "#367fa9",
            });
            event.preventDefault();
        }
        // else if (countRow == 1) {
        //     swal({
        //         type: 'error',
        //         title: 'Please add an item!',
        //         icon: 'error',
        //         confirmButtonColor: "#367fa9",
        //     }); 
        //     event.preventDefault(); // cancel default behavior
        // }
        // else if (countRow == 1) {
        // swal({
        //     type: 'error',
        //     title: 'Please add an item!',
        //     icon: 'error',
        //     confirmButtonColor: "#367fa9",
        // }); 
        //     event.preventDefault(); // cancel default behavior
        // }
        else{

            if($('#show_replacement_of').is(":visible")){
                if($('#replacement_of').val() === ""){
                    swal({
                        type: 'error',
                        title: 'Replacement Of required!',
                        icon: 'error',
                        confirmButtonColor: "#367fa9",
                    });
                    event.preventDefault();
                    return false;
                }
            }
                
            if($('#absorption_div').is(":visible")){
                if($('#absorption').val() === ""){
                    swal({
                        type: 'error',
                        title: 'Absorption required!',
                        icon: 'error',
                        confirmButtonColor: "#367fa9",
                    });
                    event.preventDefault();
                    return false;
                }
            }

            if($('#other_email_domain').is(":visible")){
                if($('#other_email').val() === ""){
                    swal({
                        type: 'error',
                        title: 'Other Email Domain required!',
                        icon: 'error',
                        confirmButtonColor: "#367fa9",
                    });
                    event.preventDefault();
                    return false;
                }
            }
            
             //header image validation
             for (var i = 0; i < $("#documents").get(0).files.length; ++i) {
                var file1=$("#documents").get(0).files[i].name;
                if(file1){                        
                    var file_size=$("#documents").get(0).files[i].size;
                        var ext = file1.split('.').pop().toLowerCase();                            
                        if($.inArray(ext,['xlsx','pdf'])===-1){
                            swal({
                                type: 'error',
                                title: 'Invalid File!',
                                icon: 'error',
                                customClass: 'swal-wide'
                            });
                            event.preventDefault();
                            return false;
                        }                                          
                }
             }
    
                var item = $("input[name^='item_description']").length;
                var item_value = $("input[name^='item_description']");
                for(i=0;i<item;i++){
                    if(item_value.eq(i).val() == 0 || item_value.eq(i).val() == null){
                        swal({  
                                type: 'error',
                                title: 'Item Description cannot be empty!',
                                icon: 'error',
                                confirmButtonColor: "#367fa9",
                            });
                            event.preventDefault();
                            return false;
                    } 
            
                } 
                var sub_cat = $(".sub_category_id option").length;
                var sub_cat_value = $('.sub_category_id').find(":selected");
                for(i=0;i<sub_cat;i++){
                    if(sub_cat_value.eq(i).val() == ""){
                        swal({  
                                type: 'error',
                                title: 'Please select Sub Category!',
                                icon: 'error',
                                confirmButtonColor: "#367fa9",
                            });
                            event.preventDefault();
                            return false;
                    } 
            
                } 
                if(countRow){
                    var sub_cat = $("input[name^='class_type']").length;
                    var sub_cat_value = $("input[name^='class_type']");
                    for(i=0;i<sub_cat;i++){
                        var val = sub_cat_value.eq(i).val() || '';
                        if(app_count == 0 && $.inArray((sub_cat_value.eq(i).val() || '').toLowerCase().replace(/\s/g, ''),['laptop','desktop','macbook']) > -1){
                            swal({  
                                    type: 'error',
                                    title: 'Please choose an Application!',
                                    icon: 'error',
                                    confirmButtonColor: "#367fa9",
                                });
                                event.preventDefault();
                                return false;
                        } 
                
                    } 
                }
               
                // if(countRow){
                //     if(app_count == 0){
                //                 swal({  
                //                     type: 'error',
                //                     title: 'Please choose an Application!',
                //                     icon: 'error',
                //                     confirmButtonColor: "#367fa9",
                                    
                //                 });
                //                 event.preventDefault();
                //                 return false;
                //     }
                // }
               
                swal({
                    title: "Are you sure?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#41B314",
                    cancelButtonColor: "#F9354C",
                    confirmButtonText: "Yes, send it!",
                    width: 450,
                    height: 200
                    }, function () {
                        $("#ERFRequest").submit();                                                   
                });
                
   
        }
    
    });

    //BACK FORM
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

    //NUMBERS VALIDATION
    $(document).on("keyup","#salary_range", function (e) {
        if (e.which >= 37 && e.which <= 40) return;
            if (this.value.charAt(0) == ".") {
                this.value = this.value.replace(
                /\.(.*?)(\.+)/,
                function (match, g1, g2) {
                    return "." + g1;
                }
                );
            }
            if (e.key == "." && this.value.split(".").length > 2) {
                this.value =
                this.value.replace(/([\d,]+)([\.]+.+)/, "$1") +
                "." +
                this.value.replace(/([\d,]+)([\.]+.+)/, "$2").replace(/\./g, "");
                return;
            }
        $(this).val(function (index, value) {
            value = value.replace(/[^-0-9.]+/g, "");
            let parts = value.toString().split(".");
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            return parts.join(".");
        });
    });

    $('#salary_range').on("keyup", function() {
        var value =  $(this).val();
        if(!value.includes("-")){
            $('#display-error').html("<span id='notif' class='label label-danger'> Invalid Salary Range! please refer to the ff.(xx,xxx-xx,xxx)</span>")
        }else{
            $('#display-error').html('')  
        }
    });

</script>
@endpush