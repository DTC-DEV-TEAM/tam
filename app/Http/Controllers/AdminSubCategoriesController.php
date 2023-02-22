<?php namespace App\Http\Controllers;

	use Session;
	use Request;
	use DB;
	use CRUDBooster;
	use App\Models\SubCategory;

	class AdminSubCategoriesController extends \crocodicstudio\crudbooster\controllers\CBController {

        public function __construct() {
			// Register ENUM type
			//$this->request = $request;
			DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping("enum", "string");
		}

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "id";
			$this->limit = "20";
			$this->orderby = "id,asc";
			$this->global_privilege = false;
			$this->button_table_action = true;
			$this->button_bulk_action = false;
			$this->button_action_style = "button_icon";
			$this->button_add = false;
			$this->button_edit = true;
			$this->button_delete = FALSE;
			$this->button_detail = true;
			$this->button_show = true;
			$this->button_filter = true;
			$this->button_import = false;
			$this->button_export = true;
			$this->table = "class";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			
			$this->col[] = ["label"=>"FA Code","name"=>"category_code"];
			$this->col[] = ["label"=>"Description","name"=>"class_description"];
			$this->col[] = ["label"=>"Category","name"=>"category_id","join"=>"category,category_description"];
			$this->col[] = ["label"=>"Status","name"=>"class_status"];
			$this->col[] = ["label"=>"Limit Code","name"=>"limit_code"];
			$this->col[] = ["label" => "Created At", "name" => "created_at"];
			$this->col[] = ["label" => "Updated At", "name" => "updated_at"];
			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'Category','name'=>'category_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-5','datatable'=>'category,category_description'];
			$this->form[] = ['label'=>'Sub Category Name','name'=>'class_description','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-5'];

			if(CRUDBooster::getCurrentMethod() == 'getEdit' || CRUDBooster::getCurrentMethod() == 'postEditSave' || CRUDBooster::getCurrentMethod() == 'getDetail') {
				
				$this->form[] = ['label'=>'Status','name'=>'class_status','type'=>'select','validation'=>'required','width'=>'col-sm-5','dataenum'=>'ACTIVE;INACTIVE'];

				//$this->form[] = ['label'=>'Useful Life','name'=>'useful_life','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-5'];
			}



			if(CRUDBooster::getCurrentMethod() == 'getDetail'){
				//$this->form[] = ["label"=>"Created By","name"=>"created_by",'type'=>'select',"datatable"=>"cms_users,name"];
				$this->form[] = ['label'=>'Created Date','name'=>'created_at', 'type'=>'datetime'];
				//$this->form[] = ["label"=>"Updated By","name"=>"updated_by",'type'=>'select',"datatable"=>"cms_users,name"];
				$this->form[] = ['label'=>'Updated Date','name'=>'updated_at', 'type'=>'datetime'];
			}
			
			//$this->form[] = ['label'=>'Created By','name'=>'created_by','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-5'];
			//$this->form[] = ['label'=>'Updated By','name'=>'updated_by','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-5'];
			# END FORM DO NOT REMOVE THIS LINE

			# OLD START FORM
			//$this->form = [];
			//$this->form[] = ["label"=>"Category Id","name"=>"category_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"category,id"];
			//$this->form[] = ["label"=>"Class Code","name"=>"class_code","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Class Description","name"=>"class_description","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Class Status","name"=>"class_status","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
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
			if(CRUDBooster::getCurrentMethod() == 'getIndex'){
				$this->index_button[] = ["label"=>"Add Category","icon"=>"fa fa-plus-circle","url"=>CRUDBooster::mainpath('add-category'),"color"=>"success"];
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

				//$('#category_id').attr('disabled', 'true');

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
	    	if($column_index == 5){
				if($column_value != null){
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
	        $fields = Request::all();
			$from_code = $fields['from_code'];
			$from_to = $fields['from_to'];
			$category_id = $fields['category_id'];
			$category_description = $fields['category_description'];
			
			$checkRowDbExist = DB::table('class')->select(DB::raw("(category_code) AS code"))->get()->toArray();
			$checkRowDbColumnExist = array_column($checkRowDbExist, 'code');
			//code check validity
			$fromCode   = SubCategory::select('*')->where('from_code','>=', $from_code)->orWhere('to_code','>=', $from_code)->get()->count();
			$FromtoCode = SubCategory::select('*')->where('to_code','>=', $from_to)->orWhere('from_code','>=', $from_to)->get()->count();
			if (in_array($from_code . ' - ' . $from_to, $checkRowDbColumnExist)) {
				return CRUDBooster::redirect(CRUDBooster::mainpath("add-category"),trans("crudbooster.alert_exist_data_danger",['code'=>$from_code . ' - ' . $from_to]),"danger");
			}else if($fromCode != 0){
				return CRUDBooster::redirect(CRUDBooster::mainpath("add-category"),trans("crudbooster.alert_invalid_code_danger",['code'=>$from_code . ' - ' . $from_to]),"danger");
			}else if($FromtoCode != 0){
				return CRUDBooster::redirect(CRUDBooster::mainpath("add-category"),trans("crudbooster.alert_invalid_code_danger",['code'=>$from_code . ' - ' . $from_to]),"danger");
			}else{
				$postdata['from_code']= $from_code;
				$postdata['to_code']= $from_to;
				$postdata['category_code']= $from_code . " - " . $from_to;
				$postdata['category_id'] = $category_id;
				$postdata['class_description'] = $category_description;
				$postdata['class_status'] = "ACTIVE";
				$postdata['created_by']=CRUDBooster::myId();
			}
			
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
			//unset($postdata['category_id']);

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

		/*****CUSTOM FUNCTION AREA */ 
		public function getAddCategory() {

			if(!CRUDBooster::isCreate() && $this->global_privilege == false) {
				CRUDBooster::redirect(CRUDBooster::adminPath(), trans('crudbooster.denied_access'));
			}

			$this->cbLoader();
			$data['page_title'] = 'Add Sub Category';
			$data['categories'] = DB::table('category')->where('category_status', 'ACTIVE')->whereIn('id', [5,1])->orderby('category_description', 'asc')->get();
			return $this->view("sub_categories.add-sub-category", $data);

		}

		public function getSubCatCodeRangeFrom(Request $request){
			$fields = Request::all();
			$code = $fields['code'] ? $fields['code'] : "";
			$data = [];
			$countCode = SubCategory::select('*')->where('from_code','>=', $code)->orWhere('to_code','>=', $code)->get()->count();
			if($countCode) {
				$data['item'] = "<span id='notif' class='label label-danger'> Invalid Code</span>";
				$data['disabled'] = 1;
			  }else{
				$data['item'] = "<span id='notif' class='label label-success'> Code Available.</span>";
				$data['disabled'] = 0;
			  }
			  
			echo json_encode($data);
		}

		public function getSubCatCodeRangeTo(Request $request){
			$fields = Request::all();
			$code = $fields['code'] ? $fields['code'] : "";
			$data = [];
			$countCode = SubCategory::select('*')->where('to_code','>=', $code)->orWhere('from_code','>=', $code)->get()->count();
			  if($countCode) {
				$data['item'] = "<span id='notif' class='label label-danger'> Invalid Code</span>";
				$data['disabled'] = 1;
			  }else{
				$data['item'] = "<span id='notif' class='label label-success'> Code Available.</span>";
				$data['disabled'] = 0;
			  }
			echo json_encode($data);
		}

		public function getSubCatCodeRangeAll(Request $request){
			$fields = Request::all();
			$code = $fields['code'];
			dd($code);
			$data = SubCategory::select('from_code')->get();
			echo json_encode($data);
		}

	}