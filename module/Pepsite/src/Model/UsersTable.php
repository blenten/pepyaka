<?php
namespace Pepsite\Model;

use Zend\Db\Sql\Select;
use Pepsite\Entity\User;

class UsersTable extends DBTable
{
    public static function makeFactories()
    {
        return parent::makeFactoriesFor('users', self::class, User::class);
    }

    public function getUser($login)
    {
        $login = (string) $login;
        $rowset = $this->tableGateway->select(['login' => $login]);
        $row = $rowset->current();
        if (! $row) {
            return null;
        }
        return $row;
    }

    public function getTopUsers()
    {
        return $this->tableGateway->select(function (Select $select) {
            $select->order('votes DESC')->limit(15);
        });
    }
}
