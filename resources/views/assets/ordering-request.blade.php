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
        Request Form
    </div>

    <form method='post' id="myform" action='{{CRUDBooster::mainpath('edit-save/'.$Header->requestid)}}'>
        <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
        <input type="hidden" value="0" name="action" id="action">


        <!-- Modal -->
        <div id="myModal" class="modal">

            <!-- Modal content -->
            <div class="modal-content">

                <div class='callout callout-info'>
                    <h5>SEARCH FOR <label id="item_search"></label></h5>
                    <input type="hidden"  class="form-control" id="add_item_id">
                    <input type="hidden"  class="form-control" id="button_count">
                    <input type="hidden"  class="form-control" id="button_remove">
                    
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label">{{ trans('message.form-label.add_item1') }}</label>
                            <input class="form-control auto" style="width:100%;" placeholder="Search Item" id="search">
                            <ul class="ui-autocomplete ui-front ui-menu ui-widget ui-widget-content" id="ui-id-2" style="display: none; top: 60px; left: 15px; width: 570px;">
                                <li>Loading...</li>
                            </ul>
                        </div>
                    </div>
                </div> 

                <br>
                <button type="button"  class="btn btn-info pull-right btnsearch" id="searchclose" >Close</button>

            </div>
          
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

                                                                <th width="5%" class="text-center">{{ trans('message.table.action') }}</th>

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

                                                                           
                                                                            <td>

                                                                                <input type="hidden"  class="form-control"  name="add_item_id[]" id="add_item_id{{$tableRow}}"  required  value="{{$rowresult->id}}">
                                                                                
                                                                                <input type="hidden"  class="form-control"  name="item_description[]" id="item_description{{$tableRow}}"  required  value="{{$rowresult->item_description}}">
                                                                                 
                                                                                <input type="hidden"  class="form-control"  name="remove_btn[]" id="remove_btn{{$tableRow}}"  required  value="{{$tableRow}}">

                                                                            @if( $MoveOrder->count() != 0 )
                                                                                    
                                                                                @if( $MoveOrder->where('body_request_id', $rowresult->id)->count() != 0 )

                                                                                            @foreach($MoveOrder->where('body_request_id', $rowresult->id) as $rowresult1)

                                                                                                @if( $rowresult->id == $rowresult1->body_request_id )

                                                                                                    <button type="button"  data-id="{{$tableRow}}"  class="btn btn-info btnsearch" id="searchrow{{$tableRow}}" name="searchrow"  disabled><i class="glyphicon glyphicon-search"></i></button>

                                                                                                @else

                                                                                            
                                                                                                    <button type="button"  data-id="{{$tableRow}}"  class="btn btn-info btnsearch" id="searchrow{{$tableRow}}" name="searchrow" ><i class="glyphicon glyphicon-search"></i></button>

                                                                                                @endif


                                                                                            @endforeach


                                                                                        @else

                                                                                            <button type="button"  data-id="{{$tableRow}}"  class="btn btn-info btnsearch" id="searchrow{{$tableRow}}" name="searchrow" ><i class="glyphicon glyphicon-search"></i></button>
                                                                                @endif

                                                                            @else
                                                                            
                                                                                <button type="button"  data-id="{{$tableRow}}"  class="btn btn-info btnsearch" id="searchrow{{$tableRow}}" name="searchrow" ><i class="glyphicon glyphicon-search"></i></button>
                                                                            
                                                                            @endif

                                                                            </td>
                                                                           


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
                
                                                                <td colspan="4">
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
                                                            <th width="13%" class="text-center">{{ trans('message.table.digits_code') }}</th>
                                                            <th width="13%" class="text-center">{{ trans('message.table.asset_tag') }}</th>
                                                            <th width="26%" class="text-center">{{ trans('message.table.item_description') }}</th>
                                                            <th width="18%" class="text-center">{{ trans('message.table.serial_no') }}</th>
                                                            <th width="7%" class="text-center">{{ trans('message.table.item_quantity') }}</th>
                                                            <th width="10%" class="text-center">{{ trans('message.table.item_cost') }}</th>
                                                            <th width="10%" class="text-center">{{ trans('message.table.item_total_cost') }}</th>
                                                            <th width="5%" class="text-center">{{ trans('message.table.action') }}</th>
                                                        </tr>

                                                        <?php   $tableRow1 = 0; ?>

                                                        @if( !empty($MoveOrder) )

                                                          

                                                            @foreach($MoveOrder as $rowresult)

                                                                <?php   $tableRow1++; ?>

                                                                <tr>

                                                                    <td>
                                                                        <input class="form-control text-center" type="text" name="mo_digits_code[]" readonly value="{{$rowresult->digits_code}}">
                                                                    </td>

                                                                    <td>
                                                                        <input class="form-control text-center" type="text" name="mo_asset_code[]" readonly value="{{$rowresult->asset_code}}">
                                                                    </td>

                                                                    <td>
                                                                        <input class="form-control" type="text" name="mo_item_description[]" readonly value="{{$rowresult->item_description}}">
                                                                    </td>

                                                                    <td>
                                                                        <input class="form-control" type="text" name="mo_serial_no[]" readonly value="{{$rowresult->serial_no}}">
                                                                    </td>

                                                                    <td>
                                                                        <input class="form-control text-center quantity_item" type="number" name="mo_quantity[]" id="quantity{{$tableRow1}}"  value="{{$rowresult->quantity}}" min="0" max="9999999999" step="any" onKeyPress="if(this.value.length==10) return false;" oninput="validity.valid||(value=0);" readonly="readonly">
                                                                    </td>

                                                                    <td>
                                                                        <input class="form-control text-center cost_item" type="number" name="mo_unit_cost[]" id="unit_cost{{$tableRow1}}"   data-id="{{$tableRow1}}"  value="{{$rowresult->unit_cost}}" min="0" max="9999999999" step="any" onKeyPress="if(this.value.length==10) return false;" oninput="validity.valid||(value=0);" readonly="readonly">
                                                                    </td>

                                                                    <td>
                                                                        <input class="form-control text-center total_cost_item" type="number" name="mo_total_unit_cost[]"  id="total_unit_cost{{$tableRow1}}"   value="{{$rowresult->total_unit_cost}}" readonly="readonly" step="0.01" required maxlength="100">
                                                                    </td>

                                                                    <td class="text-center">
                                                                       <!-- <button id="{{$tableRow1}}" data-id="{{$tableRow1}}" onclick="reply_click1(this.id)" class="btn btn-xs btn-danger delete_item" style="width:60px;height:30px;font-size: 11px;text-align: center;">REMOVE</button> -->
                                                                    </td>

                                                                </tr>


                                                            @endforeach


                                                        @endif
                                                        
                                                        <tr class="tableInfo">
                                                            <td colspan="6" align="right"><strong>{{ trans('message.table.total') }}</strong></td>
                                                            <td align="left" colspan="1">


                                                                <input type='hidden' name="quantity_total" class="form-control text-center" id="quantity_total" readonly>

                                                                <input type='hidden' name="cost_total" class="form-control text-center" id="cost_total" readonly>

                                                                <input type='number' name="total" class="form-control text-center" id="total" readonly value="{{$Header->total}}">
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
           
            
            <button class="btn btn-primary pull-right" type="submit" id="btnSubmit"> <i class="fa fa-save" ></i> {{ trans('message.form.new') }}</button>
            
            <!-- <button class="btn btn-warning pull-right" type="submit" id="btnPrint" style="margin-right: 10px;"> <i class="fa fa-print" ></i> {{ trans('message.form.print') }}</button> -->

        
        </div>

    </form>





