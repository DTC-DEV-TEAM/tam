<?php namespace App\Http\Controllers;

	use Session;
	use Request;
	use DB;
	use CRUDBooster;
	use App\HeaderRequest;
	use App\BodyRequest;
	use App\ApprovalMatrix;
	use App\StatusMatrix;
	use App\Users;
	use App\Models\AssetsSuppliesInventory;
	use App\Models\AssetsInventoryReserved;
	//use Illuminate\Http\Request;
	//use Illuminate\Support\Facades\Input;
	use Illuminate\Support\Facades\Log;
	use Illuminate\Support\Facades\Redirect;

	class AdminApprovalController extends \crocodicstudio\crudbooster\controllers\CBController {

        public function __construct() {
			// Register ENUM type
			//$this->request = $request;
			$this->middleware('check.approvalschedule',['only' => ['getRequestApprovalSupplies']]);
			DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping("enum", "string");
		}

		private static $apiContext; 

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "employee_name";
			$this->limit = "20";
			$this->orderby = "id,desc";
			$this->global_privilege = false;
			$this->button_table_action = true;
			$this->button_bulk_action = true;
			$this->button_action_style = "button_icon";
			$this->button_add = true;
			$this->button_edit = false;
			$this->button_delete = true;
			$this->button_detail = false;
			$this->button_show = true;
			$this->button_filter = true;
			$this->button_import = false;
			$this->button_export = false;
			$this->table = "header_request";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Status","name"=>"status_id","join"=>"statuses,status_description"];
			$this->col[] = ["label"=>"Reference Number","name"=>"reference_number"];
			$this->col[] = ["label"=>"Request Type","name"=>"request_type_id","join"=>"requests,request_name"];
			$this->col[] = ["label"=>"Company Name","name"=>"company_name"];
			$this->col[] = ["label"=>"Employee Name","name"=>"employee_name","join"=>"cms_users,bill_to"];
			$this->col[] = ["label"=>"Department","name"=>"department","join"=>"departments,department_name"];
			$this->col[] = ["label"=>"Requested By","name"=>"created_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"Requested Date","name"=>"created_at"];
			//$this->col[] = ["label"=>"Updated By","name"=>"updated_by","join"=>"cms_users,name"];
			//$this->col[] = ["label"=>"Updated Date","name"=>"updated_at"];
			$this->col[] = ["label"=>"Approved By","name"=>"approved_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"Approved Date","name"=>"approved_at"];
			$this->col[] = ["label"=>"Rejected Date","name"=>"rejected_at"];
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
				
				$pending           = DB::table('statuses')->where('id', 1)->value('id');

				$this->addaction[] = ['title'=>'Update','url'=>CRUDBooster::mainpath('getRequestApproval/[id]'),'icon'=>'fa fa-pencil', "showIf"=>"[status_id] == $pending && [request_type_id] != 7"];
				$this->addaction[] = ['title'=>'Update','url'=>CRUDBooster::mainpath('getRequestApprovalSupplies/[id]'),'icon'=>'fa fa-pencil', "showIf"=>"[status_id] == $pending && [request_type_id] == 7"];
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
	        //Your code here
			if(CRUDBooster::isSuperadmin()){

				$pending           = DB::table('statuses')->where('id', 1)->value('id');

				$query->whereNull('header_request.deleted_at')->orderBy('header_request.status_id', 'DESC')->where('header_request.status_id', $pending)->orderBy('header_request.id', 'DESC');
			
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
	
				$query->whereIn('header_request.created_by', $userslist)
				//->whereIn('header_request.company_name', explode(",",$user_data->company_name_id))
				->where('header_request.status_id', $pending) 
				->whereNull('header_request.deleted_at')
				->orderBy('header_request.id', 'DESC');

			}
	            
	    }

	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate row of index table html 
	    | ---------------------------------------------------------------------- 
	    |
	    */    
	    public function hook_row_index($column_index,&$column_value) {	        
	    	//Your code here
			$pending  =  		DB::table('statuses')->where('id', 1)->value('status_description');
			$approved =  		DB::table('statuses')->where('id', 4)->value('status_description');
			$rejected =  		DB::table('statuses')->where('id', 5)->value('status_description');
			$it_reco  = 		DB::table('statuses')->where('id', 7)->value('status_description');

			if($column_index == 2){
				if($column_value == $pending){
					$column_value = '<span class="label label-warning">'.$pending.'</span>';
				}else if($column_value == $approved){
					$column_value = '<span class="label label-info">'.$approved.'</span>';
				}else if($column_value == $rejected){
					$column_value = '<span class="label label-danger">'.$rejected.'</span>';
				}else if($column_value == $it_reco){
					$column_value = '<span class="label label-info">'.$it_reco.'</span>';
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
			$cont = (new static)->apiContext;

			$dataLines = array();

			$approval_action 		= $fields['approval_action'];
			$approver_comments 		= $fields['approver_comments'];
			$body_ids 		        = $fields['body_ids'];
			if(in_array($arf_header->request_type_id, [7])){
			    $wh_qty 		    = $fields['wh_qty'];
			}else{
				$wh_qty 		    = $fields['it_wh_qty'];
			}

			$approved               =  DB::table('statuses')->where('id', 4)->value('id');
			$rejected               =  DB::table('statuses')->where('id', 5)->value('id');
			$for_move_order         =  DB::table('statuses')->where('id', 14)->value('id');

			$arf_header             = HeaderRequest::where(['id' => $id])->first();

			$arf_body               = BodyRequest::where(['header_request_id' => $id])->whereNull('deleted_at')->get();

			if($approval_action  == 1){
				$postdata['status_id']          = $for_move_order;
				$postdata['approver_comments'] 	= $approver_comments;
				$postdata['approved_by'] 		= CRUDBooster::myId();
				$postdata['approved_at'] 		= date('Y-m-d H:i:s');

				foreach($arf_body as $body_arf){
					if($body_arf->category_id == "IT ASSETS"){
						$postdata['to_reco'] 	= 1;
					}

				}

				for ($i = 0; $i < count($body_ids); $i++) {
					BodyRequest::where('id', $body_ids[$i])
					->update([
						'wh_qty'=> 		$wh_qty[$i],
					]);	
		    	}

				if(in_array($arf_header->request_type_id, [7])){
					//Get the inventory value per digits code
					$arraySearch = DB::table('assets_supplies_inventory')->select('*')->get()->toArray();
				
					$finalBodyValue = [];
					foreach($arf_body as $bodyfKey => $bodyVal){
						$i = array_search($bodyVal['digits_code'], array_column($arraySearch,'digits_code'));
						if($i !== false){
							$bodyVal['inv_value'] = $arraySearch[$i];
							$finalBodyValue[] = $bodyVal;
						}else{
							$bodyVal['inv_value'] = "";
							$finalBodyValue[] = $bodyVal;
						}
					}

					//Set data in each qty
					$containerData = [];
					$finalContData = [];
					foreach($finalBodyValue as $fBodyKey => $fBodyVal){
                        if($fBodyVal['inv_value']->quantity > $fBodyVal['quantity']){
							//less quantity in inventory
							BodyRequest::where('id', $fBodyVal['id'])
							->update([
								'replenish_qty'      =>  $fBodyVal['quantity'],
								'reorder_qty'        =>  NULL,
								'serve_qty'          =>  NULL,
								'unserved_qty'       =>  $fBodyVal['quantity'],
								'unserved_rep_qty'   =>  $fBodyVal['quantity'],
								'unserved_ro_qty'    =>  NULL
							]);	
							DB::table('assets_supplies_inventory')
							->where('digits_code', $fBodyVal['digits_code'])
							->decrement('quantity', $fBodyVal['quantity']);
						}else{
							$reorder = $fBodyVal['quantity'] - $fBodyVal['inv_value']->quantity;
							$containerData['serve_qty']     = $fBodyVal['inv_value']->quantity;  
							$containerData['unserve_qty']   = $containerData['reorder_qty'];
							BodyRequest::where('id', $fBodyVal['id'])
							->update([
								'replenish_qty'      =>  $fBodyVal['inv_value']->quantity,
								'reorder_qty'        =>  $reorder,
								'serve_qty'          =>  NULL,
								'unserved_qty'       =>  $fBodyVal['quantity'],
								'unserved_rep_qty'   =>  $fBodyVal['inv_value']->quantity,
								'unserved_ro_qty'    =>  $reorder
							]);	
							AssetsSuppliesInventory::where('digits_code', $fBodyVal['digits_code'])
							->update([
								'quantity'   =>  0,
							]);	
					    }
						$finalContData[] = $containerData;
					}
			    }else{
					//GET ASSETS INVENTORY AVAILABLE COUNT
					$inventoryList = DB::table('assets_inventory_body')->select('digits_code as digits_code',DB::raw('SUM(quantity) as avail_qty'))->where('statuses_id',6)->groupBy('digits_code')->get();
					//GET RESERVED QTY 
					$reservedList = DB::table('assets_inventory_reserved')->select('digits_code as digits_code',DB::raw('SUM(approved_qty) as reserved_qty'))->whereNotNull('reserved')->groupBy('digits_code')->get()->toArray();
					
					$resultInventory = [];
					foreach($inventoryList as $invKey => $invVal){
						$i = array_search($invVal->digits_code, array_column($reservedList,'digits_code'));
						if($i !== false){
							$invVal->reserved_value = $reservedList[$i];
							$resultInventory[] = $invVal;
						}else{
							$invVal->reserved_value = "";
							$resultInventory[] = $invVal;
						}
					}
					//get the final available qty
					$finalInventory = [];
					foreach($resultInventory as $fKey => $fVal){
						$fVal->available_qty = max($fVal->avail_qty - $fVal->reserved_value->reserved_qty,0);
						$finalInventory[] = $fVal;
					}

					$finalItFaBodyValue = [];
					foreach($arf_body as $bodyItFafKey => $bodyItFaVal){
						$i = array_search($bodyItFaVal['digits_code'], array_column($finalInventory,'digits_code'));
						if($i !== false){
							$bodyItFaVal->inv_qty = $finalInventory[$i];
							$finalItFaBodyValue[] = $bodyItFaVal;
						}else{
							$bodyItFaVal->inv_qty = "";
							$finalItFaBodyValue[] = $bodyItFaVal;
						}
					}
                   
					foreach($finalItFaBodyValue as $fBodyItFaKey => $fBodyItFaVal){
						$countAvailQty = DB::table('assets_inventory_body')->select('digits_code as digits_code',DB::raw('SUM(quantity) as avail_qty'))->where('statuses_id',6)->where('digits_code',$fBodyItFaVal->digits_code)->groupBy('digits_code')->count();
                        $reservedListCount = DB::table('assets_inventory_reserved')->select('digits_code as digits_code',DB::raw('SUM(approved_qty) as reserved_qty'))->whereNotNull('reserved')->where('digits_code',$fBodyItFaVal->digits_code)->groupBy('digits_code')->count();
						$available_quantity = max($countAvailQty - $reservedListCount,0);
			
						if($available_quantity >= $fBodyItFaVal->quantity){
							//add to reserved taable
							AssetsInventoryReserved::Create(
								[
									'reference_number'    => $arf_header->reference_number, 
									'body_id'             => $fBodyItFaVal->id,
									'digits_code'         => $fBodyItFaVal->digits_code, 
									'approved_qty'        => $fBodyItFaVal->quantity,
									'reserved'            => $fBodyItFaVal->quantity,
									'for_po'              => NULL,
									'created_by'          => CRUDBooster::myId(),
									'created_at'          => date('Y-m-d H:i:s'),
									'updated_by'          => CRUDBooster::myId(),
									'updated_at'          => date('Y-m-d H:i:s')
								]
							); 
							
							//update details in body table
							BodyRequest::where('id', $fBodyItFaVal->id)
							->update([
								'replenish_qty'      =>  $fBodyItFaVal->quantity,
								'reorder_qty'        =>  NULL,
								'serve_qty'          =>  NULL,
								'unserved_qty'       =>  $fBodyItFaVal->quantity,
								'unserved_rep_qty'   =>  $fBodyItFaVal->quantity,
								'unserved_ro_qty'    =>  NULL
							]);	

							HeaderRequest::where('id',$id)
							->update([
								'to_mo' => 1
							]);
							 
						}else{
							$reorder = $fBodyItFaVal->quantity - $available_quantity;
							AssetsInventoryReserved::Create(
								[
									'reference_number'    => $arf_header->reference_number, 
									'body_id'             => $fBodyItFaVal->id,
									'digits_code'         => $fBodyItFaVal->digits_code, 
									'approved_qty'        => $fBodyItFaVal->quantity,
									'reserved'            => NULL,
									'for_po'              => 1,
									'created_by'          => CRUDBooster::myId(),
									'created_at'          => date('Y-m-d H:i:s'),
									'updated_by'          => CRUDBooster::myId(),
									'updated_at'          => date('Y-m-d H:i:s')
								]
							);  

							BodyRequest::where('id', $fBodyItFaVal->id)
							->update([
								'replenish_qty'      =>  $available_quantity,
								'reorder_qty'        =>  $reorder,
								'serve_qty'          =>  NULL,
								'unserved_qty'       =>  $fBodyItFaVal->quantity,
								'unserved_rep_qty'   =>  $available_quantity,
								'unserved_ro_qty'    =>  $reorder
							]);	

							
					    }
					}

				}

			}else{

				$postdata['status_id'] 			= $rejected;
				$postdata['approver_comments'] 	= $approver_comments;
				$postdata['approved_by'] 		= CRUDBooster::myId();
				$postdata['rejected_at'] 		= date('Y-m-d H:i:s');

				for ($i = 0; $i < count($body_ids); $i++) {
					BodyRequest::where('id', $body_ids[$i])
					->update([
						'deleted_at'=> 		date('Y-m-d H:i:s'),
						'deleted_by'=> 		CRUDBooster::myId()
					]);	
			    }
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

			$fields = Request::all();
			$cont = (new static)->apiContext;

			$arf_header = HeaderRequest::where(['id' => $id])->first();

			$approved       =  DB::table('statuses')->where('id', 4)->value('id');
			$rejected       =  DB::table('statuses')->where('id', 5)->value('id');
			$for_tagging    =  DB::table('statuses')->where('id', 7)->value('id');
			$for_move_order =  DB::table('statuses')->where('id', 14)->value('id');

			if($arf_header->status_id  == $approved){
				CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.alert_petty_cash_approve_success",['reference_number'=>$arf_header->reference_number]), 'info');
			}elseif($arf_header->status_id  == $for_tagging){
				CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.alert_petty_cash_approve_success",['reference_number'=>$arf_header->reference_number]), 'info');
			}elseif($arf_header->status_id  == $for_move_order){
				CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.alert_petty_cash_approve_success",['reference_number'=>$arf_header->reference_number]), 'info');
			}else{
				CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.alert_petty_cash_reject_success",['reference_number'=>$arf_header->reference_number]), 'danger');
			}
			
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



	    //By the way, you can still create your own method in here... :) 

		public function getRequestApproval($id){
			$this->cbLoader();
			if(!CRUDBooster::isUpdate() && $this->global_privilege==FALSE) {    
				CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
			}  

			$data = array();

			$data['page_title'] = 'Approve Request';

			$data['Header'] = HeaderRequest::
				  leftjoin('request_type', 'header_request.purpose', '=', 'request_type.id')
				->leftjoin('condition_type', 'header_request.conditions', '=', 'condition_type.id')
				->leftjoin('cms_users as employees', 'header_request.employee_name', '=', 'employees.id')
				->leftjoin('companies', 'header_request.company_name', '=', 'companies.id')
				->leftjoin('departments', 'header_request.department', '=', 'departments.id')
				->leftjoin('positions', 'header_request.position', '=', 'positions.id')
				->leftjoin('locations', 'employees.location_id', '=', 'locations.id')

				->leftjoin('cms_users as requested', 'header_request.created_by','=', 'requested.id')
				->leftjoin('cms_users as approved', 'header_request.approved_by','=', 'approved.id')
				->select(
						'header_request.*',
						'header_request.id as requestid',
						'header_request.created_at as created',
						'request_type.*',
						'condition_type.*',
						'requested.name as requestedby',
						'employees.bill_to as employee_name',
						'employees.company_name_id as company_name',
						'departments.department_name as department',
						//'positions.position_description as position',
						'locations.store_name as store_branch',
						'approved.name as approvedby'
						)
				->where('header_request.id', $id)->first();

				$body = BodyRequest::leftjoin('assets_supplies_inventory', 'body_request.digits_code','=', 'assets_supplies_inventory.digits_code')
				->select(
				  'body_request.*',
				  'assets_supplies_inventory.quantity as wh_qty'
				)
				->where('body_request.header_request_id', $id)
				->whereNull('deleted_at')
				->get();
			$arraySearch = DB::table('assets_inventory_body')->select('digits_code as digits_code',DB::raw('SUM(quantity) as wh_qty'))->where('statuses_id',6)->groupBy('digits_code')->get()->toArray();
			$items = [];
			foreach($body as $itemKey => $itemVal){
				$i = array_search($itemVal->digits_code, array_column($arraySearch,'digits_code'));
				if($i !== false){
					$itemVal->inv_value = $arraySearch[$i];
					$items[] = $itemVal;
				}else{
					$itemVal->inv_value = "";
					$items[] = $itemVal;
				}
			}
			//get reserved qty
			$reservedList = DB::table('assets_inventory_reserved')->select('digits_code as digits_code',DB::raw('SUM(approved_qty) as reserved_qty'))->whereNotNull('reserved')->groupBy('digits_code')->get()->toArray();
			$resultInventory = [];
			foreach($items as $invKey => $invVal){
				$i = array_search($invVal->digits_code, array_column($reservedList,'digits_code'));
				if($i !== false){
					$invVal->reserved_value = $reservedList[$i];
					$resultInventory[] = $invVal;
				}else{
					$invVal->reserved_value = "";
					$resultInventory[] = $invVal;
				}
			}
			//get the final available qty
			$finalInventory = [];
			foreach($resultInventory as $fKey => $fVal){
				$fVal->available_qty = max($fVal->inv_value->wh_qty - $fVal->reserved_value->reserved_qty,0);
				$finalInventory[] = $fVal;
			}

			$data['Body'] = $finalInventory;

			return $this->view("assets.approval-request", $data);
		}

		public function getRequestApprovalSupplies($id){
			$this->cbLoader();
			if(!CRUDBooster::isUpdate() && $this->global_privilege==FALSE) {    
				CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
			}  

			$data = array();

			$data['page_title'] = 'Approve Request';

			$data['Header'] = HeaderRequest::
				  leftjoin('request_type', 'header_request.purpose', '=', 'request_type.id')
				->leftjoin('condition_type', 'header_request.conditions', '=', 'condition_type.id')
				->leftjoin('cms_users as employees', 'header_request.employee_name', '=', 'employees.id')
				->leftjoin('companies', 'header_request.company_name', '=', 'companies.id')
				->leftjoin('departments', 'header_request.department', '=', 'departments.id')
				->leftjoin('positions', 'header_request.position', '=', 'positions.id')
				->leftjoin('locations', 'employees.location_id', '=', 'locations.id')

				->leftjoin('cms_users as requested', 'header_request.created_by','=', 'requested.id')
				->leftjoin('cms_users as approved', 'header_request.approved_by','=', 'approved.id')
				->select(
						'header_request.*',
						'header_request.id as requestid',
						'header_request.created_at as created',
						'request_type.*',
						'condition_type.*',
						'requested.name as requestedby',
						'employees.bill_to as employee_name',
						'employees.company_name_id as company_name',
						'departments.department_name as department',
						//'positions.position_description as position',
						'locations.store_name as store_branch',
						'approved.name as approvedby'
						)
				->where('header_request.id', $id)->first();

			$body = BodyRequest::leftjoin('assets_supplies_inventory', 'body_request.digits_code','=', 'assets_supplies_inventory.digits_code')
				->select(
				  'body_request.*',
				  'assets_supplies_inventory.quantity as wh_qty'
				)
				->where('body_request.header_request_id', $id)
				->whereNull('deleted_at')
				->get();
			$arraySearch = DB::table('assets_inventory_body')->select('digits_code as digits_code',DB::raw('SUM(quantity) as wh_qty'))->where('statuses_id',6)->groupBy('digits_code')->get()->toArray();
			$items = [];
			foreach($body as $itemKey => $itemVal){
				$i = array_search($itemVal->digits_code, array_column($arraySearch,'digits_code'));
				if($i !== false){
					$itemVal->inv_value = $arraySearch[$i];
					$items[] = $itemVal;
				}else{
					$itemVal->inv_value = "";
					$items[] = $itemVal;
				}
			}
			//get reserved qty
			$reservedList = DB::table('assets_inventory_reserved')->select('digits_code as digits_code',DB::raw('SUM(approved_qty) as reserved_qty'))->whereNotNull('reserved')->groupBy('digits_code')->get()->toArray();
			$resultInventory = [];
			foreach($items as $invKey => $invVal){
				$i = array_search($invVal->digits_code, array_column($reservedList,'digits_code'));
				if($i !== false){
					$invVal->reserved_value = $reservedList[$i];
					$resultInventory[] = $invVal;
				}else{
					$invVal->reserved_value = "";
					$resultInventory[] = $invVal;
				}
			}
			//get the final available qty
			$finalInventory = [];
			foreach($resultInventory as $fKey => $fVal){
				$fVal->available_qty = max($fVal->inv_value->wh_qty - $fVal->reserved_value->reserved_qty,0);
				$finalInventory[] = $fVal;
			}

			$data['Body'] = $finalInventory;
	
			return $this->view("assets.approval-request", $data);
		}


	}