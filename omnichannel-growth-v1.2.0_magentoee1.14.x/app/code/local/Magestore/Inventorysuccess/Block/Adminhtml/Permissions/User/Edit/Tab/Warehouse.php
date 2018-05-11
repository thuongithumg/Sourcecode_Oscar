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
 * Permission User Edit Warehouse Tab Block
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Permissions_User_Edit_Tab_Warehouse 
    extends Mage_Adminhtml_Block_Template
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * @var string
     */
    protected $_template = 'inventorysuccess/permission/user/edit/tab/warehouse.phtml';

    /**
     * @var Magestore_Inventorysuccess_Block_Adminhtml_Warehouse_Edit_Tab_Stockonhand_Grid
     */
    protected $blockGrid;
    
    /**
     * Constructor
     * Prepare grid parameters
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Retrieve instance of grid block
     *
     * @return Magestore_Inventorysuccess_Block_Adminhtml_Warehouse_Edit_Tab_Stockonhand_Grid
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                'inventorysuccess/adminhtml_permissions_user_edit_tab_warehouse_grid',
                'permission.user.edit.tab.inventorysuccess.warehouse.grid'
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
    
    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Warehouses');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Warehouses');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return true
     */
    public function canShowTab()
    {
        if($this->getRequest()->getParam('user_id'))
            return true;
        return false;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        if($this->getRequest()->getParam('user_id'))
            return false;
        return true;
    }
}