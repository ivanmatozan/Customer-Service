<?php

namespace App\Middleware;

use Psr\Container\ContainerInterface;

/**
 * Base middleware
 */
abstract class Middleware
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
}