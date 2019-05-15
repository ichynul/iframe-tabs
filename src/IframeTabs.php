<?php

namespace Ichynul\IframeTabs;

use Encore\Admin\Admin;
use Encore\Admin\Extension;
use Encore\Admin\Auth\Database\Menu;
use Encore\Admin\Auth\Database\Permission;

class IframeTabs extends Extension
{
    public $name = 'iframe-tabs';

    public $views = __DIR__ . '/../resources/views';

    public $assets = __DIR__ . '/../resources/assets';

    public static $manifestData = [];

    /**
     * {@inheritdoc}
     */
    public static function import()
    {
        if ($menu = Menu::where('uri', '/')->first()) {
            $menu->update(['uri' => 'dashboard']);
        }
        if (!Permission::where('slug', 'tabs.dashboard')->first()) {
            parent::createPermission('Tab-dashboard', 'tabs.dashboard', 'dashboard');
        }
    }

    public static function fixMinify()
    {
        if (!static::isMinify()) {
            return;
        }
        Admin::$baseJs = Admin::$baseCss = Admin::$css =  Admin::$js = [];

        Admin::js(static::getManifestData('js'));
        Admin::css(static::getManifestData('css'));
    }

    public static function isMinify()
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
    public static function getManifestData($key)
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
}
