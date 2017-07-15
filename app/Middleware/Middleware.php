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

    /**
     * Get service from container
     *
     * @param string $property
     * @return mixed
     */
    public function __get(string $property)
    {
        if ($this->container->has($property)) {
            return $this->container->get($property);
        }
    }
}