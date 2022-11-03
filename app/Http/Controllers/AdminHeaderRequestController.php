<?php namespace App\Http\Controllers;

	use Session;
	use Request;
	use DB;
	use CRUDBooster;
	use App\HeaderRequest;
	use App\BodyRequest;
	use App\ApprovalMatrix;
	use App\StatusMatrix;
	use App\GeneratedAssetsHistories;
	use App\AssetsInventoryHeader;
	use App\AssetsHeaderImages;
	use App\AssetsInventoryBody;
	//use Illuminate\Http\Request;
	//use Illuminate\Support\Facades\Input;
	use Illuminate\Support\Facades\Log;
	use Illuminate\Support\Facades\Redirect;
	use App\MoveOrder;

	class AdminHeaderRequestController extends \crocodicstudio\crudbooster\controllers\CBController {

        public function __construct() {
			// Register ENUM type
			//$this->request = $request;
			DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping("enum", "string");
		}

		private static $apiContext; 

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
			$this->button_delete = true;
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
			$this->col[] = ["label"=>"Company Name","name"=>"company_name","join"=>"companies,company_name"];
			$this->col[] = ["label"=>"Employee Name","name"=>"employee_name","join"=>"employees,bill_to"];
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
			//$this->form[] = ['label'=>'Reference Number','name'=>'reference_number','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Status Id','name'=>'status_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'status,id'];
			//$this->form[] = ['label'=>'Created By','name'=>'created_by','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Updated By','name'=>'updated_by','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			# END FORM DO NOT REMOVE THIS LINE

			# OLD START FORM
			//$this->form = [];
			//$this->form[] = ["label"=>"Reference Number","name"=>"reference_number","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Status Id","name"=>"status_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"status,id"];
			//$this->form[] = ["label"=>"Created By","name"=>"created_by","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Updated By","name"=>"updated_by","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
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
				$released  = 		DB::table('statuses')->where('id', 12)->value('id');

				$this->addaction[] = ['title'=>'Cancel Request','url'=>CRUDBooster::mainpath('getRequestCancel/[id]'),'icon'=>'fa fa-times', "showIf"=>"[status_id] == $pending"];
			
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

				$this->index_button[] = ["label"=>"IT Asset Request","icon"=>"fa fa-files-o","url"=>CRUDBooster::mainpath('add-requisition'),"color"=>"success"];

				$this->index_button[] = ["label"=>"FA Request","icon"=>"fa fa-files-o","url"=>CRUDBooster::mainpath('add-requisition-fa'),"color"=>"success"];

				//$this->index_button[] = ["label"=>"Return Request","icon"=>"fa fa-files-o","url"=>CRUDBooster::mainpath('add-return'),"color"=>"success"];

				//$this->index_button[] = ["label"=>"Transfer Request","icon"=>"fa fa-files-o","url"=>CRUDBooster::mainpath('add-transfer'),"color"=>"success"];

				//$this->index_button[] = ["label"=>"Disposal Request","icon"=>"fa fa-files-o","url"=>CRUDBooster::mainpath('add-disposal'),"color"=>"success"];
			
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

				$('.fa.fa-times').click(function(){

					var strconfirm = confirm('Are you sure you want to cancel this request?');

					if (strconfirm == true) {

						return true;
						
					}else{
						
						return false;
						window.stop();

					}

				});
				
				
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
			if($button_name == 'void') {

				HeaderRequest::whereIn('id',$id_selected)->update([
					'status_id'=> 8,
					'cancelled_by'=> CRUDBooster::myId(),
					'cancelled_at'=> date('Y-m-d H:i:s')

				]);

			}
	            
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
					  ->where('header_request.status_id', '!=' , $released)
					  ->orderBy('header_request.status_id', 'DESC')
					  ->orderBy('header_request.id', 'DESC');

			}else{

				$user = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();

				$query->where(function($sub_query){

					$user = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();

					$released  = 		DB::table('statuses')->where('id', 12)->value('id');

					$sub_query->where('header_request.created_by', CRUDBooster::myId())
							  ->where('header_request.status_id', '!=' , $released)
							  ->whereNull('header_request.deleted_at'); 
					$sub_query->orwhere('header_request.employee_name', $user->employee_id)
							  ->where('header_request.status_id', '!=' , $released)
							  ->whereNull('header_request.deleted_at');

				});

				$query->orderBy('header_request.status_id', 'asc')->orderBy('header_request.id', 'DESC');

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
			$for_picking =  	DB::table('statuses')->where('id', 15)->value('status_description');
			$for_printing_adf = DB::table('statuses')->where('id', 18)->value('status_description');
			$for_closing = 		DB::table('statuses')->where('id', 19)->value('status_description');
			$for_move_order =  	DB::table('statuses')->where('id', 14)->value('status_description');
			$for_printing =  	DB::table('statuses')->where('id', 17)->value('status_description');

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

			$employee_name 		= $fields['employee_name'];
			$company_name 		= $fields['company_name'];
			$position 			= $fields['position'];
			$department 		= $fields['department'];
			$store_branch 		= $fields['store_branch'];
			$purpose 			= $fields['purpose'];
			$condition 			= $fields['condition'];
			$quantity_total 	= $fields['quantity_total'];
			$cost_total 		= $fields['cost_total'];
			$total 				= $fields['total'];
			$request_type_id 	= $fields['request_type_id'];

			$requestor_comments = $fields['requestor_comments'];

			$application 		= $fields['application'];
			$application_others = $fields['application_others'];
	
			$count_header = 	DB::table('header_request')->count();
			$header_ref   =  	str_pad($count_header + 1, 7, '0', STR_PAD_LEFT);			
			$reference_number	= "ARF-".$header_ref;


			$employees = 	DB::table('employees')->where('bill_to', $employee_name)->first();

			$pending = DB::table('statuses')->where('id', 1)->value('id');

			$approved =  		DB::table('statuses')->where('id', 4)->value('id');


			if(CRUDBooster::myPrivilegeName() == "Employee"){ 

				//$postdata['status_id']		 			= $pending;

				$postdata['status_id']		 			= StatusMatrix::where('current_step', 1)
																		->where('request_type', $request_type_id)
																		->where('id_cms_privileges', CRUDBooster::myPrivilegeId())
																		->value('status_id');
			}else{

				$postdata['status_id']		 			= StatusMatrix::where('current_step', 1)
																		->where('request_type', $request_type_id)
																		->where('id_cms_privileges', CRUDBooster::myPrivilegeId())
																		->value('status_id');

				
			}
				
			$postdata['reference_number']		 	= $reference_number;
			$postdata['employee_name'] 				= $employees->id;
			$postdata['company_name'] 				= $employees->company_name;
			$postdata['position'] 					= $employees->position_id;
			$postdata['department'] 				= $employees->department_id;
			$postdata['store_branch'] 				= $store_branch;
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

			$fields = Request::all();

			$cont = (new static)->apiContext;

			$dataLines = array();

			$arf_header = DB::table('header_request')->where(['created_by' => CRUDBooster::myId()])->orderBy('id','desc')->first();

			/*
			$digits_code 		= $fields['digits_code'];
			$serial_no 			= $fields['serial_no'];
			$remarks 			= $fields['remarks'];
			$quantity 			= $fields['quantity'];
			$unit_cost 			= $fields['unit_cost'];
			$total_unit_cost 	= $fields['total_unit_cost'];
			$item_id 			= $fields['item_id'];
			*/

			$item_description 	= $fields['item_description'];
			$category_id 		= $fields['category_id'];

			$sub_category_id 	= $fields['sub_category_id'];
			
			$app_id_others 		= $fields['app_id_others'];
			
			$quantity 			= $fields['quantity'];
			$image 				= $fields['image'];

			$request_type_id 	= $fields['request_type_id'];
			
			$app_count = 2;


			for($x=0; $x < count((array)$item_description); $x++) {

				$apps_array = array();

				$app_no = 'app_id'.$app_count;

				$app_id 			= $fields[$app_no];

				for($xxx=0; $xxx < count((array)$app_id); $xxx++) {
					array_push($apps_array,$app_id[$xxx]); 
				}
	
				

				$app_count++;

				if (!empty($image[$x])) {

					$extension1 =  $app_count.time() . '.' .$image[$x]->getClientOriginalExtension();
					$filename = $extension1;
					$image[$x]->move('vendor/crudbooster/',$filename);

				}

				if(CRUDBooster::myPrivilegeName() == "HR"){ 

					if($category_id[$x] == "IT ASSETS"){

						HeaderRequest::where('id', $arf_header->id)->update([
							'to_reco'=> 1
						]);
						
					}
					
				}


				$dataLines[$x]['header_request_id'] = $arf_header->id;
				$dataLines[$x]['item_description'] 	= $item_description[$x];
				$dataLines[$x]['category_id'] 		= $category_id[$x];
				$dataLines[$x]['sub_category_id'] 	= $sub_category_id[$x];
				$dataLines[$x]['app_id'] 			= implode(", ",$apps_array);
				$dataLines[$x]['app_id_others'] 	= $app_id_others[$x];
				$dataLines[$x]['quantity'] 			= $quantity[$x];

				if($request_type_id == 5){

					$dataLines[$x]['to_reco'] = 0;
					
				}else{

					if (str_contains($item_description[$x], 'LAPTOP')) {
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
				/*
				$dataLines[$x]['digits_code'] 		= $digits_code[$x];
				$dataLines[$x]['serial_no'] 		= $serial_no[$x];
				$dataLines[$x]['remarks'] 			= $remarks[$x];
				$dataLines[$x]['quantity'] 			= $quantity[$x];
				$dataLines[$x]['unit_cost'] 		= $unit_cost[$x];
				$dataLines[$x]['total_unit_cost'] 	= $total_unit_cost[$x];
				$dataLines[$x]['item_id'] 			= $item_id[$x];
				*/
				$dataLines[$x]['created_at'] 		= date('Y-m-d H:i:s');



				unset($apps_array);
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

			$data['employees'] = DB::table('employees')->where('status_id', 1)->orderby('bill_to', 'asc')->get();

			$data['departments'] = DB::table('departments')->where('status', 'ACTIVE')->get();

			$data['stores'] = DB::table('stores')->where('status', 'ACTIVE')->get();

			$data['departments'] = DB::table('departments')->where('status', 'ACTIVE')->get();

			$data['user'] = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();


			$data['employeeinfos'] = DB::table('employees')
										 ->leftjoin('positions', 'employees.position_id', '=', 'positions.id')
										 ->leftjoin('departments', 'employees.department_id', '=', 'departments.id')
										 ->leftjoin('companies', 'employees.company_name', '=', 'companies.id')
										 ->select( 'employees.*', 'positions.position_description as position_description', 'departments.department_name as department_name', 'companies.company_name as company_name')
										 ->where('employees.id', $data['user']->employee_id)->first();
			
			$data['categories'] = DB::table('category')->where('category_status', 'ACTIVE')->where('id', 5)->orderby('category_description', 'asc')->get();

			$data['sub_categories'] = DB::table('class')->where('class_status', 'ACTIVE')->where('category_id', 5)->orderby('class_description', 'asc')->get();

			$data['applications'] = DB::table('applications')->where('status', 'ACTIVE')->orderby('app_name', 'asc')->get();
			
			$data['companies'] = DB::table('companies')->where('status', 'ACTIVE')->get();
			
			if(CRUDBooster::myPrivilegeName() == "Employee"){ 

				$data['purposes'] = DB::table('request_type')->where('status', 'ACTIVE')->where('privilege', 'Employee')->get();

				return $this->view("assets.add-requisition", $data);

			}else if(CRUDBooster::myPrivilegeName() == "Store Ops"){ 

				$data['purposes'] = DB::table('request_type')->where('status', 'ACTIVE')->where('privilege', 'Employee')->get();



				$data['stores'] = DB::table('stores')->where('id', $data['user']->store_id)->first();

				return $this->view("assets.add-store-requisition", $data);

			}else{

				$data['purposes'] = DB::table('request_type')->where('status', 'ACTIVE')->where('privilege', 'HR')->get();

				return $this->view("assets.add-hr-requisition", $data);

			}
				

			

		}

		public function getAddRequisitionFA() {

			if(!CRUDBooster::isCreate() && $this->global_privilege == false) {
				CRUDBooster::redirect(CRUDBooster::adminPath(), trans('crudbooster.denied_access'));
			}

			$this->cbLoader();

			$data['page_title'] = 'Create New FA Request';

			

			$data['conditions'] = DB::table('condition_type')->where('status', 'ACTIVE')->get();

			$data['employees'] = DB::table('employees')->where('status_id', 1)->orderby('bill_to', 'asc')->get();

			$data['departments'] = DB::table('departments')->where('status', 'ACTIVE')->get();

			$data['stores'] = DB::table('stores')->where('status', 'ACTIVE')->get();

			$data['departments'] = DB::table('departments')->where('status', 'ACTIVE')->get();

			$data['user'] = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();


			$data['employeeinfos'] = DB::table('employees')
										 ->leftjoin('positions', 'employees.position_id', '=', 'positions.id')
										 ->leftjoin('departments', 'employees.department_id', '=', 'departments.id')
										 ->leftjoin('companies', 'employees.company_name', '=', 'companies.id')
										 ->select( 'employees.*', 'positions.position_description as position_description', 'departments.department_name as department_name', 'companies.company_name as company_name')
										 ->where('employees.id', $data['user']->employee_id)->first();
			
			$data['categories'] = DB::table('category')->where('id', 1)->where('category_status', 'ACTIVE')
													   ->orwhere('id', 6)->where('category_status', 'ACTIVE')
													   ->orderby('category_description', 'asc')
													   ->get();

			$data['applications'] = DB::table('applications')->where('status', 'ACTIVE')->orderby('app_name', 'asc')->get();
			
			$data['companies'] = DB::table('companies')->where('status', 'ACTIVE')->get();
			
			if(CRUDBooster::myPrivilegeName() == "Employee"){ 

				$data['purposes'] = DB::table('request_type')->where('status', 'ACTIVE')->where('privilege', 'Employee')->get();

				return $this->view("assets.add-requisition-fa", $data);

			}else if(CRUDBooster::myPrivilegeName() == "Store Ops"){ 

				$data['purposes'] = DB::table('request_type')->where('status', 'ACTIVE')->where('privilege', 'Employee')->get();



				$data['stores'] = DB::table('stores')->where('id', $data['user']->store_id)->first();

				return $this->view("assets.add-store-requisition-fa", $data);

			}else{

				$data['purposes'] = DB::table('request_type')->where('status', 'ACTIVE')->where('privilege', 'HR')->get();

				return $this->view("assets.add-hr-requisition", $data);

			}
				

			
		}

		public function getDetail($id){
			

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
				->leftjoin('stores', 'header_request.store_branch', '=', 'stores.id')
				->leftjoin('cms_users as requested', 'header_request.created_by','=', 'requested.id')
				->leftjoin('cms_users as approved', 'header_request.approved_by','=', 'approved.id')
				->leftjoin('cms_users as recommended', 'header_request.recommended_by','=', 'recommended.id')
				->leftjoin('cms_users as processed', 'header_request.purchased2_by','=', 'processed.id')
				->leftjoin('cms_users as picked', 'header_request.picked_by','=', 'picked.id')
				->leftjoin('cms_users as received', 'header_request.received_by','=', 'received.id')
				->leftjoin('cms_users as closed', 'header_request.closed_by','=', 'closed.id')
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
						'stores.bea_mo_store_name as store_branch',
						'approved.name as approvedby',
						'recommended.name as recommendedby',
						'picked.name as pickedby',
						'received.name as receivedby',
						'processed.name as processedby',
						'closed.name as closedby',
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

			$data['BodyReco'] = DB::table('recommendation_request')
				->select(
				  'recommendation_request.*'
				)
				->where('recommendation_request.header_request_id', $id)
				->get();				

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

			//$search_item =  DB::table('digits_code')>where('digits_code','LIKE','%'.$request->search.'%')->first();

			$items = DB::table('assets')
			    ->where('assets.digits_code','LIKE','%'.$search.'%')->where('assets.category_id','=',1)
				->orwhere('assets.digits_code','LIKE','%'.$search.'%')->where('assets.category_id','=',5)
				->orWhere('assets.item_description','LIKE','%'.$search.'%')->where('assets.category_id','=',1)
				->orWhere('assets.item_description','LIKE','%'.$search.'%')->where('assets.category_id','=',5)
				//->where('assets.digits_code','LIKE','%'.$request->search.'%')
				//->orWhere('assets.item_description','LIKE','%'.$request->search.'%')
			
				->join('category', 'assets.category_id','=', 'category.id')
				//->join('digits_imfs', 'assets.digits_code','=', 'digits_imfs.id')
				->select(	'assets.*',
				            'category.id as cat_id',
							'assets.id as assetID',
							//'digits_imfs.digits_code as dcode',
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

					$return_data[$i]['id'] = 				$value->assetID;
					$return_data[$i]['cat_id'] = 				$value->cat_id;
					$return_data[$i]['asset_code'] = 		$value->asset_code;
					$return_data[$i]['digits_code'] = 		$value->digits_code;
					$return_data[$i]['asset_tag'] = 		$value->asset_tag;
					$return_data[$i]['serial_no'] = 		$value->serial_no;
					$return_data[$i]['item_description'] = 	$value->item_description;
					$return_data[$i]['category_description'] = 		$value->category_description;
					$return_data[$i]['item_cost'] = 				$value->item_cost;
					$return_data[$i]['item_type'] = 				$value->item_type;
					$return_data[$i]['image'] = 				$value->image;
					$return_data[$i]['quantity'] = 				$value->quantity;
					$return_data[$i]['total_quantity'] = 				$value->total_quantity;

					$i++;

				}
				$data['items'] = $return_data;
			}


			echo json_encode($data);
			exit;  
		}


		public function Employees(Request $request)
		{
			$employees = 	DB::table('employees')
							->leftjoin('positions', 'employees.position_id', '=', 'positions.id')
							->leftjoin('departments', 'employees.department_id', '=', 'departments.id')
							->leftjoin('companies', 'employees.company_name', '=', 'companies.id')
							->select( 'employees.*', 'positions.position_description as position_description', 'departments.department_name as department_name', 'companies.company_name as company_name')
							->where('status_id', 1)->where('bill_to', $request->employee_name)->get();
	
			return($employees);
		}


		public function Companies(Request $request)
		{

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

		public function SubCategories($id)
		{

			$categories = DB::table('category')->where('category_description', $id)->first();

			$subcategories = DB::table('class')
							->select( 'class.*' )
							->where('category_id', $categories->id)
							->where('class_status', "ACTIVE")
							->orderby('class_description', 'ASC')->get();
	
			return($subcategories);
		}

		public function RemoveItem(Request $request)
		{

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


			if($quantity_total == 0){
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


	}