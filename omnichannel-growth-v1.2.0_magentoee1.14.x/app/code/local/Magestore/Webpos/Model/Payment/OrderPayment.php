<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

class Magestore_Webpos_Model_Payment_OrderPayment extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('webpos/orderPayment');
    }

    /**
     * @param object $order
     * @return array
     */
    public function getPaymentListOfOrder($order)
    {
        /** @var Magestore_Webpos_Model_Mysql4_OrderPayment_Collection $collection */
        $collection = $this->getCollection()->addFieldToFilter('order_id', $order->getId());
        $validCollection = array();
        $totalPaid = 0;

        foreach ($collection as $item) {
            if ($totalPaid >= $order->getGrandTotal()) {
                break;
            }

            $validCollection[] = $item->getData();
            $totalPaid += $item->getRealAmount() * 1;
        }

        return $validCollection;
    }

}
