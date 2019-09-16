<?php

namespace Pepsite\ViewHelper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;
use Pepsite\Service\IdentityManager;

class Navbar extends AbstractHelper
{
    private $identityManager;

    public function __construct(IdentityManager $identityManager)
    {
        $this->identityManager = $identityManager;
    }

    public function render()
    {
//        $viewModel = new ViewModel([
//            'test' => 'test'
//        ]);
//        $viewModel->setTemplate('navbar.phtml');
//        $viewRender = $this->getServiceLocator()->get('ViewRenderer');
//        $html = $viewRender->render($viewModel);
        return 'sas sos';
    }
}
