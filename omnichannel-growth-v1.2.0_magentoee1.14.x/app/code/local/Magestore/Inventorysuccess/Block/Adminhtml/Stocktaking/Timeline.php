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
 * Class Magestore_Inventorysuccess_Block_Adminhtml_Stocktaking_Timeline
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Stocktaking_Timeline extends Mage_Adminhtml_Block_Template
{
    /**
     * get steps
     *
     * @return mixed
     */
    public function getSteps() {
        $steps = array(
            array(
                'title' => Mage::helper('inventorysuccess')->__('General Information'),
                'status' => Magestore_Inventorysuccess_Model_Stocktaking::STATUS_NEW,
                'url' => '#'
            ),
            array(
                'title' => Mage::helper('inventorysuccess')->__('Prepare Products'),
                'status' => Magestore_Inventorysuccess_Model_Stocktaking::STATUS_PENDING,
                'url' => '#'
            ),
            array(
                'title' => Mage::helper('inventorysuccess')->__('Stock Counting'),
                'status' => Magestore_Inventorysuccess_Model_Stocktaking::STATUS_PROCESSING,
                'url' => '#'
            ),
            array(
                'title' => Mage::helper('inventorysuccess')->__('Complete Data Entry'),
                'status' => Magestore_Inventorysuccess_Model_Stocktaking::STATUS_VERIFIED,
                'url' => '#'
            ),
            array(
                'title' => Mage::helper('inventorysuccess')->__('Complete Stocktaking'),
                'status' => Magestore_Inventorysuccess_Model_Stocktaking::STATUS_COMPLETED,
                'url' => '#'
            ),
        );
        return $steps;
    }

    /**
     * get stocktaking status
     *
     * @return int
     */
    public function getStocktakingStatus()
    {
        if (Mage::registry('stocktaking_data')
            && Mage::registry('stocktaking_data')->getId()
        ) {
            return Mage::registry('stocktaking_data')->getStatus();
        }
        return Magestore_Inventorysuccess_Model_Stocktaking::STATUS_NEW;
    }

    /**
     * get stocktaking status
     *
     * @return int
     */
    public function isActive($step)
    {
        if($this->getStocktakingStatus() == $step['status']){
            return true;
        }
        return false;
    }

}