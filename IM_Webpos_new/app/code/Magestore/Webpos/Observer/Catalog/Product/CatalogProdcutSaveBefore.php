<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Observer\Catalog\Product;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class CatalogProdcutSaveAfter
 * @package Magestore\Webpos\Observer\Catalog\Product
 */
class CatalogProdcutSaveBefore implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * CatalogProdcutSaveAfter constructor.
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\Registry $registry
    ) {
        $this->_registry = $registry;
    }

    /**
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        if(!$this->_registry->registry('check_save')) {
            $this->_registry->register('check_save',1);
            $product = $observer->getProduct();
            $product->setUpdatedDatetime(date('Y-m-d H:i:s'));
        }
        return $this;
    }
}