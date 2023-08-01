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
            #asset-items th, td, tr {
                border: 1px solid rgba(000, 0, 0, .5);
                padding: 8px;
            }
            .select2-results .select2-disabled {
                display:none;
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
        Request Form
    </div>

    <form method='post' id="myform" action='{{CRUDBooster::mainpath('edit-save/'.$HeaderID->id)}}'>
        <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
        <input type="hidden" value="0" name="action" id="action">

        <div class='panel-body'>

            <div class="row">                           
                <label class="control-label col-md-2">{{ trans('message.form-label.reference_number') }}:</label>
                <div class="col-md-4">
                        <p>{{$Header->reference_number}}</p>
                </div>
                <label class="control-label col-md-2">{{ trans('message.form-label.created_at') }}:</label>
                <div class="col-md-4">
                        <p>{{$Header->created}}</p>
                </div>
            </div>

            <div class="row">                           
                <label class="control-label col-md-2">{{ trans('message.form-label.employee_name') }}:</label>
                <div class="col-md-4">
                        <p>{{$Header->employee_name}}</p>
                </div>

                <label class="control-label col-md-2">{{ trans('message.form-label.company_name') }}:</label>
                <div class="col-md-4">
                        <p>{{$Header->company_name}}</p>
                </div>
            </div>

            <div class="row">                           
                <label class="control-label col-md-2">{{ trans('message.form-label.department') }}:</label>
                <div class="col-md-4">
                        <p>{{$Header->department}}</p>
                </div>

                <label class="control-label col-md-2">{{ trans('message.form-label.position') }}:</label>
                <div class="col-md-4">
                        <p>{{$Header->position}}</p>
                </div>
            </div>

            @if($Header->store_branch != null || $Header->store_branch != "")
                <div class="row">                           
                    <label class="control-label col-md-2">{{ trans('message.form-label.store_branch') }}:</label>
                    <div class="col-md-4">
                            <p>{{$Header->store_branch}}</p>
                    </div>
                </div>
            @endif

            @if($Header->if_from_erf != null || $Header->if_from_erf != "")
                <div class="row">                           
                    <label class="control-label col-md-2">Erf Number:</label>
                    <div class="col-md-4">
                            <p>{{$Header->if_from_erf}}</p>
                    </div>
                </div>
            @endif

            <hr/>

            <div class="row">                           
                <label class="control-label col-md-2">{{ trans('message.form-label.purpose') }}:</label>
                <div class="col-md-4">
                        <p>{{$Header->request_description}}</p>
                </div>
            </div>
  
            @if($Header->requestor_comments != null || $Header->requestor_comments != "")
                <hr/>
                <div class="row">                           
                    <label class="control-label col-md-2">{{ trans('message.table.requestor_comments') }}:</label>
                    <div class="col-md-10">
                            <p>{{$Header->requestor_comments}}</p>
                    </div>   
                </div>
            @endif  

            <hr />

            <div class="row">
                <div class="col-md-12">
                    <div class="box-header text-center">
                        <h3 class="box-title"><b>{{ trans('message.form-label.asset_items') }}</b></h3>
                    </div>
                    <div class="box-body no-padding">
                        <div class="pic-container">
                            <div class="pic-row">
                                <table id="asset-items">
                                    <tbody id="bodyTable">
                                        <tr class="tbl_header_color dynamicRows">
                                            <!-- <th width="13%" class="text-center">{{ trans('message.table.action') }}</th>  -->
                                            {{-- <th width="5%" class="text-center">Good</th> 
                                            <th width="5%" class="text-center">Defective</th> --}}
                                            <th width="10%" class="text-center">{{ trans('message.table.mo_reference_number') }}</th>
                                            <!-- <th width="13%" class="text-center">{{ trans('message.table.status_id') }}</th> -->
                                            <th width="8%" class="text-center">{{ trans('message.table.digits_code') }}</th>
                                            <th width="8%" class="text-center">{{ trans('message.table.asset_tag') }}</th>
                                            <th width="20%" class="text-center">{{ trans('message.table.item_description') }}</th>
                                            <th width="8%" class="text-center">{{ trans('message.table.serial_no') }}</th>
                                            <th width="4%" class="text-center">{{ trans('message.table.item_quantity') }}</th>
                                            <!-- <th width="8%" class="text-center">{{ trans('message.table.item_cost') }}</th>
                                            <th width="16%" class="text-center">{{ trans('message.table.item_total_cost') }}</th>
                                            -->
                                            {{-- <th width="8%">Comments</th> --}}
                                            <th width="8%">Asset Code Tagging</th>
                                        </tr>
                                        
                                        <?php   $tableRow1 = 0; ?>

                                        <?Php   $item_count = 0; ?>

                                        @if( !empty($MoveOrder) )
                                            
                                            @foreach($MoveOrder as $rowresult)

                                                <?php   $tableRow1++; ?>

                                                <?Php $item_count++; ?>

                                                <tr>
                                                    {{-- <td style="text-align:center" height="10"> --}}
                                                        <input type="hidden" value="{{$rowresult->id}}" name="item_id[]">
                                                        <input type="hidden" value="{{$rowresult->body_request_id}}" name="body_id[]">
                                                        <input type="hidden" name="good_text[]" id="good_text{{$tableRow1}}" value="0" />

                                                        {{-- <input type="checkbox" name="good[]" id="good{{$tableRow1}}" class="good" required data-id="{{$tableRow1}}" value="{{$rowresult->asset_code}}"/> --}}
                                                        <!-- for good and defect comment -->
                                                        <input type="hidden" name="arf_number" id="arf_number" value="{{$Header->reference_number}}" />
                                                        <input type="hidden" name="digits_code[]" id="digits_code{{$tableRow1}}" value="{{$rowresult->digits_code}}" />
                                                        <input type="hidden" name="asset_code[]" id="asset_code{{$tableRow1}}" value="{{$rowresult->asset_code}}" />
                                                    {{-- </td> --}}

                                                    {{-- <td style="text-align:center" height="10"> --}}
                                                        <input type="hidden" name="defective_text[]" id="defective_text{{$tableRow1}}" value="0" />
                                                        {{-- <input type="checkbox" name="defective[]" id="defective{{$tableRow1}}" class="defective" required data-id="{{$tableRow1}}"  value="{{$rowresult->asset_code}}"/> --}}
                                                    {{-- </td> --}}

                                                    <td style="text-align:center" height="10">
                                                        {{$rowresult->mo_reference_number}}
                                                    </td>

                                                    <td style="text-align:center" height="10">
                                                        {{$rowresult->digits_code}}
                                                    </td>

                                                    <td style="text-align:center" height="10">
                                                        {{$rowresult->asset_code}}
                                                    </td>

                                                    <td style="text-align:center" height="10">
                                                        {{$rowresult->item_description}}
                                                    </td>

                                                    <td style="text-align:center" height="10">
                                                        {{$rowresult->serial_no}}
                                                    </td>

                                                    <td style="text-align:center" height="10">
                                                        {{$rowresult->quantity}}
                                                    </td>
                  
                                                    {{-- <td>
                                                        <select required selected data-placeholder="Select Comments" id="comments{{$tableRow1}}" data-id="{{$tableRow1}}" name="comments[]" class="form-select select2 comments" style="width:100%;" multiple="multiple">
                                                            @foreach($good_defect_lists as $good_defect_list)
                                                                <option value=""></option>
                                                                <option value="{{ $rowresult->asset_code. '|' .$rowresult->digits_code. '|' .$good_defect_list->defect_description }}">{{ $good_defect_list->defect_description }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td> --}}

                                                    <td>
                                                        <select required selected data-placeholder="Tag Asset Code" id="asset_code_tag{{$tableRow1}}" data-id="{{$tableRow1}}" name="asset_code_tag[]" class="form-select asset_code_tag" style="width:100%;">
                                                            @foreach($assets_code as $asset_code)
                                                                <option value=""></option>
                                                                <option value="{{ $asset_code->id }}">{{ $asset_code->asset_code }} | {{ $asset_code->digits_code }}</option>
                                                            @endforeach
                                                        </select>
                                                        </td>
                                                    
                                                </tr>

                                                <tr id="others{{$tableRow1}}" style="display:none">
                                                <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                                <td><input type="text" class="form-control" placeholder="Please input other comments" id="inputValue{{$tableRow1}}" name="other_comment[]"></td>                                                   
                                                </tr>

                                                <?Php $cost_total = $rowresult->total_unit_cost; ?>

                                            @endforeach


                                        @endif
                                    
                                    </tbody>
                                    
                                    <tfoot>

                                        <tr id="tr-table1" class="bottom">

                                            <!-- <td colspan="6" align="right">
                                                <label>Total Qty:</label>

                                                
                                            </td> 
                                            <td align="center" colspan="1">
                                                
                                                <label>{{$Header->quantity_total}}</label>

                                            </td> -->
                                                    
                                            <!-- 
                                            <td align="right" colspan="8">
                                                <label>Total Cost:</label>
                                            </td>

                                            <td align="center">
                                                @if($item_count == 1)
                                                    <label>{{$cost_total}}</label>
                                                @else
                                                    <label>{{$Header->total}}</label>
                                                @endif
                                            </td>
                                            -->
                                        </tr>
                                    </tfoot>

                                </table>
                            </div>
                        </div>            
                    </div>
                </div>
            </div>

            <hr />

            @if($Header->application != null || $Header->application != "")
               
                <div class="form-group">                          
                    <label class="control-label col-md-2">{{ trans('message.form-label.application') }}:</label>
                    <div class="col-md-4">
                            <p>{{$Header->application}}</p>
                    </div>
                    
                    @if($Header->application_others != null || $Header->application_others != "")
                        <label class="control-label col-md-2">{{ trans('message.form-label.application_others') }}:</label>
                        <div class="col-md-4">
                                <p>{{$Header->application_others}}</p>
                        </div>
                    @endif  

                </div>

            @endif 
           
             <!-- Comment Section -->
            {{-- <div class="row">  
                <div class="col-md-12">
                <hr>
                    <div class="form-group">
                        <label>Comments:</label>
                        <div class="comment_div">
                        
                            <!-- <span class="text-comment">
                                <p style="margin-top:5px"><strong> </strong>   </p>
                                <p style="text-align:right; font-size:10px; font-style: italic; border-bottom:1px solid #d2d6de"> {{ $comment->created_at }} </p>
                            </span> -->
                      
                        </div>
                        <br>
                        
                        <!-- <textarea placeholder="Comment ..." rows="3" class="form-control" name="comments"></textarea> -->
                    </div>
                 </div>
            </div>   --}}
        </div>
      

        <div class='panel-footer'>
            <a href="{{ CRUDBooster::mainpath() }}" class="btn btn-default">{{ trans('message.form.cancel') }}</a>    
            <button class="btn btn-primary pull-right" type="submit" id="btnSubmit"> <i class="fa fa-circle-o" ></i> {{ trans('message.form.pick') }}</button>
        </div>
    </form>
</div>

@endsection
@push('bottom')
<script type="text/javascript">
    $(function(){
        $('body').addClass("sidebar-collapse");
    });

    $('.select2').select2({
    placeholder_text_single : "-- Select --",
    multiple: true});

    $('.asset_code_tag').select2({allowClear: true});
    var searchfield = $(this).find('.select2-search--inline');
    var selection = $(this).find('.select2-selection__rendered');
    $(this).find('.select2-search__field').html("");
    selection.prepend(searchfield);
    function preventBack() {
        window.history.forward();
    }
    window.onunload = function() {
        null;
    };
    setTimeout("preventBack()", 0);

    //$('#btnSubmit').attr("disabled", true);

    var count_pick = 0;

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
            //$("#defective"+id).val("0");
            count_pick--;
            if(count_pick == 0){
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
            // $("#good_text"+id).val("1");
            //$("#defective"+id).val("1");
            count_pick++;
            $('#btnSubmit').attr("disabled", false);
        }

    });


    $('.defective').change(function() {
        // $('.good').not(this).prop('checked', false);    
        var asset_code = $(this).val();
        var id = $(this).attr("data-id");
        $("#good_text"+id).val("0");
        var ischecked= $(this).is(':checked');
            if(ischecked == false){

                
                $(".comment_div").html("");
                $("#defective_text"+id).val("0");

                count_pick--;

                if(count_pick == 0){
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

                // $("#defective_text"+id).val("1");

                count_pick++;

                $('#btnSubmit').attr("disabled", false);
            }

    });

    $(document).ready(function () {
        var $selects = $('select');
        $selects.select2();
        $('.asset_code_tag').change(function () {
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
        //each value validation
        $('.asset_code_tag').each(function() {
            asset_code = $(this).val();
            if (asset_code == null) {
                swal({
                    type: 'error',
                    title: 'Asset Code Tagging Required',
                    icon: 'error',
                    confirmButtonColor: "#367fa9",
                }); 
                event.preventDefault(); // cancel default behavior
            } else if (asset_code == "") {
                swal({
                    type: 'error',
                    title: 'Asset Code Tagging Required',
                    icon: 'error',
                    confirmButtonColor: "#367fa9",
                }); 
                event.preventDefault(); // cancel default behavior
            }else{
                swal({
                    title: "Are you sure?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#41B314",
                    cancelButtonColor: "#F9354C",
                    confirmButtonText: "Yes, pick it!",
                    width: 450,
                    height: 200
                    }, function () {
                        $(this).attr('disabled','disabled');
                        $('#myform').submit();                                                  
                });
            }
        });
    });

</script>
@endpush