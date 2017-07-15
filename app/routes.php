<?php

$app->get('/', 'App\Controllers\HomeController:index')->setName('home');

// Logged in users can't access login and register forms
$app->group('', function () {
    $this->get('/register', 'App\Controllers\UserController:getRegister')->setName('user.register');
    $this->post('/register', 'App\Controllers\UserController:postRegister');
    $this->get('/login', 'App\Controllers\UserController:getLogin')->setName('user.login');
    $this->post('/login', 'App\Controllers\UserController:postLogin');
})->add(new \App\Middleware\Auth\GuestMiddleware($container));

// Only logged in user can logout and access profile view
$app->group('', function () {
    $this->get('/logout', 'App\Controllers\UserController:logout')->setName('user.logout');
    $this->get('/profile', 'App\Controllers\UserController:profile')->setName('user.profile');
    $this->get('/profile/edit', 'App\Controllers\UserController:getEdit')->setName('user.edit');
    $this->post('/profile/edit', 'App\Controllers\UserController:postEdit');
})->add(new \App\Middleware\Auth\AuthMiddleware($container));

// Only webadmin can access user-management
$app->group('/user-management', function () {
    $this->get('', 'App\Controllers\UserManagementController:show')->setName('user-management.show');
    $this->get('/create', 'App\Controllers\UserManagementController:getCreate')->setName('user-management.create');
    $this->post('/create', 'App\Controllers\UserManagementController:postCreate');
    $this->get('/edit/{id}', 'App\Controllers\UserManagementController:getEdit')->setName('user-management.edit');
    $this->post('/edit/{id}', 'App\Controllers\UserManagementController:postEdit');
    $this->get('/delete/{id}', 'App\Controllers\UserManagementController:confirmDelete')->setName('user-management.delete');
    $this->post('/delete/{id}', 'App\Controllers\UserManagementController:delete');
})->add(new \App\Middleware\Auth\RoleMiddleware($container, ['webadmin']))
    ->add(new \App\Middleware\Auth\AuthMiddleware($container));

// Only admin and user can see question(s)
$app->group('/questions', function () {
    $this->get('', 'App\Controllers\QuestionController:list')->setName('question.list');
    $this->get('/show/{id}', 'App\Controllers\QuestionController:show')->setName('question.show');
})->add(new \App\Middleware\Auth\RoleMiddleware($container, ['admin', 'user']))
    ->add(new \App\Middleware\Auth\AuthMiddleware($container));

// Only user can create new question
$app->group('/questions', function () {
    $this->get('/create', 'App\Controllers\QuestionController:getCreate')->setName('question.create');
    $this->post('/create', 'App\Controllers\QuestionController:postCreate');
})->add(new \App\Middleware\Auth\RoleMiddleware($container, ['user']))
    ->add(new \App\Middleware\Auth\AuthMiddleware($container));

// Only user can edit|delete|close question
$app->group('/questions', function () {
    $this->get('/edit/{id}', 'App\Controllers\QuestionController:getEdit')->setName('question.edit');
    $this->post('/edit/{id}', 'App\Controllers\QuestionController:postEdit');
    $this->get('/delete/{id}', 'App\Controllers\QuestionController:confirmDelete')->setName('question.delete');
    $this->post('/delete/{id}', 'App\Controllers\QuestionController:delete');
    $this->get('/close/{id}', 'App\Controllers\QuestionController:confirmClose')->setName('question.close');
    $this->post('/close/{id}', 'App\Controllers\QuestionController:close');
})->add(new \App\Middleware\Auth\RoleMiddleware($container, ['user']))
    ->add(new \App\Middleware\Auth\AuthMiddleware($container));

// Only admin and user can create|edit|delete response
$app->group('', function () {
    $this->post('/questions/{id}/response', 'App\Controllers\ResponseController:postCreate')->setName('response.create');
    $this->get('/response/edit/{id}', 'App\Controllers\ResponseController:getEdit')->setName('response.edit');
    $this->post('/response/edit/{id}', 'App\Controllers\ResponseController:postEdit');
    $this->get('/response/delete/{id}', 'App\Controllers\ResponseController:confirmDelete')->setName('response.delete');
    $this->post('/response/delete/{id}', 'App\Controllers\ResponseController:delete');
})->add(new \App\Middleware\Auth\RoleMiddleware($container, ['admin', 'user']))
    ->add(new \App\Middleware\Auth\AuthMiddleware($container));
