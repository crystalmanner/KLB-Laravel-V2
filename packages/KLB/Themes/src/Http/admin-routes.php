<?php
//this is where we add routing to Webkul
Route::group(['middleware' => ['web', 'admin']], function () {

    Route::get('/admin/themes', 'KLB\Themes\Http\Controllers\Admin\ThemesController@index')->defaults('_config', [
        'view' => 'themes::admin.index',
    ])->name('themes.admin.index');

    Route::get('/admin/klb/autocomplete', 'KLB\Themes\Http\Controllers\Admin\TableSearchController@autoCompleteOptions');

    // Route::get('/admin/themes/customer/abandoned-cart', 'KLB\Themes\Http\Controllers\Admin\AbandonedCartController@index')->defaults('_config', [
    //     'view' => 'themes::admin.customer.abandoned-cart',
    // ])->name('themes.admin.customer.abandoned-cart');
    Route::view('/admin/themes/customer/abandoned-cart',  'KLB-theme::admin.customer.abandoned-cart')->name('themes.admin.customer.abandoned-cart');

    Route::get('/admin/themes/customer/abandoned-cart/view-cart/{id}', 'KLB\Themes\Http\Controllers\Admin\AbandonedCartController@view')->defaults('_config', [
        'view' => 'KLB-theme::admin.customer.abandoned-cart-details',
    ])->name('themes.admin.customer.abandoned-cart-details');

    Route::get('/admin/themes/customer/abandoned-cart/send-email/{email}', 'KLB\Themes\Http\Controllers\Admin\AbandonedCartController@sendEmail')->defaults('_config', [
        'view' => 'KLB-theme::admin.customer.abandoned-cart-sendEmail'
    ])->name('themes.admin.customer.abandoned-cart-email');
    
    //route for sending mass emails to customers
    Route::post('/admin/themes/customer/abandoned-cart/send-mass-email', 'KLB\Themes\Http\Controllers\Admin\AbandonedCartController@sendMassEmail')->name('themes.admin.customer.abandoned-cart-mass-email');
    
});