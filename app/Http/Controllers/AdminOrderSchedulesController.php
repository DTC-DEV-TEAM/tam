<?php namespace App\Http\Controllers;

	use Session;
	use Request;
	use DB;
	use CRUDBooster;
	use Illuminate\Support\Str;
	use Illuminate\Support\Facades\Redirect;
	use App\Models\OrderSchedule;
	use App\Models\CmsPrivileges;
	use App\Store;
	use App\Channel;
	use Carbon\Carbon;

	class AdminOrderSchedulesController extends \crocodicstudio\crudbooster\controllers\CBController {

        public function __construct() {
			// Register ENUM type
			DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping("enum", "string");
		}

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "schedule_code";
			$this->limit = "20";
			$this->orderby = "id,desc";
			$this->global_privilege = false;
			$this->button_table_action = true;
			$this->button_bulk_action = true;
			$this->button_action_style = "button_icon";
			$this->button_add = true;
			$this->button_edit = true;
			$this->button_delete = true;
			$this->button_detail = true;
			$this->button_show = true;
			$this->button_filter = true;
			$this->button_import = false;
			$this->button_export = true;
			$this->table = "order_schedules";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Schedule Name","name"=>"schedule_name"];
			$this->col[] = ["label"=>"Start Date","name"=>"start_date"];
			$this->col[] = ["label"=>"End Date","name"=>"end_date"];
			$this->col[] = ["label"=>"Time Unit","name"=>"time_unit"];
			$this->col[] = ["label"=>"Time Period","name"=>"period"];
			$this->col[] = ["label"=>"Privilege Name","name"=>"privilege_id"]; //additional code 20200624
			$this->col[] = ["label"=>"Status","name"=>"status"];
			$this->col[] = ["label"=>"Created Date","name"=>"created_at"];
			$this->col[] = ["label"=>"Updated Date","name"=>"updated_at"];
			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'Schedule Name','name'=>'schedule_name','type'=>'text','validation'=>'required|min:3|max:50','width'=>'col-sm-5'];
			$this->form[] = ['label'=>'Start Date','name'=>'start_date','type'=>'datetime','validation'=>'required|date_format:Y-m-d H:i:s','width'=>'col-sm-5'];
			$this->form[] = ['label'=>'End Date','name'=>'end_date','type'=>'datetime','validation'=>'required|date_format:Y-m-d H:i:s|after:start_date','width'=>'col-sm-5'];
			
			$this->form[] = ['label'=>'Time Unit','name'=>'time_unit','type'=>'number','validation'=>'required','width'=>'col-sm-5'];
			$this->form[] = ['label'=>'Time Period','name'=>'period','type'=>'select','validation'=>'required','width'=>'col-sm-5','dataenum'=>'DAY;HOUR'];
			//start - additional code 20200624
			// $this->form[] = ["label"=>"Channel","name"=>"channels_id","type"=>"select","datatable"=>"channels,channel_name", 'width'=>'col-sm-5'];
			$this->form[] = ["label"=>"Privilege Name","name"=>"privilege_id","type"=>"check-box","datatable"=>"cms_privileges,name", 'width'=>'col-sm-5'];
			//end - additional code 20200624
			if(CRUDBooster::getCurrentMethod() == 'getEdit' || CRUDBooster::getCurrentMethod() == 'postEditSave' || CRUDBooster::getCurrentMethod() == 'getDetail') {
				$this->form[] = ['label'=>'Status','name'=>'status','type'=>'select','validation'=>'required','width'=>'col-sm-5','dataenum'=>'ACTIVE;INACTIVE'];
			}
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
	        $this->alert = array();
	                

	        
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
			$this->script_js = "
				$('#alerts_msg').fadeTo(1500, 500).slideUp(500, function(){
					$('#alerts_msg').slideUp(500);
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
			if($column_index == 7){
				$privilegeLists = $this->privilegeListing($column_value);
				
				foreach ($privilegeLists as $value) {
					$col_values .= '<span stye="display: block;" class="label label-info">'.$value.'</span><br>';
				}
				$column_value = $col_values;
			}else if($column_index == 8){
                if($column_value == 'ACTIVE'){
					$column_value = '<span class="label label-success">'.$column_value.'</span>';
				}else{
					$column_value = '<span class="label label-danger">'.$column_value.'</span>';
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
            $current_schedule = OrderSchedule::where('status','ACTIVE')->orderBy('id','desc')->count();
	
			if($current_schedule == 1){
				return CRUDBooster::redirect(CRUDBooster::mainpath(),"Already have active Ordering Schedule!","danger");
			}else{
				$postdata['schedule_code'] = Str::random(10);
				if(count((array)$postdata['privilege_id']) > 0) {

					// Approval Ids
					$approval_array = array();
					foreach($postdata['privilege_id'] as $priv){
						array_push($approval_array, $priv);
					}
					$approval_string = implode(",",$approval_array);
					$userslist = array_map('intval',explode(",",$approval_string));
		
					$approverList = DB::table('cms_users')->whereIn('id_cms_privileges',$userslist)->where('status', 'ACTIVE')->get();
		
					$approverIds      = [];
					foreach($approverList as $value){
						array_push($approverIds, $value->approver_id);
					}
					
					$saveApprover = implode(",",array_unique($approverIds));
					$postdata['approver_id'] = $saveApprover;

					// Privilege ids
					$privData = array();
					$privList = json_encode($postdata['privilege_id'], true);
					$privArray = explode(",", $privList);
			
					foreach ($privArray as $key => $value) {
						$privData[$key] = preg_replace("/[^0-9]/","",$value);
					}
					
					$postdata['privilege_id'] = implode(",", $privData);
					// end-Privilege ids
				}
				else{
					$postdata['approver_id'] = 0;
					$postdata['privilege_id'] = 0;
				}
			}
			
			/*
			$query_schedules = DB::table('order_schedules')->whereDate('start_date', '<', $postdata['end_date'])
			->orWhereDate('end_date', '>=', $postdata['end_date'])
			->orWhereDate('start_date', '>=', $postdata['start_date'])
			->orWhereDate('end_date', '<', $postdata['start_date'])->count();
			
            //dd($query_schedules);
            if($query_schedules > 0) {
                //CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.alert_add_save_order_schedule_failed"), 'danger')->send();
                return redirect(CRUDBooster::mainpath())->with(['message_type' => 'danger', 'message' => trans('crudbooster.alert_add_save_order_schedule_failed')])->send();
                exit;
            }*/
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
			if(count((array)$postdata['privilege_id']) > 0) {
				// Approval Ids
				$approval_array = array();
				foreach($postdata['privilege_id'] as $priv){
					array_push($approval_array, $priv);
				}
				$approval_string = implode(",",$approval_array);
				$userslist = array_map('intval',explode(",",$approval_string));
	
				$approverList = DB::table('cms_users')->whereIn('id_cms_privileges',$userslist)->where('status', 'ACTIVE')->get();
	
				$approverIds      = [];
				foreach($approverList as $value){
					array_push($approverIds, $value->approver_id);
				}
				
				$saveApprover = implode(",",array_unique($approverIds));
				$postdata['approver_id'] = $saveApprover;

				// Privilege Ids
				$privilegeData = array();
				$privilegeList = json_encode($postdata['privilege_id'], true);
				$privilegeArray = explode(",", $privilegeList);
		
				foreach ($privilegeArray as $key => $value) {
					$privilegeData[$key] = preg_replace("/[^0-9]/","",$value);
				}
				//dd($privilegeData);
				$postdata['privilege_id'] = implode(",", $privilegeData);
				// end-Privilege Ids
			}
			else{
				$postdata['approver_id'] = 0;
				$postdata['privilege_id'] = 0;
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
		
		public function getAdd() {
	        $this->cbLoader();
	        if(!CRUDBooster::isCreate() && $this->global_privilege == false) {
				CRUDBooster::redirect(CRUDBooster::adminPath(), trans('crudbooster.denied_access'));
			}
			
			$data['page_title'] = 'Create Order Schedule';
			
			$data['privileges'] = DB::table('cms_privileges')->select('*')->whereNotIn('id',[1,13])->get();

			return $this->view("order-schedule.schedule_add", $data);
	    }
	    
	    public function getEdit($id) {
	        $this->cbLoader();
	        if(!CRUDBooster::isUpdate() && $this->global_privilege == false) {
				CRUDBooster::redirect(CRUDBooster::adminPath(), trans('crudbooster.denied_access'));
			}
			
			$data['page_title'] = 'Edit Order Schedule';
			
			$data['privileges'] = DB::table('cms_privileges')->select('*')->whereNotIn('id',[1,13])->get();

			$editData = OrderSchedule::where('id', $id)->first();
			$data['oldData'] = $editData;
			$data['oldStores'] = explode(",",$editData->privilege_id);
			
			return $this->view("order-schedule.schedule_edit", $data);
	    }
        
        public function deactivateSchedule(){
            $current_date = Carbon::now();
            $current_schedule = OrderSchedule::where('status','ACTIVE')->orderBy('id','desc')->first();
            if($current_date->gte($current_schedule->end_date)){
                DB::beginTransaction();
                try {
					OrderSchedule::where('status','ACTIVE')->update(['status'=>'INACTIVE', 'updated_at'=>date('Y-m-d H:i:s')]);
					DB::commit();
				} catch (\Exception $e) {
					DB::rollback();
				}
                
            }
		}
		
		public function privilegeListing($ids) {
    		$privilege = explode(",", $ids);
    		return CmsPrivileges::whereIn('id', $privilege)->pluck('name');
    	}

	}