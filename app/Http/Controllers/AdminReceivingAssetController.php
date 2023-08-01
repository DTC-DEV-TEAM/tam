<?php namespace App\Http\Controllers;

	use Session;
	use Request;
	use DB;
	use CRUDBooster;
	use App\HeaderRequest;
	use App\BodyRequest;
	use App\ApprovalMatrix;
	use App\StatusMatrix;
	use App\MoveOrder;
	use App\Models\InAssets;
	//use Illuminate\Http\Request;
	//use Illuminate\Support\Facades\Input;
	use Illuminate\Support\Facades\Log;
	use Illuminate\Support\Facades\Redirect;

	class AdminReceivingAssetController extends \crocodicstudio\crudbooster\controllers\CBController {

        public function __construct() {
			// Register ENUM type
			//$this->request = $request;
			DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping("enum", "string");
		}

		private static $apiContext; 

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "mo_reference_number";
			$this->limit = "20";
			$this->orderby = "id,asc";
			$this->global_privilege = false;
			$this->button_table_action = true;
			$this->button_bulk_action = true;
			$this->button_action_style = "button_icon";
			$this->button_add = false;
			$this->button_edit = false;
			$this->button_delete = false;
			$this->button_detail = false;
			$this->button_show = true;
			$this->button_filter = true;
			$this->button_import = false;
			$this->button_export = false;
			$this->table = "mo_body_request";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Status","name"=>"status_id","join"=>"statuses,status_description"];

			$this->col[] = ["label"=>"Reference Number","name"=>"mo_reference_number"];

			$this->col[] = ["label"=>"Request Type","name"=>"header_request_id","join"=>"header_request,request_type_id"];

			$this->col[] = ["label"=>"Employee Name","name"=>"header_request_id","join"=>"header_request,employee_name"];
			$this->col[] = ["label"=>"Department","name"=>"header_request_id","join"=>"header_request,department"];

			$this->col[] = ["label"=>"MO By","name"=>"header_request_id","join"=>"header_request,mo_by"];
			$this->col[] = ["label"=>"MO Date","name"=>"header_request_id","join"=>"header_request,mo_at"];

			$this->col[] = ["label"=>"MO Plug","name"=>"mo_plug","visible"=>false];

			$this->col[] = ["label"=>"To Pick","name"=>"to_pick","visible"=>false];

			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			# END FORM DO NOT REMOVE THIS LINE

			# OLD START FORM
			//$this->form = [];
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

				$this->addaction[] = ['title'=>'Update','url'=>CRUDBooster::mainpath('getRequestReceiving/[id]'),'icon'=>'fa fa-pencil'];

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
	        //$released  = 		DB::table('statuses')->where('id', 12)->value('id');
			$for_receiving =  	DB::table('statuses')->where('id', 16)->value('id');
			if(CRUDBooster::isSuperadmin()){
				$query->where('mo_body_request.status_id', $for_receiving); 
				$List = MoveOrder::orderby('mo_body_request.id', 'asc')->where('mo_body_request.status_id', $for_receiving)->get();
						$list_array = array();
						$id_array = array();
						foreach($List as $matrix){
			
							if(! in_array($matrix->mo_reference_number,$list_array)) {
			
								array_push($list_array, $matrix->mo_reference_number);
								array_push($id_array, $matrix->id);
							}
								
			
						}
			
				$list_string = implode(",",$id_array);
				$MOList = array_map('intval',explode(",",$list_string));	  
				$query->whereIn('mo_body_request.id', $MOList)->orderBy('header_request.id', 'asc');
			}else{
				$query->where('header_request.created_by', CRUDBooster::myId())
				->where('mo_body_request.status_id', $for_receiving); 
				$List = MoveOrder::orderby('mo_body_request.id', 'asc')->where('mo_body_request.status_id', $for_receiving)->get();
						$list_array = array();
						$id_array = array();
						foreach($List as $matrix){
			
							if(! in_array($matrix->mo_reference_number,$list_array)) {
			
								array_push($list_array, $matrix->mo_reference_number);
								array_push($id_array, $matrix->id);
							}
								
			
						}
			
				$list_string = implode(",",$id_array);
				$MOList = array_map('intval',explode(",",$list_string));	  
				$query->whereIn('mo_body_request.id', $MOList)->orderBy('header_request.id', 'asc');
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
			$cancelled  = 		DB::table('statuses')->where('id', 8)->value('status_description');
			$released  = 		DB::table('statuses')->where('id', 12)->value('status_description');
			$processing  = 		DB::table('statuses')->where('id', 11)->value('status_description');
			$closed  = 			DB::table('statuses')->where('id', 13)->value('status_description');
			$received  = 		DB::table('statuses')->where('id', 16)->value('status_description');

			if($column_index == 2){

				if($column_value == $pending){
					$column_value = '<span class="label label-warning">'.$pending.'</span>';
				}else if($column_value == $approved){
					$column_value = '<span class="label label-info">'.$approved.'</span>';
				}else if($column_value == $rejected){
					$column_value = '<span class="label label-danger">'.$rejected.'</span>';
				}else if($column_value == $it_reco){
					$column_value = '<span class="label label-info">'.$it_reco.'</span>';
				}else if($column_value == $cancelled){
					$column_value = '<span class="label label-danger">'.$cancelled.'</span>';
				}else if($column_value == $released){
					$column_value = '<span class="label label-info">'.$released.'</span>';
				}else if($column_value == $processing){
					$column_value = '<span class="label label-info">'.$processing.'</span>';
				}else if($column_value == $closed){
					$column_value = '<span class="label label-success">'.$closed.'</span>';
				}else if($column_value == $received){
					$column_value = '<span class="label label-info">'.$received.'</span>';
				}

			}

			if($column_index == 4){

				$request_type = 			DB::table('requests')->where(['id' => $column_value])->first();
				
				if($column_value == $request_type->id){

					$column_value = $request_type->request_name;

				}


			}

			if($column_index == 5){

				$request_type = 			DB::table('cms_users')->where(['id' => $column_value])->first();
				
				if($column_value == $request_type->id){

					$column_value = $request_type->bill_to;

				}


			}

			if($column_index == 6){

				$request_type = 			DB::table('departments')->where(['id' => $column_value])->first();
				
				if($column_value == $request_type->id){

					$column_value = $request_type->department_name;

				}


			}

			if($column_index == 7){

				$request_type = 			DB::table('cms_users')->where(['id' => $column_value])->first();
				
				if($column_value == $request_type->id){

					$column_value = $request_type->name;

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

			$item_id 					= $fields['item_id'];
			$inventory_id 				= $fields['inventory_id'];

			$HeaderID 					= MoveOrder::where('id', $id)->first();

			$arf_header 				= HeaderRequest::where(['id' => $HeaderID->header_request_id])->first();

		
			if($arf_header->request_type_id == 5){
				$for_closing 				= StatusMatrix::where('current_step', 9)
												->where('request_type', $arf_header->request_type_id)
												//->where('id_cms_privileges', CRUDBooster::myPrivilegeId())
												->value('status_id');
			}else if(in_array($arf_header->request_type_id, [6, 7])){
				//if($arf_header->request_type_id == 5){
					$for_closing 				= StatusMatrix::where('current_step', 10)
													->where('request_type', $arf_header->request_type_id)
													//->where('id_cms_privileges', CRUDBooster::myPrivilegeId())
													->value('status_id');
			}else{
				$for_closing 				= StatusMatrix::where('current_step', 10)
												->where('request_type', $arf_header->request_type_id)
												//->where('id_cms_privileges', CRUDBooster::myPrivilegeId())
												->value('status_id');
			}

			$employee_name = DB::table('cms_users')->where('id', $arf_header->employee_name)->first();
				for($x=0; $x < count((array)$item_id); $x++) {
					if(in_array($arf_header->request_type_id, [1, 5])){
						MoveOrder::where('id',$item_id[$x])
						->update([
							'status_id'=> 	$for_closing
						]);	
						DB::table('assets_inventory_body')->where('id', $inventory_id[$x])
						->update([
							'statuses_id'=> 			3,
							'deployed_to'=> 			$employee_name->bill_to,
							'deployed_by'=> 			CRUDBooster::myId(),
							'deployed_at'=> 			date('Y-m-d H:i:s'),
							'location'=> 				4
						]);
						DB::table('assets_inventory_body')->where('id', $inventory_id[$x])->update(['quantity'=>0]);
					}else{
						MoveOrder::where('id',$item_id[$x])
						->update([
							'status_id'=> 	$for_closing,
							'closed_at'=> 	date('Y-m-d H:i:s')
						]);	
					}
				}	
		    
			
			//if($arf_header->received_by == null){
				if(in_array($arf_header->request_type_id, [1, 5])){
					HeaderRequest::where('id', $arf_header->id)
					->update([
						'status_id'=> 	 	$for_closing,
						'received_by'=> 	CRUDBooster::myId(),
						'received_at'=> 	date('Y-m-d H:i:s')
					]);	
			    }else{
					HeaderRequest::where('id', $arf_header->id)
					->update([
						    'status_id'=> 	 	$for_closing,
							'received_by'=> 	CRUDBooster::myId(),
							'received_at'=> 	date('Y-m-d H:i:s'),
							'closing_plug'=> 1,
							'closed_by'=> CRUDBooster::myId(),
							'closed_at'=> date('Y-m-d H:i:s')
						
					]);	
				}
			//}
            //save in IN assets
			// $inAssets 	= MoveOrder::whereIn('id',$item_id)->get();
	
			// $mo_reference = [];
			// $request_type_id_mo = [];
			// $digits_code = [];
            // $asset_code = [];
            // $item_description = [];
			// $serial_no = [];
			// $quantity = [];
			// $unit_cost = [];
			// foreach($inAssets as $invData){
			// 	array_push($mo_reference, $invData['mo_reference_number']);
			// 	array_push($request_type_id_mo, $invData['request_type_id_mo']);
			// 	array_push($digits_code, $invData['digits_code']);
            //     array_push($asset_code, $invData['asset_code']);
            //     array_push($item_description, $invData['item_description']);
			// 	array_push($serial_no, $invData['serial_no']);
			// 	array_push($quantity, $invData['quantity']);
			// 	array_push($unit_cost, $invData['unit_cost']);
			// }

			// //put in in assets deployed
			// for($x=0; $x < count((array)$item_id); $x++) {
			// 	InAssets::create([
			// 		'arf_number'          => $arf_header->reference_number,
			// 		'mo_ref_number'       => $mo_reference[$x],
			// 		'requestor_id'        => $arf_header->employee_name,
			// 		'requestor_name'      => $employee_name->bill_to,
			// 		'transfer_to'         => NULL,
			// 		'transaction_type'    => $request_type_id_mo[$x],
			// 		'request_type'        => "REQUEST",
			// 		'asset_code'          => $asset_code[$x],
			// 		'digits_code'         => $digits_code[$x],
			// 		'item_description'    => $item_description[$x],
			// 		'serial_no'           => $serial_no[$x],
			// 		'quantity'            => $quantity[$x],
			// 		'amount'              => $unit_cost[$x],
			// 		'date_received'       => date('Y-m-d H:i:s'),
			// 	]);
		    // }

			
			/*$arf_header = 		HeaderRequest::where(['id' => $id])->first();

			if($arf_header->request_type_id == 5){
				
				$postdata['status_id']		 	= 	StatusMatrix::where('current_step', 7)
																->where('request_type', $arf_header->request_type_id)
																//->where('id_cms_privileges', CRUDBooster::myPrivilegeId())
																->value('status_id');

			}else{

				$postdata['status_id']		 	= 	StatusMatrix::where('current_step', 8)
																->where('request_type', $arf_header->request_type_id)
																//->where('id_cms_privileges', CRUDBooster::myPrivilegeId())
																->value('status_id');

			}


														
			$postdata['received_by']		 		=  	CRUDBooster::myId();
			$postdata['received_at']		 		=  	date('Y-m-d H:i:s');

			*/

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

			$mo_request = MoveOrder::where(['id' => $id])->first();

			CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.alert_receiving_success",['reference_number'=>$mo_request->mo_reference_number]), 'info');

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
		public function getRequestReceiving($id){
			

			$this->cbLoader();
			if(!CRUDBooster::isUpdate() && $this->global_privilege==FALSE) {    
				CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
			}  


			$data = array();

			$itemID = array();

			$data['page_title'] = 'Receiving Request';

			$HeaderID = MoveOrder::where('id', $id)->first();

			$data['Header'] = HeaderRequest::
				  leftjoin('request_type', 'header_request.purpose', '=', 'request_type.id')
				->leftjoin('condition_type', 'header_request.conditions', '=', 'condition_type.id')
				->leftjoin('cms_users as employees', 'header_request.employee_name', '=', 'employees.id')
				->leftjoin('companies', 'header_request.company_name', '=', 'companies.id')
				->leftjoin('departments', 'header_request.department', '=', 'departments.id')
				->leftjoin('locations', 'employees.location_id', '=', 'locations.id')
				->leftjoin('cms_users as requested', 'header_request.created_by','=', 'requested.id')
				->leftjoin('cms_users as approved', 'header_request.approved_by','=', 'approved.id')
				->leftjoin('cms_users as recommended', 'header_request.recommended_by','=', 'recommended.id')
				->leftjoin('cms_users as picked', 'header_request.picked_by','=', 'picked.id')
				->leftjoin('cms_users as processed', 'header_request.purchased2_by','=', 'processed.id')
				->leftjoin('cms_users as mo', 'header_request.mo_by','=', 'mo.id')
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
						'locations.store_name as store_branch',
						'approved.name as approvedby',
						'recommended.name as recommendedby',
						'picked.name as pickedby',
						'processed.name as processedby',
						'mo.name as moby',
						'header_request.created_at as created_at'
						)
				->where('header_request.id', $HeaderID->header_request_id)->first();



			$for_receiving =  	DB::table('statuses')->where('id', 16)->value('id');

			$data['MoveOrder'] = MoveOrder::
				select(
				  'mo_body_request.*',
				  'statuses.status_description as status_description'
				)
				->where('mo_body_request.mo_reference_number', $HeaderID->mo_reference_number)
				->where('mo_body_request.status_id', $for_receiving)
				->leftjoin('statuses', 'mo_body_request.status_id', '=', 'statuses.id')
				->orderby('mo_body_request.id', 'desc')
				->get();	

			foreach($data['MoveOrder'] as $value){
				array_push($itemID, $value->body_request_id);
			}

			$item_string = implode(",",$itemID);

			$itemList = array_map('intval',explode(",",$item_string));

			$data['Body'] = BodyRequest::
				select(
				'body_request.*'
				)
				->wherein('body_request.id', $itemList)
				->get();

			$data['HeaderID'] = MoveOrder::where('id', $id)->first();

			return $this->view("assets.receiving-request", $data);
		}

		public function getADFStatus($id){

			//dd($id);

			$data = array();

			$for_receiving =  	DB::table('statuses')->where('id', 16)->value('id');

			$arf_header 				= HeaderRequest::where(['id' => $id])->first();

			if($arf_header->request_type_id == 5){
				$for_closing 				= StatusMatrix::where('current_step', 9)
												->where('request_type', $arf_header->request_type_id)
												//->where('id_cms_privileges', CRUDBooster::myPrivilegeId())
												->value('status_id');
			}else if(in_array($arf_header->request_type_id, [6, 7])){
				//if($arf_header->request_type_id == 5){
					$for_closing 				= StatusMatrix::where('current_step', 10)
													->where('request_type', $arf_header->request_type_id)
													//->where('id_cms_privileges', CRUDBooster::myPrivilegeId())
													->value('status_id');
			}else{
				$for_closing 				= StatusMatrix::where('current_step', 10)
												->where('request_type', $arf_header->request_type_id)
												//->where('id_cms_privileges', CRUDBooster::myPrivilegeId())
												->value('status_id');
			}
			
			$MO_infos =  	MoveOrder::where('mo_body_request.header_request_id', $id)->where('mo_body_request.status_id', $for_receiving)->get();

			$employee_name = DB::table('cms_users')->where('id', $arf_header->employee_name)->first();

			foreach( $MO_infos as $request_value ){
				if(in_array($arf_header->request_type_id, [1, 5])){
					DB::table('assets_inventory_body')->where('id', $request_value->inventory_id)
						->update([
							'statuses_id'=> 			3,
							'deployed_to'=> 			$employee_name->bill_to,
							'deployed_by'=> 			CRUDBooster::myId(),
							'deployed_at'=> 			date('Y-m-d H:i:s'),
							'location'=> 				4
						]);
					DB::table('assets_inventory_body')->where('id', $request_value->inventory_id)->update(['quantity'=>0]);
					MoveOrder::where('id', $request_value->id)
					->update([
						'status_id'=> 	$for_closing
					]);	
			    }else{
					MoveOrder::where('id', $request_value->id)
					->update([
						'status_id'=> 	$for_closing,
						'closed_at'=> 	date('Y-m-d H:i:s')
					]);
				}
			}

			
				if(in_array($arf_header->request_type_id, [1, 5])){
					//if($arf_header->received_by == null){
						HeaderRequest::where('id', $arf_header->id)
						->update([
							'status_id'=> 	 	$for_closing,
							'received_by'=> 	$arf_header->created_by,
							'received_at'=> 	date('Y-m-d H:i:s')
						]);	
			    	//}
			    }else{
					if($arf_header->received_by == null){
						HeaderRequest::where('id',$arf_header->id)
						->update([
								'received_by'=> 	$arf_header->created_by,
								'received_at'=> 	date('Y-m-d H:i:s'),
								'closing_plug'=> 1,
								'status_id'=> 	 	$for_closing,
								'closed_by'=> $arf_header->created_by,
								'closed_at'=> date('Y-m-d H:i:s')
							
						]);	
				    }
				}
			
			

			$data['alertmessage'] = 1;

			return view('crudbooster::login', $data);
			
	
			//CRUDBooster::redirect(CRUDBooster::mainpath(), "Assets has been received successfully!", 'success');

			//return $data;
		}
	}