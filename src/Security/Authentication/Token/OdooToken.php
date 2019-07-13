<?php

namespace Refact\OdooBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class OdooToken extends AbstractToken
{
    public function __construct($user, array $roles = [])
    {
        parent::__construct($roles);

        $this->setUser($user);
        $this->setAuthenticated(true);
    }

    public function getCredentials()
    {
        return null;
    }
}
