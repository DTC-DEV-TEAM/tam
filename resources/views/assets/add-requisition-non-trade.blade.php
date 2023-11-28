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
                <label class="require control-label col-md-2">*{{ trans('message.form-label.purpose') }}</label>
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
                    <div class="box-header text-center">
                        <h3 class="box-title"><b>{{ trans('message.form-label.asset_items') }}</b></h3>
                    </div>
                    <div class="box-body no-padding">
                        <div class="table-responsive">
                            <div class="pic-container">
                                <div class="pic-row">
                                    <table class="table table-bordered" id="asset-items">
                                      <thead>
                                        <tr class="tbl_header_color dynamicRows">
                                            <th width="30%" class="text-center">*{{ trans('message.table.item_description') }}</th>
                                            <th width="20%" class="text-center">Tasteless Code</th>
                                            <th width="25%" class="text-center">{{ trans('message.table.category_id_text') }}</th>                                                                                                                    
                                            <th width="20%" class="text-center">{{ trans('message.table.sub_category_id_text') }}</th> 
                                            <th width="15%" class="text-center"> Wh Quantity</th>
                                            <th width="15%" class="text-center"> Unserved Quantity</th> 
                                            <th width="7%" class="text-center">*Request Qty</th> 
                                            <th width="5%" class="text-center">{{ trans('message.table.action') }}</th>
                                        </tr>
                                      </thead>

                                        <tbody>
                        
                                        </tbody>

                                        <tfoot>
                                            <tr id="tr-table1" class="bottom">
                                                <td colspan="6">
                                                    <input type="button" id="add-Row" name="add-Row" class="btn btn-success add" value='Add Item' />
                                                </td>
                                                <td align="left" colspan="1">
                                                    <input type='number' name="quantity_total" class="form-control text-center" id="quantity_total" readonly>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12" style="margin-top: 10px">
                    <div class="form-group">
                        <label>{{ trans('message.table.note') }}</label>
                        <textarea placeholder="{{ trans('message.table.comments') }} ..." rows="3" class="form-control finput" name="requestor_comments"></textarea>
                    </div>
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
    $(document).ready(function() {
        function preventBack() {
            window.history.forward();
        }
         window.onunload = function() {
            null;
        };
        setTimeout("preventBack()", 0);
       
        var tableRow = 1;
        $("#add-Row").click(function() {
            const isAddRow = true;

            tableRow++;

            if(isAddRow == true){
                addRow();
            }
        });

        function addRow(){
            tableRow++;
            var newrow =`
                    <tr>
                        <td>
                        <input type="text" placeholder="Search Item ..." class="form-control finput itemDesc" id="itemDesc${tableRow}" data-id="${tableRow}"   name="item_description[]"  required maxlength="100"> 
                          <ul class="ui-autocomplete ui-front ui-menu ui-widget ui-widget-content" data-id="${tableRow}" id="ui-id-2${tableRow}" style="display: none; top: 60px; left: 15px; width: 100%;"> 
                           <li>Loading...</li> 
                          </ul> 
                         <div id="display-error${tableRow}"></div>
                        </td>
                        <td>  
                            <input type="text" onkeyup="this.value = this.value.toUpperCase();" class="form-control digits_code finput" data-id="${tableRow}" id="digits_code${tableRow}"  name="digits_code[]"   maxlength="100" readonly> 
                            <input type="hidden" onkeyup="this.value = this.value.toUpperCase();" class="form-control fixed_description finput" data-id="${tableRow}" id="fixed_description${tableRow}"  name="fixed_description[]"   maxlength="100" readonly> 
                        </td> 
                        <td>  
                            <input type="text" onkeyup="this.value = this.value.toUpperCase();" class="form-control text-center category_id sinput" data-id="${tableRow}" id="category_id${tableRow}"  name="category_id[]"   maxlength="100" readonly> 
                        </td> 
                        <td>  
                            <input type="text" onkeyup="this.value = this.value.toUpperCase();" class="form-control text-center sub_category_id sinput" data-id="${tableRow}" id="sub_category_id${tableRow}"  name="sub_category_id[]"   maxlength="100" readonly> 
                        </td> 
   
                        <td>
                            <input class="form-control text-center sinput wh_quantity" type="text" required name="wh_quantity[]" id="wh_quantity${tableRow}" data-id="${tableRow}" readonly>
                        </td> 
                        
                        <td>
                            <input class="form-control text-center sinput unserved_quantity" type="text" required name="unserved_quantity[]" id="unserved_quantity${tableRow}" data-id="${tableRow}" readonly>
                        </td>      
                        
                        <td>
                            <input class="form-control text-center quantity_item" type="number" required name="quantity[]" id="quantity${tableRow}" data-id="${tableRow}"  value="1" min="0" max="9999999999" step="any" onKeyPress="if(this.value.length==4) return false;" oninput="validity.valid;" readonly>
                        </td> 
                             
                        <td> 
                            <button id="deleteRow${tableRow}" name="removeRow" data-id="${tableRow}" class="btn btn-danger removeRow"><i class="glyphicon glyphicon-trash"></i></button> 
                        </td> 

                    </tr>
                    `;
                    $('#asset-items tbody').append(newrow);
        }
    });
    </script>
@endpush