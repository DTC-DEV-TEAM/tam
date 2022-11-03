@extends('crudbooster::admin_template')
    @push('head')
    
        <style type="text/css">   
            /* loading spinner */
            .loading {
                z-index: 20;
                position: absolute;
                top: 0;
                bottom:0;
                left:0;
                width: 100%;
                height: 1500px;
                background-color: rgba(0,0,0,0.4);
            }
            .loading-content {
                position: absolute;
                border: 16px solid #f3f3f3; /* Light grey */
                border-top: 16px solid #3498db; /* Blue */
                border-radius: 50%;
                width: 50px;
                height: 50px;
                top: 40%;
                left:50%;
                bottom:0;
                /* margin-left: -4em; */
                animation: spin 2s linear infinite;
                }
                
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
            .comment_div {
                box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
                background: #f5f5f5;
                height: 300px;
                padding: 10px;
                overflow-y: scroll;
                word-wrap: break-word;
            }
            .text-comment{
                display: block;
                background: #fff;
                padding:5px;
                border-radius: 2px;
                box-shadow: rgba(0, 0, 0, 0.05) 0px 6px 24px 0px, rgba(0, 0, 0, 0.08) 0px 0px 0px 1px;
                margin-bottom:0;
            }
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
    <div class='panel-heading'>
    Edit Asset Inventory Form
    </div>

    <form action='{{CRUDBooster::mainpath('edit-save/'.$Body->id)}}' method="POST" id="EditInventoryForm" enctype="multipart/form-data">
        <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
        <input type="hidden" value="{{$Body->id}}" name="request_type_id" id="request_type_id">
        <input type="hidden" value="{{$Body->digits_code}}" name="digits_code" id="digits_code">
        <input type="hidden" value="{{$Body->asset_code}}" name="asset_code" id="asset_code">

        <div class='panel-body'>
        <div class="row">     
         <div class="col-md-12">                      
            <label class="control-label col-md-2">Asset Code:</label>
            <div class="col-md-4">
                    <p>{{$Body->asset_code}}</p>
            </div>
            <label class="control-label col-md-2">Serial No:</label>
            <div class="col-md-4">
                    <p>{{$Body->serial_no}}</p>
            </div>
            <label class="control-label col-md-2">Digits Code:</label>
            <div class="col-md-4">
                    <p>{{$Body->digits_code}}</p>
            </div>
            <label class="control-label col-md-2">Item Description:</label>
            <div class="col-md-4">
                    <p>{{$Body->item_description}}</p>
            </div>
         </div>
        </div>
           <div class="row">
             <div class="col-md-12">
                     <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Item Condition</label>
                            <select required selected data-placeholder="-- Please Select Item Condition --" id="item_condition" name="item_condition" class="form-select select2" style="width:100%;">
                           
                            <option <?php if ($Body->item_condition == 'Good') echo 'selected'; ?>>Good</option>
                            <option <?php if ($Body->item_condition == 'Defective') echo 'selected'; ?>>Defective</option>
                                <!-- <option value="{{ $key }}" {{$suggestions_datas[0]->categories_id == $key ? "selected" : "" }}>{{ $value->item_condition }}</option> -->
                         
                            </select>
                        </div>
                       </div>
             </div>
            </div>
            <br>
            <!-- Comment Section -->
            <div class="row">  
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Comments:</label>
                        <div class="comment_div">
                        @foreach($comments as $key => $comment)
                            <span class="text-comment">
                            <p style="margin-top:5px"><strong>{{ $comment->asset_code }}:</strong>  {{ $comment->comments }} </p>
                            <p style="text-align:right; font-size:10px; font-style: italic; border-bottom:1px solid #d2d6de"> {{ $comment->created_at }} </p>                 
                            </span>
                        @endforeach
                        </div>
                        <br>
                        <select required selected data-placeholder="-- Select Comments --" id="comments" name="comments[]" class="form-select select2forcomments" style="width:100%;" multiple="multiple">
                            @foreach($good_defect_lists as $good_defect_list)
                                <option value=""></option>
                                <option value="{{ $good_defect_list->defect_description }}">{{ $good_defect_list->defect_description }}</option>
                            @endforeach
                        </select>
                        <br>
                        <br>
                        <div class="form-group" id="others" style="display:none">
                        <label class="control-label"> Other Comment</label>
                            <input class="form-control" type="text"  placeholder="Please input comments" name="other_comment" id="other_comment">
                        </div>
                        <!-- <textarea placeholder="Comment ..." rows="3" class="form-control" name="comments"></textarea> -->
                    </div>
                </div>
            </div>  
        </div>
        

        <div class='panel-footer'>

            <a href="{{ CRUDBooster::mainpath() }}" class="btn btn-default">{{ trans('message.form.cancel') }}</a>

            <button class="btn btn-primary pull-right" type="submit" id="btnEditSubmit"> <i class="fa fa-save" ></i>  Edit</button>

        </div>

    </form>


</div>

@endsection

@push('bottom')
    <script type="text/javascript">
        $(document).ready(function() {
            $('.select2').select2({placeholder_text_single : "-- Select --"})

            $('.select2forcomments').select2({
            placeholder_text_single : "-- Select --",
            multiple: true});
            $("#comments").find(':selected').attr('disabled','disabled');
            $("#comments").trigger('change');
            
            $('#comments').change(function(){
              var value =  this.value;
              var allselected = [];
              $("#comments :selected").each(function() {
                allselected.push(this.value.toLowerCase().replace(/\s/g, ''));
              });
            
              if($.inArray("others",allselected) > -1){
                $('#others').show();
              }else{
                $('#others').hide();
              }
 
            });

            $("#btnEditSubmit").click(function(event) {
            event.preventDefault();
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
                        $("#EditInventoryForm").submit();  
                        showLoading();                      
                });
            });
        });
    </script>
@endpush