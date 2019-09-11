<?php
/** @noinspection PhpUndefinedMethodInspection */

namespace Pepsite;

use Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module implements ConfigProviderInterface
{
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
                    return new Controller\IndexController($container->get(Model\UsersTable::class));
                },
                Controller\UserController::class => function ($container) {
                    return new Controller\UserController($container->get(Model\UsersTable::class));
                },
                Controller\AuthController::class => function ($container) {
                    return new Controller\AuthController($container->get(Model\UsersTable::class));
                }
            ]
        ];
    }
}
