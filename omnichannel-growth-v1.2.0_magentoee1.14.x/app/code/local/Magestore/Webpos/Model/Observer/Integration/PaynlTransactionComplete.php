<?php
/**
 * Created by Wazza Rooney on 12/8/17 3:17 PM
 * Copyright (c) 2017. All rights reserved.
 * Last modified 9/27/17 5:25 PM
 */


class Magestore_Webpos_Model_Observer_Integration_PaynlTransactionComplete extends Magestore_Webpos_Model_Observer_Abstract
{
    /**
     * @param $observer
     * @return $this
     */
    public function execute($observer)
    {
        $event = $observer->getEvent();
        /* @var Mage_Sales_Model_Order $order */
        $order = $event->getOrder();
        $my_file = Magestore_Webpos_Helper_Payment::NL_PAY_TRANSACTION_LOG_PATH . $order->getId() . '.txt';
        $handle = fopen($my_file, 'w');
        return $this;
    }
}