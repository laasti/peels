<?php

namespace Laasti\Peels;

class IORunner
{
    protected $middlewares;
    protected $resolver;

    public function __construct(MiddlewareResolverInterface $resolver, array $middlewares = [])
    {
        $this->resolver = $resolver;
        $this->middlewares = $middlewares;
    }

    public function setMiddlewares(array $middlewares)
    {
        $this->middlewares = [];
        array_walk($middlewares, [$this, 'push']);
        return $this;
    }

    public function push($middleware)
    {
        $this->middlewares[] = $this->resolver->resolve($middleware);
        return $this;
    }

    public function unshift($middleware)
    {
        array_unshift($this->middlewares, $this->resolver->resolve($middleware));
        return $this;
    }

    public function __invoke($input, $output)
    {
        if (!count($this->middlewares)) {
            throw new IncompleteRunException('IORunner middleware must return a value before the end.');
        }

        $middleware = array_shift($this->middlewares);

        return call_user_func_array($middleware, [$input, $output, $this]);
    }
}
