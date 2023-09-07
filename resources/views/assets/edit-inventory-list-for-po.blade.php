@extends('crudbooster::admin_template')
@push('head')
<style type="text/css">   
    
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
        Assets Inventory For PO
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
                    <label class="control-label"> Reference No</label>
                    <input class="form-control" type="text" value="{{$Header->inv_reference_number}}" readonly>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label">Location</label>
                    <input class="form-control" type="text"  value="{{$Header->warehouse_location}}" readonly>
                </div>
            </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label"><span style="color:red">*</span> PO NO</label>
                        <input class="form-control finput" type="text"  placeholder="PO NO" name="po_no" id="po_no">
                    </div>
                </div>
            </div>
        </div>
            
        <hr>

        <!-- Body Area -->
      
        <div class="box-header text-center">
        <!-- <a style="float:left" class='btn btn-success btn-xs' href='{{CRUDBooster::mainpath("generate-barcode/".$Header->header_id)."?return_url=".urlencode(Request::fullUrl())}}'><i class='fa fa-barcode'></i> Print all Barcode</a> -->
            <h3 class="box-title"><b>{{ trans('message.form-label.asset_items') }}</b></h3>
        </div>

        <div class="box-body">
            <div class="table-responsive">           
                <table id="asset-items">
                    <thead>
                        <tr>
                            <th width="10%" class="text-center">Asset Code</th>
                            <th width="10%" class="text-center">Digits Code</th>   
                            <th width="30%" class="text-center">Item Condition</th>      
                            <th width="5%" class="text-center">Location</th>                                         
                            <th width="5%" class="text-center">Quantity</th>                   
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($Body as $res)
                            <tr>
                            <td style="display:none">
                                <input class="form-control" type="text"name="body_id[]" value="{{$res->for_approval_body_id}}">
                                <input class="form-control text-center" type="text" id="dc" name="digits_code[]" value="{{$res->digits_code}}">
                            </td>
                            <td class="text-center">{{$res->asset_code}}</td>
                            <td class="text-center">{{$res->digits_code}}</td>
                            <td>{{$res->item_description}}</td>   
                            <td class="qty" style="text-align:center">{{$res->warehouse_location}}</td>  
                            <td class="qty" style="text-align:center">{{$res->quantity}}</td>                                                                                                       
                            </tr>
                        @endforeach
                    </tbody>
                </table> 
            </div>
        </div>
        <hr>
        @if(in_array(CRUDBooster::myPrivilegeId(),[1,6]))
            @if($Header->header_approval_status == 47)
            <button class="btn btn-success pull-right" type="submit" id="btnSubmit" style="margin-right:12px"> <i class="fa fa-check-circle"></i> Submit</button>
            {{-- <button class="btn btn-success pull-right" value="approvercancel" type="submit" id="btnSubmit" style="margin-right:12px"><i class="fa fa-check-circle" ></i> Submit</button> --}}
            @endif 
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
   
    /**Send Request*/
    $('#btnSubmit').on('click', function (event) {
    event.preventDefault();
    var fired_button = $(this).val();
    var id = $('#header_id').val();
    var remarks = $('#remarks').val();
    var saveRemarks = $('#remarkscancel').val(remarks);
        if($('#po_no').val() === ""){
            swal({
                type: 'error',
                title: 'Po No required!',
                icon: 'error',
                customClass: 'swal-wide',
                confirmButtonColor: "#367fa9"
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
                    confirmButtonText: "Yes, save it!",
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


                    formData.append('form_data', $('#ForApprovalForm').serialize());
                    formData.append('approvalMethod', fired_button);
                    formData.append('remarks', remarks);  
                    formData.append('id', id);
                    $.ajax({
                        url: "{{ route('assets.get.forPoProcess') }}",
                        dataType: "json",
                        type: "POST",
                        data: formData,
                        processData : false,
                        contentType : false,

                        success: function (data) {
                            if (data.status == "success") {
                                swal({
                                    type: data.status,
                                    title: data.message,
                                });
                                setTimeout(function(){
                                    //window.location.replace(document.referrer);
                                    window.location.replace(data.redirect_url);
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
        "<td colspan='4' style='text-align:center'>"+
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