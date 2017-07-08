<?php

namespace App\Controllers;

use App\Models\Role;
use App\Models\User;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Respect\Validation\Validator as v;

/**
 * User Controller
 */
class UserController extends Controller
{
    /**
     * Display user registration form
     *
     * @param Request $request
     * @param Response $response
     */
    public function getRegister(Request $request, Response $response)
    {
        return $this->view->render($response, 'user/register.twig');
    }

    /**
     * Process user registration form
     *
     * @param Request $request
     * @param Response $response
     */
    public function postRegister(Request $request, Response $response)
    {
        // Validate form data
        $validator = $this->validator->validate($request->getParams(), [
            'name' => v::notEmpty()->alpha(),
            'email' => v::notEmpty()->email()->emailAvailable(),
            'password' => v::notEmpty()->length(6, null),
            'confirm_password' => v::notEmpty()->confirmPassword($request->getParam('password'))
        ]);

        // Redirect back to form if validation failed
        if ($validator->failed()) {
            // Add input data to session
            $_SESSION['old_form_data'] = $request->getParams();

            return $response->withRedirect($this->router->pathFor('user.register'));
        }

        // Insert into DB after validation passed
        User::create([
            'name' => $request->getParam('name'),
            'email' => $request->getParam('email'),
            'password' => password_hash($request->getParam('password'), PASSWORD_DEFAULT),
            'role_id' => Role::where('name', 'user')->first()->id
        ]);

        $this->flash->addMessage('success', 'Account successfully created. Please login.');

        return $response->withRedirect($this->router->pathFor('home'));
    }

    /**
     * Display login form
     *
     * @param Request $request
     * @param Response $response
     */
    public function getLogin(Request $request, Response $response)
    {
        return $this->view->render($response, 'user/login.twig');
    }

    /**
     * Process login form
     *
     * @param Request $request
     * @param Response $response
     */
    public function postLogin(Request $request, Response $response)
    {
        $validator = $this->validator->validate($request->getParams(), [
            'email' => v::notEmpty()->email(),
            'password' => v::notEmpty()
        ]);

        // Redirect back to login form if validation fails
        if ($validator->failed()) {
            return $response->withRedirect($this->router->pathFor('user.login'));
        }

        // Attempt to authenticate user
        $auth = $this->auth->attempt($request->getParam('email'), $request->getParam('password'));

        if (!$auth) {
            $this->flash->addMessage(
                'error',
                'Unsuccessful login, email or password do not match with any user in our Database.'
            );

            return $response->withRedirect($this->router->pathFor('user.login'));
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