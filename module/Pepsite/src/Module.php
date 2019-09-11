<?php
/** @noinspection PhpUndefinedClassInspection */
/** @noinspection PhpParamsInspection */
/** @noinspection PhpUndefinedMethodInspection */

namespace Pepsite;

use Pepsite\Controller\UserController;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
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
            'factories' => [
                Model\UsersTable::class => function ($container) {
                    return new Model\UsersTable(
                        $container->get(Model\UsersGateway::class)
                    );
                },
                Model\UsersGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Entity\User());
                    return new TableGateway('users', $dbAdapter, null, $resultSetPrototype);
                },
            ],
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
