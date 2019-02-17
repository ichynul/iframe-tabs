<?php

namespace Ichynul\IframeTabs;

use Encore\Admin\Extension;

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
        parent::createPermission('Tab-dashboard', 'tabs.dashboard', '/admin/dashboard');
    }
}