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
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Created by PhpStorm.
 * User: Quoc Viet
 * Date: 07/07/2015
 * Time: 10:20 SA
 */
class Magestore_Webpos_Block_Adminhtml_Userlocation extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_userlocation';
        $this->_blockGroup = 'webpos';
        $this->_headerText = Mage::helper('webpos')->__('Sales By Locations');
        $this->_addButtonLabel = Mage::helper('webpos')->__('Add Location');
        parent::__construct();
        $buttonList = array();
        $container = new Varien_Object();
        $container->setButtons($buttonList);
        Mage::dispatchEvent('webpos_location_edit_container',
            array(
                'container' => $container
            ));
        $buttonList = $container->getButtons();
        $this->prepareButtons($buttonList);
    }

    /**
     * get mapping url
     *
     * @return string
     */
    public function prepareButtons($buttonList)
    {
        foreach ($buttonList as $button) {
            $this->_addButton($button['key'], array(
                'label'     => $button['label'],
                'onclick'   => 'setLocation(\'' . $this->getUrl($button['url']) .'\')',
                'class'     => $button['class'],
            ));
        }
    }

    /**
     * get mapping url
     *
     * @return string
     */
    public function getMappingUrl()
    {
        return Mage::getUrl('*/inventorysuccess_warehouse_location/mapping');
    }
}