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
 * @package     Magestore_Suppliersuccess
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Suppliersuccess Adminhtml Block
 * 
 * @category    Magestore
 * @package     Magestore_Suppliersuccess
 * @author      Magestore Developer
 */
class Magestore_Suppliersuccess_Block_Adminhtml_Pricelist extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    
    /**
     * 
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_pricelist';
        $this->_blockGroup = 'suppliersuccess';
        $this->_headerText = Mage::helper('suppliersuccess')->__('Pricelist Management');
        parent::__construct();
        
        $this->removeButton('add');
        
        $this->_addButton('mass_delete', array(
            'label'        => Mage::helper('adminhtml')->__('Mass Remove'),
            'onclick'    => 'massRemovePricelist()',
            'class'        => 'delete',
        ));
        
        $this->_addButton('mass_update', array(
            'label'        => Mage::helper('adminhtml')->__('Mass Update'),
            'onclick'    => 'massUpdatePricelist()',
            'class'        => 'save',
        ));        
    }

    /**
     * Produce buttons HTML
     *
     * @param string $area
     * @return string
     */
    public function getButtonsHtml($area = null)
    {
        $out = parent::getButtonsHtml($area);
        
        $out .= Mage::app()->getLayout()->createBlock('coresuccess/adminhtml_widget_button')
                    ->setData(array(
                        'label' => Mage::helper('adminhtml')->__('Import Pricelist'),
                        'class' => 'import-csv',
                        'attributes' => array(
                            'data-toggle' => 'modal',
                            'data-target' => '#import_pricelist'
                        )
                    ))
                    ->toHtml();
        
        return $out;
    }    
}