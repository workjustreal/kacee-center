<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Fruitcake\Cors\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        // \App\Http\Middleware\HttpsMiddleware::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'super_admin' => \App\Http\Middleware\SuperAdmin::class,
        'admin' => \App\Http\Middleware\Admin::class,
        'manager' => \App\Http\Middleware\Manager::class,
        'normal' => \App\Http\Middleware\Normal::class,
        'guestuser' => \App\Http\Middleware\Guest::class,
        'role_admin' => \App\Http\Middleware\RoleAdmin::class,
        'role_hr' => \App\Http\Middleware\RoleHR::class,
        'role_sales' => \App\Http\Middleware\RoleSales::class,
        'role_secretary' => \App\Http\Middleware\RoleSecretary::class,
        'role_it' => \App\Http\Middleware\RoleIT::class,
        'manage_event' => \App\Http\Middleware\ManageEvent::class,
        'manage_employee' => \App\Http\Middleware\ManageEmployee::class,
        'user_active' => \App\Http\Middleware\UserActive::class,
        'session_timeout' => \App\Http\Middleware\SessionTimeout::class,
        'prevent-back-history' => \App\Http\Middleware\PreventBackHistory::class,
        'prevent-maintenance' => \App\Http\Middleware\PreventMaintenance::class,
        'account_verified' => \App\Http\Middleware\AccountVerified::class,
        'app_leave' => \App\Http\Middleware\AppLeave::class,
        'app_sale_report' => \App\Http\Middleware\AppSaleReport::class,
        'app_generate_barcode' => \App\Http\Middleware\AppGenerateBarcode::class,
        'app_shipping' => \App\Http\Middleware\AppShipping::class,
        'app_shipping_checkout' => \App\Http\Middleware\AppShippingCheckout::class,
        'app_organization' => \App\Http\Middleware\AppOrganization::class,
        'manager_approve' => \App\Http\Middleware\MangerApproveReqest::class,
        'secretary_approve' => \App\Http\Middleware\SecretaryApprove::class,
        'headsecretary_approve' => \App\Http\Middleware\HeadSecretaryApprove::class,
        'discountmar' => \App\Http\Middleware\DiscountMar::class,
        'decoratemar' => \App\Http\Middleware\DiscountMar::class,
        'marandsec' => \App\Http\Middleware\CheckMarandSec::class,
        'marandheadsec' => \App\Http\Middleware\CheckMarandHeadSec::class,
        'specialmar' => \App\Http\Middleware\DiscountMar::class,
        'accounting' => \App\Http\Middleware\Accounting::class,
    ];
}
