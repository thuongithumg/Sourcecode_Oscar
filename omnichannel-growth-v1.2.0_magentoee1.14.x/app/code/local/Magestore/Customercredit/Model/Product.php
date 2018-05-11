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
 * @package     Magestore_Storecredit
 * @module      Storecredit
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * Class Magestore_Customercredit_Model_Product
 */
class Magestore_Customercredit_Model_Product extends Mage_Rule_Model_Rule
{

    public function _construct()
    {
        parent::_construct();
        $this->_init('customercredit/product');
    }

    /**
     * @param $product
     * @return $this
     */
    public function loadByProduct($product)
    {
        if (is_object($product)) {
            if ($product->getId()) {
                return $this->load($product->getId(), 'product_id');
            }
            return $this;
        }
        if ($product) {
            return $this->load($product, 'product_id');
        }
        return $this;
    }

}
