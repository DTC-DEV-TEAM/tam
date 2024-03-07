@extends('crudbooster::admin_template')
@push('head')
        <style type="text/css">   
            .comment_div {
                box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
                background: #f5f5f5;
                height: 300px;
                padding: 10px;
                overflow-y: scroll;
                word-wrap: break-word;
            }
            .text-comment{
                display: block;
                background: #fff;
                padding:5px;
                border-radius: 2px;
                box-shadow: rgba(0, 0, 0, 0.05) 0px 6px 24px 0px, rgba(0, 0, 0, 0.08) 0px 0px 0px 1px;
                margin-bottom:0;
            }
            .select2-container--default .select2-selection--multiple .select2-selection__choice{color:black;}
            input.qty:read-only {
                background-color: #fff;
                border: none
            }
            #asset-items th, td, tr {
                border: 1px solid rgba(000, 0, 0, .5);
                padding: 8px;
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
        Return details
    </div>

    <form method="post" id="verificationForm" action="{{route('submit-for-verification-return')}}">
        <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
        <input type="hidden" value="0" name="action" id="action">
        <input type="hidden" value="{{$Header->request_type_id}}" name="request_type_id" id="request_type_id">

        <div class='panel-body'>

            <div class="row">                           
                <label class="control-label col-md-2">From:</label>
                <div class="col-md-4">
                        <p>{{$Header->employee_name}}</p>
                </div>
                <label class="control-label col-md-2">{{ trans('message.form-label.created_at') }}:</label>
                <div class="col-md-4">
                        <p>{{$Header->requested_date}}</p>
                </div>

                
            </div>


            <div class="row">                           
                <label class="control-label col-md-2">{{ trans('message.form-label.company_name') }}:</label>
                <div class="col-md-4">
                        <p>{{$Header->company}}</p>
                </div>
                <label class="control-label col-md-2">{{ trans('message.form-label.department') }}:</label>
                <div class="col-md-4">
                        <p>{{$Header->department_name}}</p>
                </div>
            </div>

            <div class="row">                           
                <label class="control-label col-md-2">{{ trans('message.form-label.position') }}:</label>
                <div class="col-md-4">
                        <p>{{$Header->position}}</p>
                </div>
                @if($Header->store_branch != null || $Header->store_branch != "")
                <label class="control-label col-md-2">{{ trans('message.form-label.store_branch') }}:</label>
                    <div class="col-md-4">
                            <p>{{$Header->store_branch}}</p>
                    </div>
                @endif
            </div>

            <hr/>
            <div class="row">
                <label class="control-label col-md-2">Purpose:</label>
                <div class="col-md-4">
                        <p>{{$Header->request_type}}</p>
                </div>                    
            </div>

            <hr/>
            @if($Header->approvedby != null || $Header->approvedby != "")
            <div class="row">                           
                <label class="control-label col-md-2">{{ trans('message.form-label.approved_by') }}:</label>
                <div class="col-md-4">
                        <p>{{$Header->approvedby}}</p>
                </div>
                <label class="control-label col-md-2">{{ trans('message.form-label.approved_at') }}:</label>
                <div class="col-md-4">
                        <p>{{$Header->approved_date}}</p>
                </div>
            </div>
            @endif
            @if($Header->approver_comments != null || $Header->approver_comments != "")
                <div class="row">                           
                    <label class="control-label col-md-2">{{ trans('message.table.approver_comments') }}:</label>
                    <div class="col-md-10">
                            <p>{{$Header->approver_comments}}</p>
                    </div>
                </div>
            @endif 
            <hr>

            <table class='table' id="asset-items">
                <thead>
                    <tr style="background-color:#00a65a; border: 0.5px solid #000;">
                        <th style="text-align: center" colspan="16"><h4 class="box-title" style="color: #fff;"><b>Item details</b></h4></th>
                    </tr>
                    <tr>
                        <th width="10%" class="text-center">Item to Receive <br> <span>Check All <br> <input type="checkbox" id="check_all"> </span></th> 
                        <th width="5%" class="text-center">Good</th> 
                        <th width="5%" class="text-center">Defective</th>
                        <th width="10%" class="text-center">Reference No</th>
                        @if(in_array($Header->request_type_id, [1,5]))
                            <th width="7%" class="text-center">Asset Code</th>
                        @endif
                        <th width="7%" class="text-center">Digits Code</th>
                        <th width="20%" class="text-center">{{ trans('message.table.item_description') }}</th>  
                        @if(!in_array($Header->request_type_id, [1,5]))
                            <th width="7%" class="text-center">Qty</th>
                        @endif
                        <th width="10%" class="text-center">Asset Type</th>                                                         
                        <th width="12%" class="text-center">Comments</th>
                        @if(CRUDBooster::myPrivilegeId() == 6)
                        <th width="10%">Location</th>
                        @endif
                    </tr>
                    <?php   $tableRow1 = 0; ?>
                    <?Php   $item_count = 0; ?>
                </thead>
                <tbody>
                    @foreach($return_body as $rowresult)
                    <?php $tableRow1++; ?>
                    <?Php $item_count++; ?>
                        <tr>
                            <td style="text-align:center" height="10">
                                <input type="checkbox" name="item_to_receive_id[]" id="item_to_receive_id{{$tableRow1}}" class="item_to_receive_id" required data-id="{{$tableRow1}}" value="{{$rowresult->body_id}}"/>
                            </td>
                            <td style="text-align:center" height="10">
                                <input type="hidden" value="{{$rowresult->id}}" name="item_id[]">
                                <input type="hidden" value="{{$rowresult->mo_id}}" name="mo_id[]">
                                <input type="hidden" name="good_text[]" id="good_text{{$tableRow1}}" />
                                <input type="checkbox" name="good[]" id="good{{$tableRow1}}" class="good" required data-id="{{$tableRow1}}" value="{{$rowresult->asset_code}}"/>
                                <input type="hidden" name="arf_number[]" id="arf_number[]" value="{{$rowresult->reference_no}}" />
                                <input type="hidden" name="digits_code[]" id="digits_code{{$tableRow1}}" value="{{$rowresult->digits_code}}" />
                                <input type="hidden" name="asset_code[]" id="asset_code{{$tableRow1}}" value="{{$rowresult->asset_code}}" />
                            </td>
                            <td style="text-align:center" height="10">
                            <input type="hidden" name="defective_text[]" id="defective_text{{$tableRow1}}" />
                            <input type="checkbox" name="defective[]" id="defective{{$tableRow1}}" class="defective" required data-id="{{$tableRow1}}"  value="{{$rowresult->asset_code}}"/>
                            </td>
                            <td style="text-align:center" height="10">{{$rowresult->reference_no}}</td>
                            @if(in_array($Header->request_type_id, [1,5]))
                                <td style="text-align:center" height="10">{{$rowresult->asset_code}}</td>
                            @endif
                            <td style="text-align:center" height="10">{{$rowresult->digits_code}}</td>
                            <td style="text-align:center" height="10">{{$rowresult->description}}</td>
                            @if(!in_array($Header->request_type_id, [1,5]))
                                <td style="text-align:center" height="10"> 
                                    <input type="text" class="form-control text-center qty" name="mo_qty[]" value="{{$rowresult->quantity}}" readonly> 
                                </td>
                            @endif
                            <td style="text-align:center" height="10">{{$rowresult->asset_type}}</td>
                            <td style="text-align:center" height="10">
                                <select required selected data-placeholder="Select Comments" id="comments{{$tableRow1}}" data-id="{{$tableRow1}}" name="comments[]" class="form-select select2 comments" style="width:100%;" multiple="multiple">
                                    @foreach($good_defect_lists as $good_defect_list)
                                        <option value=""></option>
                                        <option value="{{ $rowresult->asset_code. '|' .$rowresult->digits_code. '|' .$good_defect_list->defect_description }}">{{ $good_defect_list->defect_description }}</option>
                                    @endforeach
                                </select>
                            </td>   
                            <!-- @if(CRUDBooster::myPrivilegeId() == 9)
                            <td>
                                <select required selected data-placeholder="-- Select Location --" id="location{{$tableRow1}}" data-id="{{$tableRow1}}" name="location" class="form-select location" style="width:100%;">
                                    @foreach($warehouse_location as $locations)
                                        <option value=""></option>
                                        <option value="{{ $locations->id}}">{{ $locations->location }}</option>
                                    @endforeach
                                </select>
                            </td>   
                            @endif      -->
                        </tr>
                        <tr id="others{{$tableRow1}}" style="display:none">
                        <td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                        <td><input type="text" class="form-control" placeholder="Please input other comments" id="inputValue{{$tableRow1}}" name="other_comment[]"></td>                                                   
                        </tr>
                    @endforeach

                </tbody>
                
            </table> 

        </div>


        <div class='panel-footer'>
            <a href="{{ CRUDBooster::mainpath() }}" class="btn btn-default">{{ trans('message.form.cancel') }}</a>
            <button class="btn btn-success pull-right" type="submit" id="btnSubmit"> <i class="fa fa-thumbs-up" ></i> Verify</button>

        </div>

    </form>



