<?php namespace App\Http\Controllers;

	use Session;
	use Request;
	use DB;
	use CRUDBooster;
	use App\Users;
	use App\MoveOrder;
	use App\Models\ReturnTransferAssets;
	use App\Models\ReturnTransferAssetsHeader;
	use App\WarehouseLocationModel;

	class AdminScheduleReturnTransferController extends \crocodicstudio\crudbooster\controllers\CBController {
		private const ToSchedule       = 48;
		private const ForTurnOver      = 24;
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

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];

			# END FORM DO NOT REMOVE THIS LINE

			$this->addaction = array();
			if(CRUDBooster::isUpdate()) {
				$this->addaction[] = ['title'=>'Update','url'=>CRUDBooster::mainpath('getToScheduleReturn/[id]'),'icon'=>'fa fa-edit', "showIf"=>"[status] == ".self::ToSchedule.""];
			}
			$this->load_js = array();
			$this->load_js[] = asset("datetimepicker/bootstrap-datetimepicker.min.js");
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
			if(CRUDBooster::isSuperadmin()){
				$query->orderBy('return_transfer_assets_header.status', 'DESC')->where('return_transfer_assets_header.status', self::ToSchedule)->orderBy('return_transfer_assets_header.id', 'DESC');
			}else if(in_array(CRUDBooster::myPrivilegeId(),[9])){
				$query->where('return_transfer_assets_header.status', self::ToSchedule) 
				->orderBy('return_transfer_assets_header.id', 'DESC');
			}
	            
	    }

	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate row of index table html 
	    | ---------------------------------------------------------------------- 
	    |
	    */    
		public function hook_row_index($column_index,&$column_value) {	       
			$ToSchedule  = DB::table('statuses')->where('id', self::ToSchedule)->value('status_description');
			if($column_index == 1){
				if($column_value == $ToSchedule){
					$column_value = '<span class="label label-warning">'.$ToSchedule.'</span>';
				}
			}
	    }


	    public function hook_before_edit(&$postdata,$id) {        
			$fields = Request::all();
			$postdata['status']		 	     = self::ForTurnOver;
			$postdata['schedule_by'] 		 = CRUDBooster::myId();
			$postdata['schedule_at'] 		 = $fields['schedule_date'];
			$postdata['transport_type']      = $fields['transport_type'];
			if($fields['hand_carry']){
				$postdata['hand_carry_name'] = $fields['hand_carry'];
			}
			$postdata['location_to_pick']    = $fields['location_to_pick'];
			$header = ReturnTransferAssetsHeader::where('id',$id)->first();

			if(in_array($header->request_type_id,[1,5])){
				ReturnTransferAssets::where('return_header_id', $id)
				->update([
					'status'           => self::ForTurnOver,
					'location_to_pick' => $fields['location_to_pick']
				]);	
				foreach($fields['mo_id'] as $key => $val){
					$mo_info = MoveOrder::where('id',$val)->first();
					DB::table('assets_inventory_body')->where('id', $mo_info->inventory_id)
					->update([
						'location'=> $fields['location_to_pick']
					]);
				}
			}
	    }

	    public function hook_after_edit($id) { 

	    }

		public function getToScheduleReturn($id){
			$this->cbLoader();
			if(!CRUDBooster::isUpdate() && $this->global_privilege==FALSE) {    
				CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
			}  

			$data = array();
			$data['page_title'] = 'Schedule Return/Transfer Request';
			$data['user'] = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
			$data['Header'] = ReturnTransferAssetsHeader::detail($id)->first();
			$data['return_body'] = ReturnTransferAssets::detail($id)->get();	
			$data['stores'] = DB::table('locations')->where('id', $data['user']->location_id)->first();
			$data['transport_types'] = DB::table('transport_types')->get();
			$data['warehouse_location'] = WarehouseLocationModel::whereNotIn('id',[1,4])->get();
			return $this->view("assets.schedule-return-transfer", $data);
		}


	}