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
	//use Illuminate\Http\Request;
	//use Illuminate\Support\Facades\Input;
	use Illuminate\Support\Facades\Log;
	use Illuminate\Support\Facades\Redirect;

	class AdminForPurchasingController extends \crocodicstudio\crudbooster\controllers\CBController {

        public function __construct() {
			// Register ENUM type
			//$this->request = $request;
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
			$this->col[] = ["label"=>"Company Name","name"=>"company_name","join"=>"companies,company_name"];
			$this->col[] = ["label"=>"Employee Name","name"=>"employee_name","join"=>"employees,bill_to"];
			$this->col[] = ["label"=>"Department","name"=>"department","join"=>"departments,department_name"];
			$this->col[] = ["label"=>"Requested By","name"=>"created_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"Requested Date","name"=>"created_at"];
			$this->col[] = ["label"=>"Approved By","name"=>"approved_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"Approved Date","name"=>"approved_at"];
			$this->col[] = ["label"=>"Recommended By","name"=>"recommended_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"Recommended Date","name"=>"recommended_at"];
			$this->col[] = ["label"=>"Processed By","name"=>"purchased2_by","join"=>"cms_users,name", "visible"=>false];

			$this->col[] = ["label"=>"MO By","name"=>"mo_by","visible"=>false];
			
			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
		
			# END FORM DO NOT REMOVE THIS LINE

			# OLD START FORM
			//$this->form = [];
			//$this->form[] = ["label"=>"Reference Number","name"=>"reference_number","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Status Id","name"=>"status_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"status,id"];
			//$this->form[] = ["label"=>"Employee Name","name"=>"employee_name","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Company Name","name"=>"company_name","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Position","name"=>"position","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Department","name"=>"department","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Store Branch","name"=>"store_branch","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Purpose","name"=>"purpose","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Conditions","name"=>"conditions","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Quantity Total","name"=>"quantity_total","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Cost Total","name"=>"cost_total","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Total","name"=>"total","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Approved By","name"=>"approved_by","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Approved At","name"=>"approved_at","type"=>"datetime","required"=>TRUE,"validation"=>"required|date_format:Y-m-d H:i:s"];
			//$this->form[] = ["label"=>"Created By","name"=>"created_by","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Updated By","name"=>"updated_by","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Rejected At","name"=>"rejected_at","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Requestor Comments","name"=>"requestor_comments","type"=>"textarea","required"=>TRUE,"validation"=>"required|string|min:5|max:5000"];
			//$this->form[] = ["label"=>"Request Type Id","name"=>"request_type_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"request_type,id"];
			//$this->form[] = ["label"=>"Privilege Id","name"=>"privilege_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"privilege,id"];
			//$this->form[] = ["label"=>"Approver Comments","name"=>"approver_comments","type"=>"textarea","required"=>TRUE,"validation"=>"required|string|min:5|max:5000"];
			//$this->form[] = ["label"=>"To Reco","name"=>"to_reco","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"It Comments","name"=>"it_comments","type"=>"textarea","required"=>TRUE,"validation"=>"required|string|min:5|max:5000"];
			//$this->form[] = ["label"=>"Recommended By","name"=>"recommended_by","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Recommended At","name"=>"recommended_at","type"=>"datetime","required"=>TRUE,"validation"=>"required|date_format:Y-m-d H:i:s"];
			//$this->form[] = ["label"=>"Cancelled By","name"=>"cancelled_by","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Cancelled At","name"=>"cancelled_at","type"=>"datetime","required"=>TRUE,"validation"=>"required|date_format:Y-m-d H:i:s"];
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

				$processing  = 		DB::table('statuses')->where('id', 11)->value('id');

				$picked =  			DB::table('statuses')->where('id', 15)->value('id');

				$this->addaction[] = ['title'=>'Update','url'=>CRUDBooster::mainpath('getRequestPurchasing/[id]'),'icon'=>'fa fa-pencil' , "showIf"=>"[purchased2_by] == null"];
				

				$this->addaction[] = ['title'=>'Detail','url'=>CRUDBooster::mainpath('getDetailPurchasing/[id]'),'icon'=>'fa fa-eye', "showIf"=>"[mo_by] != null"];

				//$this->addaction[] = ['title'=>'Print','url'=>CRUDBooster::mainpath('getRequestPrintPickList/[id]'),'icon'=>'fa fa-print', "showIf"=>"[purchased2_by] != null && [status_id] == $processing"];
				
				//$this->addaction[] = ['title'=>'Print','url'=>CRUDBooster::mainpath('getRequestPrint/[id]'),'icon'=>'fa fa-print', "showIf"=>"[status_id] == $picked"];

				//$this->addaction[] = ['title'=>'Print','url'=>CRUDBooster::mainpath('getRequestPrint/[id]'),'icon'=>'fa fa-print', "showIf"=>"[purchased2_by] != null"];
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

				$query->where(function($sub_query){
				

					$approved =  		DB::table('statuses')->where('id', 7)->value('id');

					$it_reco  = 		DB::table('statuses')->where('id', 4)->value('id');

					$processing = 		DB::table('statuses')->where('id', 11)->value('id');

					$picked =  			DB::table('statuses')->where('id', 15)->value('id');

					$sub_query->where('header_request.to_reco', 0)->where('header_request.status_id', $approved)->whereNull('header_request.deleted_at'); 
					$sub_query->orwhere('header_request.to_reco', 1)->where('header_request.status_id', $approved)->whereNull('header_request.deleted_at');
					$sub_query->orwhere('header_request.status_id', $processing)->whereNull('header_request.deleted_at');
					$sub_query->orwhere('header_request.status_id', $picked)->whereNull('header_request.deleted_at');

				});

				$query->orderBy('header_request.status_id', 'asc')->orderBy('header_request.id', 'DESC');
			
			}else{

				$query->where(function($sub_query){

					$approved =  		DB::table('statuses')->where('id', 7)->value('id');

					$it_reco  = 		DB::table('statuses')->where('id', 4)->value('id');

					$processing = 		DB::table('statuses')->where('id', 11)->value('id');

					$picked =  			DB::table('statuses')->where('id', 15)->value('id');

					$sub_query->where('header_request.to_reco', 0)->where('header_request.status_id', $approved)->whereNull('header_request.deleted_at')->whereNull('mo_by'); 
					$sub_query->orwhere('header_request.to_reco', 1)->where('header_request.status_id', $approved)->whereNull('header_request.deleted_at')->whereNull('mo_by');
					$sub_query->orwhere('header_request.status_id', $processing)->whereNull('header_request.deleted_at')->whereNull('mo_by');
					$sub_query->orwhereNotNull('header_request.purchased2_by')->where('header_request.closing_plug', 0)->whereNull('mo_by');

					//$sub_query->orwhere('header_request.status_id', $picked)->whereNull('header_request.deleted_at');
				});

				$query->orderBy('header_request.status_id', 'desc')->orderBy('header_request.id', 'asc');

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
			$processing  = 		DB::table('statuses')->where('id', 11)->value('status_description');
			$picked =  			DB::table('statuses')->where('id', 15)->value('status_description');
			$for_printing =  	DB::table('statuses')->where('id', 17)->value('status_description');
			$for_move_order =  	DB::table('statuses')->where('id', 14)->value('status_description');
			$for_receiving =  	DB::table('statuses')->where('id', 16)->value('status_description');

			$for_printing_adf = DB::table('statuses')->where('id', 18)->value('status_description');

			$for_closing  = 	DB::table('statuses')->where('id', 19)->value('status_description');

			$closed  = 			DB::table('statuses')->where('id', 13)->value('status_description');

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
				}else if($column_value == $processing){
					$column_value = '<span class="label label-info">'.$processing.'</span>';
				}else if($column_value == $picked){
					$column_value = '<span class="label label-info">'.$picked.'</span>';
				}elseif($column_value == $for_printing){
					$column_value = '<span class="label label-info">'.$for_printing.'</span>';
				}elseif($column_value == $for_move_order){
					$column_value = '<span class="label label-info">'.$for_move_order.'</span>';
				}elseif($column_value == $for_receiving){

					$column_value = '<span class="label label-info">'.$for_receiving.'</span>';

				}elseif($column_value == $for_printing_adf){

					$column_value = '<span class="label label-info">'.$for_printing_adf.'</span>';

				}elseif($column_value == $for_closing){
					$column_value = '<span class="label label-info">'.$for_closing.'</span>';
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
			$arf_header = HeaderRequest::where(['id' => $id])->first();

			$user_data         = DB::table('cms_users')->where('employee_id', $arf_header->employee_name)->first();

			$fields = Request::all();
			$cont = (new static)->apiContext;

			$dataLines = array();

			$po_number 				= $fields['po_number'];
			$po_date 				= $fields['po_date'];
			$dr_number 				= $fields['dr_number'];
			$employee_dr_date 		= $fields['employee_dr_date'];
			$quote_date 			= $fields['quote_date'];
			$action 				= $fields['action'];
			$quantity_total 		= $fields['quantity_total'];
			//$cost_total 			= $fields['cost_total'];
			$total 					= $fields['total'];
			$ac_comments 			= $fields['ac_comments'];

			$ids 					= $fields['ids'];

			$recommendation 			= $fields['recommendation'];
			$reco_digits_code 			= $fields['reco_digits_code'];
			$reco_item_description 		= $fields['reco_item_description'];


			$postdata['ac_comments'] 		= $ac_comments;
			$postdata['po_number'] 			= $po_number;
			$postdata['po_date'] 			= $po_date;
			$postdata['dr_number'] 			= $dr_number;
			$postdata['employee_dr_date'] 	= $employee_dr_date;
			$postdata['quote_date'] 		= $quote_date;
			$postdata['purchased1_by'] 		= CRUDBooster::myId();
			$postdata['purchased1_at'] 		= date('Y-m-d H:i:s');

			$processing 			 		= DB::table('statuses')->where('id', 11)->value('id');

			//$postdata['status_id'] 			 = $processing;
			if($arf_header->request_type_id == 5){

				$postdata['status_id']		 			=	 StatusMatrix::where('current_step', 3)
																		   ->where('request_type', $arf_header->request_type_id)
																		   //->where('id_cms_privileges', CRUDBooster::myPrivilegeId())
																		   ->value('status_id');

			}else{

				$postdata['status_id']		 			=	 StatusMatrix::where('current_step', 4)
																		   ->where('request_type', $arf_header->request_type_id)
																		   //->where('id_cms_privileges', CRUDBooster::myPrivilegeId())
																		   ->value('status_id');

			}


			for($x=0; $x < count((array)$ids); $x++) {

				BodyRequest::where('id',	$ids[$x])
				->update([
					'recommendation'=> 				$recommendation[$x],
					'reco_digits_code'=> 			$reco_digits_code[$x],
					'reco_item_description'=> 		$reco_item_description[$x]
				]);	

			}

			/*

			$digits_code 			= $fields['digits_code'];
			$item_description 		= $fields['item_description'];
			$serial_no 				= $fields['serial_no'];
			$asset_tag 				= $fields['asset_tag'];
			$quantity 				= $fields['quantity'];
			$unit_cost 				= $fields['unit_cost'];
			$total_unit_cost 		= $fields['total_unit_cost'];
			$item_id 				= $fields['item_id'];
			$category_id 			= $fields['category_id'];

			if(count((array)$digits_code) != 0){

				$postdata['quantity_total'] 	= $quantity_total;
				$postdata['total'] 				= $total;


				for($x=0; $x < count((array)$digits_code); $x++) {

				

					$dataLines[$x]['header_request_id'] 	= $arf_header->id;
					$dataLines[$x]['digits_code'] 			= $digits_code[$x];
					$dataLines[$x]['item_description'] 		= $item_description[$x];
					$dataLines[$x]['serial_no'] 			= $serial_no[$x];
					$dataLines[$x]['asset_code'] 			= $asset_tag[$x];
					$dataLines[$x]['quantity'] 				= $quantity[$x];
					$dataLines[$x]['unit_cost'] 			= $unit_cost[$x];
					$dataLines[$x]['total_unit_cost'] 		= $total_unit_cost[$x];
					$dataLines[$x]['item_id'] 				= $item_id[$x];
					$dataLines[$x]['category_id'] 			= $category_id[$x];
					$dataLines[$x]['created_at'] 			= date('Y-m-d H:i:s');


						
					DB::table('assets')->where('id', $item_id[$x])
					->update([
						'status_id1'=> 			2,
						'released_by'=> 		CRUDBooster::myId(),
						'released_date'=> 		date('Y-m-d H:i:s'),
						'assign_by'=> 			$user_data->id,
						'assign_date'=> 		date('Y-m-d H:i:s')
					]);	

					//DB::table('assets')->where('id', $item_id[$x])->decrement('quantity');
					//DB::table('assets')->where('id', $item_id[$x])->decrement('total_quantity');

				}

			}

			DB::beginTransaction();
	
			try {
				BodyRequest::insert($dataLines);
				DB::commit();
				//CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.alert_pullout_data_success",['mps_reference'=>$pullout_header->reference]), 'success');
			} catch (\Exception $e) {
				DB::rollback();


				CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.alert_database_error",['database_error'=>$e]), 'danger');
			}
			*/

			if($action == 1){

				$postdata['purchased2_by'] 		= CRUDBooster::myId();
				$postdata['purchased2_at'] 		= date('Y-m-d H:i:s');




				if($arf_header->request_type_id == 5){

					$postdata['status_id']		 	=	StatusMatrix::where('current_step', 4)
					->where('request_type', $arf_header->request_type_id)
					//->where('id_cms_privileges', CRUDBooster::myPrivilegeId())
					->value('status_id');

				}else{

					$postdata['status_id']		 	=	StatusMatrix::where('current_step', 5)
					->where('request_type', $arf_header->request_type_id)
					//->where('id_cms_privileges', CRUDBooster::myPrivilegeId())
					->value('status_id');

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

			$action 				= $fields['action'];

			$arf_header = HeaderRequest::where(['id' => $id])->first();

			if($action == 1){
				
				/*

				$items = BodyRequest::where('header_request_id',$id)->where('digits_code', '!=', null)->get();

				foreach($items as $item_Value){
	
					$assets = DB::table('assets')->where('assets.id', $item_Value->item_id)
								->join('category', 'assets.category_id','=', 'category.id')
								->join('digits_imfs', 'assets.digits_code','=', 'digits_imfs.id')
								->leftjoin('cms_users as assigned', 'assets.assign_by','=', 'assigned.id')
								->select(	'assets.*',
											'assets.id as assetID',
											'digits_imfs.digits_code as dcode',
											'category.category_description as category_description',
											'assigned.name as assignedby'
										)->first();
	
					$data = [	'assign_to'=>$assets->name,
								'asset_tag'=>$assets->asset_tag,
								'digits_code'=>$assets->dcode,
								'serial_no'=>$assets->serial_no,
								'item_description'=>$assets->item_description,
								'category_id'=>$assets->category_description,
								'assign_date'=>$assets->assign_date,
								'assign_by'=>$assets->assignedby
					]; 
	
				}
	
		
				//CRUDBooster::sendEmail(['to'=>'rickyalnin201995@gmail.com','data'=>$data,'template'=>'assets_confirmation','attachments'=>$files]);


				return redirect()->action('AdminForPurchasingController@getRequestPrintPickList',['id'=>$arf_header->id])->send();
				exit;
				*/

				CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.arf_proceed_success",['reference_number'=>$arf_header->reference_number]), 'info');

			}else{


				CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.arf_purchasing_success",['reference_number'=>$arf_header->reference_number]), 'info');
			
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
		public function getRequestPurchasing($id){
			

			$this->cbLoader();
			if(!CRUDBooster::isUpdate() && $this->global_privilege==FALSE) {    
				CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
			}  


			$data = array();

			$data['page_title'] = 'Processing Request';

			$data['Header'] = HeaderRequest::
				  leftjoin('request_type', 'header_request.purpose', '=', 'request_type.id')
				->leftjoin('condition_type', 'header_request.conditions', '=', 'condition_type.id')
				->leftjoin('employees', 'header_request.employee_name', '=', 'employees.id')
				->leftjoin('companies', 'header_request.company_name', '=', 'companies.id')
				->leftjoin('departments', 'header_request.department', '=', 'departments.id')
				->leftjoin('positions', 'header_request.position', '=', 'positions.id')
				->leftjoin('stores', 'header_request.store_branch', '=', 'stores.id')
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
						'companies.company_name as company_name',
						'departments.department_name as department',
						//'positions.position_description as position',
						'stores.bea_mo_store_name as store_branch',
						'approved.name as approvedby',
						'recommended.name as recommendedby',
						'header_request.created_at as created_at'
						)
				->where('header_request.id', $id)->first();

			$data['Body'] = BodyRequest::
				select(
				  'body_request.*'
				)
				->where('body_request.header_request_id', $id)
				->whereNull('deleted_at')
				->get();

			$data['BodyReco'] = DB::table('recommendation_request')
				->select(
				  'recommendation_request.*'
				)
				->where('recommendation_request.header_request_id', $id)
				->get();				

			$data['recommendations'] = DB::table('recommendations')->where('status', 'ACTIVE')->get();

			return $this->view("assets.purchasing-request", $data);
		}

		public function getRequestPrint($id){
			

			$this->cbLoader();
			if(!CRUDBooster::isUpdate() && $this->global_privilege==FALSE) {    
				CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
			}  


			$data = array();

			$data['page_title'] = 'Print Request';

			$data['Header'] = HeaderRequest::
				  leftjoin('request_type', 'header_request.purpose', '=', 'request_type.id')
				->leftjoin('condition_type', 'header_request.conditions', '=', 'condition_type.id')
				->leftjoin('employees', 'header_request.employee_name', '=', 'employees.id')
				->leftjoin('companies', 'header_request.company_name', '=', 'companies.id')
				->leftjoin('departments', 'header_request.department', '=', 'departments.id')
				->leftjoin('positions', 'header_request.position', '=', 'positions.id')
				->leftjoin('stores', 'header_request.store_branch', '=', 'stores.id')
				->leftjoin('cms_users as requested', 'header_request.created_by','=', 'requested.id')
				->leftjoin('cms_users as approved', 'header_request.approved_by','=', 'approved.id')
				->leftjoin('cms_users as recommended', 'header_request.recommended_by','=', 'recommended.id')
				->leftjoin('cms_users as processed', 'header_request.purchased2_by','=', 'processed.id')
				->leftjoin('cms_users as picked', 'header_request.picked_by','=', 'picked.id')
				->select(
						'header_request.*',
						'header_request.id as requestid',
						'request_type.*',
						'condition_type.*',
						'requested.name as requestedby',
						'employees.bill_to as employee_name',
						'companies.company_name as company_name',
						'departments.department_name as department',
						'positions.position_description as position',
						'stores.bea_mo_store_name as store_branch',
						'approved.name as approvedby',
						'recommended.name as recommendedby',
						'processed.name as processedby',
						'picked.name as pickedby',
						'header_request.created_at as created_at'
						)
				->where('header_request.id', $id)->first();

			$data['Body'] = BodyRequest::
				select(
				  'body_request.*'
				)
				->where('body_request.header_request_id', $id)
				->orderby('body_request.id', 'desc')
				->get();

			$data['BodyReco'] = DB::table('recommendation_request')
				->select(
				  'recommendation_request.*'
				)
				->where('recommendation_request.header_request_id', $id)
				->get();				

			$data['recommendations'] = DB::table('recommendations')->where('status', 'ACTIVE')->get();

			return $this->view("assets.print-request", $data);
		}

		public function ARFUpdate(){

			$data = 			Request::all();	

			$cont = (new static)->apiContext;

			$requestid = 		$data['requestid']; 

			$arf_header = 		HeaderRequest::where(['id' => $requestid])->first();

			$released =  		DB::table('statuses')->where('id', 12)->value('id');

			if($arf_header->request_type_id == 5){

				HeaderRequest::where('id',	$requestid)
				->update([
					'status_id'=> 			StatusMatrix::where('current_step', 6)
											->where('request_type', $arf_header->request_type_id)
											//->where('id_cms_privileges', CRUDBooster::myPrivilegeId())
											->value('status_id'),

					'purchased3_by'=> 		CRUDBooster::myId(),
					'purchased3_at'=> 		date('Y-m-d H:i:s')
				]);	

			}else{

				HeaderRequest::where('id',	$requestid)
				->update([
					'status_id'=> 			StatusMatrix::where('current_step', 7)
											->where('request_type', $arf_header->request_type_id)
											//->where('id_cms_privileges', CRUDBooster::myPrivilegeId())
											->value('status_id'),

					'purchased3_by'=> 		CRUDBooster::myId(),
					'purchased3_at'=> 		date('Y-m-d H:i:s')
				]);	

			}




			$arf_body = BodyRequest::where(['header_request_id' => $requestid])->get();


			foreach($arf_body as $arf_value){

				DB::table('assets')->where('id', $arf_value->item_id)
				->update([

					'status_id1'=> 			3,
					'released_by'=> 		CRUDBooster::myId(),
					'released_date'=> 		date('Y-m-d H:i:s')
					//'assign_by'=> 			$user_data->id,
					//'assign_date'=> 		date('Y-m-d H:i:s')
				]);	


				DB::table('assets')->where('id', $arf_value->item_id)->decrement('quantity');
				DB::table('assets')->where('id', $arf_value->item_id)->decrement('total_quantity');
				
			}


			//CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.arf_print_success",['reference_number'=>$arf_header->reference_number]), 'info');

		}


		public function getRequestPrintPickList($id){
			

			$this->cbLoader();
			if(!CRUDBooster::isUpdate() && $this->global_privilege==FALSE) {    
				CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
			}  


			$data = array();

			$data['page_title'] = 'Print Picklist';

			$data['Header'] = HeaderRequest::
				  leftjoin('request_type', 'header_request.purpose', '=', 'request_type.id')
				->leftjoin('condition_type', 'header_request.conditions', '=', 'condition_type.id')
				->leftjoin('employees', 'header_request.employee_name', '=', 'employees.id')
				->leftjoin('companies', 'header_request.company_name', '=', 'companies.id')
				->leftjoin('departments', 'header_request.department', '=', 'departments.id')
				->leftjoin('positions', 'header_request.position', '=', 'positions.id')
				->leftjoin('stores', 'header_request.store_branch', '=', 'stores.id')
				->leftjoin('cms_users as requested', 'header_request.created_by','=', 'requested.id')
				->leftjoin('cms_users as approved', 'header_request.approved_by','=', 'approved.id')
				->leftjoin('cms_users as recommended', 'header_request.recommended_by','=', 'recommended.id')
				->leftjoin('cms_users as processed', 'header_request.purchased2_by','=', 'processed.id')
				->select(
						'header_request.*',
						'header_request.id as requestid',
						'header_request.created_at as created',
						'request_type.*',
						'condition_type.*',
						'requested.name as requestedby',
						'employees.bill_to as employee_name',
						'companies.company_name as company_name',
						'departments.department_name as department',
						'positions.position_description as position',
						'stores.bea_mo_store_name as store_branch',
						'approved.name as approvedby',
						'recommended.name as recommendedby',
						'processed.name as processedby',
						'header_request.created_at as created_at'
						)
				->where('header_request.id', $id)->first();

			$data['Body'] = BodyRequest::
				select(
				  'body_request.*'
				)
				->where('body_request.header_request_id', $id)
				->orderby('body_request.id', 'desc')
				->get();

			$data['BodyReco'] = DB::table('recommendation_request')
				->select(
				  'recommendation_request.*'
				)
				->where('recommendation_request.header_request_id', $id)
				->get();				

			$data['recommendations'] = DB::table('recommendations')->where('status', 'ACTIVE')->get();

			return $this->view("assets.print-picklist", $data);
		}


		public function PickListUpdate(){

			$data = 			Request::all();	
			
			$cont = (new static)->apiContext;


			$requestid = 			$data['requestid']; 

			$arf_header = 			HeaderRequest::where(['id' => $requestid])->first();

			$for_picklist =  		DB::table('statuses')->where('id', 14)->value('id');


			HeaderRequest::where('id',$requestid)
			->update([
				'status_id'=> 			$for_picklist,
				'purchased3_by'=> 		CRUDBooster::myId(),
				'purchased3_at'=> 		date('Y-m-d H:i:s')
			]);	

			//CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.arf_print_success",['reference_number'=>$arf_header->reference_number]), 'info');

		}


		public function itemSearch(Request $request) {

			$fields = Request::all();

			$search 				= $fields['search'];

			$data = array();

			$data['status_no'] = 0;
			$data['message']   ='No Item Found!';
			$data['items'] = array();

			$item_list = array(); 

			//$search_item =  DB::table('digits_code')>where('digits_code','LIKE','%'.$request->search.'%')->first();

			$items = DB::table('assets_inventory_body')
				->where('assets_inventory_body.digits_code','LIKE','%'.$search.'%')
				->where('assets_inventory_body.quantity','>', 0)
				->where('assets_inventory_body.statuses_id', 6)
				->where('assets_inventory_body.item_condition', "Good")
				//->orWhere('assets.item_description','LIKE','%'.$request->search.'%')
				->orWhere('assets_inventory_body.asset_code','LIKE','%'.$search.'%')
				->where('assets_inventory_body.quantity','>', 0)
				->where('assets_inventory_body.statuses_id', 6)
				->where('assets_inventory_body.item_condition', "Good")
				//->join('category', 'assets_inventory_body.category_id','=', 'category.id')
				//->join('digits_imfs', 'assets.digits_code','=', 'digits_imfs.id')
				->select(	'assets_inventory_body.*'
							//'assets_inventory_body.id as assetID'
							//'digits_imfs.digits_code as dcode',
							//'category.category_description as category_description'
						)->take(10)->orderBy('id', 'asc')->get();


			$count = count($items);
	
			if ($count > 0){

				$data['status'] = 1;
				$data['problem']  = 1;
				$data['status_no'] = 1;
				$data['message']   ='Item Found';
				$i = 0;

				foreach ($items as $key => $value) {

					if(!in_array($value->digits_code, $item_list)){

						$return_data[$i]['id'] = 					$value->id;
						$return_data[$i]['asset_code'] = 			$value->asset_code;
						$return_data[$i]['digits_code'] = 			$value->digits_code;
						$return_data[$i]['serial_no'] = 			$value->serial_no;
						$return_data[$i]['item_description'] = 		$value->item_description;
						$return_data[$i]['value'] = 				$value->value;
						$return_data[$i]['quantity'] = 				$value->quantity;
						$return_data[$i]['item_id'] = 				$value->item_id;
						$i++;

						array_push($item_list, $value->digits_code);

					}


				}

				$data['items'] = $return_data;

			}

			echo json_encode($data);
			exit;  

		}

		public function getDetailPurchasing($id){
			

			$this->cbLoader();
            if(!CRUDBooster::isRead() && $this->global_privilege==FALSE) {    
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

			$data = array();

			$data['page_title'] = 'View Request';

			$data['Header'] = HeaderRequest::
				  leftjoin('request_type', 'header_request.purpose', '=', 'request_type.id')
				->leftjoin('condition_type', 'header_request.conditions', '=', 'condition_type.id')
				->leftjoin('employees', 'header_request.employee_name', '=', 'employees.id')
				->leftjoin('companies', 'header_request.company_name', '=', 'companies.id')
				->leftjoin('departments', 'header_request.department', '=', 'departments.id')
				->leftjoin('positions', 'header_request.position', '=', 'positions.id')
				->leftjoin('stores', 'header_request.store_branch', '=', 'stores.id')
				->leftjoin('cms_users as requested', 'header_request.created_by','=', 'requested.id')
				->leftjoin('cms_users as approved', 'header_request.approved_by','=', 'approved.id')
				->leftjoin('cms_users as recommended', 'header_request.recommended_by','=', 'recommended.id')
				->leftjoin('cms_users as tagged', 'header_request.purchased2_by','=', 'tagged.id')
				->select(
						'header_request.*',
						'header_request.id as requestid',
						'header_request.created_at as created',
						'request_type.*',
						'condition_type.*',
						'requested.name as requestedby',
						'employees.bill_to as employee_name',
						'companies.company_name as company_name',
						'departments.department_name as department',
						//'positions.position_description as position',
						'stores.bea_mo_store_name as store_branch',
						'approved.name as approvedby',
						'recommended.name as recommendedby',
						'tagged.name as taggedby',
						'header_request.created_at as created_at'
						)
				->where('header_request.id', $id)->first();

			$data['Body'] = BodyRequest::
				select(
				  'body_request.*'
				)
				->where('body_request.header_request_id', $id)
				->get();

			$data['Body1'] = BodyRequest::
				select(
				  'body_request.*'
				)
				->where('body_request.header_request_id', $id)
				->wherenotnull('body_request.digits_code')
				->orderby('body_request.id', 'desc')
				->get();

			$data['MoveOrder'] = MoveOrder::
				select(
				  'mo_body_request.*',
				  'statuses.status_description as status_description'
				)
				->where('mo_body_request.header_request_id', $id)
				->leftjoin('statuses', 'mo_body_request.status_id', '=', 'statuses.id')
				->orderby('mo_body_request.id', 'desc')
				->get();
	
			//dd($data['MoveOrder']->count());

			$data['BodyReco'] = DB::table('recommendation_request')
				->select(
				  'recommendation_request.*'
				)
				->where('recommendation_request.header_request_id', $id)
				->get();				

			$data['recommendations'] = DB::table('recommendations')->where('status', 'ACTIVE')->get();


			
			
			return $this->view("assets.mo-detail", $data);
		}

	}