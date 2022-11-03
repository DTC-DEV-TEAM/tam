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
			if(CRUDBooster::myPrivilegeName() == "Asset Custodian"){ 
			    $this->addaction[] = ['url'=>CRUDBooster::mainpath('detail-view/[id]'),'icon'=>'fa fa-eye','color'=>'default'];
			}else if(CRUDBooster::myPrivilegeName() == "IT" OR CRUDBooster::myPrivilegeName() == "Admin" OR CRUDBooster::myPrivilegeName() == "Super Administrator" ){
				$for_approval = DB::table('statuses')->where('id', 20)->value('id');
				$approved = DB::table('statuses')->where('id', 22)->value('id');
				$reject = DB::table('statuses')->where('id', 21)->value('id');
				$this->addaction[] = ['url'=>CRUDBooster::mainpath('detail/[id]'),'icon'=>'fa fa-pencil','color'=>'default', "showIf"=>"[header_approval_status] == $for_approval"];
				$this->addaction[] = ['url'=>CRUDBooster::mainpath('detail-view/[id]'),'icon'=>'fa fa-eye','color'=>'default', "showIf"=>"[header_approval_status] == $approved or [header_approval_status] == $reject"];
			
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
			  $this->alert[] = ['message'=>"Cancelling PO's will not deduct inventory!",'type'=>'danger'];
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
				if(CRUDBooster::myPrivilegeName() == "Asset Custodian" OR CRUDBooster::myPrivilegeName() == "Super Administrator"){ 
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
				if(CRUDBooster::myPrivilegeName() == "Asset Custodian"){ 
					$this->script_js = "
					$(document).ready(function() {
					$('h1').contents().filter(function(){
						return this.nodeType != 1;
						}).remove()
					});
				
					newPageTitle = 'Add PO';
					var parent = document.querySelector('h1');
					parent.firstElementChild.textContent = newPageTitle;
					parent.firstElementChild.style.marginRight = \"20px\";
				
					var tag = document.getElementsByTagName('a');
					for (var i = 0; i < tag.length; i++) {
						tag[i].style.marginRight = \"5px\";
					}
					";
					
				}
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
			if(CRUDBooster::myPrivilegeName() == "IT"){ 
				$query->where('assets_inventory_header_for_approval.location', $it_warehouse)
					  ->orderBy('assets_inventory_header_for_approval.id', 'DESC');

			}else if(CRUDBooster::myPrivilegeName() == "Admin"){ 
				$query->whereIn('assets_inventory_header_for_approval.location', [$admin_threef, $admin_gf])
					  ->orderBy('assets_inventory_header_for_approval.id', 'DESC');

			}else{
				$query->whereNull('assets_inventory_header_for_approval.archived')->orderBy('assets_inventory_header_for_approval.id', 'DESC');  
			}
			$query->whereNull('assets_inventory_header_for_approval.archived')->orderBy('assets_inventory_header_for_approval.id', 'DESC');    
	    }

	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate row of index table html 
	    | ---------------------------------------------------------------------- 
	    |
	    */    
	    public function hook_row_index($column_index,&$column_value) {	        
	    	$for_approval  =    DB::table('statuses')->where('id', 20)->value('status_description');
			$approved  =  		DB::table('statuses')->where('id', 22)->value('status_description');
			$reject  =  		DB::table('statuses')->where('id', 21)->value('status_description');
			
			if($column_index == 1){
				if($column_value == $for_approval){
					$column_value = '<span class="label label-warning">'.$for_approval.'</span>';
				}else if($column_value == $approved){
					$column_value = '<span class="label label-success">'.$approved.'</span>';
				}else if($column_value == $reject){
					$column_value = '<span class="label label-danger">'.$reject.'</span>';
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
				if(!CRUDBooster::myPrivilegeName() === "Asset Custodian") {    
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

			$data['warehouse_location'] = WarehouseLocationModel::all();

			return $this->view("assets.add-inventory", $data);

		}

		//Get Invetory Approval List
		public function getDetail($id){
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
				->leftjoin('cms_users as cms_users_updated_by', 'assets_inventory_body_for_approval.updated_by', '=', 'cms_users_updated_by.id')
				->select(
				  'assets_inventory_body_for_approval.*',
				  'assets_inventory_body_for_approval.id as for_approval_body_id',
				  'statuses.*',
				  'assets_inventory_header_for_approval.location as location',
				  'assets_inventory_body_for_approval.location as body_location',
				  'assets.item_type as itemType',
				  'assets.image as itemImage',
				  'assets_inventory_body_for_approval.updated_at as date_updated',
				  'cms_users_updated_by.name as updated_by'
				)
				->where('assets_inventory_body_for_approval.header_id', $id)
				->get();
				$data['warehouse_location'] = WarehouseLocationModel::all();
				return $this->view("assets.edit-inventory-list-for-approval", $data);
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
				  'assets.item_type as itemType',
				  'assets.image as itemImage',
				  'assets_inventory_body_for_approval.updated_at as date_updated',
				  'cms_users_updated_by.name as updated_by'
				)
				->where('assets_inventory_body_for_approval.header_id', $id)
				->get();
				$data['warehouse_location'] = WarehouseLocationModel::all();
				return $this->view("assets.inventory_list_for_approval", $data);
		}

		public function getapprovedProcess(Request $request){
			$this->cbLoader();
			if(!CRUDBooster::isUpdate() && $this->global_privilege==FALSE) {  
				if(!CRUDBooster::myPrivilegeName() === "Asset Custodian") {    
					CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
				}
			}
			$lock = Cache::lock('processing', 5);
 
			try {
				$lock->block(5);
			$fields = Request::all();
			$files = $fields['si_dr'];
			$id = $fields['id'];
			$remarks = $fields['remarks'];
	
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
	        //parse data in form
			parse_str($fields['form_data'], $fields);
			$invoice_date = $fields['invoice_date'];
			$invoice_no = $fields['invoice_no'];
			$rr_date = $fields['rr_date'];
			$body_id = $fields['body_id'];
			$serial_no = $fields['serial_no'];
		
			//update header status
			AssetsInventoryHeaderForApproval::where('id', $id)
											->update([
											    	'invoice_date' => $invoice_date, 
													'invoice_no' => $invoice_no, 
													'rr_date' => $rr_date, 
													'header_approval_status' => 22, 
													'remarks' => $remarks, 
													'updated_by' => CRUDBooster::myId(), 
													'date_updated' => date('Y-m-d H:i:s')
													]);
		
            //header details
			$header = AssetsInventoryHeaderForApproval::leftjoin('assets_header_images', 'assets_inventory_header_for_approval.id', '=', 'assets_header_images.header_id')
				->leftjoin('cms_users', 'assets_inventory_header_for_approval.created_by', '=', 'cms_users.id')
				->select(
					'assets_inventory_header_for_approval.*',
					'assets_inventory_header_for_approval.id as header_id',
					'cms_users.*',
					'assets_inventory_header_for_approval.created_by as header_created_by',
					'assets_inventory_header_for_approval.created_at as date_created'
					)
			    ->where('assets_inventory_header_for_approval.id', $id)
			    ->first();
			AssetsInventoryHeader::Create(
				[
					'id' => $id, 
					'po_no' => $header->po_no, 
					'invoice_date' => $header->invoice_date,
					'invoice_no' => $header->invoice_no,
					'rr_date' => $header->rr_date,
					'location' => $header->location,
					'header_approval_status' => 6,
					'created_by' => $header->header_created_by,
					'created_at' => Carbon::parse($header->date_created)->toDateTimeString(),
				]
			);        
			for ($i = 0; $i < count($body_id); $i++) {
				AssetsInventoryBodyForApproval::where(['id' => $body_id[$i]])
				   ->update([
					       'statuses_id' => 22, 
						   'quantity' => 1,
					       'serial_no' => $serial_no[$i]
				           ]);
			}

	        //Body details
			$body = AssetsInventoryBodyForApproval::leftjoin('assets_inventory_header_for_approval', 'assets_inventory_body_for_approval.header_id', '=', 'assets_inventory_header_for_approval.id')
			->select(
				  'assets_inventory_body_for_approval.*',
				  'assets_inventory_body_for_approval.created_at as created_at'
				)
				->where('assets_inventory_body_for_approval.header_id', $id)
				->get();
			/* process to generate chronological sequential numbers asset code */
            //segregate it assets to get category id
			$ItAssetsArr = [];
			$itCatId = DB::table('category')->find(5);
			foreach ($body as $key => $value) {
				if (strtolower($value['item_category']) == strtolower($itCatId->category_description)) {
					$ItAssetsArr[] = $value;
					unset($body[$key]);
				}
				//
				foreach($ItAssetsArr as $valIt){
					$getItAssets = $valIt['item_category'];
				}
			}
		
			//segregate fixed assets to get category id
			$FixAssetsArr = [];
			$FaCatId = DB::table('category')->find(1);
			foreach ($body as $fkey => $fvalue) {
				if (strtolower($value['item_category']) == strtolower($FaCatId->category_description)) {
					$FixAssetsArr[] = $fvalue;
					unset($body[$fkey]);
				}
				foreach($FixAssetsArr as $valFa){
					$getFaAssets = $valFa['item_category'];
				}
			}

			//put asset code per based on  item category IT ASSETS
			$finalItAssetsArr = [];
			$DatabaseCounterIt = DB::table('assets_inventory_body')->where('item_category',$getItAssets)->count();
			foreach((array)$ItAssetsArr as $finalItkey => $finalItvalue) {
					$finalItvalue['asset_code'] = "A1".str_pad ($DatabaseCounterIt + 1, 6, '0', STR_PAD_LEFT);
					$DatabaseCounterIt++; // or any rule you want.	
					$finalItAssetsArr[] = $finalItvalue;	
			}
	
			//put asset code per based on  item category FIXED ASSETS
			$finalFixAssetsArr = [];
			$DatabaseCounterFixAsset = DB::table('assets_inventory_body')->where('item_category',$getFaAssets)->count();
			foreach((array)$FixAssetsArr as $finalfakey => $finalfavalue) {
					$finalfavalue['asset_code'] = "A2".str_pad ($DatabaseCounterFixAsset + 1, 6, '0', STR_PAD_LEFT);
					$DatabaseCounterFixAsset++; // or any rule you want.	
					$finalFixAssetsArr[] = $finalfavalue;
			}

            //Merge all data from segragating per item category
			$finalDataofSplittingArray = array_merge($finalItAssetsArr, $finalFixAssetsArr);
			//save final data
			$saveData = [];
			$saveContainerData = [];
			foreach($finalDataofSplittingArray as $frKey => $frData){		
				$saveContainerData['header_id'] = $frData['header_id'];
				$saveContainerData['item_id'] = $frData['item_id'];
				$saveContainerData['statuses_id'] = 6;
				$saveContainerData['location'] = $frData['location'];
				$saveContainerData['digits_code'] = $frData['digits_code'];
				$saveContainerData['item_description'] = $frData['item_description'];
				$saveContainerData['value'] = $frData['value'];
				$saveContainerData['quantity'] = 1;	
				$saveContainerData['serial_no'] = $frData['serial_no'];
				$saveContainerData['warranty_coverage'] = $frData['warranty_coverage'];
				$saveContainerData['asset_code'] = $frData['asset_code'];
				$saveContainerData['barcode'] = $frData['digits_code'].''.$frData['asset_code'];
				$saveContainerData['item_condition'] = $frData['item_condition'];
				$saveContainerData['item_category'] = $frData['item_category'];
				$saveContainerData['transaction_per_asset'] = $frData['transaction_per_asset'];
				$saveContainerData['created_by'] = $frData['created_by'];
				$saveContainerData['created_at'] = Carbon::parse($frData['created_at'])->toDateTimeString();
			
				$saveData[] = $saveContainerData;
			}
			AssetsInventoryBody::insert($saveData);

			$message = ['status'=>'success', 'message' => 'Received!','redirect_url'=>CRUDBooster::mainpath('generate-barcode/'.$id)];
			echo json_encode($message);
			sleep(3);
			// Lock acquired after waiting a maximum of 5 seconds...
			} catch (LockTimeoutException $e) {
				// Unable to acquire lock...
				return;
			} finally {
				optional($lock)->release();
			}
			
		}

		public function getrejectedProcess(Request $request){
			$this->cbLoader();
			if(!CRUDBooster::isUpdate() && $this->global_privilege==FALSE) {  
				if(!CRUDBooster::myPrivilegeName() === "Asset Custodian") {    
					CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
				}
			}
			$fields = Request::all();
			if($fields['approvalMethod'] === "approvercancel"){
				$files = $fields['si_dr'];
				$id = $fields['id'];
				$remarks = $fields['remarks'];
		
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
				 //parse data in form
				 parse_str($fields['form_data'], $fields);
				 $invoice_date = $fields['invoice_date'] ? $fields['invoice_date'] : NULL;
				 $invoice_no = $fields['invoice_no'] ? $fields['invoice_no'] : NULL;
				 $rr_date = $fields['rr_date'] ? $fields['rr_date'] : NULL;
				 $body_id = $fields['body_id'];
				 $serial_no = $fields['serial_no'];
				
				//update header status
				AssetsInventoryHeaderForApproval::where('id', $id)
												->update([
													'invoice_date' => $invoice_date, 
													'invoice_no' => $invoice_no, 
													'rr_date' => $rr_date, 
													'header_approval_status' => 21, 
													'remarks' => $remarks, 
													'updated_by' => CRUDBooster::myId(), 
													'date_updated' => date('Y-m-d H:i:s')
												]);
				for ($i = 0; $i < count($body_id); $i++) {
					AssetsInventoryBodyForApproval::where(['id' => $body_id[$i]])
						->update([
								'statuses_id' => 21, 
								'quantity' => 1,
								'serial_no' => $serial_no[$i]
								]);
				}  
			}else{
				$id = $fields['id'];
				//update header status
				AssetsInventoryHeaderForApproval::where('id', $id)
												->update([
													'header_approval_status' => 21, 
													'updated_by' => CRUDBooster::myId(), 
													'date_updated' => date('Y-m-d H:i:s')
												]);
				//update body status
				AssetsInventoryBodyForApproval::where(['id' => $body_id[$i]])
												->update([
													'statuses_id' => 21, 
													'quantity' => 1,
												]);
			}
			
			$message = ['status'=>'success', 'message' => 'Cancelled!'];
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

	}