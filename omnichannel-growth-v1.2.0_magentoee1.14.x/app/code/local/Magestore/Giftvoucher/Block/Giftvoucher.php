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
 * Giftvoucher block
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */
class Magestore_Giftvoucher_Block_Giftvoucher extends Mage_Core_Block_Template
{

    /**
     * @return Mage_Core_Block_Abstract
     */
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('giftvoucher/index/check');
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return Mage::app()->getRequest()->getParam('code', null);
    }

    /**
     * @return mixed
     */
    public function getCodeTxt()
    {
        return Mage::helper('giftvoucher')->getHiddenCode($this->getCode());
    }

    /**
     * @return null
     */
    public function getGiftVoucher()
    {
        if ($code = $this->getCode()) {
            $codes = Mage::getSingleton('giftvoucher/session')->getCodes();
            $codes[] = $code;
            $codes = array_unique($codes);
            if ($max = Mage::helper('giftvoucher')->getGeneralConfig('maximum')) {
                if (count($codes) > $max) {
                    return null;
                }
            }

            Mage::getSingleton('giftvoucher/session')->setCodes($codes);
            $giftVoucher = Mage::getModel('giftvoucher/giftvoucher')->loadByCode($code);
            if ($giftVoucher->getId()) {
                return $giftVoucher;
            }
        }
        return null;
    }

    /**
     * Returns the formatted balance
     * 
     * @param Magestore_Giftvoucher_Model_Giftvoucher $giftVoucher
     * @return string
     */
    public function getBalanceFormat($giftVoucher)
    {
        $currency = Mage::getModel('directory/currency')->load($giftVoucher->getCurrency());
        return $currency->format($giftVoucher->getBalance());
    }

    /**
     * Get status of gift code
     * 
     * @param Magestore_Giftvoucher_Model_Giftvoucher $giftVoucher
     * @return string
     */
    public function getStatus($giftVoucher)
    {
        $status = $giftVoucher->getStatus();
        $statusArray = Mage::getSingleton('giftvoucher/status')->getOptionArray();
        return $statusArray[$status];
    }

}
