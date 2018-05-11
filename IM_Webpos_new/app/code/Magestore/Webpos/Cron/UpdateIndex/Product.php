<?php

namespace Magestore\Webpos\Cron\UpdateIndex;

class Product extends UpdateAbstract
{
    public function __construct(
        \Magestore\Webpos\Api\Synchronization\ProductInterface $synchronizationModel
    )
    {
        $this->synchronizationModel = $synchronizationModel;
    }
}