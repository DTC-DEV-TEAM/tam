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
        Asset Form (<span style="color:red">Kindly close the transaction within 15 days after you received the request</span>)
    </div>

    <form action="{{ CRUDBooster::mainpath('add-save') }}" method="POST" id="AssetRequest" enctype="multipart/form-data">
        <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
        <input type="hidden" value="10" name="request_type_id" id="request_type_id">

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
                        {{-- <input type="text" class="form-control finput"  id="department" name="department"  required readonly value="{{$employeeinfos->department_name}}"> --}}
                        <select required id="department" name="department" class="form-select select2" style="width:100%;">
                            @foreach($departments as $res)
                                <option value="{{ $res->id }}">{{ $res->department_name }}</option>
                            @endforeach
                        </select>
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
            <br>
            <div class="row">
             <div class="col-md-12">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label"><span style="color:red">*</span> Search Items to request</label>
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
                                                <th width="30%" class="text-center">*{{ trans('message.table.item_description') }}</th>
                                                <th width="20%" class="text-center">Digits Code</th>
                                                <th width="20%" class="text-center">{{ trans('message.table.category_id_text') }}</th>     
                                                <th width="20%" class="text-center">{{ trans('message.table.sub_category_id_text') }}</th> 
                                                <th width="15%" class="text-center"> Wh Quantity</th>
                                                <th width="15%" class="text-center"> Unserved Quantity</th> 
                                                <th width="15%" class="text-center">*Request Qty</th> 
                                                <th width="5%" class="text-center">{{ trans('message.table.action') }}</th>
                                            </tr>
                                        </thead>
                                                                                                                         
                                        <tbody id="bodyTable">
                                            <tr id="tr-table">
                                                <tr></tr>
                                            </tr>
                                        </tbody>

                                        <tfoot>
                                            <tr id="tr-table1" class="bottom">
                                                <td colspan="6">
                                                    {{-- <input type="button" id="add-Row" name="add-Row" class="btn btn-primary add" value='Add Item' /> --}}
                                                </td>
                                                <td align="left" colspan="1">
                                                    <input type='text' name="quantity_total" class="form-control sinput text-center" id="quantity_total" readonly>
                                                </td>
                                                <td></td>
                                            </tr>
                                        </tfoot>

                                    </table>
                                </div>
                            </div>
                    
                         </div>
                        <br>
                    </div>
                </div>
          
                <div class="col-md-12">
                    <div class="form-group">
                        <label>{{ trans('message.table.note') }}</label>
                        <textarea placeholder="{{ trans('message.table.comments') }} ..." rows="3" class="form-control finput" name="requestor_comments"></textarea>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group text-center">
                    <label>CAN'T FIND WHAT YOU ARE LOOKING FOR?</label>
                    <a type="button" id="getVersions" data-toggle="modal" data-target="#viewDigitsCode"> VIEW HERE </a> <br>
                </div>
            </div>
        </div>

        <div class='panel-footer'>
            <a href="{{ CRUDBooster::mainpath() }}" class="btn btn-default">{{ trans('message.form.cancel') }}</a>
            <button class="btn btn-success pull-right" type="submit" id="btnSubmit"> <i class="fa fa-save" ></i> {{ trans('message.form.save') }}</button>
        </div>
    </form>
</div>

{{-- Digits Code Modal --}}
<div id="viewDigitsCode" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title text-center"><strong>View Item Code</strong></h4>
            </div>
            <div class="modal-body">
               <table id="asset-items" class="view-digits-code" style="width: 100%">
                <thead>
                    <tr>
                        <th>Item Code</th>
                        <th>Item Description</th>
                        <th>Category</th>
                        <th>Sub Category</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($item_master as $items)
                        <tr>
                            <td>{{$items->tasteless_code}}</td>
                            <td>{{$items->full_item_description}}</td>
                            <td>{{$items->category_description}}</td>
                            <td>{{$items->subcategory_description}}</td>
                        </tr>
                    @endforeach
                </tbody>
               </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection
