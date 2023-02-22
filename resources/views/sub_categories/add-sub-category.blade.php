@extends('crudbooster::admin_template')
    @push('head')
    
        <style type="text/css">   
            .select2-container--default .select2-selection--multiple .select2-selection__choice{color:black;}
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
                background-color: #fff;
            }

            input.addinput:read-only {
                background-color: #f5f5f5;
            }

            .input-group-addon {
                background-color: #f5f5f5 !important;
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
    <div class='panel-heading'>Add Sub Category Form</div>

    <form action='{{CRUDBooster::mainpath('add-save')}}' method="POST" id="AddCategoryForm" enctype="multipart/form-data">
        <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">

        <div class='panel-body'>
           <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label">FA Code From</label>
                    <input type="text" class="form-control finput" name="from_code" id="from_code" onKeyPress="if(this.value.length==8) return false;" onChange="checkCodeRangeFromAvailability()">
                    <input type="hidden" id="from_value">
                    <div id="code-range-from-availability-status"></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label">FA Code To</label>
                    <input type="text" class="form-control finput" name="from_to" id="from_to" onKeyPress="if(this.value.length==8) return false;" onChange="checkCodeRangeToAvailability()">
                    <input type="hidden" id="to_value">
                    <div id="code-range-to-availability-status"></div>
                </div>
            </div>
          </div>
           <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                    <label class="control-label">Category</label>
                        <select selected data-placeholder="- Select Category -" class="form-control drop" name="category_id" data-id="" id="category_id" required style="width:100%">
                            <option value=""></option>
                            @foreach($categories as $data)
                              <option value="{{$data->id}}">{{$data->category_description}}</option>
                             @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Description</label>
                        <input type="text" class="form-control finput" name="category_description" id="category_description" >
                    </div>
                </div>
          </div>
        
        </div>
        
        <div class='panel-footer'>
            <a href="{{ CRUDBooster::mainpath() }}" class="btn btn-default">{{ trans('message.form.cancel') }}</a>
            <button class="btn btn-success pull-right" type="submit" id="btnSubmit"> <i class="fa fa-plus-circle" ></i>  Add</button>
        </div>
    </form>


</div>

@endsection

@push('bottom')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#category_id').select2({placeholder_text_single : "-- Select --"})

            $("#btnSubmit").click(function(event) {
                event.preventDefault();
                if($("#from_code").val() === ""){
                    swal({
                        type: 'error',
                        title: 'FA From Code Required!',
                        icon: 'error',
                        confirmButtonColor: "#367fa9",
                    }); 
                    event.preventDefault(); // cancel default behavior
                    return false;
                }else if($("#from_code").val().length !== 8){
                    swal({
                        type: 'error',
                        title: 'Invalid FA From code Length!',
                        icon: 'error',
                        confirmButtonColor: "#367fa9",
                    }); 
                    event.preventDefault(); // cancel default behavior
                    return false;
                }else if($("#from_to").val() === ""){
                    swal({
                        type: 'error',
                        title: 'FA To Code Required!',
                        icon: 'error',
                        confirmButtonColor: "#367fa9",
                    }); 
                    event.preventDefault(); // cancel default behavior
                    return false;
                }else if($("#from_to").val().length !== 8){
                    swal({
                        type: 'error',
                        title: 'Invalid FA To code Length!',
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
                        confirmButtonText: "Yes, send it!",
                        }, function () {
                            $("#AddCategoryForm").submit();                                                   
                    });
                }
                        
            });

           
        });
        //check from code availability in database
        function checkCodeRangeFromAvailability() {
            $.ajax({
                url: "{{ route('getRangeCodeFrom') }}",
                dataType: "json",
                data:'code='+$("#from_code").val(),
                type: "POST",
                success:function(data){
                    console.log(data);
                    $("#code-range-from-availability-status").html(data.item);
                    $('#from_value').val(data.disabled);
                    if($('#from_value').val() == 1 && $('#to_value').val() == 1){
                        $('#btnSubmit').attr('disabled','disabled');
                    }else if($('#from_value').val() == 0 && $('#to_value').val() == 1){
                        $('#btnSubmit').attr('disabled','disabled');
                    }else if($('#from_value').val() == 1 && $('#to_value').val() == 0){
                        $('#btnSubmit').attr('disabled','disabled');
                    }else{
                        $('#btnSubmit').prop("disabled", false);
                    }
                },
            error:function (){}
            });
        }

        //check to Code availability in database
        function checkCodeRangeToAvailability() {
            $.ajax({
                url: "{{ route('getRangeCodeTo') }}",
                dataType: "json",
                data:'code='+$("#from_to").val(),
                type: "POST",
                success:function(data){
                    console.log(data);
                    $("#code-range-to-availability-status").html(data.item);
                    $('#to_value').val(data.disabled);
                    if($('#to_value').val() == 1 && $('#from_value').val() == 1){
                        $('#btnSubmit').attr('disabled','disabled');
                    }else if($('#to_value').val() == 0 && $('#from_value').val() == 1){
                        $('#btnSubmit').attr('disabled','disabled');
                    }else if($('#to_value').val() == 1 && $('#from_value').val() == 0){
                        $('#btnSubmit').attr('disabled','disabled');
                    }else{
                        $('#btnSubmit').prop("disabled", false);
                    }
                },
            error:function (){}
            });
        }
    </script>
@endpush