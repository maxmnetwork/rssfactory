<?php

namespace Joomla\Component\Rssfactory\Administrator\Extension;

\defined('_JEXEC') or die;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Dispatcher\DispatcherInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\DI\ContainerInterface;

/**
 * Component class for com_rssfactory
 *
 * @since 4.0
 */
final class RssfactoryComponent implements ComponentInterface
{
    /**
     * @var ComponentDispatcherFactoryInterface
     */
    protected ComponentDispatcherFactoryInterface $dispatcherFactory;

    /**
     * @var MVCFactoryInterface
     */
    protected MVCFactoryInterface $mvcFactory;

    /**
     * @var string
     */
    private string $defaultController = 'feeds';

    /**
     * Constructor.
     */
    public function __construct(
        ComponentDispatcherFactoryInterface $dispatcherFactory,
        MVCFactoryInterface $mvcFactory
    ) {
        $this->dispatcherFactory = $dispatcherFactory;
        $this->mvcFactory = $mvcFactory;
    }

    /**
     * Optional boot logic during component initialization.
     */
    public function boot(ContainerInterface $container): void
    {
        // Reserved for service bindings or other boot logic
    }

    /**
     * Sets the default controller name to be used.
     */
    public function setDefaultController(string $controller): void
    {
        $this->defaultController = $controller;
    }

    /**
     * Returns the MVC factory.
     */
    public function getMVCFactory(ContainerInterface $container): MVCFactoryInterface
    {
        return $this->mvcFactory;
    }

    /**
     * Returns the dispatcher for this component.
     */
    public function getDispatcher(CMSApplicationInterface $app): DispatcherInterface
    {
        $dispatcher = $this->dispatcherFactory->createDispatcher(
            'com_rssfactory',
            $app,
            $this->mvcFactory
        );

        $dispatcher->setDefaultController($this->defaultController);

        return $dispatcher;
    }

    /**
     * Returns additional custom routes (optional).
     */
    public function getRoutes(): array
    {
        return [];
    }
}
