<?php

namespace Laasti\Peels;

use Interop\Container\ContainerInterface;
use InvalidArgumentException;

class MiddlewareResolver implements MiddlewareResolverInterface
{
    protected $container;

    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function resolve($middlewareDefinition)
    {
        if (is_callable($middlewareDefinition)) {
            return $middlewareDefinition;
        } else if (!is_null($this->container) && $this->container->has($middlewareDefinition)) {
            return $this->container->get($middlewareDefinition);
        }

        throw new InvalidArgumentException('Middleware not resolvable: '.(is_object($middlewareDefinition) ? get_class($middlewareDefinition) : $middlewareDefinition));
    }
}
