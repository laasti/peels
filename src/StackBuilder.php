<?php


namespace Laasti\Peels;

class StackBuilder implements StackBuilderInterface
{

    protected $middlewares = [];
    protected $resolver;
    protected $runnerClass;

    public function __construct(MiddlewareResolverInterface $resolver = null, $runnerClass = 'Laasti\Peels\IORunner')
    {
        $this->setResolver($resolver ?: new MiddlewareResolver);
        $this->setRunnerClass($runnerClass);
        return $this;
    }

    public function setRunnerClass($runnerClass)
    {
        $this->runnerClass = $runnerClass;
        return $this;
    }
    

    public function setResolver($resolver)
    {
        $this->resolver = $resolver;
        return $this;
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

    public function create($runnerClass = null)
    {
        $runnerClass = $runnerClass ?: $this->runnerClass;
        //TODO Seems quirky
        if (is_null($runnerClass) || !class_exists($runnerClass)) {
            throw new \InvalidArgumentException('StackBuilder needs a runner class.');
        }
        if (!count($this->middlewares)) {
            throw new \InvalidArgumentException('StackBuilder needs at least one middleware to create a runner.');
        }
        return new $runnerClass($this->middlewares);
    }
}
