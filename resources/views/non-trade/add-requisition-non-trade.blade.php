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
                background-color: #d2d6de;
            }

            .input-group-addon {
                background-color: #f5f5f5 !important;
            }

            ::-webkit-input-placeholder {
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
            }

            .ui-state-focus {
                background: none !important;
                background-color: #00a65a !important;
                border: 1px solid rgb(255, 254, 254) !important;
                color: #fff !important;
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
        Fill up Non Trade form
    </div>

    <form action="{{ CRUDBooster::mainpath('add-save') }}" method="POST" id="AssetRequest" enctype="multipart/form-data">
        <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
        <input type="hidden" value="9" name="request_type_id" id="request_type_id">

        <div class='panel-body'>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label require">{{ trans('message.form-label.employee_name') }}</label>          
                        <input type="text" class="form-control finput"  id="employee_name" name="employee_name"  required readonly value="{{$employeeinfos->bill_to}}"> 
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label require">{{ trans('message.form-label.company_name') }}</label>
                        <input type="text" class="form-control finput"  id="company_name" name="company_name"  required readonly value="{{$employeeinfos->company_name_id}}">                                   
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label require">{{ trans('message.form-label.department') }}</label>
                        <input type="text" class="form-control finput"  id="department" name="department"  required readonly value="{{$employeeinfos->department_name}}">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label require">{{ trans('message.form-label.position') }}</label>
                        <input type="text" class="form-control finput"  id="position" name="position"  required readonly value="{{$employeeinfos->position_id}}">                                   
                    </div>
                </div>
            </div>

            @if(CRUDBooster::myPrivilegeId() == 8)
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label require">{{ trans('message.form-label.store_branch') }}</label>
                            
                            <input type="text" class="form-control finput"  id="store_branch" name="store_branch"  required readonly value="{{$stores->store_name}}"> 
                            <input type="hidden" class="form-control"  id="store_branch_id" name="store_branch_id"  required readonly value="{{$stores->id}}"> 

                        </div>
                    </div>
                </div>
            @endif
            <hr/>

            <div class="row"> 
                <label class="require control-label col-md-2">*{{ trans('message.form-label.purpose') }}</label>
                @foreach($purposes as $data)
                    @if($data->id == 1)
                        <div class="col-md-5">
                            <label class="radio-inline control-label col-md-5" ><input type="radio" required   class="purpose" name="purpose" value="{{$data->id}}" >{{$data->request_description}}</label>
                            <br>
                        </div>
                    @else
                        <div class="col-md-5">
                            <label class="radio-inline control-label col-md-5"><input type="radio" required  class="purpose" name="purpose" value="{{$data->id}}" >{{$data->request_description}}</label>
                            <br>
                        </div>
                    @endif
                @endforeach
            </div>

            <hr/>

            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label" style="font-style: italic"><span style="color:red">*</span> Search items</label>
                            <input class="form-control auto finput" placeholder="Search Item..." id="search">
                            <ul class="ui-autocomplete ui-front ui-menu ui-widget ui-widget-content" id="ui-id-2" style="display: none; top: 60px; left: 15px; width: 520px;">
                                <li>Loading...</li>
                            </ul>
                        </div>
                        <div id="display-error">
                            <span class="test"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="box-header text-center">
                        <h3 class="box-title"><b>{{ trans('message.form-label.asset_items') }}</b></h3>
                    </div>
                    <div class="box-body no-padding">
                        <div class="table-responsive">
                            <div class="pic-container">
                                <div class="pic-row">
                                    <table class="table table-bordered" id="asset-items">
                                      <thead>
                                        <tr class="tbl_header_color dynamicRows">
                                            <th width="30%" class="text-center">{{ trans('message.table.item_description') }}</th>
                                            <th width="15%" class="text-center">{{ trans('message.table.item_code') }}</th>
                                            <th width="15%" class="text-center">{{ trans('message.table.category_id_text') }}</th>                                                                                                                    
                                            <th width="15%" class="text-center">{{ trans('message.table.sub_category_id_text') }}</th> 
                                            <th width="7%" class="text-center">{{ trans('message.table.wh_qty') }}</th>
                                            <th width="7%" class="text-center">{{ trans('message.table.prev_balance_quantity') }}</th> 
                                            <th width="10%" class="text-center">{{ trans('message.table.request_qty') }}</th> 
                                            <th width="5%" class="text-center">{{ trans('message.table.action') }}</th>
                                        </tr>
                                      </thead>

                                        <tbody>
                        
                                        </tbody>

                                        <tfoot>
                                            <tr id="tr-table1" class="bottom">
                                                <td colspan="6">
                                                    {{-- <input type="button" id="add-Row" name="add-Row" class="btn btn-success add" value='Add Item' /> --}}
                                                </td>
                                                <td align="left" colspan="1">
                                                    <input type='text' name="quantity_total" class="form-control text-center" id="quantity_total" readonly>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12" style="margin-top: 10px">
                    <div class="form-group">
                        <label>{{ trans('message.table.note') }}</label>
                        <textarea placeholder="{{ trans('message.table.comments') }} ..." rows="3" class="form-control finput" name="requestor_comments"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class='panel-footer'>
            <a href="{{ CRUDBooster::mainpath() }}" id="btnCancel" class="btn btn-default">{{ trans('message.form.cancel') }}</a>
            <button class="btn btn-success pull-right" type="submit" id="btnSubmit"> <i class="fa fa-save" ></i> {{ trans('message.form.create') }}</button>
        </div>
    </form>
</div>

@endsection
@push('bottom')
    <script type="text/javascript">
        $(document).ready(function() {
            function preventBack() {
                window.history.forward();
            }
            window.onunload = function() {
                null;
            };
            setTimeout("preventBack()", 0);
        
            var tableRow = 1;
            var stack = [];
            var token = $("#token").val();
            let countrow = 1;

            $(function(){
                tableRow++;  
                $('#search').autocomplete({
                    source: function (request, response) {
                    $.ajax({
                        url: "{{ route('item.non.trade.search') }}",
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
            
                            if (data.status_no == 1) {

                                $("#val_item").html();
                                var data = data.items;
                                $('#ui-id-2').css('display', 'none');

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
                                        wh_qty:                     item.wh_qty,
                                        unserved_qty:               item.unserved_qty,
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
                            if (!stack.includes(e.id)) {     
                                stack.push(e.id);
                                addRow(e);
                                $(this).val('');
                                $("#quantity_total").val(calculateTotalQuantity());
                                return false;
                                
                            }else{
                                const tr = $('#asset-items tbody').find('tr');
                                const qty = tr.find('input[id="quantity'+e.id+'"]');
                                const newQty = parseInt(qty.val()) + 1;
                                qty.val(newQty);
                                $(this).val('');
                                $("#quantity_total").val(calculateTotalQuantity());
                                $('#quantity_item' + e.id).val(function (i, oldval) {
                                    return ++oldval;
                                });
                                $("#val_item").html();
                                return false;
                            }
                        }
                    },
                    
                    minLength: 1,
                    autoFocus: true
                });     
            });

            function addRow(e){
                tableRow++;
                var newrow =
                        '<tr class="nr" id="rowid' + e.id + '" rows>' +

                            '<td >' +
                                '<input type="text" class="form-control sinput itemDesc" id="itemDesc'+ tableRow +'" data-id="'+ tableRow +'"   name="item_description[]" value="'+e.value+'"  required maxlength="100" readonly>' +

                            '</td>' +  

                            '<td>' + 
                                '<input type="text" onkeyup="this.value = this.value.toUpperCase();" class="form-control text-center digits_code sinput" data-id="'+ tableRow +'" id="digits_code'+ tableRow +'"  name="digits_code[]" value="'+e.digits_code+'"  maxlength="100" readonly>' +
                            '</td>' +
                            '<td style="display:none">' + 
                                '<input type="hidden" class="form-control cost" data-id="'+ tableRow +'" id="cost'+ tableRow +'"  name="supplies_cost[]"   maxlength="100" readonly>' +
                                '<input type="hidden" onkeyup="this.value = this.value.toUpperCase();" class="form-control fixed_description sinput" data-id="'+ tableRow +'" id="fixed_description'+ tableRow +'"  name="fixed_description[]" value="'+e.value+'"  maxlength="100" readonly>' +
                            '</td>' +

                            '<td>' + 
                                '<input type="text" onkeyup="this.value = this.value.toUpperCase();" class="form-control text-center category_id sinput" data-id="'+ tableRow +'" id="category_id'+ tableRow +'"  name="category_id[]" value="'+e.category_description+'"  maxlength="100" readonly>' +
                            '</td>' +
                            '<td>' + 
                                '<input type="text" onkeyup="this.value = this.value.toUpperCase();" class="form-control text-center sub_category_id sinput" data-id="'+ tableRow +'" id="sub_category_id'+ tableRow +'"  name="sub_category_id[]" value="'+e.class_description+'"  maxlength="100" readonly>' +
                            '</td>' +
            
                            '<td><input class="form-control text-center sinput wh_quantity" type="text" required name="wh_quantity[]" id="wh_quantity' + tableRow + '" data-id="' + tableRow  + '" value="'+e.wh_qty+'" readonly></td>' +
                            
                            '<td><input class="form-control text-center sinput unserved_quantity" type="text" required name="unserved_quantity[]" value="'+e.unserved_qty+'" id="unserved_quantity' + tableRow + '" data-id="' + tableRow  + '" readonly></td>' +
                            
                            '<td><input class="form-control text-center finput quantity_item" type="text" required name="quantity[]" id="quantity' + e.id + '" data-id="' + tableRow  + '"  value="1" max="9999999999"></td>' +
                
                            '<td>' +
                                '<button id="deleteRow" name="removeRow" class="btn btn-danger removeRow"><i class="glyphicon glyphicon-trash"></i></button>' +
                            '</td>' +

                        '</tr>';
                    $('#asset-items tbody').append(newrow);
            }

             //deleteRow
             $(document).on('click', '.removeRow', function() {
                $(this).closest('tr').remove();
                $("#quantity_total").val(calculateTotalQuantity());
                return false;
            });

            function calculateTotalQuantity() {
                var totalQuantity = 0;
                $('.quantity_item').each(function() {
                    if($(this).val() === ''){
                        var qty = 0;
                    }else{
                        var qty = parseInt($(this).val().replace(/,/g, ''));
                    }
        
                    totalQuantity += qty;
                });
                return totalQuantity.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
            }

            $(document).on('keyup', '.quantity_item', function(ev) {
                $("#quantity_total").val(calculateTotalQuantity());
                var value = $(this).val();
                value = value.replace(/^(0*)/,"");
                $(this).val(value); 
            });

            $('.quantity_item').keydown(function(event) { 
                if (event.keyCode == 13) {
                    event.preventDefault();
                }
            });

            $(document).on('keyup','.quantity_item', function (e) {
                if(event.which >= 37 && event.which <= 40) return;

                if(this.value.charAt(0) == '.'){
                    this.value = this.value.replace(/\.(.*?)(\.+)/, function(match, g1, g2){
                        return '.' + g1;
                    })
                }

                // if(event.key == '.' && this.value.split('.').length > 2){
                if(this.value.split('.').length > 2){
                    this.value = this.value.replace(/([\d,]+)([\.]+.+)/, '$1') 
                        + '.' + this.value.replace(/([\d,]+)([\.]+.+)/, '$2').replace(/\./g,'')
                    return;
                }

                $(this).val( function(index, value) {
                    value = value.replace(/[^0-9.]+/g,'')
                    let parts = value.toString().split(".");
                    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    return parts.join(".");
                });

                if(event.which >= 37 && event.which <= 40) return;
                $(this).val(function(index, value) {
                    return value
                    .replace(/\D/g, "")
                    .replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                    ;
                });

            });  

            //SUBMIT
            $("#btnSubmit").click(function(event) {
            event.preventDefault();
            var countRow = $('#asset-items tbody tr').length;
                // var value = $('.vvalue').val();
                if(! $(".purpose").is(':checked')){
                    swal({
                        type: 'error',
                        title: 'Please choose Purpose!',
                        icon: 'error',
                        confirmButtonColor: "#367fa9",
                    }); 
                    event.preventDefault(); // cancel default behavior
                    return false;
                }else if (countRow == 0) {
                    swal({
                        type: 'error',
                        title: 'Please add an item!',
                        icon: 'error',
                        confirmButtonColor: "#367fa9",
                    }); 
                    event.preventDefault(); // cancel default behavior
                    return false;
                }else{ 
                    //quantity validation
                    var v = $("input[name^='quantity']").length;
                    var value = $("input[name^='quantity']");
                    var reg = /^0/gi;
                        for(i=0;i<v;i++){
                            if(value.eq(i).val() == 0){
                                swal({  
                                        type: 'error',
                                        title: 'Quantity cannot be empty or zero!',
                                        icon: 'error',
                                        confirmButtonColor: "#367fa9",
                                    });
                                    event.preventDefault();
                                    return false;
                            }else if(value.eq(i).val() < 0){
                                swal({
                                    type: 'error',
                                    title: 'Negative Value is not allowed!',
                                    icon: 'error',
                                    confirmButtonColor: "#367fa9",
                                }); 
                                event.preventDefault(); // cancel default behavior
                                return false;
                            }else if(value.eq(i).val().match(reg)){
                                swal({
                                    type: 'error',
                                    title: 'Invalid Quantity Value!',
                                    icon: 'error',
                                    confirmButtonColor: "#367fa9",
                                }); 
                                event.preventDefault(); // cancel default behavior
                                return false;     
                            }  
                    
                        } 
                                          
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
                                $("#AssetRequest").submit();                   
                        });
                    
                }     
        });

            //BACK FORM
            $("#btnCancel").click(function(event) {
            event.preventDefault();
            swal({
                    title: 'Are you sure?',
                    type: 'warning',
                    text: "You won't be able to revert this!",
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
        });

    </script>
@endpush