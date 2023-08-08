@extends('crudbooster::admin_template')
    @push('head')
        <style type="text/css">   
          .modal-content  {
                -webkit-border-radius: 10px !important;
                -moz-border-radius: 10px !important;
                border-radius: 10px !important; 
            }
            #other-detail th, td {
                border: 1px solid rgba(000, 0, 0, .5);
                padding: 8px;
            }
            #item-sourcing-options th, td {
                border: 1px solid rgba(000, 0, 0, .5);
                padding: 8px;
            }
         
            .finput {
                border:none;
                /* border-bottom: 1px solid rgba(18, 17, 17, 0.5); */
            }
            .alink {
                border:none;
                /* border-bottom: 1px solid rgba(18, 17, 17, 0.5); */
            }
            input.finput:read-only {
                background-color: #fff;
            }
            .suggested{
                border:none;
                height: 10px;
                font-weight: bold;
                width: 63%;
                margin-top:4px;
                /* margin-left:10px; */
            }
            input.suggested:read-only {
                background-color: #fff;
            }


            .green-color {
                color:green;
                margin-top:12px;
            }
        
            .plus{
                font-size:20px;
            }
            #add-Row{
                border:none;
                background-color: #fff;
            }
          
            .iconPlus{
                background-color: #3c8dbc: 
            }
            
            .iconPlus:before {
                content: '';
                display: flex;
                justify-content: center;
                align-items: center;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                /* border: 1px solid rgb(194, 193, 193); */
                font-size: 35px;
                color: white;
                background-color: #3c8dbc;
       
            }
            #bigplus{
                transition: transform 0.5s ease 0s;
            }
            #bigplus:before {
                content: '\FF0B';
                background-color: #3c8dbc: 
                font-size: 50px;
            }
            #bigplus:hover{
                /* cursor: default;
                transform: rotate(180deg); */
                -webkit-animation: infinite-spinning 1s ease-out 0s infinite normal;
                 animation: infinite-spinning 1s ease-out 0s infinite normal;
               
            }

            @keyframes infinite-spinning {
                from {
                    transform: rotate(0deg);
                }
                to {
                    transform: rotate(360deg);
                }
            }

            table { border-collapse: collapse; empty-cells: show; }

            td { position: relative; }

            tr.strikeout td:before {
            content: " ";
            position: absolute;
            top: 50%;
            left: 0;
            border-bottom: 1px solid #111;
            width: 100%;
            
            }

            tr.strikeout td:after {
            content: "\00B7";
            font-size: 1px;
            }

            /* Extra styling */
            td { width: 100px; }
            th { text-align: left; }
            .selected {
                border:none;
                background-color:#d4edda
            }
            .selectedAlternative {
                border:none;
                background-color:#f0ad4e
            }

            .copyBtn{
                background-color:#fff;
                border: 1px solid #3c8dbc;
                border-radius: 5px !important; 
            }

            .cancelled {
                border:none;
                background-color:#dd4b39;
                color:#fff;
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
        Detail Form
    </div>

    <form method='post' id="myform" action='{{CRUDBooster::mainpath('edit-save/'.$Header->requestid)}}' enctype="multipart/form-data">
        <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
        <input type="hidden" value="0" name="action" id="action">
        <input type="hidden" value="" name="button_action" id="button_action">
        <input type="hidden" value="{{$Header->requestid}}" name="headerID" id="headerID">
        <input type="hidden" value="{{$Header->request_type_id}}" name="request_type_id" id="request_type_id">
        <input type="hidden" value="{{$countOptions}}" name="countRow" id="countRow">
        <input type="hidden" value="{{$allOptions}}" name="allcountOption" id="allcountOption">
        <input type="hidden" value="{{$Header->reference_number}}" name="ref_no" id="ref_no">
        

        <input type="hidden" value="" name="bodyID" id="bodyID">

        <div class='panel-body'>
            <section id="loading">
                <div id="loading-content"></div>
            </section>
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
                   @if($Header->header_created_by != null || $Header->header_created_by != "")
                        <p>{{$Header->employee_name}}</p>
                    @else
                    <p>{{$Header->header_emp_name}}</p>
                    @endif
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

            @if(in_array($Header->request_type_id,[6]))
                <div class="row">
                    <label class="control-label col-md-2">Color Proofing:</label>
                    <div class="col-md-4">
                            <p >{{$Header->sampling}}</p>
                    </div>
                            
                    <label class="control-label col-md-2">Mock Up:</label>
                    <div class="col-md-4">
                            <p>{{$Header->mark_up}}</p>
                    </div>
                </div>
                <div class="row">            
                    <label class="control-label col-md-2">Date Needed:</label>
                    <div class="col-md-4">
                            <p>{{$Header->date_needed}}</p>
                    </div>
                    <label class="control-label col-md-2">Artworklink:</label>
                    <div class="col-md-4">
                            <a href="{{$Header->artworklink}}" target="_blank"> <span style="word-wrap: break-word;">{{$Header->artworklink}}</span></a>
                    </div>
                </div>
            @endif
            @if(!in_array($Header->request_type_id,[6]))
                <div class="row">                          
                    @if($versions->version != null)
                        <label class="control-label col-md-2">Version:</label>
                        <div class="col-md-4">
                                <a type="button" value="{{$Header->requestid}}" id="getVersions" data-toggle="modal" data-target="#versionModal"><strong>{{$versions->version}}</strong></a>
                        </div>
                    @endif
                </div>
            @endif
            @if(in_array($Header->request_type_id,[6]))
                <div class="row">
                    <label class="control-label col-md-2">Uploaded Photos/Files:</label>
                    <div class="col-md-4">
                        <div class="flex-div">
                            @foreach($header_files as $header_file)                                    
                                @if(in_array($header_file->ext,['jpg','jpeg','png','gif']))
                                <a  href='{{CRUDBooster::adminpath("item-sourcing-header/download/".$header_file->id)."?return_url=".urlencode(Request::fullUrl())}}' class="alink"><img style="margin-left:10px;" width="120px"; height="90px"; src="{{URL::to('vendor/crudbooster/item_source_header_file').'/'.$header_file->file_name}}" alt="" data-action="zoom"> </a>   
                                @else
                                <a  href='{{CRUDBooster::adminpath("item-sourcing-header/download/".$header_file->id)."?return_url=".urlencode(Request::fullUrl())}}' class="alink">{{$header_file->file_name}} <i style="color:#007bff" class="fa fa-download"></i></a>     
                                @endif                                         
                            @endforeach
                        </div>
                    </div>
                    @if($versions->version != null)
                        <label class="control-label col-md-2">Version:</label>
                        <div class="col-md-4">
                                <a type="button" value="{{$Header->requestid}}" id="getVersions" data-toggle="modal" data-target="#versionModal"><strong>{{$versions->version}}</strong></a>
                        </div>
                    @endif
                </div>
                <br>
            @endif
            
            <div class="row">  
                <label class="control-label col-md-2">Status:</label>
                <div class="col-md-4">
                    <select required selected data-placeholder="-- Please Select ERF --" id="status" name="status" class="form-select" style="width:50%;">
                        @foreach($statuses as $res)
                        <option value="{{ $res->id }}"
                            {{ isset($Header->status_id) && $Header->status_id == $res->id ? 'selected' : '' }}>
                            {{ $res->status_description }} 
                        </option>>
                        @endforeach
                    </select>
                </div> 
                @if($Header->store_branch != null || $Header->store_branch != "")                        
                    <label class="control-label col-md-2">{{ trans('message.form-label.store_branch') }}:</label>
                    <div class="col-md-4">
                            <p>{{$Header->store_name}}</p>
                    </div>
                @endif
            </div>
           
            <div class="row">
                @if($Header->po_number != null)
                    <label class="control-label col-md-2">{{ trans('message.form-label.po_number') }}:</label>
                        <div class="col-md-4">
                            <p >{{$Header->po_number}}</p>
                    </div>
                @endif
            </div>
         
            <hr/>                
            <div class="row">
                <div class="col-md-12">
                    <div class="box-header text-center">
                        <h3 class="box-title"><b>Item Source</b></h3>
                    </div>
                    <div class="box-body no-padding">
                        <div class="table-responsive">
                            <div class="pic-container">
                                <div class="pic-row">
                                    <table class="table table-bordered" id="item-sourcing">
                                        <tbody id="bodyTable">
                                            <tr class="tbl_header_color dynamicRows">
                                                @if(!in_array($Header->request_type_id,[6])) 
                                                  <th width="5%" class="text-center">Digits Code</th>
                                                @endif
                                                @if(in_array($Header->request_type_id,[1,5,7])) 
                                                <th width="12%" class="text-center">Category</th> 
                                                <th width="12%" class="text-center">Sub Category</th>
                                                <th width="12%" class="text-center">Class</th> 
                                                <th width="12%" class="text-center">Sub Class</th> 
                                                @endif
                                                <th width="12%" class="text-center">{{ trans('message.table.item_description') }}</th>   
                                                <th width="7%" class="text-center">Brand</th> 
                                                <th width="7%" class="text-center">Model</th>  
                                                <th width="7%" class="text-center">Size(L x W x H in cm)</th> 
                                                <th width="7%" class="text-center">Actual Color</th>   
                                                @if(in_array($Header->request_type_id,[6]))
                                                    <th width="7%" class="text-center">Material</th> 
                                                    <th width="7%" class="text-center">Thickness</th> 
                                                    <th width="7%" class="text-center">Lamination</th>
                                                    <th width="7%" class="text-center">Add Ons</th>
                                                    <th width="7%" class="text-center">Installation</th>
                                                    <th width="7%" class="text-center">Dismantling</th>    
                                                @endif   
                                                <th width="2%" class="text-center">Quantity</th> 
                                                @if(!in_array($Header->request_type_id,[6]))                                                                                                                
                                                    <th width="10%" class="text-center">Budget</th> 
                                                @endif
                                            </tr>
                                            <tr id="tr-table">
                                                <?php   $tableRow = 1; ?>
                                            
                                                    @foreach($Body as $rowresult)
                                                    @if(in_array($Header->request_type_id,[1,5,7])) 
                                                    <tr>
                                                        <input type="hidden"  class="form-control"  name="ids[]" id="ids{{$tableRow}}"  required  value="{{$rowresult->id}}" readonly>        
                                                        @if(!in_array($Header->request_type_id,[6])) 
                                                            <td style="text-align:center" height="10">
                                                                {{$rowresult->digits_code}}                               
                                                            </td>
                                                        @endif
                                                        <td style="text-align:center" height="10">
                                                            {{$rowresult->category_description}}                               
                                                        </td>
                                                        <td style="text-align:center" height="10">
                                                            {{$rowresult->sub_category_description}}                              
                                                        </td>
                                                        <td style="text-align:center" height="10">
                                                            {{$rowresult->class_description}}                               
                                                        </td>
                                                        <td style="text-align:center" height="10">
                                                            {{$rowresult->sub_class_description}}                               
                                                        </td>
                                                                                            
                                                        <td style="text-align:center" height="10">                                                             
                                                            {{$rowresult->item_description}} 
                                                        </td>
                                                        <td style="text-align:center" height="10">                                                             
                                                            {{$rowresult->brand}} 
                                                        </td>
                                                        <td style="text-align:center" height="10">                                                             
                                                            {{$rowresult->model}} 
                                                        </td>
                                                        <td style="text-align:center" height="10">                                                             
                                                            {{$rowresult->size}} 
                                                        </td>
                                                        <td style="text-align:center" height="10">                                                             
                                                            {{$rowresult->actual_color}}  
                                                        </td>                       
                                                        <td style="text-align:center" height="10" class="qty">
                                                            {{$rowresult->quantity}} 
                                                        </td>     
                                                        @if(!in_array($Header->request_type_id,[6]))   
                                                            <td style="text-align:center" height="10" class="cost">
                                                                    {{$rowresult->budget}}
                                                            </td> 
                                                        @endif                                                                                                            
                                                    </tr>
                                                    @else
                                                        <tr>
                                                            <input type="hidden"  class="form-control"  name="ids[]" id="ids{{$tableRow}}"  required  value="{{$rowresult->id}}" readonly>        
                                                            @if(!in_array($Header->request_type_id,[6])) 
                                                                <td style="text-align:center" height="10">
                                                                    {{$rowresult->digits_code}}                               
                                                                </td>
                                                            @endif                                                                                                                                                     
                                                            <td style="text-align:center" height="10">                                                             
                                                                {{$rowresult->item_description}} 
                                                            </td>
                                                            <td style="text-align:center" height="10">                                                             
                                                                {{$rowresult->brand}} 
                                                            </td>
                                                            <td style="text-align:center" height="10">                                                             
                                                                {{$rowresult->model}} 
                                                            </td>
                                                            <td style="text-align:center" height="10">                                                             
                                                                {{$rowresult->size}} 
                                                            </td>
                                                            <td style="text-align:center" height="10">                                                             
                                                                {{$rowresult->actual_color}}  
                                                            </td>
                                                            @if(in_array($Header->request_type_id,[6]))
                                                                <td style="text-align:center" height="10">
                                                                    {{$rowresult->material}}                               
                                                                </td>
                                                                <td style="text-align:center" height="10">
                                                                    {{$rowresult->thickness}}                               
                                                                </td>
                                                                <td style="text-align:center" height="10">
                                                                    {{$rowresult->lamination}}                               
                                                                </td>
                                                                <td style="text-align:center" height="10">
                                                                    {{$rowresult->add_ons}}                               
                                                                </td>
                                                                <td style="text-align:center" height="10">
                                                                    {{$rowresult->installation}}                               
                                                                </td>
                                                                <td style="text-align:center" height="10">
                                                                    {{$rowresult->dismantling}}                               
                                                                </td>
                                                            @endif
                                                            <td style="text-align:center" height="10" class="qty">
                                                                {{$rowresult->quantity}} 
                                                            </td>  
                                                            @if(!in_array($Header->request_type_id,[6]))   
                                                                <td style="text-align:center" height="10" class="cost">
                                                                        {{$rowresult->budget}}
                                                                </td> 
                                                            @endif                                                                                                          
                                                        </tr>
                                                    @endif

                                                    @endforeach     
                                                    
                                                    <input type='hidden' name="quantity_total" class="form-control text-center" id="quantity_total" readonly value="{{$Header->quantity_total}}">
                                                </tr>
                                            
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    <table class="table" id="item-sourcing-options">
                        <tbody id="bodyTable">    
                            <tr>
                                <th width="10%" class="text-center">Option</th> 
                                <th width="15%" class="text-center">Vendor Name</th>
                                <th width="10%" class="text-center">Total Price</th> 
                                <th width="20%" class="text-center">Quotation</th> 
                                <th width="5%" class="text-center"><i class="fa fa-trash"></i></th>
                            </tr>       
                                <?php   $tableRow = 1; ?>
                                @foreach($item_options as $res)
                                <?php   $tableRow1++; ?>
                                    @if($res->deleted_at != null && $res->selected_alternative_at == null)
                                      <tr style="background-color: #dd4b39; color:#fff">                                    
                                        <td style="text-align:center" height="10">
                                            {{$res->options}}                               
                                        </td>
                                        <td style="text-align:center" height="10">
                                            {{$res->vendor_name}}                               
                                        </td>
                                        <td style="text-align:center" height="10">
                                            {{number_format($res->price, 2, '.', ',')}}                             
                                        </td>
                                        <td style="text-align:center;" height="10">
                                            <a  href='{{CRUDBooster::adminpath("item_sourcing_for_quotation/download/".$res->file_id)."?return_url=".urlencode(Request::fullUrl())}}' class="form-control cancelled">{{$res->file_name}}   <i style="color:#007bff" class="fa fa-download"></i></a>                             
                                        </td>
                                        <td colspan="2" style="text-align:center; color:#fff">
                                            <i data-toggle="tooltip" data-placement="right" title="Cancelled" class="fa fa-times-circle"></i>
                                        </td>                               
                                      </tr>
                                    @elseif($res->selected_at != null || $res->selected_at != "")
                                      <tr style="background-color: #d4edda; color:#155724">                                    
                                        <td style="text-align:center" height="10">
                                            {{$res->options}}                               
                                        </td>
                                        <td style="text-align:center" height="10">
                                            {{$res->vendor_name}}                               
                                        </td>
                                        <td style="text-align:center" height="10">
                                            {{number_format($res->price, 2, '.', ',')}}                               
                                        </td>
                                        <td style="text-align:center;" height="10">
                                            <a  href='{{CRUDBooster::adminpath("item_sourcing_for_quotation/download/".$res->file_id)."?return_url=".urlencode(Request::fullUrl())}}' class="form-control selected">{{$res->file_name}}   <i style="color:#007bff" class="fa fa-download"></i></a>                             
                                        </td>
                                        <td colspan="2"  style="text-align:center; color:white">
                                            <i data-toggle="tooltip" data-placement="right" title="Selected" class="fa fa-check-circle text-success"></i>
                                        </td>                               
                                      </tr>
                                    @elseif($res->selected_alternative_at != null)
                                    <tr style="background-color: #f0ad4e; color:#fff">                                    
                                        <td style="text-align:center" height="10">
                                            {{$res->options}}                               
                                        </td>
                                        <td style="text-align:center" height="10">
                                            {{$res->vendor_name}}                               
                                        </td>
                                        <td style="text-align:center" height="10">
                                            {{number_format($res->price, 2, '.', ',')}}                               
                                        </td>
                                        <td style="text-align:center;" height="10">
                                            <a style="color:#fff" href='{{CRUDBooster::adminpath("item_sourcing_for_quotation/download/".$res->file_id)."?return_url=".urlencode(Request::fullUrl())}}' class="form-control selectedAlternative">{{$res->file_name}}   <i style="color:#007bff" class="fa fa-download"></i></a>                             
                                        </td>
                                        <td colspan="3"  style="text-align:center;">
                                            <i data-toggle="tooltip" data-placement="right" title="Selected Alternative" class="fa fa-check-circle text-white"></i>
                                        </td>                               
                                    </tr>
                                   @else
                                    <tr id="tr-tableOption">                                    
                                        <td style="text-align:center" height="10">
                                            <input type="hidden"  class="form-control"  name="opt_id" id="opt_id"  required  value="{{$res->optId}}" readonly>  
                                            {{$res->options}}                               
                                        </td>
                                        <td style="text-align:center" height="10">
                                            {{$res->vendor_name}}                               
                                        </td>
                                        <td style="text-align:center" height="10">
                                            {{number_format($res->price, 2, '.', ',')}}                               
                                        </td>
                                        <td style="text-align:center" height="10">
                                            <a  href='{{CRUDBooster::adminpath("item_sourcing_for_quotation/download/".$res->file_id)."?return_url=".urlencode(Request::fullUrl())}}' class="form-control alink">{{$res->file_name}}   <i style="color:#007bff" class="fa fa-download"></i></a>                             
                                        </td>
                                        <td>
                                            {{-- <button id="deleteRow" name="removeRow" data-id="' + tableRow + '" class="btn btn-danger removeRow"><i class="glyphicon glyphicon-trash"></i></button> --}}
                                        
                                        </td>
                                    </tr>
                                   @endif
                                @endforeach                             
                         
                        
                        </tbody>
                        <tfoot>
                            @if($Header->if_selected == null)
                                <tr id="tr-tableOption1" class="bottom">
                                    <td style="text-align:left" colspan="5">
                                        <button class="red-tooltip" data-toggle="tooltip" data-placement="right" id="add-Row" name="add-Row" title="Add Row"><div class="iconPlus" id="bigplus"></div></button>
                                        <div id="display_error" style="text-align:left"></div>
                                    </td>
                                </tr>
                            @else
                            <tr id="tr-tableOption1" class="bottom">
                                <td style="text-align:center" colspan="5">
                                    <span class="label label-success">Already Selected <i class="fa fa-check"></i> </span>
                                </td>
                            </tr>
                            @endif
                        </tfoot>

                    </table>
                </div>   
            </div>
            <hr>

            <div class="row">
                @include('item-sourcing.comments',['comments'=>$comments])
                @include('item-sourcing.other_detail',['Header'=>$Header])
            </div>
        </div>

        <div id="myModal" class="modal fade" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><strong>Input PO</strong></h4>
                       
                    </div>
                    <div class="modal-body">
                       <div class='row'>
                         <div class='col-md-12'>
                          <input oninput="validate(this)" type"text" class="form-control" name="po_no"  id="po_no" placeholder="Please input PO">
                         </div>
                         <br>	
                       </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type='button' id="closed" class="btn btn-primary btn-sm">
                         <i class="fa fa-save"></i> Close
                         </button>
                    </div>
                </div>
            </div>
        </div>

       {{-- FOR DIGITS CODE CREATION --}}
        <div id="itemCreationModal" class="modal fade" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title text-center"><strong>Input Digits Code</strong></h4>
                       
                    </div>
                    <div class="modal-body">
                       <div class='row'>
                         <div class='col-md-12'>
                            @if(in_array($Header->request_type_id, [1,5,7]))
                             <input oninput="validate(this)" type"text" class="form-control" name="digits_code"  id="digits_code" placeholder="Please input Digits Code" onKeyPress="if(this.value.length==8) return false;">
                            @else
                             <input oninput="validate(this)" type"text" class="form-control" placeholder="Please input Digits Code" onKeyPress="if(this.value.length==8) return false;">
                            @endif
                        </div>
                         <br>	
                       </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type='button' id="digitsCodeBtn" class="btn btn-primary btn-sm">
                         <i class="fa fa-save"></i> Save</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class='panel-footer'>

            <a href="{{ CRUDBooster::mainpath() }}" id="btn-cancel" class="btn btn-default">{{ trans('message.form.back') }}</a>
            {{-- <button class="btn btn-success pull-right" type="button" id="btnClose" style="margin-left: 5px;"><i class="fa fa-times-circle" ></i> Close</button> --}}
            <button class="btn btn-primary pull-right" type="button" id="btnUpdate"><i class="fa fa-refresh" ></i> Update</button>
        </div>
    </form>
</div>

  {{-- Modal Edi Version --}}
@include('item-sourcing.modal-edit-version')

@endsection
@push('bottom')
<script src="https://cdn.jsdelivr.net/npm/clipboard@2.0.8/dist/clipboard.min.js"></script>
<script type="text/javascript">
    
    $(function(){
        $('body').addClass("sidebar-collapse");
        item_source_value();
        var deleteRow = $('#countRow').val();
        var rowCount = $('#item-sourcing-options tr').length-2-deleteRow;
        if(rowCount > 3){
              $('#add-Row').prop("disabled", true)
        }else{
            $('#add-Row').prop("disabled", false)
        }
    });

    function preventBack() {
        window.history.forward();
    }

    function validate(input){
     if(/^\s/.test(input.value))
        input.value = '';
    }

    window.onunload = function() {
        null;
    };
  
    $('.chat').scrollTop($('.chat')[0].scrollHeight);
    
    $('#status').select2({})
    setTimeout("preventBack()", 0);
    var searchcount = <?php echo json_encode($tableRow); ?>;
    let countrow = 1;
    var tableRow = 1;

    //validation value
    $(function(){
        for (let i = 0; i < searchcount; i++) {
            countrow++;
            $('#po_date'+countrow).datepicker({
                constrainInput: false,  
                dateFormat: 'yy-mm-dd'
              
            });
            $('#qoute_date'+countrow).datepicker({
                constrainInput: false,  
                dateFormat: 'yy-mm-dd'
               
            });
            $('#po_date'+countrow).keyup(function() {
                    this.value = this.value.toLocaleUpperCase();
            });
            $('#qoute_date'+countrow).keyup(function() {
                    this.value = this.value.toLocaleUpperCase();
            });
               
        }
        
    });

    //Chat
    $('#btnChat').click(function() {
        event.preventDefault();
       
        var header_id = $('#headerID').val();
        var message = $('#message').val();
        if ($('#message').val() === "") {
            swal({
                type: 'error',
                title: 'Message Required',
                icon: 'error',
                confirmButtonColor: "#367fa9",
            }); 
            event.preventDefault(); // cancel default behavior
        }else{
            $.ajax({
                url: "{{ route('save-message') }}",
                type: "POST",
                dataType: 'json',

                data: {
                    "_token": token,
                    "header_id" : header_id,
                    "message": message,
                },
                success: function (data) {
                    if (data.status == "success") {
                        $('.body-comment').append(
                                            '<strong style="margin-left: 95%">Me</strong>' +
                                            '<span class="session-comment"> ' +
                                            '<p><span class="comment">'+data.message.comments +'</span> </p>'+
                                            '<p style="text-align:right; font-size:12px; font-style: italic; padding-right:5px;"> '+ new Date(data.message.created_at) +'</p></span>');
                        $('#message').val('');
                    }
                    var interval = setTimeout(function() {
                     $('.chat').scrollTop($('.chat')[0].scrollHeight);
                    },200);
                }
                 
            });
           
        }
    
    });

    $('#status').change(function(){
        var status =  this.value;
        if(status == 13){
            $("#myModal").modal('show');	
        }else if(status == 41){
            $("#itemCreationModal").modal('show');
        }
        else{
            $("#itemCreationModal").modal('hide');
            $("#myModal").modal('hide');
        }
    });

    $('#myModal').on('hidden.bs.modal', function () {
      location.reload();
    });
    $('#itemCreationModal').on('hidden.bs.modal', function () {
      location.reload();
    });

    //Get Edit Verions
    $('#getVersions').click(function(evennt) {
        event.preventDefault();
        var header_id = $('#headerID').val();
        var reques_type_id = $('#request_type_id').val();
        if(reques_type_id == 6){
            $.ajax({
                url: "{{ route('get-versions') }}",
                type: "GET",
                dataType: 'json',

                data: {
                    "_token": token,
                    "header_id" : header_id
                },
                success: function (data) {
                    $.each(data, function(i, item) {
                        $('#appendVersions').append(
                    '<tr>' +
                        '<tr>' +
                
                            '<td colspan="4" style="background-color:#3c8dbc; color:white; font-weight:bold">' + item.version + '</td>' +
                        '</tr>' +

                        '<tr>' +
                            '<th style="padding-top:25px" rowspan="2">Description</th>' +
                            '<th colspan="2">' + 'From' + '</th>' +
                            '<th colspan="2">' + 'To' + '</th>' +
                        '</tr>' +

                        '<tr>'  +
                            '<td colspan="2">' + item.old_description + '</td>' +
                            '<td colspan="2">' + item.new_description + '</td>' +
                        '</tr>' +

                        '<tr>' +
                            '<th style="padding-top:25px" rowspan="2">Brand</th>' +
                            '<th colspan="2">' + 'From' + '</th>' +
                            '<th colspan="2">' + 'To' + '</th>' +
                        '</tr>' +
                        '<tr>'  +
                            '<td colspan="2">' + item.old_brand_value + '</td>' +
                            '<td colspan="2">' + item.new_brand_value + '</td>' +
                        '</tr>' +

                        
                        '<tr>' +
                            '<th style="padding-top:25px" rowspan="2">Model</th>' +
                            '<th colspan="2">' + 'From' + '</th>' +
                            '<th colspan="2">' + 'To' + '</th>' +
                        '</tr>' +
                        '<tr>'  +
                            '<td colspan="2">' + item.old_model_value + '</td>' +
                            '<td colspan="2">' + item.new_model_value + '</td>' +
                        '</tr>' +

                        '<tr>' +
                            '<th style="padding-top:25px" rowspan="2">Size</th>' +
                            '<th colspan="2">' + 'From' + '</th>' +
                            '<th colspan="2">' + 'To' + '</th>' +
                        '</tr>' +
                        '<tr>'  +
                            '<td colspan="2">' + item.old_size_value + '</td>' +
                            '<td colspan="2">' + item.new_size_value + '</td>' +
                        '</tr>' +

                        
                        '<tr>' +
                            '<th style="padding-top:25px" rowspan="2">Actual Color</th>' +
                            '<th colspan="2">' + 'From' + '</th>' +
                            '<th colspan="2">' + 'To' + '</th>' +
                        '</tr>' +
                        '<tr>'  +
                            '<td colspan="2">' + item.old_ac_value + '</td>' +
                            '<td colspan="2">' + item.new_ac_value + '</td>' +
                        '</tr>' +

                        '<tr>' +
                            '<th style="padding-top:25px" rowspan="2">Material</th>' +
                            '<th colspan="2">' + 'From' + '</th>' +
                            '<th colspan="2">' + 'To' + '</th>' +
                        '</tr>' +
                        '<tr>'  +
                            '<td colspan="2">' + item.old_material + '</td>' +
                            '<td colspan="2">' + item.new_material + '</td>' +
                        '</tr>' +

                        '<tr>' +
                            '<th style="padding-top:25px" rowspan="2">Thickness</th>' +
                            '<th colspan="2">' + 'From' + '</th>' +
                            '<th colspan="2">' + 'To' + '</th>' +
                        '</tr>' +
                        '<tr>'  +
                            '<td colspan="2">' + item.old_thickness + '</td>' +
                            '<td colspan="2">' + item.new_thickness + '</td>' +
                        '</tr>' +

                        '<tr>' +
                            '<th style="padding-top:25px" rowspan="2">Lamination</th>' +
                            '<th colspan="2">' + 'From' + '</th>' +
                            '<th colspan="2">' + 'To' + '</th>' +
                        '</tr>' +
                        '<tr>'  +
                            '<td colspan="2">' + item.old_lamination + '</td>' +
                            '<td colspan="2">' + item.new_lamination + '</td>' +
                        '</tr>' +

                        '<tr>' +
                            '<th style="padding-top:25px" rowspan="2">Add Ons</th>' +
                            '<th colspan="2">' + 'From' + '</th>' +
                            '<th colspan="2">' + 'To' + '</th>' +
                        '</tr>' +
                        '<tr>'  +
                            '<td colspan="2">' + item.old_add_ons + '</td>' +
                            '<td colspan="2">' + item.new_add_ons + '</td>' +
                        '</tr>' +

                        '<tr>' +
                            '<th style="padding-top:25px" rowspan="2">Installation</th>' +
                            '<th colspan="2">' + 'From' + '</th>' +
                            '<th colspan="2">' + 'To' + '</th>' +
                        '</tr>' +
                        '<tr>'  +
                            '<td colspan="2">' + item.old_installation + '</td>' +
                            '<td colspan="2">' + item.new_installation + '</td>' +
                        '</tr>' +

                        '<tr>' +
                            '<th style="padding-top:25px" rowspan="2">Dismantling</th>' +
                            '<th colspan="2">' + 'From' + '</th>' +
                            '<th colspan="2">' + 'To' + '</th>' +
                        '</tr>' +
                        '<tr>'  +
                            '<td colspan="2">' + item.old_dismantling + '</td>' +
                            '<td colspan="2">' + item.new_dismantling + '</td>' +
                        '</tr>' +

                        '<tr>' +
                            '<th style="padding-top:25px" rowspan="2">Quantity</th>' +
                            '<th colspan="2">' + 'From' + '</th>' +
                            '<th colspan="2">' + 'To' + '</th>' +
                        '</tr>' +
                        '<tr>'  +
                            '<td colspan="2">' + item.old_qty_value + '</td>' +
                            '<td colspan="2">' + item.new_qty_value + '</td>' +
                        '</tr>' +

                        '<tr>' +
                            '<th>Updated Date</th>' +
                            '<td colspan="3">' + item.updated_at + '</td>' +
                        '</tr>' +

                        '<tr>' +
                            '<th>Updated By</th>' +
                            '<td colspan="3">' + item.name + '</td>' +
                        '</tr>' +
                    '</tr>'
                        );
                    });
                }
            
            });
        }else{
            $.ajax({
                url: "{{ route('get-versions') }}",
                type: "GET",
                dataType: 'json',

                data: {
                    "_token": token,
                    "header_id" : header_id
                },
                success: function (data) {
                    $.each(data, function(i, item) {
                        $('#appendVersions').append(
                    '<tr>' +
                        '<tr>' +
                
                            '<td colspan="4" style="background-color:#3c8dbc; color:white; font-weight:bold">' + item.version + '</td>' +
                        '</tr>' +

                        '<tr>' +
                            '<th style="padding-top:25px" rowspan="2">Description</th>' +
                            '<th colspan="2">' + 'From' + '</th>' +
                            '<th colspan="2">' + 'To' + '</th>' +
                        '</tr>' +

                        '<tr>'  +
                            '<td colspan="2">' + item.old_description + '</td>' +
                            '<td colspan="2">' + item.new_description + '</td>' +
                        '</tr>' +

                        '<tr>' +
                            '<th style="padding-top:25px" rowspan="2">Brand</th>' +
                            '<th colspan="2">' + 'From' + '</th>' +
                            '<th colspan="2">' + 'To' + '</th>' +
                        '</tr>' +
                        '<tr>'  +
                            '<td colspan="2">' + item.old_brand_value + '</td>' +
                            '<td colspan="2">' + item.new_brand_value + '</td>' +
                        '</tr>' +

                        
                        '<tr>' +
                            '<th style="padding-top:25px" rowspan="2">Model</th>' +
                            '<th colspan="2">' + 'From' + '</th>' +
                            '<th colspan="2">' + 'To' + '</th>' +
                        '</tr>' +
                        '<tr>'  +
                            '<td colspan="2">' + item.old_model_value + '</td>' +
                            '<td colspan="2">' + item.new_model_value + '</td>' +
                        '</tr>' +

                        '<tr>' +
                            '<th style="padding-top:25px" rowspan="2">Size</th>' +
                            '<th colspan="2">' + 'From' + '</th>' +
                            '<th colspan="2">' + 'To' + '</th>' +
                        '</tr>' +
                        '<tr>'  +
                            '<td colspan="2">' + item.old_size_value + '</td>' +
                            '<td colspan="2">' + item.new_size_value + '</td>' +
                        '</tr>' +

                        
                        '<tr>' +
                            '<th style="padding-top:25px" rowspan="2">Actual Color</th>' +
                            '<th colspan="2">' + 'From' + '</th>' +
                            '<th colspan="2">' + 'To' + '</th>' +
                        '</tr>' +
                        '<tr>'  +
                            '<td colspan="2">' + item.old_ac_value + '</td>' +
                            '<td colspan="2">' + item.new_ac_value + '</td>' +
                        '</tr>' +

                        '<tr>' +
                            '<th style="padding-top:25px" rowspan="2">Quantity</th>' +
                            '<th colspan="2">' + 'From' + '</th>' +
                            '<th colspan="2">' + 'To' + '</th>' +
                        '</tr>' +
                        '<tr>'  +
                            '<td colspan="2">' + item.old_qty_value + '</td>' +
                            '<td colspan="2">' + item.new_qty_value + '</td>' +
                        '</tr>' +

                        '<tr>' +
                            '<th>Updated Date</th>' +
                            '<td colspan="3">' + item.updated_at + '</td>' +
                        '</tr>' +

                        '<tr>' +
                            '<th>Updated By</th>' +
                            '<td colspan="3">' + item.name + '</td>' +
                        '</tr>' +
                    '</tr>'
                        );
                    });
                }
            
            });
        }
       
    });

    $('#versionModal').on('hidden.bs.modal', function () {
        $("#modal-version tbody").html("");
    });

    //Add Row
    $("#add-Row").click(function() {
        event.preventDefault();
        var vendor_name = "";
        var price = "";
        var count_fail = 0;
        tableRow++;

        $('.vendor_name').each(function() {
            vendor_name = $(this).val();
            if (vendor_name == null) {
                swal({  
                    type: 'error',
                    title: 'Please fill all Fields!',
                    icon: 'error',
                    confirmButtonColor: "#367fa9",
                });
                count_fail++;

            } else if (vendor_name == "") {
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
        $('.price').each(function() {
            price = $(this).val();
            if (price == null) {
                swal({  
                    type: 'error',
                    title: 'Please fill all Fields!',
                    icon: 'error',
                    confirmButtonColor: "#367fa9",
                });
                count_fail++;

            } else if (price == "") {
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
        $('.optionFile').each(function() {
            optFile = $(this).val();
            if (optFile == null) {
                swal({  
                    type: 'error',
                    title: 'Please fill all Fields!',
                    icon: 'error',
                    confirmButtonColor: "#367fa9",
                });
                count_fail++;

            } else if (optFile == "") {
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
        var deleteRow = $('#countRow').val();
        var allOptionsCount =  parseInt($('#allcountOption').val());
       
        var rowCount = $('#item-sourcing-options tr').length - 1 - deleteRow;
        
        var rowCountOption = $('#item-sourcing-options tr').length - 2;
        var finalOptCount = ((rowCountOption + 1));
        var ref_no = $('#ref_no').val();
        if(count_fail == 0){
            if(rowCount > 3){
              $('#add-Row').prop("disabled", true);
              $('#display_error').html("<span id='notif' class='label label-danger'> More than 3 options not allowed!</span>")
            }else{
                $('#add-Row').prop("disabled", false);
                $('#display_error').html("");
                var newrow =
                '<tr>' +

                    '<td >' +
                    '<input type="text" placeholder="Option..." onkeyup="this.value = this.value.toUpperCase();" class="form-control finput text-center" data-id="' + tableRow + '" id="option' + tableRow + '"  name="option[]" value="OPTION '+ finalOptCount +'"  required maxlength="100" style="width:100%" readonly>' +
                    '</td>' +  

                    '<td>' +
                    '<input class="form-control text-center finput vendor_name" type="text" onkeyup="this.value = this.value.toUpperCase();" placeholder="Vendor Name..." name="vendor_name[]" id="vendor_name' + tableRow + '" data-id="' + tableRow  + '" style="width:100%">' + 
                    '</td>' +

                    '<td>' +
                    '<input class="form-control text-center finput price" type="text" placeholder="Price..." name="price[]" id="price' + tableRow + '" data-id="' + tableRow  + '"  max="9999999999" step="any" onkeypress="return event.charCode <= 57" style="width:100%">' +
                    '</td>' +

                    '<td>' +
                    '<span style="display:none" id="copy-text'+tableRow+'">'+ref_no+'-OPTION '+ finalOptCount +'</span>' +
                    '<input class="form-control finput optionFile" type="file" placeholder="File..." name="optionFile[]" id="optionFile' + tableRow + '" data-id="' + tableRow  + '" style="width:100%">' + 
                    '<div style="display:flex;align-content: flex-center;">Filename: <input type="text" name="fileName[]" class="form-control text-center suggested" id="fileName' + tableRow +' "  value="'+ref_no+'-OPTION '+ finalOptCount +'" readonly><button type="button" data-toggle="tooltip" data-placement="right" title="Copy to Clipboard" class="copyBtn" id="btn" onclick="CopyThis();"> <i style="color:#3c8dbc" class="fa fa-clipboard"></i> </button> <span class="label label-primary" style="margin-left:2px; padding-top:6px" id="text-copied'+tableRow+'"></span></div>' +
                    
                    '</td>' +

                    '<td>' +
                        '<button id="deleteRow" name="removeRow" data-id="' + tableRow + '" class="btn btn-danger btn-sm removeRow"><i class="glyphicon glyphicon-trash"></i></button>' +
                    '</td>' +

                '</tr>';
                $('#item-sourcing-options tbody').append(newrow);
        }
     }

    });

    function CopyThis(){
        console.time('time1');
        var temp = $("<input>");
        $("body").append(temp);
        temp.val($('#copy-text'+tableRow).text()).select();
        document.execCommand("copy");
        $('#text-copied'+tableRow).text('text copied');
        setTimeout(function(){ $('#text-copied'+tableRow).text('');}, 2000);
        temp.remove();
            console.timeEnd('time1');
    }

      //cost fields validation
      $(document).on("keyup",".price", function (e) {
        if (e.which >= 37 && e.which <= 40) return;
                if (this.value.charAt(0) == ".") {
                    this.value = this.value.replace(
                    /\.(.*?)(\.+)/,
                    function (match, g1, g2) {
                        return "." + g1;
                    }
                    );
                }
                if (e.key == "." && this.value.split(".").length > 2) {
                    this.value =
                    this.value.replace(/([\d,]+)([\.]+.+)/, "$1") +
                    "." +
                    this.value.replace(/([\d,]+)([\.]+.+)/, "$2").replace(/\./g, "");
                    return;
                }
            $(this).val(function (index, value) {
                value = value.replace(/[^-0-9.]+/g, "");
                let parts = value.toString().split(".");
                parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                return parts.join(".");
            });
    });  

    //deleteRow
    $(document).on('click', '.removeRow', function() {
        if ($('#asset-items tbody tr').length != 1) { //check if not the first row then delete the other rows
            tableRow--;

            $(this).closest('tr').remove();
            var deleteRow = $('#countRow').val();
            var rowCount = $('#item-sourcing-options tr').length-1-deleteRow;
            if(rowCount > 3){
            $('#add-Row').prop("disabled", true)
            }else{
            $('#add-Row').prop("disabled", false)
            $('#display_error').html("");
            }
            return false;
        }
    });
  
    var stack = [];
    var token = $("#token").val();
   
    //Submit Request
    $('#btnUpdate').click(function(event) {
        event.preventDefault(); // cancel default behavior
        var rowCount = $('#item-sourcing-options tr').length-1;
        tableRow++;
        // if(rowCount == 1) {
        //     swal({
        //         type: 'error',
        //         title: 'Please add an item!',
        //         icon: 'error',
        //         confirmButtonColor: "#367fa9",
        //     }); 
        //     event.preventDefault(); // cancel default behavior
        //     return false;
        // }else{
       
 
            if(rowCount != 1){
              
                var vendor = $("input[name^='vendor_name']").length;
                var vendor_value = $("input[name^='vendor_name']");
                for(i=0;i<vendor;i++){
                    if(vendor_value.eq(i).val() == 0 || vendor_value.eq(i).val() == null){
                        swal({  
                                type: 'error',
                                title: 'Vendor Name Fields cannot be empty!',
                                icon: 'error',
                                confirmButtonColor: "#367fa9",
                            });
                            event.preventDefault();
                            return false;
                    } 
            
                } 
                var price = $("input[name^='price']").length;
                var price_value = $("input[name^='price']");
                for(i=0;i<price;i++){
                    if(price_value.eq(i).val() == 0 || price_value.eq(i).val() == null){
                        swal({  
                                type: 'error',
                                title: 'Price Fields cannot be empty!',
                                icon: 'error',
                                confirmButtonColor: "#367fa9",
                            });
                            event.preventDefault();
                            return false;
                    } 
            
                } 
                var optionFile = $("input[name^='optionFile']").length;
                var optionFile_value = $("input[name^='optionFile']");
                var checkFileName = $("input[name^='fileName']");
                for(i = 0; i < optionFile; i++){
                  
                    var ext = optionFile_value.eq(i).val().replace(/C:\\fakepath\\/i, '');
                    var name = optionFile_value.eq(i).val().replace(/C:\\fakepath\\/i, '');
                    console.log(name, checkFileName);
                    if(optionFile_value.eq(i).val() == 0 || optionFile_value.eq(i).val() == null){
                        swal({  
                                type: 'error',
                                title: 'File Fields cannot be empty!',
                                icon: 'error',
                                confirmButtonColor: "#367fa9",
                            });
                            event.preventDefault();
                            return false;
                    }
                    else if($.inArray(ext.split('.').pop().toLowerCase(),['xlsx','pdf','docs'])===-1){
                        swal({  
                                type: 'error',
                                title: 'Invalid File Extension! please refer to the ff(.xlsx,.pdf,.docs)',
                                icon: 'error',
                                confirmButtonColor: "#367fa9",
                            });
                            event.preventDefault();
                            return false;
                    }
                    else if(name.split('.').shift() !== checkFileName.eq(i).val()){
                        swal({  
                                type: 'error',
                                title: 'File Name Invalid! (please copy recommended filename in system)',
                                icon: 'error',
                                confirmButtonColor: "#367fa9",
                            });
                            event.preventDefault();
                            return false;
                    }
            
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
                    $(this).attr('disabled','disabled');
                    $('#button_action').val('1');
                    $("#myform").submit();                   
            });
            
          
           
        //}
    });

    //Closed Request
    $('#closed').click(function(event) {
        event.preventDefault();
        var rowCount = $('#item-sourcing-options tr').length-1;

        if(rowCount == 1) {
            swal({
                type: 'error',
                title: 'Please add an item!',
                icon: 'error',
                confirmButtonColor: "#367fa9",
            }); 
            event.preventDefault(); // cancel default behavior
        }else if($('#po_no').val() == "") {
            swal({
                type: 'error',
                title: 'PO No required!',
                icon: 'error',
                confirmButtonColor: "#367fa9",
            }); 
            event.preventDefault(); // cancel default behavior
        }else{
            swal({
                title: "Are you sure?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#41B314",
                cancelButtonColor: "#F9354C",
                confirmButtonText: "Yes, close it!",
                width: 450,
                height: 200
                }, function () {
                    $(this).attr('disabled','disabled');
                    $('#button_action').val('0');
                    $("#myform").submit();                   
            });
        }
    });

    // ADD DIGITS CODE
    $('#digitsCodeBtn').click(function(event) {
        event.preventDefault();
        var header_id       = $('#headerID').val();
        var request_type_id = $('#request_type_id').val();
        var digits_code     = $('#digits_code').val();
        var rowCount        = $('#item-sourcing-options tr').length-1;

        if(rowCount == 1) {
            swal({
                type: 'error',
                title: 'Please add an item!',
                icon: 'error',
                confirmButtonColor: "#367fa9",
            }); 
            event.preventDefault(); // cancel default behavior
        }else if($('#digits_code').val() == "") {
            swal({
                type: 'error',
                title: 'Digits Code required!',
                icon: 'error',
                confirmButtonColor: "#367fa9",
            }); 
            event.preventDefault(); // cancel default behavior
        }else{
            swal({
                title: "Are you sure?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#41B314",
                cancelButtonColor: "#F9354C",
                confirmButtonText: "Yes, save it!",
                width: 450,
                height: 200
                }, function () {
                    $.ajax({ 
                        url:  '{{ url('admin/item_sourcing_for_quotation/addDigitsCode') }}',
                        type: "GET",
                        data: { 
                            header_id: header_id,
                            request_type_id : request_type_id,
                            digits_code : digits_code
                        },
                        dataType: 'json',
                        success: function(data){    
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
                                    icon: 'error',
                                    confirmButtonColor: "#367fa9"
                                });
                                setTimeout(function(){
                                    location.reload();
                                }, 4000); 
                            }
                        }
                    });   
                    showLoading();                 
            });
        }
    });

    $("#btn-cancel").click(function(event) {
       event.preventDefault();
       swal({
            title: "Are you sure?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#41B314",
            cancelButtonColor: "#F9354C",
            confirmButtonText: "Yes, Go back!",
            width: 450,
            height: 200
            }, function () {
                window.history.back();                                                  
        });
    });

    function item_source_value(){
        var total = 0;
        $('.item_source_value').each(function(){
            total += $(this).val() === "" ? 0 : parseFloat($(this).val().trim().replace(/,/g, ''));
        })
    
        $('#item-source-value-total').text(thousands_separators(total.toFixed(2)));
    }

    function thousands_separators(num) {
        var num_parts = num.toString().split(".");
        num_parts[0] = num_parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return num_parts.join(".");
    }
    
</script>
@endpush