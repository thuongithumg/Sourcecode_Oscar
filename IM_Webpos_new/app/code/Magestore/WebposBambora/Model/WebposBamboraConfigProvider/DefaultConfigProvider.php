<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposBambora\Model\WebposBamboraConfigProvider;
/**
 * Class DefaultConfigProvider
 * @package Magestore\WebposBambora\Model\WebposBamboraConfigProvider
 */
class DefaultConfigProvider
{
    protected $helperData;

    /**
     * DefaultConfigProvider constructor.
     * @param \Magestore\WebposBambora\Helper\Data $data
     */
    public function __construct(
        \Magestore\WebposBambora\Helper\Data $data
    ){
        $this->helperData = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $output = array();
        $output['bambora_timeout'] = $this->helperData->getTimeoutSession();
        $output['is_enabled_bambora'] = $this->helperData->isEnableBambora();
        return $output;
    }

}