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
 * Purchaseordersuccess Model
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
class Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_AbstractOption
{
    /**
     * Status value
     */
    const STATUS_ENABLE = 1;

    const STATUS_DISABLE = 0;

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionHash()
    {
        return array(
            self::STATUS_ENABLE => Mage::helper('purchaseordersuccess')->__('Enable'),
            self::STATUS_DISABLE => Mage::helper('purchaseordersuccess')->__('Disable')
        );
    }

    /**
     * get model option hash as array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $options = array();
        foreach ($this->getOptionHash() as $value => $label) {
            $options[] = array(
                'value' => $value,
                'label' => $label
            );
        }
        return $options;
    }

    /**
     * Return array of options as value-label pairs.
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return $this->getOptionArray();
    }

    /**
     * Return array of options as key-value pairs.
     *
     * @return array Format: array('<key>' => '<value>', '<key>' => '<value>', ...)
     */
    public function toOptionHash()
    {
        return $this->getOptionHash();
    }

    /**
     * @param string $value
     * @return array
     */
    public function unserializeArray($value)
    {
        if (!is_array($value)) {
            return unserialize($value);
        }
        return $value;
    }
}