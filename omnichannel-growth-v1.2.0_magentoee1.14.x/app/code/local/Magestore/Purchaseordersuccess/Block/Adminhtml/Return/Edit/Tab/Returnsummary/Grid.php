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
use Magestore_Purchaseordersuccess_Model_Return_Options_Status as ReturnStatus;
use Magestore_Purchaseordersuccess_Model_Return_Item as ReturnItem;

class Magestore_Purchaseordersuccess_Block_Adminhtml_Return_Edit_Tab_Returnsummary_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * @var Magestore_Purchaseordersuccess_Model_Return
     */
    protected $returnRequest;

    /**
     * @var array
     */
    protected $selectedId;

    /**
     * @var array
     */
    protected $selectedItemData = array();
    /**
     * @var string
     */
    protected $hiddenInputField = 'selected_items';

    /**
     * @var array
     */
    protected $editFields = array('qty_returned');

    public function __construct()
    {
        parent::__construct();
        $this->setId('return_request_item_list');
        $this->setDefaultSort('return_item_id');
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
        $collection = $this->modifyCollection($collection);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return Magestore_Purchaseordersuccess_Model_Mysql4_Return_Item_Collection
     */
    protected function getDataColllection()
    {
        $collection = $this->returnRequest->getItems();
        return $collection;
    }

    /**
     * @return Magestore_Purchaseordersuccess_Model_Mysql4_Return_Item_Collection
     */
    protected function modifyCollection($collection)
    {
        $selectedItems = $this->_getSelectedItems();
        $filter = $this->getParam($this->getVarNameFilter(), null);
        $data = $this->helper('adminhtml')->prepareFilterString($filter);
        if (array_key_exists('in_return_request', $data)) {
            $condition = 'nin';
            if ($data['in_return_request'] == '1')
                $condition = 'in';
            $collection->addFieldToFilter('product_id', array($condition => $selectedItems));
        }
        $collection->getSelect()
            ->order(new \Zend_Db_Expr('FIELD(product_id, "' . implode('","', $selectedItems) . '") DESC'));
        return $collection;
    }

    /**
     * Prepare Purchase Item grid columns
     *
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $editable = false;
        $status = $this->returnRequest->getStatus();
        if ($status == ReturnStatus::STATUS_PENDING) {
            $editable = true;
            $this->addColumn(
                "in_return_request",
                array(
                    "type" => "checkbox",
                    "name" => "in_return_request",
                    "values" => $this->_getSelectedItems(),
                    "index" => "product_id",
                    "use_index" => true,
                    "header_css_class" => "col-select col-massaction a-center",
                    "column_css_class" => "col-select col-massaction",
                    "align" => "center"
                )
            );
        }
        $this->addColumn("product_sku",
            array(
                "header" => $this->__("SKU"),
                "index" => "product_sku",
                "sortable" => true,
            )
        );
        $this->addColumn("product_name",
            array(
                "header" => $this->__("Product Name"),
                "index" => "product_name",
                "sortable" => true,
            )
        );
        $this->addColumn("product_supplier_sku",
            array(
                "header" => $this->__("Supplier SKU"),
                "index" => "product_supplier_sku",
                "sortable" => true,
            )
        );
        $this->addColumn("qty_returned",
            array(
                "header" => $this->__("Qty Returned"),
                'renderer' => 'purchaseordersuccess/adminhtml_grid_column_renderer_text',
                "index" => "qty_returned",
                'type' => 'number',
                "editable" => $editable,
                "sortable" => true
            )
        );
        if ($status == ReturnStatus::STATUS_PENDING) {
            $this->addColumn("delete",
                array(
                    "header" => $this->__("Action"),
                    'renderer' => 'purchaseordersuccess/adminhtml_grid_column_renderer_returnGridDelete',
                    'filters' => false,
                    'sortable' => false,
                )
            );
        } else {
            $this->addColumn("qty_transferred",
                array(
                    "header" => $this->__("Delivered Qty"),
                    "index" => "qty_transferred",
                    'type' => 'number',
                    "sortable" => true
                )
            );
        }
        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            "*/purchaseordersuccess_return_item/grid",
            array("_current" => true, 'supplier_id' => $this->returnRequest->getSupplierId())
        );
    }

    /**
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl(
            '*/purchaseordersuccess_return_item/save',
            array("_current" => true, 'supplier_id' => $this->returnRequest->getSupplierId())
        );
    }

    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/purchaseordersuccess_return_item/delete');
    }

    /**
     * @return string
     */
    public function getReloadTotalUrl()
    {
        return $this->getUrl('*/purchaseordersuccess_return_item/reloadtotal', array('_current' => true));
    }

    /**
     * @return array
     */
    protected function _getSelectedItems()
    {
        if (empty($this->selectedId)) {
            $direction = $this->getParam($this->getVarNameDir(), $this->_defaultDir);
            if($direction == 'desc')
                $direction = 'asc';
            else
                $direction = 'desc';
            $collection = $this->getDataColllection()
                ->addFieldToFilter(ReturnItem::QTY_RETURNED, array('lteq' => 0))
                ->addOrder(
                    $this->getParam($this->getVarNameSort(), $this->_defaultSort),
                    $direction
                );
            /** @var ReturnItem $item */
            foreach ($collection as $item) {
                $this->selectedItemData[$item->getProductId()] = array(
                    ReturnItem::QTY_RETURNED => $item->getQtyReturned(),
                    ReturnItem::QTY_RETURNED . '_old' => $item->getQtyReturned(),
                );
            }
            $this->selectedId = array_keys($this->selectedItemData);
        }
        return $this->selectedId;
    }

    /**
     * @return string
     */
    public function getSelectedItems()
    {
        return Zend_Json::encode($this->_getSelectedItems());
    }

    /**
     * @return array
     */
    public function getEditFields()
    {
        return Zend_Json::encode($this->editFields);
    }
}