</div>

@endsection
@push('bottom')
<script type="text/javascript">
    $(function(){
        $('body').addClass("sidebar-collapse");
    });
    $('.select2').select2({multiple: true});

    $('#check_all').change(function() {
        if(this.checked) {
            $(".item_to_receive_id").prop("checked", true);
            $('.good').attr("disabled", false);
            $('.defective').attr("disabled", false);
            $('.comments').attr("disabled", false);
            
            if ($('.item_to_receive_id:checked').length == $('.item_to_receive_id').length) {
                if ($('.good:checked').length == $('.good').length) {
                    $('#btnSubmit').attr("disabled", false);
                }else if($('.item_to_receive_id:checked').length == $('.good:checked').length + $('.defective:checked').length){
                    $('#btnSubmit').attr("disabled", false);
                }if ($('.good:checked').length == 0 || $('.defective:checked').length == 0) {
                    $('#btnSubmit').attr("disabled", true);
                }else{
                    $('#btnSubmit').attr("disabled", true);  
                }
            }
        }
        else{
            $('#btnSubmit').attr("disabled", true);
            $(".item_to_receive_id").prop("checked", false);
            $('.good').attr("disabled", true);
            $('.good').prop("checked", false);
            $('.defective').attr("disabled", true);
            $('.defective').prop("checked", false);
            $('.comments').attr("disabled", true);
        }
    });

    function preventBack() {
        window.history.forward();
    }
     window.onunload = function() {
        null;
    };
    setTimeout("preventBack()", 0);
   
    setTimeout("preventBack()", 0);

    $('#btnSubmit').attr("disabled", true);
    $('.good').attr("disabled", true);
    $('.defective').attr("disabled", true);
    $('.comments').attr("disabled", true);
    var count_pick = 0;
    //ITEM TO SELECT
    $('.item_to_receive_id').change(function() {
        var asset_code = $(this).val();
        var id = $(this).attr("data-id");
        $("#defective_text"+id).val("0");
        var ischecked= $(this).is(':checked');
        if(ischecked == false){
            count_pick--;
           
            if ($('.item_to_receive_id:checked').length == $('.item_to_receive_id').length) {
                if ($('.good:checked').length == $('.good').length) {
                    $('#btnSubmit').attr("disabled", false);
                }else if($('.item_to_receive_id:checked').length == $('.good:checked').length + $('.defective:checked').length){
                    $('#btnSubmit').attr("disabled", false);
                }else{
                    $('#btnSubmit').attr("disabled", true);  
                }
            }

            if ($('#good'+id).is(':checked')) {
                $('#btnSubmit').attr("disabled", false);
            }else if ($('#defective'+id).is(':checked')) {
                $('#btnSubmit').attr("disabled", false);
            }else{
                $('#btnSubmit').attr("disabled", true);  
            }

            if ($('.item_to_receive_id:checked').length == 0) {
                $('#btnSubmit').attr("disabled", true); 
            }

            $('#good'+id).attr("disabled", true);
            $('#good'+id).not(this).prop('checked', false); 

            $('#defective'+id).attr("disabled", true);
            $('#defective'+id).not(this).prop('checked', false); 
           
            $('#comments'+id).attr("disabled", true);
                    
        }else{
            count_pick++;
            if ($('.item_to_receive_id:checked').length == $('.item_to_receive_id').length) {
                if ($('.good:checked').length == $('.good').length) {
                    $('#btnSubmit').attr("disabled", false);
                }else if($('.item_to_receive_id:checked').length == $('.good:checked').length + $('.defective:checked').length){
                    $('#btnSubmit').attr("disabled", false);
                }else{
                    $('#btnSubmit').attr("disabled", true);  
                }
            }else{
                if ($('#good'+id).is(':checked').length) {
                    $('#btnSubmit').attr("disabled", false);
                }else{
                    $('#btnSubmit').attr("disabled", true);  
                }
            }
         
            $('#good'+id).removeAttr("disabled");
            $('#defective'+id).removeAttr("disabled");
            $('#comments'+id).removeAttr("disabled");
        }

    });

    var a = 0;
    var alreadyAdded = [];
    $('.good').change(function() {
        var asset_code = $(this).val();
        var id = $(this).attr("data-id");
        $("#defective_text"+id).val("0");
        var ischecked= $(this).is(':checked');
        if(ischecked == false){
            $(".comment_div").html("");
            $("#good_text"+id).val("0");
            count_pick--;

            if ($('.item_to_receive_id:checked').length == $('.item_to_receive_id').length) {
                if ($('.good:checked').length == $('.good').length) {
                    $('#btnSubmit').attr("disabled", false);
                }else if($('.item_to_receive_id:checked').length == $('.good:checked').length + $('.defective:checked').length){
                    $('#btnSubmit').attr("disabled", false);
                }else{
                    $('#btnSubmit').attr("disabled", true);  
                }
            }else{
                if(count_pick == 0){
                    $('#btnSubmit').attr("disabled", true);
                }   
                $('#btnSubmit').attr("disabled", true);  
            }
             
        }else{
            $("#good_text"+id).val("1");
            $('#defective'+id).not(this).prop('checked', false); 
            $.ajax({
            url: "{{ route('assets.get.comments') }}",
            dataType: "json",
            type: "POST",
            data: {
                "asset_code": asset_code
            },
            success: function (data) {
                var json = JSON.parse(JSON.stringify(data.items));
                if(data.items != null){
                    $.each(json, function (index, item) { 
                            var row = '<span class="text-comment" id="text-comment-id'+id+'">' + 
                                    '<p style="margin-top:5px"><strong>' + item.asset_code + 
                                    ':</strong>' + item.comments + 
                                    '</p>' + 
                                    '<p style="text-align:right; font-size:10px; font-style: italic; border-bottom:1px solid #d2d6de">' + item.created_at + 
                                    '</p></span>'
                                    ;
                    $(".comment_div").append(row);
                }); 
                }
                
                
            }
        });
            $("#good_text"+id).val("1");
            //$("#defective"+id).val("1");
            count_pick++;
            if ($('.item_to_receive_id:checked').length == $('.item_to_receive_id').length) {
                if ($('.good:checked').length == $('.good').length) {
                    $('#btnSubmit').attr("disabled", false);
                }else if($('.item_to_receive_id:checked').length == $('.good:checked').length + $('.defective:checked').length){
                    $('#btnSubmit').attr("disabled", false);
                }else{
                    $('#btnSubmit').attr("disabled", true);  
                }
            }else{
                if ($('.good:checked').length == $('.good').length) {
                    $('#btnSubmit').attr("disabled", false);
                }else if($('.item_to_receive_id:checked').length == $('.good:checked').length + $('.defective:checked').length){
                    $('#btnSubmit').attr("disabled", false);
                }else{
                    $('#btnSubmit').attr("disabled", true);  
                } 
            }
            //$('#btnSubmit').attr("disabled", false);
            
            
        }

    });


    $('.defective').change(function() {
        // $('.good').not(this).prop('checked', false);    
        var asset_code = $(this).val();
        var id = $(this).attr("data-id");
        $("#good_text"+id).val("0");
        var ischecked= $(this).is(':checked');
            if(ischecked == false){
                //$("#good"+id).val("0");
                $(".comment_div").html("");
                $("#defective_text"+id).val("0");
                count_pick--;

                if ($('.item_to_receive_id:checked').length == $('.item_to_receive_id').length) {
                    if ($('.defective:checked').length == $('.defective').length) {
                        $('#btnSubmit').attr("disabled", false);
                    }else if($('.item_to_receive_id:checked').length == $('.defective:checked').length + $('.good:checked').length){
                        $('#btnSubmit').attr("disabled", false);
                    }else{
                        $('#btnSubmit').attr("disabled", true);  
                    }
                }else{
                    if(count_pick == 0){
                        $('#btnSubmit').attr("disabled", true);
                    }   
                    $('#btnSubmit').attr("disabled", true);  
                }    
        
            }else{
                $("#defective_text"+id).val("1");
                $('#good'+id).not(this).prop('checked', false); 
                $.ajax({
                url: "{{ route('assets.get.comments') }}",
                dataType: "json",
                type: "POST",
                data: {
                    "asset_code": asset_code
                },
                success: function (data) {
                    var json = JSON.parse(JSON.stringify(data.items));
                    if(data.items != null){
                        $.each(json, function (index, item) { 
                                var row = '<span class="text-comment" id="text-comment-id'+id+'">' + 
                                        '<p style="margin-top:5px"><strong>' + item.asset_code+ 
                                        ':</strong>' + item.comments + 
                                        '</p>' + 
                                        '<p style="text-align:right; font-size:10px; font-style: italic; border-bottom:1px solid #d2d6de">' + item.created_at + 
                                        '</p></span>'
                                        ;
                        $(".comment_div").append(row);
                    }); 
                    }
                    
                    
                }
            });
                //$("#good"+id).val("1");

                $("#defective_text"+id).val("1");

                count_pick++;

                if ($('.item_to_receive_id:checked').length == $('.item_to_receive_id').length) {
                    if ($('.defective:checked').length == $('.defective').length) {
                        $('#btnSubmit').attr("disabled", false);
                    }else if($('.item_to_receive_id:checked').length == $('.defective:checked').length + $('.good:checked').length){
                        $('#btnSubmit').attr("disabled", false);
                    }else{
                        $('#btnSubmit').attr("disabled", true);  
                    }
                }else{ 
                    if ($('.good:checked').length == $('.good').length) {
                        $('#btnSubmit').attr("disabled", false);
                    }else if($('.item_to_receive_id:checked').length == $('.defective:checked').length + $('.good:checked').length){
                        $('#btnSubmit').attr("disabled", false);
                    }else{
                        $('#btnSubmit').attr("disabled", true);  
                    } 
                }    
                
                
            }

    });

    $('.comments').each(function(){
        var eachData = this.value;
        count_pick++;
        //other comment
        $('#comments'+count_pick).change(function(){
            var other_id = $(this).attr("data-id");
            var value =  this.value;
            var allselected = [];
            $(".comments :selected").each(function() {
            allselected.push(this.value.toLowerCase().replace(/\s/g, ''));
            });
            var splitData = this.value.split('|');
            var asset_code = splitData[0];
            var digits_code = splitData[1];
            var concat_asset_digits_code = asset_code.concat('|',digits_code);
            var others = concat_asset_digits_code.concat('|', "others").toLowerCase().replace(/\s/g, '');
        
            if($.inArray(others,allselected) > -1){
                $('#others'+other_id).show();
            }else{
                $('#others'+other_id).hide();
                $('#others'+other_id).hide();
                $('#inputValue'+other_id).val("");
            }

        });
    });

    $('#btnSubmit').click(function(event) {
        event.preventDefault();
        swal({
            title: "Are you sure?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#41B314",
            cancelButtonColor: "#F9354C",
            confirmButtonText: "Yes, verify it!",
            width: 450,
            height: 200
            }, function () {
                $(this).attr('disabled','disabled');
                $('#verificationForm').submit();                                                  
        });
    });

</script>
@endpush