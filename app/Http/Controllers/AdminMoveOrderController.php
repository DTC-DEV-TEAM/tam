<?php namespace App\Http\Controllers;

	use Session;
	use Request;
	use DB;
	use CRUDBooster;
	use App\HeaderRequest;
	use App\BodyRequest;
	use App\ApprovalMatrix;
	use App\StatusMatrix;
	use App\MoveOrder;
	//use Illuminate\Http\Request;
	//use Illuminate\Support\Facades\Input;
	use Illuminate\Support\Facades\Log;
	use Illuminate\Support\Facades\Redirect;
	use App\Exports\ExportConso;
	use Maatwebsite\Excel\Facades\Excel;
	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Reader\Exception;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
	use PhpOffice\PhpSpreadsheet\IOFactory;

	class AdminMoveOrderController extends \crocodicstudio\crudbooster\controllers\CBController {

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "mo_reference_number";
			$this->limit = "20";
			$this->orderby = "id,desc";
			$this->global_privilege = false;
			$this->button_table_action = true;
			$this->button_bulk_action = true;
			$this->button_action_style = "button_icon";
			$this->button_add = false;
			$this->button_edit = false;
			$this->button_delete = true;
			$this->button_detail = false;
			$this->button_show = true;
			$this->button_filter = true;
			$this->button_import = false;
			$this->button_export = false;
			$this->table = "mo_body_request";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];

		

			$this->col[] = ["label"=>"Status","name"=>"status_id","join"=>"statuses,status_description"];

			$this->col[] = ["label"=>"Reference Number","name"=>"mo_reference_number"];

			$this->col[] = ["label"=>"Request Type","name"=>"header_request_id","join"=>"header_request,request_type_id"];

			$this->col[] = ["label"=>"Employee Name","name"=>"request_created_by","join"=>"cms_users,bill_to"];
			$this->col[] = ["label"=>"Department","name"=>"header_request_id","join"=>"header_request,department"];

			$this->col[] = ["label"=>"MO By","name"=>"header_request_id","join"=>"header_request,mo_by"];
			$this->col[] = ["label"=>"MO Date","name"=>"header_request_id","join"=>"header_request,mo_at"];

			$this->col[] = ["label"=>"MO Plug","name"=>"mo_plug","visible"=>false];

			$this->col[] = ["label"=>"To Pick","name"=>"to_pick","visible"=>false];

			$this->col[] = ["label"=>"To Print","name"=>"to_print","visible"=>false];

			//$this->col[] = ["label"=>"To Print","name"=>"to_print","visible"=>false];

			

			//$this->col[] = ["label"=>"Requested By","name"=>"created_by","join"=>"cms_users,name"];
			//$this->col[] = ["label"=>"Requested Date","name"=>"created_at"];


			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			# END FORM DO NOT REMOVE THIS LINE

			# OLD START FORM
			//$this->form = [];
			//$this->form[] = ["label"=>"Reference Number","name"=>"reference_number","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Status Id","name"=>"status_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"status,id"];
			//$this->form[] = ["label"=>"Employee Name","name"=>"employee_name","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Company Name","name"=>"company_name","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Position","name"=>"position","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Department","name"=>"department","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Store Branch","name"=>"store_branch","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Purpose","name"=>"purpose","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Conditions","name"=>"conditions","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Quantity Total","name"=>"quantity_total","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Cost Total","name"=>"cost_total","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Total","name"=>"total","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Approved By","name"=>"approved_by","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Approved At","name"=>"approved_at","type"=>"datetime","required"=>TRUE,"validation"=>"required|date_format:Y-m-d H:i:s"];
			//$this->form[] = ["label"=>"Created By","name"=>"created_by","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Updated By","name"=>"updated_by","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Rejected At","name"=>"rejected_at","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Requestor Comments","name"=>"requestor_comments","type"=>"textarea","required"=>TRUE,"validation"=>"required|string|min:5|max:5000"];
			//$this->form[] = ["label"=>"Request Type Id","name"=>"request_type_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"request_type,id"];
			//$this->form[] = ["label"=>"Privilege Id","name"=>"privilege_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"privilege,id"];
			//$this->form[] = ["label"=>"Approver Comments","name"=>"approver_comments","type"=>"textarea","required"=>TRUE,"validation"=>"required|string|min:5|max:5000"];
			//$this->form[] = ["label"=>"To Reco","name"=>"to_reco","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"It Comments","name"=>"it_comments","type"=>"textarea","required"=>TRUE,"validation"=>"required|string|min:5|max:5000"];
			//$this->form[] = ["label"=>"Recommended By","name"=>"recommended_by","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Recommended At","name"=>"recommended_at","type"=>"datetime","required"=>TRUE,"validation"=>"required|date_format:Y-m-d H:i:s"];
			//$this->form[] = ["label"=>"Cancelled By","name"=>"cancelled_by","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Cancelled At","name"=>"cancelled_at","type"=>"datetime","required"=>TRUE,"validation"=>"required|date_format:Y-m-d H:i:s"];
			//$this->form[] = ["label"=>"Purchased1 By","name"=>"purchased1_by","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Purchased1 At","name"=>"purchased1_at","type"=>"datetime","required"=>TRUE,"validation"=>"required|date_format:Y-m-d H:i:s"];
			//$this->form[] = ["label"=>"Purchased2 By","name"=>"purchased2_by","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Purchased2 At","name"=>"purchased2_at","type"=>"datetime","required"=>TRUE,"validation"=>"required|date_format:Y-m-d H:i:s"];
			//$this->form[] = ["label"=>"Purchased3 By","name"=>"purchased3_by","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Purchased3 At","name"=>"purchased3_at","type"=>"datetime","required"=>TRUE,"validation"=>"required|date_format:Y-m-d H:i:s"];
			//$this->form[] = ["label"=>"Quote Date","name"=>"quote_date","type"=>"date","required"=>TRUE,"validation"=>"required|date"];
			//$this->form[] = ["label"=>"Po Date","name"=>"po_date","type"=>"date","required"=>TRUE,"validation"=>"required|date"];
			//$this->form[] = ["label"=>"Po Number","name"=>"po_number","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Employee Dr Date","name"=>"employee_dr_date","type"=>"date","required"=>TRUE,"validation"=>"required|date"];
			//$this->form[] = ["label"=>"Dr Number","name"=>"dr_number","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Received By","name"=>"received_by","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Received At","name"=>"received_at","type"=>"datetime","required"=>TRUE,"validation"=>"required|date_format:Y-m-d H:i:s"];
			//$this->form[] = ["label"=>"Picked By","name"=>"picked_by","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Picked At","name"=>"picked_at","type"=>"datetime","required"=>TRUE,"validation"=>"required|date_format:Y-m-d H:i:s"];
			//$this->form[] = ["label"=>"Closed By","name"=>"closed_by","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Closed At","name"=>"closed_at","type"=>"datetime","required"=>TRUE,"validation"=>"required|date_format:Y-m-d H:i:s"];
			//$this->form[] = ["label"=>"Application","name"=>"application","type"=>"textarea","required"=>TRUE,"validation"=>"required|string|min:5|max:5000"];
			//$this->form[] = ["label"=>"Application Others","name"=>"application_others","type"=>"textarea","required"=>TRUE,"validation"=>"required|string|min:5|max:5000"];
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

				$for_printing     = DB::table('statuses')->where('id', 17)->value('id');

				$for_receiving    = DB::table('statuses')->where('id', 16)->value('id');

				$for_printing_adf = DB::table('statuses')->where('id', 18)->value('id');
				$cancelled        = DB::table('statuses')->where('id', 8)->value('id');

				//dd("[status_id]");

				//$arf_header 				= HeaderRequest::where(['id' => $HeaderID->header_request_id])->first();

				//dd($id);
				// or [to_print] == 1
				$this->addaction[] = ['title'=>'Print','url'=>CRUDBooster::mainpath('getRequestPrintADF/[id]'),'icon'=>'fa fa-print', "showIf"=>"[status_id] == $for_printing_adf"];

				$this->addaction[] = ['title'=>'Print','url'=>CRUDBooster::mainpath('getRequestPrintPickList/[id]'),'icon'=>'fa fa-print', "showIf"=>"[mo_plug] == 0"];

				$this->addaction[] = ['title'=>'Detail','url'=>CRUDBooster::mainpath('getDetailOrdering/[id]'),'icon'=>'fa fa-eye', "showIf"=>"[mo_plug] == 1"];

				//$this->addaction[] = ['title'=>'Update','url'=>CRUDBooster::mainpath('getRequestOrdering/[id]'),'icon'=>'fa fa-pencil', "showIf"=>"[mo_plug] == 0"];

				//$this->addaction[] = ['title'=>'Detail','url'=>CRUDBooster::mainpath('getDetailOrdering/[id]'),'icon'=>'fa fa-eye', "showIf"=>"[mo_plug] == 1"];
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
			if(CRUDBooster::getCurrentMethod() == 'getIndex'){
				if(in_array(CRUDBooster::myPrivilegeId(), [1,6,20])){
					$this->index_button[] = ["label"=>"Consolidation","icon"=>"fa fa-download",'url'=>"javascript:showConsoExport()"];
					$this->index_button[] = ["label"=>"Upload PO","icon"=>"fa fa-upload","url"=>CRUDBooster::adminpath('for_purchasing/po-upload'),'color'=>'success'];
				}
				if(in_array(CRUDBooster::myPrivilegeId(), [1,5,9,17])){
					$this->index_button[] = ["label"=>"MO Request","icon"=>"fa fa-files-o","url"=>CRUDBooster::mainpath('add-mo'),"color"=>"success"];
					$this->index_button[] = ["title"=>"Export","label"=>"Export","icon"=>"fa fa-download","url"=>CRUDBooster::mainpath('GetExtractMO').'?'.urldecode(http_build_query(@$_GET))];
				}
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
				$('.date').datetimepicker({
						viewMode: \"days\",
						format: \"YYYY-MM-DD\",
						dayViewHeaderFormat: \"MMMM YYYY\",
				});

				$('#category').select2({});

				$('#exportBtn').click(function(event) {
					event.preventDefault();
					var from = $('#from').val();
					var to = $('#to').val();
					if(from > to){
						swal({
							type: 'error',
							title: 'Invalid Date of Range',
							icon: 'error',
							confirmButtonColor: \"#367fa9\",
						}); 
						event.preventDefault(); // cancel default behavior
						return false;
					}else{
						$('#filterForm').submit(); 
					}
				   
				});
		    });
			function showConsoExport() {
				$('#modal-conso-export').modal('show');
			}
			
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
	        $this->pre_index_html = "
            
			   <!-- Modal HTML -->
			   <div id=\"modal-conso-export\" class=\"modal fade\" tabindex=\"-1\">
				   <div class=\"modal-dialog\">
					   <div class=\"modal-content\">
						   <div class=\"modal-header\">
						   <button type=\"button\" class=\"close\" data-dismiss=\"modal\">&times;</button>
							   <h4 class=\"modal-title\"><strong>Filter Export</strong></h4>
							  
						   </div>
						   <div class=\"modal-body\">
						    <form  id=\"filterForm\" method='post' target='_blank' name=\"filterForm\" action='".route('export-conso')."'>
						      <div class='row'>
							  <input type=\"hidden\" name=\"request_id\" id=\"request_id\">
								<input type=\"hidden\" value='".csrf_token()."' name=\"_token\" id=\"token\">

								<div class='col-md-6'>
								  <div class=\"form-group\">
									<label class=\"control-label require\"> Approved Date From</label>
								     <input type\"text\" class=\"form-control date\" name=\"from\"  id=\"from\" placeholder=\"Please Select Date From\">
								  </div>
								</div>

								<div class='col-md-6'>
								   <div class=\"form-group\">
                                    <label class=\"control-label require\"> Approved Date To</label>
								    <input type\"text\" class=\"form-control date\" name=\"to\"  id=\"to\" placeholder=\"Please Select Date To\">
								   </div>
								</div>

								<div class=\"col-md-6\">
									<div class=\"form-group\">
										<label class=\"control-label require\">Category</label>
										<select selected data-placeholder=\"-- Select Category --\" id=\"category\" name=\"category\" class=\"form-select erf\" style=\"width:100%;\">
											<option value=\"\"></option>
											<option value=\"1\">IT ASSETS</option>
												<option value=\"5\">FA</option>
												<option value=\"7\">SUPPLIES</option>
										</select>
									</div>
								</div>  
								
								<br>	
							  </div>
						    </form>
						   </div>
						   <div class=\"modal-footer\">
							   <button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Cancel</button>
							   <button type='button' id=\"exportBtn\" class=\"btn btn-primary btn-sm\">
                                <i class=\"fa fa-save\"></i> Export
                                </button>
						   </div>
					   </div>
				   </div>
			   </div>
			
			";
	        
	        
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
	        $this->load_js[] = asset("datetimepicker/bootstrap-datetimepicker.min.js");
	        
	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | Add css style at body 
	        | ---------------------------------------------------------------------- 
	        | css code in the variable 
	        | $this->style_css = ".style{....}";
	        |
	        */
	        $this->style_css = NULL;
	        $this->style_css = "
			.fa.fa-plus-circle{
				color:green;
				font-size:18px;
				margin-top: 3px;
			}
			.modal-content  {
				-webkit-border-radius: 5px !important;
				-moz-border-radius: 5px !important;
				border-radius: 5px !important; 
			}
			.select2-selection__choice{
				font-size:14px !important;
				color:black !important;
			}
			.select2-selection__rendered {
				line-height: 31px !important;
			}
			.select2-container .select2-selection--single {
				height: 35px !important;
			}
			.select2-selection__arrow {
				height: 34px !important;
			}
			";
	        
	        
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
			$this->load_css[] = asset("datetimepicker/bootstrap-datetimepicker.min.css");
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

			$for_printing_adf = DB::table('statuses')->where('id', 18)->value('id');
			
			$cancelled  = 		DB::table('statuses')->where('id', 8)->value('id');

			$List = MoveOrder::whereNull('closed_at')->whereNotNull('mo_reference_number')->whereNotNull('header_request_id')->orderby('mo_body_request.status_id', 'asc')->orderby('mo_body_request.id', 'asc')->get();

			$list_array = array();

			$id_array = array();

			foreach($List as $matrix){

				if($matrix->status_id == $cancelled){

					$mo_count_cancelled = MoveOrder::
										  where(['mo_reference_number' => $matrix->mo_reference_number])
										  ->where(['status_id' => $cancelled])
										  ->count();


					if($mo_count_cancelled == 1){

						//if(! in_array($matrix->mo_reference_number,$list_array)){

							//array_push($list_array, $matrix->mo_reference_number);

							array_push($id_array, $matrix->id);

						//}

					}else{

						if(! in_array($matrix->mo_reference_number,$list_array)){

							array_push($list_array, $matrix->mo_reference_number);
	
							array_push($id_array, $matrix->id);
	
						}

					}

				}else{

					if(! in_array($matrix->mo_reference_number,$list_array)){

						array_push($list_array, $matrix->mo_reference_number);

						array_push($id_array, $matrix->id);

					}

				}
					

			}

			$list_string = implode(",",$id_array);

			$MOList = array_map('intval',explode(",",$list_string));
			if(in_array(CRUDBooster::myPrivilegeId(),[5,17])){
			    $query->whereIn('mo_body_request.id', $MOList)->where('header_request.request_type_id', 1);
			}else if(in_array(CRUDBooster::myPrivilegeId(),[9])){
				$query->whereIn('mo_body_request.id', $MOList)->where('header_request.request_type_id', 5);
			}else{
				$query->whereIn('mo_body_request.id', $MOList);
			}
			//dd($list_string);
	            
	    }

	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate row of index table html 
	    | ---------------------------------------------------------------------- 
	    |
	    */    
	    public function hook_row_index($column_index,&$column_value) {	        
	    	//Your code here
			$for_move_order =  	DB::table('statuses')->where('id', 14)->value('status_description');
			$for_picking =  	DB::table('statuses')->where('id', 15)->value('status_description');
			$for_printing =  	DB::table('statuses')->where('id', 17)->value('status_description');
			$for_receiving =  	DB::table('statuses')->where('id', 16)->value('status_description');
			$for_printing_adf = DB::table('statuses')->where('id', 18)->value('status_description');
			$for_closing  = 	DB::table('statuses')->where('id', 19)->value('status_description');
			$closed  = 			DB::table('statuses')->where('id', 13)->value('status_description');
			$cancelled  = 		DB::table('statuses')->where('id', 8)->value('status_description');

			if($column_index == 2){

				if($column_value == $for_move_order){

					$column_value = '<span class="label label-info">'.$for_move_order.'</span>';

				}elseif($column_value == $for_printing){

					$column_value = '<span class="label label-info">'.$for_printing.'</span>';

				}elseif($column_value == $for_picking){

					$column_value = '<span class="label label-info">'.$for_picking.'</span>';

				}elseif($column_value == $for_receiving){

					$column_value = '<span class="label label-info">'.$for_receiving.'</span>';

				}elseif($column_value == $for_printing_adf){

					$column_value = '<span class="label label-info">'.$for_printing_adf.'</span>';

				}elseif($column_value == $for_closing){
					$column_value = '<span class="label label-info">'.$for_closing.'</span>';
				}else if($column_value == $closed){
					$column_value = '<span class="label label-success">'.$closed.'</span>';
				}else if($column_value == $cancelled){
					$column_value = '<span class="label label-danger">'.$cancelled.'</span>';
				}

			}

			if($column_index == 4){

				$request_type = 			DB::table('requests')->where(['id' => $column_value])->first();
				
				if($column_value == $request_type->id){

					$column_value = $request_type->request_name;

				}


			}

			if($column_index == 5){

				$request_type = 			DB::table('cms_users')->where(['id' => $column_value])->first();
				
				if($column_value == $request_type->id){

					$column_value = $request_type->bill_to;

				}


			}


			if($column_index == 6){

				$request_type = 			DB::table('departments')->where(['id' => $column_value])->first();
				
				if($column_value == $request_type->id){

					$column_value = $request_type->department_name;

				}


			}


			if($column_index == 7){

				$request_type = 			DB::table('cms_users')->where(['id' => $column_value])->first();
				
				if($column_value == $request_type->id){

					$column_value = $request_type->name;

				}


			}


			if($column_index == 9){

				$arf_header 				= HeaderRequest::where(['id' => $column_value])->first();
				
				if($column_value == $arf_header->id){

					$column_value = $arf_header->to_print;

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
		

			$fields = Request::all();
			//dd($fields);
			$cont = (new static)->apiContext;

			$dataLines1 = array();

			$locationArray = array();

			$Header_id 							= $fields['header_request_id'];
			$digits_code 						= $fields['add_digits_code'];
			//$asset_code 						= $fields['add_asset_code'];
			$item_description 					= $fields['add_item_description'];
			$serial_no 							= $fields['add_serial_no'];
			$quantity 							= $fields['add_quantity'];
			$unit_cost 							= $fields['add_unit_cost'];
			$total_unit_cost 					= $fields['add_total_unit_cost'];
			$body_request_id 					= $fields['body_request_id'];
			$category_id 					    = $fields['category_id'];
			$sub_category_id 					= $fields['sub_category_id'];
			$quantity_total 					= $fields['quantity_total'];
			$total 								= $fields['total'];

			if(!$digits_code || !$item_description){
				return CRUDBooster::redirect(CRUDBooster::mainpath(),"No Reserved Item","danger");
			}
			//$postdata['quantity_total']		 	= $quantity_total;
			//$postdata['total']		 			= $total;

			$arf_header 		= HeaderRequest::where(['id' => $Header_id])->first();
			// if(in_array($arf_header->request_type_id, [1, 5])){
			//     $inventory_id 						= $fields['inventory_id'];
			// 	$item_id 							= $fields['item_id'];
			// }else{
			// 	$inventory_id 						= NULL;
			// 	$item_id 							= $fields['inventory_id'];
			// }
			$body_request 		= BodyRequest::where(['header_request_id' => $Header_id])->count();

			$count_header 		= MoveOrder::count();

			$count_header1  	= $count_header + 1;

			//dd(count((array)$digits_code));
			if(in_array($arf_header->request_type_id, [5, 6, 7])){
			//if($arf_header->request_type_id == 5){
				$for_printing = 				StatusMatrix::where('current_step', 5)
												->where('request_type', $arf_header->request_type_id)
												->value('status_id');
			}else{
				$for_printing = 				StatusMatrix::where('current_step', 6)
												->where('request_type', $arf_header->request_type_id)
												->value('status_id');
			}

		
            //dd($body_request_id, $body_request_id);
			for($x=0; $x < count((array)$item_description); $x++) {


				$inventory_info = 	DB::table('assets_inventory_body')->where('digits_code', $digits_code[$x])->where('statuses_id',6)->first();

				$ref_inventory   =  		str_pad($inventory_info->location, 2, '0', STR_PAD_LEFT);	

					if(count((array)$digits_code) != $body_request){

						if($body_request_id[$x] == "" || $body_request_id[$x] == null){

							$count_header++;
							$header_ref   =  		str_pad($count_header, 7, '0', STR_PAD_LEFT);			
							$reference_number	= 	"MO-".$header_ref.$ref_inventory;

						}else{
							$header_ref   =  		str_pad($count_header1, 7, '0', STR_PAD_LEFT);			
							$reference_number	= 	"MO-".$header_ref.$ref_inventory;

						}
						// $count_header++;
						// $header_ref   =  		str_pad($count_header, 7, '0', STR_PAD_LEFT);			
						// $reference_number	= 	"MO-".$header_ref.$ref_inventory;

						//$reference_number	= 	"MO-".$header_ref;
					}else{
						$header_ref   =  		str_pad($count_header1, 7, '0', STR_PAD_LEFT);			
						$reference_number	= 	"MO-".$header_ref.$ref_inventory;

						//$reference_number	= 	"MO-".$header_ref;
					}

				
					// $items = 				DB::table('assets')->where('assets.id', $item_id[$x])->first();

					// $category_id = 			DB::table('category')->where('id',	$items->category_id)->value('category_description');

					// $sub_category_id = 		DB::table('class')->where('id',	$items->class_id)->value('class_description');


	
				$dataLines1[$x]['status_id'] 			= $for_printing;
				$dataLines1[$x]['mo_reference_number'] 	= $reference_number;
				$dataLines1[$x]['header_request_id'] 	= $arf_header->id;
				$dataLines1[$x]['body_request_id'] 		= $body_request_id[$x];
				//$dataLines1[$x]['item_id'] 				= $item_id[$x];
				//$dataLines1[$x]['inventory_id'] 		= $inventory_id[$x];
				$dataLines1[$x]['digits_code'] 			= $digits_code[$x];
				//$dataLines1[$x]['asset_code'] 			= $asset_code[$x];
				$dataLines1[$x]['item_description'] 	= $item_description[$x];
				$dataLines1[$x]['category_id'] 			= $category_id[$x];
				$dataLines1[$x]['sub_category_id'] 		= $sub_category_id[$x];

				

				$array_location = [1,2];
				if(in_array($arf_header->request_type_id, [1, 5])){
					$location 						= $inventory_info->location;
				}else{
					$location 						= implode(",",$array_location);
				}

				//$dataLines1[$x]['serial_no'] 			= $serial_no[$x];
				$dataLines1[$x]['quantity'] 			= $quantity[$x];
				$dataLines1[$x]['unit_cost'] 			= $unit_cost[$x];
				$dataLines1[$x]['total_unit_cost'] 		= $total_unit_cost[$x];
				//$dataLines1[$x]['to_reco'] 				= $arf_header->to_reco;
				$dataLines1[$x]['location_id'] 			= $location;
				$dataLines1[$x]['created_by'] 			= CRUDBooster::myId();
				$dataLines1[$x]['created_at'] 			= date('Y-m-d H:i:s');
				$dataLines1[$x]['request_created_by']   = $arf_header->created_by;
				$dataLines1[$x]['request_type_id_mo']   = $arf_header->request_type_id;

				array_push($locationArray, $inventory_info->location);
			
				BodyRequest::where('id',$body_request_id[$x])
				->update([
					'mo_plug'=> 		1,
					'location_id'=> 	$location,
					'to_mo'=> 	0
				]);	

				// DB::table('assets_inventory_body')->where('id', $inventory_id[$x])
				// ->update([
				// 	'statuses_id'=> 			2
				// ]);

			}

			
			DB::beginTransaction();
	
			try {
				MoveOrder::insert($dataLines1);
				DB::commit();
				//CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.alert_pullout_data_success",['mps_reference'=>$pullout_header->reference]), 'success');
			} catch (\Exception $e) {
				DB::rollback();

				CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.alert_database_error",['database_error'=>$e]), 'danger');
			}


			if(in_array($arf_header->request_type_id, [1, 5])){
				$headLocation 						= implode(",", $locationArray);
			}else{
				$headLocation 						= $location;
			}

			if($arf_header->print_by == null){	
				
				HeaderRequest::where('id',$Header_id)
				->update([
					'mo_by'          => CRUDBooster::myId(),
					'mo_at'          => date('Y-m-d H:i:s'),
					'purchased2_by'	 => CRUDBooster::myId(),
				    'purchased2_at'  => date('Y-m-d H:i:s'),
					'status_id'      => $for_printing,
					'quantity_total' => $quantity_total,
					'total'          => $total,
					'location_id'    => $headLocation,
					'to_mo'          => 0
				]);


				/*MoveOrder::where('header_request_id',$Header_id)
				->update([
					'location_id_list'=> implode(",", $locationArray)
				]);*/
				
			}else{

				$sum_qty = $arf_header->quantity_total + $quantity_total;

				$sum = $arf_header->total + $total;

				HeaderRequest::where('id',$Header_id)
				->update([
					'mo_by'          => CRUDBooster::myId(),
					'mo_at'          => date('Y-m-d H:i:s'),
					'purchased2_by'	 => CRUDBooster::myId(),
				    'purchased2_at'  => date('Y-m-d H:i:s'),
					'quantity_total' => $sum_qty,
					'total'          => $sum,
					'to_mo'          => 0
				]);


			}

			$cancelled  = 		DB::table('statuses')->where('id', 8)->value('id');


			$body_request 		= BodyRequest::where(['header_request_id' => $Header_id])
											   ->where(['to_mo' => 0])
											   ->whereNull('deleted_at')
											   ->count();

			$mo_request 		= MoveOrder::where(['header_request_id' => $Header_id])
											 ->where('status_id', '!=', $cancelled)
											 //->where('category_id', '!=', "FREEBIES")
											 //->orwhere('category_id', '!=', "FREEBIES")
											 //->where(['header_request_id' => $Header_id])
								             ->count();

			if($body_request == $mo_request){

					HeaderRequest::where('id',$Header_id)
					->update([
						'mo_plug'=> 1
					]);

			}else{
				
				HeaderRequest::where('id',$Header_id)
				->update([
					'to_mo'   => 1,
					'mo_plug' => 0
				]);
			}

			//$postdata['mo_print']		 	= 1;

			//unset($postData['id']);

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

			MoveOrder::wherenull('status_id')->delete();

			//$fields 	= Request::all();

			//$Header_id 		= $fields['header_request_id'];

			//$arf_header = HeaderRequest::where(['id' => $Header_id])->first();

			$mo_request = MoveOrder::where(['created_by' => CRUDBooster::myId()])->orderBy('id','desc')->first();

			
			$arf_header 		= HeaderRequest::where(['id' => $mo_request->header_request_id])->first();
			
			if(in_array($arf_header->request_type_id, [5, 6, 7])){
			//if($arf_header->request_type_id == 5){
				$for_printing = 				StatusMatrix::where('current_step', 5)
												->where('request_type', $arf_header->request_type_id)
												->value('status_id');
			}else{
				$for_printing = 				StatusMatrix::where('current_step', 6)
												->where('request_type', $arf_header->request_type_id)
												->value('status_id');
			}

			$mo_info = MoveOrder::where(['header_request_id' => $mo_request->header_request_id])
								  ->where(['status_id' => $for_printing])
								  ->get();


			$approval_array = array();
			foreach($mo_info as $matrix){

				array_push($approval_array, $matrix->mo_reference_number);

			}
			$approval_string = implode(",",$approval_array);


			CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.mo_success",['reference_number'=>$approval_string]), 'info');

			//return redirect()->action('AdminMoveOrderController@getRequestPrintPickList',['id'=>$mo_request->id])->send();
			
			//exit;
			
	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate data input before update data is execute
	    | ---------------------------------------------------------------------- 
	    | @postdata = input post data 
	    | @id       = current id 
	    | 
	    */
	    public function hook_before_edit(&$postdata,$id){        
	        //Your code here

			$arf_header = HeaderRequest::where(['id' => $id])->first();

			$fields = Request::all();
			$cont = (new static)->apiContext;

			$for_printing = 				StatusMatrix::where('current_step', 6)
											->where('request_type', $arf_header->request_type_id)
											//->where('id_cms_privileges', CRUDBooster::myPrivilegeId())
											->value('status_id');

			$postdata['mo_by'] 				= CRUDBooster::myId();
			$postdata['mo_at'] 				= date('Y-m-d H:i:s');
			
			if($arf_header->print_by == null){

				$postdata['status_id']		 	= $for_printing;

			}

			$digits_code 			= $fields['add_digits_code'];
			$asset_code 			= $fields['add_asset_code'];
			$item_description 		= $fields['add_item_description'];
			$serial_no 				= $fields['add_serial_no'];
			$quantity 				= $fields['add_quantity'];
			$unit_cost 				= $fields['add_unit_cost'];
			$total_unit_cost 		= $fields['add_total_unit_cost'];
			$body_request_id 		= $fields['body_request_id'];
			$item_id 				= $fields['item_id'];
			$inventory_id 			= $fields['inventory_id'];
			$quantity_total 		= $fields['quantity_total'];
			$total 					= $fields['total'];


			$postdata['quantity_total']		 	= $quantity_total;
			$postdata['total']		 			= $total;


			$count_header = 	MoveOrder::count();
			

			for($x=0; $x < count((array)$digits_code); $x++) {

				$count_header++;

				$header_ref   =  		str_pad($count_header, 7, '0', STR_PAD_LEFT);			
				$reference_number	= 	"MO-".$header_ref;

				$items = 				DB::table('assets')->where('assets.id', $item_id[$x])->first();

				$category_id = 			DB::table('category')->where('id',	$items->category_id)->value('category_description');

				$sub_category_id = 		DB::table('class')->where('id',	$items->class_id)->value('class_description');

				BodyRequest::where('id',$body_request_id[$x])
				->update([
					'mo_plug'=> 	1
				]);	
	
				$dataLines[$x]['status_id'] 			= $for_printing;
				$dataLines[$x]['mo_reference_number'] 	= $reference_number;
				$dataLines[$x]['header_request_id'] 	= $arf_header->id;
				$dataLines[$x]['body_request_id'] 		= $body_request_id[$x];
				$dataLines[$x]['item_id'] 				= $item_id[$x];
				$dataLines[$x]['inventory_id'] 			= $inventory_id[$x];
				$dataLines[$x]['digits_code'] 			= $digits_code[$x];
				$dataLines[$x]['asset_code'] 			= $asset_code[$x];
				$dataLines[$x]['item_description'] 		= $item_description[$x];
				$dataLines[$x]['category_id'] 			= $category_id;
				$dataLines[$x]['sub_category_id'] 		= $sub_category_id;
				$dataLines[$x]['serial_no'] 			= $serial_no[$x];
				$dataLines[$x]['quantity'] 				= $quantity[$x];
				$dataLines[$x]['unit_cost'] 			= $unit_cost[$x];
				$dataLines[$x]['total_unit_cost'] 		= $total_unit_cost[$x];
				$dataLines[$x]['created_by'] 			= CRUDBooster::myId();
				$dataLines[$x]['created_at'] 			= date('Y-m-d H:i:s');



				DB::table('assets_inventory_body')->where('digits_code', $digits_code[$x])
				->update([
					'status_id1'=> 			2
				]);	

			}
			

			DB::beginTransaction();
	
			try {
				MoveOrder::insert($dataLines);
				DB::commit();
				//CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.alert_pullout_data_success",['mps_reference'=>$pullout_header->reference]), 'success');
			} catch (\Exception $e) {
				DB::rollback();


				CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.alert_database_error",['database_error'=>$e]), 'danger');
			}


			$body_request = BodyRequest::where(['header_request_id' => $id])->count();

			$mo_request = MoveOrder::where(['header_request_id' => $id])->count();


			if($body_request == $mo_request){

				$postdata['mo_plug']		= 1;

			}else{
				$postdata['mo_plug']		= 0;
			}

			//$postdata['mo_print']		 	= 1;
			
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

			$arf_header = HeaderRequest::where(['id' => $id])->first();

			return redirect()->action('AdminMoveOrderController@getRequestPrintPickList',['id'=>$arf_header->id])->send();
			exit;
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

	    //By the way, you can still create your own method in here... :) 

		public function getAddMO(){
			

			if(!CRUDBooster::isCreate() && $this->global_privilege == false) {
				CRUDBooster::redirect(CRUDBooster::adminPath(), trans('crudbooster.denied_access'));
			}

			$this->cbLoader();

			$data = array();

			$data['page_title'] = 'Ordering Request';

			$for_move_order =  	DB::table('statuses')->where('id', 14)->value('id');

			//where('status_id', $for_move_order)->
			//$cancelled  = 		DB::table('statuses')->where('id', 8)->value('id');

			//Option 1
			// $data['AssetRequest'] = HeaderRequest::whereNotNull('purchased2_by')->where('mo_plug', 0)
			// 									   ->orwhere('to_mo', 1)
			// 									   ->get();
			//Option 2
			if(in_array(CRUDBooster::myPrivilegeId(),[5,17])){
				$data['AssetRequest'] = HeaderRequest::
				  where('mo_plug', 0)
				->where('to_mo', 1)
				->where('request_type_id' , 1)
				->whereNotIn('status_id',[8,13])
				->whereNotNull('created_by')
				->get();
			}else if(in_array(CRUDBooster::myPrivilegeId(),[9])){
				$data['AssetRequest'] = HeaderRequest::
				  where('mo_plug', 0)
				->where('to_mo', 1)
				->where('request_type_id' , 5)
				->whereNotIn('status_id',[8,13])
				->whereNotNull('created_by')
			
				->get();
			}else{
				$data['AssetRequest'] = HeaderRequest::
				  where('mo_plug', 0)
				->where('to_mo', 1)
				->whereNotIn('status_id',[8,13])
				->whereNotNull('created_by')
				->get();
			}
			

			$data['Header'] = HeaderRequest::
				  leftjoin('request_type', 'header_request.purpose', '=', 'request_type.id')
				->leftjoin('condition_type', 'header_request.conditions', '=', 'condition_type.id')
				->leftjoin('cms_users as employees', 'header_request.employee_name', '=', 'employees.id')
				->leftjoin('companies', 'header_request.company_name', '=', 'companies.id')
				->leftjoin('departments', 'header_request.department', '=', 'departments.id')
				->leftjoin('positions', 'header_request.position', '=', 'positions.id')
				->leftjoin('locations', 'employees.location_id', '=', 'locations.id')
				->leftjoin('cms_users as requested', 'header_request.created_by','=', 'requested.id')
				->leftjoin('cms_users as approved', 'header_request.approved_by','=', 'approved.id')
				->leftjoin('cms_users as recommended', 'header_request.recommended_by','=', 'recommended.id')
				->leftjoin('cms_users as tagged', 'header_request.purchased2_by','=', 'tagged.id')
				->select(
						'header_request.*',
						'header_request.id as requestid',
						'header_request.created_at as created',
						'request_type.*',
						'condition_type.*',
						'requested.name as requestedby',
						'employees.bill_to as employee_name',
						'employees.company_name_id as company_name',
						'departments.department_name as department',
						//'positions.position_description as position',
						'locations.store_name as store_branch',
						'approved.name as approvedby',
						'recommended.name as recommendedby',
						'tagged.name as taggedby',
						'header_request.created_at as created_at'
						)
				->where('header_request.id', $id)->first();
        
			$data['Body'] = BodyRequest::
				select(
				  'body_request.*'
				)
				->where('body_request.header_request_id', $id)
				->get();

			$data['Body1'] = BodyRequest::
				select(
				  'body_request.*'
				)
				->where('body_request.header_request_id', $id)
				->wherenotnull('body_request.digits_code')
				->orderby('body_request.id', 'desc')
				->get();

			$data['MoveOrder'] = MoveOrder::
				select(
				  'mo_body_request.*'
				)
				->where('mo_body_request.header_request_id', $id)
				->orderby('mo_body_request.id', 'desc')
				->get();
	
			//dd($data['MoveOrder']->count());

			$data['BodyReco'] = DB::table('recommendation_request')
				->select(
				  'recommendation_request.*'
				)
				->where('recommendation_request.header_request_id', $id)
				->get();				

			$data['recommendations'] = DB::table('recommendations')->where('status', 'ACTIVE')->get();

			return $this->view("assets.add-mo", $data);
		}

		public function getRequestOrdering($id){
			

			$this->cbLoader();
			if(!CRUDBooster::isUpdate() && $this->global_privilege==FALSE) {    
				CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
			}  


			$data = array();

			$data['page_title'] = 'Ordering Request';

			$data['Header'] = HeaderRequest::
				  leftjoin('request_type', 'header_request.purpose', '=', 'request_type.id')
				->leftjoin('condition_type', 'header_request.conditions', '=', 'condition_type.id')
				->leftjoin('cms_users as employees', 'header_request.employee_name', '=', 'employees.id')
				->leftjoin('companies', 'header_request.company_name', '=', 'companies.id')
				->leftjoin('departments', 'header_request.department', '=', 'departments.id')
				->leftjoin('positions', 'header_request.position', '=', 'positions.id')
				->leftjoin('locations', 'employees.location_id', '=', 'locations.id')
				->leftjoin('cms_users as requested', 'header_request.created_by','=', 'requested.id')
				->leftjoin('cms_users as approved', 'header_request.approved_by','=', 'approved.id')
				->leftjoin('cms_users as recommended', 'header_request.recommended_by','=', 'recommended.id')
				->leftjoin('cms_users as tagged', 'header_request.purchased2_by','=', 'tagged.id')
				->select(
						'header_request.*',
						'header_request.id as requestid',
						'header_request.created_at as created',
						'request_type.*',
						'condition_type.*',
						'requested.name as requestedby',
						'employees.bill_to as employee_name',
						'employees.company_name_id as company_name',
						'departments.department_name as department',
						//'positions.position_description as position',
						'locations.store_name as store_branch',
						'approved.name as approvedby',
						'recommended.name as recommendedby',
						'tagged.name as taggedby',
						'header_request.created_at as created_at'
						)
				->where('header_request.id', $id)->first();

			$data['Body'] = BodyRequest::
				select(
				  'body_request.*'
				)
				->where('body_request.header_request_id', $id)
				->get();

			$data['Body1'] = BodyRequest::
				select(
				  'body_request.*'
				)
				->where('body_request.header_request_id', $id)
				->wherenotnull('body_request.digits_code')
				->orderby('body_request.id', 'desc')
				->get();

			$data['MoveOrder'] = MoveOrder::
				select(
				  'mo_body_request.*'
				)
				->where('mo_body_request.header_request_id', $id)
				->orderby('mo_body_request.id', 'desc')
				->get();
	
			//dd($data['MoveOrder']->count());

			$data['BodyReco'] = DB::table('recommendation_request')
				->select(
				  'recommendation_request.*'
				)
				->where('recommendation_request.header_request_id', $id)
				->get();				

			$data['recommendations'] = DB::table('recommendations')->where('status', 'ACTIVE')->get();

			return $this->view("assets.ordering-request", $data);
		}


		public function getRequestPrintPickList($id){

		
			$this->cbLoader();
			if(!CRUDBooster::isUpdate() && $this->global_privilege==FALSE) {    
				CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
			}  


			$data = array();

			$data['page_title'] = 'Print Picklist';

			$HeaderID = MoveOrder::where('id', $id)->first();

			$location = substr($HeaderID->mo_reference_number,11);

			$data['Location'] = DB::table('warehouse_location_model')->where('id', $location)->first();

			$data['Header'] = HeaderRequest::
				  leftjoin('request_type', 'header_request.purpose', '=', 'request_type.id')
				->leftjoin('condition_type', 'header_request.conditions', '=', 'condition_type.id')
				->leftjoin('cms_users as employees', 'header_request.employee_name', '=', 'employees.id')
				->leftjoin('companies', 'header_request.company_name', '=', 'companies.id')
				->leftjoin('departments', 'header_request.department', '=', 'departments.id')
				->leftjoin('positions', 'header_request.position', '=', 'positions.id')
				->leftjoin('locations', 'employees.location_id', '=', 'locations.id')
				->leftjoin('cms_users as requested', 'header_request.created_by','=', 'requested.id')
				->leftjoin('cms_users as approved', 'header_request.approved_by','=', 'approved.id')
				->leftjoin('cms_users as recommended', 'header_request.recommended_by','=', 'recommended.id')
				->leftjoin('cms_users as processed', 'header_request.purchased2_by','=', 'processed.id')
				->select(
						'header_request.*',
						'header_request.id as requestid',
						'header_request.created_at as created',
						'request_type.*',
						'condition_type.*',
						'requested.name as requestedby',
						'employees.bill_to as employee_name',
						'employees.company_name_id as company_name',
						'departments.department_name as department',
						'positions.position_description as position',
						'locations.store_name as store_branch',
						'approved.name as approvedby',
						'recommended.name as recommendedby',
						'processed.name as processedby',
						'header_request.created_at as created_at'
						)
				->where('header_request.id', $HeaderID->header_request_id)->first();

			$data['MoveOrder'] = MoveOrder::
				select(
				  'mo_body_request.*',
				  'statuses.status_description as status_description'
				)
				->where('mo_body_request.mo_reference_number', $HeaderID->mo_reference_number)
				->leftjoin('statuses', 'mo_body_request.status_id', '=', 'statuses.id')
				->orderby('mo_body_request.id', 'desc')
				->get();	


			return $this->view("assets.print-picklist", $data);

		}


		public function getDetailOrdering($id){

			$this->cbLoader();
            if(!CRUDBooster::isRead() && $this->global_privilege==FALSE) {    
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

			$data = array();

			$data['page_title'] = 'View Request';

			$HeaderID = MoveOrder::where('id', $id)->first();

			$data['Header'] = HeaderRequest::
				  leftjoin('request_type', 'header_request.purpose', '=', 'request_type.id')
				->leftjoin('condition_type', 'header_request.conditions', '=', 'condition_type.id')
				->leftjoin('cms_users as employees', 'header_request.employee_name', '=', 'employees.id')
				->leftjoin('companies', 'header_request.company_name', '=', 'companies.id')
				->leftjoin('departments', 'header_request.department', '=', 'departments.id')
				->leftjoin('positions', 'header_request.position', '=', 'positions.id')
				->leftjoin('locations', 'employees.location_id', '=', 'locations.id')
				->leftjoin('cms_users as requested', 'header_request.created_by','=', 'requested.id')
				->leftjoin('cms_users as approved', 'header_request.approved_by','=', 'approved.id')
				->leftjoin('cms_users as recommended', 'header_request.recommended_by','=', 'recommended.id')
				->leftjoin('cms_users as tagged', 'header_request.purchased2_by','=', 'tagged.id')
				->select(
						'header_request.*',
						'header_request.id as requestid',
						'header_request.created_at as created',
						'request_type.*',
						'condition_type.*',
						'requested.name as requestedby',
						'employees.bill_to as employee_name',
						'header_request.employee_name as header_emp_name',
						'employees.company_name_id as company_name',
						'departments.department_name as department',
						//'positions.position_description as position',
						'locations.store_name as store_branch',
						'approved.name as approvedby',
						'recommended.name as recommendedby',
						'tagged.name as taggedby',
						'header_request.created_at as created_at'
						)
				->where('header_request.id', $HeaderID->header_request_id)->first();
			$data['Body'] = BodyRequest::
				select(
				  'body_request.*'
				)
				->where('body_request.header_request_id', $HeaderID->header_request_id)
				->get();

			$data['Body1'] = BodyRequest::
				select(
				  'body_request.*'
				)
				->where('body_request.header_request_id', $HeaderID->header_request_id)
				->wherenotnull('body_request.digits_code')
				->orderby('body_request.id', 'desc')
				->get();

			$data['MoveOrder'] = MoveOrder::
				select(
				  'mo_body_request.*',
				  'statuses.status_description as status_description'
				)
				->where('mo_body_request.mo_reference_number', $HeaderID->mo_reference_number)
				->where('mo_body_request.defective', $HeaderID->defective)
				->leftjoin('statuses', 'mo_body_request.status_id', '=', 'statuses.id')
				->orderby('mo_body_request.id', 'desc')
				->get();	
			$data['BodyReco'] = DB::table('recommendation_request')
				->select(
				  'recommendation_request.*'
				)
				->where('recommendation_request.header_request_id', $id)
				->get();				

			$data['recommendations'] = DB::table('recommendations')->where('status', 'ACTIVE')->get();
			return $this->view("assets.mo-new-detail", $data);

		}

		public function PickListUpdate(){

			$data = 			Request::all();	
			
			$cont = (new static)->apiContext;

			$requestid = 			$data['requestid']; 
			$mo_id = 				$data['mo_id']; 

			$arf_header = 			HeaderRequest::where(['id' => $requestid])->first();

			//$for_picking =  		DB::table('statuses')->where('id', 15)->value('id');
			if(in_array($arf_header->request_type_id, [5, 6, 7])){
			//if($arf_header->request_type_id == 5){
				$for_picking = 			StatusMatrix::where('current_step', 6)
										->where('request_type', $arf_header->request_type_id)
										->value('status_id');
			}else{
				$for_picking = 			StatusMatrix::where('current_step', 7)
										->where('request_type', $arf_header->request_type_id)
										->value('status_id');
			}


			//$mo_request = 			MoveOrder::where(['header_request_id' => $requestid])->get();

			for($x=0; $x < count((array)$mo_id); $x++) {

				MoveOrder::where('id', $mo_id[$x])
				->update([
					'status_id'=> 		$for_picking,
					'mo_plug'=> 		1,
					'printed_at'=> 		date('Y-m-d H:i:s')
				]);	

			}

			if($arf_header->print_by == null){

				HeaderRequest::where('id',$requestid)
					->update([
						'status_id'=> 		$for_picking,
						'print_by'=> 		CRUDBooster::myId(),
						'print_at'=> 		date('Y-m-d H:i:s')
					]);	

			}


			//CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.arf_print_success",['reference_number'=>$arf_header->reference_number]), 'info');

		}

		public function selectedHeader(Request $request) {

			$fields 		= Request::all();
			$search 		= $fields['header_request_id'];

			$data['ARFHeader'] = '';
			$data['ARFBody'] = '';
			$data['ARFBodyTable'] = '';

			$stoClass = '';
			$store_data = '';
            $storeClassIndex = array();
            $storeClassValue = array();

			$data['Header'] = HeaderRequest::
								leftjoin('request_type', 'header_request.purpose', '=', 'request_type.id')
								->leftjoin('condition_type', 'header_request.conditions', '=', 'condition_type.id')
								->leftjoin('cms_users as employees', 'header_request.employee_name', '=', 'employees.id')
								->leftjoin('companies', 'header_request.company_name', '=', 'companies.id')
								->leftjoin('departments', 'header_request.department', '=', 'departments.id')
								->leftjoin('positions', 'header_request.position', '=', 'positions.id')
								->leftjoin('locations', 'employees.location_id', '=', 'locations.id')
								->leftjoin('cms_users as requested', 'header_request.created_by','=', 'requested.id')
								->leftjoin('cms_users as approved', 'header_request.approved_by','=', 'approved.id')
								->leftjoin('cms_users as recommended', 'header_request.recommended_by','=', 'recommended.id')
								->leftjoin('cms_users as tagged', 'header_request.purchased2_by','=', 'tagged.id')
								->select(
										'header_request.*',
										'header_request.id as requestid',
										'header_request.created_at as created',
										'request_type.*',
										'condition_type.*',
										'requested.name as requestedby',
										'employees.bill_to as employee_name',
										'employees.company_name_id as company_name',
										'departments.department_name as department',
										//'positions.position_description as position',
										'locations.store_name as store_branch',
										'approved.name as approvedby',
										'recommended.name as recommendedby',
										'tagged.name as taggedby',
										'header_request.created_at as created_at'
										)
								->where('header_request.id', $search)->first();
			
			$data['Body'] = BodyRequest::leftjoin('assets_inventory_reserved', 'body_request.id', '=', 'assets_inventory_reserved.body_id')
								->select(
								  'body_request.*',
								  'assets_inventory_reserved.reserved as reserved'
								)
								->where('body_request.header_request_id', $search)
								->where('body_request.mo_plug', 0)
								->whereNull('deleted_at')
								->orwhere('to_mo', 1)
								->where('body_request.header_request_id', $search)
								->get();


			$data['ARFHeader'] .= '
				<hr/>
				<div class="row">                           
					<label class="control-label col-md-2">Reference Number:</label>
					<div class="col-md-4">

							<input type="hidden" value="'. $data['Header']->requestid .'" name="header_request_id" id="header_request_id">		
							
							<p>'. $data['Header']->reference_number .'</p>
					</div>
					<label class="control-label col-md-2">Requested Date:</label>
					<div class="col-md-4">
							<p>'.$data['Header']->created .'</p>
					</div>
				</div>
				<div class="row">                           
					<label class="control-label col-md-2">Employee Name:</label>
					<div class="col-md-4">
							<p>'. $data['Header']->employee_name .'</p>
					</div>

					<label class="control-label col-md-2">Company Name:</label>
					<div class="col-md-4">
							<p>'. $data['Header']->company_name .'</p>
					</div>
            	</div>
				<div class="row">                           
					<label class="control-label col-md-2">Department:</label>
					<div class="col-md-4">
							<p>'. $data['Header']->department .'</p>
					</div>

					<label class="control-label col-md-2">Position:</label>
					<div class="col-md-4">
							<p>'. $data['Header']->position .'</p>
					</div>
            	</div>
				';

				if($data['Header']->store_branch != null || $data['Header']->store_branch != ""){ 
					$data['ARFHeader'] .= ' <div class="row">                           
											<label class="control-label col-md-2">Store/Branch:</label>
											<div class="col-md-4">
													<p>'. $data['Header']->store_branch .'</p>
											</div>
										 </div>';
				} 

				if($data['Header']->if_from_erf != null || $data['Header']->if_from_erf != ""){ 
					$data['ARFHeader'] .= ' <div class="row">                           
											<label class="control-label col-md-2">Erf Number:</label>
											<div class="col-md-4">
													<p>'. $data['Header']->if_from_erf .'</p>
											</div>
										 </div>';
				} 

				if($data['Header']->if_from_item_source != null || $data['Header']->if_from_item_source != ""){ 
					$data['ARFHeader'] .= ' <div class="row">                           
											<label class="control-label col-md-2">Item Sourcing Number:</label>
											<div class="col-md-4">
													<p>'. $data['Header']->if_from_item_source .'</p>
											</div>
										 </div>';
				} 

			$data['ARFHeader'] .= '
				<hr/>
				<div class="row">                           
					<label class="control-label col-md-2">Purpose:</label>
					<div class="col-md-4">
							<p>'. $data['Header']->request_description .'</p>
					</div>
				</div>
				';

			$tableRow = 1;

			$total = 0;

			foreach($data['Body'] as $rowresult){

				$tableRow++;

				$total++;

				$data['ARFBody'] .='

					<tr style="background-color: #d4edda; color:#155724">
						<input type="hidden"  class="form-control text-center finput"  name="item_description[]" id="item_description'.$tableRow.'"  required  value="'.$rowresult->item_description.'">
						<input type="hidden"  class="form-control"  name="remove_btn[]" id="remove_btn'.$tableRow.'"  required  value="'.$tableRow.'">
						<input type="hidden"  class="form-control"  name="remove_btn[]" id="category"  required  value="'.$data['Header']->request_type_id.'">
					
				';
			    if($rowresult->reserved != null || $rowresult->reserved != ""){ 
					$data['ARFBody'] .='
					   <td style="text-align:center" height="10">
					        <input type="hidden"  class="form-control"  name="body_request_id[]" id="body_request_id'.$tableRow.'"  required  value="'.$rowresult->id.'">                                                                               
							<input class="form-control text-center itemDcode finput" type="text" name="add_digits_code[]" value="'.$rowresult->digits_code.'" required max="99999999" readonly>                                                                              
						</td>
						<td style="text-align:center" height="10">
						    <input type="text"  class="form-control text-center finput"  name="add_item_description[]" id="add_item_description'.$tableRow.'"  required  value="'.$rowresult->item_description.'" readonly>
						</td>

						<td style="text-align:center" height="10">
							<input type="text"  class="form-control text-center finput"  name="category_id[]" id="category_id'.$tableRow.'"  required  value="'.$rowresult->category_id.'" readonly>
                        </td>

						<td style="text-align:center" height="10">
						   <input type="text"  class="form-control text-center finput"  name="sub_category_id[]" id="sub_category_id'.$tableRow.'"  required  value="'.$rowresult->sub_category_id.'" readonly>
                        </td>

						<td style="text-align:center" height="10">
						  <input type="text"  class="form-control text-center finput"  name="add_quantity[]" id="add_quantity'.$tableRow.'"  required  value="'.$rowresult->quantity.'" readonly>
                        </td>	

						<td style="text-align:center" class="rep_qty">
						 '. ($rowresult->replenish_qty ? $rowresult->replenish_qty : 0) .'
						</td>  
						<td style="text-align:center" class="re_qty">
						 '. ($rowresult->reorder_qty ? $rowresult->reorder_qty : 0) .'
						</td>     
						<td style="text-align:center" class="served_qty">
						 '. ($rowresult->serve_qty ? $rowresult->serve_qty : 0) .'
						 </td>                                                           
						<td style="text-align:center" class="unserved_qty">
						'. ($rowresult->unserved_qty ? $rowresult->unserved_qty : 0).'
						</td>
						<td style="text-align:center" class="unit_cost">
						'. ($rowresult->unit_cost ? $rowresult->unit_cost : 0) .'
						</td>
						<td style="text-align:center" class="total_cost">
						'. ($rowresult->unit_cost * $rowresult->serve_qty) .'
						</td>
						<td style="text-align:center"><i data-toggle="tooltip" data-placement="right" title="reserved" class="fa fa-check-circle text-success"></i></td>
					';
				}else{
					$data['ARFBody'] .='
					<tr >
					   <td style="text-align:center" height="10">
							'.$rowresult->digits_code.'                                                                            
						</td>
						<td style="text-align:center" height="10">
						    '.$rowresult->item_description.'
						</td>
						<td style="text-align:center" height="10">
							'.$rowresult->category_id.'
                        </td>
						<td style="text-align:center" height="10">
						   '.$rowresult->sub_category_id.'
                        </td>
						<td style="text-align:center" height="10">
						  '.$rowresult->quantity.'
                        </td>	
						<td style="text-align:center" class="rep_qty">
						 '. ($rowresult->replenish_qty ? $rowresult->replenish_qty : 0) .'
						</td>  
						<td style="text-align:center" class="re_qty">
						 '. ($rowresult->reorder_qty ? $rowresult->reorder_qty : 0) .'
						</td>     
						<td style="text-align:center" class="served_qty">
						 '. ($rowresult->serve_qty ? $rowresult->serve_qty : 0) .'
						 </td>                                                           
						<td style="text-align:center" class="unserved_qty">
						'. ($rowresult->unserved_qty ? $rowresult->unserved_qty : 0).'
						</td>
						<td style="text-align:center" class="unit_cost">
						'. ($rowresult->unit_cost ? $rowresult->unit_cost : 0) .'
						</td>
						<td style="text-align:center" class="total_cost">
						'. ($rowresult->unit_cost * $rowresult->serve_qty) .'
						</td>
						<td style="text-align:center"><i data-toggle="tooltip" data-placement="right" title="Unserved" class="fa fa-times-circle text-danger"></i></td>
					</tr>';
				}
				// if($data['Header']->recommendedby != null || $data['Header']->recommendedby != ""){ 
				// 	$data['ARFBody'] .='

				// 			<td style="text-align:center" height="10">
				// 				'.$rowresult->recommendation.'
				// 			</td>

				// 			<td style="text-align:center" height="10">
				// 				'.$rowresult->reco_digits_code.'
                //              </td>

                //              <td style="text-align:center" height="10">
				// 			 	'.$rowresult->reco_item_description.'
                //              </td>

				// 		</tr>
				// 	';
				// }

			}

			$data['ARFBodyTable'] .= '
				<hr/>
				<div class="col-md-12">
					<div class="box-header text-center">
						<h3 class="box-title"><b>Recommendation</b></h3>
					</div>
					<div class="box-body no-padding">
					  <div class="table-responsive">
						<div class="pic-container">
							<div class="pic-row">
								<table id="asset-items1">
									<tbody id="bodyTable">
										<tr class="tbl_header_color dynamicRows">
											<th width="9%" class="text-center">Digits Code</th>
											<th width="20%" class="text-center">Item Description</th>
											<th width="9%" class="text-center">Category</th>                                                         
											<th width="9%" class="text-center">Sub Category</th> 
											<th width="3%" class="text-center">Request Qty</th>
											<th width="3%" class="text-center">For Replenish Qty</th> 
                                            <th width="3%" class="text-center">For Re Order Qty</th> 
                                            <th width="3%" class="text-center">Fulfilled Qty</th> 
                                            <th width="3%" class="text-center">UnServed Qty</th> 
                                            <th width="5%" class="text-center">Item Cost</th> 
                                            <th width="5%" class="text-center">Total Cost</th>
											<th width="3%" class="text-center">Reserved</th>
											'; 

											// if($data['Header']->recommendedby != null || $data['Header']->recommendedby != ""){ 
											// 	$data['ARFBodyTable'] .= '
											// 		<th width="7%" class="text-center">Laptop Type</th> 
											// 		<th width="10%" class="text-center">Digits Code Reco</th> 
											// 		<th width="20%" class="text-center">Item Description Reco</th>
											// 	';
											// }
											

		$data['ARFBodyTable'] .= '	
										<tr id="tr-table">	
											<tr>
												'.$data['ARFBody'].'
											</tr>
										</tr>

										</tr>
									</tbody>
									<tfoot>
										
									</tfoot>
								</table>
							</div>
						</div>
					   </div>
					</div>	
				</div>

				<script type="text/javascript">

					var modal = document.getElementById("myModal");
					var modal2 = document.getElementById("myModal2");

					$(".btnsearch").click(function() {
						if($("#category").val() == 1 || $("#category").val() == 5){
							document.querySelector("body").style.overflow = "hidden";
							modal.style.display = "block";
						}else {
							document.querySelector("body").style.overflow = "hidden";
							modal2.style.display = "block";
						}
					});

					$("#searchclose").click(function() {
						document.querySelector("body").style.overflow = "visible";
						modal.style.display = "none";
					});
					$("#searchclose2").click(function() {
						document.querySelector("body").style.overflow = "visible";
						modal2.style.display = "none";
					});

					$(".btnsearch").click(function(event) {
       
						var searchID = $(this).attr("data-id");
						
						//alert($("#item_description"+searchID).val());
				 
						$("#item_search").text($("#item_description"+searchID).val());
				 
						$("#add_item_id").val($("#add_item_id"+searchID).val());
				 
						$("#button_count").val(searchID);
				  
						$("#button_remove").val($("#remove_btn"+searchID).val());
				 
					 });

				</script>
			';



			return $data;
		}

		public function getRequestPrintADF($id){
			
		
			$this->cbLoader();
			if(!CRUDBooster::isUpdate() && $this->global_privilege==FALSE) {    
				CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
			}  

			$for_printing_adf = DB::table('statuses')->where('id', 18)->value('id');


			$data = array();

			$data['page_title'] = 'Print Request';

			$HeaderID = MoveOrder::where('id', $id)->first();

			$data['Header'] = HeaderRequest::
				  leftjoin('request_type', 'header_request.purpose', '=', 'request_type.id')
				->leftjoin('condition_type', 'header_request.conditions', '=', 'condition_type.id')
				->leftjoin('cms_users as employees', 'header_request.employee_name', '=', 'employees.id')
				->leftjoin('companies', 'header_request.company_name', '=', 'companies.id')
				->leftjoin('departments', 'header_request.department', '=', 'departments.id')
				->leftjoin('locations', 'employees.location_id', '=', 'locations.id')
				->leftjoin('cms_users as requested', 'header_request.created_by','=', 'requested.id')
				->leftjoin('cms_users as approved', 'header_request.approved_by','=', 'approved.id')
				->leftjoin('cms_users as recommended', 'header_request.recommended_by','=', 'recommended.id')
				->leftjoin('cms_users as processed', 'header_request.purchased2_by','=', 'processed.id')
				->leftjoin('cms_users as picked', 'header_request.picked_by','=', 'picked.id')
				->select(
						'header_request.*',
						'header_request.id as requestid',
						'request_type.*',
						'condition_type.*',
						'requested.name as requestedby',
						'employees.bill_to as employee_name',
						'employees.company_name_id as company_name',
						'departments.department_name as department',
						'locations.store_name as store_name',
						'approved.name as approvedby',
						'recommended.name as recommendedby',
						'processed.name as processedby',
						'picked.name as pickedby',
						'header_request.created_at as created_at'
						)
				->where('header_request.id', $HeaderID->header_request_id)->first();

			$data['MoveOrder'] = MoveOrder::
				select(
				  'mo_body_request.*',
				  'statuses.status_description as status_description'
				)
				->where('mo_body_request.mo_reference_number', $HeaderID->mo_reference_number)
				->where('mo_body_request.to_pick', 1)
				->whereIn('mo_body_request.status_id', [$for_printing_adf,8])
				->leftjoin('statuses', 'mo_body_request.status_id', '=', 'statuses.id')
				->orderby('mo_body_request.id', 'desc')
				->get();

			$data['HeaderID'] = MoveOrder::where('id', $id)->first();

			return $this->view("assets.print-request", $data);
		}

		public function ADFUpdate(){

				$email_infos = array();
				$mo_reference_number = array();
				$asset_code = array();
				$digits_code = array();
				$item_description = array();
				$item_category = array();
				$serial_no = array();
	
				$data = 			Request::all();	
	
				$itemID = array();
	
				$cont = (new static)->apiContext;
	
				$requestid = 			$data['requestid']; 
	
				$mo_id = 				$data['mo_id']; 
	
				$inventory_id = 		$data['inventory_id'];

				$item_id = 		        $data['item_id'];
	
				$arf_header = 			HeaderRequest::where(['id' => $requestid])->first();
				
				if(in_array($arf_header->request_type_id, [5, 6, 7])){
				//if($arf_header->request_type_id == 5){
					$for_receiving = 		StatusMatrix::where('current_step', 8)
											->where('request_type', $arf_header->request_type_id)
											->value('status_id');
				}else{
					$for_receiving = 		StatusMatrix::where('current_step', 9)
											->where('request_type', $arf_header->request_type_id)
											->value('status_id');
				}

				$employee_name = DB::table('cms_users')->where('id', $arf_header->employee_name)->first();

				for($x=0; $x < count((array)$mo_id); $x++){

					MoveOrder::where('id', $mo_id[$x])
					->update([
						'status_id'=> 		$for_receiving
					]);	


					/*DB::table('assets_inventory_body')->where('id', $inventory_id[$x])
					->update([
						'statuses_id'=> 			3,
						'deployed_to'=> 			$employee_name->bill_to,
						'deployed_by'=> 			CRUDBooster::myId(),
						'deployed_at'=> 			date('Y-m-d H:i:s')
					]);

					DB::table('assets_inventory_body')->where('id', $inventory_id[$x])->decrement('quantity');
					*/

					array_push($itemID, $mo_id[$x]);
                    
					if(in_array($arf_header->request_type_id, [1, 5])){
						$email_info = 	DB::table('assets_inventory_body')->where('id', $inventory_id[$x])->first();
						//$mo_info = 		MoveOrder::where('inventory_id', $email_info->id)->whereNull('return_flag')->first();
						$mo_info = 		MoveOrder::where('id', $mo_id[$x])->whereNull('return_flag')->first();
					}else{
						$email_info = 	DB::table('assets')->where('id', $item_id[$x])->first();
						$mo_info = 		MoveOrder::where('item_id', $email_info->id)->whereNull('return_flag')->first();
					}
					$category_id = 			DB::table('category')->where('id',	$email_info->category_id)->value('category_description');

					array_push($mo_reference_number, $mo_info->mo_reference_number);
					array_push($asset_code, $email_info->asset_code);
					array_push($digits_code, $email_info->digits_code);
					array_push($item_description, $email_info->item_description);
					array_push($item_category, $email_info->item_category ? $email_info->item_category : $category_id);
					array_push($serial_no, $email_info->serial_no ? $email_info->serial_no : "");

						/*$full_date = 	"<b> Reference Number: </b> ".$mo_info->mo_reference_number."<br>".
										"<b> Assign Code:</b> ".$email_info->asset_code."<br>".
										"<b> Digits Code:</b> ".$email_info->digits_code."<br>".
										"<b> Item Description:</b> ".$email_info->item_description."<br>".
										"<b> Category:</b> ".$email_info->item_category."<br>".
										"<b> Serial No:</b> ".$email_info->serial_no."<br>"
										;
					
						array_push($email_infos, $full_date);*/


						/*$data = [	
									'assign_to'=>	$employee_name->bill_to,
									'asset_tag'=>	$item_Value->asset_code,
									'digits_code'=>	$item_Value->digits_code,
									'serial_no'=>	$item_Value->serial_no,
									'item_description'=>	$item_Value->item_description,
									'category_id'=>	$item_Value->item_category,
									'assign_date'=>	$item_Value->deployed_by,
									'assign_by'=>	$item_Value->deployed_at
								]; */

				}
				
				HeaderRequest::where('id',$requestid)
				->update([
					'status_id'      => $for_receiving,
					'print_by_form'  => CRUDBooster::myId(),
					'print_at_form'  => date('Y-m-d H:i:s')
				]);	

				MoveOrder::where('header_request_id', $arf_header->id)
				->update([
					'to_print'=> 	0
				]);	
				
				$item_string = implode(",",$itemID);

				$itemList = array_map('intval',explode(",",$item_string));

				$items = MoveOrder::wherein('id',$id)->get();
				
				$infos['assign_to'] = $employee_name->bill_to;
				$infos['reference_number'] = $arf_header->reference_number;
				//if(app()->environment('production')) {
					//$infos['systemlink'] = "<a href='https://dam.digitstrading.ph/public/admin/receiving_asset/getADFStatus/$arf_header->id'>I have read and agree to the terms of use, and have received this item.</a>";
				//}else if(app()->environment('staging')){
					//$infos['systemlink'] = "<a href='https://dam-test.digitstrading.ph/public/admin/receiving_asset/getADFStatus/$arf_header->id'>I have read and agree to the terms of use, and have received this item.</a>";
				//}else{
					$infos['systemlink'] = "<a href='https://localhost/dam/public/admin/receiving_asset/getADFStatus/$arf_header->id'>I have read and agree to the terms of use, and have received this item.</a>";
				//}
			
				$infos['mo_reference_number'] = '<p>'. implode("<br>", $mo_reference_number) .'</p>';
				$infos['asset_code'] = '<p>'. implode("<br>", $asset_code) .'</p>';
				$infos['digits_code'] = '<p>'. implode("<br>", $digits_code) .'</p>';
				$infos['item_description'] = '<p>'. implode("<br>", $item_description) .'</p>';
				$infos['item_category'] = '<p>'. implode("<br>", $item_category) .'</p>';
				$infos['serial_no'] = '<p>'. implode("<br>", $serial_no) .'</p>';
				
				//CRUDBooster::sendEmail(['to'=>$employee_name->email,'data'=>$infos,'template'=>'assets_confirmation','attachments'=>$files]);
				CRUDBooster::sendEmail(['to'=>'marvinmosico@digits.ph','data'=>$infos,'template'=>'assets_confirmation','attachments'=>$files]);

		}


		public function GetExtractMO(Request $request) {
			    
			$filter_column = \Request::get('filter_column');
            $dbhost = env('DB_HOST');
            $dbport = env('DB_PORT');
            $dbname = env('DB_DATABASE');
            $dbuser = env('DB_USERNAME');
            $dbpass = env('DB_PASSWORD');
			$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname, $dbport);

			if(! $conn ){
				die('Could not connect: ');
			}
		
			$sql_query = "	SELECT  
			                statuses.status_description,
						 	mo_body_request.mo_reference_number,
							header_request.reference_number,
							cms_users.bill_to,
							departments.department_name,
							mo_body_request.digits_code,
							mo_body_request.asset_code,
							mo_body_request.item_description,
							mo_body_request.category_id,
							mo_body_request.sub_category_id,
							mo_body_request.serial_no,
							mo_body_request.quantity,
							mo_body_request.unit_cost,
							mo_body_request.total_unit_cost
						 ";

			$sql_query .= "FROM `mo_body_request` 
				INNER JOIN `header_request` ON `mo_body_request`.header_request_id = `header_request`.id
				LEFT JOIN `cms_users` ON `header_request`.created_by = `cms_users`.id
				LEFT JOIN `departments` ON `header_request`.department = `departments`.id
				LEFT JOIN `statuses` ON `mo_body_request`.status_id = `statuses`.id";


            $sql_query .= "	WHERE `mo_body_request`.deleted_at is null";

			if($filter_column){
				foreach($filter_column as $key=>$fc) {

					$value = @$fc['value'];
					$type  = @$fc['type'];

					if($type == 'empty') {
						$sql_query .= "AND ".$key." IS NULL OR ".$key." = ''";
						continue;
					}

					if($value=='' || $type=='') continue;

					if($type == 'between') continue;

					switch($type) {
						default:
						
							if($key && $type && $value) $sql_query .= "AND ".$key." ".$type." '".$value."'";
						break;
						case 'like':
						case 'not like':
							$value = '%'.$value.'%';
							
							if($key && $type && $value) $sql_query .= "AND ".$key." ".$type." '".$value."'";
						break;
						case 'in':
						case 'not in':
							if($value) {
								$value = explode(',',$value);
								
								if($key && $value) $sql_query .= $key." IN (".$value.")";
							}
						break;
					}
				}

				foreach($filter_column as $key=>$fc) {
					$value = @$fc['value'];
					$type  = @$fc['type'];
	
					if ($type=='between') {
						if($key && $value) 
							$sql_query .= "AND (".$key." BETWEEN '".$value[0]."' AND '".$value[1]."')";
							//$result->whereBetween($key,$value);
					}else{
						continue;
					}
				}
			}
		
			$resultset = mysqli_query($conn, $sql_query) or die("Database Error:". mysqli_error($conn));
		
		

			$filename = "Move Orders - " . date('Ymd H:i:s') . ".xls";
			header("Content-Type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=\"$filename\"");
			
			$delimiter = "\t";
			//while ($header = mysqli_fetch_field($resultset)) {
			//    echo $header->name."\t";
			//}
			echo "Status"."\t"; 				// 0-product id
			echo "MO#"."\t"; 				// 0-product id
			echo "ARF#"."\t"; 				// 1-product name
			echo "Employee Name"."\t"; 				// 0-product id
			echo "Department"."\t"; 
			echo "DIGITS CODE"."\t";              // 7-standard cost per pc
			echo "ASSET CODE"."\t"; 				// 8-list price per pc
			echo "ITEM DESCRIPTION"."\t";               // 9-generic name
			echo "CATEGORY"."\t"; 					// 10-barcode 1
			echo "SUB CATEGORY"."\t";                  // 11-barcode 2
			echo "SERIAL NUMBER"."\t";             // 13-alternate code
			echo "QUANTITY"."\t";                  // 12-barcode 3
			echo "Item Cost"."\t"; 				// 14-product type
			echo "Total Cost"."\t";                   // 15-class id
		
			print "\n";
			while($row = mysqli_fetch_row($resultset))
			{

				$schema_insert = "";
			    $schema_insert .= "$row[0]".$delimiter;                 
				$schema_insert .= "$row[1]".$delimiter;  
				$schema_insert .= '="'."$row[2]".'"'.$delimiter;  
				$schema_insert .= "$row[3]".$delimiter; 
				$schema_insert .= "$row[4]".$delimiter; 
				$schema_insert .= "$row[5]".$delimiter; 
				$schema_insert .= "$row[6]".$delimiter; 
				$schema_insert .= "$row[7]".$delimiter; 
				$schema_insert .= "$row[8]".$delimiter; 
				$schema_insert .= "$row[9]".$delimiter; 
				$schema_insert .= "$row[10]".$delimiter; 
				$schema_insert .= "$row[11]".$delimiter; 
				$schema_insert .= "$row[12]".$delimiter; 
				$schema_insert .= "$row[13]".$delimiter; 

		        $schema_insert = str_replace($delimiter."$", "", $schema_insert);
		        $schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
		        $schema_insert .= "\t";
		        print(trim($schema_insert));
		        print "\n";
			}

			mysqli_close($conn);
			exit;
			 
			}

			//Export Conso
		public function ExportConso(Request $request){
			$data = Request::all();
			return Excel::download(new ExportConso($data), 'Consolidation-'.date('Y-m-d H:i:s') .'.xlsx');
		}

		//UPLOAD PO
		public function UploadPo() {
			$data['page_title']= 'PO Upload';
			return view('import.po-upload', $data)->render();
		}
	}