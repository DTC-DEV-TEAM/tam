@extends('crudbooster::admin_template')
@section('content')
    @push('head')
        <style type="text/css">   
            table, th, td {
            border: 1px solid rgba(000, 0, 0, .5);
            padding: 8px;
            border-radius: 5px 0 0 5px;
            }
            #asset-items th, td, tr {
                border: 1px solid rgba(000, 0, 0, .5);
                padding: 8px;
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
                        <p>{{$Header->approvedby}} / <strong>{{$Header->approved_at}}</strong></p>
                </div>
                @if($Header->approver_comments != null || $Header->approver_comments != "")          
                    <label class="control-label col-md-2">{{ trans('message.table.approver_comments') }}:</label>
                    <div class="col-md-4">
                            <p>{{$Header->approver_comments}}</p>
                    </div>
                @endif 
            </div>
            @endif      

            @if($Header->recommendedby != null || $Header->recommendedby != "")
                <hr/>
                <div class="row">                           
                    <label class="control-label col-md-2">{{ trans('message.form-label.recommended_by') }}:</label>
                    <div class="col-md-4">
                            <p>{{$Header->recommendedby}} / <strong>{{$Header->recommended_at}}</strong> </p>
                    </div>
                    @if($Header->it_comments != null || $Header->it_comments != "")                        
                        <label class="control-label col-md-2">{{ trans('message.table.it_comments') }}:</label>
                        <div class="col-md-4">
                                <p>{{$Header->it_comments}}</p>
                        </div>
                    @endif 
                </div>
            @endif 

            <hr />


            <div class="row">
                <div class="col-md-12">
                    <div class="box-header text-center">
                        <h3 class="box-title"><b>Item Tagged</b></h3>
                    </div>
                                <div class="box-body no-padding">
                                    <div class="table-responsive">
                                            <div class="pic-container">
                                                <div class="pic-row">
                                                    <table id="asset-items">
                                                        <tbody>
                                                            <tr class="tbl_header_color dynamicRows">
                                                                <th width="10%" class="text-center">{{ trans('message.table.mo_reference_number') }}</th>
                                                                <!-- <th width="13%" class="text-center">{{ trans('message.table.status_id') }}</th> -->
                                                                <th width="10%" class="text-center">{{ trans('message.table.digits_code') }}</th>
                                                                <th width="10%" class="text-center">{{ trans('message.table.asset_tag') }}</th>
                                                                <th width="32%" class="text-center">{{ trans('message.table.item_description') }}</th>
                                                                <th width="18%" class="text-center">{{ trans('message.table.serial_no') }}</th>
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

                                                                            <input type="hidden" value="{{$rowresult->inventory_id}}" name="inventory_id[]">

                                                                            {{$rowresult->mo_reference_number}}

                                                                        </td>
                                                                        
                                                                        <!--
                                                                        <td style="text-align:center" height="10">
    
                                                                            <label style="color: #3c8dbc;">
                                                                                {{$rowresult->status_description}}
                                                                            </label>
                                                                           
    
                                                                        </td>
                                                                        -->
    
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
    
                                                                        <td style="text-align:center" height="10" class="mo_qty">
                                                                            {{$rowresult->quantity}}
                                                                        </td>
    
                                                                        <td style="text-align:center" height="10" class="mo_unit_cost">
                                                                            {{$rowresult->unit_cost}}
                                                                        </td>
    
                                                                        <td style="text-align:center" height="10" class="mo_total_cost">
                                                                            {{$rowresult->total_unit_cost}}
                                                                        </td>
                                                                         
                                                                    </tr> 
                                                                @endforeach
    
    
                                                            @endif
                                                            
                                                            {{-- <tr class="tableInfo">
                                                                <td colspan="7" align="right"><strong>{{ trans('message.table.total') }}</strong></td>
                                                                <td align="center" colspan="1">
                                                                    <label>{{$Header->total}}</label>
                                                                </td>
                                                            </tr> --}}
                                                        
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                    </div>
                                    <br>
                                </div>
                </div>
            </div>
            <hr>
            @if( $Header->processedby != null )
                <div class="row">
                    <div class="col-md-6">
                        <table style="width:100%">
                            <tbody id="footer">
                                <tr>
                                    <th class="control-label col-md-2">{{ trans('message.form-label.mo_by') }}:</th>
                                    <td class="col-md-4">{{$Header->mo_by}} / {{$Header->mo_at}}</td>     
                                </tr>
                                @if($Header->ac_comments != null)
                                    <tr>
                                        <th class="control-label col-md-2">{{ trans('message.table.ac_comments') }}:</th>
                                        <td class="col-md-4">{{$Header->ac_comments}}</td>
                                    </tr>
                                @endif
                                @if( $Header->pickedby != null )
                                    <tr>
                                        <th class="control-label col-md-2">{{ trans('message.form-label.picked_by') }}:</th>
                                        <td class="col-md-4">{{$Header->pickedby}} / {{$Header->picked_at}}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="col-md-6">
                        <table style="width:100%">
                            <tbody id="footer">
                                @if( $Header->receivedby != null )
                                    <tr>
                                        <th class="control-label col-md-2">{{ trans('message.form-label.received_by') }}:</th>
                                        <td class="col-md-4">{{$Header->receivedby}} / {{$Header->received_at}}</td>
                                    </tr>
                                @endif
                                @if( $Header->closedby != null )
                                    <tr>
                                        <th class="control-label col-md-2">{{ trans('message.form-label.closed_by') }}:</th>
                                        <td class="col-md-4">{{$Header->closedby}} / {{$Header->closed_at}}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
 

        </div>


        <div class='panel-footer'>

            <a href="{{ CRUDBooster::mainpath() }}" class="btn btn-default">{{ trans('message.form.cancel') }}</a>
           
            
            <button class="btn btn-primary pull-right" type="submit" id="btnSubmit"> <i class="fa fa-save" ></i> {{ trans('message.form.receive') }}</button>

        
        </div>

    </form>



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

    $('#btnSubmit').click(function(event) {
        event.preventDefault();
        swal({
            title: "Are you sure?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#41B314",
            cancelButtonColor: "#F9354C",
            confirmButtonText: "Yes, receive it!",
            width: 450,
            height: 200
            }, function () {
                $(this).attr('disabled','disabled');
                $('#myform').submit();                                                  
        });

    });

    var tds = document.getElementById("asset-items").getElementsByTagName("td");
        var moQty        = 0;
        var moUnitCost   = 0;
        var moTotalCost  = 0;

        for (var i = 0; i < tds.length; i++) {
            if(tds[i].className == "mo_qty") {
                moQty += isNaN(tds[i].innerHTML) ? 0 : parseFloat(tds[i].innerHTML);
            }else if(tds[i].className == "mo_unit_cost"){
                moUnitCost += isNaN(tds[i].innerHTML) ? 0 : parseFloat(tds[i].innerHTML);
            }else if(tds[i].className == "mo_total_cost"){
                moTotalCost += isNaN(tds[i].innerHTML) ? 0 : parseFloat(tds[i].innerHTML);
            }
        }
        document.getElementById("asset-items").innerHTML +=
        "<tr>"+
            "<td colspan='5' style='text-align:right'>"+
                    "<strong>TOTAL</strong>"+
                "</td>"+
                
                "<td style='text-align:center'>"+
                    "<strong>" +
                        moQty +
                    "</strong>"+
                "</td>"+
                "<td style='text-align:center'>"+
                    "<strong>" +
                        moUnitCost +
                    "</strong>"+
                "</td>"+
                "<td style='text-align:center'>"+
                    "<strong>" +
                        moTotalCost +
                    "</strong>"+
                "</td>"+       
        "</tr>";

</script>
@endpush