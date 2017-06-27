<?php

namespace App\Controllers\Auth;

use App\Controllers\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Respect\Validation\Validator as v;

/**
 * Login controller
 */
class LoginController extends Controller
{
    /**
     * Display login form
     *
     * @param Request $request
     * @param Response $response
     */
    public function getLogin(Request $request, Response $response)
    {
        return $this->view->render($response, 'auth/login.twig');
    }

    /**
     * Process login form
     *
     * @param Request $request
     * @param Response $response
     */
    public function postLogin(Request $request, Response $response)
    {
        $validator = $this->validator->validate($request, [
            'email' => v::notEmpty()->email(),
            'password' => v::notEmpty()
        ]);

        // Redirect back to login form if validation fails
        if ($validator->failed()) {
            return $response->withRedirect($this->router->pathFor('auth.login'));
        }

        // Attempt to authenticate user
        $auth = $this->auth->attempt($request->getParam('email'), $request->getParam('password'));

        if (!$auth) {
            $this->flash->addMessage('error', 'Unsuccessful login, email or password do not match.');

            return $response->withRedirect($this->router->pathFor('auth.login'));
        }

        return $response->withRedirect($this->router->pathFor('home'));
    }

    /**
     * Logout user
     *
     * @param Request $request
     * @param Response $response
     */
    public function logout(Request $request, Response $response)
    {
        $this->auth->logout();

        return $response->withRedirect($this->router->pathFor('home'));
    }
}