$( document ).ready(function() {
    
    let x = $(location).attr('pathname').split('/');
    let add_action = x.includes("add");
    let edit_action = x.includes("edit");

    var  address_1 = '';
    var  address_2 = '';
    var  address_3 = '';
    var  address_4 = '';
    var  address_5 = '';
    var  address_6 = '';
    var  address_7 = '';
    var  name1 = '';
    var  name2 = '';
    var  number1 = '';
    var  number2 = '';
    var  number3 = '';
    var concat_number1 = '';
    var concat_number2 = '';
    var concat_number3 = '';

    var  number4 = '';
    var  number5 = '';
    var  number6 = '';
    var concat_number4 = '';
    var concat_number5 = '';
    var concat_number6 = '';

    var offset = 250;
    var duration = 300;
    var channel_code = '';
     
    /*jQuery(window).scroll(function() {
        if (jQuery(this).scrollTop() > offset) {
            jQuery('.back-to-top').fadeIn(duration);
        } 
        else {
            jQuery('.back-to-top').fadeOut(duration);
        }
    });
     
    jQuery('.back-to-top').click(function(event) {
     
        event.preventDefault();
        jQuery('html, body').animate({scrollTop: 0}, duration);
        return false;
     
    });

    $('#alerts_msg').fadeTo(1500, 500).slideUp(500, function(){
     	$('#alerts_msg').slideUp(1500);
    });*/
 
    if (edit_action) {

        address_1 = $('#building_no').val()+' ';
        address_2 = $('#lot_blk_no_streetname').val()+' ';
        address_3 = $('#barangay').val()+' ';
        address_6 = $('#area_code_zip_code').val()+' ';

        var search = $('#city_id').val();
        var search2 = $('#state_id').val();
        var search3 = $('#country_id').val();

        var bill_to = $('#bill_to').val();

        var department = $('#department_id').val();



        try {

            $.ajax({

                type: 'GET',
                url: 'https://localhost/dam/public/admin/employees/getCity/' + search,
                data: '',
                success: function(result) {
                    address_4 = result+' ';
                },

                error: function() {
                    alert(error);
                }
            });

        }catch (error){
            console.log(error);
        }

        try {

            $.ajax({

                type: 'GET',
                url: 'https://localhost/dam/public/admin/employees/getState/' + search2,
                data: '',
                success: function(result) {
                    address_5 = result+' ';
                },

                error: function() {
                    alert(error);
                }
            });

        } catch (error) {
            console.log(error);
        }  

        try {

            $.ajax({

                type: 'GET',
                url: 'https://localhost/dam/public/admin/employees/getCountry/' + search3,
                data: '',
                success: function(result) {
                    address_7 = result;
                },

                error: function() {
                    alert(error);
                }
            });

        } catch (error) {
            console.log(error);
        }    
        

        try {

            $.ajax({

                type: 'GET',
                url: 'https://localhost/dam/public/admin/employees/getEmployee/' + bill_to,
                data: '',
                success: function(result) {

                    bill_to = result;
                          

    
                        for (i = 0; i < result.length; ++i) {
                            
                            bill_to = result[i].position_id;

                        }

               
                        $.ajax({

                            type: 'GET',
                            url: 'https://localhost/dam/public/admin/employees/getPosition/' + department,
                            data: '',
                            success: function(result) {
                           
                                position = result;
                                    
                                    var i;
                                    var showData = [];

                                    showData[0] = "<option value=''>** Please select a Position</option>";
                
                                    for (i = 0; i < result.length; ++i) {
                                        var j = i + 1;
                                        
                                    

                                        if(bill_to == result[i].id){
                                            showData[j] = "<option value='"+result[i].id+"' selected>"+result[i].sub_department_name+"</option>";
                                        }else{
                                            showData[j] = "<option value='"+result[i].id+"'>"+result[i].sub_department_name+"</option>";
                                        }
                                            
                                    }
                                    //$('.account'+id_data).find('option').remove();
                                    //jQuery('.account'+id_data).html(showData);          
                                    
                                    jQuery('#sub_department_id').html(showData);
                                    $('#sub_department_id').attr('disabled', false);

                                

                            },

                            error: function() {
                                alert(error);
                            }
                        });
                     

                },

                error: function() {
                    alert(error);
                }
            });

        } catch (error) {
            console.log(error);
        } 

        

        try {



        } catch (error) {
            console.log(error);
        }  

        

    }

    if (add_action) {
        $('#company_name').val('1');
    }

    $("#supplier_name, #building_no, #lot_blk_no_streetname, #barangay, #area_code_zip_code, #contact_person_ln, #contact_person_fn, #company_name, #position_id").keyup(function() {
        this.value = this.value.toLocaleUpperCase();
    });

    $("#building_no, #lot_blk_no_streetname, #barangay, #area_code_zip_code, #contact_person_ln, #contact_person_fn").keyup(function() {
        this.value = this.value.toLocaleUpperCase();
    });

    $('#building_no').on('change keyup click', function() {

        address_1 = this.value;

     

        if (add_action) {

           if(address_1 == 'N/A' || address_1 == 'n/a'){

               address_1 = '';
               checkConcat();
           }else{
                address_1 += ' ';
                checkConcat();
           } 

        }else if(edit_action) {

           if(address_1 == 'N/A' || address_1 == 'n/a'){

               address_1 = '';
               checkConcat();
           }else{
                address_1 += ' ';
                checkConcat();
           } 

        }
    });

    $('#lot_blk_no_streetname').on('change keyup click', function() {

        address_2 = this.value;

        if (add_action) {

            if(address_2 == 'N/A' || address_2 == 'n/a'){
 
                address_2 = '';
                checkConcat();
            }else{
                 address_2 += ' ';
                 checkConcat();
            }    
         }else if(edit_action) {
            if(address_2 == 'N/A' || address_2 == 'n/a'){
 
                address_2 = '';
                checkConcat();
            }else{
                 address_2 += ' ';
                 checkConcat();
            }  
         }
         
    });
    
    $('#barangay').on('change keyup click', function() {

        address_3 = this.value;

        if (add_action) {

            if(address_3 == 'N/A' || address_3 == 'n/a'){
 
                address_3 = '';
                checkConcat();
            }else{
                address_3 += ' ';
                 checkConcat();
            }    

         }else if(edit_action) {

            if(address_3 == 'N/A' || address_3 == 'n/a'){
 
                address_3 = '';
                checkConcat();
            }else{
                address_3 += ' ';
                 checkConcat();
            } 

         }
    });


    $('#city_id').on('change', function() {

        address_4 = this.value;

       
       
         try {

            $.ajax({

                type: 'GET',
                url: 'https://localhost/dam/public/admin/employees/getCity/' + address_4,
                data: '',
                success: function(result) {

                    address_4 = result;

                    if (add_action) {

                        if(address_4 == 'N/A' || address_4 == 'n/a'){
             
                            address_4 = '';
                            checkConcat();
                        }else{
                            address_4 += ' ';
                             checkConcat();
                        }  

                     }else if(edit_action) {

                        if(address_4 == 'N/A' || address_4 == 'n/a'){
             
                            address_4 = '';
                            checkConcat();
                        }else{
                            address_4 += ' ';
                             checkConcat();
                        }  

                     }

                },

                error: function() {
                    alert(error);
                }
            });

        } catch (error) {
            console.log(error);
        }

    });


  //  $('#position_id').attr('disabled', true);

    

    $('#department_id').on('change', function() {

        department = this.value;

         try {

            $.ajax({

                type: 'GET',
                url: 'https://localhost/dam/public/admin/employees/getPosition/' + department,
                data: '',
                success: function(result) {

                    position = result;

                    if (add_action) {

        
                        var i;
                        var showData = [];

                        showData[0] = "<option value=''>** Please select a Position</option>";
    
                        for (i = 0; i < result.length; ++i) {
                            var j = i + 1;
                            
                            showData[j] = "<option value='"+result[i].id+"'>"+result[i].sub_department_name+"</option>";
                        }
                        //$('.account'+id_data).find('option').remove();
                        //jQuery('.account'+id_data).html(showData);          
                        
                        jQuery('#sub_department_id').html(showData);
                        $('#sub_department_id').attr('disabled', false);
                      
                     }else if(edit_action) {
                          
                        var i;
                        var showData = [];

                        showData[0] = "<option value=''>** Please select a Position</option>";
    
                        for (i = 0; i < result.length; ++i) {
                            var j = i + 1;
                            
                            showData[j] = "<option value='"+result[i].id+"'>"+result[i].sub_department_name+"</option>";
                        }
                        //$('.account'+id_data).find('option').remove();
                        //jQuery('.account'+id_data).html(showData);          
                        
                        jQuery('#sub_department_id').html(showData);
                        $('#sub_department_id').attr('disabled', false);

                     }

                },

                error: function() {
                    alert(error);
                }
            });

        } catch (error) {
            console.log(error);
        }    

    });


    $('#state_id').on('change', function() {

        address_5 = this.value;

         try {

            $.ajax({

                type: 'GET',
                url: 'https://localhost/dam/public/admin/employees/getState/' + address_5,
                data: '',
                success: function(result) {

                    address_5 = result;

                    if (add_action) {

                        if(address_5 == 'N/A' || address_5 == 'n/a'){
             
                            address_5 = '';
                            checkConcat();
                        }else{
                            address_5 += ' ';
                             checkConcat();
                        }    
                     }else if(edit_action) {
                        if(address_5 == 'N/A' || address_5 == 'n/a'){
             
                            address_5 = '';
                            checkConcat();
                        }else{
                            address_5 += ' ';
                             checkConcat();
                        }     
                     }

                },

                error: function() {
                    alert(error);
                }
            });

        } catch (error) {
            console.log(error);
        }    

    });


    $('#area_code_zip_code').on('change keyup click', function() {

        address_6 = this.value;

        if (add_action) {

            if(address_6 == 'N/A' || address_6 == 'n/a'){
 
                address_6 = '';
                checkConcat();
            }else{
                address_6 += ' ';
                 checkConcat();
            }    
         }else if(edit_action) {
            if(address_6 == 'N/A' || address_6 == 'n/a'){
 
                address_6 = '';
                checkConcat();
            }else{
                address_6 += ' ';
                 checkConcat();
            }         
         }
    });

    $('#country_id').on('change', function() {

        address_7 = this.value;

        try {

            $.ajax({

                type: 'GET',
                url: 'https://localhost/dam/public/admin/employees/getCountry/' + address_7,
                data: '',
                success: function(result) {

                    address_7 = result;

                    if (add_action) {

                        if(address_7 == 'N/A' || address_7 == 'n/a'){
             
                            address_7 = '';
                            checkConcat();
                        }else{
                          
                             checkConcat();
                        }    
                     }else if(edit_action) {
                        if(address_7 == 'N/A' || address_7 == 'n/a'){
             
                            address_7 = '';
                            checkConcat();
                        }else{
                          
                             checkConcat();
                        } 
                     }

                },

                error: function() {
                    alert(error);
                }
            });

        } catch (error) {
            console.log(error);
        }      

    });

    $('#contact_person_ln').on('change keyup click', function() {

        name1 = this.value;

        if (add_action) {

            if(name1 == 'N/A' || name1 == 'n/a'){
 
                name1 = '';
                checkConcatName();
            }else{

                if($('#contact_person_ln').val() == null || $('#contact_person_ln').val() == ''){
                    // name1 += ', ';
                    name1 = '';
                    channel_code = '';
                }else{
                    name1 += ', ';
                    channel_code = '.EEE';
                }

                checkConcatName();
            }    
         }else if(edit_action) {

            if(name1 == 'N/A' || name1 == 'n/a'){
 
                name1 = '';
                checkConcatName();
            }else{

                if($('#contact_person_ln').val() == null || $('#contact_person_ln').val() == ''){
                    // name1 += ', ';
                    name1 = '';
                    channel_code = '';
                }else{
                    name1 += ', ';
                    channel_code = '.EEE';
                }

                checkConcatName();
            }

         }
    });


    $('#contact_person_fn').on('change keyup click', function() {

        name2 = this.value;

        if (add_action) {

            if(name2 == 'N/A' || name2 == 'n/a'){
          
                name2 = '';
                checkConcatName();
            }else{
                checkConcatName();
            }    
         }else if(edit_action) {
            if(name2 == 'N/A' || name2 == 'n/a'){
          
                name2 = '';
                checkConcatName();
            }else{
                checkConcatName();
            }   
         }
    });

    $('#international_country_code_1').on('change keyup click', function() {

        number1 = this.value;

        if (add_action) {

            if(number1 == 'N/A' || number1 == 'n/a' || number1 == ''){
 
                concat_number1 = number1 = '';
                checkConcatNumber1();
            }else{
                 concat_number1 = '+('+ number1 + ')';
                checkConcatNumber1();
            }    
         }else if(edit_action) {
            if(number1 == 'N/A' || number1 == 'n/a' || number1 == ''){
 
                concat_number1 = number1 = '';
                checkConcatNumber1();
            }else{
                 concat_number1 = '+('+ number1 + ')';
                checkConcatNumber1();
            } 
         }
    });


    $('#area_code_1').on('change keyup click', function() {

        number2 = this.value;

        if (add_action) {

            if(number2 == 'N/A' || number2 == 'n/a' || number2 == ''){
 
                concat_number2 =  number2 = '';
                checkConcatNumber1();
            }else{
                 concat_number2 = '('+ number2 + ')';
                checkConcatNumber1();
            }    
         }else if(edit_action) {
            if(number2 == 'N/A' || number2 == 'n/a' || number2 == ''){
 
                concat_number2 =  number2 = '';
                checkConcatNumber1();
            }else{
                 concat_number2 = '('+ number2 + ')';
                checkConcatNumber1();
            }             
         }
    });

    $('#number_1').on('change keyup click', function() {

        number3 = this.value;

        if (add_action) {

            if(number3 == 'N/A' || number3 == 'n/a' || number3 == ''){
 
                concat_number3 = number3 = '';
                checkConcatNumber1();
            }else{
                 concat_number3 = number3;
                checkConcatNumber1();
            }    
         }else if(edit_action) {
            if(number3 == 'N/A' || number3 == 'n/a' || number3 == ''){
 
                concat_number3 = number3 = '';
                checkConcatNumber1();
            }else{
                 concat_number3 = number3;
                checkConcatNumber1();
            }    
         }
    });

    //
    $('#international_country_code_2').on('change keyup click', function() {

        number4 = this.value;

        if (add_action) {

            if(number4 == 'N/A' || number4 == 'n/a' || number4 == ''){
 
                concat_number4 =  number4 = '';
                checkConcatNumber2();
            }else{
                 concat_number4 = '+('+ number4 + ')';
                 checkConcatNumber2();
            }    
         }else if(edit_action) {
            if(number4 == 'N/A' || number4 == 'n/a' || number4 == ''){
 
                concat_number4 =  number4 = '';
                checkConcatNumber2();
            }else{
                 concat_number4 = '+('+ number4 + ')';
                 checkConcatNumber2();
            }  
         }
    });


    $('#area_code_2').on('change keyup click', function() {

        number5 = this.value;

        if (add_action) {

            if(number5 == 'N/A' || number5 == 'n/a' || number5 == ''){
 
                    concat_number5 =  number5 = '';
                    checkConcatNumber2();
                }else{
                    concat_number5 = '('+ number5 + ')';
                    checkConcatNumber2();
                }   

        }else if(edit_action) {
                if(number5 == 'N/A' || number5 == 'n/a' || number5 == ''){
 
                    concat_number5 =  number5 = '';
                    checkConcatNumber2();
                }else{
                    concat_number5 = '('+ number5 + ')';
                    checkConcatNumber2();
                }   
        }
    });

    $('#number_2').on('change keyup click', function() {

        number6 = this.value;

        if (add_action) {

            if(number6 == 'N/A' || number6 == 'n/a' || number6 == ''){
 
                concat_number6 =   number6 = '';
                checkConcatNumber2();
            }else{
                 concat_number6 = number6;
                 checkConcatNumber2();
            }    
         }else if(edit_action) {
            if(number6 == 'N/A' || number6 == 'n/a' || number6 == ''){
 
                concat_number6 =   number6 = '';
                checkConcatNumber2();
            }else{
                 concat_number6 = number6;
                 checkConcatNumber2();
            }    
         }
    });

    function checkConcat() {

            $('#address_line1').val(address_1.concat(address_2, address_3, address_4, address_5, address_6, address_7,));

    }

    function checkConcatName() {

        if(name1 == ""){
            name1 = $('#contact_person_ln').val();

            name1 += ', ';

            channel_code = '.EEE';
        }

        $('#contact_person').val(name1.concat(name2));
        $('#bill_to').val(name1.concat(name2));
        $('#customer_location_name').val(name1.concat(name2).concat(channel_code));
    }

    function checkConcatNumber1(){

        $('#contact_landline_no').val(concat_number1.concat(concat_number2, concat_number3));

    }

    function checkConcatNumber2(){

        $('#mobile_number').val(concat_number4.concat(concat_number5, concat_number6));

    }

});