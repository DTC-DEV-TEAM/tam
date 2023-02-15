<?php namespace App\Http\Controllers;

	use Session;
	//use Request;
	use DB;
	use CRUDBooster;

	use App\Assets;
	use App\Statuses;
	use Excel;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Input;
	use Illuminate\Support\Facades\Log;
	use Illuminate\Support\Facades\Redirect;
	use Maatwebsite\Excel\HeadingRowImport;
	use App\Imports\ItemMasterImport;
	//use App\Imports\InventoryUpload;

	class AdminAssetsController extends \crocodicstudio\crudbooster\controllers\CBController {

        public function __construct() {
			// Register ENUM type
			//$this->request = $request;
			DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping("enum", "string");
		}

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "id";
			$this->limit = "20";
			$this->orderby = "id,desc";
			$this->global_privilege = false;
			$this->button_table_action = true;
			$this->button_bulk_action = true;
			$this->button_action_style = "button_icon";
			$this->button_add = false;
			if(CRUDBooster::isSuperadmin()){
				$this->button_edit = true;
			}else{
				$this->button_edit = false;
			}
			$this->button_delete = false;
			$this->button_detail = true;
			$this->button_show = true;
			$this->button_filter = true;
			$this->button_import = false;
			$this->button_export = true;
			$this->table = "assets";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];

			//$this->col[] = ["label"=>"Status","name"=>"status_id","join"=>"statuses,status_description"];
			$this->col[] = ["label"=>"Device Image","name"=>"image","image"=>1];
			//$this->col[] = ["label"=>"Asset Code","name"=>"asset_code"];
			//$this->col[] = ["label"=>"Asset Tag","name"=>"asset_tag"];
			$this->col[] = ["label"=>"Digits Code","name"=>"digits_code"];
			//$this->col[] = ["label"=>"Item Type","name"=>"item_type"];
			//$this->col[] = ["label"=>"Serial No","name"=>"serial_no"];
			$this->col[] = ["label"=>"Item Description","name"=>"item_description"];
			$this->col[] = ["label"=>"Category","name"=>"category_id","join"=>"category,category_description"];
			$this->col[] = ["label"=>"Sub Category","name"=>"class_id","join"=>"class,class_description"];

			$this->col[] = ["label"=>"Useful Life","name"=>"class_id","join"=>"class,useful_life"];

			//$this->col[] = ["label"=>"Item Cost","name"=>"item_cost"];
			/*$this->col[] = ["label"=>"Quantity","name"=>"quantity"];
			$this->col[] = ["label"=>"Status","name"=>"status_id","join"=>"statuses,status_description"];
			$this->col[] = ["label"=>"Assign To","name"=>"assign_to", "join" => "cms_users,name"];
			$this->col[] = ["label"=>"Assign Date","name"=>"assign_date"]; */
			//$this->col[] = ["label" => "Created By", "name" => "created_by", "join" => "cms_users,name"];
			$this->col[] = ["label" => "Created At", "name" => "created_at"];
			//$this->col[] = ["label" => "Updated By", "name" => "updated_by", "join" => "cms_users,name"];
			$this->col[] = ["label" => "Updated At", "name" => "updated_at"];

			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			if(CRUDBooster::getCurrentMethod() == 'getEdit' || CRUDBooster::getCurrentMethod() == 'postEditSave' || CRUDBooster::getCurrentMethod() == 'getDetail') {
			$this->form[] = ['label'=>'Digits Code','name'=>'digits_code','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-5', 'readonly'=>true];
			$this->form[] = ['label'=>'Item Description','name'=>'item_description','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-5'];
			$this->form[] = ['label'=>'Category','name'=>'category_id','type'=>'select','validation'=>'required|integer|min:0','width'=>'col-sm-5','datatable'=>'category,category_description','datatable_where'=>"category_status = 'ACTIVE' && id = 1 || id = 5"];
			$this->form[] = ['label'=>'Class','name'=>'class_id','type'=>'select','validation'=>'required|integer|min:0','width'=>'col-sm-5','datatable'=>'class,class_description','datatable_where'=>"class_status = 'ACTIVE'"];

			$this->form[] = ['label'=>'Cost','name'=>'item_cost','type'=>'text','validation'=>'required','width'=>'col-sm-5'];
			
			//$this->form[] = ['label'=>'Status','name'=>'status_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-5','datatable'=>'statuses,status_description','datatable_where'=>"status_type = 'ASSETS'"];
			}
			if(CRUDBooster::getCurrentMethod() == 'getDetail'){
				//$this->form[] = ["label"=>"Created By","name"=>"created_by",'type'=>'select',"datatable"=>"cms_users,name"];
				$this->form[] = ['label'=>'Created Date','name'=>'created_at', 'type'=>'datetime'];
				//$this->form[] = ["label"=>"Updated By","name"=>"updated_by",'type'=>'select',"datatable"=>"cms_users,name"];
				$this->form[] = ['label'=>'Updated Date','name'=>'updated_at', 'type'=>'datetime'];
			}
			# END FORM DO NOT REMOVE THIS LINE

			# OLD START FORM
			//$this->form = [];
			//$this->form[] = ["label"=>"Digits Code","name"=>"digits_code","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Asset Tag","name"=>"asset_tag","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Serial No","name"=>"serial_no","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Item Description","name"=>"item_description","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Category Id","name"=>"category_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"category,id"];
			//$this->form[] = ["label"=>"Status Id","name"=>"status_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"status,id"];
			//$this->form[] = ["label"=>"Assign To","name"=>"assign_to","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
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
			|
			 @button_icon    = Font Awesome Class  
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
			/*if(CRUDBooster::isUpdate())
	        {
				if(CRUDBooster::isSuperadmin()){

					$this->button_selected[] = ['label'=>'Pending',
												'icon'=>'fa fa-check',
												'name'=>'set_pending'];

					$this->button_selected[] = ['label'=>'Ready to Deploy',
												'icon'=>'fa fa-check',
												'name'=>'set_ready_to_deploy'];			
												
												
					$this->button_selected[] = ['label'=>'Deployed',
												'icon'=>'fa fa-check',
												'name'=>'set_deployed'];	
												
				}else if(CRUDBooster::myPrivilegeName() == "Employee"){

					$this->button_selected[] = ['label'=>'Receive',
												'icon'=>'fa fa-check',
												'name'=>'set_deployed'];	
				}
											
	        }*/

	                
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
			if(CRUDBooster::getCurrentMethod() == 'getIndex') {
				if(CRUDBooster::isSuperadmin()){
					$this->index_button[] = [
						"title"=>"Upload Item Master",
						"label"=>"Upload Item Master",
						"icon"=>"fa fa-upload",
						"url"=>CRUDBooster::mainpath('item-master-upload')];
				}
				// $this->index_button[] = ["label"=>"Sync Data","icon"=>"fa fa-refresh","color"=>"primary"];
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
				$('#category_id, #class_id').select2();
				 $('#digits_code').change(function() {
					var digits_code = this.value;
					$.ajax
					({ 
						type: 'GET',
						url: 'https://localhost/dam/public/admin/assets/item/' + digits_code,
						data: '',
						success: function(result)
						{
							//alert(result.length);
					        var i;
							for (i = 0; i < result.length; ++i) {
								$('#item_description').val(result[i].item_description);
								$('#category_id').val(result[i].category_id);
								$('#brand_id').val(result[i].brand_id);
								$('#class_id').val(result[i].class_id);
								$('#vendor_id').val(result[i].vendor_id);
								$('#item_cost').val(result[i].current_srp);
							}
						}
					});
				});

				setInterval(getItemMasterData, 60*60*1000);
				function getItemMasterData(){
					$.ajax({
						type: 'POST',
						url: '".route('get-item-master-data')."',
						dataType: 'json',
						data: {
							'_token': $(\"#token\").val(),
						},
						success: function(response) {
							if (response.status == \"success\") {
								swal({
									type: response.status,
									title: response.message,
								});
								location.reload();
								} else if (response.status == \"error\") {
								swal({
									type: response.status,
									title: response.message,
								});
								}
						},
						error: function(e) {
							console.log(e);
						}
					});
				}
                
				//updated item master data
				setInterval(getItemMasterUpdatedData, 60*60*1000);
				function getItemMasterUpdatedData(){
					$.ajax({
						type: 'POST',
						url: '".route('get-item-master-updated-data')."',
						dataType: 'json',
						data: {
							'_token': $(\"#token\").val(),
						},
						success: function(response) {
							if (response.status == \"success\") {
								swal({
									type: response.status,
									title: response.message,
								});
								location.reload();
								} else if (response.status == \"error\") {
								swal({
									type: response.status,
									title: response.message,
								});
								}
						},
						error: function(e) {
							console.log(e);
						}
					});
				}


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
			if($button_name == 'set_pending') {

				$checker = Assets::whereIn('id', $id_selected)->get();

				foreach($checker as $check){

							Assets::where('id',	$check->id)->update([
										'status_id'=> 6, 
										'updated_at' => date('Y-m-d H:i:s'), 
										'updated_by' => CRUDBooster::myId()]);

				}

			}else if($button_name == 'set_ready_to_deploy') {

				$checker = Assets::whereIn('id', $id_selected)->get();

				foreach($checker as $check){

							$assign = DB::table("cms_users")->where('id', $check->assign_to)->first();

							$category = DB::table("category")->where('id', $check->category_id)->first();


							$assign_by = DB::table("cms_users")->where('id', $check->assign_by)->first();

							Assets::where('id',	$check->id)->update([
										'status_id'=> 2, 
										'updated_at' => date('Y-m-d H:i:s'), 
										'updated_by' => CRUDBooster::myId()]);


							$data = [	'assign_to'=>$assign->name,
										'asset_tag'=>$check->asset_tag,
										'digits_code'=>$check->digits_code,
										'serial_no'=>$check->serial_no,
										'item_description'=>$check->item_description,
										'category_id'=>$category->category_description,
										'assign_date'=>$check->assign_date,
										'assign_by'=>$assign_by->name
									]; 
		    
							CRUDBooster::sendEmail(['to'=>'rickyalnin201995@gmail.com','data'=>$data,'template'=>'assets_confirmation','attachments'=>$files]);

				}


			}else if($button_name == 'set_deployed') {


				$checker = Assets::whereIn('id', $id_selected)->get();

				foreach($checker as $check){

							Assets::where('id',	$check->id)->update([
										'status_id'=> 3, 
										'updated_at' => date('Y-m-d H:i:s'), 
										'updated_by' => CRUDBooster::myId()]);

				}

			}
	            
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

			// if(CRUDBooster::myPrivilegeName() == "Employee"){

			// 	$query->where('assets.assign_to', CRUDBooster::myId());

			// }else{

			// 	$query->whereNull('assets.image')->whereNull('assets.deleted_at');
			// }

	            
	    }

	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate row of index table html 
	    | ---------------------------------------------------------------------- 
	    |
	    */    
	    public function hook_row_index($column_index,&$column_value) {	        
	    	//Your code here
			$active = Statuses::where('id','9')->value('status_description');
			$inactive = Statuses::where('id','10')->value('status_description');
	

			if($column_index == 2){
				if($column_value == $active) {
					$column_value = '<span class="label label-info">'.$active.'</span>';
				}elseif($column_value == $inactive) {
				    $column_value = '<span class="label label-warning">'.$inactive.'</span>';
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

			$postdata['created_by']=CRUDBooster::myId();


			$postdata['status_id']= 9;
			

			$postdata['total_quantity']= $postdata['quantity'];

			

			$count_header = DB::table("assets")->count();
			$header_ref   =  str_pad($count_header + 1, 7, '0', STR_PAD_LEFT);			
			$reference_number	= "ASS-".$header_ref;
			$reference_number1	= "DTC-".$header_ref;

			$postdata['asset_code']= $reference_number;

			$postdata['asset_tag']= $reference_number1;

			//$postdata['category_id'] 		= DB::table('category')->where('category_description', $postdata['category_id'])->value('id');	
			//$postdata['brand_id'] 		= DB::table('brand')->where('brand_description', $postdata['brand_id'])->value('id');	
			//$postdata['class_id'] 		= DB::table('class')->where('class_description', $postdata['class_id'])->value('id');	
			//$postdata['vendor_id'] 		= DB::table('vendor')->where('vendor_name', $postdata['vendor_id'])->value('id');


			if($postdata['assign_to'] != null || $postdata['assign_to'] != "" ){

				$postdata['status_id']= 2;

				$postdata['assign_by']=		CRUDBooster::myId();
				$postdata['assign_date']=	date('Y-m-d H:i:s');



				//$check = Assets::where('id', $id)->first();

				$assign = DB::table("cms_users")->where('id', $postdata['assign_to'])->first();

				$category = DB::table("category")->where('id', $postdata['category_id'])->first();

				$assign_by = DB::table("cms_users")->where('id',CRUDBooster::myId())->first();

				$data = [	'assign_to'=>			$assign->name,
							'asset_tag'=>			$postdata['asset_tag'],
							'digits_code'=>			$postdata['digits_code'],
							'serial_no'=>			$postdata['serial_no'],
							'item_description'=>	$postdata['item_description'],
							'category_id'=>			$category->category_description,
							'assign_date'=>			date('Y-m-d H:i:s'),
							'assign_by'=>			$assign_by->name
						]; 

				CRUDBooster::sendEmail(['to'=>'rickyalnin201995@gmail.com','data'=>$data,'template'=>'assets_confirmation','attachments'=>$files]);

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

			$check = Assets::where('id', $id)->first();

			if($check->assign_to != null || $check->assign_to != ""){

					

					$assign = DB::table("cms_users")->where('id', $check->assign_to)->first();

					$category = DB::table("category")->where('id', $check->category_id)->first();

					$assign_by = DB::table("cms_users")->where('id', $check->assign_by)->first();

					$data = [	'assign_to'=>$assign->name,
								'asset_tag'=>$check->asset_tag,
								'digits_code'=>$check->digits_code,
								'serial_no'=>$check->serial_no,
								'item_description'=>$check->item_description,
								'category_id'=>$category->category_description,
								'assign_date'=>$check->assign_date,
								'assign_by'=>$assign_by->name
							]; 

					CRUDBooster::sendEmail(['to'=>'rickyalnin201995@gmail.com','data'=>$data,'template'=>'assets_confirmation','attachments'=>$files]);
			}

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

		public function Items($id)
		{
			$item_details = DB::table("digits_imfs")
						->leftjoin('category', 'digits_imfs.category_id', '=', 'category.id')
						->leftjoin('brand', 'digits_imfs.brand_id', '=', 'brand.id')
						->leftjoin('class', 'digits_imfs.class_id', '=', 'class.id')
						->leftjoin('vendor', 'digits_imfs.vendor_id', '=', 'vendor.id')
						
						->select( 	'digits_imfs.*', 
									'category.category_description as category_description',
									'brand.brand_description as brand_description',
									'class.class_description as class_description',
									'vendor.vendor_name as vendor_name'
								)
						->where('digits_imfs.id', $id)->get();

			return $item_details;
		}

		public function UploadItemMaster() {
			$data['page_title']= 'Item Master Upload';
			return view('import.item-master-upload', $data)->render();
		}

		public function itemMasterUpload(Request $request) {
			$path_excel = $request->file('import_file')->store('temp');
			$path = storage_path('app').'/'.$path_excel;
			Excel::import(new ItemMasterImport, $path);	
			CRUDBooster::redirect(CRUDBooster::adminpath('assets'), trans("Upload Successfully!"), 'success');
		}

		public function getItemMasterDataApi(Request $request) {
			$secretKey = "1f9653c84409990a899f5bd63f719771"; 
            $uniqueString = time(); 
            $userAgent = $_SERVER['HTTP_USER_AGENT']; 
            $userAgent = $_SERVER['HTTP_USER_AGENT']; 
            if($userAgent == '' || is_null($userAgent)){
                $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Safari/537.36';    
            }
            $xAuthorizationToken = md5( $secretKey . $uniqueString . $userAgent);
            $xAuthorizationTime = $uniqueString;
            $vars = [
                "your_param"=>1
            ];
    
            //https://stackoverflow.com/questions/8115683/php-curl-custom-headers
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,"https://aimfs.digitstrading.ph/public/api/imfs_items_created");
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_POST, FALSE);
            curl_setopt($ch, CURLOPT_POSTFIELDS,null);
            curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, 300);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 30);
    
            $headers = [
            'X-Authorization-Token: ' . $xAuthorizationToken,
            'X-Authorization-Time: ' . $xAuthorizationTime,
            'User-Agent: '.$userAgent
            ];
    
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $server_output = curl_exec ($ch);
            curl_close ($ch);
    
            $response = json_decode($server_output, true);
          
            $data = [];
            $count = 0;
            if(!empty($response["data"])) {
				foreach ($response["data"] as $key => $value) {
					$count++;
						DB::beginTransaction();
						try {
							Assets::updateOrcreate([
								'digits_code' => $row['digits_code'] 
							],
							[
								'digits_code' => $value['digits_code'],
								'item_description' => $value['item_description'],
								'brand_id' => $value['brand_id'],
								'category_id' => $value['category_id'],
								'class_id' => $value['class_id'],
								'vendor_id' => $value['vendor_id'],
								'item_cost' => $value['current_srp'],
								'asset_tag' => "",
								'quantity' => 0,
								'add_quantity' => 0,
								'total_quantity' => 0,
								'status_id' => 0,
								'created_by' => CRUDBooster::myId(),
								'created_at' => date('Y-m-d H:i:s'),
							]);
							DB::commit();
						} catch (\Exception $e) {
							\Log::debug($e);
							DB::rollback();
						}
					
				}
            }
            \Log::info('Item Create: executed! items');
		}

		public function getItemMasterUpdatedDataApi(Request $request) {
			$secretKey = "1f9653c84409990a899f5bd63f719771"; 
            $uniqueString = time(); 
            $userAgent = $_SERVER['HTTP_USER_AGENT']; 
            $userAgent = $_SERVER['HTTP_USER_AGENT']; 
            if($userAgent == '' || is_null($userAgent)){
                $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Safari/537.36';    
            }
            $xAuthorizationToken = md5( $secretKey . $uniqueString . $userAgent);
            $xAuthorizationTime = $uniqueString;
            $vars = [
                "your_param"=>1
            ];
    
            //https://stackoverflow.com/questions/8115683/php-curl-custom-headers
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,"https://aimfs.digitstrading.ph/public/api/imfs_items_updated");
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_POST, FALSE);
            curl_setopt($ch, CURLOPT_POSTFIELDS,null);
            curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, 300);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 30);
    
            $headers = [
            'X-Authorization-Token: ' . $xAuthorizationToken,
            'X-Authorization-Time: ' . $xAuthorizationTime,
            'User-Agent: '.$userAgent
            ];
    
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $server_output = curl_exec ($ch);
            curl_close ($ch);
    
            $response = json_decode($server_output, true);
       
            $data = [];
            $count = 0;
            if(!empty($response["data"])) {
				foreach ($response["data"] as $key => $value) {
					$count++;
						DB::beginTransaction();
						try {
							Assets::updateOrcreate([
								'digits_code' => $row['digits_code'] 
							],
							[
								'digits_code' => $value['digits_code'],
								'item_description' => $value['item_description'],
								'brand_id' => $value['brand_id'],
								'category_id' => $value['category_id'],
								'class_id' => $value['class_id'],
								'vendor_id' => $value['vendor_id'],
								'item_cost' => $value['current_srp'],
								'asset_tag' => "",
								'quantity' => 0,
								'add_quantity' => 0,
								'total_quantity' => 0,
								'status_id' => 0,
								'updated_by' => CRUDBooster::myId(),
								'updated_at' => date('Y-m-d H:i:s'),
							]);
							DB::commit();
						} catch (\Exception $e) {
							\Log::debug($e);
							DB::rollback();
						}
					
				}
            }
            \Log::info('Item Create: executed! items');
		}


	}