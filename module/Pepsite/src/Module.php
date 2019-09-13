<?php
/** @noinspection PhpUndefinedMethodInspection */

namespace Pepsite;

use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\MvcEvent;
use Zend\Session\SessionManager;

class Module implements ConfigProviderInterface
{
    public function onBootstrap(MvcEvent $event)
    {
        $application = $event->getApplication();
        $serviceManager = $application->getServiceManager();

        // The following line instantiates the SessionManager and automatically
        // makes the SessionManager the 'default' one. -__-
        $serviceManager->get(SessionManager::class);
    }

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getServiceConfig()
    {
        return [
            'factories' => array_merge(
                Model\UsersTable::makeFactories(),
                Model\VotesTable::makeFactories(),
                Model\CommentsTable::makeFactories(),
            )
        ];
    }

    public function getControllerConfig()
    {
        return [
            'factories' => [
                Controller\IndexController::class => function ($container) {
                    return new Controller\IndexController(
                        $container->get(Model\UsersTable::class),
                        $container->get('UserAuthContainer'),
                    );
                },
                Controller\UserController::class => function ($container) {
                    return new Controller\UserController(
                        $container->get(Model\UsersTable::class),
                        $container->get('UserAuthContainer')
                    );
                },
                Controller\AuthController::class => function ($container) {
                    return new Controller\AuthController(
                        $container->get(Model\UsersTable::class),
                        $container->get('UserAuthContainer'),
                        $container->get(SessionManager::class),
                        $container->get(AdapterInterface::class),
                    );
                }
            ]
        ];
    }
}
