<?php
namespace Pepsite\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Pepsite\Model\UsersTable;

class UserController extends AbstractActionController
{
    private $usersTable;
    private $votesTable;
    private $commentsTable;

    public function __construct(UsersTable $usersTable)
    {
        $this->usersTable = $usersTable;
    }

    public function profileAction()
    {
        $userId = $this->params()->fromRoute('id');
        $user = $this->usersTable->getUser($userId);
        if (is_null($user)) {
            return $this->notFoundAction();
        }
        return new ViewModel([
            'user' => $user
        ]);
    }

    public function editAction()
    {
        $userId = $this->params()->fromRoute('id');
        $user = $this->usersTable->getUser($userId);
        if (is_null($user)) {
            return $this->notFoundAction();
        }
        echo "user/edit {$this->params()->fromRoute('id')} сасай";
    }
}
