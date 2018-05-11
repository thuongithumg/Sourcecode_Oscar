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
 * Giftvoucher Order Escape Item Block
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */
class Magestore_Giftvoucher_Block_Order_Escape_Item extends Mage_Sales_Block_Order_Item_Renderer_Default
{

    /**
     * Get Gift Card item options
     *
     * @return array
     */
    public function getItemOptions()
    {
        $result = parent::getItemOptions();
        $item = $this->getOrderItem();

        if ($item->getProductType() != 'giftvoucher') {
            return $result;
        }

        if ($options = $item->getProductOptionByCode('info_buyRequest')) {
            foreach (Mage::helper('giftvoucher')->getGiftVoucherOptions() as $code => $label) {
                if (isset($options[$code]) && $options[$code]) {
                    if ($code == 'giftcard_template_id') {
                        $valueTemplate = Mage::getModel('giftvoucher/gifttemplate')->load($options[$code]);
                        $result[] = array(
                            'label' => $label,
                            'value' => Mage::helper('core')->escapeHtml($valueTemplate->getTemplateName()),
                            'option_value' => Mage::helper('core')->escapeHtml($valueTemplate->getTemplateName()),
                        );
                    } else {
                        $result[] = array(
                            'label' => $label,
                            'value' => Mage::helper('core')->escapeHtml($options[$code]),
                            'option_value' => Mage::helper('core')->escapeHtml($options[$code]),
                        );
                    }
                }
            }
        }

        $giftVouchers = Mage::getModel('giftvoucher/giftvoucher')->getCollection()->addItemFilter($item->getId());
        if ($giftVouchers->getSize()) {
            $giftVouchersCode = array();
            foreach ($giftVouchers as $giftVoucher) {
                $currency = Mage::getModel('directory/currency')->load($giftVoucher->getCurrency());
                $balance = $giftVoucher->getBalance();
                if ($currency) {
                    $balance = $currency->format($balance, array(), false);
                }
                if ($giftVoucher->getSetId() > 0) {
                    $giftVouchersCode[] = 'XXXXXXXXX' . ' (' . $balance . ') ';
                } else {
                    $giftVouchersCode[] = $giftVoucher->getGiftCode() . ' (' . $balance . ') ';
                }
            }
            $codes = implode(' ', $giftVouchersCode);
            $result[] = array(
                'label' => $this->__('Gift Card Code'),
                'value' => $codes,
                'option_value' => $codes,
            );
        }

        return $result;
    }

}
