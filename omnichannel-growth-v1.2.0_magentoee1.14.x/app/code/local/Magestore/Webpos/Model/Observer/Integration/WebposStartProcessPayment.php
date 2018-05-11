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

class Magestore_Webpos_Model_Observer_Integration_WebposStartProcessPayment extends Magestore_Webpos_Model_Observer_Abstract
{
    /**
     * @param $observer
     * @return $this
     */
    public function execute($observer)
    {
        /* Data Format
        $eventData = array(
            'method_data' => '',
            'method' => '',
            'amount' => '',
            'base_amount' => '',
            'order' => ''
        );
        */
        $event = $observer->getEvent();
    }
}