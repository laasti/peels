<?php

namespace Laasti\Peels\Http;

use Laasti\Peels\IncompleteRunException;
use Laasti\Peels\Runner;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HttpRunner extends Runner
{
    /**
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws IncompleteRunException
     */
    public function __invoke($request, $response)
    {
        if (! $request instanceof RequestInterface || ! $response instanceof ResponseInterface) {
            throw new \InvalidArgumentException('HttpRunner needs its two arguments to be a RequestInterface and a ResponseInterface');
        }
        if (!count($this->middlewares)) {
            throw new IncompleteRunException('HttpRunner middleware must return a response before the end.');
        }

        $middleware = $this->resolver->resolve(array_shift($this->middlewares));

        return call_user_func_array($middleware, [$request, $response, $this]);
    }
}
