<?php

namespace Laasti\Peels\Test;


class StackBuilderTest extends \PHPUnit_Framework_TestCase
{

    public function testNoMiddlewares()
    {
        $this->setExpectedException('InvalidArgumentException');
        $builder = new \Laasti\Peels\StackBuilder();
        $builder->create("Laasti\Peels\Http\HttpRunner");
    }

    public function testNoRunner()
    {
        $this->setExpectedException('InvalidArgumentException');
        $builder = new \Laasti\Peels\StackBuilder();
        $builder->create("");
    }

    public function testCreateDefaultRunner()
    {
        $builder = new \Laasti\Peels\StackBuilder();
        $builder->push(function($x, $y, $next) {
            return $y;
        });
        $runner = $builder->create();

        $this->assertTrue($runner instanceof \Laasti\Peels\IORunner);
    }

    public function testCreateMiddlewareInOrder()
    {
        $builder = new \Laasti\Peels\StackBuilder();
        $builder->push(function($x, $y, $next) {
            return 3;
        });
        $builder->push(function($x, $y, $next) {
            return 2;
        });
        $runner = $builder->create();
        $this->assertTrue($runner(1,1) === 3);

        $builder->unshift(function($x, $y, $next) {
            return 4;
        });
        $runner = $builder->create();
        $this->assertTrue($runner(1,1) === 4);

    }

    public function testSetMiddlewares()
    {
        $builder = new \Laasti\Peels\StackBuilder();
        $builder->push(function($x, $y, $next) {
            return 3;
        });
        $builder->setMiddlewares([
            function($x, $y, $next) {
                return 4;
            }
        ]);
        $runner = $builder->create();
        $this->assertTrue($runner(1,1) === 4);

    }
}
