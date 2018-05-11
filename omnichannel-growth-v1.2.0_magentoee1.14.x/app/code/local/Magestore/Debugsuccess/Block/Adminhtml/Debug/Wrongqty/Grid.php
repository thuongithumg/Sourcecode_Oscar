<?php
/**
 * Created by PhpStorm.
 * User: duongdiep
 * Date: 18/02/2017
 * Time: 20:28
 */
use Magestore_Debugsuccess_Helper_Data as Data;
class Magestore_Debugsuccess_Block_Adminhtml_Debug_Wrongqty_Grid extends
    Magestore_Debugsuccess_Block_Adminhtml_AbstractGridProduct {

    protected $ALLOW_CORRECT = true;

    protected $_massactionIdField = 'product_id';

    public function __construct()
    {
        parent::__construct();
        $this->setId('wrongqtyGrid');
        $this->setDefaultSort('sku');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
    }

    /**
     * @return string
     */
//    public function getMainButtonsHtml()
//    {
//        $html = parent::getMainButtonsHtml();//get the parent class buttons
//        $addButton = $this->getLayout()->createBlock('adminhtml/widget_button')//create the add button
//        ->setData(array(
//            'label' => Mage::helper('adminhtml')->__('Correct Qty'),
//            'onclick' => 'Correctqty.correctQty(this)',
//            'id' => 'correct_wrong_qty',
//            'class' => 'task'
//        ))->toHtml();
//        return $addButton . $html;
//    }

    /**
     * @param $collection
     * @return mixed
     */
    public function modifyCollection($collection)
    {
        return $collection;
    }

    /**
     *
     */
    public function modifyColumns()
    {
        return $this->service()->modifiColumns($this,Data::WRONG_QTY);
    }

    /**
     * @return mixed
     */
    public function getGridUrl() {
        return $this->getUrl('*/*/wrongqtygrid', array(
            '_current' => true
        ));
    }

    /**
     * @param $row
     * @return bool
     */
    public function getRowUrl($row) {
        return false;
    }

    /**
     * @return bool
     */
    public function _isNotAllWarehouse(){
        $warehouseId = $this->getRequest()->getParam('warehouse_id', null);
        return $this->service()->getWarehouse($warehouseId,Data::STOCK_ON_HAND);

    }
    public function getWarehouseOption(){
        return Mage::getResourceModel('inventorysuccess/warehouse_collection')->toOptionArray();
    }
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('product_id');
        $this->getMassactionBlock()->setFormFieldName('product');

        $this->getMassactionBlock()->addItem('correct_by_catalog', array(
            'label'=> Mage::helper('catalog')->__('Collect by Catalog'),
            'url'  => $this->getUrl('*/*/wrongqty', array('_current'=>true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'warehouse_id',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('catalog')->__('Warehouse to Sync'),
                    'values' => $this->getWarehouseOption()
                )
            )
        ));
        $this->getMassactionBlock()->addItem('correct_by_warehouse', array(
            'label'=> Mage::helper('catalog')->__('Collect by Warehouses'),
            'url'  => $this->getUrl('*/*/wrongqty', array('_current'=>true))
        ));

       // Mage::dispatchEvent('adminhtml_catalog_product_grid_prepare_massaction', array('block' => $this));
        return $this;
    }

}
