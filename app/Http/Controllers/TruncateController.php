<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use CRUDBooster;

class TruncateController extends \crocodicstudio\crudbooster\controllers\CBController
{
    public function dbtruncate(){
        if(!CRUDBooster::isSuperadmin()) {    
            CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"),'danger');
        }
        DB::table('header_request')->truncate();
        DB::table('body_request')->truncate();
        DB::table('mo_body_request')->truncate();
        return "Truncated Successfully";
    }
}
