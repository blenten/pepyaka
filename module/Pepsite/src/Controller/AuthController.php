<?php
/** @noinspection PhpUndefinedMethodInspection */
/** @noinspection PhpUndefinedFieldInspection */

namespace Pepsite\Controller;

use Pepsite\Entity\User;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Zend\Session\SessionManager;
use Pepsite\Model\UsersTable;
use Pepsite\Form\RegistrationForm;

class AuthController extends AbstractActionController
{
    private $usersTable;
    private $sessionContainer;
    private $sessionManager;
    private $dbAdapter;

    public function __construct(
        UsersTable $usersTable,
        Container $sessionContainer,
        SessionManager $sessionManager,
        $dbAdapter
    ) {
        $this->usersTable = $usersTable;
        $this->sessionContainer = $sessionContainer;
        $this->sessionManager = $sessionManager;
        $this->dbAdapter = $dbAdapter;
    }

    public function registerAction()
    {
        if (isset($this->sessionContainer->userLogin)) {
            return $this->redirect()->toRoute('home');
        }
        $form = new RegistrationForm($this->dbAdapter);
        if ($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if ($form->isValid()) {
                $data = $form->getData();
                $user = new User();
                $user->exchangeArray($data);
                $this->usersTable->saveUser($user);
                $this->sessionContainer->userLogin = $data['login'];
                return $this->redirect()->toRoute('user', ['id' => $data['login']]);
            }
        }
        return new ViewModel([
            'form' => $form
        ]);
    }

    public function loginAction()
    {
        if (isset($this->sessionContainer->userLogin)) {
            return $this->redirect()->toRoute('home');
        }
        if ($this->getRequest()->isPost()) {
            return $this->redirect()->toRoute('home');
        }
        return new ViewModel();
    }

    public function logoutAction()
    {
        if ($this->sessionManager->sessionExists()) {
            $this->sessionManager->destroy();
        }
        return $this->redirect()->toRoute('home');
    }
}
