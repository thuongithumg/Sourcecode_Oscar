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
 * Giftvoucher Product View Block
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */
class Magestore_Giftvoucher_Block_Product_View extends Mage_Catalog_Block_Product_View_Abstract
{
    /**
     *
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $media = $this->getLayout()->getBlock('product.info.media');
        $_product = $this->getProduct();
        if ($media && $_product->getTypeId() == 'giftvoucher') {
            $media->setTemplate('giftvoucher/product/media.phtml');
        }
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
        $store = Mage::app()->getStore();
        switch ($giftValue['type']) {
            case 'range':
                $giftValue['from'] = $this->convertPrice($product, $giftValue['from']);
                $giftValue['to'] = $this->convertPrice($product, $giftValue['to']);
                $giftValue['from_txt'] = $store->formatPrice($giftValue['from']);
                $giftValue['to_txt'] = $store->formatPrice($giftValue['to']);
                break;
            case 'dropdown':
                $giftValue['options'] = $this->_convertPrices($product, $giftValue['options']);
                $giftValue['prices'] = $this->_convertPrices($product, $giftValue['prices']);
                $giftValue['prices'] = array_combine($giftValue['options'], $giftValue['prices']);
                $giftValue['options_txt'] = $this->_formatPrices($giftValue['options']);
                break;
            case 'static':
                $giftValue['value'] = $this->convertPrice($product, $giftValue['value']);
                $giftValue['value_txt'] = $store->formatPrice($giftValue['value']);
                $giftValue['price'] = $this->convertPrice($product, $giftValue['gift_price']);
                break;
            default:
                $giftValue['type'] = 'any';
        }
        return $giftValue;
    }
    /**
     * Convert Gift Card base price
     *
     * @param Magestore_Giftvoucher_Model_Product $product
     * @param float $basePrices
     * @return float
     */
    protected function _convertPrices($product, $basePrices)
    {
        foreach ($basePrices as $key => $price) {
            $basePrices[$key] = $this->convertPrice($product, $price);
        }
        return $basePrices;
    }
    /**
     * Get Gift Card product price with all tax settings processing
     *
     * @param Magestore_Giftvoucher_Model_Product $product
     * @param float $price
     * @return float
     */
    public function convertPrice($product, $price)
    {
        $includeTax = ( Mage::getStoreConfig('tax/display/type') != 1 );
        $store = Mage::app()->getStore();
        $priceWithTax = Mage::helper('tax')->getPrice($product, $price, $includeTax);
        return $store->convertPrice($priceWithTax);
    }
    /**
     * Formatted Gift Card price
     *
     * @param array $prices
     * @return array
     */
    protected function _formatPrices($prices)
    {
        $store = Mage::app()->getStore();
        foreach ($prices as $key => $price) {
            $prices[$key] = $store->formatPrice($price, false);
        }
        return $prices;
    }
    /**
     * @return int
     */
    public function messageMaxLen()
    {
        return (int) Mage::helper('giftvoucher')->getInterfaceConfig('max');
    }
    /**
     * @return mixed
     */
    public function enablePhysicalMail()
    {
        return Mage::helper('giftvoucher')->getInterfaceConfig('postoffice');
    }
    /**
     * @return bool
     */
    public function isInConfigurePage()
    {
        $request = Mage::app()->getRequest();
        $action = $request->getRequestedRouteName() . '_' . $request->getRequestedControllerName() . '_' .
            $request->getRequestedActionName();

        if ($action == 'checkout_cart_configure' && $request->getParam('id')) {
            return true;
        }
        return false;
    }
    /**
     * @return Varien_Object
     */
    public function getFormConfigData()
    {
        $request = Mage::app()->getRequest();
        $store = Mage::app()->getStore();
        $action = $request->getRequestedRouteName() . '_' . $request->getRequestedControllerName() . '_' .
            $request->getRequestedActionName();
        if ($action == 'checkout_cart_configure' && $request->getParam('id')) {
            $options = Mage::getModel('sales/quote_item_option')
                ->getCollection()->addItemFilter($request->getParam('id'));
            $formData = array();
            $result = array();
            foreach ($options as $option) {
                $result[$option->getCode()] = $option->getValue();
            }
            if (isset($result['base_gc_value'])) {
                if (isset($result['gc_product_type']) && $result['gc_product_type'] == 'range') {
                    $currency = $store->getCurrentCurrencyCode();
                    $baseCurrencyCode = $store->getBaseCurrencyCode();
                    if ($currency != $baseCurrencyCode) {
                        $currentCurrency = Mage::getModel('directory/currency')->load($currency);
                        $baseCurrency = Mage::getModel('directory/currency')->load($baseCurrencyCode);
                        $value = $baseCurrency->convert($result['base_gc_value'], $currentCurrency);
                    } else {
                        $value = $result['base_gc_value'];
                    }
                }
            }
            foreach ($options as $option) {
                if ($option->getCode() == 'amount') {
                    if (isset($value)) {
                        $formData[$option->getCode()] = $value;
                    } else {
                        $formData[$option->getCode()] = $option->getValue();
                    }
                } else {
                    $formData[$option->getCode()] = $option->getValue();
                }
            }
            return new Varien_Object($formData);
        }
        return new Varien_Object();
    }
    /**
     * @return mixed
     */
    public function enableScheduleSend()
    {
        return Mage::helper('giftvoucher')->getInterfaceConfig('schedule');
    }
    /**
     * @return mixed
     */
    public function getGiftAmountDescription()
    {
        if (!$this->hasData('gift_amount_description')) {
            $product = $this->getProduct();
            $this->setData('gift_amount_description', '');
            if ($product->getShowGiftAmountDesc()) {
                if ($product->getGiftAmountDesc()) {
                    $this->setData('gift_amount_description', $product->getGiftAmountDesc());
                } else {
                    $this->setData('gift_amount_description',
                        Mage::helper('giftvoucher')->getInterfaceConfig('description')
                    );
                }
            }
        }
        return $this->getData('gift_amount_description');
    }
    /**
     * @return mixed
     */
    public function getAvailableTemplate()
    {
        $product = $this->getProduct();
        $productTemplate = $product->getGiftTemplateIds();
        if ($productTemplate) {
            $productTemplate = explode(',', $productTemplate);
        } else {
            $productTemplate = array();
        }
        $templates = Mage::getModel('giftvoucher/gifttemplate')->getCollection()
            ->addFieldToFilter('status', '1')
            ->addFieldToFilter('giftcard_template_id', array('in' => $productTemplate));
        return $templates->getData();
    }
    /**
     * @param $type
     * @return bool
     */
    public function getStyleDesignPattern($type){
        $templates = $this->getAvailableTemplate();
        $designPatternArr = array();
        foreach($templates as $template){
            $designPatternArr[] = $template['design_pattern'];
        }
        $key = array_search($type, $designPatternArr);
        if($key !== false){
            return $templates[$key];
        }
        return false;
    }
    /**
     * @return mixed
     */
    public function getPriceFormatJs()
    {
        $priceFormat = Mage::app()->getLocale()->getJsPriceFormat();
        return Mage::helper('core')->jsonEncode($priceFormat);
    }
    /**
     * @return bool
     */
    public function contentCondition()
    {
        $giftProduct = Mage::getModel('giftvoucher/product')->loadByProduct($this->getProduct());
        if ($giftProduct->getGiftcardDescription()) {
            return $giftProduct->getGiftcardDescription();
        }
        return false;
    }
    public function getGiftcardTemplateFile($templatePatternFile)
    {
        return Mage::getBaseDir('media').'/giftvoucher/design-template/'.$templatePatternFile. '.html';
    }
    public function getAllTemplatePatternId()
    {
        return Mage::helper('giftvoucher')->getAllTemplatePatternId();
    }
    public function getTemplateContent($templatePattern)
    {
        $variables = array();
        $variables['notes'] = '<%- notes %>';
        $variables['logo_url'] = '<%- logo_url %>';
        $variables['giftImageUrl'] = '<%- giftImageUrl %>';
        $variables['giftBackgroundUrl'] = '<%- giftBackgroundUrl %>';
        $variables['textColor'] = '<%- textColor %>';
        $variables['styleColor'] = '<%- styleColor %>';
        $variables['giftValue'] = '<%- giftValue %>';
        $variables['giftCode'] = '<%- giftCode %>';
        $variables['barcodeUrl'] = '<%- barcodeUrl %>';
        $variables['expiredDate'] = '<%- expiredDate %>';
        $variables['giftMessage'] = '<%- giftMessage %>';
        $variables['senderName'] = '<%- senderName %>';
        $variables['recipientName'] = '<%- recipientName %>';
        $variables['giftCardTitleText'] = '<%- giftCardTitleText %>';
        $variables['giftCardValueText'] = '<%- giftCardValueText %>';
        $variables['fromText'] = '<%- fromText %>';
        $variables['toText'] = '<%- toText %>';
        $variables['bkgValueUrl'] = '<%- bkgValueUrl %>';
        $variables['bkgTitleUrl'] = '<%- bkgTitleUrl %>';
        $fileContent = file_get_contents($this->getGiftcardTemplateFile($templatePattern));
        $fileContent = Mage::getModel('core/email_template_filter')
            ->setVariables($variables)
            ->filter($fileContent);
        return $fileContent;
    }
}