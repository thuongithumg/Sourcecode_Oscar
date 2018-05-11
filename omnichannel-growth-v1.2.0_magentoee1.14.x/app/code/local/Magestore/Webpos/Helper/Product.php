<?php
/**
 * Created by Wazza Rooney on 9/11/17 8:34 AM
 * Copyright (c) 2017. All rights reserved.
 * Last modified 9/11/17 8:34 AM
 */

/**
 * Created by PhpStorm.
 * User: MSI
 * Date: 9/11/2017
 * Time: 8:34 AM
 */

class Magestore_Webpos_Helper_Product
{
    /**
     * @param Magestore_Giftvoucher_Model_Product $product
     * @return array
     */
    public function getGiftvoucherOption($product) {

        $options = $this->getGiftAmount($product);
        $options['gift_template_ids'] = $product->getData('gift_template_ids');
        $options['gift_price_type'] = $product->getData('gift_price_type');
        $options['is_integrate_post_office'] = Mage::helper('giftvoucher')->getInterfaceConfig('postoffice', $product->getStoreId());

        return array(
            $options
        );
    }

    /**
     * Get the price information of Gift Card product
     *
     * @param Magestore_Giftvoucher_Model_Product $product
     * @return array
     */
    public function getGiftAmount($product)
    {
        $giftValue = Mage::helper('giftvoucher/giftproduct')->getGiftValue($product);
        switch ($giftValue['type']) {
            case 'range':
            case 'static':
                break;
            case 'dropdown':
                $giftValue['options'] = $giftValue['prices'];
                break;
            default:
                $giftValue['type'] = 'any';
        }
        return $giftValue;
    }

    /**
     * @param Mage_Sales_Model_Order_Item $item
     * @return array
     */
    public function getGiftVoucherInfoFromOrderItem($item)
    {
        $giftvoucherInfo = array();
//                    if ($options = $item->getProductOptionByCode('info_buyRequest')) {
//                        foreach (Mage::helper('giftvoucher')->getGiftVoucherOptions() as $code => $label) {
//                            if (isset($options[$code]) && $options[$code]) {
//                                if ($code == 'giftcard_template_id') {
//                                    $valueTemplate = Mage::getModel('giftvoucher/gifttemplate')->load($options[$code]);
//                                    $giftvoucherInfo[] = array(
//                                        'label' => $label,
//                                        'value' => Mage::helper('core')->escapeHtml($valueTemplate->getTemplateName()),
//                                        'option_value' => Mage::helper('core')->escapeHtml($valueTemplate->getTemplateName()),
//                                    );
//                                } else {
//                                    $giftvoucherInfo[] = array(
//                                        'label' => $label,
//                                        'value' => Mage::helper('core')->escapeHtml($options[$code]),
//                                        'option_value' => Mage::helper('core')->escapeHtml($options[$code]),
//                                    );
//                                }
//                            }
//                        }
//                    }

        $giftVouchers = Mage::getModel('giftvoucher/giftvoucher')->getCollection()->addItemFilter($item->getId());

        if ($giftVouchers->getSize()) {
            $giftVouchersCode = array();
            foreach ($giftVouchers as $giftVoucher) {
                $currency = Mage::getModel('directory/currency')->load($giftVoucher->getCurrency());
                $balance = $giftVoucher->getBalance();
                if ($currency) {
                    $balance = $currency->format($balance, array(), false);
                }
                $giftVouchersCode[] = $giftVoucher->getGiftCode() . ' (' . $balance . ') ';
            }
            $codes = implode(' ', $giftVouchersCode);
            $giftvoucherInfo[] = array(
                'label' => Mage::helper('webpos')->__('Gift Card Code'),
                'value' => $codes,
                'option_value' => $codes,
            );
        }

        return $giftvoucherInfo;
    }

    /**
     * @param Mage_Catalog_Model_Product | Mage_Catalog_Model_Abstract $productModel
     * @return array
     */
    public function getOptions($productModel) {
        $options = array();
        /** @var Mage_Catalog_Model_Product_Option $option */
        foreach ($productModel->getOptions() as $optionSortOrder => $option) {
            if ($option->getType() === 'drop_down' || $option->getType() === 'radio'
                || $option->getType() === 'checkbox' || $option->getType() === 'multiple'
            ) {
                $values = $option->getValues();
                $valueArray = array();
                /** @var Mage_Catalog_Model_Product_Option_Value $value */
                foreach ($values as $valueSortOrder => $value) {
                    $valueData = $value->getData();
                    $valueData['sort_order'] = $valueSortOrder;
                    $valueArray[] = $valueData;
                }
                $option->setData('values', $valueArray);
            }
            if ($option->getData('is_require')) {
                $option->setData('is_require', true);
            } else {
                $option->setData('is_require', false);
            }

            $optionData = $option->getData();
            $optionData['sort_order'] = $optionSortOrder;

            $options[] = $optionData;
        }

        return $options;
    }
}