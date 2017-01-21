<?php

namespace Laasti\Peels\Test;

use Laasti\Peels\Runner;

class RunnerTest extends \PHPUnit_Framework_TestCase
{
    public function testIncompleteRunException()
    {
        $this->setExpectedException('Laasti\Peels\IncompleteRunException');
        $runner = new Runner(new \Laasti\Peels\MiddlewareResolver, [
            function ($x, $y, $this) {
                return $this($x, $y);
            }
        ]);
        $runner(3, 5);
    }

    public function testBaseRunner()
    {
        $runner = new Runner(new \Laasti\Peels\MiddlewareResolver, [
            function ($x, $y, $this) {
                return $y;
            }
        ]);
        $this->assertTrue($runner(10, 15) === 15);
    }

    public function testMultipleRunner()
    {
        $runner = new Runner(new \Laasti\Peels\MiddlewareResolver, [
            function ($x, $y, $this) {
                $y++;
                return $this($x, $y);
            },
            function ($x, $y, $this) {
                $y++;
                return $y;
            }
        ]);
        $this->assertTrue($runner(1, 1) === 3);
    }
}
