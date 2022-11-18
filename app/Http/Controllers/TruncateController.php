<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class TruncateController extends Controller
{
    public function dbtruncate()
    {
        DB::table('header_request')->truncate();
        DB::table('body_request')->truncate();
        DB::table('mo_body_request')->truncate();

        return "Truncated Successfully";
    }
}
