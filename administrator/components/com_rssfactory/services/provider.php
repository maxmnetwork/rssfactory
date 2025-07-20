<?php

\defined('_JEXEC') or die;

use Joomla\DI\Container;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\Component\Rssfactory\Administrator\Extension\RssfactoryComponent;
use Joomla\Component\Rssfactory\Administrator\Services\RssfactoryServiceProvider;
use Joomla\CMS\Extension\MVCComponentInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;

return function (Container $container) {
    // Register custom service provider (your class must exist in src/Services/)
    $container->registerServiceProvider(new RssfactoryServiceProvider());

    // Register Joomla MVC and dispatcher factories
    $container->registerServiceProvider(new MVCFactory('\\Joomla\\Component\\Rssfactory'));
    $container->registerServiceProvider(new ComponentDispatcherFactory('\\Joomla\\Component\\Rssfactory'));

    // Register the component as a service
    $container->set(
        RssfactoryComponent::class,
        function (Container $c): RssfactoryComponent {
            return new RssfactoryComponent(
                $c->get(ComponentDispatcherFactoryInterface::class),
                $c->get(MVCFactoryInterface::class)
            );
        }
    );

    // Alias for interface resolution
    $container->alias(MVCComponentInterface::class, RssfactoryComponent::class);
    $container->alias(ComponentInterface::class, RssfactoryComponent::class);
};
