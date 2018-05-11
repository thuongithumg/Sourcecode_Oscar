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

class Magestore_Webpos_Model_Payment_Method_Cash extends Mage_Payment_Model_Method_Abstract {
    /* This model define payment method */

    protected $_code = 'cashforpos';
    protected $_infoBlockType = 'webpos/payment_method_cash_info_cash';

    public function isAvailable($quote = null) {
        $isWebposApi = Mage::helper('webpos/permission')->validateRequestSession();
        $routeName = Mage::app()->getRequest()->getRouteName();
        $cashenabled = Mage::helper('webpos/payment')->isCashPaymentEnabled();
        if (($routeName == "webpos" || $isWebposApi) && $cashenabled == true)
            return true;
        else
            return false;
    }

}