<?php

namespace App\Middleware;

/**
 * Check if form validation errors exist in session and
 * sets them to Twig's global variable
 */
class ValidationErrorsMiddleware extends Middleware
{
    function __invoke($request, $response, $next)
    {
        if (!empty($_SESSION['validation_errors'])) {
            $twig = $this->container->view;
            $twig->getEnvironment()->addGlobal('validationErrors', $_SESSION['validation_errors']);

            unset($_SESSION['validation_errors']);
        }

        $response = $next($request, $response);

        return $response;
    }
}