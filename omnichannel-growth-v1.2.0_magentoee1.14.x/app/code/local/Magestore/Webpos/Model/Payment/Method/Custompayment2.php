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

class Magestore_Webpos_Model_Payment_Method_Custompayment2 extends Mage_Payment_Model_Method_Abstract {
    /* This model define payment method */

    protected $_code = 'cp2forpos';
    protected $_infoBlockType = 'webpos/payment_method_cc_info_cp2';
    protected $_formBlockType = 'webpos/payment_method_cc_ccforpos';

    public function isAvailable($quote = null) {
        $isWebposApi = Mage::helper('webpos/permission')->validateRequestSession();
        $routeName = Mage::app()->getRequest()->getRouteName();
        $ccenabled = Mage::helper('webpos/payment')->isCp2PaymentEnabled();
        if (($routeName == "webpos" || $isWebposApi)&& $ccenabled == true)
            return true;
        else
            return false;
    }

    public function assignData($data) {
        $info = $this->getInfoInstance();

        if ($data->getData('cp2forpos_ref_no')) {
            $info->setData('cp2forpos_ref_no', $data->getData('cp2forpos_ref_no'));
        }

        return $this;
    }

}
