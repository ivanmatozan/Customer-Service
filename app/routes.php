<?php

$app->get('/', 'App\Controllers\HomeController:index')->setName('home');

$app->get('/register', 'App\Controllers\Auth\RegisterController:getRegister')->setName('auth.register');
$app->post('/register', 'App\Controllers\Auth\RegisterController:postRegister');