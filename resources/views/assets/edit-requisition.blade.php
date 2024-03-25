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
                background-color: #eee;
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

            .ui-state-focus {
                background: none !important;
                background-color: #00a65a !important;
                border: 1px solid rgb(255, 254, 254) !important;
                color: #fff !important;
            }

            #asset-items th, td {
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
        Fill up IT asset form
    </div>

    <form action="{{ CRUDBooster::mainpath('add-save') }}" method="POST" id="AssetRequest" enctype="multipart/form-data">
        <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
        <input type="hidden" value="1" name="request_type_id" id="request_type_id">

        <div class='panel-body'>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label require">{{ trans('message.form-label.employee_name') }}</label>          
                        <input type="text" class="form-control finput"  id="employee_name" name="employee_name"  required readonly value="{{$employeeinfos->bill_to}}"> 
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label require">{{ trans('message.form-label.company_name') }}</label>
                        <input type="text" class="form-control finput"  id="company_name" name="company_name"  required readonly value="{{$employeeinfos->company_name_id}}">                                   
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label require">{{ trans('message.form-label.department') }}</label>
                        <input type="text" class="form-control finput"  id="department" name="department"  required readonly value="{{$employeeinfos->department_name}}">
                    </div>

                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label require">{{ trans('message.form-label.position') }}</label>
                        <input type="text" class="form-control finput"  id="position" name="position"  required readonly value="{{$employeeinfos->position_id}}">                                   
                    </div>
                </div>
            </div>

            @if(CRUDBooster::myPrivilegeId() == 8)
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label require">{{ trans('message.form-label.store_branch') }}</label>
                            
                            <input type="text" class="form-control finput"  id="store_branch" name="store_branch"  required readonly value="{{$stores->store_name}}"> 
                            <input type="hidden" class="form-control"  id="store_branch_id" name="store_branch_id"  required readonly value="{{$stores->id}}"> 

                        </div>
                    </div>
                </div>
            @endif
            <hr/>

            <div class="row"> 
                <label class="require control-label col-md-2"><span style="color:red">*</span>{{ trans('message.form-label.purpose') }}</label>
                    @foreach($purposes as $data)
                    
                        @if($data->id == 1)
                                    <div class="col-md-5">
                                        <label class="radio-inline control-label col-md-5" ><input type="radio" required   class="purpose" name="purpose" value="{{$data->id}}" >{{$data->request_description}}</label>
                                        <br>
                                    </div>
                            @else
                                    <div class="col-md-5">
                                        <label class="radio-inline control-label col-md-5"><input type="radio" required  class="purpose" name="purpose" value="{{$data->id}}" >{{$data->request_description}}</label>
                                        <br>
                                    </div>
                        @endif

                    @endforeach
            </div>

            <hr/>

            <div class="row">
                <div class="col-md-12">
                    <div class="box-body no-padding">
                        <div class="table-responsive">
                            <div class="pic-container">
                                <div class="pic-row">
                                    <table id="asset-items">
                                        <tbody id="bodyTable">
                                            <tr style="background-color:#00a65a; border: 0.5px solid #000;">
                                                <th style="text-align: center" colspan="11"><h4 class="box-title" style="color: #fff;"><b>{{ trans('message.form-label.asset_items') }}</b></h4></th>
                                            </tr>
                                            <tr class="tbl_header_color dynamicRows">
                                                <th width="25%" class="text-center"><span style="color:red">*</span>{{ trans('message.table.item_description') }}</th>
                                                <th width="15%" class="text-center">{{ trans('message.table.digits_code') }}</th>
                                                <th width="15%" class="text-center">{{ trans('message.table.category_id_text') }}</th>                                                                                                                    
                                                <th width="15%" class="text-center">{{ trans('message.table.sub_category_id_text') }}</th> 
                                                <th width="7%" class="text-center">{{ trans('message.table.wh_qty') }}</th>
                                                <th width="7%" class="text-center">{{ trans('message.table.prev_balance_quantity') }}</th> 
                                                <th width="7%" class="text-center">{{ trans('message.table.request_qty') }}</th> 
                                                <th width="15%" class="text-center">{{ trans('message.table.budget_range') }}</th> 
                                                <th width="5%" class="text-center">{{ trans('message.table.action') }}</th>
                                            </tr>

                                            <tr id="tr-table">
                                                <tr>
                                
                                                </tr>
                                            </tr>
                                        
                                        </tbody>

                                        <tfoot>
                                            <tr id="tr-table1" class="bottom">
                                                <td colspan="6">
                                                    <a type="button" id="add-Row" name="add-Row" class="btn btn-success add"> <i class="fa fa-plus-circle"></i> Add Item</a>
                                                </td>
                                                <td align="left" colspan="1">
                                                    <input type='number' name="quantity_total" class="form-control text-center" id="quantity_total" readonly>
                                                </td>
                                                <td colspan="2"></td>
                                            </tr>
                                        </tfoot>

                                    </table>
                                </div>
                            </div>
                    
                        </div>
                    </div>
                    <br>
                    <label class="checkbox-inline mt-2 control-label col-md-12"><input type="checkbox" id="checkApplications"> <span style="font-style: italic"> Applications for Laptop or Desktop</span></label>      
                </div>

                <div class="col-md-12 mt-2" id="application_div">
                    <hr/>
                    
                    <div class="row"> 
                        <label class="require control-label col-md-2" required>*{{ trans('message.form-label.application') }}</label>
                            @foreach($applications as $data)
                                <div class="col-md-2">
                                    <label class="checkbox-inline control-label col-md-12"><input type="checkbox"  class="application" id="{{$data->app_name}}" name="application[]" value="{{$data->app_name}}" >{{$data->app_name}}</label>
                                    <br>
                                </div>
                            @endforeach
                    </div>
                    <hr/>
                </div>

                <div class="col-md-12" id="application_others_div">
                    <div class="row">
                        <label class="require control-label col-md-2">*{{ trans('message.form-label.application_others') }}</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control"  id="application_others" name="application_others"  required placeholder="e.g. VIBER, WHATSAPP, TELEGRAM" onkeyup="this.value = this.value.toUpperCase();">
                        </div>
                    </div>
                    <hr/>
                </div>

                <div class="col-md-12" style="margin-top: 10px">
                    <div class="form-group">
                        <label>{{ trans('message.table.note') }}</label>
                        <textarea placeholder="{{ trans('message.table.comments') }} ..." rows="3" class="form-control finput" name="requestor_comments"></textarea>
                    </div>
                </div>
            </div>
            <hr>
            <div class="col-md-12">
                <div class="form-group text-center">
                    <label>CAN'T FIND WHAT YOU ARE LOOKING FOR?</label>
                    <a href='{{CRUDBooster::adminpath("header_request/download")."?return_url=".urlencode(Request::fullUrl())}}'>CHECK HERE</a>
                </div>
            </div>
        </div>

        <div class='panel-footer'>

            <a href="{{ CRUDBooster::mainpath() }}" class="btn btn-default">{{ trans('message.form.cancel') }}</a>

            <button class="btn btn-success pull-right" type="submit" id="btnSubmit"> <i class="fa fa-save" ></i> {{ trans('message.form.save') }}</button>

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
        
        var tableRow = 1;

    </script>
@endpush