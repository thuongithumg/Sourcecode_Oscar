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

class Magestore_Purchaseordersuccess_Block_Adminhtml_Return_Edit_Tab_Returnsummary_Abstractmodalgrid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * @var Magestore_Purchaseordersuccess_Model_Return
     */
    protected $returnRequest;

    /**
     * @var Magestore_Suppliersuccess_Model_Supplier
     */
    protected $supplier;

    /**
     * Grid ID
     *
     * @var string
     */
    protected $gridId = 'return_request_all_supplier_product_list';
    /**
     * @var string
     */
    protected $hiddenInputField = 'selected_all_supplier_products';

    /**
     * @var string
     */
    protected $modalName = 'allsupplierproduct';

    /**
     * @var array
     */
    protected $editFields = array('qty_returned');

    protected $availableField = 'main_table.qty_returned - main_table.qty_transferred';


    public function __construct()
    {
        parent::__construct();
        $this->setId($this->gridId);
        $this->setDefaultSort('supplier_product_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
        $this->returnRequest = Mage::registry('current_return_request');
    }

    /**
     * Set hidden input field name for selected products
     *
     * @param $name
     */
    protected function setHiddenInputField($name)
    {
        $this->hiddenInputField = $name;
    }

    /**
     * get hidden input field name for selected products
     *
     * @return string
     */
    public function getHiddenInputField()
    {
        return $this->hiddenInputField;
    }

    /**
     * Prepare collection for grid product
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->getDataColllection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return Magestore_Purchaseordersuccess_Model_Mysql4_Return_Item_Collection
     */
    protected function getDataColllection()
    {
        /** @var Magestore_Suppliersuccess_Model_Mysql4_Supplier_Product_Collection $collection */
        $collection = Mage::getResourceModel('suppliersuccess/supplier_product_collection');

        // check with warehouse
        $prdOnWarehouse = Mage::getResourceModel('inventorysuccess/warehouse_product_collection');
        $prdOnWarehouse->getSelect()->where("main_table.stock_id = ".$this->returnRequest->getWarehouseId());
        $prdIdOnWarehouse = $prdOnWarehouse->getColumnValues('product_id');

        $collection->addFieldToFilter('product_id', ['in' => $prdIdOnWarehouse]);
        $collection->getSelect()->columns(array(
            'qty_returned' => "(0)",
        ));
        $supplierId = $this->getRequest()->getParam('supplier_id', null);
        $collection->addFieldToFilter('supplier_id', $supplierId);
        if ($supplierId) {
            $productIds = $this->returnRequest->getItems()->getColumnValues('product_id');
            if (!empty($productIds))
                $collection->addFieldToFilter('product_id', array('nin' => $productIds));
        }
        return $collection;
    }

    /**
     * Prepare grid columns
     *
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            "product_id",
            array(
                "type" => "checkbox",
                "name" => "product_id",
                "index" => "product_id",
                "use_index" => true,
                "header_css_class" => "col-select col-massaction a-center",
                "column_css_class" => "col-select col-massaction",
                "align" => "center"
            )
        );
        $this->addColumn("product_sku",
            array(
                "header" => $this->__("Product SKU"),
                "index" => "product_sku",
                "width" => '120'
            )
        );
        $this->addColumn("product_supplier_sku",
            array(
                "header" => $this->__("Supplier SKU"),
                "index" => "product_supplier_sku",
                "width" => '120'
            )
        );
        $this->addColumn("product_name",
            array(
                "header" => $this->__("Product Name"),
                "index" => "product_name",
                "width" => '200'
            )
        );
        $this->addColumn("qty_returned",
            array(
                "header" => $this->__("Qty Returned"),
                'renderer' => 'purchaseordersuccess/adminhtml_grid_column_renderer_text',
                "index" => "qty_returned",
                'type' => 'number',
                "editable" => true,
                "sortable"  => false,
                "filter"    => false
            )
        );

        $this->modifyColumn();

        return parent::_prepareColumns();
    }

    /**
     * Modify modal grid columns
     *
     * @return $this
     */
    protected function modifyColumn()
    {
        return $this;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            "*/purchaseordersuccess_return_item/modal",
            array("_current" => true, 'supplier_id' => $this->returnRequest->getSupplierId(), 'modal' => $this->modalName)
        );
    }

    /**
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl(
            '*/purchaseordersuccess_return_item/save',
            array("_current" => true, 'supplier_id' => $this->returnRequest->getSupplierId(), 'modal' => $this->modalName)
        );
    }

    /**
     * @return array
     */
    public function getEditFields()
    {
        return Zend_Json::encode($this->editFields);
    }

    /**
     * @return string
     */
    public function getSelectedItems()
    {
        return Zend_Json::encode(array());
    }

    /**
     * @return string
     */
    public function getReloadTotalUrl()
    {
        return '';
//        return $this->getUrl('*/purchaseordersuccess_purchaseorder_item/reloadtotal', array('_current' => true));
    }

    /**
     * Apply `qty` filter to product grid.
     *
     * @param $collection
     * @param $column
     */
    protected function _filterAvailableQtyCallback($collection, $column)
    {
        if (!($value = $column->getFilter()->getValue())) {
            return;
        }
        if ($column->getIndex() == 'available_qty') {
            if (isset($value['from'])) {
                $collection->addFieldToFilter(
                    new \Zend_Db_Expr($this->availableField),
                    array(
                        'gteq' => $value['from']
                    )
                );
            }
            if (isset($value['to'])) {
                $collection->addFieldToFilter(
                    new \Zend_Db_Expr($this->availableField),
                    array(
                        'lteq' => $value['to']
                    )
                );
            }
        }
        return $collection;
    }
}