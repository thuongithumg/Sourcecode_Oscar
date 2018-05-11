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
 * Class Magestore_Storepickup_Block_Adminhtml_Specialday_Edit_Tabs
 */
class Magestore_Storepickup_Block_Adminhtml_Specialday_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    /**
     * Magestore_Storepickup_Block_Adminhtml_Specialday_Edit_Tabs constructor.
     */
    public function __construct()
  {
      parent::__construct();
      $this->setId('specialday_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('storepickup')->__('Special Day Information'));
  }

    /**
     * @return mixed
     */
    protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('storepickup')->__('Special Day Information'),
          'title'     => Mage::helper('storepickup')->__('Special Day Information'),
          'content'   => $this->getLayout()->createBlock('storepickup/adminhtml_specialday_edit_tab_form')->toHtml(),
      ));

      return parent::_beforeToHtml();
  }
}