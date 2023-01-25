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

        <div class='panel-body'>

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


        </div>

        <div class='panel-footer'>

            <a href="{{ CRUDBooster::mainpath() }}" class="btn btn-default">Back</a>

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