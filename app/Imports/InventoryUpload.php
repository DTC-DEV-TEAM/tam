<?php

namespace App\Imports;
use DB;
use CRUDBooster;
use App\AssetsInventoryBody;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Storage;
use App\MoveOrder;
use App\Assets;
use App\Users;
use App\Models\AssetCodeModel;
class InventoryUpload implements ToCollection, SkipsEmptyRows, WithHeadingRow, WithValidation
{
    public function __construct() {
        $this->digits_code = Assets::get();
    }

    /**
     * @param array $row
     *
     * @return Users|null
     */
    public function collection(Collection $rows){   
        $cooking_and_equipment = DB::table('class')->find(1);
        //$DatabaseCounterCE = DB::table('assets_inventory_body')->where('sub_category_id',$cooking_and_equipment->id)->count();
        $DatabaseCounterCE = $cooking_and_equipment->code_counter;
        $refrigeration_equipment = DB::table('class')->find(2);
		//$DatabaseCounterRE = DB::table('assets_inventory_body')->where('sub_category_id',$refrigeration_equipment->id)->count();
        $DatabaseCounterRE = $refrigeration_equipment->code_counter;
        $commercial_ovens = DB::table('class')->find(3);
		$DatabaseCounterCO = DB::table('assets_inventory_body')->where('sub_category_id',$commercial_ovens->id)->count();
        $refrigeration_and_freezer = DB::table('class')->find(4);
		$DatabaseCounterRAF = DB::table('assets_inventory_body')->where('sub_category_id',$refrigeration_and_freezer->id)->count();
        $commercial_sinks = DB::table('class')->find(5);
		$DatabaseCounterCS = DB::table('assets_inventory_body')->where('sub_category_id',$commercial_sinks->id)->count();
        $work_table_stations = DB::table('class')->find(6);
        $DatabaseCounterWTS = DB::table('assets_inventory_body')->where('sub_category_id',$work_table_stations->id)->count();
        $food_preparation_equipmen = DB::table('class')->find(7);
        $DatabaseCounterFPE = DB::table('assets_inventory_body')->where('sub_category_id',$food_preparation_equipmen->id)->count();
        $faucet_and_plumbing = DB::table('class')->find(8);
        $DatabaseCounterFAP = DB::table('assets_inventory_body')->where('sub_category_id',$faucet_and_plumbing->id)->count();
        $food_holding_warming_equip = DB::table('class')->find(9);
        $DatabaseCounterFHWE = DB::table('assets_inventory_body')->where('sub_category_id',$food_holding_warming_equip->id)->count();
        $other_restaurant_equipment = DB::table('class')->find(10);
        $DatabaseCounterORE = DB::table('assets_inventory_body')->where('sub_category_id',$other_restaurant_equipment->id)->count();
        $other_vehicle = DB::table('class')->find(11);
        $DatabaseCounterOV = DB::table('assets_inventory_body')->where('sub_category_id',$other_vehicle->id)->count();
        $other_fixed_asset = DB::table('class')->find(12);
        $DatabaseCounterOFA = DB::table('assets_inventory_body')->where('sub_category_id',$other_fixed_asset->id)->count();
        $communication_equipment = DB::table('class')->find(13);
        $DatabaseCounterCOMME = DB::table('assets_inventory_body')->where('sub_category_id',$communication_equipment->id)->count();
        $furnitures_fixtures = DB::table('class')->find(14);
        $DatabaseCounterFF = DB::table('assets_inventory_body')->where('sub_category_id',$furnitures_fixtures->id)->count();
        $facilities_equipment = DB::table('class')->find(15);
        $DatabaseCounterFE = DB::table('assets_inventory_body')->where('sub_category_id',$facilities_equipment->id)->count();
        $leasehold_equipment = DB::table('class')->find(16);
        $DatabaseCounterLE = DB::table('assets_inventory_body')->where('sub_category_id',$leasehold_equipment->id)->count();
        $machinery_equipmen = DB::table('class')->find(17);
        $DatabaseCounterME = DB::table('assets_inventory_body')->where('sub_category_id',$machinery_equipmen->id)->count();
        $vehicle = DB::table('class')->find(18);
        $DatabaseCounterV = DB::table('assets_inventory_body')->where('sub_category_id',$vehicle->id)->count();
        $computer_software_program = DB::table('class')->find(19);
        $DatabaseCounterCSP = DB::table('assets_inventory_body')->where('sub_category_id',$computer_software_program->id)->count();

        foreach ($rows->toArray() as $row) {
            $name_id      = DB::table('cms_users')->where('id','!=',1)->where(DB::raw('LOWER(TRIM(email))'),strtolower(trim($row['email'])))->value('id');
            $name         = DB::table('cms_users')->where('id','!=',1)->where(DB::raw('LOWER(TRIM(email))'),strtolower(trim($row['email'])))->value('name');
            $item_id 	  = DB::table('assets')->where(['digits_code' => preg_replace('/\s+/', '', $row['digits_code'])])->first();
            $itemCat 	  = DB::table('tam_categories')->where(['id' => $item_id->category_id])->first();
            $location_id  = DB::table('warehouse_location_model')->where(DB::raw('LOWER(TRIM(location))'),strtolower(trim($row['location'])))->first();
            $sub_cat_code = DB::table('class')->where(DB::raw('LOWER(TRIM(class_description))'),strtolower(trim($row['sub_category_code'])))->value('id');
        
            if(strtolower($row['status']) == "working" && empty($row['email'])){
				$statuses = 6;
                $item_condition = "Good";
                $deployed = NULL;
                $quantity = $row['qty'];
			}else if(strtolower($row['status']) == "defective" && empty($row['email'])) {
                $statuses = 23;
                $item_condition = "Defective";
                $deployed = NULL;
                $quantity = $row['qty'];
            }else{
                $statuses = 3;
                $item_condition = "Good";
                $deployed = $name;
                $quantity = 0;           
            }

            if(empty($row['email'])){
                $location = $location_id->id;
            }else{
                $location = 4;
            }
            
            if($sub_cat_code == 1){
                //$asset_code                = $cooking_and_equipment->from_code + $DatabaseCounterCE;
                $asset_code                = $DatabaseCounterCE;
                DB::table('class')->where('id',1)->increment('code_counter');
                $DatabaseCounterCE++; 
                $request_type_id_inventory = 5;
                $item_category             = "FIXED ASSET";
                $sub_cat_id                = $sub_cat_code;

                if($asset_code > $cooking_and_equipment->to_code){
                    DB::table('class')->where('id',$cooking_and_equipment->id)
                    ->update([
                        'limit_code'   => 1
                    ]);	
                    return CRUDBooster::redirect(CRUDBooster::mainpath(),"Asset Code Exceed in Asset Lists! ".$cooking_and_equipment->class_description." : ".($key+2),"danger");
                }

            }else if($sub_cat_code == 2){
                //$asset_code                = $refrigeration_equipment->from_code + $DatabaseCounterRE;
                $asset_code                = $DatabaseCounterRE;
                DB::table('class')->where('id',2)->increment('code_counter');
                $DatabaseCounterRE++; 
                $request_type_id_inventory = 5;
                $item_category             = "FIXED ASSET";
                $sub_cat_id                = $sub_cat_code;

                if($asset_code > $refrigeration_equipment->to_code){
                    DB::table('class')->where('id',$refrigeration_equipment->id)
                    ->update([
                        'limit_code'   => "Code exceed in Asset Lists",
                    ]);	
                    return CRUDBooster::redirect(CRUDBooster::mainpath(),"Asset Code Exceed in Asset Lists! ".$refrigeration_equipment->class_description." : ".($key+2),"danger");
                }

            }else if($sub_cat_code == 3){
                $asset_code                = $commercial_ovens->from_code + $DatabaseCounterCO;
                $DatabaseCounterCO++; 
                $request_type_id_inventory = 5;
                $item_category             = "FIXED ASSET";
                $sub_cat_id                = $sub_cat_code;

                if($asset_code > $commercial_ovens->to_code){
                    DB::table('class')->where('id',$commercial_ovens->id)
                    ->update([
                        'limit_code'   => "Code exceed in Asset Lists",
                    ]);	
                    return CRUDBooster::redirect(CRUDBooster::mainpath(),"Asset Code Exceed in Asset Lists! ".$commercial_ovens->class_description." : ".($key+2),"danger");
                }
                
            }else if($sub_cat_code == 4){
                $asset_code                = $refrigeration_and_freezer->from_code + $DatabaseCounterRAF;
                $DatabaseCounterRAF++; 
                $request_type_id_inventory = 5;
                $item_category             = "FIXED ASSET";
                $sub_cat_id                = $sub_cat_code;

                if($asset_code > $refrigeration_and_freezer->to_code){
                    DB::table('class')->where('id',$refrigeration_and_freezer->id)
                    ->update([
                        'limit_code'   => "Code exceed in Asset Lists",
                    ]);	
                    return CRUDBooster::redirect(CRUDBooster::mainpath(),"Asset Code Exceed in Asset Lists! ".$refrigeration_and_freezer->class_description." : ".($key+2),"danger");
                }

            }else if($sub_cat_code == 5){
                $asset_code                = $commercial_sinks->from_code + $DatabaseCounterCS;
                $DatabaseCounterCS++; 
                $request_type_id_inventory = 5;
                $item_category             = "FIXED ASSET";
                $sub_cat_id                = $sub_cat_code;

                if($asset_code > $commercial_sinks->to_code){
                    DB::table('class')->where('id',$commercial_sinks->id)
                    ->update([
                        'limit_code'   => "Code exceed in Asset Lists",
                    ]);	
                    return CRUDBooster::redirect(CRUDBooster::mainpath(),"Asset Code Exceed in Asset Lists! ".$commercial_sinks->class_description." : ".($key+2),"danger");
                }

            }else if($sub_cat_code == 6){
                $asset_code                = $work_table_stations->from_code + $DatabaseCounterWTS;
                $DatabaseCounterWTS++; 
                $request_type_id_inventory = 5;
                $item_category             = "FIXED ASSET";
                $sub_cat_id                = $sub_cat_code;

                if($asset_code > $work_table_stations->to_code){
                    DB::table('class')->where('id',$work_table_stations->id)
                    ->update([
                        'limit_code'   => "Code exceed in Asset Lists",
                    ]);	
                    return CRUDBooster::redirect(CRUDBooster::mainpath(),"Asset Code Exceed in Asset Lists! ".$work_table_stations->class_description." : ".($key+2),"danger");
                }

            }else if($sub_cat_code == 7){
                $asset_code                = $food_preparation_equipmen->from_code + $DatabaseCounterFPE;
                $DatabaseCounterFPE++; 
                $request_type_id_inventory = 5;
                $item_category             = "FIXED ASSET";
                $sub_cat_id                = $sub_cat_code;

                if($asset_code > $food_preparation_equipmen->to_code){
                    DB::table('class')->where('id',$food_preparation_equipmen->id)
                    ->update([
                        'limit_code'   => "Code exceed in Asset Lists",
                    ]);	
                    return CRUDBooster::redirect(CRUDBooster::mainpath(),"Asset Code Exceed in Asset Lists! ".$food_preparation_equipmen->class_description." : ".($key+2),"danger");
                }

            }else if($sub_cat_code == 8){
                $asset_code                = $faucet_and_plumbing->from_code + $DatabaseCounterFAP;
                $DatabaseCounterFAP++; 
                $request_type_id_inventory = 5;
                $item_category             = "FIXED ASSET";
                $sub_cat_id                = $sub_cat_code;

                if($asset_code > $faucet_and_plumbing->to_code){
                    DB::table('class')->where('id',$faucet_and_plumbing->id)
                    ->update([
                        'limit_code'   => "Code exceed in Asset Lists",
                    ]);	
                    return CRUDBooster::redirect(CRUDBooster::mainpath(),"Asset Code Exceed in Asset Lists! ".$faucet_and_plumbing->class_description." : ".($key+2),"danger");
                }

            }else if($sub_cat_code == 9){
                $asset_code                = $food_holding_warming_equip->from_code + $DatabaseCounterFHWE;
                $DatabaseCounterFHWE++; 
                $request_type_id_inventory = 5;
                $item_category             = "FIXED ASSET";
                $sub_cat_id                = $sub_cat_code;

                if($asset_code > $food_holding_warming_equip->to_code){
                    DB::table('class')->where('id',$food_holding_warming_equip->id)
                    ->update([
                        'limit_code'   => "Code exceed in Asset Lists",
                    ]);	
                    return CRUDBooster::redirect(CRUDBooster::mainpath(),"Asset Code Exceed in Asset Lists! ".$food_holding_warming_equip->class_description." : ".($key+2),"danger");
                }

            }else if($sub_cat_code == 10){
                $asset_code                = $other_restaurant_equipment->from_code + $DatabaseCounterORE;
                $DatabaseCounterORE++; 
                $request_type_id_inventory = 5;
                $item_category             = "FIXED ASSET";
                $sub_cat_id                = $sub_cat_code;

                if($asset_code > $other_restaurant_equipment->to_code){
                    DB::table('class')->where('id',$other_restaurant_equipment->id)
                    ->update([
                        'limit_code'   => "Code exceed in Asset Lists",
                    ]);	
                    return CRUDBooster::redirect(CRUDBooster::mainpath(),"Asset Code Exceed in Asset Lists! ".$other_restaurant_equipment->class_description." : ".($key+2),"danger");
                }

            }else if($sub_cat_code == 11){
                $asset_code                = $other_vehicle->from_code + $DatabaseCounterOV;
                $DatabaseCounterOV++; 
                $request_type_id_inventory = 5;
                $item_category             = "FIXED ASSET";
                $sub_cat_id                = $sub_cat_code;

                if($asset_code > $other_vehicle->to_code){
                    DB::table('class')->where('id',$other_vehicle->id)
                    ->update([
                        'limit_code'   => "Code exceed in Asset Lists",
                    ]);	
                    return CRUDBooster::redirect(CRUDBooster::mainpath(),"Asset Code Exceed in Asset Lists! ".$other_vehicle->class_description." : ".($key+2),"danger");
                }

            }else if($sub_cat_code == 12){
                $asset_code                = $other_fixed_asset->from_code + $DatabaseCounterOFA;
                $DatabaseCounterOFA++; 
                $request_type_id_inventory = 5;
                $item_category             = "FIXED ASSET";
                $sub_cat_id                = $sub_cat_code;

                if($asset_code > $other_fixed_asset->to_code){
                    DB::table('class')->where('id',$other_fixed_asset->id)
                    ->update([
                        'limit_code'   => "Code exceed in Asset Lists",
                    ]);	
                    return CRUDBooster::redirect(CRUDBooster::mainpath(),"Asset Code Exceed in Asset Lists! ".$other_fixed_asset->class_description." : ".($key+2),"danger");
                }

            }else if($sub_cat_code == 13){
                $asset_code                = $communication_equipment->from_code + $DatabaseCounterCOMME;
                $DatabaseCounterCOMME++; 
                $request_type_id_inventory = 1;
                $item_category             = "IT ASSETS";
                $sub_cat_id                = $sub_cat_code;

                if($asset_code > $communication_equipment->to_code){
                    DB::table('class')->where('id',$communication_equipment->id)
                    ->update([
                        'limit_code'   => "Code exceed in Asset Lists",
                    ]);	
                    return CRUDBooster::redirect(CRUDBooster::mainpath(),"Asset Code Exceed in Asset Lists! ".$communication_equipment->class_description." : ".($key+2),"danger");
                }

            }else if($sub_cat_code == 14){
                $asset_code                = $furnitures_fixtures->from_code + $DatabaseCounterFF;
                $DatabaseCounterFF++; 
                $request_type_id_inventory = 5;
                $item_category             = "FIXED ASSET";
                $sub_cat_id                = $sub_cat_code;

                if($asset_code > $furnitures_fixtures->to_code){
                    DB::table('class')->where('id',$furnitures_fixtures->id)
                    ->update([
                        'limit_code'   => "Code exceed in Asset Lists",
                    ]);	
                    return CRUDBooster::redirect(CRUDBooster::mainpath(),"Asset Code Exceed in Asset Lists! ".$furnitures_fixtures->class_description." : ".($key+2),"danger");
                }

            }else if($sub_cat_code == 15){
                $asset_code                = $facilities_equipment->from_code + $DatabaseCounterFE;
                $DatabaseCounterFE++; 
                $request_type_id_inventory = 5;
                $item_category             = "FIXED ASSET";
                $sub_cat_id                = $sub_cat_code;

                if($asset_code > $facilities_equipment->to_code){
                    DB::table('class')->where('id',$facilities_equipment->id)
                    ->update([
                        'limit_code'   => "Code exceed in Asset Lists",
                    ]);	
                    return CRUDBooster::redirect(CRUDBooster::mainpath(),"Asset Code Exceed in Asset Lists! ".$facilities_equipment->class_description." : ".($key+2),"danger");
                }

            }else if($sub_cat_code == 16){
                $asset_code                = $leasehold_equipment->from_code + $DatabaseCounterLE;
                $DatabaseCounterLE++; 
                $request_type_id_inventory = 5;
                $item_category             = "FIXED ASSET";
                $sub_cat_id                = $sub_cat_code;

                if($asset_code > $leasehold_equipment->to_code){
                    DB::table('class')->where('id',$leasehold_equipment->id)
                    ->update([
                        'limit_code'   => "Code exceed in Asset Lists",
                    ]);	
                    return CRUDBooster::redirect(CRUDBooster::mainpath(),"Asset Code Exceed in Asset Lists! ".$leasehold_equipment->class_description." : ".($key+2),"danger");
                }

            }else if($sub_cat_code == 17){
                $asset_code                = $machinery_equipmen->from_code + $DatabaseCounterME;
                $DatabaseCounterME++; 
                $request_type_id_inventory = 5;
                $item_category             = "FIXED ASSET";
                $sub_cat_id                = $sub_cat_code;

                if($asset_code > $machinery_equipmen->to_code){
                    DB::table('class')->where('id',$machinery_equipmen->id)
                    ->update([
                        'limit_code'   => "Code exceed in Asset Lists",
                    ]);	
                    return CRUDBooster::redirect(CRUDBooster::mainpath(),"Asset Code Exceed in Asset Lists! ".$machinery_equipmen->class_description." : ".($key+2),"danger");
                }

            }else if($sub_cat_code == 18){
                $asset_code                = $vehicle->from_code + $DatabaseCounterV;
                $DatabaseCounterV++; 
                $request_type_id_inventory = 5;
                $item_category             = "FIXED ASSET";
                $sub_cat_id                = $sub_cat_code;

                if($asset_code > $vehicle->to_code){
                    DB::table('class')->where('id',$vehicle->id)
                    ->update([
                        'limit_code'   => "Code exceed in Asset Lists",
                    ]);	
                    return CRUDBooster::redirect(CRUDBooster::mainpath(),"Asset Code Exceed in Asset Lists! ".$vehicle->class_description." : ".($key+2),"danger");
                }

            }else if($sub_cat_code == 19){
                $asset_code                = $computer_software_program->from_code + $DatabaseCounterCSP;
                $DatabaseCounterCSP++; 
                $request_type_id_inventory = 1;
                $item_category             = "IT ASSETS";
                $sub_cat_id                = $sub_cat_code;

                if($asset_code > $computer_software_program->to_code){
                    DB::table('class')->where('id',$computer_software_program->id)
                    ->update([
                        'limit_code'   => "Code exceed in Asset Lists",
                    ]);	
                    return CRUDBooster::redirect(CRUDBooster::mainpath(),"Asset Code Exceed in Asset Lists! ".$computer_software_program->class_description." : ".($key+2),"danger");
                }

            }

            if(!empty($row['serial_number'])){
                $serial_no = $row['serial_number'];
            }else{
                $serial_no = "N/A";
            }
         
           
            $latestRequest = DB::table('assets_inventory_body')->select('id')->orderBy('id','DESC')->first();
			$latestRequestId = $latestRequest->id != NULL ? $latestRequest->id : 0;
            AssetsInventoryBody::create([
                'header_id'                      => NULL,
                'item_id'                        => $item_id->id,
                'statuses_id'                    => $statuses,
                'deployed_to'                    => $deployed,
                'location'                       => $location,
                'asset_code'                     => $asset_code,
                'digits_code'                    => $row['digits_code'],
                'item_description'               => $item_id->item_description,
                'value'                          => $item_id->item_cost,
                'quantity'                       => $quantity,
                'serial_no'                      => $serial_no,
                'warranty_coverage'              => $row['warranty_coverage'],
                'item_condition'                 => $item_condition,
                'created_by'		             => CRUDBooster::myId(),
                'deployed_to_id'                 => $name_id,
                'request_type_id_inventory'      => $request_type_id_inventory,
                'item_category'                  => $item_category,
                'sub_category_id'                => $sub_cat_id,
                'received'                       => 1,
            ]); 

            $deployed_id = DB::table('assets_inventory_body')->where('id','>', $latestRequestId)->where('statuses_id',3)->get();
          
            $finalinventory_id =  [];
            $finalcreatedBy =  [];
            $request_type_id =  [];
            $digits_code = [];
            $asset_code = [];
            $item_description = [];
            $category = [];
            $serial_no = [];
            $item_id = [];
            $unit_cost = [];
			foreach($deployed_id as $invData){
				array_push($finalinventory_id, $invData->id);
                array_push($finalcreatedBy, $invData->deployed_to_id);
                array_push($request_type_id, $invData->request_type_id_inventory);
                array_push($digits_code, $invData->digits_code);
                array_push($asset_code, $invData->asset_code);
                array_push($item_description, $invData->item_description);
                array_push($category, $invData->item_category);
                array_push($serial_no, $invData->serial_no);
                array_push($item_id, $invData->item_id);
                array_push($unit_cost, $invData->value);
			}
            for($x=0; $x < count($finalinventory_id); $x++) {
                MoveOrder::create([
                    'status_id'           => 13,
                    'inventory_id'        => $finalinventory_id[$x],
                    'item_id'             => $item_id[$x],
                    'request_created_by'  => $finalcreatedBy[$x],
                    'request_type_id_mo'  => $request_type_id[$x],
                    'digits_code'         => $digits_code[$x],
                    'asset_code'          => $asset_code[$x],
                    'item_description'    => $item_description[$x],
                    'category_id'         => $category[$x],
                    'serial_no'           => $serial_no[$x],
                    'quantity'            => 1,
                    'unit_cost'           => $unit_cost[$x],
                ]);
            }
  
        }
    }

