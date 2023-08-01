<?php namespace App\Http\Controllers;

	use Session;
	use Request;
	use DB;
	use CRUDBooster;
	use App\Models\Applicant;
	use App\Statuses;
	use App\Models\ErfHeaderRequest;
	use App\Imports\ApplicantUpload;
	use Maatwebsite\Excel\Facades\Excel;
	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Reader\Exception;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
	use PhpOffice\PhpSpreadsheet\IOFactory;
	use Illuminate\Support\Facades\Log;
	use Illuminate\Support\Facades\Redirect;
	use Illuminate\Contracts\Cache\LockTimeoutException;
	use Carbon\Carbon;

	class AdminApplicantSummaryReportController extends \crocodicstudio\crudbooster\controllers\CBController {

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "first_name";
			$this->limit = "20";
			$this->orderby = "id,desc";
			$this->global_privilege = false;
			$this->button_table_action = true;
			$this->button_bulk_action = true;
			$this->button_action_style = "button_icon";
			$this->button_add = true;
			$this->button_edit = true;
			$this->button_delete = false;
			$this->button_detail = true;
			$this->button_show = true;
			$this->button_filter = true;
			$this->button_import = false;
			$this->button_export = false;
			$this->table = "applicant_table";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Erf Number","name"=>"erf_number"];
			$this->col[] = ["label"=>"Status","name"=>"status"];
			$this->col[] = ["label"=>"First Name","name"=>"first_name"];
			$this->col[] = ["label"=>"Last Name","name"=>"last_name"];
			$this->col[] = ["label"=>"Full Name","name"=>"full_name"];
			$this->col[] = ["label"=>"Screen Date","name"=>"screen_date"];
			$this->col[] = ["label"=>"Created By","name"=>"created_by"];
			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];

			# END FORM DO NOT REMOVE THIS LINE

			# OLD START FORM
			//$this->form = [];
			//$this->form[] = ["label"=>"Erf Number","name"=>"erf_number","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Status","name"=>"status","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"First Name","name"=>"first_name","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Last Name","name"=>"last_name","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Full Name","name"=>"full_name","type"=>"text","required"=>TRUE,"validation"=>"required|string|min:3|max:70","placeholder"=>"You can only enter the letter only"];
			//$this->form[] = ["label"=>"Screen Date","name"=>"screen_date","type"=>"date","required"=>TRUE,"validation"=>"required|date"];
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
	        //Your code here

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

		//customize index
		public function getIndex() {
			$this->cbLoader();
			 if(!CRUDBooster::isView() && $this->global_privilege == false) CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));
			 $data = [];
			 $data['page_title'] = 'Applicant Summary Report';
			 $data['getData'] = Applicant::
		
			 leftJoin('applicant_table as jo_done', function($join) 
			 {
				 $join->on('applicant_table.id', '=', 'jo_done.id')
				 ->where('applicant_table.status',31);
			 })
			 ->leftJoin('applicant_table as first_interview', function($join) 
			 {
				 $join->on('applicant_table.id', '=', 'first_interview.id')
				 ->where('applicant_table.status',34);
			 })
			 ->leftJoin('applicant_table as final_interview', function($join) 
			 {
				 $join->on('applicant_table.id', '=', 'final_interview.id')
				 ->where('applicant_table.status',35);
			 })
			 ->leftJoin('applicant_table as cancelled', function($join) 
			 {
				 $join->on('applicant_table.id', '=', 'cancelled.id')
				 ->where('applicant_table.status',8);
			 })
			 ->leftJoin('applicant_table as rejected', function($join) 
			 {
				 $join->on('applicant_table.id', '=', 'rejected.id')
				 ->where('applicant_table.status',5);
			 })
			 ->leftJoin('applicant_table as for_comparison', function($join) 
			 {
				 $join->on('applicant_table.id', '=', 'for_comparison.id')
				 ->where('applicant_table.status',42);
			 })
			 	 
			 ->select(
				'applicant_table.*',
			    DB::raw('COUNT(jo_done.erf_number) as jo_done'),
				DB::raw('COUNT(first_interview.erf_number) as first_interview'),
				DB::raw('COUNT(final_interview.erf_number) as final_interview'),
				DB::raw('COUNT(cancelled.erf_number) as cancelled'),
				DB::raw('COUNT(rejected.erf_number) as rejected'),
				DB::raw('COUNT(for_comparison.erf_number) as for_comparison'),
				'jo_done.status as jo_status',
				'first_interview.status as first_interview_status',
				'final_interview.status as final_interview_status',
				'cancelled.status as cancelled_status',
				'rejected.status as rejected_status',
				'for_comparison.status as for_comparison_status',
				)
				->groupBy('applicant_table.erf_number')
				->get();

			$data['erf_number'] = DB::table('erf_header_request')->get();
			//dd($data['getData']);
			 return $this->view('applicant.summary_report',$data);
		  }

		  public function getSummaryReport($search){		
			$erf_number = $fields['erf_number'];
			$status = $fields['status'];
			$screen_date = $fields['screen_date'];
		
			$data = [];
            $filters = [];
			$data['page_title'] = 'Applicant Summary Report Per Status';
			$applicant = Applicant::orderby('applicant_table.id','desc');
			$applicant->where(DB::raw('CONCAT(applicant_table.erf_number,"-",applicant_table.status)'),'=',$search);
			$applicant->leftjoin('statuses', 'applicant_table.status', '=', 'statuses.id')
			->leftjoin('cms_users as created', 'applicant_table.created_by', '=', 'created.id')
			->leftjoin('cms_users as updated_by', 'applicant_table.updated_by', '=', 'updated_by.id')
			->select(
				'applicant_table.*',
				'applicant_table.id as apid',
				'statuses.status_description as status_description',
				'created.name as created_name',
				'updated_by.name as updated_by'
			);
			
			$data['summary_report'] = $applicant->get();

			return $this->view("applicant.applicant-summary-report-per-status", $data);
		}

	}