@extends('crudbooster::admin_template')
    @push('head')
        <style type="text/css">   

            .select2-selection__choice{
                    font-size:14px !important;
                    color:black !important;
            }
            table.dataTable td.dataTables_empty {
                text-align: center;    
            }
            .active{
                font-weight: bold;
                font-size: 13px;
                color:#3c8dbc
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
        Asset Form
    </div>

    <form action="{{ CRUDBooster::mainpath('add-save') }}" method="POST" id="AssetReturnRequest" enctype="multipart/form-data">
        <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
        <input type="hidden" value="1" name="request_type_id" id="request_type_id">
         
           <div class="form-group" style="padding:10px">
                <label class="require control-label" style="font-style: italic">Transfer to:</label>
                <select class="users" data-placeholder="** Select Transfer to **"  style="width: 50%;" name="users_id" id="users_id">
                    <option value=""></option>
                    @foreach($users as $value)
                        <option value="{{$value->id}}">{{$value->name}}</option>
                    @endforeach
                </select>
            </div>
      
            
        <div class="box-body">
            <div class="table-responsive"> 
                <table id='table_dashboard' class="table table-hover table-striped table-bordered">
                    <thead>
                        <tr class="active">
                            <th>Select to Return</th>  
                            <th>Arf Number</th>  
                            <th>Reference Number</th> 
                            <th>Asset Code</th>  
                            <th>Digits Code</th>   
                            <th>Item Description</th>    
                            <th>Asset Type</th>                                                                                 
                            
                        </tr>
                        <?php   $tableRow1 = 0; ?>
                        <?Php   $item_count = 0; ?>
                    </thead>
                    <tbody>
                        @foreach($mo_body as $res)
                        <?php   $tableRow1++; ?>
                        <?Php $item_count++; ?>
                            <tr>
                            <td style="text-align:center">
                              <input type="checkbox" name="mo_id[]" id="mo_id{{$tableRow1}}" class="id" required data-id="{{$tableRow1}}" value="{{$res->mo_id}}"/>
                              <input type="hidden" name="request_type_id[]" id="request_type_id{{$tableRow1}}" class="id" required data-id="{{$tableRow1}}" value="{{$res->request_type_id}}"/>
                              <input type="hidden" name="location_id" id="location_id{{$tableRow1}}" class="id" required data-id="{{$tableRow1}}" value="{{$stores->id}}"/>
                            </td>
                            <td>{{$res->reference_number}}</td>
                            <td>{{$res->mo_reference_number}}</td>
                            <td>{{$res->asset_code}}</td>
                            <td>{{$res->digits_code}}</td> 
                            <td>{{$res->item_description}}</td>   
                            <td>{{$res->asset_type}}</td>                                                                                                                  
                            </tr>
                        @endforeach
                    </tbody>
                </table> 
            </div>
        </div>

        <div class='panel-footer'>

            <a href="{{ CRUDBooster::mainpath() }}" class="btn btn-default">{{ trans('message.form.cancel') }}</a>

            <button class="btn btn-primary pull-right" type="submit" id="btnSubmit"> <i class="fa fa-save" ></i> {{ trans('message.form.save') }}</button>

        </div>

    </form>


</div>



@endsection
@push('bottom')

<script type="text/javascript">
var table;
    function preventBack() {
        window.history.forward();
    }
    window.onunload = function() {
        null;
    };
    $('.users').select2({
    placeholder_text_single : "-- Select --"});
    setTimeout("preventBack()", 0);
    var tableRow = <?php echo json_encode($tableRow); ?>;
    var tableRow1 = tableRow;
    tableRow1++;
    table = $("#table_dashboard").DataTable({
        ordering:false,
        pageLength:100,
    });
    $("#btnSubmit").click(function(event) {
        var Ids = [];
        var request_type_id;
        var location_id;
        $.each($("input[name='mo_id[]']:checked"), function(){
            Ids.push($(this).val());
            request_type_id = $("#request_type_id"+$(this).attr("data-id")).val();
            location_id = $("#location_id"+$(this).attr("data-id")).val();
        });

        var check = $('input:checkbox:checked').length;
        event.preventDefault();
        if($('#users_id').val() == "") {
            swal({
                type: 'error',
                title: 'Please select Transfer to!',
                icon: 'error',
                confirmButtonColor: "#367fa9",
            }); 
            event.preventDefault(); // cancel default behavior
        }else if (check == 0) {
            swal({
                type: 'error',
                title: 'Please select assets to transfer!',
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
                confirmButtonText: "Yes, send it!",
                width: 450,
                height: 200
                }, function () {
                    $.ajax({
                        url: "{{ route('assets.save.transfer.assets') }}",
                        type: "POST",
                        dataType: 'json',
                        data: {
                            //"_token": token,
                            "Ids": Ids,
                            "request_type_id": request_type_id,
                            "location_id": location_id,
                            "users_id" : $('#users_id').val(),
                        },
                        success: function (data) {
                            if (data.status == "success") {
                                swal({
                                    type: data.status,
                                    title: data.message,
                                });
                                window.location.replace(data.redirect_url);
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

    

</script>
@endpush