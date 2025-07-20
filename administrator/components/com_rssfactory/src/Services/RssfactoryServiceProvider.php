<?php

namespace Joomla\Component\Rssfactory\Administrator\Services;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Component\Rssfactory\Administrator\Extension\RssfactoryComponent;

class RssfactoryServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        // Register services in the container here
        $container->set('component.rssfactory', function($c) {
            return new RssfactoryComponent($c->get('serviceName'));  // Replace with actual dependencies
        });
    }
}

class RssfactoryServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        // Register services in the container here
        $container->set('component.rssfactory', function($c) {
            return new RssfactoryComponent($c->get('serviceName'));  // Replace with actual dependencies
        });
    }
}
