<?php namespace App\Http\Controllers;

	use Session;
	use Request;
	use DB;
	use CRUDBooster;
	use App\Users;
	use App\HeaderRequest;
	use App\BodyRequest;
	use App\ApprovalMatrix;
	use App\StatusMatrix;
	use App\GeneratedAssetsHistories;
	use App\AssetsInventoryHeader;
	use App\AssetsInventoryHeaderForApproval;
	use App\AssetsHeaderImages;
	use App\AssetsInventoryBody;
	use App\Exports\ExportTamReportList;
	use App\Models\AssetsSuppliesInventory;
	use App\Models\AssetsNonTradeInventory;
	use App\Models\AssetsNonTradeInventoryReserved;
	use App\Models\AssetsInventoryReserved;
	use Illuminate\Support\Facades\Log;
	use Illuminate\Support\Facades\Redirect;
	use App\MoveOrder;
	use Maatwebsite\Excel\Facades\Excel;
	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Reader\Exception;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
	use Illuminate\Support\Facades\Response;
	use Carbon\Carbon;

	class AdminHeaderRequestController extends \crocodicstudio\crudbooster\controllers\CBController {
		private $pending;   		
		private $approved;  		
		private $rejected; 		
		private $it_reco;  		
		private $cancelled;  		
		private $released;  		
		private $processing;  		
		private $closed;  			
		private $received;  		
		private $for_picking;  	
		private $for_printing_adf; 
		private $for_closing; 		
		private $for_move_order;  	
		private $for_printing;  	
		private const returnForApproval  = 49;
        public function __construct() {
			// Register ENUM type
			$this->middleware('check.orderschedule',['only' => ['getAddRequisitionSupplies']]);
			DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping("enum", "string");
			$this->pending           = 1;   		
			$this->approved          = 4;  		
			$this->rejected          = 5; 		
			$this->it_reco           = 7;  		
			$this->cancelled         = 8;  		
			$this->released          = 12;  		
			$this->processing        = 11;  		
			$this->closed            = 13;  			
			$this->received          = 16;  		
			$this->for_picking       = 15;  	
			$this->for_printing_adf  = 18; 
			$this->for_closing       = 19; 		
			$this->for_move_order    = 14;  	
			$this->for_printing      = 17;  
		}
		
	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "id";
			$this->limit = "20";
			$this->orderby = "id,desc";
			$this->global_privilege = false;
			$this->button_table_action = true;
			$this->button_bulk_action = true;
			$this->button_action_style = "button_icon";
			$this->button_add = false;
			$this->button_edit = false;
			$this->button_delete = false;
			$this->button_detail = true;
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
			$this->col[] = ["label"=>"Approved By","name"=>"approved_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"Approved Date","name"=>"approved_at"];
			$this->col[] = ["label"=>"Rejected Date","name"=>"rejected_at"];
			$this->col[] = ["label"=>"Age of ticket","name"=>"reference_number"];
			# END COLUMNS DO NOT REMOVE THIS LINE
			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];


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
				$released  = 		DB::table('statuses')->where('id', 12)->value('id');

				$this->addaction[] = ['title'=>'Cancel Request',
				'url'=>CRUDBooster::mainpath('getRequestCancel/[id]'),
				'icon'=>'fa fa-times', 
				"showIf"=>"[status_id] == $pending",
				'confirmation'=>'yes',
				'confirmation_title'=>'Confirm Voiding',
				'confirmation_text'=>'Are you sure to VOID this request?'];
				//$this->addaction[] = ['title'=>'Receive Asset','url'=>CRUDBooster::mainpath('getRequestReceive/[id]'),'icon'=>'fa fa-check', "showIf"=>"[status_id] == $released"];
				$this->addaction[] = ['title'=>'Edit','url'=>CRUDBooster::mainpath('getEdit/[id]'),'icon'=>'fa fa-edit', "showIf"=>"[status_id] == ".self::returnForApproval.""];
			
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
			/*if(CRUDBooster::isUpdate())
	        {
						$this->button_selected[] = ['label'=>'Void',
													'icon'=>'fa fa-times-circle',
													'name'=>'void'];
	        }*/
	                
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
				if(CRUDBooster::isSuperadmin()){
					$this->index_button[] = ["label"=>"Export Lists","icon"=>"fa fa-download","url"=>CRUDBooster::mainpath('export'),"color"=>"primary"];
				}
				$this->index_button[] = ["label"=>"IT Asset Request","icon"=>"fa fa-plus-circle","url"=>CRUDBooster::mainpath('add-requisition'),"color"=>"success"];
				$this->index_button[] = ["label"=>"FA Request","icon"=>"fa fa-plus-circle","url"=>CRUDBooster::mainpath('add-requisition-fa'),"color"=>"success"];
				// $this->index_button[] = ["label"=>"Non Trade","icon"=>"fa fa-files-o","url"=>CRUDBooster::mainpath('add-requisition-non-trade'),"color"=>"success"];
			
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
	        $this->script_js = NULL;
			$this->script_js = "
				// $('.fa.fa-times').click(function(){
				// 	var strconfirm = confirm('Are you sure you want to cancel this request?');
				// 	if (strconfirm == true) {
				// 		return true;
				// 	}else{
				// 		return false;
				// 		window.stop();
				// 	}
				// });
				
				
				$('.fa.fa-check').click(function(){
					var strconfirm = confirm('Are you sure you want to close this request?');
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
				$released  = 		DB::table('statuses')->where('id', 12)->value('id');
				$query->whereNull('header_request.deleted_at')
					  ->orderBy('header_request.status_id', 'ASC')
					  ->orderBy('header_request.id', 'DESC');
			}else{
				$user = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
				$query->where(function($sub_query){
				$user = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
				$released  = 		DB::table('statuses')->where('id', 12)->value('id');
				$sub_query->where('header_request.created_by', CRUDBooster::myId())
							->whereNull('header_request.deleted_at'); 
				});

				$query->orderBy('header_request.id', 'desc')->orderBy('header_request.created_at', 'desc');
				//$query->orderByRaw('FIELD( header_request.status_id, "For Approval")');
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
			$pending           = DB::table('statuses')->where('id', $this->pending)->value('status_description');
			$approved          = DB::table('statuses')->where('id', $this->approved)->value('status_description');
			$rejected          = DB::table('statuses')->where('id', $this->rejected)->value('status_description');
			$it_reco           = DB::table('statuses')->where('id', $this->it_reco)->value('status_description');
			$cancelled         = DB::table('statuses')->where('id', $this->cancelled)->value('status_description');
			$released          = DB::table('statuses')->where('id', $this->released)->value('status_description');
			$processing        = DB::table('statuses')->where('id', $this->processing)->value('status_description');
			$closed            = DB::table('statuses')->where('id', $this->closed)->value('status_description');
			$received          = DB::table('statuses')->where('id', $this->received)->value('status_description');
			$for_picking       = DB::table('statuses')->where('id', $this->for_picking)->value('status_description');
			$for_printing_adf  = DB::table('statuses')->where('id', $this->for_printing_adf)->value('status_description');
			$for_closing       = DB::table('statuses')->where('id', $this->for_closing)->value('status_description');
			$for_move_order    = DB::table('statuses')->where('id', $this->for_move_order)->value('status_description');
			$for_printing      = DB::table('statuses')->where('id', $this->for_printing)->value('status_description');
			$forReturnApproval = DB::table('statuses')->where('id', self::returnForApproval)->value('status_description');

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
				}else if($column_value == $for_picking){
					$column_value = '<span class="label label-info">'.$for_picking.'</span>';
				}else if($column_value == $for_move_order){
					$column_value = '<span class="label label-info">'.$for_move_order.'</span>';
				}elseif($column_value == $for_printing_adf){
					$column_value = '<span class="label label-info">'.$for_printing_adf.'</span>';
				}elseif($column_value == $for_closing){
					$column_value = '<span class="label label-info">'.$for_closing.'</span>';
				}else if($column_value == $for_printing){
					$column_value = '<span class="label label-info">'.$for_printing.'</span>';
				}else if($column_value == $forReturnApproval){
					$column_value = '<span class="label label-warning">'.$forReturnApproval.'</span>';
				}

			}

			if($column_index == 6){
				if($column_value == null){
					$column_value = "ERF";
				}
			}
			
			if($column_index == 13){
				$info = HeaderRequest::where('reference_number',$column_value)->first();
				if(!in_array($info->status_id,[13,19])){
					$start = Carbon::parse($info->created_at);
					$now = Carbon::now();
					$column_value = $start->diffInDays($now) .' Days';
				}else{
					$column_value = 'Transacted';
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
			$fields = Request::all();
			$cont = (new static)->apiContext;

			$dataLines = array();
			$digits_code 		= $fields['supplies_digits_code'];
			$supplies_cost 		= $fields['supplies_cost'];
			$employee_name 		= $fields['employee_name'];
			$company_name 		= $fields['company_name'];
			$position 			= $fields['position'];
			$department 		= $fields['department'];
			$store_branch 		= $fields['store_branch'];
			$store_branch_id    = $fields['store_branch_id'];
			$purpose 			= $fields['purpose'];
			$condition 			= $fields['condition'];
			$quantity_total 	= $fields['quantity_total'];
			$cost_total 		= $fields['cost_total'];
			$total 				= $fields['total'];
			$request_type_id 	= $fields['request_type_id'];
			$requestor_comments = $fields['requestor_comments'];
			$application 		= $fields['application'];
			$application_others = $fields['application_others'];
			$count_header       = DB::table('header_request')->count();
			$header_ref         = str_pad($count_header + 1, 7, '0', STR_PAD_LEFT);			
			$reference_number	= "ARF-".$header_ref;
			$employees          = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
			$pending            = DB::table('statuses')->where('id', 1)->value('id');
			$approved           = DB::table('statuses')->where('id', 4)->value('id');

			if(in_array(CRUDBooster::myPrivilegeId(), [10,11,13,14])){ 
				//$postdata['status_id']		 			= $pending;
				$postdata['status_id']		 			= StatusMatrix::where('current_step', 2)
																		->where('request_type', $request_type_id)
																		//->where('id_cms_privileges', CRUDBooster::myPrivilegeId())
																		->value('status_id');
			}else{
				$postdata['status_id']		 			= StatusMatrix::where('current_step', 1)
																		->where('request_type', $request_type_id)
																		//->where('id_cms_privileges', CRUDBooster::myPrivilegeId())
																		->value('status_id');
	
			}
				
			$postdata['reference_number']		 	= $reference_number;
			$postdata['employee_name'] 				= $employees->id;
			$postdata['company_name'] 				= $employees->company_name_id;
			$postdata['position'] 					= $employees->position_id;
			$postdata['department'] 				= $employees->department_id;
			if(CRUDBooster::myPrivilegeId() == 8){
				$postdata['store_branch'] 			= $employees->location_id;
			}else{
				$postdata['store_branch'] 			= NULL;
			}
			$postdata['purpose'] 					= $purpose;
			$postdata['conditions'] 				= $condition;
			$postdata['quantity_total'] 			= $quantity_total;
			$postdata['cost_total'] 				= $cost_total;
			$postdata['total'] 						= $total;
			$postdata['requestor_comments'] 		= $requestor_comments;
			$postdata['created_by'] 				= CRUDBooster::myId();
			$postdata['created_at'] 				= date('Y-m-d H:i:s');
			$postdata['request_type_id']		 	= $request_type_id;
			$postdata['privilege_id']		 		= CRUDBooster::myPrivilegeId();
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
	        //Your code here
			$fields             = Request::all();
			$dataLines          = array();
			$arf_header         = DB::table('header_request')->where(['created_by' => CRUDBooster::myId()])->orderBy('id','desc')->first();
			$digits_code 		= $fields['digits_code'];
			$item_cost 		    = $fields['item_cost'];
			$item_description 	= $fields['item_description'];
			$category_id 		= $fields['category_id'];
			$sub_category_id 	= $fields['sub_category_id'];
			$app_id_others 		= $fields['app_id_others'];
			$quantity 			= $fields['quantity'];
			$image 				= $fields['image'];
			$request_type_id 	= $fields['request_type_id'];
			$budget_range 	    = $fields['budget_range'];
			$app_count = 2;
         
			for($x=0; $x < count((array)$item_description); $x++) {
				$apps_array = array();
				$app_no     = 'app_id'.$app_count;
				$app_id     = $fields[$app_no];
				for($xxx=0; $xxx < count((array)$app_id); $xxx++) {
					array_push($apps_array,$app_id[$xxx]); 
				}
	
				$app_count++;
				if (!empty($image[$x])) {
					$extension1 =  $app_count.time() . '.' .$image[$x]->getClientOriginalExtension();
					$filename = $extension1;
					$image[$x]->move('vendor/crudbooster/',$filename);
				}

				if(in_array(CRUDBooster::myPrivilegeId(), [11,12,14,15])){ 
					if($category_id[$x] == "IT ASSETS"){
						HeaderRequest::where('id', $arf_header->id)->update([
							'to_reco'=> 1
						]);
					}
				}

				$dataLines[$x]['header_request_id'] = $arf_header->id;
				$dataLines[$x]['digits_code'] 	    = $digits_code[$x];
				$dataLines[$x]['item_description'] 	= $item_description[$x];
				$dataLines[$x]['category_id'] 		= $category_id[$x];
				$dataLines[$x]['sub_category_id'] 	= $sub_category_id[$x];
				$dataLines[$x]['app_id'] 			= implode(", ",$apps_array);
				$dataLines[$x]['app_id_others'] 	= $app_id_others[$x];
				$dataLines[$x]['quantity'] 			= intval(str_replace(',', '', $quantity[$x]));
				$dataLines[$x]['unit_cost'] 		= $item_cost[$x];
				$dataLines[$x]['total_unit_cost'] 	= $item_cost[$x];
				$dataLines[$x]['budget_range'] 		= $budget_range[$x];
				if($request_type_id == 5){
					$dataLines[$x]['to_reco'] = 0;
				}else{
					if (str_contains($sub_category_id[$x], 'LAPTOP') || str_contains($sub_category_id[$x], 'DESKTOP')) {
						$dataLines[$x]['to_reco'] = 1;
					}else{
						$dataLines[$x]['to_reco'] = 0;
					}
				}

				if (!empty($image[$x])) {
					$dataLines[$x]['image'] 			= 'vendor/crudbooster/'.$filename;
				}else{
					$dataLines[$x]['image'] 			= "";
				}
				$dataLines[$x]['created_by'] 		= CRUDBooster::myId();
				$dataLines[$x]['created_at'] 		= date('Y-m-d H:i:s');

				unset($apps_array);
			}

			DB::beginTransaction();
			try {
				BodyRequest::insert($dataLines);
				DB::commit();
			
			    //manager replenishment
				$arf_body = BodyRequest::where(['header_request_id' => $arf_header->id])->whereNull('deleted_at')->get();
				if(in_array(CRUDBooster::myPrivilegeId(), [11,12,14,15])){ 
					if(in_array($request_type_id, [7])){
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
						}
					}else if(in_array($request_type_id, [9])){
						//GET ASSETS NON TRADE INVENTORY AVAILABLE COUNT
					$inventoryList = DB::table('assets_non_trade_inventory_body')->select('digits_code as digits_code',DB::raw('SUM(quantity) as avail_qty'))->groupBy('digits_code')->get();
					//GET RESERVED QTY 
					$reservedList = DB::table('assets_non_trade_inventory_reserved')->select('digits_code as digits_code',DB::raw('SUM(approved_qty) as reserved_qty'))->whereNotNull('reserved')->groupBy('digits_code')->get()->toArray();
					
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
						$countAvailQty = DB::table('assets_non_trade_inventory_body')->select('digits_code as digits_code','quantity')->where('digits_code',$fBodyItFaVal->digits_code)->first();
                        $reservedListCount = DB::table('assets_non_trade_inventory_reserved')->select('digits_code as digits_code','approved_qty')->whereNotNull('reserved')->where('digits_code',$fBodyItFaVal->digits_code)->first();
						$available_quantity = max($countAvailQty->quantity - $reservedListCount->approved_qty,0);
			
						if($available_quantity >= $fBodyItFaVal->quantity){
							//add to reserved taable
							AssetsNonTradeInventoryReserved::Create(
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
							AssetsNonTradeInventoryReserved::Create(
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

								HeaderRequest::where('id',$arf_header->id)
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
				}
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

	    //By the way, you can still create your own method in here... :) 
		public function getAddRequisition() {
			if(!CRUDBooster::isCreate() && $this->global_privilege == false) {
				CRUDBooster::redirect(CRUDBooster::adminPath(), trans('crudbooster.denied_access'));
			}

			$this->cbLoader();
			$data['page_title'] = 'Create New IT Asset Request';
			$data['conditions'] = DB::table('condition_type')->where('status', 'ACTIVE')->get();
			$data['departments'] = DB::table('departments')->where('status', 'ACTIVE')->get();
			$data['stores'] = DB::table('stores')->where('status', 'ACTIVE')->get();
			$data['departments'] = DB::table('departments')->where('status', 'ACTIVE')->get();
			$data['user'] = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
			$data['employeeinfos'] = Users::user($data['user']->id);
			$data['categories'] = DB::table('category')->where('category_status', 'ACTIVE')->where('id', 6)->orderby('category_description', 'asc')->get();
			$data['sub_categories'] = DB::table('sub_category')->where('class_status', 'ACTIVE')->where('category_id', 6)->orderby('class_description', 'asc')->get();
			$data['applications'] = DB::table('applications')->where('status', 'ACTIVE')->orderby('app_name', 'asc')->get();
			$data['companies'] = DB::table('companies')->where('status', 'ACTIVE')->get();
			$data['budget_range'] = DB::table('sub_masterfile_budget_range')->where('status', 'ACTIVE')->get();
			$privilegesMatrix = DB::table('cms_privileges')->get();
			$privileges_array = array();
			foreach($privilegesMatrix as $matrix){
				array_push($privileges_array, $matrix->id);
			}
			$privileges_string = implode(",",$privileges_array);
			$privilegeslist = array_map('intval',explode(",",$privileges_string));

			if(in_array(CRUDBooster::myPrivilegeId(), $privilegeslist)){ 
				$data['purposes'] = DB::table('request_type')->where('status', 'ACTIVE')->where('privilege', 'Employee')->get();
				$data['stores'] = DB::table('locations')->where('id', $data['user']->location_id)->first();
				return $this->view("assets.add-requisition", $data);

			}			
		}

		public function getEdit($id){
            if(!CRUDBooster::isUpdate() && $this->global_privilege==FALSE) {    
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

			$data = [];
			$data['page_title'] = 'Edit Assets Request';
			$data['conditions'] = DB::table('condition_type')->where('status', 'ACTIVE')->get();
			$data['departments'] = DB::table('departments')->where('status', 'ACTIVE')->get();
			$data['stores'] = DB::table('stores')->where('status', 'ACTIVE')->get();
			$data['departments'] = DB::table('departments')->where('status', 'ACTIVE')->get();
			$data['user'] = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
			$data['employeeinfos'] = Users::user($data['user']->id);
			$data['categories'] = DB::table('category')->where('category_status', 'ACTIVE')->where('id', 6)->orderby('category_description', 'asc')->get();
			$data['sub_categories'] = DB::table('sub_category')->where('class_status', 'ACTIVE')->where('category_id', 6)->orderby('class_description', 'asc')->get();
			$data['applications'] = DB::table('applications')->where('status', 'ACTIVE')->orderby('app_name', 'asc')->get();
			$data['companies'] = DB::table('companies')->where('status', 'ACTIVE')->get();
			$data['budget_range'] = DB::table('sub_masterfile_budget_range')->where('status', 'ACTIVE')->get();
			$data['Header'] = HeaderRequest::header($id);
			$data['Body'] = BodyRequest::select('body_request.*')->where('body_request.header_request_id', $id)->whereNull('deleted_at')->get();
			
			$data['purposes'] = DB::table('request_type')->where('status', 'ACTIVE')->where('privilege', 'Employee')->get();
			$data['stores'] = DB::table('locations')->where('id', $data['user']->location_id)->first();
			$applicationsExplode = explode(",",$data['Header']->application);
			$data['applicationsExplode'] = array_map('trim', $applicationsExplode);
			// dd($data['purposes'],$data['Header']->purpose);
			return $this->view("assets.edit-requisition", $data);
		}

		public function getAddRequisitionFA() {

			if(!CRUDBooster::isCreate() && $this->global_privilege == false) {
				CRUDBooster::redirect(CRUDBooster::adminPath(), trans('crudbooster.denied_access'));
			}

			$this->cbLoader();
			$data['page_title'] = 'Create New FA Request';
			$data['conditions'] = DB::table('condition_type')->where('status', 'ACTIVE')->get();
			$data['departments'] = DB::table('departments')->where('status', 'ACTIVE')->get();
			$data['stores'] = DB::table('stores')->where('status', 'ACTIVE')->get();
			$data['departments'] = DB::table('departments')->where('status', 'ACTIVE')->get();
			$data['user'] = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
			$data['employeeinfos'] = Users::user($data['user']->id);
			$data['categories'] = DB::table('category')->whereIn('id', [4])->where('category_status', 'ACTIVE')
													   ->orderby('category_description', 'asc')
													   ->first();
			$data['sub_categories'] = DB::table('sub_category')->where('class_status', 'ACTIVE')->whereIn('category_id', [1,4,7,8])->orderby('class_description', 'asc')->get();
			$data['applications'] = DB::table('applications')->where('status', 'ACTIVE')->orderby('app_name', 'asc')->get();
			$data['companies'] = DB::table('companies')->where('status', 'ACTIVE')->get();
			$data['budget_range'] = DB::table('sub_masterfile_budget_range')->where('status', 'ACTIVE')->get();
			$privilegesMatrix = DB::table('cms_privileges')->get();
			$privileges_array = array();
			foreach($privilegesMatrix as $matrix){
				array_push($privileges_array, $matrix->id);
			}
			$privileges_string = implode(",",$privileges_array);
			$privilegeslist = array_map('intval',explode(",",$privileges_string));

			if(in_array(CRUDBooster::myPrivilegeId(), $privilegeslist)){ 
				$data['purposes'] = DB::table('request_type')->where('status', 'ACTIVE')->where('privilege', 'Employee')->get();
				$data['stores'] = DB::table('locations')->where('id', $data['user']->location_id)->first();
				return $this->view("assets.add-requisition-fa", $data);
			}
				
		}

		// public function getAddRequisitionNonTrade() {

		// 	if(!CRUDBooster::isCreate() && $this->global_privilege == false) {
		// 		CRUDBooster::redirect(CRUDBooster::adminPath(), trans('crudbooster.denied_access'));
		// 	}

		// 	$this->cbLoader();
		// 	$data['page_title'] = 'Create New Non Trade Request';
		// 	$data['conditions'] = DB::table('condition_type')->where('status', 'ACTIVE')->get();
		// 	$data['departments'] = DB::table('departments')->where('status', 'ACTIVE')->get();
		// 	$data['stores'] = DB::table('stores')->where('status', 'ACTIVE')->get();
		// 	$data['departments'] = DB::table('departments')->where('status', 'ACTIVE')->get();
		// 	$data['user'] = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
		// 	$data['employeeinfos'] = DB::table('cms_users')
		// 								 ->leftjoin('positions', 'cms_users.position_id', '=', 'positions.id')
		// 								 ->leftjoin('departments', 'cms_users.department_id', '=', 'departments.id')
		// 								 ->select( 'cms_users.*', 'positions.position_description as position_description', 'departments.department_name as department_name')
		// 								 ->where('cms_users.id', $data['user']->id)->first();
		// 	$data['categories'] = DB::table('category')->where('category_status', 'ACTIVE')->where('id', 6)->orderby('category_description', 'asc')->get();
		// 	$data['sub_categories'] = DB::table('sub_category')->where('class_status', 'ACTIVE')->where('category_id', 6)->orderby('class_description', 'asc')->get();
		// 	$data['applications'] = DB::table('applications')->where('status', 'ACTIVE')->orderby('app_name', 'asc')->get();
		// 	$data['companies'] = DB::table('companies')->where('status', 'ACTIVE')->get();
			
		// 	$privilegesMatrix = DB::table('cms_privileges')->get();
		// 	$privileges_array = array();
		// 	foreach($privilegesMatrix as $matrix){
		// 		array_push($privileges_array, $matrix->id);
		// 	}
		// 	$privileges_string = implode(",",$privileges_array);
		// 	$privilegeslist = array_map('intval',explode(",",$privileges_string));

		// 	if(in_array(CRUDBooster::myPrivilegeId(), $privilegeslist)){ 
		// 		$data['purposes'] = DB::table('request_type')->where('status', 'ACTIVE')->where('privilege', 'Employee')->get();
		// 		$data['stores'] = DB::table('locations')->where('id', $data['user']->location_id)->first();
		// 		return $this->view("non-trade.add-requisition-non-trade", $data);

		// 	}			
		// }

		// public function getAddRequisitionSupplies() {

		// 	if(!CRUDBooster::isCreate() && $this->global_privilege == false) {
		// 		CRUDBooster::redirect(CRUDBooster::adminPath(), trans('crudbooster.denied_access'));
		// 	}
		// 	$this->cbLoader();
		// 	$data['page_title'] = 'Create New Supplies Request';
		// 	$data['conditions'] = DB::table('condition_type')->where('status', 'ACTIVE')->get();
		// 	$data['departments'] = DB::table('departments')->where('status', 'ACTIVE')->get();
		// 	$data['stores'] = DB::table('stores')->where('status', 'ACTIVE')->get();
		// 	$data['departments'] = DB::table('departments')->where('status', 'ACTIVE')->get();
		// 	$data['user'] = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
		// 	$data['employeeinfos'] = DB::table('cms_users')
		// 								 ->leftjoin('positions', 'cms_users.position_id', '=', 'positions.id')
		// 								 ->leftjoin('departments', 'cms_users.department_id', '=', 'departments.id')
		// 								 ->select( 'cms_users.*', 'positions.position_description as position_description', 'departments.department_name as department_name')
		// 								 ->where('cms_users.id', $data['user']->id)->first();
		// 	$data['categories'] = DB::table('category')->where('id', 2)->where('category_status', 'ACTIVE')
		// 											   ->orderby('category_description', 'asc')
		// 											   ->get();
		// 	$data['sub_categories'] = DB::table('class')->where('class_status', 'ACTIVE')->where('category_id', 2)->orderby('class_description', 'asc')->get();
		// 	$data['item_description'] = DB::table('assets')->where('category_id', 2)
		// 	                                           //->where('category_status', 'ACTIVE')
		// 											   ->orderby('item_description', 'asc')
		// 											   ->get();  
		// 	$data['applications'] = DB::table('applications')->where('status', 'ACTIVE')->orderby('app_name', 'asc')->get();	
		// 	$data['companies'] = DB::table('companies')->where('status', 'ACTIVE')->get();

		// 	//$privilegesMatrix = DB::table('cms_privileges')->where('id', '!=', 8)->get();
		// 	$privilegesMatrix = DB::table('cms_privileges')->get();
		// 	$privileges_array = array();
		// 	foreach($privilegesMatrix as $matrix){
		// 		array_push($privileges_array, $matrix->id);
		// 	}
		// 	$privileges_string = implode(",",$privileges_array);
		// 	$privilegeslist = array_map('intval',explode(",",$privileges_string));

		// 	if(in_array(CRUDBooster::myPrivilegeId(), $privilegeslist)){ 
		// 		$data['purposes'] = DB::table('request_type')->where('status', 'ACTIVE')->where('privilege', 'Employee')->get();
		// 		$data['stores'] = DB::table('locations')->where('id', $data['user']->location_id)->first();
		// 		return $this->view("assets.add-requisition-supplies", $data);
		// 	}
				
		// }

		public function getDetail($id){
			$this->cbLoader();
            if(!CRUDBooster::isRead() && $this->global_privilege==FALSE) {    
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

			$data = array();
			$data['page_title'] = 'View Request';
			$data['Header'] = HeaderRequest::header($id);
			$data['Body'] = BodyRequest::select('body_request.*')->where('body_request.header_request_id', $id)->get();
			$data['Body1'] = BodyRequest::select('body_request.*')
				->where('body_request.header_request_id', $id)
				->wherenotnull('body_request.digits_code')
				->orderby('body_request.id', 'desc')
				->get();

			$data['MoveOrder'] = MoveOrder::select('mo_body_request.*','statuses.status_description as status_description')
				->where('mo_body_request.header_request_id', $id)
				->leftjoin('statuses', 'mo_body_request.status_id', '=', 'statuses.id')
				->orderby('mo_body_request.id', 'desc')
				->get();

			$data['BodyReco'] = DB::table('recommendation_request')->select('recommendation_request.*')->where('recommendation_request.header_request_id', $id)->get();				

			$data['recommendations'] = DB::table('recommendations')->where('status', 'ACTIVE')->get();		
			return $this->view("assets.detail", $data);
		}

		public function itemSearch(Request $request) {
			$data = array();

			$fields = Request::all();
			$search 				= $fields['search'];
			$data['status_no'] = 0;
			$data['message']   ='No Item Found!';
			$data['items'] = array();
			$items = DB::table('assets')
			    ->where('assets.digits_code','LIKE','%'.$search.'%')->where('assets.category_id','=',1)
				->orwhere('assets.digits_code','LIKE','%'.$search.'%')->where('assets.category_id','=',5)
				->orWhere('assets.item_description','LIKE','%'.$search.'%')->where('assets.category_id','=',1)
				->orWhere('assets.item_description','LIKE','%'.$search.'%')->where('assets.category_id','=',5)
				->join('category', 'assets.category_id','=', 'category.id')
				->select(	'assets.*',
				            'category.id as cat_id',
							'assets.id as assetID',
							'category.category_description as category_description'
						)
				->take(10)->get();
			
			if($items){
				$data['status'] = 1;
				$data['problem']  = 1;
				$data['status_no'] = 1;
				$data['message']   ='Item Found';
				$i = 0;
				foreach ($items as $key => $value) {

					$return_data[$i]['id']                   = $value->assetID;
					$return_data[$i]['cat_id']               = $value->cat_id;
					$return_data[$i]['digits_code']          = $value->digits_code;
					$return_data[$i]['asset_tag']            = $value->asset_tag;
					$return_data[$i]['serial_no']            = $value->serial_no;
					$return_data[$i]['item_description']     = $value->item_description;
					$return_data[$i]['category_description'] = $value->category_description;
					$return_data[$i]['item_cost']            = $value->item_cost;

					$i++;

				}
				$data['items'] = $return_data;
			}

			echo json_encode($data);
			exit;  
		}


		public function Employees(Request $request){
			$employees = 	DB::table('employees')
							->leftjoin('positions', 'employees.position_id', '=', 'positions.id')
							->leftjoin('departments', 'employees.department_id', '=', 'departments.id')
							->leftjoin('companies', 'employees.company_name', '=', 'companies.id')
							->select( 'employees.*', 'positions.position_description as position_description', 'departments.department_name as department_name', 'companies.company_name as company_name')
							->where('status_id', 1)->where('bill_to', $request->employee_name)->get();
	
			return($employees);
		}


		public function Companies(Request $request){

			$companies = DB::table('companies')->where('company_name', $request->company_name)->first();

			$employees = 	DB::table('employees')
							->leftjoin('positions', 'employees.position_id', '=', 'positions.id')
							->leftjoin('departments', 'employees.department_id', '=', 'departments.id')
							//->leftjoin('companies', 'employees.company_name', '=', 'companies.id')
							->select( 'employees.*', 'positions.position_description as position_description', 'departments.department_name as department_name')
							->where('status_id', 1)->where('company_name', $companies->id)->get();
	
			return($employees);
		}

		public function getRequestCancel($id) {

			HeaderRequest::where('id',$id)
			->update([
					'status_id'=> 8,
					'cancelled_by'=> CRUDBooster::myId(),
					'cancelled_at'=> date('Y-m-d H:i:s')	
			]);	

			BodyRequest::where('header_request_id', $id)
			->update([
				'deleted_at'=> 		date('Y-m-d H:i:s'),
				'deleted_by'=> 		CRUDBooster::myId()
			]);	
			
			CRUDBooster::redirect(CRUDBooster::mainpath(), trans("Request has been cancelled successfully!"), 'info');
		}


		public function getRequestReceive($id) {

			$arf_header = 		HeaderRequest::where(['id' => $id])->first();

			HeaderRequest::where('id',$id)
			->update([
					'status_id'=> 	StatusMatrix::where('current_step', 8)
									->where('request_type', $arf_header->request_type_id)
									//->where('id_cms_privileges', CRUDBooster::myPrivilegeId())
									->value('status_id'),
					'received_by'=> CRUDBooster::myId(),
					'received_at'=> date('Y-m-d H:i:s')	
			]);	
			
			//CRUDBooster::redirect(CRUDBooster::mainpath(), trans("Request has been cancelled successfully!"), 'info');
		}

		public function SubCategories(Request $request){
			$data = Request::all();	
			$id = $data['id'];

			$categories = DB::table('category')->where('category_description', $id)->first();

			$subcategories = DB::table('class')
							->select( 'class.*' )
							->where('category_id', $categories->id)
							->where('class_status', "ACTIVE")
							->whereNull('deleted_at')
							->orderby('class_description', 'ASC')->get();
	
			return($subcategories);
		}

		public function RemoveItem(Request $request){
			$data = 				Request::all();	
			$headerID = 			$data['headerID'];
			$bodyID = 				$data['bodyID'];
			$quantity_total = 		$data['quantity_total']; 

			HeaderRequest::where('id', $headerID)
			->update([
				'quantity_total'=> 		$quantity_total
			]);	

			BodyRequest::where('id', $bodyID)
			->update([
				'deleted_at'=> 		date('Y-m-d H:i:s'),
				'deleted_by'=> 		CRUDBooster::myId()
			]);	


			$bodyCount = DB::table('body_request')->where('header_request_id',$headerID)->whereNull('body_request.deleted_at')->count();

			if($bodyCount == 0){
				HeaderRequest::where('id', $headerID)
				->update([
					'status_id'=> 8,
					'cancelled_by'=> CRUDBooster::myId(),
					'cancelled_at'=> date('Y-m-d H:i:s')
				]);	
			}

			
		}


		//Get Assets Hitory tru modal
		public function getHistory(Request $request) {
			$data = array();
			$data['status_no'] = 0;
			$data['message']   ='No Item Found!';
			$data['items'] = array();

			$items = DB::table('assets_movement_histories')
				->where('assets_movement_histories.body_id', $request->id)
				//->whereNull('assets_movement_histories.archived')
				->join('assets_inventory_body', 'assets_movement_histories.body_id','=','assets_inventory_body.id')
				->join('assets_inventory_header', 'assets_movement_histories.header_id','=','assets_inventory_header.id')
				//->join('cms_users', 'assets_movement_histories.updated_by', '=', 'cms_users.id')
				->select(	'assets_movement_histories.*',
							'assets_inventory_body.*',
							'assets_inventory_body.location as location_per_item',
							'assets_inventory_header.*',
							'assets_inventory_header.location as location_all_item',
							'assets_movement_histories.deployed_to as history_deployed_to',
							'assets_movement_histories.location as history_location'
							//'cms_users.*'
						)
				->get();

			if($items){
					$data['id'] = $request->id;
					$data['status'] = 1;
					$data['problem']  = 1;
					$data['status_no'] = 1;
					$data['message']   ='Item Found';
					$i = 0;
					foreach ($items as $key => $value) {

						$return_data[$i]['id']          = 		$value->id;
						$return_data[$i]['assets_code'] = 		$value->asset_code;
						$return_data[$i]['digits_code'] = 		$value->digits_code;
						$return_data[$i]['serial_no']   = 		$value->serial_no;
						$return_data[$i]['date_update'] = 		$value->date_update;
						$return_data[$i]['updated_by'] = 		$value->history_updated_by;
						$return_data[$i]['description'] = 		$value->description;
						$return_data[$i]['deployed_to'] = 		$value->history_deployed_to ? $value->history_deployed_to : "";
						$return_data[$i]['remarks']     = 	    $value->remarks;
						$return_data[$i]['location']     = 	    $value->history_location ? $value->history_location : "";
						$i++;
	
					}
					$data['items'] = $return_data;
				}	
			echo json_encode($data);
			exit;  
		}
		
		//Check Data
		public function getCheckData(Request $request) {
			$data = GeneratedAssetsHistories::all();
			echo json_encode(["count"=>count($data)]);
		}
		
		//Display data by search
		public function getHistories(Request $request) {	
			$fields = Request::all();
			
			ini_set('memory_limit', '-1');
			ini_set('max_execution_time', 3000);

			$asset_code = $fields['asset_code'];
			$date_from = $fields['date_from'];
			$date_to = $fields['date_to'];
			$overwrite = $fields['Overwrite'];

			if($date_from > $date_to){
				$data = ['status'=>'error', 'message'=>'Invalid Selected Date Range!'];

			}else if(empty($date_from) && empty($date_to)){
				$data = ['status'=>'error', 'message'=>'Please Select Date Range!'];
			}else{
				$date=date('Y-m-d');
				if($overwrite == "true"){
					GeneratedAssetsHistories::truncate(); 
				}

				//get Inventory per asset items per header
				$inventoryData = AssetsInventoryHeader::leftjoin('cms_users', 'assets_inventory_header.created_by', '=', 'cms_users.id')
				->leftjoin('assets_inventory_body', 'assets_inventory_header.id', '=', 'assets_inventory_body.header_id')
				->select(
						'assets_inventory_header.*',
						'assets_inventory_body.*',
						'cms_users.*'
				)
				->groupBy('assets_inventory_body.header_id');
				
				if (!empty($date_from) && !empty($date_to)) {
					$inventoryData->whereDate('assets_inventory_body.created_at','>=' ,$date_from)->whereDate('assets_inventory_body.created_at','<=' ,$date_to);
				}
				if (!empty($asset_code)) {
					$inventoryData->where('assets_inventory_body.asset_code','LIKE','%'.$asset_code.'%');
				}
				$result = $inventoryData->get()->toArray();

				//get data that we need
				$containerData = [];
				$insertData = [];
				foreach($result as $key => $val){
					$containerData['header_id'] = $val['header_id'];
					$containerData['body_id'] = (int) "";
					$containerData['transaction_type'] = $val['transaction_per_asset'];
					$containerData['reference_no'] = "";
					$containerData['po_no'] = $val['po_no'];
					$containerData['invoice_no'] = $val['invoice_no'];
					$containerData['invoice_date'] = $val['invoice_date'];
					$containerData['rr_date'] = $val['rr_date'];
					$containerData['start_date'] = $date_from;
					$containerData['end_date'] = $date_to;
					$insertData[] = $containerData;
				}
				//get Deployed asset 
				$deployedData = $data['Header'] = HeaderRequest::
				leftjoin('mo_body_request', 'header_request.id', '=', 'mo_body_request.header_request_id')
				->leftjoin('statuses', 'mo_body_request.status_id', '=', 'statuses.id')
				->select(
						'header_request.*',
						'mo_body_request.*',
						'statuses.*'
						)

				->groupBy('mo_body_request.header_request_id');
				
				if (!empty($date_from) && !empty($date_to)) {
					$deployedData->whereDate('mo_body_request.created_at','>=' ,$date_from)
					->whereDate('mo_body_request.created_at','<=' ,$date_to)
					->whereIn('mo_body_request.status_id', array(16, 13, 19))
					->where('mo_body_request.asset_code','LIKE','%'.$asset_code.'%');
				}

				$result2 = $deployedData->get()->toArray();

				foreach($result2 as $key => $val){
					$containerData['header_id'] = $val['header_request_id'];
					$containerData['body_id'] = (int) "";
					$containerData['transaction_type'] = "Deployed";
					$containerData['reference_no'] = $val['mo_reference_number'];
					$containerData['po_no'] = NULL;
					$containerData['invoice_no'] = NULL;
					$containerData['invoice_date'] = NULL;
					$containerData['rr_date'] = NULL;
					$containerData['start_date'] = $date_from;
					$containerData['end_date'] = $date_to;
					$insertData[] = $containerData;
				}
				//dd($insertData);
				if($insertData){
					GeneratedAssetsHistories::insert($insertData);
				}
				if($insertData){
					$data = ['status'=>'success', 'message'=>'History Search!'];
				}else{
					$data = ['status'=>'error', 'message'=>'Nothing Selected or No Data within Selected Filter!!!'];
				}
				
			}
			//dd($insertData);
			echo json_encode($data);
		}
		
		//get item description
		public function getassetDescription(Request $request) {
			$fields = Request::all();
			$asset_code = $fields['asset_code'];
			$data = AssetsInventoryBody::select(
				'assets_inventory_body.item_description as description'
				)
				->where('assets_inventory_body.asset_code', $asset_code)
				->get()->toArray();
			echo json_encode($data);
		}

		//Get Assets Hitory tru modal
		public function getComments(Request $request) {
			$fields = Request::all();
			$data = array();
			$data['status_no'] = 0;
			$data['message']   ='No Item Found!';
			$data['items'] = array();
			$asset_code = $fields['asset_code'];
			$comment = DB::table('comments_good_defect_tbl')
				->where('comments_good_defect_tbl.asset_code', $asset_code)	
				->where('comments_good_defect_tbl.comments', '!=' ,'OTHERS')
				->join('cms_users', 'comments_good_defect_tbl.users', '=', 'cms_users.id')
				->select(	'comments_good_defect_tbl.*',
							'cms_users.*'
						)
				->get();
			$other_comment = DB::table('comments_good_defect_tbl')
				->where('comments_good_defect_tbl.asset_code', $asset_code)	
				->where('comments_good_defect_tbl.comments', '=' ,'OTHERS')
				->join('cms_users', 'comments_good_defect_tbl.users', '=', 'cms_users.id')
				->select(DB::raw("CONCAT(comments_good_defect_tbl.comments ,'/', comments_good_defect_tbl.other_comment) AS comments, comments_good_defect_tbl.asset_code, cms_users.name, comments_good_defect_tbl.created_at as created_at")
						)
				->get();
			$items = $comment->toBase()->merge($other_comment);

			if($items){
					$data['id'] = $request->id;
					$data['status'] = 1;
					$data['problem']  = 1;
					$data['status_no'] = 1;
					$data['message']   ='Item Found';
					$i = 0;
					foreach ($items as $key => $value) {

						$return_data[$i]['id']          = 		$value->id;
						$return_data[$i]['asset_code'] = 		$value->asset_code;
						$return_data[$i]['comments'] = 		$value->comments;
						$return_data[$i]['name'] = 		$value->name;
						$return_data[$i]['created_at'] = 		$value->created_at;
						$i++;
	
					}
					$data['items'] = $return_data;
				}	
			echo json_encode($data);
			exit;  
		}

		function getSupplies(Request $request){
			$fields = Request::all();
			$search = $fields['query'];
			if($search){
				$data = DB::table('assets')
					->where('item_description', 'LIKE', "%{$search}%")
					->where('category_id', 2)
					->get();
				$output = '<ul class="dropdown-menu" style="display:block; position:relative;width:100%;height:auto;">';
				foreach($data as $row)
				{
					$output .= '
					<li><a class="dropdown-item" href="#">'.$row->item_description. " - " .$row->digits_code.'</a></li>
					';

				}
				$output .= '</ul>';
				
				echo $output;
			}
		}

		public function getExport(){
			return Excel::download(new ExportTamReportList, 'TAM-Report-List.xlsx');
		}

		public function itemITSearch(Request $request) {

			$request = Request::all();
			$search 		= $request['search'];

			$data = array();
			$data['status_no'] = 0;
			$data['message']   ='No Item Found!';
			$data['items'] = array();

			$item = DB::table('assets')
			->where('assets.digits_code','LIKE','%'.$search.'%')->whereNotIn('assets.status',['EOL-DIGITS','INACTIVE'])->whereNotNull('assets.from_dam')
			->orWhere('assets.item_description','LIKE','%'.$search.'%')->whereNotIn('assets.status',['EOL-DIGITS','INACTIVE'])->whereNotNull('assets.from_dam')
			->leftjoin('tam_categories', 'assets.tam_category_id','=', 'tam_categories.id')
			->leftjoin('tam_subcategories','assets.tam_sub_category_id','tam_subcategories.id')
			->leftjoin('category', 'assets.dam_category_id','=', 'category.id')
			->leftjoin('sub_category', 'assets.dam_class_id','=', 'sub_category.id')
			->select(	'assets.*',
						'assets.id as assetID',
						'tam_categories.category_description as tam_category_description',
						'tam_subcategories.subcategory_description as tam_sub_category_description',
						'category.category_description as dam_category_description',
						'sub_category.class_description as dam_sub_category_description'
					)
			->take(10)
			->get();

			$arraySearch = DB::table('assets_inventory_body')->select('digits_code as digits_code',DB::raw('SUM(quantity) as wh_qty'))->where('statuses_id',6)->groupBy('digits_code')->get()->toArray();
			$items = [];
			foreach($item as $itemKey => $itemVal){
				$i = array_search($itemVal->digits_code, array_column($arraySearch,'digits_code'));
				if($i !== false){
					$itemVal->inv_value = $arraySearch[$i];
					$items[] = $itemVal;
				}else{
					$itemVal->inv_value = "";
					$items[] = $itemVal;
				}
			}

			$arraySearchUnservedQty = DB::table('body_request')->select('digits_code as digits_code',DB::raw('SUM(unserved_qty) as unserved_qty'))->where('body_request.created_by',CRUDBooster::myId())->groupBy('digits_code')->get()->toArray();
			$finalItems = [];
			foreach($items as $itemsKey => $itemsVal){
				$i = array_search($itemsVal->digits_code, array_column($arraySearchUnservedQty,'digits_code'));
				if($i !== false){
					$itemsVal->unserved_qty = $arraySearchUnservedQty[$i];
					$finalItems[] = $itemsVal;
				}else{
					$itemsVal->unserved_qty = "";
					$finalItems[] = $itemsVal;
				}
			}

			//get reserved qty
			$reservedList = DB::table('assets_inventory_reserved')->select('digits_code as digits_code',DB::raw('SUM(approved_qty) as reserved_qty'))->whereNotNull('reserved')->groupBy('digits_code')->get()->toArray();
			$resultInventory = [];
			foreach($finalItems as $invKey => $invVal){
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

			if($finalInventory){
				$data['status'] = 1;
				$data['problem']  = 1;
				$data['status_no'] = 1;
				$data['message']   ='Item Found';
				$i = 0;
				foreach ($finalInventory as $key => $value) {

					$return_data[$i]['id']                       = 	$value->assetID;
					$return_data[$i]['digits_code']              = 	$value->digits_code;
					$return_data[$i]['item_description']         = 	$value->item_description;
					$return_data[$i]['category_description']     = 	$value->tam_category_description ? $value->tam_category_description : $value->dam_category_description;
					$return_data[$i]['sub_category_description'] =  $value->tam_sub_category_description ? $value->tam_sub_category_description : $value->dam_sub_category_description;
					$return_data[$i]['item_cost']                = 	$value->item_cost;
					$return_data[$i]['quantity']                 = 	$value->quantity;
					$return_data[$i]['total_quantity']           = 	$value->total_quantity;
					$return_data[$i]['wh_qty']                   =  $value->available_qty ? $value->available_qty : 0;
					$return_data[$i]['unserved_qty']             =  $value->unserved_qty->unserved_qty ? $value->unserved_qty->unserved_qty : 0;

					$i++;

				}
				$data['items'] = $return_data;
			}

			echo json_encode($data);
			exit;  
		}

		public function itemFASearch(Request $request) {

			$request = Request::all();
			$search 		= $request['search'];

			$data = array();
			$data['status_no'] = 0;
			$data['message']   ='No Item Found!';
			$data['items'] = array();

			$item = DB::table('assets')
			->where('assets.digits_code','LIKE','%'.$search.'%')->whereNotIn('assets.status',['EOL-DIGITS','INACTIVE'])->whereNull('assets.from_dam')
			->orWhere('assets.item_description','LIKE','%'.$search.'%')->whereNotIn('assets.status',['EOL-DIGITS','INACTIVE'])->whereNull('assets.from_dam')
			->leftjoin('tam_categories', 'assets.tam_category_id','=', 'tam_categories.id')
			->leftjoin('tam_subcategories','assets.tam_sub_category_id','tam_subcategories.id')
			->select(	'assets.*',
						'assets.id as assetID',
						'tam_categories.category_description as tam_category_description',
						'tam_subcategories.subcategory_description as tam_sub_category_description',
					)
			->take(10)
			->get();
					
			$arraySearch = DB::table('assets_inventory_body')->select('digits_code as digits_code',DB::raw('SUM(quantity) as wh_qty'))->where('statuses_id',6)->groupBy('digits_code')->get()->toArray();
			$items = [];
			foreach($item as $itemKey => $itemVal){
				$i = array_search($itemVal->digits_code, array_column($arraySearch,'digits_code'));
				if($i !== false){
					$itemVal->inv_value = $arraySearch[$i];
					$items[] = $itemVal;
				}else{
					$itemVal->inv_value = "";
					$items[] = $itemVal;
				}
			}

			$arraySearchUnservedQty = DB::table('body_request')->select('digits_code as digits_code',DB::raw('SUM(unserved_qty) as unserved_qty'))->where('body_request.created_by',CRUDBooster::myId())->groupBy('digits_code')->get()->toArray();
			$finalItems = [];
			foreach($items as $itemsKey => $itemsVal){
				$i = array_search($itemsVal->digits_code, array_column($arraySearchUnservedQty,'digits_code'));
				if($i !== false){
					$itemsVal->unserved_qty = $arraySearchUnservedQty[$i];
					$finalItems[] = $itemsVal;
				}else{
					$itemsVal->unserved_qty = "";
					$finalItems[] = $itemsVal;
				}
			}

			//get reserved qty
			$reservedList = DB::table('assets_inventory_reserved')->select('digits_code as digits_code',DB::raw('SUM(approved_qty) as reserved_qty'))->whereNotNull('reserved')->groupBy('digits_code')->get()->toArray();
			$resultInventory = [];
			foreach($finalItems as $invKey => $invVal){
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

			if($finalInventory){
				$data['status'] = 1;
				$data['problem']  = 1;
				$data['status_no'] = 1;
				$data['message']   ='Item Found';
				$i = 0;
				foreach ($finalInventory as $key => $value) {

					$return_data[$i]['id']                       = 	$value->assetID;
					$return_data[$i]['asset_code']               = 	$value->asset_code;
					$return_data[$i]['digits_code']              = 	$value->digits_code;
					$return_data[$i]['asset_tag']                = 	$value->asset_tag;
					$return_data[$i]['serial_no']                = 	$value->serial_no;
					$return_data[$i]['item_description']         = 	$value->item_description;
					$return_data[$i]['category_description']     = 	$value->tam_category_description;
					$return_data[$i]['sub_category_description'] =  $value->tam_sub_category_description;
					$return_data[$i]['item_cost']                = 	$value->item_cost;
					$return_data[$i]['quantity']                 = 	$value->quantity;
					$return_data[$i]['total_quantity']           = 	$value->total_quantity;
					$return_data[$i]['wh_qty']                   =  $value->wh_qty  ? $value->wh_qty : 0;
					$return_data[$i]['unserved_qty']             =  $value->unserved_qty  ? $value->unserved_qty : 0;
					$return_data[$i]['wh_qty']                   =  $value->available_qty  ? $value->available_qty : 0;
					$return_data[$i]['unserved_qty']             =  $value->unserved_qty->unserved_qty  ? $value->unserved_qty->unserved_qty : 0;

					$i++;

				}
				$data['items'] = $return_data;
			}

			echo json_encode($data);
			exit;  
		}

		public function itemNonTradeSearch(Request $request) {
			$request = Request::all();
			$search 		= $request['search'];
			$data = array();
			$data['status_no'] = 0;
			$data['message']   ='No Item Found!';
			$data['items'] = array();

			$items = DB::table('assets')
			->where('assets.digits_code','LIKE','%'.$search.'%')->whereNotIn('assets.status',['EOL-DIGITS','INACTIVE'])->whereNull('assets.from_dam')
			->orWhere('assets.item_description','LIKE','%'.$search.'%')->whereNotIn('assets.status',['EOL-DIGITS','INACTIVE'])->whereNull('assets.from_dam')
			->leftjoin('tam_categories', 'assets.tam_category_id','=', 'tam_categories.id')
			->leftjoin('tam_subcategories','assets.tam_sub_category_id','tam_subcategories.id')
			->leftjoin('assets_non_trade_inventory_body', 'assets.digits_code','=', 'assets_non_trade_inventory_body.digits_code')

			->select(	'assets.*',
						'assets.id as assetID',
						'assets_non_trade_inventory_body.quantity as wh_qty',
						'tam_categories.category_description as tam_category_description',
						'tam_subcategories.subcategory_description as tam_sub_category_description',
					)
			->take(10)
			->get();

			$arraySearchUnservedQty = DB::table('body_request')->select('digits_code as digits_code',DB::raw('SUM(unserved_qty) as unserved_qty'))->where('body_request.created_by',CRUDBooster::myId())->groupBy('digits_code')->get()->toArray();
			$finalItems = [];
			foreach($items as $itemsKey => $itemsVal){
				$i = array_search($itemsVal->digits_code, array_column($arraySearchUnservedQty,'digits_code'));
				if($i !== false){
					$itemsVal->unserved_qty = $arraySearchUnservedQty[$i];
					$finalItems[] = $itemsVal;
				}else{
					$itemsVal->unserved_qty = "";
					$finalItems[] = $itemsVal;
				}
			}
			if($finalItems){
				$data['status'] = 1;
				$data['problem']  = 1;
				$data['status_no'] = 1;
				$data['message']   ='Item Found';
				$i = 0;
				foreach ($finalItems as $key => $value) {

					$return_data[$i]['id']                   = $value->assetID;
					$return_data[$i]['asset_code']           = $value->asset_code;
					$return_data[$i]['digits_code']          = $value->digits_code;
					$return_data[$i]['asset_tag']            = $value->asset_tag;
					$return_data[$i]['serial_no']            = $value->serial_no;
					$return_data[$i]['item_description']     = $value->item_description;
					$return_data[$i]['category_description'] = $value->tam_category_description;
					$return_data[$i]['class_description']    = $value->tam_sub_category_description;
					$return_data[$i]['item_cost']            = $value->item_cost;
					$return_data[$i]['item_type']            = $value->item_type;
					$return_data[$i]['image']                = $value->image;
					$return_data[$i]['quantity']             = $value->quantity;
					$return_data[$i]['total_quantity']       = $value->total_quantity;
					$return_data[$i]['wh_qty']               = $value->wh_qty  ? $value->wh_qty : 0;
					$return_data[$i]['unserved_qty']         = $value->unserved_qty->unserved_qty  ? $value->unserved_qty->unserved_qty : 0;
					$i++;

				}
				$data['items'] = $return_data;
			}

			echo json_encode($data);
			exit;  
		}

		public function itemSuppliesSearch(Request $request) {
			$request = Request::all();
			$search 		= $request['search'];
			$data = array();
			$data['status_no'] = 0;
			$data['message']   ='No Item Found!';
			$data['items'] = array();

			$items = DB::table('assets')
			->where('assets.digits_code','LIKE','%'.$search.'%')->where('assets.status','!=','INACTIVE')
			->orWhere('assets.item_description','LIKE','%'.$search.'%')->where('assets.status','!=','INACTIVE')
			->leftjoin('tam_categories', 'assets.category_id','=', 'tam_categories.id')
			->leftjoin('class', 'assets.class_id','=', 'class.id')
			->leftjoin('tam_subcategories', 'assets.class_id','=', 'tam_subcategories.id')
			->leftjoin('assets_supplies_inventory', 'assets.digits_code','=', 'assets_supplies_inventory.digits_code')

			->select(	'assets.*',
						'assets.id as assetID',
						'assets_supplies_inventory.quantity as wh_qty',
						'tam_categories.category_description as category_description',
						'class.class_description as class_description',
						'tam_subcategories.subcategory_description as sub_category_description',
					)
			->take(10)
			->get();

			$arraySearchUnservedQty = DB::table('body_request')->select('digits_code as digits_code',DB::raw('SUM(unserved_qty) as unserved_qty'))->where('body_request.created_by',CRUDBooster::myId())->groupBy('digits_code')->get()->toArray();
			$finalItems = [];
			foreach($items as $itemsKey => $itemsVal){
				$i = array_search($itemsVal->digits_code, array_column($arraySearchUnservedQty,'digits_code'));
				if($i !== false){
					$itemsVal->unserved_qty = $arraySearchUnservedQty[$i];
					$finalItems[] = $itemsVal;
				}else{
					$itemsVal->unserved_qty = "";
					$finalItems[] = $itemsVal;
				}
			}
			if($finalItems){
				$data['status'] = 1;
				$data['problem']  = 1;
				$data['status_no'] = 1;
				$data['message']   ='Item Found';
				$i = 0;
				foreach ($finalItems as $key => $value) {

					$return_data[$i]['id']                   = $value->assetID;
					$return_data[$i]['asset_code']           = $value->asset_code;
					$return_data[$i]['digits_code']          = $value->digits_code;
					$return_data[$i]['asset_tag']            = $value->asset_tag;
					$return_data[$i]['serial_no']            = $value->serial_no;
					$return_data[$i]['item_description']     = $value->item_description;
					$return_data[$i]['category_description'] = $value->category_description;
					$return_data[$i]['class_description']    = $value->sub_category_description;
					$return_data[$i]['item_cost']            = $value->item_cost;
					$return_data[$i]['item_type']            = $value->item_type;
					$return_data[$i]['image']                = $value->image;
					$return_data[$i]['quantity']             = $value->quantity;
					$return_data[$i]['total_quantity']       = $value->total_quantity;
					$return_data[$i]['wh_qty']               = $value->wh_qty  ? $value->wh_qty : 0;
					$return_data[$i]['unserved_qty']         = $value->unserved_qty->unserved_qty  ? $value->unserved_qty->unserved_qty : 0;
					$i++;

				}
				$data['items'] = $return_data;
			}

			echo json_encode($data);
			exit;  
		}

		public function cancelArfRequest(Request $request){
			$data = Request::all();	
			$id   = $data['id'];
			$remarks = $data['remarks'];
			
			HeaderRequest::where('id', $id)
			->update([
				'status_id'=> 8,
				'to_mo'    => 0
			]);	

			BodyRequest::where('header_request_id', $id)
			->update([
				'unserved_rep_qty' => 0, 
				'unserved_ro_qty'  => 0,
				'serve_qty'        => 0,
				'unserved_qty'     => 0, 
				'cancelled_qty'    => 1, 
				'reason_to_cancel' => $remarks,
				'deleted_at'       => date('Y-m-d H:i:s'),
				'deleted_by'       => CRUDBooster::myId()
			]);	

			$arf_number = HeaderRequest::where('id',$id)->first();

			DB::table('assets_inventory_reserved')->where('reference_number', $arf_number->reference_number)->delete();

			$message = ['status'=>'success', 'message' => 'Cancelled Successfully!','redirect_url'=>CRUDBooster::mainpath()];
			echo json_encode($message);
			
		}

		public function cancelArfMoPerLineRequest(Request $request){
			$data     = Request::all();	
			$id       = $data['id'];
			$body_ids = $data['Ids'];
			$remarks  = $data['remarks'];
			
			for($x=0; $x < count((array)$body_ids); $x++) {
				BodyRequest::where('id', $body_ids[$x])
				->update([
					'unserved_rep_qty' => 0, 
					'unserved_ro_qty'  => 0,
					'serve_qty'        => 0,
					'unserved_qty'     => 0, 
					'cancelled_qty'    => 1, 
					'reason_to_cancel' => $remarks,
					'deleted_at'       => date('Y-m-d H:i:s'),
					'deleted_by'       => CRUDBooster::myId()
				]);	

				DB::table('assets_inventory_reserved')->where('body_id', $body_ids[$x])->delete();
			}

			$header           = DB::table('header_request')->where('id',$id)->first();
			$bodyCountAll     = DB::table('body_request')->where('header_request_id',$id)->count();
			$bodyCountDeleted = DB::table('body_request')->where('header_request_id',$id)->whereNotNull('deleted_at')->count();
			$bodyCountMo      = DB::table('mo_body_request')->where('header_request_id',$id)->where('status_id', '!=', 8)->count();
			$getStatus        = DB::table('mo_body_request')->where('header_request_id',$id)->where('status_id', '!=', 8)->first();

			if($bodyCountMo == 0){
				if($bodyCountAll ==  $bodyCountDeleted){
					HeaderRequest::where('id', $id)
					->update([
						'status_id' => 8,
						'to_mo'     => 0
					]);	
				}
			}else{
				if($bodyCountAll ==  ($bodyCountDeleted + $bodyCountMo)){
					HeaderRequest::where('id', $id)
					->update([
						'status_id' => $getStatus->status_id,
						'to_mo'    => 0
					]);	
				}
			}

			$message = ['status'=>'success', 'message' => 'Cancelled Successfully!','redirect_url'=>CRUDBooster::mainpath()];
			echo json_encode($message);
			
		}

		public function getDownload() {
			$file= public_path(). "/vendor/crudbooster/it_assets_price/IT Assets Pricelist.xlsx";
            if(in_array($getFile->ext,['xlsx','docs','pdf'])){
			    $headers = array(
					'Content-Type: application/pdf',
					);
			    return Response::download($file, $getFile->file_name, $headers);
			}else{
				return Response::download($file, $getFile->file_name);
			}
		}

		//EDIT REQUEST FROM RETURN APPROVAL
		public function editRequestAssets(Request $request){
			$fields             = Request::all();
			$dataLines          = array();
			$arf_header         = DB::table('header_request')->where(['id' => $fields['headerID']])->first();
			$digits_code 		= $fields['digits_code'];
			$supplies_cost 		= $fields['supplies_cost'];
			$item_description 	= $fields['item_description'];
			$category 		    = $fields['category'];
			$sub_category 	    = $fields['sub_category'];
			$app_id_others 		= $fields['app_id_others'];
			$quantity 			= $fields['quantity'];
			$request_type_id 	= $fields['request_type_id'];
			$budget_range 	    = $fields['budget_range'];
			$app_count          = 2;
			$body_budget_range 	= $fields['body_budget_range'];

			if(!empty($fields['application'])){
				$application 				        = implode(", ",$fields['application']);
				$application_others		            = $fields['application_others'];
			}

			//UPDATE HEADER
			HeaderRequest::where('id',$fields['headerID'])
			->update([
					'status_id'          => $this->pending,
					'purpose'            => $fields['purpose'],
					'quantity_total'     => $fields['quantity_total'],
					'application'        => $application,
					'application_others' => $application_others,
					'requestor_comments' => $fields['requestor_comments']
			]);	
			//UPDATE BODY
			foreach($fields['body_id'] as $key => $line){
				BodyRequest::where('id',$line)
				->update([
						'budget_range'   => $body_budget_range[$key]
				]);	
			}

			//INSERT NEW LINES
			if(is_array($digits_code)){
				foreach($digits_code as $key => $val){
					$apps_array = array();
					$app_no     = 'app_id'.$app_count;
					$app_id     = $fields[$app_no];
					for($xxx=0; $xxx < count((array)$app_id); $xxx++) {
						array_push($apps_array,$app_id[$xxx]); 
					}

					$dataLines[$key]['header_request_id']   = $arf_header->id;
					$dataLines[$key]['digits_code'] 	    = $digits_code[$key];
					$dataLines[$key]['item_description'] 	= $item_description[$key];
					$dataLines[$key]['category_id'] 		= $category[$key];
					$dataLines[$key]['sub_category_id'] 	= $sub_category[$key];
					$dataLines[$key]['app_id'] 			    = implode(", ",$apps_array);
					$dataLines[$key]['app_id_others'] 	    = $app_id_others[$key];
					$dataLines[$key]['quantity'] 			= 1;
					$dataLines[$key]['unit_cost'] 		    = $supplies_cost[$key];
					$dataLines[$key]['budget_range'] 		= $budget_range[$key];
					$dataLines[$key]['created_by'] 		    = CRUDBooster::myId();
					$dataLines[$key]['created_at'] 		    = date('Y-m-d H:i:s');

					unset($apps_array);
				}

				DB::beginTransaction();
				try {
					BodyRequest::insert($dataLines);
					DB::commit();
				} catch (\Exception $e) {
					DB::rollback();
					CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.alert_database_error",['database_error'=>$e]), 'danger');
				}
				
			}

			//DELETE LINES IF SELECTED
			foreach($fields['deleteRowData'] as $dKey => $dLine){
				BodyRequest::where('id', $dLine)
				->update([
					'deleted_at'=> 		date('Y-m-d H:i:s'),
					'deleted_by'=> 		CRUDBooster::myId()
				]);	
			}
			
			CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.alert_edit_employee",['reference_number'=>$arf_header->reference_number]), 'success');

		}

		// //DELETE LINES FROM RETURN APPROVAL
		// public function deleteLinetAssetsFromApproval(Request $request){
		// 	$fields = Request::all();
		// 	$id     = $fields['lineId'];
		// 	BodyRequest::where('id', $id)
		// 	->update([
		// 		'deleted_at'=> 		date('Y-m-d H:i:s'),
		// 		'deleted_by'=> 		CRUDBooster::myId()
		// 	]);	
		// 	$message = ['status'=>'success', 'message' => 'Delete Successfully!','redirect_url'=>CRUDBooster::mainpath()];
		// 	echo json_encode($message);
		// }
	}