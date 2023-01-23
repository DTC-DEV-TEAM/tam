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
@if(g('return_url'))
	<p class="noprint"><a title='Return' href='{{g("return_url")}}'><i class='fa fa-chevron-circle-left '></i> &nbsp; {{trans("crudbooster.form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}}</a></p>       
@else
	<p class="noprint"><a title='Main Module' href='{{CRUDBooster::mainpath()}}'><i class='fa fa-chevron-circle-left '></i> &nbsp; {{trans("crudbooster.form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}}</a></p>       
@endif

<div class='panel panel-default'>
    <div class='panel-heading'>
        Asset Form
    </div>

    <form action="{{ CRUDBooster::mainpath('add-save') }}" method="POST" id="AssetRequest" enctype="multipart/form-data">
        <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
        <input type="hidden" value="1" name="request_type_id" id="request_type_id">

        <div class='panel-body'>

            <div class="row">

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label require">{{ trans('message.form-label.employee_name') }}</label>
                         
                        <input type="text" class="form-control"  id="employee_name" name="employee_name"  required readonly value="{{$employeeinfos->bill_to}}"> 

                   

                    </div>

                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label require">{{ trans('message.form-label.company_name') }}</label>
                        <input type="text" class="form-control"  id="company_name" name="company_name"  required readonly value="{{$employeeinfos->company_name_id}}">                                   
                    </div>
                </div>

            </div>


            <div class="row">




                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label require">{{ trans('message.form-label.department') }}</label>
                        <input type="text" class="form-control"  id="department" name="department"  required readonly value="{{$employeeinfos->department_name}}">
                        <!--
                         <select class="form-control select2" style="width: 100%;" required name="department" id="department">
                                            <option value="">-- Select Department --</option>

                                            @foreach($departments as $datas)    
                                                <option  value="{{$datas->department_name}}">{{$datas->department_name}}</option>
                                            @endforeach

                         </select> -->

                    </div>

                </div>


                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label require">{{ trans('message.form-label.position') }}</label>
                        <input type="text" class="form-control"  id="position" name="position"  required readonly value="{{$employeeinfos->position_id}}">                                   
                    </div>
                </div>

            </div>

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
            <!--
            <hr/>

            <div class="row"> 
                <label class="require control-label col-md-2">*{{ trans('message.form-label.condition') }}</label>
                    @foreach($conditions as $data)
                    
                        @if($data->id == 1)
                                    <div class="col-md-5">
                                        <label class="radio-inline control-label col-md-5" ><input type="radio" required   class="condition" name="condition" value="{{$data->id}}" >{{$data->condition_description}}</label>
                                        <br>
                                    </div>
                            @else
                                    <div class="col-md-5">
                                        <label class="radio-inline control-label col-md-5"><input type="radio" required  class="condition" name="condition" value="{{$data->id}}" >{{$data->condition_description}}</label>
                                        <br>
                                    </div>
                        @endif

                    @endforeach
            </div> -->

            <hr/>

            <!--<div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label">{{ trans('message.form-label.add_item') }}</label>
                        <input class="form-control auto" style="width:420px;" placeholder="Search Item" id="search">
                        <ul class="ui-autocomplete ui-front ui-menu ui-widget ui-widget-content" id="ui-id-2" style="display: none; top: 60px; left: 15px; width: 520px;">
                            <li>Loading...</li>
                        </ul>
                    </div>
                </div>
            </div> -->

            <!-- 
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
                                                    <tbody>
                                                        <tr class="tbl_header_color dynamicRows">
                                                            <th width="15%" class="text-center">{{ trans('message.table.digits_code') }}</th>
                                                            <th width="20%" class="text-center">{{ trans('message.table.item_description') }}</th>
                                                            <th width="15%" class="text-center">{{ trans('message.table.serial_no') }}</th>
                                                            <th width="20%" class="text-center">{{ trans('message.table.item_remarks') }}</th>
                                                            <th width="5%" class="text-center">{{ trans('message.table.item_quantity') }}</th>
                                                            <th width="10%" class="text-center">{{ trans('message.table.item_cost') }}</th>
                                                            <th width="10%" class="text-center">{{ trans('message.table.item_total_cost') }}</th>
                                                            <th width="5%" class="text-center">{{ trans('message.table.action') }}</th>
                                                        </tr>
                                                        
                                                        <tr class="tableInfo">
                                                            <td colspan="6" align="right"><strong>{{ trans('message.table.total') }}</strong></td>
                                                            <td align="left" colspan="1">


                                                                <input type='hidden' name="quantity_total" class="form-control text-center" id="quantity_total" readonly>

                                                                <input type='hidden' name="cost_total" class="form-control text-center" id="cost_total" readonly>

                                                                <input type='number' name="total" class="form-control text-center" id="total" readonly>
                                                            </td>
                                                            <td colspan="1"></td>
                                                        </tr>
                                                    
                                                    </tbody>
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
                        <textarea placeholder="{{ trans('message.table.comments') }} ..." rows="3" class="form-control" name="requestor_comments"></textarea>
                    </div>
                </div>
         
            </div>  -->

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
                                                    <tbody id="bodyTable">
                                                        <tr class="tbl_header_color dynamicRows">
                                                            <th width="30%" class="text-center">*{{ trans('message.table.item_description') }}</th>
                                                            <th width="25%" class="text-center">{{ trans('message.table.category_id_text') }}</th>                                                         
                                                            
                                                            <th width="20%" class="text-center">{{ trans('message.table.sub_category_id_text') }}</th> 

                                                            <!-- <th width="20%" class="text-center">{{ trans('message.table.application_id_text') }}</th> -->
                                                            
                                                            <th width="7%" class="text-center">*{{ trans('message.table.quantity_text') }}</th> 
                                                           <!-- <th width="8%" class="text-center">{{ trans('message.table.image') }}</th>  -->
                                                            <th width="5%" class="text-center">{{ trans('message.table.action') }}</th>
                                                        </tr>
                                                        
                                                        <!--tr class="tableInfo">
                                                            <td colspan="6" align="right"><strong>{{ trans('message.table.total') }}</strong></td>
                                                            <td align="left" colspan="1">


                                                                <input type='hidden' name="quantity_total" class="form-control text-center" id="quantity_total" readonly>

                                                                <input type='hidden' name="cost_total" class="form-control text-center" id="cost_total" readonly>

                                                                <input type='number' name="total" class="form-control text-center" id="total" readonly>
                                                            </td>
                                                            <td colspan="1"></td>
                                                        </tr> -->

                                                        <tr id="tr-table">
                                                            <tr>
                                            
                                                            </tr>
                                                        </tr>
                                                    
                                                    </tbody>

                                                    <tfoot>

                                                        <tr id="tr-table1" class="bottom">
            
                                                            <td colspan="3">
                                                                <input type="button" id="add-Row" name="add-Row" class="btn btn-info add" value='Add Item' />
                                                            </td>
                                                            <td align="left" colspan="1">
                                                                <input type='number' name="quantity_total" class="form-control text-center" id="quantity_total" readonly>
                                                            </td>
                                                        </tr>
                                                    </tfoot>

                                                </table>
                                            </div>
                                        </div>
                                
                                    </div>
                                    <br>
                                </div>
                </div>

                <div class="col-md-12" id="application_div">
                    <hr/>
                    <div class="row"> 
                        <label class="require control-label col-md-2" required>*{{ trans('message.form-label.application') }}</label>
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
                        <label class="require control-label col-md-2">*{{ trans('message.form-label.application_others') }}</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control"  id="application_others" name="application_others"  required placeholder="e.g. VIBER, WHATSAPP, TELEGRAM" onkeyup="this.value = this.value.toUpperCase();">
                        </div>
                    </div>
                    <hr/>
                </div>



                <div class="col-md-12">
                    <div class="form-group">
                        <label>{{ trans('message.table.note') }}</label>
                        <textarea placeholder="{{ trans('message.table.comments') }} ..." rows="5" class="form-control" name="requestor_comments"></textarea>
                    </div>
                </div>
         
            </div>

        </div>

        <div class='panel-footer'>

            <a href="{{ CRUDBooster::mainpath() }}" class="btn btn-default">{{ trans('message.form.cancel') }}</a>

            <button class="btn btn-primary pull-right" type="submit" id="btnSubmit"> <i class="fa fa-save" ></i> {{ trans('message.form.save') }}</button>

        </div>

    </form>


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
        
        var tableRow = 1;

        $("#application_div").hide();
        $("#application_others_div").hide();

        $("#application_others").removeAttr('required');
        $(".application").removeAttr('required');


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


        $(document).ready(function() {

            const fruits = [];

            $("#add-Row").click(function() {

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
                        '  <input type="text" onkeyup="this.value = this.value.toUpperCase();" class="form-control itemDesc" data-id="' + tableRow + '" id="itemDesc' + tableRow + '"  name="item_description[]"  required maxlength="100">' +
                        '</td>' +  

                        '<td>'+
                            '<select class="form-control drop'+ tableRow + '" name="category_id[]" data-id="' + tableRow + '" id="category_id' + tableRow + '" required>' +
                            //'  <option value="">- Select Category -</option>' +
                            '        @foreach($categories as $data)'+
                            '        <option value="{{$data->category_description}}">{{$data->category_description}}</option>'+
                            '         @endforeach'+
                            '</select>'+
                        '</td>' +


                        '<td>'+
                            '<select selected data-placeholder="- Select Sub Category -" class="form-control sub_category_id" name="sub_category_id[]" data-id="' + tableRow + '" id="sub_category_id' + tableRow + '" required style="width:100%">' +
                            '  <option value=""></option>' +
                            '        @foreach($sub_categories as $data)'+
                            '        <option value="{{$data->class_description}}">{{$data->class_description}}</option>'+
                            '         @endforeach'+
                            '</select>'+
                        '</td>' +

                        /*    
                        '<td>'+
                            '<select class="js-example-basic-multiple" multiple="multiple" name="app_id' + tableRow + '[]" data-id="' + tableRow + '" id="app_id' + tableRow + '" required style="width:100%;">' +
                        
                            '        @foreach($applications as $data)'+
                            '        <option value="{{$data->app_name}}">{{$data->app_name}}</option>'+
                            '         @endforeach'+
                            '</select>'+
                            '<br/><br/><input type="text" onkeyup="this.value = this.value.toUpperCase();" class="form-control AppOthers" data-id="' + tableRow + '" id="AppOthers' + tableRow + '"  name="app_id_others[]" maxlength="100">' +
                        '</td>' +   
                        */        
                        
                        '<td><input class="form-control text-center quantity_item" type="number" required name="quantity[]" id="quantity' + tableRow + '" data-id="' + tableRow  + '"  value="1" min="0" max="9999999999" step="any" onKeyPress="if(this.value.length==4) return false;" oninput="validity.valid;"></td>' +
                        
                        /*'<td><input type="file" name="image[]" id="image' + tableRow + '" accept="image/*"></td>' + */
                        
                        '<td>' +
                            '<button id="deleteRow" name="removeRow" data-id="' + tableRow + '" class="btn btn-danger removeRow"><i class="glyphicon glyphicon-trash"></i></button>' +
                        '</td>' +

                    '</tr>';
                    $(newrow).insertBefore($('table tr#tr-table1:last'));

                    //$('#sub_category_id'+tableRow).attr('disabled', true);

                    $('.js-example-basic-multiple').select2();
                    $('.sub_category_id').select2({
                    placeholder_text_single : "- Select Sub Category -"});
                    $('#app_id'+tableRow).change(function(){

                            /*var appothers = this.value;

                            alert(appothers);
                            if(appothers.includes("OTHERS")){
                                alert('');

                                $('#AppOthers'+$(this).attr("data-id")).show();
                                $('#AppOthers'+$(this).attr("data-id")).attr('required', 'required');

                            }else{

                                $('#AppOthers'+$(this).attr("data-id")).hide();
                                $('#AppOthers'+$(this).attr("data-id")).removeAttr('required');

                            }*/

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
                        
                            // alert(description);

                            $('#app_id'+$(this).attr("data-id")).attr('disabled', false);

                        }else{
                        
                            $('#app_id'+$(this).attr("data-id")).attr('disabled', true);
                        }


                    });


                    $('#AppOthers'+tableRow).hide();
                    $('#AppOthers'+tableRow).removeAttr('required');

                    /*$('#category_id'+tableRow).change(function(){

                        var category = this.value;
                        var description = $('#itemDesc'+$(this).attr("data-id")).val();

                

                        if(description.includes("LAPTOP") && category == "IT ASSETS"){
                        
                        // alert(description);

                            $('#app_id'+$(this).attr("data-id")).attr('disabled', false);

                        }else{
                        
                            $('#app_id'+$(this).attr("data-id")).attr('disabled', true);
                        }

                    });*/

                    

                    $('.sub_category_id').change(function(){

                        var sub_category_id =  this.value;
                        var fruits = [];
                        $(".sub_category_id :selected").each(function() {
                            fruits.push(this.value.toLowerCase().replace(/\s/g, ''));
                        });
                         console.log(fruits);
                        if( fruits.includes("laptop") || fruits.includes("desktop")){

                            $("#application_div").show();
                           // $("#application_others_div").show();

                            //$("#application_others").attr('required', 'required');

                            //$(".application").attr('required', 'required');

                        }else{

                            $("#application_div").hide();
                            $("#application_others_div").hide();

                            $("#application_others").removeAttr('required');
                            //$(".application").removeAttr('required');

                        }

                        

                    });

                    $("#quantity_total").val(calculateTotalQuantity());
                    
                }

            });
            
            //deleteRow
            $(document).on('click', '.removeRow', function() {
               
                var id_data = $(this).attr("data-id");

                if($("#sub_category_id"+id_data).val().toLowerCase().replace(/\s/g, '') == "laptop" || $("#sub_category_id"+id_data).val().toLowerCase().replace(/\s/g, '') == "desktop"){

                        $("#application_div").hide();
                        $("#application_div").val("");
                        $("#application_others_div").hide();
                        $("#application_others").removeAttr('required');
   
                }

                if ($('#asset-items tbody tr').length != 1) { //check if not the first row then delete the other rows
                    tableRow--;

                    $(this).closest('tr').remove();

                    $("#quantity_total").val(calculateTotalQuantity());

                    //$("#tQuantity").val(calculateTotalQuantity());
                    //$("#tValue2").val(calculateTotalValue2());
                    return false;
                }
            });

        });

        var stack = [];
        var token = $("#token").val();

        $(document).ready(function(){
            $(function(){

                $("#search").autocomplete({

                    source: function (request, response) {
                    $.ajax({
                        url: "{{ route('asset.item.search') }}",
                        dataType: "json",
                        type: "POST",
                        data: {
                            "_token": token,
                            "search": request.term
                        },
                        success: function (data) {
                            var rowCount = $('#asset-items tr').length;
                            //myStr = data.sample;   
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
                                        item_cost:                  item.item_cost,
                                     
                                    }

                                }));

                            } else {

                                $('.ui-menu-item').remove();
                                $('.addedLi').remove();
                                $("#ui-id-2").append($("<li class='addedLi'>").text(data.message));
                                var searchVal = $("#search").val();
                                if (searchVal.length > 0) {
                                    $("#ui-id-2").css('display', 'block');
                                } else {
                                    $("#ui-id-2").css('display', 'none');
                                }
                            }
                        }
                    })
                },
                select: function (event, ui) {
                        var e = ui.item;

                        if (e.id) {
                            
                            // if (!in_array(e.id, stack)) {
                                if (!stack.includes(e.id)) {
            
                                    stack.push(e.id);           
                                    
                                    

                                        var new_row = '<tr class="nr" id="rowid' + e.id + '">' +
                                                '<td><input class="form-control text-center" type="text" name="digits_code[]" readonly value="' + e.digits_code + '"></td>' +
                                                '<td><input class="form-control" type="text" name="item_description[]" readonly value="' + e.value + '"></td>' +
                                                '<td><input class="form-control" type="text" name="serial_no[]" readonly value="' + e.serial_no + '"></td>' +
                                                '<td><textarea placeholder="Remarks ..." rows="3" class="form-control" name="remarks[]"></textarea></td>' +

                                                '<td><input class="form-control text-center quantity_item" type="number" name="quantity[]" id="quantity' + e.id  + '" data-id="' + e.id  + '"  value="1" min="0" max="9999999999" step="any" onKeyPress="if(this.value.length==10) return false;" oninput="validity.valid||(value=0);"></td>' +
                            
                                                '<td><input class="form-control text-center cost_item" type="number" name="unit_cost[]" id="unit_cost' + e.id  + '"   data-id="' + e.id  + '"  value="' + e.item_cost + '" min="0" max="9999999999" step="any" onKeyPress="if(this.value.length==10) return false;" oninput="validity.valid||(value=0);"></td>' +
                                                
                                                '<td><input class="form-control text-center total_cost_item" type="number" name="total_unit_cost[]"  id="total_unit_cost' + e.id  + '"   value="' + e.item_cost + '" readonly="readonly" step="0.01" required maxlength="100"></td>' +

                                                '<td class="text-center"><button id="' +e.id + '" onclick="reply_click1(this.id)" class="btn btn-xs btn-danger delete_item" style="width:60px;height:30px;font-size: 11px;text-align: center;">REMOVE</button></td>' +
                                                
                                                '<input type="hidden" name="item_id[]" readonly value="' +e.id + '">' +

                                                '</tr>';
                                    $(new_row).insertAfter($('table tr.dynamicRows:last'));
                
                                    //blank++;

                                    $("#total").val(calculateTotalValue2());

                                    $(this).val('');
                                    $('#val_item').html('');
                                    return false;
                                
                                }else{



                                        $('#quantity' + e.id).val(function (i, oldval) {
                                            return ++oldval;
                                        });

                                        var temp_qty = $('#quantity'+ e.id).attr("data-id");

                                        var q = $('#quantity' +e.id).val();
                                        var r = $("#unit_cost" + e.id).val();

                                        $('#unit_cost' + e.id).val(function (i, amount) {
                                            if (q != 0) {
                                                var itemPrice = (q * r);
                                                return itemPrice;
                                            } else {
                                                return 0;
                                            }
                                        });

                                        $('#'+temp_qty).val(q);



                                        var price = calculatePrice(q, r).toFixed(2); 

                                        $("#total_unit_cost" + e.id).val(price);

                                        $("#quantity_total").val(calculateTotalQuantity());
                                        $("#cost_total").val(calculateTotalValue());
                                        $("#total").val(calculateTotalValue2());

                                        //var subTotalQuantity = calculateTotalQuantity();
                                        //$("#totalQuantity").val(subTotalQuantity);


                                        $(this).val('');
                                        $('#val_item').html('');
                                        return false;

                                }
                                

                        }
                },
              
                minLength: 1,
                autoFocus: true
                });


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
                        
                        
                        /*var i;
                        var showData = [];

                        for (i = 0; i < result.length; ++i) {
                            var j = i + 1;
                            showData[i] = "<option value='"+result[i].id+"'>"+result[i].sub_department_name+"</option>";
                        }
                        //$('.account'+id_data).find('option').remove();
                        //jQuery('.account'+id_data).html(showData);          
                        
                        jQuery('#sub_department_id').html(showData);*/
                    }
                });

        });

        $(document).on('keyup', '.quantity_item', function(ev) {

           // var id = $(this).attr("data-id");
            //var rate = parseFloat($(this).val());
            //var qty = $("#unit_cost" + id).val();

           // var price = calculatePrice(rate, qty).toFixed(2); 

           // $("#total_unit_cost" + id).val(price);
            $("#quantity_total").val(calculateTotalQuantity());
           // $("#cost_total").val(calculateTotalValue());
            //$("#total").val(calculateTotalValue2());
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
            // var strconfirm = confirm("Are you sure you want to send this request?");
            // if (strconfirm == true) {
            //     var countRow = $('#asset-items tfoot tr').length;
            //     // var value = $('.vvalue').val();
            //     if (countRow == 1) {
            //         alert("Please add an item!");
            //         event.preventDefault(); // cancel default behavior
            //     }
            //     var qty = 0;

            //     $('.quantity_item').each(function() {
            //         qty = $(this).val();
            //         if (qty == 0) {
            //             alert("Quantity cannot be empty or zero!");
            //             event.preventDefault(); // cancel default behavior
            //         } else if (qty < 0) {
            //             alert("Negative Value is not allowed!");
            //             event.preventDefault(); // cancel default behavior
            //         }

            //     });
            //     var description = "test";
            //     $('.itemDesc').each(function() {
            //         description = $(this).val();
            //         if (description == null) {
            //             alert("Item Description cannot be empty!");
            //             event.preventDefault(); // cancel default behavior
            //         } else if (description == "") {
            //             alert("Item Description cannot be empty!");
            //             event.preventDefault(); // cancel default behavior
            //         }
            //     });
            //     $(".sub_category_id :selected").each(function() {
            //         if(app_count == 0 && $.inArray($(this).val().toLowerCase().replace(/\s/g, ''),['laptop','desktop']) > -1){
            //             alert("Application cannot be empty!");
            //             event.preventDefault(); // cancel default behavior
            //         }
            //     });                                
            // }else{
            //     return false;
            //     window.stop();
            // }
            event.preventDefault();
            var countRow = $('#asset-items tfoot tr').length;
            var reg = /^0/gi;
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
   
                    $(".sub_category_id :selected").each(function() {
                        if(app_count == 0 && $.inArray($(this).val().toLowerCase().replace(/\s/g, ''),['laptop','desktop']) > -1){
                            swal({  
                                type: 'error',
                                title: 'Please choose an Application!',
                                icon: 'error',
                                confirmButtonColor: "#367fa9",
                                
                            });
                            event.preventDefault();
                            return false;
                        }else{
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
                  
                }
            
        });

    </script>
@endpush