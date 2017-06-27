<?php

namespace App\Controllers\Auth;

use App\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Respect\Validation\Validator as v;

/**
 * Register controller
 */
class RegisterController extends Controller
{
    /**
     * Display user registration form
     *
     * @param Request $request
     * @param Response $response
     */
    public function getRegister(Request $request, Response $response)
    {
        return $this->view->render($response, 'auth/register.twig');
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
        $validator = $this->validator->validate($request, [
            'name' => v::notEmpty()->alpha(),
            'email' => v::notEmpty()->email()->emailAvailable(),
            'password' => v::notEmpty()->length(6, null),
            'confirm_password' => v::notEmpty()->confirmPassword($request->getParam('password'))
        ]);

        // Redirect back to form if validation failed
        if ($validator->failed()) {
            // Add input data to session
            $_SESSION['old_form_data'] = $request->getParams();

            return $response->withRedirect($this->router->pathFor('auth.register'));
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
}