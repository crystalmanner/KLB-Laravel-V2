<?php

Route::group(['middleware' => ['web']], function () {

    Route::prefix('admin')->group(function () {

        Route::group(['middleware' => ['admin']], function () {

            // Bulk Upload Products
            Route::get('bulkupload-upload-files', 'Webkul\BulkUpload\Http\Controllers\Admin\BulkUploadController@index')->defaults('_config', [
                'view' => 'bulkupload::admin.bulk-upload.upload-files.index'
            ])->name('admin.bulk-upload.index');

            Route::get('bulk-upload-run-profile', 'Webkul\BulkUpload\Http\Controllers\Admin\BulkUploadController@index')->defaults('_config', [
                'view' => 'bulkupload::admin.bulk-upload.run-profile.index'
            ])->name('admin.run-profile.index');

            Route::post('read-csv', 'Webkul\BulkUpload\Http\Controllers\Admin\HelperController@readCSVData')
            ->name('bulk-upload-admin.read-csv');

            Route::post('getprofiles', 'Webkul\BulkUpload\Http\Controllers\Admin\HelperController@getAllDataFlowProfiles')
            ->name('bulk-upload-admin.get-all-profile');

            // Download Sample Files
            Route::post('download','Webkul\BulkUpload\Http\Controllers\Admin\HelperController@downloadFile')->defaults('_config',[
                'view' => 'bulkupload::admin.bulk-upload.upload-files.index'
            ])->name('download-sample-files');

            // import new products
            Route::post('importnew', 'Webkul\BulkUpload\Http\Controllers\Admin\HelperController@importNewProductsStore')->defaults('_config',['view' => 'bulkupload::admin.bulk-upload.upload-files.index' ])->name('import-new-products-form-submit');

            Route::get('dataflowprofile', 'Webkul\BulkUpload\Http\Controllers\Admin\BulkUploadController@index')->defaults('_config', [
                'view' => 'bulkupload::admin.bulk-upload.data-flow-profile.index'
            ])->name('admin.dataflow-profile.index');

            Route::post('addprofile', 'Webkul\BulkUpload\Http\Controllers\Admin\BulkUploadController@store')->defaults('_config', [
                'view' => 'bulkupload::admin.bulk-upload.data-flow-profile.index'
            ])->name('bulkupload.bulk-upload.dataflow.add-profile');

            Route::post('runprofile', 'Webkul\BulkUpload\Http\Controllers\Admin\HelperController@runProfile')->defaults('_config', [
                'view' => 'bulkupload::admin.bulk-upload.run-profile.progressbar'
            ])->name('bulk-upload-admin.run-profile');

            // edit actions
            Route::post('/dataflowprofile/delete/{id}','Webkul\BulkUpload\Http\Controllers\Admin\BulkUploadController@destroy')->name('bulkupload.admin.profile.delete');

            Route::get('/dataflowprofile/edit/{id}', 'Webkul\BulkUpload\Http\Controllers\Admin\BulkUploadController@edit')->defaults('_config', [
                'view' => 'bulkupload::admin.bulk-upload.data-flow-profile.edit'
            ])->name('bulkupload.admin.profile.edit');

            Route::post('/dataflowprofile/update/{id}', 'Webkul\BulkUpload\Http\Controllers\Admin\BulkUploadController@update')->defaults('_config', [
                'view' => 'bulkupload::admin.bulk-upload.data-flow-profile.index'
            ])->name('admin.bulk-upload.dataflow.update-profile');

            //mass destroy
            Route::post('products/massdestroy', 'Webkul\BulkUpload\Http\Controllers\Admin\BulkUploadController@massDestroy')->defaults('_config', [
                'redirect' => 'admin.dataflow-profile.index'
            ])->name('bulkupload.admin.profile.massDelete');
        });
    });
});