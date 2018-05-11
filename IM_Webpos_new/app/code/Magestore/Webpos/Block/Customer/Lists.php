<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Block\Customer;

/**
 * Class Lists
 * @package Magestore\Webpos\Block\Customer
 */
class Lists extends \Magestore\Webpos\Block\AbstractBlock
{

    /**
     * @return array
     */
    public function getCountryOptions()
    {
        $options = $this->getCountryCollection()->toOptionArray(false);
        return $options;
    }

    /**
     * @return mixed
     */
    public function getCountryCollection(){
        return $this->_objectManager->get('Magento\Directory\Helper\Data')->getCountryCollection();
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        $havePermission = $this->_permissionHelper->isAllowResource('Magestore_Webpos::manage_customer');
        if ($havePermission) {
            return parent::toHtml();
        } else {
            return '';
        }
    }

}
