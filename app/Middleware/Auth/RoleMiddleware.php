<?php

namespace App\Middleware\Auth;

use App\Middleware\Middleware;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class RoleMiddleware extends Middleware
{
    /**
     * Roles to check
     *
     * @var array
     */
    protected $roles;

    /**
     * RoleMiddleware constructor
     *
     * @param ContainerInterface $container
     * @param array $roles
     */
    function __construct(ContainerInterface $container, array $roles = [])
    {
        parent::__construct($container);

        $this->roles = $roles;
    }

    /**
     * Check user role
     *
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next): Response
    {
        $userRole = $this->auth->getUser()->role->name;

        if (!in_array($userRole, $this->roles)) {
            return $response->withRedirect($this->router->pathFor('home'));
        }

        return $next($request, $response);
    }
}