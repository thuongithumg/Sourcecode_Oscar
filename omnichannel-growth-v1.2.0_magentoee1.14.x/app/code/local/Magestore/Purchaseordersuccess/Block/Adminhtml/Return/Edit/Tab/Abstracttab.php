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
 * Purchaseordersuccess Adminhtml Block
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */

class Magestore_Purchaseordersuccess_Block_Adminhtml_Return_Edit_Tab_Abstracttab extends Mage_Adminhtml_Block_Template
{
    /**
     * @var Magestore_Purchaseordersuccess_Model_Return
     */
    protected $returnRequest;

    /**
     * @var Mage_Adminhtml_Block_Widget_Grid
     */
    protected $blockGrid;

    /**
     * @var string
     */
    protected $tabContainer = 'return_request_tabsJsTabs';

    /**
     * @var array
     */
    protected $reloadTabs = array();

    /**
     * Internal constructor, that is called from real constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->returnRequest = Mage::registry('current_return_request');
    }

    /**
     * Retrieve instance of grid block
     *
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                'adminhtml/widget_grid',
                'widget.grid'
            );
        }
        return $this->blockGrid;
    }

    /**
     * Return HTML of grid block
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getBlockGrid()->toHtml();
    }

    public function getTabContainer(){
        return $this->tabContainer;
    }

    public function getReloadTabs(){
        return Zend_Json::encode($this->reloadTabs);
    }

    /**
     * @return string
     */
    public function getJsParentObjectName(){
        return $this->getLayout()
            ->createBlock('purchaseordersuccess/adminhtml_return_edit_tab_returnsummary_grid')
            ->getJsObjectName();
    }

    /**
     * Add child element
     *
     * if $after parameter is false - then element adds to end of collection
     * if $after parameter is null - then element adds to befin of collection
     * if $after parameter is string - then element adds after of the element with some id
     *
     * @param   string $elementId
     * @param   string $type
     * @param   array $config
     * @param   mixed $after
     * @return Varien_Data_Form_Element_Abstract
     */
    public function addField($elementId, $type, $config, $after = false)
    {
        if ($type == 'date')
            $className = 'Magestore_Purchaseordersuccess_Block_Adminhtml_Form_Field_Element_Date';
        else
            $className = 'Varien_Data_Form_Element_' . ucfirst(strtolower($type));
        $element = new $className($config);
        $element->setId($elementId);
        $element->setForm($this->getForm());
        return $element->toHtml();
    }

    /**
     * Check if Magestore_Barcodesuccess is enable
     *
     * @return bool
     */
    public function isBarcodeSuccessEnable()
    {
        return Mage::helper('purchaseordersuccess')->isModuleEnabled('Magestore_Barcodesuccess');
    }

    /**
     * allowed file types
     *
     * @var array
     */
    protected $allowFileTypes = array(
        'text/csv',
        'application/vnd.ms-excel'
    );

    /**
     *
     * @return string
     */
    public function getAllowedFileTypes()
    {
        return Zend_Json::encode($this->allowFileTypes);
    }
}