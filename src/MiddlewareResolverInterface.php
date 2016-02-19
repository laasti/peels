<?php

namespace Laasti\Peels;

interface MiddlewareResolverInterface
{
    public function resolve($middlewareDefinition);
}
