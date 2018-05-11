<?php

namespace Magestore\Webpos\Cron\UpdateIndex;

class Stock extends UpdateAbstract
{
    public function __construct(
        \Magestore\Webpos\Api\Synchronization\StockInterface $synchronizationModel
    )
    {
        $this->synchronizationModel = $synchronizationModel;
    }
}