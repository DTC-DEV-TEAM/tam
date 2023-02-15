@extends('crudbooster::admin_template')
    @push('head')
        <style type="text/css">   
 
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

            table, th, td {
            border: 1px solid rgba(000, 0, 0, .5);
            padding: 8px;
            border-radius: 5px 0 0 5px;
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
        ERF History Details
    </div>

        <div class='card'>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                    <label class="control-label">Company</i></label>
                            <input type="text" class="form-control finput" value="{{$Header->company}}" aria-describedby="basic-addon1" readonly>             
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label"> Requested Date</label>
                        <input type="text" class="form-control finput" value="{{$Header->date_requested}}" aria-describedby="basic-addon1" readonly>             
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label"> Department</label>
                      <input type="text" class="form-control finput" value="{{$Header->department}}" aria-describedby="basic-addon1" readonly>             
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label"> Date Needed</label>
                        <input type="text" class="form-control finput" value="{{$Header->date_needed}}" aria-describedby="basic-addon1" readonly>             
                    </div>
                </div>
               
            </div>
            <div class="row"> 
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label"> Position</label>
                        <input type="text" class="form-control finput" value="{{$Header->position}}" aria-describedby="basic-addon1" readonly>                                                      
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label"> Work Location</label>
                        <input type="text" class="form-control finput" value="{{$Header->work_location}}" aria-describedby="basic-addon1" readonly>                                                                                    
                    </div>
                </div>
                
            </div>
            <div class="row"> 
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label"> Salary Range</label>
                        <input type="text" class="form-control finput" value="{{number_format($Header->salary_range_from) .' - '. number_format($Header->salary_range_to)}}" aria-describedby="basic-addon1" readonly>                                                                                         
      
                    </div>
                </div>
                
            </div>
        </div>
        <div class="card">
           <div class="row"> 
                <div class="col-md-6">
                    <label class="require control-label"> Schedule</label><br>
                    <input type="text" class="form-control finput" value="{{$Header->schedule}}" aria-describedby="basic-addon1" readonly>                                                                                    
                </div>
                <div class="col-md-6">
                    <label class="require control-label"> Allow Wfh</label><br>
                    <input type="text" class="form-control finput" value="{{$Header->allow_wfh}}" aria-describedby="basic-addon1" readonly>                                                                                    
                </div>
            </div>
            <div class="row"> 
                <div class="col-md-6">
                    <label class="require control-label"> Manpower</label><br>
                    <input type="text" class="form-control finput" value="{{$Header->manpower}}" aria-describedby="basic-addon1" readonly>                                                                                    
                </div>
                <div class="col-md-6">
                    <label class="require control-label"> Manpower Type</label><br>
                    <input type="text" class="form-control finput" value="{{$Header->manpower_type}}" aria-describedby="basic-addon1" readonly>                                                                                     
                </div>
            </div>
            <br>
                @if($Header->replacement_of != NULL || $Header->replacement_of != "")
                <div class="row"> 
                    <div class="col-md-6">
                        <label class="require control-label"> Replacement Of</label><br>
                        <input type="text" class="form-control finput" value="{{$Header->replacement_of}}" aria-describedby="basic-addon1" readonly>                                                                                    
                    </div>
                </div>
                @endif
        </div>
            
        <div class="card">
            <div class="row"> 
                <div class="col-md-6">
                    <label class="require control-label"><span style="color:red">*</span> Required Exams</label><br>
                    @foreach($required_exams as $val)
                    <input type="text" class="form-control finput" value="{{trim($val)}}" aria-describedby="basic-addon1" readonly>                                                                                       
                    @endforeach
                </div>
                <div class="col-md-6">
                    <label class="require control-label"> Does the Employee need to shared files?</label><br>
                    <input type="text" class="form-control finput" value="{{$Header->shared_files}}" aria-describedby="basic-addon1" readonly>                                                                                      
  
                </div>
            </div>
        </div>
        <div class="card">
            <div class="row">
                <div class="col-md-6">
                    <label class="require control-label"> Who will the employee interact with?</label><br>
                    @foreach($interaction as $val)   
                    <input type="text" class="form-control finput" value="{{trim($val)}}" aria-describedby="basic-addon1" readonly>                                                                                       
                    @endforeach
                </div>
                <div class="col-md-6">
                    <label class="require control-label"> What will you be using the PC for?</label><br>
                    @foreach($asset_usage as $val)   
                    <input type="text" class="form-control finput" value="{{trim($val)}}" aria-describedby="basic-addon1" readonly>                                                                                       
                    @endforeach
                </div>
            </div>
        </div>
        <div class="card">
            <div class="row">
                <div class="col-md-6">
                    <label class="require control-label"> Email Domain</label><br>
                    <input type="text" class="form-control finput" value="{{$Header->email_domain}}" aria-describedby="basic-addon1" readonly>                                                                                      

                </div>
                @if($Header->required_system != "" || $Header->required_system != NULL)
                <div class="col-md-6">
                    <label class="require control-label"> Required System</label><br>
                    @foreach($required_system as $val) 
                    <input type="text" class="form-control finput" value="{{trim($val)}}" aria-describedby="basic-addon1" readonly>                                                                                                                                                                       
                    @endforeach
                </div>
                @endif
            </div>
        </div>
        <div class="card">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label> Qualifications</label>
                        <input type="text" class="form-control finput" value="{{$Header->qualifications}}" aria-describedby="basic-addon1" readonly>                                                                                     
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label> Job Descriptions</label>
                        <input type="text" class="form-control finput" value="{{$Header->job_description}}" aria-describedby="basic-addon1" readonly>                                                                                      
                    </div>
                </div>
            </div>
            <div class="row">   
                <div class="col-md-6">                        
                <label class="control-label">Attached Documents</label>
                @foreach($erf_header_documents as $erf_header_document)                                    
                    <a href='{{CRUDBooster::mainpath("download/".$erf_header_document->id)."?return_url=".urlencode(Request::fullUrl())}}' class="form-control finput">{{$erf_header_document->file_name}} <i style="color:#007bff" class="fa fa-download"></i></a>                                       
                @endforeach
                </div>
            </div>
        </div>
        <div class="card">
            <div class="row">
                <div class="col-md-12">
                    <div class="box-header text-center">
                        <h3 class="box-title"><b> Required Assets</b></h3>
                    </div>
                    <div class="box-body no-padding">
                        <div class="table-responsive">
                            <div class="pic-container">
                                <div class="pic-row">
                                    <table id='table_dashboard' class="table table-hover table-striped table-bordered">
                                        <tbody>
                                            <tr class="tbl_header_color dynamicRows">
                                                <th width="10%" class="text-center">Item Description</th>
                                                <th width="10%" class="text-center">Category</th> 
                                                <th width="10%" class="text-center">Sub Category</th>  
                                                <th width="10%" class="text-center">Quantity</th>   
                                            </tr>
                                            @foreach($Body as $rowresult)
                                            <tr>
                                                <td style="text-align:center" height="10">
                                                    {{$rowresult->item_description}}
                                                </td>
                                                <td style="text-align:center" height="10">
                                                    {{$rowresult->category_id}}
                                                </td>
                                                <td style="text-align:center" height="10">
                                                    {{$rowresult->sub_category_id}}
                                                </td>
                                                <td style="text-align:center" height="10" class="qty">
                                                    {{$rowresult->quantity}}
                                                </td>
                                                

                                            </tr>
                                            @endforeach
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
        @if($Header->application != "")
        <div class="card">
            <div class="row">
              <div class="col-md-6" >
                        <label class="require control-label">{{ trans('message.form-label.application') }}</label>
                        @foreach($application as $val)   
                        <input type="text" class="form-control finput" value="{{trim($val)}}" aria-describedby="basic-addon1" readonly>                                                                                       
                        @endforeach  
                </div>
                @if($Header->application_others != "")
                <div class="col-md-6">
                    <div class="row">
                        <label class="require control-label">*{{ trans('message.form-label.application_others') }}</label>
                        <div class="col-md-6">
                        <p>{{$Header->application_others}}</p>   
                        </div>
                    </div>
                    <hr/>
                </div>
                @endif
            </div>
        </div>
        @endif
        @if($Header->approved_immediate_head_by != NULL)
        <div class="card">
            <div class="row">
                <div class="col-md-6">
                    <table style="width:100%">
                        <tbody>
                            <tr>
                                <th class="control-label col-md-2">{{ trans('message.form-label.approved_by') }}:</th>
                                <td class="col-md-4">{{$Header->approved_head_by}} / {{$Header->approved_immediate_head_at}}</td>     
                            </tr>
                            @if($Header->approver_comments != NULL)
                            <tr>
                                <th class="control-label col-md-2">{{ trans('message.table.approver_comments') }}:</th>
                                <td class="col-md-4">{{$Header->approver_comments}}</td>
                            </tr>
                            @endif

                        </tbody>
                    </table>
                </div>
                
                @if($Header->approved_hr_by != NULL)
                <div class="col-md-6" style="margin-bottom:10px">
                    <table style="width:100%">
                        <tbody>
                            <tr>
                                <th class="control-label col-md-2">Verified By:</th>
                                <td class="col-md-4">{{$Header->verified_by}} / {{$Header->approved_hr_at}}</td>     
                            </tr>
                            @if($Header->hr_comments != NULL)
                            <tr>
                                <th class="control-label col-md-2">Verifier Comments:</th>
                                <td class="col-md-4">{{$Header->hr_comments}}</td>
                            </tr>
                            @endif

                        </tbody>
                    </table>
                </div>
                @endif
                <br>
                @if($Header->onboarding_date != NULL)
                <div class="col-md-6">
                    <table style="width:100%">
                        <tbody>
                            <tr>
                                <th class="control-label col-md-2">Employee:</th>
                                <td class="col-md-4">{{$Header->first_name}} {{$Header->last_name}}</td>     
                            </tr>
                            <tr>
                                <th class="control-label col-md-2">On Boarding Date:</th>
                                <td class="col-md-4">{{$Header->onboarding_date}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
        @endif
            
                
@endsection
@push('bottom')
<script type="text/javascript">
var tds = document
    .getElementById("table_dashboard")
    .getElementsByTagName("td");
    var sumqty = 0;
    var sumcost = 0;
    for (var i = 0; i < tds.length; i++) {
    if (tds[i].className == "qty") {
        sumcost += isNaN(tds[i].innerHTML) ? 0 : parseFloat(tds[i].innerHTML);
    }
    }
    document.getElementById("table_dashboard").innerHTML +=
    "<tr><td colspan='3' style='text-align:right'><strong>TOTAL</strong></td><td style='text-align:center'><strong>" +
    sumcost +
    "</strong></td></td></tr>";
</script>
@endpush