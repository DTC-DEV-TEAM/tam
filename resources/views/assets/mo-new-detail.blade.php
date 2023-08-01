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
    #asset-items th, td, tr {
        border: 1px solid rgba(000, 0, 0, .5);
        padding: 8px;
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

            @if($Header->if_from_item_source != null || $Header->if_from_item_source != "")
                <div class="row">                           
                    <label class="control-label col-md-2">Item Sourcing Number:</label>
                    <div class="col-md-4">
                            <p>{{$Header->if_from_item_source}}</p>
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
            <div class="row">
                <div class="col-md-12">
                    <div class="box-header text-center">
                        <h3 class="box-title"><b>Item Request</b></h3>
                    </div>
                    <div class="box-body no-padding">
                        <div class="pic-container">
                            <div class="pic-row">
                                <table id="asset-items1">
                                    <tbody id="bodyTable">
                                        <tr class="tbl_header_color dynamicRows">
                                            <th width="10%" class="text-center">{{ trans('message.table.digits_code') }}</th>
                                            <th width="20%" class="text-center">{{ trans('message.table.item_description') }}</th>
                                            <th width="9%" class="text-center">{{ trans('message.table.category_id_text') }}</th>                                                         
                                            <th width="10%" class="text-center">{{ trans('message.table.sub_category_id_text') }}</th> 
                                            <th width="5%" class="text-center">{{ trans('message.table.quantity_text') }}</th> 
                                                                                   
                                            <th width="5%" class="text-center">For Replenish Qty</th> 
                                            <th width="5%" class="text-center">For ReOrder Qty</th> 
                                            <th width="5%" class="text-center">Fulfilled Qty</th> 
                                            <th width="5%" class="text-center">UnServed Qty</th>                                                                                                                                                                                                          
                                            <th width="5%" class="text-center">DR Qty</th>
                                            <th width="5%" class="text-center">PO Qty</th>     
                                            <th width="10%" class="text-center">DR#</th>         
                                            <th width="10%" class="text-center">PO#</th>   
                                            <th width="5%" class="text-center">Cancelled Qty</th> 
                                            <th>Reason</th>                                
                       
                                            @if($Header->recommendedby != null || $Header->recommendedby != "")
                                                <th width="13%" class="text-center">{{ trans('message.table.recommendation_text') }}</th> 
                                                <th width="14%" class="text-center">{{ trans('message.table.reco_digits_code_text') }}</th> 
                                                <th width="24%" class="text-center">{{ trans('message.table.reco_item_description_text') }}</th>
                                            @endif 
                                            
                                            @if($Header->approved_by == null || $Header->approved_by == "")
                                                <th width="5%" class="text-center">{{ trans('message.table.action') }}</th>
                                            @endif 

                                        </tr>
                                        <tr id="tr-table">
                                            <?php   $tableRow = 1; ?>
                                            <tr>
                                                @foreach($Body as $rowresult)
                                                    <?php   $tableRow++; ?>
                                                
                                                            <tr>
                                                                <td style="text-align:center" height="10">
                                                                        <input type="hidden"  class="form-control"  name="ids[]" id="ids{{$tableRow}}"  required  value="{{$rowresult->id}}">                               
                                                                        {{$rowresult->digits_code}}
                                                                </td>
                                                                <td style="text-align:center" height="10">
                                                                        <input type="hidden"  class="form-control"  name="ids[]" id="ids{{$tableRow}}"  required  value="{{$rowresult->id}}">                               
                                                                        {{$rowresult->item_description}}
                                                                </td>
                                                                <td style="text-align:center" height="10">
                                                                        {{$rowresult->category_id}}
                                                                </td>
                                                                <td style="text-align:center" height="10">
                                                                        {{$rowresult->sub_category_id}}
                                                                </td>
                                                                <td style="text-align:center" height="10" class="qty">
                                                                    {{$rowresult->quantity}}
                                                                        {{-- <input type='hidden' name="quantity" class="form-control text-center quantity_item" id="quantity" readonly value="{{$rowresult->quantity}}">
                                                                        <input type='hidden' name="quantity_body" id="quantity{{$tableRow}}" readonly value="{{$rowresult->quantity}}"> --}}
                                                                </td>
                                                        
                                                                <td style="text-align:center" class="rep_qty">{{$rowresult->replenish_qty ? $rowresult->replenish_qty : 0}}</td>  
                                                                <td style="text-align:center" class="ro_qty">{{$rowresult->reorder_qty ? $rowresult->reorder_qty : 0}}</td>                                                           
                                                                <td style="text-align:center" class="served_qty">{{$rowresult->serve_qty ? $rowresult->serve_qty : 0}}</td>
                                                                <td style="text-align:center" class="unserved_qty">{{$rowresult->unserved_qty ? $rowresult->unserved_qty : 0}}</td>
                                                                <td style="text-align:center" class="dr_qty">{{$rowresult->dr_qty ? $rowresult->dr_qty : 0}}</td> 
                                                                <td style="text-align:center" class="po_qty">{{$rowresult->po_qty ? $rowresult->po_qty : 0}}</td>   
                                                                <td style="text-align:center">{{$rowresult->mo_so_num}}</td>   
                                                                <td style="text-align:center">{{$rowresult->po_no}}</td>     
                                                                <td style="text-align:center" class="po_qty">{{$rowresult->cancelled_qty ? $rowresult->cancelled_qty : 0}}</td>                                                                 <td style="text-align:center">{{$rowresult->reason_to_cancel}}</td>  
                                                                
                                                             
                                                                @if($Header->recommendedby != null || $Header->recommendedby != "")                                                                               
                                                                    <td style="text-align:center" height="10">
                                                                        {{$rowresult->recommendation}}
                                                                    </td>                                                                                  
                                                                    <td style="text-align:center" height="10">
                                                                        {{$rowresult->reco_digits_code}}
                                                                    </td>
                                                                    <td style="text-align:center" height="10">
                                                                        {{$rowresult->reco_item_description}}
                                                                    </td>
                                                                @endif

                                                                @if($Header->status_id == 1)    
                                                                    <td style="text-align:center" height="10">
                                                                        <button id="deleteRow{{$tableRow}}" name="removeRow" data-id="{{$tableRow}}" class="btn btn-danger removeRow btn-sm" data-toggle="tooltip" data-placement="bottom" title="Cancel"><i class="fa fa-trash"></i></button>
                                                                    </td>
                                                                @endif
                                                                                        
                                                            </tr>
                                                    
                                                @endforeach     
                                                
                                                <input type='hidden' name="quantity_total" class="form-control text-center" id="quantity_total" readonly value="{{$Header->quantity_total}}">
                                            </tr>
                                        </tr>          
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    <div class="box-header text-center">
                        <h3 class="box-title"><b>{{ trans('message.form-label.asset_items') }}</b></h3>
                    </div>
                    <div class="box-body no-padding">
                        <div class="pic-container">
                            <div class="pic-row">
                                <table id="asset-items">
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

                                        <?Php   $item_count = 0; ?>

                                        @if( !empty($MoveOrder) )

                                            

                                            @foreach($MoveOrder as $rowresult)

                                                <?php   $tableRow1++; ?>

                                                <?Php $item_count++; ?>

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

                                                <?Php $cost_total = $rowresult->total_unit_cost; ?>

                                            @endforeach


                                        @endif
                                        
                                        <tr class="tableInfo">
                                            <td colspan="8" align="right"><strong>{{ trans('message.table.total') }}</strong></td>
                                            <td align="center" colspan="1">

                                                @if($item_count == 1)
                                                        <label>{{$cost_total}}</label>
                                                    @else
                                                        <label>{{$Header->total}}</label>
                                                @endif
                                                        
                                            </td>
                                        </tr>
                                    
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
            

        </div>

        <div class='panel-footer'>

            <a href="{{ CRUDBooster::mainpath() }}" class="btn btn-default">{{ trans('message.form.cancel') }}</a>
        
        </div>
</div>
@endsection
