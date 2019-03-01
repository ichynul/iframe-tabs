<?php

namespace Ichynul\IframeTabs;

use Encore\Admin\Extension;
use Encore\Admin\Auth\Database\Permission;
use Encore\Admin\Auth\Database\Menu;

class IframeTabs extends Extension
{
    public $name = 'iframe-tabs';

    public $views = __DIR__ . '/../resources/views';

    public $assets = __DIR__ . '/../resources/assets';

    /**
     * {@inheritdoc}
     */
    public static function import()
    {
        \Log::info(trans('admin.iframe_tabss'));
        if ($menu = Menu::where('uri', '/')->first()) {
            $menu->update(['uri' => 'dashboard']);
        }
        if (!Permission::where('slug', 'tabs.dashboard')->first()) {
            parent::createPermission('Tab-dashboard', 'tabs.dashboard', 'dashboard');
        }
    }
}
