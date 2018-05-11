<?php

namespace Magestore\Webpos\Cron\UpdateIndex;

class Customer extends UpdateAbstract
{
    public function __construct(
        \Magestore\Webpos\Api\Synchronization\CustomerInterface $synchronizationModel
    )
    {
        $this->synchronizationModel = $synchronizationModel;
    }
}