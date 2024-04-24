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
            
           
            $coa = DB::table('class')->find($sub_cat_code);
            $asset_code = $coa->code_counter;
            
            if(in_array($sub_cat_code,[13,19])){
                $request_type_id_inventory = 1;
                $item_category             = "IT ASSETS";
            }else{
                $request_type_id_inventory = 5;
                $item_category             = "FIXED ASSET";
            }
            $sub_cat_id                    = $sub_cat_code;

            if($asset_code > $coa->to_code){
                AssetCodeModel::where('id',$coa->id)
                ->update([
                    'limit_code'   => "Code exceed in Asset Lists"
                ]);	
                return CRUDBooster::redirect(CRUDBooster::mainpath(),"Asset Code Exceed in Asset Lists! ".$coa->class_description . $coa->id ." : ".($key+2),"danger");
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
                'received'                       => 1
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

            DB::table('class')->where('id',$coa->id)->increment('code_counter');
  
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
