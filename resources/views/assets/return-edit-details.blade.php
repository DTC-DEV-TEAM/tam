@extends('crudbooster::admin_template')
@section('content')
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
            table, th, td {
            border: 1px solid rgba(000, 0, 0, .5);
            padding: 8px;
            }
            #asset-items th, td, tr {
                border: 1px solid rgba(000, 0, 0, .5);
                padding: 8px;
            }

            .ui-state-focus {
                background: none !important;
                background-color: #00a65a !important;
                border: 1px solid rgb(255, 254, 254) !important;
                color: #fff !important;
            }
        </style>
    @endpush
@if(g('return_url'))
	<p class="noprint"><a title='Return' href='{{g("return_url")}}'><i class='fa fa-chevron-circle-left '></i> &nbsp; {{trans("crudbooster.form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}}</a></p>       
@else
	<p class="noprint"><a title='Main Module' href='{{CRUDBooster::mainpath()}}'><i class='fa fa-chevron-circle-left '></i> &nbsp; {{trans("crudbooster.form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}}</a></p>       
@endif
<div class='panel panel-default'>
    <div class='panel-heading'>
        Edit Form
    </div>
    <form action="{{ route('editReturnAssets')}}" method="POST" id="EditAssetReturnRequest" enctype="multipart/form-data">
        <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
        <input type="hidden" name="header_id" id="header_id" value="{{$Header->requestid}}">
        <div class='panel-body'>

            <div class="row">                           
                <label class="control-label col-md-2">{{ trans('message.form-label.employee_name') }}:</label>
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
            <hr>
            <div class="row">
                <label class="control-label col-md-2">Request type:</label>
                <div class="col-md-4">
                        <p>{{$Header->request_type}}</p>
                </div> 
               
                <label class="control-label col-md-2">Purpose:</label>
                <div class="col-md-4">
                    <select id="purpose" name="purpose" class="form-select" style="width:100%;">
                        @foreach($purposes as $res)
                        <option value="{{ $res->description }}"
                            {{ isset($Header->purpose) && $Header->purpose == $res->description ? 'selected' : '' }}>
                            {{ $res->description }} 
                        </option>>
                        @endforeach
                    </select>
                </div>                    
            </div>
            @if($Header->transfer_to != null)    
                <div class="row">
                    <label class="control-label col-md-2">Transfer to:</label>
                    <div class="col-md-4">
                        <select id="users_id" name="users_id" class="form-select" style="width:100%;">
                            @foreach($users as $transfer)
                            <option value="{{ $transfer->id }}"
                                {{ isset($Header->transfer_to) && $Header->transfer_to == $transfer->id ? 'selected' : '' }}>
                                {{ $transfer->name }} 
                            </option>>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif

            <hr/>
        
            <table  class='table' id="asset-items">
                <thead>
                    <tr style="background-color:#00a65a; border: 0.5px solid #000;">
                        <th style="text-align: center" colspan="16"><h4 class="box-title" style="color: #fff;"><b>Item details</b></h4></th>
                    </tr>
                    <tr>
                        <th width="15%" class="text-center">Asset Code</th>
                        <th width="15%" class="text-center">Digits Code</th>
                        <th width="25%" class="text-center">{{ trans('message.table.item_description') }}</th>
                        <th width="25%" class="text-center">Asset Type</th>        
                        <td width="10%" style="text-align:center"><i class="fa fa-trash"></i></td>                                                     
                    </tr>
                </thead>
                <tbody>
                    @foreach($return_body as $rowresult)
                        <tr>
                            <td style="text-align:center" height="10">{{$rowresult->asset_code}}</td>
                            <td style="text-align:center" height="10">{{$rowresult->digits_code}}</td>
                            <td style="text-align:center" height="10">{{$rowresult->description}}</td>
                            <td style="text-align:center" height="10">{{$rowresult->asset_type}}</td>
                            <td style="text-align:center" height="10">
                                <button id="deleteRowData{{$tableRow}}" value="{{$rowresult->body_id}}" name="deleteRowData" data-id="{{$tableRow}}" class="btn btn-danger deleteRowData btn-sm" data-toggle="tooltip" data-placement="bottom" title="Cancel"><i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr id="tr-tableOption1" class="bottom">
                        <td style="text-align:left" colspan="6">
                            <button class="btn btn-success btn-sm"  id="add-Row" name="add-Row"> <i class="fa fa-plus-circle"></i> Add new item</button>
                        </td>
                    </tr>
                </tfoot>
            </table> 

      
            @if($Header->approvedby != null || $Header->approvedby != "")
            <hr/>
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
        
            @if( $Header->receivedby != null )
            <hr>
                <div class="row">                           
                    @if($Header->transfer_to == null)                        
                        <label class="control-label col-md-2">Transacted By:</label>
                        <div class="col-md-4">
                                <p>{{$Header->receivedby}}</p>
                        </div>
                        <label class="control-label col-md-2">Transacted Date:</label>
                        <div class="col-md-4">
                                <p>{{$Header->transacted_date}}</p>
                        </div>
                    @else
                        <label class="control-label col-md-2">Transferred To:</label>
                        <div class="col-md-4">
                                <p>{{$Header->receivedby}}</p>
                        </div>
                        <label class="control-label col-md-2">Transferred Date:</label>
                        <div class="col-md-4">
                                <p>{{$Header->transacted_date}}</p>
                        </div>
                    @endif
                </div>
            @endif
        
            @if( $Header->closedby != null )
            <hr>
                <div class="row">                           
                    <label class="control-label col-md-2">{{ trans('message.form-label.closed_by') }}:</label>
                    <div class="col-md-4">
                            <p>{{$Header->closedby}}</p>
                    </div>
                    <label class="control-label col-md-2">{{ trans('message.form-label.closed_at') }}:</label>
                    <div class="col-md-4">
                            <p>{{$Header->close_at}}</p>
                    </div>
                </div>
            @endif
            
            </div>
            <div class='panel-footer'>
                <a href="{{ CRUDBooster::mainpath() }}" class="btn btn-default">{{ trans('message.form.back') }}</a>
                <button class="btn btn-success pull-right" type="submit" id="btnUpdate"> <i class="fa fa-refresh"></i>
                    {{ trans('message.form.update') }}</button>
            </div>
        </div>
    </form>
</div>

@endsection
@push('bottom')
<script type="text/javascript">
    var tableRow = 1;
    var token = $("#token").val();
    $(document).ready(function() {
        $('#purpose,#users_id').select2();
        //Add Row
        $("#add-Row").click(function() {
            event.preventDefault();
            var count_fail = 0;
            tableRow++;

            if(count_fail == 0){
                $('#add-Row').prop("disabled", false);
                $('#display_error').html("");
                addRow();
            }
        });
    
        function addRow(){
            $('.digits_code').each(function() {
                digits_code = $(this).val();
                if (digits_code == null || digits_code == "") {
                    swal({  
                        type: 'error',
                        title: 'Please fill all Fields!',
                        icon: 'error',
                        confirmButtonColor: "#367fa9",
                    });
                    count_fail++;

                }else{
                    count_fail = 0;
                }
            });
            $('.item_description').each(function() {
                item_description = $(this).val();
                if (item_description == null || item_description == "") {
                    swal({  
                        type: 'error',
                        title: 'Please fill all Fields!',
                        icon: 'error',
                        confirmButtonColor: "#367fa9",
                    });
                    count_fail++;

                }else{
                    count_fail = 0;
                }
            });
            $('.asset_type').each(function() {
                asset_type = $(this).val();
                if (asset_type == null || asset_type == "") {
                    swal({  
                        type: 'error',
                        title: 'Please fill all Fields!',
                        icon: 'error',
                        confirmButtonColor: "#367fa9",
                    });
                    count_fail++;

                }else{
                    count_fail = 0;
                }
            });
            var newrow =
                `<tr>
                    <td>
                        <input type="text" placeholder="Search item" class="form-control search_asset_code text-center" data-id="${tableRow}" id="search_asset_code${tableRow}"  name="asset_code[]" required maxlength="100">
                        <input type="hidden" name="mo_id[]" id="mo_id${tableRow}" class="id" required data-id="${tableRow}"/>
                            <ul class="ui-autocomplete ui-front ui-menu ui-widget ui-widget-content" data-id="${tableRow}" id="ui-id-2${tableRow}" style="display:none; top: 60px; left: 15px; width: 100%;">
                                <li>Loading...</li>
                            </ul>
                    </td>   
                    <td>
                        <input type="text" placeholder="Digits code" class="form-control digits_code text-center" data-id="${tableRow}" id="digits_code${tableRow}"  name="digits_code[]" readonly>
                    </td>  
                    <td>
                        <input type="text" placeholder="Item description" class="form-control item_description text-center" data-id="${tableRow}" id="item_description${tableRow}"  name="item_description[]" readonly>
                    </td>   
                    <td>
                        <input type="text" placeholder="Asset type" class="form-control asset_type text-center" data-id="${tableRow}" id="asset_type${tableRow}"  name="asset_type[]" readonly>
                    </td> 
                    <td style="text-align:center">
                        <button id="deleteRow" name="removeRow" data-id="${tableRow}" class="btn btn-danger btn-sm removeRow" ><i class="glyphicon glyphicon-trash"></i></button>
                    </td>
                </tr>`;
            $('#asset-items tbody').append(newrow);
            
            //Search item
            let countrow = 1;
            
            $(function(){
                countrow++;
                $('#search_asset_code'+tableRow).autocomplete({
                    source: function (request, response) {
                    $.ajax({
                        url: "{{ route('searchItem') }}",
                        dataType: "json",
                        type: "POST",
                        data: {
                            "_token": token,
                            "search": request.term
                        },
                        success: function (data) {
                        
                            if(data.items === null){
                                swal({  
                                    type: 'error',
                                    title: 'No Found Item',
                                    icon: 'error',
                                    confirmButtonColor: "#5cb85c",
                                });
                            }else{ 
                                if (data.status_no == 1) {
                                    var data = data.items;
                                    $('#ui-id-2'+tableRow).css('display', 'none');
                                    response($.map(data, function (item) {
                                        return {
                                            id:                 item.id,
                                            asset_code:         item.asset_code,
                                            digits_code:        item.digits_code,
                                            value:              item.item_description,
                                            asset_type:         item.asset_type
                                        }

                                    }));

                                } else {
                                    $('.ui-menu-item').remove();
                                    $('.addedLi').remove();
                                    $("#ui-id-2"+tableRow).append($("<li class='addedLi'>").text(data.message));
                                    var searchVal = $('#search_asset_code'+tableRow).val();
                                    if (searchVal.length > 0) {
                                        $("#ui-id-2"+tableRow).css('display', 'block');
                                    } else {
                                        $("#ui-id-2"+tableRow).css('display', 'none');
                                    }
                                }
                            }
                        }
                    })
                    },
                    select: function (event, ui) {
                        var e = ui.item;
                        if (e.id) {
                            $("#search_asset_code"+$(this).attr("data-id")).val(e.asset_code);
                            $('#search_asset_code'+$(this).attr("data-id")).attr('readonly','readonly');
                            $('#mo_id'+$(this).attr("data-id")).val(e.id);
                            $('#digits_code'+$(this).attr("data-id")).val(e.digits_code);
                            $('#item_description'+$(this).attr("data-id")).val(e.value);
                            $('#asset_type'+$(this).attr("data-id")).val(e.asset_type);
                            return false;

                        }
                    },

                    minLength: 1,
                    autoFocus: true
                });

            });
        }
        //deleteRow
        $(document).on('click', '.removeRow', function() {
            if ($('#asset-items tbody tr').length != 1) { //check if not the first row then delete the other rows
                tableRow--;
                $(this).closest('tr').remove();
                return false;
            }
        });

        $('#btnUpdate').click(function(event) {
            event.preventDefault();
            var item = $("input[name^='asset_code']").length;
            var item_value = $("input[name^='asset_code']");
            for(i=0;i<item;i++){
                if(item_value.eq(i).val() == 0 || item_value.eq(i).val() == null){
                    swal({  
                            type: 'error',
                            title: 'Please fill fields!',
                            icon: 'error',
                            confirmButtonColor: "#5cb85c",
                        });
                        event.preventDefault();
                        return false;
                } 
        
            } 
            swal({
                title: "Are you sure?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#41B314",
                cancelButtonColor: "#F9354C",
                confirmButtonText: "Yes, update it!",
                width: 450,
                height: 200
                }, function () {
                    $('#btnUpdate').attr('disabled',true);
                    $('#EditAssetReturnRequest').submit();                 
            });
        });

        $(".deleteRowData").click(function(event) {
            event.preventDefault();
            const id = $(this).val();
            swal({
                    title: "Are you sure?",
                    type: "warning",
                    text: "You won't be able to revert this!",
                    showCancelButton: true,
                    confirmButtonColor: "#41B314",
                    cancelButtonColor: "#F9354C",
                    confirmButtonText: "Yes, cancel it!"
                    }, function () {
                    $.ajax({
                        url: "{{ route('delete-line-return-assets') }}",
                        type: "POST",
                        dataType: 'json',
                        data: {
                            '_token': token,
                            'lineId': id
                        },
                        success: function (data) {
                            if (data.status == "success") {
                                swal({
                                    type: data.status,
                                    title: data.message,
                                });
                                setTimeout(function(){
                                    location.reload();
                                    }, 1000);
                                } else if (data.status == "error") {
                                swal({
                                    type: data.status,
                                    title: data.message,
                                });
                            }
                        }
                    }); 
            });
        });

    });

</script>
@endpush