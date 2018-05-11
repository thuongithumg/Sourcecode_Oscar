<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposAuthorizenet\Block;

/**
 * Class Authorizenet
 * @package Magestore\WebposAuthorizenet\Block
 */
class Authorizenet extends \Magestore\Webpos\Block\AbstractBlock
{
    /**
     * @return string
     */
    public function toHtml()
    {
        /** @var \Magestore\WebposAuthorizenet\Helper\Data $helper */
        $helper = $this->_objectManager->get('Magestore\WebposAuthorizenet\Helper\Data');
        if ($helper->isEnableAuthorizenet())
            return parent::toHtml();
        return '';
    }

    /**
     * Check authorizenet is sandbox mode or production mode
     * 
     * @return boolean
     */
    public function isSandbox(){
        /** @var \Magestore\WebposAuthorizenet\Helper\Data $helper */
        $helper = $this->_objectManager->get('Magestore\WebposAuthorizenet\Helper\Data');
        return $helper->getStoreConfig('webpos/payment/authorizenet/is_sandbox');
    }
}
