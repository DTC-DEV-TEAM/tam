<?php namespace App\Http\Controllers;
   use Illuminate\Support\Facades\Cache;
	use Session;
	use Request;
	use DB;
	use CRUDBooster;
	use Maatwebsite\Excel\Facades\Excel;
	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Reader\Exception;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
	use PhpOffice\PhpSpreadsheet\IOFactory;
	use Illuminate\Support\Facades\Log;
	use Illuminate\Support\Facades\Redirect;
	use App\Models\AssetsNonTradeInventoryHeader;
	use App\Models\AssetsNonTradeHeaderImages;
	use App\Models\AssetsNonTradeInventoryBody;
	use App\AssetsInventoryStatus;
	use App\GeneratedAssetsHistories;
	use App\MoveOrder;
	use App\HeaderRequest;
	use App\BodyRequest;
	use App\WarehouseLocationModel;
	use App\Models\AssetsNonTradeInventoryReserved;
	use App\Exports\ExportHeaderInventory;
	use Illuminate\Support\Facades\File;
	use Illuminate\Contracts\Cache\LockTimeoutException;
	use Carbon\Carbon;

	class AdminAssetsNonTradeInventoryHeaderController extends \crocodicstudio\crudbooster\controllers\CBController {
		// LOCATION
		private $admin_threef;
		private $admin_gf;
		private $it_warehouse;
		private $cavite;
		private $san_juan;
		private $p_tuazon;
		private $forItemCreation;
		private $forArfCreation;
		private $rejected;

		//APPROVALS
		private	$for_receiving;
		private	$reject;
		private	$recieved;
		private	$closed;
		private	$for_po;


		public function __construct() {
			DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping("enum", "string");
			// LOCATION
			$this->admin_threef =  1;    
			$this->admin_gf     =  2;
			$this->it_warehouse =  3;       
			$this->cavite       =  5;
			$this->san_juan     =  6;   
			$this->p_tuazon     =  7;       

			//APPROVALS
			$this->for_receiving = 20;
			$this->reject       = 21;
			$this->recieved     = 22;
			$this->closed       = 13;
			$this->for_po       = 47;
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
			$this->table = "assets_non_trade_inventory_header";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Reference No","name"=>"inv_reference_number"];
			$this->col[] = ["label"=>"Status","name"=>"header_approval_status","join"=>"statuses,status_description"];
			$this->col[] = ["label"=>"PO No","name"=>"po_no"];
			$this->col[] = ["label"=>"Location","name"=>"location","join"=>"warehouse_location_model,location"];
			$this->col[] = ["label"=>"Invoice Date","name"=>"invoice_date"];
			$this->col[] = ["label"=>"Invoice No","name"=>"invoice_no"];
			$this->col[] = ["label"=>"RR Date","name"=>"rr_date"];
			$this->col[] = ["label"=>"Created By","name"=>"created_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"Date Created","name"=>"created_at"];
			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'Po No','name'=>'po_no','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Invoice Date','name'=>'invoice_date','type'=>'date','validation'=>'required|date','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Invoice No','name'=>'invoice_no','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Rr Date','name'=>'rr_date','type'=>'date','validation'=>'required|date','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Location','name'=>'location','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			# END FORM DO NOT REMOVE THIS LINE


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
			if(in_array(CRUDBooster::myPrivilegeId(),[1,6])){
				$this->addaction[] = ['url'=>CRUDBooster::mainpath('detail-view-print/[id]'),'icon'=>'fa fa-eye','color'=>'default', "showIf"=>"[header_approval_status] == $this->recieved"];
				//$this->addaction[] = ['url'=>CRUDBooster::mainpath('detail/[id]'),'icon'=>'fa fa-pencil','color'=>'default', "showIf"=>"[header_approval_status] == $for_receiving && [location] == 2"];
				$this->addaction[] = ['url'=>CRUDBooster::mainpath('detail-for-receiving/[id]'),'icon'=>'fa fa-pencil','color'=>'default', "showIf"=>"[header_approval_status] == $this->for_receiving && [location] == 2"];
				$this->addaction[] = ['url'=>CRUDBooster::mainpath('detail-view-print/[id]'),'icon'=>'fa fa-eye','color'=>'default', "showIf"=>"[header_approval_status] == $this->for_receiving && [location] != 2"];
				// $this->addaction[] = ['url'=>CRUDBooster::mainpath('detail-view/[id]'),'icon'=>'fa fa-eye','color'=>'default', "showIf"=>"[header_approval_status] == $reject"];
				// $this->addaction[] = ['url'=>CRUDBooster::mainpath('detail-view/[id]'),'icon'=>'fa fa-eye','color'=>'default', "showIf"=>"[header_approval_status] == $for_receiving"];
				$this->addaction[] = ['url'=>CRUDBooster::mainpath('detail/[id]'),'icon'=>'fa fa-pencil','color'=>'default', "showIf"=>"[header_approval_status] == $this->for_po"];
			
			}
			else if(in_array(CRUDBooster::myPrivilegeId(),[1,5,9,20,21,22])){
				$this->addaction[] = ['url'=>CRUDBooster::mainpath('detail-view-print/[id]'),'icon'=>'fa fa-eye','color'=>'default', "showIf"=>"[header_approval_status] == $this->recieved"];
				$this->addaction[] = ['url'=>CRUDBooster::mainpath('detail-for-receiving/[id]'),'icon'=>'fa fa-pencil','color'=>'default', "showIf"=>"[header_approval_status] == $this->for_receiving"];
				$this->addaction[] = ['url'=>CRUDBooster::mainpath('detail-view/[id]'),'icon'=>'fa fa-eye','color'=>'default', "showIf"=>"[header_approval_status] == $this->reject"];
				$this->addaction[] = ['url'=>CRUDBooster::mainpath('detail/[id]'),'icon'=>'fa fa-eye','color'=>'default', "showIf"=>"[header_approval_status] == $this->for_po"];
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
				$this->index_button[] = ["label"=>"Export","icon"=>"fa fa-files-o","url"=>CRUDBooster::mainpath('export'),"color"=>"primary"];
				if(in_array(CRUDBooster::myPrivilegeId(),[1,6])){ 
				    $this->index_button[] = ["label"=>"Add Inventory","icon"=>"fa fa-files-o","url"=>CRUDBooster::mainpath('add-inventory'),"color"=>"success"];
				}
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

		/*****CUSTOM FUNCTION AREA */ 
        public function getAddInventory() {

			if(!CRUDBooster::isCreate() && $this->global_privilege == false) {
				CRUDBooster::redirect(CRUDBooster::adminPath(), trans('crudbooster.denied_access'));
			}

			$this->cbLoader();

			$data['page_title'] = 'Add Inventory';

			if(in_array(CRUDBooster::myPrivilegeId(),[5,17])){
				$data['reserved_assets'] = AssetsNonTradeInventoryReserved::leftjoin('header_request','assets_non_trade_inventory_reserved.reference_number','=','header_request.reference_number')->select('assets_non_trade_inventory_reserved.*','header_request.*','assets_non_trade_inventory_reserved.id as served_id')->whereNotNull('for_po')->where('header_request.request_type_id',1)->get();
				$data['warehouse_location'] = WarehouseLocationModel::where('id','=',3)->get();
			}else if(CRUDBooster::myPrivilegeId() == 9){
				$data['warehouse_location'] = WarehouseLocationModel::whereIn('id',[2])->get();
				$data['reserved_assets'] = AssetsNonTradeInventoryReserved::leftjoin('header_request','assets_non_trade_inventory_reserved.reference_number','=','header_request.reference_number')->select('assets_non_trade_inventory_reserved.*','header_request.*','assets_non_trade_inventory_reserved.id as served_id')->whereNotNull('for_po')->where('header_request.request_type_id',5)->get();
			}else if(CRUDBooster::myPrivilegeId() == 20){
				$data['warehouse_location'] = WarehouseLocationModel::whereIn('id',[5])->get();
				$data['reserved_assets'] = AssetsNonTradeInventoryReserved::leftjoin('header_request','assets_non_trade_inventory_reserved.reference_number','=','header_request.reference_number')->select('assets_non_trade_inventory_reserved.*','header_request.*','assets_non_trade_inventory_reserved.id as served_id')->whereNotNull('for_po')->where('header_request.request_type_id',5)->get();
			}else if(CRUDBooster::myPrivilegeId() == 21){
				$data['warehouse_location'] = WarehouseLocationModel::whereIn('id',[6])->get();
				$data['reserved_assets'] = AssetsNonTradeInventoryReserved::leftjoin('header_request','assets_non_trade_inventory_reserved.reference_number','=','header_request.reference_number')->select('assets_non_trade_inventory_reserved.*','header_request.*','assets_non_trade_inventory_reserved.id as served_id')->whereNotNull('for_po')->where('header_request.request_type_id',5)->get();
			}else if(CRUDBooster::myPrivilegeId() == 22){
				$data['warehouse_location'] = WarehouseLocationModel::whereIn('id',[7])->get();
				$data['reserved_assets'] = AssetsNonTradeInventoryReserved::leftjoin('header_request','assets_non_trade_inventory_reserved.reference_number','=','header_request.reference_number')->select('assets_non_trade_inventory_reserved.*','header_request.*','assets_non_trade_inventory_reserved.id as served_id')->whereNotNull('for_po')->where('header_request.request_type_id',5)->get();
			}else{
				$data['warehouse_location'] = WarehouseLocationModel::where('id','!=',4)->get();
				$data['reserved_assets'] = AssetsNonTradeInventoryReserved::leftjoin('header_request','assets_non_trade_inventory_reserved.reference_number','=','header_request.reference_number')->select('assets_non_trade_inventory_reserved.*','header_request.*','assets_non_trade_inventory_reserved.id as served_id')->whereNotNull('for_po')->get();
			}

			$data['header_images'] = AssetsNonTradeHeaderImages::select(
				'assets_non_trade_header_images.*'
			  )
			  ->where('assets_non_trade_header_images.header_id', $id)
			  ->get();
			$data['sub_categories'] = DB::table('class')->where('class_status', 'ACTIVE')->whereNull('limit_code')->orderby('class_description', 'asc')->get();
			$data['warehouse_location'] = WarehouseLocationModel::whereNotIn('id',[1,4])->get();
			return $this->view("non-trade.add-inventory", $data);

		}

		public function nonTradeApprovedProcess(Request $request){
			$this->cbLoader();
			if(!CRUDBooster::isUpdate() && $this->global_privilege==FALSE) {  
				if(!CRUDBooster::myPrivilegeId() == 6) {    
					CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
				}
			}
			$lock = Cache::lock('processing', 5);
			$fields = Request::all();
			
			$files = $fields['si_dr'];
			//$id = $fields['id'];
			$remarks = $fields['remarks'];

			//parse data in form
			parse_str($fields['form_data'], $fields);
	
			$po_no        = $fields['po_no'];
			$location     = $fields['location'];
			$invoice_date = $fields['invoice_date'];
			$invoice_no   = $fields['invoice_no'];
			$rr_date      = $fields['rr_date'];
			$body_id      = $fields['body_id'];
			$serial_no    = $fields['serial_no'];
			$asset_code   = $fields['asset_code'];
			$tag_id       = $fields['arf_tag'];
			$upc_code     = $fields['upc_code'];
			$brand        = $fields['brand'];
			$specs        = $fields['specs'];

			//Body details
			$allData    = [];
			$container  = [];
			$item_id           = $fields['item_id'];
			$digits_code       = $fields['digits_code'];
			$item_desc         = $fields['item_description'];
			$value             = $fields['value'];
			$serial_no         = $fields['serial_no'];
			$quantity          = $fields['add_quantity'];
			$item_category     = $fields['item_category'];
			$category_id       = $fields['category_id'];
			$sub_category_id   = $fields['sub_category_id'];
			$rr_date           = $fields['rr_date'];
			$location          = $fields['location'];
			$warranty_coverage = $fields['warranty_coverage'];
			$upc_code          = $fields['upc_code'];
			$brand             = $fields['brand'];
			$specs             = $fields['specs'];

			//make base default value		
			foreach($digits_code as $key => $val){
				$container['item_id']               = $item_id[$key];
				$container['serial_no']             = $serial_no[$key];
				$container['digits_code']           = $val;
				$container['location']              = $location;
				$container['item_description']      = $item_desc[$key];
				$container['quantity']              = $quantity[$key];
				$container['warranty_coverage']     = $warranty_coverage[$key];
				$container['item_category']         = $item_category[$key];
				$container['category_id']           = $category_id[$key];
				$container['sub_category_id']       = $sub_category_id[$key];
				$container['created_by']            = CRUDBooster::myId();
				$container['upc_code']              = $upc_code[$key];
				$container['brand']                 = $brand[$key];
				$container['specs']                 = $specs[$key];
				$container['transaction_per_asset'] = "Inventory";
				$container['item_condition']        = "Good";
				$allData[] = $container;
			
			}

			/* process to generate chronological sequential numbers asset code */
			
			//segregate COOKING AND EQUIPMENT to get category id
			$cooking_and_equipment_array = [];
			$cooking_and_equipment = DB::table('class')->find(1);
			foreach ($allData as $cKey => $cValue) {
				if ($cValue['sub_category_id'] == $cooking_and_equipment->id) {
					$cooking_and_equipment_array[] = $cValue;
					unset($allData[$cKey]);
				}
			}

			//put asset code per based on  item category COOKING AND EQUIPMENT
			$finalCEAssetsArr = [];
			$DatabaseCounterCE = DB::table('assets_inventory_body')->where('sub_category_id',$cooking_and_equipment->id)->count();
			foreach((array)$cooking_and_equipment_array as $finalfakey => $finalfavalue) {
				$finalfavalue['asset_code'] = $cooking_and_equipment->from_code + $DatabaseCounterCE;
				$DatabaseCounterCE++; // or any rule you want.	
				$finalCEAssetsArr[] = $finalfavalue;
			}
			//check if code is in limit
			foreach((array)$finalCEAssetsArr as $checkKey => $checkValue) {
				if($checkValue['asset_code'] > $cooking_and_equipment->to_code){
					DB::table('class')->where('id',$cooking_and_equipment->id)
						->update([
							'limit_code'   => "Code exceed in Asset Lists",
						]);	
					return json_encode(['status'=>'error', 'message' => 'Asset Code Exceed!!','redirect_url'=>CRUDBooster::mainpath()]);
				}
			}

			//segregate REFRIGERATION EQUIPMENT to get category id
			$refrigeration_equipment_array = [];
			$refrigeration_equipment = DB::table('class')->find(2);
			foreach ($allData as $reKey => $reValue) {
				if ($reValue['sub_category_id'] == $refrigeration_equipment->id) {
					$refrigeration_equipment_array[] = $reValue;
					unset($allData[$reKey]);
				}
			}

			//put asset code per based on  item category REFRIGERATION EQUIPMENT
			$finalREassetsArr = [];
			$DatabaseCounterRE = DB::table('assets_inventory_body')->where('sub_category_id',$refrigeration_equipment->id)->count();
			foreach((array)$refrigeration_equipment_array as $finalrekey => $finalrevalue) {
					$finalrevalue['asset_code'] = $refrigeration_equipment->from_code + $DatabaseCounterRE;
					$DatabaseCounterRE++; // or any rule you want.	
					$finalREassetsArr[] = $finalrevalue;
			}
			//check if code is in limit
			foreach((array)$finalREassetsArr as $checkKey => $checkValue) {
				if($checkValue['asset_code'] > $refrigeration_equipment->to_code){
					DB::table('class')->where('id',$refrigeration_equipment->id)
						->update([
							'limit_code'   => "Code exceed in Asset Lists",
						]);	
					return json_encode(['status'=>'error', 'message' => 'Asset Code Exceed in Asset Lists!!'. $refrigeration_equipment->class_description .'','redirect_url'=>CRUDBooster::mainpath()]);
				}
			}

			//segregate COMMERCIAL OVENS to get category id
			$commercial_ovens_array = [];
			$commercial_ovens = DB::table('class')->find(3);
			foreach ($allData as $coKey => $coValue) {
				if ($coValue['sub_category_id'] == $commercial_ovens->id) {
					$commercial_ovens_array[] = $coValue;
					unset($allData[$coKey]);
				}
			}

			//put asset code per based on  item category COMMERCIAL OVENS
			$finalCOassetsArr = [];
			$DatabaseCounterCO = DB::table('assets_inventory_body')->where('sub_category_id',$commercial_ovens->id)->count();
			foreach((array)$commercial_ovens_array as $finalcokey => $finalcovalue) {
					$finalcovalue['asset_code'] = $commercial_ovens->from_code + $DatabaseCounterCO;
					$DatabaseCounterCO++; // or any rule you want.	
					$finalCOassetsArr[] = $finalcovalue;
			}
			//check if code is in limit
			foreach((array)$finalCOassetsArr as $checkKey => $checkValue) {
				if($checkValue['asset_code'] > $commercial_ovens->to_code){
					DB::table('class')->where('id',$commercial_ovens->id)
						->update([
							'limit_code'   => "Code exceed in Asset Lists",
						]);	
					return json_encode(['status'=>'error', 'message' => 'Asset Code Exceed in Asset Lists!!'. $commercial_ovens->class_description .'','redirect_url'=>CRUDBooster::mainpath()]);
				}
			}

			//segregate REFRIGERATION AND FREEZER to get category id
			$refrigeration_and_freezer_array = [];
			$refrigeration_and_freezer = DB::table('class')->find(4);
			foreach ($allData as $key => $value) {
				if ($value['sub_category_id'] == $refrigeration_and_freezer->id) {
					$refrigeration_and_freezer_array[] = $value;
					unset($allData[$key]);
				}
			}

			//put asset code per based on  item category REFRIGERATION AND FREEZER
			$finalRAFAssetsArr = [];
			$DatabaseCounterRAF = DB::table('assets_inventory_body')->where('sub_category_id',$refrigeration_and_freezer->id)->count();
			foreach((array)$refrigeration_and_freezer_array as $finalfakey => $finalfavalue) {
					$finalfavalue['asset_code'] = $refrigeration_and_freezer->from_code + $DatabaseCounterRAF;
					$DatabaseCounterRAF++; // or any rule you want.	
					$finalRAFAssetsArr[] = $finalfavalue;
			}
			//check if code is in limit
			foreach((array)$finalRAFAssetsArr as $checkKey => $checkValue) {
				if($checkValue['asset_code'] > $refrigeration_and_freezer->to_code){
					DB::table('class')->where('id',$refrigeration_and_freezer->id)
						->update([
							'limit_code'   => "Code exceed in Asset Lists",
						]);	
					return json_encode(['status'=>'error', 'message' => 'Asset Code Exceed in Asset Lists!!'. $refrigeration_and_freezer->class_description .'','redirect_url'=>CRUDBooster::mainpath()]);
				}
			}

			//segregate COMMERCIAL SINKS to get category id
			$commercial_sinks_array = [];
			$commercial_sinks = DB::table('class')->find(5);
			foreach ($allData as $cskey => $csvalue) {
				if ($csvalue['sub_category_id'] == $commercial_sinks->id) {
					$commercial_sinks_array[] = $csvalue;
					unset($allData[$cskey]);
				}
			}

			//put asset code per based on  item category COMMERCIAL SINKS
			$finalCSAssetsArr = [];
			$DatabaseCounterCS = DB::table('assets_inventory_body')->where('sub_category_id',$commercial_sinks->id)->count();
			foreach((array)$commercial_sinks_array as $finalcskey => $finalcsvalue) {
					$finalcsvalue['asset_code'] = $commercial_sinks->from_code + $DatabaseCounterCS;
					$DatabaseCounterCS++; // or any rule you want.	
					$finalCSAssetsArr[] = $finalcsvalue;
			}
			//check if code is in limit
			foreach((array)$finalCSAssetsArr as $checkKey => $checkValue) {
				if($checkValue['asset_code'] > $commercial_sinks->to_code){
					DB::table('class')->where('id',$commercial_sinks->id)
						->update([
							'limit_code'   => "Code exceed in Asset Lists",
						]);	
					return json_encode(['status'=>'error', 'message' => 'Asset Code Exceed in Asset Lists!!'. $commercial_sinks->class_description .'','redirect_url'=>CRUDBooster::mainpath()]);
				}
			}

			//segregate WORK TABLES AND STATIONS to get category id
			$work_table_stations_array = [];
			$work_table_stations = DB::table('class')->find(6);
			foreach ($allData as $wtskey => $wtsvalue) {
				if ($wtsvalue['sub_category_id'] == $work_table_stations->id) {
					$work_table_stations_array[] = $wtsvalue;
					unset($allData[$wtskey]);
				}
			}

			//put asset code per based on  item category WORK TABLES AND STATIONS
			$finalWTSAssetsArr = [];
			$DatabaseCounterWTS = DB::table('assets_inventory_body')->where('sub_category_id',$work_table_stations->id)->count();
			foreach((array)$work_table_stations_array as $finalwtskey => $finalwtsvalue) {
					$finalwtsvalue['asset_code'] = $work_table_stations->from_code + $DatabaseCounterWTS;
					$DatabaseCounterWTS++; // or any rule you want.	
					$finalWTSAssetsArr[] = $finalwtsvalue;
			}
			//check if code is in limit
			foreach((array)$finalWTSAssetsArr as $checkKey => $checkValue) {
				if($checkValue['asset_code'] > $work_table_stations->to_code){
					DB::table('class')->where('id',$work_table_stations->id)
						->update([
							'limit_code'   => "Code exceed in Asset Lists",
						]);	
					return json_encode(['status'=>'error', 'message' => 'Asset Code Exceed in Asset Lists!!'. $work_table_stations->class_description .'','redirect_url'=>CRUDBooster::mainpath()]);
				}
			}

			//segregate FOOD PREPARATION EQUIPMENT to get category id
			$food_preparation_equipment_array = [];
			$food_preparation_equipmen = DB::table('class')->find(7);
			foreach ($allData as $fpekey => $fpevalue) {
				if ($fpevalue['sub_category_id'] == $food_preparation_equipmen->id) {
					$food_preparation_equipment_array[] = $fpevalue;
					unset($allData[$fpekey]);
				}
			}

			//put asset code per based on  item category FOOD PREPARATION EQUIPMENT
			$finalFPEAssetsArr = [];
			$DatabaseCounterFPE = DB::table('assets_inventory_body')->where('sub_category_id',$food_preparation_equipmen->id)->count();
			foreach((array)$food_preparation_equipment_array as $finalfpekey => $finalfpevalue) {
					$finalfpevalue['asset_code'] = $food_preparation_equipmen->from_code + $DatabaseCounterFPE;
					$DatabaseCounterFPE++; // or any rule you want.	
					$finalFPEAssetsArr[] = $finalfpevalue;
			}
			//check if code is in limit
			foreach((array)$finalFPEAssetsArr as $checkKey => $checkValue) {
				if($checkValue['asset_code'] > $food_preparation_equipmen->to_code){
					DB::table('class')->where('id',$food_preparation_equipmen->id)
						->update([
							'limit_code'   => "Code exceed in Asset Lists",
						]);	
					return json_encode(['status'=>'error', 'message' => 'Asset Code Exceed in Asset Lists!!'. $food_preparation_equipmen->class_description .'','redirect_url'=>CRUDBooster::mainpath()]);
				}
			}

			//segregate FAUCETS AND PLUMBING to get category id
			$faucet_and_plumbing_array = [];
			$faucet_and_plumbing = DB::table('class')->find(8);
			foreach ($allData as $fapkey => $fapvalue) {
				if ($fapvalue['sub_category_id'] == $faucet_and_plumbing->id) {
					$faucet_and_plumbing_array[] = $fapvalue;
					unset($allData[$fapkey]);
				}
			}

			//put asset code per based on  item category FAUCETS AND PLUMBING
			$finalFAPAssetsArr = [];
			$DatabaseCounterFAP = DB::table('assets_inventory_body')->where('sub_category_id',$faucet_and_plumbing->id)->count();
			foreach((array)$faucet_and_plumbing_array as $finalfapkey => $finalfapvalue) {
					$finalfapvalue['asset_code'] = $faucet_and_plumbing->from_code + $DatabaseCounterFAP;
					$DatabaseCounterFAP++; // or any rule you want.	
					$finalFAPAssetsArr[] = $finalfapvalue;
			}
			//check if code is in limit
			foreach((array)$finalFAPAssetsArr as $checkKey => $checkValue) {
				if($checkValue['asset_code'] > $faucet_and_plumbing->to_code){
					DB::table('class')->where('id',$faucet_and_plumbing->id)
						->update([
							'limit_code'   => "Code exceed in Asset Lists",
						]);	
					return json_encode(['status'=>'error', 'message' => 'Asset Code Exceed in Asset Lists!!'. $faucet_and_plumbing->class_description .'','redirect_url'=>CRUDBooster::mainpath()]);
				}
			}

			//segregate FOOD HOLDING & WARMING EQUIP to get category id
			$food_holding_warming_equip_array = [];
			$food_holding_warming_equip = DB::table('class')->find(9);
			foreach ($allData as $fhwekey => $fhwevalue) {
				if ($fhwevalue['sub_category_id'] == $food_holding_warming_equip->id) {
					$food_holding_warming_equip_array[] = $fhwevalue;
					unset($allData[$fhwekey]);
				}
			}

			//put asset code per based on  item category FOOD HOLDING & WARMING EQUIP
			$finalFHWEAssetsArr = [];
			$DatabaseCounterFHWE = DB::table('assets_inventory_body')->where('sub_category_id',$food_holding_warming_equip->id)->count();
			foreach((array)$food_holding_warming_equip_array as $finalfhwekey => $finalfhwevalue) {
					$finalfhwevalue['asset_code'] = $food_holding_warming_equip->from_code + $DatabaseCounterFHWE;
					$DatabaseCounterFHWE++; // or any rule you want.	
					$finalFHWEAssetsArr[] = $finalfhwevalue;
			}
			//check if code is in limit
			foreach((array)$finalFHWEAssetsArr as $checkKey => $checkValue) {
				if($checkValue['asset_code'] > $food_holding_warming_equip->to_code){
					DB::table('class')->where('id',$food_holding_warming_equip->id)
						->update([
							'limit_code'   => "Code exceed in Asset Lists",
						]);	
					return json_encode(['status'=>'error', 'message' => 'Asset Code Exceed in Asset Lists!!'. $food_holding_warming_equip->class_description .'','redirect_url'=>CRUDBooster::mainpath()]);
				}
			}

			//segregate OTHER - RESTAURANT EQUIPMENT to get category id
			$other_restaurant_equipment_array = [];
			$other_restaurant_equipment = DB::table('class')->find(10);
			foreach ($allData as $orekey => $orevalue) {
				if ($orevalue['sub_category_id'] == $other_restaurant_equipment->id) {
					$other_restaurant_equipment_array[] = $orevalue;
					unset($allData[$orekey]);
				}
			}

			//put asset code per based on  item category OTHER - RESTAURANT EQUIPMENT
			$finalOREAssetsArr = [];
			$DatabaseCounterORE = DB::table('assets_inventory_body')->where('sub_category_id',$other_restaurant_equipment->id)->count();
			foreach((array)$other_restaurant_equipment_array as $finalorekey => $finalorevalue) {
					$finalorevalue['asset_code'] = $other_restaurant_equipment->from_code + $DatabaseCounterORE;
					$DatabaseCounterORE++; // or any rule you want.	
					$finalOREAssetsArr[] = $finalorevalue;
			}
			//check if code is in limit
			foreach((array)$finalOREAssetsArr as $checkKey => $checkValue) {
				if($checkValue['asset_code'] > $other_restaurant_equipment->to_code){
					DB::table('class')->where('id',$other_restaurant_equipment->id)
						->update([
							'limit_code'   => "Code exceed in Asset Lists",
						]);	
					return json_encode(['status'=>'error', 'message' => 'Asset Code Exceed in Asset Lists!!'. $other_restaurant_equipment->class_description .'','redirect_url'=>CRUDBooster::mainpath()]);
				}
			}

			//segregate OTHER VEHICLE to get category id
			$other_vehicle_array = [];
			$other_vehicle = DB::table('class')->find(11);
			foreach ($allData as $ovkey => $ovvalue) {
				if ($ovvalue['sub_category_id'] == $other_vehicle->id) {
					$other_vehicle_array[] = $ovvalue;
					unset($allData[$ovkey]);
				}
			}

			//put asset code per based on  item category OTHER VEHICLE
			$finalOVAssetsArr = [];
			$DatabaseCounterOV = DB::table('assets_inventory_body')->where('sub_category_id',$other_vehicle->id)->count();
			foreach((array)$other_vehicle_array as $finalovkey => $finalovvalue) {
					$finalovvalue['asset_code'] = $other_vehicle->from_code + $DatabaseCounterOV;
					$DatabaseCounterOV++; // or any rule you want.	
					$finalOVAssetsArr[] = $finalovvalue;
			}
			//check if code is in limit
			foreach((array)$finalOVAssetsArr as $checkKey => $checkValue) {
				if($checkValue['asset_code'] > $other_vehicle->to_code){
					DB::table('class')->where('id',$other_vehicle->id)
						->update([
							'limit_code'   => "Code exceed in Asset Lists",
						]);	
					return json_encode(['status'=>'error', 'message' => 'Asset Code Exceed in Asset Lists!!'. $other_vehicle->class_description .'','redirect_url'=>CRUDBooster::mainpath()]);
				}
			}

			//segregate OTHER FIXED ASSET to get category id
			$other_fixed_asset_array = [];
			$other_fixed_asset = DB::table('class')->find(12);
			foreach ($allData as $ofakey => $ofavalue) {
				if ($ofavalue['sub_category_id'] == $other_fixed_asset->id) {
					$other_fixed_asset_array[] = $ofavalue;
					unset($allData[$ofakey]);
				}
			}

			//put asset code per based on  item category OTHER FIXED ASSET
			$finalOFAAssetsArr = [];
			$DatabaseCounterOFA = DB::table('assets_inventory_body')->where('sub_category_id',$other_fixed_asset->id)->count();
			foreach((array)$other_fixed_asset_array as $finalofakey => $finalofavalue) {
					$finalofavalue['asset_code'] = $other_fixed_asset->from_code + $DatabaseCounterOFA;
					$DatabaseCounterOFA++; // or any rule you want.	
					$finalOFAAssetsArr[] = $finalofavalue;
			}
			//check if code is in limit
			foreach((array)$finalOFAAssetsArr as $checkKey => $checkValue) {
				if($checkValue['asset_code'] > $other_fixed_asset->to_code){
					DB::table('class')->where('id',$other_fixed_asset->id)
						->update([
							'limit_code'   => "Code exceed in Asset Lists",
						]);	
					return json_encode(['status'=>'error', 'message' => 'Asset Code Exceed in Asset Lists!!'. $other_fixed_asset->class_description .'','redirect_url'=>CRUDBooster::mainpath()]);
				}
			}

			//segregate COMMUNICATION EQUIPMENT to get category id
			$communication_equipment_array = [];
			$communication_equipment = DB::table('class')->find(13);
			foreach ($allData as $commekey => $commevalue) {
				if ($commevalue['sub_category_id'] == $communication_equipment->id) {
					$communication_equipment_array[] = $commevalue;
					unset($allData[$commekey]);
				}
			}

			//put asset code per based on  item category COMMUNICATION EQUIPMENT
			$finalOCOMMEAssetsArr = [];
			$DatabaseCounterCOMME = DB::table('assets_inventory_body')->where('sub_category_id',$communication_equipment->id)->count();
			foreach((array)$communication_equipment_array as $finalcommekey => $finalcommevalue) {
					$finalcommevalue['asset_code'] = $communication_equipment->from_code + $DatabaseCounterCOMME;
					$DatabaseCounterCOMME++; // or any rule you want.	
					$finalOCOMMEAssetsArr[] = $finalcommevalue;
			}
			//check if code is in limit
			foreach((array)$finalOCOMMEAssetsArr as $checkKey => $checkValue) {
				if($checkValue['asset_code'] > $communication_equipment->to_code){
					DB::table('class')->where('id',$communication_equipment->id)
						->update([
							'limit_code'   => "Code exceed in Asset Lists",
						]);	
					return json_encode(['status'=>'error', 'message' => 'Asset Code Exceed in Asset Lists!!'. $communication_equipment->class_description .'','redirect_url'=>CRUDBooster::mainpath()]);
				}
			}

			//segregate FURNITURES & FIXTURES to get category id
			$furnitures_fixtures_array = [];
			$furnitures_fixtures = DB::table('class')->find(14);
			foreach ($allData as $ffkey => $ffvalue) {
				if ($ffvalue['sub_category_id'] == $furnitures_fixtures->id) {
					$furnitures_fixtures_array[] = $ffvalue;
					unset($allData[$ffkey]);
				}
			}

			//put asset code per based on  item category FURNITURES & FIXTURES
			$finalFFAssetsArr = [];
			$DatabaseCounterFF = DB::table('assets_inventory_body')->where('sub_category_id',$furnitures_fixtures->id)->count();
			foreach((array)$furnitures_fixtures_array as $finalffkey => $finalffvalue) {
					$finalffvalue['asset_code'] = $furnitures_fixtures->from_code + $DatabaseCounterFF;
					$DatabaseCounterFF++; // or any rule you want.	
					$finalFFAssetsArr[] = $finalffvalue;
			}
			//check if code is in limit
			foreach((array)$finalFFAssetsArr as $checkKey => $checkValue) {
				if($checkValue['asset_code'] > $furnitures_fixtures->to_code){
					DB::table('class')->where('id',$furnitures_fixtures->id)
						->update([
							'limit_code'   => "Code exceed in Asset Lists",
						]);	
					return json_encode(['status'=>'error', 'message' => 'Asset Code Exceed in Asset Lists!!'. $furnitures_fixtures->class_description .'','redirect_url'=>CRUDBooster::mainpath()]);
				}
			}

			//segregate FACILITIES EQUIPMENT to get category id
			$facilities_equipment_array = [];
			$facilities_equipment = DB::table('class')->find(15);
			foreach ($allData as $fekey => $fevalue) {
				if ($fevalue['sub_category_id'] == $facilities_equipment->id) {
					$facilities_equipment_array[] = $fevalue;
					unset($allData[$fekey]);
				}
			}

			//put asset code per based on  item category FACILITIES EQUIPMENT
			$finalFEssetsArr = [];
			$DatabaseCounterFE = DB::table('assets_inventory_body')->where('sub_category_id',$facilities_equipment->id)->count();
			foreach((array)$facilities_equipment_array as $finalffkey => $finalfevalue) {
					$finalfevalue['asset_code'] = $facilities_equipment->from_code + $DatabaseCounterFE;
					$DatabaseCounterFE++; // or any rule you want.	
					$finalFEssetsArr[] = $finalfevalue;
			}
			//check if code is in limit
			foreach((array)$finalFEssetsArr as $checkKey => $checkValue) {
				if($checkValue['asset_code'] > $facilities_equipment->to_code){
					DB::table('class')->where('id',$facilities_equipment->id)
						->update([
							'limit_code'   => "Code exceed in Asset Lists",
						]);	
					return json_encode(['status'=>'error', 'message' => 'Asset Code Exceed in Asset Lists!!'. $facilities_equipment->class_description .'','redirect_url'=>CRUDBooster::mainpath()]);
				}
			}

			//segregate LEASEHOLD IMPROVEMENT to get category id
			$leasehold_equipment_array = [];
			$leasehold_equipment = DB::table('class')->find(16);
			foreach ($allData as $lekey => $levalue) {
				if ($levalue['sub_category_id'] == $leasehold_equipment->id) {
					$leasehold_equipment_array[] = $levalue;
					unset($allData[$lekey]);
				}
			}

			//put asset code per based on  item category LEASEHOLD IMPROVEMENT
			$finalLEssetsArr = [];
			$DatabaseCounterLE = DB::table('assets_inventory_body')->where('sub_category_id',$leasehold_equipment->id)->count();
			foreach((array)$leasehold_equipment_array as $finalffkey => $finallevalue) {
					$finallevalue['asset_code'] = $leasehold_equipment->from_code + $DatabaseCounterLE;
					$DatabaseCounterLE++; // or any rule you want.	
					$finalLEssetsArr[] = $finallevalue;
			}
			//check if code is in limit
			foreach((array)$finalLEssetsArr as $checkKey => $checkValue) {
				if($checkValue['asset_code'] > $leasehold_equipment->to_code){
					DB::table('class')->where('id',$leasehold_equipment->id)
						->update([
							'limit_code'   => "Code exceed in Asset Lists",
						]);	
					return json_encode(['status'=>'error', 'message' => 'Asset Code Exceed in Asset Lists!!'. $leasehold_equipment->class_description .'','redirect_url'=>CRUDBooster::mainpath()]);
				}
			}

			//segregate MACHINERY & EQUIPMENT to get category id
			$machinery_equipment_array = [];
			$machinery_equipmen = DB::table('class')->find(17);
			foreach ($allData as $mekey => $mevalue) {
				if ($mevalue['sub_category_id'] == $machinery_equipmen->id) {
					$machinery_equipment_array[] = $mevalue;
					unset($allData[$mekey]);
				}
			}

			//put asset code per based on  item category MACHINERY & EQUIPMENT
			$finalMEssetsArr = [];
			$DatabaseCounterME = DB::table('assets_inventory_body')->where('sub_category_id',$machinery_equipmen->id)->count();
			foreach((array)$machinery_equipment_array as $finalffkey => $finalmevalue) {
					$finalmevalue['asset_code'] = $machinery_equipmen->from_code + $DatabaseCounterME;
					$DatabaseCounterME++; // or any rule you want.	
					$finalMEssetsArr[] = $finalmevalue;
			}
			//check if code is in limit
			foreach((array)$finalMEssetsArr as $checkKey => $checkValue) {
				if($checkValue['asset_code'] > $machinery_equipmen->to_code){
					DB::table('class')->where('id',$machinery_equipmen->id)
						->update([
							'limit_code'   => "Code exceed in Asset Lists",
						]);	
					return json_encode(['status'=>'error', 'message' => 'Asset Code Exceed in Asset Lists!!'. $machinery_equipmen->class_description .'','redirect_url'=>CRUDBooster::mainpath()]);
				}
			}

			//segregate VEHICLE to get category id
			$vehicle_array = [];
			$vehicle = DB::table('class')->find(18);
			foreach ($allData as $vkey => $vvalue) {
				if ($vvalue['sub_category_id'] == $vehicle->id) {
					$vehicle_array[] = $vvalue;
					unset($allData[$vkey]);
				}
			}

			//put asset code per based on  item category VEHICLE
			$finalVssetsArr = [];
			$DatabaseCounterV = DB::table('assets_inventory_body')->where('sub_category_id',$vehicle->id)->count();
			foreach((array)$vehicle_array as $finalffkey => $finalvvalue) {
					$finalvvalue['asset_code'] = $vehicle->from_code + $DatabaseCounterV;
					$DatabaseCounterV++; // or any rule you want.	
					$finalVssetsArr[] = $finalvvalue;
			}
			//check if code is in limit
			foreach((array)$finalVssetsArr as $checkKey => $checkValue) {
				if($checkValue['asset_code'] > $vehicle->to_code){
					DB::table('class')->where('id',$vehicle->id)
						->update([
							'limit_code'   => "Code exceed in Asset Lists",
						]);	
					return json_encode(['status'=>'error', 'message' => 'Asset Code Exceed in Asset Lists!!'. $vehicle->class_description .'','redirect_url'=>CRUDBooster::mainpath()]);
				}
			}

			//segregate COMPUTER SOFTWARE/PROGRAM to get category id
			$computer_software_program_array = [];
			$computer_software_program = DB::table('class')->find(19);
			foreach ($allData as $fkey => $fvalue) {
				if ($fvalue['sub_category_id'] == $computer_software_program->id) {
					$computer_software_program_array[] = $fvalue;
					unset($allData[$fkey]);
				}
			}

			//put asset code per based on  item category COMPUTER SOFTWARE/PROGRAM
			$finalCSPAssetsArr = [];
			$DatabaseCounterCSP = DB::table('assets_inventory_body')->where('sub_category_id',$computer_software_program->id)->count();
			foreach((array)$computer_software_program_array as $finalItkey => $finalItvalue) {
					$finalItvalue['asset_code'] = $computer_software_program->from_code + $DatabaseCounterCSP;
					$DatabaseCounterCSP++; // 
					$finalCSPAssetsArr[] = $finalItvalue;	
			}
			//check if code is in limit
			foreach((array)$finalCSPAssetsArr as $checkKey => $checkValue) {
				if($checkValue['asset_code'] > $computer_software_program->to_code){
					DB::table('class')->where('id',$computer_software_program->id)
						->update([
							'limit_code'   => "Code exceed in Asset Lists",
						]);	
					return json_encode(['status'=>'error', 'message' => 'Asset Code Exceed in Asset Lists!!'. $computer_software_program->class_description .'','redirect_url'=>CRUDBooster::mainpath()]);
				}
			}

			/*Next Code if thers new Asset Code in Sub Masterfile
            //segregate NEXT ASSET CODE DESCRIPTION to get category id
			$next_asset_code_array = [];
			$next_asset_code = DB::table('class')->find(22);
			foreach ($allData as $fkey => $fvalue) {
				if ($fvalue['sub_category_id'] == $next_asset_code->id) {
					$next_asset_code_array[] = $fvalue;
					unset($allData[$fkey]);
				}
			}

			//put asset code per based on  item category NEXT ASSET CODE DESCRIPTION
			$finalCSPAssetsArr = [];
			$DatabaseCounterCSP = DB::table('assets_inventory_body')->where('sub_category_id',$next_asset_code->id)->count();
			foreach((array)$next_asset_code_array as $finalItkey => $finalItvalue) {
					$finalItvalue['asset_code'] = $next_asset_code->from_code + $DatabaseCounterCSP;
					$DatabaseCounterCSP++; // 
					$finalCSPAssetsArr[] = $finalItvalue;	
			}
			//check if code is in limit
			foreach((array)$finalCSPAssetsArr as $checkKey => $checkValue) {
				if($checkValue['asset_code'] > $next_asset_code->to_code){
					DB::table('class')->where('id',$next_asset_code->id)
						->update([
							'limit_code'   => "Code exceed in Asset Lists",
						]);	
					return json_encode(['status'=>'error', 'message' => 'Asset Code Exceed in Asset Lists!!'. $next_asset_code->class_description .'','redirect_url'=>CRUDBooster::mainpath()]);
				}
			}

			EndNext Code if thers new Asset Code in Sub Masterfile*/
			
			$finalDataofSplittingArray = array_merge($finalCEAssetsArr,$finalREassetsArr,$finalCOassetsArr,$finalRAFAssetsArr,$finalCSAssetsArr,$finalWTSAssetsArr,$finalFPEAssetsArr,$finalFAPAssetsArr,$finalFHWEAssetsArr,$finalOREAssetsArr,$finalOVAssetsArr,$finalOFAAssetsArr,$finalOCOMMEAssetsArr,$finalFFAssetsArr,$finalFEssetsArr,$finalLEssetsArr,$finalMEssetsArr,$finalVssetsArr,$finalCSPAssetsArr);
             
			if(empty($finalDataofSplittingArray)){
				return json_encode(['status'=>'error', 'message' => 'Something went wrong. Please contact your administrator!!','redirect_url'=>CRUDBooster::mainpath()]);
			}else{
				//CREATE HEADER INVENTORY
				$count_header = DB::table('assets_non_trade_inventory_header')->count();
				$header_ref   = str_pad($count_header + 1, 7, '0', STR_PAD_LEFT);			
				$inv_ref_no	  = "NT#-".$header_ref;

				$getLastId = AssetsNonTradeInventoryHeader::Create(
					[
						'inv_reference_number'   => $inv_ref_no,
						'location'               => $location, 
						'header_approval_status' => 47, 
						'created_by'             => CRUDBooster::myId(), 
						'created_at'             => date('Y-m-d H:i:s')
					]
				);     

				$id = $getLastId->id;

				//CREATE ASSET LISTS		
				$saveData = [];
				$saveContainerData = [];
				foreach($finalDataofSplittingArray as $akey => $aVal){
					$saveContainerData['header_id']             = $id;
					$saveContainerData['item_id']               = $aVal['item_id'];
					$saveContainerData['statuses_id']           = 47;
					$saveContainerData['location']              = $aVal['location'];
					$saveContainerData['digits_code']           = $aVal['digits_code'];
					$saveContainerData['item_description']      = $aVal['item_description'];
					$saveContainerData['quantity']              = $aVal['quantity'];	
					$saveContainerData['serial_no']             = $aVal['serial_no'];
					$saveContainerData['warranty_coverage']     = $aVal['warranty_coverage'];
					$saveContainerData['asset_code']            = "NT-".$aVal['asset_code'];
					$saveContainerData['barcode']               = $aVal['digits_code'].''.$aVal['asset_code'];
					$saveContainerData['item_condition']        = $aVal['item_condition'];
					$saveContainerData['item_category']         = $aVal['item_category'];
					$saveContainerData['sub_category_id']       = $aVal['sub_category_id'];
					$saveContainerData['transaction_per_asset'] = $aVal['transaction_per_asset'];
					$saveContainerData['upc_code']              = $aVal['upc_code'];
					$saveContainerData['brand']                 = $aVal['brand'];
					$saveContainerData['specs']                 = $aVal['specs'];
					$saveContainerData['created_by']            = $aVal['created_by'];
					$saveContainerData['created_at']            = Carbon::parse($aVal['created_at'])->toDateTimeString();

					$saveData[] = $saveContainerData;				   
				}

				//dd($saveData);

				AssetsNonTradeInventoryBody::insert($saveData);
				$message = ['status'=>'success', 'message' => 'Success!','redirect_url'=>CRUDBooster::mainpath('detail/'.$id)];
				echo json_encode($message);
			}
		}

		//Get Invetory Approval List
		public function getDetail($id){
			$this->cbLoader();
			if(!CRUDBooster::isRead() && $this->global_privilege==FALSE) {    
				CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
			}

			$data = [];
			$data['page_title'] = 'View Asset Inventory for PO Details';
			//header details
			$data['Header'] = AssetsNonTradeInventoryHeader::leftjoin('assets_non_trade_header_images', 'assets_non_trade_inventory_header.id', '=', 'assets_non_trade_header_images.header_id')
				->leftjoin('cms_users', 'assets_non_trade_inventory_header.created_by', '=', 'cms_users.id')
				->leftjoin('cms_users as approver', 'assets_non_trade_inventory_header.updated_by', '=', 'approver.id')
				->leftjoin('warehouse_location_model', 'assets_non_trade_inventory_header.location', '=', 'warehouse_location_model.id')
				->select(
					'assets_non_trade_inventory_header.*',
					'assets_non_trade_inventory_header.id as header_id',
					'cms_users.*',
					'warehouse_location_model.location as warehouse_location',
					'approver.name as approver',
					'assets_non_trade_inventory_header.created_at as date_created'
					)
				->where('assets_non_trade_inventory_header.id', $id)
				->first();

			//Body details
			$data['Body'] = AssetsNonTradeInventoryBody::leftjoin('statuses', 'assets_non_trade_inventory_body.statuses_id','=','statuses.id')
				->leftjoin('assets_non_trade_inventory_header', 'assets_non_trade_inventory_body.header_id', '=', 'assets_non_trade_inventory_header.id')
				->leftjoin('assets', 'assets_non_trade_inventory_body.item_id', '=', 'assets.id')
				->leftjoin('cms_users as cms_users_updated_by', 'assets_non_trade_inventory_body.updated_by', '=', 'cms_users_updated_by.id')
				->leftjoin('warehouse_location_model', 'assets_non_trade_inventory_body.location', '=', 'warehouse_location_model.id')
				->select(
				'assets_non_trade_inventory_body.*',
				'assets_non_trade_inventory_body.id as for_approval_body_id',
				'statuses.*',
				'warehouse_location_model.location as warehouse_location',
				'assets_non_trade_inventory_header.location as location',
				'assets_non_trade_inventory_body.location as body_location',
				'assets_non_trade_inventory_body.updated_at as date_updated',
				'cms_users_updated_by.name as updated_by'
				)
				->where('assets_non_trade_inventory_body.header_id', $id)
				->get();

				return $this->view("non-trade.edit-inventory-list-for-po", $data);
		}

		public function nonTradeForPoProcess(Request $request){
			$this->cbLoader();
			if(!CRUDBooster::isUpdate() && $this->global_privilege==FALSE) {  
				if(!CRUDBooster::myPrivilegeId() == 6) {    
					CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
				}
			}

			$fields = Request::all();
			$id           = $fields['id'];
			//parse data in form
			parse_str($fields['form_data'], $fields);
			$po_no        = $fields['po_no'];

			AssetsNonTradeInventoryHeader::where('id', $id)
			->update([
				'header_approval_status' => 20, 
				'po_no'                  => $po_no
			]);

			AssetsNonTradeInventoryBody::where(['header_id' => $id])
			->update([
					'statuses_id'  => 20
					]);

			$message = ['status'=>'success', 'message' => 'Success!','redirect_url'=>CRUDBooster::mainpath()];
			echo json_encode($message);
		}

	}