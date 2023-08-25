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
        z-index: 1000;
    cursor: pointer;
    cursor: -webkit-zoom-in;
    cursor: -moz-zoom-in;
    }
    .header_images,
    .header_images-wrap {
        z-index: 1000;
    position: relative;
    z-index: 666;
    -webkit-transition: all 300ms;
        -o-transition: all 300ms;
            transition: all 300ms;
    }
    img.header_images {
        z-index: 1000;
    cursor: pointer;
    cursor: -webkit-zoom-out;
    cursor: -moz-zoom-out;
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
        z-index: 1000;
    filter: "alpha(opacity=100)";
    opacity: 1;
    }
    .zoom-overlay-open,
    .zoom-overlay-transitioning {
    cursor: default;
    z-index: 1000;
    position: relative;
    }
    #friendsoptionstable {
    table-layout: fixed;
    word-wrap: break-word;
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

        #asset-items th, td {
            border: 1px solid rgba(000, 0, 0, .5);
            padding: 8px;
        }
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
            background-color: #00a65a;
            /* border: px solid #367fa9; */
        }
</style>
@endpush
@section('content')
<!-- link -->
@if(g('return_url'))
	<p class="noprint"><a title='Return' href='{{g("return_url")}}'><i class='fa fa-chevron-circle-left '></i> &nbsp; {{trans("crudbooster.form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}}</a></p>       
@else
	<p class="noprint"><a title='Main Module' href='{{CRUDBooster::mainpath()}}'><i class='fa fa-chevron-circle-left '></i> &nbsp; {{trans("crudbooster.form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}}</a></p>       
@endif
  <div class='panel panel-default'>
    <div class='panel-heading'>  
        Assets Inventory Form
    </div>
    <form id="ForReceivingForm" name="ForReceivingForm" enctype="multipart/form-data">
        <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
        <input type="hidden" value="{{$Header->header_id}}" name="header_id" id="header_id">
          
    <div class='panel-body'>    
    <section id="loading">
        <div id="loading-content"></div>
    </section>
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"> Reference No</label>
                            <input class="form-control" type="text" value="{{$Header->inv_reference_number}}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"> PO NO</label>
                            <input class="form-control" type="text" value="{{$Header->po_no}}" placeholder="PO NO" name="po_no" id="po_no" readonly>
                        </div>
                    </div>
                 </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"><span style="color:red">*</span> Location</label>
                            <select  id="location" name="location" class="form-select select2" id="location" style="width:100%;">
                            @foreach($warehouse_location as $res)
                                <option value="{{ $res->id }}">{{ $res->location }}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"><span style="color:red">*</span>  Invoice Date</label>
                            <input type="text" class="form-control date finput" placeholder="Select Date" name="invoice_date" id="invoice_date">
                        
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                      <div class="col-md-6">
                          <div class="form-group">
                              <label class="control-label"><span style="color:red">*</span>  Invoice No.</label>
                              <input type="text" class="form-control finput" style="" placeholder="Invoice NO" name="invoice_no" id="invoice_no">
                          </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"><span style="color:red">*</span> RR Date</label>
                            <input class="form-control date finput" type="text" placeholder="Select Date" name="rr_date" id="rr_date">
                        </div>
                    </div>
                  </div>
            </div>

            <div class="row">
              <div class="col-md-12">
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
            </div>

           
            <br>
        <hr>

        <!-- Body Area -->
      
        <div class="box-header text-center">
        <!-- <a style="float:left" class='btn btn-success btn-xs' href='{{CRUDBooster::mainpath("generate-barcode/".$Header->header_id)."?return_url=".urlencode(Request::fullUrl())}}'><i class='fa fa-barcode'></i> Print all Barcode</a> -->
            <h3 class="box-title"><b>{{ trans('message.form-label.asset_items') }}</b></h3>
        </div>

        <div class="box-body">
            <div class="table-responsive">           
                <table id="asset-items" style="width: 130%">
                    <thead>
                        <tr>
                            <th width="5%" class="text-center">Asset Code</th>
                            <th width="5%" class="text-center">Digits Code</th>   
                            <th width="12%" class="text-center">Item Condition</th>    
                            <th width="2%" class="text-center">Quantity</th> 
                            <th width="5%" class="text-center">Value</th>                                            
                            <th width="7%" class="text-center"> Serial No <span style="font-style: italic; font-size:11px; color:red"> <br>(Put N/A if not Applicable)</span></th> 
                            <th width="5%" class="text-center"> Warranty Month Expiration <span style="font-style: italic; font-size:11px; color:red"> <br>(Note: 1 is equivalent of 1 month)</span></th>                                                     
                            <th width="5%" class="text-center">UPC Code</th>     
                            <th width="6%" class="text-center" >Brand</th>
                            <th width="7%" class="text-center" >Specs <span style="font-style: italic; font-size:11px; color:red"><br>(Ex: ADM Ryzen 5 3rd Gen/8 GB DDR4 RAM 512 GB SSD)</span></th>                                      
                            <th width="10%" class="text-center">For Re Order Items(ARF Number)<span style="font-style: italic; font-size:12px; color:red"> <br>(Please check if item is assign to ARF)</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php   $tableRow = 1; ?>
                        @foreach($Body as $res)
                        <?php   $tableRow++; ?>
                            <tr>
                            <td style="display:none">
                                <input class="form-control" type="text"name="body_id[]" value="{{$res->for_approval_body_id}}">
                                <input class="form-control text-center" type="text" id="dc" name="digits_code[]" value="{{$res->digits_code}}">
                            </td>
                            <td class="text-center">{{$res->asset_code}}</td>
                            <td class="text-center">{{$res->digits_code}}</td>
                            <td class="text-center">{{$res->item_description}}</td>   
                            <td class="qty" style="text-align:center">{{$res->quantity}}</td> 
                            <td>
                                <input class="form-control text-center finput" name="value[]" id="value" type="text" placeholder="{{$res->item_cost}}">
                            </td> 
                            <td>
                                <input class="form-control text-center finput" name="serial_no[]" id="serial_no" type="text">
                            </td>  
                            <td>
                                <input class="form-control text-center finput" name="warranty_coverage[]" id="warranty_coverage" type="text" min="1" max="9999999999" step="1" onkeypress="return event.charCode <= 57" value="0">
                            </td>    
                            <td>
                                <input class="form-control text-center finput" name="upc_code[]" id="upc_code" type="text">
                            </td>
                            <td>
                                <input class="form-control text-center finput" name="brand[]" id="brand" type="text">
                            </td>     
                            <td>
                                <input class="form-control text-center finput" name="specs[]" id="warranty_coverage" type="text">
                            </td>        
                            <td>
                                <select selected data-placeholder="Select ARF" class="form-control arf_tag" name="arf_tag[]" data-id="{{$tableRow}}" id="arf_tag{{$tableRow}}" required style="width:100%">
                                    <option value=""></option>
                                           @foreach($reserved_assets as $reserve)
                                               <option value="{{$reserve->served_id}}">{{$reserve->reference_number}} | {{$reserve->digits_code}}</option> 
                                           @endforeach
                                </select>
                                </td>                                                                                                
                            </tr>
                        @endforeach
                    </tbody>
                </table> 
            </div>
        </div>
        <hr>
        <button class="btn btn-success pull-right" type="submit" id="btnSubmit" style="margin-right:12px"> <i class="fa fa-check-circle"></i> Submit</button>
          
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
        $('#location').select2({})
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

    var searchcount = <?php echo json_encode($tableRow); ?>;
    let countrow = 1;
    $(function(){
        for (let i = 0; i < searchcount; i++) {
                countrow++;
                $('#arf_tag'+countrow).select2({})
        }
    });

    $(document).ready(function () {
        var $selects = $('.arf_tag');
        $selects.select2();
        $('.arf_tag').change(function () {
            $('option:hidden', $selects).each(function () {
                var self = this,
                    toShow = true;
                $selects.not($(this).parent()).each(function () {
                    if (self.value == this.value) toShow = false;
                })
                if (toShow) {
                    $(this).removeAttr('disabled');
                    $(this).parent().select2();
                }
            });
            if (this.value != "") {
                //$selects.not(this).children('option[value=' + this.value + ']').attr('disabled', 'disabled');
                $selects.not(this).children('option[value=' + this.value + ']').remove();
                $selects.select2();
            }
   
        });
    })
    
   

    $(".date").datetimepicker({
        viewMode: "days",
        format: "YYYY-MM-DD",
        dayViewHeaderFormat: "MMMM YYYY",
    });
   
    $('#btnSubmit').on('click', function (event) {
            event.preventDefault();
            var fired_button = $(this).val();
            var id = $('#header_id').val();
            var remarks = $('#remarks').val();
            if($('#location').val() === ""){
                swal({
                    type: 'error',
                    title: 'Please select location!',
                    icon: 'error',
                    customClass: 'swal-wide',
                    confirmButtonColor: "#367fa9"
                });
                event.preventDefault();
            }else if($('#invoice_date').val() === ""){
                swal({
                    type: 'error',
                    title: 'Invoice Date required!',
                    icon: 'error',
                    customClass: 'swal-wide',
                    confirmButtonColor: "#367fa9"
                });
                event.preventDefault();
            }else if($('#invoice_no').val() === ""){
                swal({
                    type: 'error',
                    title: 'Invoice No required!',
                    icon: 'error',
                    customClass: 'swal-wide',
                    confirmButtonColor: "#367fa9"
                });
                event.preventDefault();
            }else if($('#rr_date').val() === ""){
                swal({
                    type: 'error',
                    title: 'RR Date required!',
                    icon: 'error',
                    customClass: 'swal-wide',
                    confirmButtonColor: "#367fa9"
                });
                event.preventDefault();
            }else if($('#si_dr').val() === ""){
                swal({
                    type: 'error',
                    title: 'Upload SR/DR required!',
                    icon: 'error',
                    customClass: 'swal-wide',
                    confirmButtonColor: "#367fa9"
                });
                event.preventDefault();
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
                    
                        //header image validation
                        for (var i = 0; i < $("#si_dr").get(0).files.length; ++i) {
                            var file1=$("#si_dr").get(0).files[i].name;
                            if(file1){                        
                                var file_size=$("#si_dr").get(0).files[i].size;
                                if(file_size<2097152){
                                    var ext = file1.split('.').pop().toLowerCase();                            
                                    if($.inArray(ext,['jpg','jpeg','gif','png'])===-1){
                                        swal({
                                            type: 'error',
                                            title: 'Invalid Image Extension for SI/DR!',
                                            icon: 'error',
                                            customClass: 'swal-wide',
                                            confirmButtonColor: "#367fa9"
                                        });
                                        event.preventDefault();
                                        return false;
                                    }

                                }else{
                                    alert("Screenshot size is too large.");
                                    return false;
                                }                        
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
                                        confirmButtonColor: "#367fa9",
                                    });
                                    event.preventDefault();
                                    return false;
                            }
                        
                        }

                        //not allowed duplicate
                        var finalDuplicateData = checkRowForNa;
                        var dupArrData = finalDuplicateData.sort(); 

                        if(dupArrData.length !== 0){
                            if($('.serial_no').val() != ""){
                                for (var i = 0; i < dupArrData.length - 1; i++) {
                                if (dupArrData[i + 1] == dupArrData[i]) {
                                    swal({
                                            type: 'error',
                                            title: 'Not allowed duplicate Serial No. and Digits Code!/Put N/A(not NA, na)',
                                            icon: 'error',
                                            confirmButtonColor: "#367fa9"
                                        }); 
                                        event.preventDefault();
                                        return false;
                                    }
                                }
                            }
                        }
                        

                        //each value validation
                        var v = $("input[name^='serial_no']").length;
                        var value = $("input[name^='serial_no']");
                        for(i=0;i<v;i++){
                            if(value.eq(i).val() == 0){
                                swal({  
                                        type: 'error',
                                        title: 'Put N/A in Serial No if not available/Put N/A(not NA, na)',
                                        icon: 'error',
                                        confirmButtonColor: "#367fa9"
                                    });
                                    event.preventDefault();
                                    return false;
                            }
                    
                        }
        
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
                                        confirmButtonColor: "#367fa9"
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
                                        confirmButtonColor: "#367fa9"
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
                                        confirmButtonColor: "#367fa9"
                                    });
                                    event.preventDefault();
                                    return false;
                            }
                    
                        }

                    
                        //check existing
                        $.each(checkRowFinal, function(index, item) {
                            if($.inArray(item, data.items) != -1){
                                swal({
                                        type: 'error',
                                        title: 'Digits Code and Serial Already Exist! (' + item + ')',
                                        icon: 'error',
                                        confirmButtonColor: "#367fa9",
                                    }); 
                                    event.preventDefault();
                                    return false;
                            }else{
                                //$('.arf_tag').attr('disabled',false);
                                swal({
                                    title: "Are you sure?",
                                    type: "warning",
                                    showCancelButton: true,
                                    confirmButtonColor: "#41B314",
                                    cancelButtonColor: "#F9354C",
                                    confirmButtonText: "Yes, receive it!",
                                    width: 450,
                                    height: 200
                                    }, function () {
                                    //showLoading();   
                                    $.ajaxSetup({
                                        headers: {
                                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                            }
                                    });
                                    var formData = new FormData();
                                    const totalImages = $("#si_dr")[0].files.length;
                                    let images = $("#si_dr")[0];
                                    for (let i = 0; i < totalImages; i++) {
                                        formData.append('si_dr[]', images.files[i]);
                                    }

                                    formData.append('form_data', $('#ForReceivingForm').serialize());
                                    formData.append('remarks', remarks);
                                    formData.append('id', id);
                                    $.ajax({
                                        url: "{{ route('assets.get.forReceivingProcess') }}",
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
                                                //window.location.replace(data.redirect_url);
                                                setTimeout(function(){
                                                    window.location.replace(document.referrer);
                                                }, 1000); 
                                                } else if (data.status == "error") {
                                                swal({
                                                    type: data.status,
                                                    title: data.message,
                                                });
                                            }
                                        }
                                    })
                                });
                            }
                                        
                        });
                    }    
                });
            }
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

    function RefreshPage(){
        setTimeout(function(){
            location.reload();  
        }, 2000); 
    }

    $('.serial_no').keyup(function() {
			this.value = this.value.toLocaleUpperCase();
	});

    var tds = document.getElementById("asset-items").getElementsByTagName("td");
    var qty = 0;
    for (var i = 0; i < tds.length; i++) {
        if(tds[i].className == "qty") {
            qty += isNaN(tds[i].innerHTML) ? 0 : parseFloat(tds[i].innerHTML);
        }
    }
    document.getElementById("asset-items").innerHTML +=
    "<tr>"+
        "<td colspan='3' style='text-align:center'>"+
                "<strong>TOTAL</strong>"+
            "</td>"+
            
            "<td style='text-align:center'>"+
                "<strong>" +
                    qty +
                "</strong>"+
            "</td>"+
    "</tr>";

    </script>
@endpush