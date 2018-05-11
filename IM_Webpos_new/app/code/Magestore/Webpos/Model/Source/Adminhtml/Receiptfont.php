<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Source\Adminhtml;

/**
 * class \Magestore\Webpos\Model\Source\Adminhtml\Receiptfont
 * 
 * Receiptfont source model
 * Methods:
 *  getAllowFonts
 *  toOptionArray
 * 
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class Receiptfont implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * Options array
     *
     * @var array
     */
    protected $_options;

    /**
     * @param 
     */
    public function __construct()
    {
        $this->_options = array('monospace' => __('Monospace'), 'sans-serif' => __('Sans-serif'));
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (!count($this->_options))
            return;

        $options = array();
        foreach ($this->_options as $value => $label) {
            $options[] = array('value' => $value, 'label' => $label);
        }
        return $options;
    }

    /**
     * @return array
     */
    public function getAllowFonts()
    {
        return $this->$_options;
    }

}
