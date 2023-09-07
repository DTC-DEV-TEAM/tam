<?php namespace App\Http\Controllers;

	use Session;
	use Request;
	use DB;
	use CRUDBooster;
	use App\HeaderRequest;
	use App\BodyRequest;
	use App\ApprovalMatrix;
	use App\StatusMatrix;
	use App\CommentsGoodDefect;
	use App\MoveOrder;
	use App\GoodDefectLists;
	use App\AssetsInventoryBody;
	//use Illuminate\Http\Request;
	//use Illuminate\Support\Facades\Input;
	use Illuminate\Support\Facades\Log;
	use Illuminate\Support\Facades\Redirect;

	class AdminPickingController extends \crocodicstudio\crudbooster\controllers\CBController {

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
			$this->orderby = "id,desc";
			$this->global_privilege = false;
			$this->button_table_action = true;
			$this->button_bulk_action = true;
			$this->button_action_style = "button_icon";
			$this->button_add = false;
			$this->button_edit = false;
			$this->button_delete = true;
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

			/*$this->col[] = ["label"=>"Request Type","name"=>"request_type_id","join"=>"requests,request_name"];
			$this->col[] = ["label"=>"Company Name","name"=>"company_name","join"=>"companies,company_name"];
			$this->col[] = ["label"=>"Employee Name","name"=>"employee_name","join"=>"employees,bill_to"];
			$this->col[] = ["label"=>"Department","name"=>"department","join"=>"departments,department_name"];
			$this->col[] = ["label"=>"Requested By","name"=>"created_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"Requested Date","name"=>"created_at"];
			$this->col[] = ["label"=>"Approved By","name"=>"approved_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"Approved Date","name"=>"approved_at"];

			if(CRUDBooster::myPrivilegeName() == "IT"){ 
				$this->col[] = ["label"=>"Recommended By","name"=>"recommended_by","join"=>"cms_users,name"];
				$this->col[] = ["label"=>"Recommended Date","name"=>"recommended_at"];
			}

			$this->col[] = ["label"=>"Tagged By","name"=>"purchased2_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"Tagged Date","name"=>"purchased2_at"];

			$this->col[] = ["label"=>"MO By","name"=>"mo_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"MO Date","name"=>"mo_at"];
			*/

			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
		
			# END FORM DO NOT REMOVE THIS LINE

			# OLD START FORM
			//$this->form = [];
			//$this->form[] = ['label'=>'Reference Number','name'=>'reference_number','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Status Id','name'=>'status_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'status,id'];
			//$this->form[] = ['label'=>'Employee Name','name'=>'employee_name','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Company Name','name'=>'company_name','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Position','name'=>'position','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Department','name'=>'department','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Store Branch','name'=>'store_branch','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Purpose','name'=>'purpose','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Conditions','name'=>'conditions','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Quantity Total','name'=>'quantity_total','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Cost Total','name'=>'cost_total','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Total','name'=>'total','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Approved By','name'=>'approved_by','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Approved At','name'=>'approved_at','type'=>'datetime','validation'=>'required|date_format:Y-m-d H:i:s','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Created By','name'=>'created_by','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Updated By','name'=>'updated_by','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Rejected At','name'=>'rejected_at','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Requestor Comments','name'=>'requestor_comments','type'=>'textarea','validation'=>'required|string|min:5|max:5000','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Request Type Id','name'=>'request_type_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'request_type,id'];
			//$this->form[] = ['label'=>'Privilege Id','name'=>'privilege_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'privilege,id'];
			//$this->form[] = ['label'=>'Approver Comments','name'=>'approver_comments','type'=>'textarea','validation'=>'required|string|min:5|max:5000','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'To Reco','name'=>'to_reco','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'It Comments','name'=>'it_comments','type'=>'textarea','validation'=>'required|string|min:5|max:5000','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Recommended By','name'=>'recommended_by','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Recommended At','name'=>'recommended_at','type'=>'datetime','validation'=>'required|date_format:Y-m-d H:i:s','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Cancelled By','name'=>'cancelled_by','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Cancelled At','name'=>'cancelled_at','type'=>'datetime','validation'=>'required|date_format:Y-m-d H:i:s','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Purchased1 By','name'=>'purchased1_by','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Purchased1 At','name'=>'purchased1_at','type'=>'datetime','validation'=>'required|date_format:Y-m-d H:i:s','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Purchased2 By','name'=>'purchased2_by','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Purchased2 At','name'=>'purchased2_at','type'=>'datetime','validation'=>'required|date_format:Y-m-d H:i:s','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Purchased3 By','name'=>'purchased3_by','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Purchased3 At','name'=>'purchased3_at','type'=>'datetime','validation'=>'required|date_format:Y-m-d H:i:s','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Quote Date','name'=>'quote_date','type'=>'date','validation'=>'required|date','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Po Date','name'=>'po_date','type'=>'date','validation'=>'required|date','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Po Number','name'=>'po_number','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Employee Dr Date','name'=>'employee_dr_date','type'=>'date','validation'=>'required|date','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Dr Number','name'=>'dr_number','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Received By','name'=>'received_by','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Received At','name'=>'received_at','type'=>'datetime','validation'=>'required|date_format:Y-m-d H:i:s','width'=>'col-sm-10'];
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
				
				//$approved =  		DB::table('statuses')->where('id', 4)->value('id');

				$this->addaction[] = ['title'=>'Update','url'=>CRUDBooster::mainpath('getRequestPicking/[id]'),'icon'=>'fa fa-pencil'];

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
			$this->load_css[] = asset("css/font-family.css");
	        
	        
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

			$for_picking =  	DB::table('statuses')->where('id', 15)->value('id');

			$user_info = 		DB::table('cms_users')->where(['id' => CRUDBooster::myId()])->get();

			$approval_array = array();
			foreach($user_info as $matrix){
				array_push($approval_array, $matrix->location_to_pick);
			}
			$approval_string = implode(",",$approval_array);
			$locationList = array_map('intval',explode(",",$approval_string));


			$List = MoveOrder::whereIn('mo_body_request.location_id', $locationList)->where('mo_body_request.status_id', $for_picking)->orderby('mo_body_request.id', 'asc')->get();

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
          
	        //Your code here
	        // if(CRUDBooster::myPrivilegeId() == 5){ 

			// 	$for_picking =  	DB::table('statuses')->where('id', 15)->value('id');

			// 	$query->where('mo_body_request.status_id', $for_picking)
			// 		  ->where('mo_body_request.to_reco', 1)
			// 		  ->where('mo_body_request.to_pick', 0)
			// 		  ->orderBy('mo_body_request.id', 'ASC');

			// }else if(CRUDBooster::myPrivilegeId() == 6){ 

			// 	$for_picking =  	DB::table('statuses')->where('id', 15)->value('id');

			// 	$query->where('mo_body_request.status_id', $for_picking)
			// 		->where('mo_body_request.to_reco', 0)
			// 		->where('mo_body_request.to_pick', 0)
			// 		->orderBy('mo_body_request.id', 'ASC');

			// }else{
				
				$for_picking =  	DB::table('statuses')->where('id', 15)->value('id');

				$query->where('mo_body_request.status_id', $for_picking)
					  ->where('mo_body_request.to_pick', 0)
				      ->orderBy('mo_body_request.id', 'ASC');

			//}

			$query->whereIn('mo_body_request.id', $MOList);
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
			$for_picklist =  	DB::table('statuses')->where('id', 14)->value('status_description');
			$for_picking =  	DB::table('statuses')->where('id', 15)->value('status_description');
		

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
				}else if($column_value == $for_picklist){
					$column_value = '<span class="label label-info">'.$for_picklist.'</span>';
				}else if($column_value == $for_picking){
					$column_value = '<span class="label label-info">'.$for_picking.'</span>';
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

			$cont = (new static)->apiContext;

			$item_id 					= $fields['item_id'];

			//$pick_value 				= $fields['pick_value'];

			$good_text 					= $fields['good_text'];

			$defective_text 			= $fields['defective_text'];
 
			//good and defect value
			$arf_number     = $fields['arf_number'];
			$digits_code    = $fields['digits_code'];
			$asset_code     = $fields['asset_code'];
			$comments       = $fields['comments'];
			$other_comment  = $fields['other_comment'];
			$asset_code_tag = $fields['asset_code_tag'];
			$body_id        = $fields['body_id'];
         
			$HeaderID 					= MoveOrder::where('id', $id)->first();

			//dd($fields);

			$arf_header    = HeaderRequest::where(['id' => $HeaderID->header_request_id])->first();
			$employee_name = DB::table('cms_users')->where('id', $arf_header->employee_name)->first();
			if(in_array($arf_header->request_type_id, [5, 6, 7])){
			//if($arf_header->request_type_id == 5){
				$for_receiving 				= StatusMatrix::where('current_step', 7)
												->where('request_type', $arf_header->request_type_id)
												//->where('id_cms_privileges', CRUDBooster::myPrivilegeId())
												->value('status_id');
			}else{
				$for_receiving 				= StatusMatrix::where('current_step', 8)
												->where('request_type', $arf_header->request_type_id)
												//->where('id_cms_privileges', CRUDBooster::myPrivilegeId())
												->value('status_id');
			}


			for($x=0; $x < count((array)$item_id); $x++) {

				//if($item_id[$x] == 1){

				// if($defective_text[$x] == 1){

				// 	$cancelled  = 		DB::table('statuses')->where('id', 8)->value('id');
				// 	$inventoryDetails = AssetsInventoryBody::where('id',$asset_code_tag[$x])->first();
					
				// 	MoveOrder::where('id',$item_id[$x])
				// 	->update([
				// 		'item_id'         => $inventoryDetails->item_id,
				// 		'inventory_id'    => $inventoryDetails->id,
				// 		'asset_code'      => $inventoryDetails->asset_code,
				// 		'serial_no'       => $inventoryDetails->serial_no,
				// 		'unit_cost'       => $inventoryDetails->value,
				// 		'total_unit_cost' => $inventoryDetails->value,
				// 		'status_id'       => $cancelled,
				// 		'to_pick'         => 1,
				// 		'good'            => $good_text[$x],
				// 		'defective'       => $defective_text[$x]
				// 	]);	

				// 	$mo_info 	= 		MoveOrder::where('id',$item_id[$x])->first();

				// 	// HeaderRequest::where('id', $arf_header->id)
				// 	// ->update([
				// 	// 	'to_mo'=> 	1
				// 	// ]);	

				// 	// BodyRequest::where('id', $mo_info->body_request_id)
				// 	// ->update([
				// 	// 	'to_mo'=> 	1
				// 	// ]);	

				// 	BodyRequest::where('id', $body_id[$x])
				// 	->update(
				// 				[
				// 				'serve_qty'         => 1, 
				// 				'unserved_rep_qty'  => DB::raw("unserved_rep_qty - 1"), 
				// 				'unserved_ro_qty'   => DB::raw("unserved_ro_qty - 1"), 
				// 				'unserved_qty'      => DB::raw("unserved_qty - 1"),
				// 				'cancelled_qty'     => 1,
				// 				'reason_to_cancel'  => 'DEFECTIVE'          
				// 				]
				// 			);

				// 	DB::table('assets_inventory_reserved')->where('body_id', $mo_info->body_request_id)->delete();

				// 	DB::table('assets_inventory_body')->where('id', $mo_info->inventory_id)
				// 	->update([
				// 		'statuses_id'=> 			23,
				// 		'item_condition'=> 			"Defective"
						
				// 	]);


				// }else{
					$inventoryDetails = AssetsInventoryBody::where('id',$asset_code_tag[$x])->first();

					MoveOrder::where('id',$item_id[$x])
					->update([
						'item_id'         => $inventoryDetails->item_id,
						'inventory_id'    => $inventoryDetails->id,
						'asset_code'      => $inventoryDetails->asset_code,
						'serial_no'       => $inventoryDetails->serial_no,
						'unit_cost'       => $inventoryDetails->value,
						'total_unit_cost' => $inventoryDetails->value,
						'status_id'       => $for_receiving,
						'to_pick'         => 1,
						'good'            => $good_text[$x],
						'defective'       => $defective_text[$x]
					]);	

					BodyRequest::where('id', $body_id[$x])
					->update(
								[
								'serve_qty'        => 1, 
								'unserved_rep_qty' => DB::raw("unserved_rep_qty - 1"), 
								'unserved_ro_qty'  => DB::raw("unserved_ro_qty - 1"), 
								'unserved_qty'     => DB::raw("unserved_qty - 1"),      
								'dr_qty'           => 1,
								'mo_so_num'        => $HeaderID->mo_reference_number       
								]
							);

					DB::table('assets_inventory_body')->where('id', $asset_code_tag[$x])
					->update([
						'statuses_id'=> 2,
						'deployed_to'=> $employee_name->bill_to
					]);

					DB::table('assets_inventory_reserved')->where('body_id', $body_id[$x])->delete();

				//}
				//}
			}


			MoveOrder::where('header_request_id', $arf_header->id)
			->update([
				'to_print'=> 	1
			]);	

			if($arf_header->picked_by == null){
				HeaderRequest::where('id', $arf_header->id)
				->update([
					'picked_by'=> 	CRUDBooster::myId(),
					'picked_at'=> 	date('Y-m-d H:i:s')
				]);	
			}

			$body_request = BodyRequest::where(['header_request_id' => $arf_header->id])->whereNull('deleted_at')->count();
			$mo_request   = MoveOrder::where(['header_request_id' => $arf_header->id])->where('status_id', '!=', 8)->count();

			if($body_request == $mo_request){
				HeaderRequest::where('id',$arf_header->id)
				->update([
					'status_id'      => $for_receiving,
				]);	
			}

			//save defect and good comments
			// $invACode = array();
			// foreach($item_id as $code){
			// 	array_push($invACode, $code);
			// }
			// $searchCode = implode(",",$invACode);
			// $searchCodeFinal = array_map('intval',explode(",",$searchCode));
			// $inventoryDetailsDefect = MoveOrder::whereIn('id',$searchCodeFinal)->where('defective',1)->get();
			// $assetCode = [];

			// foreach($inventoryDetailsDefect as $asset_code){
            //   array_push($assetCode, $asset_code->asset_code);
			// }
			
			// $container = [];
			// $containerSave = [];
			// foreach((array)$comments as $key => $val){
			// 	$container['arf_number'] = $arf_number;
			// 	$container['digits_code'] = explode("|",$val)[1];
			// 	$container['asset_code'] = $assetCode[$key];
			// 	$container['comments'] = explode("|",$val)[2];
			// 	$container['users'] = CRUDBooster::myId();
			// 	$container['created_at'] = date('Y-m-d H:i:s');
			// 	$containerSave[] = $container;
			// }
			// $otherCommentContainer = [];
			// $otherCommentFinalData = [];
			// foreach((array)$assetCode as $aKey => $aVal){
			// 	$otherCommentContainer['asset_code'] = $aVal;
			// 	$otherCommentContainer['digits_code'] = $digits_code[$aKey];
			// 	$otherCommentContainer['other_comment'] = $other_comment[$aKey];
			// 	$otherCommentFinalData[] = $otherCommentContainer;
			// }
			
			// //search other comment in another array
			// $finalData = [];
			// foreach((array)$containerSave as $csKey => $csVal){
			// 	$i = array_search($csVal['asset_code'], array_column($otherCommentFinalData,'asset_code'));
			// 	if($i !== false){
			// 		$csVal['other_comment'] = $otherCommentFinalData[$i];
			// 		$finalData[] = $csVal;
			// 	}else{
			// 		$csVal['other_comment'] = "";
			// 		$finalData[] = $csVal;
			// 	}
			// }
			
			// $finalContainerSave = [];
			// $finalContainer = [];
			// foreach((array)$finalData as $key => $val){
			// 	$finalContainer['arf_number'] = $val['arf_number'];
			// 	$finalContainer['digits_code'] = $val['digits_code'];
			// 	$finalContainer['asset_code'] = $val['asset_code'];
			// 	$finalContainer['comments'] = $val['comments'];
			// 	$finalContainer['other_comment'] = $val['other_comment'] ? $val['other_comment']['other_comment'] : $val['other_comment'];
			// 	$finalContainer['users'] = $val['users'];
			// 	$finalContainer['created_at'] = $val['created_at'];
			// 	$finalContainerSave[] = $finalContainer;
			// }
	
			// CommentsGoodDefect::insert($finalContainerSave);

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
			//$fields = Request::all();
			//$cont = (new static)->apiContext;

			$mo_request = MoveOrder::where(['id' => $id])->first();

			CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.arf_picked_success",['reference_number'=>$mo_request->mo_reference_number]), 'info');

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

		public function getRequestPicking($id){
			

			$this->cbLoader();
			if(!CRUDBooster::isUpdate() && $this->global_privilege==FALSE) {    
				CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
			}  


			$data = array();

			$data['page_title'] = 'Picking Request';

			$HeaderID = MoveOrder::where('id', $id)->first();

			$user_info = 		DB::table('cms_users')->where(['id' => CRUDBooster::myId()])->get();


			$approval_array = array();
			foreach($user_info as $matrix){
				array_push($approval_array, $matrix->location_to_pick);
			}
			$approval_string = implode(",",$approval_array);
			$locationList = array_map('intval',explode(",",$approval_string));

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
						//'locations.store_name as store_branch',
						'approved.name as approvedby',
						'recommended.name as recommendedby',
						'header_request.created_at as created_at'
						)
				->where('header_request.id', $HeaderID->header_request_id)->first();

			$data['MoveOrder'] = MoveOrder::
				select(
				  'mo_body_request.*',
				  'statuses.status_description as status_description'
				)
				->where('mo_body_request.mo_reference_number', $HeaderID->mo_reference_number)
				->where('mo_body_request.to_pick', 0)
				->whereIn('mo_body_request.location_id', $locationList)
				->leftjoin('statuses', 'mo_body_request.status_id', '=', 'statuses.id')
				->orderby('mo_body_request.id', 'desc')
				->get();	
			$arrayDigitsCode = [];
            foreach($data['MoveOrder'] as $codes) {
				$digits_code['digits_code'] = $codes['digits_code'];
				$asset_code['asset_code'] = $codes['asset_code'];
				array_push($arrayDigitsCode, $codes['digits_code']);
			}
			$data['HeaderID'] = MoveOrder::where('id', $id)->first();

			// $data['comments'] = CommentsGoodDefect::
			// leftjoin('cms_users', 'comments_good_defect_tbl.users', '=', 'cms_users.id')
			// ->select(
			// 	'comments_good_defect_tbl.*',
			// 	'comments_good_defect_tbl.id as bodyId',
			// 	'cms_users.name'
			//   )
			//   ->where('comments_good_defect_tbl.digits_code', $digits_code['digits_code'])
			//   ->where('comments_good_defect_tbl.asset_code', $asset_code['asset_code'])
			//   ->get();
			
			$data['good_defect_lists'] = GoodDefectLists::all();
			if(in_array(CRUDBooster::myPrivilegeId(),[5,17])){
			    $data['assets_code'] = AssetsInventoryBody::select('asset_code as asset_code','id as id','digits_code as digits_code')->where('statuses_id',6)->whereIn('digits_code', $arrayDigitsCode)->get();
			}else{
				$data['assets_code'] = AssetsInventoryBody::select('asset_code as asset_code','id as id','digits_code as digits_code')->where('statuses_id',6)->whereIn('item_category', ['FIXED ASSETS','FIXED ASSET'])->whereIn('digits_code', $arrayDigitsCode)->get();
			}
			return $this->view("assets.picking-request", $data);
		}


	}