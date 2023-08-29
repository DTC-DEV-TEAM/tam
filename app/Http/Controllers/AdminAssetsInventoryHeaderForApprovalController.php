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
	use App\AssetsInventoryHeaderForApproval;
	use App\AssetsInventoryHeader;
	use App\AssetsInventoryBodyForApproval;
	use App\AssetsHeaderImages;
	use App\AssetsInventoryBody;
	use App\AssetsInventoryStatus;
	use App\AssetsMovementHistory;
	use App\GeneratedAssetsHistories;
	use App\MoveOrder;
	use App\HeaderRequest;
	use App\BodyRequest;
	use App\WarehouseLocationModel;
	use App\Models\AssetsInventoryReserved;
	use App\Exports\ExportHeaderInventory;
	use Illuminate\Support\Facades\File;
	use Illuminate\Contracts\Cache\LockTimeoutException;
	use Carbon\Carbon;
	class AdminAssetsInventoryHeaderForApprovalController extends \crocodicstudio\crudbooster\controllers\CBController {

		public function __construct() {
			// Register ENUM type
			//$this->request = $request;
			DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping("enum", "string");
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
			$this->table = "assets_inventory_header_for_approval";
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

			# OLD START FORM
			//$this->form = [];
			//$this->form[] = ["label"=>"Po No","name"=>"po_no","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Invoice Date","name"=>"invoice_date","type"=>"date","required"=>TRUE,"validation"=>"required|date"];
			//$this->form[] = ["label"=>"Invoice No","name"=>"invoice_no","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Rr Date","name"=>"rr_date","type"=>"date","required"=>TRUE,"validation"=>"required|date"];
			//$this->form[] = ["label"=>"Expiration Date","name"=>"expiration_date","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Location","name"=>"location","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Wattage","name"=>"wattage","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Phase","name"=>"phase","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Created By","name"=>"created_by","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Updated By","name"=>"updated_by","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Date Updated","name"=>"date_updated","type"=>"datetime","required"=>TRUE,"validation"=>"required|date_format:Y-m-d H:i:s"];
			//$this->form[] = ["label"=>"Archived","name"=>"archived","type"=>"datetime","required"=>TRUE,"validation"=>"required|date_format:Y-m-d H:i:s"];
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
			$for_approval = DB::table('statuses')->where('id', 20)->value('id');
			$reject       = DB::table('statuses')->where('id', 21)->value('id');
			$recieved     = DB::table('statuses')->where('id', 22)->value('id');
			$closed       = DB::table('statuses')->where('id', 13)->value('id');
			$for_po       = DB::table('statuses')->where('id', 47)->value('id');
			if(CRUDBooster::myPrivilegeId() == 6){
				$this->addaction[] = ['url'=>CRUDBooster::mainpath('detail-view-print/[id]'),'icon'=>'fa fa-eye','color'=>'default', "showIf"=>"[header_approval_status] == $recieved"];
				// $this->addaction[] = ['url'=>CRUDBooster::mainpath('detail/[id]'),'icon'=>'fa fa-pencil','color'=>'default', "showIf"=>"[header_approval_status] == $for_approval && [location] == 2"];
				$this->addaction[] = ['url'=>CRUDBooster::mainpath('detail-view-print/[id]'),'icon'=>'fa fa-eye','color'=>'default', "showIf"=>"[header_approval_status] == $for_approval "];
				// $this->addaction[] = ['url'=>CRUDBooster::mainpath('detail-view/[id]'),'icon'=>'fa fa-eye','color'=>'default', "showIf"=>"[header_approval_status] == $reject"];
				// $this->addaction[] = ['url'=>CRUDBooster::mainpath('detail-view/[id]'),'icon'=>'fa fa-eye','color'=>'default', "showIf"=>"[header_approval_status] == $for_approval"];
				$this->addaction[] = ['url'=>CRUDBooster::mainpath('detail/[id]'),'icon'=>'fa fa-pencil','color'=>'default', "showIf"=>"[header_approval_status] == $for_po"];
			}
			else if(CRUDBooster::myPrivilegeId() == 5 || CRUDBooster::myPrivilegeId() == 9 || CRUDBooster::isSuperadmin()){
				$this->addaction[] = ['url'=>CRUDBooster::mainpath('detail-view-print/[id]'),'icon'=>'fa fa-eye','color'=>'default', "showIf"=>"[header_approval_status] == $recieved"];
				$this->addaction[] = ['url'=>CRUDBooster::mainpath('detail-for-receiving/[id]'),'icon'=>'fa fa-pencil','color'=>'default', "showIf"=>"[header_approval_status] == $for_approval"];
				$this->addaction[] = ['url'=>CRUDBooster::mainpath('detail-view/[id]'),'icon'=>'fa fa-eye','color'=>'default', "showIf"=>"[header_approval_status] == $reject"];
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
			if(CRUDBooster::getCurrentMethod() == 'getIndex'){
			  //$this->alert[] = ['message'=>"Cancelling PO's will not deduct inventory!",'type'=>'danger'];
			}
	        
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
			if(CRUDBooster::getCurrentMethod() == 'getIndex'){
				//if(CRUDBooster::myPrivilegeId() == 6){ 
					// $this->script_js = "
					// $(document).ready(function() {
					// $('h1').contents().filter(function(){
					// 	return this.nodeType != 1;
					// 	}).remove()
					// });
				
					// newPageTitle = 'Add PO';
					// var parent = document.querySelector('h1');
					// parent.firstElementChild.textContent = newPageTitle;
					// parent.firstElementChild.style.marginRight = \"20px\";
				
					// var tag = document.getElementsByTagName('a');
					// for (var i = 0; i < tag.length; i++) {
					// 	tag[i].style.marginRight = \"5px\";
					// }
					// ";
					
				//}
		    }
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
	        $this->load_js[] = asset("jquery-fat-zoom/js/zoom.js");
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
			$it_warehouse  =    DB::table('warehouse_location_model')->where('id', 3)->value('id');
			$admin_threef  =    DB::table('warehouse_location_model')->where('id', 1)->value('id');
			$admin_gf  =  		DB::table('warehouse_location_model')->where('id', 2)->value('id');
			// if(CRUDBooster::myPrivilegeId() == 5){ 
			// 	$query->where('assets_inventory_header_for_approval.location', $it_warehouse)
			// 		  ->orderBy('assets_inventory_header_for_approval.id', 'DESC');

			// }else if(CRUDBooster::myPrivilegeId() == 9){ 
			// 	$query->whereIn('assets_inventory_header_for_approval.location', [$admin_threef, $admin_gf])
			// 		  ->orderBy('assets_inventory_header_for_approval.id', 'DESC');

			// }else{
			// 	$query->whereNull('assets_inventory_header_for_approval.archived')->orderBy('assets_inventory_header_for_approval.id', 'DESC');  
			// }
			$query->whereNull('assets_inventory_header_for_approval.archived')->orderBy('assets_inventory_header_for_approval.id', 'DESC');    
	    }

	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate row of index table html 
	    | ---------------------------------------------------------------------- 
	    |
	    */    
	    public function hook_row_index($column_index,&$column_value) {	        
	    	$for_receiving = DB::table('statuses')->where('id', 20)->value('status_description');
			$received      = DB::table('statuses')->where('id', 22)->value('status_description');
			$reject        = DB::table('statuses')->where('id', 21)->value('status_description');
			$closed        = DB::table('statuses')->where('id', 13)->value('status_description');
			$for_po        = DB::table('statuses')->where('id', 47)->value('status_description');
			if($column_index == 2){
				if($column_value == $for_receiving){
					$column_value = '<span class="label label-info">'.$for_receiving.'</span>';
				}else if($column_value == $received){
					$column_value = '<span class="label label-success">'.$received.'</span>';
				}else if($column_value == $reject){
					$column_value = '<span class="label label-danger">'.$reject.'</span>';
				}else if($column_value == $closed){
					$column_value = '<span class="label label-success">'.$closed.'</span>';
				}else if($column_value == $for_po){
					$column_value = '<span class="label label-info">'.$for_po.'</span>';
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
			$postdata['header_approval_status']           = 20;
			$postdata['created_by'] 		              = CRUDBooster::myId();
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
			$header = AssetsInventoryHeaderForApproval::where(['created_by' => CRUDBooster::myId()])->orderBy('id','desc')->first();
			
			$files 	= $fields['si_dr'];
			$images = [];
			if (!empty($files)) {
				$counter = 0;
				foreach($files as $file){
					$counter++;
					$name = time().rand(1,50) . '.' . $file->getClientOriginalExtension();
					$filename = $name;
					$file->move('vendor/crudbooster/inventory_header',$filename);
					$images[]= $filename;

					$header_images = new AssetsHeaderImages;
					$header_images->header_id 		        = $header->id;
					$header_images->file_name 		        = $filename;
					$header_images->ext 		            = $file->getClientOriginalExtension();
					$header_images->created_by 		        = CRUDBooster::myId();
					$header_images->save();
				}
			}
            //SAVE FOR APPROVAL BODY
			$item_id = $fields['item_id'];
			$digits_code = $fields['digits_code'];
			$item_desc = $fields['item_description'];
			$value = $fields['value'];
			$item_type = $fields['item_type'];
			$serial_no = $fields['serial_no'];
			$digits_code_on_qty = $fields['digits_code_on_qty'];
			$serial_no_on_qty = $fields['serial_no_on_qty'];
			$quantity = $fields['add_quantity'];
			$item_category = $fields['item_category'];
			$category_id = $fields['category_id'];
			$rr_date = $fields['rr_date'];
			$location = $fields['location'];
			$warranty_coverage = $fields['warranty_coverage'];

			//MAKE ARRAY DATA
			$allData = [];
			$container = [];
			foreach($digits_code as $key => $val){
				$container['item_id'] = $item_id[$key];
				$container['header_id'] = $header->id;
				$container['serial_no'] = $serial_no[$key];
				$container['statuses_id'] = 20;
				$container['digits_code'] = $val;
				$container['item_description'] = $item_desc[$key];
				$container['value'] = $value[$key];
				$container['item_type'] = $item_type[$key];
				$container['quantity'] = $quantity[$key];
				$container['warranty_coverage'] = $warranty_coverage[$key];
				$container['item_category'] = $item_category[$key];
				$container['category_id'] = $category_id[$key];
				$container['created_by'] = CRUDBooster::myId();
				$allData[] = $container;
			}
            
			//SAVE FINAL DATA
			$saveData = [];
			foreach($allData as $frKey => $frData){		
					$setWarrantyDate = date('Y-m-d', strtotime($rr_date. '+' . $frData['warranty_coverage'] .'Years'));
					$value = str_replace(',', '', $frData['value']);
					$frData['value'] = $value;	
					$frData['quantity'] = 1;	
					$frData['warranty_coverage'] = $setWarrantyDate;
					$frData['item_condition'] = "Good";
					$frData['transaction_per_asset'] = "Inventory";
					$frData['location'] = $location;
					$frData['created_at'] = date('Y-m-d H:i:s');
					unset($frData['category_id']);
					$saveData[] = $frData;
			}
			AssetsInventoryBodyForApproval::insert($saveData);

			CRUDBooster::redirect(CRUDBooster::mainpath(), trans("Added Successfully!"), 'success');
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
	        $this->cbLoader();
			if(!CRUDBooster::isUpdate() && $this->global_privilege==FALSE) {  
				if(!CRUDBooster::myPrivilegeId() == 6) {    
					CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
				}
			}
			$fields = Request::all();
			$id = $fields['header_id'];
			$remarks = $fields['remarkscancel'];
	
			//archived header images
			AssetsHeaderImages::where('header_id', $id)->update(['archived' => date('Y-m-d H:i:s')]);
			//update header status
			AssetsInventoryHeaderForApproval::where('id', $id)
			->update(['header_approval_status' => 21, 'remarks' => $remarks, 'updated_by' => CRUDBooster::myId(), 'date_updated' => date('Y-m-d H:i:s')]);
              
			AssetsInventoryBodyForApproval::where('header_id', $id)->update(['statuses_id' => 21]);
			CRUDBooster::redirect(CRUDBooster::mainpath(),trans("Cancelling PO's will not deduct Inventory!"),'success');
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
				$data['reserved_assets'] = AssetsInventoryReserved::leftjoin('header_request','assets_inventory_reserved.reference_number','=','header_request.reference_number')->select('assets_inventory_reserved.*','header_request.*','assets_inventory_reserved.id as served_id')->whereNotNull('for_po')->where('header_request.request_type_id',1)->get();
				$data['warehouse_location'] = WarehouseLocationModel::where('id','=',3)->get();
			}else if(CRUDBooster::myPrivilegeId() == 9){
				$data['warehouse_location'] = WarehouseLocationModel::whereIn('id',[2])->get();
				$data['reserved_assets'] = AssetsInventoryReserved::leftjoin('header_request','assets_inventory_reserved.reference_number','=','header_request.reference_number')->select('assets_inventory_reserved.*','header_request.*','assets_inventory_reserved.id as served_id')->whereNotNull('for_po')->where('header_request.request_type_id',5)->get();
			}else{
				$data['warehouse_location'] = WarehouseLocationModel::where('id','!=',4)->get();
				$data['reserved_assets'] = AssetsInventoryReserved::leftjoin('header_request','assets_inventory_reserved.reference_number','=','header_request.reference_number')->select('assets_inventory_reserved.*','header_request.*','assets_inventory_reserved.id as served_id')->whereNotNull('for_po')->get();
			}

			$data['header_images'] = AssetsHeaderImages::select(
				'assets_header_images.*'
			  )
			  ->where('assets_header_images.header_id', $id)
			  ->get();
			$data['sub_categories'] = DB::table('class')->where('class_status', 'ACTIVE')->whereNull('limit_code')->orderby('class_description', 'asc')->get();
			
			return $this->view("assets.add-inventory", $data);

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
			$data['Header'] = AssetsInventoryHeaderForApproval::leftjoin('assets_header_images', 'assets_inventory_header_for_approval.id', '=', 'assets_header_images.header_id')
				->leftjoin('cms_users', 'assets_inventory_header_for_approval.created_by', '=', 'cms_users.id')
				->leftjoin('cms_users as approver', 'assets_inventory_header_for_approval.updated_by', '=', 'approver.id')
				->select(
					'assets_inventory_header_for_approval.*',
					'assets_inventory_header_for_approval.id as header_id',
					'cms_users.*',
					'approver.name as approver',
					'assets_inventory_header_for_approval.created_at as date_created'
					)
			    ->where('assets_inventory_header_for_approval.id', $id)
			    ->first();

	        //Body details
			$data['Body'] = AssetsInventoryBody::leftjoin('statuses', 'assets_inventory_body.statuses_id','=','statuses.id')
			    ->leftjoin('assets_inventory_header_for_approval', 'assets_inventory_body.header_id', '=', 'assets_inventory_header_for_approval.id')
			    ->leftjoin('assets', 'assets_inventory_body.item_id', '=', 'assets.id')
				->leftjoin('cms_users as cms_users_updated_by', 'assets_inventory_body.updated_by', '=', 'cms_users_updated_by.id')
				->select(
				  'assets_inventory_body.*',
				  'assets_inventory_body.id as for_approval_body_id',
				  'statuses.*',
				  'assets_inventory_header_for_approval.location as location',
				  'assets_inventory_body.location as body_location',
				  'assets_inventory_body.updated_at as date_updated',
				  'cms_users_updated_by.name as updated_by'
				)
				->where('assets_inventory_body.header_id', $id)
				->get();

				return $this->view("assets.edit-inventory-list-for-po", $data);
		}

		public function getDetailForReceiving($id){
			$this->cbLoader();
            if(!CRUDBooster::isRead() && $this->global_privilege==FALSE) {    
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

			$data = [];
			$data['page_title'] = 'View Asset Inventory Details';
            //header details
			$data['Header'] = AssetsInventoryHeaderForApproval::leftjoin('assets_header_images', 'assets_inventory_header_for_approval.id', '=', 'assets_header_images.header_id')
				->leftjoin('cms_users', 'assets_inventory_header_for_approval.created_by', '=', 'cms_users.id')
				->leftjoin('cms_users as approver', 'assets_inventory_header_for_approval.updated_by', '=', 'approver.id')
				->select(
					'assets_inventory_header_for_approval.*',
					'assets_inventory_header_for_approval.id as header_id',
					'cms_users.*',
					'approver.name as approver',
					'assets_inventory_header_for_approval.created_at as date_created'
					)
			    ->where('assets_inventory_header_for_approval.id', $id)
			    ->first();

			$data['header_images'] = AssetsHeaderImages::select(
				  'assets_header_images.*'
				)
				->where('assets_header_images.header_id', $id)
				->get();
	        //Body details
			$data['Body'] = AssetsInventoryBody::leftjoin('statuses', 'assets_inventory_body.statuses_id','=','statuses.id')
			    ->leftjoin('assets_inventory_header_for_approval', 'assets_inventory_body.header_id', '=', 'assets_inventory_header_for_approval.id')
			    ->leftjoin('assets', 'assets_inventory_body.item_id', '=', 'assets.id')
				->leftjoin('cms_users as cms_users_updated_by', 'assets_inventory_body.updated_by', '=', 'cms_users_updated_by.id')
				->select(
				  'assets_inventory_body.*',
				  'assets_inventory_body.id as for_approval_body_id',
				  'statuses.*',
				  'assets.item_cost as item_cost',
				  'assets_inventory_header_for_approval.location as location',
				  'assets_inventory_body.location as body_location',
				  'assets_inventory_body.updated_at as date_updated',
				  'cms_users_updated_by.name as updated_by'
				)
				->where('assets_inventory_body.header_id', $id)
				->get();
			$arrayDigitsCode = [];
            foreach($data['Body'] as $codes) {
				$digits_code['digits_code'] = $codes['digits_code'];
				array_push($arrayDigitsCode, $codes['digits_code']);
			}
			if(in_array(CRUDBooster::myPrivilegeId(),[5,17])){
				$data['warehouse_location'] = WarehouseLocationModel::where('id','=',3)->get();
			}else if(CRUDBooster::myPrivilegeId() == 9){
				$data['warehouse_location'] = WarehouseLocationModel::whereIn('id',[2])->get();
			}else{
				$data['warehouse_location'] = WarehouseLocationModel::where('id','!=',4)->get();
			}
	
			$data['reserved_assets'] = AssetsInventoryReserved::leftjoin('header_request','assets_inventory_reserved.reference_number','=','header_request.reference_number')->select('assets_inventory_reserved.*','header_request.*','assets_inventory_reserved.id as served_id')->whereNotNull('for_po')->whereIn('digits_code', $arrayDigitsCode)->get();
			return $this->view("assets.edit-inventory-list-for-receiving", $data);
		}

		//Get Invetory Approval List
		public function getDetailView($id){
			$this->cbLoader();
            if(!CRUDBooster::isRead() && $this->global_privilege==FALSE) {    
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

			$data = [];
			$data['page_title'] = 'View Asset Inventory Details';
            //header details
			$data['Header'] = AssetsInventoryHeaderForApproval::leftjoin('assets_header_images', 'assets_inventory_header_for_approval.id', '=', 'assets_header_images.header_id')
				->leftjoin('cms_users', 'assets_inventory_header_for_approval.created_by', '=', 'cms_users.id')
				->leftjoin('cms_users as approver', 'assets_inventory_header_for_approval.updated_by', '=', 'approver.id')
				->select(
					'assets_inventory_header_for_approval.*',
					'assets_inventory_header_for_approval.id as header_id',
					'cms_users.*',
					'approver.name as approver',
					'assets_inventory_header_for_approval.created_at as date_created'
					)
			    ->where('assets_inventory_header_for_approval.id', $id)
			    ->first();

			$data['header_images'] = AssetsHeaderImages::select(
				  'assets_header_images.*'
				)
				->where('assets_header_images.header_id', $id)
				->get();
	        //Body details
			$data['Body'] = AssetsInventoryBodyForApproval::leftjoin('statuses', 'assets_inventory_body_for_approval.statuses_id','=','statuses.id')
			    ->leftjoin('assets_inventory_header_for_approval', 'assets_inventory_body_for_approval.header_id', '=', 'assets_inventory_header_for_approval.id')
			    ->leftjoin('assets', 'assets_inventory_body_for_approval.item_id', '=', 'assets.id')
				->leftjoin('warehouse_location_model', 'assets_inventory_body_for_approval.location', '=', 'warehouse_location_model.id')
				->leftjoin('cms_users as cms_users_updated_by', 'assets_inventory_body_for_approval.updated_by', '=', 'cms_users_updated_by.id')
				->select(
				  'assets_inventory_body_for_approval.*',
				  'assets_inventory_body_for_approval.id as for_approval_body_id',
				  'statuses.*',
				  'assets_inventory_header_for_approval.location as location',
				  'warehouse_location_model.location as body_location',
				  'assets_inventory_body_for_approval.updated_at as date_updated',
				  'cms_users_updated_by.name as updated_by'
				)
				->where('assets_inventory_body_for_approval.header_id', $id)
				->get();
			$data['warehouse_location'] = WarehouseLocationModel::where('id','!=',4)->get();
			return $this->view("assets.inventory_list_for_approval", $data);
		}

		public function getDetailViewPrint($id){
			$this->cbLoader();
            if(!CRUDBooster::isRead() && $this->global_privilege==FALSE) {    
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

			$data = [];
			$data['page_title'] = 'View Asset Movement History Inventory Details';
            //header details
			$data['Header'] = AssetsInventoryHeaderForApproval::leftjoin('assets_header_images', 'assets_inventory_header_for_approval.id', '=', 'assets_header_images.header_id')
				->leftjoin('cms_users', 'assets_inventory_header_for_approval.created_by', '=', 'cms_users.id')
				->leftjoin('cms_users as approver', 'assets_inventory_header_for_approval.updated_by', '=', 'approver.id')
				->select(
					'assets_inventory_header_for_approval.*',
					'assets_inventory_header_for_approval.id as header_id',
					'cms_users.*',
					'approver.name as approver',
					'assets_inventory_header_for_approval.created_at as date_created'
					)
			    ->where('assets_inventory_header_for_approval.id', $id)
			    ->first();

			$data['header_images'] = AssetsHeaderImages::select(
				  'assets_header_images.*'
				)
				->where('assets_header_images.header_id', $id)
				->get();
	        //Body details
			$data['Body'] = AssetsInventoryBody::leftjoin('statuses', 'assets_inventory_body.statuses_id','=','statuses.id')
			    ->leftjoin('assets_inventory_header_for_approval', 'assets_inventory_body.header_id', '=', 'assets_inventory_header_for_approval.id')
			    ->leftjoin('assets', 'assets_inventory_body.item_id', '=', 'assets.id')
				->leftjoin('cms_users as cms_users_updated_by', 'assets_inventory_body.updated_by', '=', 'cms_users_updated_by.id')
				->leftjoin('warehouse_location_model', 'assets_inventory_body.location', '=', 'warehouse_location_model.id')
				->select(
				  'assets_inventory_body.*',
				  'assets_inventory_body.id as aib_id',
				  'statuses.*',
				  'assets_inventory_header_for_approval.location as location',
				  'warehouse_location_model.location as body_location',
				  'assets_inventory_body.updated_at as date_updated',
				  'cms_users_updated_by.name as updated_by'
				)
				->where('assets_inventory_body.header_id', $id)
				->get();

				return $this->view("assets.inventory_list", $data);
		}

		public function getapprovedProcess(Request $request){
			$this->cbLoader();
			if(!CRUDBooster::isUpdate() && $this->global_privilege==FALSE) {  
				if(!CRUDBooster::myPrivilegeId() == 6) {    
					CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
				}
			}

			$lock = Cache::lock('processing', 5);
 
			// try {
			// $lock->block(5);

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
				$count_header = DB::table('assets_inventory_header_for_approval')->count();
				$header_ref   = str_pad($count_header + 1, 7, '0', STR_PAD_LEFT);			
				$inv_ref_no	  = "INV#-".$header_ref;

				$getLastId = AssetsInventoryHeaderForApproval::Create(
					[
						'inv_reference_number'   => $inv_ref_no, 
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
					$saveContainerData['quantity']              = 1;	
					$saveContainerData['serial_no']             = $aVal['serial_no'];
					$saveContainerData['warranty_coverage']     = $aVal['warranty_coverage'];
					$saveContainerData['asset_code']            = $aVal['asset_code'];
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

				AssetsInventoryBody::insert($saveData);
				$message = ['status'=>'success', 'message' => 'Success!','redirect_url'=>CRUDBooster::mainpath('detail/'.$id)];
				echo json_encode($message);
			}
			
			
		}

		public function forPoProcess(Request $request){
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

			AssetsInventoryHeaderForApproval::where('id', $id)
			->update([
				'header_approval_status' => 20, 
				'po_no'                  => $po_no
			]);

			AssetsInventoryBody::where(['header_id' => $id])
			->update([
					'statuses_id'  => 20
					]);

			$message = ['status'=>'success', 'message' => 'Success!'];
			echo json_encode($message);
		}

		public function forReceivingProcess(Request $request){
			$this->cbLoader();
			if(!CRUDBooster::isUpdate() && $this->global_privilege==FALSE) {  
				// if(!CRUDBooster::myPrivilegeId() == 6) {    
				// 	CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
				// }
			}

			$fields = Request::all();
			$id     = $fields['id'];
			$files  = $fields['si_dr'];
			//parse data in form
		
			parse_str($fields['form_data'], $fields);

			$location          = $fields['location'];
			$invoice_date      = $fields['invoice_date'];
			$invoice_no        = $fields['invoice_no'];
			$rr_date           = $fields['rr_date'];
			$body_id           = $fields['body_id'];
			$serial_no         = $fields['serial_no'];
			$value             = $fields['value'];
			$warranty_coverage = $fields['warranty_coverage'];
			$asset_code        = $fields['asset_code'];
			$tag_id            = $fields['arf_tag'];
			$upc_code          = $fields['upc_code'];
			$brand             = $fields['brand'];
			$specs             = $fields['specs'];

			$images = [];
			if (isset($files)) {
				$counter = 0;
				foreach($files as $file){
					$counter++;
					$name = time().rand(1,50) . '.' . $file->getClientOriginalExtension();
					$filename = $name;
					$file->move('vendor/crudbooster/inventory_header',$filename);
					$images[]= $filename;

					$header_images = new AssetsHeaderImages;
					$header_images->header_id 		        = $id;
					$header_images->file_name 		        = $filename;
					$header_images->ext 		            = $file->getClientOriginalExtension();
					$header_images->created_by 		        = CRUDBooster::myId();
					$header_images->save();
				}
			}

			AssetsInventoryHeaderForApproval::where('id', $id)
			->update([
				'header_approval_status' => 22, 
				'location'               => $location,
				'invoice_date'           => $invoice_date,
				'invoice_no'             => $invoice_no,
				'rr_date'                => $rr_date,
				'updated_by'             => CRUDBooster::myId(), 
				'date_updated'           => date('Y-m-d H:i:s')
			]);

			for ($x = 0; $x < count($body_id); $x++) {
				AssetsInventoryBody::where(['id' => $body_id[$x]])
				   ->update([
					       'statuses_id'       => 6,
						   'value'             => str_replace(',', '', $value[$x]),
						   'location'          => $location,
						   'serial_no'         => $serial_no[$x],
						   'warranty_coverage' => $warranty_coverage[$x],
						   'upc_code'          => $upc_code[$x],
						   'brand'             => $brand[$x],
						   'specs'             => $specs[$x],
						   'received'          => 1
						   ]);
			}

			//update reserved table
			if($tag_id){
				for ($t = 0; $t < count($tag_id); $t++) {
					AssetsInventoryReserved::where(['id' => $tag_id[$t]])
					   ->update([
							   'reserved' => 1,
							   'for_po'   => NULL
							   ]);
					$arfNumber = AssetsInventoryReserved::where(['id' => $tag_id[$t]])->groupBy('reference_number')->get();
					foreach($arfNumber as $val){
						HeaderRequest::where('reference_number',$val->reference_number)
						->update([
							'to_mo' => 1
						]);
					}
				}
				
			}

			$message = ['status'=>'success', 'message' => 'Received!'];
			echo json_encode($message);
		}

		public function getCloseProcess(Request $request){
			$this->cbLoader();
			if(!CRUDBooster::isUpdate() && $this->global_privilege==FALSE) {  
				// if(!CRUDBooster::myPrivilegeId() == 6) {    
				// 	CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
				// }
			}
			$fields = Request::all();
			
			$id = $fields['id'];
			//update header status
			AssetsInventoryHeaderForApproval::where('id', $id)
											->update([
												'header_approval_status' => 13, 
												'updated_by' => CRUDBooster::myId(), 
												'date_updated' => date('Y-m-d H:i:s')
											]);
			//update body status
			// AssetsInventoryBodyForApproval::where(['id' => $body_id[$i]])
			// 								->update([
			// 									'statuses_id' => 21, 
			// 									'quantity' => 1,
			// 								]);
			
			
			$message = ['status'=>'success', 'message' => 'Closed!'];
			echo json_encode($message);

		}


		public function getGenerateBarcode($id) {
			if(!CRUDBooster::isView()) CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));
			//Create your own query 
			$data = [];
			$data['page_title'] = 'Generate Barcode';
			//$code = DB::table('assets_inventory_body')->find($id);
			$code = AssetsInventoryBody::select('*')->where('header_id', $id)->get();
			$finalCode = [];
			foreach($code as $headerId){
				$finalCode['header_id'] = $headerId['header_id'];
			}
			$bodyID = [];
			foreach($code as $bodyId){
				$bodyID['body_id'] = $bodyId['id'];
				$created_at['created_at'] = $bodyId['created_at']->toDateString();
			}
			$inv_date = DB::table('assets_inventory_header')->find($finalCode);
			$data['header_data'] = $inv_date;
			$data['header_id'] = $finalCode['header_id'];
			$data['created_at'] = $created_at['created_at'];
			$data['details'] = $code;
            //dd($data);
			//Create a view. Please use `view` method instead of view method from laravel.
			return $this->view('assets.assets_inventory_generate_barcode',$data);
			
		}

		public function getExport() 
		{
			return Excel::download(new ExportHeaderInventory, 'requested_inventory.xlsx');
		}

		public function assetSearch(Request $request) {
			$data = array();
			$fields = Request::all();
			$search 		   = $fields['search'];
			$data['status_no'] = 0;
			$data['message']   ='No Item Found!';
			$data['items'] = array();

			$items = DB::table('assets')
				->orWhere('assets.digits_code','LIKE','%'.$search.'%')->whereNotIn('assets.status',['EOL-DIGITS','INACTIVE'])
				->orWhere('assets.item_description','LIKE','%'.$search.'%')->whereNotIn('assets.status',['EOL-DIGITS','INACTIVE'])
				->join('tam_categories', 'assets.category_id','=', 'tam_categories.id')
				->select(	'assets.*',
				            'tam_categories.id as cat_id',
							'assets.id as assetID',
							'tam_categories.category_description as category_description'
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

		//Check reserved digits code
		public function checkDigitsCode(Request $request) {
			$fields = Request::all();
			$search = $fields['search'];
			$data = array();
			$data['status_no'] = 0;
			$data['message']   ='No Item Found!';
			$data['items'] = array();
			$data['items'] =  DB::table('assets_inventory_reserved')->where('digits_code',$search)->whereNotNull('for_po')->count();
			//dd($search,$data['items']);
			echo json_encode($data);
			exit;  
		}
		//selection digits code
		public function selectionDigitsCode(Request $request){
			$data = Request::all();	
			$digits_code = $data['digits_code'];
		
			$selectdititscode = DB::table('assets_inventory_reserved')
							->select('assets_inventory_reserved.*',
										'assets_inventory_reserved.id as served_id',)
							->where('digits_code', $digits_code)
							->whereNotNull('for_po')
							->get();
	
			return($selectdititscode);
		}

	}