<?php namespace App\Http\Controllers;

	use Session;
	//use Request;
	use Illuminate\Http\Request;
	use DB;
	use CRUDBooster;
	use App\Models\Requests;
	use App\Models\ReturnTransferAssets;
	use App\Models\ReturnTransferAssetsHeader;
	use App\MoveOrder;
	use App\Users;
	class AdminReturnTransferAssetsHeaderController extends \crocodicstudio\crudbooster\controllers\CBController {
		private const ForApproval       = 1;
		private const Rejected          = 5;
		private const Cancelled         = 8;
		private const ForTurnOver       = 24;
		private const ToClosed          = 25;
		private const Closed            = 13;
		private const ForClosing        = 19;
		private const ForVerification   = 29;
		private const ToSchedule        = 48;
		private const returnForApproval = 49;

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
			$this->table = "return_transfer_assets_header";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Status","name"=>"status","join"=>"statuses,status_description"];
			$this->col[] = ["label"=>"Reference No","name"=>"reference_no"];
			$this->col[] = ["label"=>"Name","name"=>"requestor_name","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"Return Type","name"=>"request_type_id","join"=>"requests,request_name"];
			$this->col[] = ["label"=>"Type of Request","name"=>"request_type"];
			$this->col[] = ["label"=>"Purpose","name"=>"purpose"];
			$this->col[] = ["label"=>"Requested Date","name"=>"requested_date"];
			# END COLUMNS DO NOT REMOVE THIS LINE


	        $this->addaction = array();
			if(CRUDBooster::isUpdate()) {
				$this->addaction[] = ['title'=>'Cancel Request',
									  'url'=>CRUDBooster::mainpath('getRequestCancelReturn/[id]'),
									  'icon'=>'fa fa-times', 
									  'showIf'=>"[status] == ".self::ForApproval."",
									  'confirmation'=>'yes',
									  'confirmation_title'=>'Confirm Voiding',
									  'confirmation_text'=>'Are you sure to VOID this request?'];
				$this->addaction[] = ['title'=>'Print','url'=>CRUDBooster::mainpath('getRequestPrintTF/[id]'),'icon'=>'fa fa-print', "showIf"=>"[status] == ".self::ForTurnOver." || [status] == ".self::ToClosed." || [status] == ".self::ForClosing.""];
				$this->addaction[] = ['title'=>'Edit','url'=>CRUDBooster::mainpath('getEdit/[id]'),'icon'=>'fa fa-edit', "showIf"=>"[status] == ".self::returnForApproval.""];
			}

	        $this->index_button = array();
			if(CRUDBooster::getCurrentMethod() == 'getIndex'){
				$this->index_button[] = ["label"=>"Return Assets","icon"=>"fa fa-files-o","url"=>CRUDBooster::mainpath('return-assets'),"color"=>"success"];
				$this->index_button[] = ["label"=>"Transfer Assets","icon"=>"fa fa-files-o","url"=>CRUDBooster::mainpath('transfer-assets'),"color"=>"success"];
			}

	        $this->style_css = "
			.fa.fa-times{
				color:#df4759;
				font-size:15px;
				margin-top: 2px;
			}
			";
	        
	        $this->load_css = array();
	        $this->load_css[] = asset("css/font-family.css");
	        
	    }


	    public function hook_query_index(&$query) {
			if(CRUDBooster::isSuperadmin()){
				$query->whereNull('return_transfer_assets_header.archived')
					  ->orderBy('return_transfer_assets_header.status', 'ASC')
					  ->orderBy('return_transfer_assets_header.id', 'DESC');
			}else{
				$user = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
				$query->where(function($sub_query){
					$user = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
					$sub_query->where('return_transfer_assets_header.requested_by', CRUDBooster::myId())
							  ->whereNull('return_transfer_assets_header.archived'); 
				});
				$query->orderBy('return_transfer_assets_header.id', 'DESC')->orderBy('return_transfer_assets_header.created_at', 'DESC');
			}
	    }

	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate row of index table html 
	    | ---------------------------------------------------------------------- 
	    |
	    */    
	    public function hook_row_index($column_index,&$column_value) {	        
	    	$pending           = DB::table('statuses')->where('id', self::ForApproval)->value('status_description');
			$forVerification   = DB::table('statuses')->where('id', self::ForVerification)->value('status_description');
			$toSchedule        = DB::table('statuses')->where('id', self::ToSchedule)->value('status_description');
			$rejected          = DB::table('statuses')->where('id', self::Rejected)->value('status_description');
			$cancelled         = DB::table('statuses')->where('id', self::Cancelled)->value('status_description');
			$forturnover       = DB::table('statuses')->where('id', self::ForTurnOver)->value('status_description');
			$toClose           = DB::table('statuses')->where('id', self::ToClosed)->value('status_description');
			$closed            = DB::table('statuses')->where('id', self::Closed)->value('status_description');
			$returnForApproval = DB::table('statuses')->where('id', self::returnForApproval)->value('status_description');
			if($column_index == 1){
				if($column_value == $pending){
					$column_value = '<span class="label label-warning">'.$pending.'</span>';
				}else if($column_value == $forVerification){
					$column_value = '<span class="label label-warning">'.$forVerification.'</span>';
				}else if($column_value == $toSchedule){
					$column_value = '<span class="label label-info">'.$toSchedule.'</span>';
				}else if($column_value == $cancelled){
					$column_value = '<span class="label label-danger">'.$cancelled.'</span>';
				}else if($column_value == $rejected){
					$column_value = '<span class="label label-danger">'.$rejected.'</span>';
				}else if($column_value == $forturnover){
					$column_value = '<span class="label label-info">'.$forturnover.'</span>';
				}else if($column_value == $toClose){
					$column_value = '<span class="label label-info">'.$toClose.'</span>';
				}else if($column_value == $closed){
					$column_value = '<span class="label label-success">'.$closed.'</span>';
				}else if($column_value == $returnForApproval){
					$column_value = '<span class="label label-warning">'.$returnForApproval.'</span>';
				}
			}
	    }


		public function getDetail($id){
            if(!CRUDBooster::isRead() && $this->global_privilege==FALSE) {    
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

			$data = array();
			$data['page_title'] = 'View Return Request';
			$data['user'] = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
			$data['Header'] = ReturnTransferAssetsHeader::detail($id)->first();
			$data['return_body'] = ReturnTransferAssets::viewDetail($id)->get();	
			$data['stores'] = DB::table('locations')->where('id', $data['user']->location_id)->first();

			return $this->view("assets.view-return-details", $data);
		}

		//RETURN IT/FA ASSETS
		public function getReturnAssets(){
			
			if(!CRUDBooster::isCreate() && $this->global_privilege == false) {
				CRUDBooster::redirect(CRUDBooster::adminPath(), trans('crudbooster.denied_access'));
			}

			$this->cbLoader();

			$data = array();
			$data['page_title'] = 'Return Request';
			$closed      =  self::Closed;
			$for_closing =  self::ForClosing;
			$data['user'] = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
			$data['mo_body'] = MoveOrder::moReturn(CRUDBooster::myId());

			$data['purposes'] = DB::table('purposes')->where('status', 'ACTIVE')->where('type', 'RETURN')->get();
			if(CRUDBooster::myPrivilegeId() == 8){ 
				$data['stores'] = DB::table('locations')->where('id', $data['user']->location_id)->first();
			}else{
				$data['stores'] = NULL;
			}	
			return $this->view("assets.return-assets", $data);
		}

		//TRANSFER AREA
		public function getTransferAssets(){
			
			if(!CRUDBooster::isCreate() && $this->global_privilege == false) {
				CRUDBooster::redirect(CRUDBooster::adminPath(), trans('crudbooster.denied_access'));
			}

			$this->cbLoader();
			$data = array();
			$data['page_title'] = 'Transfer Request';

			$closed      =  self::Closed;
			$for_closing =  self::ForClosing;
			$data['user'] = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
			$data['mo_body'] = MoveOrder::moReturn(CRUDBooster::myId());
			$data['purposes'] = DB::table('purposes')->where('status', 'ACTIVE')->where('type', 'RETURN')->get();
			if(CRUDBooster::myPrivilegeId() == 8){ 
				$data['stores'] = DB::table('locations')->where('id', $data['user']->location_id)->first();
			}else{
				$data['stores'] = NULL;
			}	
			$data['users'] = Users::where('id_cms_privileges','!=',1)->where('department_id',$data['user']->department_id)->get();
			return $this->view("assets.transfer-assets", $data);
		}

		//RETURN NON TRADE ASSETS
		public function getReturnNonTradeAssets(){
			
			if(!CRUDBooster::isCreate() && $this->global_privilege == false) {
				CRUDBooster::redirect(CRUDBooster::adminPath(), trans('crudbooster.denied_access'));
			}

			$this->cbLoader();
			$data = array();
			$data['page_title'] = 'Return Non Trade Request';
			$closed      =  self::Closed;
			$for_closing =  self::ForClosing;
			$data['user'] = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
			$data['mo_body'] = MoveOrder::moReturnNonTrade($closed, $for_closing, CRUDBooster::myId());
			$data['purposes'] = DB::table('purposes')->where('status', 'ACTIVE')->where('type', 'RETURN')->get();
			if(CRUDBooster::myPrivilegeId() == 8){ 
				$data['stores'] = DB::table('locations')->where('id', $data['user']->location_id)->first();
			}else{
				$data['stores'] = NULL;
			}	
			return $this->view("non-trade.return-nontrade-assets", $data);
		}

		public function saveReturnAssets(Request $request){
			$moId = $request['Ids'];
			$rid = $request['request_type_id'];
			$request_type_id = array_unique($rid);
			$location = $request['location_id'];
			$asset_location_id = $request['asset_location_id'];
			$purpose = $request['purpose'];
			$getData = MoveOrder::leftjoin('header_request', 'mo_body_request.header_request_id', '=', 'header_request.id')
			->leftjoin('requests', 'header_request.request_type_id', '=', 'requests.id')
			->select(
				'header_request.*',
				'mo_body_request.*',
				'mo_body_request.id as mo_id',
				'requests.*',
				DB::raw('IF(header_request.request_type_id IS NULL, mo_body_request.request_type_id_mo, header_request.request_type_id) as request_type_id')
				)
			->whereIn('mo_body_request.id', $moId)
			->get();

			//Get Latest ID
			$callStart = $this->call_start;
			$latestRequest = DB::table('return_transfer_assets_header')->select('id')->orderBy('id','DESC')->first();
			$latestRequestId = $latestRequest->id != NULL ? $latestRequest->id : 0;
			// Header Area
			$conHeader = [];
			$conHeaderSave = [];
			$count_header       = DB::table('return_transfer_assets_header')->count();
			$forApproval        = DB::table('statuses')->where('id', 1)->value('id');
			$forturnover        = DB::table('statuses')->where('id', 24)->value('id');
			$forReturn          = DB::table('statuses')->where('id', 26)->value('id');

			$inventory_id 	    = MoveOrder::whereIn('id',$moId)->get();
			$finalinventory_id = [];
			foreach($inventory_id as $invData){
				array_push($finalinventory_id, $invData['inventory_id']);
			}
			
			if(in_array(CRUDBooster::myPrivilegeId(), [11,12,14,15])){ 
				$status		 			= $forturnover;
				for($x=0; $x < count($moId); $x++) {
					DB::table('assets_inventory_body')->where('id', $finalinventory_id[$x])
					->update([
						'statuses_id'=> 			$forReturn,
					]);
				}
			}else{
				$status		 			= $forApproval;
	
			}
		    
			foreach($request_type_id as $hKey => $hData){
				$conHeader['status'] = $status;
				$conHeader['requestor_name'] = CRUDBooster::myId();
				$conHeader['request_type_id'] = $hData;
				$conHeader['request_type'] = "RETURN";
				$conHeader['purpose'] = $purpose;
				$conHeader['requested_by'] = CRUDBooster::myId(); 
				$conHeader['requested_date'] = date('Y-m-d H:i:s');
				if(in_array(CRUDBooster::myPrivilegeId(), [11,12,14,15])){ 
					$conHeader['approved_date'] = date('Y-m-d H:i:s');
				}
				if($hData == 1){
					$conHeader['location_to_pick'] = $asset_location_id[$hKey]; 
				}else{
					$conHeader['location_to_pick'] = $asset_location_id[$hKey];
				}
				$conHeader['store_branch'] = $location[$hKey];

				if($hData == 1){
					$conHeader['reference_no'] = "1".str_pad($count_header + 1, 6, '0', STR_PAD_LEFT)."ITAR";
					$count_header++;
				}else if($hData == 9){
					$conHeader['reference_no'] = "1".str_pad($count_header + 1, 6, '0', STR_PAD_LEFT)."NTAR";
					$count_header++;
				}else{
					$conHeader['reference_no'] = "1".str_pad($count_header + 1, 6, '0', STR_PAD_LEFT)."FAR";
					$count_header++;
				}
				$conHeaderSave[] = $conHeader;
			}
		
			ReturnTransferAssetsHeader::insert($conHeaderSave);
			$itId = DB::table('return_transfer_assets_header')->select('*')->where('id','>', $latestRequestId)->where('request_type_id',1)->first();
			$faId = DB::table('return_transfer_assets_header')->select('*')->where('id','>', $latestRequestId)->where('request_type_id',5)->first();
			$ntId = DB::table('return_transfer_assets_header')->select('*')->where('id','>', $latestRequestId)->where('request_type_id',9)->first();
	
			$resultArrforIT = [];
			foreach($getData as $item){
				if($item['request_type_id'] == 1){
					for($i = 0; $i < $item['request_type_id']; $i++){
						$t = $item;
						$t['return_header'] = $itId->id;
						$t['reference_no'] = $itId->reference_no;
						$resultArrforIT[] = $t;
					}
				}
			}
			
			$resultArrforFA = [];
			foreach($getData as $itemFa){
				if($itemFa['request_type_id'] == 5){
					for($x = 0; $x < $itemFa['request_type_id']; $x++){
						$fa = $itemFa;
						$fa['return_header'] = $faId->id;
						$fa['reference_no'] = $faId->reference_no;
						$resultArrforFA[] = $fa;
					}
				}
			}

			$resultArrforNTA = [];
			foreach($getData as $itemNta){
				if($itemNta['request_type_id'] == 9){
					for($x = 0; $x < $itemNta['request_type_id']; $x++){
						$nta = $itemNta;
						$nta['return_header'] = $ntId->id;
						$nta['reference_no'] = $ntId->reference_no;
						$resultArrforNTA[] = $nta;
					}
				}
			}
	
			$finalReturnData = array_merge($resultArrforIT, $resultArrforFA, $resultArrforNTA);
	
			// Body Area
			$container = [];
			$containerSave = [];
	       
			foreach($getData as $rKey => $rData){		
				$container['status'] = $status;
				$container['return_header_id'] = $rData['return_header'];
				$container['mo_id'] = $rData['mo_id'];
				if($rData['request_type_id'] == 1){
					$container['reference_no'] = $rData['reference_no'];
					$container['location_to_pick'] = $rData['location_id'];
				}else{
					$container['reference_no'] = $rData['reference_no'];
					$container['location_to_pick'] = $rData['location_id'];
				}
				$container['asset_code'] =  $rData['asset_code'];
				$container['digits_code'] = $rData['digits_code'];
				$container['description'] = $rData['item_description'];
				$container['asset_type'] = $rData['category_id'];
				$container['requested_by'] = CRUDBooster::myId(); 
				$container['requested_date'] = date('Y-m-d H:i:s');
				$containerSave[] = $container;
			}
			ReturnTransferAssets::insert($containerSave);

			for ($i = 0; $i < count($moId); $i++) {
				MoveOrder::where(['id' => $moId[$i]])
				   ->update([
						   'return_flag' => 1,
				           ]);
			}

			$message = ['status'=>'success', 'message' => 'Send Successfully!','redirect_url'=>CRUDBooster::mainpath()];
			echo json_encode($message);
		}

		public function saveTransferAssets(Request $request){
			$moId = $request['Ids'];
			$rid = $request['request_type_id'];
			$request_type_id = $rid;
			$location = $request['location_id'];
			$user_id = $request['users_id'];
			$purpose = $request['purpose'];
            //dd($request->all());
			$getData = MoveOrder::leftjoin('header_request', 'mo_body_request.header_request_id', '=', 'header_request.id')
			->leftjoin('requests', 'header_request.request_type_id', '=', 'requests.id')
			->select(
				'header_request.*',
				'mo_body_request.*',
				'mo_body_request.id as mo_id',
				'requests.*',
				DB::raw('IF(header_request.request_type_id IS NULL, mo_body_request.request_type_id_mo, header_request.request_type_id) as request_type_id')
				)
			->whereIn('mo_body_request.id', $moId)
			->get();

			$forApproval        = DB::table('statuses')->where('id', 1)->value('id');
			$forturnover        = DB::table('statuses')->where('id', 24)->value('id');
			$forTransfer          = DB::table('statuses')->where('id', 27)->value('id');

			$inventory_id 	    = MoveOrder::whereIn('id',$moId)->get();
			$finalinventory_id = [];
			foreach($inventory_id as $invData){
				array_push($finalinventory_id, $invData['inventory_id']);
			}
			
			if(in_array(CRUDBooster::myPrivilegeId(), [11,12,14,15])){ 
				$status		 			= $forturnover;
				for($x=0; $x < count($moId); $x++) {
					DB::table('assets_inventory_body')->where('id', $finalinventory_id[$x])
					->update([
						'statuses_id'=> 			$forTransfer,
					]);
				}
			}else{
				$status		 			= $forApproval;
	
			}

			// Header Area
			$count_header       = DB::table('return_transfer_assets_header')->count();
			$reference_no = "1".str_pad($count_header + 1, 7, '0', STR_PAD_LEFT)."AT";
			if(in_array(CRUDBooster::myPrivilegeId(), [11,12,14,15])){ 
				$approved = date('Y-m-d H:i:s');
			}else{
			    $approved = NULL;
			}
			$id = ReturnTransferAssetsHeader::Create(
                [
                    'status' => $status, 
					'reference_no' => $reference_no,
                    'requestor_name' => CRUDBooster::myId(), 
                    'request_type_id' => 8,
                    'request_type' => "TRANSFER",
					'purpose'      => $purpose,
                    'requested_by' => CRUDBooster::myId(),
                    'requested_date' => date('Y-m-d H:i:s'),
					'approved_date'  => $approved,
                    'location_to_pick' => 0,
                    'store_branch' => $location,
                    'transfer_to' => $user_id,
                ]
            );   
		
		    $header_id = $id->id;
			$ref_no 	= 	ReturnTransferAssetsHeader::where('id',$header_id)->first();
			// Body Area
			$container = [];
			$containerSave = [];
			
			foreach($getData as $rKey => $rData){		
				$container['status'] = $status;
				$container['return_header_id'] = $header_id;
				$container['mo_id'] = $rData['mo_id'];
				$container['reference_no'] = $ref_no->reference_no;
				$container['location_to_pick'] = 0;
				$container['asset_code'] =  $rData['asset_code'];
				$container['digits_code'] = $rData['digits_code'];
				$container['description'] = $rData['item_description'];
				$container['asset_type'] = $rData['category_id'];
				$container['requested_by'] = CRUDBooster::myId(); 
				$container['requested_date'] = date('Y-m-d H:i:s');
				$container['transfer_to'] = $user_id;
				$containerSave[] = $container;
			}
			ReturnTransferAssets::insert($containerSave);

			for ($i = 0; $i < count($moId); $i++) {
				MoveOrder::where(['id' => $moId[$i]])
				   ->update([
						   'return_flag' => 1,
				           ]);
			}

			$message = ['status'=>'success', 'message' => 'Send Successfully!','redirect_url'=>CRUDBooster::mainpath()];
			echo json_encode($message);
		}

		public function getRequestCancelReturn($id) {
			ReturnTransferAssetsHeader::where('id',$id)
				->update([
					    'status' => 8
				]);	

			$getAssetCode = ReturnTransferAssets::where('return_header_id',$id)->get();
			$arrCode = [];
			foreach($getAssetCode as $code){
              array_push($arrCode, $code['asset_code']);
			}

			for ($i = 0; $i < count($arrCode); $i++) {
				MoveOrder::where('asset_code',$arrCode[$i])
				->update([
						'return_flag'=> NULL,
				]);	
		    }
			ReturnTransferAssets::where('return_header_id',$id)
				->update([
					    'status' => 8,
						'archived'=> date('Y-m-d H:i:s'),
				]);	

			CRUDBooster::redirect(CRUDBooster::mainpath(), trans("Request has been cancelled successfully!"), 'info');
		}

		public function getRequestPrintTF($id){
			if(!CRUDBooster::isUpdate() && $this->global_privilege==FALSE) {    
				CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
			}  

			$data['page_title'] = 'Print Return/Transfer Request';
			$data['user'] = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
			$data['Header'] = ReturnTransferAssetsHeader::leftjoin('cms_users as employees', 'return_transfer_assets_header.requestor_name', '=', 'employees.id')
			->leftjoin('requests', 'return_transfer_assets_header.request_type_id', '=', 'requests.id')
			->leftjoin('departments', 'employees.department_id', '=', 'departments.id')
			->leftjoin('cms_users as requested', 'return_transfer_assets_header.requested_by','=', 'requested.id')
			->leftjoin('cms_users as transfer_to', 'return_transfer_assets_header.transfer_to','=', 'transfer_to.id')
			->leftjoin('cms_users as approved', 'return_transfer_assets_header.approved_by','=', 'approved.id')
			->leftjoin('cms_users as received', 'return_transfer_assets_header.transacted_by','=', 'received.id')
			->leftjoin('cms_users as closed', 'return_transfer_assets_header.close_by','=', 'closed.id')

			->leftjoin('locations', 'return_transfer_assets_header.store_branch', '=', 'locations.id')
			->select(
					'return_transfer_assets_header.*',
					'return_transfer_assets_header.id as requestid',
					'requests.request_name as request_name',
					'requested.name as requestedby',
					'transfer_to.name as transferTo',
					'employees.name as employee_name',
					'employees.company_name_id as company',
					'employees.position_id as position',
					'departments.department_name as department_name',
					'approved.name as approvedby',
					'received.name as receivedby',
					'closed.name as closedby',
					'locations.store_name as store_branch',
					
					)
			->where('return_transfer_assets_header.id', $id)->first();
	   
			$data['return_body'] = ReturnTransferAssets::
					leftjoin('statuses', 'return_transfer_assets.status', '=', 'statuses.id')
					->leftjoin('mo_body_request', 'return_transfer_assets.mo_id', '=', 'mo_body_request.id')
				->select(
					'return_transfer_assets.*',
					'mo_body_request.*',
					'statuses.*',
					)
					->whereNull('archived')
					->where('return_transfer_assets.return_header_id', $id)->get();	
		
			$data['stores'] = DB::table('locations')->where('id', $data['user']->location_id)->first();
			
			return $this->view("assets.print-request-trf", $data);
		}

		public function getEdit($id){
            if(!CRUDBooster::isUpdate() && $this->global_privilege==FALSE) {    
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

			$data = [];
			$data['page_title'] = 'Edit Return Request';
			$data['user'] = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
			$data['Header'] = ReturnTransferAssetsHeader::detail($id)->first();
			$data['return_body'] = ReturnTransferAssets::detail($id)->get();	
			$data['stores'] = DB::table('locations')->where('id', $data['user']->location_id)->first();
			$data['purposes'] = DB::table('purposes')->where('status', 'ACTIVE')->where('type', 'RETURN')->get();
			$data['users'] = Users::where('id_cms_privileges','!=',1)->where('department_id',$data['user']->department_id)->get();
			return $this->view("assets.return-edit-details", $data);
		}

		public function searchItem(Request $request){
			$search 		= $request->search;
			$data = [];
			$data['status_no'] = 0;
			$data['message']   ='No Item Found!';
			$items = MoveOrder::moSearchItem($search, CRUDBooster::myId());
			if($items){
				$data['status'] = 1;
				$data['problem']  = 1;
				$data['status_no'] = 1;
				$data['message']   ='Item Found';
				foreach ($items as $key => $value) {
					$return_data[$key]['id']                = $value->mo_id;
					$return_data[$key]['asset_code']        = $value->asset_code;
					$return_data[$key]['digits_code']       = $value->digits_code;
					$return_data[$key]['item_description']  = $value->item_description;
					$return_data[$key]['asset_type']        = $value->asset_type;
				}
				$data['items'] = $return_data;
			}
			echo json_encode($data);
			exit;  
		}

		public function editReturnAssets(Request $request){
			$header_id = $request->header_id;
			$mo_id     = $request->mo_id;
			$headerInfo = ReturnTransferAssetsHeader::detail($header_id)->first();
			
			$container = [];
			$containerSave = [];
			if(is_array($request->asset_code)){
				foreach($request->asset_code as $key => $val){
					$isExist = ReturnTransferAssets::where([
						'reference_no' =>$headerInfo->reference_no,
						'asset_code'   =>$request->asset_code[$key],
						'digits_code'  =>$request->digits_code[$key]
					])->exists();
					if($isExist){
						if($headerInfo->request_type_id == 8){
							ReturnTransferAssets::where([
								'reference_no' =>$headerInfo->reference_no,
								'asset_code'   =>$request->asset_code[$key],
								'digits_code'  =>$request->digits_code[$key]
							])
							->update([
								'status'      => self::ForApproval,
								'transfer_to' => $request->users_id,
								'archived'    => NULL
							]);
						}else{
							ReturnTransferAssets::where([
								'reference_no' =>$headerInfo->reference_no,
								'asset_code'   =>$request->asset_code[$key],
								'digits_code'  =>$request->digits_code[$key]
							])
							->update([
								'status'   => self::ForApproval,
								'archived' => NULL
							]);
						}
					}else{
						if($headerInfo->request_type_id == 8){
							$insertLines = new ReturnTransferAssets([
								'status'           => self::ForApproval,
								'return_header_id' => $header_id,
								'reference_no'     => $headerInfo->reference_no,
								'mo_id'            => $mo_id[$key],
								'asset_code'       => $request->asset_code[$key],
								'digits_code'      => $request->digits_code[$key],
								'description'      => $request->item_description[$key],
								'asset_type'       => $request->asset_type[$key],
								'requested_by'     => CRUDBooster::myId(), 
								'requested_date'   => date('Y-m-d H:i:s'),
								'transfer_to'      => $request->users_id,
							]);
						}else{
							$insertLines = new ReturnTransferAssets([
								'status'           => self::ForApproval,
								'return_header_id' => $header_id,
								'reference_no'     => $headerInfo->reference_no,
								'mo_id'            => $mo_id[$key],
								'asset_code'       => $request->asset_code[$key],
								'digits_code'      => $request->digits_code[$key],
								'description'      => $request->item_description[$key],
								'asset_type'       => $request->asset_type[$key],
								'requested_by'     => CRUDBooster::myId(), 
								'requested_date'   => date('Y-m-d H:i:s')
							]);
						}
						$insertLines->save();
					}
					
					//UPDATE FLAG RETURN
					MoveOrder::where(['id' => $mo_id[$key]])
					->update([
						'return_flag' => 1
					]);
				}
			}
			if(!$request->users_id){
				ReturnTransferAssetsHeader::where(['id' => $header_id])
				->update([
					'status'  => self::ForApproval,
					'purpose' => $request->purpose
				]);
			}else{
				ReturnTransferAssetsHeader::where(['id' => $header_id])
				->update([
					'status'  => self::ForApproval,
					'purpose' => $request->purpose,
					'transfer_to' => $request->users_id
				]);
			}
		
			ReturnTransferAssets::where(['return_header_id' => $header_id])
			->update([
				'status' => self::ForApproval
			]);

			CRUDBooster::redirect(CRUDBooster::mainpath(), trans("Updated successfully!"), 'info');
		}

		public function deleteLineReturnAssets(Request $request){
			$lineInfo = ReturnTransferAssets::lineDetail($request->lineId)->first();

			ReturnTransferAssets::where(['id' => $request->lineId])
			->update([
				'status'   => self::Cancelled,
				'archived' => date('Y-m-d H:i:s')
			]);

			MoveOrder::where(['id' => $lineInfo->mo_id])
			->update([
				'return_flag' => NULL
			]);

			$message = ['status'=>'success', 'message' => 'Delete Successfully!','redirect_url'=>CRUDBooster::mainpath()];
			echo json_encode($message);
		}
	}