<?php

use Ichynul\IframeTabs\Http\Controllers\IframeTabsController;
use Ichynul\IframeTabs\IframeTabs;

Route::get('/', IframeTabsController::class . '@index')->name('admin.index');
Route::get('/dashboard', IframeTabs::config('home_action', IframeTabsController::class . '@default'))->name('admin.dashboard');
