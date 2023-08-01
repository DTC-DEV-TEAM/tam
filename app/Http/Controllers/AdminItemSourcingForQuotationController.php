<?php namespace App\Http\Controllers;

	use Session;
	use Request;
	use DB;
	use CRUDBooster;
	use App\StatusMatrix;
	use App\Models\ItemHeaderSourcing;
	use App\Models\ItemBodySourcing;
	use App\Models\ItemSourcingComments;
	use App\Models\ItemSourcingOptions;
	use App\Models\ItemSourcingOptionsFile;
	use App\Models\ItemSourcingHeaderFile;
	use App\HeaderRequest;
	use App\BodyRequest;
	use App\Statuses;
	use App\Exports\ExportItemSource;
	use Maatwebsite\Excel\Facades\Excel;
	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Reader\Exception;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
	use PhpOffice\PhpSpreadsheet\IOFactory;
	use Illuminate\Support\Facades\Response;
	use App\Mail\EmailForPo;
	use Mail;

	class AdminItemSourcingForQuotationController extends \crocodicstudio\crudbooster\controllers\CBController {
		private $forApproval;
		private $cancelled;
		private $closed;
		private $forDiscussion;
		private $forSourcing;
		private $forStreamlining;
		private $forItemCreation;
		private $forArfCreation;
		private $rejected;

		public function __construct() {
			DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping("enum", "string");
			$this->forApproval      =  1;    
			$this->cancelled        =  8;
			$this->closed           =  13;      
			$this->forDiscussion    =  37;  
			$this->forSourcing      =  38;
			$this->forStreamlining  =  39;   
			$this->forItemCreation  =  40;        
			$this->forArfCreation   =  41;
		    $this->rejected         =  5;
		}
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
			$this->button_detail = false;
			$this->button_show = true;
			$this->button_filter = true;
			$this->button_import = false;
			$this->button_export = false;
			$this->table = "item_sourcing_header";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Status","name"=>"status_id","join"=>"statuses,status_description"];
			$this->col[] = ["label"=>"Reference Number","name"=>"reference_number"];
			$this->col[] = ["label"=>"Request Type","name"=>"request_type_id","join"=>"requests,request_name"];
			$this->col[] = ["label"=>"Company Name","name"=>"company_name"];
			$this->col[] = ["label"=>"Employee Name","name"=>"employee_name","join"=>"cms_users,bill_to"];
			$this->col[] = ["label"=>"Department","name"=>"department","join"=>"departments,department_name"];
			$this->col[] = ["label"=>"Requested By","name"=>"created_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"Requested Date","name"=>"created_at"];
			//$this->col[] = ["label"=>"Updated By","name"=>"updated_by","join"=>"cms_users,name"];
			//$this->col[] = ["label"=>"Updated Date","name"=>"updated_at"];

			$this->col[] = ["label"=>"Approved By","name"=>"approved_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"Approved Date","name"=>"approved_at"];
			$this->col[] = ["label"=>"Rejected Date","name"=>"rejected_at"];
			# END COLUMNS DO NOT REMOVE THIS LINE

			
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
			if(CRUDBooster::getCurrentMethod() == 'getIndex') {
				$this->index_button[] = ["label"=>"Export Data","icon"=>"fa fa-download","url"=>CRUDBooster::mainpath('item-source-export')];
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
	        //$this->load_js[] = asset("datetimepicker/bootstrap-datetimepicker.min.js");
	        $this->load_js[] = asset("js/spinner.js");
	        
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
			//$this->load_css[] = asset("datetimepicker/bootstrap-datetimepicker.min.css");
			$this->load_css[] = asset("css/chatbox.css");
	        $this->load_css[] = asset("css/spinner.css");
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
				$query->where(function($sub_query){
					$sub_query->whereNull('item_sourcing_header.deleted_at');
				});

				$query->orderBy('item_sourcing_header.status_id', 'desc')->orderBy('item_sourcing_header.id', 'asc');
			}else{

				$query->where(function($sub_query){
					$sub_query->whereIn('item_sourcing_header.status_id', [$this->forDiscussion, $this->forSourcing,$this->forStreamlining,$this->forItemCreation,$this->forArfCreation])->whereNull('item_sourcing_header.deleted_at'); 
				});

				$query->orderBy('item_sourcing_header.status_id', 'desc')->orderBy('item_sourcing_header.id', 'asc');

			}
	            
	    }

	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate row of index table html 
	    | ---------------------------------------------------------------------- 
	    |
	    */    
	    public function hook_row_index($column_index,&$column_value) {	        
	    	$forApproval        = DB::table('statuses')->where('id', $this->forApproval)->value('status_description');     
			$cancelled          = DB::table('statuses')->where('id', $this->cancelled)->value('status_description');   
			$closed             = DB::table('statuses')->where('id', $this->closed)->value('status_description');  
			$forDiscussion      = DB::table('statuses')->where('id', $this->forDiscussion)->value('status_description');  
			$forSourcing        = DB::table('statuses')->where('id', $this->forSourcing)->value('status_description');  
			$forStreamlining    = DB::table('statuses')->where('id', $this->forStreamlining)->value('status_description');
			$forItemCreation    = DB::table('statuses')->where('id', $this->forItemCreation)->value('status_description');
			$forArfCreation     = DB::table('statuses')->where('id', $this->forArfCreation)->value('status_description');
			$rejected           = DB::table('statuses')->where('id', $this->rejected)->value('status_description');	
			
			if($column_index == 1){
				if($column_value == $forApproval){
					$column_value = '<span class="label label-warning">'.$forApproval.'</span>';
				}else if($column_value == $forDiscussion){
					$column_value = '<span class="label label-info">'.$forDiscussion.'</span>';
				}else if($column_value == $closed){
					$column_value = '<span class="label label-success">'.$closed.'</span>';
				}else if($column_value == $cancelled){
					$column_value = '<span class="label label-danger">'.$cancelled.'</span>';
				}else if($column_value == $forStreamlining){
					$column_value = '<span class="label label-info">'.$forStreamlining.'</span>';
				}else if($column_value == $forSourcing){
					$column_value = '<span class="label label-info">'.$forSourcing.'</span>';
				}else if($column_value == $forItemCreation){
					$column_value = '<span class="label label-info">'.$forItemCreation.'</span>';
				}else if($column_value == $forArfCreation){
					$column_value = '<span class="label label-info">'.$forArfCreation.'</span>';
				}else if($column_value == $rejected){
					$column_value = '<span class="label label-danger">'.$rejected.'</span>';
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
	        $fields             = Request::all();
			// dd($fields);
			$status             = $fields['status'];
			$po_no              = $fields['po_no'];
			$header_id          = $fields['headerID'];
			$ids                = $fields['ids'];
			$option             = $fields['option'];
			$vendor_name        = $fields['vendor_name'];
			$price              = $fields['price'];
			$optionFile         = $fields['optionFile'];
			$button_action      = $fields['button_action'];

			if($button_action == 1){
				ItemHeaderSourcing::where('id',$header_id)
					->update([
						'status_id'		    => $status,
						'processed_by'      => CRUDBooster::myId(),
						'processed_at'      => date('Y-m-d H:i:s'),
					]);	
				
				if($option){
					$latestOption = DB::table('item_sourcing_options')->select('id')->orderBy('id','DESC')->first();
					$latestOptionId = $latestOption->id != NULL ? $latestOption->id : 0;
					for($x=0; $x < count((array)$option); $x++) {		
						$dataLines[$x]['header_id']         = $header_id;
						$dataLines[$x]['options'] 	        = $option[$x];
						$dataLines[$x]['vendor_name'] 		= $vendor_name[$x];
						$dataLines[$x]['price'] 	        = intval(str_replace(',', '', $price[$x]));
						$dataLines[$x]['created_by']        = CRUDBooster::myId();
						$dataLines[$x]['created_at'] 		= date('Y-m-d H:i:s');
						
					}

					ItemSourcingOptions::insert($dataLines);
					$optId = DB::table('item_sourcing_options')->select('*')->where('id','>', $latestOptionId)->get();

					$finalOptId = [];
					foreach($optId as $optData){
						array_push($finalOptId, $optData->id);
					}
					$item_sourcing_header = ItemHeaderSourcing::where(['id' => $header_id])->first();
					$countHeader = DB::table('item_sourcing_options')->where('item_sourcing_options.header_id', $id)->count();
					$finalCountHead = $countHeader;
					$documents = [];
					if (!empty($optionFile)) {
						$counter = 0;
						foreach($optionFile as $key => $file){
							$counter++;
							$name = $item_sourcing_header->reference_number .'-'. $option[$key] .'.'.$file->getClientOriginalExtension();
							$filename = $name;
							$file->move('vendor/crudbooster/item_source',$filename);
							$documents[]= $filename;

							$header_documents = new ItemSourcingOptionsFile;
							$header_documents->header_id 		    = $header_id;
							$header_documents->opt_body_id          = $finalOptId[$key];
							$header_documents->file_name 		    = $filename;
							$header_documents->ext 		            = $file->getClientOriginalExtension();
							$header_documents->created_by 		    = CRUDBooster::myId();
							$header_documents->save();
						}
					}
				}
		    }else{
				$countHeader = DB::table('item_sourcing_options')->where('item_sourcing_options.header_id', $id)->count();
				if($countHeader === 0){
                    return  CRUDBooster::redirect(CRUDBooster::mainpath('edit/'.$header_id), trans('Please add an Options before close transaction!'), 'danger');
				}else{
					ItemHeaderSourcing::where('id',$header_id)
						->update([
							'status_id'		 => $status,
							'po_number'      => $po_no,
							'closed_by'      => CRUDBooster::myId(),
							'closed_at'      => date('Y-m-d H:i:s'),
						]);	
						return CRUDBooster::redirect(CRUDBooster::mainpath(), trans('Transaction Closed!'), 'success');
				}
				
			}

			return CRUDBooster::redirect(CRUDBooster::mainpath('edit/'.$header_id), trans('Successfully Added!'), 'success');
		    

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

		public function getEdit($id){
			$this->cbLoader();
            if(!CRUDBooster::isRead() && $this->global_privilege==FALSE) {    
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

			$data = array();
			$data['page_title']   = 'Item Sourcing For PO';
			$data['Header']       = ItemHeaderSourcing::header($id);
			$data['Body']         = ItemBodySourcing::body($id);
			$data['comments']     = ItemSourcingComments::comments($id);
		    $data['item_options'] = ItemSourcingOptions::options($id);

			$data['statuses'] = Statuses::select('statuses.*')->whereIn('id', [37,38,39,40,41, 13, 8])->get();
			$data['countOptions'] = DB::table('item_sourcing_options')->where('item_sourcing_options.header_id', $id)->whereNotNull('deleted_at')->count();
			$data['versions'] = DB::table('item_sourcing_edit_versions')->where('header_id', $id)->latest('created_at')->first();
			$data['allOptions'] = DB::table('item_sourcing_options')->where('item_sourcing_options.header_id', $id)->count();
            $data['header_files'] = ItemSourcingHeaderFile::select('item_sourcing_header_file.*')->where('item_sourcing_header_file.header_id', $id)->get();
			$data['yesno']        = DB::table('sub_masterfile_yes_no')->get();
			return $this->view("item-sourcing.item-sourcing-for-po", $data);
		}

		public function getDownload($id) {

			$getFile = DB::table('item_sourcing_option_file')->where('id',$id)->first();
			$file= public_path(). "/vendor/crudbooster/item_source/".$getFile->file_name;

			$headers = array(
					'Content-Type: application/pdf',
					);

			return Response::download($file, $getFile->file_name, $headers);
		}

		//Export Conso
		public function getItemSourceExport(){
			return Excel::download(new ExportItemSource, 'item-source-data-'.date('Y-m-d') .'.xlsx');
		}

		//Select row in option
		public function addDigitsCode(Request $request){
			$data            = Request::all();	
			$id              = $data['header_id'];
			$digits_code     = $data['digits_code'];
			$request_type_id = $data['request_type_id'];

			$checkRowDbDigitsCode       = DB::table('assets')->select("digits_code AS codes")->get()->toArray();
            $checkRowDbColumnDigitsCode = array_column($checkRowDbDigitsCode, 'codes');
            
			if(in_array($request_type_id, [1,5,7])){
				if(!in_array($digits_code, $checkRowDbColumnDigitsCode)){
					exit(json_encode($message = ['status'=>'error', 'message' => 'Digits Code not exist in Item Master!']));
				}
			}

			$header_info   = ItemHeaderSourcing::headerInfo($id);
			$body_info     = ItemBodySourcing::bodyInfo($id);
			
			$option_into   = DB::table('item_sourcing_options')->where('header_id',$id)->whereNotNull('selected_at')->first();
			$file_info     = DB::table('item_sourcing_option_file')->where('opt_body_id',$option_into->id)->first();

			if(!$file_info){
               exit(json_encode($message = ['status'=>'error', 'message' => 'Option not yet selected!']));
			}
           
			ItemBodySourcing::where('header_request_id', $id)
			->update([
				'digits_code'=> 		$digits_code
			]);	

			ItemHeaderSourcing::where('id',$id)
			->update([
				'status_id'		    => 41,
				'processed_by'      => CRUDBooster::myId(),
				'processed_at'      => date('Y-m-d H:i:s'),
			]);	

			//SEND EMAIL
			$infos['reference_number'] = $header_info->reference_number;
			$infos['created_at']       = $header_info->created_at;
			$infos['employee_name']    = $header_info->bill_to;
			$infos['company_name']     = $header_info->company_name;
			$infos['department']       = $header_info->department_name;
			$infos['position']         = $header_info->position;
			$infos['date_needed']      = $header_info->date_needed;
			$infos['status']           = $header_info->status_description;
			$infos['request_type']     = $header_info->request_name;
			$infos['digits_code']      = $body_info->digits_code;
			$infos['item_description'] = $body_info->item_description;
			$infos['category']         = $body_info->category_description;
			$infos['sub_category']     = $body_info->sub_category_description;
			$infos['class']            = $body_info->class_description;
			$infos['sub_class']        = $body_info->sub_class_description;
			$infos['brand']            = $body_info->brand;
			$infos['model']            = $body_info->model;
			$infos['size']             = $body_info->size;
			$infos['actual_color']     = $body_info->actual_color;
			$infos['quantity']         = $body_info->quantity;
			$infos['budget']           = $body_info->budget;
			$infos['attachment']       = $file_info->file_name;

			// $sdm                       = "sdm@digits.ph";
			// $purchasing                = "purchasing@digits.ph";
			$sdm                       = "marvinmosico@digits.ph";
			$purchasing                = "marvinmosico@digits.ph";
		
			if($request_type_id == 7){
				$infos['subject'] = "SUPPLIES-NEW ORDER-REF#";
				$infos['assign_to'] = $sdm;
				Mail::to($sdm)
				//->cc([$fhil])
				->send(new EmailForPo($infos));
			}else if($request_type_id == 1){
				$infos['subject'] = "IT ASSETS-NEW ORDER-REF#";
				$infos['assign_to'] = $purchasing;
				Mail::to($purchasing)
				//->cc([$fhil])
				->send(new EmailForPo($infos));
			}else if($request_type_id == 5){
				$infos['subject'] = "ADMIN ASSETS-NEW ORDER-REF#";
				$infos['assign_to'] = $purchasing;
				Mail::to($purchasing)
				//->cc([$fhil])
				->send(new EmailForPo($infos));
			}else if($request_type_id == 6){
				$infos['subject'] = "MARKETING-NEW ORDER-REF#";
				$infos['assign_to'] = $purchasing;
				Mail::to($purchasing)
				//->cc([$fhil])
				->send(new EmailForPo($infos));
			}else if($request_type_id == 9){
				$infos['subject'] = "BREX-NEW ORDER-REF#";
				$infos['assign_to'] = $purchasing;
				Mail::to($purchasing)
				//->cc([$fhil])
				->send(new EmailForPo($infos));
			}else if($request_type_id == 10){
				$infos['subject'] = "SUBSCRIPTION-NEW ORDER-REF#";
				$infos['assign_to'] = $purchasing;
				Mail::to($purchasing)
				//->cc([$fhil])
				->send(new EmailForPo($infos));
			}

			$message = ['status'=>'success', 'message' => 'Save Successfully!'];
			echo json_encode($message);
			
		}


	}

	?>