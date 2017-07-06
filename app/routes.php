<?php

$app->get('/', 'App\Controllers\HomeController:index')->setName('home');

$app->get('/register', 'App\Controllers\Auth\RegisterController:getRegister')->setName('auth.register');
$app->post('/register', 'App\Controllers\Auth\RegisterController:postRegister');

$app->get('/login', 'App\Controllers\Auth\LoginController:getLogin')->setName('auth.login');
$app->post('/login', 'App\Controllers\Auth\LoginController:postLogin');

$app->get('/logout', 'App\Controllers\Auth\LoginController:logout')->setName('auth.logout');

$app->get('/users', 'App\Controllers\UserController:show')->setName('user.show');
$app->get('/users/create', 'App\Controllers\UserController:getCreate')->setName('user.create');
$app->post('/users/create', 'App\Controllers\UserController:postCreate');
$app->get('/users/edit/{id}', 'App\Controllers\UserController:getEdit')->setName('user.edit');
$app->post('/users/edit/{id}', 'App\Controllers\UserController:postEdit');
$app->get('/users/delete/{id}', 'App\Controllers\UserController:confirmDelete')->setName('user.delete');
$app->post('/users/delete/{id}', 'App\Controllers\UserController:delete');

$app->get('/questions/all[/{tab}]', 'App\Controllers\QuestionController:allQuestions')->setName('question.all-questions');
$app->get('/questions/list[/{tab}]', 'App\Controllers\QuestionController:userQuestions')->setName('question.user-questions');
$app->get('/questions/create', 'App\Controllers\QuestionController:getCreate')->setName('question.create');
$app->post('/questions/create', 'App\Controllers\QuestionController:postCreate');
$app->get('/questions/edit/{id}', 'App\Controllers\QuestionController:getEdit')->setName('question.edit');
$app->post('/questions/edit/{id}', 'App\Controllers\QuestionController:postEdit');
//$app->get('/questions/delete/{id}', 'App\Controllers\QuestionController:confirmDelete')->setName('question.delete');
//$app->post('/questions/delete/{id}', 'App\Controllers\QuestionController:delete');
$app->get('/questions/close/{id}', 'App\Controllers\QuestionController:confirmClose')->setName('question.close');
$app->post('/questions/close/{id}', 'App\Controllers\QuestionController:close');
$app->get('/questions/show/{id}', 'App\Controllers\QuestionController:show')->setName('question.show');

$app->post('/questions/{id}/response', 'App\Controllers\ResponseController:postCreate')->setName('response.create');