<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\Admin\LoginService;
use App\Services\Admin\ForgotPasswordService;
use App\Services\Admin\ProfileSettingService;
use App\Services\Admin\CategoryService;
use App\Services\Admin\UserService;
use App\Services\Admin\ProductService;
use App\Services\Admin\OrderService;
use App\Services\Admin\SupportService;
use App\Services\Admin\ShippingMethodService;
use App\Services\Admin\FaqService;
use App\Services\Admin\VatRateService;

class AdminBaseController extends Controller
{
    //Services
    protected $login_service;
    protected $forgot_password_service;
    protected $profile_setting_service;
    protected $category_service;
    protected $user_service;
    protected $product_service;
    protected $order_service;
    protected $support_service;
    protected $shipping_method_service;
    protected $faq_service;
    protected $vat_rate_service;

    public function __construct(
        LoginService $login_service,
        ForgotPasswordService $forgot_password_service,
        ProfileSettingService $profile_setting_service,
        CategoryService $category_service,
        UserService $user_service,
        ProductService $product_service,
        OrderService $order_service,
        SupportService $support_service,
        ShippingMethodService $shipping_method_service,
        FaqService $faq_service,
        VatRateService $vat_rate_service
    ){
        // services class
        $this->login_service = $login_service;
        $this->forgot_password_service = $forgot_password_service;
        $this->profile_setting_service = $profile_setting_service;
        $this->category_service = $category_service;
        $this->user_service = $user_service;
        $this->product_service = $product_service;
        $this->order_service = $order_service;
        $this->support_service = $support_service;
        $this->shipping_method_service = $shipping_method_service;
        $this->faq_service = $faq_service;
        $this->vat_rate_service = $vat_rate_service;
    }

}
