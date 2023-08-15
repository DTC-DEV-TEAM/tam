<?php namespace App\Http\Controllers;

	use Session;
	//use Request;
	use Illuminate\Http\Request;
	use DB;
	use CRUDBooster;
	use App\Models\PositionsModel;
	use App\Imports\PositionsImport;
	use App\Department;
	use App\Models\CmsPrivileges;
	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Reader\Exception;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
	use PhpOffice\PhpSpreadsheet\IOFactory;
	use Excel;

	class AdminPositionsController extends \crocodicstudio\crudbooster\controllers\CBController {

        public function __construct() {
			// Register ENUM type
			//$this->request = $request;
			DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping("enum", "string");
		}

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "id";
			$this->limit = "20";
			$this->orderby = "department_id,asc";
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
			$this->table = "positions";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Departments","name"=>"department_id"];
			
			$this->col[] = ["label"=>"Position Description","name"=>"position_description"];
			$this->col[] = ["label"=>"Privilege","name"=>"privilege_id"];
			$this->col[] = ["label"=>"Status","name"=>"status"];
			$this->col[] = ["label" => "Created By", "name" => "created_by", "join" => "cms_users,name"];
			$this->col[] = ["label" => "Created At", "name" => "created_at"];
			$this->col[] = ["label" => "Updated By", "name" => "updated_by", "join" => "cms_users,name"];
			$this->col[] = ["label" => "Updated At", "name" => "updated_at"];
			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];

			$this->form[] = ['label'=>'Department','name'=>'department_id','type'=>'select2-new','width'=>'col-sm-5','datatable'=>'departments,department_name','datatable_where'=>"status = 'ACTIVE'"];

			$this->form[] = ['label'=>'Position Description','name'=>'position_description','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-5'];

			$this->form[] = ['label'=>'Privilege','name'=>'privilege_id','type'=>'select2','width'=>'col-sm-5','datatable'=>'cms_privileges,name'];
			
			if(CRUDBooster::getCurrentMethod() == 'getEdit' || CRUDBooster::getCurrentMethod() == 'postEditSave' || CRUDBooster::getCurrentMethod() == 'getDetail') {
				$this->form[] = ['label'=>'Status','name'=>'status','type'=>'select','validation'=>'required','width'=>'col-sm-5','dataenum'=>'ACTIVE;INACTIVE'];
			}

			if(CRUDBooster::getCurrentMethod() == 'getDetail'){
				$this->form[] = ["label"=>"Created By","name"=>"created_by",'type'=>'select',"datatable"=>"cms_users,name"];
				$this->form[] = ['label'=>'Created Date','name'=>'created_at', 'type'=>'datetime'];
				$this->form[] = ["label"=>"Updated By","name"=>"updated_by",'type'=>'select',"datatable"=>"cms_users,name"];
				$this->form[] = ['label'=>'Updated Date','name'=>'updated_at', 'type'=>'datetime'];
			}
			# END FORM DO NOT REMOVE THIS LINE

			# OLD START FORM
			//$this->form = [];
			//$this->form[] = ["label"=>"Position Description","name"=>"position_description","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Status","name"=>"status","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Created By","name"=>"created_by","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Updated By","name"=>"updated_by","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
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
			if(CRUDBooster::isSuperadmin()){
				$this->index_button[] = [
					"title"=>"Import Position",
					"label"=>"Import Position",
					"icon"=>"fa fa-download",
					"color"=>"success",
					"url"=>CRUDBooster::mainpath('positions-upload')];
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
			$this->script_js = "
			$(document).ready(function() {
				$('#department_id').select2();
				let x = $(location).attr('pathname').split('/');
				let add_action = x.includes('add');
				let edit_action = x.includes('edit');
				if (edit_action){
					var a  = 	department_id.split(',').length;
					var b = 	department_id.split(',');
					var selectedValues = new Array();
	
					for (let i = 0; i < a; i++) {
					
						selectedValues[i] = b[i];
	
						$('#department_id').val(selectedValues);
					}
				}
				
				$('#position_description').keyup(function() {
					this.value = this.value.toLocaleUpperCase();
				});
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
	    	if($column_index == 2){
				$departmentLists = $this->departmentListing($column_value);
				
				foreach ($departmentLists as $value) {
					$col_values .= '<span stye="display: block;" class="label label-info">'.$value.'</span><br>';
				}
				$column_value = $col_values;
			}
			if($column_index == 4){
				$privilegeLists = $this->privilegeListing($column_value);
				
				foreach ($privilegeLists as $value) {
					$col_values .= '<span stye="display: block;" class="label label-info">'.$value.'</span><br>';
				}
				$column_value = $col_values;
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
	        $departmentIds = array();
    		$department = json_encode($postdata['department_id'], true);
    		$departmentArray1 = explode(",", $department);
    
    		foreach ($departmentArray1 as $key => $value) {
    			$departmentIds[$key] = preg_replace("/[^0-9]/","",$value);
    		}
    
    		$postdata['department_id'] = implode(",", $departmentIds);

			$postdata['created_by']=CRUDBooster::myId();

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
			$departmentIds = array();
    		$department = json_encode($postdata['department_id'], true);
    		$departmentArray1 = explode(",", $department);
    
    		foreach ($departmentArray1 as $key => $value) {
    			$departmentIds[$key] = preg_replace("/[^0-9]/","",$value);
    		}

    		$postdata['department_id'] = implode(",", $departmentIds);

			$postdata['updated_by']=CRUDBooster::myId();
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

        public function uploadPositionsView() {
			// if(!CRUDBooster::isSuperadmin()) {    
			// 	CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
			// }
			$data['page_title']= 'Positions Upload';
			return view('import.positions-import', $data)->render();
		}

		public function positionsUpload(Request $request) {
			$path_excel = $request->file('import_file')->store('temp');
			$path = storage_path('app').'/'.$path_excel;
			Excel::import(new PositionsImport, $path);	
			CRUDBooster::redirect(CRUDBooster::adminpath('positions'), trans("Upload Successfully!"), 'success');
		}

		public function departmentListing($ids) {
    		$departmentIds = explode(",", $ids);
    		return Department::whereIn('id', $departmentIds)->pluck('department_name');
    	}

		public function privilegeListing($ids) {
    		$privilegeIds = explode(",", $ids);
    		return CmsPrivileges::whereIn('id', $privilegeIds)->pluck('name');
    	}

	}