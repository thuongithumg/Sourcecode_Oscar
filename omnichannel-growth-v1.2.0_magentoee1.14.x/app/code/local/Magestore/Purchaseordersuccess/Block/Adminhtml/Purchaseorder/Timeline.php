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
 * Class Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Timeline
 */
use Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_Status as PurchaseOrderStatus;
class Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Timeline extends Mage_Adminhtml_Block_Template
{
    /**
     * get steps
     *
     * @return mixed
     */
    public function getSteps()
    {
        $steps = array(
            array(
                'title' => $this->__('New'),
                'status' => PurchaseOrderStatus::STATUS_NEW,
                'url' => '#'
            ),
            array(
                'title' => $this->__('Pending'),
                'status' => PurchaseOrderStatus::STATUS_PENDING,
                'url' => '#'
            ),
            array(
                'title' => $this->__('Processing'),
                'status' => PurchaseOrderStatus::STATUS_PROCESSING,
                'url' => '#'
            ),
            array(
                'title' => $this->__('Completed'),
                'status' => PurchaseOrderStatus::STATUS_COMPLETED,
                'url' => '#'
            )
        );
        if(Mage::registry('current_purchase_order')->getStatus() == PurchaseOrderStatus::STATUS_CANCELED){
            $steps[] = array(
                'title' => $this->__('Canceled'),
                'status' => PurchaseOrderStatus::STATUS_CANCELED,
                'url' => '#'
            );
        }
        return $steps;
    }

    /**
     * get stocktaking status
     *
     * @return int
     */
    public function getPurchaseOrderStatus()
    {
        if (Mage::registry('current_purchase_order')
            && Mage::registry('current_purchase_order')->getId()
        ) {
            return Mage::registry('current_purchase_order')->getStatus();
        }
        return Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_Status::STATUS_NEW;
    }

    /**
     * get stocktaking status
     *
     * @return int
     */
    public function isActive($step)
    {
        if ($this->getPurchaseOrderStatus() == $step['status']) {
            return true;
        }
        return false;
    }

}