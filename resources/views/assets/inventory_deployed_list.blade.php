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
                                                <table id='table_dashboard' class="table table-hover table-striped table-bordered">
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
                                                        
                                                        <!-- <tr class="tableInfo">
                                                            <td colspan="8" align="right"><strong>{{ trans('message.table.total') }}</strong></td>
                                                            <td align="center" colspan="1">

                                                                @if($item_count == 1)
                                                                        <label>{{$cost_total}}</label>
                                                                    @else
                                                                        <label>{{$Header->total}}</label>
                                                                @endif
                                                                        
                                                            </td>
                                                            <td colspan="1"></td>
                                                        </tr> -->
                                                    
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
@push('bottom')
    <script type="text/javascript">

    $("#table_dashboard").DataTable({
        pageLength:10000,
        pagingType: "simple",
        bPaginate: false,
        paging: false,
        info: false,
        dom : '<"pull-left"f><"pull-right"l>tip',
        language: {
            searchPlaceholder: "Search"
        }
    });
    </script>
@endpush