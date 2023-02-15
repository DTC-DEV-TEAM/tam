@extends('crudbooster::admin_template')
    @push('head')
    
        <style type="text/css">   
            .select2-container--default .select2-selection--multiple .select2-selection__choice{color:black;}
        </style>
    @endpush
@section('content')
@if(g('return_url'))
	<p class="noprint"><a title='Return' href='{{g("return_url")}}'><i class='fa fa-chevron-circle-left '></i> &nbsp; {{trans("crudbooster.form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}}</a></p>       
@else
	<p class="noprint"><a title='Main Module' href='{{CRUDBooster::mainpath()}}'><i class='fa fa-chevron-circle-left '></i> &nbsp; {{trans("crudbooster.form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}}</a></p>       
@endif

<div class='panel panel-default'>
    <div class='panel-heading'>Add Asset Form</div>

    <form action='{{CRUDBooster::mainpath('add-save')}}' method="POST" id="AddCategoryForm" enctype="multipart/form-data">
        <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">

        <div class='panel-body'>
           <div class="row">
             <div class="col-md-12">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Category Description</label>
                        <input type="text" class="form-control" name="category_description" id="category_description" >
                    </div>
                </div>
             </div>
          </div>
        </div>
        
        <div class='panel-footer'>
            <a href="{{ CRUDBooster::mainpath() }}" class="btn btn-default">{{ trans('message.form.cancel') }}</a>
            <button class="btn btn-success pull-right" type="submit" id="btnSubmit"> <i class="fa fa-plus-circle" ></i>  Add</button>
        </div>
    </form>


</div>

@endsection

@push('bottom')
    <script type="text/javascript">
        $(document).ready(function() {
            $('.select2').select2({placeholder_text_single : "-- Select --"})

            $("#btnSubmit").click(function(event) {
            event.preventDefault();
                swal({
                    title: "Are you sure?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#41B314",
                    cancelButtonColor: "#F9354C",
                    confirmButtonText: "Yes, Save it!",
                    width: 450,
                    height: 200
                    }, function () {
                        $("#AddCategoryForm").submit();                     
                });
            });
        });
    </script>
@endpush