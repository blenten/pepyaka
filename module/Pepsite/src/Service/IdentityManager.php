<?php

namespace Pepsite\Service;

use Pepsite\Entity\User;
use Zend\Session\Container;
use Zend\Session\SessionManager;

class IdentityManager
{
    private $identityContainer;
    private $sessionManager;
    public const SESSION_CONTAINER = 'IdentityContainer';

    public function __construct(Container $identityContainer, SessionManager $sessionManager)
    {
        $this->identityContainer = $identityContainer;
        $this->sessionManager = $sessionManager;
    }

    public function setIdentity(User $user) : void
    {
        $this->identityContainer->identity = $user->getLogin();
        try {
            $this->identityContainer->token = bin2hex(random_bytes(32));
        } catch (\Exception $e) {
            $this->identityContainer->token = bin2hex(uniqid());
        }
    }

    public function getIdentity() : ?string
    {
        return $this->identityContainer->identity ?? null;
    }

    public function getToken() : ?string
    {
        return $this->identityContainer->token;
    }

    public function validateToken($token) : bool
    {
        return $this->identityContainer->token === $token;
    }

    public function hasIdentity() : bool
    {
        return isset($this->identityContainer->identity);
    }

    public function clearIdentity() : void
    {
        $this->sessionManager->destroy();
    }
}
