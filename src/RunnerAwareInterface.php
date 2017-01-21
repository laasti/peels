<?php

namespace Laasti\Peels;

interface RunnerAwareInterface
{
    /**
     * @return Runner
     */
    public function getRunner();

    /**
     *
     * @param \Laasti\Peels\Runner $runner
     */
    public function setRunner(Runner $runner);
}
