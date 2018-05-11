<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Catalog\Product;

use Magento\Catalog\Model\ProductFactory;

/**
 * Class BundleOptionsBuilder
 * @package Magestore\Webpos\Model\Catalog\Product
 */
class BundleOptionsBuilder
{
    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var int|null
     */
    protected $productId = null;

    /**
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $_pricingHelper;

    /**
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * ConfigOptionsBuilder constructor.
     * @param ProductFactory $productFactory
     */
    public function __construct(
        ProductFactory $productFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->productFactory = $productFactory;
        $this->_objectManager = $objectManager;
        $this->_pricingHelper = $pricingHelper;
        $this->_storeManager = $storeManager;
    }

    /**
     * @param int $productId
     * @return void
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;
    }

    /**
     * @return int|null
     */
    protected function getProductId()
    {
        return $this->productId;
    }

    /**
     * @return BundleOptionsInterface[]|null
     */
    public function create()
    {
        if ($this->getProductId()) {
            $product = $this->productFactory->create()->load($this->getProductId());
            $bundleChilds = array();
            $store_id = $this->_storeManager->getStore()->getId();
            $options = $this->_objectManager->get('Magento\Bundle\Model\Option')->getResourceCollection()
                ->setProductIdFilter($product->getId())
                ->setPositionOrder();
            $options->joinValues($store_id);
            $typeInstance = $this->_objectManager->get('Magento\Bundle\Model\Product\Type');
            $selections = $typeInstance->getSelectionsCollection($typeInstance->getOptionsIds($product), $product);
            $price_type = $product->getData('price_type');
            foreach ($options->getItems() as $option) {
                $bundleChilds[$option->getId()]['title'] = $option->getTitle();
                $bundleChilds[$option->getId()]['required'] = $option->getRequired();
                $bundleChilds[$option->getId()]['type'] = $option->getType();
                $bundleChilds[$option->getId()]['id'] = $option->getId();
                $bundleChilds[$option->getId()]['product_id'] = $product->getId();
                foreach ($selections as $selection) {
                    $selection_price_type = $selection->getData('selection_price_type');
                    $selection_price_value = $selection->getData('selection_price_value');
                    $price = $selection->getData('price');
                    $selection_price = ($selection_price_type == 0) ? $selection_price_value : $price * $selection_price_value;

                    if ($price_type == 0) {
                        $selection_price = $price;
                    }
                    if ($option->getId() == $selection->getOptionId()) {
                        $bundleChilds[$option->getId()]['items'][$selection->getSelectionId()] = array();
                        $bundleChilds[$option->getId()]['items'][$selection->getSelectionId()]['name'] = $selection->getName();
                        $bundleChilds[$option->getId()]['items'][$selection->getSelectionId()]['qty'] = $selection->getSelectionQty();
                        $bundleChilds[$option->getId()]['items'][$selection->getSelectionId()]['price'] = $selection_price;
                        $bundleChilds[$option->getId()]['items'][$selection->getSelectionId()]['can_change_qty'] = $selection->getSelectionCanChangeQty();
                        $bundleChilds[$option->getId()]['items'][$selection->getSelectionId()]['is_default'] = $selection->getIsDefault();
                    }
                }
            };
            return $bundleChilds;
        }
    }

    /**
     *
     * @param string $price
     * @return string
     */
    public function formatPrice($price){
        return $this->_pricingHelper->currency($price,true,false);
    }
}
