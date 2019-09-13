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

    public function getPassword()
    {
        return $this->password;
    }

    public function getVotes()
    {
        return $this->votes;
    }

    public function getSex()
    {
        return $this->sex;
    }

    public function getInfo()
    {
        return $this->info;
    }

    public function getAvatar()
    {
        return $this->avatar;
    }
}
