<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposPaynl\Controller\Payment;

/**
 * Class Cancel
 * @package Magestore\WebposPaynl\Controller\Payment
 */
class Cancel extends \Magestore\WebposPaynl\Controller\AbstractAction
{
    /**
     * @return \Magento\Framework\Controller\Result\Page $resultPage
     */
    public function execute()
    {
        return $this->createPageResult();
    }
}
