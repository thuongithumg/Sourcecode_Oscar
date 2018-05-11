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
 * @package     Magestore_Giftvoucher
 * @module     Giftvoucher
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * Class Magestore_Giftvoucher_Block_Adminhtml_Giftvoucher_View
 */
class Magestore_Giftvoucher_Block_Adminhtml_Giftvoucher_View extends Mage_Core_Block_Template {

    /**
     *
     */
    public function _beforeToHtml() {
        parent::_beforeToHtml();
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getGiftVoucher() {
        if (!$this->hasData('gift_voucher')) {
            $this->setData('gift_voucher', Mage::getModel('giftvoucher/giftvoucher')->load($this->getRequest()->getParam('id'))
            );
        }
        return $this->getData('gift_voucher');
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getGiftVouchers() {
        if (!$this->hasData('gift_vouchers')) {
            $giftvoucherIds = $this->getRequest()->getParam('giftvoucher');
            if(!is_array($giftvoucherIds)){
                $giftvoucherIds=  explode(',', $giftvoucherIds);
            }
            $giftvouchers = Mage::getModel('giftvoucher/giftvoucher')->getCollection()
                    ->addFieldToFilter('giftvoucher_id', array(
                'in' => $giftvoucherIds,
            ));
            $this->setData('gift_vouchers', $giftvouchers);
        }
        return $this->getData('gift_vouchers');
    }

    /**
     * @param $template_id
     * @return Mage_Core_Model_Abstract
     */
    public function getGiftcardTemplate($template_id) {
        $templates = Mage::getModel('giftvoucher/gifttemplate')->load($template_id);
        return $templates;
    }

    /**
     * Print a giftcode to HTML
     *
     * @param $giftCode
     * @return mixed
     */
    public function printGiftcodeHtml($giftCode)
    {
        return Mage::getModel('giftvoucher/service_processor')->printGiftCodeHtml($giftCode);
    }

}
