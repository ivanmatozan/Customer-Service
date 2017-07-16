<?php

$container = $app->getContainer();

// Authentication
$container['auth'] = function () {
    return new App\Services\Authentication();
};

// Flash messages
$container['flash'] = function () {
    return new Slim\Flash\Messages();
};

// Upload directory
$container['upload_directory'] = __DIR__ . '/../public/uploads/';

// Twig
$container['view'] = function ($container) {
    $twig = new \Slim\Views\Twig(__DIR__ . '/../resources/views/');
    $twig->addExtension(new \Slim\Views\TwigExtension(
        $container->router,
        $container->request->getUri()
    ));

    // Add user as global variable
    $twig->getEnvironment()->addGlobal('auth', [
        'user' => $container->auth->getUser()
    ]);

    // Add flash messages as global variable
    $twig->getEnvironment()->addGlobal('flash', $container->flash->getMessages());

    return $twig;
};

// Validator
$container['validator'] = function () {
    return new App\Validation\Validator();
};

// File Upload
$container['upload'] = function () {
    return new \App\Services\FileUpload();
};

// Question helper
$container['helper'] = function ($container) {
    return new \App\Services\QuestionHelper($container);
};

