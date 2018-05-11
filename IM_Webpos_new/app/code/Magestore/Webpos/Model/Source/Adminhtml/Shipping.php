<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Source\Adminhtml;

/**
 * class \Magestore\Webpos\Model\Source\Adminhtml\Shipping
 * 
 * Web POS Shipping source model
 * Methods:
 *  getAllowShippingMethods
 *  toOptionArray
 * 
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class Shipping implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Allow shipping methods array
     *
     * @var array
     */
    protected $_allowShippings = array();

    /**
     * Helper shipping object
     *
     * @var \Magestore\Webpos\Helper\Shipping
     */
    protected $_shippingHelper;

    /**
     * webpos shipping model
     *
     * @var \Magestore\Webpos\Model\Shipping\Shipping
     */
    protected $_shippingModel;

    /**
     * magento shipping config model
     *
     * @var \Magento\Shipping\Model\Config
     */
    protected $_shippingConfigModel;

    /**
     * @param \Magestore\Webpos\Helper\Shipping $shippingHelper
     * @param \Magestore\Webpos\Model\Shipping\Shipping $shippingModel
     * @param \Magento\Shipping\Model\Config $shippingConfigModel
     */
    public function __construct(
        \Magestore\Webpos\Helper\Shipping $shippingHelper,
        \Magestore\Webpos\Model\Shipping\ShippingFactory $shippingModel,
        \Magento\Shipping\Model\Config $shippingConfigModel
    ) {
        $this->_shippingHelper = $shippingHelper;
        $this->_shippingModel = $shippingModel;
        $this->_shippingConfigModel = $shippingConfigModel;
        $this->_allowShippings = array('webpos_shipping','flatrate','freeshipping');
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $collection = $this->_shippingConfigModel->getActiveCarriers();
        $options = array();
        if (count($collection) > 0) {
            foreach ($collection as $code => $carrier) {
                if (!in_array($code, $this->_allowShippings))
                    continue;
                $title = $carrier->getConfigData('title').' - '.$carrier->getConfigData('name');
                $options[] = array('value' => $code, 'label' => $title);
            }
        }
        return $options;
    }

    /**
     * get shipping methods for pos
     *
     * @return array
     */
    public function getPosShippingMethods()
    {
        $collection = $this->_shippingConfigModel->getActiveCarriers();
        $shippingList = array();
        if(count($collection) > 0) {
            foreach ($collection as $code => $carrier) {
                if (!in_array($code, $this->_allowShippings))
                    continue;
                if (!$this->_shippingHelper->isAllowOnWebPOS($code))
                    continue;
                $shippingModel = $this->_shippingModel->create();
                $isDefault = '0';
                if($code == $this->_shippingHelper->getDefaultShippingMethod()) {
                    $isDefault = '1';
                }
                $method_code = $code;
                if($code == 'webpos_shipping') {
                    $method_code = 'storepickup';
                }
                $methodCode = $code.'_'.$method_code;
                $methodTitle = $carrier->getConfigData('title').' - '.$carrier->getConfigData('name');
                $methodPrice = ($carrier->getConfigData('price') != null) ? $carrier->getConfigData('price') : '0';
                $methodPriceType = ($carrier->getConfigData('type') != null) ? $carrier->getConfigData('type') : '';
                $methodDescription = ($carrier->getConfigData('description') != null) ?
                                      $carrier->getConfigData('description') : '0';
                $methodSpecificerrmsg = ($carrier->getConfigData('specificerrmsg') != null) ?
                                         $carrier->getConfigData('specificerrmsg') : '';
                $shippingModel->setCode($methodCode);
                $shippingModel->setTitle($methodTitle);
                $shippingModel->setPrice($methodPrice);
                $shippingModel->setDesciption($methodDescription);
                $shippingModel->setErrorMessage($methodSpecificerrmsg);
                $shippingModel->setPriceType($methodPriceType);
                $shippingModel->setIsDefault($isDefault);
                $shippingList[] = $shippingModel->getData();
            }
        }
        return $shippingList;
    }

    /**
     * get array of allow shipping methods
     * @return array
     */
    public function getAllowShippingMethods()
    {
        return $this->_allowShippings;
    }

}
