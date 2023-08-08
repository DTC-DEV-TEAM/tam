@extends('crudbooster::admin_template')
    @push('head')
        <style type="text/css">   
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
                border-bottom: 1px solid rgba(18, 17, 17, 0.5);
            }

            .alink {
                border:none;
                /* border-bottom: 1px solid rgba(18, 17, 17, 0.5); */
            }

            input.finput:read-only {
                background-color: #fff;
            }
            .green-color {
                color:green;
                margin-top:12px;
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

            /* .btn-circle.btn-sm {
                width: 30px;
                height: 30px;
                padding: 6px 0px;
                border-radius: 15px;
                text-align: center;
            } */
     
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
        History Detail View
    </div>
        
        <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
        <input type="hidden" value="0" name="action" id="action">

        <input type="hidden" value="{{$Header->requestid}}" name="headerID" id="headerID">

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
            @endif

            <div class="row">
                @if($Header->po_number != null)
                <label class="control-label col-md-2">{{ trans('message.form-label.po_number') }}:</label>
                    <div class="col-md-4">
                        <p >{{$Header->po_number}}</p>
                </div>
                @endif
                @if($Header->store_branch != null || $Header->store_branch != "")                
                    <label class="control-label col-md-2">{{ trans('message.form-label.store_branch') }}:</label>
                    <div class="col-md-4">
                            <p>{{$Header->store_name}}</p>
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
                                                <th>Digits Code</th>
                                                <th width="12%" class="text-center">Category</th> 
                                                <th width="12%" class="text-center">Sub Category</th>
                                                <th width="12%" class="text-center">Class</th> 
                                                <th width="12%" class="text-center">Sub Class</th> 
                                                <th width="12%" class="text-center">{{ trans('message.table.item_description') }}</th>   
                                                <th width="7%" class="text-center">Brand</th> 
                                                <th width="7%" class="text-center">Model</th>  
                                                <th width="7%" class="text-center">Size(L x W x H in cm)</th> 
                                                <th width="7%" class="text-center">Actual Color</th>     
                                                @if(in_array($Header->request_type_id,[6]))
                                                    <th width="7%" class="text-center">Material</th> 
                                                    <th width="7%" class="text-center">Thickness</th> 
                                                    <th width="7%" class="text-center">lamination</th>
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
                                                <tr>
                                                    @foreach($Body as $rowresult)                                                                                                    
                                                        <tr>
                                                            <input type="hidden"  class="form-control"  name="id" id="id"  required  value="{{$rowresult->body_id}}" readonly>        
                                                            <td style="text-align:center" height="10">
                                                                {{$rowresult->digits_code}}                               
                                                            </td>
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
                                                            <td style="text-align:center" height="10">
                                                                {{$rowresult->quantity}}                               
                                                            </td>
                                    
                                                            @if(!in_array($Header->request_type_id,[6])) 
                                                                <td style="text-align:center" height="10" class="cost">
                                                                        {{$rowresult->budget}}
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
            </div>

            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <table class="table" id="item-sourcing-options">
                        <tbody id="bodyTable">
                            <tr>
                                <th class="text-center">Option</th> 
                                <th class="text-center">Vendor Name</th>
                                <th class="text-center">Total Price</th> 
                                <th class="text-center">Quotation</th> 
                                <th width="5%" class="text-center"><i class="fa fa-check-circle"></i></th>
                                <th width="5%" class="text-center"><i class="fa fa-times-circle"></i></th>
                            </tr>  
              
                           @if($item_options->isNotEmpty())                                              
                                <?php   $tableRow = 1; ?>
                                @foreach($item_options as $res)
                                    <?php   $tableRow1++; ?>
                                        @if($res->deleted_at != null && $res->selected_alternative_at == null)
                                        <input type="hidden"  class="form-control"  name="opt_id" id="opt_id"  required  value="{{$res->optId}}" readonly>  
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
                                            <td style="text-align:center" height="10">
                                                {{$res->file_name}}                              
                                            </td>
                                            <td colspan="2" style="text-align:center; color:white">
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
                                            <td style="text-align:center" height="10">
                                                {{$res->file_name}}                              
                                            </td>
                                            <td colspan="2"  style="text-align:center;">
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
                                                @if($Header->closed_at === null || $Header->closed_at === "")
                                                <div class="round">
                                                    <input data-toggle="tooltip" data-placement="bottom" title="Check" type="checkbox" id="checkbox3" class="checkbox3" name="selectRow" value="{{$res->optId}}" />
                                                    <label for="checkbox3"></label>
                                                </div>
                                                @endif
                                            </td>
                                            <td>
                                                @if($Header->closed_at === null || $Header->closed_at === "")
                                                <button type="button" data-toggle="tooltip" data-placement="right" title="Cancel" id="deleteRow" name="removeRow" data-id="' + tableRow + '" class="btn btn-danger btn-circle btn-sm removeRow" value="{{$res->optId}}"><i class="glyphicon glyphicon-remove-sign"></i></button>
                                                @endif
                                            </td>
                                        
                                        </tr>
                                    @endif
                                @endforeach        
                            @else
                                 <tr><td colspan="6">No Available data</td></tr>
                            @endif                      
                         
                        
                        </tbody>
                    </table>
                </div>   
            </div>
            <hr>

            <br>

                <div class="row">
                    @include('item-sourcing.comments',['comments'=>$comments])
                    @include('item-sourcing.other_detail',['Header'=>$Header])
     
                </div>

        <div class='panel-footer'>
            <a href="{{ CRUDBooster::mainpath() }}" class="btn btn-default">{{ trans('message.form.back') }}</a>
        </div>
</div>
            {{-- Modal Edi Version --}}
@include('item-sourcing.modal-edit-version')

@endsection
@push('bottom')
<script type="text/javascript">
    $(function(){
        $('body').addClass("sidebar-collapse");
        item_source_value();
      
        var w = $("input[name^='if_arf_created']").length;
        var if_arf_created = $("input[name^='if_arf_created']");
        for(i=0;i<w;i++){
            if(if_arf_created.eq(i).val() == 1){
                $('#btnSubmit').hide();
            }else{
                $('#btnSubmit').show();
            }
        }
    });
    
    $('.chat').scrollTop($('.chat')[0].scrollHeight);
    
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
    setTimeout("preventBack()", 0);
    var token = $("#token").val();
    
    //total value
     function item_source_value(){
        var total = 0;
        $('.item_source_value').each(function(){
            total += $(this).val() === "" ? 0 : parseFloat($(this).val());
        })

        $('#item-source-value-total').text(thousands_separators(total.toFixed(2)));
    }

    //GET VERSION
    $('#getVersions').click(function(evennt) {
        event.preventDefault();
        var header_id = $('#headerID').val();
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
        // $('#versionModal').modal('show'); 
       
    });
    $('#versionModal').on('hidden.bs.modal', function () {
    //   location.reload();
       $("#modal-version tbody").html("");
    });

    //Chat
    $('#btnChat').click(function(event) {
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

   
    //update request
    $('#btnSubmit').click(function() {
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
                $.ajax({
                    url: "{{ route('edit-item-source') }}",
                    type: "POST",
                    dataType: 'json',
                    data: $('#myform').serialize(),
                    success: function (data) {
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
                            });
                        }
                    }
                });     
                showLoading();                                              
        });
        

    });

    var tableRow = <?php echo json_encode($tableRow); ?>;
    tableRow ++;
    //remove items in options
    $(document).ready(function() {
            $(document).on('click', '.removeRow', function() {
                $(".checkbox3").prop('checked', false);
                event.preventDefault();
                var id_data = $(this).val();    
   
                if ($('#asset-items1 tbody tr').length != 1) { //check if not the first row then delete the other rows
            
                // item_id = $("#ids"+id_data).val();
                // $("#bodyID").val(item_id);
                var data = $('#myform').serialize();
                var data_id = id_data;
                swal({
                    title: "Are you sure?",
                    type: "warning",
                    text: "You won't be able to revert this!",
                    showCancelButton: true,
                    confirmButtonColor: "#dd4b39",
                    cancelButtonColor: "#F9354C",
                    confirmButtonText: "Yes, cancel it!"
                    }, function () {
                    $.ajax
                        ({ 
                            url:  '{{ url('admin/item-sourcing-header/RemoveItemSource') }}',
                            type: "GET",
                            data: { opt_id: data_id},
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
                                    });
                                }
                            }
                        });                            
                    });
                    $("#deleteRow"+id_data).attr('disabled', true);
                    tableRow--;
                    return false;   
               }
            });
    });

    //only one checked allowed
     //checkbox validations
     $("input[name^='selectRow']").on('click', function() {
        var $box = $(this);
        if ($box.is(':checked')) {
            var group = "input:checkbox[name='" + $box.attr("name") + "']";
            $(group).prop("checked", false);
            $box.prop('checked', true);
        } else {
            $box.prop('checked', false);
        }
    });

    //Select option
    $(".checkbox3").change(function() {
        var ischecked= $(this).is(':checked');
        var data_id = $(this).val();
        if(ischecked == true){
            swal({
                title: "Are you sure?",
                type: "warning",
                text: "You won't be able to revert this!",
                showCancelButton: true,
                confirmButtonColor: "#41B314",
                cancelButtonColor: "#F9354C",
                confirmButtonText: "Yes, select it!"
            }, function () {
            $.ajax
                ({ 
                    url:  '{{ url('admin/item-sourcing-header/SelectedOption') }}',
                    type: "GET",
                    data: { opt_id: data_id},
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
                            });
                        }
                    }
                });                            
            });
        }

    });

    function calculateTotalQuantity(...body_qty) {
        var totalQuantity = 0;  
        $('.quantity_item').each(function() {
        totalQuantity = parseInt($("#quantity_total").val()) - parseInt(body_qty);
        });
        return totalQuantity;

    }
    function thousands_separators(num) {
    var num_parts = num.toString().split(".");
    num_parts[0] = num_parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    return num_parts.join(".");
    }

    
</script>
@endpush