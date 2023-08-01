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
                            <input type="text" class="form-control date" placeholder="Select Date" value="{{$Header->invoice_date}}" name="invoice_date" id="invoice_date" readonly>
                        
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"><span style="color:red">*</span>  Invoice No.</label>
                            <input type="text" class="form-control" style="" placeholder="Invoice NO" value="{{$Header->invoice_no}}" name="invoice_no" id="invoice_no" readonly>
                        </div>
                    </div>
                    
                </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"><span style="color:red">*</span> RR Date</label>
                            <input class="form-control date" type="text" placeholder="Select Date" value="{{$Header->rr_date}}" name="rr_date" id="rr_date" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"><span style="color:red">*</span> Uploaded SI/DR</label>
                            {{-- <input type="file" class="form-control" style="" name="si_dr[]" id="si_dr" multiple accept="image/png, image/gif, image/jpeg"> --}}
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
                            <th>Asset Code</th>
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
                            <td>{{$res->asset_code}}</td>
                            <td>{{$res->digits_code}}</td>
                            <td>{{$res->item_description}}</td>   
                            <td>{{$res->value}}</td>
                            <td>{{$res->quantity}}</td> 
                            <td>{{$res->serial_no}}</td>   
                            <td>{{$res->body_location}}</td>                                                                                                                  
                            </tr>
                        @endforeach
                    </tbody>
                </table> 
            </div>
        </div>
    
        @if(CRUDBooster::myPrivilegeName() == "IT" OR CRUDBooster::myPrivilegeName() == "Admin" OR CRUDBooster::myPrivilegeName() == "Super Administrator")
            @if($Header->header_approval_status == 22)
            <button class="btn btn-success pull-right" value="approvercancel" type="button" id="btnClose" style="margin-left: 5px; margin-right:30px"><i class="fa fa-times-circle" ></i> Close</button>
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
   
    /**Rejected Request*/
    $('#btnClose').on('click', function (event) {
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
        // if(remarks === ""){
        //     swal({
        //         type: 'error',
        //         title: 'Remarks required for this process!',
        //         icon: 'error'
        //     }); 
        //     event.preventDefault();
        //     return false;
        // }else{
            swal({
                title: "Are you sure?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#41B314",
                cancelButtonColor: "#F9354C",
                confirmButtonText: "Yes, close it!",
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
                // const totalImages = $("#si_dr")[0].files.length;
                // let images = $("#si_dr")[0];
                // for (let i = 0; i < totalImages; i++) {
                //     formData.append('si_dr[]', images.files[i]);
                // }

                formData.append('form_data', $('#ForApprovalForm').serialize());
                formData.append('approvalMethod', fired_button);
                formData.append('remarks', remarks);  
                formData.append('id', id);
                $.ajax({
                    url: "{{ route('assets.get.closeProcess') }}",
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
        //}
                 
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