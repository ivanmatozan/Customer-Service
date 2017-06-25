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

// Container
require __DIR__ . '/../app/container.php';

// Routes
require __DIR__ . '/../app/routes.php';