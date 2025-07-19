<?php

\defined('_JEXEC') or die;

use Joomla\CMS\Extension\MVCComponentInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\Component\Rssfactory\Administrator\Extension\RssfactoryComponent;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

return new class implements ServiceProviderInterface {
    public function register(Container $container): void
    {
        // Register Joomla core MVC services
        $container->registerServiceProvider(new MVCFactory('\\Joomla\\Component\\Rssfactory'));
        $container->registerServiceProvider(new ComponentDispatcherFactory('\\Joomla\\Component\\Rssfactory'));

        // Bind RssfactoryComponent as service
        $container->set(
            RssfactoryComponent::class,
            function (Container $c): RssfactoryComponent {
                return new RssfactoryComponent(
                    $c->get(ComponentDispatcherFactoryInterface::class),
                    $c->get(MVCFactoryInterface::class)
                );
            }
        );

        // Register aliases for MVC and ComponentInterface
        $container->alias(MVCComponentInterface::class, RssfactoryComponent::class);
        $container->alias(ComponentInterface::class, RssfactoryComponent::class);
    }
};
