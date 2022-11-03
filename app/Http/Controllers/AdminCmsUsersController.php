<?php namespace App\Http\Controllers;

use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use DB;
use Excel;
use CRUDbooster;
use App\Store;
use App\Channel;

class AdminCmsUsersController extends \crocodicstudio\crudbooster\controllers\CBController {

	public function __construct() {
		// Register ENUM type
		DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
	}

	public function cbInit() {
		# START CONFIGURATION DO NOT REMOVE THIS LINE
		$this->limit				= "20";
		$this->orderby				= "id_cms_privileges,asc";
		$this->table				= 'cms_users';
		$this->primary_key			= 'id';
		$this->title_field			= "name";
		$this->button_action_style	= 'button_icon';	
		$this->button_import		= FALSE;	
		$this->button_export		= FALSE;	
		# END CONFIGURATION DO NOT REMOVE THIS LINE
	
		# START COLUMNS DO NOT REMOVE THIS LINE
		$this->col = array();
		$this->col[] = array("label"=>"Name","name"=>"name");
		$this->col[] = array("label"=>"Email","name"=>"email");
		$this->col[] = array("label"=>"Privilege","name"=>"id_cms_privileges","join"=>"cms_privileges,name");
		// $this->col[] = array("label"=>"Channel","name"=>"channels_id", "join"=>"channels,channel_name");
		//$this->col[] = array("label"=>"Store Name","name"=>"stores_id", "join"=>"stores,store_name");
		$this->col[] = array("label"=>"Photo","name"=>"photo","image"=>1);
		$this->col[] = array("label"=>"Status","name"=>"status");
		# END COLUMNS DO NOT REMOVE THIS LINE

		# START FORM DO NOT REMOVE THIS LINE
		$this->form = array();
		if(CRUDBooster::isSuperadmin() || CRUDBooster::myPrivilegeName() == "Admin" || CRUDBooster::myPrivilegeName() == "HR") {

		    $this->form[] = array("label"=>"First Name","name"=>"first_name",'required'=>true,'validation'=>'required|min:2', 'width'=>'col-sm-5');
    		$this->form[] = array("label"=>"Last Name","name"=>"last_name",'required'=>true,'validation'=>'required|min:2', 'width'=>'col-sm-5');
    		$this->form[] = array("label"=>"Full Name","name"=>"name", "type"=>"hidden",'required'=>true,'validation'=>'required|min:3','width'=>'col-sm-5','readonly'=>true);
    		$this->form[] = array("label"=>"Email","name"=>"email",'required'=>true,'type'=>'email','validation'=>'required|email|unique:cms_users,email,'.CRUDBooster::getCurrentId(), 'width'=>'col-sm-5');		
    		$this->form[] = array("label"=>"Photo","name"=>"photo","type"=>"upload","help"=>"Recommended resolution is 200x200px",'validation'=>'image|max:1000','resize_width'=>90,'resize_height'=>90, 'width'=>'col-sm-5');
		}else{
		    $this->form[] = array("label"=>"First Name","name"=>"first_name",'required'=>true,'validation'=>'required|min:3', 'width'=>'col-sm-5', 'readonly'=>true);
    		$this->form[] = array("label"=>"Last Name","name"=>"last_name",'required'=>true,'validation'=>'required|min:2', 'width'=>'col-sm-5', 'readonly'=>true);
    		$this->form[] = array("label"=>"Full Name","name"=>"name", "type"=>"hidden",'required'=>true,'validation'=>'required|min:3','width'=>'col-sm-5','readonly'=>true, 'readonly'=>true);
    		$this->form[] = array("label"=>"Email","name"=>"email",'required'=>true,'type'=>'email','validation'=>'required|email|unique:cms_users,email,'.CRUDBooster::getCurrentId(), 'width'=>'col-sm-5', 'readonly'=>true);		
    		$this->form[] = array("label"=>"Photo","name"=>"photo","type"=>"upload","help"=>"Recommended resolution is 200x200px",'validation'=>'image|max:1000','resize_width'=>90,'resize_height'=>90, 'width'=>'col-sm-5', 'readonly'=>true);
		}
		$this->form[] = array("label"=>"Password","name"=>"password","type"=>"password","help"=>"Please leave empty if not changed", 'width'=>'col-sm-5');
		
		if((CRUDBooster::isSuperadmin() || CRUDBooster::myPrivilegeName() == "Admin" || CRUDBooster::myPrivilegeName() == "HR") && (CRUDBooster::getCurrentMethod() == 'getEdit' || CRUDBooster::getCurrentMethod() == 'postEditSave')){
		    $this->form[] = array("label"=>"Status","name"=>"status","type"=>"select","dataenum"=>"ACTIVE;INACTIVE",'required'=>true, 'width'=>'col-sm-5');
		}
		if(CRUDBooster::myPrivilegeName() == "Admin"){
			$this->form[] = array("label"=>"Privilege","name"=>"id_cms_privileges","type"=>"select","datatable"=>"cms_privileges,name","datatable_where"=>"name LIKE '%REQUESTOR' || name LIKE '%OIC' || name LIKE '%AP CHECKER' || name LIKE '%TREASURY'", 'width'=>'col-sm-5');				
			// $this->form[] = array("label"=>"Channel","name"=>"channels_id","type"=>"select","datatable"=>"channels,channel_name", 'width'=>'col-sm-5');
			// $this->form[] = array("label"=>"Store Name","name"=>"stores_id","type"=>"check-box","datatable"=>"stores,store_name", 'width'=>'col-sm-10' );
			//$this->form[] = array("label"=>"Stores","name"=>"stores_id","type"=>"select","datatable"=>"stores,name_name", 'required'=>true,'width'=>'col-sm-5');				
		}elseif(CRUDBooster::myPrivilegeName() == "HR"){
			$this->form[] = array("label"=>"Privilege","name"=>"id_cms_privileges","type"=>"select","datatable"=>"cms_privileges,name","datatable_where"=>"name LIKE '%REQUESTOR' || name LIKE '%OIC' || name LIKE '%EMPLOYEE' || name LIKE '%TREASURY'", 'width'=>'col-sm-5');				
			$this->form[] = array("label"=>"Employee Name","name"=>"employee_id","type"=>"select2","datatable"=>"employees,bill_to", 'width'=>'col-sm-5' );
		}
		elseif(CRUDBooster::isSuperadmin()) {

			$this->form[] = array("label"=>"Privilege","name"=>"id_cms_privileges","type"=>"select","datatable"=>"cms_privileges,name", 'width'=>'col-sm-5');	

			//$this->form[] = ['label'=>'Company Name','name'=>'company_name_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-5','datatable'=>'companies,company_name','datatable_where'=>"status = 'ACTIVE'"];

			$this->form[] = array("label"=>"Employee Name","name"=>"employee_id","type"=>"select2","datatable"=>"employees,bill_to", 'width'=>'col-sm-5' );

			$this->form[] = array("label"=>"Location","name"=>"location_id","type"=>"check-box5","datatable"=>"warehouse_location_model,location", 'datatable_where'=>"id != '4'",'width'=>'col-sm-10' );

			$this->form[] = array("label"=>"Stores","name"=>"store_id","type"=>"check-box","datatable"=>"stores,bea_mo_store_name", 'datatable_where'=>"status = 'ACTIVE'", 'width'=>'col-sm-10' );

			
			//$this->form[] = array("label"=>"Department","name"=>"department_id","type"=>"check-box","datatable"=>"departments,department_name", 'width'=>'col-sm-10' );

		}


		# END FORM DO NOT REMOVE THIS LINE

		$this->button_selected = array();
        if(CRUDBooster::isUpdate() && (CRUDBooster::isSuperadmin() || CRUDBooster::myPrivilegeName() == "Admin" || CRUDBooster::myPrivilegeName() == "HR"))
        {
        	$this->button_selected[] = ['label'=>'Set Login Status OFFLINE ','icon'=>'fa fa-check-circle','name'=>'set_login_status_OFFLINE'];
        	$this->button_selected[] = ['label'=>'Set Status INACTIVE ','icon'=>'fa fa-check-circle','name'=>'set_status_INACTIVE'];
        	$this->button_selected[] = ['label'=>'Reset Password ','icon'=>'fa fa-check-circle','name'=>'reset_password'];
		}

		$this->table_row_color = array();     	          
	   // $this->table_row_color[] = ["condition"=>"[login_status_id] == 1","color"=>"success"];

		$this->table_row_color[] = ["condition"=>"[status] == INACTIVE","color"=>"danger"];
		
	    
	    $this->index_button = array();
        if(CRUDBooster::getCurrentMethod() == 'getIndex') {
			if(CRUDBooster::isSuperadmin()){
				$this->index_button[] = [
					"title"=>"Upload User Accounts",
					"label"=>"Upload User Accounts",
					"icon"=>"fa fa-download",
					"url"=>CRUDBooster::mainpath('useraccount-upload')];
			}
		}

		$this->style_css = NULL;
		$this->style_css = "

			.ui-datepicker-year, .ui-datepicker-month{
				color:	#337ab7 !important;
			}


			";

		$this->script_js = NULL;
		$this->script_js = "
		$(document).ready(function() {
	
			$('form').submit(function(){
 
                    $('.btn.btn-success').attr('disabled', true);
                    return true; 
            });

			$('.js-example-basic-multiple').select2();
			$('.js-example-basic-multiple').select2({
			theme: 'classic'
			});

			let x = $(location).attr('pathname').split('/');
			let add_action = x.includes('add');
			let edit_action = x.includes('edit');


			if (add_action){
  
				$('#form-group-department_id').hide();
			
				$('#department_id').removeAttr('required');

				$('#form-group-employee_id').hide();
			
				$('#employee_id').removeAttr('required');

				$('#form-group-company_name_id').hide();
				$('#company_name_id').removeAttr('required');

				$('#form-group-store_id').hide();
				$('#store_id').removeAttr('required');


				$('#form-group-location_id').hide();
				$('#location_id').removeAttr('required');
				

				$('#id_cms_privileges').change(function() {

					if($(this).val() == 2){

						$('#form-group-employee_id').show();
						$('#employee_id').attr('required', 'required');

						$('#form-group-department_id').hide();
						$('#department_id').removeAttr('required');

						$('#form-group-company_name_id').hide();
						$('#company_name_id').removeAttr('required');

						$('#form-group-store_id').hide();
						$('#store_id').removeAttr('required');

						$('#form-group-location_id').hide();
						$('#location_id').removeAttr('required');
	
					}else if($(this).val() == 8){

						$('#form-group-department_id').hide();	
						$('#department_id').removeAttr('required');
						$('#form-group-employee_id').hide();
						$('#employee_id').removeAttr('required');

						$('#form-group-company_name_id').hide();
						$('#company_name_id').removeAttr('required');


						$('#form-group-store_id').show();
						$('#store_id').attr('required', 'required');

						$('#form-group-location_id').hide();
						$('#location_id').removeAttr('required');


	
					}else if($(this).val() == 5){

						$('#form-group-department_id').hide();	
						$('#department_id').removeAttr('required');
						$('#form-group-employee_id').hide();
						$('#employee_id').removeAttr('required');

						$('#form-group-company_name_id').hide();
						$('#company_name_id').removeAttr('required');

						$('#form-group-store_id').hide();
						$('#store_id').removeAttr('required');


						$('#form-group-location_id').show();
						$('#location_id').attr('required', 'required');


	
					}else{

						$('#form-group-department_id').hide();	
						$('#department_id').removeAttr('required');
						$('#form-group-employee_id').hide();
						$('#employee_id').removeAttr('required');

						$('#form-group-company_name_id').hide();
						$('#company_name_id').removeAttr('required');

						$('#form-group-store_id').hide();
						$('#store_id').removeAttr('required');

						$('#form-group-location_id').hide();
						$('#location_id').removeAttr('required');

					}

				});

			}else if(edit_action){
				
				$('#form-group-department_id').hide();
				$('#department_id').removeAttr('required');

				$('#form-group-employee_id').hide();
				$('#employee_id').removeAttr('required');

				$('#form-group-company_name_id').hide();
				$('#company_name_id').removeAttr('required');

				$('#form-group-store_id').hide();
				$('#store_id').removeAttr('required');

				$('#form-group-location_id').hide();
				$('#location_id').removeAttr('required');
				
				$('#id_cms_privileges').change(function() {

					if($(this).val() == 2){

						$('#form-group-employee_id').show();
						$('#employee_id').attr('required', 'required');

						$('#form-group-department_id').hide();
						$('#department_id').removeAttr('required');

						$('#form-group-company_name_id').hide();
						$('#company_name_id').removeAttr('required');

						$('#form-group-store_id').hide();
						$('#store_id').removeAttr('required');

						$('#form-group-location_id').hide();
						$('#location_id').removeAttr('required');
	
					}else if($(this).val() == 8){

						$('#form-group-department_id').hide();	
						$('#department_id').removeAttr('required');
						$('#form-group-employee_id').hide();
						$('#employee_id').removeAttr('required');

						$('#form-group-company_name_id').hide();
						$('#company_name_id').removeAttr('required');


						$('#form-group-store_id').show();
						$('#store_id').attr('required', 'required');


						$('#form-group-location_id').hide();
						$('#location_id').removeAttr('required');

	
					}else if($(this).val() == 5){

						$('#form-group-department_id').hide();	
						$('#department_id').removeAttr('required');
						$('#form-group-employee_id').hide();
						$('#employee_id').removeAttr('required');

						$('#form-group-company_name_id').hide();
						$('#company_name_id').removeAttr('required');

						$('#form-group-store_id').hide();
						$('#store_id').removeAttr('required');


						$('#form-group-location_id').show();
						$('#location_id').attr('required', 'required');
					}
					else{

						$('#form-group-department_id').hide();	
						$('#department_id').removeAttr('required');
						$('#form-group-employee_id').hide();
						$('#employee_id').removeAttr('required');

						$('#form-group-company_name_id').hide();
						$('#company_name_id').removeAttr('required');

						$('#form-group-store_id').hide();
						$('#store_id').removeAttr('required');

						$('#form-group-location_id').hide();
						$('#location_id').removeAttr('required');

					}

				});


				if($('#id_cms_privileges').val() == 2){

					$('#form-group-employee_id').show();
					$('#employee_id').attr('required', 'required');

					$('#form-group-department_id').hide();
					$('#department_id').removeAttr('required');

					$('#form-group-company_name_id').hide();
					$('#company_name_id').removeAttr('required');

					$('#form-group-store_id').hide();
					$('#store_id').removeAttr('required');

					$('#form-group-location_id').hide();
					$('#location_id').removeAttr('required');

				}else if($('#id_cms_privileges').val() == 8){

					$('#form-group-department_id').hide();	
					$('#department_id').removeAttr('required');
					$('#form-group-employee_id').hide();
					$('#employee_id').removeAttr('required');

					$('#form-group-company_name_id').hide();
					$('#company_name_id').removeAttr('required');


					$('#form-group-store_id').show();
					$('#store_id').attr('required', 'required');


					$('#form-group-location_id').hide();
					$('#location_id').removeAttr('required');


				}else if($('#id_cms_privileges').val() == 5){

					$('#form-group-department_id').hide();	
					$('#department_id').removeAttr('required');
					$('#form-group-employee_id').hide();
					$('#employee_id').removeAttr('required');

					$('#form-group-company_name_id').hide();
					$('#company_name_id').removeAttr('required');

					$('#form-group-store_id').hide();
					$('#store_id').removeAttr('required');


					$('#form-group-location_id').show();
					$('#location_id').attr('required', 'required');



				}else{

					$('#form-group-department_id').hide();	
					$('#department_id').removeAttr('required');
					$('#form-group-employee_id').hide();
					$('#employee_id').removeAttr('required');

					$('#form-group-company_name_id').hide();
					$('#company_name_id').removeAttr('required');

					$('#form-group-store_id').hide();
					$('#store_id').removeAttr('required');

					$('#form-group-location_id').hide();
					$('#location_id').removeAttr('required');

				}


			}

		});
		";		

		
	}

	

