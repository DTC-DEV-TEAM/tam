@extends('crudbooster::admin_template')
@section('content')

@if(g('return_url'))
	<p class="noprint"><a title='Return' href='{{g("return_url")}}'><i class='fa fa-chevron-circle-left '></i> &nbsp; {{trans("crudbooster.form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}}</a></p>       
@else
	<p class="noprint"><a title='Main Module' href='{{CRUDBooster::mainpath()}}'><i class='fa fa-chevron-circle-left '></i> &nbsp; {{trans("crudbooster.form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}}</a></p>       
@endif
<div class='panel panel-default'>
    <div class='panel-heading'>
        Detail Form
    </div>


        <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
        <input type="hidden" value="0" name="action" id="action">

        <input type="hidden" value="{{$Header->requestid}}" name="headerID" id="headerID">

        <input type="hidden" value="" name="bodyID" id="bodyID">

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


            <hr/>                
            <div class="row">
                <div class="col-md-12">
                    <div class="box-header text-center">
                        <h3 class="box-title"><b>Item Request</b></h3>
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
                                               
                                                    <!-- <th width="5%" class="text-center">{{ trans('message.table.action') }}</th> -->
                                             
                                            </tr>
                                            <tr id="tr-table">
                                                <?php   $tableRow = 1; ?>
                                                <tr>
                                                    @foreach($Body as $rowresult)
                                                        <?php   $tableRow++; ?>
                                                        @if( $rowresult->digits_code == null || $rowresult->digits_code == "" )
                                                        
                                                                        @if($rowresult->deleted_at != null || $rowresult->deleted_at != "")
                                                                            <tr style="background-color: #d9534f; color: white;">
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
                                                                                <td style="text-align:center" height="10">
                                                                                        {{$rowresult->quantity}}
                                                                                        <input type='hidden' name="quantity" class="form-control text-center quantity_item" id="quantity" readonly value="{{$rowresult->quantity}}">
                                                                                </td>
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
                                                                                    <td style="text-align:center" height="10">
                                                                                        <button id="deleteRow{{$tableRow}}" name="removeRow" data-id="{{$tableRow}}" class="btn btn-danger removeRow" disabled><i class="glyphicon glyphicon-remove"></i></button>
                                                                                    </td>                 
                                                                            </tr>
                                                                        @else
                                                                            <tr>
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
                                                                                <td style="text-align:center" height="10">
                                                                                        {{$rowresult->quantity}}
                                                                                        <input type='hidden' name="quantity" class="form-control text-center quantity_item" id="quantity" readonly value="{{$rowresult->quantity}}">
                                                                                </td>
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
                                                                                <!-- @if($Header->po_number == null || $Header->po_number == "")    
                                                                                        <td style="text-align:center" height="10">
                                                                                            <button id="deleteRow{{$tableRow}}" name="removeRow" data-id="{{$tableRow}}" class="btn btn-danger removeRow"><i class="glyphicon glyphicon-remove"></i></button>
                                                                                        </td>
                                                                                    @else
                                                                                        <td style="text-align:center" height="10">
                                                                                            <button id="deleteRow{{$tableRow}}" name="removeRow" data-id="{{$tableRow}}" class="btn btn-danger removeRow" disabled><i class="glyphicon glyphicon-remove"></i></button>
                                                                                        </td>
                                                                                @endif -->

                                                                                
                                                                            </tr>
                                                                        @endif
                                                        @endif
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
            </div>

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

            @if( $MoveOrder->count() != 0 )
                <hr />
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

                                                                            <input type="hidden" value="{{$rowresult->id}}" name="item_id[]">

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
                        </div>
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
                @if( $Header->processedby != null )
                    <div class="row">                           
                        <label class="control-label col-md-2">{{ trans('message.form-label.processed_by') }}:</label>
                        <div class="col-md-4">
                                <p>{{$Header->processedby}}</p>
                        </div>
                        <label class="control-label col-md-2">{{ trans('message.form-label.processed_date') }}:</label>
                        <div class="col-md-4">
                                <p>{{$Header->purchased2_at}}</p>
                        </div>
                    </div>
                @endif

                @if($Header->ac_comments != null)
                    <div class="row">                           
                        <label class="control-label col-md-2">{{ trans('message.table.ac_comments') }}:</label>
                        <div class="col-md-8">
                                <p>{{$Header->ac_comments}}</p>
                        </div>
                    </div>
                @endif

            
 
            @if( $Header->pickedby != null )
                <hr/>
                <div class="row">                           
                    <label class="control-label col-md-2">{{ trans('message.form-label.picked_by') }}:</label>
                    <div class="col-md-4">
                            <p>{{$Header->pickedby}}</p>
                    </div>
                    <label class="control-label col-md-2">{{ trans('message.form-label.picked_at') }}:</label>
                    <div class="col-md-4">
                            <p>{{$Header->picked_at}}</p>
                    </div>
                </div>
            @endif

            @if( $Header->receivedby != null )
                <hr/>
                <div class="row">                           
                    <label class="control-label col-md-2">{{ trans('message.form-label.received_by') }}:</label>
                    <div class="col-md-4">
                            <p>{{$Header->receivedby}}</p>
                    </div>
                    <label class="control-label col-md-2">{{ trans('message.form-label.received_at') }}:</label>
                    <div class="col-md-4">
                            <p>{{$Header->received_at}}</p>
                    </div>
                </div>
            @endif

            @if( $Header->closedby != null )
                <hr/>
                <div class="row">                           
                    <label class="control-label col-md-2">{{ trans('message.form-label.closed_by') }}:</label>
                    <div class="col-md-4">
                            <p>{{$Header->closedby}}</p>
                    </div>
                    <label class="control-label col-md-2">{{ trans('message.form-label.closed_at') }}:</label>
                    <div class="col-md-4">
                            <p>{{$Header->closed_at}}</p>
                    </div>
                </div>
            @endif

        </div>

        <div class='panel-footer'>

            <a href="{{ CRUDBooster::mainpath() }}" class="btn btn-default">{{ trans('message.form.cancel') }}</a>

        </div>

    



</div>

@endsection
@push('bottom')
<script type="text/javascript">

    function preventBack() {
        window.history.forward();
    }
    window.onunload = function() {
        null;
    };
    setTimeout("preventBack()", 0);

    $('#btnSubmit').click(function() {

        var strconfirm = confirm("Are you sure you want to close this request?");
        if (strconfirm == true) {

            $(this).attr('disabled','disabled');

            $('#myform').submit(); 
            
        }else{
            return false;
            window.stop();
        }

    });

    var tableRow = <?php echo json_encode($tableRow); ?>;

    $(document).ready(function() {
            $(document).on('click', '.removeRow', function() {
                
                var strconfirm = confirm("Are you sure you want to remove this item?");
                if (strconfirm == true) {
                    if ($('#asset-items1 tbody tr').length != 1) { //check if not the first row then delete the other rows
                                  

                        $("#quantity_total").val(calculateTotalQuantity());

                        var id_data = $(this).attr("data-id");

                        item_id = $("#ids"+id_data).val();

                        $("#bodyID").val(item_id);

                        var data = $('#myform').serialize();

                        $.ajax
                        ({ 
                            url:  '{{ url('admin/header_request/RemoveItem') }}',
                            type: "GET",
                            data: data,
                            success: function(result)
                            {   
                                console.log( response ); 
                            }
                        });

                        $("#deleteRow"+id_data).attr('disabled', true);

                        tableRow--;

                        $(this).closest('tr').css('background-color','#d9534f');

                        $(this).closest('tr').css('color','white');

                    
                        return false;
                    }
                }else{
                    return false;
                    window.stop();
                }
                
            });
    });

        function calculateTotalQuantity() {
            var totalQuantity = 0;
            $('.quantity_item').each(function() {

            totalQuantity = parseInt($("#quantity_total").val()) - 1;
            });
            return totalQuantity;
        }
    
</script>
@endpush