<?php

use Ichynul\IframeTabs\Http\Controllers\IframeTabsController;
use Ichynul\IframeTabs\IframeTabs;

Route::get('/', IframeTabsController::class . '@index')->name('iframes.index');
Route::get('/dashboard', IframeTabs::config('home_action', IframeTabsController::class . '@dashboard'))->name('iframes.dashboard');
