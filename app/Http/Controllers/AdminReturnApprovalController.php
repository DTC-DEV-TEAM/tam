<?php namespace App\Http\Controllers;

	use Session;
	use Request;
	use DB;
	use CRUDBooster;
	use App\Users;
	use App\MoveOrder;
	use App\Models\ReturnTransferAssets;
	use App\Models\ReturnTransferAssetsHeader;
	class AdminReturnApprovalController extends \crocodicstudio\crudbooster\controllers\CBController {

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
			$this->button_edit = false;
			$this->button_delete = false;
			$this->button_detail = false;
			$this->button_show = true;
			$this->button_filter = true;
			$this->button_import = false;
			$this->button_export = false;
			$this->table = "return_transfer_assets_header";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Status","name"=>"status","join"=>"statuses,status_description"];
			$this->col[] = ["label"=>"Reference No","name"=>"reference_no"];
			$this->col[] = ["label"=>"Name","name"=>"requestor_name","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"Return Type","name"=>"request_type_id","join"=>"requests,request_name"];
			$this->col[] = ["label"=>"Type of Request","name"=>"request_type"];
			$this->col[] = ["label"=>"Requested Date","name"=>"requested_date"];
			$this->col[] = ["label"=>"Transacted By","name"=>"transacted_by"];
			$this->col[] = ["label"=>"Transacted Date","name"=>"transacted_date"];
		
			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];

			# END FORM DO NOT REMOVE THIS LINE

			# OLD START FORM
			//$this->form = [];
			//$this->form[] = ["label"=>"Status","name"=>"status","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Reference No","name"=>"reference_no","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Asset Code","name"=>"asset_code","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Digits Code","name"=>"digits_code","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Description","name"=>"description","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Asset Type","name"=>"asset_type","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Transacted By","name"=>"transacted_by","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Transacted Date","name"=>"transacted_date","type"=>"datetime","required"=>TRUE,"validation"=>"required|date_format:Y-m-d H:i:s"];
			//$this->form[] = ["label"=>"Location To Pick","name"=>"location_to_pick","type"=>"textarea","required"=>TRUE,"validation"=>"required|string|min:5|max:5000"];
			//$this->form[] = ["label"=>"Requested By","name"=>"requested_by","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Requested Date","name"=>"requested_date","type"=>"datetime","required"=>TRUE,"validation"=>"required|date_format:Y-m-d H:i:s"];
			# OLD END FORM

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
				
				$pending           = DB::table('statuses')->where('id', 1)->value('id');

				$this->addaction[] = ['title'=>'Update','url'=>CRUDBooster::mainpath('getRequestApprovalReturn/[id]'),'icon'=>'fa fa-pencil', "showIf"=>"[status] == $pending"];
				//$this->addaction[] = ['title'=>'Edit','url'=>CRUDBooster::mainpath('getRequestEdit/[id]'),'icon'=>'fa fa-pencil', "showIf"=>"[status_id] == $Rejected"]; //, "showIf"=>"[status_level1] == $inwarranty"
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

				$pending           = DB::table('statuses')->where('id', 1)->value('id');

				$query->orderBy('return_transfer_assets_header.status', 'DESC')->where('return_transfer_assets_header.status', $pending)->orderBy('return_transfer_assets_header.id', 'DESC');
			
			}else{

				$pending           = DB::table('statuses')->where('id', 1)->value('id');

				//$user_data         = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();

				$approvalMatrix = Users::where('cms_users.approver_id', CRUDBooster::myId())->get();
			
				$approval_array = array();
				foreach($approvalMatrix as $matrix){
				    array_push($approval_array, $matrix->id);
				}
				$approval_string = implode(",",$approval_array);
				$userslist = array_map('intval',explode(",",$approval_string));
	
				$query->whereIn('return_transfer_assets_header.requested_by', $userslist)
				//->whereIn('return_transfer_assets_header.company_name', explode(",",$user_data->company_name_id))
				->where('return_transfer_assets_header.status', $pending) 
				->orderBy('return_transfer_assets_header.id', 'DESC');

			}
	            
	    }

	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate row of index table html 
	    | ---------------------------------------------------------------------- 
	    |
	    */    
	    public function hook_row_index($column_index,&$column_value) {	        
	    	$pending  =  		DB::table('statuses')->where('id', 1)->value('status_description');
			if($column_index == 1){
				if($column_value == $pending){
					$column_value = '<span class="label label-warning">'.$pending.'</span>';
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
	         //Your code here
			$fields = Request::all();
			$header_id 					= $fields['header_id'];
			$mo_id 					= $fields['mo_id'];
			$dataLines = array();
			$approval_action 		= $fields['approval_action'];
			$approver_comments 		= $fields['approver_comments'];
			$approved =  		DB::table('statuses')->where('id', 4)->value('id');
			$rejected =  		DB::table('statuses')->where('id', 5)->value('id');
			$forturnover =  		DB::table('statuses')->where('id', 24)->value('id');
			$forReturn =  		DB::table('statuses')->where('id', 26)->value('id');
			$forTransfer =  		DB::table('statuses')->where('id', 27)->value('id');

			$header 	= 	ReturnTransferAssetsHeader::where('id',$header_id)->first();
			$inventory_id 	= 	MoveOrder::whereIn('id',$mo_id)->get();
			$finalinventory_id = [];
			foreach($inventory_id as $invData){
				array_push($finalinventory_id, $invData['inventory_id']);
			}

			if($approval_action  == 1){
				for($x=0; $x < count((array)$mo_id); $x++) {
	
					$postdata['status']		 	    = $forturnover;
					$postdata['approved_by'] 		= CRUDBooster::myId();
					$postdata['approved_date'] 		= date('Y-m-d H:i:s');
					ReturnTransferAssets::where('return_header_id',$id)
					->update([
							'status' => $forturnover
					]);	
					if(in_array($header->request_type_id, [1,5])){
						DB::table('assets_inventory_body')->where('id', $finalinventory_id[$x])
						->update([
							'statuses_id'=> 			$forReturn,
						]);
					}else{
						DB::table('assets_inventory_body')->where('id', $finalinventory_id[$x])
						->update([
							'statuses_id'=> 			$forTransfer,
						]);
					}
					
			    }
			}else{
				$postdata['status'] 			= $rejected;
				$postdata['approver_comments'] 	= $approver_comments;
				$postdata['approved_by'] 		= CRUDBooster::myId();
				$postdata['rejected_date'] 		= date('Y-m-d H:i:s');
				ReturnTransferAssets::where('return_header_id',$id)
				->update([
					    'status' => $rejected
				]);	
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

		public function getRequestApprovalReturn($id){
			

			$this->cbLoader();
			if(!CRUDBooster::isUpdate() && $this->global_privilege==FALSE) {    
				CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
			}  


			$data = array();

			$data['page_title'] = 'Approve Return/Transfer Request';
			$data['user'] = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
			$data['Header'] = ReturnTransferAssetsHeader::leftjoin('cms_users as employees', 'return_transfer_assets_header.requestor_name', '=', 'employees.id')
				->leftjoin('requests', 'return_transfer_assets_header.request_type_id', '=', 'requests.id')
				->leftjoin('departments', 'employees.department_id', '=', 'departments.id')
				->leftjoin('locations', 'return_transfer_assets_header.store_branch', '=', 'locations.id')
				->leftjoin('cms_users as transfer_to', 'return_transfer_assets_header.transfer_to','=', 'transfer_to.id')
				->select(
						'return_transfer_assets_header.*',
						'return_transfer_assets_header.id as requestid',
						'requests.request_name as request_name',
						'employees.name as employee_name',
						'employees.company_name_id as company',
						'employees.position_id as position',
						'departments.department_name as department_name',
						'locations.store_name as store_branch',
						'transfer_to.bill_to as transfer_to',
						)
				->where('return_transfer_assets_header.id', $id)->first();
           
			$data['return_body'] = ReturnTransferAssets::
			           leftjoin('statuses', 'return_transfer_assets.status', '=', 'statuses.id')
				
				->select(
						'return_transfer_assets.*',
						'statuses.*',
						)
						->where('return_transfer_assets.return_header_id', $id)->get();	
			$data['stores'] = DB::table('locations')->where('id', $data['user']->location_id)->first();
			return $this->view("assets.approval-request-return", $data);
		}



	}