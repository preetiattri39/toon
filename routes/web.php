<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\ForgotPasswordController;
use App\Http\Controllers\Admin\ProfileSettingController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\HelpSupportController;
use App\Http\Controllers\Admin\ShippingMethodController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\FAQController;
use App\Http\Controllers\Admin\VatController;

Route::get('/', function () {
    // return view('welcome');
    return redirect()->route('login');
});

// Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::prefix('admin')->group(function () {
    Route::middleware('guest_admin')->group(function () {
        Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminLoginController::class, 'login'])->name('login');

        Route::get('/forgot-password', [ForgotPasswordController::class, 'forgotPasswordForm'])->name('admin-forgot-password');
        Route::post('/forgot-password', [ForgotPasswordController::class, 'forgotPassword'])->name('admin-forgot-password');
        Route::get('/reset-password/{token?}', [ForgotPasswordController::class, 'resetPasswordForm'])->name('admin-reset-password');
        Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('admin-reset-password');
    });

    Route::middleware(['isAdmin'])->group(function () {
        Route::get('/logout', [AdminLoginController::class, 'logoutAdmin'])->name('admin-logout');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('profile', [ProfileSettingController::class, 'Profile'])->name('profile');
        Route::post('profile', [ProfileSettingController::class, 'updateProfile'])->name('admin-profile');

        Route::get('change-password', [ProfileSettingController::class, 'changePasswordForm'])->name('change-password');
        Route::post('change-password', [ProfileSettingController::class, 'changePassword'])->name('change-password');

        Route::prefix('category')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->name('category-list');
            Route::get('add-category', [CategoryController::class, 'addCategoryForm'])->name('add-category');
            Route::post('add-category', [CategoryController::class, 'addCategory'])->name('add-category');
            Route::post('category-filter', [CategoryController::class, 'categoryFilter'])->name('category-filter');
            Route::get('delete/{cat_id}', [CategoryController::class, 'deleteCategory'])->name('delete-category');
            Route::get('edit/{cat_id}', [CategoryController::class, 'editCategory'])->name('edit-category');
            Route::post('update', [CategoryController::class, 'updateCategory'])->name('update-category');
            Route::get('category-status', [CategoryController::class, 'updateCategoryStatus'])->name('category-status');
        });

        Route::prefix('user')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('user-list');
            Route::get('data', [UserController::class, 'getUserData'])->name('user-data');
            Route::get('status', [UserController::class, 'updateUserStatus'])->name('user-status');
            Route::get('details/{user_id}', [UserController::class, 'userDetails'])->name('user-details');
        });

        Route::prefix('product')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('product-list');
            Route::get('data', [ProductController::class, 'getProductData'])->name('product-data');
            Route::get('status', [ProductController::class, 'updateProductStatus'])->name('product-status');
            Route::get('add-product', [ProductController::class, 'addProductForm'])->name('add-product');
            Route::post('add-product', [ProductController::class, 'addProduct'])->name('add-product');
            Route::get('edit-product/{product_id}', [ProductController::class, 'editProduct'])->name('edit-product');
            Route::post('update-product', [ProductController::class, 'updateProduct'])->name('update-product');
            Route::get('delete/{product_id}', [ProductController::class, 'deleteProduct'])->name('delete-product');
            Route::get('delete-product-image', [ProductController::class, 'deleteProductImage'])->name('delete-product-image');
        });

        Route::prefix('orders')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('order-list');
            Route::get('data', [OrderController::class, 'getOrderData'])->name('order-data');
            Route::get('generate-order-pdf/{order_id}', [OrderController::class, 'generateOrderPDF'])->name('generate-order-pdf');
            Route::get('view-order/{order_id}', [OrderController::class, 'viewOrder'])->name('view-order');
            Route::get('order-status', [OrderController::class, 'updateOrderStatus'])->name('order-status');
        });

        Route::prefix('notifications')->group(function (){
            Route::post('/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
            Route::get('/', [NotificationController::class, 'index'])->name('notification-list');
            Route::get('/details/{notification_id}', [NotificationController::class, 'notificationDetails'])->name('notification-detail');  
            Route::post('/mark-read', [NotificationController::class, 'markRead'])->name('mark-read');
        });

        Route::prefix('tickets')->group(function (){
            Route::get('/', [HelpSupportController::class, 'index'])->name('tickets');
            Route::get('data', [HelpSupportController::class, 'getTicketData'])->name('ticket-data');
            Route::get('details/{ticket_id}', [HelpSupportController::class, 'ticketDetails'])->name('ticket-details');
            Route::post('resolved', [HelpSupportController::class, 'resolvedTicket'])->name('resolved-ticket');
            Route::get('delete/{ticket_id}', [HelpSupportController::class, 'deleteTicket'])->name('delete-ticket');
        });

        Route::prefix('shipping')->group(function (){
            Route::get('/', [ShippingMethodController::class, 'index'])->name('shipping-list');
            Route::get('data', [ShippingMethodController::class, 'getShippingMethodData'])->name('shipping-data');
            Route::get('add-shipping', [ShippingMethodController::class, 'addShippingMethodForm'])->name('add-shipping');
            Route::post('add-shipping', [ShippingMethodController::class, 'addShippingMethod'])->name('add-shipping');
            Route::get('shipping-details/{shipping_method_id}', [ShippingMethodController::class, 'shippingMethodDetails'])->name('shipping-details');
            Route::post('shipping-update', [ShippingMethodController::class, 'updateShippingMethod'])->name('shipping-update');
            Route::get('shipping-delete/{shipping_method_id}', [ShippingMethodController::class, 'shippingMethodDelete'])->name('shipping-delete');
            Route::get('shipping-method-status', [ShippingMethodController::class, 'updateShippingMethodStatus'])->name('shipping-method-status');
        });


        Route::prefix('pages')->group(function (){
            Route::get('/', [PageController::class, 'index'])->name('page-list');
            Route::get('view-page/{page_id}', [PageController::class, 'viewPage'])->name('viewPage');
            Route::post('update-page-content', [PageController::class, 'updatePageContent'])->name('update-page-content');
        });

        Route::prefix('faqs')->group(function (){
            Route::get('/', [FAQController::class, 'index'])->name('faq-list');
            Route::get('/data', [FAQController::class, 'faqData'])->name('faq-data');
            Route::get('/add-faq', [FAQController::class, 'show'])->name('faq-show');
            Route::post('/add', [FAQController::class, 'addFaqs'])->name('add-faq');
            Route::get('/edit/{faq_id}', [FAQController::class, 'edit'])->name('edit-faq');
            Route::post('/update', [FAQController::class, 'updateFaqs'])->name('update-faq');
            Route::get('/delete', [FAQController::class, 'deleteFaq'])->name('faq-delete'); 
            Route::get('update-faq-status', [FAQController::class, 'updateFQAStatus'])->name('update-faq-status');
        });

        Route::prefix('vat-rates')->group(function () {
            Route::get('/', [VatController::class, 'index'])->name('vat.rates.index');
            Route::get('/data', [VatController::class, 'getVatRateData'])->name('vat.rates.data');
            Route::get('/create', [VatController::class, 'addVatRateForm'])->name('vat.rates.create');
            Route::post('/', [VatController::class, 'addVatRat'])->name('vat.rates.store');
            Route::get('/{vat_id}', [VatController::class, 'vatDetails'])->name('vat.rates.show');
            Route::post('/update', [VatController::class, 'updateVat'])->name('vat.rates.update');
            Route::get('/delete/{vat_id}', [VatController::class, 'vatRateDelete'])->name('vat.rates.delete');
            Route::get('/status/update', [VatController::class, 'updateVatRateStatus'])->name('vat.rates.status.update');
        });

    });
    
});