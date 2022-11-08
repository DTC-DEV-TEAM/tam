@extends('crudbooster::admin_template')
    @push('head')
        <style type="text/css">   
           
        </style>
    @endpush
@section('content')
@if(g('return_url'))
	<p class="noprint"><a title='Return' href='{{g("return_url")}}'><i class='fa fa-chevron-circle-left '></i> &nbsp; {{trans("crudbooster.form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}}</a></p>       
@else
	<p class="noprint"><a title='Main Module' href='{{CRUDBooster::mainpath()}}'><i class='fa fa-chevron-circle-left '></i> &nbsp; {{trans("crudbooster.form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}}</a></p>       
@endif
<div id="loading">
    <div id="loading-content"></div>
</div>
<div class='panel panel-default'>
    <div class='panel-heading'>Import Customer Data</div>
    <form id="AddCustomerForm" enctype="multipart/form-data">
        <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
        <div class='panel-body'>
          <div class="row">
            <div class="col-md-6">
             <input type="file" class="form-control" name="file" id="file"/>
            </div>
          </div>    
        </div>

        <div class='panel-footer'>
            <a href="{{ CRUDBooster::mainpath() }}" class="btn btn-default"> Cancel</a>
            <button class="btn btn-primary pull-right" type="submit" id="bntImport"> <i class="fa fa-save" ></i>  Upload</button>
        </div>
    </form>

</div>

@endsection

@push('bottom')
    <script type="text/javascript">

      $("#bntImport").click(function(event) {
        event.preventDefault();
        var extension = $('#file').val().split('.').pop().toLowerCase();
        if ($.inArray(extension, ['csv', 'xls', 'xlsx']) == -1) {
            swal({
                type: 'error',
                title: 'Please choose file csv/xls/xlsx!',
                icon: 'error',
            });
            event.preventDefault();
            return false;
        } else {
            var file_data = $('#file').prop('files')[0];
            var form_data = new FormData();
            form_data.append('file', file_data);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            showLoading();
            $.ajax({
                url: "{{route('customers.get.store')}}",
                data: form_data,
                type: 'POST',
                dataType: "json",
                contentType: false,
                cache: false,
                processData: false,
                success: function(response) {
                    console.log(response);
                    if (response.status == "success") {
                    swal({
                        type: response.status,
                        title: response.message,
                    });
                    setTimeout(function(){
                        window.location.replace(document.referrer);
                    }, 2000); 
                    } else if (response.status == "error") {
                    swal({
                        type: response.status,
                        title: response.message,
                    });
                    }
                },
                error: function(response){
                    swal({
                    type: "error",
                    title: "An error occurred!"
                });
                }
            });
        }
    });
    function RefreshPage(){
            setTimeout(function(){
                window.history.back();
            }, 2000); 
        }
    </script>
@endpush