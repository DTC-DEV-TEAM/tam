
    <meta charset="UTF-8">
    <title>{{ ($page_title)?get_setting('appname').': '.strip_tags($page_title):"Admin Area" }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <meta name='generator' content='CRUDBooster {{ \crocodicstudio\crudbooster\commands\CrudboosterVersionCommand::$version }}'/>
    <meta name='robots' content='noindex,nofollow'/>
    <link rel="shortcut icon"
          href="{{ CRUDBooster::getSetting('favicon')?asset(CRUDBooster::getSetting('favicon')):asset('vendor/crudbooster/assets/logo_crudbooster.png') }}">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.4.1 -->
    <link href="{{ asset("vendor/crudbooster/assets/adminlte/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet" type="text/css"/>
    <!-- Font Awesome Icons -->
    <link href="{{asset("vendor/crudbooster/assets/adminlte/font-awesome/css")}}/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <!-- Ionicons -->
    <link href="{{asset("vendor/crudbooster/ionic/css/ionicons.min.css")}}" rel="stylesheet" type="text/css"/>
    <!-- Theme style -->
    <link href="{{ asset("vendor/crudbooster/assets/adminlte/dist/css/AdminLTE.min.css")}}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset("vendor/crudbooster/assets/adminlte/dist/css/skins/_all-skins.min.css")}}" rel="stylesheet" type="text/css"/>

    <!-- support rtl-->
    @if (in_array(App::getLocale(), ['ar', 'fa']))
        <link rel="stylesheet" href="//cdn.rawgit.com/morteza/bootstrap-rtl/v3.3.4/dist/css/bootstrap-rtl.min.css">
        <link href="{{ asset("vendor/crudbooster/assets/rtl.css")}}" rel="stylesheet" type="text/css"/>
    @endif
    {{-- 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.10.1/themes/base/jquery.ui.all.css" integrity="sha256-hm+Dtd545e308BZibJ3GKEYCgiGLF87D5tXDWlMOzMU=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css" integrity="sha256-yMjaV542P+q1RnH6XByCPDfUFhmOafWbeLPmqKh11zo=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.css" integrity="sha256-p6xU9YulB7E2Ic62/PX+h59ayb3PBJ0WFTEQxq0EjHw=" crossorigin="anonymous" />
    --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/css/select2.min.css" integrity="sha256-MeSf8Rmg3b5qLFlijnpxk6l+IJkiR91//YGPCrCmogU=" crossorigin="anonymous" />


    <link rel='stylesheet' href='{{asset("vendor/crudbooster/assets/css/main.css") }}'/>

    <!-- load css -->

 

    <style type="text/css">
        .dropdown-menu-action {
            left: -130%;
        }

        .btn-group-action .btn-action {
            cursor: default
        }

        #box-header-module {
            box-shadow: 10px 10px 10px #dddddd;
        }

        .sub-module-tab li {
            background: #F9F9F9;
            cursor: pointer;
        }

        .sub-module-tab li.active {
            background: #ffffff;
            box-shadow: 0px -5px 10px #cccccc
        }

        .nav-tabs > li.active > a, .nav-tabs > li.active > a:focus, .nav-tabs > li.active > a:hover {
            border: none;
        }

        .nav-tabs > li > a {
            border: none;
        }

        .breadcrumb {
            margin: 0 0 0 0;
            padding: 0 0 0 0;
        }

        .form-group > label:first-child {
            display: block
        }

        #table_dashboard.table-bordered, #table_dashboard.table-bordered thead tr th, #table_dashboard.table-bordered tbody tr td {
            border: 1px solid #bbbbbb !important;
        }
    </style>

    @stack('head')


<!-- load js -->
@if($load_js)
    @foreach($load_js as $js)
        <script src="{{$js}}"></script>
    @endforeach
@endif
<script type="text/javascript">
    var site_url = "{{url('/')}}";
    @if($script_js)
        {!! $script_js !!}
    @endif
</script>

@stack('bottom')

@include('crudbooster::admin_template_plugins')


            <div class="row">
                <div class="col-md-12">
                       <div class="col-md-6">
                           <div class="form-group">
                               <label class="control-label">{{ trans('message.form-label.add_item') }}</label>
                               <input class="form-control auto" placeholder="Search Item" id="search">
                               <ul class="ui-autocomplete ui-front ui-menu ui-widget ui-widget-content" id="ui-id-2" style="display: none; top: 60px; left: 15px; width: 520px;">
                                   <li>Loading...</li>
                               </ul>
                           </div>
                       </div>
                </div>
            </div>




<script type="text/javascript">

    alert();

    function preventBack() {
        window.history.forward();
    }
    window.onunload = function() {
        null;
    };
    setTimeout("preventBack()", 0);

    $( "#quote_date, #po_date" ).datepicker( { format: 'yyyy-mm-dd', endDate: new Date() } );

    var tableRow = <?php echo json_encode($tableRow); ?>;
    
    var tableRow1 = tableRow;

    tableRow1++;

    /*$("#search-items").on('shown.bs.modal', function(){
        //$("#item_search").text("1s");
    });*/

    $(document).ready(function() {

        $(".add-row-button").click(function() {

            var buttonNo = $(this).attr("data-id");

            var itemVal = $("#item_id"+buttonNo).val();

            tableRow++;
            
            var newrow =
            '<br/>' +
            '<tr>' +
                
                '<td >' +
                '<div id="divreco'+ tableRow + '">' +  
                '<input type="hidden"  class="form-control"  name="add_item_id[]" id="add_item_id'+ tableRow + '"  required  value="'+ itemVal +'">' +
                '<input type="text" onkeyup="this.value = this.value.toUpperCase();" class="form-control Reco" data-id="'+ tableRow + '" id="recommendation_add'+ tableRow + '"  name="recommendation_add[]"  required maxlength="100">' +
                '</div >' +
                '</td>' +  

            '</tr>';
            $(newrow).insertBefore($('table tr#tr-table-reco'+ buttonNo + ':last'));
         
            var newrow1 =
            '<br/>' +
            '<tr>' +   
                '<td >' +
                '<div id="div'+ tableRow + '">' +  
          
                '<button id="delete-row-button'+ tableRow + '" name="delete-row-button" class="btn btn-danger delete-row-button'+ tableRow + ' removeRow" data-id="'+ tableRow + '" ><i class="glyphicon glyphicon-trash"></i></button>' +
                '</div >' +
                '</td>' +  
            '</tr>';
            $(newrow1).insertBefore($('table tr#tr-table-reco-delete'+ buttonNo + ':last'));

            return false;

        });

        //deleteRow
        $(document).on('click', '.removeRow', function() {

            var buttonNo = $(this).attr("data-id");
            
            
            $('#div'+buttonNo).remove();

            $('#divreco'+buttonNo).remove();

            return false;
            
        });

        var stack = [];
        var token = $("#token").val();

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

                        
                            //var rowCount = $('#asset-items tr').length;
                            //myStr = data.sample; 
                            //alert(data.status_no);

                            if (data.status_no == 1) {

                                $("#val_item").html();
                                var data = data.items;
                                $('#ui-id-2').css('display', 'none');  

                                response($.map(data, function (item) {

                                    //$("#ui-id-2").append($("<li class='ui-menu-item'>").text(item.item_description));
                                    return {
                                        id:                         item.id,
                                        asset_code:                 item.asset_code,
                                        digits_code:                item.digits_code,
                                        asset_tag:                  item.asset_tag,
                                        serial_no:                  item.serial_no,
                                        value:                      item.item_description,
                                        category_description:       item.category_description,
                                        item_cost:                  item.item_cost,
                                        item_type:                  item.item_type,
                                     
                                    }

                                }));
                                
                               // $("#ui-id-2").css('display', 'block');

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
                                    
                                    if(e.item_type == "GENERAL"){

                                        var new_row = '<tr class="nr" id="rowid' + e.id + '">' +
                                                '<td><input class="form-control text-center" type="text" name="digits_code[]" readonly value="' + e.digits_code + '"></td>' +
                                                '<td><input class="form-control" type="text" name="item_description[]" readonly value="' + e.value + '"></td>' +
                                                '<td><input class="form-control" type="text" name="serial_no[]" readonly value=""></td>' +
                                                '<td><input class="form-control" type="text" name="asset_tag[]" readonly value="' + e.asset_code + '"></td>' +

                                                '<td><input class="form-control text-center quantity_item" type="number" name="quantity[]" id="quantity' + e.id  + '" data-id="' + e.id  + '"  value="1" min="0" max="9999999999" step="any" onKeyPress="if(this.value.length==10) return false;" oninput="validity.valid||(value=0);"></td>' +
                            
                                                '<td><input class="form-control text-center cost_item" type="number" name="unit_cost[]" id="unit_cost' + e.id  + '"   data-id="' + e.id  + '"  value="' + e.item_cost + '" min="0" max="9999999999" step="any" onKeyPress="if(this.value.length==10) return false;" oninput="validity.valid||(value=0);"></td>' +
                                                
                                                '<td><input class="form-control text-center total_cost_item" type="number" name="total_unit_cost[]"  id="total_unit_cost' + e.id  + '"   value="' + e.item_cost + '" readonly="readonly" step="0.01" required maxlength="100"></td>' +

                                                '<td class="text-center"><button id="' +e.id + '" onclick="reply_click1(this.id)" class="btn btn-xs btn-danger delete_item" style="width:60px;height:30px;font-size: 11px;text-align: center;">REMOVE</button></td>' +
                                                
                                                '<input type="hidden" name="item_id[]" readonly value="' +e.id + '">' +
                                                '<input type="hidden" name="category_id[]" readonly value="IT ASSETS">' +

                                                '</tr>';

                                    }else{

                                        var serials = "";

                                        if(e.serial_no == null || e.serial_no == ""){
                                            serials = "";
                                        }else{
                                            serials = e.serial_no;
                                        }
                                        
                                            var new_row = '<tr class="nr" id="rowid' + e.id + '">' +
                                                    '<td><input class="form-control text-center" type="text" name="digits_code[]" readonly value="' + e.digits_code + '"></td>' +
                                                    '<td><input class="form-control" type="text" name="item_description[]" readonly value="' + e.value + '"></td>' +
                                                    '<td><input class="form-control" type="text" name="serial_no[]" readonly value="' + serials + '"></td>' +
                                                    '<td><input class="form-control" type="text" name="asset_tag[]" readonly value="' + e.asset_code + '"></td>' +

                                                    '<td><input class="form-control text-center quantity_item" type="number" name="quantity[]" id="quantity' + e.id  + '" data-id="' + e.id  + '"  value="1" min="0" max="9999999999" step="any" onKeyPress="if(this.value.length==10) return false;" oninput="validity.valid||(value=0);" readonly="readonly"></td>' +
                                
                                                    '<td><input class="form-control text-center cost_item" type="number" name="unit_cost[]" id="unit_cost' + e.id  + '"   data-id="' + e.id  + '"  value="' + e.item_cost + '" min="0" max="9999999999" step="any" onKeyPress="if(this.value.length==10) return false;" oninput="validity.valid||(value=0);"></td>' +
                                                    
                                                    '<td><input class="form-control text-center total_cost_item" type="number" name="total_unit_cost[]"  id="total_unit_cost' + e.id  + '"   value="' + e.item_cost + '" readonly="readonly" step="0.01" required maxlength="100"></td>' +

                                                    '<td class="text-center"><button id="' +e.id + '" onclick="reply_click1(this.id)" class="btn btn-xs btn-danger delete_item" style="width:60px;height:30px;font-size: 11px;text-align: center;">REMOVE</button></td>' +
                                                    
                                                    '<input type="hidden" name="item_id[]" readonly value="' +e.id + '">' +
                                                    '<input type="hidden" name="category_id[]" readonly value="IT ASSETS">' +

                                                    '</tr>';

                                    }



                                    $(new_row).insertAfter($('table tr.dynamicRows:last'));
                
                                    //blank++;

                                    //$("#total").val(calculateTotalValue2());
                                    $("#total").val(calculateTotalValue2());
                                    $("#quantity_total").val(calculateTotalQuantity());

                                    $(this).val('');
                                    $('#val_item').html('');
                                    return false;
                                
                                }else{


                                    if(e.item_type == "GENERAL"){

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
       
    });

    $(".search").click(function(event) {
       
       var searchID = $(this).attr("data-id");
       
       //alert($("#item_description"+searchID).val());

       $("#item_search").text($("#item_description"+searchID).val());

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

    $("#btnSubmit").click(function(event) {

        

        var strconfirm = confirm("Are you sure you want to proceed this request?");
        if (strconfirm == true) {

            $("#action").val("1");

            $(this).attr('disabled','disabled');

            $('#myform').submit(); 
            
        }else{
            return false;
            window.stop();
        }

        /*
        var countRow = $('#asset-items tbody tr').length;

        var countRow1 = $('#asset-items1 tbody tr').length;

        var rowsum = countRow1 - 1;

        if (countRow == 2) {
            alert("Please add an item!");
            event.preventDefault(); // cancel default behavior
        }

        var qty = 0;

        $('.quantity_item').each(function() {

            qty = $(this).val();
            if (qty == 0) {
                alert("Quantity cannot be empty or zero!");
                event.preventDefault(); // cancel default behavior
            } else if (qty < 0) {
                alert("Negative Value is not allowed!");
                event.preventDefault(); // cancel default behavior
            }
            
        });


            var text_length = $("#po_number").val().length;
            
            if($("#po_number").val().includes("PO#")){
                
                if($("#po_number").val().includes(" ")){
    
                    alert("Incorrect PO# format! e.g. PO#1001");
                    event.preventDefault(); // cancel default behavior
    
                }else if(text_length <= 3){
    
                    alert("Incorrect PO# format! e.g. PO#1001");
                    event.preventDefault(); // cancel default behavior
    
                }
                
            }else{
                    alert("Incorrect PO# format! e.g. PO#1001");
                    event.preventDefault(); // cancel default behavior
            }*/

        /*if(countRow != rowsum){

            alert("Items are not equal!");
            event.preventDefault(); // cancel default behavior
        }*/
        

    });

    $("#btnUpdate").click(function(event) {

            /* var text_length = $("#po_number").val().length;
            
            if($("#po_number").val().includes("PO#")){
                
                if($("#po_number").val().includes(" ")){
    
                    alert("Incorrect PO# format! e.g. PO#1001");
                    event.preventDefault(); // cancel default behavior
    
                }else if(text_length <= 3){
    
                    alert("Incorrect PO# format! e.g. PO#1001");
                    event.preventDefault(); // cancel default behavior
    
                }
                
            }else{
                    alert("Incorrect PO# format! e.g. PO#1001");
                    event.preventDefault(); // cancel default behavior
            }*/

            $("#action").val("0");

    });

    $(document).on('click', '.delete_item', function() {
       
        var RowID = $(this).attr("data-id");

        if ($('#asset-items tbody tr').length != 1) { //check if not the first row then delete the other rows

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

</script>
