<?php namespace App\Http\Controllers;

	use Session;
	use Request;
	use DB;
	use CRUDBooster;
	use App\AssetsInventoryHeader;
	use App\AssetsHeaderImages;
	use App\AssetsInventoryBody;
	use App\CommentsGoodDefect;
	use App\GoodDefectLists;
	use App\Exports\ExportMultipleSheet;
	use Maatwebsite\Excel\Facades\Excel;
	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Reader\Exception;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
	use PhpOffice\PhpSpreadsheet\IOFactory;
	use Carbon\Carbon;
	
	class AdminAssetsInventoryBodyController extends \crocodicstudio\crudbooster\controllers\CBController {
		private static $apiContext;
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
			$this->button_edit = true;
			$this->button_delete = false;
			$this->button_detail = true;
			$this->button_show = true;
			$this->button_filter = true;
			$this->button_import = false;
			$this->button_export = false;
			$this->table = "assets_inventory_body";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Asset Code","name"=>"asset_code"];
			$this->col[] = ["label"=>"Digits Code","name"=>"digits_code"];
			$this->col[] = ["label"=>"Serial No","name"=>"serial_no"];
			$this->col[] = ["label"=>"Status","name"=>"statuses_id","join"=>"statuses,status_description"];
			$this->col[] = ["label"=>"Deployed To","name"=>"deployed_to"];
			$this->col[] = ["label"=>"Location","name"=>"location","join"=>"warehouse_location_model,location"];
			$this->col[] = ["label"=>"Item Condition","name"=>"item_condition"];
			$this->col[] = ["label"=>"Item Description","name"=>"item_description"];
			$this->col[] = ["label"=>"Value","name"=>"value","callback_php"=>'number_format($row->value)'];
			$this->col[] = ["label"=>"RR Date","name"=>"header_id","join"=>"assets_inventory_header,rr_date","visible" => false];
			$this->col[] = ["label"=>"Quantity","name"=>"quantity"];
			$this->col[] = ["label"=>"Date Created","name"=>"created_at"];
			// $this->col[] = ["label"=>"Warranty Coverage Year","name"=>"warranty_coverage"];
			// $this->col[] = ["label"=>"Item Photo","name"=>"item_id","join"=>"assets,image","image"=>1];
			
			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'Label Name','name'=>'created_at','type'=>'hidden'];
			# END FORM DO NOT REMOVE THIS LINE

			# OLD START FORM
			//$this->form = [];
			//$this->form[] = ["label"=>"Header Id","name"=>"header_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"header,id"];
			//$this->form[] = ["label"=>"Statuses Id","name"=>"statuses_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"statuses,id"];
			//$this->form[] = ["label"=>"Digits Code","name"=>"digits_code","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Item Description","name"=>"item_description","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Value","name"=>"value","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Item Type","name"=>"item_type","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Quantity","name"=>"quantity","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Serial No","name"=>"serial_no","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Item Photo","name"=>"item_photo","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Asset Code","name"=>"asset_code","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Barcode","name"=>"barcode","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
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
	        $this->addaction = array(
				$this->addaction[] = ['url'=>CRUDBooster::mainpath('generate-barcode-single/[id]'),'icon'=>'fa fa-barcode','color'=>'default']
			);


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
			    $this->index_button[] = ["label"=>"Export Data","icon"=>"fa fa-upload","url"=>CRUDBooster::mainpath('asset-lists-export'),"color"=>"primary"];
			// 	$this->index_button[] = ["label"=>"Add Inventory","icon"=>"fa fa-files-o","url"=>CRUDBooster::adminPath('assets_inventory_header/add-inventory'),"color"=>"success"];
			// 	//$this->index_button[] = ["label"=>"Return Request","icon"=>"fa fa-files-o","url"=>CRUDBooster::mainpath('add-return'),"color"=>"success"];

			// 	//$this->index_button[] = ["label"=>"Transfer Request","icon"=>"fa fa-files-o","url"=>CRUDBooster::mainpath('add-transfer'),"color"=>"success"];

			// 	//$this->index_button[] = ["label"=>"Disposal Request","icon"=>"fa fa-files-o","url"=>CRUDBooster::mainpath('add-disposal'),"color"=>"success"];
			
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
			$this->load_js[] = asset("jquery-fat-zoom/js/zoom.js");
			//$this->load_js[] = asset("sweetalert2/sweetalert2.js");
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
			$this->load_css[] = asset("sweetalert2/sweetalert2.css");
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
			$query->whereNull('assets_inventory_body.archived')->orderBy('assets_inventory_body.id', 'DESC');    
	            
	    }

	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate row of index table html 
	    | ---------------------------------------------------------------------- 
	    |
	    */    
	    public function hook_row_index($column_index,&$column_value) {	        
	    	//Your code here
			$for_approval  =    DB::table('statuses')->where('id', 20)->value('status_description');
			$available  =  		DB::table('statuses')->where('id', 6)->value('status_description');
			$reserved  =  		DB::table('statuses')->where('id', 2)->value('status_description');
			$deployed  =  		DB::table('statuses')->where('id', 3)->value('status_description');
			$defective  =  		DB::table('statuses')->where('id', 23)->value('status_description');

			if($column_index == 4){
				if($column_value == $for_approval){
					$column_value = '<span class="label label-success">'.$for_approval.'</span>';
				}else if($column_value == $available){
					$column_value = '<span class="label label-success">'.$available.'</span>';
				}else if($column_value == $reserved){
					$column_value = '<span class="label label-warning">'.$reserved.'</span>';
				}else if($column_value == $deployed){
					$column_value = '<span class="label label-danger">'.$deployed.'</span>';
				}else if($column_value == $defective){
					$column_value = '<span class="label label-danger">'.$defective.'</span>';
				}
			}

			if($column_index == 7){
				if($column_value == "Good"){
					$column_value = '<span class="label label-success">GOOD</span>';
				}else if($column_value == "Defective"){
					$column_value = '<span class="label label-danger">DEFECTIVE</span>';
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
			app(\App\Http\Controllers\AdminAssetsInventoryHeaderController::class)->hook_before_add($postdata);

	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command after add public static function called 
	    | ---------------------------------------------------------------------- 
	    | @id = last insert id
	    | 
	    */
	    public function hook_after_add($id) {        
	       

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
	        $fields = Request::all();
			$id =  $fields['request_type_id'];
			$digits_code =  $fields['digits_code'];
			$asset_code =  $fields['asset_code'];
			$item_condition =  $fields['item_condition'];
			$comments =  $fields['comments'];
			$other_comment =  $fields['other_comment'];
		
			if($item_condition === "Good"){
               $status = 6;
			}else{
				$status = 23;
			}
			//dd($item_condition);
			DB::table('assets_inventory_body')->where('id', $id)
			->update([
				'item_condition' => $item_condition,
				'statuses_id' => $status,
				'updated_by' => CRUDBooster::myId()
			]);

			//save defect and good comments
			$container = [];
			$containerSave = [];
			foreach($comments as $key => $val){
				$container['arf_number'] = NULL;
				$container['digits_code'] = $digits_code;
				$container['asset_code'] = $asset_code;
				$container['comments'] = $val;
				$container['other_comment'] = $other_comment;
				$container['users'] = CRUDBooster::myId();
				$container['created_at'] = date('Y-m-d H:i:s');
				$containerSave[] = $container;
			}
			CommentsGoodDefect::insert($containerSave);
			CRUDBooster::redirect(CRUDBooster::mainpath('edit/'.$id), trans("Edit Successfully!"), 'success');
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
		public function getAddInventory() {

			if(!CRUDBooster::isCreate() && $this->global_privilege == false) {
				CRUDBooster::redirect(CRUDBooster::adminPath(), trans('crudbooster.denied_access'));
			}

			$this->cbLoader();

			$data['page_title'] = 'Add Inventory';

			return $this->view("assets.add-inventory", $data);

		}
	
		//Get Invetory Edit
		public function getEdit($id){
			$this->cbLoader();
            if(!CRUDBooster::isRead() && $this->global_privilege==FALSE) {    
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }
			$data = [];
			$data['page_title'] = 'Edit Assets Inventory';

			$data['Body'] = AssetsInventoryBody::select(
				'assets_inventory_body.*',
				'assets_inventory_body.id as bodyId'
			  )->where('assets_inventory_body.id', $id)->first();

			$comments = CommentsGoodDefect::
			leftjoin('cms_users', 'comments_good_defect_tbl.users', '=', 'cms_users.id')
			->select(
				'comments_good_defect_tbl.*',
				'comments_good_defect_tbl.id as bodyId',
				'cms_users.name'
			  )
			  ->where('comments_good_defect_tbl.digits_code', $data['Body']->digits_code)
			  ->where('comments_good_defect_tbl.asset_code', $data['Body']->asset_code)
			  ->where('comments_good_defect_tbl.comments', '!=' ,'OTHERS')
			  ->get();
			$other_comment = CommentsGoodDefect::
			leftjoin('cms_users', 'comments_good_defect_tbl.users', '=', 'cms_users.id')
			->select(DB::raw("CONCAT(comments_good_defect_tbl.comments ,'/', comments_good_defect_tbl.other_comment) AS comments, comments_good_defect_tbl.asset_code, cms_users.name")
			  )
			  ->where('comments_good_defect_tbl.digits_code', $data['Body']->digits_code)
			  ->where('comments_good_defect_tbl.asset_code', $data['Body']->asset_code)
			  ->where('comments_good_defect_tbl.comments', '=' ,'OTHERS')
			  ->get();
			$mergeData = collect();
			$mergeData =  $comments->toBase()->merge($other_comment);
			$data['comments'] = $mergeData;
			
			$data['good_defect_lists'] = GoodDefectLists::all();
            //dd($data['comments']);
			  return $this->view("assets.edit_assets_inventory", $data);
		}
		public function getDetail($id){
			$this->cbLoader();
            if(!CRUDBooster::isRead() && $this->global_privilege==FALSE) {    
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

			$data = [];
			$data['page_title'] = 'View Item Lists';

			$data['Body'] = AssetsInventoryBody::leftjoin('statuses', 'assets_inventory_body.statuses_id','=','statuses.id')
					->leftjoin('assets_inventory_header', 'assets_inventory_body.header_id','=','assets_inventory_header.id')
					->leftjoin('cms_users', 'assets_inventory_body.created_by', '=', 'cms_users.id')
					->leftjoin('cms_users as cms_users_updated_by', 'assets_inventory_body.updated_by', '=', 'cms_users_updated_by.id')
					->leftjoin('assets', 'assets_inventory_body.item_id', '=', 'assets.id')
					->leftjoin('warehouse_location_model', 'assets_inventory_body.location', '=', 'warehouse_location_model.id')
					->select(
					'assets_inventory_header.*',
					'assets_inventory_body.*',
					'assets_inventory_body.id as aib_id',
					'statuses.*',
					'cms_users.*',
					'assets.item_type as itemType',
					'assets.image as itemImage',
					'assets_inventory_body.created_at as date_created',
					'assets_inventory_body.updated_at as date_updated',
					'assets_inventory_header.location as header_location',
					'warehouse_location_model.location as body_location',
					'cms_users_updated_by.name as updated_by'
					)->where('assets_inventory_body.id', $id)
				     ->first();

					 $data['header_images'] = AssetsHeaderImages::select(
						'assets_header_images.*'
					  )->where('assets_header_images.header_id', $data['Body']['header_id'])
					  ->get();
				   //dd($data);
			return $this->view("assets.assets_inventory_body_detail", $data);
		}
		//Generate Assets Inventory Barcode
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
				header('Content-Disposition: attachment;filename="AssetLists.xlsx"');
				header('Cache-Control: max-age=0');
				ob_end_clean();
				$Excel_writer->save('php://output');
				exit();
			} catch (Exception $e) {
				return;
			}
		}

		public function getExportAssetsBody() {
			$data = AssetsInventoryBody::leftjoin('statuses', 'assets_inventory_body.statuses_id','=','statuses.id')
			->leftjoin('cms_users', 'assets_inventory_body.created_by', '=', 'cms_users.id')
			->leftjoin('assets', 'assets_inventory_body.item_id', '=', 'assets.id')
			->leftjoin('assets_inventory_header', 'assets_inventory_body.header_id', '=', 'assets_inventory_header.id')
			->select(
			  'assets_inventory_body.*',
			  'assets_inventory_body.id as aib_id',
			  'statuses.*',
			  'cms_users.*',
			  'assets.item_type as itemType',
			  'assets_movement_body.location as body_location',
			  'assets_inventory_header.location as location',
			  'assets_inventory_body.created_at as date_created'
			)->get();
			$data_array [] = array("Asset Code",
									"Digits Code",
									"Serial No",
									"Status",
									"Location",
									"Item Condition",
									"Item Description",
									"Value","Item Type",
									"Quantity",
									"Warranty Coverage Year",
									"Created By",
									"Date Created");
			foreach($data as $data_item)
			{
				$data_array[] = array(
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
					'Warranty Coverage Year' =>$data_item->warranty_coverage,
					'Created By' =>$data_item->name,
					'Date Created' =>$data_item->date_created,
				);
			}
			$this->ExportExcel($data_array);
		}

		public function getAssetListsExport() 
		{
			return Excel::download(new ExportMultipleSheet, 'asset_lists.xlsx');
		}

	}