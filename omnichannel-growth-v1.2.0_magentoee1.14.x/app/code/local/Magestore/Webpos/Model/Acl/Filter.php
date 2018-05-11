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
 * API ACL filter
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magestore_Webpos_Model_Acl_Filter extends Mage_Api2_Model_Acl_Filter
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
