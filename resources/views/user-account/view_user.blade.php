
@extends('crudbooster::admin_template')
@push('head')
<style type="text/css">   
</style>
@endpush
@section('content')
<!-- link -->
@if(g('return_url'))
	<p class="noprint"><a title='Return' href='{{g("return_url")}}'><i class='fa fa-chevron-circle-left '></i> &nbsp; {{trans("crudbooster.form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}}</a></p>       
@else
	<p class="noprint"><a title='Main Module' href='{{CRUDBooster::mainpath()}}'><i class='fa fa-chevron-circle-left '></i> &nbsp; {{trans("crudbooster.form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}}</a></p>       
@endif
  <div class='panel panel-default'>
    <div class='panel-heading'>  
        Details
    </div>
    <div class='panel-body'>    
        <div class="row">
         <div class="col-md-4 firstRow">   

            <label class="control-label">Photo</label>
            <br>
            <div class="input-group">                                 
                @if ($users->photo)
                <img style="margin-right:5px" width="392px"; height="200px"; src="{{URL::to($users->photo)}}" alt="" data-action="zoom"> 
                @else
                <img width="60px"; height="50px"; src="{{URL::to('vendor/crudbooster/no_image_available/No_Image_Available.jpg')}}" alt="" data-action="zoom">
                @endif                                         
            </div>
            <br><br>
            <div class="input-group">
                <div class="input-group-addon">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-user icon"></i></span>
                </div>
                <input type="text" class="form-control finput" value="{{ $users->first_name . ' ' . $users->last_name }}" aria-describedby="basic-addon1" readonly>
                </div>
            </dv>
            <br>
            <div class="input-group">
                <div class="input-group-addon">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-envelope icon"></i></span>
                </div>
                <input type="text" class="form-control finput" id="email" value="{{ $users->email}}" aria-describedby="basic-addon1" readonly>
                </div>
            </dv>
            <br>
         </div>
          <!-- SECOND DIV -->
          <div class="col-sm-8 SecondRow">
            <div class="col-sm-6">
                <div class="input-group">
                    <div class="input-group-addon">
                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-briefcase icon"></i></span>
                    </div>
                    <input type="text" class="form-control finput" value="{{ $users->position_id}}" aria-describedby="basic-addon1" readonly>
                    </div>
                </dv>
            </div>

            <div class="col-sm-6">
                <div class="input-group">
                    <div class="input-group-addon">
                        <span class="input-group-text" id="basic-addon1">Bill to(Company Name)</span>
                    </div>
                    <input type="text" class="form-control sinput" value="{{ $users->bill_to }}" aria-describedby="basic-addon1" readonly>
                    </div>
                </dv>
            </div>
            <br><br><br>
            <div class="col-sm-6">
                <div class="input-group">
                    <div class="input-group-addon">
                        <span class="input-group-text" id="basic-addon1">Customer/Location Name</span>
                    </div>
                    <input type="text" class="form-control sinput" value="{{ $users->customer_location_name }}" aria-describedby="basic-addon1" readonly>
                    </div>
                </dv>
            </div>
            
            <div class="col-sm-6">
                <div class="input-group">
                    <div class="input-group-addon">
                        <span class="input-group-text" id="basic-addon1">Company</span>
                    </div>
                    <input type="text" class="form-control sinput" value="{{ $users->company_name_id }}" aria-describedby="basic-addon1" readonly>
                    </div>
                </dv>
            </div>
            <br><br><br>
            <div class="col-sm-6">
                <div class="input-group">
                    <div class="input-group-addon">
                        <span class="input-group-text" id="basic-addon1">Department</span>
                    </div>
                    <input type="text" class="form-control sinput" value="{{ $users->department_name }}" aria-describedby="basic-addon1" readonly>
                    </div>
                </dv>
            </div>

            <div class="col-sm-6">
                <div class="input-group">
                    <div class="input-group-addon">
                        <span class="input-group-text" id="basic-addon1">Sub Department</span>
                    </div>
                    <input type="text" class="form-control sinput" value="{{ $users->sub_department }}" aria-describedby="basic-addon1" readonly>
                    </div>
                </dv>
            </div>
            <br><br><br>
            <div class="col-sm-6">
                <div class="input-group">
                    <div class="input-group-addon">
                        <span class="input-group-text" id="basic-addon1">Privilege</span>
                    </div>
                    <input type="text" class="form-control sinput" value="{{ $users->privilege_name }}" aria-describedby="basic-addon1" readonly>
                    </div>
                </dv>
            </div>
            <div class="col-sm-6">
                <div class="input-group">
                    <div class="input-group-addon">
                        <span class="input-group-text" id="basic-addon1">Approver</span>
                    </div>
                    <input type="text" class="form-control sinput" value="{{ $users->approver }}" aria-describedby="basic-addon1" readonly>
                    </div>
                </dv>
            </div>
            <br><br><br>
            <div class="col-sm-6">
                <div class="input-group">
                    <div class="input-group-addon">
                        <span class="input-group-text" id="basic-addon1">Location</span>
                    </div>
                    <input type="text" class="form-control sinput" value="{{ $users->store_name }}" aria-describedby="basic-addon1" readonly>
                    </div>
                </dv>
            </div>
            <div class="col-sm-6">
                <div class="input-group">
                    <div class="input-group-addon">
                        <span class="input-group-text" id="basic-addon1">Contact Person</span>
                    </div>
                    <input type="text" class="form-control sinput" value="{{ $users->contact_person}}" aria-describedby="basic-addon1" readonly>
                    </div>
                </dv>
            </div>
          </div>
        </div>
       <hr> 
    </div>
  </div>
  

@endsection
@push('bottom')
    <script type="text/javascript">
         
    </script>
@endpush