<?php

namespace Ichynul\IframeTabs;

use Encore\Admin\Admin;
use Illuminate\Support\ServiceProvider;

class IframeTabsServiceProvider extends ServiceProvider
{
    public static $manifestData = [];

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

        Admin::booting(function () {
            Admin::js('/vendor/laravel-admin-ext/iframe-tabs/bootstrap-tab.js');
        });

        if (!$this->app->runningInConsole()) {

            Admin::booted(function () {

                $isMinify = $this->isMinify();

                if ($isMinify) {
                    $this->fixMinify();
                }

                $layer_path = IframeTabs::config('layer_path', '');
                if ($layer_path) {
                    Admin::js($layer_path);

                    if ($isMinify) {
                        Admin::css(preg_replace('/^(.+)layer\.js.*$/i', '$1theme/default/layer.css?v=iframe-tabs', $layer_path));
                    }
                }

                if (\Request::route()->getName() == 'iframes.index') {
                    //Override view index hide partials.footer
                    \View::prependNamespace('admin', __DIR__ . '/../resources/views/index');

                    Admin::css(IframeTabs::config('tabs_css', '/vendor/laravel-admin-ext/iframe-tabs/dashboard.css'));
                } else {
                    //Override view content hide partials.header and partials.sidebar
                    \View::prependNamespace('admin', __DIR__ . '/../resources/views/content');
                    //add scritp 'Back to top' in content 
                    $this->contentScript();

                    //Override content style ,reset style of '#pjax-container' margin-left:0
                    Admin::css('vendor/laravel-admin-ext/iframe-tabs/content.css');
                }

                config(['admin.minify_assets' => false]);
            });
        }
    }

    protected function fixMinify()
    {
        Admin::$baseJs = Admin::$baseCss = Admin::$css =  Admin::$js = [];

        Admin::js($this->getManifestData('js'));
        Admin::css($this->getManifestData('css'));
    }

    protected function isMinify()
    {
        if (!isset(Admin::$manifest)) {
            return false;
        }

        if (!config('admin.minify_assets') || !file_exists(public_path(Admin::$manifest))) {
            return false;
        }

        return true;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    protected function getManifestData($key)
    {
        if (!empty(static::$manifestData)) {
            return static::$manifestData[$key];
        }

        static::$manifestData = json_decode(
            file_get_contents(public_path(Admin::$manifest)),
            true
        );

        return static::$manifestData[$key];
    }

    protected function contentScript()
    {
        $script = <<<EOT

        $('body').addClass('iframe-content');

        if(!$('button#totop'))
        {
            $('.wrapper').append('<span id="totop" class="fa fa-upload" title="Back to top" style="display:none;"></span>');

            $(window).scroll(function() {
                if ($(window).scrollTop() > 400) {
                    $("#totop").fadeIn(300);
                } else {
                    $("#totop").fadeOut(300);
                }
            });
            
            $("#totop").click(function() {
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
        }

        if($('#terminal-box').size())
        {
            // fix laravel-admin-extensions/helpers terminal
            $(window).load(function(){
                $('#terminal-box,.slimScrollDiv').css({
                    height: $('#pjax-container').height() - 247 +'px'
                });
            });
        }
        
        $('body').on('click', '.breadcrumb li a', function() {
            var url = $(this).attr('href');
            if (url == top.iframes_index) {
                top.addTabs({
                    id: '_admin_dashboard',
                    title: top.home_title,
                    close: false,
                    url: url,
                    urlType: 'absolute',
                    icon: '<i class="fa ' + top.home_icon + '"></i>'
                });
                return false;
            }
        });
        
EOT;
        Admin::script($script);
    }
}
