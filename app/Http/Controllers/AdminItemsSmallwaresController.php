<?php namespace App\Http\Controllers;

	use Session;
	use Request;
	use DB;
	use CRUDBooster;

	class AdminItemsSmallwaresController extends \crocodicstudio\crudbooster\controllers\CBController {

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "id";
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
			$this->table = "items_smallwares";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Tasteless Code","name"=>"tasteless_code"];
			$this->col[] = ["label"=>"Item Description","name"=>"full_item_description"];
			$this->col[] = ["label"=>"Category","name"=>"category_id","join"=>"timfs_categories,category_description"];
			$this->col[] = ["label"=>"Sub Category","name"=>"subcategory_id","join"=>"timfs_subcategories,subcategory_description"];
			$this->col[] = ["label" => "Created At", "name" => "created_at"];
			$this->col[] = ["label" => "Updated At", "name" => "updated_at"];
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

		//TIMFS ITEM MASTER SMALLWARES ASSETS API
		public function timfsItemMaster(){
			$api = 'https://timfs.tasteless.com.ph/item_masters/api/get-items/9190564927eea2402d14d47ace841eab';
			
			$ch = curl_init($api);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($ch);
			curl_close($ch);
		
			$data = json_decode($response, true);

			$tableName = 'items_smallwares';

			$pdo = DB::connection()->getPdo();
			$columns = $pdo->query("DESCRIBE $tableName")->fetchAll(PDO::FETCH_COLUMN);
			$not_included = ['id', 'warehouse_qty', 'reservable_qty', 'is_orderable'];

			$all_items = array_merge($data['created_items'] ?? [], $data['updated_items'] ?? []);
	
			$with_default_values = [
				'id',
				'warehouse_qty', 
				'reservable_qty', 
				'is_orderable',
				'owned_price',
				'franchise_price',
				'partner_price',
				'external_price',
			];
		
			foreach ($all_items as $item) {
				$to_be_inserted = [];
				foreach ($columns as $column) {
					if (in_array($column, $with_default_values)) {
						continue;
					} else if ($column == 'brand_id') {
						$to_be_inserted['brand_id'] = $item['brands_id'];
					} else if ($column == 'group_id') {
						$to_be_inserted['group_id'] = $item['groups_id'];
					} else if ($column == 'category_id') {
						$to_be_inserted['category_id'] = $item['categories_id'];
					} else if ($column == 'subcategory_id') {
						$to_be_inserted['subcategory_id'] = $item['subcategories_id'];
					} else if ($column == 'uom_id') {
						$to_be_inserted['uom_id'] = $item['uoms_id'];
					} else if ($column == 'packaging_id') {
						$to_be_inserted['packaging_id'] = $item['uoms_set_id'];
					} else if ($column == 'skustatus_id') {
						$to_be_inserted['skustatus_id'] = $item['sku_statuses_id'];
					} else if ($column == 'skulegend_id') {
						continue;
					} else if ($column == 'currency_id') {
						$to_be_inserted['currency_id'] = $item['currencies_id'];
					} else if ($column == 'cost_price') {
						$to_be_inserted['cost_price'] = $item['purchase_price'];
					} else {
						$to_be_inserted[$column] = $item[$column];
					}
				}

				$tasteless_code = $item['tasteless_code'];
				
				$is_existing = DB::table('items_smallwares')
					->where('tasteless_code', $tasteless_code)
					->exists();

				if ($is_existing) {
					DB::table('items_smallwares')
						->where('tasteless_code', $item['tasteless_code'])
						->update($to_be_inserted);
				} else {
					DB::table('items_smallwares')->insert($to_be_inserted);
				}
			}
		}


	}