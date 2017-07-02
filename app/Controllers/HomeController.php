<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Home controller
 */
class HomeController extends Controller
{
    /**
     * Display home page
     *
     * @param Request $request
     * @param Response $response
     */
    public function index(Request $request, Response $response)
    {
        return $this->view->render($response, 'home/index.twig');
    }
}