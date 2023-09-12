<?php




Route::group(['middleware' => ['web', 'theme', 'locale', 'currency']], function () {




    Route::get('/themes', 'KLB\Themes\Http\Controllers\Shop\ThemesController@index')->defaults('_config', [
        'view' => 'themes::shop.index',
    ])->name('themes.shop.index');

    // cart.add is found in packages/Webkul/Shop/src/Http/routes.php
    Route::post('checkout/cart/buynow/{id}', 'Webkul\Shop\Http\Controllers\CartController@buyNow')->defaults('_config', [
        'redirect' => 'shop.checkout.onepage.index'
    ])->name('cart.buy_now');

    //reorder
    Route::get('orders/reorder/{id}', 'Webkul\Shop\Http\Controllers\OrderController@reorder')->defaults('_config', [
        'view' => 'shop.checkout.cart.index',
        'redirect' => '/'
    ])->name('customer.orders.reorder');

    // Route::post('checkout/cart/buynow/{id}', 'Webkul\Shop\Http\Controllers\CartController@buyNow')->name('cart.buy_now');

    // Route::post('/checkout/cart/buynow/{id}', 'KLB\Themes\Http\Controllers\Shop\ThemesController@buyNow')->name('cart.buy_now');

});
