<?php

namespace Refact\OdooBundle;

use Refact\Odoo\Odoo;
use Refact\OdooBundle\Security\Authentication\Token\OdooToken;

class TokenFactory
{
    /**
     * @var Odoo
     */
    private $odoo;
    /**
     * @var array
     */
    private $roleMapping;

    public function __construct(Odoo $odoo, array $roleMapping)
    {
        $this->odoo = $odoo;
        $this->roleMapping = $roleMapping;
    }

    public function createToken(int $uid): OdooToken
    {
        $fields = ['login', 'groups_id'];
        $data = $this->odoo->rpc('object', 'execute_kw', ['res.users', 'read', [$uid], ['fields' => $fields]])[0];

        $roles = array_intersect_key($this->roleMapping, array_flip($data['groups_id']));

        return new OdooToken($data['login'], $roles);
    }
}