	public function getProfile() {			

		$this->button_addmore = FALSE;
		$this->button_cancel  = FALSE;
		$this->button_show    = FALSE;			
		$this->button_add     = FALSE;
		$this->button_delete  = FALSE;	
		$this->hide_form 	  = ['id_cms_privileges'];

		$data['page_title'] = trans("crudbooster.label_button_profile");
		$data['row']        = CRUDBooster::first('cms_users',CRUDBooster::myId());		
		return $this->view('crudbooster::default.form',$data);				
	}

	public function hook_row_index($column_index,&$column_value) {	        
		//Your code here
	
	}

	public function hook_before_add(&$postdata) {        
	    //Your code here

		

		$postdata['created_by']=CRUDBooster::myId();

	    if($postdata['photo'] == '' || $postdata['photo'] == NULL) {
	    	$postdata['photo'] = 'uploads/mrs-avatar.png';
	    }
		$postdata['status'] = 'ACTIVE';
		$postdata['name'] = $postdata['first_name'].' '.$postdata['last_name'];
		$postdata['user_name'] = $postdata['last_name'].''.substr($postdata['first_name'], 0, 1);




		$storeData = array();
		$storeList = json_encode($postdata['store_id'], true);
		$storeArray = explode(",", $storeList);

		foreach ($storeArray as $key => $value) {
			$storeData[$key] = preg_replace("/[^0-9]/","",$value);
		}

		$postdata['store_id'] = implode(",", $storeData);
		

	}

