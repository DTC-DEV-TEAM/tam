<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('admin/login');
    //return view('welcome');
});


Route::get('/admin/receiving_asset/getADFStatus/{id}','AdminReceivingAssetController@getADFStatus')->name('ADF.API');
//Route::get('/admin/items/item-updated','AdminItemsController@getItemsUpdatedAPI')->name('itemsupdate.API');
    
Route::group(['middleware' => ['web']], function() {

    Route::get('admin/employees/getCity/{id}','AdminEmployeesController@getCity')->name('getCity');
    Route::get('admin/employees/getState/{id}','AdminEmployeesController@getState')->name('getState');
    Route::get('admin/employees/getCountry/{id}','AdminEmployeesController@getCountry')->name('getCountry');

    Route::get('admin/employees/getPosition/{id}','AdminEmployeesController@getPosition')->name('getPosition');

    Route::get('admin/employees/getEmployee/{id}','AdminEmployeesController@getEmployee')->name('getEmployee');

    Route::get('admin/assets/item/{id}','AdminAssetsController@Items');

    //ApprovalMatrix
    Route::get(config('crudbooster.ADMIN_PATH').'/approval_matrix/add-matrix', 'AdminApprovalMatrixController@getAddMatrix')->name('assets.add.matrix');

    //AssetsInventory
    Route::post(config('crudbooster.ADMIN_PATH').'/assets_inventory/add-inventory', 'AdminAssetsInventoryHeaderController@getAddInventory')->name('assets.add.inventory'); 
    Route::post(config('crudbooster.ADMIN_PATH').'/assets_inventory/export-assets-body', 'AdminAssetsInventoryHeaderController@ExportAssetsBody')->name('assets.export.assets.body'); 
    Route::post(config('crudbooster.ADMIN_PATH').'/assets_inventory/export-assets-header', 'AdminAssetsInventoryHeaderController@ExportAssetsHeader')->name('assets.export.assets.header'); 
    Route::post(config('crudbooster.ADMIN_PATH').'/assets_inventory/check-row','AdminAssetsInventoryHeaderController@checkRow')->name('assets.check.row');
    Route::post(config('crudbooster.ADMIN_PATH').'/assets_inventory/get-approvedProcess','AdminAssetsInventoryHeaderForApprovalController@getapprovedProcess')->name('assets.get.approvedProcess');
    Route::post(config('crudbooster.ADMIN_PATH').'/assets_inventory/get-rejectedProcess','AdminAssetsInventoryHeaderForApprovalController@getrejectedProcess')->name('assets.get.rejectedProcess');
    
    Route::post(config('crudbooster.ADMIN_PATH').'/assets_inventory/get-history','AdminHeaderRequestController@getHistory')->name('assets.get.history');
    Route::post(config('crudbooster.ADMIN_PATH').'/assets_inventory/get-comments','AdminHeaderRequestController@getComments')->name('assets.get.comments');
    Route::post(config('crudbooster.ADMIN_PATH').'/assets_inventory/get-checkData','AdminHeaderRequestController@getcheckData')->name('assets.get.checkData');
    Route::post(config('crudbooster.ADMIN_PATH').'/assets_inventory/get-histories','AdminHeaderRequestController@getHistories')->name('assets.get.histories');
    Route::post(config('crudbooster.ADMIN_PATH').'/assets_inventory/get-assetDescription','AdminHeaderRequestController@getassetDescription')->name('assets.get.assetDescription');
    Route::post(config('crudbooster.ADMIN_PATH').'/header_request/export-assets-header','AdminHeaderRequestController@ExportAssetsHeader')->name('asset.export.assets.header');

    
    //CreateRequest
    Route::get(config('crudbooster.ADMIN_PATH').'/header_request/add-requisition', 'AdminHeaderRequestController@getAddRequisition')->name('assets.add.requisition'); 
    Route::post(config('crudbooster.ADMIN_PATH').'/header_request/item-search','AdminHeaderRequestController@itemSearch')->name('asset.item.search');
    Route::post(config('crudbooster.ADMIN_PATH').'/header_request/sub-categories','AdminHeaderRequestController@SubCategories')->name('asset.sub.categories');
    Route::post('/employees','AdminHeaderRequestController@Employees');
    Route::post('/companies','AdminHeaderRequestController@Companies');
    Route::get(config('crudbooster.ADMIN_PATH').'/header_request/add-requisition-fa', 'AdminHeaderRequestController@getAddRequisitionFA')->name('assets.add.requisition.fa'); 
    Route::get('admin/header_request/subcategories/{id}','AdminHeaderRequestController@SubCategories');
    Route::get('admin/header_request/RemoveItem','AdminHeaderRequestController@RemoveItem');


    Route::get('admin/header_request/getRequestCancel/{id}','AdminHeaderRequestController@getRequestCancel')->name('getRequestCancel');
    Route::get('admin/header_request/getRequestReceive/{id}','AdminHeaderRequestController@getRequestReceive')->name('getRequestReceive');
    //ApproveRequest
    Route::get('/admin/approval/getRequestApproval/{id}','AdminApprovalController@getRequestApproval')->name('approval-request');
    //RecommendationRequest
    Route::get('/admin/recommendation/getRequestReco/{id}','AdminRecommendationController@getRequestReco')->name('reco-request');
    Route::post(config('crudbooster.ADMIN_PATH').'/recommendation/item-search','AdminRecommendationController@itemSearch')->name('it.item.search');
    
    //PurchasingRequest
    Route::get('/admin/for_purchasing/getRequestPurchasing/{id}','AdminForPurchasingController@getRequestPurchasing')->name('purchasing-request');
    Route::get('/admin/for_purchasing/getRequestPrint/{id}','AdminForPurchasingController@getRequestPrint')->name('print-request');
    Route::get('/admin/for_purchasing/getRequestClose/{id}','AdminForPurchasingController@getRequestClose')->name('purchasing-request');
    
    //Route::get('/admin/for_purchasing/getRequestPrintPickList/{id}','AdminForPurchasingController@getRequestPrintPickList')->name('print-picklist');
    Route::get('admin/for_purchasing/ARFUpdate','AdminForPurchasingController@ARFUpdate');
    Route::get('admin/for_purchasing/PickListUpdate','AdminForPurchasingController@PickListUpdate');
    Route::post(config('crudbooster.ADMIN_PATH').'/for_purchasing/item-search','AdminForPurchasingController@itemSearch')->name('asset.item.tagging');
    Route::get('/admin/for_purchasing/getDetailPurchasing/{id}','AdminForPurchasingController@getDetailPurchasing')->name('purchasing-detail');
    
    //MORequest
    Route::get('/admin/move_order/getRequestOrdering/{id}','AdminMoveOrderController@getRequestOrdering')->name('ordering-request');
    Route::get('/admin/move_order/getRequestPrintPickList/{id}','AdminMoveOrderController@getRequestPrintPickList')->name('print-picklist');
    Route::get('/admin/move_order/getRequestPrintADF/{id}','AdminMoveOrderController@getRequestPrintADF')->name('print-request');
    Route::get('/admin/move_order/getDetailOrdering/{id}','AdminMoveOrderController@getDetailOrdering')->name('ordering-detail');
    Route::get('admin/move_order/PickListUpdate','AdminMoveOrderController@PickListUpdate');
    Route::get(config('crudbooster.ADMIN_PATH').'/move_order/add-mo', 'AdminMoveOrderController@getAddMO')->name('assets.add.mo'); 
    Route::post(config('crudbooster.ADMIN_PATH').'/selectedHeader','AdminMoveOrderController@selectedHeader')->name('order.selected.header');
    Route::get('admin/move_order/ADFUpdate','AdminMoveOrderController@ADFUpdate');

    Route::get('/admin/move_order/GetExtractMO','AdminMoveOrderController@GetExtractMO')->name('GetExtractMO'); 
    
    //PickingRequest
    Route::get('/admin/picking/getRequestPicking/{id}','AdminPickingController@getRequestPicking')->name('picking-request');
    //ReceivingRequest
    Route::get('/admin/receiving_asset/getRequestReceiving/{id}','AdminReceivingAssetController@getRequestReceiving')->name('receiving-request');
    //ClosingRequest
    Route::get('/admin/receiving_asset/getRequestReceiving/{id}','AdminReceivingAssetController@getRequestReceiving')->name('receiving-request');
    //HistoryRequest
    Route::get('/admin/closing/getRequestClosing/{id}','AdminClosingController@getRequestClosing')->name('closing-request');
    //Employees
    Route::get('/admin/employees/bulk-upload-employees','AdminEmployeesController@UploadEmployeeTemplate');
    Route::get('/admin/employees/download-template-employee','AdminEmployeesController@DownloadEmployeeTemplate');
    Route::post('/admin/employees/upload-employees','AdminEmployeesController@BulkEmployeesUpload')->name('upload-employees');

    //users
    Route::get('/admin/users/user-account-upload','AdminCmsUsersController@UploadUserAccount');
    Route::post('/admin/users/upload-users','AdminCmsUsersController@userAccountUpload')->name('upload-users');
    Route::get('/admin/users/upload-user-account-template','AdminCmsUsersController@uploadUserAccountTemplate');

    //truncate table
    Route::get('/admin/db-truncate','TruncateController@dbtruncate');

    //locations import
    Route::get('/admin/locations/store-location-upload','AdminLocationsController@UploadLocationsView');
    Route::post('/admin/locations/upload-locations','AdminLocationsController@locationsUpload')->name('upload-locations');
    Route::get('/admin/locations/upload-locations-template','AdminLocationsController@uploadLocationsTemplate');

    //Departments import
    Route::get('/admin/departments/departments-upload','AdminDepartmentsController@UploadDepartmentsView');
    Route::post('/admin/departments/upload-departments','AdminDepartmentsController@departmentsUpload')->name('upload-departments');
    Route::get('/admin/departments/upload-departments-template','AdminDepartmentsController@uploadDepartmentsTemplate');
    
    //Asset Return 
    Route::get(config('crudbooster.ADMIN_PATH').'/return_transfer_assets/return-assets', 'AdminReturnTransferAssetsHeaderController@getReturnAssets')->name('assets.return.assets'); 
    Route::post(config('crudbooster.ADMIN_PATH').'/selectedReturnHeader','AdminReturnTransferAssetsHeaderController@selectedReturnHeader')->name('order.selected.header');
    Route::post(config('crudbooster.ADMIN_PATH').'/return_assets/save-return-assets','AdminReturnTransferAssetsHeaderController@saveReturnAssets')->name('assets.save.return.assets');
    Route::get('admin/return_transfer_assets_header/getRequestCancelReturn/{id}','AdminReturnTransferAssetsHeaderController@getRequestCancelReturn')->name('getRequestCancelReturn');
    Route::get('/admin/return_approval/getRequestApprovalReturn/{id}','AdminReturnApprovalController@getRequestApprovalReturn')->name('approval-request-return');
    Route::get('/admin/return_close/getRequestClosingReturn/{id}','AdminReturnCloseController@getRequestClosingReturn')->name('return-closing-request');
    Route::get('/admin/return_picking/getRequestPickingReturn/{id}','AdminReturnPickingController@getRequestPickingReturn')->name('return-picking-request');
    Route::get('/admin/return_transfer_assets_header/getRequestPrintTF/{id}','AdminReturnTransferAssetsHeaderController@getRequestPrintTF')->name('print-request-tf');
    
    //Transfer Assets
    Route::get(config('crudbooster.ADMIN_PATH').'/return_transfer_assets/transfer-assets', 'AdminReturnTransferAssetsHeaderController@getTransferAssets')->name('assets.transfer.assets'); 
    Route::post(config('crudbooster.ADMIN_PATH').'/transfer_assets/save-transfer-assets','AdminReturnTransferAssetsHeaderController@saveTransferAssets')->name('assets.save.transfer.assets');
    Route::get('/admin/pick_transfer_assets/getRequestPickingTransfer/{id}','AdminPickTransferAssetsController@getRequestPickingTransfer')->name('transfer-picking-request');

    //inventory upload
    Route::get('/admin/assets_inventory_body/inventory-upload','AdminAssetsInventoryBodyController@uploadInventory');
    Route::post('/admin/assets_inventory_body/upload-inventory','AdminAssetsInventoryBodyController@inventoryUpload')->name('upload-inventory');
    
    Route::get('/admin/clear-view', function() {
        Artisan::call('view:clear');
        return "View cache is cleared!";
    });
});

