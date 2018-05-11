<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Product\Type;

use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * class \Magestore\Webpos\Model\Product\Type\CustomSale
 * 
 * Web POS CustomSale model
 * Use to work with POS location table
 * Methods:
 *  _prepareCustomSale
 *  _prepareProduct
 *  deleteTypeSpecificData
 *  isSalable
 *  isVirtual
 *  prepareForCart
 * 
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class CustomSale extends \Magento\Catalog\Model\Product\Type\AbstractType
{   
    /**
     *
     * @var \Magento\Framework\App\RequestInterface 
     */
    protected $_request;
    
    /**
     * 
     * @param \Magento\Catalog\Model\Product\Option $catalogProductOption
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Catalog\Model\Product\Type $catalogProductType
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDb
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Psr\Log\LoggerInterface $logger
     * @param ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Magento\Catalog\Model\Product\Option $catalogProductOption,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Model\Product\Type $catalogProductType,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDb,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Registry $coreRegistry,
        \Psr\Log\LoggerInterface $logger,
        ProductRepositoryInterface $productRepository,
        \Magento\Framework\App\RequestInterface $request
    ) {

        $this->_request = $request;

        parent::__construct(
            $catalogProductOption,
            $eavConfig,
            $catalogProductType,
            $eventManager,
            $fileStorageDb,
            $filesystem,
            $coreRegistry,
            $logger,
            $productRepository
        );
    }

    /**
     * 
     * @param \Magento\Framework\DataObject $buyRequest
     * @param \Magento\Catalog\Model\Product $product
     * @return array|string
     */
    public function prepareForCart(\Magento\Framework\DataObject $buyRequest, $product)
    {
        if (is_null($product)) {
            $product = $this->getProduct();
        }
        $result = parent::prepareForCart($buyRequest, $product);
        if (is_string($result)) {
            return $result;
        }
        reset($result);
        $product = current($result);
        $result = $this->_prepareCustomSale($buyRequest, $product, null);
        return $result;
    }
    
    /**
     * 
     * @param \Magento\Framework\DataObject $buyRequest
     * @param \Magento\Catalog\Model\Product $product
     * @param string $processMode
     * @return array|string
     */
    protected function _prepareProduct(\Magento\Framework\DataObject $buyRequest, $product, $processMode)
    {
        if (is_null($product)) {
            $product = $this->getProduct();
        }
        $result = parent::_prepareProduct($buyRequest, $product, $processMode);
        if (is_string($result)) {
            return $result;
        }
        reset($result);
        $product = current($result);
        $result = $this->_prepareCustomSale($buyRequest, $product);
        return $result;
    }
    
    /**
     * 
     * @param \Magento\Framework\DataObject $buyRequest
     * @param \Magento\Catalog\Model\Product $product
     * @param string $processMode
     * @return array
     */
    protected function _prepareCustomSale(\Magento\Framework\DataObject $buyRequest, $product, $processMode = null)
    {
        $options = $buyRequest->getData('options');
        if($options && isset($options['is_virtual'])){
            $product->addCustomOption('is_virtual', $options['is_virtual']);
        }
        if($options && isset($options['tax_class_id'])){
            $product->addCustomOption('tax_class_id', $options['tax_class_id']);
        }
        if($options && isset($options['name'])){
            $product->addCustomOption('name', $options['name']);
        }
        if($options && isset($options['price'])){
            $product->addCustomOption('price', $options['price']);
        }
        if($options && isset($options['custom_sale_description'])){
            $product->addCustomOption('custom_sale_description', $options['custom_sale_description']);
        }
        return array($product);
    }

    /**
     * 
     * @param \Magento\Catalog\Model\Product $product
     * @return boolean
     */
    public function isVirtual($product = null)
    {
        if (is_null($product)) {
            $product = $this->getProduct();
        }
        if ($isVirtual = $product->getCustomOption('is_virtual')) {
            return (bool) $isVirtual->getValue();
        }
        return true;
    }
    
    /**
     * 
     * @param \Magento\Catalog\Model\Product $product
     * @return boolean
     */
    public function isSalable($product = null)
    {
        $route = $this->_request->getModuleName();

        return ($route == 'webpos' || $route == 'rest')?parent::isSalable($product):false;
    }
    
    /**
     * 
     * @param \Magento\Catalog\Model\Product $product
     */
    public function deleteTypeSpecificData(\Magento\Catalog\Model\Product $product)
    {
    }
}