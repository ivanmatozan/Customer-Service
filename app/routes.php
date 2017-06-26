<?php

$app->get('/', 'App\Controllers\HomeController:index')->setName('home');

$app->get('/register', 'App\Controllers\Auth\RegisterController:getRegister')->setName('auth.register');
$app->post('/register', 'App\Controllers\Auth\RegisterController:postRegister');

$app->get('/login', 'App\Controllers\Auth\LoginController:getLogin')->setName('auth.login');
$app->post('/login', 'App\Controllers\Auth\LoginController:postLogin');

$app->get('/logout', 'App\Controllers\Auth\LoginController:logout')->setName('auth.logout');
