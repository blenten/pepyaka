<?php

namespace Pepsite\Controller;

use Pepsite\Service\UserManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    private $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    public function indexAction()
    {
        return new ViewModel([
            'topUsers' => $this->userManager->getTopUsers(),
        ]);
    }
}
