<?php

namespace Laasti\Peels;

trait RunnerAwareTrait
{
    /**
     *
     * @var Runner
     */
    protected $runner;

    /**
     *
     * @return Runner
     */
    public function getRunner()
    {
        return $this->runner;
    }

    /**
     *
     * @param \Laasti\Peels\Runner $runner
     * @return $this
     */
    public function setRunner(Runner $runner)
    {
        $this->runner = $runner;
        return $this;
    }
}
