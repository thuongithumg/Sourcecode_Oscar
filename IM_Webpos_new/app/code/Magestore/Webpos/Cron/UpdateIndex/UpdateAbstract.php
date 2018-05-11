<?php

namespace Magestore\Webpos\Cron\UpdateIndex;

abstract class UpdateAbstract
{
    /**
     * @var \Magestore\Webpos\Api\Synchronization\SynchronizationInterface
     */
    protected $synchronizationModel;

    public function execute()
    {
        if ($this->synchronizationModel->isUseIndexTable()) {
            $this->synchronizationModel->addIndexTableData();
        }
    }
}