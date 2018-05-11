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
use Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_Status as PurchaseorderStatus;

class Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Invoice_Edit_Tab_Abstracttab 
    extends Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Abstracttab
{
    /** @var  Magestore_Purchaseordersuccess_Model_Purchaseorder_Invoice */
    protected $invoice;
    
    /**
     * @var string
     */
    protected $tabContainer = 'purchase_order_invoice_tabsJsTabs';

    /**
     * @var array
     */
    protected $reloadTabs = array('purchase_order_invoice_tabs_payment_list', 'purchase_order_invoice_tabs_refund_list');

    /**
     * Internal constructor, that is called from real constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->invoice = Mage::registry('current_purchase_order_invoice');
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
     * @param   array  $config
     * @param   mixed  $after
     * @return Varien_Data_Form_Element_Abstract
     */
    public function addField($elementId, $type, $config, $after=false)
    {
        if ($type == 'date')
            $className = 'Magestore_Purchaseordersuccess_Block_Adminhtml_Form_Field_Element_Date';
        else
            $className = 'Varien_Data_Form_Element_' . ucfirst(strtolower($type));
        if(!isset($config['style'])){
            $config['style'] = 'width: 300px';
        }
        $element = new $className($config);
        $element->setId($elementId);
        $element->setForm($this->getForm());
        return $element->toHtml();
    }

    public function getForm(){
        if(!$this->form)
            $this->form = $this->getLayout()
                ->createBlock('Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Invoice_Invoice_Form');
        return $this->form;
    }
}