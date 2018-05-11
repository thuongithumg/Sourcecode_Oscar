<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposVantiv\Block\Adminhtml\Config;


class Setupguide extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'Magestore_WebposVantiv::config/setupguide.phtml';

    /**
     * Get api test url
     * @return string
     */
    public function getTestApiUrl(){
        return $this->getUrl('webposvantiv/api/test');
    }

}