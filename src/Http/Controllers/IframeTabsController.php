<?php

namespace Ichynul\IframeTabs\Http\Controllers;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Illuminate\Routing\Controller;
use Ichynul\IframeTabs\IframeTabs;

class IframeTabsController extends Controller
{
    public function index(Content $content)
    {
        if (\Request::route()->getName() == 'admin.index') {
            $this->script();
        }

        $items = [
            'header' => config('admin.name'),
            'trans' => [
                'oprations' => trans('admin.iframe_tabss.oprations'),
                'refresh_current' => trans('admin.iframe_tabss.refresh_current'),
                'close_current' => trans('admin.iframe_tabss.close_current'),
                'close_all' => trans('admin.iframe_tabss.close_all'),
                'close_other' => trans('admin.iframe_tabss.close_other'),
            ]
        ];
        return view('iframe-tabs::index', $items);
    }

    public function default(Content $content)
    {
        return $content
            ->header('Defautl page')
            ->description('Defautl page')
            ->body('Defautl page have not seted ,place edit config in `config/admin.php`'
                . "<pre>'extensions' => [
                'iframe-tabs' => [
                     // Set to `false` if you want to disable this extension
                    'enable' => true,
                    // Default page controller
                    'home_action' => App\Admin\Controllers\HomeController::class . '@index',//edit here
                    // Default page uir after user login success
                    'home_uri' => '/admin/dashboard',
                    // Default page tab-title
                    'home_title' => 'Home',
                    // Default page tab-title icon
                    'home_icon' => 'fa-home',
                    // wheath show icon befor titles for all tab
                    'use_icon' => true,
                    // layer.js path , if you do not user laravel-admin-ext\cropper , set another one
                    'layer_path' => '/vendor/laravel-admin-ext/cropper/layer/layer.js'
                ]
            ],</pre>");
    }

    protected function script()
    {
        $call_back = admin_base_path('configx/sort');
        $refresh_current = trans('admin.iframe_tabss.refresh_current');
        $open_in_new = trans('admin.iframe_tabss.open_in_new');
        $home_uri = IframeTabs::config('home_uri', '/admin/dashboard');
        $home_title = IframeTabs::config('home_title', 'Index');
        $home_icon = IframeTabs::config('home_icon', 'fa-home');
        $use_icon = IframeTabs::config('use_icon', true) ? 'true' : 'false';

        $script = <<<EOT
        window.refresh_current = '{$refresh_current}';
        window.open_in_new = '{$open_in_new}';
        window.use_icon = {$use_icon};

        if (!window.layer) {
            window.layer = {
                load: function () {
                    var html = '<div style="z-index:999;margin:0 auto;position:fixed;top:90px;left:50%;" class="loading-message"><img src="/vendor/laravel-admin-ext/iframe-tabs/loading/loading-spinner-grey.gif" /></div>';
                    $('.tab-content').append(html);
                    return 0;
                },
                close: function (index) {
                    $('.tab-content .loading-message').remove();
                }
            };
        }

        $('body').on('click', '.sidebar-menu li a', function () {
            var url = $(this).attr('href');
            var index = $('.sidebar-menu li a').index(this);
            if (!url || url == '#') {
                return;
            }
            var icon = $(this).find('i.fa').prop("outerHTML");
            addTabs({
                id: url.replace(/\W/g,'_'),
                title: $(this).find('span').text(),
                close: index!= 0,
                url: url,
                urlType: 'absolute',
                icon : icon
            });
            return false;
        });
        
        if (window == top) {
            var url = '{$home_uri}';
            addTabs({
                id: url.replace(/\W/g,'_'),
                title: '{$home_title}',
                close: false,
                url: url,
                urlType: 'absolute',
                icon : '<i class="fa {$home_icon}"></i>'
            });
        }
EOT;
        Admin::script($script);
    }
}