<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//Helper class
use App\Helpers\ResponseHelper;

//services
use App\Services\API\RegisterService;
use App\Services\API\LoginService;
use App\Services\API\ForgotPasswordService;
use App\Services\API\ResetPasswordService;
use App\Services\API\UserProfileService;
use App\Services\API\CategoryService;
use App\Services\API\ProductService;
use App\Services\API\CartService;
use App\Services\API\CheckoutService;
use App\Services\API\OrderService;
use App\Services\API\CountryService;
use App\Services\API\StateService;
use App\Services\API\CustomerOrdersService;
use App\Services\API\NotificationsService;
use App\Services\API\SupportService;
use App\Services\API\ShippingMethodService;
use App\Services\API\PagesService;
use App\Services\API\FaqService;
use App\Services\API\VatService;

class APIBaseController extends Controller
{
    //response helper
    protected $response_helper;

    //status code
    protected $success_status;
    protected $created_status;
    protected $no_content_status;
    protected $not_found_status;
    protected $internal_server_status;
    protected $unprocessable_status;

    //Services
    protected $register_service;
    protected $login_service;
    protected $forgot_password_service;
    protected $reset_password_service;
    protected $user_profile_service;
    protected $category_service;
    protected $product_service;
    protected $cart_service;
    protected $checkout_service;
    protected $order_service;
    protected $country_service;
    protected $state_service;
    protected $customer_orders_service;
    protected $notifications_service;
    protected $support_service;
    protected $shipping_method_service;
    protected $pages_service;
    protected $faq_service;
    protected $vat_service;

    public function __construct(
        ResponseHelper $response_helper,
        RegisterService $register_service,
        LoginService $login_service,
        ForgotPasswordService $forgot_password_service,
        ResetPasswordService $reset_password_service,
        UserProfileService $user_profile_service,
        CategoryService $category_service,
        ProductService $product_service,
        CartService $cart_service,
        CheckoutService $checkout_service,
        OrderService $order_service,
        CountryService $country_service,
        StateService $state_service,
        CustomerOrdersService $customer_orders_service,
        NotificationsService $notifications_service,
        SupportService $support_service,
        ShippingMethodService $shipping_method_service,
        PagesService $pages_service,
        FaqService $faq_service,
        VatService $vat_service
    ){
        // helper class
        $this->response_helper = $response_helper;

        // services class
        $this->register_service = $register_service;
        $this->login_service = $login_service;
        $this->forgot_password_service = $forgot_password_service;
        $this->reset_password_service = $reset_password_service;
        $this->user_profile_service = $user_profile_service;
        $this->category_service = $category_service;
        $this->product_service = $product_service;
        $this->cart_service = $cart_service;
        $this->checkout_service = $checkout_service;
        $this->order_service = $order_service;
        $this->country_service = $country_service;
        $this->state_service = $state_service;
        $this->customer_orders_service = $customer_orders_service;
        $this->notifications_service = $notifications_service;
        $this->support_service = $support_service;
        $this->shipping_method_service = $shipping_method_service;
        $this->pages_service = $pages_service;
        $this->faq_service = $faq_service;
        $this->vat_service = $vat_service;

        //constants
        $this->success_status = config('global-constant.STATUS_CODE.SUCCESS_STATUS');
        $this->created_status = config('global-constant.STATUS_CODE.CREATED_STATUS');
        $this->no_content_status = config('global-constant.STATUS_CODE.NO_CONTENT_STATUS');
        $this->not_found_status = config('global-constant.STATUS_CODE.NOT_FOUND_STATUS');
        $this->internal_server_status = config('global-constant.STATUS_CODE.INTERNAL_SERVER_STATUS');
        $this->unprocessable_status = config('global-constant.STATUS_CODE.UNPROCESSABLE_STATUS');
        $this->bad_request_status = config('global-constant.STATUS_CODE.BAD_REQUEST');
        $this->unauthorized_status = config('global-constant.STATUS_CODE.UNAUTHORIZED');
    }

}