	public function hook_before_edit(&$postdata,$id) {        
        //Your code here

            $postdata['name'] = $postdata['first_name'].' '.$postdata['last_name'];
    		$postdata['user_name'] = $postdata['last_name'].''.substr($postdata['first_name'], 0, 1);
    
        	/*if(!is_null($postdata['password'])){
    
        		DB::connection('mysql_timfs')->table('cms_users')
        		->where('email',$postdata['email'])
        		->update(
        			[
        				'password' => $postdata['password']
        			]);
    		}*/
    		
    		if(CRUDBooster::isSuperadmin()) {
    
	
    		}
    		

			$storeData = array();
    		$storeList = json_encode($postdata['store_id'], true);
    		$storeArray = explode(",", $storeList);
    
    		foreach ($storeArray as $key => $value) {
    			$storeData[$key] = preg_replace("/[^0-9]/","",$value);
    		}
    
    		$postdata['store_id'] = implode(",", $storeData);



			$storeData1 = array();
    		$storeList1 = json_encode($postdata['location_id'], true);
    		$storeArray1 = explode(",", $storeList1);
    
    		foreach ($storeArray1 as $key => $value) {
    			$storeData1[$key] = preg_replace("/[^0-9]/","",$value);
    		}
    
    		$postdata['location_id'] = implode(",", $storeData1);


    	    $postdata['updated_by']=CRUDBooster::myId();
    	    $postdata['id']=$id;
    }
    
