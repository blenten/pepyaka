<?php
namespace Pepsite\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Pepsite\Model\UsersTable;

class IndexController extends AbstractActionController
{
    private $usersTable;

    public function __construct(UsersTable $usersTable)
    {
        $this->usersTable = $usersTable;
    }

    public function indexAction()
    {
        return new ViewModel([
            'users' => $this->usersTable->getUser('sono'),
        ]);
    }
}
