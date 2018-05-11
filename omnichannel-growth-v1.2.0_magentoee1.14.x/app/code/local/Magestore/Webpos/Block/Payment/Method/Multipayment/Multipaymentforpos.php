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

class Magestore_Webpos_Block_Payment_Method_Multipayment_Multipaymentforpos extends Mage_Payment_Block_Form {

    protected function _construct() {
        parent::_construct();
        $this->setTemplate('webpos/content/payment/method/form/multipaymentforpos.phtml');
    }
    
    public function getActiveMethods(){
        $methods = Mage::getModel('webpos/source_adminhtml_multipaymentforpos')->getAllowPaymentMethodsWithLabel();
		$storeId = Mage::app()->getStore()->getStoreId();
		$paymentsForSplit = Mage::getStoreConfig('payment/multipaymentforpos/payments',$storeId);
		if(count(explode(',',$paymentsForSplit)) > 0){
			foreach($methods as $methodCode => $methodTitle){
				if(!in_array($methodCode,explode(',',$paymentsForSplit))){
					unset($methods[$methodCode]);
				}
			}
		}
        return $methods;
    }
}
