<?php

$app->get('/', 'App\Controllers\HomeController:index')->setName('home');

$app->get('/register', 'App\Controllers\UserController:getRegister')->setName('user.register');
$app->post('/register', 'App\Controllers\UserController:postRegister');
$app->get('/login', 'App\Controllers\UserController:getLogin')->setName('user.login');
$app->post('/login', 'App\Controllers\UserController:postLogin');
$app->get('/logout', 'App\Controllers\UserController:logout')->setName('user.logout');
$app->get('/profile', 'App\Controllers\UserController:profile')->setName('user.profile');
$app->get('/profile/edit/{id}', 'App\Controllers\UserController:getEdit')->setName('user.edit');
$app->post('/profile/edit/{id}', 'App\Controllers\UserController:postEdit');

$app->get('/user-management', 'App\Controllers\UserManagementController:show')->setName('user-management.show');
$app->get('/user-management/create', 'App\Controllers\UserManagementController:getCreate')->setName('user-management.create');
$app->post('/user-management/create', 'App\Controllers\UserManagementController:postCreate');
$app->get('/user-management/edit/{id}', 'App\Controllers\UserManagementController:getEdit')->setName('user-management.edit');
$app->post('/user-management/edit/{id}', 'App\Controllers\UserManagementController:postEdit');
$app->get('/user-management/delete/{id}', 'App\Controllers\UserManagementController:confirmDelete')->setName('user-management.delete');
$app->post('/user-management/delete/{id}', 'App\Controllers\UserManagementController:delete');

$app->get('/questions', 'App\Controllers\QuestionController:list')->setName('question.list');
$app->get('/questions/create', 'App\Controllers\QuestionController:getCreate')->setName('question.create');
$app->post('/questions/create', 'App\Controllers\QuestionController:postCreate');
$app->get('/questions/edit/{id}', 'App\Controllers\QuestionController:getEdit')->setName('question.edit');
$app->post('/questions/edit/{id}', 'App\Controllers\QuestionController:postEdit');
$app->get('/questions/delete/{id}', 'App\Controllers\QuestionController:confirmDelete')->setName('question.delete');
$app->post('/questions/delete/{id}', 'App\Controllers\QuestionController:delete');
$app->get('/questions/close/{id}', 'App\Controllers\QuestionController:confirmClose')->setName('question.close');
$app->post('/questions/close/{id}', 'App\Controllers\QuestionController:close');
$app->get('/questions/show/{id}', 'App\Controllers\QuestionController:show')->setName('question.show');

$app->post('/questions/{id}/response', 'App\Controllers\ResponseController:postCreate')->setName('response.create');
$app->get('/response/edit/{id}', 'App\Controllers\ResponseController:getEdit')->setName('response.edit');
$app->post('/response/edit/{id}', 'App\Controllers\ResponseController:postEdit');
$app->get('/response/delete/{id}', 'App\Controllers\ResponseController:confirmDelete')->setName('response.delete');
$app->post('/response/delete/{id}', 'App\Controllers\ResponseController:delete');