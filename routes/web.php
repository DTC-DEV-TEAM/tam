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
    Route::post(config('crudbooster.ADMIN_PATH').'/assets_inventory/get-closeProcess','AdminAssetsInventoryHeaderForApprovalController@getCloseProcess')->name('assets.get.closeProcess');
    Route::post(config('crudbooster.ADMIN_PATH').'/assets_inventory/check-digits-code','AdminAssetsInventoryHeaderForApprovalController@checkDigitsCode')->name('check-reserve-digits-code');//new 72723
    Route::post(config('crudbooster.ADMIN_PATH').'/assets_inventory/get-forPoProcess','AdminAssetsInventoryHeaderForApprovalController@forPoProcess')->name('assets.get.forPoProcess');
    Route::post(config('crudbooster.ADMIN_PATH').'/assets_inventory/get-forReceivingProcess','AdminAssetsInventoryHeaderForApprovalController@forReceivingProcess')->name('assets.get.forReceivingProcess');

    Route::post(config('crudbooster.ADMIN_PATH').'/assets_inventory/get-history','AdminHeaderRequestController@getHistory')->name('assets.get.history');
    Route::post(config('crudbooster.ADMIN_PATH').'/assets_inventory/get-comments','AdminHeaderRequestController@getComments')->name('assets.get.comments');
    Route::post(config('crudbooster.ADMIN_PATH').'/assets_inventory/get-checkData','AdminHeaderRequestController@getcheckData')->name('assets.get.checkData');
    Route::post(config('crudbooster.ADMIN_PATH').'/assets_inventory/get-histories','AdminHeaderRequestController@getHistories')->name('assets.get.histories');
    Route::post(config('crudbooster.ADMIN_PATH').'/assets_inventory/get-assetDescription','AdminHeaderRequestController@getassetDescription')->name('assets.get.assetDescription');
    Route::post(config('crudbooster.ADMIN_PATH').'/header_request/export-assets-header','AdminHeaderRequestController@ExportAssetsHeader')->name('asset.export.assets.header');
    Route::post(config('crudbooster.ADMIN_PATH').'/assets_inventory/searchAssets','AdminAssetsInventoryHeaderForApprovalController@assetSearch')->name('search-assets');
    Route::post(config('crudbooster.ADMIN_PATH').'/assets_inventory/search-digits-code','AdminAssetsInventoryBodyController@digitsCodeSearch')->name('search-digits-code');
    Route::post(config('crudbooster.ADMIN_PATH').'/assets_inventory/selection-digits-code','AdminAssetsInventoryHeaderForApprovalController@selectionDigitsCode')->name('selection-digits-code'); // new 72723
    Route::post(config('crudbooster.ADMIN_PATH').'/assets_inventory/sub-categories-code','AdminAssetsInventoryHeaderForApprovalController@subCatCode')->name('sub-categories-code'); // new 72723

    //CreateRequest
    Route::get(config('crudbooster.ADMIN_PATH').'/header_request/add-requisition', 'AdminHeaderRequestController@getAddRequisition')->name('assets.add.requisition'); 
    Route::post(config('crudbooster.ADMIN_PATH').'/header_request/item-search','AdminHeaderRequestController@itemSearch')->name('asset.item.search');
    Route::post(config('crudbooster.ADMIN_PATH').'/header_request/sub-categories','AdminHeaderRequestController@SubCategories')->name('asset.sub.categories');
    Route::post('/employees','AdminHeaderRequestController@Employees');
    Route::post('/companies','AdminHeaderRequestController@Companies');
    Route::get(config('crudbooster.ADMIN_PATH').'/header_request/add-requisition-fa', 'AdminHeaderRequestController@getAddRequisitionFA')->name('assets.add.requisition.fa'); 
    Route::get('admin/header_request/subcategories/{id}','AdminHeaderRequestController@SubCategories');
    Route::get('admin/header_request/RemoveItem','AdminHeaderRequestController@RemoveItem');
    //Cancel Request
    Route::post(config('crudbooster.ADMIN_PATH').'/header_request/cancel-arf-request','AdminHeaderRequestController@cancelArfRequest')->name('cancel-arf-request');
    Route::post(config('crudbooster.ADMIN_PATH').'/header_request/cancel-arf-mo-perline-request','AdminHeaderRequestController@cancelArfMoPerLineRequest')->name('cancel-arf-mo-perline-request');


    Route::get('admin/header_request/getRequestCancel/{id}','AdminHeaderRequestController@getRequestCancel')->name('getRequestCancel');
    Route::get('admin/header_request/getRequestReceive/{id}','AdminHeaderRequestController@getRequestReceive')->name('getRequestReceive');
    //ApproveRequest
    Route::get('/admin/approval/getRequestApproval/{id}','AdminApprovalController@getRequestApproval')->name('approval-request');
    Route::get('/admin/approval/getRequestApprovalSupplies/{id}','AdminApprovalController@getRequestApprovalSupplies')->name('approval-request-supplies');
    //RecommendationRequest
    Route::get('/admin/recommendation/getRequestReco/{id}','AdminRecommendationController@getRequestReco')->name('reco-request');
    Route::post(config('crudbooster.ADMIN_PATH').'/recommendation/item-search','AdminRecommendationController@itemSearch')->name('it.item.search');
    
    //PurchasingRequest
    Route::get('/admin/for_purchasing/getRequestPurchasing/{id}','AdminForPurchasingController@getRequestPurchasing')->name('purchasing-request');
    Route::get('/admin/for_purchasing/getRequestPrint/{id}','AdminForPurchasingController@getRequestPrint')->name('print-request');
    Route::post(config('crudbooster.ADMIN_PATH').'/getRequestClose','AdminForPurchasingController@getRequestClose')->name('purchasing-request-close');
     Route::get('/admin/for_purchasing/getRequestPurchasingForMoSo/{id}','AdminForPurchasingController@getRequestPurchasingForMoSo')->name('purchasing-request-per-line-close');
    Route::get('/admin/for_purchasing/getRequestClose/{id}','AdminForPurchasingController@getRequestClose')->name('purchasing-request');
    
    //Route::get('/admin/for_purchasing/getRequestPrintPickList/{id}','AdminForPurchasingController@getRequestPrintPickList')->name('print-picklist');
    Route::get('admin/for_purchasing/ARFUpdate','AdminForPurchasingController@ARFUpdate');
    Route::get('admin/for_purchasing/PickListUpdate','AdminForPurchasingController@PickListUpdate');
    Route::post(config('crudbooster.ADMIN_PATH').'/for_purchasing/item-search','AdminForPurchasingController@itemSearch')->name('asset.item.tagging');
    Route::get('/admin/for_purchasing/getDetailPurchasing/{id}','AdminForPurchasingController@getDetailPurchasing')->name('purchasing-detail');
    Route::post(config('crudbooster.ADMIN_PATH').'/for_purchasing/item-search-supplies-marketing','AdminForPurchasingController@itemSearchSuppliesMarketing')->name('asset.item.supplies.marketing.tagging');
    
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
    Route::post(config('crudbooster.ADMIN_PATH').'/get-available-digits-code','AdminMoveOrderController@getAvailableDigitsCode')->name('get-available-digits-code');
    
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
   
    //Assets
    Route::get('/admin/assets/item-master-upload','AdminAssetsController@UploadItemMaster');
    Route::post('/admin/assets/upload-item-master','AdminAssetsController@itemMasterUpload')->name('upload-item-master');
    Route::get('/admin/db-truncate','TruncateController@dbtruncate');
    
    //TIMFS API
    Route::post(config('crudbooster.ADMIN_PATH').'/get-item-master-timfs-data','AdminAssetsController@getItemMasterTimfsData')->name('get-item-master-timfs-data');
    Route::post(config('crudbooster.ADMIN_PATH').'/get-item-master-updated-timfs-data','AdminAssetsController@getItemMasterUpdatedTimfsData')->name('get-item-master-updated-timfs-data');

    //DAM API
    Route::post(config('crudbooster.ADMIN_PATH').'/get-item-master-data-dam','AdminAssetsItController@getItemMasterDataDamApi')->name('get-item-master-data');
    Route::post(config('crudbooster.ADMIN_PATH').'/get-item-master-updated-data-dam','AdminAssetsItController@getItemMasterUpdatedDataDamApi')->name('get-item-master-updated-data');
    
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
     Route::get('/admin/assets_inventory_body/upload-inventory-template','AdminAssetsInventoryBodyController@uploadInventoryTemplate'); 
     
     //inventory upload Not Available
     Route::get('/admin/assets_inventory_body/upload-inventory-not-available','AdminAssetsInventoryBodyController@uploadInventoryNotAvailable');
     Route::post('/admin/assets_inventory_body/inventory-upload-not-available','AdminAssetsInventoryBodyController@inventoryUploadNotAvailable')->name('upload-inventory-not-available');
 
     //inventory update
     Route::get('/admin/assets_inventory_body/upload-inventory-update','AdminAssetsInventoryBodyController@uploadInventoryUpdate');
     Route::post('/admin/assets_inventory_body/inventory-upload-update','AdminAssetsInventoryBodyController@inventoryUploadUpdate')->name('upload-inventory-update');
     Route::get('/admin/assets_inventory_body/update-digits-code-template','AdminAssetsInventoryBodyController@updateDigitsCodeTemplate');
 
     //Deployed Assets
     Route::get('/admin/deployed_asset/Detail/{id}','AdminDeployedAssetsController@Detail')->name('deployed-asset');
     Route::get('/admin/deployed_asset/DetailMoOnly/{id}','AdminDeployedAssetsController@DetailMoOnly')->name('deployed-asset');
     
     //hr requisition for new employee
     Route::post(config('crudbooster.ADMIN_PATH').'/hr_requisition/search-user','AdminHrRequisitionController@SearchUser')->name('hr.search.user');
     Route::get('admin/erf_header_request/getRequestCancel/{id}','AdminHrRequisitionController@getRequestCancel')->name('getRequestCancel');
     Route::get('/admin/erf_edit_status/getEditErf/{id}','AdminErfEditStatusController@getEditErf')->name('edit-erf');
     Route::get('/admin/erf_edit_status/getErfCreateAccount/{id}','AdminErfEditStatusController@getErfCreateAccount')->name('create-account-erf');
     Route::get('/admin/erf_edit_status/getDetailErf/{id}','AdminErfEditStatusController@getDetailErf')->name('details-erf');
     Route::post('/admin/erf_edit_status/create-account','AdminErfEditStatusController@createAccount')->name('create-account');
     Route::post(config('crudbooster.ADMIN_PATH').'/erf_edit_status/get-getEmail','AdminErfEditStatusController@getEmail')->name('getEmail');
     Route::post(config('crudbooster.ADMIN_PATH').'/customers/get-checkEmail','AdminErfEditStatusController@checkEmail')->name('checkEmail');
     Route::get('/admin/erf_edit_status/getErfSetOnboardingDate/{id}','AdminErfEditStatusController@getErfSetOnboardingDate')->name('set-onboarding-erf');
     Route::post(config('crudbooster.ADMIN_PATH').'/erf_edit_status/setOnboarding','AdminErfEditStatusController@setOnboarding')->name('set-onboarding-date');
 
     //hr requisition for new employee
    Route::post(config('crudbooster.ADMIN_PATH').'/hr_requisition/search-user','AdminHrRequisitionController@SearchUser')->name('hr.search.user');
    Route::get('admin/erf_header_request/getRequestCancel/{id}','AdminHrRequisitionController@getRequestCancel')->name('getRequestCancel');
    Route::get('/admin/erf_edit_status/getEditErf/{id}','AdminErfEditStatusController@getEditErf')->name('edit-erf');
    Route::get('/admin/erf_edit_status/getErfCreateAccount/{id}','AdminErfEditStatusController@getErfCreateAccount')->name('create-account-erf');
    Route::get('/admin/erf_edit_status/getDetailErf/{id}','AdminErfEditStatusController@getDetailErf')->name('details-erf');
    Route::post('/admin/erf_edit_status/create-account','AdminErfEditStatusController@createAccount')->name('create-account');
    Route::post(config('crudbooster.ADMIN_PATH').'/erf_edit_status/get-getEmail','AdminErfEditStatusController@getEmail')->name('getEmail');
    Route::post(config('crudbooster.ADMIN_PATH').'/customers/get-checkEmail','AdminErfEditStatusController@checkEmail')->name('checkEmail');
    Route::get('/admin/erf_edit_status/getErfSetOnboardingDate/{id}','AdminErfEditStatusController@getErfSetOnboardingDate')->name('set-onboarding-erf');
    Route::post(config('crudbooster.ADMIN_PATH').'/erf_edit_status/setOnboarding','AdminErfEditStatusController@setOnboarding')->name('set-onboarding-date');
    Route::post(config('crudbooster.ADMIN_PATH').'/erf_edit_status/setUpdateOnboarding','AdminErfEditStatusController@setUpdateOnboarding')->name('set-update-onboarding-date'); 
    Route::post(config('crudbooster.ADMIN_PATH').'/erf_edit_status/lockform','AdminErfEditStatusController@lockForm')->name('locking-form'); 
    Route::post(config('crudbooster.ADMIN_PATH').'/hr_requisition/erf-item-search','AdminHrRequisitionController@itemErfITSearch')->name('item.erf.it.search');
    Route::get('/admin/erf_edit_status/getLockingForm/{id}','AdminErfEditStatusController@getLockingFormView')->name('get-locking-form'); 
    Route::post(config('crudbooster.ADMIN_PATH').'/erf_edit_status/deleteLockform','AdminErfEditStatusController@lockDeleteForm')->name('delete-locking-form'); 
    Route::post(config('crudbooster.ADMIN_PATH').'/erf_edit_status/lockformcreateaccount','AdminErfEditStatusController@lockFormCreateAccount')->name('locking-form-create-account'); 
    Route::post(config('crudbooster.ADMIN_PATH').'/erf_edit_status/deleteLockformCreateAccount','AdminErfEditStatusController@createAccountlockDelete')->name('delete-locking-create-account'); 
    Route::get('/admin/erf_edit_status/getLockingErfCreateAccountForm/{id}','AdminErfEditStatusController@getLockingErfCreateAcountFormView')->name('get-locking-erf-create-account-form'); 

    Route::post(config('crudbooster.ADMIN_PATH').'/erf_edit_status/getLockingErfSetOnboardingDate','AdminErfEditStatusController@lockFormOnboardingDate')->name('locking-form-onboarding-date'); 
    Route::post(config('crudbooster.ADMIN_PATH').'/erf_edit_status/deleteLockformOnboardingDate','AdminErfEditStatusController@onboardingDatelockDelete')->name('delete-locking-onboarding-date'); 
    Route::get('/admin/erf_edit_status/getLockingErfSetOnboardingDateForm/{id}','AdminErfEditStatusController@getLockingErfOnboardingDateFormView')->name('get-locking-onboarding-erf-form'); 

    //ERF closed request
    Route::get('/admin/erf_edit_status/getErfCloseRequest/{id}','AdminErfEditStatusController@getRequestClose')->name('close-request'); //new
    Route::post(config('crudbooster.ADMIN_PATH').'/erf_edit_status/getLockingCloseRequest','AdminErfEditStatusController@lockFormCloseRequest')->name('locking-form-close-request'); //new
    Route::post(config('crudbooster.ADMIN_PATH').'/erf_edit_status/deleteLockformCloseRequest','AdminErfEditStatusController@closeRequestlockDelete')->name('delete-locking-close-request'); //new
    Route::post(config('crudbooster.ADMIN_PATH').'/erf_edit_status/setCloseRequest','AdminErfEditStatusController@setRequestClose')->name('set-close-request'); // new
    Route::get('/admin/erf_edit_status/getLockingErfCloseRequest/{id}','AdminErfEditStatusController@getLockingCloseErfFormView')->name('get-locking-erf-close-form');

    //PRINT ERF
    Route::get('/admin/erf_edit_status/getDetailPrintErf/{id}','AdminErfEditStatusController@getDetailPrintErf')->name('print-details-erf');

    //get position route api erf
    Route::post(config('crudbooster.ADMIN_PATH').'/erf_header_request/get-positions','AdminHrRequisitionController@positions')->name('get-positions'); // new

    //Applicant Moduel
    Route::get('/admin/applicant_module/getEditApplicant/{id}','AdminApplicantModuleController@getEditApplicant')->name('edit-applicant');
    Route::get('/admin/applicant_module/getDetailApplicant/{id}','AdminApplicantModuleController@getDetailApplicant')->name('applicant-detail');
    //Aplicant Import and export
    Route::get('/admin/applicant_module/applicant-upload','AdminApplicantModuleController@applicantUploadView');
    Route::post('/admin/applicant_module/upload-applicant','AdminApplicantModuleController@applicantUpload')->name('upload-applicant');
    Route::post('/admin/applicant_module/search-applicant','AdminApplicantModuleController@searchApplicant')->name('erf-search');
    Route::post('/admin/applicant_module/export-applicant','AdminApplicantModuleController@applicantExport')->name('export-applicant');
    Route::get('/admin/applicant_module/download-applicant-template','AdminApplicantModuleController@downloadApplicantTemplate');
    
    //Applicant Report
    //Route::get('/admin/applicant_module/summary-report/{id}', 'AdminApplicantSummaryReportController@applicantSummaryReport')->name('view-applicant-status');
    //report export filter
    Route::post('/admin/reports/search-approved','AdminReportsController@searchApplicant')->name('request-search');
    Route::post('/admin/reports/export-request','AdminReportsController@requestExport')->name('export-request');

    //serach per category
    Route::post(config('crudbooster.ADMIN_PATH').'/header_request/item-it-search','AdminHeaderRequestController@itemITSearch')->name('item.it.search');
    Route::post(config('crudbooster.ADMIN_PATH').'/header_request/item-fa-search','AdminHeaderRequestController@itemFASearch')->name('item.fa.search');
    Route::post(config('crudbooster.ADMIN_PATH').'/header_request/item-marketing-search','AdminHeaderRequestController@itemMarketingSearch')->name('item.marketing.search');
    Route::post(config('crudbooster.ADMIN_PATH').'/header_request/item-supplies-search','AdminHeaderRequestController@itemSuppliesSearch')->name('item.supplies.search');

    //Item Sourcing Routes
    Route::post(config('crudbooster.ADMIN_PATH').'/item-sourcing-header/create-arf','AdminItemSourcingHeaderController@createArf')->name('create-arf');
    Route::get('admin/item-sourcing-header/RemoveItemSource','AdminItemSourcingHeaderController@RemoveItemSource');
    Route::get('admin/item-sourcing-header/SelectedOption','AdminItemSourcingHeaderController@SelectedOption');
    Route::get('admin/item-sourcing-header/selectedAlternativeOption','AdminItemSourcingHeaderController@selectedAlternativeOption');
    Route::get('admin/item-sourcing-header/getRequestCancelNis/{id}','AdminItemSourcingHeaderController@getRequestCancelNis')->name('getRequestCancelNis');
    Route::post(config('crudbooster.ADMIN_PATH').'/item-sourcing-header/sub-categories','AdminItemSourcingHeaderController@SubCategories')->name('item.source.sub.categories');
    Route::post(config('crudbooster.ADMIN_PATH').'/item-sourcing-header/fa-sub-categories','AdminItemSourcingHeaderController@faSubCategories')->name('item.source.fa.sub.categories');
    Route::post(config('crudbooster.ADMIN_PATH').'/item-sourcing-header/class','AdminItemSourcingHeaderController@Class')->name('item.source.class.categories');
    Route::post(config('crudbooster.ADMIN_PATH').'/item-sourcing-header/subClass','AdminItemSourcingHeaderController@subClass')->name('item.source.sub.class.categories');
    Route::post(config('crudbooster.ADMIN_PATH').'/item-sourcing-header/save-message','AdminItemSourcingHeaderController@saveMessage')->name('save-message');
    Route::post(config('crudbooster.ADMIN_PATH').'/item-sourcing-header/edit-item-source','AdminItemSourcingHeaderController@editItemSource')->name('edit-item-source');
    Route::get(config('crudbooster.ADMIN_PATH').'/item-sourcing-header/get-versions','AdminItemSourcingHeaderController@getVersions')->name('get-versions');

    Route::get('admin/item_sourcing_for_quotation/addDigitsCode','AdminItemSourcingForQuotationController@addDigitsCode');
    //view and edit
    Route::get('admin/item-sourcing-header/getDetail/{id}','AdminItemSourcingHeaderController@getDetail')->name('getDetail');
    Route::get('admin/item-sourcing-header/getDetailReject/{id}','AdminItemSourcingHeaderController@getDetailReject')->name('getDetailReject');
    
    //reports
    Route::get('/admin/reports/export-report','AdminReportsController@exportReportAssetsList')->name('export-assets-report-list');
    //Route::get('/admin/reports/getIndex','AdminReportsController@getIndex')->name('get-report');
    //Route::get('/admin/get-reports/getIndex', [AdminReportsv2Controller::class, 'getIndex'])->name('get-report');

    //location api
    Route::post(config('crudbooster.ADMIN_PATH').'/get-location-data','AdminLocationsController@getLocationDataApi')->name('get-location-data');
    Route::post(config('crudbooster.ADMIN_PATH').'/get-location-updated-data','AdminLocationsController@getLocationUpdatedDataApi')->name('get-location-updated-data');

    //Supplies upload quantity fulfillment
    Route::get('/admin/for_purchasing/fulfillment-upload','AdminForPurchasingController@UploadFulfillment');
    Route::post('/admin/admin_import/upload-fulfillment','AdminImportController@fulfillmentUpload')->name('upload-fulfillment');
    Route::get('/admin/admin_import/download-filfill-qty-template','AdminImportController@downloadFulfillQtyTemplate');
    Route::post('/admin/for_purchasing/export-conso','AdminForPurchasingController@ExportConso')->name('export-conso');
    //PO UPLOAD
    Route::get('/admin/for_purchasing/po-upload','AdminForPurchasingController@UploadPo');
    Route::post('/admin/admin_import/upload-po','AdminImportController@poUpload')->name('upload-po');
    Route::get('/admin/admin_import/download-po-template','AdminImportController@downloadPOTemplate');
    
    //CANCELLATION UPLOAD
    Route::get('/admin/for_purchasing/cancellation-upload','AdminForPurchasingController@UploadCancellation');
    Route::post('/admin/admin_import/upload-cancellation','AdminImportController@cancellationUpload')->name('upload-cancellation');
    Route::get('/admin/admin_import/download-cancellation-template','AdminImportController@downloadCancellationTemplate');
    //Supplies Inventory
    Route::get('/admin/assets_supplies_inventory/supplies-inventory-upload','AdminAssetsSuppliesInventoryController@UploadSuppliesInventory');
    Route::post('/admin/assets_supplies_inventory/upload-supplies-inventory','AdminAssetsSuppliesInventoryController@SuppliesInventoryUpload')->name('upload-supplies-inventory');
    Route::get('/admin/assets_supplies_inventory/upload-supplies-inventory-template','AdminAssetsSuppliesInventoryController@downloadSuppliesInventoryTemplate');
    Route::post(config('crudbooster.ADMIN_PATH').'/assets_supplies_inventory/description','AdminRequestsController@getDescription')->name('get.supplies.description');
    Route::post(config('crudbooster.ADMIN_PATH').'/assets_supplies_inventory/add-supplies-inventory','AdminRequestsController@addSuppliesInventory')->name('add.supplies.inventory');
    Route::post(config('crudbooster.ADMIN_PATH').'/assets_supplies_inventory/restrict-request-asset','AdminRequestsController@restrictSuppliesRequest')->name('restrict-request-asset');

    //POSITION

    Route::get('/admin/positions/positions-upload','AdminPositionsController@uploadpositionsView');
    Route::post('/admin/positions/upload-positions','AdminPositionsController@positionsUpload')->name('upload-positions');
    Route::get('/admin/positions/upload-positions-template','AdminPositionsController@uploadpositionsTemplate');
 
     //Get Sub Category Code Range
     Route::post(config('crudbooster.ADMIN_PATH').'/sub_categories/get-getSubCatCodeRangeFrom','AdminSubCategoriesController@getSubCatCodeRangeFrom')->name('getRangeCodeFrom');
     Route::post(config('crudbooster.ADMIN_PATH').'/sub_categories/get-getSubCatCodeRangeTo','AdminSubCategoriesController@getSubCatCodeRangeTo')->name('getRangeCodeTo');
     Route::post(config('crudbooster.ADMIN_PATH').'/sub_categories/get-getSubCatCodeRangeAll','AdminSubCategoriesController@getSubCatCodeRangeAll')->name('getRangeCodeAll');
     
     //serach per category
     Route::post(config('crudbooster.ADMIN_PATH').'/header_request/item-it-search','AdminHeaderRequestController@itemITSearch')->name('item.it.search');
     Route::post(config('crudbooster.ADMIN_PATH').'/header_request/item-fa-search','AdminHeaderRequestController@itemFASearch')->name('item.fa.search');

     //Direct Delivery
     Route::post(config('crudbooster.ADMIN_PATH').'/selectedHeaderDr','AdminDirectDeliveryController@selectedHeaderDr')->name('order.selected.header');

     Route::get('/admin/db-truncate','TruncateController@dbtruncate');
     
     Route::get('/admin/clear-view', function() {
        Artisan::call('view:clear');
        return "View cache is cleared!";
     });
});

