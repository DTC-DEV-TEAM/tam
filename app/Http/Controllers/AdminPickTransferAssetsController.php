<?php namespace App\Http\Controllers;

	use Session;
	use Request;
	use DB;
	use CRUDBooster;
	use App\Models\ReturnTransferAssets;
	use App\Models\ReturnTransferAssetsHeader;
	use App\MoveOrder;
	use App\CommentsGoodDefect;
	use App\GoodDefectLists;
	class AdminPickTransferAssetsController extends \crocodicstudio\crudbooster\controllers\CBController {

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "requestor_name";
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
			$this->button_show = false;
			$this->button_filter = true;
			$this->button_import = false;
			$this->button_export = false;
			$this->table = "return_transfer_assets_header";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Status","name"=>"status","join"=>"statuses,status_description"];
			$this->col[] = ["label"=>"Reference No","name"=>"reference_no"];
			$this->col[] = ["label"=>"Name","name"=>"requestor_name","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"Return Type","name"=>"request_type_id","join"=>"requests,request_name"];
			$this->col[] = ["label"=>"Type of Request","name"=>"request_type"];
			$this->col[] = ["label"=>"Requested Date","name"=>"requested_date"];
			$this->col[] = ["label"=>"Received By","name"=>"transacted_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"Received Date","name"=>"transacted_date"];
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
				
				$forturnover           = DB::table('statuses')->where('id', 24)->value('id');

				$this->addaction[] = ['title'=>'Receive','url'=>CRUDBooster::mainpath('getRequestPickingTransfer/[id]'),'icon'=>'fa fa-edit', "showIf"=>"[status] == $forturnover"];
				//$this->addaction[] = ['title'=>'Edit','url'=>CRUDBooster::mainpath('getRequestEdit/[id]'),'icon'=>'fa fa-pencil', "showIf"=>"[status_id] == $Rejected"]; //, "showIf"=>"[status_level1] == $inwarranty"
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
	        $forturnover  =      DB::table('statuses')->where('id', 24)->value('id');

			$user_info = 		DB::table('cms_users')->where(['id' => CRUDBooster::myId()])->get();

			$approval_array = array();
			foreach($user_info as $matrix){
				array_push($approval_array, $matrix->id);

			}
			$approval_string = implode(",",$approval_array);
			$locationList = array_map('intval',explode(",",$approval_string));

			$query->where('return_transfer_assets_header.transfer_to', $locationList)
			        ->whereIn('return_transfer_assets_header.status', [24,25,13]) 
					->whereNotNull('return_transfer_assets_header.transfer_to')
					->orderBy('return_transfer_assets_header.id', 'ASC');

	            
	    }

	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate row of index table html 
	    | ---------------------------------------------------------------------- 
	    |
	    */    
	    public function hook_row_index($column_index,&$column_value) {	        
	    	$pending      =  	 DB::table('statuses')->where('id', 1)->value('status_description');
			$cancelled    =  	 DB::table('statuses')->where('id', 8)->value('status_description');
			$forturnover  =      DB::table('statuses')->where('id', 24)->value('status_description');
			$toClose      =      DB::table('statuses')->where('id', 25)->value('status_description');
			$closed       =      DB::table('statuses')->where('id', 13)->value('status_description');
			if($column_index == 1){
				if($column_value == $pending){
					$column_value = '<span class="label label-warning">'.$pending.'</span>';
				}else if($column_value == $cancelled){
					$column_value = '<span class="label label-danger">'.$cancelled.'</span>';
				}else if($column_value == $forturnover){
					$column_value = '<span class="label label-info">'.$forturnover.'</span>';
				}else if($column_value == $toClose){
					$column_value = '<span class="label label-info">'.$toClose.'</span>';
				}else if($column_value == $closed){
					$column_value = '<span class="label label-success">'.$closed.'</span>';
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
	        $fields = Request::all();
	
			$selectedItem       = $fields['item_to_receive_id'];
			$selectedItem_array = array();
			foreach($selectedItem as $select){
				array_push($selectedItem_array, $select);
			}
			$selectedItem_string = implode(",",$selectedItem_array);
			$selectedItemlist = array_map('intval',explode(",",$selectedItem_string));

			$getSelectedItemList = DB::table('return_transfer_assets')->whereIn('id',$selectedItemlist)->get();

			//MO ID, Item ID
			$mo_id       = [];
			$item_id     = [];
			$arf_number  = [];
	
			foreach($getSelectedItemList as $selectItem){
				array_push($mo_id, $selectItem->mo_id);
				array_push($item_id, $selectItem->id);
				array_push($arf_number, $selectItem->reference_no);
		
			}

			// $filter_good_text 		    = array_filter($fields['good_text'], fn($value) => !is_null($value) && $value !== '');
			// $good_text                  = array_values($filter_good_text);
			// $filter_defective_text 		= array_filter($fields['defective_text'], fn($value) => !is_null($value) && $value !== '');
			// $defective_text             = array_values($filter_defective_text);
			
			//good and defect value
			$comments = $fields['comments'];
			$other_comment = $fields['other_comment'];
        
			$arf_header 	= ReturnTransferAssetsHeader::where(['id' => $id])->first();
			
			$inventory_id 	= MoveOrder::whereIn('id',$mo_id)->get();

			$finalinventory_id = [];
			$mo_item_id = [];
			$digits_code = [];
            $asset_code = [];
            $item_description = [];
            $category = [];
            $serial_no = [];
            $unit_cost = [];
			$request_type_id_mo = [];
			foreach($inventory_id as $invData){
				array_push($finalinventory_id, $invData['inventory_id']);
				array_push($mo_item_id, $invData['item_id']);
				array_push($digits_code, $invData['digits_code']);
                array_push($asset_code, $invData['asset_code']);
                array_push($item_description, $invData['item_description']);
                array_push($category, $invData['category_id']);
                array_push($serial_no, $invData['serial_no']);
                array_push($unit_cost, $invData['unit_cost']);
				array_push($request_type_id_mo, $invData['request_type_id_mo']);
			}
			$employee_name = DB::table('cms_users')->where('id', $arf_header->transfer_to)->first();

			for($x=0; $x < count((array)$item_id); $x++) {
				$to_close  = DB::table('statuses')->where('id',25)->value('id');
				
				ReturnTransferAssets::where('id',$selectedItemlist[$x])
				->update([
						'status' => $to_close
				]);	

				$countItem = ReturnTransferAssets::where('return_header_id',$id)->where('status',24)->count();
				
				ReturnTransferAssetsHeader::where('id', $id)
				->update([
					'received_by' => CRUDBooster::myId(),
					'received_at' => date('Y-m-d H:i:s')
				]);	

				if($countItem == 0){
					ReturnTransferAssetsHeader::where('id', $id)
					->update([
						'status'          => $to_close
					]);	
				}
			
				DB::table('assets_inventory_body')->where('id', $finalinventory_id[$x])
				->update([
					'statuses_id'        => 3,
					'deployed_to'        => $employee_name->bill_to,
					'deployed_to_id'     => NULL,
					'location'           => 4
				]);
				
				DB::table('assets_inventory_body')->where('id', $finalinventory_id[$x])->update(['quantity'=>0]);
				MoveOrder::create([
					'status_id'           => 13,
					'mo_reference_number' => $arf_header->reference_no,
					'inventory_id'        => $finalinventory_id[$x],
					'item_id'             => $mo_item_id[$x],
					'request_created_by'  => $employee_name->id,
					'request_type_id_mo'  => $request_type_id_mo[$x],
					'digits_code'         => $digits_code[$x],
					'asset_code'          => $asset_code[$x],
					'item_description'    => $item_description[$x],
					'category_id'         => $category[$x],
					'serial_no'           => $serial_no[$x],
					'quantity'            => 1,
					'unit_cost'           => $unit_cost[$x],
				]);
			}
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

		public function getRequestPickingTransfer($id){
			if(!CRUDBooster::isUpdate() && $this->global_privilege==FALSE) {    
				CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
			}  

			$data = array();
			$data['page_title'] = 'Asset Transfer Receiving';
			$data['Header'] = ReturnTransferAssetsHeader::detail($id)->first();
			$data['return_body'] = ReturnTransferAssets::detailForReturnReceiving($id)->get();		
			// dd($data['return_body']);
			$data['good_defect_lists'] = GoodDefectLists::all();
			return $this->view("assets.transfer-picking-request", $data);
		}

		public function getDetail($id){
            if(!CRUDBooster::isRead() && $this->global_privilege==FALSE) {    
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

			$data = array();
			$data['page_title'] = 'View Return Request';
			$data['user'] = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
			$data['Header'] = ReturnTransferAssetsHeader::detail($id)->first();
			$data['return_body'] = ReturnTransferAssets::detail($id)->get();	
			$data['stores'] = DB::table('locations')->where('id', $data['user']->location_id)->first();
			return $this->view("assets.view-return-details", $data);
		}

	}