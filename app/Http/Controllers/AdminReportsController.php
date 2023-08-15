<?php namespace App\Http\Controllers;

	use Session;
	use Request;
	use DB;
	use CRUDBooster;
	use App\BodyRequest;
	use App\HeaderRequest;
	use App\MoveOrder;
	use App\Models\ReturnTransferAssets;
	use App\Models\GeneratedAssetsReports;
	use Maatwebsite\Excel\Facades\Excel;
	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Reader\Exception;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
	use PhpOffice\PhpSpreadsheet\IOFactory;
	use Illuminate\Support\Facades\Log;
	use Illuminate\Support\Facades\Redirect;
	use Illuminate\Contracts\Cache\LockTimeoutException;
	use App\Exports\ExportMultipleByApprover;
	use App\Exports\ExportReportAssetsList;
	use Carbon\Carbon;
	//use DataTables;

	class AdminReportsController extends \crocodicstudio\crudbooster\controllers\CBController {

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "employee_name";
			$this->limit = "20";
			$this->orderby = "id,desc";
			$this->global_privilege = false;
			$this->button_table_action = true;
			$this->button_bulk_action = true;
			$this->button_action_style = "button_icon";
			$this->button_add = false;
			$this->button_edit = true;
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
			$this->col[] = ["label"=>"Reference Number","name"=>"reference_number"];
			$this->col[] = ["label"=>"Mo Reference Number","name"=>"mo_reference_number"];
			$this->col[] = ["label"=>"Status Id","name"=>"status_id","join"=>"statuses,id"];
			$this->col[] = ["label"=>"Employee Name","name"=>"employee_name"];
			$this->col[] = ["label"=>"Company Name","name"=>"company_name"];
			$this->col[] = ["label"=>"Position","name"=>"position"];
			$this->col[] = ["label"=>"Department","name"=>"department"];
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
		//customize index
		public function getIndex(Request $request) {
			//First, Add an auth
			// dd(Request::all());
			if(!CRUDBooster::isView()) CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));
			
			//Create your own query 
			$data = [];
			$data['page_title'] = 'Request Assets Status Reports';

			// $result_one = BodyRequest::arrayone();
			// $result_two = ReturnTransferAssets::arraytwo();
            // $suppliesMarketing = [];
			// $suppliesMarketingCon = [];
	
			// foreach($result_one as $smVal){
			// 	$suppliesMarketingCon['id'] = $smVal['requestid'];
			// 	$suppliesMarketingCon['reference_number'] = $smVal['reference_number'];
			// 	$suppliesMarketingCon['requested_by'] = $smVal['requestedby'];
			// 	$suppliesMarketingCon['department'] = $smVal['department'] ? $smVal['department'] : $smVal['store_branch'];
			// 	$suppliesMarketingCon['store_branch'] = $smVal['store_branch'] ? $smVal['store_branch'] : $smVal['department'];
			// 	$suppliesMarketingCon['transaction_type'] = "REQUEST";
			// 	$bodyStatus = $smVal['body_statuses_description'] ? $smVal['body_statuses_description'] : $smVal['status_description'];
			// 	if(in_array($smVal['request_type_id'], [6,7])){
			// 		$suppliesMarketingCon['status'] = $smVal['status_description'];
			// 		$suppliesMarketingCon['description'] = $smVal['body_description'];
			// 		$suppliesMarketingCon['request_quantity'] = $smVal['body_quantity'];
			// 		$suppliesMarketingCon['request_type'] = $smVal['body_category_id'];
			// 		$suppliesMarketingCon['mo_reference'] = $smVal['body_mo_so_num'];
			// 		$suppliesMarketingCon['mo_item_code'] = $smVal['body_digits_code'];
			// 		$suppliesMarketingCon['mo_item_description'] = $smVal['body_description'];
			// 		$suppliesMarketingCon['mo_qty_serve_qty'] = $smVal['serve_qty'];
			// 	}else{
			// 		$suppliesMarketingCon['status'] = isset($smVal['mo_reference_number']) ? $smVal['mo_statuses_description'] : $bodyStatus;
			// 		$suppliesMarketingCon['description'] = $smVal['body_description'];
			// 		$suppliesMarketingCon['request_quantity'] = $smVal['body_quantity'];
			// 		$suppliesMarketingCon['request_type'] = $smVal['body_category_id'];
			// 		$suppliesMarketingCon['mo_reference'] = $smVal['mo_reference_number'];
			// 		$suppliesMarketingCon['mo_item_code'] = $smVal['digits_code'];
			// 		$suppliesMarketingCon['mo_item_description'] = $smVal['item_description'];
			// 		$suppliesMarketingCon['mo_qty_serve_qty'] = $smVal['quantity'];
			// 	}
			// 	$suppliesMarketingCon['requested_date'] = $smVal['created_at'];
			// 	$suppliesMarketingCon['transacted_by'] = $smVal['taggedby'];
			// 	$suppliesMarketingCon['transacted_date'] = $smVal['transacted_date'];
			// 	$suppliesMarketing[] = $suppliesMarketingCon;
			// }

			// $returnTransfer = [];
			// $returnTransferCon = [];
			// foreach($result_two as $rtVal){
			// 	$returnTransferCon['id'] = $rtVal['requestid'];
			// 	$returnTransferCon['reference_number'] = $rtVal['reference_no'];
			// 	$returnTransferCon['requested_by'] = $rtVal['employee_name'];
			// 	$returnTransferCon['department'] = $rtVal['department_name'] ? $rtVal['department_name'] : $rtVal['store_branch'];
			// 	$returnTransferCon['store_branch'] = $rtVal['store_branch'] ? $rtVal['store_branch'] : $rtVal['department_name'];
			// 	$returnTransferCon['status'] = $rtVal['status_description'];
			// 	$returnTransferCon['description'] = $rtVal['description'];
			// 	$returnTransferCon['request_quantity'] = $rtVal['quantity'];
			// 	$returnTransferCon['transaction_type'] = $rtVal['request_type'];
			// 	$returnTransferCon['request_type'] = $rtVal['request_name'];
			// 	$returnTransferCon['mo_reference'] = $rtVal['reference_no'];
			// 	$returnTransferCon['mo_item_code'] = $rtVal['digits_code'];
			// 	$returnTransferCon['mo_item_description'] = $rtVal['description'];
			// 	$returnTransferCon['mo_qty_serve_qty'] = $rtVal['quantity'];
			// 	$returnTransferCon['requested_date'] = $rtVal['requested_date'];
			// 	$returnTransferCon['transacted_by'] = $rtVal['receivedby'];
			// 	$returnTransferCon['transacted_date'] = $rtVal['transacted_date'];
			// 	$returnTransfer[] = $returnTransferCon;
			// }
			// //dd($returnTransfer);
			// $data['finalData'] = array_merge($suppliesMarketing, $returnTransfer);

			$data['categories'] = DB::table('requests')->whereIn('id', [1,5,6,7])->where('status', 'ACTIVE')
													   ->orderby('request_name', 'asc')
													   ->get();

			return $this->view('assets.purchasing-reports',$data);
		}

		public function searchApplicant(Request $request){
			ini_set('memory_limit','-1');
            ini_set('max_execution_time', 0);
			$fields = Request::all();
			$from = $fields['from'];
			$to = $fields['to'];
		    $category = $fields['category'];
			$overwrite = $fields['overwrite'];
			$data = [];
            $filters = [];
			$data['page_title'] = 'Export Request/Return and Transfer Reports';

			$requestRes = BodyRequest::requestfilter($fields);
			$returnTransferRes = ReturnTransferAssets::returnfilter($fields);
			$suppliesMarketing = [];
			$suppliesMarketingCon = [];
			if($overwrite == 1){
				GeneratedAssetsReports::truncate(); 
			}
			foreach($requestRes as $smVal){
				$suppliesMarketingCon['id'] = $smVal['requestid'];
				$suppliesMarketingCon['reference_number'] = $smVal['reference_number'];
				$suppliesMarketingCon['requested_by'] = $smVal['requestedby'];
				$suppliesMarketingCon['department'] = $smVal['department'] ? $smVal['department'] : $smVal['store_branch'];
				$suppliesMarketingCon['store_branch'] = $smVal['store_branch'] ? $smVal['store_branch'] : $smVal['department'];
				$suppliesMarketingCon['transaction_type'] = "REQUEST";
				$bodyStatus = $smVal['body_statuses_description'] ? $smVal['body_statuses_description'] : $smVal['status_description'];
				if(in_array($smVal['request_type_id'], [6,7])){
					$suppliesMarketingCon['status'] = $smVal['status_description'];
					$suppliesMarketingCon['body_digits_code'] = $smVal['body_digits_code'];
					$suppliesMarketingCon['description'] = $smVal['body_description'];
					$suppliesMarketingCon['request_quantity'] = $smVal['body_quantity'];
					$suppliesMarketingCon['request_type'] = $smVal['body_category_id'];
					$suppliesMarketingCon['mo_reference'] = $smVal['body_mo_so_num'];
					$suppliesMarketingCon['mo_item_code'] = $smVal['body_digits_code'];
					$suppliesMarketingCon['mo_item_description'] = $smVal['body_description'];
					$suppliesMarketingCon['mo_qty_serve_qty'] = $smVal['serve_qty'];
				}else{
					$suppliesMarketingCon['status'] = isset($smVal['mo_reference_number']) ? $smVal['mo_statuses_description'] : $bodyStatus;
					$suppliesMarketingCon['body_digits_code'] = $smVal['body_digits_code'];
					$suppliesMarketingCon['description'] = $smVal['body_description'];
					$suppliesMarketingCon['request_quantity'] = $smVal['body_quantity'];
					$suppliesMarketingCon['request_type'] = $smVal['body_category_id'];
					$suppliesMarketingCon['mo_reference'] = $smVal['mo_reference_number'];
					$suppliesMarketingCon['mo_item_code'] = $smVal['mo_digits_code'];
					$suppliesMarketingCon['mo_item_description'] = $smVal['mo_item_description'];
					$suppliesMarketingCon['mo_qty_serve_qty'] = $smVal['quantity'];
				}
				$suppliesMarketingCon['requested_date'] = $smVal['created_at'];
				$suppliesMarketingCon['approved_by'] = $smVal['approvedby'];
				$suppliesMarketingCon['approved_at'] = $smVal['approved_at'];
				$suppliesMarketingCon['transacted_by'] = $smVal['taggedby'];
				// $suppliesMarketingCon['recommended_by']          = $smVal['recommendedby'];
				// $suppliesMarketingCon['recommended_at']          = $smVal['recommended_at'];
				// $suppliesMarketingCon['it_comments']             = $smVal['it_comments'];
				$suppliesMarketingCon['transacted_date'] = $smVal['transacted_date'];
				$suppliesMarketing[] = $suppliesMarketingCon;
			}

			$returnTransfer = [];
			$returnTransferCon = [];
			foreach($returnTransferRes as $rtVal){
				$returnTransferCon['id'] = $rtVal['requestid'];
				$returnTransferCon['reference_number'] = $rtVal['reference_no'];
				$returnTransferCon['requested_by'] = $rtVal['employee_name'];
				$returnTransferCon['department'] = $rtVal['department_name'] ? $rtVal['department_name'] : $rtVal['store_branch'];
				$returnTransferCon['store_branch'] = $rtVal['store_branch'] ? $rtVal['store_branch'] : $rtVal['department_name'];
				$returnTransferCon['status'] = $rtVal['status_description'];
				$returnTransferCon['body_digits_code']    = $rtVal['r_digits_code'];
				$returnTransferCon['description'] = $rtVal['description'];
				$returnTransferCon['request_quantity'] = $rtVal['quantity'];
				$returnTransferCon['transaction_type'] = $rtVal['request_type'];
				$returnTransferCon['request_type'] = $rtVal['request_name'];
				$returnTransferCon['mo_reference'] = $rtVal['reference_no'];
				$returnTransferCon['mo_item_code'] = $rtVal['digits_code'];
				$returnTransferCon['mo_item_description'] = $rtVal['description'];
				$returnTransferCon['mo_qty_serve_qty'] = $rtVal['quantity'];
				$returnTransferCon['requested_date'] = $rtVal['requested_date'];
				$returnTransferCon['approved_by'] = $rtVal['approved_by_return'];
				$returnTransferCon['approved_at'] = $rtVal['approved_date'];
				// $returnTransferCon['recommended_by']      = NULL;
				// $returnTransferCon['recommended_at']      = NULL;
				// $returnTransferCon['it_comments']         = NULL;
				$returnTransferCon['transacted_by'] = $rtVal['receivedby'];
				$returnTransferCon['transacted_date'] = $rtVal['transacted_date'];
				$returnTransfer[] = $returnTransferCon;
			}
			//dd($returnTransfer);
			$data['result'] = array_merge($suppliesMarketing, $returnTransfer);
			$insertData = [];
			$container = [];
			foreach($data['result'] as $key => $val){
				$container['reference_number'] = $val['reference_number'];
				$container['requested_by'] = $val['requested_by'];
				$container['department'] = $val['department'];
				$container['store_branch'] = $val['store_branch'];
				$container['transaction_type'] = $val['transaction_type'];
				$container['status'] = $val['status'];
				$container['digits_code']    = $val['body_digits_code'];
				$container['description'] = $val['description'];
				$container['request_quantity'] = $val['request_quantity'];
				$container['request_type'] = $val['request_type'];
				$container['mo_reference'] = $val['mo_reference'];
				$container['mo_item_code'] = $val['mo_item_code'];
				$container['mo_item_description'] = $val['mo_item_description'];
				$container['mo_qty_serve_qty'] = $val['mo_qty_serve_qty'];
				$container['requested_date'] = $val['requested_date'];
				$container['approved_by'] = $val['approved_by'];
				$container['approved_at'] = $val['approved_at'];
				$container['transacted_by'] = $val['transacted_by'];
				// $container['recommended_by'] = $val['recommended_by'];
				// $container['recommended_at'] = $val['recommended_at'];
				// $container['it_comments'] = $val['it_comments'];
				$container['transacted_date'] = $val['transacted_date'];
				$insertData[] = $container;
			}

			$insert_data = collect($insertData);
			$chunks = $insert_data->chunk(500);

			foreach ($chunks as $chunk){
			 GeneratedAssetsReports::insert($chunk->toArray());
			}

			$data['from']          = $from;
			$data['to']            = $to;
			$data['category']      = $category;
			$data['filters']       = $filters;
		
			return $this->view("assets.purchasing-reports-view", $data);
			
		}

		public function requestExport(Request $request){
			$fields = Request::all();
			$filename = $fields['filename'];
			return Excel::download(new ExportMultipleByApprover($fields), $filename.'.xlsx');

		}

		public function getReports(){
			ini_set('memory_limit','-1');
            ini_set('max_execution_time', 0);
			$result_one = BodyRequest::arrayone();
			$result_two = ReturnTransferAssets::arraytwo();
            $suppliesMarketing = [];
			$suppliesMarketingCon = [];
	
			foreach($result_one as $smVal){
				$suppliesMarketingCon['id'] = $smVal['requestid'];
				$suppliesMarketingCon['reference_number'] = $smVal['reference_number'];
				$suppliesMarketingCon['requested_by'] = $smVal['requestedby'];
				$suppliesMarketingCon['department'] = $smVal['department'] ? $smVal['department'] : $smVal['store_branch'];
				$suppliesMarketingCon['store_branch'] = $smVal['store_branch'] ? $smVal['store_branch'] : $smVal['department'];
				$suppliesMarketingCon['transaction_type'] = "REQUEST";
				$bodyStatus = $smVal['body_statuses_description'] ? $smVal['body_statuses_description'] : $smVal['status_description'];
				if(in_array($smVal['request_type_id'], [6,7])){
					$suppliesMarketingCon['status'] = $smVal['status_description'];
					$suppliesMarketingCon['body_digits_code'] = $smVal['body_digits_code'];
					$suppliesMarketingCon['description'] = $smVal['body_description'];
					$suppliesMarketingCon['request_quantity'] = $smVal['body_quantity'];
					$suppliesMarketingCon['request_type'] = $smVal['body_category_id'];
					$suppliesMarketingCon['mo_reference'] = $smVal['body_mo_so_num'];
					$suppliesMarketingCon['mo_item_code'] = $smVal['body_digits_code'];
					$suppliesMarketingCon['mo_item_description'] = $smVal['body_description'];
					$suppliesMarketingCon['mo_qty_serve_qty'] = $smVal['serve_qty'];
				}else{
					$suppliesMarketingCon['status']              = isset($smVal['mo_reference_number']) ? $smVal['mo_statuses_description'] : $bodyStatus;
					$suppliesMarketingCon['body_digits_code']    = $smVal['body_digits_code'];
					$suppliesMarketingCon['description']         = $smVal['body_description'];
					$suppliesMarketingCon['request_quantity']    = $smVal['body_quantity'];
					$suppliesMarketingCon['request_type']        = $smVal['body_category_id'];
					$suppliesMarketingCon['mo_reference']        = $smVal['mo_reference_number'];
					$suppliesMarketingCon['mo_item_code']        = $smVal['digits_code'];
					$suppliesMarketingCon['mo_item_description'] = $smVal['item_description'];
					$suppliesMarketingCon['mo_qty_serve_qty']    = $smVal['quantity'];
				}
				$suppliesMarketingCon['requested_date']          = $smVal['created_at'];
				// $suppliesMarketingCon['recommended_by']          = $smVal['recommendedby'];
				// $suppliesMarketingCon['recommended_at']          = $smVal['recommended_at'];
				// $suppliesMarketingCon['it_comments']             = $smVal['it_comments'];
				$suppliesMarketingCon['transacted_by']           = $smVal['taggedby'];
				$suppliesMarketingCon['transacted_date']         = $smVal['transacted_date'];
				$suppliesMarketing[] = $suppliesMarketingCon;
			}

			$returnTransfer = [];
			$returnTransferCon = [];
			foreach($result_two as $rtVal){
				$returnTransferCon['id']                  = $rtVal['requestid'];
				$returnTransferCon['reference_number']    = $rtVal['reference_no'];
				$returnTransferCon['requested_by']        = $rtVal['employee_name'];
				$returnTransferCon['department']          = $rtVal['department_name'] ? $rtVal['department_name'] : $rtVal['store_branch'];
				$returnTransferCon['store_branch']        = $rtVal['store_branch'] ? $rtVal['store_branch'] : $rtVal['department_name'];
				$returnTransferCon['status']              = $rtVal['status_description'];
				$returnTransferCon['body_digits_code']    = $rtVal['r_digits_code'];
				$returnTransferCon['description']         = $rtVal['description'];
				$returnTransferCon['request_quantity']    = $rtVal['quantity'];
				$returnTransferCon['transaction_type']    = $rtVal['request_type'];
				$returnTransferCon['request_type']        = $rtVal['request_name'];
				$returnTransferCon['mo_reference']        = $rtVal['reference_no'];
				$returnTransferCon['mo_item_code']        = $rtVal['digits_code'];
				$returnTransferCon['mo_item_description'] = $rtVal['description'];
				$returnTransferCon['mo_qty_serve_qty']    = $rtVal['quantity'];
				$returnTransferCon['requested_date']      = Carbon::parse($rtVal['requested_date']);
				// $returnTransferCon['recommended_by']      = NULL;
				// $returnTransferCon['recommended_at']      = NULL;
				// $returnTransferCon['it_comments']         = NULL;
				$returnTransferCon['transacted_by']       = $rtVal['receivedby'];
				$returnTransferCon['transacted_date']     = $rtVal['transacted_date'];
				$returnTransfer[] = $returnTransferCon;
			}

			//dd($suppliesMarketing, $returnTransfer);
	
			$data['finalData'] = array_merge($suppliesMarketing, $returnTransfer);

			return datatables($data['finalData'])
			->addIndexColumn()
			->addColumn('action', function($row){
                if($row['transaction_type'] === "RETURN" || $row['transaction_type'] === "TRANSFER"){
					$actionBtn = '<a class="btn btn-primary btn-xs" href="'.CRUDBooster::adminpath("return_transfer_assets_header/detail/".$row["id"]).'"><i class="fa fa-eye"></i></a>';
				}else{
					$actionBtn = '<a class="btn btn-primary btn-xs" href="'.CRUDBooster::adminpath("request_history/detail/".$row["id"]).'"><i class="fa fa-eye"></i></a>';
				}
				
				return $actionBtn;
			})
			->editColumn('requested_date', function ($row) {
				return [
				   'display' => e($row['requested_date']->format('Y-m-d')),
				];
			 })
			->rawColumns(['action'])
			->make(true);
		}

		public function getGeneratedReports(){
           $reports = GeneratedAssetsReports::select('*')->get();
		   return datatables($reports)
			->addIndexColumn()
			->rawColumns(['action'])
			->make(true);
		}

		public function exportReportAssetsList(){
			$filename = "Request and Return Transfer Assets Report".date('Y-m-d');
			return Excel::download(new ExportReportAssetsList, $filename.'.xlsx');
		}


	}