</div>

@endsection
@push('bottom')
<script type="text/javascript">

    var stack = [];
    var token = $("#token").val();

    var modal = document.getElementById("myModal");

    $('.btnsearch').click(function() {
        document.querySelector("body").style.overflow = 'hidden';
        modal.style.display = "block";
    });

    $('#searchclose').click(function() {
        document.querySelector("body").style.overflow = 'visible';
        modal.style.display = "none";
    });
   

    function preventBack() {
        window.history.forward();
    }
    window.onunload = function() {
        null;
    };
    setTimeout("preventBack()", 0);

    $( "#quote_date, #po_date" ).datepicker( { format: 'yyyy-mm-dd', endDate: new Date() } );

    /*$("#search-items").on('shown.bs.modal', function(){
        //$("#item_search").text("1s");
    });*/

    $(".btnsearch").click(function(event) {
       
       var searchID = $(this).attr("data-id");
       
       //alert($("#item_description"+searchID).val());

       $("#item_search").text($("#item_description"+searchID).val());

       $("#add_item_id").val($("#add_item_id"+searchID).val());

       $("#button_count").val(searchID);
 
       $("#button_remove").val($("#remove_btn"+searchID).val());

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

        var strconfirm = confirm("Are you sure you want to move this request?");
        if (strconfirm == true) {

            var countRow = $('#asset-items tbody tr').length;

            if (countRow == 2) {

                alert("Please add an item!");
                event.preventDefault(); // cancel default behavior

            }else{

                $("#action").val("1");

                $(this).attr('disabled','disabled');

                $('#myform').submit(); 
            }
            
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

        var disabled = $('#remove_disable'+RowID).val();

        $("#searchrow"+disabled).attr('disabled', false);


       // alert(stack.indexOf(RowID));

        if ($('#asset-items tbody tr').length != 1) { //check if not the first row then delete the other rows

            stack.splice(stack.indexOf(parseInt(RowID)), 1);
   
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
                            var rowCount = $('#asset-items tr').length;
                            //myStr = data.sample; 

                            if (data.status_no == 1) {

                                $("#val_item").html();
                                var data = data.items;
                                $('#ui-id-2').css('display', 'none');

                                response($.map(data, function (item) {
                                    return {
                                        id:                         item.id,
                                        asset_code:                 item.asset_code,
                                        digits_code:                item.digits_code,
                                        serial_no:                  item.serial_no,
                                        value:                      item.item_description,
                                        item_cost:                  item.value,
                                        quantity:                   item.quantity,
                                        item_id:                    item.item_id
                                    }

                                }));

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

                        modal.style.display = "none";

                        document.querySelector("body").style.overflow = 'visible';

                        var e = ui.item;

                        if (e.id) {

                                //$("#btnUpdate").attr('disabled', true);
                                var remove_count = $("#button_remove").val();

                                var add_id = $("#add_item_id").val();
                         
                                $("#searchrow"+ $("#button_count").val()).attr('disabled', true);

                            // if (!in_array(e.id, stack)) {
                                if (!stack.includes(e.id)) {
            
                                    stack.push(e.id);           
                                    
                                        var serials = "";

                                        if(e.serial_no == null || e.serial_no == ""){
                                            serials = "";
                                        }else{
                                            serials = e.serial_no;
                                        }
                                        
                                            var new_row = '<tr class="nr" id="rowid' + e.id + '">' +
                                                    
                                                    '<td><input class="form-control text-center" type="text" name="add_digits_code[]" readonly value="' + e.digits_code + '"></td>' +
                                                    '<td><input class="form-control text-center" type="text" name="add_asset_code[]" readonly value="' + e.asset_code + '"></td>' +
                                                    '<td><input class="form-control" type="text" name="add_item_description[]" readonly value="' + e.value + '"></td>' +
                                                    '<td><input class="form-control" type="text" name="add_serial_no[]" readonly value="' + serials + '"></td>' +
                                                    

                                                    '<td><input class="form-control text-center quantity_item" type="number" name="add_quantity[]" id="quantity' + e.id  + '" data-id="' + e.id  + '"  value="1" min="0" max="9999999999" step="any" onKeyPress="if(this.value.length==10) return false;" oninput="validity.valid||(value=0);" readonly="readonly"></td>' +
                                
                                                    '<td><input class="form-control text-center cost_item" type="number" name="add_unit_cost[]" id="unit_cost' + e.id  + '"   data-id="' + e.id  + '"  value="' + e.item_cost + '" min="0" max="9999999999" step="any" onKeyPress="if(this.value.length==10) return false;" oninput="validity.valid||(value=0);"></td>' +
                                                    
                                                    '<td><input class="form-control text-center total_cost_item" type="number" name="add_total_unit_cost[]"  id="total_unit_cost' + e.id  + '"   value="' + e.item_cost + '" readonly="readonly" step="0.01" required maxlength="100"></td>' +

                                                    '<td class="text-center"><button id="' +e.id + '" data-id="' + e.id  + '" onclick="reply_click1(this.id)" class="btn btn-xs btn-danger delete_item" style="width:60px;height:30px;font-size: 11px;text-align: center;">REMOVE</button></td>' +
                                                    
                                                    '<input type="hidden" name="body_request_id[]" readonly value="' + add_id + '">' +

                                                    '<input type="hidden" name="inventory_id[]" readonly value="' +e.id + '">' +
                                                    
                                                    '<input type="hidden" name="item_id[]" readonly value="' +e.item_id + '">' +

                                                    '<input type="hidden" name="remove_disable[]" id="remove_disable' + e.id  + '" readonly value="' + remove_count + '">' +

                                                    '</tr>';

                                    $(new_row).insertAfter($('table tr.dynamicRows:last'));
                
                                    //blank++;

                                    //$("#total").val(calculateTotalValue2());
                                    $("#total").val(calculateTotalValue2());
                                    $("#quantity_total").val(calculateTotalQuantity());

                                    $(this).val('');
                                    $('#val_item').html('');
                                    return false;
                                
                                }else{


                                    if(e.serial_no == null || e.serial_no == ""){

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

                                        $("#searchrow"+ $("#button_count").val()).attr('disabled', false);

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

</script>
@endpush