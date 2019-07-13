<?php

namespace Refact\OdooBundle;

use Refact\OdooBundle\Security\Authentication\Token\OdooToken;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class SsoHelper
{
    /**
     * @var array
     */
    private $config;
    /**
     * @var TokenFactory
     */
    private $tokenFactory;

    public function __construct(array $config, TokenFactory $tokenFactory)
    {
        $this->config = $config;
        $this->tokenFactory = $tokenFactory;
    }

    public function getLoginResponse(Request $request)
    {
        $state = md5(random_bytes(100));
        $request->getSession()->set('_odoo_sso_state', $state);

        return new RedirectResponse($this->config['url'] . '/sso?state=' . $state);
    }

    public function getToken(Request $request): OdooToken
    {
        $uid = intval($request->query->get('uid'));
        $hash = hash('sha256', "{$request->getSession()->get('_odoo_sso_state')}/{$uid}/{$this->config['sso_secret']}");

        if ($request->query->get('code') !== $hash) {
            throw new BadCredentialsException();
        }

        return $this->tokenFactory->createToken($uid);
    }
}
