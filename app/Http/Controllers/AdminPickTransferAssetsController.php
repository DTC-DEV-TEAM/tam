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

			# OLD START FORM
			//$this->form = [];
			//$this->form[] = ["label"=>"Status","name"=>"status","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Requestor Name","name"=>"requestor_name","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Store Branch","name"=>"store_branch","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Request Type Id","name"=>"request_type_id","type"=>"select2","required"=>TRUE,"validation"=>"required|min:1|max:255","datatable"=>"request_type,id"];
			//$this->form[] = ["label"=>"Request Type","name"=>"request_type","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Requested By","name"=>"requested_by","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Requested Date","name"=>"requested_date","type"=>"datetime","required"=>TRUE,"validation"=>"required|date_format:Y-m-d H:i:s"];
			//$this->form[] = ["label"=>"Transacted By","name"=>"transacted_by","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Transacted Date","name"=>"transacted_date","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Approved By","name"=>"approved_by","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Approved Date","name"=>"approved_date","type"=>"datetime","required"=>TRUE,"validation"=>"required|date_format:Y-m-d H:i:s"];
			//$this->form[] = ["label"=>"Approver Comments","name"=>"approver_comments","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Rejected Date","name"=>"rejected_date","type"=>"datetime","required"=>TRUE,"validation"=>"required|date_format:Y-m-d H:i:s"];
			//$this->form[] = ["label"=>"Location To Pick","name"=>"location_to_pick","type"=>"textarea","required"=>TRUE,"validation"=>"required|string|min:5|max:5000"];
			//$this->form[] = ["label"=>"Close By","name"=>"close_by","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Close At","name"=>"close_at","type"=>"datetime","required"=>TRUE,"validation"=>"required|date_format:Y-m-d H:i:s"];
			//$this->form[] = ["label"=>"Archived","name"=>"archived","type"=>"datetime","required"=>TRUE,"validation"=>"required|date_format:Y-m-d H:i:s"];
			//$this->form[] = ["label"=>"Transfer To","name"=>"transfer_to","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
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
				
				$forturnover           = DB::table('statuses')->where('id', 24)->value('id');

				$this->addaction[] = ['title'=>'Receive','url'=>CRUDBooster::mainpath('getRequestPickingTransfer/[id]'),'icon'=>'fa fa-pencil', "showIf"=>"[status] == $forturnover"];
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

			$filter_good_text 		    = array_filter($fields['good_text'], fn($value) => !is_null($value) && $value !== '');
			$good_text                  = array_values($filter_good_text);
			$filter_defective_text 		= array_filter($fields['defective_text'], fn($value) => !is_null($value) && $value !== '');
			$defective_text             = array_values($filter_defective_text);
			

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
				//if($item_id[$x] == 1){
				if($defective_text[$x] == 1){
					$to_close  = 		DB::table('statuses')->where('id',25)->value('id');
					$deployed  = 		DB::table('statuses')->where('id',3)->value('id');
					$mo_info 	= 		MoveOrder::where('id',$mo_id[$x])->first();

					ReturnTransferAssets::where('id',$selectedItemlist[$x])
					->update([
							'status' => $to_close,
					]);	

					$countItem = ReturnTransferAssets::where('return_header_id',$id)->where('status',24)->count();
					
					ReturnTransferAssetsHeader::where('id', $id)
					->update([
						'transacted_by'   => CRUDBooster::myId(),
						'transacted_date' => date('Y-m-d H:i:s')
					]);	

                    if($countItem == 0){
						ReturnTransferAssetsHeader::where('id', $id)
						->update([
							'status'          => $to_close,
							'transacted_by'   => CRUDBooster::myId(),
							'transacted_date' => date('Y-m-d H:i:s')
						]);	
					}
				
					DB::table('assets_inventory_body')->where('id', $mo_info->inventory_id)
					->update([
						'statuses_id'        => 23,
						'deployed_to'        => $employee_name->bill_to,
						'item_condition'     => "Defective",
						'deployed_to_id'     => NULL    
					]);
			
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
										
				}else{
					$to_close  = 		DB::table('statuses')->where('id',25)->value('id');
					
					ReturnTransferAssets::where('id',$selectedItemlist[$x])
					->update([
							'status' => $to_close
					]);	

					$countItem = ReturnTransferAssets::where('return_header_id',$id)->where('status',24)->count();
					
					ReturnTransferAssetsHeader::where('id', $id)
					->update([
						'transacted_by'   => CRUDBooster::myId(),
						'transacted_date' => date('Y-m-d H:i:s')
					]);	

                    if($countItem == 0){
						ReturnTransferAssetsHeader::where('id', $id)
						->update([
							'status'          => $to_close,
							'transacted_by'   => CRUDBooster::myId(),
							'transacted_date' => date('Y-m-d H:i:s')
						]);	
					}
					
					DB::table('assets_inventory_body')->where('id', $finalinventory_id[$x])
					->update([
						'statuses_id'        => 3,
						'deployed_to'        => $employee_name->bill_to,
						'deployed_to_id'     => NULL
					]);
			
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

			//save defect and good comments
			$container = [];
			$containerSave = [];
			foreach((array)$comments as $key => $val){
				$container['arf_number'] = $arf_number[$key] ? $arf_number[$key] : NULL;
				$container['digits_code'] = explode("|",$val)[1];
				$container['asset_code'] = explode("|",$val)[0];
				$container['comments'] = explode("|",$val)[2];
				$container['users'] = CRUDBooster::myId();
				$container['created_at'] = date('Y-m-d H:i:s');
				$containerSave[] = $container;
			}
			$otherCommentContainer = [];
			$otherCommentFinalData = [];
			foreach((array)$asset_code as $aKey => $aVal){
				$otherCommentContainer['asset_code'] = $aVal;
				$otherCommentContainer['digits_code'] = $digits_code[$aKey];
				$otherCommentContainer['other_comment'] = $other_comment[$aKey];
				$otherCommentFinalData[] = $otherCommentContainer;
			}
			//search other comment in another array
			$finalData = [];
			foreach((array)$containerSave as $csKey => $csVal){
				$i = array_search($csVal['asset_code'], array_column($otherCommentFinalData,'asset_code'));
				if($i !== false){
					$csVal['other_comment'] = $otherCommentFinalData[$i];
					$finalData[] = $csVal;
				}else{
					$csVal['other_comment'] = "";
					$finalData[] = $csVal;
				}
			}
			$finalContainerSave = [];
			$finalContainer = [];
			foreach((array)$finalData as $key => $val){
				$finalContainer['arf_number'] = $val['arf_number'];
				$finalContainer['digits_code'] = $val['digits_code'];
				$finalContainer['asset_code'] = $val['asset_code'];
				$finalContainer['comments'] = $val['comments'];
				$finalContainer['other_comment'] = $val['other_comment']['other_comment'];
				$finalContainer['users'] = $val['users'];
				$finalContainer['created_at'] = $val['created_at'];
				$finalContainerSave[] = $finalContainer;
			}
 
			CommentsGoodDefect::insert($finalContainerSave);

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
			$this->cbLoader();
			if(!CRUDBooster::isUpdate() && $this->global_privilege==FALSE) {    
				CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
			}  


			$data = array();

			$data['page_title'] = 'Asset Transfer Receiving';

			$data['Header'] = ReturnTransferAssetsHeader::leftjoin('cms_users as employees', 'return_transfer_assets_header.requestor_name', '=', 'employees.id')
				->leftjoin('requests', 'return_transfer_assets_header.request_type_id', '=', 'requests.id')
				->leftjoin('departments', 'employees.department_id', '=', 'departments.id')
				->leftjoin('locations', 'return_transfer_assets_header.store_branch', '=', 'locations.id')
				->leftjoin('cms_users as transfer_to', 'return_transfer_assets_header.transfer_to','=', 'transfer_to.id')
				->select(
						'return_transfer_assets_header.*',
						'return_transfer_assets_header.id as requestid',
						'requests.request_name as request_name',
						'employees.name as employee_name',
						'employees.company_name_id as company',
						'employees.position_id as position',
						'departments.department_name as department_name',
						'locations.store_name as store_branch',
						'transfer_to.name as transfer_to',
						)
				->where('return_transfer_assets_header.id', $id)->first();
           
		

			$data['return_body'] = ReturnTransferAssets::
			           leftjoin('statuses', 'return_transfer_assets.status', '=', 'statuses.id')
				
				->select(
						'return_transfer_assets.*',
						'return_transfer_assets.id as body_id',
						'statuses.*',
						)
						->where('return_transfer_assets.return_header_id', $id)
						->where('return_transfer_assets.status', 24)
						->get();	
			// dd($data['return_body']);
			$data['good_defect_lists'] = GoodDefectLists::all();
			return $this->view("assets.transfer-picking-request", $data);
		}

		public function getDetail($id){
			

			$this->cbLoader();
            if(!CRUDBooster::isRead() && $this->global_privilege==FALSE) {    
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

			$data = array();

			$data['page_title'] = 'View Return Request';
			$data['user'] = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
			$data['Header'] = ReturnTransferAssetsHeader::leftjoin('cms_users as employees', 'return_transfer_assets_header.requestor_name', '=', 'employees.id')
			->leftjoin('requests', 'return_transfer_assets_header.request_type_id', '=', 'requests.id')
			->leftjoin('departments', 'employees.department_id', '=', 'departments.id')
			->leftjoin('cms_users as approved', 'return_transfer_assets_header.approved_by','=', 'approved.id')
			->leftjoin('cms_users as received', 'return_transfer_assets_header.transacted_by','=', 'received.id')
			->leftjoin('cms_users as closed', 'return_transfer_assets_header.close_by','=', 'closed.id')
			->leftjoin('locations', 'return_transfer_assets_header.store_branch', '=', 'locations.id')
			->leftjoin('cms_users as transfer_to', 'return_transfer_assets_header.transfer_to','=', 'transfer_to.id')
			->select(
					'return_transfer_assets_header.*',
					'return_transfer_assets_header.id as requestid',
					'requests.request_name as request_name',
					'employees.name as employee_name',
					'employees.company_name_id as company',
					'employees.position_id as position',
					'departments.department_name as department_name',
					'approved.name as approvedby',
					'received.name as receivedby',
					'closed.name as closedby',
					'locations.store_name as store_branch',
					'transfer_to.name as transfer_to',
					)
			->where('return_transfer_assets_header.id', $id)->first();
	   
			$data['return_body'] = ReturnTransferAssets::
					leftjoin('statuses', 'return_transfer_assets.status', '=', 'statuses.id')
				
				->select(
					'return_transfer_assets.*',
					'statuses.*',
					)
					->where('return_transfer_assets.return_header_id', $id)->get();	
			$data['stores'] = DB::table('locations')->where('id', $data['user']->location_id)->first();

			return $this->view("assets.view-return-details", $data);
		}

	}