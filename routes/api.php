<?php

use Illuminate\Http\Request;
use App\Http\Controllers\AdminAssetsInventoryBodyController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('/admin/reports/request-reports', 'AdminReportsController@getReports')->name('api.reports.index');
Route::get('/admin/reports/request-reports-search', 'AdminReportsController@getGeneratedReports')->name('api.searched.reports');

//Route::get('/admin/reports/search-approved', 'AdminReportsController@searchApplicant')->name('api.reports.approved');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
    Route::apiResources([
     'inventory' => AdminAssetsInventoryBodyController::class, 'getInventory'
    ]);

    
});
