<?php

$container = $app->getContainer();

// Twig
$container['view'] = function ($container) {
    $twig = new \Slim\Views\Twig(__DIR__ . '/../resources/views');
    $twig->addExtension(new \Slim\Views\TwigExtension(
        $container->router,
        $container->request->getUri()
    ));

    return $twig;
};