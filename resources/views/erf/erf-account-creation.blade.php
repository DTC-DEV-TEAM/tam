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
        Creation of Account Form @if($Header->locking_create_account !== CRUDBooster::myId()) <span style="color: red">(This form request currently used by {{$Header->current_user}}!)</span> @endif
    </div>

    <form method='post' id="createAccount">
        <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
        <input type="hidden" value="" name="approval_action" id="approval_action">
        <input type="hidden" name="id" id="id" value="{{$Header->requestid}}">
        <input type="hidden" value="{{$Header->locking_create_account}}" name="locking" id="locking">
        <input type="hidden" value="{{CRUDBooster::myId()}}" name="current_user" id="current_user">
     
            <div class="card">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"> Email</label>
                            <input type="text" class="form-control finput" name="email" id="email" aria-describedby="basic-addon1" onChange="checkemailAvailability()">             
                            <div id="email-availability-status"></div>
                        </div>
                 
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                        <label class="control-label">Company</i></label>
                                <input type="text" class="form-control finput" name="company" id="company" value="{{$Header->company}}" aria-describedby="basic-addon1" readonly>             
                        </div>
                    </div>
                </div>
                <div class="row"> 
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"> First Name</label>
                            <input type="text" class="form-control finput" name="first_name" id="first_name" value="{{$Header->first_name}}" aria-describedby="basic-addon1" readonly>             
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"> Last Name</label>
                            <input type="text" class="form-control finput" name="last_name" id="last_name" value="{{$Header->last_name}}" aria-describedby="basic-addon1" readonly>                                                      
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                        <label class="control-label"> Department</label>
                        <input type="text" class="form-control finput" name="department" id="department" value="{{$Header->department}}" aria-describedby="basic-addon1" readonly>             
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"> Position</label>
                            <input type="text" class="form-control finput" name="position" id="position" value="{{$Header->position}}" aria-describedby="basic-addon1" readonly>                                                      
                        </div>
                    </div>
                </div>
                
                <a href="{{ CRUDBooster::mainpath() }}" id="btn-cancel" class="btn btn-default">{{ trans('message.form.cancel') }}</a>
                @if($Header->locking_create_account === CRUDBooster::myId())
                <button class="btn btn-success pull-right" type="button" id="btnCreateAccount"> Create Account</button>
                @endif
            </div>
        
    </form>

@endsection
@push('bottom')
<script type="text/javascript">
    $(function(){
        $('body').addClass("sidebar-collapse");
    });
    window.onbeforeunload = function() {
        return "";
    };
    function preventBack() {
        window.history.forward();
    }
    setTimeout("preventBack()", 0);

    if($('#locking').val() === $('#current_user').val()){
        const pageHideListener = (event) => {
            var id = $('#id').val();
            $.ajaxSetup({
                headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
            });
            $.ajax({
                type: 'POST',
                url: "{{ route('delete-locking-create-account') }}",
                dataType: 'json',
                data: {
                    'header_request_id': id
                },
                success: function ()
                {
                    
                }
            });  
        };
        window.addEventListener("pagehide", pageHideListener);

        var online = navigator.onLine;
        if(online == false){
            window.addEventListener("pagehide", pageHideListener);
        }
    }

 $('#btnCreateAccount').click(function(event) {
        event.preventDefault();
        if($('#email').val() === ""){
            swal({
                    type: 'error',
                    title: 'Required Email!',
                    icon: 'error',
                    confirmButtonColor: "#367fa9",
                }); 
                event.preventDefault(); // cancel default behavior
                return false;
        }else if(IsEmail($('#email').val())==false){
                            swal({
                    type: 'error',
                    title: 'Invalid Email Format!',
                    icon: 'error',
                    confirmButtonColor: "#367fa9",
                });
                event.preventDefault();
        }else{
            $.ajax({
                url: "{{ route('getEmail') }}",
                dataType: "json",
                type: "POST",
                data: {
                    //"_token": token,
                },
                success: function (data) {
                var checkEmail = $('#email').val();
                    if($.inArray(checkEmail, data.items) != -1){
                        swal({
                                type: 'error',
                                title: 'Email Already Exist! (' + checkEmail + ')',
                                icon: 'error',
                                confirmButtonColor: "#367fa9",
                            }); 
                            event.preventDefault();
                            return false;
                    } else{
                        swal({
                            title: "Are you sure?",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#41B314",
                            cancelButtonColor: "#F9354C",
                            confirmButtonText: "Yes, create it!",
                            width: 450,
                            height: 200
                            }, function () {
                                $.ajax({
                                    data: $('#createAccount').serialize(),
                                    url: "{{ route('create-account') }}",
                                    type: "POST",
                                    dataType: 'json',
                                    success: function (data) {
                                        if (data.status == "success") {
                                            swal({
                                                type: data.status,
                                                title: data.message,
                                            });
                                            setTimeout(function(){
                                                window.location.replace(document.referrer);
                                            }, 2000); 
                                            } else if (data.status == "error") {
                                            swal({
                                                type: data.status,
                                                title: data.message,
                                            });
                                        }                            
                                    },
                                    error: function (data) {
                                        console.log('Error:', data);
                                    }
                                });                  
                        });
                    }
                }
            });
        }
            
    });

     //email validation
     function IsEmail(email) {
        var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        if(!regex.test(email)) {
            return false;
        }else{
            return true;
        }
    }

    //check email availability in database
    function checkemailAvailability() {
        $.ajax({
            url: "{{ route('checkEmail') }}",
            dataType: "json",
            data:'email='+$("#email").val(),
            type: "POST",
            success:function(data){
                $("#email-availability-status").html(data);
            },
        error:function (){}
        });
    }

    $("#btn-cancel").click(function(event) {
       event.preventDefault();
       swal({
            title: "Are you sure?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#41B314",
            cancelButtonColor: "#F9354C",
            confirmButtonText: "Yes, Go back!",
            width: 450,
            height: 200
            }, function () {
                window.history.back();                                                  
        });
    });
    
</script>
@endpush