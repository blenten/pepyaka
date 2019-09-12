<?php
/** @noinspection PhpUndefinedMethodInspection */
/** @noinspection PhpUndefinedFieldInspection */

namespace Pepsite\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Zend\Session\SessionManager;
use Pepsite\Model\UsersTable;

class AuthController extends AbstractActionController
{
    private $usersTable;
    private $sessionContainer;
    private $sessionManager;

    public function __construct(UsersTable $usersTable, Container $sessionContainer, SessionManager $sessionManager)
    {
        $this->usersTable = $usersTable;
        $this->sessionContainer = $sessionContainer;
        $this->sessionManager = $sessionManager;
    }

    public function registerAction()
    {
        if (isset($this->sessionContainer->userLogin)) {
            return $this->redirect()->toRoute('home');
        }
        if ($this->getRequest()->isGet()) {
            return new ViewModel();
        }
        return $this->notFoundAction();
    }

    public function loginAction()
    {
        if (isset($this->sessionContainer->userLogin)) {
            return $this->redirect()->toRoute('home');
        }
        if ($this->getRequest()->isGet()) {
//            return new ViewModel();
            $this->sessionContainer->userLogin = 'sono';
            return $this->redirect()->toRoute('home');
        }
        return $this->notFoundAction();
    }

    public function logoutAction()
    {
        if ($this->sessionManager->sessionExists()) {
            $this->sessionManager->destroy();
        }
        return $this->redirect()->toRoute('home');
    }
}
