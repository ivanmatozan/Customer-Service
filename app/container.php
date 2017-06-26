<?php

$container = $app->getContainer();

// Authentication
$container['auth'] = function () {
    return new App\Services\Authentication();
};

// Twig
$container['view'] = function ($container) {
    $twig = new \Slim\Views\Twig(__DIR__ . '/../resources/views');
    $twig->addExtension(new \Slim\Views\TwigExtension(
        $container->router,
        $container->request->getUri()
    ));

    // Add user as global variable
    $twig->getEnvironment()->addGlobal('user', $container->auth->getUser());

    return $twig;
};

// Validator
$container['validator'] = function () {
    return new App\Validation\Validator();
};