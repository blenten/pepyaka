<?php
namespace Pepsite\Entity;

class User extends DBEntity
{
    protected $login;
    protected $password;
    protected $votes;
    protected $gender;
    protected $avatar;
    protected $registrationDate;

    public const GENDER_MALE = 'M';
    public const GENDER_FEMALE = 'F';

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

    public function setVotes($votes)
    {
        $this->votes = $votes;
    }

    public function getGender()
    {
        return $this->gender;
    }

    public function setGender($gender)
    {
        if (!in_array($gender, [self::GENDER_MALE, self::GENDER_FEMALE])) {
            throw new \UnexpectedValueException("Wrong gender value: {$gender}");
        }
        $this->gender = $gender;
    }

    public function getAvatar()
    {
        return $this->avatar;
    }

    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
    }

    public function getRegistrationDate()
    {
        return $this->registrationDate;
    }
}
