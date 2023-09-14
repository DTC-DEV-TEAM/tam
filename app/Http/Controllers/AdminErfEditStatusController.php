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
	use App\Models\AssetsSuppliesInventory;
	use App\Models\AssetsInventoryReserved;
	use Illuminate\Support\Facades\Response;
	use App\HeaderRequest;
	use App\BodyRequest;
	use App\Statuses;
	use App\Models\Applicant;
	use App\Mail\EmailErfClose;
	use Illuminate\Support\Facades\Hash;
	use Mail;

	class AdminErfEditStatusController extends \crocodicstudio\crudbooster\controllers\CBController {
		private $cancelled;  
		private $pending;  
		private $rejected;  
		private $for_verification;  
		private $verified;  
		private $jo_done;    
		private $onboarding;   
		private $closed; 
		private $onboarded;  

		public function __construct() {
			DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping("enum", "string");
			$this->cancelled        =  8;  
			$this->pending          =  1;  
			$this->rejected         =  5;  
			$this->for_verification =  29;  
			$this->verified         =  30;  
			$this->jo_done          =  31;    
			$this->onboarding       =  33;   
			$this->closed           =  13;
			$this->onboarded        =  43;  
		}
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
			$this->col[] = ["label"=>"Locking","name"=>"locking_edit","visible"=>false];
			$this->col[] = ["label"=>"Locking Create Account","name"=>"locking_create_account","visible"=>false];
			$this->col[] = ["label"=>"Locking Onboarding Date","name"=>"locking_onboarding_date","visible"=>false];
			$this->col[] = ["label"=>"Locking Close","name"=>"locking_close","visible"=>false];
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
				$for_verification = 29;
				$jo_done          = 31;
				$for_onboarding   = 33; 
				$onboarded        = 43; 
                $id = CRUDBooster::myId();

				//locking in edit for verification
				$this->addaction[] = ['title'=>'Update','url'=>CRUDBooster::mainpath('getEditErf/[id]'),'icon'=>'fa fa-pencil' , "showIf"=>"[status_id] == $for_verification && [locking_edit] == null || [locking_edit] == $id"];
				$this->addaction[] = ['title'=>'LockEdit','url'=>CRUDBooster::mainpath('getLockingForm/[id]'),'icon'=>'fa fa-pencil' , "showIf"=>"[status_id] == $for_verification && [locking_edit] != null && [locking_edit] != $id"];
				
				//locking in creating  account
				$this->addaction[] = ['title'=>'Create Account','url'=>CRUDBooster::mainpath('getErfCreateAccount/[id]'),'icon'=>'fa fa-pencil', "showIf"=>"[status_id] == $jo_done && [locking_create_account] == null || [locking_create_account] == $id"];
				$this->addaction[] = ['title'=>'Lock Create Account','url'=>CRUDBooster::mainpath('getLockingErfCreateAccountForm/[id]'),'icon'=>'fa fa-pencil', "showIf"=>"[status_id] == $jo_done && [locking_create_account] != null && [locking_create_account] != $id"];

				//Locking onboarding date
				$this->addaction[] = ['title'=>'Set Onboarding Date','url'=>CRUDBooster::mainpath('getErfSetOnboardingDate/[id]'),'icon'=>'fa fa-pencil', "showIf"=>"[status_id] == $for_onboarding && [locking_onboarding_date] == null || [locking_onboarding_date] == $id"];
				$this->addaction[] = ['title'=>'Locking Onboarding Date','url'=>CRUDBooster::mainpath('getLockingErfSetOnboardingDateForm/[id]'),'icon'=>'fa fa-pencil', "showIf"=>"[status_id] == $for_onboarding && [locking_onboarding_date] != null && [locking_onboarding_date] != $id"];
				
				//Closed Request
				$this->addaction[] = ['title'=>'Close Request','url'=>CRUDBooster::mainpath('getErfCloseRequest/[id]'),'icon'=>'fa fa-pencil', "showIf"=>"[status_id] == $onboarded && [locking_close] == null || [locking_close] == $id"];
				$this->addaction[] = ['title'=>'Locking Close Request','url'=>CRUDBooster::mainpath('getLockingErfCloseRequest/[id]'),'icon'=>'fa fa-pencil', "showIf"=>"[status_id] == $onboarded && [locking_close] != null && [locking_close] != $id"];
				
				$this->addaction[] = ['title'=>'Detail','url'=>CRUDBooster::mainpath('getDetailErf/[id]'),'icon'=>'fa fa-eye', "showIf"=>"[status_id] != $for_verification && [status_id] != $for_onboarding && [status_id] != $onboarded"];

				$this->addaction[] = ['title'=>'Print PDF','url'=>CRUDBooster::mainpath('getDetailPrintErf/[id]'),'icon'=>'fa fa-print'];
				
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
			$this->script_js = "
			$(document).ready(function() {
				$('a[title=\"Update\"]').click(function(e){
					var id = $(this).attr('href').split('/').pop();
					$.ajaxSetup({
						headers: {
									'X-CSRF-TOKEN': $('meta[name=\"csrf-token\"]').attr('content')
								}
					});
					$.ajax({
						type: 'POST',
						url: '".route('locking-form')."',
						dataType: 'json',
						data: {
							'header_request_id': id
						},
						success: function(response) {
							if (response.status == \"success\") {
								swal({
									type: response.status,
									title: response.message,
								});

								window.location.replace(response.redirect_url);
								} else if (response.status == \"error\") {
								swal({
									type: response.status,
									title: response.message,
								});
								}
						},
						error: function(e) {
							console.log(e);
						}
					});
                   
				});

				//CREATE ACCOUNT LOCKING
				$('a[title=\"Create Account\"]').click(function(e){
					var id = $(this).attr('href').split('/').pop();
					$.ajaxSetup({
						headers: {
									'X-CSRF-TOKEN': $('meta[name=\"csrf-token\"]').attr('content')
								}
					});
					$.ajax({
						type: 'POST',
						url: '".route('locking-form-create-account')."',
						dataType: 'json',
						data: {
							'header_request_id': id
						},
						success: function(response) {
							if (response.status == \"success\") {
								swal({
									type: response.status,
									title: response.message,
								});

								window.location.replace(response.redirect_url);
								} else if (response.status == \"error\") {
								swal({
									type: response.status,
									title: response.message,
								});
								}
						},
						error: function(e) {
							console.log(e);
						}
					});
                   
				});

				//ONBARDING DATE LOCKING
				$('a[title=\"Set Onboarding Date\"]').click(function(e){
					var id = $(this).attr('href').split('/').pop();
					$.ajaxSetup({
						headers: {
									'X-CSRF-TOKEN': $('meta[name=\"csrf-token\"]').attr('content')
								}
					});
					$.ajax({
						type: 'POST',
						url: '".route('locking-form-onboarding-date')."',
						dataType: 'json',
						data: {
							'header_request_id': id
						},
						success: function(response) {
							if (response.status == \"success\") {
								swal({
									type: response.status,
									title: response.message,
								});

								window.location.replace(response.redirect_url);
								} else if (response.status == \"error\") {
								swal({
									type: response.status,
									title: response.message,
								});
								}
						},
						error: function(e) {
							console.log(e);
						}
					});
                   
				});

				//CLOSE REQUEST LOCKING
				$('a[title=\"Close Request\"]').click(function(e){
					var id = $(this).attr('href').split('/').pop();
					$.ajaxSetup({
						headers: {
									'X-CSRF-TOKEN': $('meta[name=\"csrf-token\"]').attr('content')
								}
					});
					$.ajax({
						type: 'POST',
						url: '".route('locking-form-close-request')."',
						dataType: 'json',
						data: {
							'header_request_id': id
						},
						success: function(response) {
							if (response.status == \"success\") {
								swal({
									type: response.status,
									title: response.message,
								});

								window.location.replace(response.redirect_url);
								} else if (response.status == \"error\") {
								swal({
									type: response.status,
									title: response.message,
								});
								}
						},
						error: function(e) {
							console.log(e);
						}
					});
                   
				});
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
	        $this->load_js[] = asset("js/spinner.js");
	        
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
			$this->load_js[] = asset("html2pdf/dist/html2pdf.bundle.min.js");
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
				$query->whereNull('erf_header_request.deleted_at')->orderBy('erf_header_request.status_id', 'DESC')->orderBy('erf_header_request.id', 'DESC');
			}else{
				$query->whereNull('erf_header_request.deleted_at')->whereIn('status_id',[13,29,30,31,33,43])->orderBy('erf_header_request.id', 'DESC');
			}
	            
	            
	    }

	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate row of index table html 
	    | ---------------------------------------------------------------------- 
	    |
	    */    
	    public function hook_row_index($column_index,&$column_value) {	        
			$cancelled        =  DB::table('statuses')->where('id', $this->cancelled)->value('status_description');  
	    	$pending          =  DB::table('statuses')->where('id', $this->pending)->value('status_description');  
			$rejected         =  DB::table('statuses')->where('id', $this->rejected)->value('status_description');  
			$for_verification =  DB::table('statuses')->where('id', $this->for_verification)->value('status_description');  
			$verified         =  DB::table('statuses')->where('id', $this->verified)->value('status_description');  
			$jo_done          =  DB::table('statuses')->where('id', $this->jo_done)->value('status_description');    
			$onboarding       =  DB::table('statuses')->where('id', $this->onboarding)->value('status_description'); 
			$onboarded        =  DB::table('statuses')->where('id', $this->onboarded)->value('status_description');   
			$closed           =  DB::table('statuses')->where('id', $this->closed)->value('status_description');   
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
				}else if($column_value == $onboarded){
					$column_value = '<span class="label label-info">'.$onboarded.'</span>';
				}else if($column_value == $closed){
					$column_value = '<span class="label label-success">'.$closed.'</span>';
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
			$fields             = Request::all();
			$hr_comments 		= $fields['additional_notess'];
			$approval_action 	= $fields['approval_action'];

			$erf_header = ErfHeaderRequest::where(['id' => $id])->first();
			$erf_body = ErfBodyRequest::where(['header_request_id' => $id])->get();
			$req_type = ErfBodyRequest::where(['header_request_id' => $id])->groupBy('request_type_id')->get();
			$latestRequest = DB::table('header_request')->select('id')->orderBy('id','DESC')->first();
			$latestRequestId = $latestRequest->id != NULL ? $latestRequest->id : 0;
			
			if($approval_action  == 1){
				ErfHeaderRequest::where('id',$id)
				->update([
					'status_id'		                    => $this->verified,
				    'hr_comments'	                    => $hr_comments,
				    'approved_hr_by' 		            => CRUDBooster::myId(),
				    'approved_hr_at' 		            => date('Y-m-d H:i:s'),
					'locking_edit'                      => NULL,
				]);	
				//add in arf heaader request table
			$count_header       = DB::table('header_request')->count();
		    if($erf_body){
				$arfHeaderSave = [];
				$arfHeaderContainer = [];
				foreach($req_type as $arfHeadKey => $arfHeadVal){
					if($arfHeadVal['request_type_id'] == 1){
						$arfHeaderContainer['status_id']              = 14;
						$arfHeaderContainer['application'] 			  = $erf_header->application;
						$arfHeaderContainer['application_others'] 	  = $erf_header->application_others;
						$arfHeaderContainer['to_reco']                = 1;
					}else{
						$arfHeaderContainer['status_id']              = 14;
						$arfHeaderContainer['application'] 			  = NULL;
						$arfHeaderContainer['application_others'] 	  = NULL;  
						$arfHeaderContainer['to_reco']                = 0;
					}
					$arfHeaderContainer['reference_number']		      = "ARF-".str_pad($count_header + 1, 7, '0', STR_PAD_LEFT);
					$count_header ++;
					$arfHeaderContainer['employee_name' ]		      = $erf_header->reference_number;
					$arfHeaderContainer['company_name'] 			  = "DIGITS";
					$arfHeaderContainer['position'] 				  = $erf_header->position;
					$arfHeaderContainer['department' ]				  = $erf_header->department;
					$arfHeaderContainer['store_branch']		          = NULL;
					$arfHeaderContainer['purpose'] 				      = 6;
					$arfHeaderContainer['conditions'] 				  = NULL;
					$arfHeaderContainer['quantity_total'] 			  = $arfHeadVal->quantity;
					$arfHeaderContainer['cost_total'] 				  = NULL;
					$arfHeaderContainer['total'] 					  = NULL;
					$arfHeaderContainer['requestor_comments'] 		  = NULL;
					$arfHeaderContainer['created_by'] 				  = NULL;
					$arfHeaderContainer['created_at'] 				  = date('Y-m-d H:i:s');
					$arfHeaderContainer['approved_by'] 		          = CRUDBooster::myId();
				    $arfHeaderContainer['approved_at'] 		          = date('Y-m-d H:i:s');
					$arfHeaderContainer['request_type_id']		 	  = $arfHeadVal['request_type_id'];
					$arfHeaderContainer['privilege_id']		 	      = NULL;
					$arfHeaderContainer['if_from_erf' ]		          = $erf_header->reference_number;
				
					$arfHeaderSave[] = $arfHeaderContainer;
				}
				$result = HeaderRequest::insert($arfHeaderSave);
                $arfHeaderId = $result->id;
				$itId = DB::table('header_request')->select('*')->where('id','>', $latestRequestId)->where('request_type_id',1)->first();
				$faId = DB::table('header_request')->select('*')->where('id','>', $latestRequestId)->where('request_type_id',5)->first();
				$SuppliesId = DB::table('header_request')->select('*')->where('id','>', $latestRequestId)->where('request_type_id',7)->first();
		
				$resultArrforIT = [];
				foreach($erf_body as $item){
					if($item['request_type_id'] == 1){
						for($i = 0; $i < $item['request_type_id']; $i++){
							$t = $item;
							$t['header_request_id'] = $itId->id;
							$resultArrforIT[] = $t;
						}
					}
				}

				$resultArrforFA = [];
				foreach($erf_body as $itemFa){
					if($itemFa['request_type_id'] == 5){
						for($x = 0; $x < $itemFa['request_type_id']; $x++){
							$fa = $itemFa;
							$fa['header_request_id'] = $faId->id;
							$resultArrforFA[] = $fa;
						}
					}
				}

				$resultArrforSu = [];	
				foreach($erf_body as $itemSu){
					if($itemSu['request_type_id'] == 7){
						for($s = 0; $s < $itemSu['request_type_id']; $s++){
							$su = $itemSu;
							$su['header_request_id'] = $SuppliesId->id;
							$resultArrforSu[] = $su;
						}
					}
				}
				$arf_ids = [];
				if($itId->id){
					array_push($arf_ids, $itId->id);
				}
				if($faId->id){
					array_push($arf_ids, $faId->id);
				}
				if($SuppliesId->id){
					array_push($arf_ids, $SuppliesId->id);
				}

				$arf_id =  implode(", ",$arf_ids);

				ErfHeaderRequest::where('id',$id)
					->update([
						'arf_id'                     => $arf_id,
						'to_tag_employee'            => 1
					]);	

				//save items in Body Request
				$insertData = [];
				$insertContainer = [];
				foreach($erf_body as $key => $val){
					$insertContainer['header_request_id']   = $val['header_request_id'];
					$insertContainer['digits_code'] 	    = $val['digits_code'];
					$insertContainer['item_description'] 	= $val['item_description'];
					$insertContainer['category_id'] 		= $val['category_id'];
					$insertContainer['sub_category_id'] 	= $val['sub_category_id'];
					$insertContainer['app_id'] 			    = NULL;
					$insertContainer['app_id_others'] 	    = NULL;
					$insertContainer['quantity'] 			= $val['quantity'];
					$insertContainer['unit_cost'] 		    = NULL;
					if($request_type_id == 5){
						$insertContainer['to_reco'] = 0;
					}else{
						if (str_contains($val['sub_category_id'], 'ITA-COMPUTER EQUIPMENT')) {
							$insertContainer['to_reco'] = 1;
						}else{
							$insertContainer['to_reco'] = 0;
						}
					}
					$insertContainer['created_at'] 		= date('Y-m-d H:i:s');
					$insertData[] = $insertContainer;
				}
			
				DB::beginTransaction();
				try {
					BodyRequest::insert($insertData);
					DB::commit();

					// //manage replenishment
					$arf_header = HeaderRequest::where(['id' => $itId->id])->whereNull('deleted_at')->first();
					$arf_body = BodyRequest::where(['header_request_id' => $itId->id])->whereNull('deleted_at')->get();
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

							HeaderRequest::where('id',$itId->id)
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
				} catch (\Exception $e) {
					DB::rollback();
					CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.alert_database_error",['database_error'=>$e]), 'danger');
				}
				
				CRUDBooster::redirect(CRUDBooster::mainpath(), trans('Successfully Added!'), 'success');
		    }
			}else{
				ErfHeaderRequest::where('id',$id)
				->update([
					'status_id'		                    => $this->rejected,
				    'hr_comments'	                    => $hr_comments,
				    'approved_hr_by' 		            => CRUDBooster::myId(),
				    'approved_hr_at' 		            => date('Y-m-d H:i:s'),
					'locking_edit'                      => NULL,
				]);	
			}
			CRUDBooster::redirect(CRUDBooster::mainpath(), trans('Successfully Rejected!'), 'success');

	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command after edit public static function called
	    | ----------------------------------------------------------------------     
	    | @id       = current id 
	    | 
	    */
	    public function hook_after_edit($id) {
			$fields = Request::all();

			// $erf_header = erfHeaderRequest::where(['id' => $id])->first();
			// $hired         =   32;

			// if($erf_header->status_id  == $hired){
			// 	CRUDBooster::redirect(CRUDBooster::adminpath('users/add'), trans("crudbooster.alert_for_hired_success",['reference_number'=>$erf_header->reference_number]), 'success');
			// }else{
			// 	CRUDBooster::redirect(CRUDBooster::mainpath(), trans("Update Successfully!"), 'success');
			// }

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

		public function getEditErf($id){
			
			$this->cbLoader();
            if(!CRUDBooster::isRead() && $this->global_privilege==FALSE) {    
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

			$data = array();

			$data['page_title'] = 'Employee Requisition Form Verification';

			$data['Header'] = ErfHeaderRequest::
				leftjoin('companies', 'erf_header_request.company', '=', 'companies.id')
				->leftjoin('departments', 'erf_header_request.department', '=', 'departments.id')
				->leftjoin('cms_users as approver', 'erf_header_request.approved_immediate_head_by', '=', 'approver.id')
				->leftjoin('cms_users as verifier', 'erf_header_request.approved_hr_by', '=', 'verifier.id')
				->leftjoin('cms_users as currentUser', 'erf_header_request.locking_edit', '=', 'currentUser.id')
				->leftJoin('applicant_table', function($join) 
				{
					$join->on('erf_header_request.reference_number', '=', 'applicant_table.erf_number')
					->where('applicant_table.status',$this->jo_done);
				})
				->select(
						'erf_header_request.*',
						'erf_header_request.id as requestid',
						'approver.name as approved_head_by',
						'verifier.name as verified_by',
						'currentUser.name as current_user',
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
			$data['statuses'] = Statuses::select(
					'statuses.*'
				  )
				  ->whereIn('id', [29,30,31,32])
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
			return $this->view("erf.erf_hr_approval", $data);
		}

		public function getDetailErf($id){
			$this->cbLoader();
            if(!CRUDBooster::isRead() && $this->global_privilege==FALSE) {    
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

			$data = array();

			$data['page_title'] = 'Edit Employee Requisition Form Status';

			$data['Header'] = ErfHeaderRequest::
				leftjoin('companies', 'erf_header_request.company', '=', 'companies.id')
				->leftjoin('departments', 'erf_header_request.department', '=', 'departments.id')
				->leftjoin('cms_users as approver', 'erf_header_request.approved_immediate_head_by', '=', 'approver.id')
				->leftjoin('cms_users as verifier', 'erf_header_request.approved_hr_by', '=', 'verifier.id')
				->leftJoin('applicant_table', function($join) 
				{
					$join->on('erf_header_request.reference_number', '=', 'applicant_table.erf_number')
					->where('applicant_table.status',$this->jo_done);
				})
				->select(
						'erf_header_request.*',
						'erf_header_request.id as requestid',
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
					'erf_header_documents.*',
				  )
				  ->where('erf_header_documents.header_id', $id)
				  ->get();
			$data['statuses'] = Statuses::select(
					'statuses.*'
				  )
				  ->whereIn('id', [29,30,31,32])
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

		public function getDetailPrintErf($id){
			
			$this->cbLoader();
            if(!CRUDBooster::isRead() && $this->global_privilege==FALSE) {    
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

			$data = array();

			$data['page_title'] = 'ERF Print Preview';

			$data['Header'] = ErfHeaderRequest::
				leftjoin('companies', 'erf_header_request.company', '=', 'companies.id')
				->leftjoin('departments', 'erf_header_request.department', '=', 'departments.id')
				->leftjoin('cms_users as approver', 'erf_header_request.approved_immediate_head_by', '=', 'approver.id')
				->leftjoin('cms_users as verifier', 'erf_header_request.approved_hr_by', '=', 'verifier.id')
				->leftJoin('applicant_table', function($join) 
				{
					$join->on('erf_header_request.reference_number', '=', 'applicant_table.erf_number')
					->where('applicant_table.status',$this->jo_done);
				})
				->select(
						'erf_header_request.*',
						'erf_header_request.id as requestid',
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
					'erf_header_documents.*',
				  )
				  ->where('erf_header_documents.header_id', $id)
				  ->get();
			$data['statuses'] = Statuses::select(
					'statuses.*'
				  )
				  ->whereIn('id', [29,30,31,32])
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
			return $this->view("erf.erf_details_print", $data);
		}

		public function getErfCreateAccount($id){
			
			$this->cbLoader();
            if(!CRUDBooster::isRead() && $this->global_privilege==FALSE) {    
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

			$data = array();

			$data['page_title'] = 'Create an Account';

			$data['Header'] = ErfHeaderRequest::
				leftjoin('companies', 'erf_header_request.company', '=', 'companies.id')
				->leftjoin('departments', 'erf_header_request.department', '=', 'departments.id')
				->leftjoin('cms_users as currentUser', 'erf_header_request.locking_create_account', '=', 'currentUser.id')
				->leftJoin('applicant_table', function($join) 
				{
					$join->on('erf_header_request.reference_number', '=', 'applicant_table.erf_number')
					->where('applicant_table.status',$this->jo_done);
				})
				->select(
						'erf_header_request.*',
						'erf_header_request.id as requestid',
						'departments.department_name as department',
						'currentUser.name as current_user',
						'applicant_table.*'
						)
				->where('erf_header_request.id', $id)->first();
		
			$res_req = explode(",",$data['Header']->required_exams);
			$interact_with = explode(",",$data['Header']->employee_interaction);
			$asset_usage = explode(",",$data['Header']->asset_usage);
			$application = explode(",",$data['Header']->application);
			$required_system = explode(",",$data['Header']->required_system);
			$data['required_exams'] = $res_req;
			$data['interaction'] = $interact_with;
			$data['asset_usage'] = $asset_usage;
			$data['application'] = $application;
			$data['required_system'] = $required_system;
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
			return $this->view("erf.erf-account-creation", $data);
		}

        public function createAccount(Request $request) {	
			$fields = Request::all();
			
			$getErfDetail              = DB::table('erf_header_request')->where('id', $fields['id'])->first();
			$getDepartment             = DB::table('cms_users')->where('id', $getErfDetail->created_by)->first();
			$privilegeId               = DB::table('positions')->where('position_description', $getErfDetail->position)->first();
			$status                    = 'ACTIVE';
			$name                      = $fields['first_name'].' '.$fields['last_name'];
			$first_name                = $fields['first_name'];
			$last_name                 = $fields['last_name'];
			$user_name                 = $fields['last_name'].''.substr($fields['first_name'], 0, 1);
			$email                     = $fields['email'];
			$password                  = Hash::make('qwerty');
			$privilege                 = $privilegeId->privilege_id;
			$department                = $getErfDetail->department;
			$company                   = "DIGITS";
			$location                  = 115;
			$approver                  = $getErfDetail->created_by;
			$contactPerson             = $fields['first_name'].', '.$fields['last_name'];
			$bill_to                   = $fields['last_name'].', '.$fields['first_name'];
			$csutomer_location         = $fields['last_name'].', '.$fields['first_name'].".EEE";
			$position                  = $fields['position'];

			$getLastId = Users::Create(
				[
					'name'                        => $name,
					'first_name'                  => $first_name,
					'last_name'                   => $last_name,
					'user_name'                   => $user_name,
					'email'                       => $email,
					'password'                    => $password,
					'id_cms_privileges'           => $privilege,
					'status'                      => $status,
					'created_by'                  => CRUDBooster::myId(),
					'department_id'               => $department,
					'company_name_id'             => $company,
					'location_id'                 => $location,
					'approver_id'                 => $approver,
					'contact_person'              => $contactPerson,
					'bill_to'                     => $bill_to,
					'customer_location_name'      => $csutomer_location,
					'position_id'                 => $position
				]
				);   
			$userID = $getLastId->id;

			$arf_array = array();
			array_push($arf_array, $getErfDetail->arf_id);
			$arf_string = implode(",",$arf_array);
			$finalArfs = array_map('intval',explode(",",$arf_string));

			for ($i = 0; $i < count($finalArfs); $i++) {
				HeaderRequest::where(['id' => $finalArfs[$i]])
					->update([
							'employee_name' => $userID, 
							'created_by' => $userID
							]);
			}

			erfHeaderRequest::where(['id' => $fields['id']])
					->update([
							'status_id' => $this->onboarding, 
							'locking_create_account' => NULL,
							]);
			$message = ['status'=>'success', 'message' => 'Created Successfully!'];
			echo json_encode($message);
		}

		//Get email for validation
		public function getEmail(Request $request) {
			$data = array();
			$data['status_no'] = 0;
			$data['message']   ='No Item Found!';
			$data['items'] = array();
			$checkEmailDb = DB::table('cms_users')->select(DB::raw("email AS email"))->get()->toArray();
			$checkEmailDbColumn = array_column($checkEmailDb, 'email');
			$data['items'] = $checkEmailDbColumn;
			//dd($checkEmailDbColumn);
			echo json_encode($data);
			exit;  
		}

		public function checkEmail(Request $request){
			$fields = Request::all();
			$email = $fields['email'];
			$countEmail = Users::select(
				'cms_users.*'
			  )
			  ->where('cms_users.email', $email)
			  ->get()->count();
			  if($countEmail == 1) {
				$data = "<span id='notif' class='label label-danger'> Email Not Available</span>";
			  }else if($countEmail == 0){
				$data = "<span id='notif' class='label label-success'> Email Available.</span>";
			  }else{
				$data = "";
			  }
			echo json_encode($data);
		}

		public function getErfSetOnboardingDate($id){
			
			$this->cbLoader();
            if(!CRUDBooster::isRead() && $this->global_privilege==FALSE) {    
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

			$data = array();

			$data['page_title'] = 'Set On Boarding Date';

			$data['Header'] = ErfHeaderRequest::
				leftjoin('companies', 'erf_header_request.company', '=', 'companies.id')
				->leftjoin('departments', 'erf_header_request.department', '=', 'departments.id')
				->leftjoin('cms_users as currentUser', 'erf_header_request.locking_onboarding_date', '=', 'currentUser.id')
				->leftJoin('applicant_table', function($join) 
				{
					$join->on('erf_header_request.reference_number', '=', 'applicant_table.erf_number')
					->where('applicant_table.status',$this->jo_done);
				})
				->select(
						'erf_header_request.*',
						'erf_header_request.id as requestid',
						'departments.department_name as department',
						'currentUser.name as current_user',
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
			return $this->view("erf.erf-set-onboarding-date", $data);
		}

		public function setOnboarding(Request $request) {	
			$fields = Request::all();
			erfHeaderRequest::where(['id' => $fields['id']])
					->update([
							'status_id' => $this->onboarded, 
							'onboarding_date' => $fields['date'],
							'locking_onboarding_date' => NULL,
							]);
			$message = ['status'=>'success', 'message' => 'Set Successfully!'];
			echo json_encode($message);
		}

		public function setUpdateOnboarding(Request $request) {	
			$fields = Request::all();
			erfHeaderRequest::where(['id' => $fields['id']])
					->update([
							'onboarding_date' => $fields['date'],
							]);
			$message = ['status'=>'success', 'message' => 'Update Successfully!'];
			echo json_encode($message);
		}

		public function getDownload($id) {
			$getFile = DB::table('erf_header_documents')->where('id',$id)->first();
			$file= public_path(). "/vendor/crudbooster/erf_folder/".$getFile->file_name;

			$headers = array(
					'Content-Type: application/pdf',
					);

			return Response::download($file, $getFile->file_name, $headers);
		}

		//LOCKING FORM EDIT
		public function lockForm(Request $request) {	
			$fields = Request::all();
			$check = DB::table('erf_header_request')->where('id',$fields['header_request_id'])->whereNull('locking_edit')->count();
			if($check == 1){
				erfHeaderRequest::where(['id' => $fields['header_request_id']])
				->update([
						'locking_edit' => CRUDBooster::myId(),
						]);
			}
		}

		public function lockDeleteForm(Request $request) {	
			$fields = Request::all();
			erfHeaderRequest::where(['id' => $fields['header_request_id']])
					->update([
							'locking_edit' => NULL,
							]);
		}

		public function getLockingFormView($id) {	
			$data = [];
			$data['user'] = DB::table('erf_header_request')->leftjoin('cms_users as currentUser', 'erf_header_request.locking_edit', '=', 'currentUser.id')->select('currentUser.name as current_user')->where('erf_header_request.id',$id)->first();
			return response()->view('errors.form-used-page',$data);
		}

		//LOCKING FORM CREATE ACCOUNT
		public function lockFormCreateAccount(Request $request) {	
			$fields = Request::all();
			$check = DB::table('erf_header_request')->where('id',$fields['header_request_id'])->whereNull('locking_create_account')->count();
			if($check == 1){
				erfHeaderRequest::where(['id' => $fields['header_request_id']])
				->update([
						'locking_create_account' => CRUDBooster::myId(),
						]);
			}
		}

		public function createAccountlockDelete(Request $request) {	
			$fields = Request::all();
			erfHeaderRequest::where(['id' => $fields['header_request_id']])
					->update([
							'locking_create_account' => NULL,
							]);
		}

		public function getLockingErfCreateAcountFormView($id) {	
			$data = [];
			$data['user'] = DB::table('erf_header_request')->leftjoin('cms_users as currentUser', 'erf_header_request.locking_create_account', '=', 'currentUser.id')->select('currentUser.name as current_user')->where('erf_header_request.id',$id)->first();
			return response()->view('errors.form-used-erf-create-account-page',$data);
		}

		//LOCKING ONBOARDING DATE
		public function lockFormOnboardingDate(Request $request) {	
			$fields = Request::all();
			$check = DB::table('erf_header_request')->where('id',$fields['header_request_id'])->whereNull('locking_onboarding_date')->count();
			if($check == 1){
				erfHeaderRequest::where(['id' => $fields['header_request_id']])
				->update([
						'locking_onboarding_date' => CRUDBooster::myId(),
						]);
			}
		}

		public function onboardingDatelockDelete(Request $request) {	
			$fields = Request::all();
			erfHeaderRequest::where(['id' => $fields['header_request_id']])
					->update([
							'locking_onboarding_date' => NULL,
							]);
		}

		public function getLockingErfOnboardingDateFormView($id) {	
			$data = [];
			$data['user'] = DB::table('erf_header_request')->leftjoin('cms_users as currentUser', 'erf_header_request.locking_onboarding_date', '=', 'currentUser.id')->select('currentUser.name as current_user')->where('erf_header_request.id',$id)->first();
			return response()->view('errors.form-used-erf-onboarding-date-page',$data);
		}

		//CLOSE REQUEST
		public function getRequestClose($id){
			
			$this->cbLoader();
            if(!CRUDBooster::isRead() && $this->global_privilege==FALSE) {    
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

			$data = array();

			$data['page_title'] = 'Close Request';

			$data['Header'] = ErfHeaderRequest::
				leftjoin('companies', 'erf_header_request.company', '=', 'companies.id')
				->leftjoin('departments', 'erf_header_request.department', '=', 'departments.id')
				->leftjoin('cms_users as currentUser', 'erf_header_request.locking_close', '=', 'currentUser.id')
				->leftJoin('applicant_table', function($join) 
				{
					$join->on('erf_header_request.reference_number', '=', 'applicant_table.erf_number')
					->where('applicant_table.status',$this->jo_done);
				})
				->select(
						'erf_header_request.*',
						'erf_header_request.id as requestid',
						'departments.department_name as department',
						'currentUser.name as current_user',
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
			return $this->view("erf.erf-close-request", $data);
		}

		//LOCKING CLOSE REQUEST
		public function lockFormCloseRequest(Request $request) {	
			$fields = Request::all();
			$check = DB::table('erf_header_request')->where('id',$fields['header_request_id'])->whereNull('locking_onboarding_date')->count();
			if($check == 1){
				erfHeaderRequest::where(['id' => $fields['header_request_id']])
				->update([
						'locking_close' => CRUDBooster::myId(),
						]);
			}
		}

		public function closeRequestlockDelete(Request $request) {	
			$fields = Request::all();
			erfHeaderRequest::where(['id' => $fields['header_request_id']])
					->update([
							'locking_close' => NULL,
							]);
		}

		public function setRequestClose(Request $request) {	
			$fields = Request::all();
			erfHeaderRequest::where(['id' => $fields['id']])
					->update([
							'status_id'     => $this->closed, 
							'regular_date'  => $fields['date'],
							'locking_close' => NULL,
							]);

			$header = erfHeaderRequest::where('id', $fields['id'])->first();
			//$hr_email = "HR@digits.ph";
			$hr_email = "marvinmosico@digits.ph";

			$infos['subject'] = " Has been close!";
			$infos['reference_number'] = $header->reference_number;
			$infos['data'] = $header;
			$infos['email'] = $hr_email;
			
			Mail::to($hr_email)
			->send(new EmailErfClose($infos));
			
			$message = ['status'=>'success', 'message' => 'Closed Successfully!'];
			echo json_encode($message);
		}

		public function getLockingCloseErfFormView($id) {	
			$data = [];
			$data['user'] = DB::table('erf_header_request')->leftjoin('cms_users as currentUser', 'erf_header_request.locking_close', '=', 'currentUser.id')->select('currentUser.name as current_user')->where('erf_header_request.id',$id)->first();
			return response()->view('errors.form-used-page',$data);
		}
	}