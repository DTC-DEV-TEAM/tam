<?php namespace App\Http\Controllers;

	use Session;
	use Request;
	use DB;
	use CRUDBooster;
	use App\StatusMatrix;
	use App\Users;
	use App\Models\ItemHeaderSourcing;
	use App\Models\ItemBodySourcing;
	use App\Models\ItemSourcingComments;
	use App\Models\ItemSourcingOptions;
	use App\Models\ItemSourcingEditVersions;
	use App\Models\ItemSourcingHeaderFile;
	use App\HeaderRequest;
	use App\BodyRequest;
	use App\Mail\Email;
	use Mail;
	use Illuminate\Support\Facades\Response;

	class AdminItemSourcingHeaderController extends \crocodicstudio\crudbooster\controllers\CBController {
		private $forApproval;
		private $cancelled;
		private $closed;
		private $forDiscussion;
		private $forSourcing;
		private $forStreamlining;
		private $forItemCreation;
		private $forArfCreation;
		private $rejected;

		public function __construct() {
			DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping("enum", "string");
			$this->forApproval      =  1;    
			$this->cancelled        =  8;
			$this->closed           =  13;      
			$this->forDiscussion    =  37;  
			$this->forSourcing      =  38;
			$this->forStreamlining  =  39;   
			$this->forItemCreation  =  40;        
			$this->forArfCreation   =  41;
			$this->rejected         =  5;
		
		}
	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "employee_name";
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
			$this->table = "item_sourcing_header";
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
				$rejected  = 		DB::table('statuses')->where('id', 5)->value('id');
				$this->addaction[] = ['title'=>'Cancel Request','url'=>CRUDBooster::mainpath('getRequestCancelNis/[id]'),'icon'=>'fa fa-times', "showIf"=>"[status_id] == $this->forApproval"];
				$this->addaction[] = ['title'=>'Detail','url'=>CRUDBooster::mainpath('getDetailReject/[id]'),'icon'=>'fa fa-eye', "showIf"=>"[status_id] == $rejected"];
				$this->addaction[] = ['title'=>'Detail','url'=>CRUDBooster::mainpath('getDetail/[id]'),'icon'=>'fa fa-eye', "showIf"=>"[status_id] != $this->rejected"];

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
				$this->index_button[] = ["label"=>"IT Assets","icon"=>"fa fa-plus-circle","url"=>CRUDBooster::mainpath('add-item-sourcing-it'),"color"=>"success"];
				$this->index_button[] = ["label"=>"Fixed Assets","icon"=>"fa fa-plus-circle","url"=>CRUDBooster::mainpath('add-item-sourcing-fa'),"color"=>"success"];
				$this->index_button[] = ["label"=>"Marketing","icon"=>"fa fa-plus-circle","url"=>CRUDBooster::mainpath('add-item-sourcing-mkt'),"color"=>"success"];
				$this->index_button[] = ["label"=>"Supplies","icon"=>"fa fa-plus-circle","url"=>CRUDBooster::mainpath('add-item-sourcing-supplies'),"color"=>"success"];
				$this->index_button[] = ["label"=>"Services","icon"=>"fa fa-plus-circle","url"=>CRUDBooster::mainpath('add-item-sourcing-services'),"color"=>"success"];
				$this->index_button[] = ["label"=>"Subscription","icon"=>"fa fa-plus-circle","url"=>CRUDBooster::mainpath('add-item-sourcing-subscription'),"color"=>"success"];

			
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
				$(document).ready(function() {
					$('#supplies').prop('title', 'Bag');
					$('#fixed-assets').prop('title', 'Cellphone');
					$('#it-assets').prop('title', 'Laptop');
					$('#marketing').prop('title', 'Card');

					$('#it-assets').attr(
							{
								\"data-toggle\":\"tooltip\", 
								\"data-placement\":\"bottom\", 
							},
				        );
					$('#fixed-assets').attr(
							{
								\"data-toggle\":\"tooltip\", 
								\"data-placement\":\"bottom\", 
							},
				        );
					$('#marketing').attr(
							{
								\"data-toggle\":\"tooltip\", 
								\"data-placement\":\"bottom\", 
							},
				        );
					$('#supplies').attr(
							{
								\"data-toggle\":\"tooltip\", 
								\"data-placement\":\"bottom\", 
							},
				        );
				    $('.btn-detail').attr(
							{
								\"data-toggle\":\"tooltip\", 
								\"data-placement\":\"bottom\", 
							},
				        );
					$('a[title=\"Cancel Request\"]').attr(
							{
								\"data-toggle\":\"tooltip\", 
								\"data-placement\":\"bottom\", 
							},
				        );
					$('a[title=\"Detail\"]').prop('class', 'btn btn-xs btn-primary btn-detail');
					$('.fa.fa-times').click(function(){
					var strconfirm = confirm('Are you sure you want to cancel this request?');
					if (strconfirm == true) {
						return true;
					}else{
						return false;
						window.stop();
					}

		    	});	
			});";


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
			$this->load_js[] = asset("js/item_source/chat.js");
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
			$this->load_css[] = asset("css/chatbox.css");
			$this->load_css[] = asset("css/spinner.css");
	        
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
				$query->whereNull('item_sourcing_header.deleted_at')
					  ->orderBy('item_sourcing_header.status_id', 'ASC')
					  ->orderBy('item_sourcing_header.id', 'DESC');

			}else if(CRUDBooster::myPrivilegeId() == 8){
				$res = $query->select('item_sourcing_header.*')->get();
				$user = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
    
				$query->where(function($sub_query){
					$sub_query->where('item_sourcing_header.created_by', CRUDBooster::myId())
							->whereNull('item_sourcing_header.deleted_at')
							->orderBy('item_sourcing_header.reference_number', 'ASC')
							->orderBy('item_sourcing_header.id', 'DESC');
							
				});
				$query->orderBy('item_sourcing_header.status_id', 'ASC')->orderBy('item_sourcing_header.id', 'DESC');
					
			}else{
				$res = $query->select('item_sourcing_header.*')->get();
				$user = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
    
				$query->where(function($sub_query){
					$sub_query->where('item_sourcing_header.created_by','LIKE','%'.CRUDBooster::myId().'%')
							->whereNull('item_sourcing_header.deleted_at')
							->orderBy('item_sourcing_header.reference_number', 'ASC')
							->orderBy('item_sourcing_header.id', 'DESC');
							
				});
				$query->orderBy('item_sourcing_header.status_id', 'ASC')->orderBy('item_sourcing_header.id', 'DESC');
					
				//$query->orderByRaw('FIELD( item_sourcing_header.status_id, "For Approval")');
			}
	            
	    }

	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate row of index table html 
	    | ---------------------------------------------------------------------- 
	    |
	    */    
	    public function hook_row_index($column_index,&$column_value) {	        
			$forApproval        = DB::table('statuses')->where('id', $this->forApproval)->value('status_description');     
			$cancelled          = DB::table('statuses')->where('id', $this->cancelled)->value('status_description');   
			$closed             = DB::table('statuses')->where('id', $this->closed)->value('status_description');  
			$forDiscussion      = DB::table('statuses')->where('id', $this->forDiscussion)->value('status_description');  
			$forSourcing        = DB::table('statuses')->where('id', $this->forSourcing)->value('status_description');  
			$forStreamlining    = DB::table('statuses')->where('id', $this->forStreamlining)->value('status_description');
			$forItemCreation    = DB::table('statuses')->where('id', $this->forItemCreation)->value('status_description');
			$forArfCreation     = DB::table('statuses')->where('id', $this->forArfCreation)->value('status_description');
			$rejected           = DB::table('statuses')->where('id', $this->rejected)->value('status_description');	
			
			if($column_index == 1){
				if($column_value == $forApproval){
					$column_value = '<span class="label label-warning">'.$forApproval.'</span>';
				}else if($column_value == $forDiscussion){
					$column_value = '<span class="label label-info">'.$forDiscussion.'</span>';
				}else if($column_value == $closed){
					$column_value = '<span class="label label-success">'.$closed.'</span>';
				}else if($column_value == $cancelled){
					$column_value = '<span class="label label-danger">'.$cancelled.'</span>';
				}else if($column_value == $rejected){
					$column_value = '<span class="label label-danger">'.$rejected.'</span>';
				}else if($column_value == $forStreamlining){
					$column_value = '<span class="label label-info">'.$forStreamlining.'</span>';
				}else if($column_value == $forSourcing){
					$column_value = '<span class="label label-info">'.$forSourcing.'</span>';
				}else if($column_value == $forItemCreation){
					$column_value = '<span class="label label-info">'.$forItemCreation.'</span>';
				}else if($column_value == $forArfCreation){
					$column_value = '<span class="label label-info">'.$forArfCreation.'</span>';
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
	        $fields = Request::all();
    
			$dataLines = array();

			$employee_name 		     = $fields['employee_name'];
			$company_name 		     = $fields['company_name'];
			$position 			     = $fields['position'];
			$date_needed             = $fields['date_needed'];
			$department 		     = $fields['department'];
			$store_branch 		     = $fields['store_branch'];
			$store_branch_id         = $fields['store_branch_id'];

			$sampling                = $fields['sampling'];
			$mark_up                 = $fields['mark_up'];
			$dismantling             = $fields['dismantling'];
			$artworklink             = $fields['artworklink'];
	
			$quantity_total 	     = $fields['quantity_total'];
			$cost_total 		     = $fields['cost_total'];
			$total 				     = $fields['total'];
			$request_type_id 	     = $fields['request_type_id'];
			$requestor_comments      = $fields['requestor_comments'];
			$suggested_supplier      = $fields['suggested_supplier'];

			$count_header            = DB::table('item_sourcing_header')->count();
			$header_ref              = str_pad($count_header + 1, 7, '0', STR_PAD_LEFT);			
			$reference_number	     = "NIS-".$header_ref;
			$employees               = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
			$approver                = DB::table('cms_users')->where('id', $employees->approver_id)->first();
			$departmentList          = array_map('intval',explode(",",$employees->department_id));
			$departmentsUsers        = DB::table('cms_users')->whereIn('department_id',$departmentList)->where('id_cms_privileges','!=',1)->where('id','!=',CRUDBooster::myId())->get();
			$eachDepartmentsIds      = [];
			foreach($departmentsUsers as $value){
				array_push($eachDepartmentsIds, CRUDBooster::myId());
				array_push($eachDepartmentsIds, $value->id);
				array_push($eachDepartmentsIds, $approver->id);
			}
			
			$saveDepartment = implode(",",array_unique($eachDepartmentsIds));
	 
			$pending                 = DB::table('statuses')->where('id', 1)->value('id');
			$approved                = DB::table('statuses')->where('id', 4)->value('id');

			if(in_array(CRUDBooster::myPrivilegeId(), [11,12,14,15])){ 
				$postdata['status_id']		 			= 37;
			}else{
				$postdata['status_id']		 			= 1;
	
			}
				
			$postdata['reference_number']		 	= $reference_number;
			$postdata['employee_name'] 				= $employees->id;
			$postdata['company_name'] 				= $employees->company_name_id;
			$postdata['position'] 					= $employees->position_id;
			$postdata['date_needed'] 				= $date_needed;
			$postdata['department'] 				= $employees->department_id;
			if(CRUDBooster::myPrivilegeId() == 8){
				$postdata['store_branch'] 			= $employees->location_id;
			}else{
				$postdata['store_branch'] 			= NULL;
			}
			
			$postdata['quantity_total'] 			= $quantity_total;
			$postdata['cost_total'] 				= $cost_total;
			$postdata['total'] 						= $total;
			
			if($request_type_id == 6){
				if(CRUDBooster::myPrivilegeId() == 8){
					$postdata['created_by'] 		= CRUDBooster::myId();
				}else{
					$postdata['created_by']  		= $saveDepartment;
				}
			}else{
				$postdata['created_by'] 		    = CRUDBooster::myId();
			}

			$postdata['created_at'] 		    = date('Y-m-d H:i:s');
			$postdata['request_type_id']		 	= $request_type_id;
			$postdata['sampling']		 	        = $sampling;
			$postdata['mark_up']		 	        = $mark_up;
			$postdata['dismantling']		 	    = $dismantling;
			$postdata['artworklink']		 	    = $artworklink;
	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command after add public static function called 
	    | ---------------------------------------------------------------------- 
	    | @id = last insert id
	    | 
	    */
	    public function hook_after_add($id) {        
	        $fields = Request::all();
			$dataLines = array();
			$nis_header = DB::table('item_sourcing_header')->where(['created_by' => CRUDBooster::myId()])->orderBy('id','desc')->first();

			$item_description 	     = $fields['item_description'];
			$category_id 		     = $fields['category_id'];
			$sub_category_id 	     = $fields['sub_category_id'];
			$class_id 			     = $fields['class_id'];
			$sub_class_id 	         = $fields['sub_class_id'];
			$brand 			         = $fields['brand'];
			$model 		     	     = $fields['model'];
			$size 			         = $fields['size'];
			$actual_color            = $fields['actual_color'];
			$material                = $fields['material'];
			$thickness               = $fields['thickness'];
			$lamination              = $fields['lamination'];
			$add_ons                 = $fields['add_ons'];
			$installation            = $fields['installation'];
			$dismantling             = $fields['dismantling_body'];
			$quantity 			     = $fields['quantity'];
			$budget 	             = $fields['budget'];
			$requestor_comments      = $fields['requestor_comments'];
			$request_type_id         = $fields['request_type_id'];

			//upload header file
			$upload_file             = $fields['upload_file'];
			$images = [];
			if (!empty($upload_file)) {
				$counter = 0;
				foreach($upload_file as $file){
					$counter++;
					$name = time().rand(1,50).'-'.$nis_header->id . '.' . $file->getClientOriginalExtension();
					$filename = $name;
					$file->move('vendor/crudbooster/item_source_header_file',$filename);
					$images[]= $filename;

					$header_images                          = new ItemSourcingHeaderFile;
					$header_images->header_id 		        = $nis_header->id;
					$header_images->file_name 		        = $filename;
					$header_images->ext 		            = $file->getClientOriginalExtension();
					$header_images->created_by 		        = CRUDBooster::myId();
					$header_images->save();
				}
			}
		
			$dataLines = [];
			$insertDataLines = [];
			if(in_array($request_type_id, [6])){
				foreach($item_description as $key => $val) {
					$dataLines['header_request_id'] = $nis_header->id;
					$dataLines['item_description'] 	= $val;
					$dataLines['brand'] 	        = $brand[$key];
					$dataLines['model'] 	        = $model[$key];
					$dataLines['size'] 	            = $size[$key];
					$dataLines['actual_color'] 	    = $actual_color[$key];
					$dataLines['material'] 	        = $material[$key];
					$dataLines['thickness'] 	    = $thickness[$key];
					$dataLines['lamination'] 	    = $lamination[$key];
					$dataLines['add_ons'] 	        = $add_ons[$key];
					$dataLines['installation'] 	    = $installation[$key];
					$dataLines['dismantling'] 	    = $dismantling[$key];
					$dataLines['quantity'] 			= intval(str_replace(',', '', $quantity[$key]));
					$dataLines['created_at'] 		= date('Y-m-d H:i:s');
					$insertDataLines[] = $dataLines;
				}
				ItemBodySourcing::insert($insertDataLines);
			}else{
				ItemBodySourcing::Create([
					'header_request_id' => $nis_header->id,
					'item_description' 	=> $item_description,
					'category_id' 		=> $category_id,
					'sub_category_id' 	=> $sub_category_id,
					'class_id' 	        => $class_id,
					'sub_class_id' 	    => $sub_class_id,
					'sub_category_id' 	=> $sub_category_id,
					'brand' 	        => $brand,
					'model' 	        => $model,
					'size' 	            => $size,
					'actual_color' 	    => $actual_color,
					'material' 	        => $material,
					'thickness' 	    => $thickness,
					'lamination' 	    => $lamination,
					'add_ons' 	        => $add_ons,
					'installation' 	    => $installation,
					'dismantling' 	    => $dismantling,
					'quantity' 			=> $quantity,
					'budget' 		    => $budget,
					'created_at' 		=> date('Y-m-d H:i:s'),
				]);
			}
	
			if($requestor_comments){
				ItemSourcingComments::Create(
				    [
					'item_header_id' => $nis_header->id,
					'user_id'        => CRUDBooster::myId(),
					'comments'       => $requestor_comments,
					'created_at' 	 => date('Y-m-d H:i:s'),
				    ]
				);   
			}

			// DB::beginTransaction();
			// try {
				
			// 	DB::commit();
			// 	//CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.alert_pullout_data_success",['mps_reference'=>$pullout_header->reference]), 'success');
			// } catch (\Exception $e) {
			// 	DB::rollback();
			// 	CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.alert_database_error",['database_error'=>$e]), 'danger');
			// }
			
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

		//IT
		public function getAddItemSourcingIt() {
			if(!CRUDBooster::isCreate() && $this->global_privilege == false) {
				CRUDBooster::redirect(CRUDBooster::adminPath(), trans('crudbooster.denied_access'));
			}
			$this->cbLoader();
			$data['page_title'] = 'Create IT Item Sourcing';
			$data['conditions'] = DB::table('condition_type')->where('status', 'ACTIVE')->get();
			$data['departments'] = DB::table('departments')->where('status', 'ACTIVE')->get();
			$data['stores'] = DB::table('stores')->where('status', 'ACTIVE')->get();
			$data['departments'] = DB::table('departments')->where('status', 'ACTIVE')->get();
			$data['user'] = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();

			$data['employeeinfos'] = Users::user($data['user']->id);

			$data['categories'] = DB::table('category')->where('id',6)->where('category_status', 'ACTIVE')->orderby('category_description', 'asc')->get();
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
				return $this->view("item-sourcing.add-item-sourcing-it", $data);

			}
				
		}

		//IT
		public function getAddItemSourcingFa() {
			if(!CRUDBooster::isCreate() && $this->global_privilege == false) {
				CRUDBooster::redirect(CRUDBooster::adminPath(), trans('crudbooster.denied_access'));
			}
			$this->cbLoader();
			$data['page_title'] = 'Create Fixed Assets Item Sourcing';
			$data['conditions'] = DB::table('condition_type')->where('status', 'ACTIVE')->get();
			$data['departments'] = DB::table('departments')->where('status', 'ACTIVE')->get();
			$data['stores'] = DB::table('stores')->where('status', 'ACTIVE')->get();
			$data['departments'] = DB::table('departments')->where('status', 'ACTIVE')->get();
			$data['user'] = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();

			$data['employeeinfos'] = Users::user($data['user']->id);

			$data['categories'] = DB::table('category')->whereIn('id',[1,4,7,8])->where('category_status', 'ACTIVE')->orderby('category_description', 'asc')->get();
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
				return $this->view("item-sourcing.add-item-sourcing-fa", $data);

			}
				
		}

		//Marketing
		public function getAddItemSourcingMkt() {
			if(!CRUDBooster::isCreate() && $this->global_privilege == false) {
				CRUDBooster::redirect(CRUDBooster::adminPath(), trans('crudbooster.denied_access'));
			}
			$this->cbLoader();
			$data['page_title'] = 'Create Marketing Item Sourcing';
			$data['conditions'] = DB::table('condition_type')->where('status', 'ACTIVE')->get();
			$data['departments'] = DB::table('departments')->where('status', 'ACTIVE')->get();
			$data['stores'] = DB::table('stores')->where('status', 'ACTIVE')->get();
			$data['departments'] = DB::table('departments')->where('status', 'ACTIVE')->get();
			$data['user'] = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();

			$data['employeeinfos'] = Users::user($data['user']->id);

			$data['categories'] = DB::table('category')->where('id',3)->where('category_status', 'ACTIVE')->orderby('category_description', 'asc')->get();
			$data['budget_range'] = DB::table('sub_masterfile_budget_range')->where('status', 'ACTIVE')->get();
			$data['yesno'] = DB::table('sub_masterfile_yes_no')->get();
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
				return $this->view("item-sourcing.add-item-sourcing-mkt", $data);

			}
				
		}
        
		//Supplies
		public function getAddItemSourcingSupplies() {
			if(!CRUDBooster::isCreate() && $this->global_privilege == false) {
				CRUDBooster::redirect(CRUDBooster::adminPath(), trans('crudbooster.denied_access'));
			}
			$this->cbLoader();
			$data['page_title'] = 'Create Supplies Item Sourcing';
			$data['conditions'] = DB::table('condition_type')->where('status', 'ACTIVE')->get();
			$data['departments'] = DB::table('departments')->where('status', 'ACTIVE')->get();
			$data['stores'] = DB::table('stores')->where('status', 'ACTIVE')->get();
			$data['departments'] = DB::table('departments')->where('status', 'ACTIVE')->get();
			$data['user'] = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();

			$data['employeeinfos'] = Users::user($data['user']->id);

			$data['categories'] = DB::table('category')->whereIn('id',[2,9])->where('category_status', 'ACTIVE')->orderby('category_description', 'asc')->get();
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
				return $this->view("item-sourcing.add-item-sourcing-supplies", $data);
			}
				
		}

		//Serveices
		public function getAddItemSourcingServices() {
			if(!CRUDBooster::isCreate() && $this->global_privilege == false) {
				CRUDBooster::redirect(CRUDBooster::adminPath(), trans('crudbooster.denied_access'));
			}
			$this->cbLoader();
			$data['page_title'] = 'Create Services Item Sourcing';
			$data['conditions'] = DB::table('condition_type')->where('status', 'ACTIVE')->get();
			$data['departments'] = DB::table('departments')->where('status', 'ACTIVE')->get();
			$data['stores'] = DB::table('stores')->where('status', 'ACTIVE')->get();
			$data['departments'] = DB::table('departments')->where('status', 'ACTIVE')->get();
			$data['user'] = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();

			$data['employeeinfos'] = Users::user($data['user']->id);

			//$data['categories'] = DB::table('new_category')->whereIn('id',[2,4])->where('category_status', 'ACTIVE')->orderby('category_description', 'asc')->get();
			$data['budget_range'] = DB::table('sub_masterfile_budget_range')->where('status', 'ACTIVE')->get();
			//$privilegesMatrix = DB::table('cms_privileges')->where('id', '!=', 8)->get();
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
				return $this->view("item-sourcing.add-item-sourcing-services", $data);

			}
				
		}

		//Subscription
		public function getAddItemSourcingSubscription() {
			if(!CRUDBooster::isCreate() && $this->global_privilege == false) {
				CRUDBooster::redirect(CRUDBooster::adminPath(), trans('crudbooster.denied_access'));
			}
			$this->cbLoader();
			$data['page_title'] = 'Create Subscription Item Sourcing';
			$data['conditions'] = DB::table('condition_type')->where('status', 'ACTIVE')->get();
			$data['departments'] = DB::table('departments')->where('status', 'ACTIVE')->get();
			$data['stores'] = DB::table('stores')->where('status', 'ACTIVE')->get();
			$data['departments'] = DB::table('departments')->where('status', 'ACTIVE')->get();
			$data['user'] = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();

			$data['employeeinfos'] = Users::user($data['user']->id);

			//$data['categories'] = DB::table('new_category')->whereIn('id',[2,4])->where('category_status', 'ACTIVE')->orderby('category_description', 'asc')->get();
			$data['budget_range'] = DB::table('sub_masterfile_budget_range')->where('status', 'ACTIVE')->get();
			//$privilegesMatrix = DB::table('cms_privileges')->where('id', '!=', 8)->get();
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
				return $this->view("item-sourcing.add-item-sourcing-subscription", $data);

			}
				
		}

		public function getDetail($id){
			$this->cbLoader();
            if(!CRUDBooster::isRead() && $this->global_privilege==FALSE) {    
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

			$data = array();
			$data['page_title']   = 'Item Sourcing Detail';

			$data['Header']       = ItemHeaderSourcing::header($id);
			$data['Body']         = ItemBodySourcing::body($id);
			$data['comments']     = ItemSourcingComments::comments($id);
		    $data['item_options'] = ItemSourcingOptions::options($id);
			$data['versions']     = DB::table('item_sourcing_edit_versions')->where('header_id', $id)->latest('created_at')->first();
			$data['allOptions']   = DB::table('item_sourcing_options')->where('item_sourcing_options.header_id', $id)->count();
			
			$data['header_files'] = ItemSourcingHeaderFile::select('item_sourcing_header_file.*')->where('item_sourcing_header_file.header_id', $id)->get();
			$data['yesno']        = DB::table('sub_masterfile_yes_no')->get();

			return $this->view("item-sourcing.item-sourcing-detail", $data);
		}
		public function getDetailReject($id){
			$this->cbLoader();
            if(!CRUDBooster::isRead() && $this->global_privilege==FALSE) {    
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

			$data = array();
			$data['page_title']   = 'Item Sourcing Detail';

			$data['Header']       = ItemHeaderSourcing::header($id);
			$data['Body']         = ItemBodySourcing::body($id);
			$data['comments']     = ItemSourcingComments::comments($id);
		    $data['item_options'] = ItemSourcingOptions::options($id);
			$data['versions']     = DB::table('item_sourcing_edit_versions')->where('header_id', $id)->latest('created_at')->first();
			$data['allOptions']   = DB::table('item_sourcing_options')->where('item_sourcing_options.header_id', $id)->count();
			$data['header_files'] = ItemSourcingHeaderFile::select('item_sourcing_header_file.*')->where('item_sourcing_header_file.header_id', $id)->get();
			$data['yesno']        = DB::table('sub_masterfile_yes_no')->get();
			return $this->view("item-sourcing.item-sourcing-detail-reject", $data);
		}

		//Remove row in option
		public function RemoveItemSource(Request $request){
			$data   = Request::all();	
			$opt_id = $data['opt_id'];

			ItemSourcingOptions::where('id', $opt_id)
			->update([
				'deleted_at'=> 		date('Y-m-d H:i:s'),
				'deleted_by'=> 		CRUDBooster::myId()
			]);	

			$message = ['status'=>'success', 'message' => 'Cancelled Successfully!'];
			echo json_encode($message);
			
		}

		//Select row in option
		public function SelectedOption(Request $request){
			
			$data   = Request::all();	
			$header_id = $data['header_id'];
			$opt_id    = $data['opt_id'];
			
			ItemSourcingOptions::where('id', $opt_id)
			->update([
				'selected_at'=> 	date('Y-m-d H:i:s'),
				'selected_by'=> 	CRUDBooster::myId()
			]);	

			ItemHeaderSourcing::where('id', $header_id)
			->update([
				'if_selected'=> 	1
			]);	

			$getDelete = ItemSourcingOptions::where('header_id',$header_id)->where('id','!=',$opt_id)->get();
			foreach($getDelete as $key => $val){
				ItemSourcingOptions::where('id', $val->id)
				->update([
					'deleted_at'=> 		date('Y-m-d H:i:s'),
					'deleted_by'=> 		CRUDBooster::myId()
				]);	
			}

			$message = ['status'=>'success', 'message' => 'Selected Successfully!'];
			echo json_encode($message);
			
		}

		//Select row in option
		public function selectedAlternativeOption(Request $request){
			$data   = Request::all();	
			$header_id = $data['header_id'];
			$opt_id    = $data['opt_id'];

			ItemSourcingOptions::where('id', $opt_id)
			->update([
				'selected_alternative_at'=> 	date('Y-m-d H:i:s'),
				'selected_alternative_by'=> 	CRUDBooster::myId()
			]);	

			ItemHeaderSourcing::where('id', $header_id)
			->update([
				'if_selected_alternative'=> 	1
			]);	

			$message = ['status'=>'success', 'message' => 'Selected Alternative Successfully!'];
			echo json_encode($message);
			
		}

		public function getRequestCancelNis($id) {
			ItemHeaderSourcing::where('id',$id)
			->update([
					'status_id'=> 8,
					'cancelled_by'=> CRUDBooster::myId(),
					'cancelled_at'=> date('Y-m-d H:i:s')	
			]);	
			ItemBodySourcing::where('header_request_id', $id)
			->update([
				'deleted_at'=> 		date('Y-m-d H:i:s'),
				'deleted_by'=> 		CRUDBooster::myId()
			]);	
			
			CRUDBooster::redirect(CRUDBooster::mainpath(), trans("Request has been cancelled successfully!"), 'success');
		}


		public function SubCategories(Request $request){
			$data = Request::all();	
			$id = $data['id'];
			//$categories = DB::table('new_category')->where('category_description', $id)->first();

			$subcategories = DB::table('new_sub_category')
							->select('new_sub_category.*')
							->where('category_id', $id)
							->where('sub_status', "ACTIVE")
							->orderby('sub_category_description', 'ASC')->get();
	
			return($subcategories);
		}

		public function Class(Request $request){
			$data = Request::all();	
			$id = $data['id'];
			$sub_categories = DB::table('new_sub_category')->where('id', $id)->first();

			$subcategories = DB::table('class')
							->select('class.*')
							->where('category_id', $sub_categories->category_id)
							->where('class_status', "ACTIVE")
							->orderby('class_description', 'ASC')->get();
	
			return($subcategories);
		}

		public function SubClass(Request $request){
			$data = Request::all();	
			$id = $data['id'];
			//$class = DB::table('new_class')->where('class_description', $id)->first();

			$subcategories = DB::table('new_sub_class')
							->select('new_sub_class.*')
							->where('class_id', $id)
							->where('sub_class_status', "ACTIVE")
							->orderby('sub_class_description', 'ASC')->get();
	
			return($subcategories);
		}

		public function saveMessage(Request $request){
            $fields   = Request::all();
			$id       = $fields['header_id'];
			$comments = $fields['message'];

			$comment = new ItemSourcingComments();
			
			$comment->item_header_id = $id;
			$comment->user_id = CRUDBooster::myId();
			$comment->comments = $comments;
			$comment->created_at = date('Y-m-d H:i:s');
			$comment->save();

			$item_sourcing_header = ItemHeaderSourcing::where(['id' => $id])->first();
		    $user = reset(explode(',', $item_sourcing_header->created_by));
	
			$config['content'] = "Item Source Message(".$item_sourcing_header->reference_number.")";
			if(in_array(CRUDBooster::myPrivilegeId(),[2,4,5,7,8,9,19])){
				$config['to'] = $link = CRUDBooster::adminPath('item_sourcing_for_quotation');
				$config['id_cms_users'] = [$item_sourcing_header->processed_by]; //The Id of the user that is going to receive notification. This could be an array of id users [1,2,3,4,5]
			}else if(in_array(CRUDBooster::myPrivilegeId(),[3,11,12,14,15,17,18])){
				$config['to'] = $link = CRUDBooster::adminPath('item-sourcing-header');
				$config['id_cms_users'] = [$item_sourcing_header->processed_by, $user];
			}else{
				$config['to'] = $link = CRUDBooster::adminPath('item-sourcing-header');
				$config['id_cms_users'] = [$user, $item_sourcing_header->approved_by];
			}
			
			
			CRUDBooster::sendNotification($config);

			$data = array();
			$data['status'] = 'error';
			$data['message'] = $comment;
			$data['comment_by'] = CRUDBooster::myName();
			if(!empty($comment)){
				$data['status'] = 'success';
			}
			return json_encode($data);
		}

		public function editItemSource(Request $request){
			$fields           = Request::all();
			$id               = $fields['id'];
			$request_type_id  = $fields['request_type_id'];
			$item_description = $fields['item_description'];
			$brand            = $fields['brand'];
			$model            = $fields['model'];
			$size             = $fields['size'];
			$actual_color     = $fields['actual_color'];
			$material         = $fields['material'];
			$thickness        = $fields['thickness'];
			$lamination       = $fields['lamination'];
			$add_ons          = $fields['add_ons'];
			$installation     = $fields['installation'];
			$dismantling      = $fields['dismantling'];
			$quantity         = $fields['quantity'];
			$header_id        = $fields['headerID'];	
			$sampling         = $fields['sampling'];
			$mark_up          = $fields['mark_up'];
       
			$item_source_body = ItemBodySourcing::where(['id' => $id])->first();
			if(in_array($request_type_id, [6])){
			  $item_source_body_marketing = ItemBodySourcing::whereIn('id', $id)->get();
			}
			//dd($item_source_body, $id);
			$countHeader = DB::table('item_sourcing_edit_versions')->where('item_sourcing_edit_versions.header_id', $id)->count();
			$finalCountHead = ($countHeader + 2);
	
			$commentLines = [];
			$insertCommentLines = [];
			if(in_array($request_type_id, [6])){
				foreach($item_source_body_marketing as $key => $val) {
					$commentLines['header_id']           = $header_id;
					$commentLines['body_id']             = $id[$key];
					$commentLines['old_description']     = $val->item_description;
					$commentLines['new_description']     = $item_description[$key];
					$commentLines['old_brand_value']     = $val->brand;
					$commentLines['new_brand_value']     = $brand[$key];
					$commentLines['old_model_value']     = $val->model;
					$commentLines['new_model_value']     = $model[$key];
					$commentLines['old_size_value']      = $val->size;
					$commentLines['new_size_value']      = $size[$key];
					$commentLines['old_ac_value']        = $val->actual_color;
					$commentLines['new_ac_value']        = $actual_color[$key];
					$commentLines['old_material']        = $val->material;
					$commentLines['new_material']        = $material[$key];
					$commentLines['old_thickness']        = $val->thickness;
					$commentLines['new_thickness']        = $thickness[$key];
					$commentLines['old_lamination']      = $val->lamination;
					$commentLines['new_lamination']      = $lamination[$key];
					$commentLines['old_add_ons']         = $val->add_ons;
					$commentLines['new_add_ons']         = $add_ons[$key];
					$commentLines['old_installation']    = $val->installation;
					$commentLines['new_installation']    = $installation[$key];
					$commentLines['old_dismantling']     = $val->dismantling;
					$commentLines['new_dismantling']     = $dismantling[$key];
					$commentLines['old_qty_value']       = $val->quantity;
					$commentLines['new_qty_value']       = $quantity[$key];
					$commentLines['version']             = "Version"."-". $finalCountHead;
					$commentLines['updated_by']          = CRUDBooster::myId();
					$commentLines['created_at'] 	     = date('Y-m-d H:i:s');
					$insertCommentLines[] = $commentLines;
				}
				ItemSourcingEditVersions::insert($insertCommentLines);
			}else{
				ItemSourcingEditVersions::Create(
					[
					'header_id'           => $header_id,
					'body_id'             => $id,
					'old_description'     => $item_source_body->item_description,
					'new_description'     => $item_description,
					'old_brand_value'     => $item_source_body->brand,
					'new_brand_value'     => $brand,
					'old_model_value'     => $item_source_body->model,
					'new_model_value'     => $model,
					'old_size_value'      => $item_source_body->size,
					'new_size_value'      => $size,
					'old_ac_value'        => $item_source_body->actual_color,
					'new_ac_value'        => $actual_color,
					'old_material'        => $item_source_body->material,
					'new_material'        => $material,
					'old_thickness'       => $item_source_body->thickness,
					'new_thickness'       => $thickness,
					'old_lamination'      => $item_source_body->lamination,
					'new_lamination'      => $lamination,
					'old_add_ons'         => $item_source_body->add_ons,
					'new_add_ons'         => $add_ons,
					'old_installation'    => $item_source_body->installation,
					'new_installation'    => $installation,
					'old_dismantling'     => $item_source_body->dismantling,
					'new_dismantling'     => $dismantling,
					'old_qty_value'       => $item_source_body->quantity,
					'new_qty_value'       => $quantity,
					'version'             => "Version"."-". $finalCountHead,
					'updated_by'          => CRUDBooster::myId(),
					'created_at' 	      => date('Y-m-d H:i:s'),
					]
				);  	
			}
			if(in_array($request_type_id, [6])){
				foreach($item_description as $key => $val) {
					ItemBodySourcing::where(['id' => $id[$key]])
					->update([
							'item_description'           => $val, 
							'brand'                      => $brand[$key],
							'model'                      => $model[$key],
							'size'                       => $size[$key],
							'actual_color'               => $actual_color[$key],
							'material'                   => $material[$key],
							'thickness'                  => $thickness[$key],
							'lamination'                 => $lamination[$key],
							'add_ons'                    => $add_ons[$key],
							'installation'               => $installation[$key],
							'dismantling'                => $dismantling[$key],
							'quantity'                   => $quantity[$key],
							'updated_by'                 => CRUDBooster::myId(),
							]);
				}
				ItemHeaderSourcing::where(['id' => $id])
					->update([
							'sampling'          => $sampling, 
							'mark_up'           => $mark_up
							]);
			}else{
				ItemBodySourcing::where(['id' => $id])
				->update([
						'item_description'           => $item_description, 
						'brand'                      => $brand,
						'model'                      => $model,
						'size'                       => $size,
						'actual_color'               => $actual_color,
						'material'                   => $material,
						'thickness'                  => $thickness,
						'lamination'                 => $lamination,
						'add_ons'                    => $add_ons,
						'installation'               => $installation,
						'dismantling'                => $dismantling,
						'quantity'                   => $quantity,
						'updated_by'                 => CRUDBooster::myId(),
						]);
			}
			
		    $item_source_header = ItemHeaderSourcing::where(['id' => $header_id])->first();
			$employee_name = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
			$approver_name = DB::table('cms_users')->where('id', $employee_name->approver_id)->first();
			$department_name = DB::table('departments')->where('id', $employee_name->department_id)->first();
			$fhil = "fhilipacosta@digits.ph";
	
			$infos['request_type_id'] = $request_type_id;
			$infos['assign_to'] = $employee_name->bill_to;
			$infos['reference_number'] = $item_source_header->reference_number;
			$infos['item_description'] = $item_description;
			$infos['brand'] = $brand;
			$infos['model'] = $model;
			$infos['size'] = $size;
			$infos['actual_color'] = $actual_color;
			$infos['material'] = $material;
			$infos['thickness'] = $thickness;
			$infos['lamination'] = $lamination;
			$infos['add_ons'] = $add_ons;
			$infos['installation'] = $installation;
			$infos['dismantling'] = $dismantling;
			$infos['quantity'] = $quantity;
		
			if($item_source_header->status_id != 1){
				Mail::to($employee_name->email)
				//->cc([$fhil])
				->send(new Email($infos));
			}
			
			$message = ['status'=>'success', 'message' => 'Update Successfully!'];
			echo json_encode($message);
		}

		public function getVersions(Request $request){
			$data = Request::all();	
		
			$id = $data['header_id'];

			$versions = DB::table('item_sourcing_edit_versions')
			                ->leftjoin('cms_users', 'item_sourcing_edit_versions.updated_by','=', 'cms_users.id')
							->select('item_sourcing_edit_versions.*',
							          'cms_users.*'
							          )
							->where('header_id', $id)
							->orderBy('version','desc')
							->get();
			return($versions);
		}

		public function getDownload($id) {
      
			$getFile = DB::table('item_sourcing_header_file')->where('id',$id)->first();
			$file= public_path(). "/vendor/crudbooster/item_source_header_file/".$getFile->file_name;
            if(in_array($getFile->ext,['xlsx','docs','pdf'])){
			    $headers = array(
					'Content-Type: application/pdf',
					);
			    return Response::download($file, $getFile->file_name, $headers);
			}else{
				return Response::download($file, $getFile->file_name);
			}
		}


	}
?>