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
                                <tbody id="bodyTable">
                                    <tr class="tbl_header_color dynamicRows">
                                         
                                        <th width="20%" class="text-center"><span style="color:red">*</span> {{ trans('message.table.item_description') }}</th>                                                 
                                        <th width="15%" class="text-center"><span style="color:red">*</span> Brand</th>
                                        <th width="10%" class="text-center"><span style="color:red">*</span> Model</th>
                                        <th width="5%" class="text-center"><span style="color:red">*</span> Size(L x W x H in cm)</th>
                                        <th width="10%" class="text-center"><span style="color:red">*</span> Actual Color</th>
                                        <th width="10%" class="text-center"><span style="color:red">*</span> Budget Range</th> 
                                        <th width="5%" class="text-center"><span style="color:red">*</span> {{ trans('message.table.quantity_text') }}</th>                                                    
                                    </tr>
                                    
                                    <tr id="tr-table">
                                        <tr>                                                      
                                           
                                            <td >
                                                <input type="text" placeholder="Item Description..." onkeyup="this.value = this.value.toUpperCase();" oninput="validate(this)" class="form-control finput itemDesc" id="itemDesc"  name="item_description"  required maxlength="100">
                                            </td> 
                                            <td >
                                                <input type="text" placeholder="Brand..." onkeyup="this.value = this.value.toUpperCase();" oninput="validate(this)" class="form-control finput brand" id="brand"  name="brand"  required maxlength="100">
                                            </td> 
                                            <td >
                                                <input type="text" placeholder="Model..." onkeyup="this.value = this.value.toUpperCase();" oninput="validate(this)" class="form-control finput model" id="model"  name="model"  required maxlength="100">
                                            </td> 
                                            <td >
                                                <input type="text" placeholder="Size..." onkeyup="this.value = this.value.toUpperCase();" oninput="validate(this)" class="form-control finput size" id="size"  name="size"  required maxlength="100">
                                            </td> 
                                            <td >
                                                <input type="text" placeholder="Actual Color..." onkeyup="this.value = this.value.toUpperCase();" oninput="validate(this)" class="form-control finput actual_color" id="actual_color"  name="actual_color"  required maxlength="100">
                                            </td> 
                                            <td> 

                                            <select selected data-placeholder="Choose" class="form-control budget" name="budget" id="budget" required required style="width:100%"> 
                                                    <option value=""></option> 
                                                        @foreach($budget_range as $data)
                                                        <option value="{{$data->description}}">{{$data->description}}</option>
                                                            @endforeach
                                                </select>
                                            </td> 

                                            <td> 
                                            <input class="form-control text-center quantity_item" type="text" oninput="validate(this)" required name="quantity" id="quantity"  value="1" min="0" max="9999999999" step="any" onkeypress="return event.charCode <= 57"> 
                                            </td> 
                                                            
                                        </tr>
                                    </tr>
                                
                                </tbody>

                            </table>
                        </div>
                    </div>
            
                </div>
                <br>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <label>Comments</label>
                <textarea placeholder="Comments ..." oninput="validate(this)" rows="3" class="form-control finput" name="requestor_comments"></textarea>
            </div>
        </div>
</div>
<hr>
<div class="col-md-12">
    <div class="form-group text-center">
        <label>CAN'T FIND WHAT YOU ARE LOOKING FOR?</label>
        <a href="#">CHECK HERE</a>
    </div>
</div>