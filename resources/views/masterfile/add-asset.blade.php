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
                    <label class="control-label">Digits Code</label>
                    <input type="text" class="form-control auto finput" name="digits_code" id="search" placeholder="Search Item">
                    <ul class="ui-autocomplete ui-front ui-menu ui-widget ui-widget-content" id="ui-id-2" style="display: none; top: 60px; left: 15px; width: 520px;">
                        <li>Loading...</li>
                    </ul>
                </div>
                <div id="display-error">
                    <span class="test"></span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                <label class="control-label">Description</label>
                    <input type="hidden" class="form-control addinput" name="item_id" id="item_id" readonly>
                    <input type="text" class="form-control addinput" name="item_description" id="item_description" readonly>
                </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                <label class="control-label">Cost</label>
                    <input type="text" class="form-control finput" name="cost" id="cost">
                </div>
            </div>
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

            var stack = [];
            var token = $("#token").val();
            $(function(){
                $('#search').autocomplete({
                    source: function (request, response) {
                    $.ajax({
                        url: "{{ route('search-digits-code') }}",
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
                                $('#ui-id-2').css('display', 'none');

                                response($.map(data, function (item) {
                                    return {
                                        id:                         item.id,
                                        digits_code:                item.digits_code,
                                        value:                      item.item_description,
                                        category_description:       item.category_description,
                                        item_cost:                  item.item_cost
                                    
                                    }

                                }));

                            } else {

                                $('.ui-menu-item').remove();
                                $('.addedLi').remove();
                                $("#ui-id-2").append($("<li class='addedLi'>").text(data.message));
                                var searchVal = $('#search').val();
                                if (searchVal.length > 0) {
                                    $("#ui-id-2").css('display', 'block');
                                } else {
                                    $("#ui-id-2").css('display', 'none');
                                }
                            }
                        }
                    }
                    })
                    },
                    select: function (event, ui) {
                        var e = ui.item;

                        if (e.id) {
                        
     
                            $('#search').val(e.digits_code);
                            $('#search').attr('readonly','readonly');
                            $('#item_description').val(e.value);
                            $('#item_id').val(e.id);
                            $("#cost").attr("placeholder", e.item_cost);
                            return false;

                        }
                    },

                    minLength: 1,
                    autoFocus: true
                });

            });
        });

        

    </script>
@endpush