    public function hook_after_add($id) {        
        //Your code here
        
       /* if(CRUDBooster::isSuperadmin()){
            return redirect()->action('AdminApprovalMatricesController@getIndex')->send();
			exit;
        }*/

    }

    public function hook_after_delete($id) {
		//Your code here
		DB::table('cms_users')->where('id', $id)->update(['status' => 'INACTIVE']);
	}

    public function hook_query_index(&$query) {
        //Your code here
        if(!CRUDBooster::isSuperadmin()) {
        	if(CRUDBooster::myPrivilegeName() == 'Admin' || CRUDBooster::myPrivilegeName() == "HR"){
        		$query->where('cms_users.id_cms_privileges','!=','1');
        	}
        	else{
        		$query->where('cms_users.id',"'".CRUDBooster::myId()."'");
        	}
        }    
    }

    /*
    | ---------------------------------------------------------------------- 
    | Hook for button selected
    | ---------------------------------------------------------------------- 
    | @id_selected = the id selected
    | @button_name = the name of button
    |
    */
    public function actionButtonSelected($id_selected,$button_name) {
        //Your code here
        if($button_name == 'set_login_status_OFFLINE') {
			DB::table('cms_users')->whereIn('id',$id_selected)->update(['login_status_id'=>'2']);	
		}
		if($button_name == 'set_status_INACTIVE') {
			DB::table('cms_users')->whereIn('id',$id_selected)->update(['status'=>'INACTIVE']);	
		}
		if($button_name == 'reset_password') {
			DB::beginTransaction();
		    DB::table('cms_users')->whereIn('id',$id_selected)->update([
		    	'password'			=> bcrypt('qwerty'),
		    	'reset_password'	=> 1	
		    ]);
		    DB::commit();	
		}  
    }

