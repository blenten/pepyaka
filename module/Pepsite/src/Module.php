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
        $application = $event->getApplication()->getServiceManager()->get(SessionManager::class);
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
                [
                    Service\UserManager::class => function ($container) {
                        return new Service\UserManager(
                            $container->get(Model\UsersTable::class),
                            $container->get(Model\VotesTable::class)
                        );
                    },
                    Service\IdentityManager::class => function ($container) {
                        return new Service\IdentityManager(
                            $container->get(Service\IdentityManager::SESSION_CONTAINER),
                            $container->get(SessionManager::class)
                        );
                    }
                ]
            )
        ];
    }

    public function getControllerConfig()
    {
        return [
            'factories' => [
                Controller\IndexController::class => function ($container) {
                    return new Controller\IndexController(
                        $container->get(Service\UserManager::class),
                    );
                },
                Controller\UserController::class => function ($container) {
                    return new Controller\UserController(
                        $container->get(Service\UserManager::class),
                        $container->get(Service\IdentityManager::class)
                    );
                },
                Controller\AuthController::class => function ($container) {
                    return new Controller\AuthController(
                        $container->get(Service\UserManager::class),
                        $container->get(Service\IdentityManager::class),
                        $container->get(AdapterInterface::class)
                    );
                }
            ]
        ];
    }

    public function getViewHelperConfig()
    {
        return [
            'factories' => [
                ViewHelper\Navbar::class => function ($container) {
                    return new ViewHelper\Navbar($container->get(Service\IdentityManager::class));
                }
            ],
            'aliases' => [
                'mainNav' => ViewHelper\Navbar::class,
            ]
        ];
    }
}
