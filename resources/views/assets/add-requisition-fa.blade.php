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
                background-color: #eee;
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
       Fill up FA asset form
    </div>

    <form action="{{ CRUDBooster::mainpath('add-save') }}" method="POST" id="AssetRequest" enctype="multipart/form-data">
        <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
        <input type="hidden" value="5" name="request_type_id" id="request_type_id">

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
                    <div class="box-body no-padding">
                        <div class="table-responsive">
                            <div class="pic-container">
                                <div class="pic-row">
                                    <table id="asset-items">
                                        <tbody id="bodyTable">
                                            <tr style="background-color:#00a65a; border: 0.5px solid #000;">
                                                <th style="text-align: center" colspan="11"><h4 class="box-title" style="color: #fff;"><b>{{ trans('message.form-label.asset_items') }}</b></h4></th>
                                            </tr>
                                            <tr class="tbl_header_color dynamicRows">
                                                <th width="22%" class="text-center">*{{ trans('message.table.item_description') }}</th>
                                                <th width="15%" class="text-center">{{ trans('message.table.digits_code') }}</th>
                                                <th width="15%" class="text-center">{{ trans('message.table.category_id_text') }}</th>                                                                                                                    
                                                <th width="15%" class="text-center">{{ trans('message.table.sub_category_id_text') }}</th> 
                                                <th width="7%" class="text-center">{{ trans('message.table.wh_qty') }}</th>
                                                <th width="7%" class="text-center">{{ trans('message.table.prev_balance_quantity') }}</th> 
                                                <th width="7%" class="text-center">{{ trans('message.table.request_qty') }}</th> 
                                                <th width="15%" class="text-center">{{ trans('message.table.budget_range') }}</th> 
                                                <th width="5%" class="text-center">{{ trans('message.table.action') }}</th>
                                            </tr>
    
                                            <tr id="tr-table">
                                                <tr>
                                
                                                </tr>
                                            </tr>
                                        
                                        </tbody>

                                        <tfoot>

                                            <tr id="tr-table1" class="bottom">

                                                <td colspan="6">
                                                    <a type="button" id="add-Row" name="add-Row" class="btn btn-success add"> <i class="fa fa-plus-circle"></i> Add Item</a>
                                                </td>
                                                <td align="left" colspan="1">
                                                    <input type='number' name="quantity_total" class="form-control text-center" id="quantity_total" readonly>
                                                </td>
                                                <td colspan="2"></td>
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

        </div>

        <div class='panel-footer'>

            <a href="{{ CRUDBooster::mainpath() }}" class="btn btn-default">{{ trans('message.form.cancel') }}</a>

            <button class="btn btn-success pull-right" type="submit" id="btnSubmit"> <i class="fa fa-save" ></i> {{ trans('message.form.create') }}</button>

        </div>

    </form>


