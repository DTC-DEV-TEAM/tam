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

            .plus{
                font-size:20px;
            }
            #add-Row{
                border:none;
                background-color: #fff;
            }
          
            .iconPlus{
                background-color: #3c8dbc: 
            }
            
            .iconPlus:before {
                content: '';
                display: flex;
                justify-content: center;
                align-items: center;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                /* border: 1px solid rgb(194, 193, 193); */
                font-size: 35px;
                color: white;
                background-color: #3c8dbc;
       
            }
            #bigplus{
                transition: transform 0.5s ease 0s;
            }
            #bigplus:before {
                content: '\FF0B';
                background-color: #3c8dbc: 
                font-size: 50px;
            }
            #bigplus:hover{
                /* cursor: default;
                transform: rotate(180deg); */
                -webkit-animation: infinite-spinning 1s ease-out 0s infinite normal;
                 animation: infinite-spinning 1s ease-out 0s infinite normal;
               
            }

            @keyframes infinite-spinning {
                from {
                    transform: rotate(0deg);
                }
                to {
                    transform: rotate(360deg);
                }
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
        Marketing Item Source Form
    </div>

    <form action="{{ CRUDBooster::mainpath('add-save') }}" method="POST" id="AssetRequest" enctype="multipart/form-data">
        <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
        <input type="hidden" value="6" name="request_type_id" id="request_type_id">

        <div class='panel-body'>
            @include('item-sourcing.item-sourcing-view-header-mkt',['Header'=>$Header])
            <hr/>
            @include('item-sourcing.item-sourcing-view-body-mkt',['categories'=>$categories, 'yesno'=>$yesno])
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
            //minDate:now.setDate(now.getDate() + numWeeks * 7).setHours(0,0,0,0),
            viewMode: "days",
            minDate: moment().add('days', 14).millisecond(0).second(0).minute(0).hour(0),
            format: "YYYY-MM-DD",
            dayViewHeaderFormat: "MMMM YYYY",
        });
   
        $(".date").val('');

        $('.select2').select2({});

        $('#budget').select2({});


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

            //ADD ROW
            $("#add-Row").click(function() {
                event.preventDefault(); 
                var description = "";
                var count_fail = 0;
                $('.itemDesc, .brand, .model, .size, .actual_color, .material, .thickness, .lamination, .add_ons, .installation, .dismantling_body').each(function() {
                    description = $(this).val();
                    if (description == null || description == "") {
                          swal({
                                type: 'error',
                                title: 'Please fill out all fields!',
                                icon: 'error',
                                confirmButtonColor: "#367fa9",
                            }); 
                            event.preventDefault(); // cancel default behavior
                        count_fail++;
                    }else{
                        count_fail = 0;
                    }
                });
               

                tableRow++;

                if(count_fail == 0){

                    var newrow =
                    '<tr>' +
                        '<td>' +
                            '<input type="text" placeholder="Item Description..." onkeyup="this.value = this.value.toUpperCase();" oninput="validate(this)" class="form-control finput itemDesc" id="itemDesc'+ tableRow +'"  name="item_description[]"  required maxlength="100">' +
                        '</td>' + 
                        '<td>' +
                            '<input type="text" placeholder="Brand..." onkeyup="this.value = this.value.toUpperCase();" oninput="validate(this)" class="form-control finput brand" id="brand'+ tableRow +'"  name="brand[]"  required maxlength="100">' +
                        '</td>' + 
                        '<td>' +
                            '<input type="text" placeholder="Model..." onkeyup="this.value = this.value.toUpperCase();" oninput="validate(this)" class="form-control finput model" id="model'+ tableRow +'"  name="model[]"  required maxlength="100">' +
                        '</td>' + 
                        '<td>' +
                            '<input type="text" placeholder="Size..." onkeyup="this.value = this.value.toUpperCase();" oninput="validate(this)" class="form-control finput size" id="size'+ tableRow +'"  name="size[]"  required maxlength="100">' +
                        '</td>' + 
                        '<td>' +
                            '<input type="text" placeholder="Actual Color..." onkeyup="this.value = this.value.toUpperCase();" oninput="validate(this)" class="form-control finput actual_color" id="actual_color'+ tableRow +'"  name="actual_color[]"  required maxlength="100">' +
                        '</td>' + 
                        '<td>' +
                            '<input type="text" placeholder="Material..." onkeyup="this.value = this.value.toUpperCase();" oninput="validate(this)" class="form-control finput material" id="material'+ tableRow +'"  name="material[]"  required maxlength="100">' +
                        '</td>' + 
                        '<td>' +
                            '<input type="text" placeholder="Thickness..." onkeyup="this.value = this.value.toUpperCase();" oninput="validate(this)" class="form-control finput thickness" id="thickness'+ tableRow +'"  name="thickness[]"  required maxlength="100">' +
                        '</td>' + 
                        '<td>' +
                            '<input type="text" placeholder="Lamination..." onkeyup="this.value = this.value.toUpperCase();" oninput="validate(this)" class="form-control finput lamination" id="lamination'+ tableRow +'"  name="lamination[]"  required maxlength="100">' +
                        '</td>' + 
                        '<td>' +
                            '<input type="text" placeholder="Add Ons..." onkeyup="this.value = this.value.toUpperCase();" oninput="validate(this)" class="form-control finput add_ons" id="add_ons'+ tableRow +'"  name="add_ons[]"  required maxlength="100">' +
                        '</td>' + 

                        '<td>' + 
                            '<select selected data-placeholder="Choose" class="form-control select2 installation" name="installation[]" id="installation'+ tableRow +'" required style="width:100%">' +
                                '<option value=""></option>' + 
                                '@foreach($yesno as $data)' +
                                    '<option value="{{$data->description}}">{{$data->description}}</option>' +
                                '@endforeach' +
                            '</select>' +
                        '</td>' + 

                        '<td>' + 
                           '<select selected data-placeholder="Choose" class="form-control select2 dismantling_body" name="dismantling_body[]" id="dismantling_body'+ tableRow +'" required style="width:100%">' + 
                                '<option value=""></option>' +
                                '@foreach($yesno as $data)' +
                                    '<option value="{{$data->description}}">{{$data->description}}</option>' +
                                '@endforeach' +
                            '</select>' +
                        '</td>' + 

                        '<td>' + 
                                '<input class="form-control text-center quantity_item" type="text" oninput="validate(this)" required name="quantity[]" id="quantity'+ tableRow +'"  value="1" min="0" max="9999999999" step="any" onkeypress="return event.charCode <= 57">' + 
                        '</td>' + 

                        '<td>' +
                            '<button id="deleteRow" name="removeRow" class="btn btn-danger removeRow"><i class="glyphicon glyphicon-trash"></i></button>' +
                        '</td>' +
                        
                    '</tr>';
                    $(newrow).insertBefore($('table tr#tr-table1:last'));
                    $('#installation'+tableRow).select2({});
                    $('#dismantling_body'+tableRow).select2({});
                    $("#quantity_total").val(calculateTotalQuantity());
                }
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

        function calculateTotalQuantity() {
            var totalQuantity = 0;
            $('.quantity_item').each(function() {

            totalQuantity += parseInt($(this).val());
            });
            return totalQuantity;
        }
     
        $("#btnSubmit").click(function(event) {
            event.preventDefault();
            var countRow = $('#asset-items tfoot tr').length;
            var reg = /^0/gi;
            
                if ($('#sampling').val() === "") {
                    swal({
                        type: 'error',
                        title: 'Color Proofing required!',
                        icon: 'error',
                        confirmButtonColor: "#367fa9",
                    }); 
                    event.preventDefault(); // cancel default behavior
                }else if ($('#mock_up').val() === "") {
                    swal({
                        type: 'error',
                        title: 'Mark Up required!',
                        icon: 'error',
                        confirmButtonColor: "#367fa9",
                    }); 
                    event.preventDefault(); // cancel default behavior
                }else if ($('#date_needed').val() === "") {
                    swal({
                        type: 'error',
                        title: 'Date needed required!',
                        icon: 'error',
                        confirmButtonColor: "#367fa9",
                    }); 
                    event.preventDefault(); // cancel default behavior
                }
                // else if ($('#artworklink').val() === "") {
                //     swal({
                //         type: 'error',
                //         title: 'Artworklink required!',
                //         icon: 'error',
                //         confirmButtonColor: "#367fa9",
                //     }); 
                //     event.preventDefault(); // cancel default behavior
                // }
                // else if ($('#upload_file').val() === "") {
                //     swal({
                //         type: 'error',
                //         title: 'Upload File/Photos required!',
                //         icon: 'error',
                //         confirmButtonColor: "#367fa9",
                //     }); 
                //     event.preventDefault(); // cancel default behavior
                // }
                else if (countRow == 1) {
                    swal({
                        type: 'error',
                        title: 'Please add an item!',
                        icon: 'error',
                        confirmButtonColor: "#367fa9",
                    }); 
                    event.preventDefault(); // cancel default behavior
                }
         
                else{

                     //header image validation
                     if($('#upload_file').val() !== ""){
                        for (var i = 0; i < $("#upload_file").get(0).files.length; ++i) {
                            var file1=$("#upload_file").get(0).files[i].name;
                            if(file1){                        
                                var file_size=$("#upload_file").get(0).files[i].size;
                                //if(file_size<2097152){
                                    var ext = file1.split('.').pop().toLowerCase();                            
                                    if($.inArray(ext,['jpg','jpeg','gif','png','xlsx','docs','pdf'])===-1){
                                        swal({
                                            type: 'error',
                                            title: 'Invalid Image/File Extension!',
                                            icon: 'error',
                                            customClass: 'swal-wide',
                                            confirmButtonColor: "#367fa9"
                                        });
                                        event.preventDefault();
                                        return false;
                                    }

                                // }else{
                                //     alert("Screenshot size is too large.");
                                //     return false;
                                // }                        
                            }
                        }
                    }

                    //Description
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

                    //Brand
                    var brand = $("input[name^='brand']").length;
                    var brand_value = $("input[name^='brand']");
                    for(i=0;i<brand;i++){
                        if(brand_value.eq(i).val() == 0 || brand_value.eq(i).val() == null){
                            swal({  
                                    type: 'error',
                                    title: 'Brand cannot be empty!',
                                    icon: 'error',
                                    confirmButtonColor: "#367fa9",
                                });
                                event.preventDefault();
                                return false;
                        } 
                    } 

                    //Model
                    var model = $("input[name^='model']").length;
                    var model_value = $("input[name^='model']");
                    for(i=0;i<model;i++){
                        if(model_value.eq(i).val() == 0 || model_value.eq(i).val() == null){
                            swal({  
                                    type: 'error',
                                    title: 'Model cannot be empty!',
                                    icon: 'error',
                                    confirmButtonColor: "#367fa9",
                                });
                                event.preventDefault();
                                return false;
                        } 
                    } 

                    //Size
                    var size = $("input[name^='size']").length;
                    var size_value = $("input[name^='size']");
                    for(i=0;i<size;i++){
                        if(size_value.eq(i).val() == 0 || size_value.eq(i).val() == null){
                            swal({  
                                    type: 'error',
                                    title: 'Size cannot be empty!',
                                    icon: 'error',
                                    confirmButtonColor: "#367fa9",
                                });
                                event.preventDefault();
                                return false;
                        } 
                    } 

                    //Actual Color
                    var ac = $("input[name^='actual_color']").length;
                    var ac_value = $("input[name^='actual_color']");
                    for(i=0;i<ac;i++){
                        if(ac_value.eq(i).val() == 0 || ac_value.eq(i).val() == null){
                            swal({  
                                    type: 'error',
                                    title: 'Actual Color cannot be empty!',
                                    icon: 'error',
                                    confirmButtonColor: "#367fa9",
                                });
                                event.preventDefault();
                                return false;
                        } 
                    } 

                    //Material
                    var material = $("input[name^='material']").length;
                    var material_value = $("input[name^='material']");
                    for(i=0;i<material;i++){
                        if(material_value.eq(i).val() == 0 || material_value.eq(i).val() == null){
                            swal({  
                                    type: 'error',
                                    title: 'Material cannot be empty!',
                                    icon: 'error',
                                    confirmButtonColor: "#367fa9",
                                });
                                event.preventDefault();
                                return false;
                        } 
                    } 

                    //Thickness
                    var thickness = $("input[name^='thickness']").length;
                    var thickness_value = $("input[name^='thickness']");
                    for(i=0;i<thickness;i++){
                        if(thickness_value.eq(i).val() == 0 || thickness_value.eq(i).val() == null){
                            swal({  
                                    type: 'error',
                                    title: 'Thickness cannot be empty!',
                                    icon: 'error',
                                    confirmButtonColor: "#367fa9",
                                });
                                event.preventDefault();
                                return false;
                        } 
                    } 

                    //Lamination
                    var lamination = $("input[name^='lamination']").length;
                    var lamination_value = $("input[name^='lamination']");
                    for(i=0;i<lamination;i++){
                        if(lamination_value.eq(i).val() == 0 || lamination_value.eq(i).val() == null){
                            swal({  
                                    type: 'error',
                                    title: 'Lamination cannot be empty!',
                                    icon: 'error',
                                    confirmButtonColor: "#367fa9",
                                });
                                event.preventDefault();
                                return false;
                        } 
                    } 

                    //Add Ons
                    var ao = $("input[name^='add_ons']").length;
                    var ao_value = $("input[name^='add_ons']");
                    for(i=0;i<ao;i++){
                        if(ao_value.eq(i).val() == 0 || ao_value.eq(i).val() == null){
                            swal({  
                                    type: 'error',
                                    title: 'Add Ons cannot be empty!',
                                    icon: 'error',
                                    confirmButtonColor: "#367fa9",
                                });
                                event.preventDefault();
                                return false;
                        } 
                    } 

                    //Installation
                    var installation = $("input[name^='installation']").length;
                    var installation_value = $("input[name^='installation']");
                    for(i=0;i<installation;i++){
                        if(installation_value.eq(i).val() == 0 || installation_value.eq(i).val() == null){
                            swal({  
                                    type: 'error',
                                    title: 'Installation cannot be empty!',
                                    icon: 'error',
                                    confirmButtonColor: "#367fa9",
                                });
                                event.preventDefault();
                                return false;
                        } 
                    } 

                    //Dismantling
                    var dismantling = $("input[name^='dismantling_body']").length;
                    var dismantling_value = $("input[name^='dismantling_body']");
                    for(i=0;i<dismantling;i++){
                        if(dismantling_value.eq(i).val() == 0 || dismantling_value.eq(i).val() == null){
                            swal({  
                                    type: 'error',
                                    title: 'Dismantling cannot be empty!',
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