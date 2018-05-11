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
 * @package     Mage_Api2
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API ACL filter
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magestore_Inventorysuccess_Model_Api2_Acl_Filter extends Mage_Api2_Model_Acl_Filter
{
    public function getAllowedAttributes($operationType = null)
    {
        if (null === $this->_allowedAttributes) {
            /** @var $helper Mage_Api2_Helper_Data */
            $helper = Mage::helper('api2/data');

            if (null === $operationType) {
                $operationType = $helper->getTypeOfOperation($this->_resource->getOperation());
            }
            if ($helper->isAllAttributesAllowed('admin')) {
                $this->_allowedAttributes = array_keys($this->_resource->getAvailableAttributes(
                    'admin', $operationType
                ));
            } else {
                $this->_allowedAttributes = $helper->getAllowedAttributes(
                    'admin', $this->_resource->getResourceType(), $operationType
                );
            }
            // force attributes to be no filtered
            foreach ($this->_resource->getForcedAttributes() as $forcedAttr) {
                if (!in_array($forcedAttr, $this->_allowedAttributes)) {
                    $this->_allowedAttributes[] = $forcedAttr;
                }
            }
        }
        return $this->_allowedAttributes;
    }

}
