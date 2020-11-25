# laravel-admin iframe-tabs

## Installation

Run :

```
$ composer require ichynul/iframe-tabs
```

Then run:

```
$ php artisan vendor:publish --tag=iframe-tabs

$ php artisan admin:import iframe-tabs
```

## Update it

```
php artisan vendor:publish --tag=iframe-tabs --force
```

This will override css and js files to `/public/vendor/laravel-admin-ext/iframe-tabs/`

## Config

Add a config in `config/admin.php`:

```php
    'extensions' => [
        'iframe-tabs' => [
           // Set to `false` if you want to disable this extension
            'enable' => true,
            // The controller and action of dashboard page `/admin/dashboard`
            'home_action' => App\Admin\Controllers\HomeController::class . '@index',
            // Default page tab-title
            'home_title' => 'Home',
            // Default page tab-title icon
            'home_icon' => 'fa-home',
            // Whether show icon befor titles for all tab
            'use_icon' => true,
            // dashboard css
            'tabs_css' =>'vendor/laravel-admin-ext/iframe-tabs/dashboard.css',
            // layer.js path
            'layer_path' => 'vendor/laravel-admin-ext/iframe-tabs/layer/layer.js',
            /**
             * href links do not open in tab .
             * selecter : .sidebar-menu li a,.navbar-nav>li a,.sidebar .user-panel a,.sidebar-form .dropdown-menu li a
             * if(href.indexOf(pass_urls[i]) > -1) //pass
             */
            'pass_urls' => ['/auth/logout', '/auth/lock'],
            // When login session state of a tab-page was expired , force top-level window goto login page .
            //登录超时是是否强制整体跳转到登录页面，设为false的话只在触发超时登录的页面跳转，最大程度保留已打开页面。
            'force_login_in_top' => true,
            // tabs left offset
            'tabs_left'  => 42,
            // bind click event of table actions [edit / view / create]  
            'bind_urls' => 'popup', //[ popup / new_tab / none]
            //table actions dom selecter, [view / edit / create]buttons ,and any thing has class pupop : <a class="pupop" popw="400px" poph="200px" href="someurl">mylink</a>
            'bind_selecter' => 'a.grid-row-view,a.grid-row-edit,.column-__actions__ ul.dropdown-menu a,.box-header .pull-right .btn-success,.popup',
            //layer popup size
            'layer_size' => '1100px,98%',
            // if run web in `cli` mode ,for example `swoole` ,set it to true，如果是以命令行方式运行网站，如`swoole` 就设置为 true
            'web_in_cli' => false
        ]
    ],

```

 If `bind_urls` set to `popup` or `new_tab` , recommend `disableView` and `disableList` in form
    `/Admin/bootstrap.php`  :
```php
    Encore\Admin\Form::init(function ($form) {
        $form->tools(function ($tools) {
            $tools->disableDelete();
            $tools->disableView();
            $tools->disableList();
        });
    });
```
See https://laravel-admin.org/docs/zh/model-form-init

 
And `disableEdit` and `disableList` in show :
```php
   $show->panel()
   ->tools(function ($tools) {
       $tools->disableEdit();
       $tools->disableList();
       $tools->disableDelete();
   });;
```

## Lang


Add a lang config in `resources/lang/{zh-CN}/admin.php`

```php
'iframe_tabs' => [
    'oprations' => '页签操作',
    'refresh_current' => '刷新当前',
    'close_current' => '关闭当前',
    'close_all' => '关闭全部',
    'close_other' => '关闭其他',
    'open_in_new' => '新窗口打开',
    'open_in_pop' => '弹出窗打开',
    'scroll_left' => '滚动到最左',
    'scroll_right' => '滚动到最右',
    'scroll_current' => '滚动到当前',
    'goto_login' => '登录超时，正在跳转登录页面...'
],
```

## Usage

Open `http://your-host/admin`

Thanks to https://github.com/bswsfhcw/AdminLTE-With-Iframe

License

---

Licensed under [The MIT License (MIT)](LICENSE).

此扩展基本稳定了，除非`laravel-admin`的UI有较大变化。鉴于本人实际中使用`laravel-admin`不是太多，后续不提供新的功能特性，只修复bug。

## 广告

使用tp框架的小伙伴可以尝试一下我新的后台开发框架：https://gitee.com/ichynul/myadmin  

参照laravel-admih封装了`form`,`table`等
