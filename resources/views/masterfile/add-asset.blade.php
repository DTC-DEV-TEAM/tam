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
    <div class='panel-heading'>Add Asset Form</div>

    <form action='{{CRUDBooster::mainpath('add-save')}}' method="POST" id="AddAssetForm" enctype="multipart/form-data">
        <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">

        <div class='panel-body'>
          <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label">Sub Category</label>
                    <select selected data-placeholder="- Select Sub Category -" class="form-control drop" name="sub_category_id" data-id="" id="sub_category_id" required style="width:100%">
                        <option value=""></option>
                        @foreach($sub_categories as $subData)
                            <option value="{{$subData->id}}">{{$subData->class_description}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                <label class="control-label">Description</label>
                    <input type="text" class="form-control finput" name="item_description" id="item_description">
                </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label"><span style="color:red">*</span> Location</label>
                    <select required selected data-placeholder="-- Please Select Location --" id="location" name="location" class="form-select select2" style="width:100%;">
                    @foreach($warehouse_location as $res)
                        <option value=""></option>
                        <option value="{{ $res->id }}">{{ $res->location }}</option>
                    @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                <label class="control-label">Cost</label>
                    <input type="text" class="form-control finput" name="cost" id="cost">
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
            $('#category_id, #sub_category_id, #location').select2({placeholder_text_single : "-- Select --"})

            $("#btnSubmit").click(function(event) {
            event.preventDefault();
                swal({
                    title: "Are you sure?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#41B314",
                    cancelButtonColor: "#F9354C",
                    confirmButtonText: "Yes, Save it!",
                    width: 450,
                    height: 200
                    }, function () {
                        $("#AddAssetForm").submit();                     
                });
            });
        });
    </script>
@endpush