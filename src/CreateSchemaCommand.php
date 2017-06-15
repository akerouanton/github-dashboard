<?php

declare(strict_types=1);

namespace NiR\GhDashboard;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateSchemaCommand extends Command
{
    private $manager;

    public function __construct(SchemaManager $manager)
    {
        parent::__construct();

        $this->manager = $manager;
    }

    protected function configure()
    {
        $this
            ->setName('app:schema:create')
            ->setDescription('Creates database schema.')
            ->addOption('remove', 'r', InputOption::VALUE_NONE)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $remove = $input->getOption('remove') ?: false;

        if ($remove) {
            $this->manager->dropAndCreateDatabase();
        }

        $this->manager->createSchema();
    }
}
