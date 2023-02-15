@extends('crudbooster::admin_template')
    @push('head')
        <style type="text/css">   

            .select2-selection__choice{
                    font-size:14px !important;
                    color:black !important;
            }
            .select2-selection__rendered {
                line-height: 31px !important;
            }
            .select2-container .select2-selection--single {
                height: 35px !important;
            }
            .select2-selection__arrow {
                height: 34px !important;
            }
            .firstRow {
                border: 1px solid rgba(39, 38, 38, 0.5);
                padding: 10px;
                margin-left: 10px;
                border-radius: 3px;
                opacity: 2;
            }

            .firstRow {
                padding: 10px;
                margin-left: 10px;
            }

            .finput {
                border:none;
                border-bottom: 1px solid rgba(18, 17, 17, 0.5);
            }

            input.finput:read-only {
                background-color: #fff;
            }

            input.sinput:read-only {
                background-color: #fff;
            }

            input.addinput:read-only {
                background-color: #f5f5f5;
            }

            .input-group-addon {
                background-color: #f5f5f5 !important;
            }
            .card, .card2, .card3, .card4, .card5, .card6, .card7, .card8{
                background-color: #fff ;
                padding: 15px;
                border-radius: 3px;
                box-shadow: rgba(0, 0, 0, 0.16) 0px 1px 4px;
                margin-bottom: 15px;
            }
            .panel-heading{
                background-color: #f5f5f5 ;
            }
        </style>
    @endpush
@section('content')
@if(g('return_url'))
	<p class="noprint"><a title='Return' href='{{g("return_url")}}'><i class='fa fa-chevron-circle-left '></i> &nbsp; {{trans("crudbooster.form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}}</a></p>       
@else
	<p class="noprint"><a title='Main Module' href='{{CRUDBooster::mainpath()}}'><i class='fa fa-chevron-circle-left '></i> &nbsp; {{trans("crudbooster.form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}}</a></p>       
@endif

        <div class='panel-heading'>
            Applicant Detail
        </div>

       <form action="{{ CRUDBooster::mainpath('edit-save/'.$applicant->apid) }}" method="POST" id="edit_applicant" enctype="multipart/form-data">
        <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
        <input type="hidden" value="1" name="request_type_id" id="request_type_id">
            <div class="card">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label require"> ERF</label>
                            <input class="form-control finput" type="text" name="erf_number" id="erf_number" value="{{$applicant->erf_number}}" readonly>
                    
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"><span style="color:red">*</span> Screen Date</label>
                            <input class="form-control finput" type="text" placeholder="Select Date" name="screen_date" id="screen_date" value="{{$applicant->screen_date}}" readonly>
                        </div>
                    </div>  
                </div>

                <div class="row"> 
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"><span style="color:red">*</span> First Name</label>
                            <input type="text" class="form-control finput"  id="first_name" name="first_name"  value="{{$applicant->first_name}}" readonly>                                   
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"><span style="color:red">*</span> Last Name</label>
                            <input type="text" class="form-control finput"  id="last_name" name="last_name"  value="{{$applicant->last_name}}" readonly>                                   
                        </div>
                    </div>
                </div>
                <div class="row"> 
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"><span style="color:red">*</span> Status</label>
                            <input type="text" class="form-control finput"  id="status" name="status"  value="{{$applicant->status_desc}}" readonly>                                   
                        </div>
                    </div>
                </div>
                <hr>
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
    $('.erf').select2({})
    $(".date").datetimepicker({
        minDate:new Date(), // Current year from transactions
        viewMode: "days",
        format: "YYYY-MM-DD",
        dayViewHeaderFormat: "MMMM YYYY",
    });
</script>
@endpush