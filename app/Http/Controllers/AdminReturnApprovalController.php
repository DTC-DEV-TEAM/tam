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

		private const ForApproval      = 4;
		private const Approved         = 4;
		private const Rejected         = 5;
		private const ForTurnOver      = 24;
		private const ForReturn        = 26;
		private const ForTransfer      = 27;
		private const ForVerification  = 29;

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
			$this->col[] = ["label"=>"Transacted By","name"=>"transacted_by"];
			$this->col[] = ["label"=>"Transacted Date","name"=>"transacted_date"];
		
			# END COLUMNS DO NOT REMOVE THIS LINE

	        $this->addaction = array();
			if(CRUDBooster::isUpdate()) {
				$this->addaction[] = ['title'=>'Update','url'=>CRUDBooster::mainpath('getRequestApprovalReturn/[id]'),'icon'=>'fa fa-pencil', "showIf"=>"[status] == ".self::ForApproval.""];
			}
			if(in_array(CRUDBooster::myPrivilegeId(),[9])){
				$this->addaction[] = ['title'=>'Update','url'=>CRUDBooster::mainpath('getRequestForVerificationReturn/[id]'),'icon'=>'fa fa-pencil', "showIf"=>"[status] == ".self::ForVerification.""];
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
					ReturnTransferAssets::where('return_header_id',$id)
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
			dd(Request::all());
		}

	}