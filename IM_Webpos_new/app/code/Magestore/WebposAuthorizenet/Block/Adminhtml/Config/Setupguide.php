<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposAuthorizenet\Block\Adminhtml\Config;


class Setupguide extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'Magestore_WebposAuthorizenet::config/setupguide.phtml';

    /**
     * Get api test url
     * @return string
     */
    public function getTestApiUrl(){
        return $this->getUrl('webposauthorizenet/api/test');
    }

}