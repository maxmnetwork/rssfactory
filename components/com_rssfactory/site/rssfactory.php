<?php
/**
 * Frontend entry file for com_rssfactory
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Component\Rssfactory\Site\Extension\RssfactoryComponent;

// Enable debug message (optional)
// \Joomla\CMS\Factory::getApplication()->enqueueMessage('🌐 site/rssfactory.php loaded', 'info');

return new class implements ServiceProviderInterface {
    public function register(Container $container)
    {
        $container->registerServiceProvider(new MVCFactory('\\Joomla\\Component\\Rssfactory'));
        $container->registerServiceProvider(new ComponentDispatcherFactory('\\Joomla\\Component\\Rssfactory'));

        $container->set(
            ComponentInterface::class,
            function (Container $container) {
                return new RssfactoryComponent(
                    $container->get(MVCFactoryInterface::class)
                );
            }
        );
    }
};
