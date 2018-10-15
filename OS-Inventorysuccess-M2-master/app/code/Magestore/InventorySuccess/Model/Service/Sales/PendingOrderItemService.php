<?php

namespace Magestore\InventorySuccess\Model\Service\Sales;

class PendingOrderItemService
{
    /**
     * @var string
     */
    protected $_pendingQty;

    protected $orderItemFactory;

    protected $cr;
    /**
     * Magestore_Inventorysuccess_Model_Service_Sales_PendingOrderItemService constructor.
     */
    public function __construct(
        \Magento\Sales\Model\Order\ItemFactory $orderItemFactory
    )
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        /** @var \Magento\Framework\App\ResourceConnection $resource */
        $cr = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $this->cr = $cr;
        $order_item_table = $cr->getTableName('sales_order_item');
        $on_hold_qty_sql = " (select (qty_ordered-qty_shipped-qty_refunded-qty_canceled) from {$order_item_table} where item_id = main_table.parent_item_id) ";
        $on_hold_only_simple = "main_table.qty_ordered - main_table.qty_shipped - main_table.qty_refunded - main_table.qty_canceled";
        $foreignField = "(select product_type from {$order_item_table} where item_id = main_table.parent_item_id)";
        $configuration_code = 'configurable';
        $this->_pendingQty = '(IF(('.$foreignField.' = "'.$configuration_code.'" ), IF('.$on_hold_qty_sql.' > 0, '.$on_hold_qty_sql.', 0), IF('.$on_hold_only_simple.' > 0, '.$on_hold_only_simple.', 0) ) )';
        $this->orderItemFactory = $orderItemFactory;
    }

    /**
     *
     * @param int $productId
     * @return collection
     */
    public function getCollection($productId = null)
    {
        /* Start SQL : select all simple products and group by product_id , if exist parent_item_id -> calculate in configuration products */
        $collection = $this->orderItemFactory->create()->getCollection()
        ->addFieldToFilter('product_id',$productId);
        $collection->getSelect()->columns(array(
            'pending_qty' => new \Zend_Db_Expr($this->_pendingQty),
        ))
            ->where("{$this->_pendingQty} > 0")
            ->where('product_type IN (?)', array(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE,\Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL));
        $collection->getSelect()->joinLeft(
            array('order' => $this->cr->getTableName('sales_order')),
            'main_table.order_id = order.entity_id',
            array('increment_id'=> 'order.increment_id'));
        return $collection;
    }

    /**
     * @param $collection
     * @param $columnId
     * @param $value
     * @return $collection
     */
    public function _filterQtyCallback($collection,$columnId,$value){
        if(isset($value['from'])){
            $collection->getSelect()->where("{$this->_pendingQty} >= ?", $value['from']);
        }
        if(isset($value['to'])){
            $collection->getSelect()->where("{$this->_pendingQty} <= ?", $value['to']);
        }
        return $collection;
    }
    public function _filterWarehouseCallback($collection,$value){
        $collection->getSelect()->where("main_table.warehouse_id = ?",$value);
    }
}