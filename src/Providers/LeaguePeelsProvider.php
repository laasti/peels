<?php

namespace Laasti\Peels\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;

class LeaguePeelsProvider extends AbstractServiceProvider
{
    protected $provides = [
        'Laasti\Peels\Http\HttpRunner',
        'Laasti\Peels\IORunner',
        'Laasti\Peels\MiddlewareResolver',
        'Laasti\Peels\MiddlewareResolverInterface',
        'Laasti\Peels\StackBuilderInterface',
        'Laasti\Peels\StackBuilder',
    ];

    protected $defaultConfig = [
        'builder' => 'Laasti\Peels\StackBuilder',
        'resolver' => 'Laasti\Peels\MiddlewareResolver',
        'runner' => 'Laasti\Peels\IORunner',
        'middlewares' => []
    ];

    public function register()
    {
        $this->getContainer()->add('Laasti\Peels\Http\HttpRunner');
        $this->getContainer()->add('Laasti\Peels\IORunner');
        $this->getContainer()->add('Laasti\Peels\MiddlewareResolver')->withArgument('Interop\Container\ContainerInterface');
        $this->getContainer()->add('Laasti\Peels\StackBuilder')->withArgument('Laasti\Peels\MiddlewareResolverInterface');
        $this->getContainer()->add('Laasti\Peels\MiddlewareResolverInterface', 'Laasti\Peels\MiddlewareResolver')->withArgument('Interop\Container\ContainerInterface');
        $this->getContainer()->add('Laasti\Peels\StackBuilderInterface', 'Laasti\Peels\StackBuilder')->withArgument('Laasti\Peels\MiddlewareResolverInterface');

        foreach ($this->getConfig() as $name => $config) {
            $config += $this->defaultConfig;
            $this->getContainer()->share('peels.'.$name, $config['builder'])
                    ->withMethodCall('setMiddlewares', [$config['middlewares']])
                    ->withArguments([$config['resolver'], new \League\Container\Argument\RawArgument($config['runner'])]);
        }
    }

    public function provides($alias = null)
    {
        $stacks = array_keys($this->getConfig());
        if (is_null($alias)) {
            if (in_array($alias, $this->provides)) {
                return true;
            }

            foreach ($stacks as $stack) {
                if ($alias === 'peels.'.$stack) {
                    return true;
                }
            }
        }
        $stacksAlias = [];
        foreach ($stacks as $stack) {
            $stacksAlias[] = 'peels.'.$stack;
        }

        return array_merge($this->provides, $stacksAlias);
    }

    protected function getConfig()
    {
        $config = $this->getContainer()->get('config');
        if (isset($config['peels'])) {
            return $config['peels'];
        }
        return [];
    }
}
