<?php

// Start session
session_start();

// Composer autoload
require __DIR__ . '/../vendor/autoload.php';

// Load PHP dotenv
$dotenv = new \Dotenv\Dotenv(__DIR__ . '/../');
$dotenv->load();

// Instantiate Slim application
$app = new Slim\App([
    'settings' => [
        'displayErrorDetails' => $_ENV['APP_DEBUG']
    ]
]);

// Instantiate Eloquent
$capsule = new Illuminate\Database\Capsule\Manager();
$capsule->addConnection([
    'driver'    => $_ENV['DB_DRIVER'],
    'host'      => $_ENV['DB_HOST'],
    'database'  => $_ENV['DB_DATABASE'],
    'username'  => $_ENV['DB_USERNAME'],
    'password'  => $_ENV['DB_PASSWORD'],
    'charset'   => $_ENV['DB_CHARSET'],
    'collation' => $_ENV['DB_COLLATION'],
    'prefix'    => $_ENV['DB_PREFIX']
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

// Set custom rules for Respect validator
Respect\Validation\Validator::with('App\\Validation\\Rules\\');

// Container
require __DIR__ . '/container.php';

// Attach Middleware
$app->add(new \App\Middleware\ValidationErrorsMiddleware($container));
$app->add(new \App\Middleware\OldFormDataMiddleware($container));

// Routes
require __DIR__ . '/routes.php';