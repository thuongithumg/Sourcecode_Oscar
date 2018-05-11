<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Tax
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Magestore_Purchaseordersuccess_Model_System_Config_Source_Product
{
    CONST TYPE_SUPPLIER = 1;
    CONST TYPE_STORE = 2;

    protected $_options;

    public function __construct()
    {
        $this->_options = array(
            array(
                'value' => self::TYPE_SUPPLIER,
                'label' => Mage::helper('purchaseordersuccess')->__('Supplier')
            ),
            array(
                'value' => self::TYPE_STORE,
                'label' => Mage::helper('purchaseordersuccess')->__('All stores')
            ),
        );
    }

    public function toOptionArray()
    {
        return $this->_options;
    }
}
