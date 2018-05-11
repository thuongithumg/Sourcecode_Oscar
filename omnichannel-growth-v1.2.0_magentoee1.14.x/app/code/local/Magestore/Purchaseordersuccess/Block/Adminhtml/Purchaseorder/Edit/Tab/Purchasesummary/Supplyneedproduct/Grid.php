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
use Magestore_Purchaseordersuccess_Model_Purchaseorder_Item as PurchaseorderItem;

class Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Purchasesummary_Supplyneedproduct_Grid
    extends Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Purchasesummary_Abstractmodalgrid
{
    /**
     * Grid ID
     *
     * @var string
     */
    protected $gridId = 'purchase_order_supply_need_product_list';

    /**
     * @var string
     */
    protected $hiddenInputField = 'selected_supply_need_products';

    /**
     * @var string
     */
    protected $modalName = 'supplyneedproduct';

    protected $mappingField = array(
        'product_supplier_sku' => 'supplier_product.product_supplier_sku',
        'current_qty' => 'warehouse_product.current_qty',
        'current_cost' => 'supplier_product.cost',
        'cost' => 'supplier_product.cost'
    );

    /**
     * @return Magestore_Purchaseordersuccess_Model_Mysql4_Purchaseorder_Item_Collection
     */
    protected function getDataColllection()
    {
        $rate = $this->purchaseOrder->getCurrencyRate();
        $topFilter = $this->prepareSupplyNeedParams();
        $collection = Magestore_Coresuccess_Model_Service::supplyNeedsService()
            ->getProductSupplyNeedsCollection($topFilter, null, null);
        $readAdapter = $collection->getResource()
            ->getReadConnection();
        $supplierId = $this->getRequest()->getParam('supplier_id', null);
        $purchaseId = $this->getRequest()->getParam('id', null);
        if(Mage::helper('purchaseordersuccess')->isProductFromSupplier()) {
            $conditions = 'e.entity_id = supplier_product.product_id';
            if ($supplierId)
                $conditions .= ' AND supplier_product.supplier_id = ' . $supplierId;
            $collection->getSelect()->joinInner(
                array('supplier_product' => Mage::getSingleton('core/resource')->getTableName('os_supplier_product')),
                $conditions,
                '*'
            )->columns(array(
                'cost' => "ROUND(supplier_product.cost * {$rate}, 2)",
                'qty_orderred' => "(0)",
            ));
        } else {
            $collection->getSelect()->columns(array(
                'cost' => "ROUND({$rate} * 0, 2)",
                'qty_orderred' => "(0)",
                'product_id' => "e.entity_id",
            ));
        }
        if ($purchaseId) {
            $productIds = $this->purchaseOrder->getItems()
                ->getColumnValues(PurchaseorderItem::PRODUCT_ID);
            if (!empty($productIds))
                $collection->addFieldToFilter('entity_id', array('nin' => $productIds));
        }
        return $collection;
    }

    /**
     * Prepare supply need params
     *
     * @return array
     */
    public function prepareSupplyNeedParams()
    {
        $params = array(
            'warehouse_ids' => explode(',', $this->getRequest()->getParam('warehouse_ids')[0]),
            'sales_period' => $this->getRequest()->getParam(
                'sales_period',
                'last_7_days'
            ),
            'from_date' => $this->getRequest()->getParam('from_date'),
            'to_date' => $this->getRequest()->getParam('to_date'),
            'forecast_date_to' => $this->getRequest()->getParam('forecast_date_to'),
        );
        return base64_encode(serialize($params));
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
                "align" => "center",
                "filter"    => false
            )
        );
        $this->addColumn("sku",
            array(
                "header" => $this->__("Product SKU"),
                "index" => "sku",
                "sortable"  => false,
            )
        );
        if(Mage::helper('purchaseordersuccess')->isProductFromSupplier()) {
            $this->addColumn("product_supplier_sku",
                array(
                    "header" => $this->__("Supplier SKU"),
                    "index" => "product_supplier_sku",
                    "sortable" => false,
                    'filter_condition_callback' => array($this, '_filterFieldCallback')
                )
            );
        }
        $this->addColumn("name",
            array(
                "header" => $this->__("Product Name"),
                "index" => "name",
                "sortable"  => false,
            )
        );
        if(Mage::helper('purchaseordersuccess')->isProductFromSupplier()) {
            $this->addColumn("current_cost",
                array(
                    "header" => $this->__("Current Cost") . ' (' . $this->purchaseOrder->getCurrencyCode() . ')',
                    "index" => "cost",
                    'type' => 'currency',
                    'currency_code' => (string)$this->purchaseOrder->getCurrencyCode(),
                    'rate' => 1,
                    "sortable" => false,
                    'filter_condition_callback' => array($this, '_filterFieldCallback')
                )
            );
        } else {
            $this->addColumn("current_cost",
                array(
                    "header" => $this->__("Current Cost") . ' (' . $this->purchaseOrder->getCurrencyCode() . ')',
                    "index" => "cost",
                    'type' => 'currency',
                    'currency_code' => (string)$this->purchaseOrder->getCurrencyCode(),
                    'rate' => 1,
                    "sortable" => false,
                    'filter' => false,
                    'filter_condition_callback' => array($this, '_filterFieldCallback')
                )
            );
        }
        $this->addColumn("cost",
            array(
                "header" => $this->__("Purchase Cost") . ' (' . $this->purchaseOrder->getCurrencyCode() . ')',
                'renderer' => 'purchaseordersuccess/adminhtml_grid_column_renderer_text',
                "index" => "cost",
                'type' => 'number',
                "editable" => true,
                'filter' => false,
                'sortable' => false
            )
        );
        $this->addColumn("qty_orderred",
            array(
                "header" => $this->__("Ordered Qty"),
                'renderer' => 'purchaseordersuccess/adminhtml_grid_column_renderer_text',
                "index" => "qty_orderred",
                'type' => 'number',
                "editable" => true,
                "sortable" => false,
                "filter" => false
            )
        );

        $this->modifyColumn();
        $this->sortColumnsByOrder();
        return $this;
    }

    /**
     * Modify modal grid columns
     *
     * @return $this
     */
    protected function modifyColumn()
    {
        $this->addColumnAfter("current_qty",
            array(
                "header" => $this->__("Current Qty"),
                "index" => "current_qty",
                'type' => 'number',
                "sortable"  => false,
                'filter_condition_callback' => array($this, '_filterFieldCallback')
            ),
            'name'
        );
        $this->addColumnAfter("avg_qty_ordered",
            array(
                "header" => $this->__("Sold Qty per Day"),
                "index" => "avg_qty_ordered",
                "sortable" => false,
                "filter" => false
            ),
            'current_qty'
        );
        $this->addColumnAfter("availability_date",
            array(
                "header" => $this->__("Availibility Date"),
                "index" => "availability_date",
                "sortable" => false,
                "filter" => false
            ),
            'avg_qty_ordered'
        );
        $this->addColumnAfter("supply_needs",
            array(
                "header" => $this->__("Supply Need"),
                "index" => "supply_needs",
                "sortable" => false,
                "filter" => false
            ),
            'availability_date'
        );
        return $this;
    }


    /**
     * Apply `qty` filter to product grid.
     *
     * @param Mage_Core_Model_Mysql4_Collection_Abstract $collection
     * @param $column
     */
    protected function _filterFieldCallback($collection, $column)
    {
        if (!($value = $column->getFilter()->getValue())) {
            return;
        }
        if (is_array($value)) {
            if (isset($value['from'])) {
                if ($column->getIndex() == 'current_cost')
                    $collection->getSelect()
                        ->where(new \Zend_Db_Expr($this->mappingField[$column->getIndex()]) . ' >= ' . $value['from'] / $this->purchaseOrder->getCurrencyRate());
                else
                    $collection->getSelect()
                        ->where(new \Zend_Db_Expr($this->mappingField[$column->getIndex()]) . ' >= ' . $value['from']);
            }
            if (isset($value['to'])) {
                if ($column->getIndex() == 'current_cost')
                    $collection->getSelect()
                        ->where(new \Zend_Db_Expr($this->mappingField[$column->getIndex()]) . ' <= ' . $value['to'] / $this->purchaseOrder->getCurrencyRate());
                else
                    $collection->getSelect()
                        ->where(new \Zend_Db_Expr($this->mappingField[$column->getIndex()]) . ' <= ' . $value['to']);
            }
        } else {
            $collection->getSelect()
                ->where(new \Zend_Db_Expr($this->mappingField[$column->getIndex()]) . " LIKE '%" . $value . "%'");
        }
        return $collection;
    }

    protected function verifyIndexColumn($column) {
        if(Mage::helper('purchaseordersuccess')->isProductFromSupplier()) {
            if ($column->getFilterIndex() == 'product_sku' || $column->getIndex() == 'product_sku') {
                $column->setFilterIndex('sku');
            }
            if (in_array($column->getFilterIndex(), ['product_name'])
                || in_array($column->getIndex(), ['product_name'])) {
                $column->setFilterIndex('name');
            }
            if (in_array($column->getFilterIndex(), ['product_supplier_sku'])
                || in_array($column->getIndex(), ['product_supplier_sku'])) {
                $column->setFilterIndex('supplier_product.product_supplier_sku');
            }
        } else {
            if ($column->getFilterIndex() == 'product_sku' || $column->getIndex() == 'product_sku') {
                $column->setFilterIndex('sku');
            }
            if (in_array($column->getFilterIndex(), ['product_name'])
                || in_array($column->getIndex(), ['product_name'])) {
                $column->setFilterIndex('value');
            }
        }
        return $column;
    }
}