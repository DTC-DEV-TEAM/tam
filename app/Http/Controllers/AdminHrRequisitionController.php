<?php namespace App\Http\Controllers;

	use Session;
	use Request;
	use DB;
	use CRUDBooster;
	use App\Users;
	use App\Models\ErfHeaderRequest;
	use App\Models\ErfBodyRequest;
	use App\ApprovalMatrix;
	use App\StatusMatrix;
	use App\Models\ErfHeaderDocuments;
	use App\Models\Applicant;
	use Illuminate\Support\Facades\Response;
	use Illuminate\Contracts\Encryption\DecryptException;
	use Illuminate\Support\Facades\Crypt;

	class AdminHrRequisitionController extends \crocodicstudio\crudbooster\controllers\CBController {

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "employee_name";
			$this->limit = "20";
			$this->orderby = "id,desc";
			$this->global_privilege = false;
			$this->button_table_action = true;
			$this->button_bulk_action = false;
			$this->button_action_style = "button_icon";
			$this->button_add = false;
			$this->button_edit = false;
			$this->button_delete = false;
			$this->button_detail = true;
			$this->button_show = true;
			$this->button_filter = true;
			$this->button_import = false;
			$this->button_export = false;
			$this->table = "erf_header_request";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Status","name"=>"status_id","join"=>"statuses,status_description"];
			$this->col[] = ["label"=>"Reference Number","name"=>"reference_number"];
			$this->col[] = ["label"=>"Company Name","name"=>"company"];
			$this->col[] = ["label"=>"Department","name"=>"department","join"=>"departments,department_name"];
			$this->col[] = ["label"=>"Position","name"=>"position"];
			$this->col[] = ["label"=>"Work Location","name"=>"work_location"];
			$this->col[] = ["label"=>"Requested Date","name"=>"date_requested"];
			$this->col[] = ["label"=>"Date Needed","name"=>"date_needed"];
			$this->col[] = ["label"=>"Requested By","name"=>"created_by","join"=>"cms_users,name"];
			
		
			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];

			# END FORM DO NOT REMOVE THIS LINE

			/* 
	        | ---------------------------------------------------------------------- 
	        | Sub Module
	        | ----------------------------------------------------------------------     
			| @label          = Label of action 
			| @path           = Path of sub module
			| @foreign_key 	  = foreign key of sub table/module
			| @button_color   = Bootstrap Class (primary,success,warning,danger)
			| @button_icon    = Font Awesome Class  
			| @parent_columns = Sparate with comma, e.g : name,created_at
	        | 
	        */
	        $this->sub_module = array();


	        /* 
	        | ---------------------------------------------------------------------- 
	        | Add More Action Button / Menu
	        | ----------------------------------------------------------------------     
	        | @label       = Label of action 
	        | @url         = Target URL, you can use field alias. e.g : [id], [name], [title], etc
	        | @icon        = Font awesome class icon. e.g : fa fa-bars
	        | @color 	   = Default is primary. (primary, warning, succecss, info)     
	        | @showIf 	   = If condition when action show. Use field alias. e.g : [id] == 1
	        | 
	        */
	        $this->addaction = array();
			if(CRUDBooster::isUpdate()) {

				$pending           = 1;
				$this->addaction[] = ['title'=>'Cancel Request','url'=>CRUDBooster::mainpath('getRequestCancel/[id]'),'icon'=>'fa fa-times', "showIf"=>"[status_id] == $pending"];
			}
	        /* 
	        | ---------------------------------------------------------------------- 
	        | Add More Button Selected
	        | ----------------------------------------------------------------------     
	        | @label       = Label of action 
	        | @icon 	   = Icon from fontawesome
	        | @name 	   = Name of button 
	        | Then about the action, you should code at actionButtonSelected method 
	        | 
	        */
	        $this->button_selected = array();

	                
	        /* 
	        | ---------------------------------------------------------------------- 
	        | Add alert message to this module at overheader
	        | ----------------------------------------------------------------------     
	        | @message = Text of message 
	        | @type    = warning,success,danger,info        
	        | 
	        */
	        $this->alert        = array();
	                

	        
	        /* 
	        | ---------------------------------------------------------------------- 
	        | Add more button to header button 
	        | ----------------------------------------------------------------------     
	        | @label = Name of button 
	        | @url   = URL Target
	        | @icon  = Icon from Awesome.
	        | 
	        */
	        $this->index_button = array();
			if(CRUDBooster::getCurrentMethod() == 'getIndex'){

				$this->index_button[] = ["label"=>"Create ERF","icon"=>"fa fa-files-o","url"=>CRUDBooster::mainpath('add-it-requisition'),"color"=>"success"];

				// $this->index_button[] = ["label"=>"FA Request","icon"=>"fa fa-files-o","url"=>CRUDBooster::mainpath('add-requisition-fa'),"color"=>"success"];
				// $this->index_button[] = ["label"=>"Marketing Request","icon"=>"fa fa-files-o","url"=>CRUDBooster::mainpath('add-requisition-marketing'),"color"=>"success"];
				// $this->index_button[] = ["label"=>"Supplies Request","icon"=>"fa fa-files-o","url"=>CRUDBooster::mainpath('add-requisition-supplies'),"color"=>"success"];

			}



	        /* 
	        | ---------------------------------------------------------------------- 
	        | Customize Table Row Color
	        | ----------------------------------------------------------------------     
	        | @condition = If condition. You may use field alias. E.g : [id] == 1
	        | @color = Default is none. You can use bootstrap success,info,warning,danger,primary.        
	        | 
	        */
	        $this->table_row_color = array();     	          

	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | You may use this bellow array to add statistic at dashboard 
	        | ---------------------------------------------------------------------- 
	        | @label, @count, @icon, @color 
	        |
	        */
	        $this->index_statistic = array();



	        /*
	        | ---------------------------------------------------------------------- 
	        | Add javascript at body 
	        | ---------------------------------------------------------------------- 
	        | javascript code in the variable 
	        | $this->script_js = "function() { ... }";
	        |
	        */
	        $this->script_js = "
			$('.fa.fa-times').click(function(){
				var strconfirm = confirm('Are you sure you want to cancel this request?');
				if (strconfirm == true) {
					return true;
				}else{
					return false;
					window.stop();
				}

			});
			";


            /*
	        | ---------------------------------------------------------------------- 
	        | Include HTML Code before index table 
	        | ---------------------------------------------------------------------- 
	        | html code to display it before index table
	        | $this->pre_index_html = "<p>test</p>";
	        |
	        */
	        $this->pre_index_html = null;
	        
	        
	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | Include HTML Code after index table 
	        | ---------------------------------------------------------------------- 
	        | html code to display it after index table
	        | $this->post_index_html = "<p>test</p>";
	        |
	        */
	        $this->post_index_html = null;
	        
	        
	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | Include Javascript File 
	        | ---------------------------------------------------------------------- 
	        | URL of your javascript each array 
	        | $this->load_js[] = asset("myfile.js");
	        |
	        */
	        $this->load_js = array();
	        $this->load_js[] = asset("datetimepicker/bootstrap-datetimepicker.min.js");
	        
	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | Add css style at body 
	        | ---------------------------------------------------------------------- 
	        | css code in the variable 
	        | $this->style_css = ".style{....}";
	        |
	        */
	        $this->style_css = NULL;
	        
	        
	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | Include css File 
	        | ---------------------------------------------------------------------- 
	        | URL of your css each array 
	        | $this->load_css[] = asset("myfile.css");
	        |
	        */
	        $this->load_css = array();
			$this->load_css[] = asset("datetimepicker/bootstrap-datetimepicker.min.css");
			$this->load_css[] = asset("css/font-family.css");
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
	            
	    }


	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate query of index result 
	    | ---------------------------------------------------------------------- 
	    | @query = current sql query 
	    |
	    */
	    public function hook_query_index(&$query) {
	        if(CRUDBooster::isSuperadmin()){
				$query->whereNull('erf_header_request.deleted_at')
					  ->orderBy('erf_header_request.status_id', 'ASC')
					  ->orderBy('erf_header_request.id', 'DESC');

			}else{
				$user = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
				$query->where(function($sub_query){
					$user = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();

					$sub_query->where('erf_header_request.created_by', CRUDBooster::myId())
							  ->whereNull('erf_header_request.deleted_at'); 

				});

				$query->orderBy('erf_header_request.status_id', 'asc')->orderBy('erf_header_request.id', 'DESC');
			}
	            
	    }

	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate row of index table html 
	    | ---------------------------------------------------------------------- 
	    |
	    */    
	    public function hook_row_index($column_index,&$column_value) {	
			$cancelled        =  DB::table('statuses')->where('id', 8)->value('status_description');   
			$pending          =  DB::table('statuses')->where('id', 1)->value('status_description');  
			$rejected         =  DB::table('statuses')->where('id', 5)->value('status_description');  
			$for_verification =  DB::table('statuses')->where('id', 29)->value('status_description');  
			$verified         =  DB::table('statuses')->where('id', 30)->value('status_description');  
			$jo_done          =  DB::table('statuses')->where('id', 31)->value('status_description');    
			$onboarding       =  DB::table('statuses')->where('id', 33)->value('status_description');   
			$closed           =  DB::table('statuses')->where('id', 13)->value('status_description'); 
			if($column_index == 1){
				if($column_value == $pending){
					$column_value = '<span class="label label-warning">'.$pending.'</span>';
				}else if($column_value == $rejected){
					$column_value = '<span class="label label-danger">'.$rejected.'</span>';
				}else if($column_value == $for_verification){
					$column_value = '<span class="label label-warning">'.$for_verification.'</span>';
				}else if($column_value == $verified){
					$column_value = '<span class="label label-info">'.$verified.'</span>';
				}else if($column_value == $jo_done){
					$column_value = '<span class="label label-info">'.$jo_done.'</span>';
				}else if($column_value == $onboarding){
					$column_value = '<span class="label label-info">'.$onboarding.'</span>';
				}else if($column_value == $cancelled){
					$column_value = '<span class="label label-danger">'.$cancelled.'</span>';
				}else if($column_value == $closed){
					$column_value = '<span class="label label-success">'.$closed.'</span>';
				}
			}
	    }

	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate data input before add data is execute
	    | ---------------------------------------------------------------------- 
	    | @arr
	    |
	    */
	    public function hook_before_add(&$postdata) {        
			$fields = Request::all();
			$data['user']       = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
			$company                   = $fields['company'];
			$department                = $fields['department'];
			$date_needed               = $fields['date_needed'];
			$position                  = $fields['position'];
			$work_location             = $fields['work_location'];
			$salary_range              = explode("-",$fields['salary_range']);
			$schedule                  = $fields['schedule'];
			$other_schedule            = $fields['other_schedule'];
			$allow_wfh                 = $fields['allow_wfh'];
			$manpower                  = $fields['manpower'];
			$replacement_of            = $fields['replacement_of'];
			$absorption                = $fields['absorption'];
			$manpower_type             = $fields['manpower_type'];
			$required_exams            = $fields['required_exams'];
			$other_required_exams      = $fields['other_required_exams'];
			$qualifications            = $fields['qualifications'];
			$job_descriptions          = $fields['job_descriptions'];
			$quantity_total 	       = $fields['quantity_total'];
			$request_type_id 	       = $fields['request_type_id'];
			$application 		       = $fields['application'];
			$application_others        = $fields['application_others'];
			$shared_files              = $fields['shared_files'];
			$employee_interaction      = $fields['employee_interaction'];
			$asset_usage               = $fields['asset_usage'];
			$email_domain              = $fields['email_domain'];
			$other_email_domain        = $fields['other_email'];
			$required_system           = $fields['required_system'];
			$count_header              = DB::table('erf_header_request')->count();
			$header_ref                = str_pad($count_header + 1, 7, '0', STR_PAD_LEFT);			
			$reference_number	       = "ERF-".$header_ref;
			$category_id 		       = $fields['category_id'];
			//dd($fields);
			$postdata['reference_number']		 	= $reference_number;
			if(in_array(CRUDBooster::myPrivilegeId(), [11,12,14,15])){ 
			    $postdata['status_id']                          = 29;
				$postdata['approved_immediate_head_by']         = CRUDBooster::myId();
				$postdata['approved_immediate_head_at']         = date('Y-m-d H:i:s');
			}else{
				$postdata['status_id']              = 1;
			}
			$postdata['company'] 				    = $company;
			$postdata['date_requested'] 	        = date('Y-m-d');
			$postdata['department'] 				= $department;
			$postdata['position'] 					= $position;
			$postdata['date_needed'] 			    = date('Y-m-d', strtotime($date_needed));
			$postdata['work_location'] 				= $work_location;
			$postdata['salary_range_from'] 			= Crypt::encryptString(str_replace(',', '', $salary_range[0]));
			$postdata['salary_range_to'] 			= Crypt::encryptString(str_replace(',', '', $salary_range[1]));

			if(!empty($schedule)){
				$postdata['schedule'] 				= $schedule;
				$postdata['other_schedule'] 	    = $other_schedule;
			}

			$postdata['allow_wfh'] 		            = $allow_wfh;
			$postdata['manpower'] 		            = $manpower;
			$postdata['replacement_of'] 		    = $replacement_of;
			$postdata['absorption'] 		        = $absorption;
			$postdata['manpower_type'] 		        = $manpower_type;

			if(!empty($required_exams)){
				$postdata['other_required_exams'] 	= $other_required_exams;
				$postdata['required_exams'] 	    = implode(", ",$required_exams);
			}

			$postdata['qualifications'] 		    = $qualifications;
			$postdata['job_description'] 		    = $job_descriptions;
			$postdata['quantity_total'] 			= $quantity_total;
			$postdata['shared_files'] 		        = $shared_files;
			if(!empty($employee_interaction)){
				$postdata['employee_interaction'] 	    = implode(", ",$employee_interaction);
			}
			if(!empty($asset_usage)){
				$postdata['asset_usage'] 	        = implode(", ",$asset_usage);
			}
			if(!empty($required_system)){
				$postdata['required_system'] 	    = implode(", ",$required_system);
			}
			$postdata['email_domain'] 		        = $email_domain;
			$postdata['other_email_domain'] 		= $other_email_domain;
			$postdata['created_by'] 				= CRUDBooster::myId();
			$postdata['created_at'] 				= date('Y-m-d H:i:s');
			$postdata['request_type_id'] 		    = NULL;
			if(!empty($application)){
				$postdata['application'] 				= implode(", ",$application);
				$postdata['application_others'] 		= $application_others;
			}

	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command after add public static function called 
	    | ---------------------------------------------------------------------- 
	    | @id = last insert id
	    | 
	    */
	    public function hook_after_add($id) {        
	        $fields = Request::all();
			$dataLines = array();
			$erf_header = DB::table('erf_header_request')->where(['created_by' => CRUDBooster::myId()])->orderBy('id','desc')->first();
			$digits_code 	    = $fields['digits_code'];
			$item_description 	= $fields['item_description'];
			$category_id 		= $fields['category_id'];
			$sub_category_id 	= $fields['sub_category_id'];
			$app_id_others 		= $fields['app_id_others'];
			$quantity 			= $fields['quantity'];
			$request_type_id 	= $fields['request_type_id'];
			$app_count = 2;

			$files 	= $fields['documents'];
			$documents = [];
			if (!empty($files)) {
				$counter = 0;
				foreach($files as $file){
					$counter++;
					$name = $erf_header->reference_number . '-' . $file->getClientOriginalName();
					$filename = $name;
					$file->move('vendor/crudbooster/erf_folder',$filename);
					$documents[]= $filename;

					$header_documents = new ErfHeaderDocuments;
					$header_documents->header_id 		    = $erf_header->id;
					$header_documents->file_name 		    = $filename;
					$header_documents->ext 		            = $file->getClientOriginalExtension();
					$header_documents->created_by 		    = CRUDBooster::myId();
					$header_documents->save();
				}
			}

			for($x=0; $x < count((array)$item_description); $x++) {		
				$dataLines[$x]['header_request_id'] = $erf_header->id;
				$dataLines[$x]['digits_code'] 	    = $digits_code[$x];
				$dataLines[$x]['item_description'] 	= $item_description[$x];
				$dataLines[$x]['category_id'] 		= $category_id[$x];
				$dataLines[$x]['sub_category_id'] 	= $sub_category_id[$x];
				$dataLines[$x]['quantity'] 			= $quantity[$x];
				$dataLines[$x]['created_at'] 		= date('Y-m-d H:i:s');
				if($category_id[$x] == "IT ASSETS"){
					$dataLines[$x]['request_type_id'] = 1;
					
				}else if($category_id[$x] == "FIXED ASSETS"){
					$dataLines[$x]['request_type_id'] = 5;
				}else{
					$dataLines[$x]['request_type_id'] = 7;
				}
			}

			DB::beginTransaction();
			try {
				ErfBodyRequest::insert($dataLines);
				DB::commit();
				//CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.alert_pullout_data_success",['mps_reference'=>$pullout_header->reference]), 'success');
			} catch (\Exception $e) {
				DB::rollback();
				CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.alert_database_error",['database_error'=>$e]), 'danger');
			}

			CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.alert_add_success",['reference_number'=>$arf_header->reference_number]), 'success');

			

	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate data input before update data is execute
	    | ---------------------------------------------------------------------- 
	    | @postdata = input post data 
	    | @id       = current id 
	    | 
	    */
	    public function hook_before_edit(&$postdata,$id) {        
	        //Your code here

	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command after edit public static function called
	    | ----------------------------------------------------------------------     
	    | @id       = current id 
	    | 
	    */
	    public function hook_after_edit($id) {
	        //Your code here 

	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command before delete public static function called
	    | ----------------------------------------------------------------------     
	    | @id       = current id 
	    | 
	    */
	    public function hook_before_delete($id) {
	        //Your code here

	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command after delete public static function called
	    | ----------------------------------------------------------------------     
	    | @id       = current id 
	    | 
	    */
	    public function hook_after_delete($id) {
	        //Your code here

	    }
 
        public function getAddItRequisition() {

			if(!CRUDBooster::isCreate() && $this->global_privilege == false) {
				CRUDBooster::redirect(CRUDBooster::adminPath(), trans('crudbooster.denied_access'));
			}

			$this->cbLoader();
			$data['page_title'] = 'Create Employee Requisition Form';
			$data['conditions'] = DB::table('condition_type')->where('status', 'ACTIVE')->get();
			$data['stores'] = DB::table('stores')->where('status', 'ACTIVE')->get();
			
			$data['user'] = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
			$data['employeeinfos'] = DB::table('cms_users')
										 ->leftjoin('positions', 'cms_users.position_id', '=', 'positions.id')
										 ->leftjoin('departments', 'cms_users.department_id', '=', 'departments.id')
										 ->select( 'cms_users.*', 'positions.position_description as position_description', 'departments.department_name as department_name')
										 ->where('cms_users.id', $data['user']->id)->first();
			$departmentList = array_map('intval',explode(",",$data['employeeinfos']->department_id));
			$data['departments'] = DB::table('departments')->whereIn('id',$departmentList)->where('status', 'ACTIVE')->get();
	
			$data['categories'] = DB::table('category')->where('category_status', 'ACTIVE')->whereIn('id', [5,1,2])->orderby('category_description', 'asc')->get();
			$data['sub_categories'] = DB::table('class')->where('class_status', 'ACTIVE')->where('category_id', 5)->orderby('class_description', 'asc')->get();
			$data['applications'] = DB::table('applications')->where('status', 'ACTIVE')->orderby('app_name', 'asc')->get();
			$data['companies'] = DB::table('companies')->where('status', 'ACTIVE')->get();

			$data['purposes'] = DB::table('request_type')->where('status', 'ACTIVE')->where('privilege', 'Employee')->get();
			$data['stores'] = DB::table('locations')->where('id', $data['user']->location_id)->first();
			$data['purposes'] = DB::table('request_type')->where('status', 'ACTIVE')->where('privilege', 'HR')->get();
			//$data['new_employee'] = Users::where('new_employee_plug','=',1)->get();
			//sub masterfile
			$data['schedule'] = DB::table('sub_masterfile_schedule')->where('status', 'ACTIVE')->get();
			$data['allow_wfh'] = DB::table('sub_masterfile_allow_wfh')->where('status', 'ACTIVE')->get();
			$data['manpower'] = DB::table('sub_masterfile_manpower')->where('status', 'ACTIVE')->get();
			$data['manpower_type'] = DB::table('sub_masterfile_manpower_type')->where('status', 'ACTIVE')->get();
			$data['required_exams'] = DB::table('sub_masterfile_required_exams')->where('status', 'ACTIVE')->get();
			$data['asset_usage'] = DB::table('sub_masterfile_asset_usage')->where('status', 'ACTIVE')->get();
			$data['shared_files'] = DB::table('sub_masterfile_shared_files')->where('status', 'ACTIVE')->get();
			$data['interact_with'] = DB::table('sub_masterfile_interact_with')->where('status', 'ACTIVE')->get();
			$data['email_domain'] = DB::table('sub_masterfile_email_domain')->where('status', 'ACTIVE')->get();
			$data['required_system'] = DB::table('sub_masterfile_required_system')->where('status', 'ACTIVE')->get();
			$data['positions'] = DB::table('positions')->where('department_id','LIKE', '%'.$data['employeeinfos']->department_id.'%')->where('status', 'ACTIVE')->get();
			return $this->view("erf.add-hr-requisition", $data);
				
		}

		public function getDetail($id){
			
			$this->cbLoader();
            if(!CRUDBooster::isRead() && $this->global_privilege==FALSE) {    
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

			$data = array();

			$data['page_title'] = 'View Erf Details';

			$data['Header'] = ErfHeaderRequest::
				leftjoin('companies', 'erf_header_request.company', '=', 'companies.id')
				->leftjoin('departments', 'erf_header_request.department', '=', 'departments.id')
				->leftjoin('cms_users as approver', 'erf_header_request.approved_immediate_head_by', '=', 'approver.id')
				->leftjoin('cms_users as verifier', 'erf_header_request.approved_hr_by', '=', 'verifier.id')
				->leftJoin('applicant_table', function($join) 
				{
					$join->on('erf_header_request.reference_number', '=', 'applicant_table.erf_number')
					->where('applicant_table.status',31);
				})
				->select(
						'erf_header_request.*',
						'approver.name as approved_head_by',
						'verifier.name as verified_by',
						'departments.department_name as department',
						'applicant_table.*'
						)
				->where('erf_header_request.id', $id)->first();
		
			$res_req = explode(",",$data['Header']->required_exams);
			$interact_with = explode(",",$data['Header']->employee_interaction);
			$asset_usage = explode(",",$data['Header']->asset_usage);
			$application = explode(",",$data['Header']->application);
			$required_system = explode(",",$data['Header']->required_system);
			$data['res_req'] = array_map('trim', $res_req);
			$data['interaction'] = array_map('trim', $interact_with);
			$data['asset_usage_array'] = array_map('trim', $asset_usage);
			$data['application'] = $application;
			$data['required_system_array'] = array_map('trim', $required_system);
			$data['Body'] = ErfBodyRequest::
				select(
				  'erf_body_request.*'
				)
				->where('erf_body_request.header_request_id', $id)
				->get();
			$data['erf_header_documents'] = ErfHeaderDocuments::select(
					'erf_header_documents.*'
				  )
				  ->where('erf_header_documents.header_id', $id)
				  ->get();
			$data['applicants'] = Applicant::leftjoin('statuses', 'applicant_table.status', '=', 'statuses.id')
			        ->select(
					'applicant_table.*',
					'statuses.status_description',
					'statuses.id as status_id',
					)
					->where('applicant_table.erf_number', $data['Header']->reference_number)
					->get();
					
			$data['schedule'] = DB::table('sub_masterfile_schedule')->where('status', 'ACTIVE')->get();
			$data['allow_wfh'] = DB::table('sub_masterfile_allow_wfh')->where('status', 'ACTIVE')->get();
			$data['manpower'] = DB::table('sub_masterfile_manpower')->where('status', 'ACTIVE')->get();
			$data['manpower_type'] = DB::table('sub_masterfile_manpower_type')->where('status', 'ACTIVE')->get();
			$data['required_exams'] = DB::table('sub_masterfile_required_exams')->where('status', 'ACTIVE')->get();
			$data['asset_usage'] = DB::table('sub_masterfile_asset_usage')->where('status', 'ACTIVE')->get();
			$data['shared_files'] = DB::table('sub_masterfile_shared_files')->where('status', 'ACTIVE')->get();
			$data['interact_with'] = DB::table('sub_masterfile_interact_with')->where('status', 'ACTIVE')->get();
			$data['email_domain'] = DB::table('sub_masterfile_email_domain')->where('status', 'ACTIVE')->get();
			$data['required_system'] = DB::table('sub_masterfile_required_system')->where('status', 'ACTIVE')->get();
			return $this->view("erf.erf_details", $data);
		}

		public function SearchUser(Request $request) {
			$request = Request::all();
			$search 		= $request['id'];

			$data = array();
			$data['status_no'] = 0;
			$data['message']   ='No Item Found!';
			$data['items'] = array();
			$items = DB::table('cms_users')
				->where('cms_users.id','=',$search)
				->leftjoin('departments', 'cms_users.department_id','=','departments.id')
				->leftjoin('sub_department', 'cms_users.sub_department_id','=','sub_department.id')
				->leftjoin('locations', 'cms_users.location_id', '=', 'locations.id')
				->select(	'cms_users.*',
				            'cms_users.id as id',
							'departments.*',
							'sub_department.*',
							'locations.*'
						)
				->first();
			$data['items'] = $items;

			echo json_encode($data);
			exit;  
		}

		public function getDownload($id) {
			$getFile = DB::table('erf_header_documents')->where('id',$id)->first();
			$file= public_path(). "/vendor/crudbooster/erf_folder/".$getFile->file_name;

			$headers = array(
					'Content-Type: application/pdf',
					);

			return Response::download($file, $getFile->file_name, $headers);
		}

		public function getRequestCancel($id) {
			erfHeaderRequest::where('id',$id)
			->update([
					'status_id'=> 8,
					'cancelled_by'=> CRUDBooster::myId(),
					'cancelled_at'=> date('Y-m-d H:i:s')	
			]);	
			CRUDBooster::redirect(CRUDBooster::mainpath(), trans("Request has been cancelled successfully!"), 'info');
		}

		public function itemErfITSearch(Request $request) {
			$request = Request::all();
			$cont = (new static)->apiContext;
			$search 		= $request['search'];
			$data = array();

			$data['status_no'] = 0;
			$data['message']   ='No Item Found!';
			$data['items'] = array();

			//$search_item =  DB::table('digits_code')>where('digits_code','LIKE','%'.$request->search.'%')->first();

			$items = DB::table('assets')
			->where('assets.digits_code','LIKE','%'.$search.'%')->where('assets.category_id','=',6)->whereNotIn('assets.status',['EOL-DIGITS','INACTIVE'])->whereNotIn('assets.category_id',[3,5])
			->orWhere('assets.item_description','LIKE','%'.$search.'%')->where('assets.category_id','=',6)->whereNotIn('assets.status',['EOL-DIGITS','INACTIVE'])->whereNotIn('assets.category_id',[3,5])
			->join('category', 'assets.category_id','=', 'category.id')
			->leftjoin('new_sub_category', 'assets.sub_category_id','=', 'new_sub_category.id')
			->leftjoin('class','assets.class_id','class.id')
			->select(
				'assets.*',
				'assets.id as assetID',
				'category.category_description as category_description',
				'new_sub_category.sub_category_description as sub_category_description',
				'class.class_description as class_type'
			)->take(10)->get();
			
			if($items){
				$data['status'] = 1;
				$data['problem']  = 1;
				$data['status_no'] = 1;
				$data['message']   ='Item Found';
				$i = 0;
				foreach ($items as $key => $value) {

					$return_data[$i]['id']                   = $value->assetID;
					$return_data[$i]['asset_code']           = $value->asset_code;
					$return_data[$i]['digits_code']          = $value->digits_code;
					$return_data[$i]['asset_tag']            = $value->asset_tag;
					$return_data[$i]['serial_no']            = $value->serial_no;
					$return_data[$i]['item_description']     = $value->item_description;
					$return_data[$i]['category_description'] = $value->category_description;
					$return_data[$i]['class_description']    = $value->sub_category_description;
					$return_data[$i]['class_type']           = $value->class_type;
					$return_data[$i]['item_cost']            = $value->item_cost;
					$return_data[$i]['item_type']            = $value->item_type;
					$return_data[$i]['image']                = $value->image;
					$return_data[$i]['quantity']             = $value->quantity;
					$return_data[$i]['total_quantity']       = $value->total_quantity;

					$i++;

				}
				$data['items'] = $return_data;
			}

			echo json_encode($data);
			exit;  
		}

		public function positions(Request $request){
			$data = Request::all();	
			$id = $data['id'];
			$positions = DB::table('positions')->select('positions.*')->where('department_id', $id)->where('status', "ACTIVE")->orderby('position_description', 'ASC')->get();
			return($positions);
		}

	}