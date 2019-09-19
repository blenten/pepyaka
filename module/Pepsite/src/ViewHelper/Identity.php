<?php

namespace Pepsite\ViewHelper;

use Pepsite\Entity\User;
use Pepsite\Service\UserManager;
use Pepsite\Service\IdentityManager;
use Zend\View\Helper\AbstractHelper;

class Identity extends AbstractHelper
{
    private $identity;
    private $token;
    private $userManager;

    public function __construct(IdentityManager $identityManager, UserManager $userManager)
    {
        $this->identity = $identityManager->getIdentity();
        $this->token = $identityManager->getToken();
        $this->userManager = $userManager;
    }

    public function hasIdentity() : bool
    {
        return !is_null($this->identity);
    }

    public function getIdentity() : ?string
    {
        return $this->identity;
    }
    public function getToken() : ?string
    {
        return $this->token;
    }

    public function getIdentityUser() : ?User
    {
        return $this->userManager->getUser($this->identity);
    }
}
