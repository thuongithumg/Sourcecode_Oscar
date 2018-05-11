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
 * Warehouse Edit Stock On Hand Tab Block
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_ManageStock_Product_StockMovement
    extends Magestore_Inventorysuccess_Block_Adminhtml_StockMovement_Grid
{
    /**
     * @param $collection
     * @return mixed
     */
    protected function modifyCollection($collection)
    {
        $collection->addProductToFilter($this->getRequest()->getParam('id'));
        return $collection;
    }

    protected function _prepareColumns()
    {
        parent::_prepareColumns();
        $params = array('_current' => true, 'id' => $this->getRequest()->getParam('id'));
        $this->_exportTypes = array(
            new Varien_Object(
                array(
                    'url' => $this->getUrl('*/*/exportStockMovementCsv', $params),
                    'label' => $this->__('CSV')
                )
            ),
            new Varien_Object(
                array(
                    'url' => $this->getUrl('*/*/exportStockMovementXml', $params),
                    'label' => $this->__('Excel XML')
                )
            )
        );
    }

    /**
     * @return $this
     */
    protected function modifyColumn()
    {
        $this->removeColumn('product_sku');
        return $this;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl("*/inventorysuccess_managestock_product/stockmovement", array("_current" => true));
    }
}