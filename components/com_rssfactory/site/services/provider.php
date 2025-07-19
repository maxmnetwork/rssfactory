<?php
defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\MVCComponentInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Component\Rssfactory\Site\Extension\RssfactoryComponent;

return new class implements ServiceProviderInterface {
    public function register(Container $container): void
    {
        $container->set(
            MVCComponentInterface::class,
            fn($c) => new RssfactoryComponent(
                $c->get(ComponentDispatcherFactoryInterface::class)
            )
        );
    }
};
