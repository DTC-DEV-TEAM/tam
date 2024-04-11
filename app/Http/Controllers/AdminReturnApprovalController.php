<?php namespace App\Http\Controllers;

	use Session;
	use Request;
	use DB;
	use CRUDBooster;
	use App\Users;
	use App\MoveOrder;
	use App\Models\ReturnTransferAssets;
	use App\Models\ReturnTransferAssetsHeader;
	use App\CommentsGoodDefect;
	use App\WarehouseLocationModel;
	use App\GoodDefectLists;

	class AdminReturnApprovalController extends \crocodicstudio\crudbooster\controllers\CBController {

		private const ForApproval        = 1;
		private const Rejected           = 5;
		private const ForTurnOver        = 24;
		private const ForReturn          = 26;
		private const ForTransfer        = 27;
		private const ForVerification    = 29;
		private const ToSchedule         = 48;
		private const returnForApproval  = 49;
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
			$this->col[] = ["label"=>"Transacted By","name"=>"transacted_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"Transacted Date","name"=>"transacted_date"];
		
			# END COLUMNS DO NOT REMOVE THIS LINE

	        $this->addaction = array();
			if(CRUDBooster::isUpdate()) {
				$this->addaction[] = ['title'=>'Update','url'=>CRUDBooster::mainpath('getRequestApprovalReturn/[id]'),'icon'=>'fa fa-edit', "showIf"=>"[status] == ".self::ForApproval.""];
			}
			if(in_array(CRUDBooster::myPrivilegeId(),[9])){
				$this->addaction[] = ['title'=>'Update','url'=>CRUDBooster::mainpath('getRequestForVerificationReturn/[id]'),'icon'=>'fa fa-edit', "showIf"=>"[status] == ".self::ForVerification.""];
			}

	        $this->load_css = array();
			$this->load_css[] = asset("css/font-family.css");
	        
	    }

	    public function hook_query_index(&$query) {
			if(CRUDBooster::isSuperadmin()){
				$query->orderBy('return_transfer_assets_header.status', 'DESC')->where('return_transfer_assets_header.status', self::ForApproval)->orderBy('return_transfer_assets_header.id', 'DESC');
			}else if(in_array(CRUDBooster::myPrivilegeId(),[9])){
				$query->where('return_transfer_assets_header.status', self::ForVerification) 
				->orderBy('return_transfer_assets_header.id', 'DESC');
			}else{
				$approvalMatrix = Users::where('cms_users.approver_id', CRUDBooster::myId())->get();
				$approval_array = array();
				foreach($approvalMatrix as $matrix){
				    array_push($approval_array, $matrix->id);
				}
				$approval_string = implode(",",$approval_array);
				$userslist = array_map('intval',explode(",",$approval_string));
	
				$query->whereIn('return_transfer_assets_header.requested_by', $userslist)
				->where('return_transfer_assets_header.status', self::ForApproval) 
				->orderBy('return_transfer_assets_header.id', 'DESC');
			}
	            
	    }

	    public function hook_row_index($column_index,&$column_value) {	        
	    	$pending          = DB::table('statuses')->where('id', self::ForApproval)->value('status_description');
			$forVerification  = DB::table('statuses')->where('id', self::ForVerification)->value('status_description');
			
			if($column_index == 1){
				if($column_value == $pending){
					$column_value = '<span class="label label-warning">'.$pending.'</span>';
				}else if($column_value == $forVerification){
					$column_value = '<span class="label label-warning">'.$forVerification.'</span>';
				}
			}
	    }

	    public function hook_before_edit(&$postdata,$id) {        
	         //Your code here
			$fields = Request::all();
			$header_id 		   = $fields['header_id'];
			$mo_id 			   = $fields['mo_id'];
			$dataLines         = array();
			$approval_action   = $fields['approval_action'];
			$approver_comments = $fields['approver_comments'];
			$header 	       = ReturnTransferAssetsHeader::where('id',$header_id)->first();
			$inventory_id 	   = MoveOrder::whereIn('id',$mo_id)->get();
			$finalinventory_id = [];
			foreach($inventory_id as $invData){
				array_push($finalinventory_id, $invData['inventory_id']);
			}

			if($approval_action  == 1){
				for($x=0; $x < count((array)$mo_id); $x++) {
	
					$postdata['status']		 	    = self::ForVerification;
					$postdata['approved_by'] 		= CRUDBooster::myId();
					$postdata['approved_date'] 		= date('Y-m-d H:i:s');
					ReturnTransferAssets::where(['return_header_id'=>$id, 'archived'=> NULL])
					->update([
							'status' => self::ForVerification
					]);	
					if(in_array($header->request_type_id, [1,5,8])){
						if(in_array($header->request_type_id, [1,5])){
							DB::table('assets_inventory_body')->where('id', $finalinventory_id[$x])
							->update([
								'statuses_id'=> self::ForReturn,
							]);
						}else{
							DB::table('assets_inventory_body')->where('id', $finalinventory_id[$x])
							->update([
								'statuses_id'=> self::ForTransfer,
							]);
						}
					}
					
			    }
			}else if($approval_action  == 2){
				$postdata['status'] 			= self::returnForApproval;
				$postdata['approver_comments'] 	= $approver_comments;
				ReturnTransferAssets::where([
					'return_header_id'=>$id,
					'status'=>self::ForApproval
				])
				->update([
					    'status' => self::returnForApproval
				]);	
			}else{
				$postdata['status'] 			= self::Rejected;
				$postdata['approver_comments'] 	= $approver_comments;
				$postdata['approved_by'] 		= CRUDBooster::myId();
				$postdata['rejected_date'] 		= date('Y-m-d H:i:s');
				ReturnTransferAssets::where('return_header_id',$id)
				->update([
					    'status' => self::Rejected
				]);	

				for ($i = 0; $i < count($mo_id); $i++) {
					MoveOrder::where('id',$mo_id[$i])
					->update([
							'return_flag'=> NULL,
					]);	
				}
			}

	    }

		public function getRequestApprovalReturn($id){
			$this->cbLoader();
			if(!CRUDBooster::isUpdate() && $this->global_privilege==FALSE) {    
				CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
			}  

			$data = array();
			$data['page_title'] = 'Approve Return/Transfer Request';
			$data['user'] = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
			$data['Header'] = ReturnTransferAssetsHeader::detail($id)->first();
			$data['return_body'] = ReturnTransferAssets::detail($id)->get();	
			$data['stores'] = DB::table('locations')->where('id', $data['user']->location_id)->first();
			return $this->view("assets.approval-request-return", $data);
		}

		public function getRequestForVerificationReturn($id){
			$this->cbLoader();
			if(!CRUDBooster::isUpdate() && $this->global_privilege==FALSE) {    
				CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
			}  

			$data = array();
			$data['page_title'] = 'Verify Return/Transfer Request';
			$data['user'] = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
			$data['Header'] = ReturnTransferAssetsHeader::detail($id)->first();
			$data['return_body'] = ReturnTransferAssets::detail($id)->get();	
			$data['good_defect_lists'] = GoodDefectLists::all();
			$data['warehouse_location'] = WarehouseLocationModel::where('id','!=',4)->get();
			return $this->view("assets.verification-request-return", $data);
		}

		public function submitForVerificationReturn(Request $request){
			$fields             = Request::all();
			$id                 = $fields['header_id'];
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
			$digits_code = [];
			$asset_code  = [];
			foreach($getSelectedItemList as $selectItem){
				array_push($mo_id, $selectItem->mo_id);
				array_push($item_id, $selectItem->id);
				array_push($arf_number, $selectItem->reference_no);
				array_push($digits_code, $selectItem->digits_code);
				array_push($asset_code, $selectItem->asset_code);
			}
			
			$filter_good_text 		    = array_filter($fields['good_text'], fn($value) => !is_null($value) && $value !== '');
			$good_text                  = array_values($filter_good_text);
			$filter_defective_text 		= array_filter($fields['defective_text'], fn($value) => !is_null($value) && $value !== '');
			$defective_text             = array_values($filter_defective_text);
			
			//good and defect value
			$comments = $fields['comments'];
			$other_comment = $fields['other_comment'];
			$location = $fields['location'];
        
			$arf_header   = ReturnTransferAssetsHeader::where(['id' => $id])->first();

			$inventory_id = MoveOrder::whereIn('id',$mo_id)->get();
		
			$finalinventory_id = [];
			$moQty = [];
			foreach($inventory_id as $invData){
				array_push($finalinventory_id, $invData['inventory_id']);
				array_push($moQty, $invData['quantity']);
			}
	
			$location_to_drop = DB::table('cms_users')->where(['id' => CRUDBooster::myId()])->first();

			for($x=0; $x < count((array)$selectedItemlist); $x++) {
				if($defective_text[$x] == 1){
					$mo_info   = MoveOrder::where('id',$mo_id[$x])->first();
					ReturnTransferAssets::where('id',$selectedItemlist[$x])
					->update([
							'status' => self::ToSchedule
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
							'status'          => self::ToSchedule,
							'transacted_by'   => CRUDBooster::myId(),
							'transacted_date' => date('Y-m-d H:i:s')
						]);	
					}

					if($arf_header->request_type_id == 1){
						DB::table('assets_inventory_body')->where('id', $mo_info->inventory_id)
						->update([
							'item_condition'=> 			"Defective"
						]);
						DB::table('assets_inventory_body')->where('id', $mo_info->inventory_id)->update(['quantity'=>1]);
					}else{
						DB::table('assets_inventory_body')->where('id', $mo_info->inventory_id)
						->update([
							'item_condition'=> 			"Defective"
						]);
						DB::table('assets_inventory_body')->where('id', $mo_info->inventory_id)->update(['quantity'=>1]);
					}
				}else{
	
					ReturnTransferAssets::where('id',$selectedItemlist[$x])
					->update([
							'status' => self::ToSchedule
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
							'status'          => self::ToSchedule,
							'transacted_by'   => CRUDBooster::myId(),
							'transacted_date' => date('Y-m-d H:i:s')
						]);	
					}
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

			CRUDBooster::redirect(CRUDBooster::mainpath(), 'Verified!', 'success');
		}

	}