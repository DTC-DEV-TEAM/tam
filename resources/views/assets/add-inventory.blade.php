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

            option:after {
                content: " ";
                height: 5px;
                width: 5px;
                background: #c00;
                border-radius: 5px;
                display: inline-block;
            }
       
            /* .select2-results__option--highlighted { 
                background-color: #41B314! important; 
                border-radius: 10px;
            }

            .select2-results__option[aria-selected=true] { background-color: #41B314 !important; } */

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

                .finput {
                border:none;
                border-bottom: 1px solid rgba(18, 17, 17, 0.5);
                }

                input.finput:read-only {
                    background-color: #fff;
                }

                input.ginput:read-only {
                    background-color: #f5f5f5;
                }
                #asset-items th, td {
                    border: 1px solid rgba(000, 0, 0, .5);
                    padding: 8px;
                }

                /* ::-webkit-scrollbar-track
                {
                    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
                    border-radius: 10px;
                    background-color: #F5F5F5;
                }

                ::-webkit-scrollbar
                {
                    width: 12px;
                    background-color: #F5F5F5;
                }

                ::-webkit-scrollbar-thumb
                {
                    border-radius: 10px;
                    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.3);
                    background-color: #555;
                } */
                ::-webkit-scrollbar-track
                {
                    /* -webkit-box-shadow: inset 0 0 6px rgba(32, 83, 178, 0.3); */
                    background-color: #F5F5F5;
                }

                ::-webkit-scrollbar
                {
                    width: 10px;
                    background-color: #F5F5F5;
                }

                ::-webkit-scrollbar-thumb
                {
                    background-color: #3c8dbc;
                    /* border: px solid #367fa9; */
                }
                /* .select2-container--default .select2-selection--multiple .select2-selection__choice{color:black;}
                .select2-results .select2-disabled {
                    display:none;
                } */
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
    
    <form id="InventoryForm" enctype="multipart/form-data">
        <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
        <input type="hidden" value="1" name="request_type_id" id="request_type_id">

        <div class='panel-body'>
            <section id="loading">
                <div id="loading-content"></div>
            </section>

            {{-- <div class="row">
                <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"><span style="color:red">*</span> PO NO</label>
                            <input class="form-control finput" type="text"  placeholder="PO NO" name="po_no" id="po_no">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"><span style="color:red">*</span> Location</label>
                            <select  id="location" name="location" class="form-select select2" style="width:100%;">
                            @foreach($warehouse_location as $res)
                                <option value="{{ $res->id }}">{{ $res->location }}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                      <div class="col-md-6">
                          <div class="form-group">
                              <label class="control-label"><span style="color:red">*</span>  Invoice Date</label>
                              <input type="text" class="form-control date finput" placeholder="Select Date" name="invoice_date" id="invoice_date">
                          
                          </div>
                      </div>
                      <div class="col-md-6">
                          <div class="form-group">
                              <label class="control-label"><span style="color:red">*</span>  Invoice No.</label>
                              <input type="text" class="form-control finput" style="" placeholder="Invoice NO" name="invoice_no" id="invoice_no">
                          </div>
                      </div>
                      
                  </div>
            </div>
          
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"><span style="color:red">*</span> RR Date</label>
                            <input class="form-control date finput" type="text" placeholder="Select Date" name="rr_date" id="rr_date">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"><span style="color:red">*</span> Upload SI/DR</label>
                            <input type="file" class="form-control finput" style="" name="si_dr[]" id="si_dr" multiple accept="image/png, image/gif, image/jpeg">
                            <div class="gallery" style="margin-bottom:5px; margin-top:15px"></div>
                            <a class="btn btn-xs btn-danger" style="display:none; margin-left:10px" id="removeImageHeader" href="#"><i class="fa fa-remove"></i></a>
                            @foreach($header_images as $res_header_images)                                    
                                @if ($res_header_images->file_name)
                                <img style="margin-right:5px" width="120px"; height="90px"; src="{{URL::to('vendor/crudbooster/inventory_header').'/'.$res_header_images->file_name}}" alt="" data-action="zoom"> 
                                @else
                                
                                @endif                                         
                            @endforeach
                        </div>
                    </div>          
                </div>
            </div> --}}

            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"><span style="color:red">*</span> Location</label>
                            <select selected data-placeholder="- Select location -" id="location" name="location" class="form-select select2" style="width:100%;">
                            @foreach($warehouse_location as $res)
                                <option value=""></option>
                                <option value="{{ $res->id }}">{{ $res->location }}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"><span style="color:red">*</span> Please indicate Item Code or Item Description</label>
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
                        <h3 class="box-title"><b>Item Details</b></h3>
                    </div>
                    <div class="box-body no-padding">
                        <div class="table-responsive">
                            <div class="pic-container">
                                <div class="pic-row">
                                    <table id="asset-items">
                                        <tbody id="bodyTable">
                                            <tr class="tbl_header_color dynamicRows">
                                                <th width="7%" class="text-center">{{ trans('message.table.digits_code') }}</th>
                                                <th width="12%" class="text-center">{{ trans('message.table.item_description') }}</th>
                                                <th width="8%" class="text-center">Category</th>
                                                <th width="7%" class="text-center">{{ trans('message.table.asset_tag') }}</th>
                                                <th width="2%" class="text-center">{{ trans('message.table.quantity_text') }}</th>
                                                {{-- <th width="5%" class="text-center">Value</th>
                                                <th width="7%" class="text-center"> Serial No <span style="font-style: italic; font-size:11px; color:red"> <br>(Put N/A if not Applicable)</span></th> 
                                                <th width="5%" class="text-center"> Warranty Month Expiration <span style="font-style: italic; font-size:11px; color:red"> <br>(Note: 1 is equivalent of 1 month)</span></th>                                                     
                                                <th width="5%" class="text-center">UPC Code</th>     
                                                <th width="6%" class="text-center" >Brand</th>
                                                <th width="7%" class="text-center" >Specs <span style="font-style: italic; font-size:11px; color:red"><br>(Ex: ADM Ryzen 5 3rd Gen/8 GB DDR4 RAM 512 GB SSD)</span></th>     --}}
                                                <th width="3%" class="text-center">Action</th>
                                            </tr>
                                    
                                            <tr id="tr-table">
                                                <tr>
                                
                                                </tr>
                                            </tr>
                                        </tbody>

                                        <tfoot>

                                            <tr id="tr-table1" class="bottom">
                                                <td colspan="4" class="text-center">
                                                    <span ><strong>Total</strong></span>
                                                </td>
                                                <td align="left">
                                                    <input type='text' name="quantity_total" class="form-control text-center finput" id="quantity_total" readonly>
                                                </td> 
                                                <td colspan="8"></td>
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
            <button class="btn btn-success pull-right" type="submit" id="btnSubmit"> <i class="fa fa-check=circle" ></i>  Save</button>
        </div>

    </form>


