<?php
namespace Pepsite\Model;

use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

abstract class DBTable
{
    protected $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    abstract public static function makeFactories();

    protected static function makeFactoriesFor($tableName, $tableClass, $entity)
    {
        $gatewayClass = $tableClass . 'Gateway';
        return [
            $tableClass   => function ($container) use ($tableClass, $gatewayClass) {
                return new $tableClass($container->get($gatewayClass));
            },
            $gatewayClass => function ($container) use ($entity, $tableName) {
                $dbAdapter = $container->get(AdapterInterface::class);
                $resultSetPrototype = new ResultSet();
                $resultSetPrototype->setArrayObjectPrototype(new $entity());
                return new TableGateway($tableName, $dbAdapter, null, $resultSetPrototype);
            }
        ];
    }
}
