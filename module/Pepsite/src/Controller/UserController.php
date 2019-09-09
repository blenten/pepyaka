<?php
namespace Pepsite\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class UserController extends AbstractActionController
{
    public function profileAction()
    {
        echo "user/profile {$this->params()->fromRoute('id')}";
    }

    public function editAction()
    {
        echo "user/edit {$this->params()->fromRoute('id')} сасай";
    }
}