</div>

@endsection

@push('bottom')

    <script type="text/javascript">
        $(function(){
            $('body').addClass("sidebar-collapse");
          
        });
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
        var tableRow = 1;
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

        });

        var tableRow = 1;
        var stack = [];
        var token = $("#token").val();
        var arf_array = [];
        $("#btnSubmit").attr("disabled", true);
        
        $(document).ready(function(){
            //selectRefresh();
            $(function(){
                $("#search").autocomplete({
                   
                    source: function (request, response) {
                        $("#btnSubmit").attr("disabled", false);
                    $.ajax({
                        url: "{{ route('search-assets') }}",
                        dataType: "json",
                        type: "POST",
                        data: {
                            "_token": token,
                            "search": request.term,
                            "location_id": $('#location').val()
                        },
                        success: function (data) {
                            //console.log(data.items);
                            if(data.items === null || data.items.length === 0){
                                swal({
                                type: 'error',
                                title: 'Item not found!',
                                icon: 'error',
                                confirmButtonColor: "#5cb85c",
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
                                        total_quantity:             item.total_quantity,
                                     
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
                      
                        tableRow++;
                        if (e.id) {   
                            if($('#location').val() === ""){
                                swal({
                                    type: 'info',
                                    title: 'Please select location!',
                                    icon: 'info',
                                });
                            }
                            
                           // if (!in_array(e.id, stack)) {
                                if (!stack.includes(e.id)) {            
                                    //stack.push(e.id);                                                                                
                                   
                                    var new_row = '<tr class="nr" id="rowid' + e.id + '" rows>' +
                                        '<input class="form-control text-center ginput" type="hidden" name="body_id[]" readonly value="' + e.id + '">' +
                                        '<td><input class="form-control text-center ginput" type="text" id="dc" name="digits_code[]" readonly value="' + e.digits_code + '"></td>' +
                                        '<td><input class="form-control text-center ginput" type="text" name="item_description[]" readonly value="' + e.value + '"></td>' +
                                        '<td><input class="form-control text-center ginput amount" placeholder="Value" type="text" readonly value="' + e.category_description + '"></td>' +
                                        '<td>' +
                                            '<select selected data-placeholder="- Select Sub Category -" class="form-control sub_category_id" name="sub_category_id[]" data-id="' + e.id  + '" id="sub_category_id' + tableRow  + '" required style="width:100%">' +
                                                // '<option value=""></option>' + 
                                                // '@foreach($sub_categories as $subData)' +
                                                //     '<option value="{{$subData->id}}">{{$subData->class_description}} | {{ $subData->category_code }}</option>' +
                                                // '@endforeach' +
                                            '</select>' +
                                        '</td>' +
                                        '<td><input class="form-control text-center ginput text-center add_quantity" placeholder="Quantity" type="text" value="1" readonly name="add_quantity[]" id="add_quantity' + e.id  + '" data-id="' + e.id  + '"  min="0" max="9999999999" step="1" onkeypress="return event.charCode >= 48 && event.charCode <= 57" oninput="validity.valid||(value=1);"></td>' +               
                                        // '<td><input class="form-control ginput text-center finput" placeholder="' + e.item_cost + '" type="text" name="value[]" id="value" required min="1" min="0" max="9999999999"></td>' +                                           
                                        // '<td><input class="form-control finput serial_no" type="text" placeholder="Serial No." name="serial_no[]" value="" data-index="1"></td>' + 
                                        // '<td><input class="form-control finput text-center" type="text" placeholder="(Month)" name="warranty_coverage[]" id="warranty_coverage" min="1" max="9999999999" step="1" onkeypress="return event.charCode <= 57" value="0"></td>' +                                                                                                                                       
                                        // '<td><input class="form-control upc_code finput" type="text" placeholder="UPC Code" name="upc_code[]" style="width:100%" data-index="1"></td>' + 
                                        // '<td><input class="form-control brand finput" type="text" placeholder="Brand" name="brand[]" style="width:100%" data-index="1"></td>' +
                                        // '<td><input class="form-control specs finput" type="text" placeholder="ADM Ryzen 5 3rd Gen/8 GB DDR4 RAM 512 GB SSD" name="specs[]" style="width:100%" data-index="1"></td>' +  
                                        '<td style="text-align:center"><a id="delete_item' +e.id + '" class="btn btn-sm btn-danger delete_item btn-lg"><i class="fa fa-trash"></i></a></td>' +
                                        '<input type="hidden" name="item_id[]" readonly value="' +e.id + '">' +
                                        '<input type="hidden" id="checkImage" value="' + e.image + '" readonly>' +
                                        '<input type="hidden" name="item_category[]" id="item_cat" value="' + e.category_description + '">' +
                                        '<input type="hidden" name="category_id[]" id="catid" value="' + e.category_id + '">' +
                                        '</tr>';
                                        //alert($reserved_assets);
                                        //}
                                    
                                    //$(new_row).insertAfter($('table tr.dynamicRows:last'));
        
                                    $("table tbody").append(new_row);
                                    $('.sub_category_id').select2({allowClear:true});  
                                    $(".text-muted").css({
                                        background: "#41B314",
                                        color: "white",
                                        padding: "2px 8px",
                                        borderRadius: "8px",
                                    });                          
                                    $(document).on('click', '#delete_item' + e.id, function () {
                                        var parentTR = $(this).parents('tr');  
                          
                                        // stack = jQuery.grep(stack, function(value) {
                                        //  return value != e.id;
                                        // });
                        
                                        $(parentTR).remove();
                                        $(".nr"+e.id).remove();
                                        $(".serial_qty"+e.id).val('');
                                        $("#dcqty"+e.id).remove();
                                        $("#dcqty"+e.id).val('');
                                        $(".delete_serial_row").remove();
                                        $('#rowid').load('#rowid');
                                        $("#quantity_total").val(calculateTotalQuantity());
                                      
                                    });
                                    $('#location').trigger('change');
                                    $(".date").datetimepicker({
                                            viewMode: "days",
                                            format: "YYYY-MM-DD",
                                            dayViewHeaderFormat: "MMMM YYYY",
                                    });

                                                           
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
                                     $("#quantity_total").val(calculateTotalQuantity());
                                   
                                 
                                    //blank++;
                                    

                                    $(this).val('');
                                    $('#val_item').html('');

                                    return false;
                                  
                                }else{

                                        $('#add_quantity' + e.id).val(function (i, oldval) {
                                            return ++oldval;
                                        });

                                        var temp_qty = $('#add_quantity'+ e.id).attr("data-id");
                                        var q = parseInt($('#add_quantity' +e.id).val());
                                        var r = parseInt($("#quantity" + e.id).val());

                                        var price = calculatePrice(q, r); 

                                        if(price == 0){
                                            price = q * 1;           
                                                $("#total_quantity" + e.id).val(price);                                    
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
       
           //Class
           $('#location').change(function(){
                var id =  this.value;
                if(id == 3){
                    $('#location').attr('disabled',true);
                }else{
                    $('#location').attr('disabled',false);
                }
                $.ajax({ 
                    type: 'POST',
                    url: "{{ route('sub-categories-code') }}",
                    data: {
                        "id": id
                    },
                    success: function(result) {
                        var i;
                        var showData = [];
                        showData[0] = "<option value=''>Choose Sub Class</option>";
                        for (i = 0; i < result.length; ++i) {
                            var j = i + 1;
                            showData[j] = "<option value='"+result[i].id+"'>"+result[i].class_description+" | "+result[i].category_code+"</option>";
                        }
                        $('#sub_class').attr('disabled', false);
                        jQuery('#sub_category_id'+tableRow).html(showData);   
                        //$('.sub_category_id').val('').trigger('change');       
                    }
                });
            });
        
  
        //VERSION 2 SUBMIT
        $('#btnSubmit').click(function(event) {
            event.preventDefault();
            var fired_button = $(this).val();
            var id = $('#header_id').val();
            var remarks = $('#remarks').val();
            var countRow = $('#asset-items tr').length - 4;

            if($('#location').val() === ""){
                swal({
                    type: 'error',
                    title: 'Location required!',
                    icon: 'error',
                        confirmButtonColor: "#5cb85c",
                });
                event.preventDefault();
                return false;
            }else if (countRow == 0) {
                swal({
                    type: 'error',
                    title: 'Please add an item!',
                    icon: 'error',
                    confirmButtonColor: "#5cb85c",
                }); 
                event.preventDefault(); // cancel default behavior
                return false;
            }else{
                $.ajax({
                    url: "{{ route('assets.check.row') }}",
                    dataType: "json",
                    type: "POST",
                    data: {},
                    success: function (data) {
                        var n = $("input[name^='digits_code']").length;
                        var dc_codes = $("input[name^='digits_code']");
                        var serial_no = $("input[name^='serial_no']");

                        var remove_na = [];
                        for(i=0;i<n;i++){
                            remove_na_value = serial_no.eq(i).val();
                            remove_na.push(remove_na_value);
                        }
                        var removeItem = 'N/A';
                        remove_na = jQuery.grep(remove_na, function(value) {
                        return value != removeItem;
                        });
                        var checker = remove_na ? remove_na.length : n;
                        //FOR NA
                        var cont_one = [];
                        for(i=0;i<checker;i++){
                            //dc_value =  dc_codes.eq(i).val().concat('-',serial_no.eq(i).val());
                            dc_value =  dc_codes.eq(i).val().concat('-',remove_na[i]);
                            cont_one.push(dc_value);
                        }
                        //FOR NOT NA
                        var for_not_na = [];
                        for(i=0;i<n;i++){
                            for_not_na_value =  dc_codes.eq(i).val().concat('-',serial_no.eq(i).val());
                            for_not_na.push(for_not_na_value);
                        }
                        var checkRowForNa = cont_one;
                        var checkRow = cont_one.length !== 0 ? cont_one : for_not_na;
                        var checkRowFinal = checkRow.filter(function(elem, index, self) {
                            return index === self.indexOf(elem);
                        });
                    
                        // //header image validation
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
                        //                     customClass: 'swal-wide',
                        //                     confirmButtonColor: "#367fa9"
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
                        
                        // //check category
                        // var cat = $("input[name^='item_category']").length;
                        // var item_category = $("input[name^='item_category']");
                        // for(i=0;i<cat;i++){
                        //     if($.inArray(item_category.eq(i).val(),['IT ASSETS','FIXED ASSETS','APPLIANCES','MAINTENANCE HARDWARE','OFFICE EQUIPMENT','FIXED ASSET']) === -1){
                        //         swal({
                        //                 type: 'error',
                        //                 title: 'Invalid Category. please check Category!',
                        //                 icon: 'error',
                        //                 confirmButtonColor: "#367fa9",
                        //             });
                        //             event.preventDefault();
                        //             return false;
                        //     }
                        
                        // }
                        //Sub Category
                        var subcategory = $(".sub_category_id").length;
                        var subcategory_value = $(".sub_category_id").find(":selected");;
                        for(i=0;i<subcategory;i++){
                            if(subcategory_value.eq(i).val() == 0 || subcategory_value.eq(i).val() == null){
                                swal({  
                                        type: 'error',
                                        title: 'Sub Category cannot be empty!',
                                        icon: 'error',
                                        confirmButtonColor: "#5cb85c",
                                    });
                                    event.preventDefault();
                                    return false;
                            } 
                        } 

                        //Value
                        var cost = $("input[name^='value']").length;
                        var valueCost = $("input[name^='value']");
                        for(i=0;i<cost;i++){
                            if(valueCost.eq(i).val() == ""){
                                swal({
                                        type: 'error',
                                        title: 'Value required!',
                                        icon: 'error',
                                        confirmButtonColor: "#5cb85c",
                                    });
                                    event.preventDefault();
                                    return false;
                            }
                        
                        }

                        // //not allowed duplicate
                        // var finalDuplicateData = checkRowForNa;
                        // var dupArrData = finalDuplicateData.sort(); 

                        // if(dupArrData.length !== 0){
                        //     if($('.serial_no').val() != ""){
                        //         for (var i = 0; i < dupArrData.length - 1; i++) {
                        //         if (dupArrData[i + 1] == dupArrData[i]) {
                        //             swal({
                        //                     type: 'error',
                        //                     title: 'Not allowed duplicate Serial No. and Digits Code!/Put N/A(not NA, na)',
                        //                     icon: 'error',
                        //                     confirmButtonColor: "#367fa9"
                        //                 }); 
                        //                 event.preventDefault();
                        //                 return false;
                        //             }
                        //         }
                        //     }
                        // }
                        

                        // //each value validation
                        // var v = $("input[name^='serial_no']").length;
                        // var value = $("input[name^='serial_no']");
                        // for(i=0;i<v;i++){
                        //     if(value.eq(i).val() == 0){
                        //         swal({  
                        //                 type: 'error',
                        //                 title: 'Put N/A in Serial No if not available/Put N/A(not NA, na)',
                        //                 icon: 'error',
                        //                 confirmButtonColor: "#367fa9"
                        //             });
                        //             event.preventDefault();
                        //             return false;
                        //     }
                    
                        // }
        
                        //upc code each value validation
                        var u = $("input[name^='upc_code']").length;
                        var upcValue = $("input[name^='upc_code']");
                        for(i=0;i<u;i++){
                            if(upcValue.eq(i).val() == 0){
                                swal({  
                                        type: 'error',
                                        title: 'UPC Code Required!',
                                        icon: 'error',
                                        customClass: 'swal-wide',
                                        confirmButtonColor: "#5cb85c"
                                    });
                                    event.preventDefault();
                                    return false;
                            }
                    
                        }

                        //upc code each value validation
                        var b = $("input[name^='brand']").length;
                        var brandValue = $("input[name^='brand']");
                        for(i=0;i<b;i++){
                            if(brandValue.eq(i).val() == 0){
                                swal({  
                                        type: 'error',
                                        title: 'Brand Required!',
                                        icon: 'error',
                                        customClass: 'swal-wide',
                                        confirmButtonColor: "#5cb85c"
                                    });
                                    event.preventDefault();
                                    return false;
                            }
                    
                        }

                        //upc code each value validation
                        var s = $("input[name^='specs']").length;
                        var specsValue = $("input[name^='specs']");
                        for(i=0;i<s;i++){
                            if(specsValue.eq(i).val() == 0){
                                swal({  
                                        type: 'error',
                                        title: 'Specs Required!',
                                        icon: 'error',
                                        customClass: 'swal-wide',
                                        confirmButtonColor: "#5cb85c"
                                    });
                                    event.preventDefault();
                                    return false;
                            }
                    
                        }

                    
                        //check existing
                        // $.each(checkRowFinal, function(index, item) {
                        //     if($.inArray(item, data.items) != -1){
                        //         swal({
                        //                 type: 'error',
                        //                 title: 'Digits Code and Serial Already Exist! (' + item + ')',
                        //                 icon: 'error',
                        //                 confirmButtonColor: "#367fa9",
                        //             }); 
                        //             event.preventDefault();
                        //             return false;
                        //     }else{
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
                                    $('#location').attr('disabled',false);
                                    //showLoading();   
                                    $.ajaxSetup({
                                        headers: {
                                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                            }
                                    });
                                    var formData = new FormData();
                                    // const totalImages = $("#si_dr")[0].files.length;
                                    // let images = $("#si_dr")[0];
                                    // for (let i = 0; i < totalImages; i++) {
                                    //     formData.append('si_dr[]', images.files[i]);
                                    // }

                                    formData.append('form_data', $('#InventoryForm').serialize());
                                    formData.append('remarks', remarks);
                                    formData.append('id', id);
                                    $.ajax({
                                        url: "{{ route('assets.get.approvedProcess') }}",
                                        dataType: "json",
                                        type: "POST",
                                        data: formData,
                                        processData : false,
                                        contentType : false,
                                        // data: {
                                        //     "form_data": $('#ForApprovalForm').serialize(),
                                        //     "id": id,
                                        //     "remarks": remarks
                                        // },
                                        success: function (data) {
                                            if (data.status == "success") {
                                                swal({
                                                    type: data.status,
                                                    title: data.message,
                                                });
                                                window.location.replace(data.redirect_url);
                                                // setTimeout(function(){
                                                //     window.location.replace(document.referrer);
                                                // }, 1000); 
                                                } else if (data.status == "error") {
                                                swal({
                                                    type: data.status,
                                                    title: data.message,
                                                    confirmButtonColor: "#367fa9",
                                                });
                                            }
                                        }
                                    })
                                });
                        //     }
                                        
                        // });
                    }    
                });
            }
                
            //}
        });

      
        $(document).on('keyup', '.add_quantity', function(ev) {
            $("#quantity_total").val(calculateTotalQuantity());
        });
              
        $(document).on('keyup', '.quantity_item', function(ev) {
            $("#quantity_total").val(calculateTotalQuantity());
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
            $('.add_quantity').each(function() {

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