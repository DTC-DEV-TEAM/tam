<?php namespace App\Http\Controllers;

use Session;

use DB;
use Excel;
use CRUDBooster;
use App\Store;
use App\Channel;
use App\Users;
use App\Employees;
use App\ApprovalMatrix;
use App\Imports\UserImport;
use App\Exports\ExportUsersList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\HeadingRowImport;

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

		if(CRUDBooster::isSuperadmin() || CRUDBooster::myPrivilegeId() == 9 || CRUDBooster::myPrivilegeId() == 4) {
			$this->form[] = array("label"=>"Email","name"=>"email",'required'=>true,'type'=>'email','validation'=>'required|email|unique:cms_users,email,'.CRUDBooster::getCurrentId(), 'width'=>'col-sm-5');		
    		$this->form[] = array("label"=>"Password","name"=>"password","type"=>"password","help"=>"Please leave empty if not changed", 'width'=>'col-sm-5');
			$this->form[] = array("label"=>"Photo","name"=>"photo","type"=>"upload","help"=>"Recommended resolution is 200x200px",'validation'=>'image|max:1000','resize_width'=>90,'resize_height'=>90, 'width'=>'col-sm-5');
		    $this->form[] = array("label"=>"First Name","name"=>"first_name",'required'=>true,'validation'=>'required|min:2', 'width'=>'col-sm-5');
    		$this->form[] = array("label"=>"Last Name","name"=>"last_name",'required'=>true,'validation'=>'required|min:2', 'width'=>'col-sm-5');
    		$this->form[] = array("label"=>"Full Name","name"=>"name", "type"=>"hidden",'required'=>true,'validation'=>'required|min:3','width'=>'col-sm-5','readonly'=>true);
			$this->form[] = ["label"=>"Contact Person","name"=>"contact_person","type"=>"text","validation"=>"required|min:1|max:255",'width'=>'col-sm-5','placeholder'=>'Contact Person','readonly'=>true];
			$this->form[] = ["label"=>"Bill To (Company Name)","name"=>"bill_to","type"=>"text","validation"=>"required|min:1|max:255",'width'=>'col-sm-5','placeholder'=>'Bill To (Company Name)','readonly'=>true];
			$this->form[] = ["label"=>"Customer/Location Name","name"=>"customer_location_name","type"=>"text","validation"=>"required|min:1|max:255",'width'=>'col-sm-5','placeholder'=>'Customer/Location Name','readonly'=>true];
			$this->form[] = ['label'=>'Company Name','name'=>'company_name_id','validation'=>'required|min:0','width'=>'col-sm-5','value' => 'TASTELESS','readonly'=>true];
			$this->form[] = ['label'=>'Department','name'=>'department_id','type'=>'select','validation'=>'required|integer|min:0','width'=>'col-sm-5','datatable'=>'departments,department_name','datatable_where'=>"status = 'ACTIVE'",'width'=>'col-sm-5'];
			$this->form[] = ['label'=>'Sub Department','name'=>'sub_department_id','type'=>'select','validation'=>'integer|min:0','width'=>'col-sm-5','datatable'=>'sub_department,sub_department_name','parent_select'=>'department_id','width'=>'col-sm-5'];
			$this->form[] = ["label"=>"Position","name"=>"position_id","type"=>"text","validation"=>"required|min:1|max:255",'width'=>'col-sm-5','placeholder'=>'Position'];

		}else{
			$this->form[] = array("label"=>"Email","name"=>"email",'required'=>true,'type'=>'email','validation'=>'required|email|unique:cms_users,email,'.CRUDBooster::getCurrentId(), 'width'=>'col-sm-5', 'readonly'=>true);		
    		$this->form[] = array("label"=>"Password","name"=>"password","type"=>"password","help"=>"Please leave empty if not changed", 'width'=>'col-sm-5');
			$this->form[] = array("label"=>"Photo","name"=>"photo","type"=>"upload","help"=>"Recommended resolution is 200x200px",'validation'=>'image|max:1000','resize_width'=>90,'resize_height'=>90, 'width'=>'col-sm-5', 'readonly'=>true);
		    $this->form[] = array("label"=>"First Name","name"=>"first_name",'required'=>true,'validation'=>'required|min:3', 'width'=>'col-sm-5', 'readonly'=>true);
    		$this->form[] = array("label"=>"Last Name","name"=>"last_name",'required'=>true,'validation'=>'required|min:2', 'width'=>'col-sm-5', 'readonly'=>true);
    		$this->form[] = array("label"=>"Full Name","name"=>"name", "type"=>"hidden",'required'=>true,'validation'=>'required|min:3','width'=>'col-sm-5','readonly'=>true, 'readonly'=>true);
    	}
		
		
		if((CRUDBooster::isSuperadmin() || CRUDBooster::myPrivilegeId() == 9 || CRUDBooster::myPrivilegeId() == 4) && (CRUDBooster::getCurrentMethod() == 'getEdit' || CRUDBooster::getCurrentMethod() == 'postEditSave')){
		    $this->form[] = array("label"=>"Status","name"=>"status","type"=>"select","dataenum"=>"ACTIVE;INACTIVE",'required'=>true, 'width'=>'col-sm-5');
		}
		
		if(CRUDBooster::myPrivilegeId() == 9){
			$this->form[] = array("label"=>"Privilege","name"=>"id_cms_privileges","type"=>"select","datatable"=>"cms_privileges,name","datatable_where"=>"name LIKE '%REQUESTOR' || name LIKE '%OIC' || name LIKE '%AP CHECKER' || name LIKE '%TREASURY'", 'width'=>'col-sm-5');				
			// $this->form[] = array("label"=>"Channel","name"=>"channels_id","type"=>"select","datatable"=>"channels,channel_name", 'width'=>'col-sm-5');
			// $this->form[] = array("label"=>"Store Name","name"=>"stores_id","type"=>"check-box","datatable"=>"stores,store_name", 'width'=>'col-sm-10' );
			//$this->form[] = array("label"=>"Stores","name"=>"stores_id","type"=>"select","datatable"=>"stores,name_name", 'required'=>true,'width'=>'col-sm-5');				
		}elseif(CRUDBooster::myPrivilegeId() == 4){
			$this->form[] = array("label"=>"Privilege","name"=>"id_cms_privileges","type"=>"select","datatable"=>"cms_privileges,name","datatable_where"=>"name LIKE '%REQUESTOR' || name LIKE '%OIC' || name LIKE '%EMPLOYEE' || name LIKE '%TREASURY'", 'width'=>'col-sm-5');				
			$this->form[] = array("label"=>"Employee Name","name"=>"employee_id","type"=>"select2","datatable"=>"employees,bill_to", 'width'=>'col-sm-5' );
			$this->form[] = array('label'=>'Approver','name'=>'approver_id','type'=>'check-box6','datatable'=>'cms_users,name','datatable_where'=>"id_cms_privileges = '3'",'width'=>'col-sm-10');
			
		}elseif(CRUDBooster::isSuperadmin()) {

			$this->form[] = array("label"=>"Privilege","name"=>"id_cms_privileges","type"=>"select","datatable"=>"cms_privileges,name", 'width'=>'col-sm-5');	
			$this->form[] = array('label'=>'Approver','name'=>'approver_id','type'=>'select2','datatable'=>'cms_users,name','datatable_where'=>"id_cms_privileges = '3' || id_cms_privileges = '10' || id_cms_privileges = '11'",'width'=>'col-sm-5');
			$this->form[] = array('label'=>'Approver','name'=>'approver_id_manager','type'=>'select2','datatable'=>'cms_users,name','datatable_where'=>"id_cms_privileges = '10'",'width'=>'col-sm-5', 'class'=>'approver_one');
			//$this->form[] = array('label'=>'Approver','name'=>'approver_id_executive','type'=>'select2','datatable'=>'cms_users,name','datatable_where'=>"id_cms_privileges = '12'",'width'=>'col-sm-5');
			$this->form[] = array("label"=>"Location","name"=>"location_id","type"=>"select2","datatable"=>"locations,store_name", 'datatable_where'=>"store_status = 'ACTIVE'",'width'=>'col-sm-5');
			//$this->form[] = array("label"=>"Stores","name"=>"store_id","type"=>"check-box","datatable"=>"stores,bea_mo_store_name", 'datatable_where'=>"status = 'ACTIVE'", 'width'=>'col-sm-10' );
            
		}

		
		# END FORM DO NOT REMOVE THIS LINE

		$this->button_selected = array();
        if(CRUDBooster::isUpdate() && (CRUDBooster::isSuperadmin() || CRUDBooster::myPrivilegeId() == 9 || CRUDBooster::myPrivilegeId() == 4))
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
				$this->index_button[] = ["label"=>"Export Lists","icon"=>"fa fa-files-o","url"=>CRUDBooster::mainpath('export'),"color"=>"primary"];
				$this->index_button[] = [
					"title"=>"Upload User Accounts",
					"label"=>"Upload User Accounts",
					"icon"=>"fa fa-download",
					"url"=>CRUDBooster::mainpath('user-account-upload')];
			}
		}

		$this->load_js[] = asset("js/employee_master.js");

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

			$('#department_id').select2();
			$('#sub_department_id').select2();

			let x = $(location).attr('pathname').split('/');
			let add_action = x.includes('add');
			let edit_action = x.includes('edit');


			if (add_action){
				$('#form-group-approver_id_manager').hide();
				$('#approver_id_manager').removeAttr('required');

				$('#form-group-approver_id_executive').hide();
				$('#approver_id_executive').removeAttr('required');

				$('#id_cms_privileges').change(function() {
					if($(this).val() == 3){
						$('#form-group-approver_id_manager').show();
						$('#approver_id_manager').attr('required', 'required');

						$('#form-group-approver_id').hide();
				        $('#approver_id').removeAttr('required');

						$('#form-group-approver_id_executive').hide();
						$('#approver_id_executive').removeAttr('required');

					}
					else if($(this).val() == 10){
						$('#form-group-approver_id_manager').hide();
						$('#approver_id_manager').removeAttr('required');

						$('#form-group-approver_id').hide();
				        $('#approver_id').removeAttr('required');

						$('#form-group-approver_id_executive').show();
						$('#approver_id_executive').attr('required', 'required');

	
					}else if($(this).val() == 11 || $(this).val() == 5){
						$('#form-group-approver_id_manager').hide();
						$('#approver_id_manager').removeAttr('required');

						$('#form-group-approver_id').hide();
				        $('#approver_id').removeAttr('required');

						$('#form-group-approver_id_executive').hide();
						$('#approver_id_executive').removeAttr('required');

					}else{
						$('#form-group-approver_id').show();
				        $('#approver_id').attr('required', 'required');

						$('#form-group-approver_id_manager').hide();
						$('#approver_id_manager').removeAttr('required');

						$('#form-group-approver_id_executive').hide();
						$('#approver_id_executive').removeAttr('required');
					}

				});

			}else if(edit_action){
				$('#form-group-approver_id_manager').hide();
				$('#approver_id_manager').removeAttr('required');

				$('#form-group-approver_id_executive').hide();
				$('#approver_id_executive').removeAttr('required');

				$('#id_cms_privileges').change(function() {
					if($(this).val() == 3){
						$('#form-group-approver_id_manager').show();
						$('#approver_id_manager').attr('required', 'required');

						$('#form-group-approver_id').hide();
				        $('#approver_id').removeAttr('required');

						$('#form-group-approver_id_executive').hide();
						$('#approver_id_executive').removeAttr('required');
					}
		
					else if($(this).val() == 10){
						$('#form-group-approver_id_manager').hide();
						$('#approver_id_manager').removeAttr('required');

						$('#form-group-approver_id').hide();
				        $('#approver_id').removeAttr('required');

						$('#form-group-approver_id_executive').show();
						$('#approver_id_executive').attr('required', 'required');

	
					}else if($(this).val() == 11 || $(this).val() == 5){
						$('#form-group-approver_id_manager').hide();
						$('#approver_id_manager').removeAttr('required');

						$('#form-group-approver_id').hide();
				        $('#approver_id').removeAttr('required');

						$('#form-group-approver_id_executive').hide();
						$('#approver_id_executive').removeAttr('required');

					}else{
						$('#form-group-approver_id').show();
				        $('#approver_id').attr('required', 'required');

						$('#form-group-approver_id_manager').hide();
						$('#approver_id_manager').removeAttr('required');

						$('#form-group-approver_id_executive').hide();
						$('#approver_id_executive').removeAttr('required');
					}

				});


				if($('#id_cms_privileges').val() == 3){
					$('#form-group-approver_id_manager').show();
					$('#approver_id_manager').attr('required', 'required');

					$('#form-group-approver_id').hide();
					$('#approver_id').removeAttr('required');

					$('#form-group-approver_id_executive').hide();
					$('#approver_id_executive').removeAttr('required');

				}else if($('#id_cms_privileges').val() == 10){
					$('#form-group-approver_id_manager').hide();
					$('#approver_id_manager').removeAttr('required');

					$('#form-group-approver_id').hide();
					$('#approver_id').removeAttr('required');

					$('#form-group-approver_id_executive').show();
					$('#approver_id_executive').attr('required', 'required');

				}else if($('#id_cms_privileges').val() == 11 || $('#id_cms_privileges').val() == 5){
					$('#form-group-approver_id_manager').hide();
					$('#approver_id_manager').removeAttr('required');

					$('#form-group-approver_id').hide();
					$('#approver_id').removeAttr('required');

					$('#form-group-approver_id_executive').hide();
					$('#approver_id_executive').removeAttr('required');

				}else{
					$('#form-group-approver_id').show();
				    $('#approver_id').attr('required', 'required');

					$('#form-group-approver_id_manager').hide();
					$('#approver_id_manager').removeAttr('required');

					$('#form-group-approver_id_executive').hide();
					$('#approver_id_executive').removeAttr('required');

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
		
		
        if($postdata['id_cms_privileges'] == 3){
			$postdata['approver_id'] = $postdata['approver_id_manager'];
			$postdata['approver_id_manager'] = $postdata['approver_id_manager'];
			$postdata['approver_id_executive'] = NULL;
		}else if($postdata['id_cms_privileges'] == 11){
			$postdata['approver_id'] = $postdata['approver_id_executive'];
			$postdata['approver_id_manager'] = NULL;
			$postdata['approver_id_executive'] = $postdata['approver_id_executive'];
		}else{
			$postdata['approver_id'] = $postdata['approver_id'];
			$postdata['approver_id_manager'] = NULL;
			$postdata['approver_id_executive'] = NULL;
		}

	}

	public function hook_after_add($id) {        
        //Your code here
		$details = Users::where(['created_by' => CRUDBooster::myId()])->orderBy('id','desc')->first();
		// if($details->approver_id){
			
		// 	    $checkRowDb = DB::table('approval_matrices')->select(DB::raw("approval_matrices.cms_users_id AS cms_users_id"))->get()->toArray();
		// 		$approval_string = $details->approver_id;
		// 		$approverlist = array_map('intval',explode(",",$approval_string));
		// 		$container=[];
		// 		$saveDatatoApprovalMatrix = [];
		// 		foreach($approverlist as $key => $data){
		// 		   $container['id_cms_privileges'] = 3;
		// 		   $container['cms_users_id'] = $approverlist[$key];
		// 		   $container['department_list'] = $details->id;
		// 		   $container['status'] = 'ACTIVE';
		// 		   $container['created_by'] = CRUDBooster::myId();
		// 		   $container['created_at'] = date('Y-m-d H:i:s');
		// 		   $saveDatatoApprovalMatrix[] = $container;
		// 		}

		// 		foreach($saveDatatoApprovalMatrix as $key=> $val){
		// 			if(count($checkRowDb) > 0){
		// 				if(in_array($val['cms_users_id'],array_column($checkRowDb,'cms_users_id'))){
		// 					$val['updated_by'] = CRUDBooster::myId();
		// 					$val['updated_at'] = date("Y-m-d H:i:s");
		// 					$arrUpdate[]= $val;
		// 				}
		// 				else{
		// 					$arrInsert[]=$val;
		// 				}
		// 			}
		// 			else{
		// 				$arrInsert[]=$val;
		// 			}
		
		// 		}
        //     if($arrInsert > 0){
		// 		ApprovalMatrix::insert($arrInsert);
		// 	}
		// 	if($arrUpdate > 0){
		// 		foreach($arrUpdate as $updateKey => $updateVal) {
		// 			ApprovalMatrix::where(['cms_users_id' => $updateVal['cms_users_id']])
		// 				->update([
		// 						'id_cms_privileges' => $updateVal['id_cms_privileges'], 
		// 						'cms_users_id' => $updateVal['cms_users_id'],
		// 						'department_list'=>DB::raw("CONCAT(department_list,',".$updateVal['department_list']."')"),
		// 						'updated_by' => $updateVal['updated_by'],
		// 						'updated_at' => $updateVal['updated_at']
		// 						]);
		// 		}  
		// 	}
		// }

       /* if(CRUDBooster::isSuperadmin()){
            return redirect()->action('AdminApprovalMatricesController@getIndex')->send();
			exit;
        }*/

    }

	public function hook_before_edit(&$postdata,$id) {        

            $postdata['name'] = $postdata['first_name'].' '.$postdata['last_name'];
    		$postdata['user_name'] = $postdata['last_name'].''.substr($postdata['first_name'], 0, 1);
    
			if($postdata['id_cms_privileges'] == 3){
				$postdata['approver_id'] = $postdata['approver_id_manager'];
				$postdata['approver_id_manager'] = $postdata['approver_id_manager'];
				$postdata['approver_id_executive'] = NULL;
			}else if($postdata['id_cms_privileges'] == 11){
				$postdata['approver_id'] = $postdata['approver_id_executive'];
				$postdata['approver_id_manager'] = NULL;
				$postdata['approver_id_executive'] = $postdata['approver_id_executive'];
			}else{
				$postdata['approver_id'] = $postdata['approver_id'];
				$postdata['approver_id_manager'] = NULL;
				$postdata['approver_id_executive'] = NULL;
			}

    	    $postdata['updated_by']=CRUDBooster::myId();
    	    $postdata['id']=$id;
  
    }

	public function hook_after_edit($id) {
		$details = Users::where(['id' => $id])->orderBy('id','desc')->first();
		if($details->approver_id){
			
			    $checkRowDb = DB::table('approval_matrices')->select(DB::raw("approval_matrices.cms_users_id AS cms_users_id"))->get()->toArray();
				$approval_string = $details->approver_id;
				$approverlist = array_map('intval',explode(",",$approval_string));
				$container=[];
				$saveDatatoApprovalMatrix = [];
				foreach($approverlist as $key => $data){
				   $container['id_cms_privileges'] = 3;
				   $container['cms_users_id'] = $approverlist[$key];
				   $container['department_list'] = $details->id;
				   $container['status'] = 'ACTIVE';
				   $container['created_by'] = CRUDBooster::myId();
				   $container['created_at'] = date('Y-m-d H:i:s');
				   $saveDatatoApprovalMatrix[] = $container;
				}

				foreach($saveDatatoApprovalMatrix as $key=> $val){
					if(count($checkRowDb) > 0){
						if(in_array($val['cms_users_id'],array_column($checkRowDb,'cms_users_id'))){
							$val['updated_by'] = CRUDBooster::myId();
							$val['updated_at'] = date("Y-m-d H:i:s");
							$arrUpdate[]= $val;
						}
						else{
							$arrInsert[]=$val;
						}
					}
					else{
						$arrInsert[]=$val;
					}
		
				}
            if($arrInsert > 0){
				ApprovalMatrix::insert($arrInsert);
			}
			if($arrUpdate > 0){
				foreach($arrUpdate as $updateKey => $updateVal) {
					ApprovalMatrix::where(['cms_users_id' => $updateVal['cms_users_id']])
						->update([
								'id_cms_privileges' => $updateVal['id_cms_privileges'], 
								'cms_users_id' => $updateVal['cms_users_id'],
								'department_list'=>DB::raw("CONCAT(department_list,',".$updateVal['department_list']."')"),
								'updated_by' => $updateVal['updated_by'],
								'updated_at' => $updateVal['updated_at']
								]);
				}  
			}
		}
	}
    

    public function hook_after_delete($id) {
		//Your code here
		DB::table('cms_users')->where('id', $id)->update(['status' => 'INACTIVE']);
	}

    public function hook_query_index(&$query) {
        //Your code here
        if(!CRUDBooster::isSuperadmin()) {
        	if(CRUDBooster::myPrivilegeName() == 'Admin' || CRUDBooster::myPrivilegeId() == 4){
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

	public function userListing($ids) {
		$users = explode(",", $ids);
		return Users::whereIn('id', $users)->pluck('name');
	}
	
	public function uploadUserAccountTemplate() {
		// Excel::create('user-account-upload-'.date("Ymd").'-'.date("h.i.sa"), function ($excel) {
		// 	$excel->sheet('useraccount', function ($sheet) {
		// 		$sheet->row(1, array('FIRST NAME', 'LAST NAME', 'EMAIL', 'PRIVILEGE', 'CHANNEL', 'STORES ID'));
		// 		$sheet->row(2, array('John', 'Doe', 'johndoe@digits.ph','Requestor','Retail','1'));
		// 	});
		// })->download('csv');
		$filename = "user-account-upload".date("Ymd")."-".date("h.i.sa"). ".csv";
	
			header("Content-Disposition: attachment; filename=\"$filename\"");
			header("Content-Type: text/csv; charset=UTF-16LE");
	
			$out = fopen("php://output", 'w');
			$flag = false;

			if(!$flag) {
				// display field/column names as first row
				fputcsv($out, array('EMAIL', 'PRIVILEGE', 'FIRST NAME', 'LAST NAME', 'DEPARTMENT', 'SUB DEPARTMENT', 'POSITION', 'APPROVER', 'LOCATION'));
				$flag = true;
			}
			
			fputcsv($out, array('johndoe@digits.ph', 'Employee', 'John', 'Doe', 'BPG', 'BPG-SYSTEM', 'Associate Software Developer' , 'Mike Rodelas', 'DIGITS HEAD OFFICE'));
			fclose($out);
			
			exit;
	}

	public function uploadUserAccount() {
	    // if(!CRUDBooster::isSuperadmin()) {    
		// 	CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
		// }
		$data['page_title']= 'User Account Upload';
		return view('user-account.user_account_upload', $data)->render();
	}

	public function userAccountUpload(Request $request) {
		$path_excel = $request->file('import_file')->store('temp');
		$path = storage_path('app').'/'.$path_excel;
		$headings = array_filter((new HeadingRowImport)->toArray($path)[0][0]);

		if (count($headings) !== 9) {
			CRUDBooster::redirect(CRUDBooster::adminpath('users'), 'Template column not match, please refer to downloaded template.', 'danger');
		} else {
			$is_diff = array_diff([ "email", "privilege", "first_name","last_name",
			"department", "sub_department", "position", "approver", "location"], $headings);

			if (count($is_diff) > 0) {
				CRUDBooster::redirect(CRUDBooster::adminpath('users'), 'Invalid Column Field, please refer to downloaded template.', 'danger');
			} else {
				try {
					Excel::import(new UserImport, $path);	
					CRUDBooster::redirect(CRUDBooster::adminpath('users'), 'Import Successfully!', 'success');
				} catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
					$failures = $e->failures();
					
					$error = [];
					foreach ($failures as $failure) {
						$line = $failure->row();
						foreach ($failure->errors() as $err) {
							$error[] = $err . " on line: " . $line; 
						}
					}
					
					$errors = collect($error)->unique()->toArray();
			
				}
				CRUDBooster::redirect(CRUDBooster::adminpath('users'), $errors[0], 'danger');
	       }
		}
	}

	public function getExport(){
		return Excel::download(new ExportUsersList, 'TAM-UsersList.xlsx');
	}
	
}
