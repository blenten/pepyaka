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
        return $row ? $row : null;
    }

    public function getTopUsers()
    {
        return $this->tableGateway->select(function (Select $select) {
            $select->order('votes DESC')->limit(15);
        });
    }

    public function createUser(User $user)
    {
        $data = [
            'login'    => $user->getLogin(),
            'password' => $user->getPassword(),
            'votes'    => 0,
            'gender'   => $user->getGender(),
            'avatar'   => $user->getAvatar()
        ];
        return $this->tableGateway->insert($data);
    }

    public function updateUser(User $user)
    {
        $login = (string) $user->getLogin();
        $data = [
            'votes'  => $user->getVotes(),
            'gender' => $user->getGender(),
            'avatar' => $user->getAvatar()
        ];

        return $this->tableGateway->update($data, ['login' => $login]);
    }
}
