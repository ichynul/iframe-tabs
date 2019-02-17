<?php

namespace Ichynul\IframeTabs;

use Illuminate\Support\ServiceProvider;
use Ichynul\IframeTabs\Middleware\TabContrl;
use Encore\Admin\Admin;

class IframeTabsServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot(IframeTabs $extension)
    {
        if (!IframeTabs::boot()) {
            return;
        }
        if ($views = $extension->views()) {
            $this->loadViewsFrom($views, 'iframe-tabs');
        }

        if ($this->app->runningInConsole() && $assets = $extension->assets()) {
            $this->publishes(
                [$assets => public_path('vendor/laravel-admin-ext/iframe-tabs')],
                'iframe-tabs'
            );
        }
        $this->app->booted(function () {
            IframeTabs::routes(__DIR__ . '/../routes/web.php');
        });
        Admin::booted(function () {
            \View::prependNamespace('admin', __DIR__ . '/../resources/views/admin');
            //覆盖views，主要针对 index.blade.php,隐藏admin::partials.header(头部)和admin::partials.sidebar(菜单)
            if (\Request::route()->getName() == 'admin.index') {
                Admin::css('vendor/laravel-admin-ext/iframe-tabs/dashboard.css');
                Admin::js('vendor/laravel-admin-ext/iframe-tabs/bootstrap-tab.js');
                Admin::js('vendor/laravel-admin-ext/iframe-tabs/sidebarMenu.js');
                Admin::js(IframeTabs::config('layer_path', '/vendor/laravel-admin-ext/cropper/layer/layer.js'));
            } else {
                Admin::css('vendor/laravel-admin-ext/iframe-tabs/content.css');
                //极特殊情况覆盖views失败的话，靠content.css隐藏头部和菜单
            }
        });
    }
}