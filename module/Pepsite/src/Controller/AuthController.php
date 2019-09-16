<?php

namespace Pepsite\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Pepsite\Service\UserManager;
use Pepsite\Service\IdentityManager;
use Pepsite\Form\LoginForm;
use Pepsite\Form\RegistrationForm;
use Zend\View\Model\ViewModel;

class AuthController extends AbstractActionController
{
    private $userManager;
    private $identityManager;
    private $dbAdapter;

    public function __construct(UserManager $userManager, IdentityManager $identityManager, $dbAdapter)
    {
        $this->userManager = $userManager;
        $this->identityManager = $identityManager;
        $this->dbAdapter = $dbAdapter;
    }

    public function registerAction()
    {
        if (isset($this->sessionContainer->user)) {
            return $this->redirect()->toRoute('home');
        }
        $form = new RegistrationForm($this->dbAdapter);
        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $user = $this->userManager->addUser($data);
                $this->identityManager->setIdentity($user);
                return $this->redirect()->toRoute('user', ['login' => $data['login']]);
            }
        }
        return new ViewModel([
            'form' => $form
        ]);
    }

    public function loginAction()
    {
        if (isset($this->sessionContainer->user)) {
            return $this->redirect()->toRoute('home');
        }
        $form = new LoginForm();
        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $user = $this->userManager->getValidUser($data['login'], $data['password']);
                if (!is_null($user)) {
                    $this->identityManager->setIdentity($user);
                    return $this->redirect()->toRoute('home');
                }
                $form->setMessages([
                    'auth' => ['Неверная комбинация логина и пароля']
                ]);
            }
        }
        return new ViewModel([
            'form' => $form
        ]);
    }

    public function logoutAction()
    {
        $this->identityManager->clearIdentity();
        return $this->redirect()->toRoute('home');
    }
}
