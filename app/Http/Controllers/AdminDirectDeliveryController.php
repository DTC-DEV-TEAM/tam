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
	use App\Models\AssetsInventoryReserved;
	use App\AssetsInventoryBody;

	class AdminDirectDeliveryController extends \crocodicstudio\crudbooster\controllers\CBController {

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "employee_name";
			$this->limit = "20";
			$this->orderby = "id,desc";
			$this->global_privilege = false;
			$this->button_table_action = true;
			$this->button_bulk_action = false;
			$this->button_action_style = "button_icon";
			$this->button_add = false;
			$this->button_edit = true;
			$this->button_delete = false;
			$this->button_detail = true;
			$this->button_show = true;
			$this->button_filter = true;
			$this->button_import = false;
			$this->button_export = false;
			$this->table = "direct_delivery";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Status","name"=>"status_id","join"=>"statuses,status_description"];
			$this->col[] = ["label"=>"ARF Number","name"=>"arf_number"];
			$this->col[] = ["label"=>"Request Type","name"=>"request_type_id"];
			$this->col[] = ["label"=>"Employee Name","name"=>"employee_name","join"=>"cms_users,bill_to"];
			$this->col[] = ["label"=>"Department","name"=>"department_id"];
			$this->col[] = ["label"=>"Asset Code","name"=>"asset_code"];
			$this->col[] = ["label"=>"Digits Code","name"=>"digits_code"];
			$this->col[] = ["label"=>"Item Description","name"=>"item_description"];
			$this->col[] = ["label"=>"Category","name"=>"category"];
			$this->col[] = ["label"=>"Sub Category","name"=>"sub_category"];
			$this->col[] = ["label" => "Created By", "name" => "created_by", "join" => "cms_users,name"];
			$this->col[] = ["label" => "Created At", "name" => "created_at"];

			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			
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
				if(in_array(CRUDBooster::myPrivilegeId(), [1,6])){
					$this->index_button[] = ["label"=>"Direct Delivery","icon"=>"fa fa-files-o","url"=>CRUDBooster::mainpath('direct-delivery'),"color"=>"success"];
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
		
		public function getDirectDelivery(){
			if(!CRUDBooster::isCreate() && $this->global_privilege == false) {
				CRUDBooster::redirect(CRUDBooster::adminPath(), trans('crudbooster.denied_access'));
			}

			$this->cbLoader();

			$data = array();

			$data['page_title'] = 'Direct Delivery';

			$for_move_order =  	DB::table('statuses')->where('id', 14)->value('id');

			//where('status_id', $for_move_order)->
			//$cancelled  = 		DB::table('statuses')->where('id', 8)->value('id');

			//Option 2
			$data['AssetRequest'] = HeaderRequest::
			  whereNotIn('status_id',[8,13])
			->whereNotNull('created_by')
			->get();
			
			

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

			return $this->view("direct-delivery.add-direct-delivery", $data);
		}

		public function selectedHeaderDr(Request $request) {

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
								->whereNull('assets_inventory_reserved.reserved')
								->where('body_request.header_request_id', $search)
								->get();

			$data['asset_code'] = AssetsInventoryBody::select('assets_inventory_body.*',)->where('statuses_id',6)->get();
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

					<tr>
						<input type="hidden"  class="form-control text-center finput"  name="item_description[]" id="item_description'.$tableRow.'"  required  value="'.$rowresult->item_description.'">
						<input type="hidden"  class="form-control"  name="remove_btn[]" id="remove_btn'.$tableRow.'"  required  value="'.$tableRow.'">
						<input type="hidden"  class="form-control"  name="remove_btn[]" id="category"  required  value="'.$data['Header']->request_type_id.'">
					
				';
		
				$data['ARFBody'] .='
					<td style="text-align:center" height="10">
						<input type="checkbox" name="item_to_receive_id[]" id="item_to_receive_id{{$tableRow1}}" class="item_to_receive_id" required data-id="{{$tableRow1}}" value="{{$rowresult->body_id}}"/>
					</td>
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
					<td>
					<select selected data-placeholder="Asset Code" class="form-control asset_code" name="asset_code[]" data-id="" id="asset_code" required style="width:100%">
					 <option value=""></option>
				';

				foreach($data['asset_code'] as $code){
					$data['ARFBody'] .='
				       <option value='.$code->asset_code.'>'. $code->asset_code .'</option>;
					';
				}
				$data['ARFBody'] .='
					 </select>
					</td>
			        ';
				$data['ARFBody'] .='
						<td style="text-align:center" height="10">
							<input type="text"  class="form-control text-center po_number"  name="po_number[]" id="po_number'.$tableRow.'"  required>
						</td>
			        ';
			
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
										    <th width="4%" class="text-center">Select</th>
											<th width="9%" class="text-center">Digits Code</th>
											<th width="20%" class="text-center">Item Description</th>
											<th width="9%" class="text-center">Category</th>                                                         
											<th width="9%" class="text-center">Sub Category</th> 
											<th width="5%" class="text-center">Quantity</th> 
											<th width="10%" class="text-center">Asset Code</th> 
											<th width="8%" class="text-center">Po No</th> 
											'; 
										

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
				$(".asset_code").select2({allowClear: true});

				$(".asset_code").attr("disabled", true);
                $(".po_number").attr("readonly", "readonly");

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


	}