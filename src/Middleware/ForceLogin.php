<?php

namespace Ichynul\IframeTabs\Middleware;

use Closure;
use Ichynul\IframeTabs\IframeTabs;

class ForceLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if (!IframeTabs::boot()) {
            return $response;
        }

        $content = $response->getContent();
        $message = trans('admin.iframe_tabs.goto_login');
        $script = <<<EOT
        
    <script>
        if (window != top) {
            top.location.reload();
            
            document.querySelector('body').innerHTML = 

            '<div style="background:#fff;z-index:999;padding-top:88px;position:fixed;top:0px;height:10000px;width:100%;text-align:center;font-size:18px;"><p>{$message}</p></div>';
            
            if(!!(window.attachEvent && !window.opera)){
                document.execCommand("stop");
            }
            else {
                window.stop();
            }
        }
    </script>

EOT;
        $response->setContent(preg_replace('/<body([^>]*)>/i', '<body$1>' . $script, $content));
        \Session::forget('url.intended');
        return $response;
    }
}
