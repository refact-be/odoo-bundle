<?php

namespace Refact\OdooBundle\Security;

use Refact\Odoo\Odoo;
use Refact\OdooBundle\TokenFactory;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class OdooAuthenticationProvider implements AuthenticationProviderInterface
{
    /**
     * @var Odoo
     */
    private $odoo;
    /**
     * @var TokenFactory
     */
    private $tokenFactory;

    public function __construct(Odoo $odoo, TokenFactory $tokenFactory)
    {
        $this->odoo = $odoo;
        $this->tokenFactory = $tokenFactory;
    }

    public function authenticate(TokenInterface $token)
    {
        $uid = $this->odoo->auth($token->getUser(), $token->getCredentials());

        if ($uid === false) {
            throw new BadCredentialsException();
        }

        return $this->tokenFactory->createToken($uid);
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof UsernamePasswordToken;
    }
}
