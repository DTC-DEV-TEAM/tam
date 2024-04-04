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
                background-color: #eee;
            }

            input.addinput:read-only {
                background-color: #f5f5f5;
            }

            .input-group-addon {
                background-color: #f5f5f5 !important;
            }

            /* ::-webkit-input-placeholder {
            font-style: italic;
            }
            :-moz-placeholder {
            font-style: italic;  
            }
            ::-moz-placeholder {
            font-style: italic;  
            }
            :-ms-input-placeholder {  
            font-style: italic; 
            } */

            .ui-state-focus {
                background: none !important;
                background-color: #00a65a !important;
                border: 1px solid rgb(255, 254, 254) !important;
                color: #fff !important;
            }

            #asset-items th, td {
                border: 1px solid rgba(000, 0, 0, .5);
                padding: 8px;
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
    <div class='panel-heading'>
        Edit request form
    </div>

    <form action="{{ route('editRequestAssets')}}" method="POST" id="EditAssetRequest" enctype="multipart/form-data">
        <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
        <input type="hidden" value="{{$Header->request_type_id}}" name="request_type_id" id="request_type_id">
        <input type="hidden" value="{{$Header->requestid}}" name="headerID" id="headerID">
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
                   @if($Header->header_created_by != null || $Header->header_created_by != "")
                        <p>{{$Header->employee_name}}</p>
                    @else
                    <p>{{$Header->header_emp_name}}</p>
                    @endif
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

            @if(CRUDBooster::myPrivilegeId() == 8 || CRUDBooster::isSuperadmin())
                <div class="row">                           
                    <label class="control-label col-md-2">{{ trans('message.form-label.store_branch') }}:</label>
                    <div class="col-md-4">
                            <p>{{$Header->store_branch}}</p>
                    </div>
                </div>
            @endif
            <hr/>

            <div class="row"> 
                <label class="require control-label col-md-2"><span style="color:red">*</span>{{ trans('message.form-label.purpose') }}</label>
                @foreach($purposes as $data)
                    <div class="col-md-5">
                        <label class="radio-inline control-label col-md-5"><input type="radio" required  class="purpose" name="purpose" value="{{$data->id}}" {{ ($Header->purpose == $data->id) ? "checked" : ""}}>{{$data->request_description}}</label>
             
                        <br>
                    </div>
                @endforeach
            </div>

            <hr/>

            <div class="row">
                <div class="col-md-12">
                    <div class="box-body no-padding">
                        <div class="table-responsive">
                            <div class="pic-container">
                                <div class="pic-row">
                                    <table id="asset-items">
                                        <thead>
                                            <tr style="background-color:#00a65a; border: 0.5px solid #000;">
                                                <th style="text-align: center" colspan="11"><h4 class="box-title" style="color: #fff;"><b>{{ trans('message.form-label.asset_items') }}</b></h4></th>
                                            </tr>
                                            <tr class="tbl_header_color dynamicRows">
                                                <th width="25%" class="text-center"><span style="color:red">*</span>{{ trans('message.table.item_description') }}</th>
                                                <th width="15%" class="text-center">{{ trans('message.table.digits_code') }}</th>
                                                <th width="15%" class="text-center">{{ trans('message.table.category_id_text') }}</th>                                                                                                                    
                                                <th width="15%" class="text-center">{{ trans('message.table.sub_category_id_text') }}</th> 
                                                <th width="7%" class="text-center">{{ trans('message.table.wh_qty') }}</th>
                                                <th width="7%" class="text-center">{{ trans('message.table.prev_balance_quantity') }}</th> 
                                                <th width="7%" class="text-center">{{ trans('message.table.request_qty') }}</th> 
                                                <th width="15%" class="text-center">{{ trans('message.table.budget_range') }}</th> 
                                                <th width="5%" class="text-center">{{ trans('message.table.action') }}</th>
                                            </tr>
                                        </thead>
                                            
                                        <tbody id="bodyTable">
                                            @foreach($Body as $rowresult)
                                                <tr>
                                                    <input type="hidden" name="body_id[]" value="{{$rowresult->id}}">
                                                    <td style="text-align:center" height="10">{{$rowresult->item_description}}</td>
                                                    <td style="text-align:center" height="10">{{$rowresult->digits_code}}</td>
                                                    <td style="text-align:center" height="10">{{$rowresult->category_id}}</td>
                                                    <td style="text-align:center" height="10">{{$rowresult->sub_category_id}}</td>
                                                    <td style="text-align:center" height="10">{{$rowresult->wh_qty}}</td>
                                                    <td style="text-align:center" height="10">{{$rowresult->unserved_qty}}</td>
                                                    <td style="text-align:center" height="10" class="qty">{{$rowresult->quantity}}</td>
                                                    <td style="text-align:center" height="10">
                                                        <select selected data-placeholder="Choose" class="form-control budget" name="body_budget_range[]" id="budget${tableRow}" required required style="width:100%"> 
                                                            <option value=""></option> 
                                                            @foreach($budget_range as $data)
                                                                <option value="{{$data->description}}" {{ isset($rowresult->budget_range) && $rowresult->budget_range == $data->description ? 'selected' : '' }}>
                                                                    {{$data->description}}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td style="text-align:center" height="10">
                                                        <button id="deleteRowData{{$tableRow}}" value="{{$rowresult->id}}" name="deleteRowData" data-id="{{$tableRow}}" class="btn btn-danger deleteRowData btn-sm" data-toggle="tooltip" data-placement="bottom" title="Cancel"><i class="fa fa-trash"></i></button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>

                                        <tfoot>
                                            <tr id="tr-table1" class="bottom">
                                                <td colspan="6">
                                                    <a type="button" id="add-Row" name="add-Row" class="btn btn-success add"> <i class="fa fa-plus-circle"></i> Add new item</a>
                                                </td>
                                                <td align="left" colspan="1">
                                                    <input type="text" name="quantity_total" class="form-control text-center" id="quantity_total" readonly>
                                                </td>
                                                <td colspan="2"></td>
                                            </tr>
                                        </tfoot>

                                    </table>
                                </div>
                            </div>
                    
                        </div>
                    </div>
                </div>

                @if($Header->request_type_id == 1)
                    <div class="col-md-12 mt-2" id="application_div">
                        <hr/>
                        
                        <div class="row"> 
                            <label class="require control-label col-md-2" required>*{{ trans('message.form-label.application') }}</label>
                            @foreach($applications as $key => $data)
                                <div class="col-md-2">
                                    <label class="checkbox-inline control-label col-md-12"><input type="checkbox"  class="application" id="{{$data->app_name}}" name="application[]" value="{{$data->app_name}}" {{ (in_array($data->app_name,$applicationsExplode)) ? "checked" : ""}}>{{$data->app_name}}</label>
                                    <br>
                                </div>             
                            @endforeach
                        
                        </div>
                        <br>
                        <div class="row mt-2">
                            @if($Header->application_others != null || $Header->application_others != "")
                                <label class="control-label col-md-2">{{ trans('message.form-label.application_others') }}:</label>
                                <div class="col-md-4">
                                        <p>{{$Header->application_others}}</p>
                                </div>
                            @endif 
                        </div>
                        
                        <hr/>
                    </div>

                    <div class="col-md-12" id="application_others_div">
                        <div class="row">
                            <label class="require control-label col-md-2">*{{ trans('message.form-label.application_others') }}</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control"  id="application_others" name="application_others"  required placeholder="e.g. VIBER, WHATSAPP, TELEGRAM" onkeyup="this.value = this.value.toUpperCase();">
                            </div>
                        </div>
                        <hr/>
                    </div>
                @endif

                <div class="col-md-12" style="margin-top: 10px">
                    <div class="form-group">
                        <label>{{ trans('message.table.note') }}</label>
                        <textarea placeholder="{{ trans('message.table.comments') }} ..." rows="3" class="form-control finput" name="requestor_comments">{{ $Header->requestor_comments }}</textarea>
                    </div>
                </div>
            </div>
            <hr>
            @if($Header->request_type_id == 1)
                <div class="col-md-12">
                    <div class="form-group text-center">
                        <label>CAN'T FIND WHAT YOU ARE LOOKING FOR?</label>
                        <a href='{{CRUDBooster::adminpath("header_request/download")."?return_url=".urlencode(Request::fullUrl())}}'>CHECK HERE</a>
                    </div>
                </div>
            @endif
        </div>

        <div class='panel-footer'>
            <a href="{{ CRUDBooster::mainpath() }}" class="btn btn-default">{{ trans('message.form.cancel') }}</a>
            <button class="btn btn-success pull-right" type="submit" id="btnUpdate"> <i class="fa fa-save" ></i> {{ trans('message.form.update') }}</button>
        </div>
    </form>
