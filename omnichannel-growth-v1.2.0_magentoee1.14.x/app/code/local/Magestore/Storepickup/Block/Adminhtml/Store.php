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
 * @package     Magestore_Storepickup
 * @module      Storepickup
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * Class Magestore_Storepickup_Block_Adminhtml_Store
 */
class Magestore_Storepickup_Block_Adminhtml_Store extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Magestore_Storepickup_Block_Adminhtml_Store constructor.
     */
    public function __construct()
  {
    $this->_controller = 'adminhtml_store';
    $this->_blockGroup = 'storepickup';
    $this->_headerText = Mage::helper('storepickup')->__('Store Manager');
    $this->_addButtonLabel = Mage::helper('storepickup')->__('Add Store');
    parent::__construct();
	$this->_addButton('import_store', array(
		'label'     => Mage::helper('storepickup')->__('Import Store'),
		'onclick'   => 'location.href=\''. $this->getUrl('*/storepickup_import/importstore',array()) .'\'',
		'class'     => 'add',
	),1000);		
  }
}