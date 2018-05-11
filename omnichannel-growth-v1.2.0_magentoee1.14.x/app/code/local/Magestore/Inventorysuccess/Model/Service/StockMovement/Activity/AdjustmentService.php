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
 * Inventorysuccess Observer Model
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Service_StockMovement_Activity_AdjustmentService
    extends Magestore_Inventorysuccess_Model_Service_StockMovement_Activity_AbstractService
{
    const STOCK_MOVEMENT_ACTION_CODE = 'adjustment';
    const STOCK_MOVEMENT_ACTION_LABEL = 'Stock Adjustment';

    /**
     * Get action reference of stock movement
     *
     * @return string
     */
    public function getStockMovementActionReference($id = null)
    {
        return Mage::getModel('inventorysuccess/adjuststock')->load($id)
            ->getAdjuststockCode();
    }

    /**
     * Get stock movement action URL
     *
     * @param $id
     * @return string|null
     */
    public function getStockMovementActionUrl($id = null)
    {
        return $this->getUrl('*/inventorysuccess_adjuststock/edit', array('id' => $id));
    }
}
