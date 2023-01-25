<?php namespace App\Http\Controllers;

	use Session;
	//use Request;
	use Illuminate\Http\Request;
	use DB;
	use CRUDBooster;
	use App\Models\Requests;
	use App\Models\ReturnTransferAssets;
	use App\Models\ReturnTransferAssetsHeader;
	use App\MoveOrder;
	use App\Users;
	class AdminReturnTransferAssetsHeaderController extends \crocodicstudio\crudbooster\controllers\CBController {

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
			$this->button_detail = true;
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
				$forTurnOver  = 		DB::table('statuses')->where('id', 24)->value('id');

				$this->addaction[] = ['title'=>'Cancel Request','url'=>CRUDBooster::mainpath('getRequestCancelReturn/[id]'),'icon'=>'fa fa-times', "showIf"=>"[status] == $pending"];
				$this->addaction[] = ['title'=>'Print','url'=>CRUDBooster::mainpath('getRequestPrintTF/[id]'),'icon'=>'fa fa-print', "showIf"=>"[status] == $forTurnOver"];
				//$this->addaction[] = ['title'=>'Receive Asset','url'=>CRUDBooster::mainpath('getRequestReceive/[id]'),'icon'=>'fa fa-check', "showIf"=>"[status_id] == $released"];
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
				$this->index_button[] = ["label"=>"Return Assets","icon"=>"fa fa-files-o","url"=>CRUDBooster::mainpath('return-assets'),"color"=>"success"];
				$this->index_button[] = ["label"=>"Transfer Assets","icon"=>"fa fa-files-o","url"=>CRUDBooster::mainpath('transfer-assets'),"color"=>"success"];
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

			})
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
	        
	        
	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | Add css style at body 
	        | ---------------------------------------------------------------------- 
	        | css code in the variable 
	        | $this->style_css = ".style{....}";
	        |
	        */
	        $this->style_css = "
			.fa.fa-times{
				color:#df4759;
				font-size:15px;
				margin-top: 2px;
			}
			";
	        
	        
	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | Include css File 
	        | ---------------------------------------------------------------------- 
	        | URL of your css each array 
	        | $this->load_css[] = asset("myfile.css");
	        |
	        */
	        $this->load_css = array();
	        
	        
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

				$query->whereNull('return_transfer_assets_header.archived')
					  ->orderBy('return_transfer_assets_header.status', 'ASC')
					  ->orderBy('return_transfer_assets_header.id', 'DESC');

			}else{

				$user = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();

				$query->where(function($sub_query){

					$user = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();

					$sub_query->where('return_transfer_assets_header.requested_by', CRUDBooster::myId())
							  ->whereNull('return_transfer_assets_header.archived'); 

				});
				$query->orderBy('return_transfer_assets_header.status', 'asc')->orderBy('return_transfer_assets_header.id', 'DESC');
			}
	    }

	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate row of index table html 
	    | ---------------------------------------------------------------------- 
	    |
	    */    
	    public function hook_row_index($column_index,&$column_value) {	        
	    	$pending      =  	 DB::table('statuses')->where('id', 1)->value('status_description');
			$cancelled    =  	 DB::table('statuses')->where('id', 8)->value('status_description');
			$forturnover  =      DB::table('statuses')->where('id', 24)->value('status_description');
			$toClose      =      DB::table('statuses')->where('id', 25)->value('status_description');
			$closed       =      DB::table('statuses')->where('id', 13)->value('status_description');
			if($column_index == 1){
				if($column_value == $pending){
					$column_value = '<span class="label label-warning">'.$pending.'</span>';
				}else if($column_value == $cancelled){
					$column_value = '<span class="label label-danger">'.$cancelled.'</span>';
				}else if($column_value == $forturnover){
					$column_value = '<span class="label label-info">'.$forturnover.'</span>';
				}else if($column_value == $toClose){
					$column_value = '<span class="label label-info">'.$toClose.'</span>';
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

		public function getDetail($id){
			

			$this->cbLoader();
            if(!CRUDBooster::isRead() && $this->global_privilege==FALSE) {    
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

			$data = array();

			$data['page_title'] = 'View Return Request';
			$data['user'] = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
			$data['Header'] = ReturnTransferAssetsHeader::leftjoin('cms_users as employees', 'return_transfer_assets_header.requestor_name', '=', 'employees.id')
			->leftjoin('requests', 'return_transfer_assets_header.request_type_id', '=', 'requests.id')
			->leftjoin('departments', 'employees.department_id', '=', 'departments.id')
			->leftjoin('cms_users as approved', 'return_transfer_assets_header.approved_by','=', 'approved.id')
			->leftjoin('cms_users as received', 'return_transfer_assets_header.transacted_by','=', 'received.id')
			->leftjoin('cms_users as closed', 'return_transfer_assets_header.close_by','=', 'closed.id')
			->leftjoin('locations', 'return_transfer_assets_header.store_branch', '=', 'locations.id')
			->select(
					'return_transfer_assets_header.*',
					'return_transfer_assets_header.id as requestid',
					'requests.request_name as request_name',
					'employees.name as employee_name',
					'employees.company_name_id as company',
					'employees.position_id as position',
					'departments.department_name as department_name',
					'approved.name as approvedby',
					'received.name as receivedby',
					'closed.name as closedby',
					'locations.store_name as store_branch'
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

			return $this->view("assets.view-return-details", $data);
		}

		public function getReturnAssets(){
			
			if(!CRUDBooster::isCreate() && $this->global_privilege == false) {
				CRUDBooster::redirect(CRUDBooster::adminPath(), trans('crudbooster.denied_access'));
			}

			$this->cbLoader();

			$data = array();

			$data['page_title'] = 'Return Request';

			$closed =  	DB::table('statuses')->where('id', 13)->value('id');
			$for_closing =  	DB::table('statuses')->where('id', 19)->value('id');
			$data['user'] = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
			$data['mo_body'] = MoveOrder::leftjoin('header_request', 'mo_body_request.header_request_id', '=', 'header_request.id')
				->leftjoin('request_type', 'header_request.purpose', '=', 'request_type.id')
				->leftjoin('requests', 'header_request.request_type_id', '=', 'requests.id')
				->leftjoin('condition_type', 'header_request.conditions', '=', 'condition_type.id')
				->leftjoin('cms_users as employees', 'header_request.employee_name', '=', 'employees.id')
				->leftjoin('companies', 'header_request.company_name', '=', 'companies.id')
				->leftjoin('departments', 'header_request.department', '=', 'departments.id')
				->leftjoin('positions', 'header_request.position', '=', 'positions.id')
				->leftjoin('locations', 'header_request.store_branch', '=', 'locations.id')
				->leftjoin('cms_users as requested', 'header_request.created_by','=', 'requested.id')
				->leftjoin('cms_users as approved', 'header_request.approved_by','=', 'approved.id')
				->leftjoin('cms_users as recommended', 'header_request.recommended_by','=', 'recommended.id')
				->leftjoin('cms_users as tagged', 'header_request.purchased2_by','=', 'tagged.id')
			
				->select(
						'header_request.*',
						'mo_body_request.*',
						'mo_body_request.id as mo_id',
						'header_request.id as requestid',
						'header_request.created_at as created',
						'request_type.*',
						'condition_type.*',
						'requested.name as requestedby',
						'employees.bill_to as employee_name',
						'employees.company_name_id as company_name',
						'departments.department_name as department',
						'mo_body_request.category_id as asset_type',
						'locations.store_name as store_branch',
						'locations.id as location_id',
						'approved.name as approvedby',
						'recommended.name as recommendedby',
						'tagged.name as taggedby',
						'header_request.created_at as created_at',
						DB::raw('IF(header_request.request_type_id IS NULL, mo_body_request.request_type_id_mo, header_request.request_type_id) as request_type_id')
						)
				->where('mo_body_request.request_created_by', CRUDBooster::myId())
				->whereIn('mo_body_request.status_id', [$closed, $for_closing])
				->whereNull('mo_body_request.return_flag')
				->get();
			if(CRUDBooster::myPrivilegeId() == 8){ 
				$data['stores'] = DB::table('locations')->where('id', $data['user']->location_id)->first();
			}else{
				$data['stores'] = NULL;
			}	
			return $this->view("assets.return-assets", $data);
		}

		//TRANSFER AREA
		public function getTransferAssets(){
			
			if(!CRUDBooster::isCreate() && $this->global_privilege == false) {
				CRUDBooster::redirect(CRUDBooster::adminPath(), trans('crudbooster.denied_access'));
			}

			$this->cbLoader();

			$data = array();

			$data['page_title'] = 'Transfer Request';

			$closed =  	DB::table('statuses')->where('id', 13)->value('id');
			$for_closing =  	DB::table('statuses')->where('id', 19)->value('id');
			$data['user'] = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
			$data['mo_body'] = MoveOrder::leftjoin('header_request', 'mo_body_request.header_request_id', '=', 'header_request.id')
				->leftjoin('request_type', 'header_request.purpose', '=', 'request_type.id')
				->leftjoin('requests', 'header_request.request_type_id', '=', 'requests.id')
				->leftjoin('condition_type', 'header_request.conditions', '=', 'condition_type.id')
				->leftjoin('cms_users as employees', 'header_request.employee_name', '=', 'employees.id')
				->leftjoin('companies', 'header_request.company_name', '=', 'companies.id')
				->leftjoin('departments', 'header_request.department', '=', 'departments.id')
				->leftjoin('positions', 'header_request.position', '=', 'positions.id')
				->leftjoin('locations', 'header_request.store_branch', '=', 'locations.id')
				->leftjoin('cms_users as requested', 'header_request.created_by','=', 'requested.id')
				->leftjoin('cms_users as approved', 'header_request.approved_by','=', 'approved.id')
				->leftjoin('cms_users as recommended', 'header_request.recommended_by','=', 'recommended.id')
				->leftjoin('cms_users as tagged', 'header_request.purchased2_by','=', 'tagged.id')
			
				->select(
						'header_request.*',
						'mo_body_request.*',
						'mo_body_request.id as mo_id',
						'header_request.id as requestid',
						'header_request.created_at as created',
						'request_type.*',
						'condition_type.*',
						'requested.name as requestedby',
						'employees.bill_to as employee_name',
						'employees.company_name_id as company_name',
						'departments.department_name as department',
						'mo_body_request.category_id as asset_type',
						'locations.store_name as store_branch',
						'locations.id as location_id',
						'approved.name as approvedby',
						'recommended.name as recommendedby',
						'tagged.name as taggedby',
						'header_request.created_at as created_at',
						DB::raw('IF(header_request.request_type_id IS NULL, mo_body_request.request_type_id_mo, header_request.request_type_id) as request_type_id')
						)
				->where('mo_body_request.request_created_by', CRUDBooster::myId())
				->whereIn('mo_body_request.status_id', [$closed, $for_closing])
				->whereNull('mo_body_request.return_flag')
				->get();
			if(CRUDBooster::myPrivilegeId() == 8){ 
				$data['stores'] = DB::table('locations')->where('id', $data['user']->location_id)->first();
			}else{
				$data['stores'] = NULL;
			}	
			
			$data['users'] = Users::where('id_cms_privileges','!=',1)->where('department_id',$data['user']->department_id)->get();
	
			return $this->view("assets.transfer-assets", $data);
		}

		public function saveReturnAssets(Request $request){
			$moId = $request['Ids'];
			$rid = $request['request_type_id'];
			$request_type_id = array_unique($rid);
			$location = $request['location_id'];
            
			$getData = MoveOrder::leftjoin('header_request', 'mo_body_request.header_request_id', '=', 'header_request.id')
			->leftjoin('requests', 'header_request.request_type_id', '=', 'requests.id')
			->select(
				'header_request.*',
				'mo_body_request.*',
				'mo_body_request.id as mo_id',
				'requests.*',
				DB::raw('IF(header_request.request_type_id IS NULL, mo_body_request.request_type_id_mo, header_request.request_type_id) as request_type_id')
				)
			->whereIn('mo_body_request.id', $moId)
			->get();

			//Get Latest ID
			$callStart = $this->call_start;
			$latestRequest = DB::table('return_transfer_assets_header')->select('id')->orderBy('id','DESC')->first();
			$latestRequestId = $latestRequest->id != NULL ? $latestRequest->id : 0;
			// Header Area
			$conHeader = [];
			$conHeaderSave = [];
			$count_header       = DB::table('return_transfer_assets_header')->count();
			$forApproval        = DB::table('statuses')->where('id', 1)->value('id');
			$forturnover        = DB::table('statuses')->where('id', 24)->value('id');
			$forReturn          = DB::table('statuses')->where('id', 26)->value('id');

			$inventory_id 	    = MoveOrder::whereIn('id',$moId)->get();
			$finalinventory_id = [];
			foreach($inventory_id as $invData){
				array_push($finalinventory_id, $invData['inventory_id']);
			}
			
			if(in_array(CRUDBooster::myPrivilegeId(), [11,12,14,15])){ 
				$status		 			= $forturnover;
				for($x=0; $x < count($moId); $x++) {
					DB::table('assets_inventory_body')->where('id', $finalinventory_id[$x])
					->update([
						'statuses_id'=> 			$forReturn,
					]);
				}
			}else{
				$status		 			= $forApproval;
	
			}
		    
			foreach($request_type_id as $hKey => $hData){
				$conHeader['status'] = $status;
				$conHeader['requestor_name'] = CRUDBooster::myId();
				$conHeader['request_type_id'] = $hData;
				$conHeader['request_type'] = "RETURN";
				$conHeader['requested_by'] = CRUDBooster::myId(); 
				$conHeader['requested_date'] = date('Y-m-d H:i:s');
				if($hData == 1){
					$conHeader['location_to_pick'] = 3; 
				}else{
					$conHeader['location_to_pick'] = 2; 
				}
				$conHeader['store_branch'] = $location[$hKey];

				if($hData == 1){
					$conHeader['reference_no'] = "1".str_pad($count_header + 1, 6, '0', STR_PAD_LEFT)."ITAR";
					$count_header++;
				}else{
					$conHeader['reference_no'] = "1".str_pad($count_header + 1, 6, '0', STR_PAD_LEFT)."FAR";
					$count_header++;
				}
				$conHeaderSave[] = $conHeader;
			}
		
			ReturnTransferAssetsHeader::insert($conHeaderSave);
			$itId = DB::table('return_transfer_assets_header')->select('*')->where('id','>', $latestRequestId)->where('request_type_id',1)->first();
			$faId = DB::table('return_transfer_assets_header')->select('*')->where('id','>', $latestRequestId)->where('request_type_id',5)->first();
	
			$resultArrforIT = [];
			foreach($getData as $item){
				if($item['request_type_id'] == 1){
					for($i = 0; $i < $item['request_type_id']; $i++){
						$t = $item;
						$t['return_header'] = $itId->id;
						$t['reference_no'] = $itId->reference_no;
						$resultArrforIT[] = $t;
					}
				}
			}
			
			$resultArrforFA = [];
			foreach($getData as $itemFa){
				if($itemFa['request_type_id'] == 5){
					for($x = 0; $x < $itemFa['request_type_id']; $x++){
						$fa = $itemFa;
						$fa['return_header'] = $faId->id;
						$fa['reference_no'] = $faId->reference_no;
						$resultArrforFA[] = $fa;
					}
				}
			}
	
			$finalReturnData = array_merge($resultArrforIT, $resultArrforFA);
	
			// Body Area
			$container = [];
			$containerSave = [];
	       
			foreach($getData as $rKey => $rData){		
				$container['status'] = $status;
				$container['return_header_id'] = $rData['return_header'];
				$container['mo_id'] = $rData['mo_id'];
				if($rData['request_type_id'] == 1){
					$container['reference_no'] = $rData['reference_no'];
					$container['location_to_pick'] = 3;
				}else{
					$container['reference_no'] = $rData['reference_no'];
					$container['location_to_pick'] = 2;
				}
				$container['asset_code'] =  $rData['asset_code'];
				$container['digits_code'] = $rData['digits_code'];
				$container['description'] = $rData['item_description'];
				$container['asset_type'] = $rData['category_id'];
				$container['requested_by'] = CRUDBooster::myId(); 
				$container['requested_date'] = date('Y-m-d H:i:s');
				$containerSave[] = $container;
			}
			ReturnTransferAssets::insert($containerSave);

			for ($i = 0; $i < count($moId); $i++) {
				MoveOrder::where(['id' => $moId[$i]])
				   ->update([
						   'return_flag' => 1,
				           ]);
			}

			$message = ['status'=>'success', 'message' => 'Send Successfully!','redirect_url'=>CRUDBooster::mainpath()];
			echo json_encode($message);
		}

		public function saveTransferAssets(Request $request){
			$moId = $request['Ids'];
			$rid = $request['request_type_id'];
			$request_type_id = $rid;
			$location = $request['location_id'];
			$user_id = $request['users_id'];
            //dd($request->all());
			$getData = MoveOrder::leftjoin('header_request', 'mo_body_request.header_request_id', '=', 'header_request.id')
			->leftjoin('requests', 'header_request.request_type_id', '=', 'requests.id')
			->select(
				'header_request.*',
				'mo_body_request.*',
				'mo_body_request.id as mo_id',
				'requests.*',
				DB::raw('IF(header_request.request_type_id IS NULL, mo_body_request.request_type_id_mo, header_request.request_type_id) as request_type_id')
				)
			->whereIn('mo_body_request.id', $moId)
			->get();

			$forApproval        = DB::table('statuses')->where('id', 1)->value('id');
			$forturnover        = DB::table('statuses')->where('id', 24)->value('id');
			$forTransfer          = DB::table('statuses')->where('id', 27)->value('id');

			$inventory_id 	    = MoveOrder::whereIn('id',$moId)->get();
			$finalinventory_id = [];
			foreach($inventory_id as $invData){
				array_push($finalinventory_id, $invData['inventory_id']);
			}
			
			if(in_array(CRUDBooster::myPrivilegeId(), [11,12,14,15])){ 
				$status		 			= $forturnover;
				for($x=0; $x < count($moId); $x++) {
					DB::table('assets_inventory_body')->where('id', $finalinventory_id[$x])
					->update([
						'statuses_id'=> 			$forTransfer,
					]);
				}
			}else{
				$status		 			= $forApproval;
	
			}

			// Header Area
			$count_header       = DB::table('return_transfer_assets_header')->count();
			$reference_no = "1".str_pad($count_header + 1, 7, '0', STR_PAD_LEFT)."AT";

			$id = ReturnTransferAssetsHeader::Create(
                [
                    'status' => $status, 
					'reference_no' => $reference_no,
                    'requestor_name' => CRUDBooster::myId(), 
                    'request_type_id' => 8,
                    'request_type' => "TRANSFER",
                    'requested_by' => CRUDBooster::myId(),
                    'requested_date' => date('Y-m-d H:i:s'),
                    'location_to_pick' => 5,
                    'store_branch' => $location,
                    'transfer_to' => $user_id,
                ]
            );   
		
		    $header_id = $id->id;
			$ref_no 	= 	ReturnTransferAssetsHeader::where('id',$header_id)->first();
			// Body Area
			$container = [];
			$containerSave = [];
			
			foreach($getData as $rKey => $rData){		
				$container['status'] = $status;
				$container['return_header_id'] = $header_id;
				$container['mo_id'] = $rData['mo_id'];
				$container['reference_no'] = $ref_no->reference_no;
				$container['location_to_pick'] = 5;
				$container['asset_code'] =  $rData['asset_code'];
				$container['digits_code'] = $rData['digits_code'];
				$container['description'] = $rData['item_description'];
				$container['asset_type'] = $rData['category_id'];
				$container['requested_by'] = CRUDBooster::myId(); 
				$container['requested_date'] = date('Y-m-d H:i:s');
				$container['transfer_to'] = $user_id;
				$containerSave[] = $container;
			}
			ReturnTransferAssets::insert($containerSave);

			for ($i = 0; $i < count($moId); $i++) {
				MoveOrder::where(['id' => $moId[$i]])
				   ->update([
						   'return_flag' => 1,
				           ]);
			}

			$message = ['status'=>'success', 'message' => 'Send Successfully!','redirect_url'=>CRUDBooster::mainpath()];
			echo json_encode($message);
		}

		public function getRequestCancelReturn($id) {
			ReturnTransferAssetsHeader::where('id',$id)
				->update([
					    'status' => 8
				]);	

			$getAssetCode = ReturnTransferAssets::where('return_header_id',$id)->get();
			$arrCode = [];
			foreach($getAssetCode as $code){
              array_push($arrCode, $code['asset_code']);
			}

			for ($i = 0; $i < count($arrCode); $i++) {
				MoveOrder::where('asset_code',$arrCode[$i])
				->update([
						'return_flag'=> NULL,
				]);	
		    }
			ReturnTransferAssets::where('return_header_id',$id)
				->update([
					    'status' => 8,
						'archived'=> date('Y-m-d H:i:s'),
				]);	

			CRUDBooster::redirect(CRUDBooster::mainpath(), trans("Request has been cancelled successfully!"), 'info');
		}

		public function getRequestPrintTF($id){
			
		
			$this->cbLoader();
			if(!CRUDBooster::isUpdate() && $this->global_privilege==FALSE) {    
				CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
			}  

			$data['page_title'] = 'Print Return/Transfer Request';
			$data['user'] = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
			$data['Header'] = ReturnTransferAssetsHeader::leftjoin('cms_users as employees', 'return_transfer_assets_header.requestor_name', '=', 'employees.id')
			->leftjoin('requests', 'return_transfer_assets_header.request_type_id', '=', 'requests.id')
			->leftjoin('departments', 'employees.department_id', '=', 'departments.id')
			->leftjoin('cms_users as requested', 'return_transfer_assets_header.requested_by','=', 'requested.id')
			->leftjoin('cms_users as transfer_to', 'return_transfer_assets_header.transfer_to','=', 'transfer_to.id')
			->leftjoin('cms_users as approved', 'return_transfer_assets_header.approved_by','=', 'approved.id')
			->leftjoin('cms_users as received', 'return_transfer_assets_header.transacted_by','=', 'received.id')
			->leftjoin('cms_users as closed', 'return_transfer_assets_header.close_by','=', 'closed.id')

			->leftjoin('locations', 'return_transfer_assets_header.store_branch', '=', 'locations.id')
			->select(
					'return_transfer_assets_header.*',
					'return_transfer_assets_header.id as requestid',
					'requests.request_name as request_name',
					'requested.name as requestedby',
					'transfer_to.name as transferTo',
					'employees.name as employee_name',
					'employees.company_name_id as company',
					'employees.position_id as position',
					'departments.department_name as department_name',
					'approved.name as approvedby',
					'received.name as receivedby',
					'closed.name as closedby',
					'locations.store_name as store_branch',
					
					)
			->where('return_transfer_assets_header.id', $id)->first();
	   
			$data['return_body'] = ReturnTransferAssets::
					leftjoin('statuses', 'return_transfer_assets.status', '=', 'statuses.id')
					->leftjoin('mo_body_request', 'return_transfer_assets.mo_id', '=', 'mo_body_request.id')
				->select(
					'return_transfer_assets.*',
					DB::raw('SUM(mo_body_request.unit_cost) as total_cost'),
					'mo_body_request.*',
					'statuses.*',
					)
					->where('return_transfer_assets.return_header_id', $id)->get();	;
			$data['stores'] = DB::table('locations')->where('id', $data['user']->location_id)->first();
			
			return $this->view("assets.print-request-trf", $data);
		}
	}