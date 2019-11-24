<?php

namespace Ichynul\IframeTabs;

use Encore\Admin\Layout\Content;

class Index extends Content
{
    /**
     * Render this content.
     *
     * @return string
     */
    public function render()
    {
        $items = [
            'header' => $this->title,
            'description' => $this->description,
            'breadcrumb' => $this->breadcrumb,
            '_content_' => $this->build(),
            '_view_' => $this->view,
            '_user_' => $this->getUserData(),
        ];

        return view('iframe-tabs::ext.index', $items)->render();
    }
}
