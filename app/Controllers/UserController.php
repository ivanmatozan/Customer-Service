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

    /**
     * Display user profile page
     *
     * @param Request $request
     * @param Response $response
     */
    public function profile(Request $request, Response $response)
    {
        // Currently logged in user
        $user = $this->auth->getUser();

        return $this->view->render($response, 'user/profile.twig', compact('user'));
    }

    /**
     * Display edit profile form
     *
     * @param Request $request
     * @param Response $response
     */
    public function getEdit(Request $request, Response $response)
    {
        $user = $this->auth->getUser();

        return $this->view->render($response, 'user/edit.twig', compact('user'));
    }

    /**
     * Process edit profile form
     *
     * @param Request $request
     * @param Response $response
     */
    public function postEdit(Request $request, Response $response)
    {
        // Currently logged in user
        $user = $this->auth->getUser();

        $uploadedFiles = $request->getUploadedFiles();
        $uploadedFile = $uploadedFiles['image'];

        // Validation rules
        $rules = [];

        // Check if image is set
        if (!empty($uploadedFile->file)) {
            $rules['image'] = v::image()->size(null, '100KB');
        }

        // Check if name is changed
        if ($request->getParam('name') !== $user->name) {
            $rules['name'] = v::notEmpty();

            $user->name = $request->getParam('name');
        }

        // Check if email is changed
        if ($request->getParam('email') !== $user->email) {
            $rules['email'] = v::notEmpty()->email()->emailAvailable();

            $user->email = $request->getParam('email');
        }

        // Check if password is changed
        if (!empty($request->getParam('password'))) {
            $rules['password'] = v::length(6, null);
            $rules['confirm_password'] = v::notEmpty()->confirmPassword($request->getParam('password'));

            $user->password = password_hash($request->getParam('password'), PASSWORD_DEFAULT);
        }

        // Don't try to validate and save if nothing changed
        if (!empty($rules)) {
            $data = $request->getParams();
            $data['image'] = $uploadedFile->file;

            // Validate form data
            $validator = $this->validator->validate($data, $rules);

            // Redirect back to form if validation failed
            if ($validator->failed()) {
                // Add input data to session
                $_SESSION['old_form_data'] = $request->getParams();

                return $response->withRedirect($this->router->pathFor('user.edit'));
            }

            // File upload
            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                $directory = $this->container->get('upload_directory');

                $fileName = $this->upload->moveUploadedFile($directory, $uploadedFile);

                // Delete old file
                if ($user->image) {
                    unlink($directory . $user->image);
                }

                $user->image = $fileName;
            }

            // Save changes to DB
            $user->save();

            $this->flash->addMessage('success', 'Profile successfully updated.');
        }

        return $response->withRedirect($this->router->pathFor('user.profile'));
    }
}