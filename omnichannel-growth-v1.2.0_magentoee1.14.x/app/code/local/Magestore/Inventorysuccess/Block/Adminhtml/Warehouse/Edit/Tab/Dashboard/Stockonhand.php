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
 * Warehouse Edit Dashboard Stock on hand Block
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Warehouse_Edit_Tab_Dashboard_Stockonhand
    extends Magestore_Inventorysuccess_Block_Adminhtml_Warehouse_Edit_Tab_Dashboard_AbstractChart
{
    const NUMBER_PRODUCT = 10;

    /**
     * Initialize factory instance
     *
     * @param array $args
     */
    public function __construct(array $args)
    {
        parent::__construct($args);
        $this->setContainerId('stock_on_hand_container');
        $this->setTitle('Stock On Hand');
        $this->setSubtitle('Stock On Hand Reports');
        $this->setYAxisTitle('Values');
        $this->setTooltip('Total Qty: '.'<b>{point.y}</b>');
        $this->setSeriesName(array('On-Hand Qty'));
        $this->setSeriesDataLabel(array(array('format'=>'{point.y}')));
        $this->getStockOnHand();
    }


    protected function getStockOnHand(){
        $stockCollection = Magestore_Coresuccess_Model_Service::warehouseStockService()
            ->getAllStocksWithProductInformation()
            ->getHighestQtyProducts(self::NUMBER_PRODUCT, $this->getRequest()->getParam('id'));
        $seriesData = array();
        $data = array();
        foreach ($stockCollection as $item){
            $data[] = array($item->getSku(), floatval($item->getTotalQty()));
        }
        $seriesData[] = $data;
        $this->setSeriesData($seriesData);
        return $this;
    }
}