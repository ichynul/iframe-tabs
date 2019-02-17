# laravel-admin iframe-tabs

## Installation

Then run :

```
$ composer require ichynul/iframe-tabs
```

Then run:

```
$ php artisan vendor:publish --provider=Ichynul\IframeTabs\IframeTabsServiceProvider
```

Add a config in `config/admin.php`:

```php
    'extensions' => [
        'iframe-tabs' => [
            // Set to `false` if you want to disable this extension
            'enable' => true,
            // Default page uir after user login success
            'home_uri' => '/admin',
            // Default page tab-title
            'home_title' => 'Home',
            // Default page tab-title icon
            'home_icon' => 'fa-home',
            // wheath show icon befor titles for all tab
            'use_icon' => true,
        ]
    ],

```

Add a lang config in `resources/lang/{zh-CN}/admin.php`

```php
'iframe_tabss' => [
    'oprations' => '页签操作',
    'refresh_current' => '刷新当前',
    'close_current' => '关闭当前',
    'close_all' => '关闭全部',
    'close_other' => '关闭其他',
    'open_in_new' => '新窗口打开'
],
```

## Usage

Open `http://your-host/admin/dashboard`

To make sure open dashboard page after user login successed ,you need to edit `App\Admin\Controllers\AuthController.php` :
    add line `protected $redirectTo = '/admin/dashboard'`;
## Demo

```php
namespace App\Admin\Controllers;

use Encore\Admin\Controllers\AuthController as BaseAuthController;

class AuthController extends BaseAuthController
{
    protected $redirectTo = '/admin/dashboard';
}
```

Thanks to https://github.com/bswsfhcw/AdminLTE-With-Iframe

License

---

Licensed under [The MIT License (MIT)](LICENSE).
