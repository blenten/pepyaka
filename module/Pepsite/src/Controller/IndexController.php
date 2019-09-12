<?php
/** @noinspection PhpUndefinedFieldInspection */

namespace Pepsite\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Pepsite\Model\UsersTable;

class IndexController extends AbstractActionController
{
    private $usersTable;
    private $sessionContainer;

    public function __construct(UsersTable $usersTable, Container $sessionContainer)
    {
        $this->usersTable = $usersTable;
        $this->sessionContainer = $sessionContainer;
    }

    public function indexAction()
    {
        return new ViewModel([
            'topUsers' => $this->usersTable->getTopUsers(),
            'user'     => $this->sessionContainer->userLogin ?? null,
        ]);
    }
}
