<?php

namespace App\Controllers;

use App\Models\Role;
use App\Models\User;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Respect\Validation\Validator as v;

/**
 * UserManagement Controller
 */
class UserManagementController extends Controller
{
    /**
     * Roles to show in dropdown at create|edit user form
     *
     * @var array
     */
    protected $rolesToShow = [
        'admin',
        'user'
    ];

    /**
     * Display user management view
     *
     * @param Request $request
     * @param Response $response
     */
    public function show(Request $request, Response $response)
    {
        $users = User::whereHas('role', function ($query) {
            $query->where('name', '!=', 'webadmin');
        })->get();

        return $this->view->render($response, 'user-management/show.twig', compact('users'));
    }

    /**
     * Display form for creating new user
     *
     * @param Request $request
     * @param Response $response
     */
    public function getCreate(Request $request, Response $response)
    {
        $roles = Role::whereIn('name', $this->rolesToShow)->get();

        return $this->view->render($response, 'user-management/create.twig', compact('roles'));
    }

    /**
     * Process create new user form
     *
     * @param Request $request
     * @param Response $response
     */
    public function postCreate(Request $request, Response $response)
    {
        // Validate form data
        $validator = $this->validator->validate($request->getParams(), [
            'role' => v::notEmpty(),
            'name' => v::notEmpty(),
            'email' => v::notEmpty()->email()->emailAvailable(),
            'password' => v::notEmpty()->length(6, null),
            'confirm_password' => v::notEmpty()->confirmPassword($request->getParam('password'))
        ]);

        // Redirect back to form if validation failed
        if ($validator->failed()) {
            // Add input data to session
            $_SESSION['old_form_data'] = $request->getParams();

            return $response->withRedirect($this->router->pathFor('user-management.create'));
        }

        // Insert into DB after validation passed
        User::create([
            'role_id' => $request->getParam('role'),
            'name' => $request->getParam('name'),
            'email' => $request->getParam('email'),
            'password' => password_hash($request->getParam('password'), PASSWORD_DEFAULT),
        ]);


        $this->flash->addMessage('success', 'New user successfully created.');

        return $response->withRedirect($this->router->pathFor('user-management.show'));
    }

    /**
     * Display edit user form
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     */
    public function getEdit(Request $request, Response $response, $args)
    {
        $roles = Role::whereIn('name', $this->rolesToShow)->get();
        $user = User::find($args['id']);

        if ($user) {
            // If user exists display edit form
            return $this->view->render($response, 'user-management/edit.twig', compact('roles', 'user'));
        }

        return $response->withRedirect($this->router->pathFor('user-management.show'));
    }

    /**
     * Process edit user form
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     */
    public function postEdit(Request $request, Response $response, $args)
    {
        $user = User::find($args['id']);

        // Validation rules
        $rules = [];

        // Validate role
        $rules['role'] = v::notEmpty();
        $user->role_id = $request->getParam('role');

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

        // Don't try to validate and save if user doesn't exist
        // and if nothing changed
        if ($user && !empty($rules)) {
            // Validate form data
            $validator = $this->validator->validate($request->getParams(), $rules);

            // Redirect back to form if validation failed
            if ($validator->failed()) {
                // Add input data to session
                $_SESSION['old_form_data'] = $request->getParams();

                return $response->withRedirect($this->router->pathFor('user-management.edit', ['id' => $args['id']]));
            }

            // Update user in DB
            $user->save();

            $this->flash->addMessage('success', 'User successfully edited.');
        }

        return $response->withRedirect($this->router->pathFor('user-management.show'));
    }

    /**
     * Show delete confirmation
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     */
    public function confirmDelete(Request $request, Response $response, $args)
    {
        return $this->view->render($response, 'templates/confirmation.twig', [
            'id' => $args['id'],
            'routeName' => 'user-management.delete',
            'message' => 'Are you sure that you want to delete user?'
        ]);
    }

    /**
     * Process user delete
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     */
    public function delete(Request $request, Response $response, $args)
    {
        $user = User::find($args['id']);

        // Delete user
        if ($request->getParam('yes') && $user) {
            User::destroy($args['id']);

            $this->flash->addMessage('success', 'User successfully deleted.');
        }

        return $response->withRedirect($this->router->pathFor('user-management.show'));
    }
}