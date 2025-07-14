<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\ForgotPasswordController;
use App\Http\Controllers\API\ResetPasswordController;
use App\Http\Controllers\API\UserProfileController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\CheckoutController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\CountryController;
use App\Http\Controllers\API\StateController;
use App\Http\Controllers\API\CustomerOrderController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\SupportController;
use App\Http\Controllers\API\ShippingMethodController;
use App\Http\Controllers\API\PageController;
use App\Http\Controllers\API\FaqController;
use App\Http\Controllers\API\VatController;
use App\Models\PageContent;





Route::get('/page-content/{page_id}', function ($page_id) {
    // Fetch the page or throw 404
    $page = PageContent::findOrFail($page_id);

    // Pass the model into the view
    return view('page-content', [
        'page' => $page,
    ]);
});

Route::post('register', [RegisterController::class, 'register']);
Route::post('user-verify-Otp', [RegisterController::class, 'userVerifyOtp']);
Route::get('resend-user-otp', [RegisterController::class, 'resendUserOtp']);
Route::post('login', [LoginController::class, 'login']);
Route::post('forgot-password', [ForgotPasswordController::class, 'forgotPassword']);
Route::post('reset-password', [ResetPasswordController::class, 'resetPassword']);
Route::get('get-countries', [CountryController::class, 'getCountries']);
Route::get('get-states', [StateController::class, 'getCountryState']);

// Google auth login
Route::get('/auth/google/redirect', [LoginController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [LoginController::class, 'handleGoogleCallback']);

//Facebook auth login
Route::get('/auth/facebook/redirect', [LoginController::class, 'redirectToFacebook']);
Route::get('/auth/facebook/callback', [LoginController::class, 'handleFacebookCallback']);

//Apple login
Route::get('/auth/apple/redirect', [LoginController::class, 'redirectToApple']);
Route::get('/auth/apple/callback', [LoginController::class, 'handleAppleCallback']);

//social login
Route::post('/auth/social-login', [LoginController::class, 'socialLogin']);


Route::prefix('category')->group(function () {
    Route::get('list', [CategoryController::class, 'getCategories']);
});

Route::prefix('page')->group(function () {
    Route::get('/details', [PageController::class, 'pageDetails']);
    Route::get('/faqs', [FaqController::class, 'getFaqs']);
});



Route::prefix('order')->group(function () {
    Route::get('confirm-order-payment', [OrderController::class, 'confirmOrderPayment']);
});   

Route::middleware('auth:api')->group(function () {

    Route::post('logout', [LoginController::class, 'logout']);
    Route::post('change-password', [UserProfileController::class, 'changePassword']);

    Route::middleware(['check_status'])->group(function () {
        Route::get('profile', [UserProfileController::class, 'profile']);
        Route::post('update-profile', [UserProfileController::class, 'updateProfile']);
    });

    Route::prefix('product')->group(function () {
        Route::post('rating', [ProductController::class, 'productRating']);
    });

    Route::prefix('product')->group(function () {
        Route::get('list', [ProductController::class, 'getProducts']);
        Route::get('single-product/{product_id}', [ProductController::class, 'getSingleProduct']);
    });

    Route::prefix('bookmark')->group(function () {
        Route::get('add-bookmark-product', [ProductController::class, 'addBookMarkProduct']);
        Route::get('get-bookmark-product', [ProductController::class, 'getBookMarkProduct']);
        Route::delete('remove-bookmark-product', [ProductController::class, 'removeBookMarkProduct']);
    });

    Route::prefix('cart')->group(function () {
        Route::post('add-to-cart', [CartController::class, 'addProductToCart']);
        Route::get('get-cart-items', [CartController::class, 'getUserCart']);
        Route::delete('remove-item', [CartController::class, 'removeItem']);
        Route::delete('remove-all-items', [CartController::class, 'removeAllItems']);
        Route::get('increase-quantity', [CartController::class, 'increaseQuantity']);
        Route::get('decrease-quantity', [CartController::class, 'decreaseQuantity']);
    });

    Route::prefix('checkout')->group(function () {
        Route::get('checkout-process', [CheckoutController::class, 'checkoutProcess']);
    });
    
    Route::prefix('order')->group(function () {
        Route::post('add-shipping-address', [OrderController::class, 'addShippingAddress']);
        Route::get('get-shipping-address', [OrderController::class, 'getShippingAddress']);
        Route::get('current-pending-orders', [OrderController::class, 'currentPendingOrders']);
        Route::get('get-shipping-method', [OrderController::class, 'shippingMethod']);
    });  
    
    Route::prefix('my-order')->group(function () {
        Route::get('orders', [CustomerOrderController::class, 'orderDetails']);
        Route::get('single-order', [CustomerOrderController::class, 'singleOrder']);
    });

    Route::prefix('notifications')->group(function(){
        Route::get('listing',[NotificationController::class,'getCustomerNotifications']);
        Route::get('recent-notifications',[NotificationController::class,'getCustomerRecentNotifications']);
        Route::get('details',[NotificationController::class,'getSingleCustomerNotification']);
        // Route::post('mark-read',[NotificationController::class,'customerMarkReadNotification']);
        // Route::post('delete-notification',[NotificationController::class,'customerDeleteNotification']);
    });

    Route::prefix('ticket')->group(function (){
        Route::get('get-user-ticket', [SupportController::class, 'userTickets']);
        Route::post('sent-ticket', [SupportController::class, 'sentTicket']);
        Route::get('get-single-ticket', [SupportController::class, 'getSingleTicket']);
    });

    Route::prefix('shipping-method')->group(function (){
        Route::get('/', [ShippingMethodController::class, 'getShippingMethod']);
        Route::get('single-shipping-method', [ShippingMethodController::class, 'getSingleShippingMethod']);
    });

    Route::prefix('vat-rates')->group(function (){
        Route::get('/', [VatController::class, 'getVat']);
    });

});