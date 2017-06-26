<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;

/**
 * Base controller
 */
abstract class Controller
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