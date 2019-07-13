<?php

namespace Refact\OdooBundle\Security\Http\Firewall;

use Refact\OdooBundle\SsoHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Firewall\AbstractAuthenticationListener;

class SsoAuthenticationListener extends AbstractAuthenticationListener
{
    /** @var SsoHelper */
    private $sso;

    public function setSsoHelper(SsoHelper $sso): void
    {
        $this->sso = $sso;
    }

    /**
     * Performs authentication.
     *
     * @return TokenInterface|Response|null The authenticated token, null if full authentication is not possible, or a Response
     *
     * @throws AuthenticationException if the authentication fails
     */
    protected function attemptAuthentication(Request $request)
    {
        if ($request->attributes->has('_odoo_sso_login')) {
            return $this->sso->getLoginResponse($request);
        }

        return $this->sso->getToken($request);
    }

    protected function requiresAuthentication(Request $request)
    {
        if ($this->httpUtils->checkRequestPath($request, $this->options['login_path'])) {
            $request->attributes->set('_odoo_sso_login', true);

            return true;
        }

        return parent::requiresAuthentication($request);
    }
}