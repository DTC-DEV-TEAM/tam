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

            img[data-action="zoom"] {
            cursor: pointer;
            cursor: -webkit-zoom-in;
            cursor: -moz-zoom-in;
            }
            .gallery {
            position: relative;
            z-index: 666;
            -webkit-transition: all 300ms;
                -o-transition: all 300ms;
                    transition: all 300ms;
            }
            .zoom-overlay {
            z-index: 1000;
            background: #fff;
            top: 0;
            left: ;
            right: 0;
            bottom: 0;
            pointer-events: none;
            filter: "alpha(opacity=0)";
            opacity: 0;
            -webkit-transition:      opacity 300ms;
                -o-transition:      opacity 300ms;
                    transition:      opacity 300ms;
            }
            .zoom-overlay-open .zoom-overlay {
            filter: "alpha(opacity=100)";
            opacity: 1;
            }
            .zoom-overlay-open,
            .zoom-overlay-transitioning {
            cursor: default;
            z-index: 1000;
            position: relative;
            }
            .swal-wide{
                width:500px !important;
            }
                
            .body_gallery_image {
                display:flex;
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

            /* loading spinner */
            .loading {
                z-index: 20;
                position: absolute;
                top: 0;
                bottom:0;
                left:0;
                width: 100%;
                height: 1500px;
                background-color: rgba(0,0,0,0.4);
            }
            .loading-content {
                position: absolute;
                border: 16px solid #f3f3f3; /* Light grey */
                border-top: 16px solid #3498db; /* Blue */
                border-radius: 50%;
                width: 50px;
                height: 50px;
                top: 40%;
                left:50%;
                bottom:0;
                /* margin-left: -4em; */
                animation: spin 2s linear infinite;
                }
                
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }

                .swal-wide{
                    width:480px !important;
                    height:315px !important;
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
    Asset Inventory Form
    </div>
    
    <form action='{{CRUDBooster::mainpath('add-save')}}' method="POST" id="InventoryForm" enctype="multipart/form-data">
        <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
        <input type="hidden" value="1" name="request_type_id" id="request_type_id">

        <div class='panel-body'>
        <section id="loading">
            <div id="loading-content"></div>
        </section>
           <div class="row">
             <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"><span style="color:red">*</span> PO NO</label>
                            <input class="form-control" type="text"  placeholder="PO NO" name="po_no" id="po_no">
                        </div>
                    </div>
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
                    <!-- <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"><span style="color:red">*</span> RR Date</label>
                            <input class="form-control date" type="text" placeholder="Select Date" name="rr_date" id="rr_date">
                        </div>
                    </div> -->
             </div>
            </div>
            <!-- <div class="row">
              <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"><span style="color:red">*</span>  Invoice Date</label>
                            <input type="text" class="form-control date" placeholder="Select Date" name="invoice_date" id="invoice_date">
                        
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"><span style="color:red">*</span>  Invoice No.</label>
                            <input type="text" class="form-control" style="" placeholder="Invoice NO" name="invoice_no" id="invoice_no">
                        </div>
                    </div>
                    
                </div>
            </div> -->

            <div class="row">
              <div class="col-md-12">
                    <!-- <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"><span style="color:red">*</span> Warranty Coverage Date</label>
                            <input class="form-control" type="text" style="" placeholder="(Years)" name="expiration_date" id="expiration_date">
                        </div>
                    </div>
                       <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"><span style="color:red">*</span> Location</label>
                            <select required selected data-placeholder="-- Please Select Location --" id="location" name="location" class="form-select select2" style="width:100%;">
                            @foreach($warehouse_location as $res)
                                <option value=""></option>
                                <option value="{{ $res->location }}">{{ $res->location }}</option>
                            @endforeach
                            </select>
                        </div>
                       </div> -->
                    <!-- <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"><span style="color:red">*</span> Upload SI/DR</label>
                            <input type="file" class="form-control" style="" name="si_dr[]" id="si_dr" multiple accept="image/png, image/gif, image/jpeg">
                            <div class="gallery" style="margin-bottom:5px; margin-top:15px"></div>
                            <a class="btn btn-xs btn-danger" style="display:none; margin-left:10px" id="removeImageHeader" href="#"><i class="fa fa-remove"></i></a>
                        </div>
                    </div> -->
                    <!-- <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Wattage</label>
                            <input class="form-control" type="text" style="" name="wattage" id="wattage">
                        </div>
                    </div> -->
                  
                </div>
            </div>
            <div class="row">
              <div class="col-md-12">
              <!-- <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">3 Phase</label>
                            <input class="form-control" type="text" style="" name="phase" id="phase">
                        </div>
                    </div> -->
                    
                </div>
            </div>
            <br>
            <div class="row">
             <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"><span style="color:red">*</span> {{ trans('message.form-label.add_item') }}</label>
                            <input class="form-control auto" placeholder="Search Item" id="search">
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
                        <h3 class="box-title"><b>Asset Details</b></h3>
                    </div>
                                <div class="box-body no-padding">
                                    <div class="table-responsive">
                                        <div class="pic-container">
                                            <div class="pic-row">
                                                <table class="table table-bordered" id="asset-items" style="overflow-x: auto; height:auto;">
                                                    <tbody id="bodyTable">
                                                        <tr class="tbl_header_color dynamicRows">
                                                            <th width="10%" class="text-center">{{ trans('message.table.digits_code') }}</th>
                                                            <th width="30%" class="text-center">{{ trans('message.table.item_description') }}</th>
                                                            <th width="8%" class="text-center">Value</th>
                                                            <!-- <th width="10%" class="text-center">{{ trans('message.table.item_type') }}</th>      -->
                                                            <th width="15%" class="text-center">{{ trans('message.table.quantity_text') }}</th>
                                                            <!-- <th width="15%" class="text-center"> Serial No</th>                                                                                                   -->
                                                            <th width="15%" class="text-center"> Warranty Coverage(Year)</th>                                                     
                                                            <!-- <th width="10%" class="text-center">{{ trans('message.table.image') }}</th>  -->
                                                            <th width="8%" class="text-center">Action</th>
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
            
                                                            <!--<td colspan="3">
                                                                <input type="button" id="add-Row" name="add-Row" class="btn btn-info add" value='Add Item' />
                                                            </td>
                                                            <td align="left" colspan="1">
                                                                <input type='number' name="quantity_total" class="form-control text-center" id="quantity_total" readonly>
                                                            </td> -->
                                                        </tr>
                                                    </tfoot>

                                                </table>
                                            </div>
                                        </div>
                                
                                    </div>
                                    <br>
                                </div>
                </div>
            </div>

        </div>

        <div class='panel-footer'>

            <a href="{{ CRUDBooster::mainPath() }}" class="btn btn-default">{{ trans('message.form.cancel') }}</a>

            <button class="btn btn-primary pull-right" type="submit" id="btnSubmit"> <i class="fa fa-save" ></i>  Save</button>

        </div>

    </form>


</div>

@endsection

@push('bottom')
    <script type="text/javascript">
        //preview image before save
        $(function() {
        // Multiple images preview in browser
        var imagesPreview = function(input, placeToInsertImagePreview) {

            if (input.files) {
                var filesAmount = input.files.length;

                for (i = 0; i < filesAmount; i++) {
                    var reader = new FileReader();

                    reader.onload = function(event) {
                        $($.parseHTML('<img height="120px" class="header_images" width="180px;" hspace="10" data-action="zoom">')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
                    }

                    reader.readAsDataURL(input.files[i]);
                }
            }

        };

            $('#si_dr').on('change', function() {
                imagesPreview(this, 'div.gallery');
                $("#removeImageHeader").toggle(); 
            });
        });
          //remove image header from preview
          $("#removeImageHeader").click(function(e) {
            e.preventDefault(); // prevent default action of link
            $('.header_images').attr('src', ""); //clear image src
            $("#si_dr").val(""); // clear image input value
            $('.header_images').remove();
            $("#removeImageHeader").toggle(); // hide remove link.
        });
        function preventBack() {    
            window.history.forward();
        }
         window.onunload = function() {
            null;
        };
        setTimeout("preventBack()", 0);
        function selectRefresh() {
        $('.main .select2').select2({
            //-^^^^^^^^--- update here
            tags: true,
            placeholder: "Select an Option",
            allowClear: true,
            //width: '100%'
        });
        }
        $(document).ready(function() {
            $('.select2').select2({placeholder_text_single : "-- Select --"})
            $("#InventoryForm").submit(function(event) {
                $("#btnSubmit").attr("disabled", true);
                return true;
            });

            $('#InventoryForm').on('keypress', function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) { 
                e.preventDefault();
                return false;
            }
            });
            $(".date").datetimepicker({
                    viewMode: "days",
                    format: "YYYY-MM-DD",
                    dayViewHeaderFormat: "MMMM YYYY",
            });

        
            // $table  = $('#asset-items'),            // cache the target table DOM element
            // $rows   = $('tbody > tr', $table);     // cache all rows from the target table body

            // $rows.sort(function(a, b) {

            //     var keyA = $('td',a).text();
            //     var keyB = $('td',b).text();

            //     //if (sortAsc) {
            //         return (keyA > keyB) ? 1 : 0;     // A bigger than B, sorting ascending
            //     // } else {
            //     //     return (keyA < keyB) ? 1 : 0;     // B bigger than A, sorting descending
            //     // }
            // });

            // $rows.each(function(index, row){
            // $table.append(row);                    // append rows after sort
            // });
        });
       
  
        $("#btnSubmit").click(function(event) {
            event.preventDefault();
            var countRow = $('#asset-items tbody tr').length;
            // bind test on any change event
       
            if($('#po_no').val() === ""){
                        swal({
                            type: 'error',
                            title: 'PO No required!',
                            icon: 'error',
                            customClass: 'swal-wide'
                        });
                        event.preventDefault();
            }else if($('#rr_date').val() === ""){
                swal({
                    type: 'error',
                    title: 'RR Date required!',
                    icon: 'error',
                    customClass: 'swal-wide'
                });
                event.preventDefault();
            }else if($('#invoice_date').val() === ""){
                swal({
                    type: 'error',
                    title: 'Invoice Date required!',
                    icon: 'error',
                    customClass: 'swal-wide'
                });
                event.preventDefault();
            }else if($('#invoice_no').val() === ""){
                swal({
                    type: 'error',
                    title: 'Invoice No required!',
                    icon: 'error',
                    customClass: 'swal-wide'
                });
                event.preventDefault();
            }else if($('#expiration_date').val() === ""){
                swal({
                    type: 'error',
                    title: 'Warranty Coverage Date required!',
                    icon: 'error',
                    customClass: 'swal-wide'
                });
                event.preventDefault();
            }else if($('#si_dr').val() === ""){
                swal({
                    type: 'error',
                    title: 'Upload SR/DR required!',
                    icon: 'error',
                    customClass: 'swal-wide'
                });
                event.preventDefault();
            }else if (countRow == 3) {
                swal({
                    type: 'error',
                    title: 'Please add an item!',
                    icon: 'error',
                    customClass: 'swal-wide'
                });
                event.preventDefault(); // cancel default behavior
            }else{

            $.ajax({
                    url: "{{ route('assets.check.row') }}",
                    dataType: "json",
                    type: "POST",
                    data: {
                        "_token": token,
                        //"search": request.term
                    },
                    success: function (data) {
                        var n = $("input[name^='digits_code']").length;
                        var dc_codes = $("input[name^='digits_code']");
                        var serial_no = $("input[name^='serial_no']");
                        var cont_one = [];
                        for(i=0;i<n;i++){
                        dc_value =  dc_codes.eq(i).val().concat('-',serial_no.eq(i).val());
                        cont_one.push(dc_value);
                        }
                     
                        var n_qty = $("input[name^='digits_code_on_qty']").length;
                        var dc_codes_qty = $("input[name^='digits_code_on_qty']");
                        var serial_no_qty = $("input[name^='serial_no_on_qty']");
                        var cont_two = [];
                        for(i=0;i<n_qty;i++){
                        dc_value_qty =  dc_codes_qty.eq(i).val().concat('-',serial_no_qty.eq(i).val());
                        cont_two.push(dc_value_qty);
                        }
                        var checkRow = $.merge(cont_one, cont_two);
                        console.log(checkRow);
                        var checkRowFinal = checkRow.filter(function(elem, index, self) {
                            return index === self.indexOf(elem);
                        });

                        //header image validation
                        // for (var i = 0; i < $("#si_dr").get(0).files.length; ++i) {
                        //     var file1=$("#si_dr").get(0).files[i].name;
                        //     if(file1){                        
                        //         var file_size=$("#si_dr").get(0).files[i].size;
                        //         if(file_size<2097152){
                        //             var ext = file1.split('.').pop().toLowerCase();                            
                        //             if($.inArray(ext,['jpg','jpeg','gif','png'])===-1){
                        //                 swal({
                        //                     type: 'error',
                        //                     title: 'Invalid Image Extension for SI/DR!',
                        //                     icon: 'error',
                        //                     customClass: 'swal-wide'
                        //                 });
                        //                 event.preventDefault();
                        //                 return false;
                        //             }

                        //         }else{
                        //             alert("Screenshot size is too large.");
                        //             return false;
                        //         }                        
                        //     }
                        // }

                        //each value validation
                        var v = $("input[name^='value']").length;
                        var value = $("input[name^='value']");
                        for(i=0;i<v;i++){
                            if(value.eq(i).val() == 0){
                                swal({
                                        type: 'error',
                                        title: 'Value required!',
                                        icon: 'error',
                                        customClass: 'swal-wide'
                                    });
                                    event.preventDefault();
                                    return false;
                            }else if(value.eq(i).val() < 0){
                                swal({
                                    type: 'error',
                                    title: 'Value should not be negative!',
                                    icon: 'error',
                                    customClass: 'swal-wide'
                                });
                                event.preventDefault();
                                return false;
                            }
                        
                        }

                        // var finalDuplicateData = checkRow;
                        // var dupArrData = finalDuplicateData.sort(); 
                        // if($('.serial_no').val() != ""){
                        //     for (var i = 0; i < dupArrData.length - 1; i++) {
                        //     if (dupArrData[i + 1] == dupArrData[i]) {
                        //         swal({
                        //                 type: 'error',
                        //                 title: 'Not allowed duplicate Serial No. and Digits Code!',
                        //                 icon: 'error'
                        //             }); 
                        //            event.preventDefault();
                        //             return false;
                        //         }
                        //     }
                        // }

                        //Each Warranty Coverage Validation
                        var w = $("input[name^='warranty_coverage']").length;
                        var warranty_coverage = $("input[name^='warranty_coverage']");
                        for(i=0;i<w;i++){
                            if(warranty_coverage.eq(i).val() === ""){
                                swal({
                                        type: 'error',
                                        title: 'Warranty Coverage Year required!',
                                        icon: 'error',
                                        customClass: 'swal-wide'
                                    });
                                    event.preventDefault();
                                    return false;
                            }else if(warranty_coverage.eq(i).val() <= 0){
                                swal({
                                        type: 'error',
                                        title: 'Warranty Coverage Year Cannot be zero!',
                                        icon: 'error'
                                    });
                                    event.preventDefault();
                                    return false;
                            }
                        
                        }

                        //limit adding per row by 90
                        var quantity = $(".add_quantity").length;              
                            if(quantity > 90){
                                swal({
                                        type: 'error',
                                        title: 'Quantity cannot be greater than 90!',
                                        icon: 'error',
                                        customClass: 'swal-wide'
                                    });
                                    event.preventDefault();
                                    return false;
                            }
                        
                        

                        $.each(checkRowFinal, function(index, item) {
                            if($.inArray(item, data.items) != -1){
                                swal({
                                        type: 'error',
                                        title: 'Digits Code and Serial Already Exist! (' + item + ')',
                                        icon: 'error'
                                    }); 
                                   event.preventDefault();
                                    return false;
                            }
                            else if($('#warranty_coverage').val() === ""){
                                swal({
                                    type: 'error',
                                    title: 'Warranty Coverage Year required!',
                                    icon: 'error',
                                    customClass: 'swal-wide'
                                });
                                event.preventDefault();
                            }
                            else{
                                swal({
                                title: "Are you sure?",
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonColor: "#41B314",
                                cancelButtonColor: "#F9354C",
                                confirmButtonText: "Yes, submit it!",
                                customClass: 'swal-wide'
                                }, function () {
                                    $("#InventoryForm").submit();  
                                    showLoading();                      
                                });
                            }
                            
                        });
                        }
                    });
                }
           
            // var value = $('.vvalue').val();
            // input validation goes here
            // var rowTotalQty = 0;
            // $('.add_quantity').each(function () {
            //     rowTotalQty += parseFloat($(this).val());
            //     if(rowTotalQty > 90){
            //         swal({
            //         type: 'error',
            //         title: 'Quantity cannot be greater than 90!',
            //         icon: 'error',
            //         width: 450,
            //         height: 200
            //     });
            //     event.preventDefault(); 
            //     }
            // });
       
          
            var qty = 0;
            // $('.add_quantity').each(function() {
            // var qty = $(this).val();
            //    if (qty == 0) {
            //     swal({
            //         type: 'error',
            //         title: 'Quantity cannot be empty or zero!',
            //         icon: 'error',
            //         width: 450,
            //         height: 200
            //     });
            //     event.preventDefault(); // cancel default behavior
            // } else 
            // if (qty < 0) {
            //     swal({
            //         type: 'error',
            //         title: 'Negative Value is not allowed!',
            //         icon: 'error',
            //         width: 450,
            //         height: 200
            //     });
            //     event.preventDefault(); // cancel default behavior
            // }
            // });

           //body image validation
        //    var image = 0;
        //    $('.body_image').each(function () {
        //         image = $(this).val();
        //         if($('#checkImage').val() === "null" && image === ""){
        //             swal({
        //             type: 'error',
        //             title: 'Please upload image for this item!',
        //             icon: 'error',
        //             width: 450,
        //             height: 200
        //         });
        //         event.preventDefault(); 
        //         }else if($('#checkImage').val() != "null" && image != ""){
        //                 swal({
        //                     type: 'error',
        //                     title: 'Item has already image!',
        //                     icon: 'error',
        //                     width: 450,
        //                     height: 200
        //                 });
        //                 event.preventDefault();
        //             }
             
        //     });
 
        });
        
        var stack = [];
        var token = $("#token").val();

        $(document).ready(function(){
            selectRefresh();
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
                            console.log(data.items);
                            if(data.items === null){
                                swal({
                                type: 'error',
                                title: 'Item not Found!',
                                icon: 'error',
                                width: 450,
                                height: 200
                               });
                            }else{                              
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
                                        category_id:                item.cat_id,
                                        item_cost:                  item.item_cost,
                                        item_type:                  item.item_type,
                                        image:                      item.image,
                                        quantity:                   item.quantity,
                                        total_quantity:                   item.total_quantity,
                                     
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
                        }
                    })
                    
                },
                select: function (event, ui) {
                        var e = ui.item;               
                        if (e.id) {   
                           // if (!in_array(e.id, stack)) {
                                if (!stack.includes(e.id)) {            
                                    //stack.push(e.id);                                                                                
                                    // if(e.item_type == "SERIAL"){
                                    //    console.log(e.item_type);
                                        // if(e.quantity != 0){
                                        //     swal({
                                        //         type: 'error',
                                        //         title: 'Only 1 quantity is allowed in serialized items!',
                                        //         icon: 'error',
                                        //         width: 450,
                                        //         height: 200
                                        //     });

                                        // }else{
                                            var new_row = '<tr class="nr" id="rowid' + e.id + '" rows>' +
                                                '<td><input class="form-control text-center" type="text" id="dc" name="digits_code[]" readonly value="' + e.digits_code + '" style="width:150px;"></td>' +
                                                '<td><input class="form-control" type="text" name="item_description[]" readonly value="' + e.value + '" style="width:250px;"></td>' +
                                                '<td><input class="form-control amount" placeholder="Value" type="text" name="value[]" id="value" style="width:160px;" required min="1" max="9999999999"></td>' +
                                                //'<td><select required selected data-placeholder="-- Select Type --" id="item_type' + e.id + '" name="item_type[]" class="select2 item_type" style="width:150px;"><option value=""></option><option value="Serial" data-id="' + e.digits_code + '">Serial</option><option value="General" data-id="' + e.digits_code + '">GENERAL</option></select></td>' +
                                                '<td><input class="form-control text-center add_quantity" placeholder="Quantity" style="width:160px;" type="text" value="1" readonly name="add_quantity[]" id="add_quantity' + e.id  + '" data-id="' + e.id  + '"  min="0" max="9999999999" step="1" onkeypress="return event.charCode >= 48 && event.charCode <= 57" oninput="validity.valid||(value=1);"></td>' +   
                                                // '<td><input class="form-control serial_no" type="text" placeholder="Serial No (Optional)" name="serial_no[]" value="" style="width:150px;" data-index="1"></td>' + 
                                                '<td><input class="form-control" type="text" placeholder="(Year)" name="warranty_coverage[]" style="width:160px;" id="warranty_coverage" min="1" max="9999999999" step="1" onkeypress="return event.charCode <= 57"></td>' +                                                                           
                                                //'<td class="images_flex"><input type="file" class="form-control body_image" onchange="readURL(this);" id="body_image_body' + e.id + '" name="item_photo[]" style="width:200px;" accept="image/png, image/gif, image/jpeg"><br><div class="body_gallery_image' + e.id + '"></div></td>' + 
                                                //'<td><img width="50px"; height="50px"; src="{{URL::to('+e.image+')}}" alt="" data-action="zoom"></td>' +
                                                '<td class="text-center" style="width:20px;"><a id="delete_item' +e.id + '" class="btn btn-xs btn-danger delete_item" style="margin-right:100px;margin-top:5px;" ><i class="fa fa-remove"></i> remove</a></td>' +
                                                '<input type="hidden" name="item_id[]" readonly value="' +e.id + '">' +
                                                '<td><input type="hidden" id="checkImage" value="' + e.image + '" readonly></td>' +
                                                '<td><input type="hidden" name="item_category[]" id="item_cat" value="' + e.category_description + '"></td>' +
                                                '<td><input type="hidden" name="category_id[]" id="catid" value="' + e.category_id + '"></td>' +
                                                '</tr>';
                                           
                                        //}
                         
                                    //$(new_row).insertAfter($('table tr.dynamicRows:last'));
                                    $("table tbody").append(new_row);
                                    $('.select2').select2({placeholder_text_single : "-- Select --"})

                                    $(document).on('click', '#delete_item' + e.id, function () {
                                        var parentTR = $(this).parents('tr');  
                                        $(parentTR).remove();
                                        $(".nr"+e.id).remove();
                                        $(".serial_qty"+e.id).val('');
                                        $("#dcqty"+e.id).remove();
                                        $("#dcqty"+e.id).val('');
                                        $(".delete_serial_row").remove();
                                        $('#rowid').load('#rowid');
                                       
                                    });
                                    
                                    // $(function() {
                                    // // single images preview in browser
                                    // var imagesPreview = function(input, placeToInsertImagePreview) {

                                    //     if (input.files) {
                                    //         var filesAmount = input.files.length;

                                    //         for (i = 0; i < filesAmount; i++) {
                                    //             var reader = new FileReader();

                                    //             reader.onload = function(event) {
                                    //                 $($.parseHTML('<img height="120px" id="body_image_id' + e.id + '" class="body_image_class" width="180px;" hspace="10"  data-action="zoom">')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
                                    //             }

                                    //             reader.readAsDataURL(input.files[i]);
                                    //         }
                                    //     }

                                    // };
                                    //     $('#body_image_body' + e.id).on('change', function() {
                                    //         var imgs = $('div.body_gallery_image' + e.id).find('img').attr('src');
                                      
                                    //             imagesPreview(this, 'div.body_gallery_image' + e.id);
                                                                        
                                    //         //$("#removeImage").toggle(); 
                                    //     });
                                    // });
                                    //remove image from preview
                                    // $("#removeImage").click(function(e) {
                                    //     e.preventDefault(); // prevent default action of link
                                    //     $('.body_image_class').attr('src', ""); //clear image src
                                    //     $('#body_image_body' + e.id).val(""); // clear image input value
                                    //     $('.body_image_class').remove();
                                    //     $("#removeImage").toggle(); // hide remove link.
                                    // });
                                    $(document).on("keyup","#quantity, #amount, #value", function (e) {
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
                                    $(document).ready(function() {
                                        // $('#item_type' + e.id).change(function() {
                                        // var parentTR = $(this).parents('tr');   
                                        // var item_type = $('#item_type' + e.id).val().toLowerCase().replace(/\s/g, '');
                                        //     if(item_type == "serial"){
                                        //         $(parentTR).find(".serial_no").prop('readonly', false);
                                        //         $(parentTR).find(".serial_no").attr("required", true);
                                        //     }
                                        //     if(item_type != "serial"){
                                        //         $(parentTR).find(".serial_no").prop('readonly', true);
                                        //         $(parentTR).find(".serial_no").attr("required", false);
                                        //         $(parentTR).find(".serial_no").val("");
                                        //     }
                                        // });
                                        // add serial per line
                                        // if(e.category_description == "SUPPLIES"){
                                        //     $("#add_quantity"+ e.id).prop('readonly', false);
                                        //     $("#add_quantity"+ e.id).val("");   
                                        // }

                                        // adding serial fields per quantity
                                        $("#add_quantity"+ e.id).on("input", function(){
                                            // Not checking for Invalid input
                                                //var item_type_serial = $('#item_type' + e.id).val().toLowerCase().replace(/\s/g, '');
                                                if (this.value != '') {
                                                    var val = parseInt(this.value, 10) - 1;
                                                    var parentTR = $(this).parents('tr');  
                                                    for (var i = 0; i < val; i++) {
                                                        var serial_row = '<tr class="nr'+e.id+'" id="rowid' + e.id + '" rows>' +
                                                    
                                                            '<td><input class="form-control id="dcqty'+e.id+'" text-center" type="text" name="digits_code_on_qty[]" readonly value="' + e.digits_code + '" style="display:none"></td>' +
                                                            '<td></td>' +
                                                            '<td></td>' +
                                                            '<td></td>' + 
                                                       
                                                            '<td><input class="form-control serial_qty'+e.id+'" placeholder="Serial No (Optional)" id="serial_qty" type="text" name="serial_no_on_qty[]" value="" data-index="2"></td>' +                                            
                                                            '<td class="text-center"><a id="' +e.id + '" class="btn btn-xs btn-danger delete_serial_row'+e.id+'" style="margin-right:150px;margin-top:5px"><i class="fa fa-remove" ></i></a></td>' +
                                                            '<td></td>' +
                                                            '<td></td>' +
                                                            '<td></td>' +
                                                            '</tr>';
                                                        $(serial_row).insertAfter(parentTR);
                                                    }
                                                }
                                                // if(item_type_serial == "serial"){  
                                                //     $('#item_type' + e.id).change(function() {
                                                //     var parentTR = $(this).parents('tr');   
                                                //     var item_type = $('#item_type' + e.id).val().toLowerCase().replace(/\s/g, '');
                                                //         if(item_type == "general"){
                                                //             $(".nr"+e.id).remove();
                                                //             $(".serial_qty"+e.id).val('');
                                                //             $("#dcqty"+e.id).remove();
                                                //             $("#dcqty"+e.id).val('');
                                                //             $(".delete_serial_row"+e.id).remove();
                                                //         }
                                                        
                                                //     }); 
                                                    
                                                // }
                                            });
                                        

                                        // Delete per row Serial Fields
                                        $(document).on('click', '.delete_serial_row'+e.id, function () {
                                            var parentTR = $(this).parents('tr');  
                                            var bal = $('#add_quantity' + e.id).val();
                                            var res = bal - 1;
                                            $("#add_quantity" + e.id).val(res);
                                            $(parentTR).remove();
                                        });
                                        // Add per row Serial Fields Value using keypress
                                        $('#InventoryForm').on('keydown', 'input',function (event) {
                                            var keyCode = event.keyCode || event.which;
                                            if (event.which == 13) {
                                                event.preventDefault();
                                                var $this = $(event.target);
                                                var index = parseFloat($this.attr('data-index'));
                                                $('[data-index="' + (index + 1) + '"]').focus();
                                            }
                                        });                                       

                                    });
                                    //blank++;
                                    $("#total").val(calculateTotalValue2());

                                    $(this).val('');
                                    $('#val_item').html('');

                                    return false;
                                
                                }else{

                                    // if(e.item_type == "SERIAL"){
                                    //     swal({
                                    //         type: 'error',
                                    //         title: 'Only 1 quantity is allowed in serialized items!',
                                    //         icon: 'error',
                                    //         width: 450,
                                    //         height: 200
                                    //     });
                                    //     $(this).val('');
                                    //     $('#val_item').html('');
                                    //     return false;

                                    // }else{

                                        $('#add_quantity' + e.id).val(function (i, oldval) {
                                            return ++oldval;
                                        });

                                        var temp_qty = $('#add_quantity'+ e.id).attr("data-id");

                                        var q = parseInt($('#add_quantity' +e.id).val());
                                        var r = parseInt($("#quantity" + e.id).val());

                                        /*$('#quantity' + e.id).val(function (i, amount) {
                                            if (q != 0) {
                                                var itemPrice = (q * r);
                                                return itemPrice;
                                            } else {
                                                return 0;
                                            }
                                        });*/

                                       //$('#'+temp_qty).val(q);

                                        var price = calculatePrice(q, r); 

                                        if(price == 0){
                                            price = q * 1;
                                       // }
                                        

                                        $("#total_quantity" + e.id).val(price);


                                        //var subTotalQuantity = calculateTotalQuantity();
                                        //$("#totalQuantity").val(subTotalQuantity);


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
                       
                        //$('#company_name').val(result[0].company_name);
                        $('#position').val(result[0].position_description);
                        $('#department').val(result[0].department_name);
                        
                        if(result[0].department_name.includes("STORE")){


                            $('#div_store_branch').show();
                            
                            $('#store_branch').attr('required', 'required');

                        }else{

                            $('#div_store_branch').hide();
                            $('#store_branch').removeAttr('required');

                        }

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


        $('#company_name').change(function() {
    
                var company_name =  this.value;
                
                //var id_data = $(this).attr("data-id");
                // $('.account'+id_data).prop("disabled", false);

                $.ajax
                ({ 
                    url: "{{ URL::to('/companies')}}",
                    type: "POST",
                    data: {
                        'company_name': company_name,
                        _token: '{!! csrf_token() !!}'
                        },
                        
                    
                        
                    success: function(result)
                    {   
                        //alert(result.length);
                    
                        var i;
                        var showData = [];

                        showData[0] = "<option value=''>-- Select Employee Name --</option>";
                        
                        for (i = 0; i < result.length; ++i) {
                            var j = i + 1;
                            showData[j] = "<option value='"+result[i].bill_to+"'>"+result[i].bill_to+"</option>";
                        }
                            
                        $('#employee_name').attr('disabled', false);
                        
                        jQuery('#employee_name').html(showData);        
                        
   

                    }
                });

        });

        
        $(document).on('keyup', '.quantity_item', function(ev) {

            //var id = $(this).attr("data-id");
            //var rate = parseFloat($(this).val());
            //var qty = $("#unit_cost" + id).val();

            //var price = calculatePrice(rate, qty).toFixed(2); 

           // $("#total_unit_cost" + id).val(price);
            $("#quantity_total").val(calculateTotalQuantity());
           // $("#cost_total").val(calculateTotalValue());
           // $("#total").val(calculateTotalValue2());
        });

        $(document).on('keyup', '.add_quantity', function(ev) {

       

                var id = $(this).attr("data-id");
                var rate = parseInt($(this).val());

                var qty = parseInt($("#quantity" + id).val());

              

                var price = calculatePrice(qty, rate); // this is for total Value in row


                if(price == 0){
                    price = rate * 1;
                }

                $("#total_quantity" + id).val(price);


        });

        function calculatePrice(qty, rate) {
            if (qty != 0) {
            var price = (qty + rate);
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

    </script>
@endpush