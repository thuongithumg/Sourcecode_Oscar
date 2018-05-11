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
 * @package     Magestore_Purchaseordersuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Purchaseordersuccess Helper
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
class Magestore_Purchaseordersuccess_Block_Adminhtml_Form_Field_Status extends Mage_Core_Block_Html_Select
{
    const ENABLE_VALUE = 1;
    const DISABLE_VALUE = 0;
    const ENABLE_LABEL = 'Enable';
    const DISABLE_LABEL = 'Disable';
    
    
    protected function getAllOptions(){
        return array(
            self::ENABLE_VALUE => Mage::helper('purchaseordersuccess')->__(self::ENABLE_LABEL), 
            self::DISABLE_VALUE => Mage::helper('purchaseordersuccess')->__(self::DISABLE_LABEL)
        );
    }
    
    /**
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            foreach ($this->getAllOptions() as $value => $label) {
                $this->addOption(
                    $value, 
                    addslashes($label)
                );
            }
        }
        return parent::_toHtml();
    }
}
