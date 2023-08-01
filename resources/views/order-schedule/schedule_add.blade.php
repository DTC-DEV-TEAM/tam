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
    </style>
@endpush
@section('content')

<div>
    <p><a title="Return" href="{{ CRUDBooster::mainpath() }}"><i class="fa fa-chevron-circle-left "></i>&nbsp; Back To List Data Order Schedule</a></p>

    <div class="panel panel-default">
        <div class="panel-heading">
            <strong>Add Order Schedule</strong>
        </div>

        <div class="panel-body" style="padding:20px 0px 0px 0px">
            <form action="{{ CRUDBooster::mainpath('add-save') }}" method="POST" id="orderScheduleForm" autocomplete="off">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                
                <div class="box-body" id="parent-form-area">

                    <div class="form-group header-group-0 col-sm-12" id="form-group-schedule_name" >
                        <label class="control-label col-sm-2">
                            Schedule Name
                            <span class="text-danger" title="This field is required">*</span>
                        </label>

                        <div class="col-sm-5">
                            <input type="text" required="" maxlength="50" class="form-control" name="schedule_name" id="schedule_name" value="" title="Schedule Name">

                            <div class="text-danger"></div>
                            <p class="help-block"></p>

                        </div>
                    </div>
                    
                    <div class="form-group form-datepicker header-group-0 col-sm-12" id="form-group-start_date" >
                        <label class="control-label col-sm-2">Start Date
                            <span class="text-danger" title="This field is required">*</span>
                        </label>

                        <div class="col-sm-5">
                            <div class="input-group">

                                <span class="input-group-addon"><a href="javascript:void(0)" onclick="$('#start_date').data('daterangepicker').toggle()"><i class="fa fa-calendar"></i></a></span>

                                <input type="text" title="Start Date" required class="form-control notfocus datetimepicker" name="start_date" id="start_date" value="">
                            </div>
                            <div class="text-danger"></div>
                            <p class="help-block"></p>
                        </div>
                    </div>
                    
                    <div class="form-group form-datepicker header-group-0 col-sm-12" id="form-group-end_date" >
                        <label class="control-label col-sm-2">End Date
                            <span class="text-danger" title="This field is required">*</span>
                        </label>

                        <div class="col-sm-5">
                            <div class="input-group">

                                <span class="input-group-addon"><a href="javascript:void(0)" onclick="$('#end_date').data('daterangepicker').toggle()"><i class="fa fa-calendar"></i></a></span>

                                <input type="text" title="End Date" required class="form-control notfocus datetimepicker" name="end_date" id="end_date" value="">
                            </div>
                            <div class="text-danger"></div>
                            <p class="help-block"></p>
                        </div>
                    </div>
                    
                    <div class="form-group header-group-0 col-sm-12" id="form-group-time_unit" >
                        <label class="control-label col-sm-2">Time Unit
                            <span class="text-danger" title="This field is required">*</span>
                        </label>

                        <div class="col-sm-5">
                            <input type="number" step="1" title="Time Unit" required class="form-control" name="time_unit" id="time_unit" value="">
                            <div class="text-danger"></div>
                            <p class="help-block"></p>
                        </div>
                    </div>
                    
                    <div class="form-group header-group-0 col-sm-12" id="form-group-period" >
                        <label class="control-label col-sm-2">Time Period
                            <span class="text-danger" title="This field is required">*</span>
                        </label>

                        <div class="col-sm-5">
                            <select selected data-placeholder="** Please select a Time Period" class="form-control" id="period" required name="period">
                                <option value=""></option>
                                <option value="DAY">DAY</option>
                                <option value="HOUR">HOUR</option>
                            </select>
                            <div class="text-danger"></div>
                            <p class="help-block"></p>
                        </div>
                    </div>

                    <div class="form-group header-group-0 col-sm-12" id="form-group-period" >
                        <div class="col-md-6">
                            <label class="require control-label"><span style="color:red">*</span> Select Privileges</label> <span> <input type="checkbox" id="select_all_privilege" style="margin-left:10px"> Select All Privileges<br></span><br>
                            @foreach($privileges as $data)
                            <div class="col-md-6">
                                <label class="checkbox-inline control-label col-md-12" ><br>
                                <input type="checkbox" class="privilege_id" name="privilege_id[]" value="{{$data->id}}" >{{$data->name}}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <br/>
                    <br/>
                    
                    
                </div><!-- /.box-body -->

                <div class="box-footer">
                    <div class='pull-right'>
                        <a href='{{ CRUDBooster::mainpath() }}' class='btn btn-default'>Cancel</a>
                        <button type='submit' class='btn btn-success' id="btnSubmit">Create</button>
                    </div>
                </div><!-- /.box-footer-->
            </form>
        </div>
    </div>
</div>

@endsection

@push('bottom')
<script type="text/javascript">

$(document).ready(function() {
    $('#period').select2({});
    $('#select_all_privilege').change(function() {
        if(this.checked) {
            $(".privilege_id").prop("checked", true);
        }
        else{
            $(".privilege_id").prop("checked", false);
        }
    });

    $("#btnSubmit").click(function(event) {
        event.preventDefault();

        if($("#schedule_name").val() === ""){
            swal({
                type: 'error',
                title: 'Schedule Name Required!',
                icon: 'error',
                confirmButtonColor: "#367fa9",
            }); 
            event.preventDefault(); // cancel default behavior
            return false;
        }else if($("#start_date").val() === ""){
            swal({
                type: 'error',
                title: 'Start Date!',
                icon: 'error',
                confirmButtonColor: "#367fa9",
            }); 
            event.preventDefault(); // cancel default behavior
            return false;
        }else if($("#end_date").val() === ""){
            swal({
                type: 'error',
                title: 'End Date Required!',
                icon: 'error',
                confirmButtonColor: "#367fa9",
            }); 
            event.preventDefault(); // cancel default behavior
            return false;
        }else if($("#time_unit").val() === ""){
            swal({
                type: 'error',
                title: 'Time Unit Required!',
                icon: 'error',
                confirmButtonColor: "#367fa9",
            }); 
            event.preventDefault(); // cancel default behavior
            return false;
        }else if($("#period").val() === ""){
            swal({
                type: 'error',
                title: 'Period Required!',
                icon: 'error',
                confirmButtonColor: "#367fa9",
            }); 
            event.preventDefault(); // cancel default behavior
            return false;
        }else{
            swal({
                title: "Are you sure?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#41B314",
                cancelButtonColor: "#F9354C",
                confirmButtonText: "Yes, create it!",
                }, function () {
                    $("#orderScheduleForm").submit();                                                   
            });
        }
                
        
    });
    
});


</script>

@endpush