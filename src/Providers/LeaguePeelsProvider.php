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
        /*'example' => [
            'resolver' => 'Laasti\Peels\MiddlewareResolverInterface',
            'runner' => 'Laasti\Peels\IORunner',
            'middlewares' => []
        ]*/
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
            $config += [
                'resolver' => 'Laasti\Peels\MiddlewareResolverInterface',
                'runner' => 'Laasti\Peels\IORunner',
                'middlewares' => []
            ];
            $this->getContainer()->share('peels.stacks.'.$name, 'Laasti\Peels\StackBuilderInterface')
                    ->withArguments([$config['resolver'], $config['runner']])
                    ->withMethodCall('setMiddlewares', [$config['middlewares']]);
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
                if ($alias === 'peels.stacks.'.$stack) {
                    return true;
                }
            }
        }
        $stacksAlias = [];
        foreach ($stacks as $stack) {
            $stacksAlias[] = 'peels.stacks.'.$stack;
        }

        return array_merge($this->provides, $stacksAlias);
    }

    protected function getConfig()
    {
        $config = $this->getContainer()->get('config');
        if (isset($config['peels']) && isset($config['peels']['stacks'])) {
            return $config['peels']['stacks'];
        }
        return [];
    }
}
