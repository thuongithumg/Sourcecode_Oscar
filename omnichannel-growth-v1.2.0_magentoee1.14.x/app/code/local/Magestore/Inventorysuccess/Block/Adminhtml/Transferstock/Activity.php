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
 * @package     Magestore_Inventorysuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Inventorysuccess Adminhtml Block
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Transferstock_Activity extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Magestore_Inventorysuccess_Block_Adminhtml_Transferstock_Activity constructor.
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_transferstock_activity';
        $this->_blockGroup = 'inventorysuccess';
        $this->_headerText = $this->_getHeaderText();
        parent::__construct();
        $this->_removeButton('add');
        $this->_addButton('back', array(
            'label' => $this->getBackButtonLabel(),
            'onclick' => 'window.history.back();',
            'class' => 'back',
        ));
    }


    /**
     * get header text
     * @return string
     */
    public function _getHeaderText()
    {
        $activity = Mage::registry('transfer_activity');
        if ($activity->getActivityType() == Magestore_Inventorysuccess_Model_Transferstock_Activity::ACTIVITY_TYPE_RECEIVING) {
            return Mage::helper('inventorysuccess')->__('View Receiving');
        } else {
            return Mage::helper('inventorysuccess')->__('View Delivery');
        }
    }
}
