@extends('crudbooster::admin_template')

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

    <form method='post' id="myform" action='{{CRUDBooster::mainpath('edit-save/'.$Header->requestid)}}'>
        <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
        <input type="hidden" value="0" name="action" id="action">

        <!-- Modal -->
        <div class="modal fade" id="search-items" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Item Search</h4>
                </div>
                
                    <div class="modal-body">
                            <div class='callout callout-info'>
                                    <h5>SEARCH FOR <label id="item_search"></label></h5>
                            </div>
                
            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">{{ trans('message.form-label.add_item1') }}</label>
                                        <input class="form-control auto" style="width:100%;" placeholder="Search Item" id="search">
                                        <ul class="ui-autocomplete ui-front ui-menu ui-widget ui-widget-content" id="ui-id-2" style="display: none; top: 60px; left: 15px; width: 420px;">
                                            <li>Loading...</li>
                                        </ul>
                                    </div>
                                </div>
                            </div> 
                            
                    </div>
                    <div class="modal-footer">
                        
                        <!-- <input type="submit" class="btn btn-success" id="upload-excel1" value="Upload Excel"> -->
                        <button type="button" class="btn btn-default" id="upload-close1" data-dismiss="modal">Close</button>
                    </div>
        
                
                </div>
            </div>
        </div>

        <div class='panel-body'>

            <div class="row">

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label require">{{ trans('message.form-label.po_number') }}</label>
                        <input type="text" class="form-control"  id="po_number" name="po_number"   value="{{$Header->po_number}}" >      
         
                        <p style="font: italic bold 12px/30px arial, arial;">Type N/A if not applicable</p>                         
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">

                   

                        <label class="control-label require">{{ trans('message.form-label.po_date') }}</label>
                        <div class="input-group date">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <!-- <input type='input' name='po_date' id="po_date" value="{{$Header->po_date}}"  onkeydown="return false"   autocomplete="off"  class='form-control' placeholder="yyyy-mm-dd" />    -->
                            <input type="text" class="form-control date" name="po_date" id="po_date" value="{{$Header->po_date}}" >
                        </div>
                        <p style="font: italic bold 12px/30px arial, arial;">Type N/A if not applicable</p> 
                    </div>

                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label require">{{ trans('message.form-label.quote_date') }}</label>
                        <div class="input-group date">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            
                            <!-- <input type='input' name='quote_date' id="quote_date" value="{{$Header->quote_date}}" onkeydown="return false"   autocomplete="off"  class='form-control' placeholder="yyyy-mm-dd" /> --> 
                            
                            <input type="text" class="form-control date" name="quote_date" id="quote_date" value="{{$Header->quote_date}}" >

                          </div>
                          <p style="font: italic bold 12px/30px arial, arial;">Type N/A if not applicable</p> 
                    </div>

                </div>

            </div>
            <!--
            <div class="row">

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label require">*{{ trans('message.form-label.dr_number') }}</label>
                        <input type="text" class="form-control"  id="dr_number" name="dr_number"  required value="{{$Header->dr_number}}">                                   
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label require">*{{ trans('message.form-label.employee_dr_date') }}</label>
                        <div class="input-group date">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type='input' name='employee_dr_date' id="employee_dr_date" value="{{$Header->employee_dr_date}}" onkeydown="return false" required  autocomplete="off"  class='form-control' placeholder="yyyy-mm-dd" />     
                          </div>
                    </div>

                </div>

            </div>
         
            <div class="row">

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label require">{{ trans('message.form-label.quote_date') }}</label>
                        <div class="input-group date">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type='input' name='quote_date' id="quote_date" value="{{$Header->quote_date}}" onkeydown="return false"   autocomplete="off"  class='form-control' placeholder="yyyy-mm-dd" />     
                          </div>
                    </div>

                </div>

            </div>
        -->

            <hr/>

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

            <hr/>

            <div class="row">                           
                <label class="control-label col-md-2">{{ trans('message.form-label.purpose') }}:</label>
                <div class="col-md-4">
                        <p>{{$Header->request_description}}</p>
                </div>

        
            </div>
            <!--
            <hr/>

            <div class="row">                           
                <label class="control-label col-md-2">{{ trans('message.form-label.condition') }}:</label>
                <div class="col-md-4">
                        <p>{{$Header->condition_description}}</p>
                </div>

        
            </div> -->

            @if($Header->requestor_comments != null || $Header->requestor_comments != "")
                <hr/>
                <div class="row">                           
                    <label class="control-label col-md-2">{{ trans('message.table.requestor_comments') }}:</label>
                    <div class="col-md-10">
                            <p>{{$Header->requestor_comments}}</p>
                    </div>

            
                </div>
            @endif  

            @if($Header->approvedby != null || $Header->approvedby != "")
            <hr/>

            <div class="row">                           


                <label class="control-label col-md-2">{{ trans('message.form-label.approved_by') }}:</label>
                <div class="col-md-4">
                        <p>{{$Header->approvedby}}</p>
                </div>

                <label class="control-label col-md-2">{{ trans('message.form-label.approved_at') }}:</label>
                <div class="col-md-4">
                        <p>{{$Header->approved_at}}</p>
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


            @if($Header->recommendedby != null || $Header->recommendedby != "")

                <hr/>
                <div class="row">                           
                    <label class="control-label col-md-2">{{ trans('message.form-label.recommended_by') }}:</label>
                    <div class="col-md-4">
                            <p>{{$Header->recommendedby}}</p>
                    </div>

                    <label class="control-label col-md-2">{{ trans('message.form-label.recommended_at') }}:</label>
                    <div class="col-md-4">
                            <p>{{$Header->recommended_at}}</p>
                    </div>
                </div>

            @endif 


            @if($Header->it_comments != null || $Header->it_comments != "")

                <div class="row">                           
                    <label class="control-label col-md-2">{{ trans('message.table.it_comments') }}:</label>
                    <div class="col-md-10">
                            <p>{{$Header->it_comments}}</p>
                    </div>
                </div>

            @endif 


            <hr/>                

            <div class="row">
                <div class="col-md-12">
                    <div class="box-header text-center">
                        <h3 class="box-title"><b>{{ trans('message.form-label.asset_reco') }}</b></h3>
                    </div>
                                <div class="box-body no-padding">
                                    <div class="table-responsive">
                                      

                                            <div class="pic-container">
                                                <div class="pic-row">
                                                    <table class="table table-bordered" id="asset-items1">
                                                        <tbody id="bodyTable">
                                                            <tr class="tbl_header_color dynamicRows">

                                                                <!--<th width="5%" class="text-center">{{ trans('message.table.action') }}</th>-->

                                                                <th width="20%" class="text-center">{{ trans('message.table.item_description') }}</th>
                                                                <th width="9%" class="text-center">{{ trans('message.table.category_id_text') }}</th>                                                         
                                                                <th width="15%" class="text-center">{{ trans('message.table.sub_category_id_text') }}</th> 
                                                                <th width="5%" class="text-center">{{ trans('message.table.quantity_text') }}</th> 

                                                                @if($Header->recommendedby != null || $Header->recommendedby != "")
                                                                    <th width="13%" class="text-center">{{ trans('message.table.recommendation_text') }}</th> 
                                                                    <th width="14%" class="text-center">{{ trans('message.table.reco_digits_code_text_mo') }}</th> 
                                                                    <th width="24%" class="text-center">{{ trans('message.table.reco_item_description_text_mo') }}</th>
                                                                @endif 
                                                            <!-- <th width="8%" class="text-center">{{ trans('message.table.image') }}</th> 
                                                                <th width="5%" class="text-center">{{ trans('message.table.action') }}</th> -->
                                                            </tr>
                                                            
                                                            <!--tr class="tableInfo">
                                                                <td colspan="6" align="right"><strong>{{ trans('message.table.total') }}</strong></td>
                                                                <td align="left" colspan="1">


                                                                    <input type='hidden' name="quantity_total" class="form-control text-center" id="quantity_total" readonly>

                                                                    <input type='hidden' name="cost_total" class="form-control text-center" id="cost_total" readonly>

                                                                    <input type='number' name="total" class="form-control text-center" id="total" readonly>
                                                                </td>
                                                                <td colspan="1"></td>
                                                            </tr> -->

                                                            <tr id="tr-table">
                                                                        <?php   $tableRow = 1; ?>
                                                                <tr>

                                                                    @foreach($Body as $rowresult)

                                                                        <?php   $tableRow++; ?>

                                                                        <tr>

                                                                            <!--
                                                                            <td>

                                                                                <input type="hidden"  class="form-control"  name="item_id[]" id="item_id{{$tableRow}}"  required  value="{{$rowresult->id}}">
                                                                                
                                                                                <input type="hidden"  class="form-control"  name="item_description[]" id="item_description{{$tableRow}}"  required  value="{{$rowresult->item_description}}">


                                                                                <button type="button"  data-id="{{$tableRow}}"  class="btn btn-info search" id="searchrow{{$tableRow}}" name="searchrow" tabindex="-1" data-toggle="modal" data-target="#search-items"><i class="glyphicon glyphicon-search"></i></button>


                                                                            </td>
                                                                            -->


                                                                            <td style="text-align:center" height="10">
                                                                                
                                                                                   <!-- <input type="hidden"  class="form-control"  name="item_id[]" id="item_id{{$tableRow}}"  required  value="{{$rowresult->id}}"> -->
                                                                                
                                                                                   <input type="hidden"  class="form-control"  name="ids[]" id="ids{{$tableRow}}"  required  value="{{$rowresult->id}}">
                                                                                   
                                                                                    {{$rowresult->item_description}}
                                                                            </td>
                                                                            <td style="text-align:center" height="10">
                                                                                    {{$rowresult->category_id}}
                                                                            </td>
                                                                            <td style="text-align:center" height="10">

                                                                                {{$rowresult->sub_category_id}}
                                                                                
                                                                                <!--
                                                                                    {{$rowresult->app_id}}
                                                    
                                                                                    @if($rowresult->app_id_others != null || $rowresult->app_id_others != "" )
                                                                                        <br>
                                                                                        {{$rowresult->app_id_others}}
                                                                                    @endif
                                                                                -->
                                                                            
                                                                            </td>
                                                                            <td style="text-align:center" height="10">
                                                                                    {{$rowresult->quantity}}
                                                                            </td>

                                                                            @if($Header->recommendedby != null || $Header->recommendedby != "")
                                                                            
                                                                                <td>
                                                                                    @if($rowresult->to_reco == 1)
                                                                                        <select class="js-example-basic-single recodropdown" style="width: 100%; height: 35px;" required name="recommendation[]" id="recommendation" data-id="{{$tableRow}}">
                                                                                            <option value="">-- Select Recommendation --</option>
                                                
                                                                                            @foreach($recommendations as $datas)    
                                                                                                @if($rowresult->recommendation == $datas->user_type)
                                                                                                    <option  value="{{$datas->user_type}}" selected>{{$datas->user_type}}</option>
                                                                                                @else
                                                                                                    <option  value="{{$datas->user_type}}">{{$datas->user_type}}</option>
                                                                                                @endif
                                                                                            @endforeach
                                                
                                                                                        </select>
                                                                                    @else
                                                                                    <input type="text" class="form-control" data-id="{{$tableRow}}" id="recommendation{{$tableRow}}" value="{{$rowresult->recommendation}}"  name="recommendation[]"  readonly>
                                                                                        <!-- <select class="js-example-basic-single recodropdown" style="width: 100%; height: 35px;"  name="recommendation[]" id="recommendation" data-id="{{$tableRow}}" disabled>
                                                                                            <option value="">-- Select Recommendation --</option>
                                                
                                                                                            @foreach($recommendations as $datas)    
                                                                                                @if($rowresult->recommendation == $datas->user_type)
                                                                                                    <option  value="{{$datas->user_type}}" selected>{{$datas->user_type}}</option>
                                                                                                @else
                                                                                                    <option  value="{{$datas->user_type}}">{{$datas->user_type}}</option>
                                                                                                @endif
                                                                                            @endforeach
                                                
                                                                                        </select> -->
                                                                                    @endif

                                                                                </td>
                                                                                
                                                                                <td>
                                                                                        <div class="form-group">
                                                                                            <input class="form-control auto" type="text" style="width: 100%;" placeholder="Search Item" id="search{{$tableRow}}" data-id="{{$tableRow}}"  name="reco_digits_code[]" value="{{$rowresult->reco_digits_code}}">
                                                                                            <ul class="ui-autocomplete ui-front ui-menu ui-widget ui-widget-content" data-id="{{$tableRow}}" id="ui-id-2{{$tableRow}}" style="display: none; top: 60px; left: 15px; width: 100%;">
                                                                                                <li>Loading...</li>
                                                                                            </ul>
                                                                                        </div>
                                                                                </td>

                                                                                <td>
                                                                                    <input type="text" onkeyup="this.value = this.value.toUpperCase();" class="form-control itemDesc" data-id="{{$tableRow}}" id="item_description{{$tableRow}}"  name="reco_item_description[]" maxlength="100" readonly value="{{$rowresult->reco_item_description}}">
                                                                                </td>

                                                                            @endif

                                                                            <!--
                                                                            <td>

                                                                            

                                                                                <button id="add-row-button" name="add-row-button" class="btn btn-info add-row-button" data-id="{{$tableRow}}" ><i class="glyphicon glyphicon-plus"></i></button>


                                                                                <table id="reco-table-delete{{$tableRow}}" class="reco-table-delete" style="width: 100%;">
                                                                                    <tbody>

                                                                                        <tr id="tr-reco-delete">

                                                                                            <tr>

                                                                                                
                                                                                            </tr>
                                                                                            

                                                                                        </tr>
                                                                                    </tbody>
                                                                                    <tfoot>
                                                                                        <tr id="tr-table-reco-delete{{$tableRow}}" class="bottom">
                                                                                            
                                                                                        </tr>
                                                                                    </tfoot>

                                                                                </table>

                                                                            </td> -->

                                                                        </tr>

                                                                    @endforeach
                                                
                                                                </tr>
                                                            </tr>
                                                        
                                                        </tbody>

                                                        <tfoot>

                                                            <tr id="tr-table1" class="bottom">
                
                                                                <td colspan="3">
                                                                    <!-- <input type="button" id="add-Row" name="add-Row" class="btn btn-info add" value='Add Item' /> -->
                                                                </td> 
                                                                <td align="center" colspan="1">
                                                                    
                                                                    <label>{{$Header->quantity_total}}</label>

                                                                </td>
                                                            </tr>
                                                        </tfoot>

                                                    </table>
                                                </div>
                                            </div>
                                  
                                    </div>
                               
                                </div>
                </div>
            </div>

            @if($Header->application != null || $Header->application != "")
                <div class="row">
                                        
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

            <hr />
            <div class="row">  
                <div class="col-md-12">
                    <div class="form-group">
                        <label>{{ trans('message.table.note') }}:</label>
                        <textarea placeholder="{{ trans('message.table.note') }} ..." rows="3" class="form-control" name="ac_comments">{{$Header->ac_comments}}</textarea>
                    </div>
                </div>
            </div>

      
            <!--
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
                                                    <tbody>
                                                        <tr class="tbl_header_color dynamicRows">
                                                            <th width="15%" class="text-center">{{ trans('message.table.digits_code') }}</th>
                                                            <th width="20%" class="text-center">{{ trans('message.table.item_description') }}</th>
                                                            <th width="20%" class="text-center">{{ trans('message.table.serial_no') }}</th>
                                                            <th width="15%" class="text-center">{{ trans('message.table.asset_tag') }}</th>
                                                            <th width="5%" class="text-center">{{ trans('message.table.item_quantity') }}</th>
                                                            <th width="10%" class="text-center">{{ trans('message.table.item_cost') }}</th>
                                                            <th width="10%" class="text-center">{{ trans('message.table.item_total_cost') }}</th>
                                                            <th width="5%" class="text-center">{{ trans('message.table.action') }}</th>
                                                        </tr>
                                                        
                                                        <tr class="tableInfo">
                                                            <td colspan="6" align="right"><strong>{{ trans('message.table.total') }}</strong></td>
                                                            <td align="left" colspan="1">


                                                                <input type='hidden' name="quantity_total" class="form-control text-center" id="quantity_total" readonly>

                                                                <input type='hidden' name="cost_total" class="form-control text-center" id="cost_total" readonly>

                                                                <input type='number' name="total" class="form-control text-center" id="total" readonly>
                                                            </td>
                                                            <td colspan="1"></td>
                                                        </tr>
                                                    
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                
                                    </div>
                                    <br>
                                </div>
                </div>
          
            </div> 
            -->

        </div>

        <div class='panel-footer'>
            <a href="{{ CRUDBooster::mainpath() }}" class="btn btn-default">{{ trans('message.form.cancel') }}</a>
            <button class="btn btn-primary pull-right" type="submit" id="btnSubmit"> <i class="fa fa-save" ></i> {{ trans('message.form.new') }}</button>
            <!-- <button class="btn btn-warning pull-right" type="submit" id="btnPrint" style="margin-right: 10px;"> <i class="fa fa-print" ></i> {{ trans('message.form.print') }}</button> -->
             <button class="btn btn-warning pull-right" type="submit" id="btnUpdate" style="margin-right: 10px;"> <i class="fa fa-circle-o" ></i> {{ trans('message.form.update') }}</button> 
        </div>

    </form>