</div>

@endsection
@push('bottom')
    <script type="text/javascript">
        $(function(){
            $('body').addClass("sidebar-collapse");
            $('#quantity_total').val(calculateTotalQuantity());
        });
        function preventBack() {
            window.history.forward();
        }
         window.onunload = function() {
            null;
        };
        setTimeout("preventBack()", 0);
        var token = $("#token").val();
        var tableRow = 1;
        $("#application_others_div").hide();
        $("#application_others").removeAttr('required');
    
        $('.budget').select2();
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

        $('#checkApplications').change(function() {
            if(this.checked) {
                $("#application_div").show();
            }else{
                $("#application_div").hide();
                $("#application_others_div").hide();
                $(".application").prop('checked', false);
                $(".application_others").prop('checked', false);
                $("#application_others").removeAttr('required');
            }
        });

        $("#add-Row").click(function() {
            event.preventDefault();
            var count_fail = 0;
            tableRow++;

            if(count_fail == 0){
                $('#add-Row').prop("disabled", false);
                $('#display_error').html("");
                addRow();
                $('#quantity_total').val(calculateTotalQuantity());
            }
        });

        function addRow(){
            $('.digits_code').each(function() {
                digits_code = $(this).val();
                if (digits_code == null || digits_code == "") {
                    swal({  
                        type: 'error',
                        title: 'Please fill all Fields!',
                        icon: 'error',
                        confirmButtonColor: "#367fa9",
                    });
                    count_fail++;

                }else{
                    count_fail = 0;
                }
            });
            $('.item_description').each(function() {
                item_description = $(this).val();
                if (item_description == null || item_description == "") {
                    swal({  
                        type: 'error',
                        title: 'Please fill all Fields!',
                        icon: 'error',
                        confirmButtonColor: "#367fa9",
                    });
                    count_fail++;

                }else{
                    count_fail = 0;
                }
            });
            $('.asset_type').each(function() {
                asset_type = $(this).val();
                if (asset_type == null || asset_type == "") {
                    swal({  
                        type: 'error',
                        title: 'Please fill all Fields!',
                        icon: 'error',
                        confirmButtonColor: "#367fa9",
                    });
                    count_fail++;

                }else{
                    count_fail = 0;
                }
            });
            var newrow =
                `<tr>
                    <td>
                        <input type="text" placeholder="Search item" class="form-control itemDesc text-center" data-id="${tableRow}" id="itemDesc${tableRow}"  name="item_description[]" required maxlength="100">
                        <input type="hidden" name="mo_id[]" id="mo_id${tableRow}" class="id" required data-id="${tableRow}"/>
                            <ul class="ui-autocomplete ui-front ui-menu ui-widget ui-widget-content" data-id="${tableRow}" id="ui-id-2${tableRow}" style="display:none; top: 60px; left: 15px; width: 100%;">
                                <li>Loading...</li>
                            </ul>
                    </td>   
                    <td>
                        <input type="text" placeholder="Item code" class="form-control digits_code text-center" data-id="${tableRow}" id="digits_code${tableRow}"  name="digits_code[]" readonly>
                    </td>  
                    <td>
                        <input type="text" placeholder="Catrgory" class="form-control category text-center" data-id="${tableRow}" id="category${tableRow}"  name="category[]" readonly>
                    </td>   
                    <td>
                        <input type="text" placeholder="Sub category" class="form-control sub_category text-center" data-id="${tableRow}" id="sub_category${tableRow}"  name="sub_category[]" readonly>
                    </td> 
             
                    <td><input class="form-control text-center sinput wh_quantity" type="text" required name="wh_quantity[]" id="wh_quantity${tableRow}" data-id="${tableRow}" readonly></td> 
                        
                    <td><input class="form-control text-center sinput unserved_quantity" type="text" required name="unserved_quantity[]" id="unserved_quantity${tableRow}" data-id="${tableRow}" readonly></td>    
                    
                    <td class="qty text-center">1</td> 
                    <td> 
                        <select selected data-placeholder="Choose" class="form-control budget" name="budget_range[]" id="budget${tableRow}" required required style="width:100%"> 
                            <option value=""></option> 
                            @foreach($budget_range as $data)
                                <option value="{{$data->description}}">{{$data->description}}</option>
                            @endforeach
                        </select>
                    </td> 
                    <td style="text-align:center">
                        <button id="deleteRow" name="removeRow" data-id="${tableRow}" class="btn btn-danger btn-sm removeRow" ><i class="glyphicon glyphicon-trash"></i></button>
                    </td>
                </tr>`;
            $('#asset-items tbody').append(newrow);
            $('#budget'+tableRow).select2();
            var url = '';
            if($('#request_type_id').val() == 1){
                 url = "{{ route('item.it.search') }}";
            }else{
                 url = "{{ route('item.fa.search') }}";
            }
            //Search item
            let countrow = 1;
            
            $(function(){
                countrow++;
                $('#itemDesc'+tableRow).autocomplete({
                    source: function (request, response) {
                    $.ajax({
                        url: url,
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
                                    confirmButtonColor: "#5cb85c",
                                });
                            }else{ 
                                if (data.status_no == 1) {
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
                                            sub_category_description:   item.sub_category_description,
                                            item_cost:                  item.item_cost,
                                            wh_qty:                     item.wh_qty,
                                            unserved_qty:               item.unserved_qty,
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
                        if (e.id) {
                            $("#digits_code"+$(this).attr("data-id")).val(e.digits_code);
                            $('#category'+$(this).attr("data-id")).val(e.category_description);
                            $('#sub_category'+$(this).attr("data-id")).val(e.sub_category_description);
                            $("#supplies_cost"+$(this).attr("data-id")).val(e.item_cost);
                            $('#itemDesc'+$(this).attr("data-id")).val(e.value);
                            $('#itemDesc'+$(this).attr("data-id")).attr('readonly','readonly');
                            $('#fixed_description'+$(this).attr("data-id")).val(e.value);
                            $('#wh_quantity'+$(this).attr("data-id")).val(e.wh_qty);
                            $('#unserved_quantity'+$(this).attr("data-id")).val(e.unserved_qty);
                            $('#val_item').html('');
                            return false;
                            return false;

                        }
                    },

                    minLength: 1,
                    autoFocus: true
                });

            });
        }

        //deleteRow
        $(document).on('click', '.removeRow', function() {
            if ($('#asset-items tbody tr').length != 1) { //check if not the first row then delete the other rows
                tableRow--;
                $(this).closest('tr').remove();
                $('#quantity_total').val(calculateTotalQuantity());
                return false;
            }
        });

        //DELETE EXISTING LINES
        $(".deleteRowData").click(function(event) {
            event.preventDefault();
            const id = $(this).val();
            swal({
                    title: "Are you sure?",
                    type: "warning",
                    text: "You won't be able to revert this!",
                    showCancelButton: true,
                    confirmButtonColor: "#41B314",
                    cancelButtonColor: "#F9354C",
                    confirmButtonText: "Yes, delete it!"
                    }, function () {
                    $.ajax({
                        url: "{{ route('delete-line-assets-from-approval') }}",
                        type: "POST",
                        dataType: 'json',
                        data: {
                            '_token': token,
                            'lineId': id
                        },
                        success: function (data) {
                            if (data.status == "success") {
                                swal({
                                    type: data.status,
                                    title: data.message,
                                });
                                setTimeout(function(){
                                    location.reload();
                                    }, 1000);
                                } else if (data.status == "error") {
                                swal({
                                    type: data.status,
                                    title: data.message,
                                });
                            }
                        }
                    }); 
            });
        });

        function calculateTotalQuantity() {
            let totalQuantity = 0;
            $('.qty').each(function() {
                let qty = 0;
                if($(this).text().trim()) {
                    qty = parseInt($(this).text().replace(/,/g, ''));
                }

                totalQuantity += qty;
            });
            return totalQuantity;
        }

        $('#btnUpdate').click(function(event) {
            event.preventDefault();
            var item = $("input[name^='item_description']").length;
            var item_value = $("input[name^='item_description']");
            for(i=0;i<item;i++){
                if(item_value.eq(i).val() == 0 || item_value.eq(i).val() == null){
                    swal({  
                            type: 'error',
                            title: 'Please fill fields!',
                            icon: 'error',
                            confirmButtonColor: "#5cb85c",
                        });
                        event.preventDefault();
                        return false;
                } 
            } 

            var item = $("input[name^='digits_code']").length;
            var item_value = $("input[name^='digits_code']");
            for(i=0;i<item;i++){
                if(item_value.eq(i).val() == 0 || item_value.eq(i).val() == null){
                    swal({  
                            type: 'error',
                            title: 'Please fill fields!',
                            icon: 'error',
                            confirmButtonColor: "#5cb85c",
                        });
                        event.preventDefault();
                        return false;
                } 
            } 

            var sub_cat = $(".budget option").length;
            var sub_cat_value = $('.budget').find(":selected");
            for(i=0;i<sub_cat;i++){
                if(sub_cat_value.eq(i).val() == ""){
                    swal({  
                            type: 'error',
                            title: 'Please choose budget range!',
                            icon: 'error',
                            confirmButtonColor: "#5cb85c",
                        });
                        event.preventDefault();
                        return false;
                } 
        
            } 

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
                    $('#btnUpdate').attr('disabled',true);
                    $('#EditAssetRequest').submit();                 
            });
        });

    </script>
@endpush