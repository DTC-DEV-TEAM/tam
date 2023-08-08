<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label require">{{ trans('message.form-label.employee_name') }}</label>
             
            <input type="text" class="form-control finput"  id="employee_name" name="employee_name"  required readonly value="{{$employeeinfos->bill_to}}"> 
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label require">{{ trans('message.form-label.company_name') }}</label>
            <input type="text" class="form-control finput"  id="company_name" name="company_name"  required readonly value="{{$employeeinfos->company_name_id}}">                                   
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label require">{{ trans('message.form-label.department') }}</label>
            <input type="text" class="form-control finput"  id="department" name="department"  required readonly value="{{$employeeinfos->department_name}}">
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label require">{{ trans('message.form-label.position') }}</label>
            <input type="text" class="form-control finput"  id="position" name="position"  required readonly value="{{$employeeinfos->position_id}}">                                   
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label class="control-label require"><span style="color:red">*</span> Color Proofing</label>
                <select selected data-placeholder="Choose" class="form-control select2" name="sampling" id="sampling" required required style="width:100%"> 
                    <option value=""></option> 
                        @foreach($yesno as $data)
                        <option value="{{$data->description}}">{{$data->description}}</option>
                            @endforeach
                </select>
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label class="control-label require"><span style="color:red">*</span> Mock Up</label>
                <select selected data-placeholder="Choose" class="form-control select2" name="mark_up" id="mark_up" required required style="width:100%"> 
                    <option value=""></option> 
                        @foreach($yesno as $data)
                        <option value="{{$data->description}}">{{$data->description}}</option>
                            @endforeach
                </select>                                   
        </div>
    </div>


    <div class="col-md-3">
        <div class="form-group">
            <label class="control-label require"><span style="color:red">*</span> Date Needed</label>
            <input class="form-control finput date" autocomplete="off" type="text" placeholder="Select Needed Date" name="date_needed" id="date_needed">
        </div>
    </div> 

    
    <div class="col-md-3">
        <div class="form-group">
            <label class="control-label require"> Photos/File <span style="color:red; font-style:italic">(jpg, jpeg, gif, png, xlsx, docs, pdf)</span></label>
            <input type="file" class="form-control finput" style="" name="upload_file[]" id="upload_file" multiple>                                
        </div>
    </div>

</div>


<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label class="control-label require"> Artwork Link</label>
            <input type="text" class="form-control finput"  id="artworklink" name="artworklink"  required>                                   
        </div>
    </div>
   
</div>

@if(CRUDBooster::myPrivilegeId() == 8)
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label require">{{ trans('message.form-label.store_branch') }}</label>
                    
                <input type="text" class="form-control finput"  id="store_branch" name="store_branch"  required readonly value="{{$stores->store_name}}"> 
                <input type="hidden" class="form-control"  id="store_branch_id" name="store_branch_id"  required readonly value="{{$stores->id}}"> 

            </div>
        </div>
    </div>
@endif

