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

(本扩展依赖一些js和css文件，composer update 若版本号有变请强制发布资源，可能是更新了某些样式)

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
            // Default page controller
            'home_action' => App\Admin\Controllers\HomeController::class . '@index',
            // Default page uir after user login success
            'home_uri' => '/admin/dashboard',
            // Default page tab-title
            'home_title' => 'Home',
            // Default page tab-title icon
            'home_icon' => 'fa-home',
            // wheath show icon befor titles for all tab
            'use_icon' => true,
            // layer.js path , if you do not use laravel-admin-ext\cropper , set another one
            'layer_path' => '/vendor/laravel-admin-ext/cropper/layer/layer.js'
        ]
    ],

```

Edit existing menu `index`
```php
[
    'title'     => 'Index',
    'icon'      => 'fa-bar-chart',
    //'uri'       => '/', //old
    'uri'       => '/admin/dashboard',   // new
]
```

Add a lang config in `resources/lang/{zh-CN}/admin.php`

```php
'iframe_tabss' => [
    'oprations' => '页签操作',
    'refresh_current' => '刷新当前',
    'close_current' => '关闭当前',
    'close_all' => '关闭全部',
    'close_other' => '关闭其他',
    'open_in_new' => '新窗口打开',
    'open_in_pop' => '弹出窗打开'
],
```

## Usage

Open `http://your-host/admin`

Thanks to https://github.com/bswsfhcw/AdminLTE-With-Iframe

License

---

Licensed under [The MIT License (MIT)](LICENSE).
