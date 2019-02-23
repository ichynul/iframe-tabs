<?php

namespace Ichynul\IframeTabs\Http\Controllers;

use Illuminate\Routing\Route;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Ichynul\IframeTabs\IframeTabs;
use Illuminate\Routing\Controller;
use function GuzzleHttp\json_encode;

class IframeTabsController extends Controller
{
    public function index(Route $route)
    {
        $this->script();
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
        return view('iframe-tabs::ext.index', $items);
    }

    public function dashboard(Content $content)
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
        $refresh_current = trans('admin.iframe_tabss.refresh_current');
        $open_in_new = trans('admin.iframe_tabss.open_in_new');
        $open_in_pop = trans('admin.iframe_tabss.open_in_pop');
        $home_uri = IframeTabs::config('home_uri', '/admin/dashboard');
        $home_title = IframeTabs::config('home_title', 'Index');
        $home_icon = IframeTabs::config('home_icon', 'fa-home');
        $use_icon = IframeTabs::config('use_icon', true) ? 'true' : 'false';
        $pass_urls = json_encode(IframeTabs::config('pass_urls', ['/admin/auth/logout']));

        $script = <<<EOT
        window.refresh_current = '{$refresh_current}';
        window.open_in_new = '{$open_in_new}';
        window.open_in_pop = '{$open_in_pop}';
        window.use_icon = {$use_icon};
        window.pass_urls = JSON.parse('{$pass_urls}');

        window.openPop = function(url,title){
            layer.open({
                type: 2,
                title: title,
                anim: 2,
                closeBtn: 1, 
                shade: false,
                maxmin: true, //开启最大化最小化按钮
                area: ['90%', '90%'],
                content: url,
              });
        }

        if (!window.layer) {
            window.layer = {
                load: function () {
                    var html = '<div style="z-index:999;margin:0 auto;position:fixed;top:90px;left:50%;" class="loading-message"><img src="/vendor/laravel-admin-ext/iframe-tabs/images/loading-spinner-grey.gif" /></div>';
                    $('.tab-content').append(html);
                    return 1;
                },
                close: function (index) {
                    $('.tab-content .loading-message').remove();
                },
                open : function()
                {
                    alert('layer.js dose not work.');
                }
            };
        }

        $('body').on('click', '.sidebar-menu li a,.navbar-nav>li a', function () {
            var url = $(this).attr('href');
            if (!url || url == '#') {
                return;
            }
            if(window.pass_urls)
            {
                for(var i in window.pass_urls)
                {
                    if(url.indexOf(window.pass_urls[i]) > -1)
                    {
                        return true;
                    }
                }
            }
            var icon = '<i class="fa fa-edge"></i>';
            if($(this).find('i.fa').size())
            {
                var icon = $(this).find('i.fa').prop("outerHTML");
            }
            var span = $(this).find('span');
            addTabs({
                id: url.replace(/\W/g,'_'),
                title: span.size() ? span.text() : $(this).text().length ? $(this).text() : '*' ,
                close: true,
                url: url,
                urlType: 'absolute',
                icon : icon
            });

            if($(this).parents('.dropdown').size())
            {
                $(this).parents('.dropdown').find('.dropdown-toggle').trigger('click');
            }
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
        else 
        {
            if(/\/admin\/?$/i.test(location.href))
            {
                $('body').html('....');
                location.href = '{$home_uri}';
            }
        }
EOT;
        Admin::script($script);
    }
}
