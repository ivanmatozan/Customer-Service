<?php

namespace App\Middleware;

/**
 * Checks if old form input data exists in session
 * and sets it in Twig's global variable
 */
class OldFormDataMiddleware extends Middleware
{
    function __invoke($request, $response, $next)
    {
        if (!empty($_SESSION['old_form_data'])) {
            $twig = $this->container->view;
            $twig->getEnvironment()->addGlobal('oldData', $_SESSION['old_form_data']);

            unset($_SESSION['old_form_data']);
        }

        $response = $next($request, $response);

        return $response;
    }
}