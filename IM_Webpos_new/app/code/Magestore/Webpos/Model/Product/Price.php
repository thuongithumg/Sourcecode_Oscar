<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Product;

/**
 * class \Magestore\Webpos\Model\Product\Price
 * 
 * Price model
 * Methods:
 *  _applyOptionsPrice
 * 
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class Price extends \Magento\Catalog\Model\Product\Type\Price
{
    /**
     * 
     * @param \Magento\Catalog\Model\Product $product
     * @param float $qty
     * @param float $finalPrice
     * @return _applyOptionsPrice
     */
    protected function _applyOptionsPrice($product, $qty, $finalPrice)
    {
        if ($amount = $product->getCustomOption('price')) {
            $finalPrice = $amount->getValue();
        }
        return parent::_applyOptionsPrice($product, $qty, $finalPrice);
    }
}
