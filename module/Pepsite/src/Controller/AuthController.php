<?php
namespace Pepsite\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Pepsite\Model\UsersTable;

class AuthController extends AbstractActionController
{
    private $usersTable;

    public function __construct(UsersTable $usersTable)
    {
        $this->usersTable = $usersTable;
    }

    public function registerAction()
    {
        echo 'register';
    }

    public function loginAction()
    {
        echo 'login';
    }
}
