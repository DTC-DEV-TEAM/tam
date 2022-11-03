<?php namespace App\Http\Controllers;

	use Session;
	//use Request;
	use DB;
	use CRUDBooster;
	use App\Country;
	use App\PaymentMethod;
	use App\Items;
	use App\OrderHeader;
	use App\OrderItems;
	use App\Status;
	use App\OrderCounter;
	use App\OrderShipping;
	use App\OrderFee;
	use App\Serials;
	use App\Platform;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Input;
	use Illuminate\Support\Facades\Log;
	use Illuminate\Support\Facades\Redirect;
	use Excel;
	use Carbon\Carbon;


	class AdminEmployeesController extends \crocodicstudio\crudbooster\controllers\CBController {

        public function __construct() {
			// Register ENUM type
			//$this->request = $request;
			DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping("enum", "string");
		}

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "customer_location_name";
			$this->limit = "20";
			$this->orderby = "id,desc";
			$this->global_privilege = false;
			$this->button_table_action = true;
			$this->button_bulk_action = true;
			$this->button_action_style = "button_icon";
			$this->button_add = true;
			$this->button_edit = true;
			$this->button_delete = true;
			$this->button_detail = true;
			$this->button_show = true;
			$this->button_filter = true;
			$this->button_import = false;
			$this->button_export = true;
			$this->table = "employees";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Employee Code","name"=>"employee_code"];
			$this->col[] = ["label"=>"Customer/Location Name","name"=>"customer_location_name"];
			//$this->col[] = ["label"=>"Bill To (Company Name)","name"=>"bill_to"];
			$this->col[] = ["label"=>"Company Name","name"=>"company_name", "join"=>"companies,company_name"];

			$this->col[] = ["label"=>"Department","name"=>"department_id", "join"=>"departments,department_name"];

			$this->col[] = ["label"=>"Sub Department","name"=>"sub_department_id", "join"=>"sub_department,sub_department_name"];

			$this->col[] = ["label"=>"Position","name"=>"position_id"];

			$this->col[] = ["label"=>"Ship To Address","name"=>"address_line1"];
			$this->col[] = ["label"=>"Status","name"=>"status_id","join"=>"statuses_employees,status_description"];
			//$this->col[] = ["label"=>"Created By","name"=>"created_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"Created Date","name"=>"created_at"];
			//$this->col[] = ["label"=>"Updated By","name"=>"updated_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"Updated Date","name"=>"updated_at"];

			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ["label"=>"Last Name","name"=>"contact_person_ln","type"=>"text","validation"=>"required|min:1|max:255",'width'=>'col-sm-6','placeholder'=>'Last Name','help'=>'Put N/A if not applicable'];
			$this->form[] = ["label"=>"First Name","name"=>"contact_person_fn","type"=>"text","validation"=>"required|min:1|max:255",'width'=>'col-sm-6','placeholder'=>'First Name','help'=>'Put N/A if not applicable'];
			$this->form[] = ["label"=>"Contact Person","name"=>"contact_person","type"=>"text","validation"=>"required|min:1|max:255",'width'=>'col-sm-6','placeholder'=>'Contact Person','readonly'=>true];
			$this->form[] = ["label"=>"Bill To (Company Name)","name"=>"bill_to","type"=>"text","validation"=>"required|min:1|max:255",'width'=>'col-sm-6','placeholder'=>'Bill To (Company Name)','readonly'=>true];
			$this->form[] = ["label"=>"Customer/Location Name","name"=>"customer_location_name","type"=>"text","validation"=>"required|min:1|max:255",'width'=>'col-sm-6','placeholder'=>'Customer/Location Name','readonly'=>true];
			
			//$this->form[] = ["label"=>"Company Name","name"=>"company_name","type"=>"text","validation"=>"required|min:1|max:255",'width'=>'col-sm-6','placeholder'=>'Company Name','help'=>'Put N/A if not applicable'];
			
			$this->form[] = ['label'=>'Company Name','name'=>'company_name','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-6','datatable'=>'companies,company_name','datatable_where'=>"status = 'ACTIVE'"];

			//$this->form[] = ["label"=>"Designation","name"=>"contact_designation_id","type"=>"select2","validation"=>"required","width"=>"col-sm-6","datatable"=>"designation,designation_description","datatable_where"=>"status!='INACTIVE'"];
			//$this->form[] = ["label"=>"Department","name"=>"contact_department_id","type"=>"select2","validation"=>"required","width"=>"col-sm-6","datatable"=>"department,department_name","datatable_where"=>"status!='INACTIVE'"];

			$this->form[] = ['label'=>'Department','name'=>'department_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-6','datatable'=>'departments,department_name','datatable_where'=>"status = 'ACTIVE'"];

			$this->form[] = ['label'=>'Sub Department','name'=>'sub_department_id','type'=>'select2','validation'=>'integer|min:0','width'=>'col-sm-6','datatable'=>'sub_department,sub_department_name','datatable_where'=>"status = 'ACTIVE'"];
			
			$this->form[] = ["label"=>"Position","name"=>"position_id","type"=>"text","validation"=>"required|min:1|max:255",'width'=>'col-sm-6','placeholder'=>'Position'];

			//$this->form[] = ['label'=>'Position','name'=>'position_id','type'=>'select2','validation'=>'integer|min:0','width'=>'col-sm-6','datatable'=>'positions,position_description','datatable_where'=>"status = 'ACTIVE'" ,'parent_select'=>'department_id'];

			//$this->form[] = ['label'=>'Position','name'=>'position_id','type'=>'select2','validation'=>'required','width'=>'col-sm-6'];

			if(CRUDBooster::getCurrentMethod() == 'getEdit' || CRUDBooster::getCurrentMethod() == 'postEditSave' || CRUDBooster::getCurrentMethod() == 'getDetail') {
				$this->form[] = ["label"=>"Building#/Building Name","name"=>"building_no","type"=>"text","validation"=>"min:1|max:255",'width'=>'col-sm-6','placeholder'=>'Building#/Building Name'];
				$this->form[] = ["label"=>"Lot & Blk#/Street Name","name"=>"lot_blk_no_streetname","type"=>"text","validation"=>"min:1|max:255",'width'=>'col-sm-6','placeholder'=>'Lot & Blk#/Street Name'];
				$this->form[] = ["label"=>"Barangay","name"=>"barangay","type"=>"text","validation"=>"min:1|max:255",'width'=>'col-sm-6','placeholder'=>'Barangay'];
				$this->form[] = ["label"=>"City/Province","name"=>"city_id","type"=>"select2","validation"=>"","width"=>"col-sm-6","datatable"=>"cities,city_name","datatable_where"=>"status!='INACTIVE'"];
				$this->form[] = ["label"=>"State/Region","name"=>"state_id","type"=>"select2","validation"=>"","width"=>"col-sm-6","datatable"=>"states,state_name","datatable_where"=>"status!='INACTIVE'"];
				$this->form[] = ["label"=>"Area Code/Zip Code","name"=>"area_code_zip_code","type"=>"text","validation"=>"min:1|max:255",'width'=>'col-sm-6','placeholder'=>'Area Code/Zip Code'];
				$this->form[] = ["label"=>"Country","name"=>"country_id","type"=>"select2","validation"=>"","width"=>"col-sm-6","datatable"=>"countries,country_name","datatable_where"=>"status!='INACTIVE'"];
				$this->form[] = ["label"=>"Ship To Address","name"=>"address_line1","type"=>"text","validation"=>"required|min:1|max:255",'width'=>'col-sm-6','placeholder'=>'Ship To Address','readonly'=>true];
			}
			
			//$this->form[] = ["label"=>"International Country Code 1","name"=>"international_country_code_1","type"=>"number","validation"=>"min:1",'width'=>'col-sm-6','placeholder'=>'International Country Code 1'];
			//$this->form[] = ["label"=>"Area Code 1","name"=>"area_code_1","type"=>"number","validation"=>"min:1",'width'=>'col-sm-6','placeholder'=>'Area Code 1'];
			//$this->form[] = ["label"=>"Number 1","name"=>"number_1","type"=>"number","validation"=>"min:1",'width'=>'col-sm-6','placeholder'=>'Number 1'];
			//$this->form[] = ["label"=>"Landline#","name"=>"contact_landline_no","type"=>"text","validation"=>"min:1",'width'=>'col-sm-6','placeholder'=>'Landline#','readonly'=>true];
			//$this->form[] = ["label"=>"International Country Code 2","name"=>"international_country_code_2","type"=>"number","validation"=>"min:1",'width'=>'col-sm-6','placeholder'=>'International Country Code 1'];
			//$this->form[] = ["label"=>"Area Code 2","name"=>"area_code_2","type"=>"number","validation"=>"min:1",'width'=>'col-sm-6','placeholder'=>'Area Code 1'];
			//$this->form[] = ["label"=>"Number 2","name"=>"number_2","type"=>"number","validation"=>"min:1",'width'=>'col-sm-6','placeholder'=>'Number 1'];
			//$this->form[] = ["label"=>"Mobile#","name"=>"mobile_number","type"=>"text","validation"=>"min:1|max:255",'width'=>'col-sm-6','placeholder'=>'Mobile#','readonly'=>true];
			$this->form[] = ["label"=>"Email Address","name"=>"email_address","type"=>"email",'validation'=>'email|unique:employees,email_address,'.CRUDBooster::getCurrentId(),'width'=>'col-sm-6'];
			//$this->form[] = ["label"=>"Bank Details","name"=>"bank_details","type"=>"text","validation"=>"min:1|max:255",'width'=>'col-sm-6','placeholder'=>'Bank Details'];
			//$this->form[] = ["label"=>"Beneficiary Name","name"=>"beneficiary_name","type"=>"text","validation"=>"min:1|max:255",'width'=>'col-sm-6','placeholder'=>'Beneficiary Name'];
			//$this->form[] = ["label"=>"Account Number","name"=>"account_number","type"=>"text","validation"=>"min:1|max:255",'width'=>'col-sm-6','placeholder'=>'Account Number'];
			//$this->form[] = ["label"=>"Beneficiary Address","name"=>"beneficiary_address","type"=>"text","validation"=>"min:1|max:255",'width'=>'col-sm-6','placeholder'=>'Beneficiary Address'];
			//$this->form[] = ["label"=>"Bank Beneficiary","name"=>"bank_beneficiary","type"=>"text","validation"=>"min:1|max:255",'width'=>'col-sm-6','placeholder'=>'Bank Beneficiary'];
			//$this->form[] = ["label"=>"Bank Address","name"=>"bank_address","type"=>"text","validation"=>"min:1|max:255",'width'=>'col-sm-6','placeholder'=>'Bank Address'];
			//$this->form[] = ["label"=>"Bank Code","name"=>"bank_code","type"=>"text","validation"=>"min:1|max:255",'width'=>'col-sm-6','placeholder'=>'Bank Code'];
			//$this->form[] = ["label"=>"SWIFT Code","name"=>"switf_code","type"=>"text","validation"=>"min:1|max:255",'width'=>'col-sm-6','placeholder'=>'SWIFT Code'];
			//$this->form[] = ["label"=>"BIC","name"=>"bic","type"=>"text","validation"=>"min:1|max:255",'width'=>'col-sm-6','placeholder'=>'BIC'];
			//$this->form[] = ["label"=>"IBAN","name"=>"iban","type"=>"text","validation"=>"min:1|max:255",'width'=>'col-sm-6','placeholder'=>'IBAN'];
			//$this->form[] = ["label"=>"ABA","name"=>"aba","type"=>"text","validation"=>"min:1|max:255",'width'=>'col-sm-6','placeholder'=>'ABA'];
			//$this->form[] = ["label"=>"Currency","name"=>"currency_id","type"=>"select2","validation"=>"required","width"=>"col-sm-6","datatable"=>"currencies,currency_code","datatable_where"=>"status!='INACTIVE'"];
			//$this->form[] = ["label"=>"Credit Limit","name"=>"credit_limit","type"=>"text","validation"=>"min:1|max:255",'width'=>'col-sm-6','placeholder'=>'Credit Limit'];
			//$this->form[] = ["label"=>"Payment Terms","name"=>"payment_terms_id","type"=>"select2","validation"=>"required","width"=>"col-sm-6","datatable"=>"payment_terms,payment_terms_description","datatable_where"=>"status!='INACTIVE'"];
			//$this->form[] = ["label"=>"Payment Mode","name"=>"payment_mode_id","type"=>"select2","validation"=>"required","width"=>"col-sm-6","datatable"=>"payment_mode,payment_mode_description","datatable_where"=>"status!='INACTIVE'"];
			if(CRUDBooster::getCurrentMethod() == 'getEdit' || CRUDBooster::getCurrentMethod() == 'postEditSave'){

				$this->form[] = ['label'=>'Status','name'=>'status_id','type'=>'select','validation'=>'required','width'=>'col-sm-6',"datatable"=>"statuses_employees,status_description"];
				
			}
			if(CRUDBooster::getCurrentMethod() == 'getDetail'){
				$this->form[] = ["label"=>"Created By","name"=>"created_by",'type'=>'select',"datatable"=>"cms_users,name"];
				$this->form[] = ["label"=>"Created Date","name"=>"created_at"];
				$this->form[] = ["label"=>"Updated By","name"=>"updated_by",'type'=>'select',"datatable"=>"cms_users,name"];
				$this->form[] = ["label"=>"Updated Date","name"=>"updated_at"];
			}
			# END FORM DO NOT REMOVE THIS LINE

			# OLD START FORM
			//$this->form = [];
			//$this->form[] = ["label"=>"Employee Code","name"=>"employee_code","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Channel Id","name"=>"channel_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"channel,id"];
			//$this->form[] = ["label"=>"Customer Type","name"=>"customer_type","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Customer Location Name","name"=>"customer_location_name","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Bill To","name"=>"bill_to","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Address Line1","name"=>"address_line1","type"=>"textarea","required"=>TRUE,"validation"=>"required|string|min:5|max:5000"];
			//$this->form[] = ["label"=>"Building No","name"=>"building_no","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Lot Blk No Streetname","name"=>"lot_blk_no_streetname","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Barangay","name"=>"barangay","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"City Id","name"=>"city_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"city,id"];
			//$this->form[] = ["label"=>"State Id","name"=>"state_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"state,id"];
			//$this->form[] = ["label"=>"Area Code Zip Code","name"=>"area_code_zip_code","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Country Id","name"=>"country_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"country,id"];
			//$this->form[] = ["label"=>"Contact Person","name"=>"contact_person","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Contact Person Ln","name"=>"contact_person_ln","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Contact Person Fn","name"=>"contact_person_fn","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Company Name","name"=>"company_name","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Email Address","name"=>"email_address","type"=>"email","required"=>TRUE,"validation"=>"required|min:1|max:255|email|unique:employees","placeholder"=>"Please enter a valid email address"];
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

				$this->index_button[] = ["label"=>"Upload Employees","icon"=>"fa fa-upload","url"=>CRUDBooster::mainpath('bulk-upload-employees'),"color"=>"primary"];

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
			$this->load_js[] = asset("js/employee_master.js");
	        
	        
	        
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
	            
	    }

	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate row of index table html 
	    | ---------------------------------------------------------------------- 
	    |
	    */    
	    public function hook_row_index($column_index,&$column_value) {	        
	    	//Your code here
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

			$building = "DIGITS BLDG";
			$street = "#56 MAYOR IGNACIO SANTOS DIAZ";
			$baranggay = "SAN MARTIN DE PORRES";
			$city = "QUEZON";
			$state = "NCR";
			$zip_code = "1111";
			$country = "PHILIPPINES";

			$full_address = $building." ".$street." ".$baranggay." ".$city." ".$state." ".$zip_code." ".$country;
			
			$count_header = DB::table("employees")->count();
			$header_ref   =  str_pad($count_header + 1, 4, '0', STR_PAD_LEFT);			
			$reference_number	= "EMP-".$header_ref;

			$postdata["employee_code"]				= $reference_number; //description
			
			$postdata["created_by"]					= CRUDBooster::myId();

			$postdata["status_id"]					= 1;

			$postdata["building_no"]				= $building;
			$postdata["lot_blk_no_streetname"]		= $street;
			$postdata["barangay"]					= $baranggay;
			$postdata["city_id"]					= "1060";
			$postdata["state_id"]					= "15";
			$postdata["area_code_zip_code"]			= $zip_code;
			$postdata["country_id"]					= "10";					
			$postdata["address_line1"]				= $full_address;							
									
			//$postdata["action_type"]				="Create";
			//$postdata["encoder_privilege_id"]		= CRUDBooster::myPrivilegeId();
			//$postdata['approval_status_id']			= ApprovalWorkflowSetting::where('workflow_number', 1)->where('action_type', 'Create')->where('encoder_privilege_id', CRUDBooster::myPrivilegeId())->where('cms_moduls_id', 'LIKE', '%' . CRUDBooster::getCurrentModule()->id . '%')->value('current_state');

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

			$employee 	= DB::table('employees')->where(['id' => $id])->first();

			CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.alert_add_employee",['reference_number'=>$employee->employee_code]), 'success');

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
			$postdata["updated_by"]				= CRUDBooster::myId();

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
			$employee 	= DB::table('employees')->where(['id' => $id])->first();

			CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.alert_edit_employee",['reference_number'=>$employee->employee_code]), 'success');

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


		public function getCity($id){

	    	$city_value = DB::table("cities")->where('id', $id)->value('city_name');
			return $city_value;
			
		}
		
		public function getState($id){

	    	$state_value = DB::table("states")->where('id', $id)->value('state_name');
			return $state_value;
			
	    }

		public function getCountry($id){

	    	$country_value = DB::table("countries")->where('id', $id)->value('country_name');
			return $country_value;
			
		}

		public function getPosition($id){

	    	$country_value = DB::table("sub_department")->where('department_id', $id)->get();
			return $country_value;
			
		}


		public function getEmployee($id){

	    	$country_value = DB::table("employees")->where('bill_to', $id)->get();
			return $country_value;
			
		}


		public function UploadEmployeeTemplate() {

			$data['page_title']= 'Employees Upload';

			return view('bulk_upload_employees', $data)->render();

		}

		
		public function DownloadEmployeeTemplate(){
	
			
			$filename = "download-employees-template".date("Ymd")."-".date("h.i.sa"). ".csv";
	
			header("Content-Disposition: attachment; filename=\"$filename\"");
			header("Content-Type: text/csv; charset=UTF-16LE");
	
			$out = fopen("php://output", 'w');
			$flag = false;

			if(!$flag) {
				// display field/column names as first row
				fputcsv($out, array('Last Name', 'First Name', 'Company Name', 'Department', 'Sub Department','Position', 'Email Address'));
				$flag = true;
			}
			
			fputcsv($out, array('Doe', 'James', 'DIGITS', 'BUSINESS PROCESS GROUP DEPARTMENT', 'BPG-SYSTEM', 'ASSOCIATE SOFTWARE DEVELOPER', 'doe@digits.ph'));
			fputcsv($out, array('Doe', 'Victor', 'DIGITS', 'BUSINESS PROCESS GROUP DEPARTMENT', 'BPG-SYSTEM', 'ASSOCIATE SOFTWARE DEVELOPER', 'doe@digits.ph'));

			fclose($out);
			
			exit;

		

		}

		/*
		public function DownloadEmployeeTemplate(){

			$db_con = mysqli_connect(
				env('DB_HOST'), 
				env('DB_USERNAME'), 
				env('DB_PASSWORD'), 
				env('DB_DATABASE'), 
				env('DB_PORT')
			);
	
			if(!$db_con) {
				die('Could not connect: ' . mysqli_error($db_con));
			}
	
			$filename = "download-employees-template".date("Ymd")."-".date("h.i.sa"). ".csv";
	
			header("Content-Disposition: attachment; filename=\"$filename\"");
			header("Content-Type: text/csv; charset=UTF-16LE");
	
			$out = fopen("php://output", 'w');
			$flag = false;
	
			//$query = "SELECT * FROM `brand`";

		

			ini_set('memory_limit', '-1');


			//$result = mysqli_query($db_con, $query) or die("Database Error:". mysqli_error($db_con));
			
			

			//while($row = mysqli_fetch_row($result)) {
				
				if(!$flag) {
					// display field/column names as first row
					fputcsv($out, array('Last Name', 'First Name', 'Company Name', 'Department', 'Position', 'Email Address'));
					$flag = true;
				}
				

				fputcsv($out, array('Doe', 'James', 'DIGITS', 'BUSINESS PROCESS GROUP DEPARTMENT', 'ASSOCIATE SOFTWARE DEVELOPER', 'doe@digits.ph'));


			//	fputcsv($out, array_values($row), ',', '"');
			//}
	
			fclose($out);
			exit;



			

			Excel::create('download-employees-template'.date("Ymd").'-'.date("h.i.sa"), function ($excel) {

				$excel->sheet('employees', function ($sheet) {
					$sheet->row(1, array('Last Name', 'First Name', 'Company Name', 'Department', 'Position', 'Email Address'));
					$sheet->row(2, array('Doe', 'James', 'DIGITS', 'BUSINESS PROCESS GROUP DEPARTMENT', 'ASSOCIATE SOFTWARE DEVELOPER', 'doe@digits.ph'));
					//$sheet->row(3, array('1 YEAR WARRANTY', '4466', '80001349', 'USB-C to Lightning Cable', '1', '1750.00', 'SN-1003'));
					//$sheet->row(3, array('1000-1001', date("Y-m-d"), 'HOME OFFICE.DIGITS.FBD.ONL', '', '', 'Jhon', 'Doe', '', 'Quezon City', '', 'jhondoe@gmail.com', '09205589441', '80019059', 'IFG HED SOUND HUB SYNC BLACK', '1 YEAR WARRANTY', '', '990','1', '', 'Shipping', 'Flat Rate', '100', 'Points', '50'));
					//$sheet->row(4, array('1000-1002', date("Y-m-d"), 'HOME OFFICE.DIGITS.FBD.ONL', '', '', 'Jhon', 'Doe', '', 'Quezon City', '', 'jhondoe@gmail.com', '09205589441', '80020797', 'AMZ UNT T-REX ROCK BLACK', '1 YEAR WARRANTY', '', '6590','2', '', '', '', '', '', ''));
				

					$sheet->row(1, function ($row) {
						$row->setBackground('#FFFF00');
						$row->setAlignment('center');
					});
				
				}); 

				$excel->sheet('company', function ($sheet) {

					$sheet->row(1, array('Company Name'));

					$companies = DB::table('companies')->where('status', 'ACTIVE')->get();

					$counter = 1;
					foreach($companies as $company){

						$counter++;

						$sheet->row($counter, array($company->company_name));

					}

				}); 


				$excel->sheet('department', function ($sheet) {

					$sheet->row(1, array('Department Name'));

					$departments = DB::table('departments')->where('status', 'ACTIVE')->get();

					$counter = 1;
					foreach($departments as $department){

						$counter++;

						$sheet->row($counter, array($department->department_name));

					}

				});

				$excel->sheet('position', function ($sheet) {

					$sheet->row(1, array('Department Name', 'Position Name'));

					$positions = DB::table('positions')->where('positions.status', 'ACTIVE')
										->join('departments', 'positions.department_id','=', 'departments.id')
										->select(	'positions.*',
													'departments.department_name as department_name'
												)->orderby('positions.department_id', 'asc')->get();

					$counter = 1;
					foreach($positions as $position){

						$counter++;

						$sheet->row($counter, array($position->department_name, $position->position_description));

					}

				});


			})->download('xlsx');

			


		}
		*/

		
		public function BulkEmployeesUploadOld(Request $request) {
			
			$file = $request->file('import_file');
			
			$validator = \Validator::make(
				[
					'file' => $file,
					'extension' => strtolower($file->getClientOriginalExtension()),
				],
				[
					'file' => 'required',
					'extension' => 'required|in:xlsx',
				]
			);

			if ($validator->fails()) {
				CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.alert_upload_employee_failed"), 'danger');
			}

			if (Input::hasFile('import_file')) {
				$path = Input::file('import_file')->getRealPath();
				
				$csv = array_map('str_getcsv', file($path));
				$dataExcel = Excel::load($path, function($reader) {
                })->get();
				

				$datarowchecker = Excel::load($path, function($reader) {
					$reader->noHeading()->all();			
				})->get();

				if($datarowchecker[0][0][0] != "L"){

					if( $datarowchecker[0][0][0] != "Last Name"){

						CRUDBooster::redirect(CRUDBooster::mainpath(), trans("Error, Invalid Tempalte Header !"), 'danger');
					}

					if( $datarowchecker[0][0][1] != "First Name"){

						CRUDBooster::redirect(CRUDBooster::mainpath(), trans("Error, Invalid Tempalte Header !"), 'danger');
					}

					if( $datarowchecker[0][0][2] != "Company Name"){

						CRUDBooster::redirect(CRUDBooster::mainpath(), trans("Error, Invalid Tempalte Header !"), 'danger');
					}

					if( $datarowchecker[0][0][3] != "Department"){

						CRUDBooster::redirect(CRUDBooster::mainpath(), trans("Error, Invalid Tempalte Header !"), 'danger');
					}

					if( $datarowchecker[0][0][4] != "Position"){

						CRUDBooster::redirect(CRUDBooster::mainpath(), trans("Error, Invalid Tempalte Header !"), 'danger');
					}

					if( $datarowchecker[0][0][5] != "Email Address"){

						CRUDBooster::redirect(CRUDBooster::mainpath(), trans("Error, Invalid Tempalte Header !"), 'danger');
					}

									
					$data = array();

					$order_id = array(); 
					
					$dataLines = array();
					
					if(!empty($dataExcel[0]) && $dataExcel[0]->count()) {

						$cnt_success = 0;
						$cnt_fail = 0;
						$error_message = "";


						foreach ($dataExcel[0] as $key => $value) {

							if( empty($value->last_name) ) {

								$cnt_fail++;
								$error_message = "Error, Blank Last Name !";
							}

							if( empty($value->first_name) ) {

								$cnt_fail++;
								$error_message = "Error, Blank First Name !";
							}
							

							if( empty($value->company_name) ) {

								$cnt_fail++;
								$error_message = "Error, Blank Company Name !";
							}

							if( empty($value->department) ) {

								$cnt_fail++;
								$error_message = "Error, Blank Department !";
							}

							if( empty($value->position) ) {

								$cnt_fail++;
								$error_message = "Error, Blank Position !";

							}

							/*if( empty($value->email_address) ) {

								$cnt_fail++;
								$error_message = "Error, Blank Email Address !";

							}*/

							$companies 		= DB::table('companies')->where(['company_name' => $value->company_name])->first();

							$departments 	= DB::table('departments')->where(['department_name' => $value->department])->first();

							$positions 		= DB::table('positions')->where(['position_description' => $value->position])->first();
							
							if( empty($companies) ) {

								$cnt_fail++;
								$error_message = "Error, Invalid Company Name !";
							}

							if( empty($departments) ) {

								$cnt_fail++;
								$error_message = "Error, Invalid Department !";
							}

							if( empty($positions) ) {

								$cnt_fail++;
								$error_message = "Error, Invalid Position !";
							}


						}
						

						if($cnt_fail == 0){

							foreach ($dataExcel[0] as $key => $value) {

								//if(!in_array($value->order_id,$order_id)){

									$companies 		= DB::table('companies')->where(['company_name' => $value->company_name])->first();

									$departments 	= DB::table('departments')->where(['department_name' => $value->department])->first();
		
									$positions 		= DB::table('positions')->where(['position_description' => $value->position])->first();

									$full_name   			= strtoupper($value->last_name).", ".strtoupper($value->first_name);

									$full_name_employee 	= strtoupper($value->last_name).", ".strtoupper($value->first_name).".EEE";

									$building = "DIGITS BLDG";
									$street = "#56 MAYOR IGNACIO SANTOS DIAZ";
									$baranggay = "SAN MARTIN DE PORRES";
									$city = "QUEZON";
									$state = "NCR";
									$zip_code = "1111";
									$country = "PHILIPPINES";

									$full_address = $building." ".$street." ".$baranggay." ".$city." ".$state." ".$zip_code." ".$country;

									$count_header = DB::table("employees")->count();
									$header_ref   =  str_pad($count_header + 1, 4, '0', STR_PAD_LEFT);			
									$reference_number	= "EMP-".$header_ref;


									DB::beginTransaction();

									try {

											DB::table('employees')->insert([
												'status_id'							=> "1",
												'employee_code'						=> $reference_number,
												'contact_person_ln'					=> strtoupper($value->last_name),
												'contact_person_fn'					=> strtoupper($value->first_name),
												'company_name'						=> $companies->id,
												'department_id'						=> $departments->id,
												'position_id'						=> $positions->id,
												'email_address'						=> $value->email_address,
												'contact_person'					=> $full_name,
												'bill_to'							=> $full_name,
												'customer_location_name'			=> $full_name_employee,
												'building_no'						=> $building,
												'lot_blk_no_streetname'				=> $street,
												'barangay'							=> $baranggay,
												'city_id'							=> "1060",
												'state_id'							=> "15",
												'area_code_zip_code'				=> $zip_code,
												'country_id'						=> "10",
												'address_line1'						=> $full_address,
												'created_by' 						=> CRUDBooster::myId(),
												'created_at' 						=> date('Y-m-d H:i:s')
											]);

											DB::commit();
											//CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.alert_pullout_data_success",['mps_reference'=>$pullout_header->reference]), 'success');
									} catch (\Exception $e) {
										DB::rollback();
										CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.alert_database_error",['database_error'=>$e]), 'danger');
									}


									//array_push($order_id,$value->order_id);

								//}
								
							}

							CRUDBooster::redirect(CRUDBooster::mainpath(), trans("Orders Upload successfully!"), 'success');

						}else{

							CRUDBooster::redirect(CRUDBooster::mainpath(), trans($error_message), 'danger');

						}


					
					}
					
				}

			}
		}

		public function BulkEmployeesUpload(Request $request) {
			
			$file = $request->file('import_file');

			$validator = \Validator::make(
				[
					'file' => $file,
					'extension' => strtolower($file->getClientOriginalExtension()),
				],
				[
					'file' => 'required',
					'extension' => 'required|in:csv',
				]
			);

			if ($validator->fails()) {
				CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.alert_upload_employee_failed"), 'danger');
			}



			if ($request->hasFile('import_file')) {
				$path = $request->file('import_file')->getRealPath();
				
				$csv = array_map('str_getcsv', file($path));

			
				$dataExcel = Excel::toArray([],$path);

				//if($datarowchecker[0][0][0] != "L"){

					if( $dataExcel[0][0][0] != "Last Name"){

						CRUDBooster::redirect(CRUDBooster::mainpath(), trans("Error, Invalid Tempalte Header !"), 'danger');
					}

					if( $dataExcel[0][0][1] != "First Name"){

						CRUDBooster::redirect(CRUDBooster::mainpath(), trans("Error, Invalid Tempalte Header !"), 'danger');
					}

					if( $dataExcel[0][0][2] != "Company Name"){

						CRUDBooster::redirect(CRUDBooster::mainpath(), trans("Error, Invalid Tempalte Header !"), 'danger');
					}

					if( $dataExcel[0][0][3] != "Department"){

						CRUDBooster::redirect(CRUDBooster::mainpath(), trans("Error, Invalid Tempalte Header !"), 'danger');
					}

					if( $dataExcel[0][0][4] != "Sub Department"){

						CRUDBooster::redirect(CRUDBooster::mainpath(), trans("Error, Invalid Tempalte Header !"), 'danger');
					}

					if( $dataExcel[0][0][5] != "Position"){

						CRUDBooster::redirect(CRUDBooster::mainpath(), trans("Error, Invalid Tempalte Header !"), 'danger');
					}

					if( $dataExcel[0][0][6] != "Email Address"){

						CRUDBooster::redirect(CRUDBooster::mainpath(), trans("Error, Invalid Tempalte Header !"), 'danger');
					}

					//dd($dataExcel[0][1]);
									
					$data = array();

					$order_id = array(); 
					
					$dataLines = array();
					
					if(!empty($dataExcel[0])) {

						$cnt_success = 0;
						$cnt_fail = 0;
						$error_message = "";

						$count_row = 0;

						//dd(count((array)$dataExcel[0]));

							for($x=0; $x < count((array)$dataExcel[0]) - 1; $x++) {

								$count_row++;


								//dd($dataExcel[0][3][0]);


								if( is_null($dataExcel[0][$count_row][0]) ) {

									$cnt_fail++;
									$error_message = "Error, Blank Last Name !";
								}

								if( empty($dataExcel[0][$count_row][1]) ) {

									$cnt_fail++;
									$error_message = "Error, Blank First Name !";
								}
								

								if( empty($dataExcel[0][$count_row][2]) ) {

									$cnt_fail++;
									$error_message = "Error, Blank Company Name !";
								}

								if( empty($dataExcel[0][$count_row][3]) ) {

									$cnt_fail++;
									$error_message = "Error, Blank Department !";
								}


								if( empty($dataExcel[0][$count_row][4]) ) {

									$cnt_fail++;
									$error_message = "Error, Blank Sub Department !";
								}

								if( empty($dataExcel[0][$count_row][5]) ) {

									$cnt_fail++;
									$error_message = "Error, Blank Position !";

								}

					
								$companies 				= DB::table('companies')->where(['company_name' => $dataExcel[0][$count_row][2]])->first();

								$departments 			= DB::table('departments')->where(['department_name' => $dataExcel[0][$count_row][3]])->first();

								$sub_departments 		= DB::table('sub_department')->where(['sub_department_name' => $dataExcel[0][$count_row][4]])->first();
			

								if( empty($companies) ) {

									$cnt_fail++;
									$error_message = "Error, Invalid Company Name !";
								}

								if( empty($departments) ) {

									$cnt_fail++;
									$error_message = "Error, Invalid Department !";
								}

								if( empty($sub_departments) ) {

									$cnt_fail++;
									$error_message = "Error, Invalid Sub Department !";
								}

							}


						//dd($count_row);
						

						if($cnt_fail == 0){

							$count_row = 0;

							for($x=0; $x < count((array)$dataExcel[0]) - 1; $x++) {

									$count_row++;

								//if(!in_array($value->order_id,$order_id)){

									$companies 				= DB::table('companies')->where(['company_name' => $dataExcel[0][$count_row][2]])->first();

									$departments 			= DB::table('departments')->where(['department_name' => $dataExcel[0][$count_row][3]])->first();
	
									$sub_departments 		= DB::table('sub_department')->where(['sub_department_name' => $dataExcel[0][$count_row][4]])->first();

									//$positions 		= DB::table('positions')->where(['position_description' => $value->position])->first();

									$full_name   			= strtoupper($dataExcel[0][$count_row][0]).", ".strtoupper($dataExcel[0][$count_row][1]);

									$full_name_employee 	= strtoupper($dataExcel[0][$count_row][0]).", ".strtoupper($dataExcel[0][$count_row][1]).".EEE";

									$building = "DIGITS BLDG";
									$street = "#56 MAYOR IGNACIO SANTOS DIAZ";
									$baranggay = "SAN MARTIN DE PORRES";
									$city = "QUEZON";
									$state = "NCR";
									$zip_code = "1111";
									$country = "PHILIPPINES";

									$full_address = $building." ".$street." ".$baranggay." ".$city." ".$state." ".$zip_code." ".$country;

									$count_header = DB::table("employees")->count();
									$header_ref   =  str_pad($count_header + 1, 4, '0', STR_PAD_LEFT);			
									$reference_number	= "EMP-".$header_ref;


									DB::beginTransaction();

									try {

											DB::table('employees')->insert([
												'status_id'							=> "1",
												'employee_code'						=> $reference_number,
												'contact_person_ln'					=> strtoupper($dataExcel[0][$count_row][0]),
												'contact_person_fn'					=> strtoupper($dataExcel[0][$count_row][1]),
												'company_name'						=> $companies->id,
												'department_id'						=> $departments->id,
												'sub_department_id'					=> $sub_departments->id,
												'position_id'						=> $dataExcel[0][$count_row][5],
												'email_address'						=> $dataExcel[0][$count_row][6],
												'contact_person'					=> $full_name,
												'bill_to'							=> $full_name,
												'customer_location_name'			=> $full_name_employee,
												'building_no'						=> $building,
												'lot_blk_no_streetname'				=> $street,
												'barangay'							=> $baranggay,
												'city_id'							=> "1060",
												'state_id'							=> "15",
												'area_code_zip_code'				=> $zip_code,
												'country_id'						=> "10",
												'address_line1'						=> $full_address,
												'created_by' 						=> CRUDBooster::myId(),
												'created_at' 						=> date('Y-m-d H:i:s')
											]);

											DB::commit();
											//CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.alert_pullout_data_success",['mps_reference'=>$pullout_header->reference]), 'success');
									} catch (\Exception $e) {
										DB::rollback();
										CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.alert_database_error",['database_error'=>$e]), 'danger');
									}


									//array_push($order_id,$value->order_id);

								//}
								
							}

							CRUDBooster::redirect(CRUDBooster::mainpath(), trans("Employees uploaded successfully!"), 'success');

						}else{

							CRUDBooster::redirect(CRUDBooster::mainpath(), trans($error_message), 'danger');

						}


					
					}
					
				//}

			}
		}

	}