    public function prepareForValidation($data, $index)
    {
        //DIGITS CODE
        $data['digits_code_exist']['check'] = false;
        $checkRowDbCode = DB::table('assets')->select("digits_code AS digits_code")->get()->toArray();
        $checkRowDbCodeColumn = array_column($checkRowDbCode, 'digits_code');
        $data['digits_code_exist']['code'] = $data['digits_code'];
        if(!empty($data['digits_code'])){
            if(in_array(preg_replace('/\s+/', '', $data['digits_code']), $checkRowDbCodeColumn)){
                $data['digits_code_exist']['check'] = true;
            }
        }else{
            $data['digits_code_exist']['check'] = true;
        }

        //EMPLOYEE 
        $data['employee_exist']['check'] = false;
        $checkRowDb = DB::table('cms_users')->select(DB::raw("LOWER(TRIM(email)) AS emails"))->get()->toArray();
        $checkRowDbColumn = array_column($checkRowDb, 'emails');
    
        if(!empty($data['email'])){
            if(in_array(strtolower(trim($data['email'])), $checkRowDbColumn)){
                $data['employee_exist']['check'] = true;
            }
        }else{
            $data['employee_exist']['check'] = true;
        }

        // SUB CAT CODE
        $data['sub_cat_code_exist']['check'] = false;
        $checkRowDbSubCat = DB::table('class')->select(DB::raw("LOWER(TRIM(class_description)) AS class_description"))->get()->toArray();
        $checkRowDbSubCatColumn = array_column($checkRowDbSubCat, 'class_description');
    
        if(!empty($data['sub_category_code'])){
            if(in_array(strtolower(trim($data['sub_category_code'])), $checkRowDbSubCatColumn)){
                $data['sub_cat_code_exist']['check'] = true;
            }
        }else{
            $data['sub_cat_code_exist']['check'] = true;
        }

        //DIGITS CODE AND SERIAL NO
        $data['digits_code_serial_exist']['check'] = false;
        $checkRowDbDigitsCode = DB::table('assets_inventory_body')->select(DB::raw("CONCAT(assets_inventory_body.digits_code,'-',LOWER(assets_inventory_body.serial_no)) AS codes"))->where('serial_no','!=','N/A')->get()->toArray();
        
        $checkRowDbColumnDigitsCode = array_column($checkRowDbDigitsCode, 'codes');
        if(!empty($data['serial_number'])){
            if(in_array($data['digits_code']."-".strtolower($data['serial_number']), $checkRowDbColumnDigitsCode)){
                $data['digits_code_serial_exist']['check'] = true;
            }else{
                $data['digits_code_serial_exist']['check'] = false;
            }
        }

        return $data;
    }

    public function rules(): array
    {
       
        return [
            '*.employee_exist' => function($attribute, $value, $onFailure) {
                if ($value['check'] === false) {
                    $onFailure('Employee Email not exist in Users List!');
                }
            },
            '*.sub_cat_code_exist' => function($attribute, $value, $onFailure) {
                if ($value['check'] === false) {
                    $onFailure('Sub Category Code not exist in Sub Master Asset Code!');
                }
            },
            '*.digits_code_serial_exist' => function($attribute, $value, $onFailure) {
                if ($value['check'] === true) {
                    $onFailure('Digits Code and Serial No Exist!');
                }
            },
            '*.digits_code_exist' => function($attribute, $value, $onFailure) {
                if ($value['check'] === false) {
                    $onFailure('Digits Code '.$value['code'].' not exist in Item Master');
                }
            },
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.digits_code_exist.in' => 'Digits Code :input not exist in Item Master.',
            '*.digits_code.required' => 'Digits Code Required!',
            '*.employee_name.employee_exist' => 'Employee Name :input not exist in Users list.',
        ];
    }
}
