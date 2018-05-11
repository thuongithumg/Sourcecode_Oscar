<?php

namespace Magestore\Webpos\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class Synchronization extends Command
{
    const OPTION = 'type';

    protected $listType = [
        \Magestore\Webpos\Model\Service\Synchronization\Product::SYNCHRONIZATION_TYPE,
        \Magestore\Webpos\Model\Service\Synchronization\Stock::SYNCHRONIZATION_TYPE,
        \Magestore\Webpos\Model\Service\Synchronization\Customer::SYNCHRONIZATION_TYPE
    ];

    protected function configure()
    {
        $options = [
            new InputOption(
                self::OPTION,
                null,
                InputOption::VALUE_OPTIONAL,
                'Type'
            )
        ];

        $this->setName('webpos:reindex');
        $this->setDescription('Update index data');
        $this->setDefinition($options);

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $type = $input->getOption(self::OPTION);
        if($type) {
            if(!in_array($type, $this->listType)) {
                $output->writeln("This type is not exist!");
            } else {
                $output->writeln("Start reindex ...");
                $this->updateIndex($type);
                $output->writeln("Reindex successfully!");
            }
        } else {
            $output->writeln("Start reindex ...");
            foreach ($this->listType as $type) {
                $this->updateIndex($type);
                $output->writeln("Reindex " . $type . " successfully!");
            }
            $output->writeln("Reindex successfully!");
        }
    }

    protected function updateIndex($type) {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magestore\Webpos\Api\InstallManagementInterface $installManagement */
        $installManagement = $om->get('Magestore\Webpos\Api\InstallManagementInterface');

        $installManagement->createIndexTable($type);
        $installManagement->addIndexTableData($type);
    }
}