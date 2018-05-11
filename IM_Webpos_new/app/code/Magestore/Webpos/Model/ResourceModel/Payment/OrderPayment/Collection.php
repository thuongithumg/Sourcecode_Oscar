<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\ResourceModel\Payment\OrderPayment;

/**
 * class \Magestore\Webpos\Model\ResourceModel\Payment\OrderPayment
 *
 * Web POS Products Collection resource model
 * Methods:
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Initialize collection resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\Webpos\Model\Payment\OrderPayment', 'Magestore\Webpos\Model\ResourceModel\Payment\OrderPayment');
    }
}