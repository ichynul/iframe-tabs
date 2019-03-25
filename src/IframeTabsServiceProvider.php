<?php

namespace Ichynul\IframeTabs;

use Encore\Admin\Admin;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;

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
            if (\Request::route()->getName() == 'iframes.index') {
                //Override view index hide partials.footer
                \View::prependNamespace('admin', __DIR__ . '/../resources/views/index');
                // add script ands css
                Admin::css(IframeTabs::config('tabs_css', '/vendor/laravel-admin-ext/iframe-tabs/dashboard.css'));
                Admin::js('/vendor/laravel-admin-ext/iframe-tabs/bootstrap-tab.js');
                Admin::js('/vendor/laravel-admin-ext/iframe-tabs/sidebarMenu.js');
                Admin::js(IframeTabs::config('layer_path', '/vendor/laravel-admin-ext/cropper/layer/layer.js'));
            } else {
                //Override view content hide partials.header and partials.sidebar
                \View::prependNamespace('admin', __DIR__ . '/../resources/views/content');
                //Override content style ,reset style of '#pjax-container' margin-left:0
                Admin::css('vendor/laravel-admin-ext/iframe-tabs/content.css');
                //add scritp 'Back to top' in content 
                $this->contentScript();
            }
        });
    }

    protected function contentScript()
    {
        $script = <<<EOT

        $('.wrapper').append('<span id="back-to-top" class="fa fa-upload" title="Back to top" style="display:none;"></span>');
        $(window).scroll(function() {
            if ($(window).scrollTop() > 400) {
                $("#back-to-top").fadeIn(300);
            } else {
                $("#back-to-top").fadeOut(300);
            }
        });
        
        $("#back-to-top").click(function() {
            if ($('html').scrollTop()) {
                $('html').animate({
                    scrollTop: 0
                }, 300);
                return false;
            }
            $('body').animate({
                scrollTop: 0
            }, 300);
            return false;
        });

        if($('#terminal-box').size())
        {
            // fix laravel-admin-extensions/helpers terminal
            $(document).ready(function(){
                $('#terminal-box,.slimScrollDiv').css({
                    height: $('#pjax-container').height() - 305 +'px'
                });
            });
        }
EOT;
        Admin::script($script);
    }
}
