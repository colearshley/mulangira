<?php

use Illuminate\Support\Facades\Route;

Route::get('/clear', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
});

// User Support Ticket
Route::controller('TicketController')->prefix('ticket')->name('ticket.')->group(function () {
    Route::get('/', 'supportTicket')->name('index');
    Route::get('new', 'openSupportTicket')->name('open');
    Route::post('create', 'storeSupportTicket')->name('store');
    Route::get('view/{ticket}', 'viewTicket')->name('view');
    Route::post('reply/{ticket}', 'replyTicket')->name('reply');
    Route::post('close/{ticket}', 'closeTicket')->name('close');
    Route::get('download/{ticket}', 'ticketDownload')->name('download');
});

Route::controller('SiteController')->group(function () {
    // Digital File Download
    Route::get('digital/file/download/{id}/{fileName}', 'download')->name('download');

    Route::get('/contact', 'contact')->name('contact');
    Route::post('/contact', 'contactSubmit');
    Route::post('subscribe', 'subscribe')->name('subscribe');
    Route::get('/change/{lang?}', 'changeLanguage')->name('lang');

    Route::get('cookie-policy', 'cookiePolicy')->name('cookie.policy');
    Route::get('/cookie/accept', 'cookieAccept')->name('cookie.accept');
    Route::get('policy/{slug}/{id}', 'policyPages')->name('policy.pages');
    Route::get('placeholder-image/{size}', 'placeholderImage')->name('placeholder.image');

    Route::get('category/product/{slug}/{id}', 'categoryProduct')->name('category.products');
    Route::get('brand/product/{slug}/{id}', 'brandProduct')->name('brand.products');
    Route::get('brand/all', 'allBrand')->name('brand.all');
    Route::get('subcategory/product/{slug}/{id}', 'subCategoryProduct')->name('subcategory.products');
    Route::get('all/products', 'products')->name('products');
    Route::get('product/quick-view', 'quickView')->name('product.quickView');

    Route::get('product/details/{slug}/{id}', 'productDetails')->name('product.detail');
    Route::get('reviews/{id}', 'fetchReviews')->name('fetch.reviews');
    Route::get('product/hot-deal', 'hotDeal')->name('product.hot.deal');
    Route::get('product/featured', 'featured')->name('products.featured');
    Route::get('product/best-selling', 'bestSelling')->name('products.best.selling');
    Route::get('category/all', 'categoryAll')->name('category.all');

    Route::get('product/filter', 'filterProduct')->name('all.products.filter');

    //Track Order
    Route::get('track/order', 'trackOrder')->name('track.order');
    Route::post('get-track/order', 'getTrackOrder')->name('get.track.order');

    //Default Url
    Route::get('/', 'index')->name('home');
});

Route::controller('WishController')->prefix('wish-list')->name('wish.list.')->group(function () {
    Route::post('add', 'addWishList')->name('add');
    Route::get('count', 'wishListCount')->name('count');
    Route::get('product', 'wishListProduct')->name('product');
    Route::get('remove', 'removeWishList')->name('remove');
});

Route::controller('CartController')->prefix('cart-list')->name('cart.list.')->group(function () {
    Route::post('add', 'addToCart')->name('add');
    Route::get('count', 'getCartCount')->name('count');
    Route::get('product', 'cartProducts')->name('product');
    Route::post('remove', 'removeCart')->name('remove');
    Route::post('update', 'updateCart')->name('update');
    Route::post('coupon-apply', 'couponApply')->name('apply.coupon');
});
