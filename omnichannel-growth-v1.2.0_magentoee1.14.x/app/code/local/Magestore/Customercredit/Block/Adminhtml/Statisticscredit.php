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
 * @package     Magestore_Storecredit
 * @module      Storecredit
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * Customercredit Adminhtml Block
 * 
 * @category    Magestore
 * @package     Magestore_Customercredit
 * @author      Magestore Developer
 */
class Magestore_Customercredit_Block_Adminhtml_Statisticscredit extends Mage_Core_Block_Template
{

    /**
     * prepare block's layout
     *
     * @return Magestore_Bannerslider_Block_Adminhtml_Addbutton
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('customercredit/statisticscredit.phtml');
    }

    /**
     * @return mixed
     */
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    /**
     * @return mixed
     */
    public function getTotalCredit()
    {
        $collections = Mage::getResourceModel('customer/customer_collection')
            ->joinAttribute('credit_value', 'customer/credit_value', 'entity_id', null, 'left');

        // toi uu hoa truy van sql By RONALD //
        $collections->getSelect()
        ->reset(Zend_DB_Select::COLUMNS)
        ->columns(array('total_credit_value' => 'SUM(at_credit_value.value)'));
        $totalCredit = 0;
        $data = $collections->getFirstItem()->getData();

        if(array_key_exists('total_credit_value', $data)){
            $totalCredit = $data['total_credit_value'];
        }
        // end By RONALD //

        // $totalCredit = 0;
        // foreach ($collections as $item) {
        //     if ($item->getCreditValue()) {
        //         $totalCredit += $item->getCreditValue();
        //     }
        // }
        return Mage::helper('core')->currency($totalCredit);
    }

    /**
     * @return mixed
     */
    public function getCreditUsed()
    {
        return Mage::getResourceModel('customercredit/transaction')->getCreditUsed();
    }

    /**
     * @return int
     */
    public function getCustomerWithCredit()
    {
        $collections = Mage::getResourceModel('customer/customer_collection')
            ->addAttributeToFilter('credit_value', array('gt' => 0.00));

        // toi uu hoa truy van sql By RONALD //
        $collections->getSelect()
        ->reset(Zend_DB_Select::COLUMNS)
        ->columns(array('total_customer' => 'count(e.entity_id)'));
        $data = $collections->getFirstItem()->getData();

        $numCustomer = 0;
        if(array_key_exists('total_customer', $data)){
            $numCustomer = $data['total_customer'];
        }
        // end By RONALD //

        // $numCustomer = count($collections);
        return $numCustomer;
    }

    /**
     * @return float
     */
    public function percentCredit()
    {
        $collections = Mage::getResourceModel('customer/customer_collection');

        // toi uu hoa truy van sql By RONALD //
        $collections->getSelect()
        ->reset(Zend_DB_Select::COLUMNS)
        ->columns(array('total_customer' => 'count(e.entity_id)'));

        $data = $collections->getFirstItem()->getData();

        $totalCustomer = 0;
        if(array_key_exists('total_customer', $data)){
            $totalCustomer = $data['total_customer'];
        }
        // end By RONALD //

        // $totalCustomer = count($collections);
        $percent = ($this->getCustomerWithCredit() / $totalCustomer) * 100;
        return round($percent, 2);
    }

}
