<?php

namespace Refact\OdooBundle\Command;

use Refact\Odoo\Odoo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class GetRolesCommand extends Command
{
    protected static $defaultName = 'odoo:get-roles';

    /**
     * @var Odoo
     */
    private $odoo;

    public function __construct(Odoo $odoo)
    {
        parent::__construct();

        $this->odoo = $odoo;
    }

    protected function configure()
    {
        $this
            ->setDescription('Autogenerate the "odoo_role_mapping" parameter')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $groups = $this->odoo->rpc('object', 'execute_kw', ['res.groups', 'search_read', [[['category_id', '!=', 'Technical Settings']]], ['fields' => ['full_name']]]);

        $roles = array_reduce($groups, function ($roles, $group) {
            $roles[$group['id']] = 'ROLE_' . preg_replace('/[^A-Z]+/', '_', strtoupper($group['full_name']));

            return $roles;
        }, []);

        ksort($roles);

        $yaml = Yaml::dump([
            'odoo' => [
                'role_mapping' => $roles,
            ]
        ], 3);

        $output->writeln($yaml);
    }
}
