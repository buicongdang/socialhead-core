<?php
use Illuminate\Support\Facades\Route;
Route::group(['prefix' => 'api/shopify'], function() {
    Route::group([], function () {
        Route::post('generate-url', [\Socialhead\Core\Http\Controllers\ShopifyController::class, 'generateUrl']);
        Route::get('auth/handle', [\Socialhead\Core\Http\Controllers\ShopifyController::class, 'authHandle']);
    });

    Route::group([], function () {
        Route::post('webhook', [\Socialhead\Core\Http\Controllers\ShopifyController::class, 'webhook']);
    });

    Route::get('webhooks', function(){
        $sdk = \Socialhead\Core\Shopify\RestSDK::config([
           'myshopify_domain' => 'dang-flashify-7.myshopify.com',
           'access_token' => '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'
        ]);
        $webhooks = $sdk->Webhooks()->lists();
        dd($webhooks);
    });


});