@push('bottom')
      
    <script type="text/javascript">
        function preventBack() {
            window.history.forward();
        }
         window.onunload = function() {
            null;
        };
        setTimeout("preventBack()", 0);
        $('#department').select2({});
        var tableRow = <?php echo json_encode($tableRow); ?>;

        $(".view-digits-code").DataTable({
            ordering:false,
            pageLength:10,
            scrollCollapse: true,
            crollX: true,
            fixedColumns: true,
            fixedHeader: true,
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"],
            ],
        });

        $(document).ready(function() {
            var stack = [];
            var token = $("#token").val();
            let countrow = 1;

            $(function(){
                countrow++;
                $('#search').autocomplete({
                    source: function (request, response) {
                        $.ajax({
                            url: "{{ route('item.smallwares.search') }}",
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
                                                digits_code:                item.digits_code,
                                                value:                      item.item_description,
                                                category_description:       item.category_description,
                                                subcategory_description:    item.subcategory_description,
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
                        });
                    },
                    select: function (event, ui) {
                        var e = ui.item;
                        if (e.id) {
                            if (!stack.includes(e.id)) {     
                                stack.push(e.id);
                                var newrow =
                                `<tr class="nr" id="rowid${e.id}" row-data="${e.id}" rows>
                                    <td >
                                        <input type="text" class="form-control sinput itemDesc" id="itemDesc${tableRow}" data-id="${tableRow}"   name="item_description[]" value="${e.value}"  required maxlength="100" readonly>
                                    </td> 
                                    <td>
                                        <input type="text" onkeyup="this.value = this.value.toUpperCase();" class="form-control text-center digits_code sinput" data-id="${tableRow}" id="digits_code${tableRow}"  name="digits_code[]" value="${e.digits_code}"  maxlength="100" readonly>
                                    </td>
                                    <td style="display:none">
                                        <input type="hidden" class="form-control cost" data-id="${tableRow}" id="cost${tableRow}"  name="supplies_cost[]"   maxlength="100" readonly>
                                        <input type="hidden" onkeyup="this.value = this.value.toUpperCase();" class="form-control fixed_description sinput" data-id="${tableRow}" id="fixed_description${tableRow}"  name="fixed_description[]" value="${e.value}"  maxlength="100" readonly>
                                    </td>

                                    <td>
                                        <input type="text" onkeyup="this.value = this.value.toUpperCase();" class="form-control text-center category_id sinput" data-id="${tableRow}" id="category_id${tableRow}"  name="category_id[]" value="${e.category_description}"  maxlength="100" readonly>
                                    </td>
                                    <td>
                                        <input type="text" onkeyup="this.value = this.value.toUpperCase();" class="form-control text-center sub_category_id sinput" data-id="${tableRow}" id="sub_category_id${tableRow}"  name="sub_category_id[]" value="${e.subcategory_description}"  maxlength="100" readonly>
                                    </td>
                    
                                    <td><input class="form-control text-center sinput wh_quantity" type="text" required name="wh_quantity[]" id="wh_quantity${tableRow}" data-id="${tableRow}" value="${e.wh_qty}" readonly></td>
                                    <td><input class="form-control text-center sinput unserved_quantity" type="text" required name="unserved_quantity[]" value="${e.unserved_qty}" id="unserved_quantity${tableRow}" data-id="${tableRow}" readonly></td>                               
                                    <td><input class="form-control text-center finput quantity_item" type="text" required name="quantity[]" id="quantity${tableRow}" data-id="${tableRow}"  value="1" max="9999999999" step="any" onKeyPress="if(this.value.length==11) return false;" oninput="validity.valid;"></td>
                        
                                    <td>
                                        <a id="deleteRow${tableRow}" name="removeRow" class="btn btn-danger removeRow"><i class="glyphicon glyphicon-trash"></i></a>
                                    </td>

                                </tr>`;
                                $(newrow).insertBefore($('table tr#tr-table1:last'));

                                $(this).val('');
                                $("#quantity_total").val(calculateTotalQuantity());
                                return false;
                                
                            }else{
                                swal({
                                    type: 'error',
                                    title: 'Item Already Added!',
                                    icon: 'error',
                                    confirmButtonColor: "#00a65a",
                                }); 
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

            //deleteRow
            $(document).on('click', '.removeRow', function() {
                if ($('#asset-items tbody tr').length != 1) { //check if not the first row then delete the other rows
                    tableRow--;
                    var removeItem =  $(this).parents('tr').attr('row-data');
                        stack = jQuery.grep(stack, function(value) {
                        return value != removeItem;
                    });
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

            totalQuantity += parseInt($(this).val().replace(/,/g, ''));
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
            $("#AssetRequest").submit(function() {
                $("#btnSubmit").attr("disabled", true);
                return true;
            });
        });

        $("#btnSubmit").click(function(event) {
            event.preventDefault();
            var countRow = $('#asset-items tfoot tr').length;
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
                }else if (countRow == 1) {
                    swal({
                        type: 'error',
                        title: 'Please add an item!',
                        icon: 'error',
                        confirmButtonColor: "#367fa9",
                    }); 
                    event.preventDefault(); // cancel default behavior
                    return false;
                }else{ 
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

                    var item = $("input[name^='digits_code']").length;
                    var item_value = $("input[name^='digits_code']");
                    for(i=0;i<item;i++){
                        if(item_value.eq(i).val() == 0 || item_value.eq(i).val() == null){
                            swal({  
                                    type: 'error',
                                    title: 'Digits Code cannot be empty!',
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

    </script>
@endpush