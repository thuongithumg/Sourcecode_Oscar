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
 * Warehouse Edit Dashboard Best Seller Block
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Warehouse_Edit_Tab_Dashboard_Bestseller
    extends Magestore_Inventorysuccess_Block_Adminhtml_Warehouse_Edit_Tab_Dashboard_AbstractChart
{
    const NUMBER_PRODUCT = 5;

    /**
     * Initialize factory instance
     *
     * @param array $args
     */
    public function __construct(array $args)
    {
        parent::__construct($args);
        $this->setContainerId('best_seller_container');
        $this->setTitle('Best Seller');
        $this->setSubtitle('Lifetime Best Seller');
        $this->setYAxisTitle('Values');
        $this->setTooltip('Total Qty: '.'<b>{point.y}</b>');
        $this->setSeriesName(array('Shipped Qty'));
        $this->setSeriesDataLabel(array(array('format'=>'{point.y}')));
        $this->getBestSeller();
    }

    protected function getBestSeller(){
        $stockCollection = Magestore_Coresuccess_Model_Service::warehouseStockService()
            ->getAllStocksWithProductInformation()
            ->getBestSellerProducts(self::NUMBER_PRODUCT, $this->getRequest()->getParam('id'));
        $seriesData = array();
        $data = array();
        foreach ($stockCollection as $item){
            $qty = $item->getTotalQtyShipped();
            if(!$qty || $qty == '')
                $qty = 0;
            $data[] = array($item->getSku(), floatval($qty));
        }

        $seriesData[] = $data;
        $this->setSeriesData($seriesData);
        return $this;
    }
}