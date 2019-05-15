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

## 1.0.28
新版本1.0.28布局改动较大，更新版本后记得强制发布资源 

The layout of the new version 1.0.28 has changed a lot. After updating the version, remember to force release resources.

## Update it

(本扩展依赖一些 js 和 css 文件，composer update 若版本号有变请强制发布资源，可能是更新了某些样式)

After `composer update` , if version of this extension changed :

Run

```
php artisan vendor:publish --tag=iframe-tabs --force
```

This will override css and js fiels to `/public/vendor/laravel-admin-ext/iframe-tabs/`

Or you can and a script in `composer.json` :

```json
"scripts": {
    "post-update-cmd": "php artisan vendor:publish --tag=iframe-tabs --force",
}
```

## Usage

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
            // layer.js path , if you do not use laravel-admin-ext\cropper , set another one
            'layer_path' => 'vendor/laravel-admin-ext/cropper/layer/layer.js',
            /**
             * href links do not open in tab .
             * selecter : .sidebar-menu li a,.navbar-nav>li a,.sidebar .user-panel a,.sidebar-form .dropdown-menu li a
             * if(href.indexOf(pass_urls[i]) > -1) //pass
             */
            'pass_urls' => ['/auth/logout', '/auth/lock'],
            // When login session state of a tab-page was expired , force top-level window goto login page .
            'force_login_in_top' => true,
            // tabs left offset
            'tabs_left'  => 42,
        ]
    ],

```

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
    'scroll_current' => '滚动到当前'
],
```

## Usage

Open `http://your-host/admin`

Thanks to https://github.com/bswsfhcw/AdminLTE-With-Iframe

License

---

Licensed under [The MIT License (MIT)](LICENSE).
