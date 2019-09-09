<?php
namespace Pepsite\Entity;

class User extends DBEntity
{
    protected $login;
    protected $password;
    protected $votes;
    protected $sex;
    protected $info;
    protected $avatar;

    public function getLogin()
    {
        return $this->login;
    }
}
