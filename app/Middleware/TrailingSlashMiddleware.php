<?php

namespace App\Middleware;

/**
 * This middleware fixes trailing slash problem i routes
 * https://www.slimframework.com/docs/cookbook/route-patterns.html
 */
class TrailingSlashMiddleware
{
    function __invoke($request, $response, $next)
    {
        $uri = $request->getUri();
        $path = $uri->getPath();
        if ($path != '/' && substr($path, -1) == '/') {
            // permanently redirect paths with a trailing slash
            // to their non-trailing counterpart
            $uri = $uri->withPath(substr($path, 0, -1));

            if($request->getMethod() == 'GET') {
                return $response->withRedirect((string)$uri, 301);
            }
            else {
                return $next($request->withUri($uri), $response);
            }
        }

        $response = $next($request, $response);

        return $response;
    }
}