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
    <div class='panel-heading'>Add Service</div>

    <form action='{{CRUDBooster::mainpath('add-save')}}' method="POST" id="addForm" enctype="multipart/form-data">
        <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">

        <div class='panel-body'>
           <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                    <label class="control-label">FA Code</label>
                        <select selected data-placeholder="Choose FA code" class="form-control" name="asset_code" data-id="" id="asset_code" required style="width:100%">
                            <option value=""></option>
                            @foreach($asset_code as $data)
                              <option value="{{$data->id}}">{{$data->class_description}}</option>
                             @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Description</label>
                        <input type="text" class="form-control finput" name="services_description" id="services_description" >
                    </div>
                </div>
          </div>

          <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label">Vendor</label>
                    <input type="text" class="form-control finput" name="vendor" id="vendor" >
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label">Location</label>
                    <select selected data-placeholder="Choose location" class="form-control" name="location" data-id="" id="location" required style="width:100%">
                        <option value=""></option>
                        @foreach($location as $data)
                          <option value="{{$data->id}}">{{$data->name}}</option>
                         @endforeach
                    </select>
                </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label">Amount</label>
                    <input type="text" class="form-control finput" name="amount" id="amount" >
                </div>
            </div>
          </div>
        
        </div>
        
        <div class='panel-footer'>
            <a href="{{ CRUDBooster::mainpath() }}" class="btn btn-default">{{ trans('message.form.cancel') }}</a>
            <button class="btn btn-success pull-right" type="submit" id="btnSubmit"> <i class="fa fa-save" ></i> Create</button>
        </div>
    </form>


</div>

@endsection

@push('bottom')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#asset_code, #location').select2({placeholder_text_single : "-- Select --"})

            $("#btnSubmit").click(function(event) {
                event.preventDefault();
                if($("#asset_code").val() === ''){
                    swal({
                        type: 'error',
                        title: 'FA Code Required!',
                        icon: 'error',
                        confirmButtonColor: "#367fa9",
                    }); 
                    event.preventDefault(); // cancel default behavior
                    return false;
                }else if($("#services_description").val() === ''){
                    swal({
                        type: 'error',
                        title: 'Description required!',
                        icon: 'error',
                        confirmButtonColor: "#367fa9",
                    }); 
                    event.preventDefault(); // cancel default behavior
                    return false;
                }else if($("#vendor").val() === ''){
                    swal({
                        type: 'error',
                        title: 'Vendor Required!',
                        icon: 'error',
                        confirmButtonColor: "#367fa9",
                    }); 
                    event.preventDefault(); // cancel default behavior
                    return false;
                }else if($("#location").val() === ''){
                    swal({
                        type: 'error',
                        title: 'Location!',
                        icon: 'error',
                        confirmButtonColor: "#367fa9",
                    }); 
                    event.preventDefault(); // cancel default behavior
                    return false;
                }else if($("#amount").val() === ""){
                    swal({
                        type: 'error',
                        title: 'Amount required!',
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
                            $("#addForm").submit();                                                   
                    });
                }
                        
            });

            $(document).on("keyup","#amount", function (e) {
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
           
        });
      
    </script>
@endpush