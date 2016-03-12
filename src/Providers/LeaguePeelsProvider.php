<?php

namespace Laasti\Peels\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;

class LeaguePeelsProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    protected $provides = [
        'Laasti\Peels\Http\HttpRunner',
        'Laasti\Peels\Runner',
        'Laasti\Peels\MiddlewareResolver',
        'Laasti\Peels\MiddlewareResolverInterface',
    ];

    protected $defaultConfig = [
        'resolver' => 'Laasti\Peels\MiddlewareResolver',
        'runner' => 'Laasti\Peels\Runner',
        'middlewares' => []
    ];

    public function register()
    {
        $this->getContainer()->add('Laasti\Peels\Http\HttpRunner')->withArgument('Laasti\Peels\MiddlewareResolverInterface');
        $this->getContainer()->add('Laasti\Peels\Runner')->withArgument('Laasti\Peels\MiddlewareResolverInterface');
        $this->getContainer()->add('Laasti\Peels\MiddlewareResolver')->withArgument('Interop\Container\ContainerInterface');
        $this->getContainer()->add('Laasti\Peels\MiddlewareResolverInterface', 'Laasti\Peels\MiddlewareResolver')->withArgument('Interop\Container\ContainerInterface');
        
        foreach ($this->getConfig() as $name => $config) {
            if ($name === 'inflector') {
                continue;
            }
            $config += $this->defaultConfig;
            $this->getContainer()->share('peels.'.$name, $config['runner'])
                    ->withArguments([$config['resolver'], $config['middlewares']]);
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

    public function boot()
    {
        $config = $this->getConfig();
        if (!isset($config['inflector'])) {
            $config['inflector'] = array_shift(array_keys($config));
        }
        $this->getContainer()->inflector('Laasti\Directions\RouterAwareInterface')
             ->invokeMethod('setRouter', ['peels.'.$config['inflector']]);
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
