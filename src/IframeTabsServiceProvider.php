<?php

namespace Ichynul\IframeTabs;

use Encore\Admin\Admin;
use Ichynul\IframeTabs\Middleware\ForceLogin;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;

class IframeTabsServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        app('router')->aliasMiddleware('iframe.login', ForceLogin::class);

        Admin::booted(function () {

            if ($this->inWeb()) {
                IframeTabs::fixMinify();
            }
        });
    }

    protected function inWeb()
    {
        if (IframeTabs::config('web_in_cli' ,false)) {
            return true;
        }

        $c = request('c', '');

        return !$this->app->runningInConsole()
            && (!$c || !preg_match('/.*?admin:minify.*?/i', $c)); // if run admin:minify in `admin/helpers/terminal/artisan`
    }

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

        $layer_path = IframeTabs::config('layer_path', 'vendor/laravel-admin-ext/iframe-tabs/layer/layer.js');

        if (!file_exists(public_path($layer_path))) {
            $layer_path = '';
        }

        Admin::booting(function () use ($layer_path) {
            Admin::js('vendor/laravel-admin-ext/iframe-tabs/bootstrap-tab.js');

            if ($layer_path) {
                Admin::js($layer_path);
            }
        });

        if ($this->inWeb()) {

            Admin::booted(function () use ($layer_path) {

                if (IframeTabs::isMinify() && $layer_path) {
                    Admin::css(preg_replace('/^(.+)layer\.js.*$/i', '$1theme/default/layer.css?v=iframe-tabs', $layer_path));
                }

                if (\Request::route()->getName() == 'iframes.index') {
                    //Override view index hide partials.footer
                    \View::prependNamespace('admin', __DIR__ . '/../resources/views/index');

                    Admin::css(IframeTabs::config('tabs_css', 'vendor/laravel-admin-ext/iframe-tabs/dashboard.css'));

                    $layout = config('admin.layout', ['fixed']);
                    if (count($layout) == 1) { //['fixed sidebar-mini']
                        $layout = explode(' ', $layout[0]);
                    }
                    //['fixed', 'sidebar-mini']
                    if (count($layout) && !in_array('layout-boxed', $layout) && !in_array('fixed', $layout)) {

                        array_push($layout, 'fixed');

                        config(['admin.layout' => $layout]);
                    }
                } else {

                    $this->initSubPage();

                    //Override view content hide partials.header and partials.sidebar
                    \View::prependNamespace('admin', __DIR__ . '/../resources/views/content');
                    //add scritp 'Back to top' in content
                    $this->contentScript();

                    //Override content style ,reset style of '#pjax-container' margin-left:0
                    Admin::css('vendor/laravel-admin-ext/iframe-tabs/content.css');

                    config(['admin.layout' => ['fixed']]); // iframe page no need layout ,set default to fixed .
                }

                config(['admin.minify_assets' => false]);
            });
        }
    }

    protected function initSubPage()
    {
        if (!in_array(IframeTabs::config('bind_urls', 'none'), ['new_tab', 'popup'])) {
            return;
        }

        $method = strtolower(request()->method());
        $session = request()->session();

        if ($method == 'get') {
            $_ifraem_id_ = $session->pull('_ifraem_id_', '');
            $after_save = $session->pull('after_save', '');
            if ($_ifraem_id_ && $session->has('toastr')) {

                if ($session->has('toastr')) {
                    $toastr = $session->get('toastr');
                    $type = Arr::get($toastr->get('type'), 0, 'success');
                    $message = Arr::get($toastr->get('message'), 0, '');

                    if ($type == 'success') {
                        $session->put('_list_ifraem_id_', $_ifraem_id_);
                        $session->put('_list_after_save_', $after_save);
                        $session->put('_success_message_', $message);
                    }
                }
            }
        } else if ($method == 'put' || $method == 'post') {

            $post_ifraem_id_ = request()->input('_ifraem_id_', '');

            $post_after_save = request()->input('after-save', '');

            if ($post_ifraem_id_) {
                $session->put('_ifraem_id_', $post_ifraem_id_);
            } else {
                $session->forget('_ifraem_id_');
            }

            if ($post_after_save) {
                $session->put('after_save', $post_after_save);
            } else {
                $session->forget('after_save');
            }
        }
    }

    protected function contentScript()
    {
        $session = request()->session();

        $_pjax = request()->input('_pjax', '');

        $_ifraem_id_ = request()->input('_ifraem_id_', '');
        $_list_ifraem_id_ = $session->pull('_list_ifraem_id_', '');
        $_success_message_ = $session->pull('_success_message_', 'success');
        $_list_after_save_ = $session->pull('_list_after_save_', '');
        $layer_size = IframeTabs::config('layer_size', '1100px,98%');

        $script = <<<EOT

        var _ifraem_id_ = '{$_ifraem_id_}';

        var _pjax = '{$_pjax}';

        var _list_ifraem_id_ = '{$_list_ifraem_id_}';

        var _list_after_save_ = '{$_list_after_save_}';
        
        var layer_size = window.layer_size || '{$layer_size}';

        var bind_urls = window.bind_urls || top.bind_urls;

        var var bind_selecter = window.bind_selecter || top.bind_selecter;

        window.Pops = [];

        if (_list_ifraem_id_ && !_list_after_save_)
        {
            var iframes = top.document.getElementsByTagName("iframe");
            for(var i in iframes)
            {
                if (iframes[i].id == _list_ifraem_id_)
                {
                    var openner = iframes[i].contentWindow;

                    openner.$.pjax.reload('#pjax-container');

                    if (bind_urls =='new_tab')
                    {
                        var tab_id = getCurrentId();
                        if(tab_id)
                        {
                            top.toastr.success('{$_success_message_}');
                            top.closeTabByPageId(tab_id.replace(/^iframe_/i, ''));
                            doStop();
                        }
                    }
                    else if (bind_urls =='popup')
                    {
                        var index = parent.layer.getFrameIndex(window.name);
                        if(index)
                        {
                            top.toastr.success('{$_success_message_}');
                            parent.layer.close(index);
                            doStop();
                        }
                    }

                    break;
                }
            }
            return;
        }

        if(_ifraem_id_ && $('form').size())
        {
            $('form').append('<input type="hidden" name="_ifraem_id_" value="' + _ifraem_id_ + '" />');
        }

        if(!_pjax)
        {
            $('body').addClass('iframe-content');

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

            if ((bind_urls =='new_tab' || bind_urls =='popup') && bind_selecter)
            {
                
                $('body').on('click', bind_selecter, function() {
                    var url = $(this).attr('href');
                    if (!url || url == '#' || /^javascript|\(|\)/i.test(url)) {
                        return;
                    }

                    if ($(this).attr('target') == '_blank') {
                        return;
                    }

                    if ($(this).hasClass('iframes-pass-url')) {
                        return;
                    }

                    var icon = '<i class="fa fa-file-text"></i>';
                    if ($(this).find('i.fa').size()) {
                        icon = $(this).find('i.fa').prop("outerHTML");
                    }

                    var title = ($(this).text() || $(this).attr('title') || '').trim();

                    var tab_id = getCurrentId();

                    if(!tab_id)
                    {
                    // return true;
                    }

                    url += (url.indexOf('?')>-1? '&':'?') + '_ifraem_id_=' + tab_id;

                    tab_id = tab_id.replace(/^iframe_(.+)$/ ,'$1');

                    var tab = top.findTabTitle(tab_id);

                    if (!tab)
                    {
                        //return true;
                    }

                    if(tab)
                    {
                        title = ' ' + tab.text() + (title ? '-' + title : '');
                    }

                    if(bind_urls == 'popup')
                    {
                        var area = layer_size.split(',');
                        var popw = $(this).attr('popw');
                        var poph = $(this).attr('poph');
                        if(popw && poph)
                        {
                            area = [popw, poph];
                        }
                        openPop(url, icon + title, area);
                    }
                    else
                    {
                        top.openTab(url, title || '*', icon);
                    }

                    var toggle = false;
                    if ($(this).parents('.grid-dropdown-actions').size() && (toggle = $(this).parents('.grid-dropdown-actions').find('.dropdown-toggle'))) {
                        toggle.trigger('click');
                    }

                    return false;
                });
            }

            window.getCurrentId = function()
            {
                var iframes = top.document.getElementsByTagName("iframe");
                for(var i in iframes)
                {
                    if (iframes[i].contentWindow == window)
                    {
                        return '' + iframes[i].id;
                    }
                }
                return '';
            }

            window.doStop = function()
            {
                if(!!(window.attachEvent && !window.opera)){
                    document.execCommand("stop");
                }
                else {
                    window.stop();
                }
            }

            window.openPop = function(url, title ,area) {
                if (!area) {
                    area = ['100%', '100%'];
                }
                var index = layer.open({
                    content: url,
                    type: 2,
                    title: title,
                    anim: 2,
                    closeBtn: 1,
                    shade: false,
                    area: area,
                });

                window.Pops.push(index);

                return index;
            }

            window.closePop = function()
            {
                var index = parent.layer.getFrameIndex(window.name);
                parent.layer.close(index);
            }

            window.closeTab = function()
            {
                var tab_id = getCurrentId();
                if(tab_id)
                {
                    top.closeTabByPageId(tab_id.replace(/^iframe_/i, ''));
                    doStop();
                }
            }
        }
EOT;
        Admin::script($script);
    }
}
