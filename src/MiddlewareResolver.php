<?php

namespace Laasti\Peels;

use Interop\Container\ContainerInterface;
use InvalidArgumentException;

class MiddlewareResolver implements MiddlewareResolverInterface
{
    const CLASS_METHOD_EXTRACTOR = "/^(.+)::(.+)$/";
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
        $matches = [];
        if (is_string($middlewareDefinition) && preg_match(self::CLASS_METHOD_EXTRACTOR, $middlewareDefinition,
                $matches)
        ) {
            list($matchedString, $class, $method) = $matches;
            if ($this->container instanceof ContainerInterface && $this->container->has($class)) {
                return [$this->container->get($class), $method];
            }
        } elseif (is_string($middlewareDefinition) && $this->container instanceof ContainerInterface && $this->container->has($middlewareDefinition)) {
            return $this->container->get($middlewareDefinition);
        }

        if (is_callable($middlewareDefinition)) {
            return $middlewareDefinition;
        }
        throw new InvalidArgumentException('Middleware not resolvable: ' . (is_object($middlewareDefinition) ? get_class($middlewareDefinition) : $middlewareDefinition));
    }
}
