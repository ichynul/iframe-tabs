<?php

use Ichynul\IframeTabs\Http\Controllers\IframeTabsController;

Route::get('/home', IframeTabsController::class . '@index')->name('admin.dashboard');
Route::get('/dashboard', IframeTabsController::class . '@index')->name('admin.dashboard');