    public function showChangePasswordForm(){
    	if(CRUDBooster::myId()){
    		$array_data['data'] = "Reset Password";
    		return view('changepassword',$array_data);
    	}
        else{
        	return view('crudbooster::login');
        }
    }

    public function changePassword(Request $request){

 		$users = DB::table('cms_users')->where('id',CRUDBooster::myId())->first();

        if (!(\Hash::check($request->input('current-password'), $users->password))) {
            // The passwords matches
            return redirect()->back()->with("error","Your current password does not matches with the password you provided. Please try again.");
        }
 
        if(strcmp($request->input('current-password'), $request->input('new-password')) == 0){
            //Current password and new password are same
            return redirect()->back()->with("error","Your new password cannot be same as your current password. Please choose a different password.");
        }

        /*
        $this->validate($request, [
            'current-password' => 'required',
            'new-password' => 'required|string|min:6|confirmed',
        ]);
		*/
        
        \Validator::make($request->all(), [
		    'current-password' => 'required',
            'new-password' => 'required|string|min:6|confirmed',
		])->validate();
 		
        //Change Password
        try{
	        DB::beginTransaction();
		    DB::table('cms_users')->where('id',CRUDBooster::myId())->update([
		    	'password'			=> bcrypt($request->input('new-password')),
		    	'reset_password'	=> 0	
		    ]);
		    DB::commit();
		   /* Transaction successful. */
	    }
	    catch (\Exception $error_msg){
	        $error_code = $error_msg->errorInfo[1];
	        DB::rollback();
	    }
 
        return redirect()->back()->with("success","Your password has been changed successfully !");
 
	}

