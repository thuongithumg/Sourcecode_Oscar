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
 * @package     Magestore_Giftvoucher
 * @module     Giftvoucher
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * Giftvoucher History block
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */
class Magestore_Giftvoucher_Block_History extends Mage_Core_Block_Template
{

    protected function _construct()
    {
        parent::_construct();
        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $collection = Mage::getModel('giftvoucher/credithistory')->getCollection()
            ->addFieldToFilter('main_table.customer_id', $customerId);
        $collection->setOrder('history_id', 'DESC');
        $this->setCollection($collection);
    }

    /**
     * @return $this
     */
    public function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock('page/html_pager', 'history_pager')
            ->setTemplate('page/html/pager.phtml')
            ->setCollection($this->getCollection());
        $this->setChild('history_pager', $pager);

        $grid = $this->getLayout()->createBlock('giftvoucher/grid', 'history_grid');
        // prepare column

        $grid->addColumn('action', array(
            'header' => $this->__('Action'),
            'index' => 'action',
            'format' => 'medium',
            'align' => 'left',
            'width' => '120px',
            'type' => 'options',
            'options' => Mage::getSingleton('giftvoucher/creditaction')->getOptionArray(),
            'searchable' => true,
        ));

        $grid->addColumn('balance_change', array(
            'header' => $this->__('Balance Change'),
            'align' => 'left',
            'type' => 'baseprice',
            'index' => 'balance_change',
            'width' => '120px',
            'render' => 'getBalanceChangeFormat',
            'searchable' => true,
        ));

        $grid->addColumn('giftcard_code', array(
            'header' => $this->__('Gift Card Code'),
            'align' => 'left',
            'width' => '150px',
            'type' => 'text',
            'index' => 'giftcard_code',
            'searchable' => true,
        ));

        $grid->addColumn('order_number', array(
            'header' => $this->__('Order'),
            'align' => 'left',
            'type' => 'text',
            'index' => 'order_number',
            'width' => '80px',
            'render' => 'getOrder',
            'searchable' => true,
        ));

        $grid->addColumn('currency_balance', array(
            'header' => $this->__('Current Balance'),
            'align' => 'left',
            'width' => '150px',
            'type' => 'baseprice',
            'index' => 'currency_balance',
            'render' => 'getBalanceFormat',
        ));

        $grid->addColumn('created_date', array(
            'header' => $this->__('Changed Time'),
            'index' => 'created_date',
            'type' => 'date',
            'width' => '120px',
            'format' => 'medium',
            'align' => 'left',
            'searchable' => true,
        ));


        $this->setChild('history_grid', $grid);
        return $this;
    }

    /**
     * Returns the formatted blance
     * 
     * @param mixed $row
     * @return string
     */
    public function getBalanceFormat($row)
    {
        $currency = Mage::getModel('directory/currency')->load($row->getCurrency());
        return $currency->format($row->getCurrencyBalance());
    }

    /**
     * Render an order link
     * 
     * @param mixed $row
     * @return string
     */
    public function getOrder($row)
    {
        if ($row->getOrderId()) {
            $render = '<a href="' . $this->getUrl('sales/order/view', array(
                    'order_id' => $row->getOrderId()
                )) . '">' . $row->getOrderNumber() . '</a>';
            return $render;
        }
        return 'N/A';
    }

    /**
     * Returns the formatted blance change
     * 
     * @param mixed $row
     * @return string
     */
    public function getBalanceChangeFormat($row)
    {
        $currency = Mage::getModel('directory/currency')->load($row->getCurrency());
        return $currency->format($row->getBalanceChange());
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('history_pager');
    }

    /**
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getChildHtml('history_grid');
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $this->getChild('history_grid')->setCollection($this->getCollection());
        return parent::_toHtml();
    }

    /**
     * @return string
     */
    public function getBalanceAccount()
    {
        $store = Mage::app()->getStore();
        $creadit = Mage::getModel('giftvoucher/credit')->getCreditAccountLogin();
        $currency = Mage::app()->getStore()->getCurrentCurrency();

        return $currency->format($store->convertPrice($creadit->getBalance()));
    }

}
