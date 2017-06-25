<?php

namespace App\Controllers;

/**
 * Home controller
 */
class HomeController extends Controller
{
    /**
     * Display home page
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     */
    public function index($request, $response)
    {
       return $this->view->render($response, 'home/index.twig');
    }
}