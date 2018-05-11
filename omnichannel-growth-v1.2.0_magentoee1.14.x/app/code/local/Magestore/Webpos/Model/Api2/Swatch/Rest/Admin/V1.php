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

/**
 * API2 for catalog_product (Admin)
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magestore_Webpos_Model_Api2_Swatch_Rest_Admin_V1 extends Magestore_Webpos_Model_Api2_Abstract
{

    /**
     * get list swatch
     * @return array
     */
    protected function getListSwatch()
    {
        $swatchAttributeArray= array();
        $swatchArray = array();
//        $swatchHelper = Mage::helper('configurableswatches');
//        $swatchAttributeIds = $swatchHelper->getSwatchAttributeIds();
//        $collection = Mage::getModel('configurableSwatches/resource_catalog_product_attribute_super_collection')
//                        ->addFieldToFilter('attribute_id', array('in'=>$swatchAttributeIds));
//        foreach ($collection as $attributeModel) {
//            $swatchAttributeArray[] = $attributeModel->getId();
//            $attributeOptions = [];
//            foreach ($attributeModel->getOptions() as $option) {
//                $attributeOptions[$option->getValue()] = $this->getUnusedOption($option);
//            }
//            $attributeOptionIds = array_keys($attributeOptions);
//            $swatches = $this->getSwatchesByOptionsId($attributeOptionIds);
//            $data = [
//                'attribute_id' => $attributeModel->getId(),
//                'attribute_code' => $attributeModel->getAttributeCode(),
//                'attribute_label' => $attributeModel->getStoreLabel(),
//                'swatches' => $swatches,
//            ];
//            $swatchArray[] = $data;
//        }
//        $swatchInterface = $this->swatchResultInterface->create();
        $data['items'] = $swatchArray;
        $data['total_count'] = 0;
        return $data;
    }

    protected function getUnusedOption($swatchOption)
    {
        return array(
            'label' => $swatchOption->getLabel(),
            'link' => 'javascript:void();',
            'custom_style' => 'disabled'
        );
    }

    /**
     * Get swatch options by option id's according to fallback logic
     *
     * @param array $optionIds
     * @return array
     */
    public function getSwatchesByOptionsId($optionsIds)
    {
        $swatchCollection = Mage::getModel('configurableSwatches/resource_catalog_product_attribute_super_collection');
        $swatches = array();
        $currentStoreId = Mage::app()->getStore()->getId();
        foreach ($swatchCollection as $item) {
            if ($item['type'] != 0) {
                $swatches[$item['option_id']] = $item->getData();
            } elseif ($item['store_id'] == $currentStoreId && $item['value']) {
                $fallbackValues[$item['option_id']][$currentStoreId] = $item->getData();
            } elseif ($item['store_id'] == 0) {
                $fallbackValues[$item['option_id']][0] = $item->getData();
            }
        }

        if (!empty($fallbackValues)) {
            $swatches = $this->addFallbackOptions($fallbackValues, $swatches);
        }

        return $swatches;
    }

    /**
     * @param array $fallbackValues
     * @param array $swatches
     * @return array
     */
    private function addFallbackOptions(array $fallbackValues, array $swatches)
    {
        $currentStoreId = Mage::app()->getStore()->getId();
        foreach ($fallbackValues as $optionId => $optionsArray) {
            if (isset($optionsArray[$currentStoreId])) {
                $swatches[$optionId] = $optionsArray[$currentStoreId];
            } else {
                $swatches[$optionId] = $optionsArray[0];
            }
        }

        return $swatches;
    }

    public function dispatch()
    {
        switch ($this->getActionType() . $this->getOperation()) {
            /* Get */
            case self::ACTION_TYPE_COLLECTION . self::OPERATION_RETRIEVE:
                $result = $this->getListSwatch();
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
                break;
            default:
                $this->_critical(self::RESOURCE_METHOD_NOT_IMPLEMENTED);
                break;
        }
    }

}
