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
	use App\Imports\ItemMasterEolImport;
	use App\WarehouseLocationModel;
	use GuzzleHttp\Client;

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

			$this->col[] = ["label"=>"Tasteless Code","name"=>"digits_code"];
			$this->col[] = ["label"=>"Item Description","name"=>"item_description"];
			$this->col[] = ["label"=>"Category","name"=>"tam_category_id","join"=>"tam_categories,category_description"];
			$this->col[] = ["label"=>"Sub Category","name"=>"tam_sub_category_id","join"=>"tam_subcategories,subcategory_description"];
			//$this->col[] = ["label"=>"Location","name"=>"location","join"=>"warehouse_location_model,location"];
			$this->col[] = ["label" => "Created At", "name" => "created_at"];
			$this->col[] = ["label" => "Updated At", "name" => "updated_at"];

			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			if(CRUDBooster::getCurrentMethod() == 'getEdit' || CRUDBooster::getCurrentMethod() == 'postEditSave' || CRUDBooster::getCurrentMethod() == 'getDetail') {
			$this->form[] = ['label'=>'Item Description','name'=>'item_description','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-5'];
			$this->form[] = ['label'=>'Category','name'=>'category_id','type'=>'select','validation'=>'required|integer|min:0','width'=>'col-sm-5','datatable'=>'tam_categories,category_description','datatable_where'=>"status = 'ACTIVE'"];
			$this->form[] = ['label'=>'Sub Category','name'=>'class_id','type'=>'select','validation'=>'required|integer|min:0','width'=>'col-sm-5','datatable'=>'tam_subcategories,subcategory_description','datatable_where'=>"status = 'ACTIVE'"];
			$this->form[] = ['label'=>'Fulfillment Type','name'=>'fulfillment_type','type'=>'text','width'=>'col-sm-5'];
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
					//$this->index_button[] = ["label"=>"Add Assets","icon"=>"fa fa-plus-circle","url"=>CRUDBooster::mainpath('add-asset'),"color"=>"success"];
					$this->index_button[] = ["label"=>"Sync Data","icon"=>"fa fa-refresh","color"=>"primary"];
					// $this->index_button[] = ["label"=>"Sync Data Updated","icon"=>"fa fa-refresh","color"=>"primary"];
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

				// setInterval(getItemMasterTimfsData, 5000);
				$('#sync-data').click(function(event){
					event.preventDefault();
                    getItemMasterTimfsData();
				});
				function getItemMasterTimfsData(){
					$.ajax({
						type: 'POST',
						url: '".route('get-item-master-timfs-data')."',
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
				$('#sync-data-updated').click(function(event){
					event.preventDefault();
                    getItemMasterUpdatedTimfsData();
				});
				function getItemMasterUpdatedTimfsData(){
					$.ajax({
						type: 'POST',
						url: '".route('get-item-master-updated-timfs-data')."',
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
	             
	    }


	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate query of index result 
	    | ---------------------------------------------------------------------- 
	    | @query = current sql query 
	    |
	    */
	    public function hook_query_index(&$query) {
			$query->whereNull('assets.from_dam'); 
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
			$fields = Request::all();
			$sub_id = $fields['sub_category_id'];
			$item_description = $fields['item_description'];
			$location = $fields['location'];
			$digits_code = $fields['digits_code'];
			$cost = $fields['cost'];
			$getSubData = DB::table('class')->where('id', $sub_id)->first();
			$assetCount = DB::table('assets')->where('sub_category_id', $sub_id)->count();
            if($assetCount == 0){
				$assetCode = $getSubData->from_code;	
			}else{
				$assetCode = $getSubData->from_code + $assetCount;
			}
    
			if($assetCode > $getSubData->to_code){
				DB::table('class')->where('id',$sub_id)
					->update([
						'limit_code'   => "Code exceed in Item Master",
					]);	
				return CRUDBooster::redirect(CRUDBooster::mainpath("add-asset"),"Asset Code Exceed!","danger");
			}else{
				$postdata['asset_code'] = $assetCode;
				$postdata['digits_code'] = $digits_code;
				$postdata['item_description'] = $item_description;
				$postdata['category_id'] = $getSubData->category_id;
				$postdata['sub_category_id'] = $sub_id;
				$postdata['location'] = $location;
				$postdata['item_cost'] = $cost;
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

		public function UploadItemMaster() {
			$data['page_title']= 'Item Master Upload';
			return view('import.item-master-upload', $data)->render();
		}

		public function itemMasterUpload(Request $request) {
			$path_excel = $request->file('import_file')->store('temp');
			$path = storage_path('app').'/'.$path_excel;
			if($request->upload_type == "categories"){
			    Excel::import(new ItemMasterImport, $path);	
			}else{
				Excel::import(new ItemMasterEolImport, $path);
			}
			CRUDBooster::redirect(CRUDBooster::adminpath('assets'), trans("Upload Successfully!"), 'success');
		}

		public function getAddAsset() {

			if(!CRUDBooster::isCreate() && $this->global_privilege == false) {
				CRUDBooster::redirect(CRUDBooster::adminPath(), trans('crudbooster.denied_access'));
			}

			$this->cbLoader();
			$data['page_title'] = 'Add Asset Masterfile';
			$data['categories'] = DB::table('category')->where('category_status', 'ACTIVE')->whereIn('id', [6,4])->orderby('category_description', 'asc')->get();
			$data['sub_categories'] = DB::table('class')->where('class_status', 'ACTIVE')->whereNull('limit_code')->orderby('class_description', 'asc')->get();
			$data['warehouse_location'] = WarehouseLocationModel::where('id','!=',4)->get();
			return $this->view("masterfile.add-asset", $data);

		}

		// TIMFS ITEM MASTER CREATED
		public function getItemMasterTimfsData(Request $request) {
			$token = $this->getToken();
			$headers = array(
				'Accept: application/json',
				'Authorization: Bearer ' . $token
			);
		
			$error_msg = "";
			$url = config('env-api.get-created-items-url');

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_FAILONERROR, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			//curl_setopt($ch, CURLOPT_TIMEOUT, 0);

			$cURLresponse = curl_exec($ch);
			if (curl_errno($ch)) {
				$error_msg = curl_error($ch);
			}
			curl_close($ch);

			$cURLresponse = json_decode($cURLresponse, true);
			dd($cURLresponse);
			$data = [];
			$count = 0;
			if(!empty($cURLresponse["data"])) {
				foreach ($cURLresponse["data"] as $key => $value) {
					if($value['sku_statuses_id'] == 1){
						$status = 'ACTIVE';
					}else{
						$status = 'INACTIVE';
					}
				
					$count++;
						DB::beginTransaction();
						try {
							Assets::updateOrcreate([
								'digits_code'         => $value['tasteless_code'] 
							],
							[
								'digits_code'         => $value['tasteless_code'],
								'item_description'    => $value['full_item_description'],
								'tam_category_id'     => $value['categories_id'],
								'tam_sub_category_id' => $value['subcategories_id'],
								'dam_category_id'     => NULL,
								'dam_sub_category_id' => NULL,
								'dam_class_id'        => NULL,
								'dam_sub_class_id'    => NULL,
								'item_cost'           => $value['ttp'],
								'status'              => $status,
								'created_by'          => CRUDBooster::myId(),
								'created_at'          => date('Y-m-d H:i:s')
							]);
							DB::commit();
						} catch (\Exception $e) {
							\Log::debug($e);
							DB::rollback();
						}
					
				}
			}
			\Log::info('Item Create: executed! items');
			$message = ['status'=>'success', 'message' => 'Sync Successfully!'];
			echo json_encode($message);
		}

		// TIMFS ITEM MASTER UPDATED
		public function getItemMasterUpdatedTimfsData(Request $request) {
			$token = $this->getToken();
			$headers = array(
				'Accept: application/json',
				'Authorization: Bearer ' . $token
			);
		
			$error_msg = "";
			$url = config('env-api.get-updated-items-url');

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_FAILONERROR, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			//curl_setopt($ch, CURLOPT_TIMEOUT, 0);

			$cURLresponse = curl_exec($ch);
			if (curl_errno($ch)) {
				$error_msg = curl_error($ch);
			}
			curl_close($ch);

			$cURLresponse = json_decode($cURLresponse, true);
            //dd($cURLresponse);
			$data = [];
			$count = 0;
			if(!empty($cURLresponse["data"])) {
				foreach ($cURLresponse["data"] as $key => $value) {
					if($value['sku_statuses_id'] == 1){
						$status = 'ACTIVE';
					}else{
						$status = 'INACTIVE';
					}
				
					$count++;
						DB::beginTransaction();
						try {
							Assets::updateOrcreate([
								'digits_code'         => $value['tasteless_code'] 
							],
							[
								'digits_code'         => $value['tasteless_code'],
								'item_description'    => $value['full_item_description'],
								'tam_category_id'     => $value['categories_id'],
								'tam_sub_category_id' => $value['subcategories_id'],
								'dam_category_id'     => NULL,
								'dam_sub_category_id' => NULL,
								'dam_class_id'        => NULL,
								'dam_sub_class_id'    => NULL,
								'item_cost'           => $value['ttp'],
								'status'              => $status,
								'created_by'          => CRUDBooster::myId(),
								'created_at'          => date('Y-m-d H:i:s')
							]);
							DB::commit();
						} catch (\Exception $e) {
							\Log::debug($e);
							DB::rollback();
						}
					
				}
			}
			\Log::info('Item Create: executed! items');
			$message = ['status'=>'success', 'message' => 'Sync Successfully!'];
			echo json_encode($message);
		}

		public function getToken(){
			$client = new Client();
            $response = $client->post(config('env-api.get-token-url'), [
                'form_params' => [
                    'secret' => config('env-api.secret-key'),
                ]
            ]);

            $responseBody = $response->getBody()->getContents();
            $responseData = json_decode($responseBody, true);

            return $responseData['data']['access_token'];
		}

		//TIMFS FIXED ASSETS API
		public function assetsFaTimfs(){
			$api = 'https://timfs-test.tasteless.com.ph/item_masters_fas/api/get-items/9190564927eea2402d14d47ace841eab';
			
			$ch = curl_init($api);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($ch);
			curl_close($ch);
		
			$data = json_decode($response, true);

			$all_items = array_merge($data['created_items'] ?? [], $data['updated_items'] ?? []);
		
			$to_be_inserted = [];
			foreach ($all_items as $item) {
				if($item['sku_statuses_id'] == 1){
					$status = 'ACTIVE';
				}else if($item['sku_statuses_id'] == 2){
					$status = 'INACTIVE';
				}else{
					$status = 'FOR DEPLETION';
				}
				$to_be_inserted['digits_code']          = $item['tasteless_code'];
				$to_be_inserted['item_description']     = $item['item_description'];
				$to_be_inserted['tam_category_id']      = $item['categories_id'];
				$to_be_inserted['tam_sub_category_id']  = $item['subcategories_id'];
				$to_be_inserted['item_cost']            = $item['cost'];
				$to_be_inserted['status']               = $status;
				
				$tasteless_code = $item['tasteless_code'];
				
				$is_existing = DB::table('assets')
					->where('digits_code', $tasteless_code)
					->exists();

				if ($is_existing) {
					$to_be_inserted['updated_at'] = date('Y-m-d H:i:s');
					DB::table('assets')
						->where('digits_code', $item['tasteless_code'])
						->update($to_be_inserted);
				} else {
					$to_be_inserted['created_at'] = date('Y-m-d H:i:s');
					DB::table('assets')->insert($to_be_inserted);
				}
			}
		}

	}