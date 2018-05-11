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
class Magestore_Purchaseordersuccess_Block_Adminhtml_Form_Field_Shippingmethod
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    /**
     * @var StatusField
     */
    protected $_statusRenderer;

    /**
     * @var DescriptionField
     */
    protected $_description;

    /**
     * @var bool
     */
    protected $_hasDescription = false;

    /**
     * Retrieve status column renderer
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _getStatusRenderer()
    {
        if (!$this->_statusRenderer) {
            $this->_statusRenderer = $this->getLayout()->createBlock(
                'Magestore_Purchaseordersuccess_Block_Adminhtml_Form_Field_Status',
                '', 
                array('is_render_to_js_template' => true)
            );
        }
        return $this->_statusRenderer;
    }

    /**
     * Retrieve description column renderer
     * 
     * @return Mage_Core_Block_Abstract
     */
    protected function _getDescriptionRenderer(){
        if (!$this->_description) {
            $this->_description = $this->getLayout()->createBlock(
                'Magestore_Purchaseordersuccess_Block_Adminhtml_Form_Field_Element_Textarea'
            );
        }
        return $this->_description;
    }

    /**
     * Prepare to render
     *
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn('name', array('label' => $this->__('Name')));
        if($this->_hasDescription)
            $this->addColumn(
                'description',
                array('label' => $this->__('Description'), 'renderer' => $this->_getDescriptionRenderer())
            );
        $this->addColumn(
            'status',
            array('label' => $this->__('Status'), 'renderer' => $this->_getStatusRenderer())
        );
        $this->_addAfter = false;
    }

    /**
     * Prepare existing row data object
     *
     * @param Varien_Object
     */
    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->_getStatusRenderer()->calcOptionHash($row->getData('status')),
            'selected="selected"'
        );
    }
}