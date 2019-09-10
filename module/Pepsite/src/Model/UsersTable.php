<?php
namespace Pepsite\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;
use Pepsite\Entity\User;

class UsersTable
{
    private $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function getUser($login)
    {
        $login = (string) $login;
        $rowset = $this->tableGateway->select(['login' => $login]);
        $row = $rowset->current();
        if (! $row) {
            throw new RuntimeException("Could not find row with identifier {$login}");
        }

        return $row;
    }
}