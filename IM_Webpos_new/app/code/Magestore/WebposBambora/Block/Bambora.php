<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposBambora\Block;

/**
 * Class Bambora
 * @package Magestore\WebposAuthorizenet\Block
 */
class Bambora extends \Magestore\Webpos\Block\AbstractBlock
{
    /**
     * @return string
     */
    public function toHtml()
    {
        /** @var \Magestore\WebposBambora\Helper\Data $helper */
        $helper = $this->_objectManager->get('Magestore\WebposBambora\Helper\Data');
        if ($helper->isEnableBambora())
            return parent::toHtml();
        return '';
    }
}
