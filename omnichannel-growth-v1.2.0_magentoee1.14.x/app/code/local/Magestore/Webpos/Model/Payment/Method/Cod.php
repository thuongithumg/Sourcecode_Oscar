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

/*
 * Web POS by Magestore.com
 * Version 2.3
 * Updated by Daniel - 12/2015
 */

class Magestore_Webpos_Model_Payment_Method_Cod extends Mage_Payment_Model_Method_Abstract {
    /* This model define payment method */

    protected $_code = 'codforpos';
    protected $_infoBlockType = 'webpos/payment_method_cod_info_cod';
    protected $_formBlockType = 'webpos/payment_method_cod_codforpos';

    public function isAvailable($quote = null) {
        $isWebposApi = Mage::helper('webpos/permission')->validateRequestSession();
        $routeName = Mage::app()->getRequest()->getRouteName();
        $codenabled = Mage::helper('webpos/payment')->isCodPaymentEnabled();
        if (($routeName == "webpos" || $isWebposApi) && $codenabled == true)
            return true;
        else
            return false;
    }
    public function assignData($data) {
        $info = $this->getInfoInstance();
        if ($data->getData('codforpos_ref_no')) {
            $info->setData('codforpos_ref_no', $data->getData('codforpos_ref_no'));
        }
        return $this;
    }
}
