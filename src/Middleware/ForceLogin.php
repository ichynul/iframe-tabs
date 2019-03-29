<?php

namespace Ichynul\IframeTabs\Middleware;

use Closure;

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
        $content = $response->getContent();
        $script = <<<EOT
    <script>
        if (window != top) {
            top.location.reload();
        }
    </script>

EOT;
        $response->setContent(preg_replace('/<\/head>/i', $script . '</head>', $content));
        \Session::forget('url.intended');
        return $response;
    }
}