</div>

@endsection
@push('bottom')

<script src=
"https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js" >
    </script>
      
    <script src=
"https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js" >
    </script>

<script type="text/javascript">

    function preventBack() {
        window.history.forward();
    }
    window.onunload = function() {
        null;
    };
    setTimeout("preventBack()", 0);


   // $( "#quote_date, #po_date" ).datepicker( { format: 'yyyy-mm-dd', endDate: new Date() } );

    var tableRow = <?php echo json_encode($tableRow); ?>;
    
    var tableRow1 = tableRow;

    tableRow1++;

    /*$("#search-items").on('shown.bs.modal', function(){
        //$("#item_search").text("1s");
    });*/

    $(document).ready(function() {
            $('#myform').on('keypress', function(e) {
                var keyCode = e.keyCode || e.which;
                if (keyCode === 13) { 
                    e.preventDefault();
                    return false;
                }
            });

            $('#po_date, #quote_date').datepicker({
                    constrainInput: false,
                    maxDate: '0',
                    dateFormat: 'yy-mm-dd'
            })
    });
    
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
        var searchcount = <?php echo json_encode($tableRow); ?>;

        let countrow = 1;

            $(function(){

                for (let i = 0; i < searchcount; i++) {
                    countrow++;
                    
                    //$('#search'+countrow).attr('disabled', true);


                    $("#search"+countrow).autocomplete({

                        source: function (request, response) {
                        $.ajax({
                            url: "{{ route('it.item.search') }}",
                            dataType: "json",
                            type: "POST",
                            data: {
                                "_token": token,
                                "search": request.term
                            },
                            success: function (data) {
                                //var rowCount = $('#asset-items tr').length;
                                //myStr = data.sample;   
                                if (data.status_no == 1) {

                                    $("#val_item").html();
                                    var data = data.items;
                                    $('#ui-id-2'+countrow).css('display', 'none');

                                    response($.map(data, function (item) {
                                        return {
                                            id:                         item.id,
                                            asset_code:                 item.asset_code,
                                            digits_code:                item.digits_code,
                                            asset_tag:                  item.asset_tag,
                                            serial_no:                  item.serial_no,
                                            value:                      item.item_description,
                                            category_description:       item.category_description,
                                            item_cost:                  item.item_cost,
                                        
                                        }

                                    }));

                                } else {

                                    $('.ui-menu-item').remove();
                                    $('.addedLi').remove();
                                    $("#ui-id-2"+countrow).append($("<li class='addedLi'>").text(data.message));
                                    var searchVal = $("#search"+countrow).val();
                                    if (searchVal.length > 0) {
                                        $("#ui-id-2"+countrow).css('display', 'block');
                                    } else {
                                        $("#ui-id-2"+countrow).css('display', 'none');
                                    }
                                }
                            }
                        })
                        },
                        select: function (event, ui) {
                            var e = ui.item;

                            if (e.id) {
                      
                               

                                $("#item_description"+$(this).attr("data-id")).val(e.value);

                                $("#search"+$(this).attr("data-id")).val(e.digits_code);
                                
                                $('#val_item').html('');
                                return false;
    
                            }
                        },

                        minLength: 1,
                        autoFocus: true
                        });


                }


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

    $('#po_number, #po_date, #quote_date').keyup(function() {
			this.value = this.value.toLocaleUpperCase();
	});

    $("#btnSubmit").click(function(event) {
        // var strconfirm = confirm("Are you sure you want to proceed this request?");
        // if (strconfirm == true) {
        //     if( $("#po_number").val() == "" || $("#po_number").val() == null ){
        //         $("#po_number, #po_date, #quote_date").attr('required', 'required');
        //         window.stop();
        //     }else if( $("#po_date").val() == "" || $("#po_date").val() == null ){
        //         $("#po_number, #po_date, #quote_date").attr('required', 'required');
        //         window.stop();
        //     }else if( $("#quote_date").val() == "" || $("#quote_date").val() == null ){
        //         $("#po_number, #po_date, #quote_date").attr('required', 'required');
        //         window.stop();
        //     }else{               
        //             $("#action").val("1");
        //             $(this).attr('disabled','disabled');
        //             $('#myform').submit(); 
        //         }
        // }else{
        //     return false;
        //     window.stop();
        // }
        event.preventDefault();
        if($("#po_number").val() == "" || $("#po_number").val() == null){
            swal({
                type: 'error',
                title: 'PO Number required!',
                icon: 'error',
                confirmButtonColor: "#367fa9",
            }); 
            event.preventDefault(); // cancel default behavior
            return false;
        }else if($("#po_date").val() == "" || $("#po_date").val() == null){
            swal({
                type: 'error',
                title: 'PO Date required!',
                icon: 'error',
                confirmButtonColor: "#367fa9",
            }); 
            event.preventDefault(); // cancel default behavior
            return false;
        }else if($("#quote_date").val() == "" || $("#quote_date").val() == null){
            swal({
                type: 'error',
                title: 'Quote Date required!',
                icon: 'error',
                confirmButtonColor: "#367fa9",
            }); 
            event.preventDefault(); // cancel default behavior
            return false;
        }else{
            var item = $("input[name^='reco_digits_code']").length;
            var item_value = $("input[name^='reco_digits_code']");
            for(i=0;i<item;i++){
                if(item_value.eq(i).val() == 0 || item_value.eq(i).val() == null){
                    swal({  
                            type: 'error',
                            title: 'Digits Code and Item Description cannot be empty!!',
                            icon: 'error',
                            confirmButtonColor: "#367fa9",
                        });
                        event.preventDefault();
                        return false;
                } 
        
            } 
            swal({
            title: "Are you sure you?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#41B314",
            cancelButtonColor: "#F9354C",
            confirmButtonText: "Yes, proceed!",
            width: 450,
            height: 200
            }, function () {
                $("#action").val("1");
                $("#myform").submit();                   
        });
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
@endpush