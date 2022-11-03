@extends('crudbooster::admin_template')
@push('head')
<style type="text/css">   
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
    <form id="ForApprovalForm" name="ForApprovalForm" enctype="multipart/form-data">
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
                            <label class="control-label"><span style="color:red">*</span> PO NO</label>
                            <input class="form-control" type="text" value="{{$Header->po_no}}" placeholder="PO NO" name="po_no" id="po_no" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label"><span style="color:red">*</span> Location</label>
                        <select required selected data-placeholder="-- Please Select Location --" id="location" name="location" class="form-select select2" style="width:100%;" disabled>
                            @foreach($warehouse_location as $res)
                            <option value="{{ $res->location }}"
                                {{ isset($Header->location) && $Header->location == $res->location ? 'selected' : '' }}>
                                {{ $res->location }} 
                            </option>>
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
            </div>

            <div class="row">
              <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"><span style="color:red">*</span> RR Date</label>
                            <input class="form-control date" type="text" placeholder="Select Date" name="rr_date" id="rr_date">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"><span style="color:red">*</span> Upload SI/DR</label>
                            <input type="file" class="form-control" style="" name="si_dr[]" id="si_dr" multiple accept="image/png, image/gif, image/jpeg">
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
                <table id='table_dashboard' class="table table-hover table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Digits Code</th>   
                            <th>Item Condition</th>    
                            <th>Value</th>                                            
                            <th>Quantity</th> 
                            <th>Serial No</th>   
                            <th>Warranty Coverage Year</th>                                            
                            
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($Body as $res)
                            <tr>
                            <td style="display:none">
                                <input class="form-control" type="text"name="body_id[]" value="{{$res->for_approval_body_id}}">
                                <input class="form-control text-center" type="text" id="dc" name="digits_code[]" value="{{$res->digits_code}}">
                            </td>
                            <td>{{$res->digits_code}}</td>
                            <td>{{$res->item_description}}</td>   
                            <td>{{$res->value}}</td>
                            <td>{{$res->quantity}}</td> 
                            <th>
                                <input class="form-control serial_no"  type="text" placeholder="Serial No (Put N/A if not applicable)" name="serial_no[]" style="width:100%" data-index="1" value="{{ $res->serial_no ? $res->serial_no : "" }}">
                            </th>    
                            <td>{{$res->warranty_coverage}}</td>                                                                                                                  
                            </tr>
                        @endforeach
                    </tbody>
                </table> 
            </div>
        </div>
        <div class="row">
                <div class="col-md-12">
                <div class="col-md-6">
                <span style="color:red; font-style: italic">*Put remarks when Receiving or Cancelling</span>
                <div class="form-group">
                    <label class="control-label"> Remarks</label>
                    <input class="form-control" type="text" placeholder="Remarks" name="remarks" id="remarks">
                </div>
                </div>
        </div> 
        @if(CRUDBooster::myPrivilegeName() == "IT" OR CRUDBooster::myPrivilegeName() == "Admin" OR CRUDBooster::myPrivilegeName() == "Super Administrator")
            @if($Header->header_approval_status == 20)
            <button class="btn btn-danger pull-right" value="approvercancel" type="button" id="btnReject" style="margin-left: 5px; margin-right:30px"><i class="fa fa-thumbs-down" ></i> Cancel</button>
            <button class="btn btn-success pull-right" value="approved" type="button" id="btnApprove" style="margin-left: 5px;"><i class="fa fa-thumbs-up" ></i> Receive</button>
            @endif 
         @endif 
        
    </div>
   
    
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
    $('.select2').select2({placeholder_text_single : "-- Select --"})
    $(".date").datetimepicker({
        viewMode: "days",
        format: "YYYY-MM-DD",
        dayViewHeaderFormat: "MMMM YYYY",
    });
    /**Approved Request*/
    $('#btnApprove').on('click', function (event) {
    event.preventDefault();
    var fired_button = $(this).val();
    var id = $('#header_id').val();
    var remarks = $('#remarks').val();
    if($('#invoice_date').val() === ""){
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
    }else if($('#rr_date').val() === ""){
        swal({
            type: 'error',
            title: 'RR Date required!',
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
                                    customClass: 'swal-wide'
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
                                    icon: 'error'
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
                                    customClass: 'swal-wide'
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
                                icon: 'error'
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
                            confirmButtonText: "Yes, receive it!",
                            width: 450,
                            height: 200
                            }, function () {
                            showLoading();   
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

                            formData.append('form_data', $('#ForApprovalForm').serialize());
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

    /**Rejected Request*/
    $('#btnReject').on('click', function (event) {
    event.preventDefault();
    var fired_button = $(this).val();
    var id = $('#header_id').val();
    var remarks = $('#remarks').val();
    var saveRemarks = $('#remarkscancel').val(remarks);
        // swal({
        //     title: "Remarks",
        //     type: "input",
        //     confirmButtonText: 'Proceed',
        //     confirmButtonColor: "#41B314",
        //     showCancelButton: true,
        //     closeOnConfirm: false,
        //     animation: "slide-from-top",
        //     inputPlaceholder: "Remarks"
        //     },
        //     function(inputValue){
        //         var remarks = inputValue;
        //         if (inputValue === "") {
        //             swal.showInputError("Remarks required for this process!");
        //             return false
        //         }
        if(remarks === ""){
            swal({
                type: 'error',
                title: 'Remarks required for this process!',
                icon: 'error'
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
                confirmButtonText: "Yes, cancel it!",
                width: 450,
                height: 200
                }, function () {
                    //$("#cancelledForm").submit();  
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

                formData.append('form_data', $('#ForApprovalForm').serialize());
                formData.append('approvalMethod', fired_button);
                formData.append('remarks', remarks);  
                formData.append('id', id);
                $.ajax({
                    url: "{{ route('assets.get.rejectedProcess') }}",
                    dataType: "json",
                    type: "POST",
                    data: formData,
                    processData : false,
                    contentType : false,
                    // data: {
                    //     "approvalMethod": fired_button,
                    //     "id": id,
                    //     "remarks": remarks
                    // },
                    success: function (data) {
                        if (data.status == "success") {
                            swal({
                                type: data.status,
                                title: data.message,
                            });
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
                 
        //});
    });
    function RefreshPage(){
        setTimeout(function(){
            location.reload();  
        }, 2000); 
    }

    $('.serial_no').keyup(function() {
			this.value = this.value.toLocaleUpperCase();
	});

    </script>
@endpush