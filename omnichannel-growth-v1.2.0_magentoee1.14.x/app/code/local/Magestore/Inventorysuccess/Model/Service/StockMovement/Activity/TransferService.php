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
class Magestore_Inventorysuccess_Model_Service_StockMovement_Activity_TransferService
    extends Magestore_Inventorysuccess_Model_Service_StockMovement_Activity_AbstractService
{
    const STOCK_MOVEMENT_ACTION_CODE = 'transferstock';
    const STOCK_MOVEMENT_ACTION_LABEL = 'Transfer Stock';

    /**
     * Get action reference of stock movement
     *
     * @return string
     */
    public function getStockMovementActionReference($id = null)
    {
        return Mage::getModel('inventorysuccess/transferstock')
            ->load($id)->getTransferstockCode();
    }

    /**
     * Get stock movement action URL
     *
     * @param $id
     * @return string|null
     */
    public function getStockMovementActionUrl($id = null)
    {
        $transferStock =  Mage::getModel('inventorysuccess/transferstock')->load($id);
        $type = $transferStock->getType();

        switch ($type) {
            case Magestore_Inventorysuccess_Model_Transferstock::TYPE_REQUEST:
                return $this->getUrl('*/inventorysuccess_transferstock_requeststock/edit', array('id' => $id));
                break;
            case Magestore_Inventorysuccess_Model_Transferstock::TYPE_SEND:
                return $this->getUrl('*/inventorysuccess_transferstock_sendstock/edit', array('id' => $id));
                break;
            case Magestore_Inventorysuccess_Model_Transferstock::TYPE_FROM_EXTERNAL:
                return $this->getUrl('*/inventorysuccess_transferstock_external/edit', array('id' => $id, 'type' => 'from_external'));
                break;
            case Magestore_Inventorysuccess_Model_Transferstock::TYPE_TO_EXTERNAL:
                return $this->getUrl('*/inventorysuccess_transferstock_external/edit', array('id' => $id, 'type' => 'to_external'));
                break;
        }
    }
}