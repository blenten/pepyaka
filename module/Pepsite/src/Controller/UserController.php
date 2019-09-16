<?php
namespace Pepsite\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Pepsite\Service\UserManager;
use Pepsite\Service\IdentityManager;
use Zend\View\Model\ViewModel;

class UserController extends AbstractActionController
{
    private $userManager;
    private $identityManager;

    public function __construct(UserManager $userManager, IdentityManager $identityManager)
    {
        $this->userManager = $userManager;
        $this->identityManager = $identityManager;
    }

    public function profileAction()
    {
        $userId = $this->params()->fromRoute('login');
        $user = $this->userManager->getUser($userId);
        if (is_null($user)) {
            return $this->notFoundAction();
        }
        return new ViewModel([
            'user' => $user
        ]);
    }

    public function editAction()
    {
        $userLogin = $this->params()->fromRoute('login');
        if ($this->identityManager->hasIdentity()) {
            $identity = $this->identityManager->getIdentity();
            if ($identity->getLogin() === $userLogin) {
                $user = $this->userManager->getUser($userLogin);
                if (is_null($user)) {
                    return $this->notFoundAction();
                }
                echo "{$user->getLogin()} сасай";
                return new ViewModel();
            }
        }
        return $this->redirect()->toRoute('home');
    }
}
