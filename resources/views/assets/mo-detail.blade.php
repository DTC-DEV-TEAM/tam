@extends('crudbooster::admin_template')
@section('content')
<style>

    
    /* The Modal (background) */
    .modal {
      display: none; /* Hidden by default */
      position: fixed; /* Stay in place */
      z-index: 1; /* Sit on top */
      padding-top: 100px; /* Location of the box */
      left: 0;
      top: 0;
      width: 100%; /* Full width */
      height: 100%; /* Full height */
      overflow: auto; /* Enable scroll if needed */
      background-color: rgb(0,0,0); /* Fallback color */
      background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
      
    }
    
    /* Modal Content */
    .modal-content {
      background-color: #fefefe;
      margin: auto;
      padding: 20px;
      border: 1px solid #888;
      width: 40%;
      height: 250px;
    }
    
    /* The Close Button */
    .close {
      color: #aaaaaa;
      float: right;
      font-size: 28px;
      font-weight: bold;
    }
    
    .close:hover,
    .close:focus {
      color: #000;
      text-decoration: none;
      cursor: pointer;
    }
    </style>
@if(g('return_url'))
	<p class="noprint"><a title='Return' href='{{g("return_url")}}'><i class='fa fa-chevron-circle-left '></i> &nbsp; {{trans("crudbooster.form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}}</a></p>       
@else
	<p class="noprint"><a title='Main Module' href='{{CRUDBooster::mainpath()}}'><i class='fa fa-chevron-circle-left '></i> &nbsp; {{trans("crudbooster.form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}}</a></p>       
@endif
<div class='panel panel-default'>
    <div class='panel-heading'>
        Detail Form
    </div>

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
            <!--
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

                <label class="control-label col-md-2">{{ trans('message.form-label.po_number') }}:</label>
                <div class="col-md-4">
                        <p>{{$Header->po_number}}</p>
                </div>

                <label class="control-label col-md-2">{{ trans('message.form-label.po_date') }}:</label>
                <div class="col-md-4">
                        <p>{{$Header->po_date}}</p>
                </div>

            </div>

            <div class="row">                           

                <label class="control-label col-md-2">{{ trans('message.form-label.quote_date') }}:</label>
                <div class="col-md-4">
                        <p>{{$Header->quote_date}}</p>
                </div>



            </div>


            <div class="row">                           

                <label class="control-label col-md-2">{{ trans('message.form-label.tagged_by') }}:</label>
                <div class="col-md-4">
                        <p>{{$Header->taggedby}}</p>
                </div>

                <label class="control-label col-md-2">{{ trans('message.form-label.tagged_date') }}:</label>
                <div class="col-md-4">
                        <p>{{$Header->purchased2_at}}</p>
                </div>

            </div>
            -->

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
                                                                <th width="20%" class="text-center">{{ trans('message.table.item_description') }}</th>
                                                                <th width="9%" class="text-center">{{ trans('message.table.category_id_text') }}</th>                                                         
                                                                <th width="15%" class="text-center">{{ trans('message.table.sub_category_id_text') }}</th> 
                                                                <th width="5%" class="text-center">{{ trans('message.table.quantity_text') }}</th> 

                                                                @if($Header->recommendedby != null || $Header->recommendedby != "")
                                                                    <th width="13%" class="text-center">{{ trans('message.table.recommendation_text') }}</th> 
                                                                    <th width="14%" class="text-center">{{ trans('message.table.reco_digits_code_text') }}</th> 
                                                                    <th width="24%" class="text-center">{{ trans('message.table.reco_item_description_text') }}</th>
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

                                                                            <td style="text-align:center" height="10">
                                                                                
                                                                                   <!-- <input type="hidden"  class="form-control"  name="item_id[]" id="item_id{{$tableRow}}"  required  value="{{$rowresult->id}}"> -->
                                                                                
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
                                                                            
                                                                                <td style="text-align:center" height="10">
                                                                                    {{$rowresult->recommendation}}

                                                                                    @if($BodyReco != null || $BodyReco != "")
                                                                                        @foreach($BodyReco as $rowresult1)
                            
                                                                                            @if($rowresult1->body_request_id ==  $rowresult->id)
                                                                                                {{$rowresult1->recommendation}} <br/>
                                                                                            @endif
                            
                                                                                        @endforeach
                                                                                    @endif
                                                                                
                                                                                </td>
                                                                                
                                                                                <td style="text-align:center" height="10">
                                                                                    {{$rowresult->reco_digits_code}}
                                                                                </td>

                                                                                <td style="text-align:center" height="10">
                                                                                    {{$rowresult->reco_item_description}}
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

            <!--
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

            -->


            <hr />

            <!--

            <hr/>
            <div class="row">  
                <div class="col-md-12">
                    <div class="form-group">
                        <label>{{ trans('message.table.comments') }}:</label>
                        <textarea placeholder="{{ trans('message.table.comments') }} ..." rows="3" class="form-control" name="approver_comments">{{$Header->approver_comments}}</textarea>
                    </div>
                </div>
            </div> -->

      
           
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
                                                            <th width="10%" class="text-center">{{ trans('message.table.mo_reference_number') }}</th>
                                                            <th width="13%" class="text-center">{{ trans('message.table.status_id') }}</th>
                                                            <th width="10%" class="text-center">{{ trans('message.table.digits_code') }}</th>
                                                            <th width="10%" class="text-center">{{ trans('message.table.asset_tag') }}</th>
                                                            <th width="26%" class="text-center">{{ trans('message.table.item_description') }}</th>
                                                            <th width="13%" class="text-center">{{ trans('message.table.serial_no') }}</th>
                                                            <th width="4%" class="text-center">{{ trans('message.table.item_quantity') }}</th>
                                                            <th width="8%" class="text-center">{{ trans('message.table.item_cost') }}</th>
                                                            <th width="16%" class="text-center">{{ trans('message.table.item_total_cost') }}</th>
                                                            
                                                        </tr>

                                                        <?php   $tableRow1 = 0; ?>

                                                        @if( !empty($MoveOrder) )

                                                          

                                                            @foreach($MoveOrder as $rowresult)

                                                                <?php   $tableRow1++; ?>

                                                                <tr>
                                                                    <td style="text-align:center" height="10">
                                                                        {{$rowresult->mo_reference_number}}
                                                                    </td>

                                                                    <td style="text-align:center" height="10">

                                                                        <label style="color: #3c8dbc;">
                                                                            {{$rowresult->status_description}}
                                                                        </label>
                                                                       

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

                                                                    <td style="text-align:center" height="10">
                                                                        {{$rowresult->unit_cost}}
                                                                    </td>

                                                                    <td style="text-align:center" height="10">
                                                                        {{$rowresult->total_unit_cost}}
                                                                    </td>

                                                                    

                                                                </tr>


                                                            @endforeach


                                                        @endif
                                                        
                                                        <tr class="tableInfo">
                                                            <td colspan="8" align="right"><strong>{{ trans('message.table.total') }}</strong></td>
                                                            <td align="center" colspan="1">
                                                                <label>{{$Header->total}}</label>
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
            

        </div>

        <div class='panel-footer'>

            <a href="{{ CRUDBooster::mainpath() }}" class="btn btn-default">{{ trans('message.form.cancel') }}</a>
        
        </div>
        
</div>

@endsection
