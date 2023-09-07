@extends('crudbooster::admin_template')
    @push('head')
        <style type="text/css">   

            .select2-selection__choice{
                    font-size:14px !important;
                    color:black !important;
            }

            /* The Modal (background) */
            .modal {
                display: none; /* Hidden by default */
                position: fixed; /* Stay in place */
                z-index: 1; /* Sit on top */
                padding-top: 100px; /* Location of the box */
                left: 0;
                top: 0;
                width: 100%; /* Full width */
                height: 100%; /* Full height */
                overflow: auto; /* Enable scroll if needed */
                background-color: rgb(0,0,0); /* Fallback color */
                background-color: rgba(0,0,0,0.4); /* Black w/ opacity */ 
                
            }
            
            /* Modal Content */
            .modal-content {
                background-color: #fefefe;
                margin: auto;
                padding: 20px;
                border: 1px solid #888;
                width: 40%;
                height: auto;
            }
            
            /* The Close Button */
            .close {
                color: #aaaaaa;
                float: right;
                font-size: 28px;
                font-weight: bold;
                margin-top:0;
            }
            
            .close:hover,
            .close:focus {
                color: #000;
                text-decoration: none;
                cursor: pointer;
            }
            #asset-items1 th, td, tr {
                border: 1px solid rgba(000, 0, 0, .5);
                padding: 8px;
            }
            #asset-items th, td, tr {
                border: 1px solid rgba(000, 0, 0, .5);
                padding: 8px;
            }
            .finput {
                border:none;
                /* border-bottom: 1px solid rgba(18, 17, 17, 0.5); */
            }
            input.finput:read-only {
                background-color: #d4edda; 
                color:#155724
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
        <input type="hidden" name="freebies_val" id="freebies_val" value="0">

        <!-- Modal 1-->
        <div id="myModal" class="modal" style="padding: auto">
            <!-- Modal content -->
            <div class="modal-content">
                <div class='callout callout-info'>
                    <h3>SEARCH FOR <label id="item_search"></label></h3>
                    <span style="font-style: italic">*NOTE: Please check and match the Item Description before sending request</span>
                    <input type="hidden"  class="form-control" id="add_item_id">
                    <input type="hidden"  class="form-control" id="button_count">
                    <input type="hidden"  class="form-control" id="button_remove">
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label">{{ trans('message.form-label.add_item1') }}</label>
                               <input class="form-control auto" style="width:100%;" placeholder="Search Item" id="search">
                                 <ul class="ui-autocomplete ui-front ui-menu ui-widget ui-widget-content" id="ui-id-2" style="display: none; top: 60px; left: 15px; width: 570px;">
                                <li>No Item Found...</li>
                            </ul>
                        </div>
                        <button type="button"  class="btn btn-primary pull-right btnsearch" id="searchclose" >Close</button>
                    </div>
                </div> 
              
            </div>               
        </div>
        <!-- Modal -->

        <!-- Modal 2-->
        {{-- <div id="myModal2" class="modal" style="padding: auto">
            <!-- Modal content -->
            <div class="modal-content">
                <div class='callout callout-info'>
                    <h3>SEARCH ITEM <label id="item_search"></label></h3>
                    <span style="font-style: italic">*NOTE: Please check and match the Item Description before sending request</span>
                    <input type="hidden"  class="form-control" id="add_item_id">
                    <input type="hidden"  class="form-control" id="button_count">
                    <input type="hidden"  class="form-control" id="button_remove">
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label">{{ trans('message.form-label.add_item1') }}</label>
                               <input class="form-control auto" style="width:100%;" placeholder="Search Item" id="searchItemMaster">
                                 <ul class="ui-autocomplete ui-front ui-menu ui-widget ui-widget-content" id="ui-id-2" style="display: none; top: 60px; left: 15px; width: 570px;">
                                <li>Loading...</li>
                            </ul>
                        </div>
                        <button type="button"  class="btn btn-primary pull-right btnsearch" id="searchclose2" >Close</button>
                    </div>
                </div> 
            
            </div>               
        </div> --}}
        <!-- Modal -->

        <div class='panel-body'>

            <div class="row">

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="require control-label">*{{ trans('message.form-label.header_request_id') }}:</label>
                        <select class="js-example-basic-single" data-placeholder="** Please a Asset Request"  style="width: 100%;" name="header_request_id" id="header_request_id">
                            <option value=""></option>
                            @foreach($AssetRequest as $value)
                               @if($value->if_from_erf !== NULL)
                                <option value="{{$value->id}}">{{$value->reference_number}} | {{$value->if_from_erf}}</option>
                               @else
                                <option value="{{$value->id}}">{{$value->reference_number}}</option>
                               @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <br/>
                <input type="checkbox" name="lock" id="lock" style="height: 34px;" value="Lock"/>
                
 
            </div>

            <div class="ARFHeader" id="ARFHeader">

            </div>

            <div class="ARFBodyTable" id="ARFBodyTable">

            </div>

            {{-- <div class="row" id="Tag">
                
                <div class="col-md-12">
                    <hr/>
                    <div class="box-header text-center">
                        <h3 class="box-title"><b>{{ trans('message.form-label.asset_items') }}</b></h3>
                    </div>
                    <div class="box-body no-padding">
                        <div class="pic-container">
                            <div class="pic-row">
                                <table  id="asset-items">
                                    <tbody>
                                        <tr class="tbl_header_color dynamicRows">
                                            <th width="13%" class="text-center">{{ trans('message.table.digits_code') }}</th>
                                            <th width="13%" class="text-center">{{ trans('message.table.asset_tag') }}</th>
                                            <th width="26%" class="text-center">{{ trans('message.table.item_description') }}</th>
                                            <th width="18%" class="text-center">{{ trans('message.table.serial_no') }}</th>
                                            <th width="7%" class="text-center">{{ trans('message.table.item_quantity') }}</th>
                                            <th width="10%" class="text-center">{{ trans('message.table.item_cost') }}</th>
                                            <th width="10%" class="text-center">{{ trans('message.table.item_total_cost') }}</th>
                                            <th width="5%" class="text-center">{{ trans('message.table.action') }}</th>
                                        </tr>

                                        <tr class="tableInfo">

                                            <td colspan="6">
                                                <!-- <input type="button" id="add-Row" name="add-Row" class="btn btn-info add" value='Add Freebies' /> -->
                                            </td>
                                            <td align="left" colspan="1">
                                                <input type='hidden' name="quantity_total" class="form-control text-center" id="quantity_total" readonly>
                                                <input type='hidden' name="cost_total" class="form-control text-center" id="cost_total" readonly>
                                                <input type='number' name="total" class="form-control text-center finput" id="total" readonly value="{{$Header->total}}">
                                                </td>
                                            <td colspan="1"></td>
                                        </tr>              
                                    </tbody>

                                </table>
                            </div>
                        </div>                 
                    </div>
                </div>
            </div> --}}

        </div>

        <div class='panel-footer'>

            <a href="{{ CRUDBooster::mainpath() }}" class="btn btn-default">{{ trans('message.form.cancel') }}</a>
            <button class="btn btn-primary pull-right" type="submit" id="btnSubmit" style="margin-left: 5px;"> <i class="fa fa-save" ></i> {{ trans('message.form.save') }}</button>
            @if(CRUDBooster::isSuperadmin())
                <button class="btn btn-danger pull-right" value="cancelLineRequest" type="button" id="btnCancelLineRequest" style="margin-left: 5px;"><i class="fa fa-times-circle" ></i> Cancel Line</button>
            @endif
           

        </div>

    </form>


</div>



@endsection
@push('bottom')
<script type="text/javascript">
    $(function(){
        $('body').addClass("sidebar-collapse");
    });
    $("#Tag").hide();

    var stack = [];
    var token = $("#token").val();

    var modal = document.getElementById("myModal");
    var modal2 = document.getElementById("myModal2");
    $(document).keydown(function(event) { 
    if (event.keyCode == 27) { 
        modal.style.display = "none";
        modal2.style.display = "none";
    }
    });
    $('.btnsearch').click(function() {
        if($("#category").val() == 1 || $("#category").val() == 5){
            document.querySelector("body").style.overflow = 'hidden';
            modal.style.display = "block";
        }else{
            document.querySelector("body").style.overflow = 'hidden';
            modal2.style.display = "block";
        }
    });

    $('#searchclose').click(function() {
        document.querySelector("body").style.overflow = 'visible';
        modal.style.display = "none";
    });
    $('#searchclose2').click(function() {
        document.querySelector("body").style.overflow = 'visible';
        modal2.style.display = "none";
    });

    function preventBack() {
        window.history.forward();
    }
    window.onunload = function() {
        null;
    };
    setTimeout("preventBack()", 0);

    $( "#quote_date, #po_date" ).datepicker( { format: 'yyyy-mm-dd', endDate: new Date() } );

    $(".btnsearch").click(function(event) {
        var searchID = $(this).attr("data-id");
       //alert($("#item_description"+searchID).val());
       $("#item_search").text($("#item_description"+searchID).val());
       $("#add_item_id").val($("#add_item_id"+searchID).val());
       $("#button_count").val(searchID);
       $("#button_remove").val($("#remove_btn"+searchID).val());

    });

    $(document).on('keyup', '.quantity_item', function(ev) {
        var id = $(this).attr("data-id");
        var rate = parseInt($(this).val());
        var qty = parseFloat($("#unit_cost" + id).val());
        var price = calculatePrice(qty, rate); // this is for total Value in row
        if(price == 0){
            price = rate * 1;
        }
        $("#total_unit_cost" + id).val(price.toFixed(2));
        $("#total").val(calculateTotalValue2());
        $("#quantity_total").val(calculateTotalQuantity());

    });

    $(document).on('keyup', '.cost_item', function(ev) {
        var id = $(this).attr("data-id");
        var rate = parseFloat($(this).val());
        var qty = parseInt($("#quantity" + id).val());
        var price = calculatePrice(qty, rate); // this is for total Value in row
        if(price == 0){
            price = rate * 1;
        }
        $("#total_unit_cost" + id).val(price.toFixed(2));
        $("#total").val(calculateTotalValue2());
        $("#quantity_total").val(calculateTotalQuantity());

    });

    function calculatePrice(qty, rate) {
        if (qty != 0) {
            var price = (qty * rate);
            return price;
        }else{
            return '0';
        }

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

    function calculateTotalQuantity() {
            var totalQuantity = 0;
            $('.quantity_item').each(function() {

            totalQuantity += parseInt($(this).val());
            });
            return totalQuantity;
    }

     //CANCEL REQUEST
     $("#btnCancelLineRequest").click(function(event) {
        event.preventDefault();
        var header_id = $('#header_request_id').val();
        var Ids = [];
        $.each($("input[name='body_id_to_cancel[]']:checked"), function(){
            Ids.push($(this).val());
        });
        console.log(Ids);
        if(Ids == ""){
            swal({
                type: 'error',
                title: 'Please select item to cancel!',
                icon: 'error',
                confirmButtonColor: "#367fa9",
            }); 
            event.preventDefault();
            return false;
        }

        swal({
            title: "Reason to cancel",
            type: "input",
            confirmButtonText: 'Proceed',
            confirmButtonColor: "#41B314",
            showCancelButton: true,
            closeOnConfirm: false,
            animation: "slide-from-top",
            inputPlaceholder: "Reason to cancel"
            },
            function(inputValue){
                var remarks = inputValue;
                if (inputValue === "") {
                    swal.showInputError("Reason to cancel required for this process!");
                    return false
                }else{
                    swal({
                        title: "Are you sure?",
                        type: "warning",
                        text: "You won't be able to revert this!",
                        showCancelButton: true,
                        confirmButtonColor: "#41B314",
                        cancelButtonColor: "#F9354C",
                        confirmButtonText: "Yes, cancel it!",
                        width: 450,
                        height: 200
                        }, function () {
                            $.ajaxSetup({
                                headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    }
                            });
                            $.ajax({
                                url: "{{ route('cancel-arf-mo-perline-request') }}",
                                type: "POST",
                                dataType: 'json',
                                data: {
                                    'id' : header_id,
                                    'Ids' : Ids,
                                    'remarks': remarks
                                },
                                success: function (data) {
                                    if (data.status == "success") {
                                        swal({
                                            type: data.status,
                                            title: data.message,
                                        });
                                        window.location.replace(data.redirect_url);
                                        } else if (data.status == "error") {
                                        swal({
                                            type: data.status,
                                            title: data.message,
                                        });
                                    }
                                }
                            });                    
                    });
                }
      
        });
    });

    $("#btnSubmit").click(function(event) {
        var countRow = $('#asset-items tbody tr').length;
        var error = 0;
        event.preventDefault();
        if (countRow == 2) {
            swal({
                type: 'error',
                title: 'Please add an item!',
                icon: 'error',
                confirmButtonColor: "#367fa9",
            }); 
            event.preventDefault(); // cancel default behavior
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


    $(document).on('click', '.delete_item', function() {
        var RowID = $(this).attr("data-id");
        var disabled = $('#remove_disable'+RowID).val();
        $("#searchrow"+disabled).attr('disabled', false);
        $("#freebies_val").val("0");
       // alert(stack.indexOf(RowID));
        if ($('#asset-items tbody tr').length != 1) { //check if not the first row then delete the other rows
            stack.splice(stack.indexOf(parseInt(RowID)), 1);
            $(this).closest('tr').remove();
            $("#total").val(calculateTotalValue2());
            $("#quantity_total").val(calculateTotalQuantity());
            var countRow = $('#asset-items tbody tr').length;
            if (countRow == 2) {
                $("#btnUpdate").attr('disabled', false);
            }
            return false;
        }
    });

    //for IT and FA Request
    $(document).ready(function(){
            $(function(){
                $("#search").autocomplete({
                    source: function (request, response) {
                    $.ajax({
                        url: "{{ route('asset.item.tagging') }}",
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
                                        serial_no:                  item.serial_no,
                                        value:                      item.item_description,
                                        item_cost:                  item.value,
                                        quantity:                   item.quantity,
                                        item_id:                    item.item_id
                                    }

                                }));

                            } else {

                                $('.ui-menu-item').remove();
                                $('.addedLi').remove();
                                $("#ui-id-2").append($("<li class='addedLi'>").text(""));
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

                        modal.style.display = "none";

                        document.querySelector("body").style.overflow = 'visible';

                        $("#add-Row").attr('disabled', false);

                        var e = ui.item;

                        if (e.id) {

                                //$("#btnUpdate").attr('disabled', true);
                                var remove_count = $("#button_remove").val();

                                var add_id = $("#add_item_id").val();
                         
                                $("#searchrow"+ $("#button_count").val()).attr('disabled', true);

                            // if (!in_array(e.id, stack)) {
                                if (!stack.includes(e.id)) {
            
                                    stack.push(e.id);           
                                    
                                        var serials = "";

                                        if(e.serial_no == null || e.serial_no == ""){
                                            serials = "";
                                        }else{
                                            serials = e.serial_no;
                                        }
                                        
                                            var new_row = '<tr class="nr" id="rowid' + e.id + '">' +
                                                    
                                                    '<td><input class="form-control finput text-center" type="text" name="add_digits_code[]" readonly value="' + e.digits_code + '"></td>' +
                                                    '<td><input class="form-control finput text-center" type="text" name="add_asset_code[]" readonly value="' + e.asset_code + '"></td>' +
                                                    '<td><input class="form-control finput" type="text" name="add_item_description[]" readonly value="' + e.value + '"></td>' +
                                                    '<td><input class="form-control finput text-center" type="text" name="add_serial_no[]" readonly value="' + serials + '"></td>' +
                                                    

                                                    '<td><input class="form-control text-center finput quantity_item" type="number" name="add_quantity[]" id="quantity' + e.id  + '" data-id="' + e.id  + '"  value="1" min="0" max="9999999999" step="any" onKeyPress="if(this.value.length==10) return false;" oninput="validity.valid||(value=0);" readonly="readonly"></td>' +
                                
                                                    '<td><input class="form-control finput text-center cost_item" type="number" name="add_unit_cost[]" id="unit_cost' + e.id  + '"   data-id="' + e.id  + '"  value="' + e.item_cost + '" min="0" max="9999999999" step="any" onKeyPress="if(this.value.length==10) return false;" oninput="validity.valid||(value=0);"></td>' +
                                                    
                                                    '<td><input class="form-control finput text-center total_cost_item" type="number" name="add_total_unit_cost[]"  id="total_unit_cost' + e.id  + '"   value="' + e.item_cost + '" readonly="readonly" step="0.01" required maxlength="100"></td>' +

                                                    '<td class="text-center"><button id="' +e.id + '" data-id="' + e.id  + '" onclick="reply_click1(this.id)" class="btn btn-sm btn-danger delete_item" data-toggle="tooltip" data-placement="bottom" title="Remove"><i class="fa fa-trash"></i> </button></td>' +
                                                    
                                                    '<input type="hidden" name="body_request_id[]" readonly value="' + add_id + '">' +

                                                    '<input type="hidden" name="inventory_id[]" readonly value="' +e.id + '">' +
                                                    
                                                    '<input type="hidden" name="item_id[]" readonly value="' +e.item_id + '">' +

                                                    '<input type="hidden" name="remove_disable[]" id="remove_disable' + e.id  + '" readonly value="' + remove_count + '">' +

                                                    '</tr>';

                                    $(new_row).insertAfter($('table tr.dynamicRows:last'));
                
                                    //blank++;

                                    //$("#total").val(calculateTotalValue2());
                                    $("#total").val(calculateTotalValue2());
                                    $("#quantity_total").val(calculateTotalQuantity());

                                    $(this).val('');
                                    $('#val_item').html('');
                                    return false;
                                
                                }else{


                                    if(e.serial_no == null || e.serial_no == ""){

                                        $('#quantity' + e.id).val(function (i, oldval) {
                                            return ++oldval;
                                        });

                                       
                                        var q = parseInt($('#quantity' +e.id).val());
                                        var r = parseFloat($("#unit_cost" + e.id).val());

                                        var price = calculatePrice(q, r).toFixed(2); 

                                        $("#total_unit_cost" + e.id).val(price);

                                      

                                        //var subTotalQuantity = calculateTotalQuantity();
                                        //$("#totalQuantity").val(subTotalQuantity);

                                        $("#total").val(calculateTotalValue2());
                                        $("#quantity_total").val(calculateTotalQuantity());

                                        $(this).val('');
                                        $('#val_item').html('');
                                        return false;
                                    }else{

                                        alert("Only 1 quantity is allowed in serialized items!");

                                        $("#searchrow"+ $("#button_count").val()).attr('disabled', false);

                                        $(this).val('');
                                        $('#val_item').html('');
                                        return false;

                                    }

                                }
                                

                        }

                       
                },
              
                minLength: 1,
                autoFocus: true
                });


            });

            var AddRow = 1;

            $("#add-Row").click(function() {

                /*var description = "";
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
                
                tableRow++;*/

                //if(count_fail == 0){

                    var newrow =
                    '<tr>' +
                    '<td><input class="form-control text-center itemDcode" type="text" name="add_digits_code[]" required max="99999999"></td>' +
                    '<td><input class="form-control text-center" type="text" name="add_asset_code[]" ></td>' +
                    '<td><input class="form-control itemDesc" type="text" name="add_item_description[]" required onkeyup="this.value = this.value.toUpperCase();"></td>' +
                    '<td><input class="form-control" type="text" name="add_serial_no[]"  ></td>' +
                    '<td><input class="form-control text-center quantity_item" type="number" name="add_quantity[]" id="quantity' + AddRow  + '" data-id="' + AddRow  + '"  value="1" min="0" max="9999999999" step="any" onKeyPress="if(this.value.length==10) return false;" oninput="validity.valid||(value=0);" readonly="readonly"></td>' +
                    '<td><input class="form-control text-center cost_item" type="number" name="add_unit_cost[]" id="unit_cost' + AddRow  + '"   data-id="' + AddRow  + '"  value="0" min="0" max="9999999999" step="any" onKeyPress="if(this.value.length==10) return false;" oninput="validity.valid||(value=0);" required></td>' +
                    '<td><input class="form-control text-center total_cost_item" type="number" name="add_total_unit_cost[]"  id="total_unit_cost' + AddRow  + '" readonly="readonly" value="0" step="0.01" required maxlength="100"></td>' +
                    '<td class="text-center"><button id="' + AddRow + '" data-id="' + AddRow  + '" onclick="reply_click1(this.id)" class="btn btn-xs btn-danger delete_item" style="width:60px;height:30px;font-size: 11px;text-align: center;">REMOVE</button></td>' +
                    
                    '<input type="hidden" name="body_request_id[]" readonly value="">' +
                    '<input type="hidden" name="inventory_id[]" readonly >' +              
                    '<input type="hidden" name="item_id[]" readonly >' +

                    '<input type="hidden" name="remove_disable[]" id="remove_disable' + AddRow  + '" readonly >' +
                    
                    '</tr>';
                    $(newrow).insertAfter($('table tr.dynamicRows:last'));

                    $("#freebies_val").val("1");
                    

                    //$('#sub_category_id'+tableRow).attr('disabled', true);

                //}

            });
    });

    //for SUPPLIES and MARKETING Request
    $(document).ready(function(){
            $(function(){
                $("#searchItemMaster").autocomplete({
                    source: function (request, response) {
                    $.ajax({
                        url: "{{ route('asset.item.supplies.marketing.tagging') }}",
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
                                        asset_code:                 "",
                                        digits_code:                item.digits_code,
                                        serial_no:                  "",
                                        value:                      item.item_description,
                                        item_cost:                  item.value,
                                        quantity:                   item.quantity,
                                        item_id:                    item.item_id
                                    }

                                }));

                            } else {

                                $('.ui-menu-item').remove();
                                $('.addedLi').remove();
                                $("#ui-id-2").append($("<li class='addedLi'>").text(data.message));
                                var searchVal = $("#searchItemMaster").val();
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

                        modal2.style.display = "none";

                        document.querySelector("body").style.overflow = 'visible';

                        $("#add-Row").attr('disabled', false);

                        var e = ui.item;

                        if (e.id) {

                                //$("#btnUpdate").attr('disabled', true);
                                var remove_count = $("#button_remove").val();

                                var add_id = $("#add_item_id").val();
                         
                                $("#searchrow"+ $("#button_count").val()).attr('disabled', true);

                            // if (!in_array(e.id, stack)) {
                                if (!stack.includes(e.id)) {
            
                                    stack.push(e.id);           
                                    
                                        var serials = "";

                                        if(e.serial_no == null || e.serial_no == ""){
                                            serials = "";
                                        }else{
                                            serials = e.serial_no;
                                        }
                                        
                                            var new_row = '<tr class="nr" id="rowid' + e.id + '">' +
                                                    
                                                    '<td><input class="form-control text-center" type="text" name="add_digits_code[]" readonly value="' + e.digits_code + '"></td>' +
                                                    '<td><input class="form-control text-center" type="text" name="add_asset_code[]" readonly value="' + e.asset_code + '"></td>' +
                                                    '<td><input class="form-control" type="text" name="add_item_description[]" readonly value="' + e.value + '"></td>' +
                                                    '<td><input class="form-control" type="text" name="add_serial_no[]" readonly value="' + serials + '"></td>' +
                                                    

                                                    '<td><input class="form-control text-center quantity_item" type="number" name="add_quantity[]" id="quantity' + e.id  + '" data-id="' + e.id  + '"  value="1" min="0" max="9999999999" step="any" onKeyPress="if(this.value.length==10) return false;" oninput="validity.valid||(value=0);" readonly="readonly"></td>' +
                                
                                                    '<td><input class="form-control text-center cost_item" type="number" name="add_unit_cost[]" id="unit_cost' + e.id  + '"   data-id="' + e.id  + '"  value="' + e.item_cost + '" min="0" max="9999999999" step="any" onKeyPress="if(this.value.length==10) return false;" oninput="validity.valid||(value=0);"></td>' +
                                                    
                                                    '<td><input class="form-control text-center total_cost_item" type="number" name="add_total_unit_cost[]"  id="total_unit_cost' + e.id  + '"   value="' + e.item_cost + '" readonly="readonly" step="0.01" required maxlength="100"></td>' +

                                                    '<td class="text-center"><button id="' +e.id + '" data-id="' + e.id  + '" onclick="reply_click1(this.id)" class="btn btn-xs btn-danger delete_item" style="width:60px;height:30px;font-size: 11px;text-align: center;">REMOVE</button></td>' +
                                                    
                                                    '<input type="hidden" name="body_request_id[]" readonly value="' + add_id + '">' +

                                                    '<input type="hidden" name="inventory_id[]" readonly value="' +e.id + '">' +
                                                    
                                                    '<input type="hidden" name="item_id[]" readonly value="' +e.item_id + '">' +

                                                    '<input type="hidden" name="remove_disable[]" id="remove_disable' + e.id  + '" readonly value="' + remove_count + '">' +

                                                    '</tr>';

                                    $(new_row).insertAfter($('table tr.dynamicRows:last'));
                
                                    //blank++;

                                    //$("#total").val(calculateTotalValue2());
                                    $("#total").val(calculateTotalValue2());
                                    $("#quantity_total").val(calculateTotalQuantity());

                                    $(this).val('');
                                    $('#val_item').html('');
                                    return false;
                                
                                }else{


                                    if(e.serial_no == null || e.serial_no == ""){

                                        $('#quantity' + e.id).val(function (i, oldval) {
                                            return ++oldval;
                                        });

                                       
                                        var q = parseInt($('#quantity' +e.id).val());
                                        var r = parseFloat($("#unit_cost" + e.id).val());

                                        var price = calculatePrice(q, r).toFixed(2); 

                                        $("#total_unit_cost" + e.id).val(price);

                                      

                                        //var subTotalQuantity = calculateTotalQuantity();
                                        //$("#totalQuantity").val(subTotalQuantity);

                                        $("#total").val(calculateTotalValue2());
                                        $("#quantity_total").val(calculateTotalQuantity());

                                        $(this).val('');
                                        $('#val_item').html('');
                                        return false;
                                    }else{

                                        alert("Only 1 quantity is allowed in serialized items!");

                                        $("#searchrow"+ $("#button_count").val()).attr('disabled', false);

                                        $(this).val('');
                                        $('#val_item').html('');
                                        return false;

                                    }

                                }
                                

                        }

                       
                },
              
                minLength: 1,
                autoFocus: true
                });


            });

            var AddRow = 1;

            $("#add-Row").click(function() {

                /*var description = "";
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
                
                tableRow++;*/

                //if(count_fail == 0){

                    var newrow =
                    '<tr>' +
                    '<td><input class="form-control text-center itemDcode" type="text" name="add_digits_code[]" required max="99999999"></td>' +
                    '<td><input class="form-control text-center" type="text" name="add_asset_code[]" ></td>' +
                    '<td><input class="form-control itemDesc" type="text" name="add_item_description[]" required onkeyup="this.value = this.value.toUpperCase();"></td>' +
                    '<td><input class="form-control" type="text" name="add_serial_no[]"  ></td>' +
                    '<td><input class="form-control text-center quantity_item" type="number" name="add_quantity[]" id="quantity' + AddRow  + '" data-id="' + AddRow  + '"  value="1" min="0" max="9999999999" step="any" onKeyPress="if(this.value.length==10) return false;" oninput="validity.valid||(value=0);" readonly="readonly"></td>' +
                    '<td><input class="form-control text-center cost_item" type="number" name="add_unit_cost[]" id="unit_cost' + AddRow  + '"   data-id="' + AddRow  + '"  value="0" min="0" max="9999999999" step="any" onKeyPress="if(this.value.length==10) return false;" oninput="validity.valid||(value=0);" required></td>' +
                    '<td><input class="form-control text-center total_cost_item" type="number" name="add_total_unit_cost[]"  id="total_unit_cost' + AddRow  + '" readonly="readonly" value="0" step="0.01" required maxlength="100"></td>' +
                    '<td class="text-center"><button id="' + AddRow + '" data-id="' + AddRow  + '" onclick="reply_click1(this.id)" class="btn btn-xs btn-danger delete_item" style="width:60px;height:30px;font-size: 11px;text-align: center;">REMOVE</button></td>' +
                    
                    '<input type="hidden" name="body_request_id[]" readonly value="">' +
                    '<input type="hidden" name="inventory_id[]" readonly >' +              
                    '<input type="hidden" name="item_id[]" readonly >' +

                    '<input type="hidden" name="remove_disable[]" id="remove_disable' + AddRow  + '" readonly >' +
                    
                    '</tr>';
                    $(newrow).insertAfter($('table tr.dynamicRows:last'));

                    $("#freebies_val").val("1");
                    

                    //$('#sub_category_id'+tableRow).attr('disabled', true);

                //}

            });
    });

    $(document).ready(function(){
        $('.js-example-basic-single').select2();
    });

    $("#btnSubmit").attr('disabled', true);
    $("#add-Row").attr('disabled', true);
    $('#header_request_id').on('change', function() {
        selected_header = this.value;
        //var channel = $('#channels_id').val();
        //$("#template_checker").val(selected_template);
        //$(".nr-item").remove();
        $.ajax({
                type: 'POST',
                url: ADMIN_PATH + "/selectedHeader",
                data: {
                    "_token": token,
                    "header_request_id": selected_header,
                },
                success: function(data) {
                    $('.ARFHeader').empty().append(data.ARFHeader);
                    $('.ARFBodyTable').empty().append(data.ARFBodyTable);
                    $("#Tag").show();
                    //$('.tab').append(data.upc_nav);
                    //$('.add').append(data.upc_div);
                    //$('.hidden_div').append(data.hidden_fields);
                    //$('#div_count').val(data.count_items);
                },
                error: function(e) {
                    alert(e);
                    console.log(e);
                }
        });

    });

    $('#lock').change(function() {
        var id = $(this).attr("data-id");
        var ischecked= $(this).is(':checked');
        if(ischecked == false){
            $("#btnSubmit").attr('disabled', true);
            $(".btnsearch").attr('disabled', true);
            $("#header_request_id").attr('disabled', false);
                
        }else{
            $("#btnSubmit").attr('disabled', false);
            $(".btnsearch").attr('disabled', false);
            $("#header_request_id").attr('disabled', true);
            $("#lock").attr('disabled', true);
        }

    });

</script>
@endpush