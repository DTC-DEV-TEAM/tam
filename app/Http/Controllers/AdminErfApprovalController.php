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
	use Illuminate\Support\Facades\Response;
	use App\HeaderRequest;
	use App\BodyRequest;
	use Illuminate\Support\Facades\Crypt;

	class AdminErfApprovalController extends \crocodicstudio\crudbooster\controllers\CBController {

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "id";
			$this->limit = "20";
			$this->orderby = "id,desc";
			$this->global_privilege = false;
			$this->button_table_action = true;
			$this->button_bulk_action = false;
			$this->button_action_style = "button_icon";
			$this->button_add = false;
			$this->button_edit = true;
			$this->button_delete = false;
			$this->button_detail = false;
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
	        $this->script_js = NULL;


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
	         //Your code here
			if(CRUDBooster::isSuperadmin()){

				$pending  = 1;
				$query->orderBy('erf_header_request.status_id', 'DESC')->where('erf_header_request.status_id', $pending)->orderBy('erf_header_request.id', 'DESC');
			
			}else{
				$pending  = 1;
				$approvalMatrix = Users::where('cms_users.approver_id', CRUDBooster::myId())->get();
				$approval_array = array();
				foreach($approvalMatrix as $matrix){
				    array_push($approval_array, $matrix->id);
				}
				$approval_string = implode(",",$approval_array);
				$userslist = array_map('intval',explode(",",$approval_string));
				$query->whereIn('erf_header_request.created_by', $userslist)
				->where('erf_header_request.status_id', $pending) 
				->orderBy('erf_header_request.id', 'DESC');

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
			if($column_index == 1){
				if($column_value == $pending){
					$column_value = '<span class="label label-warning">'.$pending.'</span>';
				}else if($column_value == $rejected){
					$column_value = '<span class="label label-danger">'.$rejected.'</span>';
				}else if($column_value == $for_verification){
					$column_value = '<span class="label label-info">'.$for_verification.'</span>';
				}else if($column_value == $verified){
					$column_value = '<span class="label label-info">'.$verified.'</span>';
				}else if($column_value == $jo_done){
					$column_value = '<span class="label label-info">'.$jo_done.'</span>';
				}else if($column_value == $onboarding){
					$column_value = '<span class="label label-success">'.$onboarding.'</span>';
				}else if($column_value == $cancelled){
					$column_value = '<span class="label label-danger">'.$cancelled.'</span>';
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
	        //Your code here

	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command after add public static function called 
	    | ---------------------------------------------------------------------- 
	    | @id = last insert id
	    | 
	    */
	    public function hook_after_add($id) {        
	        //Your code here

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
	        $fields = Request::all();
			//dd($fields);
			$dataLines = array();
			$approval_action 		= $fields['approval_action'];
			$approver_comments 		= $fields['additional_notess'];
			$salary_range           = explode("-",$fields['salary_range']);

			if($approval_action  == 1){
				$postdata['status_id'] 	                        = 29;
				$postdata['approver_comments'] 	                = $approver_comments;
				$postdata['date_needed']                        = $fields['date_needed'];
				$postdata['work_location']                      = $fields['work_location'];
				$postdata['position']                           = $fields['position'];
				$postdata['salary_range_from'] 			        = Crypt::encryptString(str_replace(',', '', $salary_range[0]));
			    $postdata['salary_range_to'] 			        = Crypt::encryptString(str_replace(',', '', $salary_range[1]));
				$postdata['schedule']                           = $fields['schedule'];
				$postdata['other_schedule']                     = $fields['other_schedule'];
				$postdata['allow_wfh']                          = $fields['allow_wfh'];
				$postdata['manpower']                           = $fields['manpower'];
				$postdata['replacement_of']                     = $fields['replacement_of'];
				$postdata['absorption']                         = $fields['absorption'];
				$postdata['manpower_type']                      = $fields['manpower_type'];
				if(!empty($fields['required_exams'])){
					$postdata['other_required_exams'] 	        = $fields['other_required_exams'];
					$postdata['required_exams'] 	            = implode(", ",$fields['required_exams']);
				}
				if(!empty($fields['employee_interaction'])){
					$postdata['employee_interaction'] 	        = implode(", ",$fields['employee_interaction']);
				}
				if(!empty($fields['asset_usage'])){
					$postdata['asset_usage'] 	                = implode(", ",$fields['asset_usage']);
				}
				if(!empty($fields['required_system'])){
					$postdata['required_system'] 	            = implode(", ",$fields['required_system']);
				}
				$postdata['shared_files']                       = $fields['shared_files'];
				$postdata['email_domain']                       = $fields['email_domain'];
				$postdata['other_email_domain']                 = $fields['other_email_domain'];
				$postdata['qualifications']                     = $fields['qualifications'];
				$postdata['job_description']                    = $fields['job_descriptions'];
				$postdata['approved_immediate_head_by']         = CRUDBooster::myId();
				$postdata['approved_immediate_head_at']         = date('Y-m-d H:i:s');
				
			}else{
				$postdata['status_id'] 	                        = 5;
				$postdata['approver_comments'] 	                = $approver_comments;
				$postdata['approved_immediate_head_by']         = CRUDBooster::myId();
				$postdata['approved_immediate_head_at']         = date('Y-m-d H:i:s');
			}
			
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

		public function getEdit($id){
			
			$this->cbLoader();
            if(!CRUDBooster::isRead() && $this->global_privilege==FALSE) {    
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

			$data = array();

			$data['page_title'] = 'View Erf For Approval';

			$data['Header'] = ErfHeaderRequest::
				leftjoin('companies', 'erf_header_request.company', '=', 'companies.id')
				->leftjoin('departments', 'erf_header_request.department', '=', 'departments.id')
				->select(
						'erf_header_request.*',
						'erf_header_request.id as requestid',
						'departments.department_name as department'
						)
				->where('erf_header_request.id', $id)->first();
		
			$res_req = explode(",",trim($data['Header']->required_exams));
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
			$data['positions'] = DB::table('positions')->where('status', 'ACTIVE')->get();
			//dd($data['res_req']);
			return $this->view("erf.approved_erf", $data);
		}

		public function getDownload($id) {
			$getFile = DB::table('erf_header_documents')->where('id',$id)->first();
			$file= public_path(). "/vendor/crudbooster/erf_folder/".$getFile->file_name;

			$headers = array(
					'Content-Type: application/pdf',
					);

			return Response::download($file, $getFile->file_name, $headers);
		}


	}