<?php

namespace Pepsite\Controller;

use Pepsite\Service\{IdentityManager, UserManager};
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    private $userManager;
    private $identity;

    public function __construct(UserManager $userManager, IdentityManager $identityManager)
    {
        $this->userManager = $userManager;
        $this->identity = $identityManager->getIdentity();
    }

    public function indexAction()
    {
        $topUsersResSet = $this->userManager->getTopUsers();
        if (!is_null($this->identity)) {
            $topUsers = [];
            $targets = [];
            foreach ($topUsersResSet as $user) {
                $targets[] = $user->getLogin();
                $topUsers[] = $user;
            }
            $votes = $this->userManager->getUserVoteTargets($this->identity, $targets);
        }
        return new ViewModel([
            'topUsers' => $topUsers ?? $topUsersResSet,
            'votes'    => $votes ?? null
        ]);
    }
}
