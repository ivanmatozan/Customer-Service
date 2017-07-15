<?php

namespace App\Middleware\Role;

use App\Middleware\Middleware;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class WebAdminMiddleware extends Middleware
{
    /**
     * Check if user has role webadmin
     *
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next): Response
    {
        $userRole = $this->auth->getUser()->role->name;

        if ($userRole != 'webadmin') {
            return $response->withRedirect($this->router->pathFor('home'));
        }

        return $next($request, $response);
    }
}