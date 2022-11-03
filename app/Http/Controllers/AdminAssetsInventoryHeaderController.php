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
	use App\AssetsInventoryHeader;
	use App\AssetsHeaderImages;
	use App\AssetsInventoryBody;
	use App\AssetsInventoryStatus;
	use App\AssetsMovementHistory;
	use App\GeneratedAssetsHistories;
	use App\MoveOrder;
	use App\HeaderRequest;
	use App\BodyRequest;
	use App\WarehouseLocationModel;
	use Illuminate\Contracts\Cache\LockTimeoutException;
	use Carbon\Carbon;
	class AdminAssetsInventoryHeaderController extends \crocodicstudio\crudbooster\controllers\CBController {

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
			$this->button_bulk_action = false;
			$this->button_action_style = "button_icon";
			$this->button_add = false;
			$this->button_edit = false;
			$this->button_delete = false;
			$this->button_detail = true;
			$this->button_show = true;
			$this->button_filter = true;
			$this->button_import = false;
			$this->button_export = false;
			$this->table = "assets_inventory_header";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Po No","name"=>"po_no"];
			$this->col[] = ["label"=>"Invoice Date","name"=>"invoice_date"];
			$this->col[] = ["label"=>"Invoice No","name"=>"invoice_no"];
			$this->col[] = ["label"=>"RR Date","name"=>"rr_date"];
			// $this->col[] = ["label"=>"Wattage","name"=>"wattage"];
			// $this->col[] = ["label"=>"Phase","name"=>"phase"];
			$this->col[] = ["label"=>"Created By","name"=>"created_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"Date Created","name"=>"created_at"];
			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'Po No','name'=>'po_no','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Invoice Date','name'=>'invoice_date','type'=>'date','validation'=>'required|date','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Rr Date','name'=>'rr_date','type'=>'date','validation'=>'required|date','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Expiration Date','name'=>'expiration_date','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Wattage','name'=>'wattage','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Phase','name'=>'phase','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			# END FORM DO NOT REMOVE THIS LINE

			# OLD START FORM
			//$this->form = [];
			//$this->form[] = ["label"=>"Po No","name"=>"po_no","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Invoice Date","name"=>"invoice_date","type"=>"date","required"=>TRUE,"validation"=>"required|date"];
			//$this->form[] = ["label"=>"Rr Date","name"=>"rr_date","type"=>"date","required"=>TRUE,"validation"=>"required|date"];
			//$this->form[] = ["label"=>"Expiration Date","name"=>"expiration_date","type"=>"date","required"=>TRUE,"validation"=>"required|date"];
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
	        //$this->alert[] = ['message'=>'Inventory Successfully Added!','type'=>'success'];        

	        
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
				$this->index_button[] = ["label"=>"Export","icon"=>"fa fa-download","url"=>CRUDBooster::mainpath('export-assets-history'),"color"=>"primary"];
				//$this->index_button[] = ["label"=>"Export","icon"=>"fa fa-files-o","url"=>CRUDBooster::mainpath('export-assets-header'),"color"=>"primary"];
				
				//$this->index_button[] = ["label"=>"Add Inventory","icon"=>"fa fa-files-o","url"=>CRUDBooster::mainpath('add-inventory'),"color"=>"success"];


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
			$(document).ready(function() {
				$(\".date\").datetimepicker({
                    viewMode: \"days\",
                    format: \"YYYY-MM-DD\",
                    dayViewHeaderFormat: \"MMMM YYYY\",
                });
				$(\"#myModal\").modal('hide');
				$('#test').click(function(event) {
					event.preventDefault();
					$(\"#myModal\").modal('show');
				});
				$('#start_date, #start_end').on('dp.change', function(e){ 
					var start_date_val = $('#start_date').val();
					var start_end_val = $('#start_end').val();
		            $('#exportHistory').attr('href', 'http://127.0.0.1:8000/admin/assets_inventory_header/export-assets-header/'+ start_date_val +'/'+ start_end_val);
				})

				$(\".modal\").on(\"hidden.bs.modal\", function(){
					$(\"#start_date\").val(\"\");
					$(\"#start_end\").val(\"\");
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
			$this->pre_index_html = "

			   <!-- Modal HTML -->
			   <div id=\"myModal\" class=\"modal fade\" tabindex=\"-1\">
				   <div class=\"modal-dialog\">
					   <div class=\"modal-content\">
						   <div class=\"modal-header\">
						   <button type=\"button\" class=\"close\" data-dismiss=\"modal\">&times;</button>
							   <h5 class=\"modal-title\">Export Asset Movement History</h5>
							  
						   </div>
						   <div class=\"modal-body\">
						      <div class='row'>
							
								<input type=\"hidden\" value\csrf_token()}}\" name=\"_token\" id=\"token\">
								<div class='col-md-6'>
								<input type\"text\" class=\"form-control date\" name=\"start_date\"  id=\"start_date\" placeholder=\"Please Select Start Date(Optional)\">
								</div>
								<div class='col-md-6'>
								<input type\"text\" class=\"form-control date\" name=\"start_end\" id=\"start_end\" placeholder=\"Please Select Start End(Optional)\">
								</div>		
							  </div>
						   </div>
						   <div class=\"modal-footer\">
							   <button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Cancel</button>
							   <a id=\"exportHistory\" class=\"btn btn-primary btn-sm\" href='http://127.0.0.1:8000/admin/assets_inventory_header/export-assets-header-all/'>
                                <i class=\"fa fa-files-o\"></i> Export
                                </a>
						   </div>
					   </div>
				   </div>
			   </div>
			
			";
	        
	        
	        
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
			//$this->load_js[] = asset("sweetalert2/sweetalert2.js");
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
	        //$this->load_css[] = asset("sweetalert2/sweetalert2.css");
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
	        //Your code here
	        $query->whereNull('assets_inventory_header.archived')->orderBy('assets_inventory_header.id', 'DESC');    
	    }

	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate row of index table html 
	    | ---------------------------------------------------------------------- 
	    |
	    */    
	    public function hook_row_index($column_index,&$column_value) {	        
	    	//Your code here
			//Your code here
			$available  =  		DB::table('statuses')->where('id', 6)->value('status_description');

			$reserved  =  		DB::table('statuses')->where('id', 2)->value('status_description');

			if($column_index == 2){
				if($column_value == $available){
					$column_value = '<span class="label label-warning">'.$available.'</span>';
				}else if($column_value == $reserved){
					$column_value = '<span class="label label-info">'.$reserved.'</span>';
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
			// $cont = (new static)->apiContext;
			$lock = Cache::lock('processing', 5);
 
			try {
				$lock->block(5);
				$postdata['invoice_no']         = $fields['invoice_no'];
				$postdata['location']           = $fields['location'];
				$postdata['created_by'] 		= CRUDBooster::myId();
				sleep(3);
				// Lock acquired after waiting a maximum of 5 seconds...
			} catch (LockTimeoutException $e) {
				// Unable to acquire lock...
				return;
			} finally {
				optional($lock)->release();
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
	        $fields = Request::all();
			//$cont = (new static)->apiContext;
			$header = AssetsInventoryHeader::where(['created_by' => CRUDBooster::myId()])->orderBy('id','desc')->first();
			
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

			// Body Save
			$allData = [];
			$container = [];
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
            //$item_photo = $fields['item_photo'];
			$rr_date = $fields['rr_date'];
			$location = $fields['location'];
			$warranty_coverage = $fields['warranty_coverage'];
			
			//make base default value		
			foreach($digits_code as $key => $val){
				//get assets masterfile image
				$assetMasterFileImage = DB::table("assets")->where('digits_code', $val)->first();
				// //body image
				// $counter_body++;
				// if (!empty($item_photo[$key])) {
				// 		$name_photo = $counter_body.time() . '.' . $item_photo[$key]->getClientOriginalExtension();
				// 		$filename_photo = $name_photo;
				// 		$item_photo[$key]->move('vendor/crudbooster/inventory_body',$filename_photo);
					
				// }
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
				//$container['item_photo'] = $item_photo[$key] ? 'vendor/crudbooster/inventory_body/'.$filename_photo : $assetMasterFileImage->image;
				$container['created_by'] = CRUDBooster::myId();
				$allData[] = $container;
			
			}

			//make array base on general quantity
			$general_quantity = $fields['quantity'];
			$resultArr = [];
			foreach($allData as $item){
				if(strtolower($item['item_type']) == "general"){
					for($i = 0; $i < $item['quantity']-1; $i++){
						// make sure the quantity is now 1 and not the original > 1 value
						$t = $item;
						$t['quantity'] = 1;
						$resultArr[] = $t;
					}
				}
			}
            //merge default array data and array data from general quantity
			$arraySearch = array_merge($resultArr, $allData);
	
			//process array data from serial quantity
			$dcoqData = [];
			$dcoqContainer = [];
			foreach((array)$digits_code_on_qty as $dcoqkey => $dcoqval){
				$dcoqContainer['serial_no'] = $serial_no_on_qty[$dcoqkey];
				$dcoqContainer['digits_code'] = $dcoqval;
				$dcoqData[] = $dcoqContainer;
			
			}
            //search value of process data from defaut array value to fill his blank elements
			$dcoqfinalData = [];
			foreach((array)$dcoqData as $dcoqfKey => $dcoqfVal){
				$i = array_search($dcoqfVal['digits_code'], array_column($arraySearch,'digits_code'));
				if($i !== false){
					$dcoqfVal['other_value'] = $arraySearch[$i];
					$dcoqfinalData[] = $dcoqfVal;
				}else{
					$dcoqfVal['other_value'] = "";
					$dcoqfinalData[] = $dcoqfVal;
				}
			}
			//now process serial quantity array data now have search data, assign value from search value
			$result = [];
			$resultContainer = [];
			foreach((array)$dcoqfinalData as $finalkey => $finalval){
				$resultContainer['item_id'] = $finalval['other_value']['item_id'];
				$resultContainer['header_id'] = $finalval['other_value']['header_id'];
				$resultContainer['serial_no'] = $finalval['serial_no'];
				$resultContainer['statuses_id'] = $finalval['other_value']['statuses_id'];
				$resultContainer['digits_code'] = $finalval['digits_code'];
				$resultContainer['item_description'] = $finalval['other_value']['item_description'];
				$resultContainer['value'] = $finalval['other_value']['value'];
				$resultContainer['item_type'] = $finalval['other_value']['item_type'];
				$resultContainer['quantity'] = $finalval['other_value']['quantity'];
				$resultContainer['warranty_coverage'] = $finalval['other_value']['warranty_coverage'];
				$resultContainer['item_category'] = $finalval['other_value']['item_category'];
				//$resultContainer['item_photo'] = $finalval['other_value']['item_photo'];
				$resultContainer['created_by'] = $finalval['other_value']['created_by'];
				$result[] = $resultContainer;
			}
			//then merge default array value and process array value from serial quantity
			$final_result_data = array_merge($arraySearch, $result);

			/* process to generate chronological sequential numbers asset code */
            //segregate it assets to get category id
			$ItAssetsArr = [];
			$itCatId = DB::table('category')->find(5);
			foreach ($final_result_data as $key => $value) {
				if (strtolower($value['item_category']) == strtolower($itCatId->category_description)) {
					$ItAssetsArr[] = $value;
					unset($final_result_data[$key]);
				}
				//
				foreach($ItAssetsArr as $valIt){
					$getItAssets = $valIt['item_category'];
				}
			}
		
			//segregate fixed assets to get category id
			$FixAssetsArr = [];
			$FaCatId = DB::table('category')->find(1);
			foreach ($final_result_data as $fkey => $fvalue) {
				if (strtolower($value['item_category']) == strtolower($FaCatId->category_description)) {
					$FixAssetsArr[] = $fvalue;
					unset($final_result_data[$fkey]);
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
			foreach($finalDataofSplittingArray as $frKey => $frData){		
					$setWarrantyDate = date('Y-m-d', strtotime($rr_date. '+' . $frData['warranty_coverage'] .'Years'));
					$value = str_replace(',', '', $frData['value']);
					$frData['value'] = $value;	
					$frData['quantity'] = 0;	
					$frData['warranty_coverage'] = $setWarrantyDate;
					$frData['barcode'] = $frData['digits_code'].''.$frData['asset_code'];
					$frData['item_condition'] = "Good";
					$frData['transaction_per_asset'] = "Inventory";
					$frData['location'] = $location;
					$frData['created_at'] = date('Y-m-d H:i:s');
					unset($frData['category_id']);
					$saveData[] = $frData;
			}
			
			AssetsInventoryBody::insert($saveData);

			CRUDBooster::redirect(CRUDBooster::adminpath('assets_inventory_body'), trans("Inventory Added Successfully!"), 'success');
			//CRUDBooster::redirect(CRUDBooster::mainpath('generate-barcode/'.$header->id), trans("Inventory Added Successfully!"), 'success');
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
			// $fields = Input::all();
		    // dd($fields);
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
			CRUDBooster::redirect(CRUDBooster::mainpath(), trans("Inventory Added Successfully!"), 'success');
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
		public function getAddInventory() {

			if(!CRUDBooster::isCreate() && $this->global_privilege == false) {
				CRUDBooster::redirect(CRUDBooster::adminPath(), trans('crudbooster.denied_access'));
			}

			$this->cbLoader();

			$data['page_title'] = 'Add Inventory';

			$data['warehouse_location'] = WarehouseLocationModel::all();

			return $this->view("assets.add-inventory", $data);

		}
		//customize index
		public function getIndex() {
			//First, Add an auth
			 if(!CRUDBooster::isView()) CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));
			 
			 //Create your own query 
			 $data = [];
			 $data['page_title'] = 'Assets Movement History';
			 $data['result'] = DB::table('assets_inventory_body')
			 ->join('assets_inventory_header', 'assets_inventory_body.header_id', '=', 'assets_inventory_header.id')
			 ->join('statuses', 'assets_inventory_body.statuses_id', '=', 'statuses.id')
			 ->orderby('assets_inventory_body.id','ASC')
			 ->get();
			 $data['history'] = GeneratedAssetsHistories::all();

			 //dd($data['history']);
			 //Create a view. Please use `view` method instead of view method from laravel.
			 return $this->view('assets.assets_movement_history',$data);
		  }

        //Get Invetory List
		public function getDetail($id){
			$this->cbLoader();
            if(!CRUDBooster::isRead() && $this->global_privilege==FALSE) {    
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

			$data = [];
			$data['page_title'] = 'View Asset Movement History Inventory Details';
            //header details
			$data['Header'] = AssetsInventoryHeader::leftjoin('assets_header_images', 'assets_inventory_header.id', '=', 'assets_header_images.header_id')
				->leftjoin('cms_users', 'assets_inventory_header.created_by', '=', 'cms_users.id')
				->select(
					'assets_inventory_header.*',
					'assets_inventory_header.id as header_id',
					'cms_users.*',
					'assets_inventory_header.created_at as date_created'
					)
			    ->where('assets_inventory_header.id', $id)
			    ->first();

			$data['header_images'] = AssetsHeaderImages::select(
				  'assets_header_images.*'
				)
				->where('assets_header_images.header_id', $id)
				->get();
	        //Body details
			$data['Body'] = AssetsInventoryBody::leftjoin('statuses', 'assets_inventory_body.statuses_id','=','statuses.id')
			    ->leftjoin('assets_inventory_header', 'assets_inventory_body.header_id', '=', 'assets_inventory_header.id')
			    ->leftjoin('assets', 'assets_inventory_body.item_id', '=', 'assets.id')
				->leftjoin('cms_users as cms_users_updated_by', 'assets_inventory_body.updated_by', '=', 'cms_users_updated_by.id')
				->leftjoin('warehouse_location_model', 'assets_inventory_body.location', '=', 'warehouse_location_model.id')
				->select(
				  'assets_inventory_body.*',
				  'assets_inventory_body.id as aib_id',
				  'statuses.*',
				  'assets_inventory_header.location as location',
				  'warehouse_location_model.location as body_location',
				  'assets.item_type as itemType',
				  'assets.image as itemImage',
				  'assets_inventory_body.updated_at as date_updated',
				  'cms_users_updated_by.name as updated_by'
				)
				->where('assets_inventory_body.header_id', $id)
				->get();

				return $this->view("assets.inventory_list", $data);
		}

		//Get Deployed List
		public function getDetailDeployed($id){
			$this->cbLoader();
            if(!CRUDBooster::isRead() && $this->global_privilege==FALSE) {    
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

			$data = [];
			$data['page_title'] = 'View Asset Movement History Deployed Details';
            //header details
			//$HeaderID = MoveOrder::where('id', $id)->first();

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


			$data['MoveOrder'] = MoveOrder::
				select(
				  'mo_body_request.*',
				  'statuses.status_description as status_description'
				)
				->where('mo_body_request.header_request_id', $id)
				->whereIn('mo_body_request.status_id', array(16, 13, 19))
				->leftjoin('statuses', 'mo_body_request.status_id', '=', 'statuses.id')
				->orderby('mo_body_request.id', 'desc')
				->get();

				return $this->view("assets.inventory_deployed_list", $data);
		}

		//Get Invetory Edit
		public function getEdit($id){
			$this->cbLoader();
            if(!CRUDBooster::isRead() && $this->global_privilege==FALSE) {    
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }
			$data = [];
			$data['page_title'] = 'Edit Assets Inventory';

			$data['Header'] = AssetsInventoryHeader::leftjoin('assets_header_images', 'assets_inventory_header.id', '=', 'assets_header_images.header_id')
			->leftjoin('cms_users', 'assets_inventory_header.created_by', '=', 'cms_users.id')
			->select(
				'assets_inventory_header.*',
				'cms_users.*'
			  )->where('assets_inventory_header.id', $id)->first();
			  $data['header_images'] = AssetsHeaderImages::select(
				'assets_header_images.*'
			  )
			  ->where('assets_header_images.header_id', $id)
			  ->get();

			  return $this->view("assets.edit_assets_inventory", $data);
		}

		//Generate Assets Inventory Barcode as whole
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

		//Generate Assets Inventory Barcode as whole
		public function getGenerateBarcodeSingle($id) {
			if(!CRUDBooster::isView()) CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));
			//Create your own query 
			$data = [];
			$data['page_title'] = 'Generate Barcode';
			$code = DB::table('assets_inventory_body')->find($id);
			$inv_date = DB::table('assets_inventory_header')->find($code->header_id);
			$data['header_data'] = $inv_date;
			$data['item_code'] = $code->digits_code;
			$data['asset_tag'] = $code->asset_code;
			$data['details'] = $code;
			//Create a view. Please use `view` method instead of view method from laravel.
			return $this->view('assets.assets_inventory_generate_barcode_single',$data);
			
		}

		public function ExportExcel($assets_data){
			ini_set('max_execution_time', 0);
			ini_set('memory_limit', '4000M');
			try {
				$spreadSheet = new Spreadsheet();
				$spreadSheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
				$spreadSheet->getActiveSheet()->fromArray($assets_data);
				$Excel_writer = new Xlsx($spreadSheet);
				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment;filename="AssetsMovementHistory.xlsx"');
				header('Cache-Control: max-age=0');
				ob_end_clean();
				$Excel_writer->save('php://output');
				exit();
			} catch (Exception $e) {
				return;
			}
		}

		public function ExportExcelByDate($assets_data){
			ini_set('max_execution_time', 0);
			ini_set('memory_limit', '4000M');
			try {
				$spreadSheet = new Spreadsheet();
				$spreadSheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
				$spreadSheet->getActiveSheet()->fromArray($assets_data);
				$Excel_writer = new Xlsx($spreadSheet);
				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment;filename="AssetsMovementHistoryByDate.xlsx"');
				header('Cache-Control: max-age=0');
				ob_end_clean();
				$Excel_writer->save('php://output');
				exit();
			} catch (Exception $e) {
				return;
			}
		}

		public function getExportAssetsHeaderAll() {
			$data = AssetsInventoryBody::leftjoin('statuses', 'assets_inventory_body.statuses_id','=','statuses.id')
				->leftjoin('assets_inventory_header', 'assets_inventory_body.header_id','=','assets_inventory_header.id')
				->leftjoin('cms_users', 'assets_inventory_body.created_by', '=', 'cms_users.id')
				//->leftjoin('assets_movement_histories', 'assets_inventory_body.id', '=', 'assets_movement_histories.body_id')
				// ->leftJoin('assets_movement_histories', function($join) 
				// {
				// 	$join->on('assets_inventory_body.id', '=', 'assets_movement_histories.body_id')
				// 	->whereNull('assets_movement_histories.archived');
				// })
				->select(
				  'assets_inventory_header.*',
				  'assets_inventory_body.*',
				  'assets_inventory_body.id as aib_id',
				  'statuses.*',
				  'cms_users.*',
				//   'assets_movement_histories.*',
				  'assets_inventory_body.location as body_location',
				  'assets_inventory_body.deployed_to as body_deployed_to',
				//   'assets_movement_histories.deployed_to as history_deployed_to',
				//   'assets_movement_histories.location as history_location',
				//   'assets_movement_histories.updated_by as history_updated_by',
				//   'assets_movement_histories.date_update as history_date_updated',
				//   'assets_movement_histories.remarks as history_remarks',
				  'assets_inventory_body.created_at as date_created'
				)
			    //->where('assets_inventory_body.id NOT IN (select body_id from assets_movement_histories)',NULL,FALSE)  
				->get();
				$data_array [] = array("Po No","Invoice Date","Invoice No","RR Date",
									  "Asset Code","Digits Code","Serial No","Location",
									  "Status","Deployed To","Item Description","Value",
									  "Item Type","Quantity","Warranty Coverage Year",
									  "History Update By",
									  "History Date Updated",
									  //"History Deployed To",
									  "History Location",
									  "History Remarks",
									  "Created By","Date Created");
				foreach($data as $valArrOne){
					$data_array[] = array(
						'PO No' => $valArrOne->po_no,
						'Invoice Date' => $valArrOne->invoice_date,
						'Invoice No' => $valArrOne->invoice_no,
						'RR Date' => $valArrOne->rr_date,
						'Asset Code' => $valArrOne->asset_code,
						'Digits Code' => $valArrOne->digits_code,
						'Serial No' =>$valArrOne->serial_no,
						'Location' =>$valArrOne->body_location,
						'Status' =>$valArrOne->status_description,
						'Deployed To' =>$valArrOne->body_deployed_to,
						'Item Description' => $valArrOne->item_description,
						'Value' => $valArrOne->value,
						'Item Type' =>$valArrOne->item_type,
						'Quantity' =>$valArrOne->quantity,
						'Warranty Coverage"' => $valArrOne->warranty_coverage,
                        'History Update By' => "",
						'History Date Updated' => "",
						//'History Deployed To' => $valArrOne->history_deployed_to,
						'History Location' => "",
						'History Remarks' => "",
						'Created By' =>$valArrOne->name,
						'Date Created' =>$valArrOne->date_created,
					);
				}

				//dd($data_array);
	
				$data2 = AssetsMovementHistory::leftjoin('assets_inventory_body', 'assets_movement_histories.body_id','=','assets_inventory_body.id')
				->leftjoin('assets_inventory_header', 'assets_movement_histories.header_id','=','assets_inventory_header.id')
				//->leftjoin('cms_users', 'assets_movement_histories.updated_by', '=', 'cms_users.id')
				->leftjoin('statuses', 'assets_inventory_body.statuses_id','=','statuses.id')
				->select(
				  'assets_movement_histories.*',
				  'assets_inventory_body.location as body_location',
				  'assets_movement_histories.deployed_to as history_deployed_to',
				  'assets_movement_histories.location as history_location',
				  'assets_movement_histories.history_updated_by as history_updated_by',
				  'assets_movement_histories.remarks as history_remarks',
				  'assets_inventory_header.*',
				  'assets_inventory_body.*',
				  'assets_inventory_body.created_at as date_created',
				  'statuses.*'
				  //'cms_users.*'
				)
				//->whereNotNull('assets_movement_histories.archived')
				->get();
				
				foreach($data2 as $valArrTwo){
					$data_array[] = array(
						'PO No' => $valArrTwo->po_no,
						'Invoice Date' => $valArrTwo->invoice_date,
						'Invoice No' => $valArrTwo->invoice_no,
						'RR Date' => $valArrTwo->rr_date,
						'Asset Code' => $valArrTwo->asset_code,
						'Digits Code' => $valArrTwo->digits_code,
						'Serial No' =>$valArrTwo->serial_no,
						'Location' =>$valArrTwo->body_location,
						'Status' =>$valArrTwo->status_description,
						'Deployed To' =>$valArrTwo->deployed_to,
						'Item Description' => $valArrTwo->item_description,
						'Value' => $valArrTwo->value,
						'Item Type' =>$valArrTwo->item_type,
						'Quantity' =>$valArrTwo->quantity,
						'Warranty Coverage"' => $valArrTwo->warranty_coverage,
						'History Update By' => $valArrTwo->history_updated_by,
						//'History Deployed To' => $valArrTwo->history_deployed_to,
						'History Location' => $valArrTwo->history_location,
						'History Remarks' => $valArrTwo->history_remarks,
						'Created By' =>$valArrTwo->name,
						'Date Created' =>$valArrTwo->date_created,
					);
				}

				// $array_export = array_merge($data_array, $data_array_2);
			    // dd($data2);
				$this->ExportExcel($data_array);
		}

		public function getExportAssetsHeader($start_date, $start_end) {
			$data = AssetsInventoryBody::leftjoin('statuses', 'assets_inventory_body.statuses_id','=','statuses.id')
			->leftjoin('assets_inventory_header', 'assets_inventory_body.header_id','=','assets_inventory_header.id')
			->leftjoin('cms_users', 'assets_inventory_body.created_by', '=', 'cms_users.id')
			// ->leftjoin('assets_movement_histories', 'assets_inventory_body.id', '=', 'assets_movement_histories.body_id')
			// ->leftJoin('assets_movement_histories', function($join) 
			// {
			// 	$join->on('assets_inventory_body.id', '=', 'assets_movement_histories.body_id')
			// 	->whereNull('assets_movement_histories.archived');
			// })
			->select(
			  'assets_inventory_header.*',
			  'assets_inventory_body.*',
			  'assets_inventory_body.id as aib_id',
			  'statuses.*',
			  'cms_users.*',
			//   'assets_movement_histories.*',
			  'assets_inventory_body.location as body_location',
			  'assets_inventory_body.deployed_to as body_deployed_to',
			//   'assets_movement_histories.deployed_to as history_deployed_to',
			//   'assets_movement_histories.location as history_location',
			//   'assets_movement_histories.updated_by as history_updated_by',
			//   'assets_movement_histories.date_update as history_date_update',
			//   'assets_movement_histories.remarks as history_remarks',
			  'assets_inventory_body.created_at as date_created'
			) 
			->whereDate('assets_inventory_body.created_at','>=' ,$start_date)
			->whereDate('assets_inventory_body.created_at','<=' ,$start_end)
			->get();
			$data_array [] = array("Po No","Invoice Date","Invoice No","RR Date",
			                      "Asset Code","Digits Code","Serial No","Location",
								  "Status","Deployed To","Item Description","Value",
								  "Item Type","Quantity","Warranty Coverage Year",
								  "History Update By",
								  "History Date Updated",
								  //"History Deployed To",
								  "History Location",
								  "History Remarks",
								  "Created By","Date Created");
			foreach($data as $valArrOne){
				$data_array[] = array(
					'PO No' => $valArrOne->po_no,
					'Invoice Date' => $valArrOne->invoice_date,
					'Invoice No' => $valArrOne->invoice_no,
					'RR Date' => $valArrOne->rr_date,
					'Asset Code' => $valArrOne->asset_code,
					'Digits Code' => $valArrOne->digits_code,
					'Serial No' =>$valArrOne->serial_no,
					'Location' =>$valArrOne->body_location,
					'Status' =>$valArrOne->status_description,
					'Deployed To' =>$valArrOne->body_deployed_to,
					'Item Description' => $valArrOne->item_description,
					'Value' => $valArrOne->value,
					'Item Type' =>$valArrOne->item_type,
					'Quantity' =>$valArrOne->quantity,
					'Warranty Coverage"' => $valArrOne->warranty_coverage,
					'History Update By' => "",
					'History Date Updated' => "",
					//'History Deployed To' => $valArrOne->history_deployed_to,
					'History Location' => "",
					'History Remarks' => "",
					'Created By' =>$valArrOne->name,
					'Date Created' =>$valArrOne->date_created,
				);
			}

			$data2 = AssetsMovementHistory::leftjoin('assets_inventory_body', 'assets_movement_histories.body_id','=','assets_inventory_body.id')
			->leftjoin('assets_inventory_header', 'assets_movement_histories.header_id','=','assets_inventory_header.id')
			// ->leftjoin('cms_users', 'assets_movement_histories.updated_by', '=', 'cms_users.id')
			->leftjoin('statuses', 'assets_inventory_body.statuses_id','=','statuses.id')
			->select(
			  'assets_movement_histories.*',
			  'assets_inventory_body.location as body_location',
			  'assets_movement_histories.deployed_to as history_deployed_to',
			  'assets_movement_histories.location as history_location',
			  'assets_movement_histories.history_updated_by as history_updated_by',
			  'assets_movement_histories.remarks as history_remarks',
			  'assets_inventory_header.*',
			  'assets_inventory_body.*',
			  'assets_inventory_body.created_at as date_created',
			  'statuses.*'
			//   'cms_users.*'
			)
			->whereDate('assets_inventory_body.created_at','>=' ,$start_date)
			->whereDate('assets_inventory_body.created_at','<=' ,$start_end)
			//->whereNotNull('assets_movement_histories.archived')
			->get();
			foreach($data2 as $valArrTwo){
				$data_array[] = array(
					'PO No' => $valArrTwo->po_no,
					'Invoice Date' => $valArrTwo->invoice_date,
					'Invoice No' => $valArrTwo->invoice_no,
					'RR Date' => $valArrTwo->rr_date,
					'Asset Code' => $valArrTwo->asset_code,
					'Digits Code' => $valArrTwo->digits_code,
					'Serial No' =>$valArrTwo->serial_no,
					'Location' =>$valArrTwo->body_location,
					'Status' =>$valArrTwo->status_description,
					'Deployed To' =>$valArrTwo->deployed_to,
					'Item Description' => $valArrTwo->item_description,
					'Value' => $valArrTwo->value,
					'Item Type' =>$valArrTwo->item_type,
					'Quantity' =>$valArrTwo->quantity,
					'Warranty Coverage"' => $valArrTwo->warranty_coverage,
					'History Update By' => $valArrTwo->history_updated_by,
					//'History Deployed To' => $valArrTwo->history_deployed_to,
					'History Location' => $valArrTwo->history_location,
					'History Remarks' => $valArrTwo->history_remarks,
					'Created By' =>$valArrTwo->name,
					'Date Created' =>$valArrTwo->date_created,
				);
			}
		
			//dd($data_array);
			$this->ExportExcelByDate($data_array);
		}

		//Check row for validation
		public function checkRow(Request $request) {
			$data = array();
			$data['status_no'] = 0;
			$data['message']   ='No Item Found!';
			$data['items'] = array();
			$items =  DB::table('assets_inventory_body')->get();
			$checkRowDb = DB::table('assets_inventory_body')->select(DB::raw("CONCAT(assets_inventory_body.digits_code,'-',assets_inventory_body.serial_no) AS codes"))
			->whereNotIn('assets_inventory_body.serial_no', ['N/A','NA'])
			->get()
			->toArray();
			$checkRowDbColumn = array_column($checkRowDb, 'codes');
			$data['items'] = $checkRowDbColumn;
			echo json_encode($data);
			exit;  
		}

		//export ap for recording
		public function getExportApRecording($id, $date) {
			$data = AssetsInventoryBody::leftjoin('statuses', 'assets_inventory_body.statuses_id','=','statuses.id')
					->leftjoin('cms_users', 'assets_inventory_body.created_by', '=', 'cms_users.id')
					->leftjoin('assets', 'assets_inventory_body.item_id', '=', 'assets.id')
					->leftjoin('assets_inventory_header', 'assets_inventory_body.header_id', '=', 'assets_inventory_header.id')
					->select(
						'assets_inventory_body.*',
						'assets_inventory_body.id as aib_id',
						'statuses.*',
						'cms_users.*',
						'assets_inventory_header.*',
						'assets.item_type as itemType',
						'assets_inventory_body.location as body_location',
						'assets_inventory_header.location as location',
						'assets_inventory_header.created_at as date_created'
					)
					->where('assets_inventory_body.header_id', $id)
					->whereDate('assets_inventory_body.created_at', $date)
			        ->get();
			//dd($data);
			$data_array [] = array("Po No",
									"Invoice Date",
									"Invoice No",
									"RR Date",
									"Asset Code",
									"Digits Code",
									"Serial No",
									"Status",
									"Location",
									"Item Condition",
									"Item Description",
									"Value",
									"Item Type",
									"Quantity",
									"Warranty Coverage Year",
									"Created By",
									"Date Created");
			foreach($data as $data_item)
			{
				$data_array[] = array(
					'PO No' => $data_item->po_no,
					'Invoice Date' => $data_item->invoice_date,
					'Invoice No' => $data_item->invoice_no,
					'RR Date' => $data_item->rr_date,
					'Asset Code' => $data_item->asset_code,
					'Digit Code' => $data_item->digits_code,
					'Serial No' =>$data_item->serial_no,
					'Status' =>$data_item->status_description,
					'Location' =>$data_item->body_location,
					'Item Condition' =>$data_item->item_condition,
					'Item Description' => $data_item->item_description,
					'Value' => $data_item->value,
					'Item Type' =>$data_item->itemType,
					'Quantity' =>$data_item->quantity,
					'Warranty Coverage Year' => $data_item->warranty_coverage,
					'Created By' =>$data_item->name,
					'Date Created' =>$data_item->date_created,
				);
			}
			$this->ExportExcelForApRecording($data_array);
		}

		public function ExportExcelForApRecording($assets_data){
			ini_set('max_execution_time', 0);
			ini_set('memory_limit', '4000M');
			try {
				$spreadSheet = new Spreadsheet();
				$spreadSheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
				$spreadSheet->getActiveSheet()->fromArray($assets_data);
				$Excel_writer = new Xlsx($spreadSheet);
				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment;filename="AssetsInventoryApRecording.xlsx"');
				header('Cache-Control: max-age=0');
				ob_end_clean();
				$Excel_writer->save('php://output');
				exit();
			} catch (Exception $e) {
				return;
			}
		}

        public function getExportAssetsHistory() {
            //GET INVENTORY DATA FOR EXPORT
			$inventory = GeneratedAssetsHistories::leftjoin('assets_inventory_body', 'generated_assets_histories.header_id','=','assets_inventory_body.header_id')
	            ->leftjoin('cms_users', 'assets_inventory_body.created_by', '=', 'cms_users.id')
				->select(
				  'generated_assets_histories.*',
				  'assets_inventory_body.*',
				  'cms_users.*',
				  'assets_inventory_body.location as body_location',
				  'assets_inventory_body.deployed_to as body_deployed_to',
				  'assets_inventory_body.deployed_by as body_deployed_by',
				  'assets_inventory_body.deployed_at as body_deployed_at',
				  'assets_inventory_body.created_at as date_created'
				)
				->where('generated_assets_histories.transaction_type','Inventory')
				->get();

			//dd($inventory);
			$data_array [] = array(
									"Transaction Type",
									"Reference No",
									"Po No",
									"Invoice Date",
									"Invoice No",
									"RR Date",
									"Asset Code",
									"Digits Code",
									"Serial No",
									"Location",
									"Status",
									"Deployed To",
									"Item Description",
									"Value",
									"Item Type",
									"Quantity",
									"Warranty Coverage Year",
									"History Update By",
									"History Date Updated",
									"History Location",
									"History Remarks",
									"Created By",
									"Date Created",
									"Deployed Employee Name",
									"Deployed Department",
									"Deployed Request Date",
									"Deployed Company Name",
									"Deployed Position",
									"Deployed Purpose",
									"Deployed Mo Reference No",
									"Deployed Status",
									"Deployed Digits Code",
									"Deployed Asset Code",
									"Deployed Item Description",
									"Deployed Serial No",
									"Deployed Quantity",
									"Deployed Item Cost",
									"Deployed Total Cost",
									"Deployed To",
									"Deployed At",
									"Deployed By",
		                           );
			foreach($inventory as $inventory){
				$data_array[] = array(
					'Transaction Type' => $inventory->transaction_type,
					'Reference No' => $inventory->reference_no,
					'PO No' => $inventory->po_no,
					'Invoice Date' => $inventory->invoice_date,
					'Invoice No' => $inventory->invoice_no,
					'RR Date' => $inventory->rr_date,
					'Asset Code' => $inventory->asset_code,
					'Digits Code' => $inventory->digits_code,
					'Serial No' =>$inventory->serial_no,
					'Location' =>$inventory->body_location,
					'Status' =>$inventory->status_description,
					'Deployed To' =>$inventory->body_deployed_to,
					'Item Description' => $inventory->item_description,
					'Value' => $inventory->value,
					'Item Type' =>$inventory->item_type,
					'Quantity' =>$inventory->quantity,
					'Warranty Coverage"' => $inventory->warranty_coverage,
					'Warranty Coverage"' => $inventory->warranty_coverage,
					'History Update By' => $inventory->history_updated_by,
					'History Location' => $inventory->history_location,
					'History Remarks' => $inventory->history_remarks,
					'Created By' =>$inventory->name,
					'Date Created' =>$inventory->date_created,
					'Deployed Employee Name' => "",
					'Deployed Department' => "",
					'Deployed Request Date' => "",
					'Deployed Company Name' => "",
					'Deployed Position' => "",
					'Deployed Purpose' => "",
					'Deployed Mo Reference No' => "",
					'Deployed Status' => "",
					'Deployed Digits Code' => "",
					'Deployed Asset Code' => "",
					'Deployed Item Description' => "",
					'Deployed Serial No' => "",
					'Deployed Quantity' => "",
					'Deployed Item Cost' => "",
					'Deployed Total Cost' => "",
					'Deployed To' => $inventory->body_deployed_to,
					'Deployed At' => $inventory->body_deployed_at,
					'Deployed By' => $inventory->body_deployed_by,
				);
			}

			//GET DEPLOYED DATA FOR EXPORT
			$deployed = GeneratedAssetsHistories::
			leftjoin('header_request', 'generated_assets_histories.header_id','=','header_request.id')
			->leftjoin('mo_body_request', 'generated_assets_histories.header_id','=','mo_body_request.header_request_id')
			->leftjoin('assets_inventory_body', 'mo_body_request.inventory_id','=','assets_inventory_body.id')
			->leftjoin('cms_users', 'mo_body_request.created_by', '=', 'cms_users.id')
			->leftjoin('statuses', 'mo_body_request.status_id', '=', 'statuses.id')
			->leftjoin('request_type', 'header_request.purpose', '=', 'request_type.id')
			->leftjoin('condition_type', 'header_request.conditions', '=', 'condition_type.id')
			->leftjoin('employees', 'header_request.employee_name', '=', 'employees.id')
			->leftjoin('companies', 'header_request.company_name', '=', 'companies.id')
			->leftjoin('departments', 'header_request.department', '=', 'departments.id')
			->leftjoin('positions', 'header_request.position', '=', 'positions.id')
				->select(
				  'generated_assets_histories.*',
				  'mo_body_request.*',
				  'mo_body_request.digits_code as mo_digits_code',
				  'mo_body_request.asset_code as mo_asset_code',
				  'mo_body_request.item_description as mo_item_description',
				  'mo_body_request.serial_no as mo_serial_no',
				  'mo_body_request.quantity as mo_quantity',
				  'assets_inventory_body.*',
				  'cms_users.*',
				  'assets_inventory_body.location as body_location',
				  'assets_inventory_body.deployed_to as body_deployed_to',
				  'assets_inventory_body.deployed_by as body_deployed_by',
				  'assets_inventory_body.deployed_at as body_deployed_at',
				  'assets_inventory_body.created_at as date_created',
				  'header_request.*',
			  	  'header_request.id as requestid',
				  'header_request.created_at as created',
				  'request_type.*',
				  'condition_type.*',
				  'employees.bill_to as employee_name',
				  'companies.company_name as company_name',
				  'departments.department_name as department',
				  'statuses.status_description as status_description'
				)
				->where('generated_assets_histories.transaction_type','Deployed')
				->get();
				
			//FOREACH DEPLOYED
			foreach($deployed as $deployed){
				$data_array[] = array(
					'Transaction Type' => $deployed->transaction_type,
					'Reference No' => $deployed->reference_no,
					'PO No' => "",
					'Invoice Date' => "",
					'Invoice No' => "",
					'RR Date' => "",
					'Asset Code' => "",
					'Digits Code' => "",
					'Serial No' => "",
					'Location' => "",
					'Status' => "",
					'Deployed To' => "",
					'Item Description' => "",
					'Value' => "",
					'Item Type' => "",
					'Quantity' => "",
					'Warranty Coverage"' => "",
					'Warranty Coverage"' => "",
					'History Update By' => "",
					'History Location' => "",
					'History Remarks' => "",
					'Created By' => "",
					'Date Created' => $deployed->date_created,
					'Deployed Employee Name' => $deployed->employee_name,
					'Deployed Department' => $deployed->department,
					'Deployed Request Date' => $deployed->created,
					'Deployed Company Name' => $deployed->company_name,
					'Deployed Position' => $deployed->position,
					'Deployed Purpose' => $deployed->request_description,
					'Deployed Mo Reference No' => $deployed->mo_reference_number,
					'Deployed Status' => $deployed->status_description,
					'Deployed Digits Code' => $deployed->mo_digits_code,
					'Deployed Asset Code' => $deployed->mo_asset_code,
					'Deployed Item Description' => $deployed->mo_item_description,
					'Deployed Serial No' => $deployed->serial_no,
					'Deployed Quantity' => $deployed->mo_quantity,
					'Deployed Item Cost' => $deployed->unit_cost,
					'Deployed Total Cost' => $deployed->total_unit_cost,
					'Deployed To' => $deployed->body_deployed_to,
					'Deployed At' => $deployed->body_deployed_at,
					'Deployed By' => $deployed->body_deployed_by,
				);
			}

			//dd($data_array);
			$this->ExportAssetsHistory($data_array);
		}

		public function ExportAssetsHistory($assets_data){
			ini_set('max_execution_time', 0);
			ini_set('memory_limit', '4000M');
			try {
				$spreadSheet = new Spreadsheet();
				$spreadSheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
				$spreadSheet->getActiveSheet()->fromArray($assets_data);
				$Excel_writer = new Xlsx($spreadSheet);
				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment;filename="AssetsMovementHistory.xlsx"');
				header('Cache-Control: max-age=0');
				ob_end_clean();
				$Excel_writer->save('php://output');
				exit();
			} catch (Exception $e) {
				return;
			}
		}


	}