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