	public function storeListing($ids) {
		$stores = explode(",", $ids);
		return Store::whereIn('id', $stores)->pluck('store_name');
	}
	
	public function uploadUserAccountTemplate() {
		Excel::create('user-account-upload-'.date("Ymd").'-'.date("h.i.sa"), function ($excel) {
			$excel->sheet('useraccount', function ($sheet) {
				$sheet->row(1, array('FIRST NAME', 'LAST NAME', 'EMAIL', 'PRIVILEGE', 'CHANNEL', 'STORES ID'));
				$sheet->row(2, array('John', 'Doe', 'johndoe@digits.ph','Requestor','Retail','1'));
			});
		})->download('csv');
	}

	public function uploadUserAccount() {
	    if(!CRUDBooster::isSuperadmin()) {    
			CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
		}
		$data['page_title']= 'User Account Upload';
		return view('user-account.user_account_upload', $data)->render();
	}

	public function userAccountUpload(Request $request) {
			$file = $request->file('import_file');
			
			$validator = \Validator::make(
				[
					'file' => $file,
					'extension' => strtolower($file->getClientOriginalExtension()),
				],
				[
					'file' => 'required',
					'extension' => 'required|in:csv',
				]
			);

			if ($validator->fails()) {
				CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.alert_user_data_failed"), 'danger');
			}

			if (Input::hasFile('import_file')) {
				$path = Input::file('import_file')->getRealPath();
				
				$csv = array_map('str_getcsv', file($path));
				$dataExcel = Excel::load($path, function($reader) {
                })->get();
				
				$unMatch = [];
				$header = array('FIRST NAME', 'LAST NAME', 'EMAIL', 'PRIVILEGE', 'CHANNEL', 'STORES ID');

				for ($i=0; $i < sizeof($csv[0]); $i++) {
					if (! in_array($csv[0][$i], $header)) {
						$unMatch[] = $csv[0][$i];
					}
				}

				if(!empty($unMatch)) {
					CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.alert_user_data_failed"), 'danger');
				}
				
				$data = array();
				// DB::beginTransaction();

				// try {
				// 	//OrderLogicMatrix::destroy();
				// 	DB::commit();
				// } catch (\Exception $e) {
				// 	DB::rollback();
				// }
				
				if(!empty($dataExcel) && $dataExcel->count()) {
					$cnt_success = 0;
					$cnt_fail = 0;
					
					foreach ($dataExcel as $key => $value) {
						$check_upload = false;
						$privilegeId = DB::table('cms_privileges')->where('name', $value->privilege)->value('id');
						$channelId = Channel::where('channel_name', $value->channel)->value('id');
						if($privilegeId != 1){
    						$data = [
    						    'first_name'    =>  $value->first_name,
    						    'last_name' =>  $value->last_name,
    						    'name'  =>  $value->first_name. ' '.$value->last_name,
    						    'user_name' =>  $value->last_name.''.substr($value->first_name, 0, 1),
    						    'channels_id'   => $channelId,
    						    'stores_id' =>  $value->stores_id,
    						    'photo' => 'uploads/1/2019-05/businessman.png',
    						    'email' => $value->email,
    						    'password' => bcrypt('qwerty'),
    						    'id_cms_privileges' => $privilegeId,
    							'status'    => 'ACTIVE',
    							'created_by'    => CRUDBooster::myId(),
    							'created_at'    => date('Y-m-d H:i:s'),
    						];
						}

						DB::beginTransaction();

						try {
							$isItemUpload = DB::table('cms_users')->insert($data);
							DB::commit();
						} catch (\Exception $e) {
							DB::rollback();
						}

						if ($isItemUpload) {
							$check_upload = true;
							$cnt_success++;
                        }
                        else{
							$check_upload = false;
							$cnt_fail++;
                        }
					}

					if($check_upload){
                        CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.alert_upload_user_success", ['total_row'=>count($dataExcel),'success'=>$cnt_success,'fail'=>$cnt_fail]), 'success');
                    }
                    else{
                        CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.alert_upload_user_success", ['total_row'=>count($dataExcel),'success'=>$cnt_success,'fail'=>$cnt_fail]), 'success');
                    }
				}
			}
		}
	
}
