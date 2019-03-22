<?php

use Ichynul\IframeTabs\Http\Controllers\IframeTabsController;
use Ichynul\IframeTabs\IframeTabs;
use Ichynul\IframeTabs\Middleware\ForceLogin;
use Encore\Admin\Controllers\AuthController;

Route::get('/', IframeTabsController::class . '@index')->name('iframes.index');

Route::get('/dashboard', IframeTabs::config('home_action', IframeTabsController::class . '@dashboard'))->name('iframes.dashboard');

if (IframeTabs::config('force_login_in_top', true)) {

    $middleware = config('admin.route.middleware', []);

    array_push($middleware, 'admin.force_login');

    $authController = config('admin.auth.controller', AuthController::class);

    Route::aliasMiddleware('admin.force_login', ForceLogin::class);

    Route::get('auth/login', $authController . '@getLogin')->middleware($middleware);
}
