<?php namespace App\Http\Controllers;

	use Session;
	use Request;
	use DB;
	use CRUDBooster;
	use App\Models\Requests;
	use App\Models\ReturnTransferAssets;
	use App\Models\ReturnTransferAssetsHeader;
	use App\MoveOrder;
	use App\Users;
	class AdminReturnHistoryAssetsController extends \crocodicstudio\crudbooster\controllers\CBController {
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
			# END COLUMNS DO NOT REMOVE THIS LINE	        
			$this->load_css[] = asset("css/font-family.css");
	    }
		
	    public function hook_query_index(&$query) {
	        if(CRUDBooster::isSuperadmin()){
		
				$query->whereNull('return_transfer_assets_header.archived')->orderBy('return_transfer_assets_header.status', 'DESC')->orderBy('return_transfer_assets_header.id', 'DESC');
			
			}else if(CRUDBooster::myPrivilegeId() == 2){ 
				
				$query->where('return_transfer_assets_header.requested_by', CRUDBooster::myId())->whereNull('return_transfer_assets_header.archived')->orderBy('return_transfer_assets_header.status', 'asc')->orderBy('return_transfer_assets_header.id', 'DESC');
			
			}else if(in_array(CRUDBooster::myPrivilegeId(), [3, 11, 12, 14])){ 

				$approvalMatrix = Users::where('cms_users.approver_id', CRUDBooster::myId())->get();
				
				$approval_array = array();
				foreach($approvalMatrix as $matrix){
				    array_push($approval_array, $matrix->id);
				}
				$approval_string = implode(",",$approval_array);
				$usersmentlist = array_map('intval',explode(",",$approval_string));

				$user_data         = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
				$query->whereIn('return_transfer_assets_header.requested_by', $usersmentlist)
				//->whereIn('return_transfer_assets_header.company_name', explode(",",$user_data->company_name_id))
				->where('return_transfer_assets_header.approved_by','!=', null)
				->whereNull('return_transfer_assets_header.archived')
				->orderBy('return_transfer_assets_header.id', 'ASC');

			}else if(CRUDBooster::myPrivilegeId() == 5){ 

				//$approved =  		DB::table('statuses')->where('id', 4)->value('id');
				$user_data         = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
				$query->where('return_transfer_assets_header.transacted_by', '!=', null)
				->where('return_transfer_assets_header.request_type_id', 1)
				->whereNull('return_transfer_assets_header.archived')
				->orderBy('return_transfer_assets_header.id', 'ASC');

			}else if(CRUDBooster::myPrivilegeId() == 9){ 

				//$approved =  		DB::table('statuses')->where('id', 4)->value('id');
				$user_data         = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
				$query->where('return_transfer_assets_header.transacted_by', '!=', null)
				// ->where('return_transfer_assets_header.request_type_id', 5)
				->whereNull('return_transfer_assets_header.archived')
				->orderBy('return_transfer_assets_header.id', 'ASC');

			}else if(CRUDBooster::myPrivilegeId() == 7){ 

				$query->whereNotNull('return_transfer_assets_header.close_by')
				->where('return_transfer_assets_header.close_by', CRUDBooster::myId())
				->whereNull('return_transfer_assets_header.archived')
				->orderBy('return_transfer_assets_header.status', 'asc')->orderBy('return_transfer_assets_header.id', 'DESC');

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
			$data['Header'] = ReturnTransferAssetsHeader::detail($id)->first();
			$data['return_body'] = ReturnTransferAssets::detail($id)->get();	
			return $this->view("assets.view-return-details", $data);
		}


	}