</div>



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

        $(document).ready(function() {


            $("#add-Row").click(function() {

                var description = "";
                var count_fail = 0;

                $('.itemDesc').each(function() {
                    description = $(this).val();
                    if (description == null) {

                        swal({  
                            type: 'error',
                            title: 'Please fill all Fields!',
                            icon: 'error',
                            confirmButtonColor: "#5cb85c",
                        });
                        count_fail++;

                    } else if (description == "") {

                        swal({  
                            type: 'error',
                            title: 'Please fill all Fields!',
                            icon: 'error',
                            confirmButtonColor: "#5cb85c",
                        });
                        count_fail++;

                    }else{
                        count_fail = 0;
                    }
                });

                $('.digits_code').each(function() {
                    description = $(this).val();
                    if (description == null) {
                        swal({  
                            type: 'error',
                            title: 'Please fill all Fields!',
                            icon: 'error',
                            confirmButtonColor: "#5cb85c",
                        });
                        count_fail++;

                    } else if (description == "") {
                        swal({  
                            type: 'error',
                            title: 'Please fill all Fields!',
                            icon: 'error',
                            confirmButtonColor: "#5cb85c",
                        });
                        count_fail++;

                    }else{
                        count_fail = 0;
                    }
                });
                
                tableRow++;

                if(count_fail == 0){

                    var newrow =
                    `<tr>

                        <td >
                            <input type="text" placeholder="Search Item ..." class="form-control finput itemDesc" id="itemDesc${tableRow}" data-id="${tableRow}"   name="item_description[]"  required maxlength="100">
                          <ul class="ui-autocomplete ui-front ui-menu ui-widget ui-widget-content" data-id="${tableRow }" id="ui-id-2${tableRow}" style="display: none; top: 60px; left: 15px; width: 100%;">
                          <li>Loading...</li>
                        </ul>
                        <div id="display-error${tableRow}"></div>
                         <td>
                            <input type="text" onkeyup="this.value = this.value.toUpperCase();" class="form-control digits_code text-center sinput" data-id="${tableRow}" id="digits_code${tableRow}"  name="digits_code[]"   maxlength="100" readonly>
                            <input type="hidden" onkeyup="this.value = this.value.toUpperCase();" class="form-control fixed_description finput" data-id="${tableRow}" id="fixed_description${tableRow}"  name="fixed_description[]"   maxlength="100" readonly>
                         </td>

                        <td>

                            <input type="text" onkeyup="this.value = this.value.toUpperCase();" class="form-control text-center sinput category_id" name="category_id[]" data-id="${tableRow}" id="category_id${tableRow}" readonly>
                        </td>

                        <td>  
                            <input type="text" onkeyup="this.value = this.value.toUpperCase();" class="form-control text-center sub_category_id sinput" data-id="${tableRow}" id="sub_category_id${tableRow}"  name="sub_category_id[]"   maxlength="100" readonly> 
                            <input type="text" onkeyup="this.value = this.value.toUpperCase();" class="form-control text-center item_cost sinput" data-id="${tableRow}" id="item_cost${tableRow}"  name="item_cost[]"   maxlength="100" readonly> 
                        </td> 

                        <td><input class="form-control text-center sinput wh_quantity" type="text" required name="wh_quantity[]" id="wh_quantity${tableRow}" data-id="${tableRow}" readonly></td> 
                        
                        <td><input class="form-control text-center sinput unserved_quantity" type="text" required name="unserved_quantity[]" id="unserved_quantity${tableRow}" data-id="${tableRow}" readonly></td> 
                        
                        <td><input class="form-control text-center quantity_item" type="number" required name="quantity[]" id="quantity${tableRow}" data-id="${tableRow}"  value="1" min="0" max="9999999999" step="any" onKeyPress="if(this.value.length==4) return false;" oninput="validity.valid;" readonly></td> 
                        <td> 
                            <select selected data-placeholder="Choose" class="form-control budget" name="budget_range[]" id="budget${tableRow}" required required style="width:100%"> 
                                <option value=""></option> 
                                @foreach($budget_range as $data)
                                    <option value="{{$data->description}}">{{$data->description}}</option>
                                @endforeach
                            </select>
                        </td>                
                        <td> 
                            <button id="deleteRow" name="removeRow" class="btn btn-danger removeRow"><i class="glyphicon glyphicon-trash"></i></button> 
                        </td> 

                    </tr>`;
                    $(newrow).insertBefore($('table tr#tr-table1:last'));

                    $('#app_id'+tableRow).attr('disabled', true);
                    $('#budget'+tableRow).select2();
                    $('.js-example-basic-multiple').select2();
                    // $('#category_id'+tableRow).select2({
                    // placeholder_text_single : "- Select Category -",
                    // minimumResultsForSearch: -1});
                    // $('#sub_category_id'+tableRow).select2({
                    // placeholder_text_single : "- Select Sub Category -"});

                    //$('#sub_category_id'+tableRow).attr('disabled', true);

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

                    var stack = [];
                    var token = $("#token").val();
                    var categories = <?php echo json_encode($categories->category_description); ?>;
               

                    let countrow = 1;
                    $(function(){
                    countrow++;
                    //$('#search'+countrow).attr('disabled', true);
                    $('#itemDesc'+tableRow).autocomplete({
                        source: function (request, response) {
                        $.ajax({
                            url: "{{ route('item.fa.search') }}",
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

                                    $("#val_item").html();
                                    var data = data.items;
                                    $('#ui-id-2'+tableRow).css('display', 'none');

                                    response($.map(data, function (item) {
                                        return {
                                            id:                            item.id,
                                            asset_code:                    item.asset_code,
                                            digits_code:                   item.digits_code,
                                            asset_tag:                     item.asset_tag,
                                            serial_no:                     item.serial_no,
                                            value:                         item.item_description,
                                            category_description:          item.category_description,
                                            tam_sub_category_description:  item.sub_category_description,
                                            item_cost:                     item.item_cost,
                                            wh_qty:                        item.wh_qty,
                                            unserved_qty:                  item.unserved_qty,
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
                                $('#category_id'+$(this).attr("data-id")).val(categories);
                                $('#sub_category_id'+$(this).attr("data-id")).val(e.tam_sub_category_description);
                                $("#item_cost"+$(this).attr("data-id")).val(e.item_cost);
                                $('#itemDesc'+$(this).attr("data-id")).val(e.value);
                                $('#itemDesc'+$(this).attr("data-id")).attr('readonly','readonly');
                                $('#fixed_description'+$(this).attr("data-id")).val(e.value);
                                $('#wh_quantity'+$(this).attr("data-id")).val(e.wh_qty);
                                $('#unserved_quantity'+$(this).attr("data-id")).val(e.unserved_qty);
                                $('#val_item').html('');
                                return false;

                            }
                        },

                        minLength: 1,
                        autoFocus: true
                        });

                    });

                    $(document).on('keyup', '#itemDesc'+tableRow, function(ev) {

                        var category =  $('#category_id'+$(this).attr("data-id")).val();
                        var description = this.value;

                        if(description.includes("LAPTOP") && category == "IT ASSETS"){
                        
                            // alert(description);

                            $('#app_id'+$(this).attr("data-id")).attr('disabled', false);

                        }else{
                        
                            $('#app_id'+$(this).attr("data-id")).attr('disabled', true);
                        }


                    });


                    $('#AppOthers'+tableRow).hide();
                    $('#AppOthers'+tableRow).removeAttr('required');

                    $('#category_id'+tableRow).change(function(){

                        var category =  this.value;

                        var id_data = $(this).attr("data-id");
                        // $('.account'+id_data).prop("disabled", false);

                        $.ajax
                        ({ 

                            type: 'POST',
                           // url: 'https://localhost/dam/public/admin/header_request/subcategories/' + category,
                           // data: '',
                           url: "{{ route('asset.sub.categories') }}",
                            data: {
                                "id": category
                            },
                            success: function(result) {
                                //alert(result.length);
                            
                                var i;
                                var showData = [];

                                showData[0] = "<option value=''>-- Select Sub Category --</option>";
                                
                                for (i = 0; i < result.length; ++i) {
                                    var j = i + 1;
                                    showData[j] = "<option value='"+result[i].class_description+"'>"+result[i].class_description+"</option>";
                                }
                                    
                                $('#sub_category_id'+id_data).attr('disabled', false);
                                
                                jQuery('#sub_category_id'+id_data).html(showData);        
                            
                            }
                        });

                    });

                    $("#quantity_total").val(calculateTotalQuantity());
                    
                }

            });
            
            //deleteRow
            $(document).on('click', '.removeRow', function() {
                if ($('#asset-items tbody tr').length != 1) { //check if not the first row then delete the other rows
                    tableRow--;
                    $(this).closest('tr').remove();
                    $("#quantity_total").val(calculateTotalQuantity());
                    return false;
                }
            });

        });

   
        
        $('#employee_name').change(function() {
    
                var employee_name =  this.value;
                
                //var id_data = $(this).attr("data-id");
                // $('.account'+id_data).prop("disabled", false);

                $.ajax
                ({ 
                    url: "{{ URL::to('/employees')}}",
                    type: "POST",
                    data: {
                        'employee_name': employee_name,
                        _token: '{!! csrf_token() !!}'
                        },
                        
                    
                        
                    success: function(result)
                    {   
                        //alert(result.length);
                       
                        $('#company_name').val(result[0].company_name);
                        $('#position').val(result[0].position_description);
                        $('#department').val(result[0].department_name);
                        
                        
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
                        confirmButtonColor: "#5cb85c",
                    }); 
                    event.preventDefault(); // cancel default behavior
                    return false;
                }else if (countRow == 1) {
                    swal({
                        type: 'error',
                        title: 'Please add an item!',
                        icon: 'error',
                        confirmButtonColor: "#5cb85c",
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
                                    title: 'Digits Code cannot be empty!',
                                    icon: 'error',
                                    confirmButtonColor: "#5cb85c",
                                });
                                event.preventDefault();
                                return false;
                        } 
                
                    } 

                    var sub_cat = $(".category_id option").length;
                    var sub_cat_value = $('.category_id').find(":selected");
                    for(i=0;i<sub_cat;i++){
                        if(sub_cat_value.eq(i).val() == ""){
                            swal({  
                                    type: 'error',
                                    title: 'Please select Category!',
                                    icon: 'error',
                                    confirmButtonColor: "#5cb85c",
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
                                    confirmButtonColor: "#5cb85c",
                                });
                                event.preventDefault();
                                return false;
                        } 
                
                    }  

                    var budget = $(".budget option").length;
                    var budget_value = $('.budget').find(":selected");
                    for(i=0;i<budget;i++){
                        if(budget_value.eq(i).val() == ""){
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
                            confirmButtonText: "Yes, create it!",
                            width: 450,
                            height: 200
                            }, function () {
                                $("#AssetRequest").submit();                   
                        });
                    
                }     
        });



    </script>
@endpush