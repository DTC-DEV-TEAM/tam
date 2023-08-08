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
                border: 1px solid rgba(39, 38, 38, 0.5);
                padding: 10px;
                margin-left: 10px;
                border-radius: 3px;
                opacity: 2;
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
                background-color: #fff;
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
        Services Item Source Form
    </div>

    <form action="{{ CRUDBooster::mainpath('add-save') }}" method="POST" id="AssetRequest" enctype="multipart/form-data">
        <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
        <input type="hidden" value="9" name="request_type_id" id="request_type_id">

        <div class='panel-body'>
            @include('item-sourcing.item-sourcing-view-header',['Header'=>$Header])
            <hr/>
            @include('item-sourcing.item-sourcing-view-body-mkt-srvs-subs',['categories'=>$categories])
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
        $(function(){
            $('body').addClass("sidebar-collapse");
        });
        function preventBack() {
            window.history.forward();
        }

        function validate(input){
        if(/^\s/.test(input.value))
            input.value = '';
        }
        
         window.onunload = function() {
            null;
        };
        setTimeout("preventBack()", 0);
        
        var tableRow = 1;
        const numWeeks = 2;
        const now = new Date();
        
        $(".date").datetimepicker({
            minDate:now.setDate(now.getDate() + numWeeks * 7),
            viewMode: "days",
            format: "YYYY-MM-DD",
            dayViewHeaderFormat: "MMMM YYYY",
        });
        $(".date").val('');

        $('#category_id').select2({});
        $('#sub_category_id').select2({});
        $('#class').select2({});
        $('#sub_class').select2({});
        $('#budget').select2({});

        $('#sub_category_id').attr('disabled', true);
        $('#class').attr('disabled', true);
        $('#sub_class').attr('disabled', true);

        $(document).ready(function() {
            //value validation
            $(document).on("keyup","#"+tableRow, function (e) {
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

            //Category
            $('#category_id').change(function(){
                var category =  this.value;
                var id_data = $(this).attr("data-id");
                $.ajax
                ({ 
                    type: 'POST',
                    url: "{{ route('item.source.sub.categories') }}",
                    data: {
                        "id": category
                    },
                    success: function(result) {
                        var i;
                        var showData = [];
                        showData[0] = "<option value=''>Choose Sub Category</option>";
                        for (i = 0; i < result.length; ++i) {
                            var j = i + 1;
                            showData[j] = "<option value='"+result[i].id+"'>"+result[i].sub_category_description+"</option>";
                        }
                        $('#sub_category_id').attr('disabled', false);
                        jQuery('#sub_category_id').html(showData); 
                            
                        $('#sub_category_id').val('').trigger('change');   
                        $('#class').val('').trigger('change');  
                        $('#sub_class').val('').trigger('change');  

                    }
                });

            });

            //Sub Category
            $('#sub_category_id').change(function(){
                var sub_category =  this.value;
                var id_data = $(this).attr("data-id");
                
                $.ajax
                ({ 
                    type: 'POST',
                    url: "{{ route('item.source.class.categories') }}",
                    data: {
                        "id": sub_category
                    },
                    success: function(result) {
                        var i;
                        var showData = [];
                        showData[0] = "<option value=''>Choose Class</option>";
                        for (i = 0; i < result.length; ++i) {
                            var j = i + 1;
                            showData[j] = "<option value='"+result[i].id+"'>"+result[i].class_description+"</option>";
                        }
                        $('#class').attr('disabled', false);
                        jQuery('#class').html(showData);        
                        $('#class').val('').trigger('change');  
                        $('#sub_class').val('').trigger('change');  
                    }
                });
            });

            //Class
            $('#class').change(function(){
                var classVal =  this.value;
                var id_data = $(this).attr("data-id");
                
                $.ajax
                ({ 
                    type: 'POST',
                    url: "{{ route('item.source.sub.class.categories') }}",
                    data: {
                        "id": classVal
                    },
                    success: function(result) {
                        var i;
                        var showData = [];
                        showData[0] = "<option value=''>Choose Sub Class</option>";
                        for (i = 0; i < result.length; ++i) {
                            var j = i + 1;
                            showData[j] = "<option value='"+result[i].id+"'>"+result[i].sub_class_description+"</option>";
                        }
                        $('#sub_class').attr('disabled', false);
                        jQuery('#sub_class').html(showData);   
                        $('#sub_class').val('').trigger('change');       
                    }
                });

            });
     
        });

     
        $("#btnSubmit").click(function(event) {
            event.preventDefault();
            var countRow = $('#asset-items tfoot tr').length;
            var reg = /^0/gi;
            ;
                if ($('#date_needed').val() === "") {
                    swal({
                        type: 'error',
                        title: 'Date needed required!',
                        icon: 'error',
                        confirmButtonColor: "#367fa9",
                    }); 
                    event.preventDefault(); // cancel default behavior
                }else if($('#category_id ').val() === ""){
                    swal({  
                        type: 'error',
                        title: 'Please choose Category!',
                        icon: 'error',
                        confirmButtonColor: "#367fa9",
                    });
                    event.preventDefault();
                    return false;
                }else if($('#sub_category_id').val() === ""){
                    swal({  
                        type: 'error',
                        title: 'Please choose Sub Category!',
                        icon: 'error',
                        confirmButtonColor: "#367fa9",
                    });
                    event.preventDefault();
                    return false;
                }else if($('#class').val() === ""){
                    swal({  
                        type: 'error',
                        title: 'Please choose Class!',
                        icon: 'error',
                        confirmButtonColor: "#367fa9",
                    });
                    event.preventDefault();
                    return false;
                }else if($('#sub_class').val() === ""){
                    swal({  
                        type: 'error',
                        title: 'Please choose Sub Class!',
                        icon: 'error',
                        confirmButtonColor: "#367fa9",
                    });
                    event.preventDefault();
                    return false;
                }else if($('#itemDesc').val() === ""){
                            swal({  
                                type: 'error',
                                title: 'Item Description required!',
                                icon: 'error',
                                confirmButtonColor: "#367fa9",
                            });
                            event.preventDefault();
                            return false;
                }else if($('#brand').val() === ""){
                    swal({  
                        type: 'error',
                        title: 'Brand required!',
                        icon: 'error',
                        confirmButtonColor: "#367fa9",
                    });
                    event.preventDefault();
                    return false;
                }else if($('#model').val() === ""){
                    swal({  
                        type: 'error',
                        title: 'Model required!',
                        icon: 'error',
                        confirmButtonColor: "#367fa9",
                    });
                    event.preventDefault();
                    return false;
                }else if($('#size').val() === ""){
                    swal({  
                        type: 'error',
                        title: 'Size required!',
                        icon: 'error',
                        confirmButtonColor: "#367fa9",
                    });
                    event.preventDefault();
                    return false;
                }else if($('#actual_color').val() === ""){
                    swal({  
                        type: 'error',
                        title: 'Actual Color required!',
                        icon: 'error',
                        confirmButtonColor: "#367fa9",
                    });
                    event.preventDefault();
                    return false;
                }else if($('#budget').val() === ""){
                    swal({  
                        type: 'error',
                        title: 'Budget Range required!',
                        icon: 'error',
                        confirmButtonColor: "#367fa9",
                    });
                    event.preventDefault();
                    return false;
                }else if($('#quantity').val() == 0){
                    swal({  
                            type: 'error',
                            title: 'Quantity cannot be empty or zero!',
                            icon: 'error',
                            confirmButtonColor: "#367fa9",
                        });
                        event.preventDefault();
                        return false;
                }else if($('#quantity').val() < 0){
                    swal({
                        type: 'error',
                        title: 'Negative Value is not allowed!',
                        icon: 'error',
                        confirmButtonColor: "#367fa9",
                    }); 
                    event.preventDefault(); // cancel default behavior
                    return false;
                }else if($('#quantity').val().match(reg)){
                    swal({
                        type: 'error',
                        title: 'Invalid Quantity Value!',
                        icon: 'error',
                        confirmButtonColor: "#367fa9",
                    }); 
                    event.preventDefault(); // cancel default behavior
                    return false;     
                } else{

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